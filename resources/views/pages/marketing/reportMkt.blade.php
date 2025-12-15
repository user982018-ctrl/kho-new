@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{asset('public/css/dashboard.css')}}" /> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

<style>

  .form-group, .mb12 {
      margin-bottom: 12px !important;
  }
  .header.header-sticky {
    position: unset;
  }
    .fs-5 {
        font-size: 2.0736rem !important;
    }

    .weekly-sales span {
        color: #00894f;
        background-color: #d9f8eb;
    }

    .total-order svg {
        height: 1em;
    }

    .name-total {
      cursor: pointer;
    }
    .filter-button svg{
      transform: rotate(90deg)
    }

    .total-sales .card-body {
      padding: 10px;
    }
    
    .filter-type-button {
      border: 1px solid #9da5b1;
      border-radius: 0.375rem;
    }

    .filter-type-button:hover {
      border: 1px solid #9da5b1;
      background: #fff;
    }

    .open .dropdown-menu {
      display: block;
    }

    .dropdown-menu>li>a {
      display: block;
      padding: 3px 20px;
      clear: both;
      font-weight: 400;
      line-height: 1.42857143;
      color: #333;
      white-space: nowrap;
    }
  .dropdown-menu>li>a:focus, .dropdown-menu>li>a:hover {
      color: #262626;
      text-decoration: none;
      background-color: #f5f5f5;
  }
  .caret {
      display: inline-block;
      width: 0;
      height: 0;
      margin-left: 2px;
      vertical-align: middle;
      border-top: 4px dashed;
      border-top: 4px solid\9;
      border-right: 4px solid transparent;
      border-left: 4px solid transparent;
  }

  #dateTotal {
    /* width: 13%;zxc */
  }
  #daterange {
    color: #000;
  }
  
  
  .filter-order .daterange {
    /* min-width: 230px; */
  }

  @media only screen and (max-width: 600px) {
    .px-3 {
      padding: 0 !important;
    }

    .dropdown.dropdown-filter {
      white-space: nowrap;
    }
  }

  #daterange {
    width: 100%;
  }
</style>

<?php $checkAll = isFullAccess(Auth::user()->role);
  $enableDigital = ($checkAll || Auth::user()->is_digital);
  $isDigital = Auth::user()->is_digital;
  $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
?>

{{-- begin --}}
<style>
  /* Honor Bar */
  .honor-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 70px;
    background: #fff;
    display: flex;
    align-items: center;
    overflow: hidden;
    z-index: 9999;
  }

  .scroll-text {
    white-space: nowrap;
    display: flex;
    align-items: center;
    animation: scrollLeft 26s linear infinite;
    font-size: 18px;
  }

  .rank {
    margin: 0 40px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .rankBadge {
    display: inline-block;
    min-width: 60px;
    text-align: center;
    font-weight: bold;
    padding: 8px 14px;
    border-radius: 25px;
    font-size: 16px;
    animation: pulse 1.5s infinite;
  }

  .rankBadge.gold { background: gold; color: black; }
  .rankBadge.silver { background: silver; color: black; }
  .rankBadge.bronze { background: #cd7f32; color: white; }

  .info {
    line-height: 1.4;
  }

  .info span {
    display: block;
  }

  @keyframes scrollLeft {
    from { transform: translateX(0); }
    to   { transform: translateX(-100%); }
  }

  @keyframes pulse {
    0%   { transform: scale(1); }
    50%  { transform: scale(1.2); }
    100% { transform: scale(1); }
  }

  /* --- Confetti --- */
  .confetti-piece {
    position: fixed;
    width: 8px;
    height: 14px;
    opacity: 0.9;
    animation: fall linear forwards;
    z-index: 9999;
  }
  @keyframes fall {
    to {
      transform: translateY(100vh) rotate(720deg);
      opacity: 0;
    }
  }

  /* --- Tim bay lên --- */
  .heart {
    position: fixed;
    bottom: -20px;
    font-size: 24px;
    animation: rise 5s linear forwards;
    z-index: 5000;
    pointer-events: none;
  }
  @keyframes rise {
    to {
      transform: translateY(-100vh);
      opacity: 0;
    }
  }

  /* --- Avatar rơi xuống --- */
img.avatar {
    position: fixed;
    top: -60px;
    width: 80px;
    height: 80px;
    /* border-radius: 50%; */
    pointer-events: none;
    z-index: 1000;
}
.explosion {
    position: fixed;
    font-size: 28px;
    pointer-events: none;
    z-index: 1001;
    animation: explode 1.2s ease-out forwards;
  }
@keyframes explode {
    to {
      transform: translate(var(--dx), var(--dy)) scale(0.6);
      opacity: 0;
    }
  }

  /* --- Icon nổ ra --- */
   /* Icon nổ ra */
  .burst {
    position: fixed;
    font-size: 32px;
    animation: burstUp 1.2s ease-out forwards;
    z-index: 6001;
  }
  @keyframes burstUp {
    0%   { transform: scale(0.5) translateY(0); opacity:1; }
    60%  { transform: scale(1.3) translateY(-40px); opacity:1; }
    100% { transform: scale(0.8) translateY(-80px); opacity:0; }
  }
</style>

<div class="container-lg">
  <div class="row mb-1 filter-order">
    <div class="col-xs-12 col-sm-6 col-md-4 form-group daterange mb-1">
      <input id="daterange" class=" btn btn-outline-secondary" type="text" name="daterange"/>
    </div>
    {{ csrf_field() }}

    <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
      <select name="group" id="group-filter" class="form-select">
        <option  value="999">--Nhóm hàng--</option>  
          @if (isset($groups))
              @foreach($groups as $group)
              <option value="{{$group->id}}">{{$group->name}}</option>
              @endforeach
          @endif
      </select>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
      <select name="status" id="status-filter" class="form-select" aria-label="Default select example">
        <option value="998">Chọn Trạng Thái (Không huỷ)</option>
        <option value="999">Tổng trạng thái</option>
        <option value="1">Chưa giao vận</option>
        <option value="2">Đang giao</option>
        <option value="3">Hoàn tất</option>
        <option value="0">Huỷ</option>
      </select>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
      <select name="category" id="category-filter" class="form-select" aria-label="Default select example">
        <option value="999">--Chọn mục--</option>
        @if (isset($category))
          @foreach($category as $cate)
          <option value="{{$cate->id}}">{{$cate->name}}</option>
          @endforeach
        @endif
      </select>
    </div>

    @if ($checkAll)
    <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
      <select name="groupDigital" id="group-digital-filter" class="form-select">
        <option value="999">--Nhóm digital--</option>  
          @if (isset($groupDigital))
              @foreach($groupDigital as $group)
              <option value="{{$group->id}}">{{$group->name}}</option>
              @endforeach
          @endif
      </select>
    </div>
    @endif

    <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
      <select name="show" id="show-filter" class="form-select">
        <option value="20">Hiển thị 20 dòng</option>  
        <option value="40">Hiển thị 40 dòng</option>
        <option value="60">Hiển thị 60 dòng</option>
        <option value="80">Hiển thị 80 dòng</option>
      </select>
    </div>

    @if ($checkAll || $isLeadDigital)
    <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
      <select name="sort" id="sort-filter" class="form-select">
        <option value="">Sắp xếp theo</option>
        <option value="total_desc">Doanh số giảm dần</option>
        <option value="total_asc">Doanh số tăng dần</option>
        <option value="user_id">Thứ tự nhân viên</option>
      </select>
    </div>
    @endif
  </div>
  <div class="row mb-1">
    <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
      <button type="button" id="btn-filter"  class="btn btn-outline-primary"><svg class="icon me-2">
        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-filter')}}"></use>
      </svg>Lọc</button>
      <a  class="btn btn-outline-danger" href="{{route('home')}}"><strong>X</strong></a>
      
    </div>
  </div>

    <div class="row">
      <div class="box-body" style="padding-top: 0px;">
        <div style="clear: both;"></div>
        <?php if ($isDigital || $checkAll) { ?> 
        <div class="dragscroll1 tableFixHead table_digital">
          <span class="loader hidden">
          </span>
          <table class="table table-bordered table-multi-select" id="tableReportMarketing">
            <thead>
              <tr style="cursor: grab;" class="drags-area">
                <th class="text-center" style="width: 35px;"></th>
                <th class="text-center no-wrap" style="min-width: 10%"></th>
                <th class="text-center" rowspan="1" colspan="6">KHÁCH HÀNG MỚI</th>
                <th class="text-center" rowspan="1" colspan="4">KHÁCH HÀNG CŨ</th>
                <th class="text-center" rowspan="1" colspan="3">DOANH SỐ TỔNG</th>
              </tr>
              <tr style="cursor: grab;" class="drags-area t28">
                  <th class="text-center" style="width: 35px;">STT</th>
                  <th class="text-center">MARKETING</th>
                  <th class="text-center">Contact</th>
                  <th class="text-center">Đơn chốt</th>
                  <th class="text-center">Tỉ lệ chốt đơn (%)</th>
                  <th class="text-center">Số sản phẩm</th>
                  <th class="text-center">Doanh số</th>
                  <th class="text-center">Giá trị đơn</th>

                  <th class="text-center">Contact</th>
                  <th class="text-center">Đơn chốt</th>
                  <th class="text-center">Doanh số</th>
                  <th class="text-center">Giá trị đơn</th>

                  <th class="text-center">Tỉ lệ chốt đơn</th>
                  <th class="text-center">Doanh số</th>
                  <th class="text-center">Giá trị đơn</th>
                  
              </tr>
              <tr class="rowsum t72" id="tr-sum-digital" style="cursor: grab;">
              </tr> 
            </thead>
            
            <tbody id="body-digital">
              
                <?php $i = 1; ?>
              @foreach ($dataDigital as $digital)
                <tr>
                  <td class="text-center">{{$i}}</td>
                  <td>{{ $digital->real_name }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                <?php $i++; ?>
              @endforeach
            </tbody>
          </table>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>

 <!-- Dòng chữ chạy chân trang -->
 {{-- <div class="honor-bar">
  <div class="scroll-text">
    <div class="rank">
      <span class="rankBadge gold">🥇 Top 1</span>
        
        <div class="info">
          <span><strong>Hoàng Thị Thanh Huyền        </strong></span>
          <span>Doanh số: 1,698,542,000          </span>
        </div>
    </div>

    <div class="rank">
      <span class="rankBadge silver">🥈 Top 2</span>
      
      <div class="info">
        <span><strong>Nguyễn Đức Thắng</strong></span>
        <span>1,201,763,000        </span>
      </div>
    </div>

    <div class="rank">
      <span class="rankBadge bronze">🥉 Top 3</span>
        
        <div class="info">
          <span><strong>Nguyễn Thị Anh Luyến</strong></span>
          <span>Doanh số: 1,071,456,000
          </span>
        </div>
    </div>
    
    <div class="rank">
      <span class="rankBadge gold">🥇 Top 1</span>
        
        <div class="info">
          <span><strong>⁠Trần Nguyễn Đăng Khoa        </strong></span>
          <span>Doanh số: 482,640,000   </span>
        </div>
    </div>

    <div class="rank">
      <span class="rankBadge silver">🥈 Top 2</span>
      
      <div class="info">
        <span><strong>Phạm Thị Ánh Tuyết      </strong></span>
        <span>Doanh số: 481,600,000</span>
      </div>
    </div>

    <div class="rank">
      <span class="rankBadge bronze">🥉 Top 3</span>
        <div class="info">
          <span><strong>Nguyễn Ý Nhật        </strong></span>
          <span>Doanh số: 471,920,000
          </span>
        </div>
    </div>
  </div>
</div> --}}
<style>
  /* Honor Bar */
.honor-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  /* height: 70px; */
  background: #fff;
  display: flex;
  align-items: center;
  overflow: hidden;
  z-index: 9999;
}

.honor-track {
  display: flex;
  align-items: center;
  gap: 24px;
  font-size: 18px;
  width: max-content;
  animation: scrollLeft 15s linear infinite;
}

.rank-set {
  display: flex;
  align-items: center;
  gap: 24px;
}

.rank {
  margin: 0 40px;
  /* display: flex; */
  align-items: center;
  gap: 12px;
}

@keyframes scrollLeft {
  0% { transform: translateX(50%); }
  100% { transform: translateX(-100%); }
}

.rankBadge {
  display: inline-block;
  min-width: 60px;
  text-align: center;
  font-weight: bold;
  padding: 8px 14px;
  border-radius: 25px;
  font-size: 16px;
  animation: pulse 5s infinite;
}

.rankBadge.gold { background: gold; color: black; }
.rankBadge.silver { background: silver; color: black; }
.rankBadge.bronze { background: #cd7f32; color: white; }

/* Snow effect */
.snow-container {
  pointer-events: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 9998;
}
.snowflake {
  position: absolute;
  top: -10px;
  color: rgba(255, 255, 255, 0.9);
  text-shadow: 0 0 6px rgba(15, 23, 42, 0.4);
  animation-name: snowFallWithWind;
  animation-timing-function: linear;
  animation-iteration-count: infinite;
}
@keyframes snowFallWithWind {
  0% {
    transform: translate3d(0, 0, 0) rotate(0deg);
  }
  10% {
    transform: translate3d(var(--wind-strength, 10px), 10vh, 0) rotate(36deg);
  }
  20% {
    transform: translate3d(0, 20vh, 0) rotate(72deg);
  }
  30% {
    transform: translate3d(calc(var(--wind-strength, 10px) * -1), 30vh, 0) rotate(108deg);
  }
  40% {
    transform: translate3d(0, 40vh, 0) rotate(144deg);
  }
  50% {
    transform: translate3d(var(--wind-strength, 10px), 50vh, 0) rotate(180deg);
  }
  60% {
    transform: translate3d(0, 60vh, 0) rotate(216deg);
  }
  70% {
    transform: translate3d(calc(var(--wind-strength, 10px) * -1), 70vh, 0) rotate(252deg);
  }
  80% {
    transform: translate3d(0, 80vh, 0) rotate(288deg);
  }
  90% {
    transform: translate3d(var(--wind-strength, 10px), 90vh, 0) rotate(324deg);
  }
  100% {
    transform: translate3d(var(--drift, 20px), 110vh, 0) rotate(360deg);
  }
}

.info {
  line-height: 1.4;
}

.info span {
  display: block;
}

</style>

<style>
  .top-employees-popup.hidden { display: none; }
  .top-employees-popup {
    position: fixed;
    inset: 0;
    z-index: 11000;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .top-employees-popup .popup-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(2px);
  }
  .top-employees-popup .popup-card {
    position: relative;
    width: min(900px, 90vw);
    background: #fff;
    border-radius: 18px;
    padding: 24px 28px;
    z-index: 1;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.3);
    transform: translateY(30px);
    opacity: 0;
    transition: all 0.25s ease;
  }
  .top-employees-popup.visible .popup-card {
    transform: translateY(0);
    opacity: 1;
  }
  .popup-close {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 24px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #94a3b8;
  }
  .popup-close:hover { color: #0f172a; }
  .popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }
  .popup-subtitle {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
  }
  .popup-header h3 {
    font-weight: 700;
    color: #f6000b;
  }
  .popup-badge {
    font-size: 34px;
  }
  .popup-body {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
  }
  .popup-column {
    flex: 1;
    min-width: 260px;
    background: #f8fafc;
    border-radius: 14px;
    padding: 18px;
    border: 1px solid #e2e8f0;
  }
  .popup-column h4 {
    font-weight: 700;
    margin-bottom: 12px;
    color: #0f172a;
  }
  .popup-person {
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
    display: flex;
    gap: 12px;
    align-items: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .popup-person:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
  }
  .popup-medal {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 700;
    color: #78350f;
    box-shadow: inset 0 2px 4px rgba(255,255,255,0.5), 0 3px 8px rgba(15,23,42,0.18);
    border: 2px solid rgba(255,255,255,0.8);
  }
  .popup-medal.silver {
    background: linear-gradient(135deg, #f8fafc, #cbd5f5);
    color: #0f172a;
  }
  .popup-medal.gold {
    background: linear-gradient(135deg, #fef9c3, #fcd34d);
  }
  .popup-medal.bronze {
    background: linear-gradient(135deg, #fde68a, #fb923c);
    color: #7c2d12;
  }
  .popup-person:last-child { margin-bottom: 0; }
  .popup-person-content {
    flex: 1;
  }
  .popup-name {
    font-weight: 600;
    margin-bottom: 6px;
    color: #111827;
  }
  .popup-person p {
    margin: 0;
    font-size: 14px;
    color: #475569;
  }
  .popup-avatar {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,0.8);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.18);
    transition: transform 0.2s ease;
  }
  .popup-person:hover .popup-avatar {
    transform: scale(1.05);
  }
  .popup-person.gold {
    border-color: #eab308;
    background: linear-gradient(135deg, #fef9c3, #fde047);
    box-shadow: 0 6px 20px rgba(234, 179, 8, 0.35);
  }
  .popup-person.silver {
    border-color: #94a3b8;
    background: linear-gradient(135deg, #f1f5f9, #cbd5f5);
    box-shadow: 0 6px 20px rgba(148, 163, 184, 0.35);
  }
  .popup-person.bronze {
    border-color: #c08401;
    background: linear-gradient(135deg, #fef3c7, #f97316);
    box-shadow: 0 6px 20px rgba(249, 115, 22, 0.35);
  }
  .popup-person strong { color: #0f172a; }
  .popup-footer {
    margin-top: 12px;
    text-align: right;
  }
  .popup-dontshow {
    font-size: 13px;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
  }
  .popup-dontshow input {
    opacity: 1;
    width: 18px;
    height: 18px;
    margin: 0;
    border: 2px solid #cbd5f5;
    border-radius: 4px;
    background: #fff;
    position: relative;
    display: inline-block;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    transition: all 0.15s ease;
  }
  .popup-dontshow input:checked {
    border-color: #0f172a;
    background: #0f172a;
  }
  .popup-dontshow input:checked::after {
    content: "✓";
    position: absolute;
    top: -2px;
    left: 3px;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
  }
  .popup-dontshow-text {
    line-height: 1.2;
  }
  @media (max-width: 768px) {
    .popup-body { flex-direction: column; }
  }
  </style>
  
<div class="snow-container" aria-hidden="true"></div>
 
{{-- <div id="topEmployeesPopup" class="top-employees-popup hidden">
  <div class="popup-backdrop"></div>
  <div class="popup-card">
    <button type="button" class="popup-close" aria-label="Đóng">&times;</button>
    <div class="popup-header">
      <div>
        <p class="popup-subtitle mb-1">Tháng {{ date('m/Y') }}</p>
        <h3 class="m-0">Tuyên dương nhân viên xuất sắc</h3>
      </div>
      <span class="popup-badge">🏆</span>
    </div>
    <div class="popup-body">
      <div class="popup-column">
        <h4>Top Digital</h4>
        <div class="popup-person gold">
          <span class="popup-medal gold">🥇</span>
          <div class="popup-person-content">
            <p class="popup-name">1. Hoàng Thị Thanh Huyền</p>
            <p>Doanh số: <strong>1,698,542,000</strong></p>
            <p>Lương & Thưởng: <strong>42,084,624</strong></p>
          </div>
          <img class="popup-avatar" src="https://kho.phanbonmiennam.net/storage/app/public/uploads/1761718987_z5010330060023_655caccc35cbb5b5ffedfce00bbae709_092133_5411cfd4-23e3-4f2f-91d4-9396bfb9c8da.jpg" alt="Hoàng Thị Thanh Huyền">
        </div>
        <div class="popup-person silver">
          <span class="popup-medal silver">🥈</span>
          <div class="popup-person-content">
            <p class="popup-name">2. Nguyễn Đức Thắng</p>
            <p>Doanh số: <strong>1,201,763,000</strong></p>
            <p>Lương & Thưởng: <strong>31,957,897</strong></p>
          </div>
          <img class="popup-avatar" src="https://kho.phanbonmiennam.net/storage/app/public/uploads/1763612075_đâfadf.jpg" alt="Nguyễn Đức Thắng">
        </div>
        <div class="popup-person bronze">
          <span class="popup-medal bronze">🥉</span>
          <div class="popup-person-content">
            <p class="popup-name">3. Nguyễn Thị Anh Luyến</p>
            <p>Doanh số: <strong>1,071,456,000</strong></p>
            <p>Lương & Thưởng: <strong>31,313,922</strong></p>
          </div>
          <img class="popup-avatar" src="https://kho.phanbonmiennam.net/storage/app/public/uploads/1753430384_7688e70ba60e2f50761f.jpg" alt="Nguyễn Thị Anh Luyến">
        </div>
      </div>
      <div class="popup-column">
        <h4>Top Sale</h4>
        <div class="popup-person gold">
          <span class="popup-medal gold">🥇</span>
          <div class="popup-person-content">
            <p class="popup-name">1. Trần Nguyễn Đăng Khoa</p>
            <p>Doanh số: <strong>482,640,000</strong></p>
            <p>Lương & Thưởng: <strong>36,834,681</strong></p>
          </div>
          <img class="popup-avatar" src="https://kho.phanbonmiennam.net/storage/app/public/uploads/1764225269_z7267666472686_67341ec222a7118697e213126e9b1dfd.jpg" alt="Trần Nguyễn Đăng Khoa">
        </div>
        <div class="popup-person silver">
          <span class="popup-medal silver">🥈</span>
          <div class="popup-person-content">
            <p class="popup-name">2. Phạm Thị Ánh Tuyết</p>
            <p>Doanh số: <strong>481,600,000</strong></p>
            <p>Lương & Thưởng: <strong>36,721,848</strong></p>
          </div>
          <img class="popup-avatar" src="https://kho.phanbonmiennam.net/storage/app/public/uploads/1757235240_z6986294919981_44106868eaa2f00ccc94b1e6afd1291f.jpg" alt="Phạm Thị Ánh Tuyết">
        </div>
        <div class="popup-person bronze">
          <span class="popup-medal bronze">🥉</span>
          <div class="popup-person-content">
            <p class="popup-name">3. Nguyễn Ý Nhật</p>
            <p>Doanh số: <strong>471,920,000</strong></p>
            <p>Lương & Thưởng: <strong>35,879,239</strong></p>
          </div>
          <img class="popup-avatar" src="https://kho.phanbonmiennam.net/storage/app/public/uploads/1759976371_db124630-9dab-46c3-a4ff-39ab7cc20589.jpeg" alt="Nguyễn Ý Nhật">
        </div>
      </div>
    </div>
    <div class="popup-footer">
      <label class="popup-dontshow">
        <input type="checkbox" id="popupDontShow" class="popup-checkbox">
        <span class="popup-dontshow-text">Không hiển thị lại trong tháng này</span>
      </label>
    </div>
  </div>
</div> --}}
<script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>

<script>
   const POPUP_STORAGE_KEY = "topEmployeesPopup_v3_{{ date('Y_m') }}";

function showTopEmployeesPopup() {
  const popup = document.getElementById('topEmployeesPopup');
  if (!popup) return;
  popup.classList.remove('hidden');
  const dontShowCheckbox = document.getElementById('popupDontShow');
  if (dontShowCheckbox) {
    dontShowCheckbox.checked = false;
  }
  setTimeout(() => popup.classList.add('visible'), 100);
}

function hideTopEmployeesPopup() {
  const popup = document.getElementById('topEmployeesPopup');
  if (!popup) return;
  popup.classList.remove('visible');
  setTimeout(() => popup.classList.add('hidden'), 200);
  if (window.sessionStorage) {
    const dontShowCheckbox = document.getElementById('popupDontShow');
    try {
      if (dontShowCheckbox && dontShowCheckbox.checked) {
        sessionStorage.setItem(POPUP_STORAGE_KEY, 'true');
      } else {
        sessionStorage.removeItem(POPUP_STORAGE_KEY);
      }
    } catch (err) {
      console.warn('Không thể cập nhật trạng thái popup:', err);
    }
  }
}

function initSnowEffect() {
  const container = document.querySelector('.snow-container');
  if (!container) return;
  const flakeCount = 140;
  for (let i = 0; i < flakeCount; i++) {
    const flake = document.createElement('span');
    flake.className = 'snowflake';
    flake.textContent = Math.random() > 0.3 ? '❄' : '✻';
    flake.style.left = `${Math.random() * 100}%`;
    
    // Thời gian rơi (kết hợp cả rơi và gió)
    const fallDuration = 6 + Math.random() * 12;
    flake.style.animationDuration = `${fallDuration}s`;
    flake.style.animationDelay = `${Math.random() * 10}s`;
    
    flake.style.fontSize = `${8 + Math.random() * 20}px`;
    flake.style.opacity = 0.3 + Math.random() * 0.6;
    
    // Độ drift ngang khi rơi (tăng để có hiệu ứng gió mạnh hơn)
    const driftAmount = -80 + Math.random() * 160;
    flake.style.setProperty('--drift', `${driftAmount}px`);
    
    // Độ mạnh của gió (lắc lư ngang khi rơi)
    const windStrength = 15 + Math.random() * 35;
    flake.style.setProperty('--wind-strength', `${windStrength}px`);
    
    container.appendChild(flake);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const closeBtn = document.querySelector('#topEmployeesPopup .popup-close');
  const backdrop = document.querySelector('#topEmployeesPopup .popup-backdrop');
  if (closeBtn) closeBtn.addEventListener('click', hideTopEmployeesPopup);
  if (backdrop) backdrop.addEventListener('click', hideTopEmployeesPopup);
  let shouldShow = true;
  try {
    shouldShow = !sessionStorage.getItem(POPUP_STORAGE_KEY);
  } catch (err) {
    console.warn('Không thể truy cập sessionStorage:', err);
    shouldShow = true;
  }
  if (shouldShow) {
    setTimeout(showTopEmployeesPopup, 800);
  }
  initSnowEffect();
});
  $(document).ready(function() {
    
    var daterangePicker = $('input[name="daterange"]').daterangepicker({
      timePicker: true,
      timePicker24Hour: true,
      timePickerIncrement: 1,
      timePickerSeconds: false,
      startDate: moment().startOf('day').hour(0).minute(0),
      endDate: moment().endOf('day').hour(23).minute(59),
      ranges: {
        'Hôm nay': [moment().startOf('day').hour(0).minute(0), moment().endOf('day').hour(23).minute(59)],
        'Hôm qua': [moment().subtract(1, 'days').startOf('day').hour(0).minute(0), moment().subtract(1, 'days').endOf('day').hour(23).minute(59)],
        '7 ngày gần đây': [moment().subtract(6, 'days').startOf('day').hour(0).minute(0), moment().endOf('day').hour(23).minute(59)],
        '30 ngày gần đây': [moment().subtract(29, 'days').startOf('day').hour(0).minute(0), moment().endOf('day').hour(23).minute(59)],
        'Tháng này': [moment().startOf('month').hour(0).minute(0), moment().endOf('month').hour(23).minute(59)],
        'Tháng trước': [moment().subtract(1, 'month').startOf('month').hour(0).minute(0), moment().subtract(1, 'month').endOf('month').hour(23).minute(59)]
      },
      locale: {
        "format": 'DD/MM/YYYY HH:mm',
        "applyLabel": "OK",
        "cancelLabel": "Huỷ",
        "fromLabel": "Từ",
        "toLabel": "Đến",
        "daysOfWeek": [
          "CN", "Hai", "Ba", "Tư", "Năm", "Sáu", "Bảy" 
        ],
        "monthNames": [
          "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
	        "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12" 
        ],
      },
      opens: 'left',
      applyClass: 'btn-sm btn-primary',
      cancelClass: 'btn-sm btn-default'
    }, function(start, end, label) {
      // Tự động set thời gian mặc định: begin 00:00, end 23:59 cho tất cả các range
      start.hour(0).minute(0);
      end.hour(23).minute(59);
      daterangePicker.data('daterangepicker').setStartDate(start);
      daterangePicker.data('daterangepicker').setEndDate(end);
    });

    // Đảm bảo khi mở picker, set thời gian mặc định cho tất cả các range
    $('input[name="daterange"]').on('show.daterangepicker', function(ev, picker) {
      // Set thời gian mặc định cho tất cả các range
      if (!picker.chosenLabel || picker.chosenLabel === 'Custom Range') {
        picker.startDate.hour(0).minute(0);
        picker.endDate.hour(23).minute(59);
      }
    });
    
    // Đảm bảo khi chọn range, thời gian được set đúng
    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
      picker.startDate.hour(0).minute(0);
      picker.endDate.hour(23).minute(59);
    });
    $('[data-range-key="Custom Range"]').text('Tuỳ chỉnh');

    $('input[name="daterange"]').change(function () {
    });
  
    $("#type-period").click(function () {
      $('#filter-type-button').html('Trong khoản <span class="caret"></span>');
      $('#dateTotal').hide();
      $('#filter-order').hide();
      $('#daterange').show();
      
    });
    $("#type-day").click(function () {
      $('#filter-type-button').html('Theo ngày <span class="caret"></span>');
      $('#dateTotal').show();
      $('#filter-order').show();
      $('#daterange').hide();
    });

    $("input[name='filterTotal']").click(function () { 
      $(".filter-order label").removeClass('active');
      $(this).parent().addClass('active');
      $('.loader').show();
      var _token  = $("input[name='_token']").val();
      let type    =  $(this).val();
      let date    = $("input[name='dateTotal']").val();
      $.ajax({
            url: "{{ route('filter-total-sales') }}",
            type: 'GET',
            data: {
              _token: _token,
              type,
              date
            },
            success: function(data) {
              console.log(data);
              if (!$.isEmptyObject(data.totalSum)) {
                $("#totalSum").text(data.totalSum);
                $(".percentTotalDay").text(data.percentTotal);
                $(".countOrders").text(data.countOrders);
                $(".percentCountDay").text(data.percentCount);
                $(".avgOrders").text(data.avgOrders);
                $(".percentAvg").text(data.percentAvg);
              }
              $('.loader').hide();
            }
        });
    });

    $("#btn-filter").on( "click", function() {
      let value =  $("input[name='daterange']").val();
      let arr = value.split("-");

      var _token    = $("input[name='_token']").val();
      var status    = $("select[name='status']").val();
      var category  = $("select[name='category']").val();
      var product   = $("select[name='product']").val();
      var sale      = $("select[name='sale']").val();
      var mkt       = $("select[name='mkt']").val();
      var src       = $("select[name='src']").val();
      var group     = $("select[name='group']").val();
      var groupUser = $("select[name='groupUser']").val();
      var groupDigital = $("select[name='groupDigital']").val();
      var show = $("select[name='show']").val();
      var sort = $("select[name='sort']").val();
      data = {
        _token : _token,
        type : 'daterange',
        date : value
      };

      if (status != '999' && status != undefined) {
        data.status = status;
      } if (category != '999' && category != undefined) {
        data.category = category;
      } if (product != '999' && product != undefined) {
        data.product = product;
      } if (sale != '999' && sale != undefined) {
        data.sale = sale;
      } if (mkt != '999' && mkt != undefined) {
        data.mkt = mkt;
      } if (src != '999' && src != undefined) {
        data.src = src;
      } if (group != '999' && group != undefined) {
        data.group = group;
      } if (groupUser != '999' && groupUser != undefined) {
        data.groupUser = groupUser;
      } if (show != '20' && show != undefined) {
        data.show = show;
      } if (sort != '' && sort != undefined) {
        data.sort = sort;
      }

      ajaxGetListDigital(data);
    });
    
    $("input[name='dateTotal']").change(function () {

      let type    = $('input[name="filterTotal"]:checked').val();
      let date    = $(this).val();
      var _token  = $("input[name='_token']").val();

      $('.loader').show();
      $.ajax({
            url: "{{ route('filter-total-sales') }}",
            type: 'GET',
            data: {
              _token: _token,
              type,
              date
            },
            success: function(data) {
              if (!$.isEmptyObject(data.totalSum)) {
                $("#totalSum").text(data.totalSum);
                $(".percentTotalDay").text(data.percentTotal);
                $(".countOrders").text(data.countOrders);
                $(".percentCountDay").text(data.percentCount);
                $(".avgOrders").text(data.avgOrders);
                $(".percentAvg").text(data.percentAvg);
              }
              $('.loader').hide();
            }
        });
    });

    $("#category-filter").change(function() {
      var selectedVal = $(this).find(':selected').val();
      var selectedText = $(this).find(':selected').text();
      
      if (selectedVal == 9) {
        var _token      = $("input[name='_token']").val();
        $.ajax({
          url: "{{ route('get-products-by-category-id') }}",
          type: 'GET',
          data: {
              _token: _token,
              categoryId: selectedVal
          },
          success: function(data) {
          
            let str = '';
            str += '<div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">'
              + '<select name="product" id="product-filter" class="form-select">'
              + '<option value="999">--Chọn sản phẩm--</option>';
              data.forEach(item => {
                // console.log(item['id'])
                str += '<option value="' + item['id'] + '">' + item['name'] + '</option>';
                });
            str  += '</select>'
              + '</div>';

              $(str).appendTo(".filter-order");
          }
        });
      } else if ($('#product-filter').length > 0) {
          $('#product-filter').parent().remove();
      }
  });

  // Auto reload when sort option changes
  $("#sort-filter").change(function() {
    let value =  $("input[name='daterange']").val();
    var _token    = $("input[name='_token']").val();
    var status    = $("select[name='status']").val();
    var category  = $("select[name='category']").val();
    var product   = $("select[name='product']").val();
    var sale      = $("select[name='sale']").val();
    var mkt       = $("select[name='mkt']").val();
    var src       = $("select[name='src']").val();
    var group     = $("select[name='group']").val();
    var groupUser = $("select[name='groupUser']").val();
    var groupDigital = $("select[name='groupDigital']").val();
    var show = $("select[name='show']").val();
    var sort = $("select[name='sort']").val();
    data = {
      _token : _token,
      type : 'daterange',
      date : value
    };

    if (status != '999' && status != undefined) {
      data.status = status;
    } if (category != '999' && category != undefined) {
      data.category = category;
    } if (product != '999' && product != undefined) {
      data.product = product;
    } if (sale != '999' && sale != undefined) {
      data.sale = sale;
    } if (mkt != '999' && mkt != undefined) {
      data.mkt = mkt;
    } if (src != '999' && src != undefined) {
      data.src = src;
    } if (group != '999' && group != undefined) {
      data.group = group;
    } if (groupUser != '999' && groupUser != undefined) {
      data.groupUser = groupUser;
    } if (show != '20' && show != undefined) {
      data.show = show;
    } if (sort != '' && sort != undefined) {
      data.sort = sort;
    }

    ajaxGetListDigital(data);
  });
  });
</script>

<script>
  $.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results) {
      return results[1];
    }
    return 0;
  }

  let mkt = $.urlParam('mkt') 
  if (mkt) {
    $('#mkt-filter option[value=' + mkt +']').attr('selected','selected');
  }

  let src = $.urlParam('src') 
  if (src) {
    $('#src-filter option[value=' + src +']').attr('selected','selected');
  }
</script>
<script>
  function number_format_js(number) {
    if (!number) {
      return 0;
    }
    number = number.toLocaleString('vi-VN');
    return number.replace(/,/g, '.').replace(/\./g, ',');
  }

  // Helper: Sort data array based on sort option
  function sortData(dataArray, sortOption) {
    if (!sortOption || !dataArray || dataArray.length === 0) {
      return dataArray;
    }

    const sortedData = [...dataArray]; // Create a copy to avoid mutating original

    switch(sortOption) {
      case 'total_desc':
        sortedData.sort((a, b) => {
          const totalA = (a.summary_total?.total || 0);
          const totalB = (b.summary_total?.total || 0);
          return totalB - totalA; // Descending
        });
        break;
      case 'total_asc':
        sortedData.sort((a, b) => {
          const totalA = (a.summary_total?.total || 0);
          const totalB = (b.summary_total?.total || 0);
          return totalA - totalB; // Ascending
        });
        break;
      case 'user_id':
        sortedData.sort((a, b) => {
          // Try to get id from multiple possible locations
          const idA = (a.id || a.new_customer?.id || a.old_customer?.id || 0);
          const idB = (b.id || b.new_customer?.id || b.old_customer?.id || 0);
          return idA - idB; // Ascending by user ID
        });
        break;
      default:
        return sortedData;
    }

    return sortedData;
  }

  function ajaxGetListDigital(dataInput)
  {
    if ($('.table_digital').length > 0) {
      $('.table_digital .loader').show();
      $('.table_digital .table-multi-select').css("opacity", "0.5");
      $('.table_digital .table-multi-select').css("position", "relative");
        $.ajax({
          url: "{{ route('filter-total-digital') }}",
          type: 'GET',
          data: dataInput,
          success: function(data) {
            $('.table_digital .loader').hide();
            $('.table_digital .table-multi-select').css("opacity", "1");
            $('.table_digital .table-multi-select').css("position", "relative");

            if (data.length == 0) {
              $("#body-digital").html('');
            } else if (data.data.length > 0) {
              // Get sort option and sort data
              const sortOption = $("select[name='sort']").val();
              let sortedData = sortData(data.data, sortOption);

              /* lọc data digital*/
              var str = '';
              
              if (data.trSum) {
                var newCusomerTrSum = data.trSum.new_customer;
                var oldCusomerTrSum = data.trSum.old_customer;
                var summaryCusomerTrSum = data.trSum.sumary_total;
                var maxAvcElem = sortedData[0].summary_total.avg;

                /** lấy ra trung bình đơn lớn nhất của trong list sale**/
                sortedData.forEach((element, k) => {
                    if (element.summary_total.avg > maxAvcElem) {
                        maxAvcElem = element.summary_total.avg;
                    }
                });

                var strTdSum = '';
                strTdSum += '<td colspan="2" class="text-center font-weight-bold">Tổng: </td>'
                  + '<td class="text-center font-weight-bold"><span>' + newCusomerTrSum.contact + '</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + newCusomerTrSum.count_order + '</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + newCusomerTrSum.rate + '%</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + newCusomerTrSum.product + '</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + number_format_js(newCusomerTrSum.total) + '</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + number_format_js(newCusomerTrSum.avg) + '</span></td>';
                          
                strTdSum += '<td class="text-center font-weight-bold"><span>' + oldCusomerTrSum.contact+ '</span></td>'
                + '<td class="text-center font-weight-bold"><span>' + oldCusomerTrSum.count_order + '</span></td>'
                  +'<td class="text-center font-weight-bold"><span>' + number_format_js(oldCusomerTrSum.total) + '</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + number_format_js(oldCusomerTrSum.avg) + '</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + (summaryCusomerTrSum.rate) + '%</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + number_format_js(summaryCusomerTrSum.total) + '</span></td>'
                  + '<td class="text-center font-weight-bold"><span>' + number_format_js(summaryCusomerTrSum.avg) + '</span></td>';

                $("#tr-sum-digital").html(strTdSum);
              }

              sortedData.forEach((element, k) => {
                if (data.trSum) {
                perCentContactNew = (newCusomerTrSum.contact != 0) ? (element.new_customer.contact / newCusomerTrSum.contact * 100) : 0;
                perCentOrderNew =  (newCusomerTrSum.count_order != 0) ? (element.new_customer.count_order / newCusomerTrSum.count_order * 100) : 0;
                perCentProductNew = (newCusomerTrSum.product != 0) ? (element.new_customer.product / newCusomerTrSum.product * 100) : 0;
                perCentTotalNew = (newCusomerTrSum.total != 0) ? (element.new_customer.total / newCusomerTrSum.total * 100) : 0;
                perCentAvgNew = (newCusomerTrSum.avg != 0) ? (element.new_customer.avg / newCusomerTrSum.avg * 100) : 0;

                perCentContactOld = (oldCusomerTrSum.contact != 0) ? (element.old_customer.contact / oldCusomerTrSum.contact * 100) : 0;
                perCentOrderOld =  (oldCusomerTrSum.count_order != 0) ? (element.old_customer.count_order / oldCusomerTrSum.count_order * 100) : 0;
                perCentProductOld = (oldCusomerTrSum.product != 0) ? (element.old_customer.product / oldCusomerTrSum.product * 100) : 0;
                perCentTotalOld = (oldCusomerTrSum.total != 0) ? (element.old_customer.total / oldCusomerTrSum.total * 100) : 0;
                perCentAvgOld = (oldCusomerTrSum.avg != 0) ? (element.old_customer.avg / oldCusomerTrSum.avg * 100) : 0;

                perCentTotalSum = (summaryCusomerTrSum.total != 0) ? (element.summary_total.total / summaryCusomerTrSum.total * 100) : 0;
                perCentAvgSum = (maxAvcElem.avg != 0) ? (element.summary_total.avg / maxAvcElem * 100) : 0;
                } else {
                  perCentContactNew =  element.new_customer.contact  > 0 ? 100 : 0;
                  perCentOrderNew =  element.new_customer.count_order > 0 ? 100 : 0;
                  perCentProductNew =  element.new_customer.product > 0 ? 100 : 0;
                  perCentTotalNew =  element.new_customer.total > 0 ? 100 : 0;
                  perCentAvgNew =  element.new_customer.avg > 0 ? 100 : 0;
                  perCentContactOld =  element.old_customer.contact > 0 ? 100 : 0;
                  perCentOrderOld =  element.old_customer.count_order > 0 ? 100 : 0;
                  perCentProductOld =  element.old_customer.product > 0 ? 100 : 0;
                  perCentTotalOld =  element.old_customer.total > 0 ? 100 : 0;
                  perCentAvgOld =  element.old_customer.avg > 0 ? 100 : 0;
                  perCentTotalSum =  element.summary_total.total > 0 ? 100 : 0;
                  perCentAvgSum =  element.summary_total.avg > 0 ? 100 : 0;
                }      
                str += '<tr>'
                  + '<td class="text-center">' + (k+1) + '</td>'
                  + '<td>' + element.name + '</td>'
                  + '<td class="tdProgress tdSoContact"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentContactNew + '%"></div>'
                  + '</div><span class="progress-text">' +  element.new_customer.contact + '</span></div></td>'
                  + '<td class="tdProgress tdSoChotDon"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentOrderNew + '%"></div>'
                  + '</div><span class="progress-text">' +  element.new_customer.count_order + '</span></div></td>'
                  + '<td class="tdProgress tdTyLeChotDon"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' +  element.new_customer.rate + '%"></div>'
                  + '</div><span class="progress-text">' +  element.new_customer.rate + '%</span></div></td>'
                  + '<td class="tdProgress tdSoSanPham"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentProductNew + '%"></div>'
                  + '</div><span class="progress-text">' +  element.new_customer.product + '</span></div></td>'
                  + '<td class="tdProgress tdDoanhSo"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentTotalNew + '%"></div>'
                  + '</div><span class="progress-text">' +  number_format_js(element.new_customer.total) + '</span></div></td>'
                  + '<td class="tdProgress tdGiaTriDon"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentAvgNew + '%"></div>'
                  + '</div><span class="progress-text">' + number_format_js(element.new_customer.avg) + '</span></div></td>';
                          
                str += '<td class="tdProgress tdSoContact"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentContactOld + '%"></div>'
                  + '</div><span class="progress-text">' +  element.old_customer.contact + '</span></div></td>'
                  + '</div><span class="progress-text">' + element.old_customer.product + '</span></div></td>'
                  + '<td class="tdProgress tdSoChotDon"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentOrderOld + '%"></div>'
                  + '</div><span class="progress-text">' + element.old_customer.count_order + '</span></div></td>'
                  + '<td class="tdProgress tdDoanhSo"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentTotalOld + '%"></div>'
                  + '</div><span class="progress-text">' + number_format_js(element.old_customer.total) + '</span></div></td>'
                  
                  + '<td class="tdProgress tdGiaTriDon"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentAvgOld + '%"></div>'
                  + '</div><span class="progress-text">' + number_format_js(element.old_customer.avg) + '</span></div></td>';

                str += '<td class="tdProgress tdTyLeChotDon"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' +  element.summary_total.rate + '%"></div>'
                  + '</div><span class="progress-text">' +  element.summary_total.rate + '%</span></div></td>'
                  + '<td class="tdProgress tdDoanhSoTong"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentTotalSum + '%"></div>'
                  + '</div><span class="progress-text">' + number_format_js(element.summary_total.total) + '</span></div></td>'
                  + '<td class="tdProgress tdGiaTriDon"><div class="box-progress"><div class="progress">'
                  + '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: ' + perCentAvgSum + '%"></div>'
                  + '</div><span class="progress-text">' + number_format_js(element.summary_total.avg) + '</span></div></td></tr>';           
                  $("#body-digital").html(str);
                });

              // $("#body-digital").text(str);
            }

          
          }
      });
    }
  }

  setTimeout(function () {
    loadDataReportHome();
  }, 1000);

  
  function loadDataReportHome()
  {
    let value =  $("input[name='daterange']").val();
    // let arr = value.split("-");
    var _token    = $("input[name='_token']").val();
    var status    = $("select[name='status']").val();
    var category  = $("select[name='category']").val();
    var product   = $("select[name='product']").val();
    var sale      = $("select[name='sale']").val();
    var mkt       = $("select[name='mkt']").val();
    var src       = $("select[name='src']").val();
    var group     = $("select[name='group']").val();
    var groupUser = $("select[name='groupUser']").val();
    var sort = $("select[name='sort']").val();

    data = {
      _token : _token,
      type : 'daterange',
      date : value
    };

    if (status != '999' && status != undefined) {
      data.status = status;
    } if (category != '999' && category != undefined) {
      data.category = category;
    } if (product != '999' && product != undefined) {
      data.product = product;
    } if (sale != '999' && sale != undefined) {
      data.sale = sale;
    } if (mkt != '999' && mkt != undefined) {
      data.mkt = mkt;
    } if (src != '999' && src != undefined) {
      data.src = src;
    } if (group != '999' && group != undefined) {
      data.group = group;
    } if (groupUser != '999' && groupUser != undefined) {
      data.groupUser = groupUser;
    } if (sort != '' && sort != undefined) {
      data.sort = sort;
    }

    ajaxGetListDigital(data);
  }
</script>

@stop