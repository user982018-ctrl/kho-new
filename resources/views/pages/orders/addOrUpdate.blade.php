@extends('layouts.default')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    /* SweetAlert2 buttons hover effect */
    .swal2-actions button {
        opacity: 0;
        transition: opacity 0.3s ease, transform 0.2s ease;
    }
    
    .swal2-actions:hover button {
        opacity: 1;
    }
    
    .swal2-actions button:hover {
        transform: scale(1.05);
    }

    .gift-td {
        /* display: flex;
        padding: 8px !important;
        height: 35px;
        justify-content: space-around; */
    }
    .gift-td input[type=checkbox], input[type=radio] {
        opacity: 1; 
        width: auto;
        width: 18px;
        height: 18px;
        vertical-align: middle;
        cursor: pointer;
        position: unset;
        /* accent-color: var(--brand); */
    }
    .text-left.row {
        padding-left:0 !important;
    }
    /* .total-header, .line-total, #sub-total {
        width:100px;
    } */
    .name-product {
        font-weight: bold;
    }
    .select-attribute {
        padding: 5px;
        padding-left: 10px;
    }
    .select-attribute label {
        padding-bottom: 5px;
        font-weight: 600;
    }

    tr {
        transition: opacity 0.4s ease;
    }
    input.qty  {
        width: 54px; height: 27px; padding: 2px;
    }

    .readonly {
      color: #999999;
      font-weight: bold;
    }

    .editable {
      font-weight: bold;
      color: #d20000;
      font-size: 16px;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }

    .promo-option {
        text-align: right;
    }

    #final-total[readonly] {
      background-color: #eee;
      color: #07b021;
    }
    .row {
        margin: unset;
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
    .error_msg {color: red;}
    .btn-submit {
        position: relative;
        /* margin: 10px 0; */
    }

    .row.btn-submit {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: #fff;
        padding: 15px;
        border-top: 1px solid #ddd;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
    
    /* Tạo khoảng trống ở cuối body để tránh bị che khuất nội dung */
    body {
        padding-bottom: 80px;
    }
    .create-bill {
        margin-left: 10px;
    }

    /* Fix SweetAlert2 buttons visibility */
    .swal2-popup {
        font-family: inherit !important;
    }
    
    .swal2-actions {
        display: flex !important;
        gap: 10px !important;
        margin-top: 1.5em !important;
        justify-content: center !important;
    }
    
    .swal2-styled {
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
        margin: 0 !important;
        font-weight: 500 !important;
        padding: 0.625em 1.1em !important;
        border-radius: 0.25rem !important;
        border: none !important;
        cursor: pointer !important;
        font-size: 1em !important;
    }
    
    .swal2-confirm {
        background-color: #3085d6 !important;
        color: #fff !important;
    }
    
    .swal2-cancel {
        background-color: #6c757d !important;
        color: #fff !important;
    }
    
    .swal2-styled:hover {
        opacity: 0.8 !important;
    }
    
    .swal2-styled:focus {
        box-shadow: 0 0 0 3px rgba(100,150,200,.5) !important;
        outline: none !important;
    }

    /* Custom Modal Styles */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s ease;
    }

    .custom-modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 0;
        border: none;
        border-radius: 12px;
        width: 400px;
        max-width: 90%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease;
        overflow: hidden;
    }

    .custom-modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        text-align: center;
        position: relative;
    }

    .custom-modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .custom-modal-body {
        padding: 30px 20px;
        text-align: center;
        color: #333;
        font-size: 16px;
        line-height: 1.5;
    }

    .custom-modal-actions {
        padding: 0 20px 20px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .custom-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 100px;
    }

    .custom-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .custom-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .custom-btn-secondary {
        background: #6c757d;
        color: white;
    }

    .custom-btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .custom-btn-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
    }

    .custom-btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes slideInLeft {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .modal-icon {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
    }

    /* Button Chốt đơn đẹp */
    .btn-chot-don {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 600 !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        overflow: hidden !important;
        letter-spacing: 0.5px !important;
    }

    .btn-chot-don:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6) !important;
        background: linear-gradient(135deg, #764ba2 0%, #667eea 50%, #f093fb 100%) !important;
    }

    .btn-chot-don:active {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
    }

    .btn-chot-don::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-chot-don:hover::before {
        left: 100%;
    }

    .btn-chot-don svg {
        animation: heartbeat 2s ease-in-out infinite !important;
        background: none !important;
        padding: 8px !important;
        margin-right: 8px !important;
        width: 32px !important;
        height: 32px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s ease !important;
        position: relative !important;
    }

    /* Tạo background hình trái tim */
    .btn-chot-don svg::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.2);
        clip-path: polygon(50% 85%, 15% 45%, 35% 25%, 50% 40%, 65% 25%, 85% 45%);
        z-index: -1;
        transition: all 0.3s ease;
    }

    .btn-chot-don:hover svg::before {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        transform: translate(-50%, -50%) scale(1.1);
    }

    @keyframes heartbeat {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .btn-chot-don:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3) !important;
    }
</style>
<?php 
use App\Helpers\HelperProduct;
$listStatus = Helper::getListStatus();
$isLeadSale = Helper::isLeadSale(Auth::user()->role);
$checkAll = isFullAccess(Auth::user()->role);
$flagAccess = false;
$listAttribute = Helper::getListAttributes();
$name = $phone = '';
$flagAccessDis = 'disabled';
$isDisableTotal = false;
$isKho = Helper::isKho(Auth::user());
$isBackOrder = false;
$id = 0;
if (isset($saleCare)) {
    $name = $saleCare->full_name;
    $phone = $saleCare->phone;
}
if ($checkAll || Auth::user()->id == 58 || $isKho) {
    $flagAccessDis = '';
}
if (isset($order) && ($order->status != 1 || $order->shippingOrder) && !$checkAll) {
    $isDisableTotal = true;
} 
if (isset($order)) {
    $id = $order->id;
    if ($order->status == 0 && !$order->shippingOrder && !$checkAll) {
        $isBackOrder = true;
    }
}
?>

<script src="{{asset('public/js/number-format/cleave.min.js')}}"></script>
<link href="{{ asset('public/css/pages/styleOrders.css')}}" rel="stylesheet">
<div class="body flex-grow-1">
    <form>
        {{ csrf_field() }}
        <div class="row">
            <div id="notifi-box" class="hidden alert alert-success print-error-msg">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @if(isset($order))
            <div class="card-body card-orders" style="padding:10px 0;">
                <input type="hidden" name="id" value="{{$order->id}}">
                <input value="{{$order->sale_care}}" class="hidden form-control" name="sale-care">

                <div class="row">
                    <div class="col-sm-12 col-lg-4">
                        <div class="row">
                            <div class="col-sm-12 col-lg-6">
                                <label class="form-label" for="phoneFor">Số điện
                                    thoại</label>
                                <input onkeyup="validatePhone()" required value="{{$order->phone}}" class="form-control"
                                    name="phone" id="phoneFor" type="text">
                                <p class="error_msg" id="phone"></p>
                            </div>
                            <div class="col-sm-12 col-lg-6">
                                <label class="form-label" for="nameFor">Tên khách
                                    hàng</label>
                                <input value="{{$order->name}}" class="form-control"
                                    name="name" id="nameFor" type="text">
                                <p class="error_msg" id="name"></p>
                            </div>
                            <div class="col-sm-6 col-md-6 form-group">
                                <label class="form-label" for="distric-filter">Quận - Huyện<span class="required-input">(*)</span></label>
                                <select required name="district" id="distric-filter" class="form-control">       
                                    <option value="">--Chọn quận/huyện--</option>
                                    @foreach ($listProvince as $item)
                                    <option <?= ($item['id'] == $order->district) ? "selected" : '';?> value="{{$item['id']}}">{{$item['name']}}</option>
                                    @endforeach
                                </select>
                                <p class="error_msg" id="district"></p>
                            </div>
                            <div class="col-sm-6 col-md-6 form-group">
                                <label class="form-label" for="ward-filter">Phường - xã<span class="required-input">(*)</span></label>
                                <select name="ward" id="ward-filter" class="form-control">
                                    @if (isset($listWard))
                                    @foreach ($listWard as $ward)
                                    <option <?= ($ward['id'] == $order->ward) ? "selected" : '';?> value="{{$ward['id']}}">{{$ward['name']}}</option>
                                    @endforeach
                                    
                                    @else
                                    <option value="-1">--Chọn phường/ xã--</option>
                                    @endif
                                </select>
                                <p class="error_msg" id="ward"></p>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="addressFor">Địa chỉ chi tiết</label>
                                <input value="{{$order->address}}" class="form-control"
                                    name="address" id="addressFor" type="text">
                                <label class="error_msg" id="address" for="addressFor"></label>
                            </div>

                            @if ($checkAll || $isLeadSale)
                            <div class="col-6">
                                <label class="form-label" >Chọn Sale</label>
                                <select class="form-control" name="assign-sale">

                                @if (isset($listSale))
                                @foreach ($listSale as $item)
                                    <option <?php echo ($item->id == $order->assign_user) ? 'selected' : '';?> value="{{$item->id}}">{{$item->real_name}}</option>
                                @endforeach
                                @endif

                                </select>
                                <p class="error_msg" id="price"></p>
                            </div>
                            @else 
                            <?php $assignSale = $order->assign_user;
                            if ($order->saleCare) {
                                $assignSale = $order->saleCare->assign_user;
                            }
                            ?>
                            <div class="col-6 hidden">
                                <select class="form-control" name="assign-sale">
                                    <option value="{{$assignSale}}"></option>
                                </select>
                            </div>
                            @endif
                            <div class="col-12">
                                <label for="note" class="form-label">Ghi chú:</label>
                                <textarea name="note" class="form-control" id="note" rows="4">{{$order->note}} </textarea>
                            </div>
                            <div class="col-6 pb-1">
                                <label class="form-label" for="status">Trạng thái:</label>
                                <select {{$flagAccessDis}} name="status" id="status" class="form-control">
                                    @foreach ($listStatus as $k => $val)
                                    <option <?= (int)$order->status == (int)$k ? 'selected' : ''; ?> value="{{$k}}">{{$val}}</option>
                                    @endforeach
                                </select>
                                <p class="error_msg" id="sex"></p>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-sm-12 col-lg-8" style="padding: 20px 0;">
                        <div class="row product-list-order">
                            <div class="col-xs-12 col-sm-6 col-md-4 form-group" style="text-align:center; margin-bottom: 20px;">
                                <select id="product-select" style="display: none;">
                                    <option value="">-- Chọn sản phẩm --</option>

                                    @if(isset($listProduct))
                                    @foreach ($listProduct as $value)
                                    <option data-product_type="{{$value->type}}" value="{{$value->id}}|{{$value->price}}|{{$value->name}}">{{$value->name}}</option>
                                    @endforeach
                                    @endif

                                </select>
                               <p class="error_msg" id="qty"></p>
                            </div>
                        </div>
                                
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered table-line" style="margin-bottom:15px; font-size: 13px; ">
                                    <thead>
                                        <tr>
                                            <th colspan="1" class="text-center no-wrap col-spname" style="min-width: 155px">Sản phẩm</th>           
                                            <th colspan="1" class="text-center no-wrap">Quà </th>
                                            <th colspan="1" class="text-center no-wrap">Đơn giá</th>
                                            <th colspan="1" class="text-center no-wrap">Số lượng</th>
                                            <th colspan="1" class="text-center no-wrap total-header">Thành tiền</th>
                                            <th colspan="1" class="text-center no-wrap"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-body">
                                        <?php $sumQty = $totalTmp = 0;
                                        if ($order->id_product) {
                                            $i = 0;
                                             
                                        foreach (json_decode($order->id_product) as $item) {
                                            $product = getProductByIdHelper($item->id);
                                        
                                            if ($product) {
                                                $rowClass = '';
                                                $type = $product->type;
                                                
                                                $price = $product->price;
                                                $variantId = 0;
                                                $listAttributeOfItem = [];
                                                if ($type == 2 && !empty($item->variantId)) {
                                                    $rowClass = 'row';
                                                   
                                                    $variantId = $item->variantId;
                                                    $variant = HelperProduct::getProductVariantById($variantId);
                                                    $price = $variant->price;
                                                    if ($variant) {
                                                        foreach ($variant->attributeValues as $attribute) {
                                                            $listAttributeOfItem[] = $attribute->attribute_value_id;
                                                        }
                                                    }
                                                }
                                                
                                                $sumQty += $item->val;
                                                $totalTmp += $item->val * $price;
                                                $tmp = $item->val * $price;
                                                
                                        ?>

                                        <tr data-price="{{$price}}" data-type="{{$type}}"  data-variant-id="{{$variantId}}" class="number dh-san-pham" data-id="{{$product->id}}">
                                            <td class="text-left {{$rowClass}}">
                                                <h5 class="name-product">{{$product->name}}</h5>

                                                <?php if ($product->type == 2) {
                                                    
                                                    foreach ($listAttribute as $attribute) {
                                                ?>
                                                <div class="select-attribute col-sm-12 col-lg-6" data-id="{{$product->id}}">
                                                    <label for="{{$attribute->id}}-filter">{{$attribute->name}}:</label>
                                                    <select name="attribute-{{$attribute->id}}" id="{{$attribute->id}}-filter" class="slb-attribute form-control">       
                                                        
                                                        @foreach ($attribute->values as $value)
                                                        <option <?php echo (in_array($value->id, $listAttributeOfItem)) ? 'selected' : ''; ?>
                                                        value="{{$value->id}}">{{$value->value}}</option>

                                                        @endforeach
                                                    </select>
                                                </div>
                                                <?php }   
                                                }

                                                ?>
                                            </td>
                                            <td class="text-center gift-td">
                                                <input {{ (isset($item->gift) && $item->gift == 'true') ? 'checked' : '' }} class="row-check gift gift-checkbox" type="checkbox" />
                                            </td>
                                            <td class="no-wrap unit-price text-center" style="width: 80px">{{number_format($price)}}</td>
                                            <td style="text-align: left;" class="number">
                                                <input data-product_id="{{$product->id}}" type="number" class="qty" value="{{$item->val}}" min="1">
                                            </td>
                                            <td class="line-total no-wrap text-center" style="width: 30px"> {{number_format($tmp)}}</td>
                                            <td class="text-center"><button type="button" class="delete-btn">🗑️</button></td>
                                        </tr>
                            
                                        <?php }   
                                        $i++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="no-wrap text-right" colspan="3">Tạm tính:</td>
                                            <td style="width: 54px;ss" class="readonly" id="total-qty" data-total-qty="{{$order->qty}}">{{$order->qty}}</td>
                                            <td class="readonly text-center" id="sub-total">{{number_format($order->total)}}</td>
                                            <td colspan="1"></td>
                                        </tr>
                                        <tr>
                                            <td class="no-wrap text-right promo-option" colspan="4">Tổng đơn: <br>
                                                <input {{$isDisableTotal ? 'disabled' : ''}}
                                                 {{ $order->is_price_sale ? 'checked' : '' }} name="priceSale" type="checkbox" id="promo-checkbox" class="form-check-input">
                                                <label class="form-label" for="promo-checkbox">Khuyến mãi</label>
                                            </td>
                                            <td class="no-wrap text-center" colspan="1">
                                                <input {{ $order->is_price_sale ? '' : 'readonly' }}
                                                {{$isDisableTotal ? 'disabled' : ''}}
                                                data-product-price="{{$totalTmp}}" type="text"  name="price" id="final-total" class="text-center editable price_class" value="{{number_format($order->total)}}">
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row btn-submit">
                <div class="col-sm-12 col-lg-12" style="text-align: end;">
                    <button id="cancel" type="button" {{($isDisableTotal || $checkAll || $isKho) ? 'hidden' : ''}} class=" mb-1 btn btn-danger text-white create-bill" 
                    onclick="confirmCancel()"><svg class="icon me-2">
                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-x-circle')}}"></use>
                      </svg>Huỷ Đơn</button>

                    
                    <button data-created_at="{{$order->created_at}}" id="backOrder" type="button" {{($isBackOrder && !$isKho) ? '' : 'hidden'}} class=" mb-1 btn btn-warning text-white create-bill" 
                    onclick="confirmBackOrder()"><svg class="icon me-2">
                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-loop-circular')}}"></use>
                      </svg>Chốt Lại</button>
                    <button id="submit" class="mb-1 btn btn-primary create-bill"><svg class="icon me-2">
                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-save')}}"></use>
                      </svg>Lưu</button>
                </div>
            </div>
            @else

            <div class="card-body card-orders" style="padding:10px 0;">
                <div class="body flex-grow-1">
                    <div class="row">
                        <div class="col-sm-12 col-lg-4">
                            <div class="row">
                                <?php $saleCareId = request()->get('saleCareId');?>
                                
                                <input value="<?= ($saleCareId) ?: $saleCareId ?>" class="hidden form-control" name="sale-care">
                                <div class="col-sm-12 col-lg-6">
                                    <label class="form-label" for="phoneFor">Số điện thoại<span class="required-input">(*)</span></label>
                                    <input onkeyup="validatePhone()" placeholder="0973409613" class="form-control" name="phone"
                                        id="phoneFor" type="text" value="{{$phone}}">
                                    <p class="error_msg" id="phone"></p>
                                </div>
                                <div class="col-sm-12 col-lg-6">
                                    <label class="form-label" for="nameFor">Tên khách hàng<span class="required-input">(*)</span></label>
                                    <input placeholder="Họ và tên" class="form-control" name="name"
                                        id="nameFor" type="text" value="{{$name}}">
                                    <p class="error_msg" id="name"></p>
                                </div>
                                
                                <div class="col-xs-12 col-sm-6 col-md-6 form-group">
                                    <label class="form-label" for="distric-filter">Quận - Huyện<span class="required-input">(*)</span></label>
                                    <select required name="district" id="distric-filter" class="form-control">       
                                        <option value="">--Chọn quận/huyện--</option>
                                        @foreach ($listProvince as $item)
                                        <option value="{{$item['id']}}">{{$item['name']}}</option>

                                        @endforeach
                                    </select>
                                    <p class="error_msg" id="district"></p>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 form-group">
                                    <label class="form-label" for="ward-filter">Phường - xã<span class="required-input">(*)</span></label>
                                    <select name="ward" id="ward-filter" class="form-control">       
                                        <option value="-1">--Chọn phường/ xã--</option>
                                    </select>
                                    <p class="error_msg" id="ward"></p>
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="addressFor">Địa chỉ chi tiết<span class="required-input">(*)</span></label>
                                    <input placeholder="số nhà - tên đường/ thôn/ ấp" class="form-control" name="address"
                                        id="addressFor" type="text">
                                    <label class="error_msg" id="address" for="addressFor"></label>
                                </div>

                                <?php $checkAll = isFullAccess(Auth::user()->role);?>
                                @if ($checkAll || $isLeadSale)
                                    <div class="col-lg-6">
                                        <label class="form-label">Chọn Sale:</label>
                                        <select class="form-control" name="assign-sale" >

                                        @if (isset($listSale))
                                        @foreach ($listSale as $item)
                                            <option value="{{$item->id}}">{{$item->real_name}}</option>
                                        @endforeach
                                        @endif

                                        </select>
                                        <p class="error_msg" id="price"></p>
                                    </div>
                                @else 
                                    <div class="col-6 hidden">
                                        <select class="form-control" name="assign-sale">
                                            <option value="{{Auth::user()->id}}"></option>
                                        </select>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <label for="note" class="form-label">Ghi chú:</label>
                                    <textarea name="note" class="form-control" id="note" rows="4"></textarea>
                                    <p></p>
                                </div>

                                <div class="col-lg-6 col-sm-12 pb-1">
                                    <label class="form-label" for="statusFor">Trạng thái:</label>
                                    <select {{$flagAccessDis}} name="status" id="statusFor"
                                        class="form-control">

                                        @foreach ($listStatus as $k => $val)
                                            <option value="{{$k}}">{{$val}}</option>
                                        @endforeach

                                    </select>
                                    <p class="error_msg" id="sex"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-8" style="padding: 20px 0;">
                            <div class="row product-list-order">
                                <div class="col-xs-12 col-sm-6 col-md-4 form-group" style="text-align:center; margin-bottom: 20px;">
                                    <select id="product-select" style="display: none;">
                                        <option value="">-- Chọn sản phẩm --</option>

                                        @if(isset($listProduct))
                                        @foreach ($listProduct as $value)
                                        <option data-product_type="{{$value->type}}" value="{{$value->id}}|{{$value->price}}|{{$value->name}}">{{$value->name}}</option>
                                        @endforeach
                                        @endif

                                    </select>
                                    <p class="error_msg" id="qty"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered table-line" style="width:100%;margin-bottom:15px; font-size: 13px; ">
                                        <thead>
                                            <tr>
                                                <th colspan="1" class="text-center no-wrap col-spname" style="min-width: 155px">Sản phẩm</th>  
                                                <th colspan="1" class="text-center no-wrap">Quà</th>         
                                                <th colspan="1" class="text-center no-wrap">Đơn giá</th>
                                                <th colspan="1" class="text-center no-wrap">Số lượng</th>
                                                <th colspan="1" class="total-header text-center no-wrap" style="width:10%">Thành tiền</th>
                                                <th colspan="1" class="text-center no-wrap"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart-body"></tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="no-wrap text-right" colspan="3">Tạm tính:</td>
                                                <td style="width: 54px;" class="readonly" id="total-qty" data-total-qty="0">0</td>
                                                <td class="readonly text-center" id="sub-total"></td>
                                                <td colspan="1"></td>
                                            </tr>
                                        
                                            <tr>
                                                <td class="no-wrap text-right promo-option" colspan="4">Tổng đơn: <br>
                                                    <input name="priceSale" type="checkbox" id="promo-checkbox" class="form-check-input">
                                                    <label class="form-label" for="promo-checkbox">Khuyến mãi</label>
                                                </td>
                                                <td class="no-wrap" colspan="1">
                                                    <input readonly data-product-price="0" type="text"  name="price" id="final-total" 
                                                    class="editable price_class text-center" value="0">
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row btn-submit">
                <div class="col-sm-12 col-lg-12" style="text-align: end;">
                    <button onclick="validatePhone()" id="submit" class="mb-1 btn btn-primary create-bill btn-chot-don">
                        <i class="fa fa-heart" style="color:red;"></i> Chốt đơn</button>
                </div>
            </div>
            @endif
            
        </div>
    </form>
    <div id="loader-overlay">
        <div class="loader"></div>
    </div>

    <input type="hidden" id="list_variants"/>
    <input type="hidden" id="list_all_attribute" value="{{$listAttribute}}"/>

    <!-- Custom Modal -->
    <div id="customModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3 id="modalTitle">Xác nhận</h3>
            </div>
            <div class="custom-modal-body">
                <span id="modalIcon" class="modal-icon">⚠️</span>
                <p id="modalMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
            </div>
            <div class="custom-modal-actions">
                <button id="modalCancel" class="custom-btn custom-btn-secondary">Không</button>
                <button id="modalConfirm" class="custom-btn custom-btn-danger">Có</button>
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

$(document).ready(function() {

    $("#submit").click(function(e) {
        e.preventDefault();

        $('#loader-overlay').css('display', 'flex');
        // $('.body .row').css("opacity", "0.5");
        // $('.body .row').css("position", "relative");

        var _token      = $("input[name='_token']").val();
        var phone       = $("input[name='phone']").val();
        var name        = $("input[name='name']").val();
        var sex         = $("select[name='sex']").val();
        var ward        = $("input[name='ward']").attr('data-ward-id');
        var address     = $("input[name='address']").val();
        var qty         = $("#total-qty").attr('data-total-qty');
        var assignSale  = $("select[name='assign-sale']").val();
        var note        = $("#note").val();
        var id          = $("input[name='id']").val();
        var status      = $("select[name='status']").val();
        var saleCareId  = $("input[name='sale-care']").val();
        var district  = $("select[name='district']").val();
        var ward  = $("select[name='ward']").val();
        
        var productCart = getListProductCart();
        // dd();
        if (phone == '' || !validatePhone()) {
            $('#loader-overlay').css('display', 'none');
           return false;
        }

        // let listProduct = [];
        // $(".number input").each(function(index) {
        //     let productId = $(this).data("product_id");
        //     let val = Number($(this).val());
        //     listProduct.push({
        //         id: productId,
        //         val: val
        //     });
        // });

        var isPriceSale = $("input[name='priceSale']:checked").val();
        var price = $("input[name='price']").val();
        price = price.replace(/[^0-9]+/g, "");
        if (isPriceSale == 'on') {
            isPriceSale = 1;
        } else {
            isPriceSale = 0;
        }

        $.ajax({
            url: "{{route('save-orders')}}",
            type: 'POST',
            data: {
                _token: _token,
                saleCareId,
                phone,
                name: name,
                price: price,
                qty: qty,
                id,
                sex,
                // products: JSON.stringify(listProduct),
                products: productCart,
                qty,
                price,
                address,
                district,
                ward,
                assignSale,
                isPriceSale,
                note,
                status
            },
            success: function(data) {
                console.log(data);
                if ($.isEmptyObject(data.errors)) {
                    // window.parent.postMessage('mess-success', '*');
                    toastr.success(data.success);
                    $(".error_msg").html('');
                    // $("#notifi-box").show();
                    // $("#notifi-box").html(data.success);
                    // $("#notifi-box").slideDown('fast').delay(5000).hide(0);
                    if (data.link) {
                        // Delay 1 giây để hiển thị toastr trước khi redirect
                        setTimeout(function() {
                            window.location.href = data.link;
                        }, 1000);
                    }
                } else {
                    $('.error_msg').text('');
                    let resp = data.errors;
                    for (index in resp) {
                        $("#" + index).html(resp[index]);
                    }
                    $('#loader-overlay').css('display', 'none');
                }
            }
        });

    });

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position 
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val + 'đ';

            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }

    $("input[name='priceSale']").click(function() {
        if ($(this).is(':checked')) {
            $("input[name='price']").prop("readonly", false);
            $("input[name='price']").focus();
        } else {
            $("input[name='price']").prop("readonly", true);
            let price           = $("input[name='price']").attr("data-product-price");
            console.log(price);
            let newPriceFormat  = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
                .format(price,);
            $("input[name='price']").val(newPriceFormat);
        }
    });

    $('.refresh').click(function() {
        location.reload(true)
    });
});

document.querySelectorAll('.price_class').forEach(inp => new Cleave(inp, {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand'
}));

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        $('#distric-filter').select2();
        $('#ward-filter').select2();
        $('#product-select').select2({
            placeholder: "Chọn sản phẩm...",
            allowClear: true,
            width: 'resolve'
        });


 
    });
</script>
<script>
    $(document).ready(function() {
        // var baseLink = location.href.slice(0,location.href.lastIndexOf("/"));
        // var link = baseLink + '/public/json/local.json';
        // var listProvince = fetch(link)
        //     .then((res) => {
        //         if (!res.ok) {
        //             throw new Error
        //                 (`HTTP error! Status: ${res.status}`);
        //         }
        //         return res.json();
        //     });

        $('#distric-filter').on('change', function() {
            var id = this.value;
            var _token  = $("input[name='_token']").val();
            $.ajax({
                url: "{{ route('get-ward-by-id-distric') }}",
                type: 'GET',
                data: {
                    _token: _token,
                    id
                },
                success: function(data) {
                    if (data.length > 0) {
                        
                        let str = '';
                        $.each(data, function(index, value) {
                            str += `<option value="` +value.id+ `">` + value.name + `</option>`;
                        });

                        $('#ward-filter').html(str);
                        $('#ward-filter').select2();
                    }
                }
            });
        })
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>
<script>
function validatePhone() {
    const input = document.getElementById('phoneFor').value;
    const message = document.getElementById('phone');

    // Regex: bắt đầu bằng 0, theo sau là 9 số
    // const regex = /^(03[0-9]|05[0-9]|07[0-9]|08[0-9]|09[0-9])\d{7}$/;
    const regex = /^(?:(03[0-9]|05[0-9]|07[0-9]|08[0-9]|09[0-9])\d{7}|02\d{9})$/;
    if (regex.test(input)) {
        message.textContent = "✔️ Số điện thoại hợp lệ";
        message.style.color = "green";
        return true;
    } else {
        message.textContent = "❌ Số điện thoại không hợp lệ";
        message.style.color = "red";
        return false;
    }
}
</script>
 <script>
    const cartBody = document.getElementById('cart-body');
    const subTotal = document.getElementById('sub-total');
    const totalQtyElem = document.getElementById('total-qty');
    const finalTotal = document.getElementById('final-total');
    const promoCheckbox = document.getElementById('promo-checkbox');

    function formatVND(number) {
        return Number(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function parseVND(str) {
      return parseInt(str.replace(/\D/g, '')) || 0;
    }

    function updateSubtotal() {
      let total = 0;
      let totalQty = 0;
      const rows = cartBody.querySelectorAll('tr');

      rows.forEach(row => {
        const checkbox = row.querySelector(".gift");
        const price = parseInt(row.dataset.price);
        const qty = parseInt(row.querySelector('.qty').value) || 0;
        const lineTotal = price * qty;

        if (checkbox.checked) {
            row.querySelector('.line-total').textContent = "0";
            totalQty += qty;
        } else {
            row.querySelector('.line-total').textContent = formatVND(lineTotal);
            total += lineTotal;
            totalQty += qty;
        }
      });

      subTotal.textContent = formatVND(total);
      totalQtyElem.textContent = totalQty;
      totalQtyElem.dataset.totalQty = totalQty;

      if (!promoCheckbox.checked) {
        finalTotal.value = formatVND(total);
      }
    }

    function bindDeleteButtons() 
    {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.removeEventListener('click', btn._deleteHandler); // gỡ handler cũ nếu có

            btn._deleteHandler = function () {
                const row = this.closest('tr');
                const productName = row.querySelector('.name-product')?.textContent.trim() || 'sản phẩm này';

                // Xác nhận trước khi xoá bằng custom modal
                showCustomModal(
                    'Xác nhận xóa sản phẩm',
                    `Bạn có chắc chắn muốn xóa "${productName}"?`,
                    '🗑️',
                    function() {
                        // Thêm hiệu ứng mờ dần rồi xoá
                        row.style.transition = 'opacity 0.4s ease';
                        row.style.opacity = 0;

                        setTimeout(() => {
                            row.remove();
                            updateSubtotal();
                        }, 400);
                    },
                    'Có, xóa!',
                    'custom-btn-danger'
                );
            };

            btn.addEventListener('click', btn._deleteHandler);
        });
    }

    // Gắn sự kiện khi thay đổi số lượng
    function bindQtyInputs() {
      document.querySelectorAll('.qty').forEach(input => {
        input.addEventListener('input', updateSubtotal);
      });
    }

    function bindGiftInputs() {
      document.querySelectorAll(".gift").forEach(cb => cb.addEventListener("change", updateSubtotal));
    }
    

    promoCheckbox.addEventListener('change', function () {
      const isChecked = this.checked;
      finalTotal.readOnly = !isChecked;

      if (!isChecked) {
        finalTotal.value = subTotal.textContent;
      }
    });

    function bindSelectAttr()
    {
        const rows = cartBody.querySelectorAll('tr');
        rows.forEach(row => {
            const selects = row.querySelectorAll('.slb-attribute');
            selects.forEach(function(selectElement) {
                selectElement.addEventListener('change', function(e) {

                    var values = Array.from(selects).map(select => Number(select.value));
                    var listVariants = $('#list_variants').val();

                    listVariants = JSON.parse(listVariants);
                    // console.log('Các giá trị đang chọn là:', values);

                    const variant = listVariants.find(v =>
                        areArraysEqual(v.list_attribute, values)
                    );

                    // console.log('variant đc chọn là: ', variant)
                    const idProduct = this.parentElement.getAttribute('data-id');
                    if (variant != undefined && idProduct != undefined) {
                        // var trProduct = document.querySelector(`tr[data-id="${idProduct}"]`); 
                        trProduct = row;
                        priceVariant = variant.price;
                        trProduct.querySelector('.unit-price').textContent = formatVND(priceVariant);
                        trProduct.querySelector('.line-total').textContent = formatVND(priceVariant);
                        trProduct.querySelector('.number input.qty').value = 1;

                        const priceVariantInt = parseInt(priceVariant);
                        trProduct.dataset.price = priceVariantInt;
                        trProduct.dataset.variantId = variant.id;
                        updateSubtotal();
                    }
                });
             });
        }); 
    }

    // Khởi tạo
    updateSubtotal();
    bindDeleteButtons();
    bindQtyInputs();
    bindSelectAttr();
    bindGiftInputs();
</script>
<script>
    $('#product-select').on('select2:select', function (e) {
        const selected = $(this).val();
        const type = $(this).find(':selected').data('product_type');
        
        if (!selected) return;

        const [id, price, name] = selected.split('|');
        const priceInt = parseInt(price);
        const productId = parseInt(id);
        const productType = parseInt(type);

        const existingRow = cartBody.querySelector(`tr[data-id="${productId}"]`);

        if (existingRow && productType != 2) {
            const qtyInput = existingRow.querySelector('.qty');
            qtyInput.value = parseInt(qtyInput.value) + 1;
        } else {
            var strVariants = '';
             const tr = document.createElement('tr');
            tr.dataset.price = priceInt;
            tr.dataset.id = productId;
            tr.dataset.type = productType;
            var rowClass = values = '';
            if (productType == 2) {
                rowClass = 'row';
                strVariants = `
                    @foreach ($listAttribute as $attribute) 
                    <div class="select-attribute col-sm-12 col-lg-6" data-id="${productId}}">
                        <label class="" for="{{$attribute->id}}-filter">{{$attribute->name}}:</label>
                        <select data-attribute="{{$attribute->id}}" name="attribute-{{$attribute->id}}" id="{{$attribute->id}}-filter" class="slb-attribute form-control">       
                            
                            @foreach ($attribute->values as $value)
                            <option value="{{$value->id}}">{{$value->value}}</option>

                            @endforeach
                        </select>
                    </div>
                    @endforeach`;

                values = `<?php $values = [];
                    foreach ($listAttribute as $attribute) {
                        $values[] = $attribute->values[0]->id;
                    } 
                    echo json_encode($values);
                ?>`;

                var listVariants = $('#list_variants').val();
                listVariants = JSON.parse(listVariants);
                // console.log('Các giá trị đang chọn là:', values);

                const variant = listVariants.find(v =>
                    areArraysEqual(v.list_attribute, values)
                );
                
                if (variant != undefined) { 
                    tr.dataset.variantId = variant.id;
                }
            }
           
            tr.innerHTML = `
            <td class="text-left ${rowClass}"><h5 class="name-product">${name}</h5>${strVariants}</td>
            <td class="text-center gift-td"> 
                <input class="row-check gift-checkbox gift" type="checkbox" />
            </td>
            <td class="price unit-price text-center">${formatVND(priceInt)}</td>
            <td class="number"><input data-product_id="${productId}" type="number" class="qty" value="1" min="0"></td>
            <td class="line-total text-center">${formatVND(priceInt)}</td>
            <td class="text-center"><button type="button" class="delete-btn text-center">🗑️</button></td>
            `;
            cartBody.appendChild(tr);
            bindQtyInputs();
            bindGiftInputs();
            bindDeleteButtons();
            
            // if (values != '') {
            //     values = JSON.parse(values);
            //     values.forEach(function(number, index) {
            //         console.log(`Element at index ${index}: ${number}`);
            //         $("#" + number + "-filter").select2();
            //     });
            // }
            
        }
        updateSubtotal();
        bindSelectAttr();
       
        // Reset Select2 dropdown sau khi thêm
        $(this).val(null).trigger('change');
    });
</script>

<script>

    $.ajax({
        url: "{{ url('/api/variants-by-id-product') }}",
        type: 'GET',
        data: {
            id : 83 //npk
        },
        success: function(data) {
            if (data.length > 0) {
                $('#list_variants').val(JSON.stringify(data));
            }
        }
    });

    function arraysEqual(a, b) {
        if (a.length !== b.length) return false;
        return a.every((val, index) => val === b[index]);
    }

    function areArraysEqual(arr1, arr2) {
        // Nếu độ dài hai mảng không bằng nhau, chúng không thể giống nhau
        if (arr1.length !== arr2.length) {
            return false;
        }

        // Sắp xếp hai mảng và so sánh từng phần tử
        const sortedArr1 = arr1.slice().sort((a, b) => a - b);
        const sortedArr2 = arr2.slice().sort((a, b) => a - b);

        return sortedArr1.every((value, index) => value === sortedArr2[index]);
    }

    function getListProductCart()
    {
        const cartBody = document.querySelector("#cart-body");
        const cartData = att = [];

        cartBody.querySelectorAll("tr").forEach((row) => {
            const name = row.querySelector(".name-product")?.textContent.trim() || "N/A";
            const productId = row.dataset.id || "N/A";
            const quantity = row.querySelector(".qty")?.value || 0;
            const type = row.dataset.type || 1;
            const gift  = row.querySelector('.gift-checkbox')?.checked || false;

            var data;
            const variantId = row.dataset.variantId || 0;
            data = {
                id: parseInt(productId),
                val: parseInt(quantity),
                variantId: parseInt(variantId),
                type: parseInt(type),
                gift: Boolean(gift),
            }
            cartData.push(data);
        });
        return cartData;
    }

    // Custom Modal Functions
    function showCustomModal(title, message, icon, confirmCallback, confirmText = 'Có', confirmClass = 'custom-btn-danger') {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        document.getElementById('modalIcon').textContent = icon;
        document.getElementById('modalConfirm').textContent = confirmText;
        document.getElementById('modalConfirm').className = `custom-btn ${confirmClass}`;
        
        const modal = document.getElementById('customModal');
        modal.style.display = 'block';
        
        // Store callback
        modal._confirmCallback = confirmCallback;
    }

    function hideCustomModal() {
        document.getElementById('customModal').style.display = 'none';
    }

    // Modal event listeners
    document.getElementById('modalCancel').addEventListener('click', hideCustomModal);
    document.getElementById('modalConfirm').addEventListener('click', function() {
        const modal = document.getElementById('customModal');
        if (modal._confirmCallback) {
            modal._confirmCallback();
        }
        hideCustomModal();
    });

    // Close modal when clicking outside
    document.getElementById('customModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideCustomModal();
        }
    });

    // Function xác nhận hủy đơn hàng
    function confirmCancel() {
        showCustomModal(
            'Xác nhận hủy đơn',
            'Bạn có chắc chắn muốn hủy đơn hàng này?',
            '⚠️',
            function() {
                window.location.href = '{{route("cancel-order", $id)}}';
            },
            'Có, hủy đơn!',
            'custom-btn-danger'
        );
    }

    // Function xác nhận chốt lại đơn hàng với kiểm tra tháng
    function confirmBackOrder() {
        const backOrderBtn = document.getElementById('backOrder');
        const createdAt = backOrderBtn.getAttribute('data-created_at');
 
        // Lấy tháng và năm từ created_at
        const orderDate = new Date(createdAt);
        const orderMonth = orderDate.getMonth();
        const orderYear = orderDate.getFullYear();

        // Lấy tháng và năm hiện tại
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();

        // So sánh tháng và năm
        if (orderMonth === currentMonth && orderYear === currentYear) {
            // Cùng tháng - redirect ngay
            window.location.href = '{{route("back-order", $id)}}';
        } else {
            // Khác tháng - hiển thị modal
            showCustomModal(
                'Đơn hàng khác tháng!',
                'Đơn hàng không phải tháng này. Bạn có chắc chắn muốn tiếp tục?',
                '⚠️',
                function() {
                    window.location.href = '{{route("back-order", $id)}}';
                },
                'Có, tiếp tục!',
                'custom-btn-primary'
            );
        }
    }
</script>
@stop