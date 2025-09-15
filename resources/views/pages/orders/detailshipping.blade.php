@extends('layouts.default')
@section('content')

<style>
    .card-header a.btn-warning {
        color: #fff;
        float: right;
    }
    iFrame {
        width: 100%;
        height: 100vh;
    }
    iFrame .header-main {
        display: none;
    }
    
    .footer {
        display: none;
    }

    .hidden-iframe {
    height: 80px;
    position: fixed;
    background: #fff;
    width: 100%;
    }
</style>
<link href="{{ asset('public/css/pages/styleShippingOrders.css'); }}" rel="stylesheet">

<link href="{{ asset('public/css/pages/styleOrders.css'); }}" rel="stylesheet">

{{-- <div class="hidden-iframe"> </div> --}}

<?php $orderCode  = $ship->order_code;?>
<iFrame id="iFrame" src="https://donhang.ghn.vn/?order_code={{$orderCode}}" width="680" height="480" allowfullscreen></iFrame>

<script>
    $("#iFrame").contents().find(".header-main").style.display = 'none';
</script>
@stop