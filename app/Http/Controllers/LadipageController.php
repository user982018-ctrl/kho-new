<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Helpers\Helper;
use App\Models\Group;
use App\Models\SrcPage;
use App\Models\User;
class LadipageController  extends Controller
{    
    public function saveSpam(Request $r)
    {
        return $this->index($r);
    }

    public function checkSpam($phone)
    {
        $rs = false;
        $spam = Helper::isSeeding($phone);
        if ($spam) {
            $rs = true;
        }
        return response()->json([
            'result' => $rs,
        ]);
    }
    
        public function test()
    {
         return response()->json(['success' => 'oke', 'isSpam' => true], 200);
    }
    //
    public function index2(Request $r) 
    {
        Log::info('run api ladipag 212e');

        // dd($r->all());
        $phone = $r->phone;
        $name = $r->name;
        // $email = $r->email;
        $item = $r->form_item3209;
        $address = $r->address;
        $linkPage = $r->link;
        
        $messages = $item;
        if ( $address) {
            $messages .= "\n" . $address;
        }
        $all = json_encode($r->all());
 Log::info('sao z');
        Log::info($all);

        $ladiPage = Helper::getConfigLadiPage();
        $namePage = 'Ladi Page';
        
        $assgin_user = 0;
        $is_duplicate = 0;
        $isOldDataLadi = Helper::isOldDataLadi($phone, $linkPage, $assgin_user);

        if (!$isOldDataLadi) {
            $assignSale = Helper::getAssignSale();
            $assgin_user = $assignSale->id;
        } else {
            $is_duplicate = 1;
        }

        if($ladiPage->status == 1) {
            $sale = new SaleController();
                $data = [
                  'page_link' => $linkPage,
                  'page_name' => $namePage,
                  'sex'       => 0,
                  'old_customer' => 0,
                  'address'   => '...',
                  'messages'  => $messages,
                  'name'      => $name,
                  'phone'     => $phone,
                  'text'      => $namePage,
                  'chat_id'   => 'id_VUI',
                  'assgin'    => $assgin_user,
                  'is_duplicate' => $is_duplicate
                ];

                $request = new \Illuminate\Http\Request();
                $request->replace($data);
                $sale->save($request);
        }
        return response()->json(['success' => 'oke'], 200);
    }
    
    public function index(Request $r) 
    {
        $all = json_encode($r->all());
        // dd($all);
        // Log::info($all);

        $phone = ($r->phone) ? $r->phone : $r->phone_number;
        $name = ($r->name) ?? 'Không để tên';

        $item = $r->form_item3209;
        $address = $r->address;
        $linkPage = $r->link;
        $linkPage = $r->ip;
        $messages = $item;
        if ( $address) {
            $messages .= "\n" . $address;
        }

        $all = json_encode($r->all());
        $str = $all;
        $arr = json_decode($str, true);
        $linkPage = $arr['link'];

        $assgin_user = 0;
        $is_duplicate = 0;

        /** lấy list token của nguồn ladi (token = tricho-bacillus-km ....) */
        $listSrcLadi = SrcPage::where('type', 'ladi')
            ->whereNotNull("id_page")->get();
        
        // Lấy phần path từ URL
        $path = parse_url($linkPage, PHP_URL_PATH);

        // Lấy phần cuối cùng của path
        $slug = basename($path);
        foreach ($listSrcLadi as $src) {
            if ($slug && $slug == $src->id_page) {
                $group = $src->group;
                // Log::info(json_encode($src));
                break;
                
            }
        }

        if (!$src) {
            return;
        }
        $group = $src->group;
        
        $blockPhone = ['0963339609','0344999668', '0344411068', '0841111116', '0841265116', '0986987791', '0332783056', '0985767791',
            '0918352409', '0841265117', '0348684430', '0777399687'];

        if ($group && !in_array($phone, $blockPhone)) {
            $chatId = $group->tele_hot_data;
            $phone = Helper::getCustomPhoneNum($phone);
            $hasOldOrder = $isOldOrder = 0;
            $typeCSKH = 1;
            $isOldDataLadi = Helper::isOldDataLadi($phone, $assgin_user, $group, $hasOldOrder, $is_duplicate, $isOldOrder);
            
            $flagSpam = false;
            if (Helper::isSeeding($phone)) {
                Log::channel('ladi')->info('Số điện thoại đã nằm trong danh sách spam/seeding ladi..' . $phone);
                // return;
                $flagSpam= true;
            }

            if (!$flagSpam) {
                if (!$isOldDataLadi || $assgin_user == 0) {
                /** khách mới hoàn toàn */
                $assignSale = Helper::getAssignSaleByGroup($group)->user;
                
                } else {
                    /** khách cũ */
                    $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldOrder);
                }

                if (!$assignSale) {
                    return;
                }

                if ($isOldOrder == 1) {
                    $chatId = $group->tele_cskh_data;
                }
            } else {
                $assignSale = User::find(55);
                $chatId = '-4286962864';
            }      

            // dd($src->id_page);
            $assgin_user = $assignSale->id;
            $pageNameLadi = 'Ladi Page. Link: ' . $linkPage;
            $sale = new SaleController();
            $data = [
                'page_link' => $linkPage,
                'page_name' => $pageNameLadi,
                'sex'       => 0,
                'old_customer' => $isOldOrder,
                'address'   => '',
                'messages'  => $messages,
                'name'      => $name,
                'phone'     => $phone,
                'page_id'   => $src->id_page,
                'text'      => $pageNameLadi,
                'chat_id'   => $chatId,
                'm_id'      => 'mId',
                'assgin' => $assgin_user,
                'is_duplicate' => $is_duplicate,
                'group_id'  => $group->id,
                'has_old_order'  => $hasOldOrder,
                'src_id' => $src->id,
                'type_TN' => $typeCSKH, 
                'access' => true,
            ];

            // dd($data);

            $request = new \Illuminate\Http\Request();
            $request->replace($data);
            $sale->save($request);
        }

       return response()->json(['success' => true, 'isSpam' => $flagSpam], 200);
        
    }
}
