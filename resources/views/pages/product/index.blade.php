@extends('layouts.default')
@section('content')
<div class="tbl_mobile body flex-grow-1 px-3">
        <div class="container-lg">

        @if ($errors->any())
    <div class="col-sm-12">
        <div class="alert  alert-warning alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <span><p>{{ $error }}</p></span>
            @endforeach
        </div>
    </div>
@endif


@if (session('success'))
<div id="noti-box">
    <div class="col-sm-12">
        <div class="alert  alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
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

<?php
$active       = '';
$routeName    = \Route::getCurrentRoute()->uri;
$asRouteName  = (\Route::getCurrentRoute()->action['as']) ?? null;
?>
<div class="card mb-4">
  <div class="card-header"><strong>Quản lý sản phẩm</strong> </div>
    <div class="card-body">
      <div class="example mt-0">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item"><a class="nav-link  <?= ($routeName == 'danh-sach-san-pham')?'active':'' ?>"  href="{{URL::to('/danh-sach-san-pham')}}">
              <svg class="icon me-2">
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-media-play')}}"></use>
              </svg>Danh sách</a></li>
          <li class="nav-item"><a class="nav-link <?= ($asRouteName == 'nhap-hang-theo-thang' || $routeName == 'nhap-hang')?'active':'' ?>" href="{{URL::to('/nhap-hang')}}">
              <svg class="icon me-2">
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-code')}}"></use>
              </svg>Nhập hàng</a>
          </li>
          <li class="nav-item"><a class="nav-link" href="" target="_blank">
              <svg class="icon me-2">
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-code')}}"></use>
              </svg>Xuất hàng</a>
          </li>
          <li class="nav-item"><a class="nav-link" href="" target="_blank">
              <svg class="icon me-2">
                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-code')}}"></use>
              </svg>Tồn kho</a>
          </li>
        </ul>
<?php
  if ($routeName == 'nhap-hang' || $asRouteName == 'nhap-hang-theo-thang' || $asRouteName == 'nhap-hang-theo-nam') :
?>

@include('pages.product.contentSetProducts')

<?php
  //elseif ($routeName == 'danh-sach-san-pham') :
  else :
?>

@include('pages.product.content')

<?php
  endif
?>

        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $("#noti-box").slideDown('fast').delay(5000).hide(0);
        
    if ($(window ).width() < 600) {
        $('.tool-bar button').text('Tìm');
    }
  });
</script>
@stop