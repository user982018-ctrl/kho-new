<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserActivityLogController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử hoạt động
     */
    public function index(Request $request)
    {
        $query = UserActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter theo user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter theo module
        if ($request->has('module') && $request->module != '') {
            $query->where('module', $request->module);
        }

        // Filter theo action
        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }

        // Filter theo date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search theo description
        if ($request->has('search') && $request->search != '') {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50);

        // Lấy danh sách users để filter
        $users = User::orderBy('name')->get();

        // Lấy danh sách modules và actions
        $modules = UserActivityLog::select('module')->distinct()->pluck('module');
        $actions = UserActivityLog::select('action')->distinct()->pluck('action');

        return view('pages.user-activity-logs.index', compact('logs', 'users', 'modules', 'actions'));
    }

    /**
     * Xem chi tiết một log
     */
    public function view($id)
    {
        $log = UserActivityLog::with('user')->findOrFail($id);
        
        // Kiểm tra nếu request từ iframe/modal (không có referrer hoặc có query param)
        if (request()->has('modal') || request()->ajax()) {
            return view('pages.user-activity-logs.detail-modal', compact('log'));
        }
        
        return view('pages.user-activity-logs.detail', compact('log'));
    }

    /**
     * Xem lịch sử của một user cụ thể
     */
    public function userHistory($userId)
    {
        $user = User::findOrFail($userId);
        $logs = UserActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Kiểm tra nếu request từ iframe/modal
        if (request()->has('modal') || request()->ajax()) {
            return view('pages.user-activity-logs.user-history-modal', compact('user', 'logs'));
        }

        return view('pages.user-activity-logs.user-history', compact('user', 'logs'));
    }

    /**
     * Thống kê hoạt động theo user
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Thống kê theo user
        $userStats = UserActivityLog::select('user_activity_logs.user_id', 
                DB::raw('COALESCE(users.real_name, user_activity_logs.user_name) as user_name'),
                DB::raw('count(*) as total_actions'))
            ->leftJoin('users', 'user_activity_logs.user_id', '=', 'users.id')
            ->whereBetween('user_activity_logs.created_at', [$startDate, $endDate])
            ->groupBy('user_activity_logs.user_id', 'users.real_name', 'user_activity_logs.user_name')
            ->orderBy('total_actions', 'desc')
            ->get();

        // Thống kê theo module
        $moduleStats = UserActivityLog::select('module', DB::raw('count(*) as total_actions'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('module')
            ->orderBy('total_actions', 'desc')
            ->get();

        // Thống kê theo action
        $actionStats = UserActivityLog::select('action', DB::raw('count(*) as total_actions'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('action')
            ->orderBy('total_actions', 'desc')
            ->get();

        // Thống kê theo ngày
        $dailyStats = UserActivityLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total_actions')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('pages.user-activity-logs.statistics', compact(
            'userStats',
            'moduleStats',
            'actionStats',
            'dailyStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Xóa log cũ (chỉ admin mới được xóa)
     */
    public function deleteOldLogs(Request $request)
    {
        $daysToKeep = $request->get('days', 90); // Mặc định giữ 90 ngày
        
        $deletedCount = UserActivityLog::where('created_at', '<', now()->subDays($daysToKeep))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$deletedCount} bản ghi log cũ hơn {$daysToKeep} ngày",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Export log ra Excel
     */
    public function export(Request $request)
    {
        $query = UserActivityLog::with('user')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('module') && $request->module != '') {
            $query->where('module', $request->module);
        }

        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->get();

        // Tạo CSV
        $filename = 'user_activity_logs_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'ID',
                'Người dùng',
                'Hành động',
                'Module',
                'ID bản ghi',
                'Mô tả',
                'IP Address',
                'URL',
                'Method',
                'Thời gian'
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user_name,
                    $log->action_label,
                    $log->module_label,
                    $log->record_id,
                    $log->description,
                    $log->ip_address,
                    $log->url,
                    $log->method,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Lấy hoạt động gần nhất của user
     */
    public function recentActivities($userId, $limit = 10)
    {
        $logs = UserActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * API: So sánh thay đổi (old_values vs new_values)
     */
    public function compareChanges($id)
    {
        $log = UserActivityLog::findOrFail($id);

        if (!$log->old_values && !$log->new_values) {
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu thay đổi'
            ]);
        }

        $changes = [];
        
        if ($log->old_values && $log->new_values) {
            foreach ($log->new_values as $key => $newValue) {
                $oldValue = $log->old_values[$key] ?? null;
                
                if ($oldValue != $newValue) {
                    $changes[] = [
                        'field' => $key,
                        'old_value' => $oldValue,
                        'new_value' => $newValue
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'log' => $log,
            'changes' => $changes
        ]);
    }
}

