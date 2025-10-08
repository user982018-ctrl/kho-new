<?php
use App\Http\Controllers\OrdersController;
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

    
<link href="{{ asset('public/css/pages/order.css') }}" rel="stylesheet">

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
          <option value="999">--Trạng thái đơn hàng--</option>
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
      <div class="col-xs-12 col-sm-6 col-md-2 form-group">
        <select name="print_status" id="print-status-filter" class="form-select" style="padding-right: 12px !important;padding-left: 12px !important;">
          <option value="999">--Trạng thái in đơn--</option>
          <option value="0">Chưa in</option>
          <option value="1">Đã in</option>
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
    <div class="row" style="justify-content:space-between;">
      
      <div class="col-xs-12 col-sm-6 col-md-4" style="padding-bottom:10px;">
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
       
        <button type="button" class="btn" style="padding-left:0;">Tổng đơn: <span id="total-val" list_id="[]" data-total="0">{{$totalOrder}}</span></button>
        <button type="button" class="btn btn-total-product btn-secondary-page"><a class="orderModal"
          data-target="#createOrder" data-toggle="modal" data-href="{{$hrefProduct}}"
          > Tổng sản phẩm: {{$sumProduct}}</a></button>
        <button type="button" class="btn btn-secondary-page btn-print-all"><i class="fas fa-print"></i> In vận đơn</button>
        @endif
      </div>
      <div class="col-12 col-sm-6 col-md-4 form-group" style="display: flex; justify-content:flex-end;">
        <input style="width: 70%" name="search" type="text"  value="{{ isset($search) ? $search : null}}" class="form-control" placeholder="Nhập số điện thoại, tên khách hàng, mã vận đơn">
        <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
          <svg class="icon me-2">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-filter')}}"></use>
          </svg>Lọc
        </button>
      </div>
      
    </div>
  </form>
</div>
{{-- <div><span id="total-val" list_id="[]" data-total="0"></span></div> --}}

<div style="overflow-x: auto;" class="tab-pane p-0 pt-1 active preview" role="tabpanel" id="preview-1002">
  <table class="table table-bordered table-line">
    <thead>
      <tr>
        <th scope="col"><span class="chk-all" style="display: inline-block; min-width: 40px;">
          <input id="checkAllId" type="checkbox">
          <label for="checkAllId" >STT</label></span> </th>
        <th scope="col">ĐVVC</th>
        <th scope="col">Người nhận</th>
        <th scope="col">Sản phẩm</th>
        <th scope="col">Tổng</th>
        <th class="text-center" scope="col">Trạng thái</th>
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
        <td class="chk-item">
          @if ($shippingOrderId)
          <input data-id="{{$item->id}}" class="chk-item-input" value="{{$item->id}}" type="checkbox" id="{{$item->id}}">
          @endif
          <label for="{{$item->id}}">{{$i}}</label>
        </td>
        <?php $i++;?>
        
        </td>
        <td >@if ($shippingOrderId)
          <?= ($shippingOrder->vendor_ship)?>
          <a class="print" target="_blank" data-title="nhấn để in đơn này" href="{{route('print-order-code-'. $shippingOrder->vendor_ship,['order_code'=>$orderCode])}}" role="button">
              <svg class="icon me-2 " >
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-print')}}"></use>
              </svg>
          </a>
          <br>
          <span>Mã vận đơn: <a data-title="chi tiết đơn vận" target="_blank" href="{{route('detai-shipping-order',['id'=>$shippingOrderId])}}" role="button">{{$orderCode}}</a>
          </span>
          <br>
          @if ($shippingOrder->print_status == 1)
          <span class="status-tracking green">Đã in vận đơn</span>
          @else 
          <span class="status-tracking red">Chưa in vận đơn</span>
          @endif
          {{-- <br> --}}
          
            @else
            <a target="_blank" href="{{URL::to('tao-van-don/'. $item->id)}}" class="btn-create-tracking btn btn-warning ms-1" style="color:#fff;">Tạo vận đơn</a>
            @endif
         
        </td>
        <td style='cursor: pointer;'> 
          <a target="_blank" href="{{route('view-order', $item->id)}}">
            <span>#{{$item->id}}</span>
            <br>
            <span>{{ $item->phone }}</span> - <span>{{$name .= $item->name }}</span>

          </a>
         
          <br>
          <span class="mini-text">Ngày tạo: {{ date_format($item->created_at,"H:i d-m-Y ")}}</span>
        </td>
        <td class="products">
          <?php
          $orderCollter = new OrdersController();
          $listProduct = $orderCollter->getDetailProductsByIdOrder($item);
          echo $listProduct;
             ?>
        </td>
        <td class="total"> <p>Thu COD: <span class="price">{{ number_format($item->total) }} đ </span></p> 
          <p class="qty">Số sản phẩm: {{ $item->qty }}</p>
          <span>Phí ship: </span>
        </td>
        <td class="text-center {{$styleStatus[$item->status]}}">
          
          <span>{{$listStatus[$item->status]}}</span> </td>
       
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

  let printStatus = $.urlParam('print_status')
  if (printStatus) {
    $('#print-status-filter option[value=' + printStatus +']').attr('selected','selected');
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
      $('#dvvc-filter').select2();
      $('#status-filter').select2();
      $('#sale-filter').select2();
      $('#print-status-filter').select2();
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

<script>
  $("#checkAllId").click(function () {
    $('.chk-item-input:checkbox').not(this).prop('checked', this.checked);

    var $checkboxes = $('.chk-item-input:checkbox');
    var countCheckedCheckboxes = $checkboxes.filter(':checked').length;

    $('#total-val').data( "total", countCheckedCheckboxes );
    $('#total-val').text( countCheckedCheckboxes);

    var list = [];
    $('.chk-item-input:checkbox:checked').each(function () {
        list.push($(this).val());
    });

    list = list.map((x) => parseInt(x));

    var listIdString = JSON.stringify(list);
    $('#total-val').attr('list_id', listIdString);
    console.log(list);
    });

    $(".chk-item-input").click(function () {

        var total = $('#total-val').data('total');
        var listId = $('#total-val').attr('list_id');
        var id = $(this).data("id");

        listId = JSON.parse(listId);
        if ($(this).is(":checked")) {
            total = parseInt(total) + 1;
            if (listId.indexOf(id) == -1) {
                listId.push(id);
            }
        } else {
            total = parseInt(total) - 1;
            if (listId.indexOf(id) > -1) {
                listId.splice(listId.indexOf(id), 1);
            }
        }

        var listIdString = JSON.stringify(listId);
        $('#total-val').attr('list_id', listIdString);
        $('#total-val').data( "total", total );
        $('#total-val').text(total);
    });

     $(".btn-print-all").click(function () {
      var total = $('#total-val').data('total');
      var list_id = $('#total-val').attr('list_id');
      // console.log(typeof list_id);
      if (typeof list_id === 'string' && list_id === '[]') {
        alert('Chưa chọn đơn!');
        return;
      }

      var link = "{{URL::to('/in-tat-ca-van-don')}}";
      window.open(link + '?q=' + list_id);
    });
</script>