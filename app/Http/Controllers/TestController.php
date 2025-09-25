<?php

namespace App\Http\Controllers;

use App\Helpers\HelperProduct;
use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AddressController;
use App\Models\SaleCare;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Group;
use App\Models\SrcPage;
use DateTime;
use PHPUnit\TextUI\Help;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\assertFalse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Google\Service\AndroidPublisher\Order;

// setlocale(LC_TIME, 'vi_VN.utf8');
// setlocale(LC_TIME, "vi_VN");
class TestController extends Controller
{
  use WithoutMiddleware;

  public function updatePrintStatusGHN2()
  {
    /** orders chưa giao vận và trạng thái usu là đã in */
    $dateBegin  = date('Y-m-d',strtotime("01/09/2025"));
    $listOrder = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
      ->where('orders.status', 1)->where('shipping_order.print_status', 1)->where('shipping_order.vendor_ship', 'GHN')
      ->whereDate('orders.created_at', '>=', $dateBegin)
      ->where('shipping_order.check_cron', 0)
      // ->where('orders.id', 20062)
      ->select('orders.*','shipping_order.order_code as order_code')
      ->limit(100)
      ->get();

    foreach ($listOrder as $order) {
      $code = $order->order_code;
      $data = Helper::getTokenPrintGHN($order->order_code);
      if (isset($data['token'])) {
        $print = Helper::printGHN($data['token']);
        if ($print) {
          /**update print status GHN */
          $orderCTL = new OrdersController();
          $checkCron = $print;
          $orderCTL->updatePrintStatus($code, 'GHN', $checkCron);
        }
        
      }
    }
  }

  public function updatePrintStatusGHN()
  {
    /** orders chưa giao vận và trạng thái usu là chưa in */
    $dateBegin  = date('Y-m-d',strtotime("01/08/2025"));
    $listOrder = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
      ->where('orders.status', 1)->where('shipping_order.print_status', 0)->where('shipping_order.vendor_ship', 'GHN')
      ->whereDate('orders.created_at', '>=', $dateBegin)
      // ->where('orders.id', 20062)
      ->select('orders.*','shipping_order.order_code as order_code')
      ->limit(100)
      ->get();

    foreach ($listOrder as $order) {
      $code = $order->order_code;
      $data = Helper::getDetailOrderGHN($order->order_code);
      if (isset($data['data']) && isset($data['data']['print_by_user_id'])) {
        /**update print status GHN */
        $orderCTL = new OrdersController();
        $orderCTL->updatePrintStatus($code, 'GHN');
      }
    }
  }

  public function updateStatusOrderGHTK() 
  {
    
     Log::channel('d')->info('run log');
    $orders = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
      ->where('orders.status', 2) //dang giao
      ->where('shipping_order.vendor_ship', 'GHTK')
      ->get('orders.*');

    foreach ($orders as $order) {

      $endpoint = "https://services.giaohangtietkiem.vn/services/shipment/v2/" . $order->shippingOrder->order_code;
      $token = '1L0DDGVPfiJwazxVW0s7AQiUhRH1hb7E1s63rtd';
      $response = Http::withHeaders(['token' => $token])->get($endpoint);
      $response = $response->json();

      if (isset($response['success']) && $response['success']) {
        $data     = $response['order'];
        switch ($data['status']) {
          #chờ lây hàng
          case 1:
          case 2:
          case 7:
          case 12:
          case 8:
            $order->status = 1;
            break;
          #chờ lây hàng
            

          # đang giao
          case 3:
          case 10:
          case 4:
          case 9:
            $order->status = 2;       
            break;
          # đang giao
    
          #thành công
          case 5:
          // case 6:
            $order->status = 3;
            break;

          #hoàn/huỷ
          case 20:
          case 21:
          case 11:
          case -1:
            $order->status = 0;
            break;
          
          default:
            # đang giao
            $order->status = 2;
            break;
        }
        
        $order->save();

        //check đơn này đã có data chưa
        $issetOrder = Helper::checkOrderSaleCare($order->id);

        //getOriginal lấy trực tiếp field từ db
        // status = 3 = 'hoàn tất', tạo data tác nghiệp sale
        if ($order->getOriginal('status') == 3) {

          $orderTricho = $order->saleCare;
          $chatId = $groupId = '';
          $saleCare = $order->saleCare;

          /** dành cho những data TN và đơn hàng khi chưa nhóm group */
          if ($order->saleCare && $saleCare->group) {

            $group = $saleCare->group;
            $chatId = $group->tele_cskh_data;
            $groupId = $group->id;
            /** có tick chia đều team cskh thì chạy tìm người để phát data cskh
             *  ngược lại ko tick thì đơn của sale nào người đó care
             * nếu chọn chia đều team CSKH thì mặc định luôn có sale nhận data
             */

            // dd($group);
            if ($group->is_share_data_cskh) {
              
              $assgin_user = Helper::getAssignCskhByGroup($group, 'cskh')->id_user;
            } else {
              $assgin_user = $order->saleCare->assign_user;
              $user = $order->saleCare->user;

              //tài khoản đã khoá hoặc chặn nhận data => tìm sale khác trong nhóm
              if (!$user->is_receive_data || !$user->status) {
                $assgin_user = Helper::getAssignSaleByGroup($group, 'cskh')->id_user;
              }
            }

          } else if (!empty($orderTricho->group_id) && $orderTricho->group_id == 'tricho') {
            $groupId = 'tricho';
            
            //id_CSKH_tricho 4234584362
            $chatId = '-4286962864'; 
            $assgin_user = $order->assign_user;
          } else {
            $assgin_user = 50;
            //cskh 4128471334
            $chatId = '-4558910780';
            // $chatId = '-4128471334';
          }

          $typeCSKH = Helper::getTypeCSKH($order);
          $pageName = $order->saleCare->page_name;
          $pageId = $order->saleCare->page_id;
          $pageLink = $order->saleCare->page_link;

          $sale = new SaleController();
          $data = [
            'id_order' => $order->id,
            'sex' => $order->sex,
            'name' => $order->name,
            'phone' => $order->phone,
            'address' => $order->address,
            'assgin' => $assgin_user,
            'page_name' => $pageName,
            'page_id' => $pageId,
            'page_link' => $pageLink,
            'group_id' => $groupId,
            'chat_id' => $chatId,
            'type_TN' => $typeCSKH, 
            'old_customer' => 1
          ];

          if ($order->saleCare->src_id) {
            $data['src_id'] = $order->saleCare->src_id;
          } else if ($order->saleCare->type != 'ladi') {
            $pageSrc = SrcPage::where('id_page', $order->saleCare->page_id)->first();
            if ($pageSrc) {
              $data['src_id'] = $pageSrc->id;
            }
          }

          // dd($data);

          if ($issetOrder || $order->id) {
            $data['old_customer'] = 1;
          }

          $request = new \Illuminate\Http\Request();
          $request->replace($data);
          $sale->save($request);
        }
      }
    }
  }

  public function ghtkToShipping() 
  {
    $orders = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
      ->where('orders.status', 1) //chua giao
      ->where('shipping_order.vendor_ship', 'GHTK')
      ->get('orders.*');

    foreach ($orders as $order) {

      $endpoint = "https://services.giaohangtietkiem.vn/services/shipment/v2/" . $order->shippingOrder->order_code;
      $token = '1L0DDGVPfiJwazxVW0s7AQiUhRH1hb7E1s63rtd';
      $response = Http::withHeaders(['token' => $token])->get($endpoint);
      $response = $response->json();

      if (isset($response['success']) && $response['success']) {
        $data     = $response['order'];
        switch ($data['status']) {
          #chờ lây hàng
          case 1:
          case 2:
          case 7:
          case 12:
          case 8:
            $order->status = 1;
            break;
          #chờ lây hàng
            

          # đang giao
          case 3:
          case 10:
          case 4:
          case 9:
            $order->status = 2;       
            break;
          # đang giao
    
          #thành công
          case 5:
          // case 6:
            $order->status = 3;
            break;

          #hoàn/huỷ
          case 20:
          case 21:
          case 11:
          case -1:
            $order->status = 0;
            break;
          
          default:
            # đang giao
            $order->status = 2;
            break;
        }
        
        $order->save();

        //check đơn này đã có data chưa
        $issetOrder = Helper::checkOrderSaleCare($order->id);

        //getOriginal lấy trực tiếp field từ db
        // status = 3 = 'hoàn tất', tạo data tác nghiệp sale
        if ($order->getOriginal('status') == 3) {

          $orderTricho = $order->saleCare;
          $chatId = $groupId = '';
          $saleCare = $order->saleCare;

          /** dành cho những data TN và đơn hàng khi chưa nhóm group */
          if ($order->saleCare && $saleCare->group) {

            $group = $saleCare->group;
            $chatId = $group->tele_cskh_data;
            $groupId = $group->id;
            /** có tick chia đều team cskh thì chạy tìm người để phát data cskh
             *  ngược lại ko tick thì đơn của sale nào người đó care
             * nếu chọn chia đều team CSKH thì mặc định luôn có sale nhận data
             */

            // dd($group);
            if ($group->is_share_data_cskh) {
              
              $assgin_user = Helper::getAssignCskhByGroup($group, 'cskh')->id_user;
            } else {
              $assgin_user = $order->saleCare->assign_user;
              $user = $order->saleCare->user;

              //tài khoản đã khoá hoặc chặn nhận data => tìm sale khác trong nhóm
              if (!$user->is_receive_data || !$user->status) {
                $assgin_user = Helper::getAssignSaleByGroup($group, 'cskh')->id_user;
              }
            }

          } else if (!empty($orderTricho->group_id) && $orderTricho->group_id == 'tricho') {
            $groupId = 'tricho';
            
            //id_CSKH_tricho 4234584362
            $chatId = '-4286962864'; 
            $assgin_user = $order->assign_user;
          } else {
            $assgin_user = 50;
            //cskh 4128471334
            $chatId = '-4558910780';
            // $chatId = '-4128471334';
          }

          $typeCSKH = Helper::getTypeCSKH($order);
          $pageName = $order->saleCare->page_name;
          $pageId = $order->saleCare->page_id;
          $pageLink = $order->saleCare->page_link;

          $sale = new SaleController();
          $data = [
            'id_order' => $order->id,
            'sex' => $order->sex,
            'name' => $order->name,
            'phone' => $order->phone,
            'address' => $order->address,
            'assgin' => $assgin_user,
            'page_name' => $pageName,
            'page_id' => $pageId,
            'page_link' => $pageLink,
            'group_id' => $groupId,
            'chat_id' => $chatId,
            'type_TN' => $typeCSKH, 
            'old_customer' => 1
          ];

          if ($order->saleCare->src_id) {
            $data['src_id'] = $order->saleCare->src_id;
          } else if ($order->saleCare->type != 'ladi') {
            $pageSrc = SrcPage::where('id_page', $order->saleCare->page_id)->first();
            if ($pageSrc) {
              $data['src_id'] = $pageSrc->id;
            }
          }

          // dd($data);

          if ($issetOrder || $order->id) {
            $data['old_customer'] = 1;
          }

          $request = new \Illuminate\Http\Request();
          $request->replace($data);
          $sale->save($request);
        }
      }
    }
  }

  public function trang()
  {
    $pageOfHieu = SrcPage::where('user_digital', 115)->where('type', 'pc')->get();
    $group = Group::find(10); //npk
    foreach ($pageOfHieu as $page) {
      if ($page->type == 'pc') {
        $this->crawlerPancakePage($page, $group);
      }
    }
  }

  public function hieu()
  {
    $pageOfHieu = SrcPage::where('user_digital', 117)->where('type', 'pc')->get();
    $group = Group::find(10); //npk
    foreach ($pageOfHieu as $page) {
      if ($page->type == 'pc') {
        $this->crawlerPancakePage($page, $group);
      }
    }
  }

  public function tele() 
  {
    // echo 'hi';
    $strEncode = "Th\u00f4ng b\u00e1o d\u1eef li\u1ec7u t\u1eeb LadiPage\nname : Li\nphone : 0912523644\nform_item3209 : T\u00f4i mu\u1ed1n b\u00e1o gi\u00e1 qua \u0111i\u1ec7n tho\u1ea1i\nNgu\u1ed3n t\u1eeb: https:\/\/www.nongnghiepsachvn.net\/mua4-tang2?utm_source=120208585133120157&utm_campaign=120208585133100157&fbclid=IwAR0rlPJKCCmKp3bQjpV78Qju_3OLfoOK_VfYJ-jXDCOM_jbyLbhnUKmFxgA_aem_AY8k3fYevsitPWBGbMAfIikjN8cDkS4itppXbjvUmJ1u-HGgzpspTx9GCQnQlm_VGYUxmwSF6Wx75UPqSqsNJNQ-\n\u0110\u1ecba ch\u1ec9 IP: 14.160.234.108";
    $str = "Th\u00f4ng b\u00e1o d\u1eef li\u1ec7u t\u1eeb LadiPage\nname : dinh khanh dat\nphone : 0912523644\nform_item3209 : T\u00f4i mu\u1ed1n b\u00e1o gi\u00e1 qua \u0111i\u1ec7n tho\u1ea1i\nNgu\u1ed3n t\u1eeb: https:\/\/www.nongnghiepsachvn.net\/mua4-tang2?utm_source=120208585133120157&utm_campaign=120208585133100157&fbclid=IwAR0rlPJKCCmKp3bQjpV78Qju_3OLfoOK_VfYJ-jXDCOM_jbyLbhnUKmFxgA_aem_AY8k3fYevsitPWBGbMAfIikjN8cDkS4itppXbjvUmJ1u-HGgzpspTx9GCQnQlm_VGYUxmwSF6Wx75UPqSqsNJNQ-\n\u0110\u1ecba ch\u1ec9 IP: 14.160.234.108";
    // $strEncode = "<pre>Thông báo dữ liệu từ LadiPage
    // name : Li
    // phone : 0912523644
    // form_item3209 : Tôi muốn báo giá qua điện thoại
    // Nguồn từ: https://www.nongnghiepsachvn.net/mua4-tang2?utm_source=120208585133120157&utm_campaign=120208585133100157&fbclid=IwAR0rlPJKCCmKp3bQjpV78Qju_3OLfoOK_VfYJ-jXDCOM_jbyLbhnUKmFxgA_aem_AY8k3fYevsitPWBGbMAfIikjN8cDkS4itppXbjvUmJ1u-HGgzpspTx9GCQnQlm_VGYUxmwSF6Wx75UPqSqsNJNQ-
    // Địa chỉ IP: 14.160.234.108</pre>";

    $name = $phone = $mess = $src = '';
    $array = preg_split('/\r\n|\r|\n/', $str);
    
    foreach ($array as $item) {
      $arrItem = explode(":", $item);
      // dd($arrItem);
      if (count($arrItem) > 1) {
        // echo('> 1 ' . $arrItem[0] . '<br>');
        // $arrItem[0] = 'name';
        $strSw = preg_replace('/\s+/', '', $arrItem[0]);
        switch ($strSw) {
          case "name":
            // echo('name' . $arrItem[1] .'<br>');
            $name = $arrItem[1];
            break;
          case 'phone':
            // echo('phone' . $arrItem[1] . '<br>');
            $phone = $arrItem[1];
            break;
          case 'form_item3209':
            // echo('form_item3209' . $arrItem[1] . '<br>');
            $mess = $arrItem[1];
            break;
          case 'form_item3209':
            // echo('form_item3209' . $arrItem[1] . '<br>');
            $name = $arrItem[1];
            break;
          default:
            if (count($arrItem) == 3) {
              // echo('src ' . $arrItem[2] . '<br>');
              $src = $arrItem[2];
            }
            break;
        }

        
      
        // echo "<pre>";
        // print_r($arrItem);
        // echo "</pre>";
      }
    }
    // $name = $phone = $mess = $src ='';
    echo 'name: ' . $name . '<br>';
    echo 'phone: ' . $phone . '<br>';;
    echo 'mess: ' . $mess . '<br>';
    echo 'src: ' . $src . '<br>';
  }
  public function testTelephone() 
  {
    // Kiểm tra các số điện thoại mẫu
    $testNumbers = [
      "+84973409613",
      "0912345678", // đúng
      "0312345678", // đúng
      "07123456789", // sai (nhiều hơn 10 chữ số)
      "02123456789", // đúng (số cố định)
      "051234567", // sai (ít hơn 10 chữ số)
    ];

    foreach ($testNumbers as $number) {
      if ($this->isValidVietnamPhoneNumber($number)) {
          echo "$number là số điện thoại hợp lệ.\n";
          
      } else {
          echo "$number không phải là số điện thoại hợp lệ.\n";
      }

      echo "<br>";
    }
  }

  public function updateData() 
  {
    // $l = SaleCare::where('phone', $phone)->where('page_id', $pageId);
    // // ->update(['m_id' => $mId])
    // ;
    // echo "<pre>";
    // print($l->get());
    // echo "</pre>";

    $panCake = Helper::getConfigPanCake();
    $pageId = $panCake->page_id;
    $pages  = json_decode($pageId,1);
    $token  = $panCake->token;

    if (count($pages) > 0) {
      foreach ($pages as $key => $val) {
        $pIdPan   = $val['id'];
        $namePage = $val['name'];
        $linkPage = $val['link'];
        $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";

        $today    = strtotime(date("Y/m/d H:i"));
        // $before   = strtotime(date('Y-m-d H:i', strtotime($today. ' - 1 days')));
        // $before   = strtotime(date('Y-m-d H:i', strtotime($today. ' - 1 hour')));
        $before = strtotime ( '-20 hour' , strtotime ( date("Y/m/d H:i") ) ) ;
        $before = date ( 'Y/m/d H:i' , $before );
        $before = strtotime($before);
        // dd( $today);
        // $response = Http::withHeaders(['token' => $token])
        //   ->get($endpoint, [
        //     'type' => "PHONE,DATE:$before+-+$today",
        //     'access_token' => $token,
        //     'from_platform' => 'web'
        // ]);
        $endpoint = "$endpoint?type=PHONE,DATE:$before+-+$today&access_token=$token";
        $response = Http::get($endpoint);

        if ($response->status() == 200) {
          $content  = json_decode($response->body());
          // dd($content);
          $data     = $content->conversations;
          // dd($data);
          $i = 0;
          foreach ($data as $item) {
            $phone = $item->recent_phone_numbers[0]->phone_number;
            $mId = $item->recent_phone_numbers[0]->m_id;
            echo "\n$phone - $pIdPan - $mId" . "<br>";
            // echo "\n" . "<br>";
            $i++;
            
            $l = SaleCare::where('phone', $phone)->where('page_id', $pIdPan)->orderBy('id', 'desc')->get()->first();
              
            if ($l) {
              $l->m_id = $mId;
              $l->save();
            }
            // if ($l) {
            //   echo "<pre>";
            //   print($l->get());
            //   echo "</pre>";
            // }
         
          }
          echo $i;
        }
      }
    }

  }

  public function test3() {
    $str = Helper::getListProductByOrderId(285);
    echo($str);
  }
  public function test() {
    $listSc = SaleCare::whereNotNull('next_step')->get();
    foreach ($listSc as $sc) {
      $time       = $sc->call->time;
      $nameCall   = $sc->call->name;
      $updatedAt  = $sc->updated_at;
      $isRunjob   = $sc->is_runjob;
  
      if ($time && !$isRunjob) {
        //cộng ngày update và time cuộc gọi
        $newDate = strtotime("+$time hours", strtotime($updatedAt));

        if ($newDate <= time()) {
          $sc->is_runjob = 1;
          $sc->save();

          //gửi thông báo qua telegram
          $tokenGroupChat = '7127456973:AAGyw4O4p3B4Xe2YLFMHqPuthQRdexkEmeo';
          $chatId         = '-4140296352';
          $endpoint       = "https://api.telegram.org/bot$tokenGroupChat/sendMessage";
          $client         = new \GuzzleHttp\Client();

          $notiText       = "Khách hàng $sc->full_name sđt $sc->phone"
            . "\nĐã tới thời gian tác nghiệp."
            . "\nKết quả gọi trước đó: $nameCall"
            . "\nCây trồng: $sc->type_tree"
            . "\nNhu cầu dòng sản phẩm: $sc->product_request"
            . "\nLý do không mua hàng: $sc->reason_not_buy"
            . "\nGhi chú thông tin khách hàng: $sc->note_info_customer.";  

          $client->request('GET', $endpoint, ['query' => [
            'chat_id' => $chatId, 
            'text' => $notiText,
          ]]);
        }
      }
    }
  }

  public function test2() {
    $orders = Orders::has('shippingOrder')->whereNotIn('status', [0,3])->get();
    foreach ($orders as $order) {
      $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail" ;
      $response = Http::withHeaders(['token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55'])
        ->post($endpoint, [
          'order_code' => $order->shippingOrder->order_code,
          'token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55',
        ]);
   
      if ($response->status() == 200) {
        $content  = json_decode($response->body());
        $data     = $content->data;

        switch ($data->status) {
          case 'delivered':
            #hoàn tât
            $order->status = 3;
            break;
          case 'return':
            $order->status = 0;
          case 'cancel':
            $order->status = 0;
          case 'returned':
            #hoàn/huỷ
            $order->status = 0;
            break;
          
          default:
            # đang giao
            $order->status = 2;
            break;
        }
        
        $order->save();
        
        //chỉ áp dụng cho đơn phân bón
        $isFertilizer = Helper::checkFertilizer($order->id_product);

        //check đơn này đã có data chưa
        $issetOrder = Helper::checkOrderSaleCare($order->id);
        
        // status = 'hoàn tất', tạo data tác nghiệp sale
        if ($order->status == 3 && $isFertilizer && !$issetOrder) {
            $sale = new SaleController();
            $data = [
                'id_order' => $order->id,
                'sex' => $order->sex,
                'name' => $order->name,
                'phone' => $order->phone,
                'address' => $order->address,
                'assign_user' => $order->assign_user,
            ];

            $request = new \Illuminate\Http\Request();
            $request->replace($data);
            $sale->save($request);
        }
      }
    }
  }

  
  public function isValidVietnamPhoneNumber($phone) {
    // Biểu thức chính quy cho số điện thoại di động
    $mobilePattern = "/^(9|3|7|5|8|09|03|07|08|05)\d{8}$/";
    // Biểu thức chính quy cho số điện thoại cố định
    $landlinePattern = "/^(02|03|04|05|06|07|08|09|84)\d{7,8}$/";
    
    // Biểu thức chính quy cho số điện thoại di động với mã quốc gia
    $mobilePatternWithCountryCode = "/^(\+84|0084)(9|3|7|8|5)\d{8}$/";
    // Biểu thức chính quy cho số điện thoại cố định với mã quốc gia
    $landlinePatternWithCountryCode = "/^(\+84|0084)(2|3|4|5|6|7|8|9)\d{7,8}$/";
    // $customlinePattern = "/^(+84|84)\d{7,8}$/";
    if ( preg_match($mobilePatternWithCountryCode, $phone) || preg_match($mobilePatternWithCountryCode, $phone) || preg_match($mobilePattern, $phone) || preg_match($landlinePattern, $phone)) {
        return true;
    } else {
        return false;
    }
  }

  public function updateStatusOrderGHN() 
  {
    $orders = Orders::has('shippingOrder')->whereNotIn('status', [0,3])->get();
    foreach ($orders as $order) {
      $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail" ;
      $response = Http::withHeaders(['token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55'])
        ->post($endpoint, [
          'order_code' => $order->shippingOrder->order_code,
          'token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55',
        ]);
    
      if ($response->status() == 200) {
        $content  = json_decode($response->body());
        $data     = $content->data;
        switch ($data->status) {
          case 'ready_to_pick':
            $order->status = 1;
          case 'picking':
            #chờ lây hàng
            $order->status = 1;
            break;
            
          case 'delivered':
            #hoàn tât
            $order->status = 3;
            break;

          case 'return':
            $order->status = 0;
          case 'cancel':
            $order->status = 0;
          case 'returned':
            #hoàn/huỷ
            $order->status = 0;
            break;
          
          default:
            # đang giao
            $order->status = 2;
            break;
        }
        
        $order->save();
        
        /** ko gửi thông báo nếu đơn chỉ có sp paulo */
        $notHasPaulo = Helper::hasAllPaulo($order->id_product);

        //check đơn này đã có data chưa
        $issetOrder = Helper::checkOrderSaleCare($order->id);

        // echo "$order->status $notHasPaulo";
       
        // status = 'hoàn tất', tạo data tác nghiệp sale
        if ($order->status == 3 && $notHasPaulo) {

          $orderTricho = $order->saleCare;
          $groupId = '';
          if (!empty($orderTricho->group_id) && $orderTricho->group_id == 'tricho') {
            // $assgin_user = Helper::getSaleTricho()->id;
            $assgin_user = $order->saleCare->assign_user;
            $groupId = 'tricho';
            // echo 'case 1';
          } else {
            // $assignCSKH = Helper::getAssignCSKH();
            // echo 'case 2';
            // if ($assignCSKH) {
            //   $assgin_user = $assignCSKH->id;
            //    echo 'case 2.1';
            // } else {
            //   $assgin_user = $order->assign_user;
            //   echo 'case 2.2';
            // }
            $assgin_user = 50;
          }
          
          // echo 'sisis';
         
        

          $sale = new SaleController();
          $data = [
            'id_order' => $order->id,
            'sex' => $order->sex,
            'name' => $order->name,
            'phone' => $order->phone,
            'address' => $order->address,
            'assgin' => $assgin_user,
            'group_id' => $groupId,
          ];

          if ($issetOrder || $order->id) {
            $data['old_customer'] = 1;
          }

          $request = new \Illuminate\Http\Request();
          $request->replace($data);
          $sale->save($request);
        }
      }
    }
  }

  public function testMoveColumn()
  {
    return view('pages.test');
  }

  public function crawlerPancakeTricho()
  {
    $pages = [
      'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiI0MTlkYTE5Ny0xNzFkLTQyMjYtODFiMS0wNDA2OGQyZjA3NTMiLCJzZXNzaW9uX2lkIjoiUzBrQUx5UWtqVUJjcFhmcFJPMS9HUlUyT21jM0owVC9sYkFaR0pCUXdtVSIsIm5hbWUiOiJExrDGoW5nIFRodSIsImxvZ2luX3Nlc3Npb24iOm51bGwsImluZm8iOnsib3MiOm51bGwsImRldmljZV90eXBlIjozLCJjbGllbnRfaXAiOiIxNzEuMjUzLjI3LjIzOSIsImJyb3dzZXIiOjF9LCJpYXQiOjE3MTk5OTI4MTUsImZiX25hbWUiOiJExrDGoW5nIFRodSIsImZiX2lkIjoiMTM1MjI1ODA3NDIyOTMzIiwiZXhwIjoxNzI3NzY4ODE1LCJhcHBsaWNhdGlvbiI6MX0.lAn8-zAl6_GJhpmjj3Wx1305w62mSWj6fBUYY4um6Q4',
      'pages' => [
        [
          "name" => "Tricho Bacillus - 1Xô pha 10.000 lít nước",
          "link" => "https://www.facebook.com/trichobacillus",
          "id"   => "389136690940452",
          "group" => 'tricho'
        ],
        [
          "name" => "Tricho Basilus - 1 Lít Pha 1000 Lít Nước - 0986987791",
          "link" => "https://www.facebook.com/profile.php?id=61561817156259",
          "id"   => "378087158713964",
          "group" => 'tricho'
        ],
        [
          "name" => "Trichoderma Basilus - 1 Xô Pha 10.000 Lít Nước",
          "link" => "https://www.facebook.com/profile.php?id=61562087439362",
          "id"   => "381180601741468",
          "group" => 'tricho'
        ]
      ]
    ];

    // dd('hi');
    $token  = $pages['token'];

      foreach ($pages['pages'] as $key => $val) {
        $pIdPan   = $val['id'];
        $namePage = $val['name'];
        $linkPage = $val['link'];
        $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";
        $today    = strtotime(date("Y/m/d H:i"));
        $before = strtotime ( '-5 hour' , strtotime ( date("Y/m/d H:i") ) ) ;
        $before = date ( 'Y/m/d H:i' , $before );
        $before = strtotime($before);

        $endpoint = "$endpoint?type=PHONE,DATE:$before+-+$today&access_token=$token";
        $response = Http::withHeaders(['access_token' => $token])->get($endpoint);
    
        if ($response->status() == 200) {
          $content  = json_decode($response->body());
          if ($content->success) {
            $data     = $content->conversations;
            // dd($data);
            foreach ($data as $item) {
              $recentPhoneNumbers = $item->recent_phone_numbers[0];
              $mId      = $recentPhoneNumbers->m_id;
              $phone    = isset($recentPhoneNumbers) ? $recentPhoneNumbers->phone_number : '';
              $name     = isset($item->customers[0]) ? $item->customers[0]->name : '';
              $messages = isset($recentPhoneNumbers) ? $recentPhoneNumbers->m_content : '';

              $assgin_user = 0;
              // $assgin_user = Helper::getSaleTricho()->id;
              $is_duplicate = false;
              $phone = Helper::getCustomPhoneNum($phone);
              $checkSaleCareOld = Helper::checkOrderSaleCarebyPhonePageTricho($phone, $mId, $is_duplicate, $assgin_user);

              if ($name && $checkSaleCareOld) {  
                if ($assgin_user == 0) {
                  $assignSale = Helper::getSaleTricho();
                  $assgin_user = $assignSale->id;
                }

                $is_duplicate = ($is_duplicate) ? 1 : 0;
                $sale = new SaleController();
                $data = [
                  'page_link' => $linkPage,
                  'page_name' => $namePage,
                  'sex'       => 0,
                  'old_customer' => 0,
                  'address'   => '',
                  'messages'  => $messages,
                  'name'      => $name,
                  'phone'     => $phone,
                  'page_id'   => $pIdPan,
                  'text'      => 'Page ' . $namePage,
                  'chat_id'   => 'id_VUI_tricho',
                  'm_id'      => $mId,
                  'assgin'    => $assgin_user,
                  'is_duplicate' => $is_duplicate,
                  'group_id' => 'tricho'
                ];

                $request = new \Illuminate\Http\Request();
                $request->replace($data);
                $sale->save($request);
              }
            }
        }
      }

    }
  }

  public function crawlerGroup()
  {
    $groups = Group::where('status', 1);
    foreach ($groups->get() as $group) {

      $pages = $group->srcs;
      foreach ($pages as $page) {
        //  if ($page->id_page != '729162340281810') {
        //   continue;
        // }
        if ($page->type == 'pc' ) {
          $this->crawlerPancakePage($page, $group);
        }
      }
    }
  }

  public function crawlerPancakePage($page, $group)
  { 
    $srcId = $page->id;
    $pIdPan = $page->id_page;
    $token  = $page->token;
    $namePage = $page->name;
    $linkPage = $page->link;
    $chatId = $group->tele_hot_data;

    echo "pIdPan: $pIdPan " . '<br>';
    echo "namePage: $namePage \n" . '<br>';
    echo "linkPage: $linkPage \n" . '<br>';
    echo "token: $token \n" . '<br>';
    echo '============================'. '<br>';
    // dd('hi');
    if ( $pIdPan != '' && $token != '' && $namePage != '' && $linkPage != '') {

      $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";
      $today    = strtotime(date("Y/m/d H:i"));
      $before   = strtotime ( '-4 hour' , strtotime ( date("Y/m/d H:i") ) ) ;
      $before   = date ( 'Y/m/d H:i' , $before );
      $before   = strtotime($before);

      $endpoint = "$endpoint?type=PHONE,DATE:$before+-+$today&access_token=$token";
      $response = Http::withHeaders(['access_token' => $token])->get($endpoint);
      // dd($response);
      if ($response->status() == 200) {
        $content  = json_decode($response->body());
        // dd($content);
         //thông báo lỗi nếu ko có hội thoại
        if ($content->success) {
          $data     = $content->conversations;

          foreach ($data as $item) {
            
            try {
              $recentPhoneNumbers = $item->recent_phone_numbers[0];
              $mId      = $recentPhoneNumbers->m_id;
              
              $phone    = isset($recentPhoneNumbers) ? $recentPhoneNumbers->phone_number : '';
              $name     = isset($item->customers[0]) ? $item->customers[0]->name : '';
              $messages = (isset($recentPhoneNumbers) && !empty($recentPhoneNumbers->m_content)) ? $recentPhoneNumbers->m_content : '';
              $phone = Helper::getCustomPhoneNum($phone);
              
              $is_duplicate = $hasOldOrder = $isOldCustomer = $assgin_user = 0;
              $checkSaleCareOld = Helper::checkOrderSaleCarebyPhoneV5($phone, $mId, $is_duplicate, $hasOldOrder);
              $typeCSKH = 1;

              if (Helper::isSeeding($phone)) {
                  Log::channel('ladi')->info('Số điện thoại đã nằm trong danh sách spam/seeding tesst..');
                  return;
              }
              if ($name && $checkSaleCareOld) {
                $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldCustomer);
                if (!$assignSale) {
                  continue;
                }
                /** kiểm tra thời gian insert tin nhắn => lâu hơn 3 ngày ko nhận lại */
                  $inputTime = strtotime($item->inserted_at);
                  $now = time();
                  $secondsIn3Days = 7 * 24 * 60 * 60;
                   echo '$now - $inputTime: ' . $now - $inputTime;
                   echo '<br>';
                    echo '$secondsIn3Days: ' . $secondsIn3Days;
                    // dd($item);
                  if ($now - $inputTime >= $secondsIn3Days) {
                      echo "Đã quá 3 ngày " . $phone;
                      echo "<br>";
                      echo $item->inserted_at;
                    //   dd($item);
                      continue;
                  } 

                $assgin_user = $assignSale->id;
                $is_duplicate = ($is_duplicate) ? 1 : 0;
                if ($isOldCustomer == 1) {
                  $chatId = $group->tele_cskh_data;
                }
                
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
                  'chat_id'   => $chatId,
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
              }
            
            } catch (\Exception $e) {
               return $e;
              // echo '$phone: ' . $phone;
              // dd($e);
              // return redirect()->route('home');
            }
          }
        }
      }           
    }
  }

  public function updateStatusOrderGhnV2() 
  {
    $orders = Orders::has('shippingOrder')->whereNotIn('status', [0,3])->get();

    
    foreach ($orders as $order) {

        // if ($order->phone != '0979710678') {
        //     continue;
        // }
        
      $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail" ;
      $response = Http::withHeaders(['token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55'])
        ->post($endpoint, [
          'order_code' => $order->shippingOrder->order_code,
          'token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55',
        ]);
        // dd($order->shippingOrder->order_code );
      if ($response->status() == 200) {
        $content  = json_decode($response->body());
        $data     = $content->data;
        switch ($data->status) {
          case 'ready_to_pick':
            $order->status = 1;
          case 'picking':
            #chờ lây hàng
            $order->status = 1;
            break;
            
          case 'delivered':
            #hoàn tât
            $order->status = 3;
            break;

          case 'return':
            $order->status = 0;
          case 'cancel':
            $order->status = 0;
          case 'returned':
            #hoàn/huỷ
            $order->status = 0;
            break;
          
          default:
            # đang giao
            $order->status = 2;
            break;
        }
        
        $order->save();
        
        /** ko gửi thông báo nếu đơn chỉ có sp paulo */
        $notHasPaulo = Helper::hasAllPaulo($order->id_product);

        //check đơn này đã có data chưa
        $issetOrder = Helper::checkOrderSaleCare($order->id);

        // status = 3 = 'hoàn tất', tạo data tác nghiệp sale
        if ($order->status == 3 && $notHasPaulo) {

          $orderTricho = $order->saleCare;
          $chatId = $groupId = '';
          $saleCare = $order->saleCare;

          /** dành cho những data TN và đơn hàng khi chưa nhóm group */
          if ($order->saleCare && $saleCare->group) {

            $group = $saleCare->group;
            $chatId = $group->tele_cskh_data;
            $groupId = $group->id;
            /** có tick chia đều team cskh thì chạy tìm người để phát data cskh
             *  ngược lại ko tick thì đơn của sale nào người đó care
             * nếu chọn chia đều team CSKH thì mặc định luôn có sale nhận data
             */
            if ($group->is_share_data_cskh) {
              $assgin_user = Helper::getAssignCskhByGroup($group, 'cskh')->id_user;
            } else {
              $assgin_user = $order->saleCare->assign_user;
              $user = $order->saleCare->user;

              //tài khoản đã khoá hoặc chặn nhận data => tìm sale khác trong nhóm
              if (!$user->is_receive_data || !$user->status) {
                $assgin_user = Helper::getAssignSaleByGroup($group)->id_user;
              }
            }

          } else if (!empty($orderTricho->group_id) && $orderTricho->group_id == 'tricho') {
            $groupId = 'tricho';
            
            //id_CSKH_tricho 4234584362
            $chatId = '-4286962864'; 
            $assgin_user = $order->assign_user;
          } else {
            $assgin_user = 50;
            //cskh 4128471334
            $chatId = '-4558910780';
            // $chatId = '-4128471334';
          }

          $typeCSKH = Helper::getTypeCSKH($order);
          $pageName = $order->saleCare->page_name;
          $pageId = $order->saleCare->page_id;
          $pageLink = $order->saleCare->page_link;

          $sale = new SaleController();
          $data = [
            'id_order' => $order->id,
            'sex' => $order->sex,
            'name' => $order->name,
            'phone' => $order->phone,
            'address' => $order->address,
            'assgin' => $assgin_user,
            'page_name' => $pageName,
            'page_id' => $pageId,
            'page_link' => $pageLink,
            'group_id' => $groupId,
            'chat_id' => $chatId,
            'type_TN' => $typeCSKH, 
            // 'old_customer' => 1
          ];

            if ($order->saleCare->src_id) {
            $data['src_id'] = $order->saleCare->src_id;
          } else if ($order->saleCare->type != 'ladi') {
            $pageSrc = SrcPage::where('id_page', $order->saleCare->page_id)->first();
            if ($pageSrc) {
              $data['src_id'] = $pageSrc->id;
            }
          }
          
          if ($issetOrder || $order->id) {
            $data['old_customer'] = 1;
          }

          $request = new \Illuminate\Http\Request();
          $request->replace($data);
          $sale->save($request);
        }
      }
    }
  }

  public function done()
  {
    $orderCTL = new OrdersController();
    $req = new Request();
    $req['daterange'] = ['01/06/2025', '30/09/2025'];
    // $req['sale'] = '77';
    // $req['typeDate'] = '2';
    // $sales = ['50','74'];
    $req['status'] = 3;
    $req['group'] = 11;

    $list = $orderCTL->getListOrderByPermisson(Auth::user(), $req);
    $dataExport[] = [
      'STT', 'Tên', 'địa chỉ',
    ];

    $i = 1;
    foreach ($list->get() as $data) {
      // dd($data);
      $dataExport[] = [
        $i,
        $data->name,
        $data->address,
      ];
      $i++;
    }

    return Excel::download(new UsersExport($dataExport), 'thuysan.xlsx');
  }

  public function export()
  {
    $sale = new SaleController();
    $req = new Request();
    $req['daterange'] = ['01/08/2025', '31/08/2025'];
    // $req['sale'] = '77';
    // $req['typeDate'] = '2';
    // $sales = ['50','74'];

    $list = $sale->getListSalesByPermisson(Auth::user(), $req);
    $list->whereNull('id_order_new');
    $list->whereNull('id_order');
    $list->where('old_customer', 0);
    // $list->where('is_duplicate', 0);
    $list->where('group_id', '11');
    // $list->paginate(300, ['*'], 'page', 3);
    // $list->whereIn('assign_user', $sales);
    // dd($list->get());
    $dataExport[] = [
      'STT', 'Ngày nhận', 'Số điện thoại', 'Tên khách',
    ];

    $i = 1;
    foreach ($list->get() as $data) {

      // $tnCan = $data->TN_can;
      // if ($data->listHistory) {
      //   foreach ($data->listHistory as $his) {
      //     $tnCan .= date_format($his->created_at,"d-m-Y ") . ': ' . $his->note . ', ';
      //   }

      // }
      // dd($data->user->real_name);
      $dataExport[] = [
        $i,
        date_format($data->created_at,"d-m-Y "),
        $data->phone,
        $data->full_name,
      ];
      $i++;
    }

    return Excel::download(new UsersExport($dataExport), 'minhanh.xlsx');
  }
  
  public function wakeUp()
  {
    $listSc = SaleCare::whereNotNull('result_call')
      ->whereNotNull('type_TN')
      ->where('result_call', '!=', 0)
      ->where('result_call', '!=', -1)
      ->where('has_TN', 1)
      ->where('created_at', '>' , '2025-07-01')
      // ->limit(1000)
      // ->where('id', '74390')
      ->orderBy('id', 'DESC')
      ->get();

    // dd($listSc);
    foreach ($listSc as $sc) {

      $call = $sc->call;
      if (!isset($call->time) && $call->time !== '') {
        // Not empty (0 is allowed here)
        continue;
      }

      $time = $call->time;
      $updatedAt  = $sc->time_update_TN;
      $isRunjob   = $sc->is_runjob;
      $saleAssign   = $sc->user->real_name;

      if (!$sc->user->status || !$sc->user->is_receive_data) {
        continue;
      }

      if (!$call || !isset($time) || !$updatedAt || $isRunjob || !$saleAssign) {
        continue;
      }
      
      //cộng ngày update và time cuộc gọi
      if ($sc->time_wakeup_TN) {
        $newDate = strtotime($sc->time_wakeup_TN);
      } else {
        $newDate = strtotime("+$time hours", strtotime($updatedAt));
      }

      if ($newDate <= time()) {
        $nextTN = $call->thenCall;
        if (!$nextTN) {
          continue;
        }

        //set lần gọi tiếp theo
        if ($sc->type_TN != $nextTN->id) {
          $sc->result_call = 0;
        }

        // 24 id: nhắc lại
        if ($nextTN->id != 24) {
          $sc->type_TN = $nextTN->id;
        }

        $sc->has_TN = 0;
        $sc->is_runjob = 1;
        $sc->save();
      }
    }
  }

  public function fix()
  {
    $from = date('2024-07-01');
    $to = date('2024-07-31');
    // $list = Orders::whereNotExists(function ($query) {
    //   $query->select(\DB::raw('*'))
    //       ->from('sale_care')
    //       ->where('sale_care.id', 'orders.sale_care')
    //       ->where('old_customer', 0)
    //       ;
    //   })
    //   ->where('status', 3)
    //   ->whereBetween('created_at', [$from, $to])
    //   ->get();

    $list = \DB::select("SELECT *
FROM   orders
WHERE  NOT EXISTS
  (SELECT *
   FROM   sale_care
   WHERE  
   sale_care.id = orders.sale_care and sale_care.old_customer = 0 
   
   ) AND orders.created_at BETWEEN '2024-07-01' 
                     AND '2024-07-31 23:59:59.993' ORDER BY `id` ASC;");


      // dd($list);
    // echo "<pre>";
    // print_r($list);
    // echo "</pre>";
    //   die();
      // 
    foreach ($list as $item) {
      // dd($item->id);
      $saleCare = SaleCare::
        where('phone', 'like', '%' . $item->phone . '%')
        ->where('old_customer', 0)
        ->first();

        // dd('hi');
        // dd($saleCare);
      // trường hợp có data TN nhưng chưa map => update map
      if (!$saleCare) {
        echo $item->phone . "<br>";
        $sale = new SaleController();
        $data = [
          'page_link' => '',
          'page_name' => '',
          'sex'       => 0,
          'old_customer' => 0,
          'address'   => $item->address,
          'messages'  => '',
          'name'      => $item->name,
          'phone'     => $item->phone,
          'page_id'   => '',
          'text'      => '',
          // 'chat_id'   => $chatId,
          'm_id'      => '',
          'assgin'    => $item->assign_user,
          'is_duplicate' => 0,
          'id_order_new' => $item->id,
          'created_at'  => $item->created_at
        ];

        $request = new \Illuminate\Http\Request();
        $request->replace($data);
        $sale->save($request);

      } else {
        echo $item->phone . "<br>";
        $order = Orders::find($item->id);
        if ($order) {
          $order->sale_care = $saleCare->id;
          $order->save();
        }
       
      }
      // dd($saleCare);
      //trường hợp có đơn hàng nhưng chưa có data TN => create data và map
    }
        // dd($list);
    
  }

  public function exportTaxV2()
  {
    $time = ['31/08/2025', '06/09/2025'];
    $timeBegin  = str_replace('/', '-', $time[0]);
    $timeEnd    = str_replace('/', '-', $time[1]);
    $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
    $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

    $list = Orders::select('orders.*')->join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
      ->join('sale_care', 'sale_care.id', '=', 'orders.sale_care')
      ->where('shipping_order.vendor_ship', 'GHTK')
      ->where('orders.status', 3)
      ->whereDate('orders.created_at', '>=', $dateBegin)
      ->whereDate('orders.created_at', '<=', $dateEnd)
      ->where('sale_care.group_id', '!=', 11)
      // ->where('orders.id', '18341')
      ->orderBy('orders.id', 'desc');

    $dataExport[] = [
      'Số thứ tự hóa đơn (*)' , 'Ngày hóa đơn', 'Tên đơn vị mua hàng', 'Mã khách hàng', 'Địa chỉ', 'Mã số thuế', 'Người mua hàng',
      'Email', 'Hình thức thanh toán', 'Loại tiền', 'Tỷ giá', 'Tỷ lệ CK(%)', 'Tiền CK', 'Tên hàng hóa/dịch vụ (*)', 'Mã hàng', 
      'ĐVT', 'Số lượng', 'Đơn giá', 'Tỷ lệ CK (%)', 'Tiền CK', '% thuế GTGT', 'Tiền thuế GTGT', 'Thành tiền(*)'
    ];

    $i = 1;
    $orderTmp = [];
    $list = $list->get();

    foreach ($list as $data) {
      $orderTmp[] = $data->id;
      $listProduct = json_decode($data->id_product,true);

      /**
       * 1/ 1 Đạm tôm 20l
       *    3kg humic
       * 2/ 1 Đạm tôm 20l + 3kg humic
       * 3/ 1 Đạm tôm 20l + 3kg humic
       *    1kg humic
       */

       //trường hợp đơn chỉ cho 1 sp
      $percenTax = '5';
      $totalGTGT = '';
      if (count($listProduct) == 1) {
        $item = $listProduct[0];
        $product = getProductByIdHelper($item['id']);
        
        $total = $data->total;
        if (!$product) {
          continue;
        }

        $productName = ($product->tax_name) ? $product->tax_name : $product->name;

        $k = $i;

        //check trường hợp sản phẩm cb và sản phẩm lẻ
        // có dấu + là sản phẩm combo
        if (strpos($productName, '+') !== false) {
          $productName = $product->name;
          $tmp = [];
          if (strpos($productName, '3 xô tricho 10kg tặng 1 xô tricho 10kg') !== false) {
            $productName = $this->parseProductComboTricho($productName);
          }

          $items = $this->parseProductString($productName);
          $productTmp = [];
          $l = 0;
          foreach ($items as $key => $val)
          {
            $list = $this->listProductTmp();
            // if ($key == 'xô tricho 10kg tặng 1 xô tricho 10kg') {
            // }
            if (!isset($list[$key])) {
              continue;
            }

            $productTmp = $list[$key];
            $total = 0;

            if (!$productTmp) {
              continue;
            }

            $totalOrder = $data->total;
            $productPrice = $productTmp['price'];

            $qty = $item['val'];
            $qty = $val * $qty;
    
            if (strpos($productTmp['real_name'], "Hàng tặng") !== false ) {
              $percenTax = '../..';
              $totalGTGT = '../..';
            } else {
              /* tổng tiền bao gồm VAT 5%: 3.150.000
                số lượng: 2 sản phẩm
                thuế VAT: 5%
                b1: tổng tiền chưa VAT = 3150000/ 1.05 = 3000000 (3tr)
                b2: tính giá chưa VAT mỗi sp: 3tr /2 = 1tr5
              */
               
              $taxBeforeTotal = $totalOrder / 1.05;
              $taxbeforeProduct = $taxBeforeTotal / $qty;
              $productPrice = $taxbeforeProduct;
              $totalGTGT = $totalOrder - $taxBeforeTotal;
              $total = $totalOrder;
            }

            if ($l == 0) {
               $total = $totalOrder;
            }
            $l++;

            if ($k != $i) {
              $tmp = [
                '',//Số thứ tự hóa đơn (*)
                '', // Ngày hóa đơn
                '',// Tên đơn vị mua hàng
                '',// Mã khách hàng
                '',// Địa chỉ
                '',// Mã số thuế
                '',// Người mua hàng
                '',// Email
                '',// Hình thức thanh toán
                '',// Loại tiền
                '',// Tỷ giá
                '',// Tỷ lệ CK(%)
                '',// Tiền CK
                $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                '',// Mã hàng
                $productTmp['unit'],// 'ĐVT',
                $qty,//  'Số lượng', 
                $productTmp['price'],//  'Đơn giá', 
                '',//  'Tỷ lệ CK (%)', 
                '',//  'Tiền CK',
                $percenTax, // '% thuế GTGT',
                $totalGTGT, //  'Tiền thuế GTGT',
                $total,   // 'Thành tiền(*)'
              ];
            } else {
              $tmp = [
                $i,//Số thứ tự hóa đơn (*)
                date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
                '',// Tên đơn vị mua hàng
                '',// Mã khách hàng
                $data->address,// Địa chỉ
                '',// Mã số thuế
                $data->name,// Người mua hàng
                '',// Email
                '',// Hình thức thanh toán
                '',// Loại tiền
                '',// Tỷ giá
                '',// Tỷ lệ CK(%)
                '',// Tiền CK
                $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                '',// Mã hàng
                $productTmp['unit'],// 'ĐVT',
                $qty,//  'Số lượng', 
                $productPrice,//  'Đơn giá', 
                '',//  'Tỷ lệ CK (%)', 
                '',//  'Tiền CK',
                $percenTax, // '% thuế GTGT',
                $totalGTGT, //  'Tiền thuế GTGT',
                $total,   // 'Thành tiền(*)'
              ];
            }
            $dataExport[] = $tmp;
            $k++;
          }
        } else {

          $totalBefore = $product->price;
          $productName = ($product->tax_name) ? $product->tax_name : $product->name;
          if ($product->id == 83) {
            $variantId = $item['variantId'];
            $variant = HelperProduct::getProductVariantById($variantId);
            $weight = $variant->weight;

            if (!isset($variant->weight)) {
              $productName .= ' 5kg';
            } else {
              $weight = $variant->weight;
              if ($weight == 5000.0) {
                $productName .= ' 5kg';
              } else {
                $productName .= ' 20kg';
              }
            }
          }
          if (strpos($productName, "Hàng tặng") !== false ) {
            $percenTax = '../..';
            $totalGTGT = '../..';
          } else {
            $qty = $data->qty;
            $totalOrder = $total;
            $totalBefore = $totalOrder / 1.05;
            $taxbeforeProduct = $totalBefore / $qty;
            $productPrice = $taxbeforeProduct;
            $totalGTGT = $totalOrder - $totalBefore;
            $total = $totalOrder;
          }
          // 38095
          // 76190

          // }
          if ($k != $i) {
            $tmp = [
              '',//Số thứ tự hóa đơn (*)
              '', // Ngày hóa đơn
              '',// Tên đơn vị mua hàng
              '',// Mã khách hàng
              '',// Địa chỉ
              '',// Mã số thuế
              '',// Người mua hàng
              '',// Email
              '',// Hình thức thanh toán
              '',// Loại tiền
              '',// Tỷ giá
              '',// Tỷ lệ CK(%)
              '',// Tiền CK
              $productName,// Tên hàng hóa/dịch vụ (*)
              '',// Mã hàng
              $product->unit,// 'ĐVT',
              $item->val,//  'Số lượng', 
              $productPrice,//  'Đơn giá', 
              '',//  'Tỷ lệ CK (%)', 
              '',//  'Tiền CK',
              $percenTax, // '% thuế GTGT',
              $totalGTGT, //  'Tiền thuế GTGT',
              $total,   // 'Thành tiền(*)'
            ];  
          } else {
            $tmp = [
            $i,//Số thứ tự hóa đơn (*)
            date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
            '',// Tên đơn vị mua hàng
              '',// Mã khách hàng
              $data->address,// Địa chỉ
              '',// Mã số thuế
              $data->name,// Người mua hàng
              '',// Email
              '',// Hình thức thanh toán
              '',// Loại tiền
              '',// Tỷ giá
              '',// Tỷ lệ CK(%)
              '',// Tiền CK
              $productName,// Tên hàng hóa/dịch vụ (*)
              '',// Mã hàng
              $product->unit,// 'ĐVT',
              $item['val'],//  'Số lượng', 
              $productPrice,//  'Đơn giá', 
              '',//  'Tỷ lệ CK (%)', 
              '',//  'Tiền CK',
              $percenTax, // '% thuế GTGT',
              $totalGTGT, //  'Tiền thuế GTGT',
              $total,   // 'Thành tiền(*)'
            ];
          }
          
          $dataExport[] = $tmp;
          $k++;
        }

        /** số tổng sản phẩm lớn hơn 1 */
      } else {
        $j = $i;
        $percenTax = '5';
        $totalGTGT = '';

        $qtyNPK = 0;
        $isNPK = false;
        // dd($listProduct);
        $countVoucher = 1;
        $voucher = false;
        foreach ($listProduct as $key => $item) {
     
          // if ($item['variantId'] == 8) {
          //   continue;
          // }

          // echo $item['variantId'];
          $product = getProductByIdHelper($item['id']);
          $productName = ($product->tax_name) ? $product->tax_name : $product->name;
          $total = 0;
          $tmp = [];
          
          if (!$product) {
            continue;
          }
          if ($countVoucher % 2 == 0) {
            $voucher = true;
          }
          
          // if ($isNPK) {
          //   continue;
          // }
         
          // //npk       
          // if ($product->id == 83) {
          //   $isNPK = true;
          // } 

          if (strpos($productName, '+') !== false) {
            $productName = $product->name;
            if (strpos($productName, '3 xô tricho 10kg tặng 1 xô tricho 10kg') !== false ) {
              $productName = $this->parseProductComboTricho($productName);
            }

            $items = $this->parseProductString($productName);
            $productTmp = [];
            $l = 0;
            $percenTax = '5';
            $totalGTGT = '';
            foreach ($items as $key => $val)
            {
              $list = $this->listProductTmp();
              $productTmp = $list[$key];
              $total = 0;
              $totalOrder = $data->total;
              
              if (!$productTmp) {
                continue;
              }
              $productPrice = $productTmp['price'];
              $qty = $item['val'];
              $qty = $val * $qty;
              // if (strpos($productTmp['real_name'], "Dung dịch đạm hữu cơ") !== false || strpos($productTmp['real_name'], "tôm") !== false) {
              //   $percenTax = '5';

                /* tổng tiền bao gồm VAT 5%: 3.150.000
                  số lượng: 2 sản phẩm
                  thuế VAT: 5%
                  b1: tổng tiền chưa VAT = 3150000/ 1.05 = 3000000 (3tr)
                  b2: tính giá chưa VAT mỗi sp: 3tr /2 = 1tr5
                */

              if (strpos($productTmp['real_name'], "Hàng tặng") !== false ) {
                $percenTax = '../..';
                $totalGTGT = '../..';
              } else {
                $taxBeforeTotal = $totalOrder / 1.05;
                $taxbeforeProduct = $taxBeforeTotal / $qty;
                $productPrice = $taxbeforeProduct;
                $totalGTGT = $totalOrder - $taxBeforeTotal;
                $total = $totalOrder;
              }
              // } 

              if ($l == 0) {
                $total = $totalOrder;
              }
              $l++;

              if ($j != $i) {
                $tmp = [
                  '',//Số thứ tự hóa đơn (*)
                  '', // Ngày hóa đơn
                  '',// Tên đơn vị mua hàng
                  '',// Mã khách hàng
                  '',// Địa chỉ
                  '',// Mã số thuế
                  '',// Người mua hàng
                  '',// Email
                  '',// Hình thức thanh toán
                  '',// Loại tiền
                  '',// Tỷ giá
                  '',// Tỷ lệ CK(%)
                  '',// Tiền CK
                  $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                  '',// Mã hàng
                  $productTmp['unit'],// 'ĐVT',
                  $qty,//  'Số lượng', 
                  $productPrice,//  'Đơn giá', 
                  '',//  'Tỷ lệ CK (%)', 
                  '',//  'Tiền CK',
                  $percenTax, // '% thuế GTGT',
                  $totalGTGT, //  'Tiền thuế GTGT',
                  $total,   // 'Thành tiền(*)'
                ];
              } else {
                $tmp = [
                  $i,//Số thứ tự hóa đơn (*)
                  date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
                  '',// Tên đơn vị mua hàng
                  '',// Mã khách hàng
                  $data->address,// Địa chỉ
                  '',// Mã số thuế
                  $data->name,// Người mua hàng
                  '',// Email
                  '',// Hình thức thanh toán
                  '',// Loại tiền
                  '',// Tỷ giá
                  '',// Tỷ lệ CK(%)
                  '',// Tiền CK
                  $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                  '',// Mã hàng
                  $productTmp['unit'],// 'ĐVT',
                  $qty,//  'Số lượng', 
                  $productPrice,//  'Đơn giá', 
                  '',//  'Tỷ lệ CK (%)', 
                  '',//  'Tiền CK',
                  $percenTax, // '% thuế GTGT',
                  $totalGTGT, //  'Tiền thuế GTGT',
                  $total,   // 'Thành tiền(*)'
                ];
              }
  
              $dataExport[] = $tmp;
              $j++;
            }  
          } else {

              $totalOrder = $data->total;
              $productPrice = $product->price;
              $qty = $item['val'];
              // dd($item);
              $percenTax = '5';
              $totalGTGT = '';
              
              $productName = ($product->tax_name) ? $product->tax_name : $product->name;
              if ($product->id == 83) {
                $variantId = $item['variantId'];
                $variant = HelperProduct::getProductVariantById($variantId);
                
                if (!isset($variant->weight)) {
                  $productName .= ' 5kg';
                } else {
                  $weight = $variant->weight;
                if ($weight == 5000.0) {
                  $productName .= ' 5kg';
                } else {
                  $productName .= ' 20kg';
                }
                }
              }

              if ($voucher && strpos($productName, "Hàng tặng") == false) {
                $productName .= " (Hàng tặng không thu tiền)";
              }

              if (strpos($productName, "Hàng tặng") !== false) {
                $percenTax = '../..';
                $totalGTGT = '../..';
              } else {
                $taxBeforeTotal = $totalOrder / 1.05;
                $taxbeforeProduct = $taxBeforeTotal / $qty;
                $productPrice = $taxbeforeProduct;
                $totalGTGT = $totalOrder - $taxBeforeTotal;
                $total = $totalOrder;
              }
              // } else 
              if (strpos($productName, "Áo mưa (hàng tặng không bán)") !== false ) {
                $percenTax = '../..';
                $total = '0';
                $totalGTGT = '../..';
                $productPrice = 63720;
              }
            
              if ($j != $i) {
                $tmp = ['', '', '', '', '', '',  '', '','', '', '','', '', $productName,'', $product->unit, $qty, $productPrice,
                  '', '', $percenTax, $totalGTGT, $total,   
                ];  
              } else {
                  // dd($product->name);
                $tmp = [
                $i,//Số thứ tự hóa đơn (*)
                date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
                '',// Tên đơn vị mua hàng
                  '',// Mã khách hàng
                  $data->address,// Địa chỉ
                  '',// Mã số thuế
                  $data->name,// Người mua hàng
                  '',// Email
                  '',// Hình thức thanh toán
                  '',// Loại tiền
                  '',// Tỷ giá
                  '',// Tỷ lệ CK(%)
                  '',// Tiền CK
                  $productName,// Tên hàng hóa/dịch vụ (*)
                  '',// Mã hàng
                  $product->unit,// 'ĐVT',
                  $qty,//  'Số lượng', 
                  $productPrice,//  'Đơn giá', 
                  '',//  'Tỷ lệ CK (%)', 
                  '',//  'Tiền CK',
                  $percenTax, // '% thuế GTGT',
                  $totalGTGT, //  'Tiền thuế GTGT',
                  $total,   // 'Thành tiền(*)'
                ];
              }
              
              $dataExport[] = $tmp;
              $j++;
          }

          $countVoucher++;
        }
      }
      $i++;
    }
    
    // dd($dataExport);
    return Excel::download(new UsersExport($dataExport), 'GHTK-NEW-(31-08)-(06-09)-2025.xlsx');
  }

    public function parseProductString($str) 
  {
    // dd($str);
    $products = [];
    
    // Tách ra theo dấu +
    $parts = preg_split('/\s*\+\s*/', $str);

    // Kiểm tra có xN ở cuối không (hệ số nhân)
    $multi = 1;
    if (preg_match('/x(\d+)$/i', trim($str), $m)) {
        $multi = (int) $m[1];
    }

    foreach ($parts as $item) {
        // Loại bỏ hệ số nhân cuối mỗi item nếu có
        $cleanItem = preg_replace('/x\d+$/i', '', trim($item));

        // Lấy số lượng và tên sản phẩm
        if (preg_match('/^(\d+)(kg|l|)?\s*(.+)$/iu', $cleanItem, $matches)) {
            $qty = (int) $matches[1];
            $name = strtolower(trim($matches[3])); // chuẩn hóa tên sản phẩm
            $totalQty = $qty * $multi;

            // Cộng dồn nếu sản phẩm trùng
            if (isset($products[$name])) {
                $products[$name] += $totalQty;
            } else {
                $products[$name] = $totalQty;
            }
        }
    }

    $newProduct = [];
    // foreach ($products as $k => $product) {
    //   echo $k . '<br>';
    //   if ($k == '')
    // }
    // dd($products);
    return $products;
  }

    public function listProductTmp()
  {
    $list = [
      
      'xô tricho 10kg' => [
        'price' => 1440000,
        'unit' => 'Xô',
        'real_name' => 'Phân bón VL Vinakom Bomix - Tricho Bacillus Xô 10Kg'
      ],
      'xô tricho' => [
        'price' => 1440000,
        'unit' => 'Xô',
        'real_name' => 'Phân bón VL Vinakom Bomix - Tricho Bacillus Xô 10Kg'
      ],
      'tricho 10kg' => [
        'price' => 1440000,
        'unit' => 'Xô',
        'real_name' => 'Phân bón VL Vinakom Bomix - Tricho Bacillus Xô 10Kg'
      ],
      'Đạm tôm 20l' => [
        'price' => 1500000,
        'unit' => 'Can',
        'real_name' => 'Dung dịch đạm hữu cơ 20l'
      ],
      'đạm tôm 20l' => [
        'price' => 1500000,
        'unit' => 'Can',
        'real_name' => 'Dung dịch đạm hữu cơ 20l'
      ],
      
      'humic' => [
        'price' => 120000,
        'unit' => 'Gói',
        'real_name' => 'Phân bón Ogranic AB03- Humic Acid Powder Usa 1Kg (Hàng tặng không thu tiền)'
      ],
      'Siêu lớn trái' => [
        'price' => 120000,
        'unit' => 'Chai',
        'real_name' => 'Phân bón AB02 - Agrium Siêu Lớn Trái 500ml (Hàng tặng không thu tiền)'
      ],
      'siêu lớn trái' => [
        'price' => 120000,
        'unit' => 'Chai',
        'real_name' => 'Phân bón AB02 - Agrium Siêu Lớn Trái 500ml (Hàng tặng không thu tiền)'
      ],
      
      'siêu kích hoa' => [
        'price' => 120000,
        'unit' => 'Chai',
        'real_name' => 'Phân bón AB02 - Agrium Siêu Kích Hoa 500ml (Hàng tặng không thu tiền)'
      ],
      'vọt đọt' => [
        'price' => 120000,
        'unit' => 'Chai',
        'real_name' => 'Phân bón AB02 - Agrium Siêu Vọt Đọt 500ml (Hàng tặng không thu tiền)'
      ],
      'canxibo' => [
        'price' => 120000,
        'unit' => 'Chai',
        'real_name' => 'Phân bón AB02 - Agrium Siêu Canxibo 500ml (Hàng tặng không thu tiền)'
      ],
      'Canxibo 500ml' => [
        'price' => 120000,
        'unit' => 'Chai',
        'real_name' => 'Phân bón AB02 - Agrium Siêu Canxibo 500ml (Hàng tặng không thu tiền)'
      ],
      'A plus' => [
        'price' => 1350000,
        'unit' => 'Can',
        'real_name' => 'Phân bón Agroplus organic E can 5kg'
      ],
      'a plus' => [
        'price' => 1350000,
        'unit' => 'Can',
        'real_name' => 'Phân bón Agroplus organic E can 5kg'
      ],
      'xô aplus' => [
        'price' => 1350000,
        'unit' => 'Can',
        'real_name' => 'Phân bón Agroplus organic E can 5kg'
      ],
    ];
    return $list;
  }

    public function parseProductComboTrichoAplus($productName)
  {

    $arr = explode("+", $productName);
    // dd($arr);
    $newName = '';
    foreach ($arr as $el) {
      if ($newName != '') {
        $newName .= ' + ';
      }

      if (strpos($el, '3 xô tricho 10kg tặng 1 xô tricho 10kg') > -1) {
        $name = '4 xô tricho 10kg';
        $newName .= $name;
      } else {
        $newName .= $el;
      }

      // if (strpos($el, 'xô tricho 10kg tặng 1 aplus') > -1) {
      //   $name = '1 xô tricho 10kg + 1  aplus';
      //   $newName .= $name;
      // } else {
      //   $newName .= $el;
      // }
      
    }
    
    return $newName;
  }

    public function parseProductComboTricho($productName)
  {
    $arr = explode("+", $productName);
    // dd($arr);
    $newName = '';
    foreach ($arr as $el) {
      if ($newName != '') {
        $newName .= ' + ';
      }

      if (strpos($el, '3 xô tricho 10kg tặng 1 xô tricho 10kg') > -1) {
        $name = '4 xô tricho 10kg';
        $newName .= $name;
      } else {
        $newName .= $el;
      }

      // if (strpos($el, 'xô tricho 10kg tặng 1 aplus') > -1) {
      //   $name = '1 xô tricho 10kg + 1  aplus';
      //   $newName .= $name;
      // } else {
      //   $newName .= $el;
      // }
      
    }
    
    return $newName;
  }
}




