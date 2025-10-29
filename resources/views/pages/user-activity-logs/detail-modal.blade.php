@extends('layouts.modal')

@section('title', 'Chi tiết hoạt động')

@section('styles')
<style>
    .detail-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }
    
    .detail-card h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
    }
    
    .detail-row {
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #6c757d;
        width: 180px;
        display: inline-block;
        vertical-align: top;
    }
    
    .detail-value {
        color: #212529;
        display: inline-block;
        width: calc(100% - 190px);
    }
    
    .json-viewer {
        background: #2d2d2d;
        color: #f8f8f2;
        padding: 15px;
        border-radius: 5px;
        max-height: 400px;
        overflow-y: auto;
        font-family: 'Courier New', Consolas, Monaco, monospace;
        font-size: 12px;
        line-height: 1.5;
    }
    
    .json-viewer pre {
        margin: 0;
        color: #f8f8f2;
    }
    
    .changes-table {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
    }
    
    .changes-table th {
        background: #e9ecef;
        padding: 10px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border: 1px solid #dee2e6;
    }
    
    .changes-table td {
        padding: 10px;
        vertical-align: top;
        border: 1px solid #dee2e6;
    }
    
    .old-value {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
        font-family: monospace;
    }
    
    .new-value {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
        font-family: monospace;
    }
    
    .header-title {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        /* margin: -20px -20px 20px -20px; */
        border-radius: 8px 8px 0 0;
    }
    
    .header-title h4 {
        margin: 0;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="header-title">
    <h4><i class="fa fa-info-circle"></i> Chi Tiết Hoạt Động #{{$log->id}}</h4>
</div>

<div style="padding: 20px;">
    <!-- Thông tin người dùng -->
    <div class="detail-card">
        <h6><i class="fa fa-user"></i> Thông Tin Người Dùng</h6>
        
        <div class="detail-row">
            <span class="detail-label">ID Log:</span>
            <span class="detail-value"><strong>{{$log->id}}</strong></span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Người dùng:</span>
            <span class="detail-value">
                <strong>{{$log->user ? $log->user->real_name : $log->user_name}}</strong>
                @if($log->user)
                    <span class="badge badge-secondary">ID: {{$log->user_id}}</span>
                    @if($log->user->name)
                        <span class="badge badge-info">Username: {{$log->user->name}}</span>
                    @endif
                @endif
            </span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Thời gian:</span>
            <span class="detail-value">{{$log->created_at->format('d/m/Y H:i:s')}}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">IP Address:</span>
            <span class="detail-value"><code>{{$log->ip_address}}</code></span>
        </div>
    </div>
    
    <!-- Thông tin hoạt động -->
    <div class="detail-card">
        <h6><i class="fa fa-cog"></i> Thông Tin Hoạt Động</h6>
        
        <div class="detail-row">
            <span class="detail-label">Hành động:</span>
            <span class="detail-value">
                <span class="activity-badge badge-{{$log->action}}">
                    {{$log->action_label}}
                </span>
            </span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Module:</span>
            <span class="detail-value">
                <span class="badge badge-secondary">{{$log->module_label}}</span>
            </span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Mô tả:</span>
            <span class="detail-value">{{$log->description}}</span>
        </div>
        
        @if($log->record_id)
        <div class="detail-row">
            <span class="detail-label">ID bản ghi:</span>
            <span class="detail-value"><span class="badge badge-info">{{$log->record_id}}</span></span>
        </div>
        @endif
    </div>
    
    <!-- Thông tin request -->
    <div class="detail-card">
        <h6><i class="fa fa-globe"></i> Thông Tin Request</h6>
        
        <div class="detail-row">
            <span class="detail-label">URL:</span>
            <span class="detail-value">
                <small style="word-break: break-all;">{{$log->url}}</small>
            </span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">HTTP Method:</span>
            <span class="detail-value">
                <span class="badge badge-{{$log->method == 'GET' ? 'info' : ($log->method == 'POST' ? 'success' : 'warning')}}">
                    {{$log->method}}
                </span>
            </span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">User Agent:</span>
            <span class="detail-value">
                <small style="word-break: break-all;">{{$log->user_agent}}</small>
            </span>
        </div>
    </div>
    
    <!-- Thay đổi dữ liệu -->
    @if($log->old_values || $log->new_values)
    <div class="detail-card">
        <h6><i class="fa fa-exchange"></i> Thay Đổi Dữ Liệu</h6>
        
        <table class="changes-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Trường</th>
                    <th style="width: 37.5%;">Giá trị cũ</th>
                    <th style="width: 37.5%;">Giá trị mới</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $oldValues = $log->old_values ?? [];
                    $newValues = $log->new_values ?? [];
                    $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                    $hasChanges = false;
                @endphp
                
                @foreach($allKeys as $key)
                    @php
                        $oldValue = $oldValues[$key] ?? null;
                        $newValue = $newValues[$key] ?? null;
                        $hasChanged = $oldValue != $newValue;
                    @endphp
                    
                    @if($hasChanged)
                        @php $hasChanges = true; @endphp
                        <tr>
                            <td><strong>{{$key}}</strong></td>
                            <td>
                                <span class="old-value">
                                    {{is_array($oldValue) ? json_encode($oldValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : ($oldValue ?? '-')}}
                                </span>
                            </td>
                            <td>
                                <span class="new-value">
                                    {{is_array($newValue) ? json_encode($newValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : ($newValue ?? '-')}}
                                </span>
                            </td>
                        </tr>
                    @endif
                @endforeach
                
                @if(!$hasChanges)
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <em>Không có thay đổi giá trị</em>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Dữ liệu JSON -->
    @if($log->new_values)
    <div class="detail-card">
        <h6><i class="fa fa-code"></i> Dữ Liệu JSON (Full Data)</h6>
        <div class="json-viewer">
            <pre>{{json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)}}</pre>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Không cần script đặc biệt
</script>
@endsection

