# Hướng dẫn Debug trong Laravel/PHP

## 1. Sử dụng Laravel Debug Helpers (Nhanh nhất)

### `dd()` - Dump and Die (Dừng execution)
```php
dd($variable); // Dump và dừng ngay
dd($var1, $var2, $var3); // Dump nhiều biến

// Example:
$product = getProductByIdHelper($item['id']);
dd($product); // Dừng ở đây và hiển thị $product
```

### `dump()` - Dump nhưng không dừng
```php
dump($variable); // Dump nhưng tiếp tục chạy
dump($var1, $var2);
```

### `ds()` - Dump with stack trace
```php
ds($variable); // Dump với stack trace
```

## 2. Logging để debug từng bước

```php
use Illuminate\Support\Facades\Log;

// Log với các level khác nhau
Log::debug('Debug message', ['data' => $variable]);
Log::info('Info message');
Log::warning('Warning message');
Log::error('Error message', ['error' => $exception]);

// Example trong function:
foreach ($listProduct as $key => $item) {
    Log::debug("Processing item #$key", ['item' => $item]);
    
    $product = getProductByIdHelper($item['id']);
    Log::debug('Product loaded', ['product_id' => $product->id, 'product' => $product]);
    
    // ... code tiếp theo
}
```

File log nằm tại: `storage/logs/laravel.log`

## 3. Sử dụng Xdebug thật sự (Giống IDE debugger)

### Bước 1: Cài đặt Xdebug

**Trên macOS với XAMPP:**
```bash
# Kiểm tra PHP version
php -v

# Cài Xdebug bằng PECL (nếu có)
pecl install xdebug

# Hoặc download từ: https://xdebug.org/download
```

**Hoặc sử dụng Homebrew:**
```bash
brew install php-xdebug
```

### Bước 2: Cấu hình php.ini

Tìm file `php.ini` trong XAMPP (thường ở `/Applications/XAMPP/xamppfiles/etc/php.ini`):

```ini
[xdebug]
zend_extension="/Applications/XAMPP/xamppfiles/lib/php/extensions/no-debug-non-zts-xxxx/xdebug.so"
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_host=127.0.0.1
xdebug.client_port=9003
xdebug.idekey=PHPSTORM
```

### Bước 3: Cấu hình VS Code

1. Cài extension: **PHP Debug** (by Xdebug)
2. Tạo file `.vscode/launch.json`:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/Applications/XAMPP/xamppfiles/htdocs/kho": "${workspaceFolder}"
            },
            "log": true
        },
        {
            "name": "Launch currently open script",
            "type": "php",
            "request": "launch",
            "program": "${file}",
            "cwd": "${fileDirname}",
            "port": 9003
        }
    ]
}
```

### Bước 4: Sử dụng Breakpoints

1. Click vào số dòng bên trái để đặt breakpoint (chấm đỏ)
2. Bấm F5 hoặc chạy debug
3. Code sẽ dừng tại breakpoint
4. Xem variables, call stack, watch expressions

## 4. Sử dụng Laravel Telescope (Nếu đã cài)

```php
// Mở http://your-domain/telescope
// Xem tất cả queries, requests, logs
```

## 5. Debug trong Browser với Laravel Debugbar

Cài đặt:
```bash
composer require barryvdh/laravel-debugbar --dev
```

Tự động hiển thị debug bar ở dưới cùng trang web.

## 6. Tạo Helper Function để Debug từng dòng

Thêm vào `app/helpers.php` hoặc `app/Helpers/Helper.php`:

```php
if (!function_exists('dbg')) {
    /**
     * Debug helper - log và hiển thị variable
     */
    function dbg($var, $label = null, $stop = false) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $file = basename($trace[0]['file']);
        $line = $trace[0]['line'];
        $function = isset($trace[1]['function']) ? $trace[1]['function'] : '';
        
        $output = "\n" . str_repeat('=', 80) . "\n";
        $output .= "DEBUG: $file:$line";
        if ($function) $output .= " in $function()";
        if ($label) $output .= " [$label]";
        $output .= "\n" . str_repeat('-', 80) . "\n";
        $output .= print_r($var, true);
        $output .= "\n" . str_repeat('=', 80) . "\n";
        
        error_log($output);
        
        if ($stop) {
            dd($var);
        } else {
            dump($var);
        }
    }
}
```

Sử dụng:
```php
dbg($product, 'Product loaded'); // Không dừng
dbg($product, 'Product loaded', true); // Dừng execution
```

## 7. Debug trong Console/Artisan Commands

```php
// Trong artisan command
$this->info('Processing...');
$this->line('Step 1 complete');
$this->error('Error occurred');
```

## 8. Sử dụng Tinker để debug

```bash
php artisan tinker

# Trong tinker:
$product = \App\Models\Product::find(1);
$product->name
dd($product)
```

## 9. Debug với var_dump và die

```php
echo "<pre>";
var_dump($variable);
print_r($variable);
die("Stopped here");
```

## 10. Best Practices

### Debug một đoạn code cụ thể:
```php
Log::debug('=== START PROCESSING ===');
foreach ($items as $item) {
    Log::debug('Item', ['id' => $item->id, 'data' => $item->toArray()]);
    // Process...
}
Log::debug('=== END PROCESSING ===');
```

### Debug với context:
```php
Log::channel('single')->debug('Processing order', [
    'order_id' => $order->id,
    'products' => $order->products->toArray(),
    'total' => $order->total
]);
```

## Khuyến nghị

- **Phát triển nhanh**: Dùng `dd()`, `dump()`
- **Debug phức tạp**: Cài Xdebug + VS Code
- **Theo dõi production**: Dùng `Log::`
- **Profile performance**: Dùng Laravel Telescope

