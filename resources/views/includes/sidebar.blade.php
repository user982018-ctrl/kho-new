
<ul class="sidebar-nav " data-coreui="navigation" data-simplebar="">
    <li class="nav-item"><a class="nav-link" href="{{route('home')}}">
            <svg class="nav-icon">
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-speedometer')}}"></use>
            </svg>Tổng quan</a></li>

<?php $checkAll = isFullAccess(Auth::user()->role);
    $isLeadSale = Helper::isLeadSale(Auth::user()->role);
    $isMkt = Helper::isMkt(Auth::user());
    $isSale = Helper::isSale(Auth::user());
?>

    @if ($checkAll)
    <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-grid')}}"></use>
            </svg>Kho</a>
        <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="{{route('order')}}"><span class="nav-icon"></span> Đơn
                    hàng</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('product')}}">
                <span class="nav-icon"></span> Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('category')}}"><span class="nav-icon"></span> Danh
                    mục</a></li>
        </ul>
    </li>
    @endif
    
    <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
        <svg class="nav-icon">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-voice-over-record')}}"></use>
        </svg>TeleSale</a>
        <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="{{route('sale-index')}}"><span class="nav-icon"></span>Tác nghiệp Sale</a></li>
            @if ($checkAll || $isLeadSale || $isSale)
            <li class="nav-item"><a class="nav-link" href="{{route('sale-rank')}}"><span class="nav-icon"></span>Bảng xếp hạng </a></li>
            @endif
            <li class="nav-item"><a class="nav-link" href="{{route('spam')}}"><span class="nav-icon"></span>Seeding/Spam</a></li>

            @if ($checkAll || $isLeadSale)
            {{-- <li class="nav-item"><a class="nav-link" href="{{route('view-spam')}}"><span class="nav-icon"></span>Spam</a></li> --}}
            <li class="nav-item"><a class="nav-link" href="{{route('view-sale-report-effect-TN')}}"><span class="nav-icon"></span>Báo cáo Sale TN</a></li>
            @endif

            @if ($checkAll || $isLeadSale)
            <li class="nav-item"><a class="nav-link" href="{{route('view-sale-report')}}"><span class="nav-icon"></span>Báo cáo Doanh số Sale</a></li>
            @endif
        </ul>
    </li>

    @if ($checkAll || $isMkt)
    <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
        <svg class="nav-icon">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-chart-line')}}"></use>
        </svg>Marketing</a>
        <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="{{route('marketing-TN')}}"><span class="nav-icon"></span>Marketing Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('marketing-src')}}"><span class="nav-icon"></span>QL nguồn</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('view-mkt-report')}}"><span class="nav-icon"></span>Báo cáo Doanh số MKT</a></li>
        </ul>
    </li>
    @endif
    
    @if ($checkAll || $isLeadSale)
    <li class="nav-item"><a class="nav-link" href="{{route('manage-group')}}">
        <svg class="nav-icon">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-chart-pie')}}"></use>
        </svg> QL Nhóm</a></li>
    @endif

    @if ($checkAll)
    <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
        <svg class="nav-icon">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-group')}}"></use>
        </svg>Nhân sự</a>
        <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="{{route('group-user')}}"><span class="nav-icon"></span>Nhóm sale</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('group-digital')}}"><span class="nav-icon"></span>Nhóm digital</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('manage-user')}}"><span class="nav-icon"></span>Thành viên</a></li>
        </ul>
    </li>
    @endif
    
    {{-- <li class="nav-item"><a class="nav-link" href="{{route('manage-user')}}">
        <svg class="nav-icon">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-chart-pie')}}"></use>
        </svg> Thành viên</a>
    </li> --}}
    
    @if ($checkAll || $isLeadSale || $isMkt)
    <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
        <svg class="nav-icon">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-settings')}}"></use>
        </svg>Cài đặt</a>
        <ul class="nav-group-items">
            @if ($checkAll)
            <li class="nav-item"><a class="nav-link" href="{{route('setting-general')}}"><span class="nav-icon"></span>Chung</a></li>
            <li class="nav-group"><a class="nav-link nav-group-toggle">QL TN Sale</a>
                <ul class="nav-group-items">
                    <li class="nav-item"><a class="nav-link" href="{{route('category-call')}}"><span class="nav-icon"></span>Loại TN</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('call-result')}}"><span class="nav-icon"></span>Kết quả TN</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('call-index')}}"><span class="nav-icon"></span>Thiết lập TN Sale</a></li>
                </ul>
            </li>
            @endif

            @if ($checkAll || $isLeadSale || $isMkt)
            <li class="nav-item"><a class="nav-link" href="{{route('tool')}}"><span class="nav-icon"></span>Công cụ</a></li>
            @endif
        </ul>
    </li>
    @endif
    
</ul>
<button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>