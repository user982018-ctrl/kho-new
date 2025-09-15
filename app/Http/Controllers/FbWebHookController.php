<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FbWebHookController extends Controller
{
    // Hàm gửi tin nhắn sử dụng Facebook Send API
    function sendTextMessage($senderPsid, $message)
    {
        global $PAGE_ACCESS_TOKEN;
        $url = 'https://graph.facebook.com/v13.0/me/messages?access_token=' . $PAGE_ACCESS_TOKEN;
    
        $ch = curl_init($url);
    
        $jsonData = [
            'recipient' => ['id' => $senderPsid],
            'message' => ['text' => $message]
        ];
    
        $jsonDataEncoded = json_encode($jsonData);
    
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $result = curl_exec($ch);
        curl_close($ch);
    
        if ($result) {
            echo "Tin nhắn đã được gửi!";
        } else {
            echo "Không thể gửi tin nhắn.";
        }
    }

    public function getUserName($userId, $access_token) {
        // dd($userId);
        $url = "https://graph.facebook.com/$userId?fields=first_name,last_name&access_token=$access_token";
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $user = json_decode($response, true);
        // if ()
        // dd( $user);
        // Trả về tên đầy đủ hoặc chỉ tên riêng
        return $user['last_name'] . ' ' . $user['first_name'];
    }

    public function saveDataWebhookFBV2($group, $pageId, $phone, $name, $mId, $messages, $pageSrc)
    {
        $is_duplicate = $hasOldOrder = $isOldCustomer = $assgin_user = 0;
        $checkSaleCareOld = Helper::checkOrderSaleCarebyPhoneV5($phone, $mId, $is_duplicate, $hasOldOrder);
        $typeCSKH = 1;
        $srcId = $pageSrc->id;
        $group = $pageSrc->group;
        $phone = Helper::getCustomPhoneNum($phone);
        
         if (Helper::isSeeding($phone)) {
                Log::channel('ladi')->info('Số điện thoại đã nằm trong danh sách spam/seeding fb..' . $phone);
                return;
              }
        if ($name && $checkSaleCareOld) {
            $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldCustomer);
            if (!$assignSale) {
              return;
            }

            $chatId = $group->tele_hot_data;
            if ($isOldCustomer == 1) {
              $chatId = $group->tele_cskh_data;
            }

            $assgin_user = $assignSale->id;
            $is_duplicate = ($is_duplicate) ? 1 : 0;
            $sale = new SaleController();
            $data = [
            //   'page_link' => $linkPage,
            //   'page_name' => $namePage,
              'sex'       => 0,
              'old_customer' => $isOldCustomer,
              'address'   => '',
              'messages'  => $messages,
              'name'      => $name,
              'phone'     => $phone,
              'page_id'   => $pageId,
            //   'text'      => 'Page ' . $namePage,
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
    }

    public function saveDataWebhookFB($group, $pageId, $phone, $name, $mId, $messages, $pageSrc)
    {
        $assgin_user = 0;
        $is_duplicate = false;
        $phone = Helper::getCustomPhoneNum($phone);
        $hasOldOrder = 0;
        $checkSaleCareOld = Helper::checkOrderSaleCarebyPhoneV4($phone, $mId, $is_duplicate, $assgin_user, $group, $hasOldOrder);

        $chatId = $group->tele_hot_data;
        $linkPage = $pageSrc->link;
        $srcId = $pageSrc->id;
        $namePage = $pageSrc->name;
        // Log::channel('a')->info('$namePage' . $namePage);
        if ($checkSaleCareOld && $phone != '0335784214') {  
            if ($assgin_user == 0) {
                // dd($group);
                $assignSale = Helper::getAssignSaleByGroup($group);
                if (!$assignSale) {
                    return;
                }
                $assgin_user = $assignSale->id_user;
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
                'page_id'   => $pageId,
                'text'      => 'Page ' . $namePage,
                'chat_id'   => $chatId,
                'm_id'      => $mId,
                'assgin'    => $assgin_user,
                'is_duplicate' => $is_duplicate,
                'group_id'  => $group->id,
                'has_old_order'  => $hasOldOrder,
                'src_id' => $srcId,
            ];

            $request = new \Illuminate\Http\Request();
            $request->replace($data);
            $sale->save($request);
        }
    }


    // Xử lý sự kiện webhook
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->get('hub_verify_token') === 'dat1shot') {
                return response($request->get('hub_challenge'), 200);
            }
            return response('Invalid token', 403);
        }

        // Log message
        // Log::channel('daily')->info('Webhook received: ', $request->all());
        // $data = $request->all();
        // if ($data) {
        //     sleep(5);
        //     $this->callDataPc($data);
        // }
         $input = $request->all();
        // Log::channel('daily')->info('get type received: ', $input);
        
        // $input = json_decode($request->all(), true);
        if ($input['object'] === 'page') {
            //  Log::channel('daily')->info('input '. $input['object'] );
            foreach ($input['entry'] as $entry) {
                // Log::channel('daily')->info('$entry ', $entry );
                $webhookEvent = $entry['messaging'][0];
    Log::channel('a')->info('Webhook $senderPsid: ', $webhookEvent);
                // Lấy ID người gửi
                $senderPsid = $webhookEvent['sender']['id'];
                
                // Kiểm tra nếu có tin nhắn văn bản
                if (isset($webhookEvent['message']['text'])) {
                    $receivedMessage = $webhookEvent['message']['text'];
                    // Kiểm tra nội dung tin nhắn có chứa số điện thoại không
                    $phoneRegex = '/(?:\D|^)(\d{10,15})(?=\D|$)/'; // Biểu thức regex cho số điện thoại
    
                    if (preg_match_all($phoneRegex, $receivedMessage, $matches)) {
                        $phoneNumbers = $matches[1]; // Mảng chứa các số điện thoại tìm thấy
                        // Xử lý số điện thoại (lưu trữ, gửi thông báo, v.v.)
                        
                        // Log::channel('daily')->info('xử lý  $phoneNumber: ' , $phoneNumbers);
                        // file_put_contents("text.txt", json_encode($phoneNumbers),  FILE_APPEND);
                        foreach ($phoneNumbers as $phoneNumber) {
                            // Ví dụ: gửi tin nhắn phản hồi với số điện thoại nhận được
                            $response = "Chúng tôi đã nhận được số điện thoại của bạn: $phoneNumber";
                            // sendTextMessage($senderPsid, $response);
                            
                            $mid = $webhookEvent['message']['mid'];
                            $pageId = $entry['id'];
                            // Log::channel('daily')->info('xử lý  $phoneNumber: ' . $phoneNumber);
                            $dataParam = [
                                'pageId' => $pageId,
                                'phone' => $phoneNumber,
                                'mid' => $mid,
                                'receivedMessage' => $receivedMessage
                            ];
                            
                            sleep(35);
                            $this->callDataPc($dataParam);
                            
                        }
                    } else {
                        // Trả lời khi không tìm thấy số điện thoại
                        $response = " Không tìm ppp thấy số điện thoại trong tin nhắn của bạn.";
                        // sendTextMessage($senderPsid, $response);
                    }
                }
            }
          return response('EVENT_RECEIVED', 200);
        } 
      
    }

    public function callDataPc($data)
    {
        // $data = array (
        //     'phone' => '0973409613',
        //     'receivedMessage' => '0973409613 go',
        //     'mid' => 'm_3RWA8svAbHssJhEYb3IrlRSX13JMTib20xEA6BqKI-0Zsa9a4XJoKC3Qe_llMV-tF_q9LRDNFhNDPZIUraidmQ',
        //     'name' => 'Dat Dinh',
        //     'pageId' => '381180601741468'
        // );

        $pageId =  $data['pageId'];
        $phone =  $data['phone'];
        $phone = Helper::getCustomPhoneNum($phone);
        $mid =  $data['mid'];
        $receivedMessage = $data['receivedMessage'];
        $str  = 'pageId: ' . $pageId . '<br>';
        $str  .= 'phone: ' . $phone . '<br>';
        $str  .= 'mid: ' . $mid . '<br>';
        $str  .= 'receivedMessage: ' . $receivedMessage . '<br>';

        
        $group = Helper::getGroupByPageId($pageId);
             
        if (!$group) {
            Log::channel('a')->info('no group');
            return;
        }
        
        $pageSrc = Helper::getPageSrcByPageId($pageId);
        if (!$pageSrc) {
            Log::channel('a')->info('no pageSrc');
            return;
        }

        $token = $pageSrc->token;
        $endpoint = "https://pancake.vn/api/v1/pages/$pageId/conversations/";
        $endpoint .= "search?q=$phone&access_token=$token";
        $responseJson = file_get_contents($endpoint);
        $response = json_decode($responseJson, true);

        $name = 'Loading';
        if ($response) {
            if (!$response['success'] || !$response['conversations']) {
                $name = 'Loading';
            } else {
                $data = $response['conversations'][0];
                $name = $data['customers'][0]['name'];
                
            }
        }
        if (Helper::isSeeding($phone)) {
                Log::channel('new')->info('Số điện thoại đã nằm trong danh sách spam/seeding fb..' . $phone);
                return;
        }
        // $name= 'check event fb1090';
        $this->saveDataWebhookFBV2($group, $pageId, $phone, $name, $mid, $receivedMessage, $pageSrc);
    }
    
}