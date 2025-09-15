<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    #laravel-notify .notify {
      z-index: 9999;
  }
  .example-custom {
    font-size: 13px;
  }
  /* .header.header-sticky {
    display: none;
  } */

  .green span {
    width: 75px;
    display: inline-block; 
    color: #fff;
    background: #0f0;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #0f0;
    font-weight: 700;
  }

  .red span {
    width: 75px;
    display: inline-block; 
    color: #ff0000;
    background: #fff;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #ff0000;
    font-weight: 700;
  }

  .orange span {
    width: 75px;
    display: inline-block;
    color: #fff;
    background: #ffbe08;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #fff;
    font-weight: 700;
  }
  #myModal .modal-dialog {
    /* margin-top: 5px;
    width: 1280px; */
    /* margin: 10px; */
    height: 90%;
    /* background: #0f0; */
  }
  #myModal .modal-dialog iframe {
    /* 100% = dialog height, 120px = header + footer */
    height: 100%;
    overflow-y: scroll;
  }


  .filter-order .daterange {
    min-width: 230px;
  }

  .add-order {
    position: fixed;
    right: 10px;
    bottom: 10px;
  }

  input#daterange {
    color: #000;
    width: 100%;
  }

  .link-name {
    text-decoration: none;
    color: unset;
  }
      .select2-container {
        width: 100% !important;
    }
    /* .select2-selection__rendered { */
  .result-TN-col .select-assign, .result-TN-col .select2-container--default .select2-selection--single , .result-TN {
      background-color: inherit !important;
      border: none;
  }

  .selectedClass .select2-container {
      box-shadow: rgb(0, 123, 255) 0px 1px 1px 1px;
  }

  .form-control {
    line-height: unset;
  }
  
  .row > * {
    padding-right: 12px;
    padding-left: 12px;
  }
  .btn.active {
    border-color: #0f0;
  }
  .modal-dialog .modal-content {
    height: 100%;
    /* overflow: scroll; */
  }
  .modal-dialog,
.modal-content {
    /* 80% of window height */
    height: 90%;
}
iframe {
  height: 100%;
}

.modal-body {
    /* 100% = dialog height, 120px = header + footer */
    max-height: calc(100% - 120px);
    overflow-y: scroll;
}

.btn-outline-primary:hover a{
  color: aliceblue;
}
</style>

<?php 
  $active       = '';
  $routeName    = \Route::getCurrentRoute()->uri;
  $asRouteName  = (\Route::getCurrentRoute()->action['as']) ?? null;

  $checkAll = isFullAccess(Auth::user()->role);
  $isLeadSale = Helper::isLeadSale(Auth::user()->role);

  $listStatus = Helper::getListStatus();
  $styleStatus = [
    0 => 'red', // huy
    1 => 'white', // chua giao
    2 => 'orange', // dang giao
    3 => 'green', // thanh cong
  ];
  $listSale = Helper::getListSale(); 
  $checkAll = isFullAccess(Auth::user()->role);  
  $flag = false;

  if (($listSale->count() > 0 &&  $checkAll)) {
      $flag = true;
  }

?>

<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

<div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">
  <form action="{{route('order')}}" class="mb-1" id="orderForm">
    <div class="row mb-1">
      <div class="col-12 col-sm-6 col-md-3 form-group daterange">
        <input id="daterange" class="btn btn-outline-secondary" type="text" name="daterange" />
      </div>

      @if ($checkAll || $isLeadSale)
      <div class="col-xs-12 col-sm-6 col-md-2 form-group">
        <select name="sale" id="sale-filter" class="form-select" style="padding-right: 12px !important;padding-left: 12px !important;">
          <option value="999">--Chọn Sale--</option>
          @if (isset($sales))
            @foreach($sales as $sale)
            <option value="{{$sale->id}}">{{($sale->real_name) ? : $sale->name}}</option>
            @endforeach
          @endif
        </select>
      </div>
      @endif

      <div class="col-xs-12 col-sm-6 col-md-2 form-group">
        <select name="status" id="status-filter" class="form-select" style="padding-right: 12px !important;padding-left: 12px !important;">
          <option value="999">--Trạng Thái--</option>
          <option value="1">Chưa giao vận</option>
          <option value="4">Chờ vận đơn</option>
          <option value="5">Có vận đơn, đvvc chưa lấy</option>
          <option value="2">Đang giao</option>
          <option value="3">Hoàn tất</option>
          <option value="0">Huỷ</option>
        </select>
      </div>
      
      <div class="col-xs-12 col-sm-6 col-md-2 form-group">
        <select name="dvvc" id="dvvc-filter" class="form-select" style="padding-right: 12px !important;padding-left: 12px !important;">
          <option value="999">--Đơn vị vận chuyển--</option>
          <option value="GHN">Giao hàng nhanh</option>
          <option value="GHTK">Giao hàng tiết kiệm</option>
        </select>
      </div>
    </div>
    <div class="row filter-order">
      <div class="col-xs-12 col-sm-6 col-md-2 form-group">
        <select name="product" id="product-filter" class="form-select" style="padding-right: 12px !important;padding-left: 12px !important;">
          <option value="999">--Chọn sản phẩm --</option>
          @if (isset($products))
            @foreach($products as $product)
            <option value="{{$product->id}}">{{$product->name}}</option>
            @endforeach
          @endif
        </select>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-12 col-sm-6 col-md-4 form-group" style="display: flex;">
          <input style="width: 70%" name="search" type="text"  value="{{ isset($search) ? $search : null}}" class="form-control" placeholder="Nhập số điện thoại, tên khách hàng, mã vận đơn">
          <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
            <svg class="icon me-2">
              <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-filter')}}"></use>
            </svg>Lọc
          </button>
        </div>
      <div class="col-12 col-sm-6 col-md-4">
        @if (isset($list))
        <?php $activeBtnOrder = $activeBtnProduct = '';
          $hrefOrder = $hrefProduct = url()->full();
          $params = request()->route()->parameters();
          if ($routeName == 'don-hang') {
            $activeBtnOrder = 'active';
            $hrefProduct = route('report-product-by-order', array_merge(request()->route()->parameters(), request()->query()));
          } else if ($routeName == 'thong-ke-san-pham-theo-don'){
            $activeBtnProduct = 'active';
            $hrefOrder = request()->route('order', $params);
          }
          // $hrefProduct = url()->full();
        ?> 
       
        <button type="button" class="btn">Tổng đơn: {{$totalOrder}}</button>
        <button type="button" class="btn btn-outline-primary"><a class="orderModal"
          data-target="#createOrder" data-toggle="modal" data-href="{{$hrefProduct}}"
          > Tổng sản phẩm: {{$sumProduct}}</a></button>
        @endif
      </div>
      
    </div>
  </form>
</div>

<div style="overflow-x: auto;" class="tab-pane p-0 pt-1 active preview" role="tabpanel" id="preview-1002">
  <table class="table table-bordered table-line">
    <thead>
      <tr>
        <th scope="col">STT</th>
        <th scope="col">ID đơn hàng</th>
        <th scope="col">Sđt</th>
        <th class="mobile-col-tbl" scope="col" >Tên</th>
        <th scope="col" class="text-center">Số lượng</th>
        <th scope="col" class="text-center">Tổng tiền</th>
        <th class="text-center" scope="col">Trạng thái</th>
        <th scope="col" class="text-center">Mã vận đơn</th>
        <th scope="col" class="text-center">ĐVVC</th>
        <th class="mobile-col-tbl" scope="col">Ngày tạo đơn</th>
        <th scope="col"></th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>

    <?php $i = 1; ?>
    @foreach ($list as $item)
    <?php $name = '';
    // if (Helper::isOldCustomerV2($item->phone)) {
    //     $name .= '❤️ ';
    // }

    $shippingOrder    = $item->shippingOrder()->get()->first();
    $orderCode        = $shippingOrder->order_code ?? '';
    $shippingOrderId  = $shippingOrder->id ?? '';
    ?>
      <tr>
        <td>{{$i}}</td>
        <?php $i++;?>
        <td onclick="window.location='{{route('view-order', $item->id)}}';" style='cursor: pointer;'>#{{ $item->id }}</td>
        <td style='cursor: pointer;'> <a class="link-name" target="blank" href="{{route('view-order', $item->id)}}">{{ $item->phone }}</a> </td>
        <td style='cursor: pointer;' class="mobile-col-tbl"> <a class="link-name" target="blank" href="{{route('view-order', $item->id)}}">{{$name .= $item->name }}</a></td>
        <td class="text-center"> {{ $item->qty }} </td>
        <td class="text-center"> {{ number_format($item->total) }}đ</td>
        <td class="text-center {{$styleStatus[$item->status]}}"><span>{{$listStatus[$item->status]}}</span> </td>
        <td class="text-center">
          @if ($shippingOrderId)
          <a  title="sửa" target="_blank" href="{{route('detai-shipping-order',['id'=>$shippingOrderId])}}" role="button">{{$orderCode}}</a>
          @endif
        
        </td>
        <td class="text-center">@if ($shippingOrderId)
          <?= ($shippingOrder->vendor_ship)?>
          @endif
        </td>

        <td class="mobile-col-tbl">  {{ date_format($item->created_at,"H:i d-m-Y ")}}</td>
        <td>
          <a title="sửa" href="{{route('update-order',['id'=>$item->id])}}" role="button">
            
              <svg class="icon me-2">
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
              </svg>
          </a>
        </td>
        
        <td >
          <?php $checkAll = isFullAccess(Auth::user()->role);?>
          @if ($checkAll || $isLeadSale)
          <a title="xoá" onclick="return confirm('Bạn muốn xóa đơn này?')" href="{{route('delete-order',['id'=>$item->id])}}" role="button">
            <svg class="icon me-2">
              <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}"></use>
            </svg>
          </a>
          @endif
        </td>
        
      </tr>
      @endforeach
      
    </tbody>
  </table>
  {{ $list->appends(request()->input())->links('pagination::bootstrap-5') }}
</div>

  
<div class="modal fade" id="notify-modal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" style="color: seagreen;"><p style="margin:0">thành công</p></h6>
            <button style="border: none;" type="button" id="close-modal-notify" class="close" data-dismiss="modal" >
              <span>&times;</span>
            </button>
          </div>
        </div>
    </div>
</div>
<div id="createOrder" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Thống kê sản phẩm theo đơn hàng</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe frameborder="0"></iframe>
        </div>
    </div>
</div>
<script>
  $.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results) {
      return results[1];
    }
    return 0;
  }

  let time = $.urlParam('daterange') 
  if (time) {
    time = decodeURIComponent(time)
    time = time.replace('+-+', ' - ') //loại bỏ khoảng trắng
    $('input[name="daterange"]').val(time)
  }

  let sale = $.urlParam('sale') 
  if (sale) {
    $('#sale-filter option[value=' + sale +']').attr('selected','selected');
  }

  let status = $.urlParam('status') 
  if (status) {
    $('#status-filter option[value=' + status +']').attr('selected','selected');
  }

  let dvvc = $.urlParam('dvvc')
  if (dvvc) {
    $('#dvvc-filter option[value=' + dvvc +']').attr('selected','selected');
  }

  let category = $.urlParam('category') 
  if (category) {
    $('#category-filter option[value=' + category +']').attr('selected','selected');
  }

  let product = $.urlParam('product') 
  if (product) {
    $('#product-filter option[value=' + product +']').attr('selected','selected');
    if (product == 83) {
      const attributesJson = '<?php echo $listAttribute;?>';
      data = JSON.parse(attributesJson);
      Object.keys(data).forEach(key => {
        let str = '';
        var idAttr = 'attr_' + data[key]['id'];
        str += '<div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">'
          + '<select name="'+ idAttr +'" id="'+ idAttr +'-filter" class="form-select">'
          + '<option value="999">--'+ data[key]['name'] +' (Tất cả)--</option>';
          data[key]['values'].forEach(value => {
            str += '<option value="' + value['id'] + '">' + value['value'] + '</option>';
          });
        str  += '</select>'
          + '</div>';

        $(str).appendTo(".filter-order");
      });
    }
  }
  let product1 = $.urlParam('attr_1') 
  if (product1 ) {
    $('#attr_1-filter option[value=' + product1 +']').attr('selected','selected');
  }
  let product2 = $.urlParam('attr_2') 
  if (product2 ) {
    $('#attr_2-filter option[value=' + product2 +']').attr('selected','selected');
  }

</script>
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
              + '<option value="999">--Sản phẩm (Tất cả)--</option>';
              data.forEach(item => {
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

let mkt = $.urlParam('mkt') 
if (mkt) {
    $('#mkt-filter option[value=' + mkt +']').attr('selected','selected');
}

let src = $.urlParam('src') 
if (src) {
    // let str = '<option value="999">--Tất cả Nguồn--</option>';
    // $('.src-filter').show('slow');

    // if (mkt == 1) {
    //     mrNguyen.forEach (function(item) {
    //         // console.log(item);
    //         str += '<option value="' + item.id +'">' + item.name_page +'</option>';
    //     })
    //     $(str).appendTo("#src-filter");
    // } else if (mkt == 2) {
    //     mrTien.forEach (function(item) {
    //         // console.log(item);
    //         str += '<option value="' + item.id +'">' + item.name_page +'</option>';
    //     })
    //     $(str).appendTo("#src-filter");
    // }
    $('#src-filter option[value=' + src +']').attr('selected','selected');
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
  $(function() {
      $('#product-filter').select2();
  });
 
  $("#product-filter").change(function() {
      var selectedVal = $(this).find(':selected').val();
      var selectedText = $(this).find(':selected').text();
      
      if (selectedVal == 83) {
        const attributesJson = '<?php echo $listAttribute;?>';
        data = JSON.parse(attributesJson);
        Object.keys(data).forEach(key => {
          let str = '';
          var idAttr = 'attr_' + data[key]['id'];
          str += '<div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">'
            + '<select name="'+ idAttr +'" id="'+ idAttr +'-filter" class="form-select">'
            + '<option value="999">--'+ data[key]['name'] +' (Tất cả)--</option>';
            data[key]['values'].forEach(value => {
              str += '<option value="' + value['id'] + '">' + value['value'] + '</option>';
            });
          str  += '</select>'
            + '</div>';

          $(str).appendTo(".filter-order");
        });

        // $.ajax({
        //   url: "{{ route('attribute') }}",
        //   type: 'GET',
        //   success: function(data) {
        //     Object.keys(data).forEach(key => {
        //       let str = '';
        //       var idAttr = 'attr_' + data[key]['id'];
        //       str += '<div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">'
        //         + '<select name="'+ idAttr +'" id="'+ data[key]['id'] +'-filter" class="form-select">'
        //         + '<option value="999">--'+ data[key]['name'] +' (Tất cả)--</option>';
        //         data[key]['values'].forEach(value => {
        //           str += '<option value="' + value['id'] + '">' + value['value'] + '</option>';
        //         });
        //       str  += '</select>'
        //         + '</div>';

        //       $(str).appendTo(".filter-order");
        //     });
        //   }
        // });
      }
  });
</script>
<script>
document.getElementById('orderForm').addEventListener('submit', function (e) {
    const inputs = this.querySelectorAll('input');
    inputs.forEach(input => {
        if (input.value === '') {
            input.disabled = true; // loại bỏ khỏi dữ liệu gửi đi
        }
    });

    const selects = this.querySelectorAll('select');
    selects.forEach(select => {
      if (select.value === '999') {
        select.disabled = true; // không gửi giá trị này
      }
    });
    
    return;
});
</script>
<script src="{{asset('public/newCDN/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<script>
  $('.orderModal').on('click', function () {
    var href = $(this).data('href');
    if (href) {
        var link = "{{URL::to('/update-order/')}}";
        $("#createOrder iframe").attr("src", href);
    }
  });
</script>