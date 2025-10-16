<?php
/**
 * TEST VIETTELPOST CREATE ORDER API
 * File này để test API ViettelPost trước khi tích hợp vào hệ thống
 * 
 * Cách sử dụng:
 * 1. Thay YOUR_VIETTELPOST_TOKEN_HERE bằng token thật
 * 2. Chạy: php test_viettelpost_create_order.php
 */

// Dữ liệu mẫu để tạo đơn hàng
$data = [
    "ORDER_NUMBER" => "TEST_KHO_" . time(),
    "GROUPADDRESS_ID" => 1,
    "CUS_ID" => 0,
    
    // Thông tin người gửi (SENDER) - Địa chỉ cố định: Phân bón MN
    "SENDER_FULLNAME" => "Phân bón MN",
    "SENDER_ADDRESS" => "19/1c Nguyễn Thị Chiên",
    "SENDER_PHONE" => "0986987791",
    "SENDER_EMAIL" => "",
    "SENDER_WARD" => 691,        // Xã Tân An Hội (WARDS_ID từ ViettelPost)
    "SENDER_DISTRICT" => 36,     // Huyện Củ Chi (DISTRICT_ID từ ViettelPost)
    "SENDER_PROVINCE" => 2,      // TP. Hồ Chí Minh (PROVINCE_ID từ ViettelPost)
    
    // Thông tin người nhận (RECEIVER) - Ví dụ: Quận 1, TP.HCM
    "RECEIVER_FULLNAME" => "Nguyễn Văn A",
    "RECEIVER_ADDRESS" => "123 Đường Lê Lợi, Phường Bến Nghé",
    "RECEIVER_PHONE" => "0912345678",
    "RECEIVER_EMAIL" => "",
    "RECEIVER_WARD" => 26732,    // Phường Bến Nghé (VD - cần tìm ID chính xác)
    "RECEIVER_DISTRICT" => 19,   // Quận 1 (VD - cần tìm ID chính xác)
    "RECEIVER_PROVINCE" => 2,    // TP. Hồ Chí Minh
    
    // Thông tin sản phẩm
    "PRODUCT_NAME" => "Phân bón NPK 2kg",
    "PRODUCT_DESCRIPTION" => "Phân bón NPK cao cấp",
    "PRODUCT_QUANTITY" => 1,
    "PRODUCT_PRICE" => 200000,
    "PRODUCT_WEIGHT" => 2000,    // Gram
    "PRODUCT_LENGTH" => 20,
    "PRODUCT_WIDTH" => 20,
    "PRODUCT_HEIGHT" => 20,
    "PRODUCT_TYPE" => "HH",      // HH: Hàng hóa, TL: Tài liệu
    
    // Thông tin dịch vụ
    "ORDER_PAYMENT" => 1,        // 1: Người nhận trả phí, 2: Người gửi trả phí
    "ORDER_SERVICE" => "VCN",    // VCN: Chuyển phát nhanh
    "ORDER_SERVICE_ADD" => "",
    "ORDER_VOUCHER" => "",
    "ORDER_NOTE" => "Ghi chú test đơn hàng",
    
    // Thông tin tiền
    "MONEY_COLLECTION" => 200000,  // COD
    "MONEY_TOTALFEE" => 0,
    "MONEY_FEECOD" => 0,
    "MONEY_FEEVAS" => 0,
    "MONEY_FEEINSURRANCE" => 0,
    "MONEY_FEE" => 0,
    "MONEY_FEEOTHER" => 0,
    "MONEY_TOTALVAT" => 0,
    "MONEY_TOTAL" => 0,
    
    // Danh sách sản phẩm chi tiết
    "LIST_ITEM" => [
        [
            "PRODUCT_NAME" => "Phân bón NPK 2kg",
            "PRODUCT_PRICE" => 200000,
            "PRODUCT_WEIGHT" => 2000,
            "PRODUCT_QUANTITY" => 1
        ]
    ]
];

// Token ViettelPost (Cần thay bằng token thật)
$token = 'YOUR_VIETTELPOST_TOKEN_HERE';
$endpoint = "https://partner.viettelpost.vn/v2/order/createOrder";

// Hiển thị request
echo "=== REQUEST ===\n";
echo "Endpoint: " . $endpoint . "\n";
echo "Token: " . $token . "\n\n";
echo "Data:\n";
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Gửi request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Token: ' . $token,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Hiển thị response
echo "=== RESPONSE ===\n";
echo "HTTP Code: " . $httpCode . "\n\n";
echo "Response:\n";
$responseData = json_decode($response, true);
echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Phân tích kết quả
echo "=== PHÂN TÍCH ===\n";
if ($httpCode == 200 && isset($responseData['status']) && $responseData['status'] == 200) {
    echo "✅ TẠO ĐƠN THÀNH CÔNG!\n";
    echo "Mã đơn hàng: " . ($responseData['data']['ORDER_NUMBER'] ?? 'N/A') . "\n";
} else {
    echo "❌ TẠO ĐƠN THẤT BẠI!\n";
    echo "Lỗi: " . ($responseData['message'] ?? 'Unknown error') . "\n\n";
    
    // Gợi ý fix lỗi
    if (isset($responseData['message'])) {
        $errorMsg = $responseData['message'];
        
        if (strpos($errorMsg, 'RECEIVER_DISTRICT') !== false) {
            echo "\n🔍 LỖI RECEIVER_DISTRICT:\n";
            echo "- Kiểm tra RECEIVER_DISTRICT = " . $data['RECEIVER_DISTRICT'] . " có tồn tại trong viettel_districts.json\n";
            echo "- Kiểm tra RECEIVER_DISTRICT có thuộc RECEIVER_PROVINCE = " . $data['RECEIVER_PROVINCE'] . "\n";
        }
        
        if (strpos($errorMsg, 'RECEIVER_PROVINCE') !== false) {
            echo "\n🔍 LỖI RECEIVER_PROVINCE:\n";
            echo "- Kiểm tra RECEIVER_PROVINCE = " . $data['RECEIVER_PROVINCE'] . " có tồn tại trong viettel_provinces.json\n";
        }
        
        if (strpos($errorMsg, 'SENDER_DISTRICT') !== false) {
            echo "\n🔍 LỖI SENDER_DISTRICT:\n";
            echo "- Kiểm tra SENDER_DISTRICT = " . $data['SENDER_DISTRICT'] . " có tồn tại trong viettel_districts.json\n";
            echo "- Kiểm tra SENDER_DISTRICT có thuộc SENDER_PROVINCE = " . $data['SENDER_PROVINCE'] . "\n";
        }
        
        if (strpos($errorMsg, 'SENDER_PROVINCE') !== false) {
            echo "\n🔍 LỖI SENDER_PROVINCE:\n";
            echo "- Kiểm tra SENDER_PROVINCE = " . $data['SENDER_PROVINCE'] . " có tồn tại trong viettel_provinces.json\n";
        }
        
        if (strpos($errorMsg, 'Token') !== false || strpos($errorMsg, 'Unauthorized') !== false) {
            echo "\n🔍 LỖI TOKEN:\n";
            echo "- Token không hợp lệ hoặc đã hết hạn\n";
            echo "- Liên hệ ViettelPost để lấy token mới\n";
        }
        
        if (strpos($errorMsg, 'PRODUCT_TYPE') !== false) {
            echo "\n🔍 LỖI PRODUCT_TYPE:\n";
            echo "- Thiếu trường PRODUCT_TYPE trong request\n";
            echo "- PRODUCT_TYPE = 'HH' (Hàng hóa) hoặc 'TL' (Tài liệu)\n";
            echo "- Đã set trong code: PRODUCT_TYPE = " . $data['PRODUCT_TYPE'] . "\n";
        }
    }
}

echo "\n=== HƯỚNG DẪN ===\n";
echo "1. Đọc file HUONG_DAN_VIETTELPOST.md để hiểu rõ cấu trúc JSON\n";
echo "2. Kiểm tra ID trong các file JSON:\n";
echo "   - public/json/viettel_provinces.json\n";
echo "   - public/json/viettel_districts.json\n";
echo "   - public/json/viettel_wards.json\n";
echo "3. Đảm bảo quan hệ: Ward ⊂ District ⊂ Province\n";
echo "4. Sử dụng token ViettelPost hợp lệ\n";

