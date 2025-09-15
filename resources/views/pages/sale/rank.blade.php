@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{asset('public/css/dashboard.css')}}" /> 
<link rel="stylesheet" type="text/css" href="{{asset('public/css/pages/rank.css')}}" />
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

  .body {
    background: #fff;
  }

  .loader {
    top: 50%;
    left: 50%;
    position: absolute;
  }

  @media (max-width: 576px) {
        .hidden-xs {
          display: none;
        }

      .bxh .item-rank {
        width: 28.5% !important;
        height: 6.5% !important;
        position: unset !important;
        display: inline-block !important;
      }

      .bxh .item-rank1 .king-sale {
        right: 50%;
        width: 43%;
        top: 26%;
        height: 10%;
      }

      .bxh .king-sale img {
          width: 29%;
      }

      #table-data {
        padding-top: 60px;
        position: relative;
      }
    }
</style>

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

      <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
        <select name="group" id="group-filter" class="form-select">
          <option   value="999">--Chọn nhóm--</option>  
            @if (isset($groups))
                @foreach($groups as $group)
                <option value="{{$group->id}}">{{$group->name}}</option>
                @endforeach
            @endif
        </select>
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
        <div class="col-sm-12 col-md-12 col-lg-12 form-group">
          
            <div class="square">
                <div class="content1">
                    <div class="bxh bxh-container" style="border: 1px solid transparent;">
                        <div class="hidden-xs" style="transform: rotate(12.5deg); height: 8px; width: 100%; background-color: #ecedef; position: absolute; top: 13.5%;"></div>
                        <div id="table-data">
                            
                            @if (isset($dataSort))
                            <?php $i = 1; 
                            // dd($dataSort);
                            ?>
                            @foreach ($dataSort as $sale)
                            <div class="item-rank  item-rank{{$i}}" title="{{$sale['name']}}">
                                <div class="king-sale">
                                    <img src="{{asset('public/images/bxh2.png')}}">
                                </div>
                                <div class="avatar-container  blink{{$i}}">
                                    {{-- <img class="avatar-img" src="{{asset('public/assets/img/avatars/8.jpg')}}"> --}}
                                    
                                    <img class="avatar-img" src="{{asset($sale['profile_image'])}}">
                                </div>
                                <div class="item-info">
                                    <div class="item-stt">{{$i}}</div>
                                    <div class="item-tennv">{{$sale['name']}}</div>
                                    <div class="item-ds">
                                        {{number_format($sale['summary_total']['total'])}}
                                        
                                    </div>
                                </div>
                            </div>
                            <?php 
                            if ($i == 11) {
                                break;
                            }

                            $i++;
                            ?>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
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
      $('#loader-overlay').css('display', 'flex');
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
              $('#loader-overlay').css('display', 'none');
            }
        });
    });

    $("#btn-filter").on("click", function() {
      let date =  $("input[name='daterange']").val();
      date = date.split("-");
      var _token    = $("input[name='_token']").val();
      
      $('#loader-overlay').css('display', 'flex');
      $.ajax({
            url: "{{ route('view-rank-ajax') }}",
            type: 'GET',
            data: {
              _token: _token,
              date,
            },
            success: function(data) {
              var rs = '';
              var i = 1;
              var baseLink = location.href.slice(0,location.href.lastIndexOf("/"));
              data.forEach(element => {
                rs += '<div class="item-rank  item-rank' + i + '" title="' + element.name + '">';
                rs += '<div class="king-sale">';
                rs += '<img src="' + baseLink + '/public/images/bxh2.png">';
                rs += '</div>';
                rs += '<div class="avatar-container  blink' + i + '">';

                rs += '<img class="avatar-img" src="' + baseLink + element.profile_image +'">';
                rs += '</div>';
                rs += '<div class="item-info">';
                rs += '<div class="item-stt">' + i + '</div>'
                rs += '<div class="item-tennv">' + element.name + '</div>';
                rs += '<div class="item-ds">';
                rs += new Intl.NumberFormat().format(element.summary_total.total);
                    
                rs += '</div>';
                rs += '</div>';
                rs += '</div>';
                if (i == 10) {
                  return;
                }
                i++;
              });
             
              $('#table-data').html(rs);
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