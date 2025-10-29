# 📋 Tổng Hợp Chức Năng: Yêu Cầu Đổi Mật Khẩu Bắt Buộc

## 🎯 Mục đích
Yêu cầu người dùng đổi mật khẩu khi phát hiện mật khẩu trùng với username (`user.password == user.name`). Đây là biện pháp bảo mật để tránh người dùng sử dụng mật khẩu mặc định không an toàn.

---

## 📁 Danh Sách Các File

### 1. **Middleware: RequirePasswordChange**
**Đường dẫn:** `app/Http/Middleware/RequirePasswordChange.php`

**Chức năng:**
- Kiểm tra sau mỗi request nếu user đã đăng nhập
- Nếu `password == name`, chặn tất cả route và redirect đến trang đổi mật khẩu
- Cho phép truy cập các route: `change-password`, `change-password-post`, `log-out`, `login`

**Chi tiết:**
```php
- Kiểm tra: Hash::check($user->name, $user->password)
- Nếu true → redirect đến route('change-password')
- Chặn tất cả route khác (trừ các route được phép)
```

---

### 2. **Controller: UserController (Các method mới)**
**Đường dẫn:** `app/Http/Controllers/UserController.php`

#### **Method: `postLogin()` - Sửa đổi**
- **Dòng:** ~95-112
- **Thay đổi:** Sau khi đăng nhập thành công, kiểm tra nếu `password == name` thì redirect đến trang đổi mật khẩu

#### **Method: `changePassword()` - Mới**
- **Dòng:** ~124-138
- **Chức năng:** Hiển thị form đổi mật khẩu
- **Điều kiện:** Chỉ cho phép truy cập nếu `password == name`

#### **Method: `postChangePassword()` - Mới**
- **Dòng:** ~140-203
- **Chức năng:** Xử lý logic đổi mật khẩu
- **Validation:**
  - Mật khẩu hiện tại: required
  - Mật khẩu mới: required, min.<6 ký tự, confirmed
  - Không được trùng với username
- **Logic:**
  - Kiểm tra mật khẩu hiện tại (có thể là username hoặc password đã đổi)
  - Cập nhật mật khẩu mới
  - Log hoạt động
  - Redirect về home với thông báo success

---

### 3. **Routes: web.php**
**Đường dẫn:** `routes/web.php`

#### **Routes mới được thêm:**
```php
// Route đổi mật khẩu bắt buộc (không cần middleware admin-auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/doi-mat-khau',  [UserController::class, 'changePassword'])->name('change-password');
    Route::post('/doi-mat-khau',  [UserController::class, 'postChangePassword'])->name('change-password-post');
});
```

#### **Route group được sửa:**
```php
// Thêm middleware 'require.password.change'
Route::middleware(['admin-auth', 'log.activity', 'require.password.change'])->group(function () {
    // ... tất cả routes admin ...
});
```

---

### 4. **View: change-password.blade.php**
**Đường dẫn:** `resources/views/pages/users/change-password.blade.php`

**Chức năng:**
- Form đổi mật khẩu với 3 trường:
  1. **Mật khẩu hiện tại** (tên đăng nhập)
  2. **Mật khẩu mới** (tối thiểu 6 ký tự)
  3. **Xác nhận mật khẩu mới**
- Hiển thị thông báo:
  - Warning: Yêu cầu đổi mật khẩu
  - Error: Lỗi validation hoặc mật khẩu không đúng
  - Success: Đổi mật khẩu thành công

**UI:**
- Design tương tự trang login
- Alert messages với Bootstrap
- Font Awesome icons
- Responsive design

---

### 5. **Kernel: Đăng ký Middleware**
**Đường dẫn:** `app/Http/Kernel.php`

**Thay đổi:**
```php
protected $middlewareAliases = [
    // ... existing aliases ...
    'require.password.change' => \App\Http\Middleware\RequirePasswordChange::class,
];
```

---

## 🔄 Flow Hoạt Động

### **Kịch bản 1: Đăng nhập với password == name**
```
1. User đăng nhập thành công
2. postLogin() kiểm tra: Hash::check($user->name, $user->password)
3. Nếu true → Redirect đến /doi-mat-khau
4. Middleware RequirePasswordChange chặn tất cả route khác
5. User buộc phải đổi mật khẩu
6. Sau khi đổi thành công → Redirect về home
```

### **Kịch bản 2: User đã đăng nhập, truy cập route khác**
```
1. User đã đăng nhập (session còn hiệu lực)
2. Truy cập bất kỳ route nào trong admin group
3. Middleware RequirePasswordChange kiểm tra
4. Nếu password == name → Redirect đến /doi-mat-khau
5. Chặn tất cả route (trừ change-password, logout, login)
```

### **Kịch bản 3: Đổi mật khẩu**
```
1. User vào trang /doi-mat-khau
2. Nhập mật khẩu hiện tại (username)
3. Nhập mật khẩu mới (tối thiểu 6 ký tự)
4. Xác nhận mật khẩu mới
5. Validate:
   - Mật khẩu hiện tại phải đúng (username)
   - Mật khẩu mới không được trùng username
   - Mật khẩu mới >= 6 ký tự
   - Xác nhận mật khẩu phải khớp
6. Cập nhật password trong database
7. Log hoạt động
8. Redirect về home với thông báo success
```

---

## 🔒 Bảo Mật

### **Kiểm tra bảo mật:**
1. ✅ **Hash::check()**: Sử dụng Laravel Hash để so sánh password đã hash
2. ✅ **Validation**: Kiểm tra đầy đủ input trước khi xử lý
3. ✅ **Middleware protection**: Chặn tất cả route cho đến khi đổi mật khẩu
4. ✅ **Password rules**: 
   - Tối thiểu 6 ký tự
   - Không được trùng với username
   - Phải xác nhận lại mật khẩu mới

### **Logging:**
- Log hoạt động đổi mật khẩu vào `user_activity_logs` table
- Action: `update`
- Module: `users`
- Description: `Đổi mật khẩu`

---

## 📝 Sử Dụng

### **Test Case 1: Tạo user với password == name**
```sql
-- Tạo user test
INSERT INTO users (name, password, email, status) 
VALUES ('testuser', '$2y$10$...', 'test@example.com', 1);
-- Password phải được hash từ 'testuser'
```

### **Test Case 2: Đăng nhập**
1. Đăng nhập với username = `testuser`, password = `testuser`
2. Kỳ vọng: Redirect đến `/doi-mat-khau`

### **Test Case 3: Truy cập route khác**
1. Sau khi đ Minister đăng nhập với password == name
2. Truy cập `/home` hoặc bất kỳ route admin nào
3. Kỳ vọng: Redirect đến `/doi-mat-khau`

### **Test Case 4: Đổi mật khẩu**
1. Vào `/doi-mat-khau`
2. Nhập:
   - Mật khẩu hiện tại: `testuser`
   - Mật khẩu mới: `newpass123`
   - Xác nhận: `newpass123`
3. Submit
4. Kỳ vọng: Redirect về `/home` với thông báo success

---

## ⚠️ Lưu Ý

1. **Middleware Order**: Middleware `require.password.change` phải được đặt sau `admin-auth` để đảm bảo user đã đăng nhập

2. **Route Exceptions**: Các route sau được phép truy cập ngay cả khi password == name:
   - `change-password` (GET)
   - `change-password-post` (POST)
   - `log-out` (GET)
   - `login` (GET/POST)

3. **Password Hash**: Laravel tự động hash password khi lưu vào database (nếu model có cast `'password' => 'hashed'`), nhưng trong method `postChangePassword()` vẫn phải dùng `Hash::make()` để đảm bảo.

4. **Session**: User phải đăng nhập (có session) để middleware hoạt động.

---

## 🐛 Debug

### **Kiểm tra middleware có hoạt động:**
```php
// Thêm vào RequirePasswordChange.php
\Log::info('Checking password change requirement', [
    'user_id' => $user->id,
    'username' => $user->name,
    'password_match' => Hash::check($user->name, $user->password)
]);
```

### **Kiểm tra route name:**
```bash
php artisan route:list | grep change-password
```

### **Kiểm tra middleware đã đăng ký:**
```bash
php artisan route:list --middleware=require.password.change
```

---

## 📚 Tài Liệu Liên Quan

- **Laravel Hash**: https://laravel.com/docs/hashing
- **Laravel Middleware**: https://laravel.com/docs/middleware
- **Laravel Validation**: https://laravel.com/docs/validation

---

## ✅ Checklist Hoàn Thành

- [x] Tạo middleware `RequirePasswordChange`
- [x] Sửa method `postLogin()` trong `UserController`
- [x] Tạo method `changePassword()` trong `UserController`
- [x] Tạo method `postChangePassword()` trong `UserController`
- [x] Tạo view `change-password.blade.php`
- [x] Thêm routes cho đổi mật khẩu
- [x] Đăng ký middleware trong `Kernel.php`
- [ich] Áp dụng middleware vào admin route group
- [x] Validation đầy đủ
- [x] Logging hoạt động
- [x] UI/UX thân thiện
- [x] Xử lý lỗi và thông báo

---

**Tạo bởi:** AI Assistant  
**Ngày tạo:** 2025-01-XX  
**Phiên bản:** 1.0

