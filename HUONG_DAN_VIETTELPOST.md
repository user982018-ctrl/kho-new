# HƯỚNG DẪN TÍCH HỢP API VIETTELPOST

## 1. CẤU TRÚC JSON CHUẨN ĐỂ TẠO ĐơN HÀNG

### JSON Mẫu Đầy Đủ

```json
{
  "ORDER_NUMBER": "KHO_123_1729045678",
  "GROUPADDRESS_ID": 1,
  "CUS_ID": 0,
  "DELIVERY_DATE": "2025-10-16 08:00:00",
  
  "SENDER_FULLNAME": "Phân bón MN",
  "SENDER_ADDRESS": "19/1c Nguyễn Thị Chiên",
  "SENDER_PHONE": "0986987791",
  "SENDER_EMAIL": "",
  "SENDER_WARD": 691,
  "SENDER_DISTRICT": 36,
  "SENDER_PROVINCE": 2,
  "SENDER_LATITUDE": 0,
  "SENDER_LONGITUDE": 0,
  
  "RECEIVER_FULLNAME": "Nguyễn Văn A",
  "RECEIVER_ADDRESS": "123 Đường ABC, Phường XYZ",
  "RECEIVER_PHONE": "0912345678",
  "RECEIVER_EMAIL": "",
  "RECEIVER_WARD": 1,
  "RECEIVER_DISTRICT": 1,
  "RECEIVER_PROVINCE": 1,
  "RECEIVER_LATITUDE": 0,
  "RECEIVER_LONGITUDE": 0,
  
  "PRODUCT_NAME": "Phân bón NPK",
  "PRODUCT_DESCRIPTION": "Phân bón NPK cao cấp",
  "PRODUCT_QUANTITY": 1,
  "PRODUCT_PRICE": 200000,
  "PRODUCT_WEIGHT": 2000,
  "PRODUCT_LENGTH": 20,
  "PRODUCT_WIDTH": 20,
  "PRODUCT_HEIGHT": 20,
  "PRODUCT_TYPE": "HH",
  
  "ORDER_PAYMENT": 1,
  "ORDER_SERVICE": "VCN",
  "ORDER_SERVICE_ADD": "",
  "ORDER_VOUCHER": "",
  "ORDER_NOTE": "Ghi chú đơn hàng",
  
  "MONEY_COLLECTION": 200000,
  "MONEY_TOTALFEE": 0,
  "MONEY_FEECOD": 0,
  "MONEY_FEEVAS": 0,
  "MONEY_FEEINSURRANCE": 0,
  "MONEY_FEE": 0,
  "MONEY_FEEOTHER": 0,
  "MONEY_TOTALVAT": 0,
  "MONEY_TOTAL": 0,
  
  "LIST_ITEM": [
    {
      "PRODUCT_NAME": "Phân bón NPK 2kg",
      "PRODUCT_PRICE": 200000,
      "PRODUCT_WEIGHT": 2000,
      "PRODUCT_QUANTITY": 1
    }
  ]
}
```

## 2. GIẢI THÍCH CÁC TRƯỜNG DỮ LIỆU

### A. Thông tin đơn hàng cơ bản
- **ORDER_NUMBER**: Mã đơn hàng duy nhất (string) - VD: "KHO_123_1729045678"
- **GROUPADDRESS_ID**: ID nhóm địa chỉ (int) - Mặc định: 1
- **CUS_ID**: ID khách hàng (int) - Mặc định: 0
- **DELIVERY_DATE**: Ngày giao hàng mong muốn (datetime) - VD: "2025-10-16 08:00:00"

### B. Thông tin người gửi (SENDER) - ĐỊA CHỈ CỐ ĐỊNH
**Địa chỉ: Phân bón MN - Xã Tân An Hội, Huyện Củ Chi, TP.HCM**

- **SENDER_FULLNAME**: "Phân bón MN"
- **SENDER_ADDRESS**: "19/1c Nguyễn Thị Chiên"
- **SENDER_PHONE**: "0986987791"
- **SENDER_EMAIL**: "" (có thể để trống)
- **SENDER_WARD**: 691 (WARDS_ID - Xã Tân An Hội từ file viettel_wards.json)
- **SENDER_DISTRICT**: 36 (DISTRICT_ID - Huyện Củ Chi từ file viettel_districts.json)
- **SENDER_PROVINCE**: 2 (PROVINCE_ID - TP. Hồ Chí Minh từ file viettel_provinces.json)
- **SENDER_LATITUDE**: 0 (không bắt buộc)
- **SENDER_LONGITUDE**: 0 (không bắt buộc)

### C. Thông tin người nhận (RECEIVER) - ĐỘNG
**⚠️ QUAN TRỌNG: Phải sử dụng ID từ danh sách ViettelPost**

- **RECEIVER_FULLNAME**: Tên người nhận (string)
- **RECEIVER_ADDRESS**: Địa chỉ chi tiết (string)
- **RECEIVER_PHONE**: Số điện thoại (string)
- **RECEIVER_EMAIL**: Email (có thể để trống)
- **RECEIVER_WARD**: WARDS_ID từ `public/json/viettel_wards.json`
- **RECEIVER_DISTRICT**: DISTRICT_ID từ `public/json/viettel_districts.json`
- **RECEIVER_PROVINCE**: PROVINCE_ID từ `public/json/viettel_provinces.json`
- **RECEIVER_LATITUDE**: 0 (không bắt buộc)
- **RECEIVER_LONGITUDE**: 0 (không bắt buộc)

### D. Thông tin sản phẩm
- **PRODUCT_NAME**: Tên sản phẩm (string)
- **PRODUCT_DESCRIPTION**: Mô tả sản phẩm (string)
- **PRODUCT_QUANTITY**: Số lượng (int)
- **PRODUCT_PRICE**: Giá trị sản phẩm (int) - Đơn vị: VND
- **PRODUCT_WEIGHT**: Trọng lượng (int) - Đơn vị: GRAM
- **PRODUCT_LENGTH**: Chiều dài (int) - Đơn vị: CM
- **PRODUCT_WIDTH**: Chiều rộng (int) - Đơn vị: CM
- **PRODUCT_HEIGHT**: Chiều cao (int) - Đơn vị: CM
- **PRODUCT_TYPE**: Loại sản phẩm (string)
  - "HH": Hàng hóa (thực phẩm, thiết bị, phân bón...)
  - "TL": Tài liệu (giấy tờ, hợp đồng...)

### E. Thông tin dịch vụ
- **ORDER_PAYMENT**: Hình thức thanh toán (int)
  - 1: Người nhận trả phí
  - 2: Người gửi trả phí
  
- **ORDER_SERVICE**: Loại dịch vụ (string)
  - "VCN": Chuyển phát nhanh
  - "VTK": Chuyển phát tiết kiệm
  
- **ORDER_SERVICE_ADD**: Dịch vụ bổ sung (string) - Có thể để trống
- **ORDER_VOUCHER**: Mã voucher (string) - Có thể để trống
- **ORDER_NOTE**: Ghi chú đơn hàng (string)

### F. Thông tin tiền
- **MONEY_COLLECTION**: Tiền thu hộ COD (int) - Đơn vị: VND
- **MONEY_TOTALFEE**: Tổng phí vận chuyển (int) - ViettelPost sẽ tính
- **MONEY_FEECOD**: Phí COD (int) - ViettelPost sẽ tính
- **MONEY_FEEVAS**: Phí giá trị gia tăng (int) - ViettelPost sẽ tính
- **MONEY_FEEINSURRANCE**: Phí bảo hiểm (int) - ViettelPost sẽ tính
- **MONEY_FEE**: Phí khác (int) - ViettelPost sẽ tính
- **MONEY_FEEOTHER**: Phí phụ (int) - ViettelPost sẽ tính
- **MONEY_TOTALVAT**: Tổng VAT (int) - ViettelPost sẽ tính
- **MONEY_TOTAL**: Tổng tiền (int) - ViettelPost sẽ tính

### G. Danh sách sản phẩm chi tiết
- **LIST_ITEM**: Mảng chứa thông tin chi tiết từng sản phẩm
  ```json
  [
    {
      "PRODUCT_NAME": "Tên sản phẩm 1",
      "PRODUCT_PRICE": 100000,
      "PRODUCT_WEIGHT": 1000,
      "PRODUCT_QUANTITY": 1
    },
    {
      "PRODUCT_NAME": "Tên sản phẩm 2",
      "PRODUCT_PRICE": 200000,
      "PRODUCT_WEIGHT": 2000,
      "PRODUCT_QUANTITY": 2
    }
  ]
  ```

## 3. CÁCH TÌM ID CHÍNH XÁC

### A. Tìm PROVINCE_ID (Tỉnh/Thành phố)
File: `public/json/viettel_provinces.json`

Ví dụ: Hồ Chí Minh
```json
{
    "PROVINCE_ID": 2,
    "PROVINCE_CODE": "HCM",
    "PROVINCE_NAME": "Hồ Chí Minh"
}
```
➡️ Sử dụng: `PROVINCE_ID = 2`

### B. Tìm DISTRICT_ID (Quận/Huyện)
File: `public/json/viettel_districts.json`

Ví dụ: Huyện Củ Chi
```json
{
    "DISTRICT_ID": 36,
    "DISTRICT_VALUE": "7330",
    "DISTRICT_NAME": "HUYỆN CỦ CHI",
    "PROVINCE_ID": 2
}
```
➡️ Sử dụng: `DISTRICT_ID = 36` (phải thuộc `PROVINCE_ID = 2`)

### C. Tìm WARDS_ID (Phường/Xã)
File: `public/json/viettel_wards.json`

Ví dụ: Xã Tân An Hội
```json
{
    "WARDS_ID": 691,
    "WARDS_NAME": "XÃ TÂN AN HỘI",
    "DISTRICT_ID": 36,
    "DISTRICT_NAME": "HUYỆN CỦ CHI"
}
```
➡️ Sử dụng: `WARDS_ID = 691` (phải thuộc `DISTRICT_ID = 36`)

## 4. LƯU Ý QUAN TRỌNG

### ⚠️ NGUYÊN NHÂN GÂY LỖI

1. **"Incorrect data: RECEIVER_DISTRICT"**
   - Sử dụng ID từ hệ thống local thay vì ID từ ViettelPost
   - ID không tồn tại trong danh sách ViettelPost
   - ID không khớp với PROVINCE_ID

2. **"Incorrect data: SENDER_PROVINCE"**
   - SENDER_PROVINCE không đúng ID ViettelPost
   - SENDER_DISTRICT không thuộc SENDER_PROVINCE
   - SENDER_WARD không thuộc SENDER_DISTRICT

3. **"Incorrect data: RECEIVER_PROVINCE"**
   - RECEIVER_PROVINCE không đúng ID ViettelPost
   - RECEIVER_DISTRICT không thuộc RECEIVER_PROVINCE
   - RECEIVER_WARD không thuộc RECEIVER_DISTRICT

4. **"Incorrect data: PRODUCT_TYPE"**
   - Thiếu trường PRODUCT_TYPE trong request
   - PRODUCT_TYPE phải là "HH" (Hàng hóa) hoặc "TL" (Tài liệu)

### ✅ CÁCH FIX

1. **KHÔNG được** sử dụng ID từ database local (`$order->province`, `$order->district`, `$order->ward`)
2. **PHẢI** map từ địa chỉ text sang ID ViettelPost
3. **PHẢI** kiểm tra quan hệ: Ward ⊂ District ⊂ Province

## 5. ENDPOINT VÀ HEADER

### API Endpoint
```
POST https://partner.viettelpost.vn/v2/order/createOrder
```

### Headers
```php
[
    'Token' => 'YOUR_VIETTELPOST_TOKEN_HERE',
    'Content-Type' => 'application/json'
]
```

### Cách gọi API (Laravel)
```php
$response = Http::withHeaders([
    'Token' => $token,
    'Content-Type' => 'application/json'
])->post($endpoint, $data);
```

## 6. XỬ LÝ RESPONSE

### Response thành công
```json
{
    "status": 200,
    "error": false,
    "message": "OK",
    "data": {
        "ORDER_NUMBER": "KHO_123_1729045678",
        "ORDER_SYSTEMCODE": "VTP123456789",
        ...
    }
}
```

### Response lỗi
```json
{
    "status": 400,
    "error": true,
    "message": "Incorrect data: RECEIVER_DISTRICT",
    "data": null
}
```

## 7. VÍ DỤ MAP ĐỊA CHỈ

### Tình huống: Khách hàng ở "Phường Bến Nghé, Quận 1, TP.HCM"

1. **Tìm PROVINCE_ID**
   - Tìm "Hồ Chí Minh" trong `viettel_provinces.json`
   - Kết quả: `PROVINCE_ID = 2`

2. **Tìm DISTRICT_ID**
   - Tìm "QUẬN 1" với `PROVINCE_ID = 2` trong `viettel_districts.json`
   - Kết quả: `DISTRICT_ID = 19` (ví dụ)

3. **Tìm WARDS_ID**
   - Tìm "PHƯỜNG BẾN NGHÉ" với `DISTRICT_ID = 19` trong `viettel_wards.json`
   - Kết quả: `WARDS_ID = 123` (ví dụ)

### JSON cuối cùng
```json
{
    "RECEIVER_PROVINCE": 2,
    "RECEIVER_DISTRICT": 19,
    "RECEIVER_WARD": 123
}
```

## 8. CODE MẪU PHP

Xem file: `app/Http/Controllers/ShippingOrderController.php` 
Function: `createOrderVTPost()`

## 9. KIỂM TRA TRƯỚC KHI GỬI

- [ ] SENDER_PROVINCE = 2 (HCM)
- [ ] SENDER_DISTRICT = 36 (Củ Chi)
- [ ] SENDER_WARD = 691 (Tân An Hội)
- [ ] RECEIVER_PROVINCE là PROVINCE_ID từ ViettelPost
- [ ] RECEIVER_DISTRICT là DISTRICT_ID từ ViettelPost và thuộc RECEIVER_PROVINCE
- [ ] RECEIVER_WARD là WARDS_ID từ ViettelPost và thuộc RECEIVER_DISTRICT
- [ ] PRODUCT_WEIGHT tính bằng GRAM (không phải KG)
- [ ] MONEY_COLLECTION là số tiền COD (VND)
- [ ] ORDER_PAYMENT = 1 (người nhận trả phí) hoặc 2 (người gửi trả phí)
- [ ] Token ViettelPost hợp lệ

## 10. DEBUG

### Log request khi lỗi
```php
\Log::error('ViettelPost Error:', [
    'response' => $responseData, 
    'request' => $data
]);
```

### Kiểm tra trong file log
```bash
tail -f storage/logs/laravel.log
```

## 11. LẤY TOKEN VIETTELPOST

1. Đăng ký tài khoản tại: https://viettelpost.vn/
2. Liên hệ bộ phận hỗ trợ để lấy Token API
3. Thay thế `YOUR_VIETTELPOST_TOKEN_HERE` trong code

---

**Tác giả**: AI Assistant  
**Ngày cập nhật**: 2025-10-15  
**Version**: 1.0

