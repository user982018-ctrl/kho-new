<?php
    $listSale = Helper::getListSale(); 
    $checkAll = isFullAccess(Auth::user()->role);
    $isLeadSale = Helper::isLeadSale(Auth::user()->role);  
    $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);     
    $flag = false;
    $flagAccess = false;

    if (($listSale->count() > 0 &&  $checkAll) || $isLeadSale) {
        $flag = true;
    }

    $ladiPages = [
        [
            'name' => '1 Lít Pha 1000 Lít Nước - 0986987791',
            'id' => '378087158713964',
            // 'src' => 'https://www.nongnghiepsachvn.net/mua4tang2'
        ],
        [
            'name' => '1 Xô Pha 10.000 Lít Nước',
            'id' => '381180601741468',
            // 'src' => 'https://www.nongnghiepsachvn.net/mua4tang2'
        ],
        [
            'name' => 'Khách Cũ Tricho',
            'id' => 'Khách Cũ Tricho',
            // 'src' => 'https://www.nongnghiepsachvn.net/mua4tang2'
        ],
        [
            'name' => 'Hotline - Tricho',
            'id' => 'Hotline - Tricho',
            // 'src' => 'https://www.nongnghiepsachvn.net/mua4tang2'
        ],
        [
            'name' => 'Hotline OG',
            'id' => 'Hotline OG',
            // 'src' => 'https://www.nongnghiepsachvn.net/mua4tang2'
        ],
        [
            'name' => '1Xô pha 10.000 lít nước',
            'id' => '389136690940452',
                    // 'src' => 'https://www.nongnghiepsachvn.net/mua4tang2'
        ],
        [
            'name' => 'Ladipage ruoc-dong',
            'id' => 'ruoc-dong',
            // 'src' => 'https://www.phanbonlua.xyz/ruoc-dong'
        ],
        [
            'name' => 'Ladipage mua4tang2',
            'id' => 'mua4tang2',
            // 'src' => 'https://www.nongnghiepsachvn.net/mua4tang2'
        ],
        [
            'name' => 'Ladipage giamgia45',
            'id' => 'giamgia45',
            // 'src' => 'https://www.nongnghiepsachvn.net/giamgia45'
        ],
        [
            'name' => 'Tiễn - Ladipage mua4-tang2 ',
            'id' => 'mua4-tang2',
            // 'src' => 'https://www.nongnghiepsachvn.net/mua4-tang2'
        ],

    ];

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

    .fb {
        font-weight: bold;
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="{{ asset('public/css/pages/sale.css'); }}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
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

    /* .result-TN-col .select2-container--default .select2-selection--single {
        border: none;
    } */

    .selectedClass .select2-container {
        box-shadow: rgb(0, 123, 255) 0px 1px 1px 1px;
    }

</style>

{{-- update filter --}}

    <form action="{{route('sale-index')}}" method="get" class="pb-4">
    
        <div class="maintain-filter-main">
            <div class="m-header-wrap">
                <div class="m-header" style="top: 150px;">
                    <div class="row header-top-filter" style="">
                        <div  class="col-sm-2 col-12 form-group">
                            <a class="home-sale-index" href="{{{route('sale-index')}}}"><span class="text">Sale tác nghiệp</span></a>
                            </div>
                        <div class="col-sm-10 col-12 form-group header-filter">
                           
                            <div class="header-filter-wraper">
                                @if ($checkAll  || $isLeadSale)
                                <div class="col-12 col-sm-3 col-md-3 form-group" style="padding:0 15px;"> 
                                    <select name="group" id="group-filter" class="border-select-box-se">
                                        {{-- <option selected="selected" value="-1" >--Tất cả sale--</option> --}}
                                        <option   value="999">--Chọn nhóm--</option> 
                    
                                        @if (isset($groups))
                                            @foreach($groups as $group)
                                            <option value="{{$group->id}}">{{$group->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @endif
                                @if ($checkAll  || $isLeadSale)
                                <div class="col-12 col-sm-3 col-md-3 form-group" style="padding:0 15px;"> 
                                    <select name="sale" id="sale-filter" class="border-select-box-se">
                                        {{-- <option selected="selected" value="-1" >--Tất cả sale--</option> --}}
                                        <option   value="999">--Chọn Sale--</option> 
                    
                                        @if (isset($sales))
                                            @foreach($sales as $sale)
                                            <option value="{{$sale->id}}">{{($sale->real_name) ? : $sale->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                
                                </div>
                                @endif
                                <div class="header-filter-input col-12 col-sm-6 col-md-4 form-group">
                                    <input name="search" type="text"  value="{{ isset($search) ? $search : null}}" class="form-control" placeholder="Họ tên, số điện thoại">
                                </div>
                            
                                <div style="min-width: 158px;" class="col-12 col-sm-6 col-md-2 form-group">
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
        </div>
        <div class="box-body">
            <div class="loader hidden">
                <img src="{{asset('public/images/rocket.svg')}}">
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
        
            {{-- <form action="{{route('sale-index')}}" class="mb-1"> --}}
                {{ csrf_field() }}
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
                
                {{-- <div class="src-filter col-xs-12 col-sm-6 col-md-2 form-group mb-1">
                    <select name="src" id="src-filter" class="form-select" aria-label="Default select example">       
                        <option value="999">--Chọn nguồn--</option>
                    <?php $pagePanCake = Helper::getConfigPanCake()->page_id;
                    if ($pagePanCake) {
                        $pages = json_decode($pagePanCake);

                        foreach ($pages as $page) {
                    ?>
                        <option value="{{$page->id}}">{{($page->name) ? : $page->name}}</option>
                    <?php   }
                    }   

                    foreach ($ladiPages as $page) {
                    ?>
                        <option value="{{$page['id']}}">{{($page['name']) ? : $page['name']}}</option>
                    <?php   
                        }
                    ?> 

                    </select>
                </div> --}}

                @if ($checkAll  || $isLeadSale || $isLeadDigital)
                <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                    <select name="mkt" id="mkt-filter" class="border-select-box-se">
                        <option value="999">--chọn Marketing--</option>
                        
                        @foreach ($listMktUser->get() as $user)
                        <option value="{{$user->id}}">{{$user->real_name}} </option>
                        @endforeach
                    </select>
                </div>
                @endif


                {{-- @if ($checkAll || $isLeadSale)
                <div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">
                    <select name="sale" id="sale-filter" class="form-select">

                    @if ($checkAll)<option value="999">--Chọn Sale--</option> @endif
                    
                    @if (isset($sales))
                        @foreach($sales as $sale)
                        <option value="{{$sale->id}}">{{($sale->real_name) ? : $sale->name}}</option>
                        @endforeach
                    @endif
                    </select>
                </div>
                @endif --}}

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
                    {{-- <a class="dm-tac-nghiep" href="{{URL::to('tac-nghiep-sale?cateCall=' . $item->id)}}"> 
                        <span class="flag level-4"></span>
                        <span class="text">{{$item->name}}</span>
                        
                        <span class="live-stream"></span>
                        <span style="clear: both;"></span>
                    </a> --}}
                    {{-- <div></div> --}}
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
                {{-- thêm TN SALE --}}       
                <table class="table table-bordered table-multi-select table-sale">
                    <thead>
                        <tr class="drags-area">
                            <th style="top: 0.5px;">
                                @if ($checkAll)
                                <span class="chk-all" style="display: inline-block; min-width: 40px;">
                                    <input id="checkAllId" type="checkbox" name="dnn$ctr1441$Main$SaleTacNghiep$chkAll">
                                    <label for="checkAllId" ></label></span>
                                @else 
                                    <span class="chk-all" style="display: inline-block; min-width: 40px;">
                                    <label for="dnn_ctr1441_Main_SaleTacNghiep_chkItem">&nbsp;</label></span>
                                @endif
                            </th>
                            {{-- <th style="top: 0.5px;">Mã đơn</th> --}}
                            
                            {{-- <th style="width: 60px; top: 0px;" class="text-center hidden">Id</th> --}}

                            <th class="text-center no-wrap area5 hidden-xs" style="top: 0.5px; " ><span style="display: inline-block; min-width: 200px;" class="span-col">Nguồn dữ liệu</span><br>
                                Ngày data về</th>
                            <th class="text-center no-wrap area5 hidden-xs" style="top: 0.5px;"><span class="span-col" style="display: inline-block; width: 120px;">Sale
                            <br>
                                Ngày nhận data</span></th>

                            <th class="text-left no-wrap area1" style="top: 0.5px;">
                                <span class="span-col text-center" style="display: inline-block; min-width: 150px; max-width: 200px;">Họ tên<br>
                                    <span class="span-col">Số điện thoại</span>
                                    <br>
                                    <span id="dnn_ctr1441_Main_SaleTacNghiep_lblNgayMuonNhanHangHeader">Ngày muốn nhận hàng</span>
                                </span>
                            </th>
                            <th class="text-center no-wrap area1  hidden-xs" style="top: 0.5px;"><span class="span-col td-message td-793">Tin nhắn</span></th>
                            <th class="text-center no-wrap area2 hidden-xs" style="top: 0.5px;"><span class="span-col" style="display: inline-block; min-width: 200px;">TN cần</span></th>
                            <th class="text-center no-wrap area2" style="top: 0.5px;"><span class="span-col" style="display: inline-block; min-width: 200px;">Kết quả</span></th>
                            <th class="text-center no-wrap area2 hidden-xs" style="top: 0.5px;"><span class="span-col">TN tiếp</span></th>
                            <th class="text-center no-wrap area3 hidden-xs" style="top: 0.5px;"><span class="span-col">Sản phẩm - Số lượng - Đơn giá</span></th>
                            <th class="text-center no-wrap area3 hidden-xs" style="top: 0.5px;"><span class="span-col">Thành tiền / CK
                                <br>
                                Phí VC / Tổng tiền</span></th>
                            <th class="text-center no-wrap area3 hidden-xs" style="top: 0.5px;"><span class="span-col">Đặt cọc</span></th>
                            <th class="text-center no-wrap area4" style="top: 0.5px;"><span class="span-col"><span id="dnn_ctr1441_Main_SaleTacNghiep_lblTrangThaiGHHeader">Trạng thái giao hàng</span>
                                <br>
                                Ngày muốn nhận hàng
                                                                    </span></th>
                        </tr>
                    </thead>
                    <tbody class="tbody-sale">

                        {{ csrf_field() }}
                        <?php $i = 1; ?>
                        @foreach ($saleCare as $item)
                        <tr class="contact-row tr_{{$item->id}}">
                            <td class="text-center">

                                @if ($checkAll)
                                <span class="chk-item">
                                    <input data-id="{{$item->id}}" value="{{$item->id}}" class="chk-item-input" type="checkbox" id="{{$item->id}}">
                                    <label for="{{$item->id}}">{{$i}}</label>
                                </span>
                                @else 
                                <span class="chk-item">
                                    {{$i}}
                                </span>
                                @endif
                            </td>
                            <td class="text-center hidden">
                                <span class="chk-item"><input id="" type="checkbox" name="">
                                    <label for="">&nbsp;</label></span>
                            </td>
                        
                            <td class="text-center area5 hidden-xs">
                                <span class="span-col span-col-width cancel-col">
                                    <a target="_blank" href="{{$item->page_link}}">{{$item->page_name}}</a>
                                </span>
                
                                <br>
                                <span class="small-tip">(<span>{{date_format($item->created_at,"H:i d-m-Y ")}}</span>)
                                </span>
                            </td>
                            <td class="text-center area5 result-TN-col">
                                @if ($checkAll || $isLeadSale)
                                <div class="text-right">
                                    @if ($checkAll)
                                    <a data-id="{{$item->id}}" title="Xóa data" class="btn-icon aoh removeBtn">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    @endif
                                    <a title="chỉ định Sale nhận data" data-id="{{$item->id}}" class="update-assign-TN-sale btn-icon aoh">
                                        <i class="fa fa-save"></i>
                                    </a>
                                </div>

                                <div class="mof-container" style="margin-top: 0;">
                                    <select class="select-assign bg-dropdown" name="assignTNSale_{{$item->id}}" id="">
                                        @if ($item->user && $item->user->status == 0) 
                                        <option> {{$item->user->real_name}} </option>
                                        @endif

                                        @if (!$item->user)
                                        <option value="0">None </option>
                                            @endif
                                        @foreach ($listSale->get() as $sale)
                                        <option <?php echo ($item->user && $item->user->id == $sale->id) ? 'selected' : '' ?> value="{{$sale->id}}">{{($sale->real_name) ? $sale->real_name : ''}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="clear: both;"></div>

                                @else
                                <div>
                                    {{($item->user) ? $item->user->real_name : ''}} 
                                    {{-- <span class="small-tip">({{($item->user) ? $item->user->name : ''}})</span> --}}
                                </div>
                                @endif
                            </td>
                            <td class="area1" title="FROM_FACEBOOK_MESSAGE">
                                
                                <?php if ($item->assign_user == Auth::user()->id) {
                                    $flagAccess = true;
                                } 
                                ?>
                                @if ($checkAll || $isLeadSale || $flagAccess)
                                <?php $flagAccess = false; ?>
                                <div class="text-right">
                                    <a title="Thông tin khách hàng" data-target="#updateData" data-toggle="modal"
                                        data-tnsale-id="{{$item->id}}" class="updateDataModal btn-icon aoh"><i class="fa fa-info"></i></a>

                                    @if ($item->id_order_new)
                                    <a data-target="#createOrder" data-toggle="modal" title="Sửa đơn" data-tnsale-id="{{$item->id}}" data-id_order_new="{{$item->id_order_new}}" class="orderModal btn-icon aoh"><i class="fa fa-edit"></i></a>
                                    @else
                                        <a data-target="#createOrder" data-toggle="modal" title="Chốt đơn" data-tnsale-id="{{$item->id}}" data-address="{{$item->address}}" data-name="{{$item->full_name}}" data-phone="{{$item->phone}}" class=" orderModal btn-icon aoh"><i class="fa fa-edit"></i></a>
                                    @endif
                                </div>
                                @endif

                                <div class="" style="text-overflow: ellipsis;">
                                    {{$item->full_name}}
                                </div>
                                {{-- <span class="nha-mang">[VIETTEL]</span> --}}
                                <a href="tel:{{$item->phone}}" class="span-col" style="width: calc(100% - 90px);">
                                    {{$item->phone}}
                                    
                                </a>
                                <span class="span-col text-right" style="width: 85px;">

                                    <?php 
                                    /*
                                    old_customer: 1 -> hiển thị trái tim
                                    check khách cũ đơn thành công thì hiện trái tim
                                    */
                                    $oldCustomer = Helper::isOldCustomer($item->phone, $item->group_id);
                                    $scOldCutomer = ($oldCustomer) ? $oldCustomer->id : 0;
                                    ?>

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
                                <div class="text-left khkn sline">
                                    
                                </div>
                                <div class="small-tip">
                                    
                                </div>
                            </td>
                            <td class="area1 hidden-xs td-5055" style="max-width: 100px;">
                                <span style="cursor: pointer; overflow: hidden; max-height: 100px; display: block;">
                                    {{$item->messages}}
                                </span>
                            </td>
                            <td class="area2 hidden-xs type-TN" style="padding-bottom: 10px;">

                                @if (!$item->type_TN)
                                    @if (!$item->old_customer)
                                    <span class="fb span-col ttgh7" style="cursor: pointer; width: calc(100% - 60px);">Data nóng</span> 
                                    @elseif ($item->old_customer == 1)
                                    <span class="fb span-col" style="cursor: pointer; width: calc(100% - 60px);">CSKH</span> 
                                    @elseif ($item->old_customer == 2)
                                    <span class="fb span-col" style="cursor: pointer; width: calc(100% - 60px);">Hotline</span> 
                                    @endif
                                @else
                                <span class="fb span-col  <?= ($item->has_TN) ?: 'ttgh7' ?>" style="cursor: pointer; width: calc(100% - 60px);"> {{$item->typeTN->name}}</span>
                                @endif

                                <span class="box-TN" >
                                    <a style="color: rgb(64, 11, 209) !important; text-decoration: underline rgb(64, 11, 209) !important; font-style:italic !important;" class="TNHistoryModal" data-target="#TNHistory" data-tnsale_id="{{$item->id}}" data-toggle="modal" title="Lịch Sử TN">
                                        <i class="fa fa-history" style="color:rgb(64, 11, 209);"></i></a>
                                </span>
                                <div class="mof-container TNModal"  data-target="#TN" data-tnsale_id="{{$item->id}}" data-toggle="modal" title="Tác Nghiệp Ngay">
                                    <div data-id="{{$item->id}}" id="TNSale_{{$item->id}}" rows="2" cols="20" class="form-control txt-mof txt-dotted"
                                        data-content="Tối đa 500 ký tự" data-trigger="focus" data-toggle="popover" data-original-title="" title=""><?php if ($item->listHistory->count() > 0) {
                                            foreach ($item->listHistory as $key => $value) {
                                                echo date_format($value->created_at,"d/m") . ' ' . $value->note . "<br>";
                                            } 
                                        } else {
                                            echo $item->TN_can;
                                        }
                                        ?></div>
                                </div>
                                <div style="clear: both;"></div>
                                <span class="item-noidung-other"></span>
                            </td>

                            <?php $order = $item->orderNew ?>

                            <td class="result-TN-col area2 no-wrap fix_brower_continue_let_off" style="min-width:100px">
                                <div class="text-right">
                                    @if (isset($item->id_order_new))
                                    <a target="_blank" class="btn-icon aoh" href="{{route('view-order', ['id' => $item->id_order_new])}}" title="Xem lịch sử xem thông tin số"><i style="font-size:14px;" class="fa fa-history"></i></a>
                                    @endif
                                </div>
                                @if ($item->type_TN)
                                <?php 
                                    $listCallByTypeTN = Helper::listCallByTypeTN($item->type_TN);
                                    if ($listCallByTypeTN) {
                                            
                                ?>
                                    <select data-id="{{$item->id}}"  class="hidden result-TN select-assign ddlpb dis_val" tabindex="-1" title="" >
                                        <option value="-1">--Chọn--</option>
                                        @foreach ($listCallByTypeTN as $call)
                                            <option value="{{$call->id}}" <?= ($item->result_call == $call->id) ? 'selected' : '' ;?>>{{$call->callResult->name}}</option>
                                        @endforeach

                                    </select>
                                <?php
                                }
                                ?>
                            
                                @endif

                                <div class="small-tip text-left">
                                    @if ($order)
                                        <br>{{date_format($order->created_at,"H:i d-m-Y ")}}
                                    @endif
                                    <a class="btn-icon invisible">&nbsp;</a>
                                </div>
                                
                            </td>

                            <td class="no-wrap area2 no-wrap  hidden-xs " style="min-width:120px">
                                <span class="next-TN">
                                    @if ($item->result_call && $item->result_call != -1)
                                    {{($item->resultCall) ? $item->resultCall->thenCall->name : ''}}
                                    @endif
                                </span>

                                <div class="text-right">
                                    <a title="Sửa lịch tác nghiệp" data-target="#updateCalendarTN" data-toggle="modal"
                                        data-timeWakeup="{{$item->time_wakeup_TN}}" data-iddd="{{$item->id}}" 
                                        class="calendarModal btn-icon aoh">
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                </div>
                                <br>
                                <span class="span-col small-tip" style="width: calc(100% - 20px);">
                                    {{-- <span class="sau-bao-lau-con-lai">xxxx</span> --}}
                                </span>
                            </td>

                            <td class="text-left area3 hidden-xs">
                                <span>
                                    <table class="tb-in-sp">
                                        <tbody>
                                            
                                            @if ($order)
                                            @foreach (json_decode($order->id_product) as $product)
                                                <?php $productModel = getProductByIdHelper($product->id);
                                                $price = $productModel->price;
                                                $name = $productModel->name;
                                                if ($productModel->type == 2 && !empty($product->variantId)) {
                                                    $variantId = $product->variantId;
                                                    $variant = HelperProduct::getProductVariantById($variantId);
                                                    $price = $variant->price;
                                                    $name .= HelperProduct::getNameAttributeByVariantId($variantId);  
                                                }
                                                ?>
                                                @if ($productModel)
                                                <tr><td><span class="ten-sp" style="text-overflow:ellipsis">{{$name}}</span></td>
                                                    <td class="text-center no-wrap">&nbsp; x{{$product->val}} &nbsp;</td><td class="text-right">{{number_format($price)}}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                            @endif
                                            
                                        </tbody>
                                    </table></span>
                            </td>
                            <td class="no-wrap area3 text-right hidden-xs">
                                @if ($order)
                                <table class="tb-in-sp ">
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
                            <td class="no-wrap area3 text-right hidden-xs">
                                <span id="dnn_ctr1441_Main_SaleTacNghiep_rptData__DonHangDatCoc_0"></span>
                            </td>
                            <td class="text-center area4">
                                <span class="span-col no-wrap">
                                    <span class="span-col" style="width: 20px;">
                                        
                                    </span>
                                    <span class="span-col no-wrap" style="width: calc(100% - 50px);">
                                        <span class="ttgh0"></span>
                                    </span>
                                    <span class="span-col" style="width: 20px;">
                                        
                                    </span>
                                    <div class="small-tip">
                                        @if ($item->orderNew)
                                        {{$listStatus[$item->orderNew->status]}} <br>

                                            @if ($item->orderNew->shippingOrder)
                                            <a  title="sửa" target="blank" href="{{route('detai-shipping-order',['id'=>$item->orderNew->shippingOrder->id])}}" role="button"> {{$item->orderNew->shippingOrder->order_code}}</a>
                                            @endif
                                        
                                        @endif
                                    </div>
                                </span>
                                <span class="small-tip"></span>
                                <a class="item-mdgv" href="javascript:void(0)" style="color: darkorange;"></a>
                                
                                <br>

                                <a onclick="show_cap_nhat_ngay_giao_hang(112155407);return false;" title="Cập nhật ngày muốn nhận hàng" class="btn-icon aoh" href="javascript:__doPostBack('dnn$ctr1441$Main$SaleTacNghiep$rptData$ctl00$ctl08','')">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                </a>

                                <div style="color: green;">
                                    
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
<div id="updateData" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content ">
        <div class="modal-header">
            <h5 class="modal-title">Thông tin khách hàng</h5>
            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <iframe src="{{route('add-orders')}}" frameborder="0"></iframe>

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

        <iframe src="{{route('add-orders')}}" frameborder="0"></iframe>

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
                console.log('aaaa')
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
    const mrNguyen = [
        {
            id : '332556043267807',
            name_page : 'Rước Đòng Organic Rice - Tăng Đòng Gấp 3 Lần',
        },
        {
            id : '318167024711625',
            name_page : 'Siêu Rước Đòng Organic Rice- Hàm Lượng Cao X3',
        },
        {
            id : '341850232325526',
            name_page : 'Siêu Rước Đòng Organic Rice - Hiệu Quả 100%',
        },
        {
            id : 'mua4tang2',
            name_page : 'Ladipage mua4tang2',
        },
        {
            id : 'giamgia45',
            name_page : 'Ladipage giamgia45',
        }
    ];
    const mrTien = [
        {
            id : 'mua4-tang2',
            name_page : 'Ladipage mua4-tang2',
        }
    ];
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
        console.log(value);
        $('.body').css("opacity", '0.5');
        $('.loader').show();
        $.ajax({
            url: "{{route('update-salecare-result')}}",
            type: 'POST',
            data: {
                _token: _token,
                id,
                value
            },
            success: function(data) {
                $('.body').css("opacity", '1');
                var tr = '.tr_' + id;
                if (!data.error) {
                    var trId = 'tr.tr_' + id;
                    console.log(data.nextTN);
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
                $('.loader').hide();
            }
        });
    });
    $('.update-assign-TN-sale').click(function(){
        $('.body').css("opacity", '0.5');
        $('.loader').show();
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
                $('.body').css("opacity", '1');
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
                $('.loader').hide();
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

                        rs += '<td class="text-center area5 hidden-xs">'; 
                        rs += '<span class="span-col span-col-width cancel-col">';
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

                        rs += '<td class="text-center area5 hidden-xs result-TN-col">';

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
                        rs += '<a href="tel:' + element.phone + '" class="span-col" style="width: calc(100% - 90px);">' + element.phone;
                            
                        rs += '</a>';
                        rs += '<span class="span-col text-right" style="width: 85px;">';

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
                                rs += '<span class="fb span-col ttgh7" style="cursor: pointer; width: calc(100% - 60px);">Data nóng</span>';
                            } else if (element.old_customer == 1) {
                                rs += '<span class="fb span-col" style="cursor: pointer; width: calc(100% - 60px);">CSKH</span>';
                            } else if (element.old_customer == 2) {
                                rs += '<span class="fb span-col" style="cursor: pointer; width: calc(100% - 60px);">Hotline</span>';
                            }
                        } else {
                            var classHasTN = '';
                            if (!element.has_TN) {
                                classHasTN = 'ttgh7';
                            }
                            rs += '<span class="fb span-col ' + classHasTN + '" style="cursor: pointer; width: calc(100% - 60px);">';
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
                        rs += '<span class="span-col no-wrap">';
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