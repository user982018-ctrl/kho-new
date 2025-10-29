@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('public/css/dashboard.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>
    .detail-card {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .detail-row {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #555;
        width: 200px;
        display: inline-block;
    }
    
    .detail-value {
        color: #333;
    }
    
    .json-viewer {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        max-height: 400px;
        overflow-y: auto;
        font-family: monospace;
        font-size: 12px;
    }
    
    .changes-table {
        width: 100%;
        margin-top: 10px;
    }
    
    .changes-table th {
        background: #f8f9fa;
        padding: 10px;
    }
    
    .changes-table td {
        padding: 10px;
        vertical-align: top;
    }
    
    .old-value {
        background: #ffecec;
        padding: 5px;
        border-radius: 3px;
    }
    
    .new-value {
        background: #e7f5e7;
        padding: 5px;
        border-radius: 3px;
    }
</style>

<div class="content-wrapper" style="min-height: 779px;">
    <div id="dnn_ContentPane" class="contentPane">
        <div class="box-body m-header-wrap">
            <div class="m-header row">
                <div class="col-sm-6 form-group">
                    <h4><i class="fa fa-info-circle"></i> Chi Tiết Hoạt Động</h4>
                </div>
                <div class="col-sm-6 form-group text-right">
                    <a href="{{route('user-activity-logs')}}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
        
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="detail-card">
                        <h5 class="mb-3"><i class="fa fa-user"></i> Thông Tin Người Dùng</h5>
                        
                        <div class="detail-row">
                            <span class="detail-label">ID:</span>
                            <span class="detail-value">{{$log->id}}</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Người dùng:</span>
                            <span class="detail-value">
                                <strong>{{$log->user ? $log->user->real_name : $log->user_name}}</strong>
                                @if($log->user)
                                    (ID: {{$log->user_id}})
                                    @if($log->user->name)
                                        - Username: <code>{{$log->user->name}}</code>
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
                            <span class="detail-value">{{$log->ip_address}}</span>
                        </div>
                    </div>
                    
                    <div class="detail-card">
                        <h5 class="mb-3"><i class="fa fa-cog"></i> Thông Tin Hoạt Động</h5>
                        
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
                            <span class="detail-value">{{$log->record_id}}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="detail-card">
                        <h5 class="mb-3"><i class="fa fa-globe"></i> Thông Tin Request</h5>
                        
                        <div class="detail-row">
                            <span class="detail-label">URL:</span>
                            <span class="detail-value">
                                <small>{{$log->url}}</small>
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Method:</span>
                            <span class="detail-value">
                                <span class="badge badge-{{$log->method == 'GET' ? 'info' : 'warning'}}">
                                    {{$log->method}}
                                </span>
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">User Agent:</span>
                            <span class="detail-value">
                                <small>{{$log->user_agent}}</small>
                            </span>
                        </div>
                    </div>
                    
                    @if($log->old_values || $log->new_values)
                    <div class="detail-card">
                        <h5 class="mb-3"><i class="fa fa-exchange"></i> Thay Đổi Dữ Liệu</h5>
                        
                        <table class="table table-bordered changes-table">
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
                                @endphp
                                
                                @foreach($allKeys as $key)
                                    @php
                                        $oldValue = $oldValues[$key] ?? '-';
                                        $newValue = $newValues[$key] ?? '-';
                                        $hasChanged = $oldValue != $newValue;
                                    @endphp
                                    
                                    @if($hasChanged)
                                    <tr>
                                        <td><strong>{{$key}}</strong></td>
                                        <td>
                                            <span class="old-value">
                                                {{is_array($oldValue) ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : $oldValue}}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="new-value">
                                                {{is_array($newValue) ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : $newValue}}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                
                                @if(count($allKeys) == 0)
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Không có thay đổi</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @endif
                    
                    @if($log->new_values)
                    <div class="detail-card">
                        <h5 class="mb-3"><i class="fa fa-code"></i> Dữ Liệu JSON (New Values)</h5>
                        <div class="json-viewer">
                            <pre>{{json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)}}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@stop

