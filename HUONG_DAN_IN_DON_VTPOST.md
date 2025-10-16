# HƯỚNG DẪN IN ĐƠN HÀNG VIETTELPOST

## 📌 TỔNG QUAN

ViettelPost cung cấp API để tạo phiếu in đơn hàng dưới dạng PDF.

### API Endpoint
```
POST https://partner.viettelpost.vn/v2/order/createOrderPDF
```

### Request Headers
```php
[
    'Token' => 'YOUR_VIETTELPOST_TOKEN',
    'Content-Type' => 'application/json'
]
```

## 📋 CẤU TRÚC REQUEST

### JSON Request
```json
{
  "ORDER_ARRAY": ["KHO_123_1729045678", "KHO_124_1729045679"],
  "TYPE": 1
}
```

### Giải thích các trường

- **ORDER_ARRAY** (array): Mảng chứa danh sách mã đơn hàng cần in
  - Có thể in 1 hoặc nhiều đơn cùng lúc
  - VD: `["ORDER_001"]` hoặc `["ORDER_001", "ORDER_002", "ORDER_003"]`

- **TYPE** (int): Loại phiếu in
  - `1`: In đơn 80x80mm (phù hợp máy in nhiệt nhỏ)
  - `2`: In A5 (phù hợp máy in laser/inkjet)

## 📋 RESPONSE

### Response thành công
```json
{
  "status": 200,
  "error": false,
  "message": "OK",
  "data": {
    "URL": "https://viettelpost.vn/download/pdf/xxxxx.pdf"
  }
}
```

Hoặc:

```json
{
  "status": 200,
  "error": false,
  "message": "OK",
  "data": {
    "PDF": "base64_encoded_pdf_string_here..."
  }
}
```

### Response lỗi
```json
{
  "status": 400,
  "error": true,
  "message": "Order not found"
}
```

## 🔧 CÁC FUNCTION ĐÃ TẠO

### 1️⃣ In đơn lẻ theo order_code

**OrdersController.php:**
```php
public function printOrderByOrderCodeVTPost($orderCode)
```

**Route:**
```
GET /in-don-le-VTPOST/{order_code}
```

**Ví dụ:**
```
/in-don-le-VTPOST/KHO_123_1729045678
```

### 2️⃣ In nhiều đơn cùng lúc

**OrdersController.php:**
```php
public function printOrderByOrderAllVTPost(Request $r)
```

**Route:**
```
GET /in-tat-ca-van-don-VTPOST?q=[1,2,3,4,5]
```

**Request:**
- Method: GET
- Parameter: `q` (JSON array chứa danh sách order_id)

**Ví dụ:**
```
/in-tat-ca-van-don-VTPOST?q=[123,124,125]
```

### 3️⃣ In đơn từ ShippingOrderController (Alternative)

**ShippingOrderController.php:**
```php
public function printVTPost($orderCode)
public function printMultipleVTPost(Request $req)
```

**Routes:**
```
GET  /in-don-vtpost/{orderCode}
POST /in-nhieu-don-vtpost
```

## 🎯 CÁCH SỬ DỤNG

### Cách 1: In đơn lẻ từ link trực tiếp

```html
<a href="{{ route('print-order-code-VTPOST', $shippingOrder->order_code) }}" 
   target="_blank" 
   class="btn btn-primary">
   🖨️ In đơn ViettelPost
</a>
```

### Cách 2: In nhiều đơn từ danh sách

```javascript
// Lấy danh sách order_id đã chọn
let selectedOrders = [1, 2, 3, 4, 5];

// Tạo URL với query param
let url = '/in-tat-ca-van-don-VTPOST?q=' + JSON.stringify(selectedOrders);

// Mở trong tab mới
window.open(url, '_blank');
```

### Cách 3: In nhiều đơn qua form POST

```html
<form action="{{ route('print-multiple-vtpost') }}" method="POST" target="_blank">
    @csrf
    <input type="hidden" name="order_codes[]" value="ORDER_001">
    <input type="hidden" name="order_codes[]" value="ORDER_002">
    <input type="hidden" name="type" value="1">
    <button type="submit">In đơn</button>
</form>
```

## 📊 FLOW XỬ LÝ

```
1. User chọn đơn hàng cần in
   ↓
2. Gửi request đến Laravel controller
   ↓
3. Controller gọi API ViettelPost createOrderPDF
   ↓
4. ViettelPost trả về:
   - Option 1: URL PDF → Redirect
   - Option 2: Base64 PDF → Decode và hiển thị
   ↓
5. Browser hiển thị/tải PDF
```

## ⚠️ LƯU Ý

### 1. Token ViettelPost
- Token phải hợp lệ và không hết hạn
- Cập nhật token tại các function nếu cần

### 2. Mã đơn hàng
- Phải là mã đơn đã tạo thành công trên ViettelPost
- Định dạng: `ORDER_NUMBER` đã gửi khi tạo đơn

### 3. Loại phiếu in
- TYPE = 1: 80x80mm → Dùng cho máy in nhiệt
- TYPE = 2: A5 → Dùng cho máy in laser

### 4. In nhiều đơn
- ViettelPost cho phép in nhiều đơn cùng lúc
- Tất cả các đơn sẽ được gộp vào 1 file PDF

## 🔍 DEBUG

### Kiểm tra log khi lỗi
```bash
tail -f storage/logs/laravel.log
```

### Test API thủ công
```bash
curl -X POST "https://partner.viettelpost.vn/v2/order/createOrderPDF" \
  -H "Token: YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "ORDER_ARRAY": ["KHO_123_1729045678"],
    "TYPE": 1
  }'
```

## 📝 VÍ DỤ CODE

### Ví dụ 1: In đơn lẻ
```php
// Trong blade template
<a href="{{ route('print-order-code-VTPOST', 'KHO_123_1729045678') }}" 
   target="_blank">
   In đơn
</a>
```

### Ví dụ 2: In nhiều đơn từ danh sách checkbox
```javascript
// JavaScript
$('#btnPrintVTPost').on('click', function() {
    let selectedIds = [];
    $('input[name="order_checkbox"]:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (selectedIds.length === 0) {
        alert('Vui lòng chọn ít nhất 1 đơn hàng');
        return;
    }
    
    let url = '/in-tat-ca-van-don-VTPOST?q=' + JSON.stringify(selectedIds);
    window.open(url, '_blank');
});
```

### Ví dụ 3: In đơn sau khi tạo thành công
```php
// Trong ShippingOrderController sau khi createOrderVTPost thành công
if ($response->status() == 200) {
    $orderCode = $responseData['data']['ORDER_NUMBER'];
    $this->saveShippingCodeVTPost($orderCode, $orderId);
    
    // Tự động mở phiếu in
    $printUrl = route('print-order-code-VTPOST', $orderCode);
    
    notify()->success('Tạo vận đơn thành công!', 'Thành công!');
    return redirect('chi-tiet-don-hang/' . $orderId)
        ->with('print_url', $printUrl);
}
```

## 🎨 TÍCH HỢP VÀO GIAO DIỆN

### Thêm nút in trong chi tiết đơn hàng
```blade
@if($shippingOrder && $shippingOrder->vendor_ship == 'VTPOST')
<a href="{{ route('print-order-code-VTPOST', $shippingOrder->order_code) }}" 
   target="_blank"
   class="btn btn-danger">
   <i class="fa fa-print"></i> In đơn ViettelPost
</a>
@endif
```

### Thêm nút in trong danh sách đơn hàng
```blade
@if($order->shippingOrder && $order->shippingOrder->vendor_ship == 'VTPOST')
<a href="{{ route('print-order-code-VTPOST', $order->shippingOrder->order_code) }}" 
   target="_blank"
   class="btn btn-sm btn-danger"
   title="In đơn ViettelPost">
   <i class="fa fa-print"></i>
</a>
@endif
```

## ✅ CHECKLIST

- [ ] Token ViettelPost hợp lệ
- [ ] Mã đơn hàng đã tồn tại trên ViettelPost
- [ ] Chọn TYPE phù hợp với máy in (1: 80x80, 2: A5)
- [ ] Kiểm tra popup blocker nếu PDF không mở

## 🆘 TROUBLESHOOTING

### Lỗi: "Không thể tạo phiếu in ViettelPost!"
- Kiểm tra token có hợp lệ không
- Kiểm tra order_code có đúng không
- Xem log: `storage/logs/laravel.log`

### Lỗi: PDF không hiển thị
- Kiểm tra popup blocker
- Thử mở trong tab/window mới
- Kiểm tra response từ API

### Lỗi: "Order not found"
- Mã đơn hàng chưa được tạo trên ViettelPost
- Hoặc đã bị hủy

---

**Version**: 1.0  
**Ngày**: 2025-10-16

