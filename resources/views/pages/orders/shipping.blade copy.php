@extends('layouts.default')
@section('content')

<style>
    .card-ghn {
        padding-top: 10px !important;
    }
    .card-ghn:hover {
        /* opacity: 0.1;    */
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    }

    .choose-shipping {
        /* height:100px;
        width :100px;
        background:red; */
        display:block;
        opacity:1;
        transition : all .3s;
        -wekit-transition : all .3s;
        -moz-transition : all .3s;
    }
    .choose-shipping.active .card-ghn{
        /* opacity: 0; */
        border: 2px solid rgba(0, 0, 0, 0.35);
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    }
</style>
<link href="{{ asset('public/css/pages/styleOrders.css'); }}" rel="stylesheet">
<div class="body flex-grow-1 px-3">
    <div class="container-lg">
        <div class="row choose-shipping">
            <div id="notifi-box" class="hidden alert alert-success print-error-msg">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            </div>
            <div class="p-0 col-3 card-ghn">
                <div class="card">
                    <img src="{{asset('public/images/ghn.png')}}" class="card-img-top" alt="...">
                
                    <div class="card-body">
                    <h5 class="card-title ">Giao Hàng Nhanh</h5>
                    <button id="nextGHN" class="btn btn-primary">Chọn</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row content-ship hidden">
            <div class="mt-1 col-12 ">
                <div class="card mb-4 ">
                    <div class="tab-content rounded-bottom">
                        <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                            <div class=" col-12">
                                <div class="form-check">
                                    <input value="yes" checked class="form-check-input" type="radio" name="hasShipping" id="hasShippingFor">
                                    <label class="form-check-label" for="hasShippingFor">
                                        Đã tạo đơn giao  vận
                                    </label>
                                    <div class="col col-3 mb-3 mt-2">
                                        <form action="{{route('create-shipping-has')}}" method="post">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="vendor_ship">
                                            <input type="hidden" name="order_id" value="{{$orderId}}">
                                            <input autofocus required type="text" name="id_shipping_has" class="form-control" placeholder="Nhập mã vận đơn..." aria-label="Username" aria-describedby="basic-addon1">
                                            <button type="submit" class="mt-2 btn btn-primary">Áp dụng</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input value="no" class="form-check-input" type="radio" name="hasShipping" id="noShippingFor">
                                    <label class="form-check-label" for="noShippingFor">
                                        Chưa có
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function wardClick(name, id) {
    $("#wardFor").val(name);
    $("#listWard").removeClass('show');
    $("#listWard").addClass('hidden');
    $("#wardFor").attr('data-ward-id', id);
}


function districtClick(name, id) {
    $("#districtFor").val(name);
    $("#listDistrict").removeClass('show');
    $("#listDistrict").addClass('hidden');
    $("#districtFor").attr('data-district-id', id);

    var _token = $("input[name='_token']").val();
    $.ajax({
        url: "{{ route('get-ward-by-id') }}",
        type: 'GET',
        data: {
            _token: _token,
            id
        },
        success: function(data) {
            if (data.length > 0) {
                console.log(data);
                let str = '';

                $.each(data, function(index, value) {
                    str += '<a onclick="wardClick(\'' + value.WardName + '\', ' + '\'' + value.WardCode +
                        '\')" class="option-ward" data-ward-name="' + value.WardCode +
                        '" data-ward-id="' + value.WardCode + '">' + value.WardName +
                        '</a>';
                });

                $('#listWard').html(str);
            }
        }
    });
}

function myFunctionDistrict() {
    document.getElementById("listDistrict").classList.toggle("show");

}

function myFunctionWard() {
    document.getElementById("listWard").classList.toggle("show");

}

function myFunctionProvince() {
    document.getElementById("listProvince").classList.toggle("show");

}

function filterFunctionDistrict() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("districtFor");
    filter = input.value.toUpperCase();
    div = document.getElementById("listDistrict");
    a = div.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
        } else {
            a[i].style.display = "none";
        }
    }
}

function filterFunctionWard() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("wardFor");
    filter = input.value.toUpperCase();
    div = document.getElementById("listWard");
    a = div.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
        } else {
            a[i].style.display = "none";
        }
    }
}

function filterFunctionProvince() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("provinceFor");
    filter = input.value.toUpperCase();
    div = document.getElementById("listProvince");
    a = div.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
        } else {
            a[i].style.display = "none";
        }
    }
}

$(".option-product-province").click(function() {
    let id = $(this).data("province-id");
    let name = $(this).data("province-name");
    $("#provinceFor").val(name);
    $("#provinceFor").attr('data-province-id', id);

    $("#listProvince").removeClass('show');
    $("#listProvince").addClass('hidden');

    var _token = $("input[name='_token']").val();

    $("#wardFor").removeAttr('data-ward-id');
    $("#wardFor").val('');
    $("#districtFor").removeAttr('data-district-id');
    $("#districtFor").val('');
    $.ajax({
        url: "{{ route('get-district-by-id') }}",
        type: 'GET',
        data: {
            _token: _token,
            id
        },
        success: function(data) {
            if (data.length > 0) {
                // console.log(data);
                let str = '';

                $.each(data, function(index, value) {
                    str += '<a onclick="districtClick(\'' + value.DistrictName + '\', ' +
                        value.DistrictID + ')" class="option-ward" data-ward-name="' + value
                        .DistrictName +
                        '" data-ward-id="' + value.DistrictID + '">' + value.DistrictName +
                        '</a>';
                });

                $('#listDistrict').html(str);
            }
        }
    });

});
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");

}


function filterFunction() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    div = document.getElementById("myDropdown");
    a = div.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
        } else {
            a[i].style.display = "none";
        }
    }
}

$(document).ready(function() {


    $(".option-product").click(function() {
        let id = $(this).data("product-id");
        let name = $(this).data("product-name");
        let price = $(this).data("product-price");

        $("input[name='products[]']").val(id);

        $("#myDropdown").removeClass('show');
        $("#myDropdown").addClass('hidden');


        let priceOld = +$("input[name='price']").attr("data-product-price");
       
        newPrice = priceOld + price;
        console.log('newPrice', newPrice);

        newPriceFormat = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
            .format(newPrice,);
        $("input[name='price']").val(newPriceFormat);
        $("input[name='price']").attr('data-product-price', newPrice);
    
    
        if ($('#product-' + id).length > 0) {
            var $input = $('#product-' + id).find('input');
            $input.val(parseInt($input.val()) + 1);
            $input.change();


        } else {
            let str = '<div id="product-' + id + '" class="text-right col-4 number product-' + id +
                '"><button onclick="minus(' + id +
                ', ' + price +
                ')" type="button" class=" minus">-</button><input class="qty-input" data-product_id="' +
                id + '" disabled type="text" value="1"/><button onclick="plus(' + id +
                ', ' + price +
                ')" type="button" class="plus">+</button></div>';
            str += '<button onclick="deleteProduct(' + id +
                ', ' + price +
                ')" type="button" class="col-2 del" >X</button>';
            $("#list-product-choose").append('<div class="row product mb-0">' +
                '<div class="col-6 name">' + name +
                '</div>' + str + '</div>');
        }

        var $inputQty = $('#sum-qty').find('input');
        $inputQty.val(parseInt($inputQty.val()) + 1);
        $inputQty.change();
    });

    $("#priceSaleFor").click(function() {
        if ($(this).is(':checked') ) {
            $("input[name='id-shpping-has']").show();
            $("input[name='id-shpping-has']").focus();
            // $("input[name='price']").show();
            // let price =  $("input[name='price']").val();
            // console.log(price);
            // $("input[name='promotion']").attr("placeholder", price).blur();
        } else {
            // $("input[name='promotion']").hide();
            $("input[name='price']").prop("disabled", true);
            let price           = $("input[name='price']").attr("data-product-price");
            console.log(price);
            let newPriceFormat  = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
                .format(price,);
            $("input[name='price']").val(newPriceFormat);
        }
    });

    $("input[name='hasShipping']").click(function() {
        if ($(this).val() == 'yes') {
            $("input[name='id_shipping_has']").show();
            $("input[name='id_shipping_has']").focus();
        } else {
            $("input[name='id_shipping_has']").hide();
        }
    });
    
    $("#nextGHN").click(function() {
        console.log('click next ghn');
        $('.choose-shipping').toggleClass('active');
        // $('.choose-shipping').hide(0).delay(5000);
         $('.content-ship').show();
         $('input[name="vendor_ship"]').val('GHN');
        
    });

});
</script>
@stop