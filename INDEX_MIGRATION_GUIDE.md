# Hướng dẫn chạy Migration Index cho Database

## Tổng quan

Migration này thêm các index vào các bảng quan trọng để tối ưu hiệu suất truy vấn database.

## Các Index được thêm

### 1. Bảng `sale_care` (13 index đơn + 4 composite)
- `phone` - Tìm kiếm theo số điện thoại
- `src_id` - JOIN với src_page
- `group_id` - Lọc theo group
- `assign_user` - Lọc theo người được gán
- `old_customer` - Lọc theo loại khách hàng
- `created_at` - Sắp xếp và lọc theo ngày tạo
- `type_TN` - Lọc theo loại tác nghiệp
- `has_TN` - Lọc theo trạng thái có TN
- `result_call` - Lọc theo kết quả cuộc gọi
- `id_order_new` - Lọc theo đơn hàng mới
- `page_id` - Lọc theo page ID
- `full_name` - Tìm kiếm theo tên
- Composite indexes cho các truy vấn phức tạp

### 2. Bảng `orders` (3 index đơn + 2 composite)
- `sale_care` - JOIN với sale_care
- `status` - Lọc theo trạng thái đơn hàng
- `created_at` - Sắp xếp và lọc theo ngày tạo
- Composite indexes cho truy vấn phức tạp

### 3. Bảng `shipping_order` (5 index đơn + 2 composite)
- `order_id` - JOIN với orders
- `vendor_ship` - Lọc theo vendor (GHTK, GHN)
- `print_status` - Lọc theo trạng thái in
- `check_cron` - Lọc theo check cron
- `order_code` - Tìm kiếm theo mã đơn hàng
- Composite indexes cho truy vấn phức tạp

### 4. Bảng `src_page` (4 index đơn + 1 composite)
- `id_page` - Tìm kiếm theo page ID
- `user_digital` - Lọc theo user digital
- `status` - Lọc theo trạng thái
- `type` - Lọc theo loại (pc, ladi, etc.)
- Composite index cho truy vấn phức tạp

## Cách chạy Migration

### Chạy migration:
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/kho
php artisan migrate
```

### Rollback nếu có vấn đề:
```bash
php artisan migrate:rollback --step=1
```

### Xem trạng thái migration:
```bash
php artisan migrate:status
```

## Lưu ý

1. **Thời gian chạy**: Migration có thể mất vài phút tùy vào số lượng dữ liệu trong database
2. **Backup**: Nên backup database trước khi chạy migration
3. **Kiểm tra**: Migration sẽ tự động kiểm tra index đã tồn tại chưa, không bị lỗi nếu chạy lại
4. **Performance**: Sau khi chạy migration, các truy vấn sẽ nhanh hơn đáng kể

## Tối ưu thêm

Sau khi chạy migration, có thể chạy các lệnh sau để tối ưu database:

```sql
ANALYZE TABLE sale_care;
ANALYZE TABLE orders;
ANALYZE TABLE shipping_order;
ANALYZE TABLE src_page;

OPTIMIZE TABLE sale_care;
OPTIMIZE TABLE orders;
OPTIMIZE TABLE shipping_order;
OPTIMIZE TABLE src_page;
```

## Kiểm tra Index đã được tạo

```sql
-- Xem tất cả index của bảng sale_care
SHOW INDEX FROM sale_care;

-- Xem tất cả index của bảng orders
SHOW INDEX FROM orders;

-- Xem tất cả index của bảng shipping_order
SHOW INDEX FROM shipping_order;

-- Xem tất cả index của bảng src_page
SHOW INDEX FROM src_page;
```



