<!DOCTYPE html>
<html lang="en">
  <head>
    <link href="{{ asset('public/css/pages/notify.css') }}" rel="stylesheet">
     @include('includes.head')  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 
    
    <style>
        #laravel-notify .notify {
            z-index: 9999;
        }     
        button.refresh {
            position: fixed;
            bottom: 10px;
            right: 10px;
            color: #fff;
        }

        .ttgh6, .ttgh7 {
            width: 40px;
            color: #ff0000;
        }

        #notify-modal .modal-title {
            color:#fff;
        }
    </style>
  </head>
  <body class="body" style="height: 100vh;">
    
    @include('notify::components.notify')

    {{-- thông báo --}}
    <div class="modal fade" id="notify-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><p style="margin:0">Cập nhật kết quả thành công</p></h6>
                <button style="border: none;" type="button" id="close-modal-notify" class="close" data-dismiss="modal" >
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row form-group"> 
                <div class="col-xs-12">
                    <span class="h-label">Thời gian tác nghiệp tiếp<span class="text-red">(*)</span></span>
                </div>
                <div class="col-xs-12 form-group">
                    {{ csrf_field() }}
                    
                    <div style="display:inline-block;width:calc(100% - 115px);">
                        <input type="hidden" value="{{$id}}" name="id">
                        <input type="hidden" name="tmpDaterange" value="">
                        <input name="daterange" value="<?php if ($saleCare->time_wakeup_TN) { ?>
                             {{date_format(date_create($saleCare->time_wakeup_TN)," d/m/Y H:i")}}
                        <?php } ?>" type="text" class="form-control date-range-one">
                    </div>
                    <div style="display: inline-block; width: 110px;text-align:right;">
                        <a id="update" class="btn btn-sm btn-primary" 
                        >
                        <i class="fa fa-save"></i> Cập nhật
                        </a>
                    </div>
                    <div id="loader-overlay">
                        <div class="loader"></div>
                    </div>
                </div>

                {{-- <div class="row text-right">
                    <div><button class="refresh btn btn-info">Refresh</button></div>
                </div> --}}
                
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
          $("#noti-box").slideDown('fast').delay(2000).hide(0);
        });
      </script>
    @include('includes.foot')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/notify.js')}}"></script>
    <script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>
    <script>
        var df = 'DD/MM/YYYY HH:mm';
        
        $('input[name="daterange"]').daterangepicker({
            // "startDate": moment(),
            "singleDatePicker": true,
                "autoUpdateInput": true,
                "locale": {
                    "format": df,
                    "separator": " - ",
                    "applyLabel": "Đồng ý",
                    "cancelLabel": "Hủy",
                    "fromLabel": "Từ ngày",
                    "toLabel": "Đến ngày",
                    "customRangeLabel": "Tùy chỉnh",
                    "weekLabel": "Tuần",
                    "daysOfWeek": [
                        "CN",
                        "T2",
                        "T3",
                        "T4",
                        "T5",
                        "T6",
                        "T7"
                    ],
                    "monthNames": [
                        "Tháng 1",
                        "Tháng 2",
                        "Tháng 3",
                        "Tháng 4",
                        "Tháng 5",
                        "Tháng 6",
                        "Tháng 7",
                        "Tháng 8",
                        "Tháng 9",
                        "Tháng 10",
                        "Tháng 11",
                        "Tháng 12"
                    ],
                    "firstDay": 1
                },
                "timePicker": true,
                "timePicker24Hour": true,
        }, function (start, end, label) {
                //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')'); 
                return false;
        });
        
        $('[data-range-key="Custom Range"]').text('Tuỳ chỉnh');

        $('#update').click(function() {
            var _token   = $("input[name='_token']").val();
            var daterange   = $("input[name='daterange']").val();
            var id   = $("input[name='id']").val();

            $('#loader-overlay').css('display', 'flex');
            $.ajax({
                url: "{{route('update-calendar-TN')}}",
                type: 'POST',
                data: {
                    _token: _token,
                    daterange,
                    id
                },
                success: function(data) {
                    var tr = '.tr_' + id;
                    if (!data.error) {
                        console.log('hhiheh');
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
                    $('#loader-overlay').css('display', 'none');
                }
            });

        });
        
        </script>


    <script>
        $('.refresh').click(function() {
            location.reload(true)
        });
    </script>
  </body>
</html>