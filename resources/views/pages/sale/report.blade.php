@extends('layouts.default')
@section('content')

<link href="{{ asset('public/css/style.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{asset('public/css/dashboard.css')}}" /> 
<link rel="stylesheet" type="text/css" href="{{asset('public/css/pages/rank.css')}}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

<?php $checkAll = isFullAccess(Auth::user()->role);
  $isLeadSale = Helper::isLeadSale(Auth::user()->role);
  $enableSale = ($checkAll || $isLeadSale || Auth::user()->is_CSKH || Auth::user()->is_sale);
  $enableDigital = ($checkAll || Auth::user()->is_digital);
?>

<div class="body flex-grow-1 px-3">
  
  <div class="container-lg">
    <div id="loader-overlay">
      <div class="loader"></div>
    </div>
    <div class="row mb-1 filter-order">
      
    <div class="col-xs-12 col-sm-6 col-md-4 form-group daterange mb-1">
      <input id="daterange" class=" btn btn-outline-secondary" type="text" name="daterange"/>
    </div>

      {{ csrf_field() }}
      
    </div>
    <div class="row mb-1">
      <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
        <button type="button" id="btn-filter"  class="btn btn-outline-primary"><svg class="icon me-2">
          <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-filter')}}"></use>
        </svg>Lọc</button>
        
      </div>
    </div>

    <div class="row">
      <div class="box-body" style="padding-top: 0px;">
        <div class="dragscroll1 tableFixHead table_sale">
          <table class="table table-bordered table-multi-select" id="tableReportSale">
            <thead>
              <tr style="cursor: grab;" class="drags-area">
                <th class="text-center" style="width: 20px">STT</th>
                <th class="text-center">Sale</th>
                <th class="text-center">Tổng Data TN</th>
              </tr>
            </thead>
            <tbody id="tbody-data">
              <?php $i = 1;?>
              @if (isset($dataCountSale))
                @foreach ($dataCountSale as $data)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$data['name']}}</td>
                  <td class="text-center">{{$data['count']}}</td>
                </tr>
                <?php $i++; ?>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>
{{-- <script type="text/javascript" src="{{asset('public/js/dateRangePicker/dateRangePicker-vi.js')}}"></script> --}}
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
      ('loader-overlay').css('display', 'flex');
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
              if (!$.isEmptyObject(data.totalSum)) {
                $("#totalSum").text(data.totalSum);
                $(".percentTotalDay").text(data.percentTotal);
                $(".countOrders").text(data.countOrders);
                $(".percentCountDay").text(data.percentCount);
                $(".avgOrders").text(data.avgOrders);
                $(".percentAvg").text(data.percentAvg);
              }
              $('#loader-overlay').css('display', 'none');
            }
        });
    });

    $("#btn-filter").on("click", function() {
      let date =  $("input[name='daterange']").val();
      var _token = $("input[name='_token']").val();
      
      $('#loader-overlay').css('display', 'flex');
      $.ajax({
            url: "{{ route('view-count-dataTN-ajax') }}",
            type: 'GET',
            data: {
              _token: _token,
              date,
            },
            success: function(data) {
              
              var rs = '';
              var baseLink = location.href.slice(0,location.href.lastIndexOf("/"));
              var i = 1;
              data.forEach(element => {
                rs += `<tr>
                <td>` + i + `</td>
                <td>` + element.name + `</td>
                <td class="text-center">` + element.count + `</td>
                </tr>`;
                i++;
              });

              $('#tbody-data').html(rs);
              $('#loader-overlay').css('display', 'none');
            }
        });
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

</script>
<script>
    function number_format_js(number) {
        number = number.toLocaleString('vi-VN');
        return number.replace(/,/g, '.').replace(/\./g, ',');
    }
</script>

@stop