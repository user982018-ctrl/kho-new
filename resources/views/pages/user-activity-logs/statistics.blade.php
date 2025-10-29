@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('public/css/dashboard.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .stat-table {
        width: 100%;
        margin-top: 15px;
    }
    
    .stat-table th {
        background: #f8f9fa;
        padding: 10px;
        text-align: left;
    }
    
    .stat-table td {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 20px;
    }
</style>

<div class="content-wrapper" style="min-height: 779px;">
    <div id="dnn_ContentPane" class="contentPane">
        <div class="box-body m-header-wrap">
            <div class="m-header row">
                <div class="col-sm-6 form-group">
                    <h4><i class="fa fa-bar-chart"></i> Thống Kê Hoạt Động</h4>
                </div>
                <div class="col-sm-6 form-group text-right">
                    <a href="{{route('user-activity-logs')}}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Filter Date Range -->
        <div class="box-body">
            <form method="GET" action="{{route('user-activity-statistics')}}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Từ ngày</label>
                            <input type="date" name="start_date" class="form-control" value="{{$startDate}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Đến ngày</label>
                            <input type="date" name="end_date" class="form-control" value="{{$endDate}}">
                        </div>
                    </div>
                    <div class="col-md-2">
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
        
        <div class="box-body">
            <div class="row">
                <!-- Thống kê theo User -->
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5><i class="fa fa-users"></i> Thống Kê Theo Người Dùng</h5>
                        <table class="stat-table table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Người dùng</th>
                                    <th class="text-right">Số hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userStats as $index => $stat)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td>
                                        <a href="{{route('user-activity-history', $stat->user_id)}}">
                                            {{$stat->user_name}}
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{number_format($stat->total_actions)}}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Thống kê theo Module -->
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5><i class="fa fa-cubes"></i> Thống Kê Theo Module</h5>
                        <table class="stat-table table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Module</th>
                                    <th class="text-right">Số hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moduleStats as $index => $stat)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ucfirst($stat->module)}}</span>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{number_format($stat->total_actions)}}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Thống kê theo Action -->
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5><i class="fa fa-cogs"></i> Thống Kê Theo Hành Động</h5>
                        <table class="stat-table table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Hành động</th>
                                    <th class="text-right">Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actionStats as $index => $stat)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td>
                                        <span class="activity-badge badge-{{$stat->action}}">
                                            {{ucfirst($stat->action)}}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{number_format($stat->total_actions)}}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Biểu đồ theo ngày -->
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5><i class="fa fa-line-chart"></i> Hoạt Động Theo Ngày</h5>
                        <div class="chart-container">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Biểu đồ hoạt động theo ngày
const dailyLabels = {!! json_encode($dailyStats->pluck('date')) !!};
const dailyData = {!! json_encode($dailyStats->pluck('total_actions')) !!};

const ctx = document.getElementById('dailyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Số hành động',
            data: dailyData,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

@stop

