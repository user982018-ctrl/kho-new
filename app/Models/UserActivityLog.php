<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'record_id',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Log activity - Static method để dễ gọi từ bất kỳ đâu
     */
    public static function logActivity($action, $module, $recordId = null, $description = null, $oldValues = null, $newValues = null)
    {
        $user = Auth::user();
        
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Guest',
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin($userId, $userName)
    {
        return self::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => 'login',
            'module' => 'auth',
            'description' => 'Đăng nhập hệ thống',
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
        ]);
    }

    /**
     * Log logout activity
     */
    public static function logLogout($userId, $userName)
    {
        return self::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => 'logout',
            'module' => 'auth',
            'description' => 'Đăng xuất hệ thống',
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
        ]);
    }

    /**
     * Get action label in Vietnamese
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'create' => 'Tạo mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'view' => 'Xem',
            'login' => 'Đăng nhập',
            'logout' => 'Đăng xuất',
            'export' => 'Xuất file',
            'import' => 'Nhập file',
            'print' => 'In',
            'search' => 'Tìm kiếm',
        ];

        return $labels[$this->action] ?? $this->action;
    }

    /**
     * Get module label in Vietnamese
     */
    public function getModuleLabelAttribute()
    {
        $labels = [
            'auth' => 'Xác thực',
            'orders' => 'Đơn hàng',
            'products' => 'Sản phẩm',
            'sale_care' => 'Tác nghiệp Sale',
            'users' => 'Người dùng',
            'categories' => 'Danh mục',
            'shipping' => 'Vận đơn',
            'groups' => 'Nhóm',
            'src_pages' => 'Nguồn',
            'spam' => 'Spam',
            'settings' => 'Cài đặt',
        ];

        return $labels[$this->module] ?? $this->module;
    }

    /**
     * Scope để filter theo user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope để filter theo module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope để filter theo action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope để filter theo date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

