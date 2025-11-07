<?php
namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Group;
use App\Models\Orders;
use App\Models\SaleCare;
use App\Models\SrcPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TestController;

class ToolController extends Controller
{
    public function thuy()
    {
        $listSrc = SrcPage::where('user_digital', 114)->where('type', 'pc')
            ->where('status', 1)
            ->get();
        foreach ($listSrc as $page) {
            $group = $page->group;
            if (!$group) {
                continue;
            }
            $testCtl = new TestController();
            $testCtl->crawlerPancakePage($page, $group);
        }

    }

    public function getID(){
        $orders = Orders::whereDate('orders.created_at', '>=', '2025-09-01')
        ->whereDate('orders.created_at', '<=', '2025-10-23')
        ->where('orders.status',  '!=', 0)
        // ->where('orders.id', 24013)
        ->get();
        // dd($orders);
        $i = 0;
        foreach ($orders as $order) {
            if (!$order->saleCare) {
                // dd($order);
                $i++;
                echo $i . ' - ' . $order->phone . ' - ' . $order->id . '<br>';
            } 
        }
        echo $i;
        // dd($orders);n_decode($json, true);
        // dd($data);
    }

    public function setID(){
        // Phương pháp 1: Xử lý streaming (khuyến nghị cho file lớn)
       // $this->processJsonStreaming();
        
        // Phương pháp 2: Xử lý toàn bộ file (cần nhiều memory)
         $this->processFullJson();
    }
    
    private function processJsonStreaming() {
        $jsonFile = public_path('json/sale_care.json');
        $handle = fopen($jsonFile, 'r');
        
        $saleCareData = [];
        $inDataArray = false;
        $recordCount = 0;
        $maxRecords = 1000; // Giới hạn để test
        
        while (($line = fgets($handle)) !== false && $recordCount < $maxRecords) {
            $line = trim($line);
            
            // Tìm bắt đầu data array
            if (strpos($line, '"data":') !== false) {
                $inDataArray = true;
                continue;
            }
            
            if ($inDataArray && $line !== ']' && $line !== '[' && $line !== '') {
                // Bỏ dấu phẩy cuối dòng
                if (substr($line, -1) === ',') {
                    $line = substr($line, 0, -1);
                }
                
                // Decode từng record
                $record = json_decode($line, true);
                if ($record) {
                    $saleCareData[] = $record;
                    $recordCount++;
                }
            }
        }
        
        fclose($handle);
        
        dd([
            'method' => 'streaming',
            'records_processed' => count($saleCareData),
            'first_record' => $saleCareData[0] ?? null,
            'memory_usage' => memory_get_usage(true)
        ]);
    }
    
    private function processFullJson() {
        // Tăng memory limit để xử lý file lớn
        ini_set('memory_limit', '1G');
        
        $json = file_get_contents(public_path('json/sale_care.json'));
        
        // Decode JSON thành array
        $data = json_decode($json, true);
        // Kiểm tra lỗi JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('JSON Error: ' . json_last_error_msg());
        }
        
        // Extract data array từ PHPMyAdmin format
        $saleCareData = [];
        foreach ($data as $item) {
            if (isset($item['type']) && $item['type'] === 'table' && isset($item['data'])) {
                $saleCareData = $item['data'];
                break;
            }
        }

        $saleCareBKs = [];

        if (count($saleCareData) > 0) {
            foreach ($saleCareData as $item) {
                $saleCareBKs[$item['id']] = $item;
            }

        }
        // dd($saleCareBKs);
        // dd([
        //     'method' => 'full_decode',
        //     'total_records' => count($saleCareData),
        //     'first_record' => $saleCareData ?? null,
        //     'memory_usage' => memory_get_usage(true)
        // ]);
        $orders = Orders::whereDate('orders.created_at', '>=', '2025-09-01')
        ->whereDate('orders.created_at', '<=', '2025-10-23')
        ->where('orders.status',  '!=', 0)
        // ->where('orders.id', 24013)
        ->get();
        // dd($orders);
        foreach ($orders as $order) {
            if (!$order->saleCare && $order->sale_care && isset($saleCareBKs[$order->sale_care])) {
                $saleBK = $saleCareBKs[$order->sale_care];
                $saleCare = new SaleCare($saleBK);
                $saleCare->save();
                echo $saleCare->id . ' - ' . $order->id . '<br>';
            } 
        }
       
    }
    public function updateName(){
        $list = SaleCare::where('old_customer', 0)
        ->whereDate('created_at', '>=', '2025-11-05')
        ->whereDate('created_at', '<=', '2025-11-30')
        ->whereFullName('Loading')
        // ->limit(100)
        // ->where('id', 99145)
        ->get();
        // dd($list);
       foreach ($list as $item) {
            $src = $item->getSrcPage;
            $phoneSearch = $item->phone;
            // dd($src->id_page);
            if ($src && ($pIdPan = $src->id_page) && ($token = $src->token)) {
                // dd($pIdPan);
                $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";
                $endpoint = "$endpoint/search?q=$phoneSearch&access_token=$token&cursor_mode=true";
                $response = Http::withHeaders(['access_token' => $token])->get($endpoint);
                // dd($endpoint);
                if ($response->status() == 200) {
                    $content  = json_decode($response->body());
                    
                    // if (isset($content->success) && $content->success == false) {
                    //     dd($content);
                    // }
                    if (isset($content->conversations) && count($content->conversations) > 0) {
                        // dd($content);
                        $data     = $content->conversations;
                        $customer = $data[0]->customers[0];
                        $name = $customer->name;
                        $item->full_name = $name;
                        $item->save();
                        echo $name . ' - ' . $phoneSearch . '<br>';
                    }
                }
            }
       }
      }

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
