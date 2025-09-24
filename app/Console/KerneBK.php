<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\SaleCare;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Helpers\Helper;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Log;
use App\Models\Group;
use App\Models\SrcPage;
use App\Models\User;
use DateTime;
use PHPUnit\TextUI\Help;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
      $schedule->call(function() {
        $this->crawlerGroup();
        $this->ghtkToShipping();
        $this->updateStatusOrderGHTK();
        $this->updateStatusOrderGhnV2();
      })->cron('*/5 * * * *');

      $schedule->call(function() {
        $this->wakeUp();
      })->everyMinute();

      $schedule->call(function() {
        $this->updatePrintStatusGHN2();
        $this->updatePrintStatusGHN();
      })->everyMinute();

      $schedule->call(function() {
         $this->testCron();
      })->everyMinute();
    }

    public static function testCron()
    {
    Log::channel('d')->info('Welcome cron');
    }

  /**
   * Register the commands for the application.
   */
  protected function commands(): void
  {
      $this->load(__DIR__.'/Commands');

      require base_path('routes/console.php');
  }

  public function updatePrintStatusGHN2()
  {
    /** orders chưa giao vận và trạng thái usu là đã in */
    $dateBegin  = date('Y-m-d',strtotime("01/08/2025"));
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
      // dd($data);
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

public function wakeUp()
  {
    $listSc = SaleCare::whereNotNull('result_call')
      ->whereNotNull('type_TN')
      ->where('result_call', '!=', 0)
      ->where('result_call', '!=', -1)
      ->where('has_TN', 1)
      ->where('created_at', '>' , '2025-06-01')
      // ->limit(1000)
      // ->where('id', '44520')
      ->orderBy('id', 'DESC')
      ->get();

    // dd($listSc);
     Log::channel('d')->info(json_encode($listSc));
    foreach ($listSc as $sc) {

      $call = $sc->call;
      if (empty($call->time)) {
        continue;
      }

      $time = $call->time;
      $updatedAt  = $sc->time_update_TN;
      $isRunjob   = $sc->is_runjob;
      $saleAssign   = $sc->user->real_name;

      if (!$sc->user->status || !$sc->user->is_receive_data) {
        continue;
      }
      
      if (!$call || !$time || !$updatedAt || $isRunjob || !$saleAssign) {
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
            // // echo 'case 2';
            // if ($assignCSKH) {
            //   $assgin_user = $assignCSKH->id;
            //   //  echo 'case 2.1';
            // } else {
            //   $assgin_user = $order->assign_user;
            //   // echo 'case 2.2';
            // }
             $assgin_user = 50; //Dương Thu
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

  public function crawlerPancake()
  {
    $panCake = Helper::getConfigPanCake();
    if ($panCake->status == 1 && $panCake->page_id != '' && $panCake->token != '') {
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
          $before = strtotime ( '-2 hour' , strtotime ( date("Y/m/d H:i") ) ) ;
          $before = date ( 'Y/m/d H:i' , $before );
          $before = strtotime($before);

          $endpoint = "$endpoint?type=PHONE,DATE:$before+-+$today&access_token=$token";
          $response = Http::withHeaders(['access_token' => $token])->get($endpoint);
    
          if ($response->status() == 200) {
            $content  = json_decode($response->body());
            if ($content->success) {
              $data     = $content->conversations;
              foreach ($data as $item) {
                $recentPhoneNumbers = $item->recent_phone_numbers[0];
                $mId      = $recentPhoneNumbers->m_id;
                $phone    = isset($recentPhoneNumbers) ? $recentPhoneNumbers->phone_number : '';
                $name     = isset($item->customers[0]) ? $item->customers[0]->name : '';
                $messages = isset($recentPhoneNumbers) ? $recentPhoneNumbers->m_content : '';

                $assgin_user = 0;
                $is_duplicate = false;
                $phone = Helper::getCustomPhoneNum($phone);
                $checkSaleCareOld = Helper::checkOrderSaleCarebyPhonePage($phone, $val['id'], $mId, $assgin_user, $is_duplicate);

                if ($name && $checkSaleCareOld) {  
                  if ($assgin_user == 0) {
                    $assignSale = Helper::getAssignSale();
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
                    'chat_id'   => 'id_VUI',
                    'm_id'      => $mId,
                    'assgin'    => $assgin_user,
                    'is_duplicate' => $is_duplicate
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
    }
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
        if ($page->type == 'pc') {
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

    if ( $pIdPan != '' && $token != '' && $namePage != '' && $linkPage != '' && $chatId != '') {

      $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";
      $today    = strtotime(date("Y/m/d H:i"));
      $before   = strtotime ( '-5 hour' , strtotime ( date("Y/m/d H:i") ) ) ;
      $before   = date ( 'Y/m/d H:i' , $before );
      $before   = strtotime($before);

      $endpoint = "$endpoint?type=PHONE,DATE:$before+-+$today&access_token=$token";
      $response = Http::withHeaders(['access_token' => $token])->get($endpoint);

      if ($response->status() == 200) {
        $content  = json_decode($response->body());
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

           
              /** kiểm tra thời gian insert tin nhắn => lâu hơn 3 ngày ko nhận lại */
              $inputTime = strtotime($item->inserted_at);
              $now = time();
              $secondsIn3Days = 3 * 24 * 60 * 60;

              if ($now - $inputTime >= $secondsIn3Days) {
                //   echo "Đã quá 3 ngày";
                  continue;
              } 
              
               if (Helper::isSeeding($phone) || $phone == '0108769765') {
                Log::channel('ladi')->info('Số điện thoại đã nằm trong danh sách spam/seeding kernel.. ' . $phone);
                continue;
              } else if ($checkSaleCareOld) {
                  Log::channel('ladi')->info('Số điện thoại.. ' . $phone);
              }

              if ($name && $checkSaleCareOld) {
                $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldCustomer);
                if (!$assignSale) {
                  continue;
                }

                if ($isOldCustomer == 1) {
                  $chatId = $group->tele_cskh_data;
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
              // return $e;
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
    // $orders = Orders::has('shippingOrder')->whereNotIn('status', [0,3])->get();
    $orders = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
      ->whereNotIn('orders.status', [0,3])
      ->where('shipping_order.vendor_ship', 'GHN')
      ->get('orders.*');
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
            if ($group->is_share_data_cskh && $order->saleCare->old_customer != 1) {
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

}
