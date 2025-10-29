@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('public/css/dashboard.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>
    .user-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .user-info-card h3 {
        color: white;
        margin-bottom: 10px;
    }
    
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    
    .timeline-item {
        position: relative;
        padding: 15px;
        margin-bottom: 15px;
        background: white;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -23px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 2px solid white;
    }
    
    .timeline-date {
        font-size: 12px;
        color: #999;
        margin-bottom: 5px;
    }
    
    .timeline-content {
        margin-top: 10px;
    }
</style>

<div class="content-wrapper" style="min-height: 779px;">
    <div id="dnn_ContentPane" class="contentPane">
        <div class="box-body m-header-wrap">
            <div class="m-header row">
                <div class="col-sm-6 form-group">
                    <h4><i class="fa fa-history"></i> Lịch Sử Hoạt Động Của User</h4>
                </div>
                <div class="col-sm-6 form-group text-right">
                    <a href="{{route('user-activity-logs')}}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
        
        <div class="box-body">
            <!-- User Info Card -->
            <div class="user-info-card">
                <h3><i class="fa fa-user-circle"></i> {{$user->real_name ?? $user->name}}</h3>
                <p class="mb-0">
                    <i class="fa fa-user"></i> Username: <code>{{$user->name}}</code><br>
                    <i class="fa fa-envelope"></i> Email: {{$user->email ?? 'N/A'}}<br>
                    <i class="fa fa-id-badge"></i> ID: {{$user->id}}<br>
                    <i class="fa fa-clock-o"></i> Tổng số hoạt động: <strong>{{$logs->total()}}</strong>
                </p>
            </div>
            
            <!-- Activity Timeline -->
            <div class="row">
                <div class="col-md-12">
                    @if($logs->count() > 0)
                    <div class="activity-timeline">
                        @foreach($logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-date">
                                <i class="fa fa-calendar"></i> {{$log->created_at->format('d/m/Y H:i:s')}}
                            </div>
                            
                            <div class="timeline-content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>{{$log->description}}</strong>
                                        <br>
                                        <span class="activity-badge badge-{{$log->action}}">
                                            {{$log->action_label}}
                                        </span>
                                        <span class="badge badge-secondary">{{$log->module_label}}</span>
                                        
                                        @if($log->record_id)
                                        <span class="badge badge-info">ID: {{$log->record_id}}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <small class="text-muted">
                                            <i class="fa fa-globe"></i> {{$log->ip_address}}<br>
                                            <i class="fa fa-link"></i> {{$log->method}}
                                        </small>
                                        <br>
                                        <a href="{{route('user-activity-log-detail', $log->id)}}" 
                                           class="btn btn-sm btn-info mt-2">
                                            <i class="fa fa-eye"></i> Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination-wrapper mt-4">
                        {{ $logs->links() }}
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle"></i> Người dùng này chưa có hoạt động nào.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@stop

