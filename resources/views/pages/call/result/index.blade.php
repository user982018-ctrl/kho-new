@extends('layouts.default')
@section('content')
<div class="tbl_mobile body flex-grow-1 px-3">
    <div class="container-fluid ">

    @if ($errors->any())
        <div class="col-sm-12">
            <div class="alert  alert-warning alert-dismissible fade show" role="alert">
                @foreach ($errors->all() as $error)
                    <span><p>{{ $error }}</p></span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card mb-4">
    <div class="card-header"><strong>QL kết quả TN sale</strong> </div>
        <div class="card-body p-0">
            <div class="example mt-0">

            @include('pages.call.result.content')

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