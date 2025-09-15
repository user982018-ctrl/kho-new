@extends('layouts.default')
@section('content')

<style>
    #laravel-notify .notify {
      z-index: 9999;
  }
</style>
<div class="tbl_mobile body flex-grow-1 px-3">
    <div class="container-lg">
        <div class="card mb-4">
            <div class="card-header"><strong>Quản lý nhóm Sale</strong> </div>
            <div class="card-body p-0">
                <div class="example mt-0">

@include('pages.groupUser.content')

              </div>
            </div>
          </div>
        </div>
      </div>
<script>
  $(document).ready(function() {
    $("#noti-box").slideDown('fast').delay(5000).hide(0);
    
    if ($(window ).width() < 600) {
        console.log($(window ).width());
        $('.tool-bar button').text('Tìm');
    }
    
    if ($('.flex.items-start').length) {
        console.log('tadada')
        
        setTimeout(function() { 
            $('.notify.fixed').hide();
        }, 3000);
    }

  });
</script>
@stop