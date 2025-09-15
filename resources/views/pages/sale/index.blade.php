@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

<style type="text/css">
    .dm-tac-nghiep { display: inline-block; min-width: 180px; font-size: 11px; color: #333; padding: 6px; padding-right: 15px; transition: ease 3s all; }

        .dm-tac-nghiep:hover, .dm-tac-nghiep.selected { box-shadow: inset 0 0 4px orange; }

        .dm-tac-nghiep .flag { width: 20px; background-color: #C1E2F4; display: inline-block; height: 14px; float: left; }

        .dm-tac-nghiep .text { display: inline-block; height: 16px; line-height: 16px; float: left; margin-left: 10px; }

        .dm-tac-nghiep .count { font-weight: bold; margin-left: 6px; color: orange; float: left; line-height: 16px; transition: ease 3s all; }

        .dm-tac-nghiep .level-1 { background-color: #a6ffa8 }

        .dm-tac-nghiep .level-2 { background-color: #8ad6ff }

        .dm-tac-nghiep .level-3 { background-color: #ffc0cb }

        .dm-tac-nghiep .level-4 { background-color: orangered; }

        .dm-tac-nghiep .flash-bg { background-color: red; }

        .dm-tac-nghiep .live-stream { float: left; width: 5px; height: 5px; border-radius: 50%; background-color: red; margin-left: 5px; margin-top: 6px; display: none; }

        .dm-tac-nghiep .count.flash-bg { color: white; }

    tr.contact-row input.form-control, tr.contact-row select { font-size: 11px; }

    .area1 td, .area1 th, .area2 td, .area2 th, .area3 td, .area3 th, .area4 td, .area4 th, .area5 td, .area5 th { transition: ease 0.5s all; }

    .hide-area1 .area1, .hide-area2 .area2, .hide-area3 .area3, .hide-area4 .area4, .hide-area5 .area5 { width: 0px !important; padding: 0 !important; }

        .hide-area1 .area1 *, .hide-area2 .area2 *, .hide-area3 .area3 *, .hide-area4 .area4 *, .hide-area5 .area5 * { display: none !important; }

    .table-info th { background-color: #dfdfdf !important; color: #000 !important; border: 2px solid #dfdfdf !important; }

        .table-info th:last-child { border-color: #dfdfdf !important; }

    .table-info td { border: 2px solid #dfdfdf !important; }

    .table-info tr:nth-child(odd) td { background-color: transparent; }

    .tt1 { color: #1be61b; font-weight: bold; }

    .tt0 { color: #ff3333; font-weight: bold; }

    /* .span-col { display: inline-block; } */

    a.tao-don-fixed { display: block; width: 55px; height: 55px; background-color: #0080ff; border-radius: 50%; color: white !important; text-align: center; padding-top: 9px; bottom: 10px; position: fixed; cursor: pointer; z-index: 9999; }

    .tao-don-fixed i { font-size: 20px; }

    .tao-don-fixed .text { font-size: 10px; font-weight: bold; }

    a.chot-don-fixed { left: 90px; background-color: darkorange; }

    .txt-dotted { border-bottom: none !important; }

    .select2 { background-color: transparent; }

    .form-control { background-color: transparent; }

    .select2-container .select2-choice { background-color: transparent; }

    .mof-container, .txt-mof { background-color: transparent; height: 45px; }

        .txt-mof:focus, .txt-mof:focus, .txt-mof:hover { background-color: white; }

    textarea.txt-mof { height: 45px; }

    .ttgh { }

    .ttgh2, .ttgh3, .ttgh4 { color: #f7a300; }

    .ttgh5, .ttgh8 { color: #0b7f16 }

    .ttgh6, .ttgh7 { color: #ff0000 }

    td a.btn-ttghcs { color: darkorange; }

    @media(max-width:768px) {
        .icon.me-2 {
            width: 20px !important;
            height: 20px !important;
        }

        .contact-row .fa { font-size: 20px; }

        .dm-tac-nghiep { width: 48%; min-width: 48%; }
    }

    .span-col-width { width: calc(100% - 50px); text-overflow: ellipsis; }

    .cancel-col { width: 100% !important; }

    .td-message { display: inline-block; min-width: 150px; max-width: 200px; }
    /*Đơn vị Hùng Mạnh Phát*/
    .td-793 { width: 150px !important; max-width: 150px !important; }
    /*.td-386 {width:150px !important;max-width: 150px !important;}*/

    .tb-in-sp { border: none !important; width: 100%; font-size: 11px; }

        .tb-in-sp td { border: none !important; background-color: transparent !important; padding: 3px 0px !important; }
    /*.tb-in-sp tr:not(:last-child) td{border-bottom:1px dotted #ccc!important;}*/
    .cbvd, i.khkn { color: darkorange; font-size: 14px; }

    .khkn { color: darkorange }

    .sline { padding-top: 3px; padding-bottom: 3px; }

    .strikethrough { text-decoration: line-through; font-size: 11px !important; }

    .nha-mang { color: magenta; font-size: 9px; font-weight: bold; display: block; }

    .black-phone{color:darkorange!important;}
</style>


<div class="tbl_mobile body flex-grow-1">

    @if ($errors->any())
    <div class="col-sm-12">
        <div class="alert  alert-warning alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <span><p>{{ $error }}</p></span>
            @endforeach
        </div>
    </div>
    @endif

    @if (session('error'))
    <div id="noti-box ">
        <div class="col-sm-12">
            <div class="alert  alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-body p-0">
            @include('pages.sale.content')
        </div>
    </div>
</div>
<script>
  $(document).ready(function() {
    if ($("#noti-box").length > 0) {
        $("#noti-box").slideDown('fast').delay(5000).hide(0);
    }
    
    if ($(window ).width() < 600) {
        $('.tool-bar button').text('Tìm');
    }
    
    if ($('.flex.items-start').length) {
        console.log('tadada')
        
        setTimeout(function() { 
            $('.notify.fixed').hide();
        }, 3000);
    }

    /* thu gọn menu sidebar*/
    
    // setTimeout(function() { 
    //     $('.sidebar-toggler').click();
    // }, 1000);
    
  });
</script>
@stop