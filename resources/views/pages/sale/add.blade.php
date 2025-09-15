<!DOCTYPE html>
<html lang="en">
<head>
    <link href="{{ asset('public/css/pages/notify.css'); }}" rel="stylesheet">
    @include('includes.head')
    <link href="{{ asset('public/css/pages/styleOrders.css'); }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .call label {
        display: flex;
        justify-content: space-between;
    }
    #laravel-notify .notify {
        z-index: 2;
    }

</style>

</head>
<?php 
// use Session;
$isLeadSale = Helper::isLeadSale(Auth::user()->role);      

$name = Session::get('name');
$phone = Session::get('phone');
$address = Session::get('address');
$messages = Session::get('messages');
?>
<body>
    @include('notify::components.notify')
    <div class="body flex-grow-1 px-3 mt-2">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                @if(isset($saleCare))
                    {{-- <div class="card-header"><span><strong>Cập nhật CSKH</strong></span></div> --}}
                    <div class="card-body card-orders">
                        <div class="body flex-grow-1">
                            <div class="tab-content rounded-bottom">
                                <form method="post" action="{{route('sale-care-save')}}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{$saleCare->id}}">
                                    <div class="row" id="content-add">
                                        <div class="col-sm-12 col-lg-3">
                                            <label class="form-label" for="phoneFor">Số điện thoại</label>
                                            <input value="{{$saleCare->phone}}" class="form-control" name="phone" id="phoneFor" type="text">
                                            <p class="error_msg" id="phone"></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-3">
                                            <label class="form-label" for="nameFor">Tên khách hàng</label>
                                            <input value="{{$saleCare->full_name}}" class="form-control" name="name" id="nameFor" type="text">
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-3">
                                            <label class="form-label" for="addressFor">Địa chỉ/đường</label>
                                            <input value="{{$saleCare->address}}" class="form-control"
                                                name="address" id="addressFor" type="text">
                                            <p class="error_msg" id="address"></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-4">
                                            <label for="type_tree" class="form-label">Cây trồng:</label>
                                            <textarea name="type_tree" class="form-control" id="type_tree" rows="3">{{$saleCare->type_tree}}</textarea>
                                            <p></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-4">
                                            <label for="product-request" class="form-label">Nhu cầu dòng sản phẩm:</label>
                                            <textarea name="product_request" class="form-control" id="product-request" rows="3">{{$saleCare->product_request}}</textarea>
                                            <p></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-4">
                                            <label for="reason_not_buy" class="form-label">Lý do không mua hàng:</label>
                                            <textarea name="reason_not_buy" class="form-control" id="reason_not_buy" rows="3">{{$saleCare->reason_not_buy}}</textarea>
                                            <p></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-4">
                                            <label for="note_info_customer" class="form-label">Ghi chú thông tin khách hàng:</label>
                                            <textarea name="note_info_customer" class="form-control" id="note_info_customer" rows="3">{{$saleCare->note_info_customer}}</textarea>
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 col-lg-4">
                                            <label for="id_order" class="form-label">Mã đơn:</label>
                                            <input class="form-control" name="id_order"
                                                id="id_order" type="text" value="{{$saleCare->id_order}}">
                                            <p></p>
                                        </div>

                                        <?php $checkAll = isFullAccess(Auth::user()->role);?>
                                        @if ($checkAll)

                                        <div class="col-4">
                                            <label class="form-label" >Chọn Sale</label>
                                            <select class="form-control" name="assign_sale">

                                            @if (isset($listSale))
                                            @foreach ($listSale as $item)
                                                <option <?php echo ($item->id == $saleCare->assign_user) ? 'selected' : '';?> value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                            @endif

                                            </select>
                                            <p class="error_msg" id="price"></p>
                                        </div>

                                        @else 

                                        <div class="col-6 hidden">
                                            <label class="form-label" >Chọn Sale</label>
                                            <select class="form-control" name="assign">
                                                <option value="{{Auth::user()->id}}">{{Auth::user()->name}}</option>
                                            </select>
                                            <p class="error_msg" id="price"></p>
                                        </div>

                                        @endif
                                    </div>
                                    {{-- <div class="loader hidden">
                                        <img src="{{asset('public/images/loader.gif')}}" alt="">
                                    </div> --}}
                                    {{-- <button id="add" type="button" class="btn btn-danger text-white">Thêm lần gọi</button> --}}
                                    <button id="submit" class="btn btn-primary">Cập nhật </button>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
         
                @else
                    <div class="card-body card-orders">
                        <form method="post" action="{{route('sale-care-save')}}" onsubmit="return validatePhoneNumber()">
                            {{ csrf_field() }}
                            <div class="row mb-2" id="content-add">
                                <div class="col-sm-12 col-lg-3">
                                    <label class="form-label" for="phoneFor">Số điện thoại<span class="required-input">(*)</span></label>
                                    <input pattern="^(?:(03[0-9]|05[0-9]|07[0-9]|08[0-9]|09[0-9])\d{7}|02\d{9})$" required placeholder="Nhập số điện thoại" class="form-control" name="phone"
                                        id="phoneFor" type="text" value="{{$phone}}">
                                    <p class="error_msg" id="phone"></p>
                                </div>
                                <div class="col-sm-12 col-lg-3">
                                    <label class="form-label" for="nameFor">Tên khách hàng</label>
                                    <input placeholder="Họ và tên" class="form-control" name="name"
                                        id="nameFor" type="text" value="{{$name}}">
                                    <p class="error_msg" id="name"></p>
                                </div>
                                
                                <div class="col-sm-6 col-lg-4">
                                    <label class="form-label" for="addressFor">Địa chỉ/đường</label>
                                    <input placeholder="Nhập địa chỉ" class="form-control" name="address"
                                        id="addressFor" type="text" value="{{$address}}">
                                    <p class="error_msg" id="address"></p>
                                </div>
                                
                                {{-- <div class="col-sm-12 col-lg-4 call">
                                    <label for="call1" class="form-label ">Gọi lần 1:
                                        <span class="delete">xoá</span>
                                    </label>
                                    <textarea data-id-call=1 name="call[]" class="form-control" id="call1" rows="3"></textarea>
                                    <p></p>
                                </div> --}}
                            </div>
                            <div class="row mb-2">
                               
                                <div class="col-sm-12 col-lg-12">
                                    <label class="form-label" for="messagesFor">Tin nhắn</label><br>
                                    <textarea name="messages" id="messagesFor" cols="100" rows="5" style="width:100%">{{$messages}}</textarea>
                                    <p class="error_msg" id="address"></p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-12 col-lg-3">
                                    <label class="form-label" for="qtyIP">Chia data</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shareDataSale" value="1"
                                            id="flexRadioDefaultCSKH">
                                        <label class="form-check-label" for="flexRadioDefaultCSKH">
                                            Chỉ định sale nhận data
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input checked class="form-check-input" type="radio" name="shareDataSale" value="2"
                                            id="flexRadioDefaultCSKH2" >
                                        <label  class="form-check-label" for="flexRadioDefaultCSKH2">
                                            Hệ thống tự chia data
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 hidden" id="list-sale-cskh-div">
                                    <label class="form-label" for="saleCskhIP">Sale Data Nóng</label>
                                    {{-- <input value="{{$teleCskhData}}" class=" form-control" name="saleCskh" id="saleCskhIP" type="text" required>
                                    <p class="error_msg" id="name"></p> --}}
                                    {{-- <label for="like-color">Sale Data nóng</label> --}}
                                    <div class="" >
                                        <select style="width:50%;" name="assgin" id="assgin-filter" class="custom-select">
                                            @if (isset($listSale))
                                            @foreach ($listSale as $item)
                                                <option value="{{$item->id}}">{{$item->real_name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-xs-12 col-sm-6 col-md-4 form-group">
                                    <label class="form-label">Nguồn Data:</label>
                                    <select style="width:100%;" name="src_id" id="src-filter" class="form-control">       
                                            
                                    @foreach ($listSrc as $page) 
                                        <option value="{{$page['id']}}">{{($page['name']) ? : $page['name']}}</option>
                                    @endforeach 
                    
                                    </select>
                                </div>
                                
                            </div>
                           
                            {{-- <div class="loader hidden text-center">
                                <img src="{{asset('public/images/loader.gif')}}" alt="">
                            </div> --}}
                            {{-- <button id="add" type="button" class="btn btn-danger text-white">Thêm lần gọi</button> --}}
                            <button id="submit" class="btn btn-primary" onclick="validatePhoneNumber()">Tạo</button>
                            
                        </form>
                    </div>
                @endif

                </div>

                <div class="row text-right">
                    <div><button class="refresh btn btn-info">Refresh</button></div>
                </div>
                <div id="loader-overlay">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
<script>

    // A $( document ).ready() block.
$( document ).ready(function() {
    $('.refresh').click(function() {
        location.reload(true)
    });

    if ($('.print-error-msg').length > 0) {
        setTimeout(function() { 
            $('.print-error-msg').hide();
        }, 3000);
    }

    $( "#add" ).on( "click", function() {
        $('.delete').remove();
        length  = $("textarea[name='call[]']").length;
        number  = length + 1;
        str     = '<div class="col-sm-12 col-lg-4 call">'
            + '<label for="call' + number + '" class="form-label">Gọi lần ' + number + ':'
            + '<span class="delete" onclick="deleteCall($(this))">xoá</span>'
            + '</label>'
            + '<textarea data-id-call='+ number + ' + name="call[]" class="form-control" id="call' + number + '" rows="3"></textarea>'
            + '<p></p>'
            + '</div>';
        $("#content-add").append(str);
    });

    $(".delete").on( "click", function() {
        el = $(this).parent().parent();
        parent      = el;
        id          = parent[0].children[1].id;
        numberCall  = $("#" + id).attr("data-id-call");
        numberCall  -= 1;
        callPre     = $('#call' + numberCall);

        if (callPre.length > 0) {
            label   = callPre.parent()[0].children[0];
            str     = '<span class="delete" onclick="deleteCall($(this))">xoá</span>';
            $(label).append(str);
        }
        $(this).parent().parent().remove();
    });

    jQuery.fn.deleteCall = function() {
        $(this).parent().parent().remove();
    }

    $('.print-error-msg').on( "click", function() {
        $(this).hide();
    });

    $('#submit').on( "click", function() {
        $phone = $("input[name='phone']").val();
        $name = $("input[name='name']").val();
        $address = $("input[name='address']").val();
        if ( $phone != '' && validatePhoneNumber()) {
           $('#loader-overlay').css('display', 'flex');
        }
    });

    
});
function deleteCall(val) {
    parent      = val.parent().parent();
    id          = parent[0].children[1].id;
    numberCall  = $("#" + id).attr("data-id-call");
    numberCall  -= 1;
    callPre     = $('#call' + numberCall);

    if (callPre.length > 0) {
        label   = callPre.parent()[0].children[0];
        str     = '<span class="delete" onclick="deleteCall($(this))">xoá</span>';
        $(label).append(str);
    }
   
    val.parent().parent().remove();
}
    
</script>
    @include('includes.foot')
    <script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        $('#src-filter').select2();
        $('#assgin-filter').select2();
    });
</script>

<script>
    $(document).ready(function() {
        $("input[name='shareDataSale']").click(function() {
        if ($(this).val() == 1) {
            $("#list-sale-cskh-div").show();
            $("#list-sale-cskh-div").focus();
        } else {
            $("#list-sale-cskh-div").hide();
        }
        });
    });
</script>

<script>
    function validatePhoneNumber() {
        const phoneInput = document.getElementById('phoneFor');
        const errorElement = document.getElementById('phone');
        if (phoneInput.validity.valid) {
            errorElement.textContent = '';
            
            return true; // Form will submit
        } else {
            errorElement.textContent = 'Số điện thoại chưa đúng';
            return false; // Prevent form submission
        }
    }
</script>
</body>
</html>