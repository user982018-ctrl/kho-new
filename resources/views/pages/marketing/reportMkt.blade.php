@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{asset('public/css/dashboard.css')}}" /> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

<style>
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
    animation: scrollLeft 36s linear infinite;
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
    from { transform: translateX(100%); }
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
        <option value="999">--Chọn Trạng Thái--</option>
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


<script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>

<script>
  $(document).ready(function() {
    
    $('input[name="daterange"]').daterangepicker({
      ranges: {
        'Hôm nay': [moment(), moment()],
        'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '7 ngày gần đây': [moment().subtract(6, 'days'), moment()],
        '30 ngày gần đây': [moment().subtract(29, 'days'), moment()],
        'Tháng này': [moment().startOf('month'), moment().endOf('month')],
        'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      locale: {
        "format": 'DD/MM/YYYY',
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
      }
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
              /* lọc data digital*/
              var str = '';
              console.log('data', data.data)
              var newCusomerTrSum = data.trSum.new_customer;
              var oldCusomerTrSum = data.trSum.old_customer;
              var summaryCusomerTrSum = data.trSum.sumary_total;
              var maxAvcElem = data.data[0].summary_total.avg;

              /** lấy ra trung bình đơn lớn nhất của trong list sale**/
              data.data.forEach((element, k) => {
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

              data.data.forEach((element, k) => {
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
    }

    ajaxGetListDigital(data);
  }
</script>

@stop