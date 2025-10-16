# TÓM TẮT TÍCH HỢP VIETTELPOST API

## ✅ ĐÃ HOÀN THÀNH

### 1. Files đã tạo/cập nhật:

📄 **Controller**
- `app/Http/Controllers/ShippingOrderController.php`
  - ✅ Thêm function `createOrderVTPost()` - Tạo đơn ViettelPost
  - ✅ Thêm function `saveShippingCodeVTPost()` - Lưu mã vận đơn

📄 **Routes**
- `routes/web.php`
  - ✅ Thêm route `POST /create-order-VTPost`

📄 **View**
- `resources/views/pages/orders/shipping/vtpost.blade.php`
  - ✅ Cập nhật form action sang route mới

📄 **Tài liệu & Mẫu**
- `HUONG_DAN_VIETTELPOST.md` - Hướng dẫn chi tiết đầy đủ
- `viettelpost_sample.json` - JSON mẫu chuẩn
- `test_viettelpost_create_order.php` - File test API

### 2. Dữ liệu địa chỉ ViettelPost:
- ✅ `public/json/viettel_provinces.json` - Danh sách tỉnh/thành phố
- ✅ `public/json/viettel_districts.json` - Danh sách quận/huyện
- ✅ `public/json/viettel_wards.json` - Danh sách phường/xã

### 3. ID địa chỉ người gửi (đã tìm được):
```
SENDER_PROVINCE: 2    (TP. Hồ Chí Minh)
SENDER_DISTRICT: 36   (Huyện Củ Chi)
SENDER_WARD: 691      (Xã Tân An Hội)
```

## 🔧 CẦN LÀM TIẾP

### Bước 1: Lấy Token ViettelPost
1. Liên hệ ViettelPost để lấy Token API
2. Cập nhật token tại:
   ```php
   // File: app/Http/Controllers/ShippingOrderController.php
   // Line: 175
   $token = 'YOUR_VIETTELPOST_TOKEN_HERE';
   ```

### Bước 2: Test API
1. Cập nhật token trong file `test_viettelpost_create_order.php`
2. Chạy test:
   ```bash
   php test_viettelpost_create_order.php
   ```
3. Kiểm tra kết quả

### Bước 3: Kiểm tra form frontend
1. Truy cập: `/tao-van-don-vtpost/{order_id}`
2. Chọn tỉnh/thành phố → tự động load quận/huyện → phường/xã
3. Điền thông tin và submit
4. Kiểm tra log nếu có lỗi:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## 📌 NGUYÊN NHÂN LỖI VÀ CÁCH FIX

### Lỗi: "Incorrect data: RECEIVER_DISTRICT"

**Nguyên nhân:**
- Sử dụng ID từ database local thay vì ID ViettelPost
- ID không tồn tại trong danh sách ViettelPost
- ID không thuộc PROVINCE_ID đã chọn

**Cách fix:**
```php
// ❌ SAI - Dùng ID từ database local
"RECEIVER_DISTRICT" => $order->district

// ✅ ĐÚNG - Dùng ID từ form (đã map từ ViettelPost)
"RECEIVER_DISTRICT" => (int)$dataReq['district']
```

### Lỗi: "Incorrect data: SENDER_PROVINCE"

**Nguyên nhân:**
- SENDER_DISTRICT không thuộc SENDER_PROVINCE
- SENDER_WARD không thuộc SENDER_DISTRICT

**Cách fix:**
- Đã fix trong code với ID chính xác:
  ```php
  "SENDER_PROVINCE" => 2,   // HCM
  "SENDER_DISTRICT" => 36,  // Củ Chi
  "SENDER_WARD" => 691      // Tân An Hội
  ```

## 📖 TÀI LIỆU THAM KHẢO

1. **HUONG_DAN_VIETTELPOST.md** - Hướng dẫn chi tiết đầy đủ
   - Cấu trúc JSON
   - Giải thích từng trường
   - Cách tìm ID
   - Debug & troubleshooting

2. **viettelpost_sample.json** - JSON mẫu chuẩn
   - Copy & paste để test
   - Các trường bắt buộc
   - Ví dụ LIST_ITEM

3. **test_viettelpost_create_order.php** - Script test
   - Test API độc lập
   - Phân tích lỗi
   - Gợi ý fix

## 🔍 KIỂM TRA NHANH

### Checklist trước khi tạo đơn:

- [ ] Token ViettelPost hợp lệ
- [ ] SENDER_PROVINCE = 2 (HCM)
- [ ] SENDER_DISTRICT = 36 (Củ Chi)  
- [ ] SENDER_WARD = 691 (Tân An Hội)
- [ ] RECEIVER_* là ID từ ViettelPost (không phải local DB)
- [ ] PRODUCT_WEIGHT tính bằng GRAM
- [ ] PRODUCT_TYPE = "HH" (Hàng hóa) hoặc "TL" (Tài liệu)
- [ ] MONEY_COLLECTION là số tiền COD (VND)
- [ ] LIST_ITEM có ít nhất 1 sản phẩm

## 💡 VÍ DỤ NHANH

### JSON tối thiểu để tạo đơn:

```json
{
  "ORDER_NUMBER": "KHO_123_1729045678",
  "SENDER_PROVINCE": 2,
  "SENDER_DISTRICT": 36,
  "SENDER_WARD": 691,
  "RECEIVER_FULLNAME": "Nguyễn Văn A",
  "RECEIVER_PHONE": "0912345678",
  "RECEIVER_ADDRESS": "123 ABC",
  "RECEIVER_PROVINCE": 1,
  "RECEIVER_DISTRICT": 1,
  "RECEIVER_WARD": 1,
  "PRODUCT_WEIGHT": 2000,
  "PRODUCT_TYPE": "HH",
  "ORDER_PAYMENT": 1,
  "MONEY_COLLECTION": 200000,
  "LIST_ITEM": [
    {
      "PRODUCT_NAME": "Phân bón",
      "PRODUCT_WEIGHT": 2000,
      "PRODUCT_QUANTITY": 1
    }
  ]
}
```

## 📄 API IN ĐƠN HÀNG

### Routes đã tạo:

1️⃣ **In đơn lẻ:**
```
GET /in-don-le-VTPOST/{order_code}
GET /in-don-vtpost/{order_code}
```

2️⃣ **In nhiều đơn:**
```
GET  /in-tat-ca-van-don-VTPOST?q=[1,2,3]
POST /in-nhieu-don-vtpost
```

### Cách sử dụng:

```blade
{{-- In đơn lẻ --}}
<a href="{{ route('print-order-code-VTPOST', $orderCode) }}" target="_blank">
  🖨️ In đơn
</a>

{{-- In nhiều đơn --}}
<script>
let orderIds = [1, 2, 3];
window.open('/in-tat-ca-van-don-VTPOST?q=' + JSON.stringify(orderIds), '_blank');
</script>
```

Chi tiết xem: `HUONG_DAN_IN_DON_VTPOST.md`

## 👁️ XEM CHI TIẾT VẬN ĐƠN

### Route:
```
GET /chi-tiet-van-don/{shipping_id}
```

### Function:
- `detailDataVTPost($orderCode)` - Lấy thông tin chi tiết từ API
- View: `resources/views/pages/orders/shipping/detailVTPost.blade.php`

### Features:
- Hiển thị thông tin người nhận
- Timeline trạng thái vận chuyển
- Style đặc biệt cho đơn "Giao thành công" (màu xanh lá)

## 🆘 HỖ TRỢ

Nếu gặp lỗi:
1. Đọc `HUONG_DAN_VIETTELPOST.md` - Tạo đơn hàng
2. Đọc `HUONG_DAN_IN_DON_VTPOST.md` - In đơn hàng
3. Chạy `test_viettelpost_create_order.php` để test
4. Kiểm tra log: `storage/logs/laravel.log`
5. Xác minh ID trong các file JSON: `public/json/viettel_*.json`

---
**Version**: 1.1  
**Ngày cập nhật**: 2025-10-16

