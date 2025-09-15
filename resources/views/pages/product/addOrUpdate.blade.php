@extends('layouts.default')
@section('content')

<div class="body flex-grow-1 px-3">
    <div class="container-lg">
        <div class="row">
            <div id="notifi-box" class="hidden alert alert-success print-error-msg">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            </div>

            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header"><strong>Cập nhật sản phẩm mới </span></div>
                    @if(isset($product))
                    <div class="card-body">
                        <div class="example">
                            <div class="body flex-grow-1">
                                <div class="tab-content rounded-bottom">
                                    <form>
                                        {{ csrf_field() }}
                                        <input value="{{$product->id}}" name="id" type="hidden">
                                        <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                                            <div class="row">
                                                <div class="mb-3 col-4">
                                                    <label class="form-label" for="nameIP">Tên sản phẩm</label>
                                                    <input class="form-control" value="{{$product->name}}" name="name"
                                                        id="nameIP" type="text">
                                                    <p class="error_msg" id="name"></p>
                                                </div>
                                                <div class="mb-3 col-4">
                                                    <label class="form-label" for="nameTaxIP">Tên thuế</label>
                                                    <input class="form-control" value="{{$product->tax_name}}" name="nameTax"
                                                        id="nameTaxIP" type="text">
                                                    <p class="error_msg" id="nameTax"></p>
                                                </div>
                                                <div class="mb-3 col-4">
                                                    <label class="form-label" for="priceIP">Giá</label>
                                                    <input class="form-control" value="{{$product->price}}" name="price"
                                                        id="priceIP">
                                                    <p class="error_msg" id="price"></p>
                                                </div>
                                                <div class="mb-3 col-4">
                                                    <label class="form-label" for="weightIP">Khối lượng (gam)</label>
                                                    <input class="form-control" value="{{$product->weight}}" name="weight"
                                                        id="weightIP">
                                                    <p class="error_msg" id="weight"></p>
                                                </div>
                                                <div class="mb-3 col-4">
                                                    <label class="form-label" for="unitIP">Đơn vị tính</label>
                                                    <input class="form-control" value="{{$product->unit}}" name="unit"
                                                        id="unitIP">
                                                    <p class="error_msg" id="unit"></p>
                                                </div>
                                                <div class="mb-3 col-4">
                                                    <label class="form-label" for="orderIP">Thứ tự</label>
                                                    <input class="form-control" value="{{$product->orderBy}}" name="orderBy"
                                                        id="orderIP">
                                                    <p class="error_msg" id="orderBy"></p>
                                                </div>
                                            </div>
                                            @if(isset($listCategory))
                                            <div class="mb-3 col-4">
                                                <label class="form-label" for="category_id">Danh mục</label>
                                                <select name="category_id" id="category_id" class="form-control">

                                                    @foreach ($listCategory as $value)
                                                    <option value="{{$value->id}}"
                                                        <?= ($value->id == $product->category_id) ? 'selected' : ''; ?>>
                                                        {{$value->name}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif

                                            <div class="mb-3 col-2">
                                                <label class="form-label" for="qtyIP">Số lượng</label>
                                                <input class="form-control" value="{{$product->qty}}" name="qty"
                                                    id="qtyIP" type="bumber">
                                                <p class="error_msg" id="qty"></p>
                                            </div>
                                        <div class="row">
                                            <div class="mb-3 col-2">
                                                <label class="form-label" for="qtyIP">Trạng Thái</label>
                                                <div class="form-check">
                                                    <input <?=  $product->status == 1 ? 'checked' : '' ?> class="form-check-input" type="radio" name="status" value="1"
                                                        id="flexRadioDefault1">
                                                    <label class="form-check-label" for="flexRadioDefault1">
                                                        Bật
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input <?=  $product->status == 0 ? 'checked' : '' ?> class="form-check-input" type="radio" name="status" value="0"
                                                        id="flexRadioDefault2" >
                                                    <label  class="form-check-label" for="flexRadioDefault2">
                                                        Tắt
                                                    </label>
                                                </div>
                                            </div>
                                            
                                                <div class="mb-3 col-4">
                                                    <label class="form-label" for="qtyIP">Quyền truy cập</label>
                                                    <div class="form-check">
                                                        <input <?=  $product->roles == 1 ? 'checked' : '' ?>  id="role-all" name="role" type="radio" class="form-check-input" value="1">
                                                        <label class="form-check-label" for="role-all">Tất cả</label>
                                                    </div>
                                                    <div class="form-check">
                                                        
                                                        <input <?=  $product->roles == 2 ? 'checked' : '' ?> id="role-paulo" name="role" type="radio" class="form-check-input" value="2">
                                                        <label class="form-check-label" for="role-paulo">Paulo</label>
                                                    </div>
                                                    <div class="form-check">
                                                        
                                                        <input <?=  $product->roles == 3 ? 'checked' : '' ?> id="role-fer" name="role" type="radio" class="form-check-input" value="3">
                                                        <label class="form-check-label" for="role-fer">Phân bón</label>
                                                    </div>
                                                    <div class="form-check">
                                                        
                                                        <input <?=  $product->roles == 4 ? 'checked' : '' ?> id="other" name="role" type="radio" class="form-check-input" value="4">
                                                        <label class="form-check-label" for="other">Khác</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <button id="submit" class="btn btn-primary">Cập nhật</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card-body">
                <div class="example">
                    <div class="body flex-grow-1">
                        <div class="tab-content rounded-bottom">
                            <form>
                                {{ csrf_field() }}
                                <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                                    <div class="row">
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="nameIP">Tên sản phẩm</label>
                                            <input class="form-control" name="name" id="nameIP" type="text">
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="nameTaxIP">Tên thuế</label>
                                            <input class="form-control" name="nameTax"
                                                id="nameTaxIP" type="text">
                                            <p class="error_msg" id="nameTax"></p>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="priceIP">Giá</label>
                                            <input class="form-control" name="price" id="priceIP" type="bumber">
                                            <p class="error_msg" id="price"></p>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="weightIP">Khối lượng (gam)</label>
                                            <input class="form-control" name="weight"
                                                id="weightIP" >
                                            <p class="error_msg" id="weight"></p>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="unitIP">Đơn vị tính</label>
                                            <input class="form-control" name="unit"
                                                id="unitIP" >
                                            <p class="error_msg" id="unit"></p>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="orderIP">Thứ tự</label>
                                            <input class="form-control" name="orderBy"
                                                id="orderIP">
                                            <p class="error_msg" id="orderBy"></p>
                                        </div>
                                    </div>
                                    @if(isset($listCategory))
                                    <div class="mb-3 col-4">
                                        <label class="form-label" for="category_id">Danh mục</label>
                                        <select name="category_id" id="category_id" class="form-control">

                                            @foreach ($listCategory as $value)
                                            <option value="{{$value->id}}">
                                                {{$value->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    <div class="mb-3 col-2">
                                        <label class="form-label" for="qtyIP">Số lượng</label>
                                        <input class="form-control" name="qty" id="qtyIP" type="bumber">
                                        <p class="error_msg" id="qty"></p>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="qtyIP">Quyền truy cập</label>
                    
                                        
                                            <div class="form-check">
                                                <label class="form-check-label">
                    <?php 
                    $checkAll = $checkPaulo = $checkFertilizer = $checkOther = '';
                    $roles = json_decode($user->role, true);
                        // dd($user->role);
                    if ( is_array($roles)) {
                        foreach ($roles as $key => $value) {
                            if ($value == 1) {
                                $checkAll = $checkPaulo = $checkFertilizer = $checkOther = 'checked';
                                break;
                            } else if ($value == 2) {
                                $checkPaulo = 'checked';
                                break;
                            } else if ($value == 3) {
                                $checkFertilizer = 'checked';
                                break;
                            } else if ($value == 4) {
                                $checkOther = 'checked';
                                break;
                            }
                        }   
                    }
                    
                    
                    ?>
                                                <input {{$checkAll}} id="role-all" name="roles[]" type="checkbox" class="form-check-input" value="1">Tất cả
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                <input {{$checkPaulo}} name="roles[]" type="checkbox" class="form-check-input" value="2">Paulo
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                <input {{$checkFertilizer}} name="roles[]" type="checkbox" class="form-check-input" value="3">Phân bón
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                <input {{$checkOther}} name="roles[]" type="checkbox" class="form-check-input" value="4">Khác
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <button id="submit" class="btn btn-primary">Tạo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>
</div>
<script>
$(document).ready(function() {
    $("input[name='roles[]']").click(function () {
        // console.log($(this).val());
        // $("input[name='roles[]']").val();
        var values = [];
        
        if ($(this).val() == 1) {
            if ($(this).is(':checked') ) {
                $("input[name='roles[]']").prop('checked', true);
            } else {
                $("input[name='roles[]']").prop('checked', false);
            }
            
        } else {
            if (!$(this).is(':checked') ) {
                console.log('unchecked');
                $("#role-all").prop('checked', false);
            } else {
                let values = [];
                $("input[name='roles[]']:checked").each(function() {
                    values.push($(this).val());
                });
                console.log(values);
                
                if (values.length == 3) {
                    $("#role-all").prop('checked', true);
                }
            }
        }
    });

    $("#submit").click(function(e) {
        e.preventDefault();

        var _token = $("input[name='_token']").val();
        var name = $("input[name='name']").val();
        var price = $("input[name='price']").val();
        var qty = $("input[name='qty']").val();
        var id = $("input[name='id']").val();
        var category_id = $("select[name='category_id']").val();
        var status = $("input[name='status']:checked").val();
        var weight = $("input[name='weight']").val();
        var unit = $("input[name='unit']").val();
        var orderBy = $("input[name='orderBy']").val();
        let roles =  $("input[name='role']:checked").val();
        var nameTax = $("input[name='nameTax']").val();
        // $("input[name='roles[]']:checked").each(function() {
        //     roles.push($(this).val());
        // });
        console.log(roles);
        $.ajax({
            url: "{{ route('save-product') }}",
            type: 'POST',
            data: {
                _token: _token,
                name: name,
                nameTax,
                price: price,
                qty: qty,
                id,
                status,
                category_id,
                roles,
                weight,
                unit,
                orderBy,
            },
            success: function(data) {
                console.log(data);
                if ($.isEmptyObject(data.errors)) {
                    $(".error_msg").html('');
                    $("#notifi-box").show();
                    $("#notifi-box").html(data.success);
                    $("#notifi-box").slideDown('fast').delay(5000).hide(0);
                } else {
                    let resp = data.errors;
                    for (index in resp) {
                        console.log(index);
                        console.log(resp[index]);
                        $("#" + index).html(resp[index]);
                    }
                }
            }
        });
    });
});
</script>
@stop