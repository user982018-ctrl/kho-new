# TỔNG HỢP API VIETTELPOST

## 📚 MỤC LỤC

1. [Tạo đơn hàng](#1-tạo-đơn-hàng)
2. [In đơn hàng](#2-in-đơn-hàng)
3. [Xem chi tiết đơn hàng](#3-xem-chi-tiết-đơn-hàng)
4. [Lưu mã vận đơn](#4-lưu-mã-vận-đơn)

---

## 1️⃣ TẠO ĐƠN HÀNG

### API Endpoint
```
POST https://partner.viettelpost.vn/v2/order/createOrder
```

### Routes Laravel
```
GET  /tao-van-don-vtpost/{order_id}         → viewCreateShippingVTPost()
POST /create-order-VTPost                   → createOrderVTPost()
```

### Request mẫu
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
  "ORDER_PAYMENT": 2,
  "ORDER_SERVICE": "VSL6",
  "MONEY_COLLECTION": 200000,
  "LIST_ITEM": [...]
}
```

### Tài liệu chi tiết
📖 `HUONG_DAN_VIETTELPOST.md`

---

## 2️⃣ IN ĐƠN HÀNG

### API Endpoint
```
POST https://partner.viettelpost.vn/v2/order/createOrderPDF
```

### Routes Laravel

**In đơn lẻ:**
```
GET /in-don-le-VTPOST/{order_code}          → printOrderByOrderCodeVTPost()
GET /in-don-vtpost/{orderCode}              → printVTPost()
```

**In nhiều đơn:**
```
GET  /in-tat-ca-van-don-VTPOST?q=[1,2,3]   → printOrderByOrderAllVTPost()
POST /in-nhieu-don-vtpost                   → printMultipleVTPost()
```

### Request mẫu
```json
{
  "ORDER_ARRAY": ["KHO_123_1729045678", "KHO_124_1729045679"],
  "TYPE": 1
}
```

### TYPE Options
- `1`: In 80x80mm (máy in nhiệt)
- `2`: In A5 (máy in laser)

### Tài liệu chi tiết
📖 `HUONG_DAN_IN_DON_VTPOST.md`

---

## 3️⃣ XEM CHI TIẾT ĐƠN HÀNG

### API Endpoints

**Tracking:**
```
GET https://api.viettelpost.vn/api/setting/listOrderTrackingVTP3?OrderNumber={code}
```

**Detail:**
```
GET https://api.viettelpost.vn/api/setting/getOrderDetailForWeb?OrderNumber={code}
```

### Routes Laravel
```
GET /chi-tiet-van-don/{shipping_id}         → detailShippingOrder()
```

### Function
```php
detailDataVTPost($orderCode)  // ShippingOrderController
```

### Response
```php
[
  'order' => [...],        // Thông tin đơn hàng
  'statusLogs' => [...]    // Lịch sử trạng thái
]
```

### View
```
resources/views/pages/orders/shipping/detailVTPost.blade.php
```

---

## 4️⃣ LƯU MÃ VẬN ĐƠN

### Routes
```
POST /save-shipping-has                     → createShippingHas()
```

### Function
```php
saveShippingCodeVTPost($orderCode, $orderId)
```

### Vendor Ship Code
```php
'vendor_ship' => 'VTPOST'
```

---

## 🔑 THÔNG TIN TOKEN

### Token ViettelPost (Hiện tại)
```
eyJhbGciOiJFUzI1NiJ9.eyJzdWIiOiIwODI3NTc2NTY2IiwiU1NPSWQiOiIxLTA2OTdkNDVlLWZmZjgtNDFiYS05ZGZiLTkwZjc3YTBjZjQ4OCIsImludGVybmFsIjpmYWxzZSwiVXNlcklkIjoxNjk4MjMwNiwiRnJvbVNvdXJjZSI6MywiVG9rZW4iOiJCQzA1RjE4QUUzOUFCMDRGOEQ4QTkwRjQzRDNFQzVDNiIsInNlc3Npb25JZCI6IjEwMkEwRUI4RDBBODg3QkMzQzk4QzcyRkRFM0Q2MUMxIiwiZXhwIjoxNzYwOTQ1MTc1LCJsc3RDaGlsZHJlbiI6IiIsIlBhcnRuZXIiOjAsImRldmljZUlkIjoibHAzdGRscnRpYm12bGg0a2M1NmZsIiwidmVyc2lvbiI6MX0.oNCwZxzeB1pK7TM4c_2YTD7QUNGXHIhAZROM4h4sns9lRVpv5TDa9LU3xXo6ixhKDfTnzZhUxaoDMByfTF62tw
```

### Vị trí Token trong code:
- `ShippingOrderController::createOrderVTPost()` - Line 175
- `ShippingOrderController::detailDataVTPost()` - Line 1008
- `ShippingOrderController::printVTPost()` - Line 1088
- `OrdersController::printOrderByOrderCodeVTPost()` - Line 34

---

## 📂 CẤU TRÚC FILES

```
app/Http/Controllers/
├── ShippingOrderController.php
│   ├── createOrderVTPost()           - Tạo đơn
│   ├── saveShippingCodeVTPost()      - Lưu mã vận đơn
│   ├── detailDataVTPost()            - Chi tiết vận đơn
│   ├── printVTPost()                 - In đơn lẻ
│   └── printMultipleVTPost()         - In nhiều đơn
│
└── OrdersController.php
    ├── printOrderByOrderCodeVTPost() - In đơn lẻ
    └── printOrderByOrderAllVTPost()  - In tất cả đơn đã chọn

resources/views/pages/orders/shipping/
├── vtpost.blade.php                  - Form tạo đơn
├── detailVTPost.blade.php           - Chi tiết vận đơn
└── noti/vtpost.blade.php            - Thông báo lỗi

public/json/
├── viettel_provinces.json           - Danh sách tỉnh/TP
├── viettel_districts.json           - Danh sách quận/huyện
└── viettel_wards.json               - Danh sách phường/xã

Docs/
├── HUONG_DAN_VIETTELPOST.md         - Hướng dẫn tạo đơn
├── HUONG_DAN_IN_DON_VTPOST.md       - Hướng dẫn in đơn
├── README_VIETTELPOST.md            - Tổng quan
├── API_VIETTELPOST_SUMMARY.md       - Tổng hợp API (file này)
├── viettelpost_sample.json          - JSON mẫu
└── test_viettelpost_create_order.php - Script test
```

## 🚀 QUICK START

### 1. Tạo đơn hàng
```bash
# Truy cập
/tao-van-don-vtpost/{order_id}

# Chọn địa chỉ → Submit
```

### 2. Xem chi tiết
```bash
/chi-tiet-van-don/{shipping_id}
```

### 3. In đơn
```bash
# In đơn lẻ
/in-don-le-VTPOST/{order_code}

# In nhiều đơn
/in-tat-ca-van-don-VTPOST?q=[1,2,3,4,5]
```

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

