@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link href="{{ asset('public/css/pages/sale_.css') }}" rel="stylesheet">
<link href="{{ asset('public/css/pages/marketing.css') }}" rel="stylesheet">

<style type="text/css">


#addMktSrc .modal-dialog {
    /* margin-top: 5px;
    width: 1280px; */
    /* margin: 10px; */
    height: 90%;
    /* background: #0f0; */
}
 
#addMktSrc .modal-dialog iframe {
    /* 100% = dialog height, 120px = header + footer */
    height: 100%;
    overflow-y: scroll;
}

#addMktSrc .modal-dialog .modal-content {
    height: 100%;
    /* overflow: scroll; */
}
</style>

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

    @include('pages.marketing.src.content')

<script>
  $(document).ready(function() {
    if ($("#noti-box").length > 0) {
        $("#noti-box").slideDown('fast').delay(5000).hide(0);
    }
    
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

    /* thu gọn menu sidebar*/
    
    // setTimeout(function() { 
    //     $('.sidebar-toggler').click();
    // }, 1000);
    
  });
</script>
@stop