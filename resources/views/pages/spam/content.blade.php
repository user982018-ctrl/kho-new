@extends('layouts.default')
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('public/css/dashboard.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('public/css/pages/rank.css') }}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{ asset('public/js/moment.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('public/css/daterangepicker.css') }}" />

<style>
      #laravel-notify .notify {
      z-index: 9999;
  }
    /* .modal-backdrop.show {
        opacity: 1;
        background: none;
    }
    .modal-backdrop-notify.show {
        opacity: 0;
    } */
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

    #addSpam .modal-dialog,.modal-dialog {

        height: 90%;
        /* background: #0f0; */
    }

    #addSpam .modal-dialog iframe,
    .modal-dialog iframe {
        /* 100% = dialog height, 120px = header + footer */
        height: 100%;
        overflow-y: scroll;
    }
    #addSpam .modal-dialog .modal-content, .modal-dialog .modal-content {
    height: 100%;
    /* overflow: scroll; */
    }
    .m-header-wrap {
        position: relative;
        min-height: 50px;
        z-index: 998;
        box-shadow: 0 0 5px #999;
    }

    .header.header-sticky {
        position: unset;
    }

    .fs-5 {
        font-size: 2.0736rem !important;
    }

    .weekly-sales span {
        color: #00894f;
        background-color: #d9f8eb;
    }

    .body {
        background: #fff;
    }

    .loader {
        top: 50%;
        left: 50%;
        position: absolute;
    }

    @media (max-width: 576px) {
        .hidden-xs {
            display: none;
        }
    }
</style>

<?php $checkAll = isFullAccess(Auth::user()->role);
    $isLeadSale = Helper::isLeadSale(Auth::user()->role);
    $isDigital = Auth::user()->is_digital;
    $access = $checkAll || $isLeadSale || $isDigital;
?>
@include('notify::components.notify')
<div class="content-wrapper" style="min-height: 779px;">
<div id="dnn_ContentPane" class="contentPane">
    <div class="box-body m-header-wrap">
        <div class="m-header row">
            <div class="col-sm-8 form-group">
                <span class="text form-group"><a href="{{route('spam')}}"> Seeding/Spam</a></span>
            </div>
            <div class="col-sm-4 form-group">
                <form action="{{route('search-spam')}}">
                    {{ csrf_field() }}
                    <div style="width: calc(100% - 125px); float: left;">
                        <input name="search" type="text"
                            id="" class="form-control" placeholder="">
                    </div>
                    <div style="width: 100px; float: right;">
                        <button  type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                    
                    </div>
                </form>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>
    
    <div class="box-body" style="padding-bottom:0px;">
        <div id="addSpam" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm số Seeding</h5>
                    <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
    
                <iframe src="{{route('add-spam')}}" frameborder="0"></iframe>
    
                </div>
            </div>
        </div>

        @if ($access)
        <a id="btnAddModal" title="Thêm số seeding" class=" btn btn-sm btn-primary mr15"><i class="fa fa-plus"></i> <span class="text">Thêm số seeding</span></a>
                                       
        {{-- <a class="btn btn-sm btn-warning">
            <i class="fa fa-trash"></i> Xóa nhiều
        </a> --}}
        @endif
    </div>
    <div class="box-body">
    <div class="row">
        <div class="col-xs-12">
        <div style="width: 100%; overflow: hidden; overflow-x: auto;">
            <table class="table table-bordered table-multi-select">
                <tbody>
                    <tr>
                        <th class="text-center" style="width: 50px;">
                            <span class="chk-all"><input id="dnn_ctr1571_Main_DanhSachSeeding_chkAll"
                                    type="checkbox" name="dnn$ctr1571$Main$DanhSachSeeding$chkAll"><label
                                    for="dnn_ctr1571_Main_DanhSachSeeding_chkAll">&nbsp;</label></span>
                        </th>
                        <th style="width: 30px;" class="text-center">#</th>
                        <th class="text-center no-wrap">Số seeding</th>
                        <th class="text-center no-wrap">Người tạo</th>
                        <th class="text-center no-wrap">Ngày tạo</th>
                        <th class="text-center no-wrap"> </th>
                    </tr>

                    <?php $i = 1; ?>
                    @foreach ($list as $item)
                    
                    
                    <tr class="item26284">
                        <td class="text-center no-wrap">
                            <span class="chk-item"><input
                                    id="" type="checkbox"
                                    name=""><label
                                    for="">&nbsp;</label></span>


                        </td>
                        <td class="text-center">
                            {{$i}}
                        </td>
                        <td class="text-center">
                            {{$item->phone}}
                        </td>
                        <td class="text-center no-wrap">
                             {{$item->user->real_name}}
                        </td>
                        <td class="text-center no-wrap">
                           {{$item->created_at}}
                        </td>
                        <td class="text-center no-wrap">
                            @if ($access)
                            <a title="xoá" class="btn-icon aoh" onclick="return confirm('Bạn muốn xóa data này?')" href="{{route('delete-spam',['id'=>$item->id])}}" role="button">
                                <i class="fa fa-trash"></i>
                            </a>
                            @endif
                            
                        </td>
                    </tr>

                    <?php $i++; ?>
                    @endforeach
                    
                </tbody>
            </table>

            <div style="height: 100px;"></div>
        </div>
        </div>
    </div>
    </div>

</div>
</div>

<div class="modal fade" id="notify-modal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" style="color: seagreen;"><p style="margin:0">thành công</p></h6>
            <button style="border: none;" type="button" id="close-modal-notify" class="close" data-dismiss="modal" >
              <span>&times;</span>
            </button>
          </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
  $.urlParam = function(name) {
      var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
      if (results) {
          return results[1];
      }
      return 0;
  }
</script>
<script>
  function number_format_js(number) {
      number = number.toLocaleString('vi-VN');
      return number.replace(/,/g, '.').replace(/\./g, ',');
  }
</script>

<script>
    $('#btnAddModal').click(function() {
        $('#addSpam').modal('toggle');
    });

    $("#close-modal-notify").click(function() {
        $('#notify-modal').modal("hide");
    });

     $("#close-main").click(function() {
        $('#addSpam').modal('toggle');
    });

    
</script>
<script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
<script>
    window.addEventListener('message', function (event) {
        if (event.data === 'close-modal') {
            console.log('p')
            // Đóng modal, hoặc làm gì đó khi form trong iframe thành công
            $('#addSpam').modal('toggle');
            alert('Đã nhận thành công từ iframe!');
        }

        if (event.data === 'mess-success') {
            // $('#addSpam').modal('toggle');
            if ($('.modal-backdrop-notify').length === 0) {
                $('.modal-backdrop').addClass('modal-backdrop-notify');
                $('#notify-modal .modal-title').html('Lưu data thành công!');
            }
            $('#notify-modal').modal('toggle');
            setTimeout(function() { 
                $('#notify-modal').modal("hide");
            }, 2000);
        }

        $("#noti-box").slideDown('fast').delay(2000).hide(0);
    });
</script>

@stop
