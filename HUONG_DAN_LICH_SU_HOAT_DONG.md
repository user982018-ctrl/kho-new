# HƯỚNG DẪN SỬ DỤNG TÍNH NĂNG LỊCH SỬ HOẠT ĐỘNG

## 📋 Mục đích
Tính năng lịch sử hoạt động giúp theo dõi và ghi nhận tất cả các thao tác của người dùng trong hệ thống, bao gồm:
- Đăng nhập/đăng xuất
- Tạo mới, cập nhật, xóa dữ liệu
- Xem, tìm kiếm, in ấn, xuất file
- Và nhiều hành động khác

## 🚀 Cài đặt

### 1. Chạy Migration
```bash
php artisan migrate
```

Migration sẽ tạo bảng `user_activity_logs` với các trường:
- `user_id`: ID người dùng
- `user_name`: Tên người dùng
- `action`: Hành động (create, update, delete, view, login, logout, etc.)
- `module`: Module (orders, products, sale_care, users, etc.)
- `record_id`: ID bản ghi bị tác động
- `description`: Mô tả chi tiết
- `ip_address`: Địa chỉ IP
- `user_agent`: Thông tin trình duyệt
- `url`: URL được truy cập
- `method`: HTTP Method (GET, POST, PUT, DELETE)
- `old_values`: Giá trị cũ (JSON)
- `new_values`: Giá trị mới (JSON)

### 2. Middleware đã được đăng ký tự động
Middleware `LogUserActivity` đã được thêm vào route group `admin-auth`, tất cả các route trong group này sẽ tự động được log.

## 📖 Sử dụng

### 1. Xem lịch sử hoạt động
Truy cập: **http://localhost/kho/lich-su-hoat-dong**

Trang này hiển thị:
- Danh sách tất cả hoạt động của users
- Bộ lọc theo: User, Module, Action, Khoảng thời gian
- Phân trang
- Link xem chi tiết từng hoạt động

### 2. Xem chi tiết một hoạt động
Click vào nút "Chi tiết" ở mỗi dòng để xem:
- Thông tin người dùng
- Thông tin hoạt động
- Thông tin request (URL, Method, IP, User Agent)
- So sánh thay đổi dữ liệu (old values vs new values)
- Dữ liệu JSON đầy đủ

### 3. Xem lịch sử của một user
Truy cập: **http://localhost/kho/lich-su-user/{userId}**

Hoặc click vào tên user trong danh sách để xem:
- Timeline hoạt động của user đó
- Thống kê tổng số hoạt động
- Hiển thị dạng timeline đẹp mắt

### 4. Xem thống kê
Truy cập: **http://localhost/kho/thong-ke-hoat-dong**

Hiển thị:
- Thống kê theo người dùng
- Thống kê theo module
- Thống kê theo hành động
- Biểu đồ hoạt động theo ngày
- Bộ lọc theo khoảng thời gian

### 5. Xuất Excel
Click nút "Xuất Excel" ở trang danh sách để tải file CSV chứa:
- Tất cả log hoặc log đã lọc
- Format UTF-8 BOM để hiển thị tiếng Việt đúng

## 🔧 Sử dụng trong Code

### Log thủ công
```php
use App\Models\UserActivityLog;

// Log một hoạt động
UserActivityLog::logActivity(
    'create',           // action
    'orders',           // module
    $order->id,         // record_id
    'Tạo đơn hàng mới', // description
    null,               // old_values
    $order->toArray()   // new_values
);

// Log login
UserActivityLog::logLogin($userId, $userName);

// Log logout
UserActivityLog::logLogout($userId, $userName);
```

### Tự động log khi cập nhật
Middleware đã tự động log hầu hết các thao tác. Nếu cần log chi tiết hơn (old values vs new values), thêm vào Controller:

```php
use App\Models\UserActivityLog;

public function update(Request $request, $id)
{
    $order = Order::find($id);
    $oldValues = $order->toArray(); // Lưu giá trị cũ
    
    // Cập nhật
    $order->update($request->all());
    
    // Log với old và new values
    UserActivityLog::logActivity(
        'update',
        'orders',
        $order->id,
        'Cập nhật đơn hàng',
        $oldValues,
        $order->fresh()->toArray()
    );
    
    return redirect()->back();
}
```

## 🎯 Các Module được theo dõi

- **auth**: Đăng nhập/đăng xuất
- **orders**: Đơn hàng
- **products**: Sản phẩm
- **sale_care**: Tác nghiệp Sale
- **users**: Người dùng
- **categories**: Danh mục
- **shipping**: Vận đơn
- **groups**: Nhóm
- **spam**: Spam/Seeding
- **src_pages**: Nguồn

## 🎨 Các Action
- **create**: Tạo mới
- **update**: Cập nhật
- **delete**: Xóa
- **view**: Xem
- **login**: Đăng nhập
- **logout**: Đăng xuất
- **search**: Tìm kiếm
- **print**: In
- **export**: Xuất file

## 🔐 Quyền truy cập
- Admin và Lead Sale có quyền xem tất cả lịch sử
- User thường chỉ xem được lịch sử của mình (nếu được phân quyền)

## 🗑️ Xóa log cũ

### Qua API
```bash
curl -X POST http://localhost/kho/xoa-log-cu \
  -d "days=90" \
  -H "Content-Type: application/x-www-form-urlencoded"
```

Hoặc trong code:
```php
// Xóa log cũ hơn 90 ngày
UserActivityLog::where('created_at', '<', now()->subDays(90))->delete();
```

## 📊 API Endpoints

### 1. Lấy hoạt động gần nhất của user
```
GET /api/hoat-dong-gan-nhat/{userId}/{limit?}
```

Response:
```json
{
  "success": true,
  "data": [...]
}
```

### 2. So sánh thay đổi
```
GET /api/so-sanh-thay-doi/{id}
```

Response:
```json
{
  "success": true,
  "log": {...},
  "changes": [
    {
      "field": "status",
      "old_value": "1",
      "new_value": "3"
    }
  ]
}
```

## 💡 Lưu ý

1. **Performance**: Bảng log sẽ tăng nhanh, nên định kỳ xóa log cũ (> 3-6 tháng)

2. **Sensitive Data**: Password và các trường nhạy cảm đã được loại bỏ tự động trong middleware

3. **Middleware**: Chỉ log các route trong group `admin-auth`, webhook và public routes không được log

4. **Customization**: Có thể tùy chỉnh mapping action/module trong `LogUserActivity` middleware

## 🛠️ Troubleshooting

### Không có log nào được tạo
- Kiểm tra middleware đã được apply: `Route::middleware(['admin-auth', 'log.activity'])`
- Kiểm tra user đã đăng nhập: `Auth::check()`
- Kiểm tra route có nằm trong ignored paths không

### Log thiếu thông tin
- Middleware chỉ log basic info, để log old/new values cần log thủ công trong Controller
- Xem middleware `LogUserActivity::handle()` để thêm logic tùy chỉnh

### Lỗi khi migration
```bash
php artisan migrate:rollback
php artisan migrate
```

## 📞 Hỗ trợ
Liên hệ team dev nếu cần hỗ trợ hoặc customization thêm.

