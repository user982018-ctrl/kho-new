<?php
namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Illuminate\Support\Facades\Auth;

class ToolController extends Controller
{
    public function tool()
    {
        $checkAll = isFullAccess(Auth::user()->role);
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $isMkt = Helper::isMkt(Auth::user());
        if ($isMkt || $isLeadSale || $checkAll) {
            return view('pages.tool.index');
        }

        return redirect()->route('home');
    }

    public function getPhonePc(Request $request, $phoneSearch)
    {
        $srcs = [];
        $pageId = $request->page_id;
        if ($pageId != "") {
            $src = Helper::getPageSrcByPageId($pageId);
            $srcs[] = $src;
        } else {
            $groups = Group::where('status', 1)->get();
            foreach ($groups as $group) {
                $srcs[] = $group->srcs->toArray();
            }
            $srcs = array_merge(...$srcs);
        }

        $phoneSearch = Helper::getCustomPhoneNum($phoneSearch);
        if (Helper::isSeeding($phoneSearch)) {
            return response()->json(['error' => 'true', 'text' => 'Data này đang nằm danh sách đen.']);
        }

        /*$groups = Group::where('status', 1)->get();
        foreach ($groups as $group) {
            $srcs[] = $group->srcs->toArray();
        }

        $srcs = array_merge(...$srcs);*/
        
        foreach ($srcs as $src) {
            if ($src['type'] != 'pc') {
                continue;
            } 
            
            // if ($src['id_page'] != '689087570959486') {
            //     continue;
            // }

            $group = $src->group;
            $srcId = $src['id'];
            $pIdPan = $src['id_page'];
            $token  = $src['token'];
            $namePage = $src['name'];
            $linkPage = $src['link'];
            $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";
            // $today    = strtotime(date("Y/m/d H:i"));
            // $before   = strtotime ( '-10 hour' , strtotime( date("Y/m/d H:i"))) ;
            // $before   = date ( 'Y/m/d H:i' , $before );
            // $before   = strtotime($before);

            // $endpoint = "$endpoint?DATE:$before+-+$today&access_token=$token";
            $endpoint = "$endpoint/search?q=$phoneSearch&access_token=$token&cursor_mode=true";
            $response = Http::withHeaders(['access_token' => $token])->get($endpoint);
            // dd($endpoint);
            if ($response->status() == 200) {
                $content  = json_decode($response->body());
                // dd($content);
                if (isset($content->conversations) && count($content->conversations) > 0) {
                    $data     = $content->conversations;
                    // dd($data);
                    foreach ($data as $item) {
                        // dd($item->recent_phone_numbers);
                        if (empty($item->recent_phone_numbers)) {
                            continue;
                        }
                        $recentPhoneNumbers = $item->recent_phone_numbers[0];
                        $mId      = $recentPhoneNumbers->m_id;
                        
                        $phone    = isset($recentPhoneNumbers) ? $recentPhoneNumbers->phone_number : '';
                        $name     = isset($item->customers[0]) ? $item->customers[0]->name : '';
                        $messages = (isset($recentPhoneNumbers) && !empty($recentPhoneNumbers->m_content)) ? $recentPhoneNumbers->m_content : '';
                        $phone = Helper::getCustomPhoneNum($phone);
                        
                        $is_duplicate = $hasOldOrder = $isOldCustomer = $assgin_user = 0;
                        $checkSaleCareOld = Helper::checkOrderSaleCarebyPhoneV5($phone, $mId, $is_duplicate, $hasOldOrder);
                        $typeCSKH = 1;

                        if ($phoneSearch == $phone) {
                            if ($name && $checkSaleCareOld) {
                                $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldCustomer);
                                if (!$assignSale) {
                                    continue;
                                }

                                $assgin_user = $assignSale->id;
                                $is_duplicate = ($is_duplicate) ? 1 : 0;
                                $sale = new SaleController();
                                $data = [
                                'page_link' => $linkPage,
                                'page_name' => $namePage,
                                'sex'       => 0,
                                'old_customer' => $isOldCustomer,
                                'address'   => '',
                                'messages'  => $messages,
                                'name'      => $name,
                                'phone'     => $phone,
                                'page_id'   => $pIdPan,
                                'text'      => 'Page ' . $namePage,
                                'chat_id'   => '',
                                'm_id'      => $mId,
                                'assgin'    => $assgin_user,
                                'is_duplicate' => $is_duplicate,
                                'group_id'  => $group->id,
                                'has_old_order'  => $hasOldOrder,
                                'src_id'  => $srcId,
                                'type_TN' => $typeCSKH, 
                                ];
                                
                                $request = new \Illuminate\Http\Request();
                                $request->replace($data);
                                $sale->save($request);
                                return response()->json(
                                    [
                                        'success'=> true,
                                        'text' => 'Chúc mừng data ' . $name . ' ' . $phone . ' đã được tạo thành công!'
                                    ]);
                            }
                        }
                    }
                }
            }

        }

        return response()->json(['success'=> 'true', 'text' => 'Không tìm thấy dữ liệu phù hợp...']);
    }
}
