@extends('layouts.default')
@section('content')

<style>
    .small-tip, .h-label{
        font-size: 13px;
    }
    .text-red {
        color: #dd4b39 !important;
    }
    header {
        display: none !important;
    }
</style>
<div class="body flex-grow-1 px-3" style="position:relative">
    <div class="container-lg" >
        <div class="row">
            <div id="notifi-box" class="hidden alert alert-success print-error-msg">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            </div>
            <div class="box-body"> 
                <div class="row form-group"> 
                    <div class="col-xs-4 form-group">
                        <span class="h-label form-group">
                            Số seeding<span class="text-red">(*)</span> 
                        </span>
                    </div>
                    <div class="col-xs-8 form-group">
                        <input required name="phone" rows="5" cols="20" id="" class="form-control">
                        <p class="small-tip" id="phone" style="color:red"></p>
                        {{-- <div class="small-tip">* Có thể nhập nhiều số seeding cách nhau bằng dấu xuống dòng </div> --}}
                        <div class="small-tip">** Nếu số seeding đã tồn tại hệ thống sẽ bỏ qua</div>
                        <br>
                    </div>  
                    
                    <div class="col-xs-4 form-group">
                        <span class="h-label">&nbsp;</span>
                    </div>
                    <div class="col-xs-8 form-group">
                        <button type="button" id="add-spam-btn" class="btn btn-sm btn-primary" >
                            <i class="fa fa-plus"></i> Thêm mới
                        </button>
                    </div>

                    
                </div>  
                 
            </div>
           
        </div>
        <div id="loader-overlay">
            <div class="loader"></div>
        </div>
    </div>
</div>
{{ csrf_field() }}


<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    $('#add-spam-btn').on('click', function (){
        var seft = $(this);
        seft.prop("disabled", true);
        // window.parent.postMessage('close-modal', '*');
        var phone = $("input[name='phone']").val();
        var _token = $("input[name='_token']").val();

        $('loader-overlay').css('diplay', 'flex');

        if (phone == '') {
            $('loader-overlay').css('diplay', 'none');
            seft.prop("disabled", false);
           return false;
        }

        $.ajax({
            url: "{{ route('save-spam') }}",
            type: 'POST',
            data: {
                _token,
                phone,
            },
            success: function(data) {
                seft.prop("disabled", false);

                if ($.isEmptyObject(data.errors)) {
                    // window.parent.postMessage('close-modal', '*');
                    window.parent.postMessage('mess-success', '*');
                    $("input[name='phone']").val('');
                    
                } else {
                    let resp = data.errors;
                    for (index in resp) {
                        console.log(index);
                        console.log(resp[index]);
                        $("#" + index).html(resp[index]);
                    }
                }
                $('loader-overlay').css('diplay', 'none');
            }
        });
    });
   
    </script>
@stop