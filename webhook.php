<?php

    $myVertifyToken = 'dat1shot';
    
    $challenge = $_REQUEST['hub_challenge'];
    $verifyToken = $_REQUEST['hub_verify_token'];
    
    if ($myVertifyToken === $verifyToken) {
        echo $challenge;
        exit;
    }
    
    $PAGE_ACCESS_TOKEN = 'EAAKjmAaaUIwBO06S4Md5JCZBhPZAzTen8hpz1LYEqYIdIdo6vHV06Y7DhlZAeDj5lFjIP3S7d0j6LUWASUOFhOBjaDLxMwCdiwUA9gSzqqR2tlO82EWMaGUbxKsRTh1qIBEevZB0ilMTCpZBN9uBOowuQZAlz28fzLyRXOKpnmcWVYPs44tyJ5eZCFfKJPZACZAt1vgZDZD';
   
  
// Xử lý sự kiện từ Webhook
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy nội dung JSON từ request
    $input = json_decode(file_get_contents('php://input'), true);
    $response = file_get_contents("php://input");
    file_put_contents("text.txt", $response);
    
   
    
    // Kiểm tra đối tượng nhận có phải là trang
    if ($input['object'] === 'page') {
        foreach ($input['entry'] as $entry) {
            $webhookEvent = $entry['messaging'][0];

            // Lấy ID người gửi
            $senderPsid = $webhookEvent['sender']['id'];

            // Kiểm tra nếu có tin nhắn văn bản
            if (isset($webhookEvent['message']['text'])) {
                $receivedMessage = $webhookEvent['message']['text'];
                // file_put_contents("text.txt",$receivedMessage );
                // Kiểm tra nội dung tin nhắn có chứa số điện thoại không
                $phoneRegex = '/(?:\D|^)(\d{10,15})(?=\D|$)/'; // Biểu thức regex cho số điện thoại
                    file_put_contents("text.txt", $phoneRegex);

                if (preg_match_all($phoneRegex, $receivedMessage, $matches)) {
                    $phoneNumbers = $matches[1]; // Mảng chứa các số điện thoại tìm thấy
                    // Xử lý số điện thoại (lưu trữ, gửi thông báo, v.v.)
                    
                    file_put_contents("text.txt", json_encode($phoneNumbers),  FILE_APPEND);
                    foreach ($phoneNumbers as $phoneNumber) {
                        // Ví dụ: gửi tin nhắn phản hồi với số điện thoại nhận được
                        $response = "Chúng tôi đã nhận được số điện thoại của bạn: $phoneNumber";
                        // sendTextMessage($senderPsid, $response);
                        
                        $mid = $webhookEvent['message']['mid'];
                        $pageId = $entry['id'];
                        $receivedMessage = urlencode($receivedMessage);

                        // file_put_contents("text.txt", $userName, FILE_APPEND);
                        // file_put_contents("text.txt", $phoneNumber, FILE_APPEND);
                        $param = file_get_contents('php://input');
                        $url = "https://kho.phanboncanada.online/webhook-fb?phone=$phoneNumber&receivedMessage=$receivedMessage&mid=$mid&pageId=$pageId";
                        // CallAPI($url, 'GET');
                         file_put_contents("text.txt", $url, FILE_APPEND);
                        $response = file_get_contents($url);
                    }
                } else {
                    // Trả lời khi không tìm thấy số điện thoại
                    $response = $userName ." Không tìm ppp thấy số điện thoại trong tin nhắn của bạn.";
                    // sendTextMessage($senderPsid, $response);
                }
            }
        }
        http_response_code(200);
        echo 'EVENT_RECEIVED';
    } else {
        http_response_code(404);
        echo 'Not Found';
    }
    exit;
}
// Hàm lấy tên người dùng từ Graph API
function getUserName($userId, $access_token) {
    $url = "https://graph.facebook.com/$userId?fields=first_name,last_name&access_token=$access_token";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $user = json_decode($response, true);

    // Trả về tên đầy đủ hoặc chỉ tên riêng
    return $user['last_name'] . ' ' . $user['first_name'];
}

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
    
?>