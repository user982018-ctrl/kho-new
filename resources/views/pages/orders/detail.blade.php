@extends('layouts.default')
@section('content')

<?php
$checkAll = isFullAccess(Auth::user()->role);
$isKho = Helper::isKho(Auth::user());
$name = '';
if (Helper::isOldCustomerV2($order->phone)) {
    $name .= '❤️ ';
}
?>
<style>
    #laravel-notify .notify {
        z-index: 9999;
    }
    .green span {
    width: 80px;
    display: inline-block; 
    color: #fff;
    background: #0f0;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #0f0;
    font-weight: 700;
  }

  .red span {
    text-align: center;
    width: 80px;
    display: inline-block; 
    color: #ff0000;
    background: #fff;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #ff0000;
    font-weight: 700;
  }

  .orange span {
    /* width: 80px; */
    display: inline-block;
    color: #fff;
    background: #ffbe08;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #fff;
    font-weight: 700;
  }

  table.order .first-col {
    width: 35%;
  }

  @media only screen and (max-width: 600px) {
    table.order .first-col {
    width: 50%;
  }
}
</style>
<div class="body flex-grow-1 px-3">
    <div class="row">
        <div id="notifi-box" class="hidden alert alert-success print-error-msg">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><span><strong>Chi tiết đơn hàng #{{$order->id}} - {{date_format($order->created_at,"d-m-Y ")}}</strong></span>
                </div>
            </div>
            <table class="table order">
                <tbody>
                <tr>
                    <td class="first-col">Mã vận đơn</td>
                    <td>
                       <?php $isMappingShip = Helper::isMappingShippByOrderId($order->id);?>
                           
                        @if (!$isMappingShip)
                            @if ($checkAll || $isKho)
                            <a href="{{URL::to('tao-van-don/'. $order->id)}}" class="btn btn-warning ms-1">+ Tạo vận đơn</a>
                            @endif
                        @else
                            <a style="color: #fff;" href="{{URL::to('chi-tiet-van-don/'. $isMappingShip->id)}}" class="btn btn-warning ms-1">{{$isMappingShip->vendor_ship}} - {{$isMappingShip->order_code}}</a>
                            @if ($checkAll || $isKho)
                            <a style="color: #fff;" onclick="return confirm('Bạn muốn gỡ mã vận đơn ra khỏi đơn hàng?')" href="{{URL::to('go-van-don/'. $isMappingShip->id)}}" class="btn btn-warning ms-1">Gỡ <svg class="" width="17" height="17" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle;"><path d="M987.695 141.258c-22.002-2.006-41.46 14.204-43.465 36.204l-5.3 58.138c-89.919-141.212-246.006-235.6-426.865-235.6-194.562 0-366.024 111.044-451.14 277.709-10.048 19.673-2.244 43.768 17.43 53.815 19.678 10.047 43.768 2.247 53.816-17.431 72.034-141.048 216.689-234.094 379.894-234.094 148.754 0 282.999 77.462 359.905 198.817l-57.033-40.735c-17.979-12.839-42.961-8.673-55.798 9.302-12.839 17.977-8.675 42.959 9.302 55.798l172.95 123.523c25.348 18.070 60.31 1.528 63.082-28.92l19.421-213.059c2.007-22.002-14.201-41.462-36.2-43.468z" fill="rgb(253, 216, 0)" style="fill: rgb(255, 255, 255);"></path><path d="M945.709 692.476c-19.677-10.047-43.77-2.245-53.817 17.428-72.034 141.050-216.689 234.095-379.894 234.095-148.754 0-282.999-77.462-359.906-198.816l57.034 40.735c17.979 12.837 42.959 8.673 55.798-9.302 12.839-17.979 8.675-42.959-9.302-55.798l-172.949-123.523c-25.038-17.887-60.282-1.841-63.082 28.918l-19.422 213.060c-2.006 22.002 14.204 41.462 36.202 43.467 22.028 2.003 41.462-14.223 43.466-36.205l5.3-58.137c89.916 141.214 246.003 235.602 426.862 235.602 194.562 0 366.025-111.045 451.14-277.709 10.050-19.673 2.245-43.768-17.428-53.815z" fill="rgb(253, 216, 0)" style="fill: rgb(255, 255, 255);"></path></svg></a>
                            @endif

                        @endif

                    </td>
                </tr>
                <tr>
                    <td class="first-col">Người tạo</td>
                    <td>{{Helper::getUserByID($order->assign_user)->real_name}}</td>
                </tr>
                <tr>
                    <td class="first-col">Số điện thoại</td>
                    <td>{{$order->phone}}</td>
                </tr>
                <tr>
                    <td class="first-col">Tên khách hàng</td>
                    <td>{{$name .= $order->name}}</td>
                </tr>
                <tr>
                    <td class="first-col">Giới tính</td>
                    <td><?= $order->sex == 0 ? 'Nam' : 'Nữ'; ?></td>
                </tr>
                <tr>
                    <td class="first-col">Địa chỉ</td>
                    <td>{{$order->address}}</td>
                </tr>
                <tr>
                    <td class="first-col">Tổng tiền</td>
                    <td>{{number_format($order->total)}}đ</td>
                </tr>

                <tr>

                    <?php $listStatus = Helper::getListStatus(); 
                    // dd($listStatus); 
                    $styleStatus = [
                        0 => 'red',
                        1 => 'white',
                        2 => 'orange',
                        3 => 'green',
                        ];
                    ?>
                    <td class="first-col">Trạng thái</td>
                    <td class="{{$styleStatus[$order->status]}}"><span>{{$listStatus[$order->status]}}</span></td>
                </tr>
                <tr>
                    <td class="first-col">Ghi chú</td>
                    <td>{{$order->note}}</td>
                </tr>
                
                </tbody>
            </table>

            <table class="table order">
                <tbody>
                    <tr><th>Sản phẩm:</th></tr>
                    <?php 
                    foreach (json_decode($order->id_product) as $item) {
                        $product = getProductByIdHelper($item->id);
                        if ($product) {
                            $name = $product->name;
                            if ($product->type == 2 && !empty($item->variantId)) {
                                $variantID = $item->variantId;
                                $name .= HelperProduct::getNameAttributeByVariantId($variantID);
                            }
                    ?>
                   
                <tr>
                    <td class="first-col">{{$name}}</td>
                    <td>{{$item->val}}</td>
                </tr>
                <?php }} ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($("#laravel-notify").length > 0) {
        $("#laravel-notify").slideDown('fast').delay(5000).hide(0);
    }
});
</script>
@stop