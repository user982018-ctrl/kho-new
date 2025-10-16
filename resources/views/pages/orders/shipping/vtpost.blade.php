<?php
$listStatus = Helper::getListStatus();
$isLeadSale = Helper::isLeadSale(Auth::user()->role);
$checkAll = isFullAccess(Auth::user()->role);
$flagAccess = false;
$name = '';
if (Helper::isOldCustomerV2($order->phone)) {
  $name .= '❤️ ';
}
?>
@extends('layouts.default')
@section('content')
<link href="{{ asset('public/css/pages/notify.css'); }}" rel="stylesheet">
<link href="{{ asset('public/css/notify-override.css'); }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
{{-- <link href="{{ asset('public/css/pages/styleOrders.css')}}" rel="stylesheet"> --}}
<style>
  .select2-container {
    width: 100% !important;
  }
  .selectedClass .select2-container {
      box-shadow: rgb(0, 123, 255) 0px 1px 1px 1px;
  }
  .select-assign, .select2-container--default .select2-selection--single {
      background-color: inherit !important;
      /* border: none; */
  }

  /* Style cho Select2 */
/*   
  .select2-container--default .select2-selection--single {
      height: 38px !important;
      border: 1px solid #d8dbe0 !important;
      border-radius: 0.25rem !important;
      padding: 6px 12px !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 24px !important;
      color: #3c4b64 !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px !important;
  }

  .select2-dropdown {
      border: 1px solid #d8dbe0 !important;
      border-radius: 0.25rem !important;
  }

  .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #3399ff !important;
  }

  .select2-search--dropdown .select2-search__field {
      border: 1px solid #d8dbe0 !important;
      border-radius: 0.25rem !important;
      padding: 6px 12px !important;
  }

  /* Focus state */
  .select2-container--default.select2-container--focus .select2-selection--single,
  .select2-container--default.select2-container--open .select2-selection--single {
      border-color: #80bdff !important;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
  }

  /* Loading state */
  .select2-container--default .select2-results__option[aria-disabled=true] {
      color: #999 !important;
  } */

  #laravel-notify .notify {
      z-index: 99999 !important;
  }

  /* Z-index cho dropdown của Select2 */
  .select2-container--open {
      z-index: 10000 !important;
  }

  .select2-dropdown {
      z-index: 10001 !important;
  }

  input[readonly]:hover,  textarea[readonly]:hover, select[disabled]:hover{
    cursor: not-allowed;
  }
  .header {
    display: unset;
  }
  .border-top-info {
    width: 100%;
    height: 6px;
    background: #64a5ff;
    top: 0;
    left: 0;
    right: 0;
    background-image: url(https://cdn.ghn.vn/online-static/fe-5sao/1.43.20/media/border.73275684.svg);
    background-repeat: repeat-x;
    border-radius: 18px 18px 0 0;
    margin-bottom: 10px;
  }
  .border-top-info-usu {
    background: none
  }
  .background {
    height: 200px;
    color:#fff;
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
  }

  .label-wrap{
    display:flex;
  }

  .hasShipping{
    margin: 20px auto;
    width: 33.33%;
  }

</style>
@include('notify::components.notify')
<script>
  // Set notify timeout to 10 seconds
  if (typeof notify !== 'undefined') {
    notify.timeout = 10000;
  }
</script>
  <div class="background">
    <div class="background">
      <img style="width: auto; height: 100%;" src="{{asset('public/images/vietelpost.png')}}" class="card-img-top">
      
    </div>
   </div>
  <div>
    <div class="hasShipping">
      
      <form action="{{route('create-shipping-has')}}" method="post">
        {{ csrf_field() }}
        <div class='label-wrap form-group'>
          <label for="min">Mã vận đơn:</label><br>
          {{-- <input type="number" id="min" name="min" class="form-control" value="1"> --}}
          <input autofocus required type="text" name="id_shipping_has" class="form-control" placeholder="Nhập mã vận đơn...">
        </div>
        <input type="hidden" name="vendor_ship" value="VTPost">
        <input type="hidden" name="order_id" value="{{$order->id}}">
       
        <button type="submit" class="mt-2 btn btn-primary" style="border:none; background: #e61111;">Áp dụng</button>
      </form>
    </div>
  </div>

  <div class="card-body card-orders" style="padding:10px;">
    <div class="row">
      <div class="col-sm-12 col-lg-5" style="opacity: 0.7; box-shadow: 0 .7699px 2.17382px 0 rgba(0, 71, 111, .02), 0 2.12866px 6.01034px 0 rgba(0, 71, 111, .04), 0 5.125px 14.4706px 0 rgba(0, 71, 111, .05), 0 17px 48px 0 rgba(0, 71, 111, .07);
        background: #fff;">
        <div class="border-top-info-usu border-top-info"></div>
        <div class="row">
          <div class="col-sm-12 col-lg-6  form-group">
            <label class="form-label" for="phoneFor">Số điện thoại</label>
            <input value="{{$order->phone}}" class="form-control" readonly name="phone" id="phoneFor" type="text">
          </div>
          <div class="col-sm-12 col-lg-6  form-group">
            <label class="form-label" for="nameFor">Tên khách hàng</label>
            <input value="{{$order->name}}" class="form-control" readonly name="name" id="nameFor" type="text">
          </div>
          <div class="col-12  form-group">
            <label class="form-label" for="addressFor">Địa chỉ chi tiết</label>
            <input value="{{$order->address}}" readonly class="form-control" name="address" id="addressFor" type="text">
          </div>
          <div class="col-sm-6 col-md-6 form-group">
            <label class="form-label" for="distric-filter">Quận - Huyện<span class="required-input">(*)</span></label>
            <select style="line-height: 28px;padding: 0;padding-left: 5px;" name="district" id="distric-filter" class="form-control" disabled>       
                <option value="-1">--Chọn quận/huyện--</option>
                @foreach ($listProvince as $item)
                <option <?= ($item['id'] == $order->district) ? "selected" : '';?> value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </select>
          </div>
          <div class="col-sm-6 col-md-6 form-group">
            <label class="form-label" for="ward-filter">Phường - xã<span class="required-input">(*)</span></label>
            <select style="line-height: 28px;padding: 0;padding-left: 5px;" name="ward" id="ward-filter" class="form-control" disabled>
                @if (isset($listWard))
                @foreach ($listWard as $ward)
                <option <?= ($ward['id'] == $order->ward) ? "selected" : '';?> value="{{$ward['id']}}">{{$ward['name']}}</option>
                @endforeach
                
                @else
                <option value="-1">--Chọn phường/ xã--</option>
                @endif
            </select>
          </div>
          <div class="col-12 form-group">
            <label for="note" class="form-label"> Ghi chú cho:</label>
            <textarea readonly name="note" class="form-control" id="note" rows="4">{{$order->note}} </textarea>
          </div>
          <div class="col-12 form-group">
            <div id="list-product-choose"></div>
            <table class="table table-bordered table-line" style="margin-bottom:15px; font-size: 13px; ">
                <thead>
                    <tr>
                        <th colspan="1" class="text-center no-wrap col-spname" style="min-width: 155px">Tên sản phẩm</th>
                        <th colspan="1" class="text-center no-wrap">SL Tổng</th>
                    </tr>
                </thead>
                <tbody class="list-product-choose">
                <?php $sumQty = $totalTmp = 0;
                foreach (json_decode($order->id_product) as $item) {
                  $product = getProductByIdHelper($item->id);
                  
                  if ($product) {
                      $sumQty += $item->val;
                      $totalTmp += $item->val * $product->price;
                      $nameProduct = $product->name;
                      if ($product->type == 2 && !empty($item->variantId)) {
                        $variantID = $item->variantId;
                        $nameProduct .= HelperProduct::getNameAttributeByVariantId($variantID);
                      }
                    ?>

                  <tr class="number dh-san-pham product-{{$product->id}}">
                      <td class="text-left">
                          <span class="no-combo">{{$nameProduct}}</span><br>
                      </td>
                      <td class="no-wrap" style="width: 45px">
                        {{$item->val}}
                      </td>
                  </tr>
                  
                  <?php      
                  }    
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="no-wrap text-right" colspan="1">Tổng đơn:
                        </td>
                        <td class="no-wrap" colspan="1">{{number_format($order->total)}} </td>
                    </tr>
                </tfoot>
            </table>
          </div>
        </div>
      </div>

    <form class="col-sm-12 col-lg-7" id="form-create-order-vtpost"  action="{{route('create-order-VTPost')}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{$order->id}}">
    <input value="{{$order->sale_care}}" class="hidden form-control" name="sale-care">

      <div  style="opacity: 0.9; box-shadow: 0 .7699px 2.17382px 0 rgba(0, 71, 111, .02), 0 2.12866px 6.01034px 0 rgba(0, 71, 111, .04), 0 5.125px 14.4706px 0 rgba(0, 71, 111, .05), 0 17px 48px 0 rgba(0, 71, 111, .07);
        background: aliceblue;">
        <div class="row" >
          <div class="border-top-info"></div>
            <div class="col-sm-12 col-lg-6  form-group">
                <label class="form-label" for="phoneFor">Số điện
                    thoại</label>
                <input required value="{{$order->phone}}" class="form-control" 
                    name="phone" id="phoneFor" type="text">
            </div>
            <div class="col-sm-12 col-lg-6  form-group">
                <label class="form-label" for="nameFor">Tên khách
                    hàng</label>
                <input required value="{{$name .= $order->name}}" class="form-control" 
                    name="name" id="nameFor" type="text">
            </div>
            <div class="col-12  form-group">
              <label class="form-label" for="addressDetail">Địa chỉ chi tiết</label>
              <input required value="{{$order->address}}" class="form-control"
                  name="address" id="addressDetail" type="text">
            </div>
            <div class="col-sm-6 col-md-6 form-group address-GHN">
              <label class="form-label" for="province-filter"><b>Tỉnh - Thành phố</b><span class="required-input">(*)</span></label>
              <select name="province" id="province-filter" class="form-control" required>       
                
                  @if (isset($listProvinceVT))
                  @foreach ($listProvinceVT as $item)

                  <option value="{{$item['PROVINCE_ID']}}">{{$item['PROVINCE_NAME']}}</option>
                  @endforeach
                  @endif
              </select>
            </div>
            <div class="col-sm-6 col-md-6 form-group address-GHN">
              <label class="form-label" for="distric-filter-GHN"><b>Quận - Huyện </b><span class="required-input">(*)</span></label>
              <select name="district" id="distric-filter-GHN" class="form-control" required>       
                 
              </select>
            </div>
            <div class="col-sm-6 col-md-6 form-group address-GHN">
                <label class="form-label" for="ward-filter-GHN"><b>Phường - xã</b><span class="required-input">(*)</span></label>
                <select name="ward" id="ward-filter-GHN" class="form-control" required>
                   
                </select>
            </div>
            <div class="col-sm-6 col-md-6 form-group address-GHN">
              <label class="form-label" for="hamlet-filter"><b>Ấp/đường/xóm...</b><span class="required-input">(*)</span></label>
              <select disabled name="hamlet" id="hamlet-filter" class="form-control" required>
                  
              </select>
          </div>
            <div class="col-12 form-group">
              <label for="note" class="form-label"><b>Ghi chú cho GHN:</b></label>
              <textarea name="note" class="form-control" id="note" rows="4">{{$order->note}} </textarea>
            </div>
            <div class="col-12 form-group">
              
              <table class="table table-bordered table-line" style="margin-bottom:15px; font-size: 13px; ">
                <thead>
                  <tr>
                    <th colspan="7" class="text-center no-wrap col-spname" style="min-width: 155px; width:50%;">Tên sản phẩm</th>
                    <th colspan="1" class="text-center no-wrap" style="width:30%;">Khối lượng (gam)</th>
                    <th colspan="1" class="text-center no-wrap" style="width:10%;">SL Tổng</th>
                    <th colspan="1" class="text-center no-wrap" style="width:10%;"><button id="addProductGHN" type="button"> +Thêm </button></th>
                  </tr>
                </thead>
                  <tbody class="list-product-choose" id="list-product-GHN">
                  <?php $sumQty = $totalTmp = $i = $j = $totalWeight = 0; 
                  // dd(json_decode($order->id_product));
                  foreach (json_decode($order->id_product) as $key => $item) {
                    $product = getProductByIdHelper($item->id);

                    if ($product) {
                      $nameProduct = $product->name;
                      $weight = $product->weight;
                      if ($product->type == 2 && !empty($item->variantId)) {
                        $variantID = $item->variantId;
                        $nameProduct .= HelperProduct::getNameAttributeByVariantId($variantID);
                        $variant = HelperProduct::getProductVariantById($variantID);
                        $weight = $variant->weight;
                      }
                      $sumQty += $item->val;
                      $totalTmp += $item->val * $product->price;
                      $totalWeight += $product->weight;
                      // dd($item->val > 1 && $weight > 10000);
                    ?>

                    @if ($item->val > 1 && $weight > 10000)
                    
                    <input name="bigCart[]" type="hidden" value="{{$item->val}} {{$nameProduct}}">
                      <?php for ($j = $i; $j < $item->val + $i; $j++) { ?>
                      <tr class="number dh-san-pham product-{{$product->id}}">
                        <td colspan="7" class="text-left"> <input required name="products[{{$j}}][name]" type="text" style="width: 100%;" value="{{$nameProduct}}"><br>
                        </td>
                        <td colspan="1"><input required class="text-right price_class" required name="products[{{$j}}][weight]" type="text" style="width: 100%;" value="<?php if ($weight > 0) { echo number_format($weight);} ?>"></td>
                        <td class="no-wrap" style="width: 45px">
                          <input class="text-center" required name="products[{{$j}}][qty]" type="text" style="width: 100%;" value="1">
                        </td>
                        <td><button class="deleteProductGHN"><i class="fa fa-trash"></i></button></td>
                      </tr>
                      <?php
                      }
                      $i = $j;
                      ?>
                    @else
                    <tr class="number dh-san-pham product-{{$product->id}}">
                      
                      <td colspan="7" class="text-left"> <input required name="products[{{$i}}][name]" type="text" style="width: 100%;" value="{{$nameProduct}}"><br>
                      </td>
                      <td colspan="1"><input required class="text-right price_class" name="products[{{$i}}][weight]" type="text" style="width: 100%;" value="<?php if ($weight > 0) { echo number_format($weight);} ?>"></td>
                      <td class="no-wrap" style="width: 45px">
                        <input class="text-center" required name="products[{{$i}}][qty]" type="text" style="width: 100%;" value="{{$item->val}}">
                      </td>
                      <td><button class="deleteProductGHN"><i class="fa fa-trash"></i></button></td>
                    </tr>

                    <?php $i++; ?>
                    @endif

                    <?php   
                      
                    }    
                  }
                  ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td class="no-wrap text-right" colspan="8">Tổng đơn:
                      </td>
                      <td class="no-wrap" colspan="2"><input name="cod_amount" type="text" value="{{number_format($order->total)}}"> </td>

                    </tr>
                  </tfoot>
              </table>
              <input type="hidden" id="next-qty-index" value="{{$i}}">
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12" style="text-align: end;">
              <button id="submit" class="mb-1 btn btn-primary create-bill">Tạo vận đơn</button>
          </div>
        </div>
      </div>
    </form>
    </div>
    
  </div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        // Khởi tạo Select2 cho tất cả các select box
        $('#province-filter').select2({
            placeholder: "-- Chọn tỉnh/thành phố --",
            allowClear: false,
            width: '100%'
        });
        
        $('#distric-filter-GHN').select2({
            placeholder: "-- Chọn quận/huyện --",
            allowClear: false,
            width: '100%'
        });
        
        $('#ward-filter-GHN').select2({
            placeholder: "-- Chọn phường/xã --",
            allowClear: false,
            width: '100%'
        });

        $('#hamlet-filter').select2({
            placeholder: "-- Chọn ấp/đường/xóm --",
            allowClear: true,
            width: '100%',
            tags: true // Cho phép nhập tự do
        });
    });
</script>

<script>
  function deleteRowProductGHN(element) {
    // Find the closest <tr> and remove it
    const row = element.closest('tr');
    if (row) {
        row.remove();
    }
  }
</script>
<script>
  $(document).ready(function() {
    // $("input[name='bigCart']").each(function (index, element) {
    //   console.log(`Index: ${index}, Text: ${$(element).text()}`);
    // });
    $('.deleteProductGHN').on('click', function() {
      $(this).closest('tr').remove();
    });

    $('#addProductGHN').on('click', function() {
      var nextIndex = $('#next-qty-index').val();
                    
      str = `<tr><td colspan="7" class="text-left"><input required name="products[` + nextIndex + `][name]" type="text" style="width: 100%;"><br></td>`
        + `<td colspan="1"> <input required name="products[` + nextIndex + `][weight]" class="text-right price_class" type="text" style="width: 100%;"><br></td>`
        + `<td class="no-wrap text-center" style="width: 45px"><input class="text-center" required name="products[` + nextIndex + `][qty]" type="text" style="width: 100%;" value=1></td>`
        + `<td><button onClick="deleteRowProductGHN(this)" type="button" ><i class="fa fa-trash"></i></button></td></tr>`;
      $('#list-product-GHN').append(str);
      var productHeightInput = $("input[name='products["+ nextIndex + "][weight]']");
      console.log(productHeightInput);
      new Cleave(productHeightInput, {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand'
      });

      nextIndex++;
      $('#next-qty-index').val(nextIndex);
    });

    // Xử lý khi chọn tỉnh - load danh sách quận/huyện
    $('#province-filter').on('change', function() {
        var provinceId = this.value;
        console.log('Đã chọn tỉnh:', provinceId);
        
        // Load danh sách quận/huyện từ file JSON
        $.getJSON('{{ asset("public/json/viettel_districts.json") }}', function(response) {
            if (response.data && response.data.length > 0) {
                let str = '<option value="">--Chọn quận/huyện--</option>';
                $.each(response.data, function(index, district) {
                    if (district.PROVINCE_ID == provinceId) {
                        str += `<option value="${district.DISTRICT_ID}">${district.DISTRICT_NAME}</option>`;
                    }
                });
                $('#distric-filter-GHN').html(str).trigger('change');
                $('#ward-filter-GHN').html('<option value="">--Chọn phường/xã--</option>').trigger('change');
                
                // Refresh Select2
                $('#distric-filter-GHN').select2({
                    placeholder: "-- Chọn quận/huyện --",
                    allowClear: false,
                    width: '100%'
                });
                $('#ward-filter-GHN').select2({
                    placeholder: "-- Chọn phường/xã --",
                    allowClear: false,
                    width: '100%'
                });
            }
        });
    });

    // Xử lý khi chọn quận/huyện - load danh sách phường/xã
    $('#distric-filter-GHN').on('change', function() {
        var districtId = this.value;
        console.log('Đã chọn quận/huyện:', districtId);
        
        // Load danh sách phường/xã từ file JSON
        $.getJSON('{{ asset("public/json/viettel_wards.json") }}', function(response) {
            if (response.data && response.data.length > 0) {
                let str = '<option value="">--Chọn phường/xã--</option>';
                $.each(response.data, function(index, ward) {
                    if (ward.DISTRICT_ID == districtId) {
                        str += `<option value="${ward.WARDS_ID}">${ward.WARDS_NAME}</option>`;
                    }
                });
                $('#ward-filter-GHN').html(str).trigger('change');
                
                // Refresh Select2
                $('#ward-filter-GHN').select2({
                    placeholder: "-- Chọn phường/xã --",
                    allowClear: false,
                    width: '100%'
                });
            }
        });
    });

    // === CHỨC NĂNG TỰ ĐỘNG PHÂN TÍCH ĐỊA CHỈ KHI LOAD TRANG ===
    function autoFillAddressFromInput() {
        var addressInput = $('#addressDetail').val();
        
        console.log('📍 addressInput:', addressInput);
        
        if (!addressInput || addressInput.length < 5) {
            console.log('⚠️ Địa chỉ quá ngắn hoặc rỗng');
            return;
        }

        console.log('✅ Bắt đầu phân tích:', addressInput);

        // Phân tích địa chỉ theo dấu phẩy
        var addressParts = addressInput.split(',').map(part => part.trim());
        console.log('📋 Các phần:', addressParts);
        
        var foundProvince = null;
        var foundDistrict = null;
        var foundWard = null;
        var foundHamlet = null;

        // Helper function: Phát hiện loại địa chỉ dựa vào từ khóa
        function detectAddressType(text) {
            var lowerText = text.toLowerCase();
            
            // Kiểm tra tỉnh/thành phố
            if (lowerText.match(/^(tỉnh|thành phố|tp|tp\.)\s+/i)) {
                return 'province';
            }
            
            // Kiểm tra quận/huyện/thị xã
            if (lowerText.match(/^(quận|huyện|thị xã|tx|tx\.)\s+/i)) {
                return 'district';
            }
            
            // Kiểm tra phường/xã/thị trấn
            if (lowerText.match(/^(phường|xã|thị trấn|tt|tt\.)\s+/i)) {
                return 'ward';
            }
            
            // Kiểm tra ấp/thôn/xóm
            if (lowerText.match(/^(ấp|thôn|xóm|đường|số)\s+/i)) {
                return 'hamlet';
            }
            
            return 'unknown';
        }

        // Helper function: Chuẩn hóa tên để so sánh
        function normalizeText(text) {
            var normalized = text.toLowerCase()
                .replace(/\s+/g, ' ')
                .trim();
            
            // Loại bỏ các từ khóa phổ biến để tìm kiếm chính xác hơn
            normalized = normalized.replace(/^(tỉnh|thành phố|tp|tp\.)\s*/i, '');
            normalized = normalized.replace(/^(quận|huyện|thị xã|tx|tx\.)\s*/i, '');
            normalized = normalized.replace(/^(phường|xã|thị trấn|tt|tt\.)\s*/i, '');
            normalized = normalized.replace(/^(ấp|thôn|xóm|đường|số)\s*/i, '');
            
            // Chuẩn hóa các ký tự đặc biệt tiếng Việt
            // Chuyển tất cả các biến thể về một dạng chuẩn
            normalized = normalized
                .replace(/hòa/gi, 'hoa')  // ò -> o (dấu hỏi)
                .replace(/hoà/gi, 'hoa')  // à -> a (dấu huyền)
                .replace(/hoả/gi, 'hoa')  // ả -> a (dấu ngã)
                .replace(/hoạ/gi, 'hoa')  // ạ -> a (dấu nặng)
                .replace(/hoá/gi, 'hoa')  // á -> a (dấu sắc)
                .replace(/huỳnh/gi, 'huynh')
                .replace(/thủy/gi, 'thuy');
            
            return normalized.trim();
        }

        // Helper function: So sánh tên có match không
        function isMatch(fullName, searchPart) {
            var normalizedFull = normalizeText(fullName);
            var normalizedSearch = normalizeText(searchPart);
            
            // Kiểm tra match chính xác
            if (normalizedFull === normalizedSearch) {
                console.log('✅ Match:', searchPart, '→', normalizedSearch, '===', fullName, '→', normalizedFull);
                return true;
            }
            
            // Kiểm tra có chứa không
            if (normalizedFull.indexOf(normalizedSearch) !== -1) {
                console.log('✅ Match contains:', searchPart, '→', normalizedSearch, 'in', fullName, '→', normalizedFull);
                return true;
            }
            
            if (normalizedSearch.indexOf(normalizedFull) !== -1) {
                console.log('✅ Match contains reverse:', searchPart, '→', normalizedSearch, 'contains', fullName, '→', normalizedFull);
                return true;
            }
            
            return false;
        }

        // Phân loại các phần địa chỉ theo từ khóa
        var categorizedParts = {
            province: [],
            district: [],
            ward: [],
            hamlet: [],
            unknown: []
        };

        addressParts.forEach(function(part, index) {
            var type = detectAddressType(part);
            categorizedParts[type].push({
                text: part,
                index: index,
                type: type
            });
            console.log('  [' + index + ']', part, '→', type);
        });
        
        console.log('🏷️ Phân loại:', categorizedParts);

        // Bước 1: Load danh sách tỉnh/thành phố và tìm kiếm
        console.log('📥 Đang load provinces...');
        $.getJSON('{{ asset("public/json/viettel_provinces.json") }}', function(provinceResponse) {
            console.log('✅ Loaded provinces:', provinceResponse.data ? provinceResponse.data.length : 0);
            
            if (!provinceResponse.data) {
                console.log('❌ Không có data provinces');
                return;
            }

            // Tìm tỉnh/thành phố
            var provinceParts = [];
            
            // Ưu tiên 1: Có từ khóa "tỉnh", "thành phố", "tp"
            if (categorizedParts.province.length > 0) {
                provinceParts = categorizedParts.province;
                console.log('🎯 Tìm tỉnh theo từ khóa:', provinceParts);
            } 
            // Ưu tiên 2: Không có từ khóa → lấy phần tử CUỐI CÙNG
            else if (categorizedParts.unknown.length > 0 || addressParts.length > 0) {
                // Lấy phần tử cuối cùng làm tỉnh
                var lastIndex = addressParts.length - 1;
                provinceParts = [{
                    text: addressParts[lastIndex],
                    index: lastIndex,
                    type: 'unknown'
                }];
                console.log('🎯 Không có từ khóa → Lấy phần tử cuối làm tỉnh:', provinceParts);
            }
            
            // Tìm từ cuối lên (vì tỉnh thường ở cuối)
            provinceParts.sort((a, b) => b.index - a.index);
            
            for (var i = 0; i < provinceParts.length; i++) {
                var part = provinceParts[i].text;
                var found = false;
                
                console.log('🔎 Thử tìm:', part);
                
                $.each(provinceResponse.data, function(index, province) {
                    if (isMatch(province.PROVINCE_NAME, part)) {
                        foundProvince = province;
                        found = true;
                        return false; // Break loop
                    }
                });
                
                if (found) break;
            }

            if (!foundProvince) {
                console.log('❌ Không tìm thấy province');
                return;
            }
            
            console.log('✅ Found province:', foundProvince.PROVINCE_NAME);

            // Chọn tỉnh/thành phố
            $('#province-filter').val(foundProvince.PROVINCE_ID).trigger('change');

            // Bước 2: Load danh sách quận/huyện và tìm kiếm
            setTimeout(function() {
                $.getJSON('{{ asset("public/json/viettel_districts.json") }}', function(districtResponse) {
                    if (!districtResponse.data) {
                        return;
                    }

                    // Tìm quận/huyện
                    var districtParts = [];
                    
                    // Ưu tiên 1: Có từ khóa "quận", "huyện", "thị xã"
                    if (categorizedParts.district.length > 0) {
                        districtParts = categorizedParts.district;
                        console.log('🎯 Tìm quận/huyện theo từ khóa:', districtParts);
                    }
                    // Ưu tiên 2: Không có từ khóa → lấy phần tử KẾ CUỐI (trước tỉnh)
                    else if (addressParts.length > 1) {
                        var secondLastIndex = addressParts.length - 2;
                        districtParts = [{
                            text: addressParts[secondLastIndex],
                            index: secondLastIndex,
                            type: 'unknown'
                        }];
                        console.log('🎯 Không có từ khóa → Lấy phần tử kế cuối làm quận/huyện:', districtParts);
                    }

                    // Tìm từ cuối lên (trước tỉnh)
                    districtParts.sort((a, b) => b.index - a.index);
                    
                    for (var i = 0; i < districtParts.length; i++) {
                        var part = districtParts[i].text;
                        var found = false;
                        
                        $.each(districtResponse.data, function(index, district) {
                            if (district.PROVINCE_ID == foundProvince.PROVINCE_ID) {
                                if (isMatch(district.DISTRICT_NAME, part)) {
                                    foundDistrict = district;
                                    found = true;
                                    return false; // Break loop
                                }
                            }
                        });
                        
                        if (found) break;
                    }

                    if (!foundDistrict) {
                        return;
                    }

                    // Chọn quận/huyện
                    $('#distric-filter-GHN').val(foundDistrict.DISTRICT_ID).trigger('change');

                    // Bước 3: Load danh sách phường/xã và tìm kiếm
                    setTimeout(function() {
                        $.getJSON('{{ asset("public/json/viettel_wards.json") }}', function(wardResponse) {
                            if (!wardResponse.data) {
                                return;
                            }

                            // Tìm phường/xã
                            var wardParts = [];
                            
                            // Ưu tiên 1: Có từ khóa "phường", "xã", "thị trấn"
                            if (categorizedParts.ward.length > 0) {
                                wardParts = categorizedParts.ward;
                                console.log('🎯 Tìm phường/xã theo từ khóa:', wardParts);
                            }
                            // Ưu tiên 2: Không có từ khóa → lấy phần tử THỨ 3 TỪ CUỐI (trước huyện)
                            else if (addressParts.length > 2) {
                                var thirdLastIndex = addressParts.length - 3;
                                wardParts = [{
                                    text: addressParts[thirdLastIndex],
                                    index: thirdLastIndex,
                                    type: 'unknown'
                                }];
                                console.log('🎯 Không có từ khóa → Lấy phần tử thứ 3 từ cuối làm phường/xã:', wardParts);
                            }

                            // Tìm từ đầu lên (phường/xã thường ở đầu/giữa)
                            wardParts.sort((a, b) => a.index - b.index);
                            
                            for (var i = 0; i < wardParts.length; i++) {
                                var part = wardParts[i].text;
                                var partIndex = wardParts[i].index;
                                var found = false;
                                
                                $.each(wardResponse.data, function(index, ward) {
                                    if (ward.DISTRICT_ID == foundDistrict.DISTRICT_ID) {
                                        if (isMatch(ward.WARDS_NAME, part)) {
                                            foundWard = ward;
                                            found = true;
                                            return false; // Break loop
                                        }
                                    }
                                });
                                
                                if (found) {
                                    // Tìm ấp/thôn từ categorized parts hoặc từ phần trước ward
                                    if (categorizedParts.hamlet.length > 0) {
                                        // Có từ khóa ấp/thôn/xóm
                                        foundHamlet = categorizedParts.hamlet.map(h => h.text).join(', ');
                                        console.log('✓ Tìm thấy ấp/thôn (từ từ khóa):', foundHamlet);
                                    } else if (partIndex > 0) {
                                        // Không có từ khóa → lấy TẤT CẢ phần tử TRƯỚC phường/xã
                                        foundHamlet = addressParts.slice(0, partIndex).join(', ');
                                        console.log('✓ Tìm thấy ấp/thôn (từ vị trí trước phường):', foundHamlet);
                                    } else if (categorizedParts.ward.length === 0 && addressParts.length > 3) {
                                        // Không có từ khóa ward → lấy phần tử ĐẦU TIÊN làm ấp/thôn
                                        foundHamlet = addressParts[0];
                                        console.log('✓ Tìm thấy ấp/thôn (từ phần tử đầu):', foundHamlet);
                                    }
                                    break;
                                }
                            }

                            if (foundWard) {
                                $('#ward-filter-GHN').val(foundWard.WARDS_ID).trigger('change');
                                
                                // Nếu có ấp/thôn, tự động điền vào select hamlet
                                if (foundHamlet) {
                                    setTimeout(function() {
                                        // Thêm option mới vào select hamlet
                                        var newOption = new Option(foundHamlet, foundHamlet, true, true);
                                        $('#hamlet-filter').append(newOption).trigger('change');
                                    }, 300);
                                }
                            }
                        });
                    }, 500);
                });
            }, 500);
        });
    }

    // Tự động phân tích địa chỉ khi load trang
    setTimeout(function() {
        var testAddress = $('#addressDetail').val();
        if (testAddress && testAddress.length > 5) {
            console.log('🚀 Bắt đầu tự động phân tích địa chỉ:', testAddress);
        }
        autoFillAddressFromInput();
    }, 1000);

    // Có thể phân tích lại khi người dùng thay đổi địa chỉ
    var addressTimeout;
    $('#addressDetail').on('input', function() {
        clearTimeout(addressTimeout);
        addressTimeout = setTimeout(function() {
            autoFillAddressFromInput();
        }, 1500);
    });

    $("#noti-box").slideDown('fast').delay(10000).hide(0);
    
    if ($(window ).width() < 600) {
        $('.tool-bar button').text('Tìm');
    }
    
    if ($('.flex.items-start').length) {
        setTimeout(function() { 
            $('.notify.fixed').hide();
        }, 10000);
    }

  });
</script>

<script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
{{-- here --}}

<script src="{{asset('public/js/number-format/cleave.min.js')}}"></script>
<script>
document.querySelectorAll('.price_class').forEach(inp => new Cleave(inp, {
  numeral: true,
  numeralThousandsGroupStyle: 'thousand'
}));

</script>


@stop