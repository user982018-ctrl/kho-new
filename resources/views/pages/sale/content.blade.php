<?php

    $listSale = Helper::getListSaleOfLeaderGroup(); 
    $checkAll = isFullAccess(Auth::user()->role);
    $isLeadSale = Helper::isLeadSale(Auth::user()->role);  
    $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);     
    $flag = false;
    $flagAccess = false;
    $listSaleJson = '';

    if ($checkAll || $isLeadSale && $listSale) {
        $listSaleJson = $listSale->get()->select('id', 'real_name')->toJson();
        if (($listSale->count() > 0 &&  $checkAll) || $isLeadSale) {
            $flag = true;
        }
    }
    $groupIdOfLeadSale = Helper::getGroupOfLeadSale(Auth::user());
    $listStatus = Helper::getListStatus();
    $styleStatus = [
        0 => 'red',
        1 => 'white',
        2 => 'orange',
        3 => 'green',
    ];
?>
<style>
    .hidden {
        display: none;
    }
    .border-select-box-se {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 28px;
        user-select: none;
        -webkit-user-select: none;
        color: #444;
        line-height: 28px;
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
    }
    .mof-container {
        margin-top: 10px;
    }
    .TNModal:hover {
        cursor: pointer;
    }
    .box-TN {
        margin-left: 10px;
        height: 45px;
        overflow: hidden;
    }
    .box-TN a {
        cursor: zoom-out;
    }

    .m-header .text {
        padding: 0 var(--cui-card-cap-padding-x);
        color: #000;
        text-shadow: none !important;
        font-size: 16px;
        font-weight: bold;
        height: 100%;
        line-height: 30px;
        display: inline-block;
    }

    .modal-backdrop.in {
        opacity: -0.5;
    }

    a {
        cursor: pointer;
    }
    
    .modal-backdrop.fade.show {
        width: 100%;
        height: 100%;
    }

    #laravel-notify .notify {
        z-index: 1030;
    }
    .modal-backdrop-notify.show {
        opacity: 0;
    }
    #notify-modal .modal-header {
        border: unset;
        border-radius: unset;
        background: #4df54dcc;
    }

    #notify-modal .modal-content  {
        background: none;
        border: unset;
        border-radius: unset;
    }

    #notify-modal .modal-dialog {
        margin-right: 10px;
        width: 300px;
    }
    
    .loader img {
        position: fixed;
        right: 50%;
        top: 50%;
        z-index: 999;
    }
    .form-select {
        font-size: 14px;
    }

    /* .filter-order {
        display: none;
    } */
    input#daterange {
        text-align: left;
        color: #000;
        border: 1px solid var(--cui-form-select-border-color, #b1b7c1);
        border-radius: 0.375rem;
        width: 100%;
    }
    .mof-container, .txt-mof {
        background-color: transparent;
        height: 45px;
    }


    .mof-container {
        position: relative;
        height: 30px;
        width: 100%;
        float: left;
        background-color: white;
    }
    .ttgh6, .ttgh7 {
        width: 40px;
        color: #ff0000;
    }

    tbody tr.error{
        border: 3px solid #ff0000 !important;
    }
    tbody tr.success{
        border: 3px solid #08a322 !important;
    }
    
    th {
        cursor: move;
        border: 1px solid white;
    }

    .header.header-sticky {
        position: unset;
    }

    #sale-filter {
    transition: all 2s ease-out;
    }
    
    .header-filter, .header-filter-wraper {
        display: flex; justify-content: flex-end;
    }

    .header-filter-input {
        padding: 0;
    }

    @media (max-width: 576px) {
        .hidden-xs {
            display: none;
        }
        .m-header-wrap, .filter-order {
            padding: 10px;
        }

        .header-filter-input {
            padding: 0 15px;
        }
        .header-filter, .header-filter-wraper{
            display: block;
        }
    }

</style>                  

<link rel="stylesheet" href="{{asset('public/newCDN/css/all.min.css')}}" referrerpolicy="no-referrer" />
<link href="{{ asset('public/css/pages/sale.css'); }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

<style>
    .btn-sm {
        padding-top: 7px;
        padding-bottom: 4px;
        font-size: 11px;
        padding-right: 12px;
        font-weight: bold;
        height: 30px;
    }
    .select2-container {
        width: 100% !important;
    }
    body {
        font-family: Arial, Helvetica, sans-serif
    }
    .maintain-filter-main:hover {
        /* opacity: 0.2;
        border: 1px solid #ff0000; */
    }
    textarea.txt-mof {
        position: absolute;
        top: 0px;
        /* left: 0px; */
        /* height: 30px; */
        overflow-y: hidden;
        transition: ease 0.2s all;
        line-height: 20px;
        font-size: 11px;
        padding-top: 4px;
        background: none;
        border: none;
    }


    .home-sale-index:hover span{
        text-decoration: green wavy underline;
    }

    /* .select2-selection__rendered { */
    .result-TN-col .select-assign, .result-TN-col .select2-container--default .select2-selection--single , .result-TN {
        background-color: inherit !important;
        border: none;
    }

    .selectedClass .select2-container {
        box-shadow: rgb(0, 123, 255) 0px 1px 1px 1px;
    }

</style>

{{-- update filter --}}
<form id="saleForm" action="{{route('sale-index')}}" method="get" class="pb-4">
    {{ csrf_field() }}
    <div class="maintain-filter-main">
        <div class="m-header-wrap">
            <div class="m-header" style="top:150px;">
                <div class="row header-top-filter">
                    <div class="col-md-12 col-sm-12 col-lg-2 form-group">
                        <a class="home-sale-index" href="{{{route('sale-index')}}}"><span class="text">Sale tác nghiệp</span></a>
                    </div>
                    <div class="col-md-12 col-sm-12  col-lg-10 form-group header-filter-wraper">
                        
                        @if ($checkAll || $isLeadSale)
                        <div class="col-12 col-sm-3 col-md-3 col-lg-2 form-group" style="padding:0 15px;"> 
                            <select name="group" id="group-filter" class="border-select-box-se">
                                {{-- <option selected="selected" value="-1" >--Tất cả sale--</option> --}}
                                <option value="999">--Chọn nhóm--</option>
                                @if (isset($groups))
                                    @foreach($groups as $group)
                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        @endif

                        @if ($checkAll || $isLeadSale)
                        <div class="col-12 col-sm-3 col-md-3 col-lg-2 form-group" style="padding:0 15px;"> 
                            <select name="sale" id="sale-filter" class="border-select-box-se">
                                <option   value="999">--Chọn Sale--</option>
                                @if (isset($sales))
                                    @foreach($sales as $sale)
                                    <option value="{{$sale->id}}">{{($sale->real_name) ? : $sale->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        @endif

                        <div class="col-12 col-sm-6 col-md-3 form-group">
                            <input name="search" type="text"  value="{{ isset($search) ? $search : null}}" class="form-control" placeholder="Họ tên, số điện thoại">
                        </div>
                    
                        <div class="col-12 col-sm-6 col-md-3 col-lg-3 form-group" style="max-width: 180px;" >
                            <button class="btn btn-sm btn-primary" type="submit">
                                <i class="fa fa-search"></i>Tìm kiếm
                            </button>
                            <button id="zoom-filter" style="padding: 8px;" class="btn btn-sm btn-primary ml-1" type="button">
                                <i class="fa fa-angle-double-down" style="margin:0;"></i>
                            </button>
                        </div>

                        <div style="clear: both;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box-body">
        <div class="row mt-1 filter-order hidden">
            <div class="daterange col-xs-12 col-sm-6 col-md-2 form-group">
                <input id="daterange" class="btn" type="text" name="daterange" />
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="typeDate" id="typeDate-filter" class="border-select-box-se">       
                    <option value="999">--Kiểu ngày--</option>
                    @foreach ($typeDate as $type) 
                    <option value="{{$type['id']}}">{{($type['name']) ? : $type['name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="src-filter col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="src" id="src-filter" class="border-select-box-se">       
                    <option value="999">--Chọn nguồn--</option>
                    @foreach ($listSrc as $page) 
                    <option value="{{$page['id']}}">{{($page['name']) ? : $page['name']}}</option>
                    @endforeach
                </select>
            </div>

            @if ($checkAll  || $isLeadSale || $isLeadDigital)
            <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="mkt" id="mkt-filter" class="border-select-box-se">
                    <option value="999">--chọn Marketing--</option>
                    @foreach ($listMktUser->get() as $user)
                    <option value="{{$user->id}}">{{$user->name}} </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="product" id="product-filter" class="border-select-box-se">
                    <option value="999">--Chọn sản phẩm--</option>
                    @foreach ($listProduct as $product) 
                    <option value="{{$product->id}}">{{$product->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="statusTN" id="statusTN-filter" class="border-select-box-se">
                    <option value="999">--Chọn trạng thái Tác nghiệp--</option>
                    <option value="1">Chưa Tác Nghiệp</option>
                    <option value="2">Đã Tác Nghiệp</option>
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="resultTN" id="resultTN-filter" class="border-select-box-se">
                    <option value="999">--Tất cả Kết quả Tác nghiệp--</option>
                    @foreach ($callResults as $rs) 
                    <option value="{{$rs['id']}}">{{($rs['name']) ? : $rs['name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="type_customer" id="type_customer-filter" class="border-select-box-se">
                    <option value="999">--Tất cả Data--</option>
                    <option value="2">Hotline</option>
                    <option value="1">Data CSKH</option>
                    <option value="0">Data nóng</option>
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                <select name="status" id="status-filter" class="border-select-box-se">
                <option value="999">--Chọn trạng Thái giao hàng--</option>
                <option value="1">Chưa giao vận</option>
                <option value="2">Đang giao</option>
                <option value="3">Hoàn tất</option>
                <option value="0">Huỷ</option>
                </select>
            </div>

            @if ($listTypeTN)
            <div class="col-xs-12 form-group">
                @foreach ($listTypeTN as $item)
                <div class="dm-tac-nghiep">
                    <input type="radio" id="{{$item['data']->id}}" name="cateCall" value="{{$item['data']->id}}">
                    <span class="flag level-4"></span>
                    <label class="text" for="{{$item['data']->id}}">{{$item['data']->name}} ({{$item['yetTN']}}/{{$item['sum']}})</label><br>
                </div>
                @endforeach
                <div class="dm-tac-nghiep">
                    <span class="flag" style="background-color:#00ff48;"></span>
                    <label class="text">Tổng TN: <span id="sum-TN">0</span></label><br>
                </div>
            </div>
            @endif
        </div>
        
        <button class="hidden btn btn-sm btn-primary delete-data-SC" type="button">Xoá <span id="total-val" list_id="[]" data-total="0"></span></button>
        <div class="dragscroll1 tableFixHead" style="height: 819px; margin-top:15px;">      
            <table class="table table-bordered table-multi-select table-sale">
                <thead>
                    <tr class="drags-area">
                        <th style="top: 0.5px;">
                            @if ($checkAll)
                            <span class="chk-all" style="display: inline-block; min-width: 40px;">
                                <input id="checkAllId" type="checkbox">
                                <label for="checkAllId" ></label></span>
                            @else 
                                <span class="chk-all" style="display: inline-block; min-width: 40px;">
                                <label>&nbsp;</label></span>
                            @endif
                        </th>
                        <th class="text-center" style="top: 0.5px;">
                            <span style="display: inline-block; min-width: 200px;">Nguồn dữ liệu</span><br>
                            Ngày data về
                        </th>
                        <th class="text-center" style="top: 0.5px;"><span style="display: inline-block; width: 120px;">Nhân viên
                            <br>tư vấn</span></th>

                        <th class="text-left" style="top: 0.5px;">
                            <span class="text-center" style="display: inline-block; min-width: 150px; max-width: 200px;">Họ tên<br>
                                <span>Số điện thoại</span>
                            </span>
                        </th>
                        <th class="text-center hidden-xs" style="top: 0.5px;"><span class="td-message td-793">Tin nhắn</span></th>
                        <th class="text-center" style="top: 0.5px;"><span style="display: inline-block; min-width: 200px;">TN cần</span></th>
                        <th class="text-center" style="top: 0.5px;"><span style="display: inline-block; min-width: 200px;">Kết quả</span></th>
                        <th class="text-center hidden-xs" style="top: 0.5px;"><span>TN tiếp</span></th>
                        <th class="text-center" style="top: 0.5px;"><span>Sản phẩm - Số lượng - Đơn giá</span></th>
                        <th class="text-center hidden-xs" style="top: 0.5px;"><span>Thành tiền / CK</th>
                        <th class="text-center hidden-xs" style="top: 0.5px;"><span>Đặt cọc</span></th>
                        <th class="text-center" style="top: 0.5px;"><span style="display: inline-block; min-width: 120px;">Trạng thái giao hàng</span></th>
                    </tr>
                </thead>
                <tbody class="tbody-sale">
                    <?php $i = 1; $listHistory = [];
                    ?>
                    @foreach ($saleCare as $item)
                    <?php if ($item->assign_user == Auth::user()->id) {
                            $flagAccess = true;
                        }
                        $order = $item->orderNew;
                    ?>
                    <tr class="contact-row tr_{{$item->id}}">
                        <td class="text-center">
                            @if ($checkAll)
                            <span class="chk-item">
                                <input data-id="{{$item->id}}" class="chk-item-input" value="{{$item->id}}" type="checkbox" id="{{$item->id}}">
                                <label for="{{$item->id}}">{{$i}}</label>
                            </span>
                            @else 
                            <span class="chk-item">{{$i}}</span>
                            @endif  
                        </td>
                        <td class="text-center" style= "max-width: 200px">
                            <span><a target="_blank" href="{{$item->page_link}}">{{$item->page_name}}</a></span>
                            <br>
                            <span class="small-tip">(<span>{{date_format($item->created_at,"H:i d-m-Y ")}}</span>)</span>
                        </td>
                        
                        {{-- $checkAll: xoá data, đổi người
                        $isLeadSale: đổi người --}}
                        <td class="text-center result-TN-col">
                            <div class="text-right">
                                @if ($checkAll)
                                <a data-id="{{$item->id}}" title="Xóa data" class="btn-icon aoh removeBtn"><i class="fa fa-trash"></i></a>
                                @endif
                                @if ($checkAll || $isLeadSale)
                                <a data-id="{{$item->id}}" id="update-save-{{$item->id}}" class="update-assign-TN-sale btn-icon aoh">
                                    <i class="fa fa-save"></i>
                                </a>
                                @endif
                            </div>
                            <div id="assign-single-{{$item->id}}">{{($item->user) ? $item->user->real_name : ''}}</div>
                            @if ($checkAll || $isLeadSale)
                            <select id="assign-list-{{$item->id}}" class="select-assign hidden" name="assignTNSale_{{$item->id}}" data-sale_id="{{$item->id}}"
                                data-group_id={{$item->group_id}}
                                data-assign_id='<?php echo ($item->assign_user) ? $item->assign_user : -1;?>'>
                            
                            @if ($item->assign_user && $item->user && $item->user->status == 0) 
                                <option class="hide" value={{$item->user->id}}> {{$item->user->real_name}} </option>
                            @elseif (!$item->assign_user)
                                <option value="0">None </option>
                            @endif
                            </select>
                            @else
                            
                        @endif
                        </td>
                        <td>

                            @if ($checkAll || $isLeadSale || $flagAccess)
                            <?php $flagAccess = false; ?>
                            <div class="text-right">
                                <a title="Thông tin khách hàng" data-target="#updateData" data-toggle="modal"
                                    data-tnsale-id="{{$item->id}}" class="updateDataModal btn-icon aoh"><i class="fa fa-info"></i>
                                </a>

                                @if ($item->id_order_new)
                                <a class="orderModal btn-icon aoh save-order-{{$item->id}}" data-target="#createOrder" data-toggle="modal" title="Sửa đơn" data-tnsale-id="{{$item->id}}" data-id_order_new="{{$item->id_order_new}}"><i class="fa fa-edit"></i></a>
                                @else
                                    <a class="orderModal btn-icon aoh save-order-{{$item->id}}" data-target="#createOrder" data-toggle="modal" title="Chốt đơn" data-tnsale-id="{{$item->id}}" data-address="{{$item->address}}" data-name="{{$item->full_name}}" data-phone="{{$item->phone}}"><i class="fa fa-edit"></i></a>
                                @endif
                            </div>
                            @endif

                            <div>{{$item->full_name}}</div>
                            <a href="tel:{{$item->phone}}" style="width: calc(100% - 90px);">{{$item->phone}}</a>
                            <span style="width: 85px;">

                                @if ($item->old_customer == 1 || $item->has_old_order == 1)
                                <a data-target="#listDuplicate" data-toggle="modal" data-phone="{{$item->phone}}" title="Khách cũ, khách cũ" class="duplicate btn-icon">
                                    <i class="fa fa-heart" style="color:red;"></i>
                                </a>
                                @endif

                                @if ($item->is_duplicate)
                                <a data-target="#listDuplicate" data-toggle="modal" data-phone="{{$item->phone}}" title="Trùng só điện thoại" class="duplicate btn-icon">
                                    <svg  class="icon me-2" style="color: #ff0000">
                                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-copy')}}"></use>
                                    </svg>
                                </a>
                                @endif
                            </span>
                        </td>
                        <td class="hidden-xs" style="max-width: 100px;">
                            <span style="cursor: pointer; overflow: hidden; max-height: 100px; display: block;">
                                {{$item->messages}}
                            </span>
                        </td>
                        <td class=" type-TN" style="padding-bottom: 10px;">

                            @if (!$item->type_TN)
                                @if (!$item->old_customer)
                                <span class="fb ttgh7" style="cursor: pointer; width: calc(100% - 60px);">Data nóng</span> 
                                @elseif ($item->old_customer == 1)
                                <span class="fb" style="cursor: pointer; width: calc(100% - 60px);">CSKH</span> 
                                @elseif ($item->old_customer == 2)
                                <span class="fb" style="cursor: pointer; width: calc(100% - 60px);">Hotline</span> 
                                @endif
                            @else
                                <span class="fb <?= ($item->has_TN) ?: 'ttgh7' ?>" style="cursor: pointer; width: calc(100% - 60px);"> {{$item->typeTN->name}}</span>
                            @endif

                            <span class="box-TN">
                                <a style="color: rgb(64, 11, 209) !important; text-decoration: underline rgb(64, 11, 209) !important; font-style:italic !important;" class="TNHistoryModal" data-target="#TNHistory" data-tnsale_id="{{$item->id}}" data-toggle="modal" title="Lịch Sử TN">
                                    <i class="fa fa-history" style="color:rgb(64, 11, 209);"></i>
                                </a>
                            </span>
                            <div class="mof-container TNModal" data-target="#TN" data-tnsale_id="{{$item->id}}" data-toggle="modal" title="Tác Nghiệp Ngay">
                                <div data-id="{{$item->id}}" id="TNSale_{{$item->id}}" rows="2" cols="20" class="divTN form-control txt-mof txt-dotted"
                                    data-content="Tối đa 500 ký tự" data-trigger="focus" data-toggle="popover">
                                </div>
                            </div>
                        </td>

                        <td class="result-TN-col" style="min-width:100px">
                            <div class="text-right">
                                @if (isset($item->id_order_new))
                                <a target="_blank" class="btn-icon aoh" href="{{route('view-order', ['id' => $item->id_order_new])}}" title="Xem lịch sử xem thông tin số"><i style="font-size:14px;" class="fa fa-history"></i></a>
                                @endif
                            </div>
                            @if ($item->type_TN)
                                <?php $listCallByTypeTN = Helper::listCallByTypeTN($item->type_TN); ?>
                                @if ($listCallByTypeTN) 
                                <select data-id="{{$item->id}}"  class="hidden result-TN" tabindex="-1" title="" >
                                    <option value="-1">--Chọn--</option>
                                    @foreach ($listCallByTypeTN as $call)
                                        <option value="{{$call->id}}" <?= ($item->result_call == $call->id) ? 'selected' : '';?>>{{$call->callResult->name}}</option>
                                    @endforeach
                                </select>
                                @endif
                            @endif
                            <div class="text-left">
                                @if ($order)
                                    <br>{{date_format($order->created_at,"H:i d-m-Y ")}}
                                @endif
                            </div>
                        </td>

                        <td class="hidden-xs" style="min-width:120px">
                            <div class="text-right">
                                <span class="next-TN">
                                    @if ($item->result_call && $item->result_call != -1)
                                    {{($item->resultCall) ? $item->resultCall->thenCall->name : ''}}
                                    @endif
                                </span>
                                <a title="Sửa lịch tác nghiệp" data-target="#updateCalendarTN" data-toggle="modal"
                                    data-timeWakeup="{{$item->time_wakeup_TN}}" data-iddd="{{$item->id}}" class="calendarModal btn-icon aoh">
                                    <i class="fa fa-calendar"></i>
                                </a>
                            </div>
                            <br>
                        </td>

                        <td class="text-left" style="min-width: 250px">
                            <table class="tb-in-sp">
                                <tbody class="bodyProduct" data-id="{{$item->id}}" data-order_new="{{$item->id_order_new ?? 0}}">
                                </tbody>
                            </table>
                        </td>

                        <td class="no-wrap area3 text-right hidden-xs">
                            @if ($order)
                            <table class="tb-in-sp">
                                <tbody>
                                    <tr>
                                        <td title="Tổng tiền đơn hàng" style="font-weight: bold; font-size: 13px;">
                                            {{number_format($order->total)}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @endif
                        </td>

                        <td class="hidden-xs">
                        </td>

                        <td class="text-center">
                            <div class="small-tip">
                                @if ($item->orderNew)
                                {{$listStatus[$item->orderNew->status]}} <br>

                                    @if ($item->orderNew->shippingOrder)
                                    <a  title="sửa" target="blank" href="{{route('detai-shipping-order',['id'=>$item->orderNew->shippingOrder->id])}}" role="button"> {{$item->orderNew->shippingOrder->order_code}}</a>
                                    @endif
                                
                                @endif
                            </div>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $saleCare->appends(request()->input())->links() }}
    </div>

</form>
<div style="height: 100px;"></div>
{{-- end update filter --}}

{{-- thông báo --}}
<div id="loader-overlay">
    <div class="loader"></div>
</div>
<!-- Trigger the modal with a button -->
<a data-toggle="modal" data-target="#myModal" class="tao-don-fixed">
    <i class="fa fa-edit"></i>
    <div class="text">Tạo TN</div>
</a>
{{-- <a href="{{route('add-orders')}}" class="btn btn-primary" data-toggle="modal" data-target="#myModal" role="button">+ Thêm đơn</a>   --}}
    <!-- Modal -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Tạo tác nghiệp sale</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe src="{{route('sale-add')}}" frameborder="0"></iframe>
        </div>
    </div>
</div>
<div id="updateData" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Thông tin khách hàng</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe frameborder="0"></iframe>
        </div>
    </div>
</div>
<div id="createOrder" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Thao tác đơn hàng</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe frameborder="0"></iframe>
        </div>
    </div>
</div>

<div id="TN" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Tác nghiệp hôm nay {{date("d-m-Y")}}</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

<div id="listDuplicate" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Danh sách data trùng số điện thoại</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

<div id="TNHistory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Lịch sử Tác Nghiệp</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

<div id="updateCalendarTN" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Sửa lịch tác nghiệp</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <iframe frameborder="0"></iframe>
        </div>
    </div>
</div>
<div class="modal fade" id="notify-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" style="color: seagreen;"><p style="margin:0">Lưu data thành công</p></h6>
            <button style="border: none;" type="button" id="close-modal-notify" class="close" data-dismiss="modal" >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
    </div>
</div>

<script src="{{asset('public/newCDN/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<script>
    $('.orderModal').on('click', function () {
        var idOrderNew = $(this).data('id_order_new');
        var TNSaleId = $(this).data('tnsale-id');

        if (idOrderNew) {
            var link = "{{URL::to('/update-order/')}}";
            $("#createOrder iframe").attr("src", link + '/' + idOrderNew);
        } else {
            var phone = $(this).data('phone');
            var name = $(this).data('name');
            var address = $(this).data('address');

            var param = 'saleCareId=' + TNSaleId + '&phone=' + phone + '&name=' + name + '&address=' + address ;
            var link = "{{URL::to('/them-don-hang/')}}";
            $("#createOrder iframe").attr("src", link + '?' + param);

            //cập nhật TN Sale
            (function( $ ){
            $.fn.getIdOrderNew = function() {
                setTimeout(function() {
                    var _token  = $("input[name='_token']").val();
                    $.ajax({
                        url: "{{ route('get-salecare-idorder-new') }}",
                        type: 'POST',
                        data: {
                            _token: _token,
                            TNSaleId,
                        },
                        success: function(data) {
                            if (data.id_order_new) {
                                if ($('.tr_' + TNSaleId + ' .id-order-new a').length == 0) {
                                    var td = $('.tr_' + TNSaleId + ' .id-order-new');
                                    td.wrapInner('<a href="' + data.link + '">' + data.id_order_new + '</a>');

                                    var aCreate = $('.tr_' + TNSaleId + ' td div a.orderModal');
                                    aCreate.data('id_order_new',  data.id_order_new);
                                    aCreate.attr('title', 'Sửa đơn');
                                }
                            
                            } 
                        }
                    });
           
                }, 3000);
            }; 
            })( jQuery );

            $('#createOrder').on('click', function () {
                $.fn.getIdOrderNew();
            });
           

            $('#close-main').on('click', function () {
                $.fn.getIdOrderNew();
            });
        }

        
        // var link = "{{URL::to('/update-order')}}";
        // $("#createOrder iframe").attr("src", link + '/' + myBookId);
    });

    $('.TNModal').on('click', function () {
        var saleId = $(this).data('tnsale_id');
        var link = "{{URL::to('/sale-view-luu-TN-box')}}";
        $("#TN iframe").attr("src", link + '/' + saleId);
    });

    $('.TNHistoryModal').on('click', function () {
        var saleId = $(this).data('tnsale_id');
        var link = "{{URL::to('/sale-hien-thi-TN-box')}}";
        console.log(link + '/' + saleId);
        $("#TNHistory iframe").attr("src", link + '/' + saleId);
    });

    $('.duplicate').on('click', function () {
        var phone = $(this).data('phone');
        var link = "{{URL::to('/danh-sach-so-trung')}}";
        $("#listDuplicate iframe").attr("src", link + '/' + phone);
    });

    $('.calendarModal').on('click', function () {
        var id = $(this).data('iddd');
        console.log(id);
        var link = "{{URL::to('/view-hen-lich-TN')}}";
        $("#updateCalendarTN iframe").attr("src", link + '/' + id);
    });
    
    // $('.select2-choice').on('click', function () {
    //     var id = $(this).data('id');
    //     $(this).parent().toggleClass("select-dropdown-show");
    //     console.log(id);
    // });
    
    // $(".select2-choice, .list-call").click(function(e){
    //     e.stopPropagation();
    // });

    // $(document).click(function(e){
    //     $(".select-dropdown-show").removeClass('select-dropdown-show');
    // });
    
    $("#close-modal-notify").click(function() {
        $('#notify-modal').modal("hide");
    });
    $(".option-product").click(function() {
        $('.body').css("opacity", '0.5');
        $('.loader').show();
        let id      = $(this).data("call-id");
        let name    = $(this).data("call-name");
        var _token  = $("input[name='_token']").val();
        var itemId  = $(this).data("call-item-id");
        console.log(id)
        $('.select2-container').removeClass("select-dropdown-show");

        $.ajax({
            url: "{{ route('sale-save-ajax') }}",
            type: 'POST',
            data: {
                _token: _token,
                itemId,
                id,
                name
            },
            success: function(data) {
                $('.body').css("opacity", '1');
                if (!data.error) {
                    $('#notify-modal').modal('show');
                    if ($('.modal-backdrop-notify').length === 0) {
                        $('.modal-backdrop').addClass('modal-backdrop-notify');
                    }

                    str         = 'span.list-call-' + itemId;
                    strNextStep = 'td.next-step-' + itemId;
                    $(str).text(name);
                    $(strNextStep).text('Gọi lần ' + data.data.next_step);

                    setTimeout(function() { 
                        $('#notify-modal').modal("hide");
                    }, 20000);
                } else {
                    alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                }
                $('.loader').hide();
            }
        });
    });
 
</script>

<script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>
<script>
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
</script>
<script>
    function filterFunction(id) {
        var input, filter, ul, li, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        div = document.getElementById(id);
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
</script>

<script>
    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results) {
            return results[1];
        }
        return 0;
    }

    let token = $.urlParam('_token') 
    if (token) {
        $('.filter-order').removeClass('hidden');
        $('#zoom-filter').children('i').addClass('fa-angle-double-up');
        $('#zoom-filter').children('i').removeClass('fa-angle-double-down');
    }

    let resultTN = $.urlParam('resultTN') 
    if (resultTN && resultTN != 999) {
        $('#resultTN-filter option[value=' + resultTN +']').attr('selected','selected');
        $('#resultTN-filter').parent().addClass('selectedClass');
    }

    let sale = $.urlParam('sale') 
    if (sale && sale != 999) {
        $('#sale-filter option[value=' + sale +']').attr('selected','selected');
        $('#sale-filter').parent().addClass('selectedClass');
    }

    let mkt = $.urlParam('mkt') 
    if (mkt && mkt != 999) {
        $('#mkt-filter option[value=' + mkt +']').attr('selected','selected');
        $('#mkt-filter').parent().addClass('selectedClass');
    }

    let src = $.urlParam('src') 
    if (src && src != 999) {
        $('#src-filter option[value=' + src +']').attr('selected','selected');
        $('#src-filter').parent().addClass('selectedClass');
    }

    let typeCustomer = $.urlParam('type_customer') 
    if (typeCustomer && typeCustomer != 999) {
        $('#type_customer-filter option[value=' + typeCustomer +']').attr('selected','selected');
        $('#type_customer-filter').parent().addClass('selectedClass');
    }

    let time = $.urlParam('daterange') 
    if (time) {
        time = decodeURIComponent(time)
        time = time.replace('+-+', ' - ') //loại bỏ khoảng trắng
        $('input[name="daterange"]').val(time)
    }

    let group = $.urlParam('group') 
    if (group && group != 999) {
        $('#group-filter option[value="' + group +'"]').attr('selected','selected');
    }

    let status = $.urlParam('status') 
    if (status && status != 999) {
        $('#status-filter option[value=' + status +']').attr('selected','selected');
        $('#status-filter').parent().addClass('selectedClass');
    }

    let typeDate = $.urlParam('typeDate') 
    if (typeDate && typeDate != 999) {
        $('#typeDate-filter option[value="' + typeDate +'"]').attr('selected','selected');
        $('#typeDate-filter').parent().addClass('selectedClass');
    }

    let statusTN = $.urlParam('statusTN') 
    if (statusTN && statusTN != 999) {
        $('#statusTN-filter option[value="' + statusTN +'"]').attr('selected','selected');
        $('#statusTN-filter').parent().addClass('selectedClass');
    }

    let product = $.urlParam('product') 
    if (product && product != 999) {
        $('#product-filter option[value="' + product +'"]').attr('selected','selected');
        $('#product-filter').parent().addClass('selectedClass');
    }

    let search = $.urlParam('search')
    if (search) {
        search = decodeURIComponent(search);
        search = search.replaceAll('+', " ");
        $('input[name="search"]').val(search)
    }

    let cateCall = $.urlParam('cateCall')
    if (cateCall) {
        cateCall = decodeURIComponent(cateCall);
        var $radios = $('input:radio[name=cateCall]');
        if($radios.is(':checked') === false) {
            $radios.filter('[value=' + cateCall +']').prop('checked', true);
        }
        var radioCateCall =  $('input:radio[name="cateCall"]').filter('[value="' + cateCall +'"]');
        radioCateCall.attr('checked', true);
        radioCateCall.parent().addClass('selected');
    }
</script>

<script>
    $.fn.myFunc = function(id, type){
        
        if (type == 1) {
            $('.body').css("opacity", '0.5');
            $('.loader').show();
        }

        // var id = $(this).data("id");
        var textArea = '#TNSale_' + id;
        var textTN   = $(textArea).val();
        var _token   = $("input[name='_token']").val();

        $.ajax({
            url: "{{route('update-salecare-TNcan')}}",
            type: 'POST',
            data: {
                _token: _token,
                id,
                textTN
            },
            success: function(data) {
                if (type == 1) {
                    $('.body').css("opacity", '1');

                    var tr = '.tr_' + id;
                    if (!data.error) {
                        $('#notify-modal').modal('show');
                        if ($('.modal-backdrop-notify').length === 0) {
                            $('.modal-backdrop').addClass('modal-backdrop-notify');
                            $('#notify-modal .modal-title').html('Cập nhật data thành công!');
                        }

                        $(tr).addClass('success');
                        setTimeout(function() { 
                            $('#notify-modal').modal("hide");
                            $(tr).removeClass('success');
                        }, 2000);
                    } else {
                        alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                        $(tr).addClass('error');
                        setTimeout(function() { 
                            $(tr).removeClass('error');
                        }, 3000);
                    }
                    $('.loader').hide();
                }
            }
        });
    }
    $('.result-TN').on('change', function() {
        var  id = $(this).data("id");
        var value = this.value;
        var _token   = $("input[name='_token']").val();
        $('#loader-overlay').css('display', 'flex');
        
        $.ajax({
            url: "{{route('update-salecare-result')}}",
            type: 'POST',
            data: {
                _token: _token,
                id,
                value
            },
            success: function(data) {
                var tr = '.tr_' + id;
                if (!data.error) {
                    var trId = 'tr.tr_' + id;
                    if (data.classHasTN) {
                        $(trId + ' .type-TN span.fb').removeClass('ttgh7');
                    } else {
                        $(trId + ' .type-TN span.fb').addClass('ttgh7');
                    }

                    $(trId + ' .next-TN').text(data.nextTN);
                    
                    $('#notify-modal').modal('show');
                    if ($('.modal-backdrop-notify').length === 0) {
                        $('.modal-backdrop').addClass('modal-backdrop-notify');  
                    } 

                    $('#notify-modal .modal-title').text('Cập nhật data thành công!');

                    setTimeout(function() {
                        $('#notify-modal .modal-title').text('');
                        $('#notify-modal').modal("hide");
                    }, 2000);
                } else {
                    alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                }
                $('#loader-overlay').css('display', 'none');
            }
        });
    });
    $('.update-assign-TN-sale').click(function(){
        $('#loader-overlay').css('display', 'flex');
        var id = $(this).data("id");
        var textArea = "select[name='assignTNSale_" + id + "']";
        var assignSale  = $(textArea).val();
        var _token   = $("input[name='_token']").val();

        $.ajax({
            url: "{{route('update-salecare-assign')}}",
            type: 'POST',
            data: {
                _token: _token,
                id,
                assignSale
            },
            success: function(data) {
                var tr = '.tr_' + id;
                if (!data.error) {
                    $('#notify-modal').modal('show');
                    if ($('.modal-backdrop-notify').length === 0) {
                        $('.modal-backdrop').addClass('modal-backdrop-notify');
                    }

                    $('#notify-modal .modal-title').text('Cập nhật data thành công!');

                    setTimeout(function() {
                        $('#notify-modal .modal-title').text('')
                        $('#notify-modal').modal("hide");
                    }, 2000);
                } else {
                    alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                }
                $('#loader-overlay').css('display', 'none');
            }
        });
    });
    $('.update-TN-sale').click(function(){
        var id = $(this).data("id");
        var type = 1
        $('.body').myFunc(id, type); 
    });

    $("textarea.txt-mof").keyup(function(){
        var id = $(this).data("id");
        var type = 2
        $('.body').myFunc(id, type); 
    });
    $('#daterange').click(function(){
        $("input[name='search']").val('');
    })
</script>

<script type="text/javascript">
    function setZoom() {
      if (window.matchMedia('(min-width: 1180px) and (max-width: 2000px)').matches) {
        document.body.style.zoom = "90%";
      } else {
        document.body.style.zoom = "100%";
      }
    }
   
    // Call the function to set the zoom on page load
    // setZoom();
   
    // Handle the window resize event
    window.addEventListener('resize', setZoom);
</script>

{{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        $('#sale-filter').select2();
        $('#typeData').select2();
        $('#careOrder').select2();
        $('#srcData').select2();
        $('#productFilter').select2();
        $('#statusTN').select2();
        $('#resultTN').select2();
        $('#statusOrderShip').select2();
        $('#statusDeal').select2();
        $('.result-TN').select2();
        $('#src-filter').select2();
        $('#mkt-filter').select2();
        $('#status-filter').select2();
        $('#type_customer-filter').select2();
        $('#resultTN-filter').select2();
        $('#typeDate-filter').select2();
        $('#statusTN-filter').select2();
        $('#product-filter').select2();
        $('#group-filter').select2();
        
    });
</script>

<script>
    $("input[name='cateCall']") // select the radio by its id
        .change(function(){ // bind a function to the change event
            if( $(this).is(":checked") ){ // check if the radio is checked
                $('.dm-tac-nghiep').removeClass('selected');
                $(this).parent().addClass('selected');
            }
    });

    $("input[name='cateCall']") // select the radio by its id
        .click(function(){ // bind a function to the change event

        var typeTN = $(this).val();
        var url = window.location.href;
        paramsString = url.substring(url.lastIndexOf('?') + 1);

        var searchParams = new URLSearchParams(paramsString);
        var stringParam = '';
        var flag = false;
        for (let p of searchParams) {
            console.log(p)
            if (p[0] == 'daterange') {
                stringParam += '&daterange=' + encodeURIComponent(p[1]);
            } else if (p[1] != 999 && p[1] != '') {
               
                if (p[0] == 'cateCall') {
                    flag = true;
                    stringParam += '&' + p[0] + '=' + typeTN;
                } else {
                    var charStr = '';
                    if (stringParam == '') {
                        charStr = '?';
                        stringParam += charStr
                    } else {
                        charStr = '&';
                    }

                    stringParam += charStr + p[0] + '=' + p[1];
                }
            } 
        }

        var daterange = $("input[name='daterange']").val();
        var _token   = $("input[name='_token']").val();
        if (!flag) {
            if (searchParams.size == 1) {
                stringParam += '?_token=' + _token + '&daterange=' + encodeURIComponent(daterange);
            }

            stringParam += '&cateCall=' + typeTN;
        }

        var baseLink = location.href.slice(0,location.href.lastIndexOf("/"));

        refeshLink = baseLink + '/tac-nghiep-sale' + stringParam;
        window.history.pushState("object or string", "Title", refeshLink);

        var typeDate = $("#typeDate-filter :selected").val();
        var src = $("#src-filter :selected").val();
        var mkt = $("#mkt-filter :selected").val();
        var product = $("#product-filter :selected").val();
        var statusTN = $("#statusTN-filter :selected").val();
        var resultTN = $("#resultTN-filter :selected").val();
        var type_customer = $("#type_customer-filter :selected").val();
        var status = $("#status-filter :selected").val();
        var sale = $("#sale-filter :selected").val();

        var link = "{{URL::to('/tac-nghiep-sale')}}";
        var isAjax = true;

        $('.body').css("opacity", '0.5');
        $('.loader').show();
        var listSale = '<?php echo json_encode($listSale); ?>';
            $.ajax({
            url: link,
            type: "GET",
            data: {
                cateCall:typeTN, daterange, typeDate, src, mkt, product, statusTN, resultTN, type_customer, status, sale,
                isAjax,
                _token: _token,
            },
            success: function (data) {
                $('.body').css("opacity", '1');

                var rs = '';
                var i = 0;
                if (!data.error) {
                    var listSale = data.listSale;
                    data.dataSale.forEach(element => {
                        i++;
                        rs += '<tr class="contact-row tr_' + element.id + '">';
                        rs += '<td class="text-center">';
                        rs += '<?php $checkAll = true; if ($checkAll) { ?>';
                        rs +=     '<span class="chk-item">';
                        rs +=        '<input data-id="' + element.id + '" value="' + element.id + '" class="chk-item-input" type="checkbox" id="' + element.id + '">';
                        rs +=        '<label for="' + element.id + '">' + i + '</label>';
                        rs +=    '</span>';
                        rs +=    '<?php } else { ?> ';
                        rs +=  '<span class="chk-item">' + i + '</span>'
                        rs +=    '<?php } ?>';
                        rs +=    '</td>';

                        rs += '<td class="text-center hidden-xs">'; 
                        rs += '<span class=" cancel-col">';
                        rs +=    '<a target="_blank" href="' + element.page_link + '">' + element.page_name + '</a>';
                        rs +=            '</span>';
                
                        rs += '<br>';

                        var date = new Date(element.created_at);
                        const formatter = new Intl.DateTimeFormat('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric'});
                        const formattedTime = formatter.format(date);

                        rs += '<span class="small-tip">(<span>' + formattedTime + '</span>)</span>';
                        rs += '</td>';

                        var AuthId = '<?php echo Auth::user()->id;?>';
                        var checkAll = '<?php echo isFullAccess(Auth::user()->role)?>';
                        var isLeadSale = '<?php echo $isLeadSale;?>';

                        rs += '<td class="text-center hidden-xs result-TN-col">';

                        if (checkAll || isLeadSale) {
                            rs += '<div class="text-right">';
                            
                            if (checkAll) {
                                rs += '<a data-id="' + element.id +'" title="Xóa data" class="btn-icon aoh removeBtn">';
                                rs += '<i class="fa fa-trash"></i>';
                                rs += '</a>';
                            }
                            
                            // rs += '<a data-id="' + element.id +'" title="Xóa data" class="btn-icon aoh removeBtn">';
                            // rs += '<i class="fa fa-trash"></i>';
                            // rs += '</a>';
                            rs += '<a title="chỉ định Sale nhận data" data-id="' + element.id +'" class="update-assign-TN-sale btn-icon aoh">';
                            rs += '<i class="fa fa-save"></i>';
                            rs += '</a>';
                            rs += '</div>';
                            rs += '<div>';

                            rs += '<div class="mof-container">';
                            rs += '<select class="select-assign bg-dropdown" name="assignTNSale_' + element.id +'">';
                            var flag = false;
                            var nameSale = selected = '';

                            listSale.forEach(user => {
                                if (user.id == element.assign_user) {
                                    selected = 'selected';
                                    rs += '<option selected value="' + user.id + '">' + user.real_name + '</option>';
                                } else {
                                    rs += '<option value="' + user.id + '">' + user.real_name + '</option>';
                                }

                                if (user.id == element.assign_user) {
                                    flag = true;
                                    // break;
                                    nameSale = user.real_name;
                                }
                            });

                            if (!flag) {
                                rs += '<option selected value="0">None </option>';
                            }

                            rs += '</select>';
                            rs += '</div>';
                            rs += '<div style="clear: both;"></div>';
                            rs += '</div>';

                        } else { 
                            rs += '<div>' + '<?php echo Auth::user()->real_name; ?>' + '</div>';
                        }

                        rs += '</td>';
                        rs += '<td class="area1" title="FROM_FACEBOOK_MESSAGE">';

                        if (checkAll || isLeadSale) {
                            rs += '<div class="text-right">';
                            rs += '<a title="Thông tin khách hàng" data-target="#updateData" data-toggle="modal"';
                            rs += 'data-tnsale-id="' + element.id + '" class="updateDataModal btn-icon aoh"><i class="fa fa-info"></i></a>';
                            if (element.id_order_new) {
                                rs += '<a data-target="#createOrder" data-toggle="modal" title="Sửa đơn"';
                                rs += 'data-tnsale-id="' + element.id + '" data-id_order_new="' + element.id_order_new;
                                rs += '" class="orderModal btn-icon aoh"><i class="fa fa-edit"></i></a>';
                            } else {
                                rs += '<a data-target="#createOrder" data-toggle="modal" title="Chốt đơn" data-tnsale-id="' + element.id;
                                rs += '" data-address="' + element.address + '" data-name="' + element.full_name + '" data-phone="' + element.phone;
                                rs += '" class=" orderModal btn-icon aoh"><i class="fa fa-edit"></i></a>';
                            }
                            rs += '</div>';
                        }

                        rs += '<div class="" style="text-overflow: ellipsis;">' + element.full_name + '</div>';
                        rs += '<a href="tel:' + element.phone + '" style="width: calc(100% - 90px);">' + element.phone;
                            
                        rs += '</a>';
                        rs += '<span class="text-right" style="width: 85px;">';

                        if (element.old_customer == 1 || element.has_old_order == 1) {
                            rs += '<a  data-target="#listDuplicate" data-toggle="modal" data-phone="' + element.phone + '" title="Khách cũ, khách cũ" class="duplicate btn-icon">';
                                rs += '<i class="fa fa-heart" style="color:red;"></i>';
                            rs += '</a>';
                        }

                        if (element.is_duplicate) {
                            rs += '<a data-target="#listDuplicate" data-toggle="modal" data-phone="' + element.phone + '" title="Trùng só điện thoại" class="duplicate btn-icon">';
                                rs += '<svg  class="icon me-2" style="color: #ff0000">';
                                    rs += '<use xlink:href="<?php echo asset("public/vendors/@coreui/icons/svg/free.svg#cil-copy")?>"></use>';
                                rs += '</svg>';
                            rs += '</a>';
                        }
                        
                        rs += '</span>';
                        rs += '</div>';
                        rs += '</td>';

                        rs += '<td class="area1 hidden-xs td-5055" style="max-width: 100px;">';
                        rs += '<span style="cursor: pointer; overflow: hidden; max-height: 100px; display: block;">';
                        if (element.messages) {
                            rs += element.messages
                        }
                        
                        rs += '</span>';
                        rs += '</td>';

                        rs += '<td class="area2 hidden-xs type-TN" style="padding-bottom: 10px;">';
                        if (!element.type_TN) {
                            if (!element.old_customer) {
                                rs += '<span class="fb ttgh7" style="cursor: pointer; width: calc(100% - 60px);">Data nóng</span>';
                            } else if (element.old_customer == 1) {
                                rs += '<span class="fb" style="cursor: pointer; width: calc(100% - 60px);">CSKH</span>';
                            } else if (element.old_customer == 2) {
                                rs += '<span class="fb" style="cursor: pointer; width: calc(100% - 60px);">Hotline</span>';
                            }
                        } else {
                            var classHasTN = '';
                            if (!element.has_TN) {
                                classHasTN = 'ttgh7';
                            }
                            rs += '<span class="fb' + classHasTN + '" style="cursor: pointer; width: calc(100% - 60px);">';
                            rs += element.typeTN.name + '</span>';
                        }

                        rs += '<span class="box-TN" >';
                        rs += '<a style="color: rgb(64, 11, 209) !important; text-decoration: underline rgb(64, 11, 209) !important; font-style:italic !important;" class="TNHistoryModal" data-target="#TNHistory"';
                        rs += 'data-tnsale_id="' + element.id + '" data-toggle="modal" title="Lịch Sử TN">';
                        rs += '<i class="fa fa-history" style="color:rgb(64, 11, 209);"></i></a>';
                        rs += '</span>';
                        rs += '<div class="mof-container TNModal"  data-target="#TN" data-tnsale_id="' + element.id + '" data-toggle="modal" title="Tác Nghiệp Ngay">';
                        rs += '<div data-id="' + element.id + '" id="TNSale_' + element.id + '" rows="2" cols="20" class="form-control txt-mof txt-dotted"';
                        rs += 'data-content="Tối đa 500 ký tự" data-trigger="focus" data-toggle="popover">';
                                
                        if (element.history) {
                            rs += element.history;
                        } 
                        rs += '</div>';
                        rs += '</div>';
                        rs += '<div style="clear: both;"></div>';
                        rs += '</td>';

                        rs += '<td class="result-TN-col area2 no-wrap fix_brower_continue_let_off" style="min-width:100px">';
                        rs += '<div class="text-right">';

                        if (element.id_order_new) {
                            rs += '<a target="_blank" class="btn-icon aoh" href="<?php echo route("view-order", ["id" => ' + element.id_order_new + ']); ?>" title="Xem lịch sử xem thông tin số">';
                            rs += '<i style="font-size:14px;" class="fa fa-history"></i></a>';
                        }

                        rs += '</div>';
                        var thenCallName = '';
                        if (element.type_TN) {

                            rs += '<select data-id="' + element.id + '" class="hidden result-TN select-assign ddlpb dis_val" tabindex="-1">';
                            rs += '<option value="-1">--Chọn--</option>';

                            element.listCallByTypeTN.forEach(call => {
                                if (call.id == element.result_call) {
                                    thenCallName = call.thenCallName;
                                    rs += '<option selected value="' + call.id + '">' + call.name + '</option>';
                                } else {
                                    rs += '<option  value="' + call.id + '">' + call.name + '</option>';
                                }
                            });

                            rs += '</select>';
                        }

                        rs += '<div class="small-tip text-left">';
                        if (element.orderNew) {
                            rs += '<br>' + element.orderNew.created_at;
                        }

                        rs += '</div>';
                        rs += '</td>';

                        rs += '<td class="no-wrap area2 no-wrap  hidden-xs " style="min-width:120px">';
                        rs += '<span class="next-TN">';

                        rs += thenCallName;
                                       
                        rs += '</span>';

                        rs += '<div class="text-right">';
                        rs += '<a title="Sửa lịch tác nghiệp" data-target="#updateCalendarTN" data-toggle="modal"';
                        rs += 'data-timeWakeup="' + element.time_wakeup_TN + '" data-iddd="' + element.id + '" ';
                        rs += 'class="calendarModal btn-icon aoh">';
                        rs += '<i class="fa fa-calendar"></i>';
                        rs += '</a>';
                        rs += '</div>';
                        rs += '<br>';
                                   
                        rs += '</td>';

                        rs += '<td class="text-left area3 hidden-xs">';
                        rs += '<span id="dnn_ctr1441_Main_SaleTacNghiep_rptData__DonhangTenSanPhams_0">';
                        rs += '<table class="tb-in-sp">';
                        rs += '<tbody>';
              
                        if (element.order_new) {
                            element.order_new.listProduct.forEach(order => {
                                rs += '<tr><td><span class="ten-sp" style="text-overflow:ellipsis">' + order.name + '</span></td>';
                                rs += '  <td class="text-center no-wrap">&nbsp; x ' + order.cartQty + ' &nbsp;</td><td class="text-right">';
                                rs += order.price + '</td>';
                                rs += ' </tr>';
                            });
                        }     
                        rs += ' </tbody>';
                        rs += '</table></span>';
                        rs += ' </td>';

                        rs += '<td class="no-wrap area3 text-right hidden-xs">';
                        if (element.order_new) {
                            rs += '<table class="tb-in-sp ">';
                                rs += '<tbody>';
                                    rs += '<tr>';
                                        rs += '<td title="Tổng tiền đơn hàng" style="font-weight: bold; font-size: 13px;">';
                                        rs += new Intl.NumberFormat().format(element.order_new.total);
                                        rs += '</td>';
                                    rs += '</tr>';
                                rs += '</tbody>';
                            rs += '</table>';
                        }
                        rs += '</td>';

                        rs += '<td class="no-wrap area3 text-right hidden-xs">';
                        rs += '<span></span>';
                        rs += '</td>';

                        rs += '<td class="text-center area4">';
                        rs += '<span class=" no-wrap">';
                        rs += '<div class="small-tip">';

                        var listStatus = '<?php echo json_encode($listStatus); ?>';
                        if (element.order_new) {
                            rs += JSON.parse(listStatus)[element.order_new.status] + '<br>';

                            if (element.order_new.shipping_order) {
                                rs += '<a title="sửa" target="blank" href="' + location.href.slice(0,location.href.lastIndexOf("/")) + '/chi-tiet-van-don/' + element.order_new.shipping_order.id + '" role="button">' + element.order_new.shipping_order.order_code + '</a>';
                        
                            }
                        }
                                    
                        rs += '</div>';
                        rs += '</span>';
                        rs += '</a>';
                            
                        rs += '</td>';
                        rs += '</tr>';
                       
                    });

                    setTimeout(function() {
                        $('.result-TN').select2();
                    }, 3000);
                    $('.tbody-sale').html(rs);
                    
                    var linkJs = '<?php echo asset("public/js/page/sale.js"); ?>';
                    $.getScript(linkJs);
                }

                /**
                 * cập nhật lại phân trang 
                */
                $('.loader').hide();
            }
        });

    });

    $('#zoom-filter').click(function(){
        
        $('.filter-order').toggleClass('hidden');
        if ($('.filter-order.hidden').length > 0) {
            $(this).children('i').removeClass('fa-angle-double-up');
            $(this).children('i').addClass('fa-angle-double-down');
        } else {
            $(this).children('i').removeClass('fa-angle-double-down');
            $(this).children('i').addClass('fa-angle-double-up');
        }
    });
</script>

<script>
    function removeBtnFunction() {
        if (confirm('Bạn muốn xóa data này?')) {
        var id = $(this).data("id");
        var link = "{{URL::to('/xoa-sale-care/')}}/" + id;
        var _token   = $("input[name='_token']").val();

        $('.body').css("opacity", '0.5');
        $('.loader').show();
        $.ajax({
            url: link,
            type: "POST",
            data: {
                id,
                _token: _token,
            },
            success: function (data) {
                $('.body').css("opacity", '1');
                
                if (!data.error) {
                    $('#notify-modal').modal('show');
                    if ($('.modal-backdrop-notify').length === 0) {
                        $('.modal-backdrop').addClass('modal-backdrop-notify');
                    }

                    $('#notify-modal .modal-title').html('Xoá data thành công!');

                    setTimeout(function() {
                        $('#notify-modal .modal-title').text('');
                        $('#notify-modal').modal("hide");
                    }, 2000);
                    
                    var tr = '.tr_' + id;
                    $(tr).delay(1000).hide(0);
                } else {
                    alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                }

                $('.loader').hide();
            }
        });
    }
    }
    
    $(document).ready(function() {
    $('.removeBtn').click(function (event) {
        if (confirm('Bạn muốn xóa data này?')) {
            var id = $(this).data("id");
            var link = "{{URL::to('/xoa-sale-care/')}}/" + id;
            var _token   = $("input[name='_token']").val();

            $('.body').css("opacity", '0.5');
            $('.loader').show();
            $.ajax({
                url: link,
                type: "POST",
                data: {
                    id,
                    _token: _token,
                },
                success: function (data) {
                    $('.body').css("opacity", '1');
                    
                    if (!data.error) {
                        $('#notify-modal').modal('show');
                        if ($('.modal-backdrop-notify').length === 0) {
                            $('.modal-backdrop').addClass('modal-backdrop-notify');
                        }

                        $('#notify-modal .modal-title').html('Xoá data thành công!');

                        setTimeout(function() {
                            $('#notify-modal .modal-title').text('');
                            $('#notify-modal').modal("hide");
                        }, 2000);
                        
                        var tr = '.tr_' + id;
                        $(tr).delay(1000).hide(0);
                    } else {
                        alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                    }

                    $('.loader').hide();
                }
            });
        }
    });
});
</script>

<script>
    $("#checkAllId").click(function () {
        $('.chk-item-input:checkbox').not(this).prop('checked', this.checked);

        var $checkboxes = $('.chk-item-input:checkbox');
        var countCheckedCheckboxes = $checkboxes.filter(':checked').length;

        if (countCheckedCheckboxes > 0 ) {
            $('.delete-data-SC').show();
        } else {
            $('.delete-data-SC').hide();
        }

        $('#total-val').data( "total", countCheckedCheckboxes );
        $('#total-val').text('(' + countCheckedCheckboxes + ')');

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

        if (total > 0 ) {
            $('.delete-data-SC').show();
        } else {
            $('.delete-data-SC').hide();
        }

        var listIdString = JSON.stringify(listId);
        $('#total-val').attr('list_id', listIdString);
        $('#total-val').data( "total", total );
        $('#total-val').text('(' + total + ')');
    });

    
    $(".delete-data-SC").click(function () {
        var total = $('#total-val').data('total');
        var list_id = $('#total-val').attr('list_id');
        if (confirm('Xác nhận xóa ' + total + ' data?')) {

            var _token   = $("input[name='_token']").val();
            var link = "{{URL::to('/xoa-danh-sach-sale-care')}}";
            $('.body').css("opacity", '0.5');
            $('.loader').show();
            
            $.ajax({
                url: link,
                type: "POST",
                data: {
                    list_id,
                    _token: _token,
                },
                success: function (data) {
                    $('.body').css("opacity", '1');
                    
                    if (!data.error) {
                        $('#notify-modal').modal('show');
                        if ($('.modal-backdrop-notify').length === 0) {
                            $('.modal-backdrop').addClass('modal-backdrop-notify');
                        }

                        $('#notify-modal .modal-title').html('Xoá data thành công!');

                        setTimeout(function() {
                            $('#notify-modal .modal-title').text('');
                            $('#notify-modal').modal("hide");
                        }, 2000);
                        
                        list_id = JSON.parse(list_id);
                        for ( var i = 0; i < list_id.length; i++) {
                            console.log(list_id[i]);
                            var tr = '.tr_' + list_id[i];
                            $(tr).delay(1000).hide(0);
                        }

                        $('#total-val').attr('list_id', '[]');
                        $('#total-val').data( "total", 0 );
                        $('.delete-data-SC').hide();

                    } else {
                        alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                    }
                   
                    $('.loader').hide();
                }
            });
        }
    });
</script>

<script>

    var daterange = $("input[name='daterange']").val();
    var _token   = $("input[name='_token']").val();
    $.ajax({
        url: "{{ route('api-sum-TN') }}",
        type: "GET",
        data: {
            daterange,
            _token,
        },
        success: function (data) {
            $('#sum-TN').html(data.count);
        }
    });
</script>
<script>

    $('.updateDataModal').on('click', function () {
        var TNSaleId = $(this).data('tnsale-id');
        var link = "{{URL::to('/cap-nhat-tac-nghiep-sale/')}}";
        $("#updateData iframe").attr("src", link + '/' + TNSaleId);

    });
</script>
<script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
<script>
window.addEventListener('message', function (event) {
    if (event.data === 'mess-success') {
        setTimeout(function() { 
            $('#notify-modal').modal('show');
            // if ($('.modal-backdrop-notify').length === 0) {
            //     // $('.modal-backdrop').addClass('modal-backdrop-notify');
            //     $('#notify-modal .modal-title').html('Lưu đơn hàng thành công!');
            // }
            setTimeout(function() { 
                $('#notify-modal').modal("hide");
            }, 2000);
        }, 3000);
    }
});
</script>

<script>
document.getElementById('saleForm').addEventListener('submit', function (e) {
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

        // console.log(inputs);
    return;
});
</script>

<?php
 $checkAllNew = isFullAccess(Auth::user()->role);
?>
<script>
    setTimeout(function() { 
        const salesJson = '<?php echo $listSaleJson;?>';
        const groupIdOfLeadSaleJson = '<?php echo $groupIdOfLeadSale;?>';
        const checkAll = "<?php echo ($checkAllNew)?  1 : 0;?>";
        sales = JSON.parse(salesJson);

        // Lấy tất cả select có class 'select-asign'
        document.querySelectorAll('.select-assign').forEach(select => {
            const currentSaleId = parseInt(select.dataset.assign_id, 10);
            // Xóa các option cũ nếu cần
            // select.innerHTML = "";
            var flagListAssign = false;
            /* check data này có thuộc chung nhóm của leadsale */
            var groupIdOfData = select.dataset.group_id;
            groupIdOfLeadSale = JSON.parse(groupIdOfLeadSaleJson);
            var itemId = select.dataset.sale_id;
            
                if (groupIdOfLeadSale.includes(Number(groupIdOfData)) || checkAll == 1) {
                    flagListAssign = true;
                    /* hiện list sale, ẩn 1 sale theo data*/
                    $('#assign-single-' + itemId).hide();
                    $('#assign-list-' + itemId).show();
                } else {
                    /* ẩn nút cập nhật vì leadsale ko thuộc group của data này*/
                    $('#update-save-' + itemId).hide();
                    $('.save-order-' + itemId).hide();
                }
            
            

            if (!flagListAssign) {
                return;
            }
            // Thêm các option từ array JSON
            sales.forEach(sale => {
                const option = document.createElement('option');
                option.value = sale.id;
                option.textContent = sale.real_name;

                // Nếu id của option khớp với data-sale_id thì chọn nó
                if (sale.id === currentSaleId) {
                    option.selected = true;
                }
                // select.insertAdjacentElement("afterend", option);
                select.appendChild(option);
            });
        });
    }, 800);
</script>

<script>
    setTimeout(function() {
    // Lấy tất cả select có class 'divTN'
    document.querySelectorAll('.divTN').forEach(div => {
        const currentSaleId = parseInt(div.dataset.id, 10);
        var _token  = $("input[name='_token']").val();
            $.ajax({
                url: "{{ route('get-history-by-id-salecare') }}",
                type: 'GET',
                data: {
                    _token: _token,
                    id:currentSaleId,
                },
                success: function(data) {
                    div.innerHTML = data;
                }
            });
    });
    }, 800);
</script>

<script>
    setTimeout(function() {
        // Lấy tất cả select có class 'divTN'
        document.querySelectorAll('.bodyProduct').forEach(tbody => {
            const currentSaleId = parseInt(tbody.dataset.id, 10);
            const orderNew = parseInt(tbody.dataset.order_new, 10);
            var _token  = $("input[name='_token']").val();
            if (orderNew == 0) {
                return;
            }

            $.ajax({
                url: "{{ route('get-order-by-id-salecare') }}",
                type: 'GET',
                data: {
                    _token: _token,
                    id: currentSaleId,
                },
                success: function(data) {
                    tbody.innerHTML = data;
                }
            });
        });
    }, 800);
</script>
