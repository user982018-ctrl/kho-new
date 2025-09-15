<?php
$listStatus = Helper::getListStatus();
$isLeadSale = Helper::isLeadSale(Auth::user()->role);
$checkAll = isFullAccess(Auth::user()->role);
$flagAccess = false;
?>
@extends('layouts.default')
@section('content')
<link href="{{ asset('public/css/pages/notify.css'); }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
{{-- <link href="{{ asset('public/css/pages/styleOrders.css')}}" rel="stylesheet"> --}}
<style>
  #list-product-GHN input {
    padding: 10px;
  }
  input:focus {
    background-color: lightblue;
    border: none !important;
  }
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

  #laravel-notify .notify {
      z-index: 9999;
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
    background: #1ca54a;
    color:#fff;
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
  }
  .background span{
    font-size: 25px;
    font-weight: bold;
    font-style: italic;
  }
  .label-wrap{
    display:flex;
  }

  .hasShipping{
    margin: 20px auto;
    width: 33.33%;
  }

  input[type=radio], input[type=checkbox] {
    width: 15px; 
    opacity: unset;
    position: unset;
}

</style>
@include('notify::components.notify')
  <div class="background">
    <img style="width: auto; height: 100%;" src="{{asset('public/images/ghtk.png')}}" class="card-img-top">
    {{-- <span>GHTK</span> --}}
  </div>
  <div>
    <div class="hasShipping">
      
      <form action="{{route('create-shipping-has-ghtk')}}" method="post">
        {{ csrf_field() }}
        <div class='label-wrap form-group'>
          <label for="min">Mã vận đơn:</label><br>
          {{-- <input type="number" id="min" name="min" class="form-control" value="1"> --}}
          <input autofocus required type="text" name="id_shipping_has" class="form-control" placeholder="Nhập mã vận đơn...">
        </div>
        <input type="hidden" name="vendor_ship">
        <input type="hidden" name="order_id" value="{{$order->id}}">
       
        <button type="submit" class="mt-2 btn btn-primary" style="border:none; background: #1ca54a;">Áp dụng</button>
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
                    ?>

                  <tr class="number dh-san-pham product-{{$product->id}}">
                      <td class="text-left">
                          <span class="no-combo">{{$product->name}}</span><br>
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

    <form class="col-sm-12 col-lg-7" id="form-create-order-ghn"  action="{{route('create-order-GHN')}}" method="POST">
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
                <input required value="{{$order->name}}" class="form-control" 
                    name="name" id="nameFor" type="text">
            </div>
            <div class="col-12  form-group">
              <label class="form-label" for="addressFor">Địa chỉ chi tiết</label>
              <input required value="{{$order->address}}" class="form-control"
                  name="address" id="addressFor" type="text">
            </div>
            <div class="col-sm-6 col-md-6 form-group address-GHN">
              <label class="form-label" for="distric-filter-GHN"><b>Quận - Huyện GHTK</b><span class="required-input">(*)</span></label>
              <select name="district" id="distric-filter-GHN" class="form-control" required>       
                  <option value="">--Đang tải danh sách quận/huyện--</option>
                  @if (isset($listDistricGhn))
                  @foreach ($listDistricGhn as $item)

                  <option 
                  <?php if(strpos($item->DistrictName, $nameProvinceSystem) !== FALSE) {  
                    echo "selected";
                  } ?>
                    value="{{$item->DistrictID}}">{{$item->DistrictName}}</option>
                  @endforeach
                  @endif
              </select>
            </div>
            <div class="col-sm-6 col-md-6 form-group address-GHN">
                <label class="form-label" for="ward-filter-GHN"><b>Phường - xã GHTK</b><span class="required-input">(*)</span></label>
                <select name="ward" id="ward-filter-GHN" class="form-control" required>
                    @if (isset($listWardGHN))
                    @foreach ($listWardGHN as $ward)
                    <option 
                    <?php if(strpos($ward->WardName, $nameWardSystem) !== FALSE) {  
                      echo "selected";
                    } ?>
                    value="{{$ward->WardName}}">{{$ward->WardName}}</option>
                    @endforeach
                    
                    @else
                    <option value="-1">--Chọn phường/ xã--</option>
                    @endif
                </select>
            </div>

            <div class="col-12 form-group">
              <button type="button" id="mergeProducts" style="margin-top: 15px;">🔄 Gộp sản phẩm</button>
              <button type="button" id="undoMerge" style="margin-top: 15px; display: none;">↩️ Tách lại sản phẩm</button>
              <table class="table table-bordered table-line" style="margin-bottom:15px; font-size: 13px; ">
                  <thead>
                      <tr>
                        <th class="text-center" style="white-space: nowrap;">
                          <label style="cursor: pointer;">
                            <input type="checkbox" id="checkAllProducts">
                          </label>
                        </th>
                        <th colspan="7" class="text-center no-wrap col-spname" style="min-width: 155px; width:50%;">Tên sản phẩm</th>
                        <th colspan="1" class="text-center no-wrap" style="width:30%;">Khối lượng (gam)</th>
                        <th colspan="1" class="text-center no-wrap" style="width:10%;">SL Tổng</th>
                        <th colspan="1" class="text-center no-wrap" style="width:10%;"><button id="addProductGHN" type="button"> +Thêm </button></th>
                      </tr>
                  </thead>
                  <tbody class="list-product-choose" id="list-product-GHN">
                  <?php $sumQty = $totalTmp = $i = $totalWeight = 0; 
                  foreach (json_decode($order->id_product) as $item) {
                    $product = getProductByIdHelper($item->id);
                    
                    if ($product) {
                      $sumQty += $item->val;
                      $totalTmp += $item->val * $product->price;
                      $totalWeight += $product->weight;
                    ?>

                    @if ($item->val > 1 && $product->weight > 10000)
                    <input name="bigCart[]" type="hidden" value="{{$item->val}} {{$product->name}}">
                      <?php 
                      for ($i; $i < $item->val; $i++) {
                      ?>
                      <tr class="number dh-san-pham product-{{$product->id}}">
                        <td><input type="checkbox" class="checkbox-merge form-check-input"></td>                 
                        <td colspan="6" class="text-left"> <input class="form-control" required name="products[{{$i}}][name]" type="text" style="width: 100%;" value="{{$product->name}}"><br>
                        </td>
                        <td><input required class="text-right price_class" required name="products[{{$i}}][weight]" type="text" style="width: 100%; padding: 10px;" value="<?php if ($product->weight > 0) { echo number_format($product->weight);} ?>"></td>
                        <td class="no-wrap" style="width: 45px">
                          <input class="text-center" required name="products[{{$i}}][qty]" type="text" style="width: 100%;" value="1">
                        </td>
                        <td><button class="deleteProductGHN"><i class="fa fa-trash"></i></button></td>
                      </tr>
                      <?php
                      }
                      ?>
                    @else
                    <tr class="number dh-san-pham product-{{$product->id}}">
                      <td><input type="checkbox" class="checkbox-merge form-check-input"></td>   
                      <td colspan="7" class="text-left"> <input required name="products[{{$i}}][name]" type="text" style="width: 100%;" value="{{$product->name}}"><br>
                      </td>
                      <td colspan="1"><input required class="text-right price_class" name="products[{{$i}}][weight]" type="text" style="width: 100%;  padding: 10px;" value="<?php if ($product->weight > 0) { echo number_format($product->weight);} ?>"></td>
                      <td class="no-wrap" style="width: 45px">
                        <input class="text-center" required name="products[{{$i}}][qty]" type="text" style="width: 100%;" value="{{$item->val}}">
                      </td>
                      <td><button type="button" class="deleteProductGHN"><i class="fa fa-trash"></i></button></td>
                    </tr>
                    @endif

                    <?php   
                    $i++;   
                    }    
                  }
                  ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td></td>
                      <td class="no-wrap text-right" colspan="8">Tổng đơn:
                      </td>
                      <td class="no-wrap" colspan="2"><input class="price_class" name="cod_amount" type="text" value="{{number_format($order->total)}}"> </td>

                    </tr>
                  </tfoot>
              </table>
              <input type="hidden" id="next-qty-index" value="{{$i}}">
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12" style=" text-align: end;">
              <button id="submit" style="background:#1ca54a; border: none;" class="mb-1 btn btn-primary create-bill">Tạo vận đơn</button>
          </div>
        </div>
      </div>
    </form>
    </div>
    
  </div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        // $('#distric-filter-GHN').select2();
        // $('#ward-filter-GHN').select2();
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
                    
      str = `<tr><td><input type="checkbox" class="checkbox-merge form-check-input"></td>`
        +`<td colspan="7" class="text-left"><input required name="products[` + nextIndex + `][name]" type="text" style="width: 100%;  padding: 10px;"><br></td>`
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

    $('#distric-filter-GHN').on('change', function() {
        var id = this.value;
        var _token  = $("input[name='_token']").val();
        console.log(id)
        $.ajax({
          url: "{{ route('get-ward-by-id-distric-GHN') }}",
          type: 'GET',
          data: {
              _token: _token,
              id
          },
          success: function(data) {
              if (data.length > 0) {
                  let str = '';
                  $.each(data, function(index, value) {
                      console.log(value);
                      str += `<option value="` +value.WardCode+ `">` + value.WardName + `</option>`;
                      
                  });

                  $('#ward-filter-GHN').html(str);
                  $('#ward-filter-GHN').select2();
              }
          }
        });
      });

    $("#noti-box").slideDown('fast').delay(5000).hide(0);
    
    if ($(window ).width() < 600) {
        $('.tool-bar button').text('Tìm');
    }
    
    if ($('.flex.items-start').length) {
        setTimeout(function() { 
            $('.notify.fixed').hide();
        }, 3000);
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

<script>
  var _token = $("input[name='_token']").val();
  var id = $("input[name='order_id']").val();
  $('.address-GHN').css("opacity", "0.5");
  $.ajax({
    url: "{{ route('api-district-by-name-to-GHN') }}",
    type: 'GET',
    data: {
        _token: _token,
        id
    },
    success: function(data) {
            
      if (data.listDistricGhn) {
        let str = '';
        let selected = '';
        
        $.each(data.listDistricGhn, function(index, value) {
            if (value.DistrictID == data.idDistrictToGetWardsGHN) {
              selected = 'selected';
            }
            if( value.DistrictName.search(data.nameWardSystem) > 0) {
              selected = 'selected';
          }
            str += `<option ` + selected + ` value="` +value.DistrictID+ `">` + value.DistrictName + `</option>`;
            selected = '';
          });

        $('#distric-filter-GHN').html(str);
        $('#distric-filter-GHN').select2();
        $('.address-GHN').css("opacity", "1");
      }

      if (data.listWardGHN) {
        let strWar= '';
        let selectedWar = '';
        $.each(data.listWardGHN, function(index, value) {
          if( value.WardName.search(data.nameWardSystem) > 0) {
            selectedWar = 'selected';
          }
          strWar += `<option ` + selectedWar + ` value="` + value.WardCode+ `">` + value.WardName + `</option>`;
          selectedWar = '';
        });
        $('#ward-filter-GHN').html(strWar);
        $('#ward-filter-GHN').select2();
      }
    }
  });

  $('#form-create-order-ghn').on('submit', function(e) {
    if ($('#distric-filter-GHN').val() === '') {
      alert('Vui lòng đợi quận huyện!');
      e.preventDefault();
      console.log($('#distric-filter-GHN').val());
      return false;
    }
  });
</script>

{{-- gộp sản phẩm --}}
<script>
   let originalRowsHTML = '';

document.getElementById('mergeProducts').addEventListener('click', function () {
  if (!confirm("Bạn có chắc chắn muốn gộp các sản phẩm đã chọn không?")) return;

  const list = document.getElementById('list-product-GHN');
  const rows = list.querySelectorAll('tr');
  let selectedRows = [];

  originalRowsHTML = list.innerHTML;

  rows.forEach(row => {
    const checkbox = row.querySelector('.checkbox-merge');
    if (checkbox && checkbox.checked) selectedRows.push(row);
  });

  if (selectedRows.length === 0) {
    alert("Vui lòng chọn ít nhất 1 sản phẩm để gộp.");
    return;
  }

  let mergedName = '';
  let totalWeight = 0;
  let totalQty = 0;

  selectedRows.forEach((row, index) => {
    const name = row.querySelector('input[name*="[name]"]').value;
    const weight = parseFloat(row.querySelector('input[name*="[weight]"]').value.replace(/,/g, '')) || 0;
    const qty = parseInt(row.querySelector('input[name*="[qty]"]').value) || 0;

    mergedName += `{${qty} × ${name}}` + (index < selectedRows.length - 1 ? ' + ' : '');
    totalWeight += weight * qty;
    totalQty = 1;

    row.remove(); // xóa dòng
  });

  const newRow = document.createElement('tr');
  newRow.innerHTML = `
    <td><input type="checkbox" class="checkbox-merge" checked></td>
    <td colspan="7" class="text-left">
      <input required name="products[0][name]" type="text" style="width: 100%;  padding: 10px;" value="${mergedName}">
    </td>
    <td><input required class="text-right price_class" name="products[0][weight]" type="text" style="width: 100%;" value="${totalWeight}"></td>
    <td><input class="text-center" required name="products[0][qty]" type="text" style="width: 100%;" value="${totalQty}"></td>
    <td><button onClick="deleteRowProductGHN(this)" type="button"><i class="fa fa-trash"></i></button></td>
  `;

  list.appendChild(newRow);
  document.getElementById('mergeProducts').style.display = 'none';
  document.getElementById('undoMerge').style.display = 'inline-block';
});

document.getElementById('undoMerge').addEventListener('click', function () {
  document.getElementById('list-product-GHN').innerHTML = originalRowsHTML;
  document.getElementById('mergeProducts').style.display = 'inline-block';
  document.getElementById('undoMerge').style.display = 'none';
});

// Checkbox chọn tất cả
document.getElementById('checkAllProducts').addEventListener('change', function () {
  const checkboxes = document.querySelectorAll('.checkbox-merge');
  checkboxes.forEach(cb => cb.checked = this.checked);
});

// Tự bỏ check "chọn tất cả" nếu uncheck từng dòng
document.addEventListener('change', function (e) {
  if (e.target.classList.contains('checkbox-merge')) {
    const all = document.querySelectorAll('.checkbox-merge');
    const checked = document.querySelectorAll('.checkbox-merge:checked');
    document.getElementById('checkAllProducts').checked = (all.length === checked.length);
  }
});
  </script>
  
  

@stop