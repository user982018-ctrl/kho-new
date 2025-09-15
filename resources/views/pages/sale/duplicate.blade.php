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
    </style>
  </head>
  <body>
    
    @include('notify::components.notify')

    <div class="box">
        <div class="box-body">
            <div class="dragscroll1 tableFixHead" style="height: 874px;">
                {{-- @if ($list) --}}
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" scope="col" style="width:20px; top: 0.5px;">#</th>
                            <th class="text-center" scope="col" style=" top: 0.5px;">Nguồn dữ liệu <br>
                            Ngày data về</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Họ tên <br>
                            Số điện thoại</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Tin nhắn</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Note TN</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Sale</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Tác nghiệp</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Kết quả</th>
                            
                        </tr>                    

                    </thead>
                    
                    <tbody>
                        @if ($list)
                        <?php $i = 1; ?>
                        @foreach ($list as $dup)

                        <tr>
                            <td class="text-center">{{$i}}</td>
                            <td class="text-center">
                                {{$dup->page_name}}
                                <br>
                                {{date_format($dup->created_at,"H:i d-m-Y ")}}</td>
                            <td class="text-center">{{$dup->full_name}} <br> {{$dup->phone}}
                                <?php 
                                /*
                                old_customer: 1 -> hiển thị trái tim
                                check khách cũ đơn thành công thì hiện trái tim
                                */
                                $oldCustomer = Helper::isOldCustomer($dup->phone, $dup->group_id);
                                $scOldCutomer = ($oldCustomer) ? $oldCustomer->id : 0;
                                ?>

                                @if ($dup->old_customer == 1 || $dup->has_old_order == 1)
                                <a title="Khách cũ, khách cũ" class="btn-icon">
                                    <i class="fa fa-heart" style="color:red;"></i>
                                </a>
                                @endif

                                @if ($dup->is_duplicate)
                                    <svg  class="icon me-2" style="color: #ff0000">
                                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-copy')}}"></use>
                                    </svg>
                                @endif
                            </td>
                            <td class="text-center">{{$dup->messages}}</td>
                            <td class="text-center">
                                <?php if ($dup->listHistory->count() > 0) {
                                    foreach ($dup->listHistory as $key => $value) {
                                        echo date_format($value->created_at,"d/m") . ' ' . $value->note . "<br>";
                                        // echo date_format($value->created_at,"d/m") . ' ' . $value->note;
                                    } 
                                } else {
                                    echo $dup->TN_can;
                                }?>
                            </td>
                            <td class="text-center">{{($dup->user) ? $dup->user->real_name : ''}} </td>
                            <td>
                                

                                @if (!$dup->type_TN)
                                    @if (!$dup->old_customer)
                                    <span class="fb span-col ttgh7" style="cursor: pointer; width: calc(100% - 60px);">Data nóng</span> 
                                    @elseif ($dup->old_customer == 1)
                                    <span class="fb span-col" style="cursor: pointer; width: calc(100% - 60px);">CSKH</span> 
                                    @elseif ($dup->old_customer == 2)
                                    <span class="fb span-col" style="cursor: pointer; width: calc(100% - 60px);">Hotline</span> 
                                    @endif
                                @else
                                <span class="fb span-col  <?= ($dup->has_TN) ?: 'ttgh7' ?>" style="cursor: pointer; width: calc(100% - 60px);"> {{$dup->typeTN->name}}</span>
                                @endif
                                
                            </td>
                            <td>{{ ($dup->resultCall) ? $dup->resultCall->callResult->name : ""}}</td>
                        </tr>
                        <?php $i++; ?>
                        @endforeach
                        @endif
                       
                    </tbody>
                </table>
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
    <script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/notify.js')}}"></script>

    <script>
        $('.refresh').click(function() {
            location.reload(true)
        });
    </script>
  </body>
</html>