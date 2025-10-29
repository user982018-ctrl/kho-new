@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('public/css/dashboard.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('public/css/pages/rank.css') }}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{ asset('public/js/moment.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('public/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<style>
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
    .badge-view { background-color: #17a2b8; color: white; }
    .badge-login { background-color: #6c757d; color: white; }
    .badge-logout { background-color: #6c757d; color: white; }
    .badge-print { background-color: #007bff; color: white; }
    .badge-search { background-color: #e83e8c; color: white; }
    .badge-export { background-color: #20c997; color: white; }

    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        /* margin-bottom: 20px; */
        border-radius: 5px;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>

<?php 
$checkAll = isFullAccess(Auth::user()->role);
$isLeadSale = Helper::isLeadSale(Auth::user()->role);
$access = $checkAll || $isLeadSale;
?>

@include('notify::components.notify')

<div class="content-wrapper" style="min-height: 779px;">
<div id="dnn_ContentPane" class="contentPane">
    <div class="box-body m-header-wrap">
        <div class="m-header row">
            <div class="col-sm-6 form-group">
                <span class="text form-group"><h4><i class="fa fa-history"></i> Lịch Sử Hoạt Động</h4></span>
            </div>
            <div class="col-sm-6 form-group text-right">
                <a href="{{route('user-activity-statistics')}}" class="btn btn-sm btn-info">
                    <i class="fa fa-bar-chart"></i> Thống kê
                </a>
                <a href="{{route('export-activity-logs')}}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}" class="btn btn-sm btn-success">
                    <i class="fa fa-download"></i> Xuất Excel
                </a>
            </div>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="box-body">
        <div class="filter-section">
            <form method="GET" action="{{route('user-activity-logs')}}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Người dùng</label>
                            <select name="user_id" class="form-control select2">
                                <option value="">-- Tất cả --</option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{$user->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Module</label>
                            <select name="module" class="form-control">
                                <option value="">-- Tất cả --</option>
                                @foreach($modules as $module)
                                    <option value="{{$module}}" {{ request('module') == $module ? 'selected' : '' }}>
                                        {{ucfirst($module)}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Hành động</label>
                            <select name="action" class="form-control">
                                <option value="">-- Tất cả --</option>
                                @foreach($actions as $action)
                                    <option value="{{$action}}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ucfirst($action)}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Từ ngày</label>
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Đến ngày</label>
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}">
                        </div>
                    </div>
                    
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-filter"></i> Lọc
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <div style="width: 100%; overflow: hidden; overflow-x: auto;">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th class="text-center" style="width: 40px;">#</th>
                                <th class="text-center" style="width: 150px;">Thời gian</th>
                                <th class="text-center" style="width: 120px;">Người dùng</th>
                                <th class="text-center" style="width: 100px;">Hành động</th>
                                <th class="text-center" style="width: 100px;">Module</th>
                                <th class="text-center">Mô tả</th>
                                <th class="text-center" style="width: 100px;">IP Address</th>
                                <th class="text-center" style="width: 80px;">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($logs->count() > 0)
                                @foreach ($logs as $index => $log)
                                <tr>
                                    <td class="text-center">{{$logs->firstItem() + $index}}</td>
                                    <td class="text-center no-wrap">
                                        {{$log->created_at->format('d/m/Y H:i:s')}}
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:void(0)" 
                                           class="text-primary btn-view-user-history" 
                                           data-user-id="{{$log->user_id}}"
                                           data-user-name="{{$log->user ? $log->user->real_name : $log->user_name}}"
                                           style="cursor: pointer;">
                                            {{$log->user ? $log->user->real_name : $log->user_name}}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="activity-badge badge-{{$log->action}}">
                                            {{$log->action_label}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="">{{$log->module_label}}</span>
                                    </td>
                                    <td>{{$log->description}}</td>
                                    <td class="text-center">
                                        <small>{{$log->ip_address}}</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-info btn-view-detail"
                                                data-id="{{$log->id}}"
                                                title="Xem chi tiết">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <p class="text-muted" style="padding: 20px;">Không có dữ liệu</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>

                    <div style="height: 100px;"></div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Modal Chi tiết Log -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px; height: 90%;">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fa fa-info-circle"></i> Chi Tiết Hoạt Động
                </h5>
                <button type="button" class="close btn-close-modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; height: calc(100% - 60px);">
                <iframe id="detailIframe" src="" frameborder="0" style="width: 100%; height: 100%;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lịch sử User -->
<div class="modal fade" id="userHistoryModal" tabindex="-1" role="dialog" aria-labelledby="userHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px; height: 90%;">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                <h5 class="modal-title" id="userHistoryModalLabel">
                    <i class="fa fa-history"></i> Lịch Sử Hoạt Động - <span id="modalUserName"></span>
                </h5>
                <button type="button" class="close btn-close-user-modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; height: calc(100% - 60px);">
                <iframe id="userHistoryIframe" src="" frameborder="0" style="width: 100%; height: 100%;"></iframe>
            </div>
        </div>
    </div>
</div>

<style>
#detailModal .modal-dialog {
    margin: 30px auto;
}

#detailModal iframe {
    border: none;
    background: white;
}
</style>

<script>
$(document).ready(function() {
    // Lấy origin URL của browser
    var origin = window.location.origin;
    var baseUrl = origin;
    
    console.log('Origin:', origin); // http://localhost
    console.log('Base URL:', baseUrl); // http://localhost/kho
    
    $('.select2').select2({
        width: '100%'
    });

    // Xử lý click vào nút xem chi tiết
    $('.btn-view-detail').on('click', function() {
        var logId = $(this).data('id');
        
        // Set URL cho iframe với parameter modal=1
        var url = baseUrl + '/kho/lich-su-hoat-dong/' + logId + '?modal=1';
        $('#detailIframe').attr('src', url);
        
        // Hiển thị modal
        $('#detailModal').modal('show');
    });

    // Xử lý click vào tên user - xem lịch sử
    $('.btn-view-user-history').on('click', function() {
        var userId = $(this).data('user-id');
        var userName = $(this).data('user-name');
        
        // Set tên user vào modal title
        $('#modalUserName').text(userName);
        
        // Set URL cho iframe
        var url = baseUrl + '/kho/lich-su-user/' + userId + '?modal=1';
        $('#userHistoryIframe').attr('src', url);
        
        // Hiển thị modal
        $('#userHistoryModal').modal('show');
    });

    // Xử lý click vào nút đóng modal chi tiết
    $('.btn-close-modal').on('click', function() {
        $('#detailModal').modal('hide');
    });

    // Xử lý click vào nút đóng modal user history
    $('.btn-close-user-modal').on('click', function() {
        $('#userHistoryModal').modal('hide');
    });

    // Đóng modal khi click bên ngoài
    $('#detailModal').on('click', function(e) {
        if ($(e.target).hasClass('modal')) {
            $('#detailModal').modal('hide');
        }
    });

    $('#userHistoryModal').on('click', function(e) {
        if ($(e.target).hasClass('modal')) {
            $('#userHistoryModal').modal('hide');
        }
    });

    // Reset iframe khi đóng modal
    $('#detailModal').on('hidden.bs.modal', function () {
        $('#detailIframe').attr('src', '');
    });

    $('#userHistoryModal').on('hidden.bs.modal', function () {
        $('#userHistoryIframe').attr('src', '');
    });
});
</script>

@stop

