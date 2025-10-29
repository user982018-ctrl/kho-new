@extends('layouts.modal')

@section('title', 'Lịch sử hoạt động - ' . ($user->real_name ?? $user->name))

@section('styles')
<style>
    .user-info-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 20px;
        border-radius: 8px 8px 0 0;
        /* margin: -20px -20px 20px -20px; */
    }
    
    .user-info-header h4 {
        margin: 0 0 10px 0;
        font-weight: 600;
    }
    
    .user-info-header p {
        margin: 0;
        opacity: 0.9;
    }
    
    .timeline-container {
        padding: 20px;
    }
    
    .activity-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
        transition: all 0.2s;
    }
    
    .activity-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #28a745;
    }
    
    .activity-time {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    
    .activity-description {
        font-size: 14px;
        color: #212529;
        margin-bottom: 8px;
    }
    
    .activity-meta {
        font-size: 12px;
        color: #6c757d;
    }
    
    .activity-badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    
    .badge-create { background-color: #28a745; color: white; }
    .badge-update { background-color: #ffc107; color: #212529; }
    .badge-delete { background-color: #dc3545; color: white; }
    .badge-login { background-color: #6c757d; color: white; }
    .badge-logout { background-color: #6c757d; color: white; }
    .badge-print { background-color: #007bff; color: white; }
    .badge-export { background-color: #20c997; color: white; }
    
    .pagination-wrapper {
        margin-top: 20px;
        text-align: center;
    }
    
    .no-data {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="user-info-header">
    <h4><i class="fa fa-user-circle"></i> {{$user->real_name ?? $user->name}}</h4>
    <p>
        <i class="fa fa-user"></i> Username: <strong>{{$user->name}}</strong> &nbsp;|&nbsp;
        <i class="fa fa-id-badge"></i> ID: <strong>{{$user->id}}</strong> &nbsp;|&nbsp;
        <i class="fa fa-clock-o"></i> Tổng hoạt động: <strong>{{$logs->total()}}</strong>
    </p>
</div>

<div class="timeline-container">
    @if($logs->count() > 0)
        @foreach($logs as $log)
        <div class="activity-item">
            <div class="activity-time">
                <i class="fa fa-calendar"></i> {{$log->created_at->format('d/m/Y H:i:s')}}
            </div>
            
            <div class="activity-description">
                <strong>{{$log->description}}</strong>
            </div>
            
            <div class="activity-meta">
                <span class="activity-badge badge-{{$log->action}}">
                    {{$log->action_label}}
                </span>
                <span class="badge badge-secondary">{{$log->module_label}}</span>
                
                @if($log->record_id)
                    <span class="badge badge-info">ID: {{$log->record_id}}</span>
                @endif
                
                <span style="margin-left: 10px;">
                    <i class="fa fa-globe"></i> {{$log->ip_address}}
                </span>
                
                <span style="margin-left: 10px;">
                    <span class="badge badge-{{$log->method == 'GET' ? 'info' : 'success'}}">{{$log->method}}</span>
                </span>
            </div>
        </div>
        @endforeach
        
        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $logs->appends(['modal' => 1])->links() }}
        </div>
    @else
        <div class="no-data">
            <i class="fa fa-info-circle fa-3x" style="color: #dee2e6;"></i>
            <p style="margin-top: 15px;">Người dùng này chưa có hoạt động nào.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Không cần script đặc biệt
</script>
@endsection

