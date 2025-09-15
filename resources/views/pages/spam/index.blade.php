@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

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
            <div class="example mt-0">

            @include('pages.spam.content')

            </div>
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