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
    </style>
  </head>
  <body>
    
    @include('notify::components.notify')

    <div class="box">
        <div class="box-body">
            <h6>{{$saleCare->full_name}} - {{$saleCare->phone}}</h6>
            @if ($saleCare->TN_can)
            <div style="padding: 10px"><?php echo $saleCare->TN_can; ?></div>
            @endif 
           
            <div class="dragscroll1 tableFixHead" style="height: 874px;">
                {{-- @if ($list) --}}
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" scope="col" style="width:20px; top: 0.5px;">STT</th>
                            <th class="text-center" scope="col" style=" top: 0.5px;">Thời gian</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Nội dung</th>
                            <th class="text-center" scope="col" style="top: 0.5px;">Hình ảnh</th>
                            <th class="text-center" scope="col" style=" top: 0.5px;">
                                <a href="{{route('sale-view-save-TN-box', ['id' => $saleId])}}" style="cursor: pointer; text-decoration-line: underline !important;text-decoration-color: blue; !important" data-target="#addMktSrc" title="TN hôm nay" class="btn-icon">
                                <i class="fa fa-pen"></i> <span class="text">TN hôm nay</span>
                                </a>
                            </th>
                        </tr>                    

                    </thead>
                    
                    <tbody>
                        @if ($listHistory)
                        <?php $i = 1; ?>
                        @foreach ($listHistory as $his)
                        <tr>
                            <td class="text-center">{{$i}}</td>
                            <td class="text-center">{{date_format($his->created_at,"H:i d-m-Y ")}}</td>
                            <td class="text-center">{{$his->note}}</td>
                            <td class="text-center">
                                @if ($his->img)
                                @foreach (json_decode($his->img) as $img)
                                <a style="display: inline-block;" href="{{asset('public/files/' . $img)}}" target="_blank">
                                    <img width="100px" src="{{asset('public//files/' . $img)}}">
                                </a>
                                @endforeach
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        <?php $i++; ?>
                        @endforeach
                        @endif
                       
                    </tbody>
                </table>
    
                {{-- {{ $list->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
                {{-- @endif --}}
                <div class="row text-right">
                    <div><button class="refresh btn btn-info">Refresh</button></div>
                </div>
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