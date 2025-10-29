<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chỉ log khi user đã đăng nhập
        if (Auth::check()) {
            $user = Auth::user();
            
            // Lấy route name hoặc path
            $routeName = $request->route() ? $request->route()->getName() : null;
            $path = $request->path();
            $method = $request->method();
            
            // Xác định action và module dựa trên route hoặc path
            $actionModule = $this->determineActionAndModule($routeName, $path, $method);
            
            if ($actionModule) {
                // Không log action "view" và "search" để giảm số lượng log không cần thiết
                if (in_array($actionModule['action'], ['view', 'search'])) {
                    return $response;
                }
                
                // Lấy record ID nếu có từ route parameters
                $recordId = $this->getRecordId($request);
                
                // Tạo description
                $description = $this->generateDescription($actionModule['action'], $actionModule['module'], $recordId);
                
                // Log activity
                UserActivityLog::create([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'action' => $actionModule['action'],
                    'module' => $actionModule['module'],
                    'record_id' => $recordId,
                    'description' => $description,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'url' => $request->fullUrl(),
                    'method' => $method,
                    'new_values' => $method === 'POST' ? $request->except(['_token', 'password', 'password_confirmation']) : null,
                ]);
            }
        }

        return $response;
    }

    /**
     * Xác định action và module từ route name hoặc path
     */
    private function determineActionAndModule($routeName, $path, $method)
    {
        // Mapping từ route name sang action và module
        $routeMapping = [
            // Orders
            'save-orders' => ['action' => 'create', 'module' => 'orders'],
            'update-order' => ['action' => 'update', 'module' => 'orders'],
            'delete-order' => ['action' => 'delete', 'module' => 'orders'],
            'view-order' => ['action' => 'view', 'module' => 'orders'],
            'search-order' => ['action' => 'search', 'module' => 'orders'],
            'cancel-order' => ['action' => 'update', 'module' => 'orders'],
            'back-order' => ['action' => 'update', 'module' => 'orders'],
            
            // Products
            'save-product' => ['action' => 'create', 'module' => 'products'],
            'update-product' => ['action' => 'update', 'module' => 'products'],
            'delete-product' => ['action' => 'delete', 'module' => 'products'],
            'search-product' => ['action' => 'search', 'module' => 'products'],
            
            // Sale Care
            'sale-care-save' => ['action' => 'create', 'module' => 'sale_care'],
            'sale-care-update' => ['action' => 'update', 'module' => 'sale_care'],
            'update-sale-care' => ['action' => 'update', 'module' => 'sale_care'],
            'sale-delete' => ['action' => 'delete', 'module' => 'sale_care'],
            'search-sale-care' => ['action' => 'search', 'module' => 'sale_care'],
            
            // Users
            'save-user' => ['action' => 'create', 'module' => 'users'],
            'update-user' => ['action' => 'update', 'module' => 'users'],
            'delete-user' => ['action' => 'delete', 'module' => 'users'],
            'search-user' => ['action' => 'search', 'module' => 'users'],
            
            // Shipping
            'create-order-GHN' => ['action' => 'create', 'module' => 'shipping'],
            'create-order-GHTK' => ['action' => 'create', 'module' => 'shipping'],
            'create-order-VTPost' => ['action' => 'create', 'module' => 'shipping'],
            'remove-shipping-order' => ['action' => 'delete', 'module' => 'shipping'],
            'print-order-code-GHTK' => ['action' => 'print', 'module' => 'shipping'],
            'print-order-code-GHN' => ['action' => 'print', 'module' => 'shipping'],
            'print-order-code-VTPOST' => ['action' => 'print', 'module' => 'shipping'],
            
            // Groups
            'save-group' => ['action' => 'create', 'module' => 'groups'],
            'update-group' => ['action' => 'update', 'module' => 'groups'],
            'delete-group' => ['action' => 'delete', 'module' => 'groups'],
            
            // Spam
            'save-spam' => ['action' => 'create', 'module' => 'spam'],
            'delete-spam' => ['action' => 'delete', 'module' => 'spam'],
            'search-spam' => ['action' => 'search', 'module' => 'spam'],
            
            // Category
            'save-category' => ['action' => 'create', 'module' => 'categories'],
            'update-category' => ['action' => 'update', 'module' => 'categories'],
            'delete-category' => ['action' => 'delete', 'module' => 'categories'],
        ];

        if ($routeName && isset($routeMapping[$routeName])) {
            return $routeMapping[$routeName];
        }

        // Fallback: phân tích từ path
        return $this->parsePathForActionModule($path, $method);
    }

    /**
     * Parse path để xác định action và module
     */
    private function parsePathForActionModule($path, $method)
    {
        // Không log một số path không cần thiết
        $ignorePaths = [
            'filter-total',
            'filter-total-sales',
            'filter-total-cskh-dt',
            'filter-total-digital',
            'api-',
            'get-',
        ];

        foreach ($ignorePaths as $ignore) {
            if (strpos($path, $ignore) !== false) {
                return null;
            }
        }

        // Xác định action từ method và keywords trong path
        $action = 'view';
        
        if ($method === 'POST') {
            if (strpos($path, 'save') !== false || strpos($path, 'them') !== false) {
                $action = 'create';
            } elseif (strpos($path, 'update') !== false || strpos($path, 'cap-nhat') !== false) {
                $action = 'update';
            }
        } elseif ($method === 'GET') {
            if (strpos($path, 'delete') !== false || strpos($path, 'xoa') !== false) {
                $action = 'delete';
            } elseif (strpos($path, 'search') !== false || strpos($path, 'tim') !== false) {
                $action = 'search';
            } elseif (strpos($path, 'print') !== false || strpos($path, 'in-don') !== false) {
                $action = 'print';
            }
        }

        // Xác định module từ path
        $module = 'general';
        
        if (strpos($path, 'don-hang') !== false || strpos($path, 'order') !== false) {
            $module = 'orders';
        } elseif (strpos($path, 'san-pham') !== false || strpos($path, 'product') !== false) {
            $module = 'products';
        } elseif (strpos($path, 'sale') !== false || strpos($path, 'tac-nghiep') !== false) {
            $module = 'sale_care';
        } elseif (strpos($path, 'thanh-vien') !== false || strpos($path, 'user') !== false) {
            $module = 'users';
        } elseif (strpos($path, 'van-don') !== false || strpos($path, 'shipping') !== false) {
            $module = 'shipping';
        } elseif (strpos($path, 'nhom') !== false || strpos($path, 'group') !== false) {
            $module = 'groups';
        } elseif (strpos($path, 'spam') !== false) {
            $module = 'spam';
        } elseif (strpos($path, 'danh-muc') !== false || strpos($path, 'category') !== false) {
            $module = 'categories';
        }

        return ['action' => $action, 'module' => $module];
    }

    /**
     * Lấy record ID từ route parameters
     */
    private function getRecordId(Request $request)
    {
        $route = $request->route();
        
        if (!$route) {
            return null;
        }

        // Thử lấy các tham số thường gặp
        $possibleIds = ['id', 'order_id', 'product_id', 'user_id', 'saleId'];
        
        foreach ($possibleIds as $param) {
            if ($route->hasParameter($param)) {
                return $route->parameter($param);
            }
        }

        return null;
    }

    /**
     * Tạo description chi tiết
     */
    private function generateDescription($action, $module, $recordId)
    {
        $actionLabels = [
            'create' => 'Tạo mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'view' => 'Xem',
            'search' => 'Tìm kiếm',
            'print' => 'In',
            'export' => 'Xuất file',
        ];

        $moduleLabels = [
            'orders' => 'đơn hàng',
            'products' => 'sản phẩm',
            'sale_care' => 'tác nghiệp sale',
            'users' => 'người dùng',
            'shipping' => 'vận đơn',
            'groups' => 'nhóm',
            'spam' => 'spam',
            'categories' => 'danh mục',
            'general' => 'chung',
        ];

        $actionLabel = $actionLabels[$action] ?? $action;
        $moduleLabel = $moduleLabels[$module] ?? $module;

        $description = "{$actionLabel} {$moduleLabel}";
        
        if ($recordId) {
            $description .= " (ID: {$recordId})";
        }

        return $description;
    }
}

