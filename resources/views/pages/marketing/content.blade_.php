<style>
.box-progress .progress-text 
{
    position: absolute;
    height: 100%;
    width: 100%;
    text-align: center;
    top: 1px;
    vertical-align: middle;
    line-height: 200%;
}

.table-bordered td {
    border-right: 1px solid #f4f4f4 !important;
    border-left: 1px solid #f4f4f4 !important;
    border: 1px solid #f4f4f4 !important;
}

#daterange {
    width: 100%;
    border: 1px solid #ddd;
}
</style>
<?php $type = [
    'pc' => 'Pancake',
    'ladi' => 'Ladi Page',
    'hotline' => 'Hotline',
];

$sumContact = $sumOrder = $sumRate = $sumProduct = $sumTotal = $sumAvg = 0;

// dd($list);
if ($list) {
    foreach ($list as $key => $value) {
        $sumContact += $value['contact'];
        $sumTotal += $value['total'];
        $sumProduct += $value['product'];
    
        $sumOrder += $value['order'];

        if ($sumContact > 0) {
            $sumRate = round($sumOrder / $sumContact * 100, 2);
        }
    }
}

if ($sumOrder) {
    $sumAvg = $sumTotal / $sumOrder;
}

?>

<div>
    <form action="{{route('marketing-TN')}}" method="GET">
        <div class="m-header-wrap">
            <div class="m-header" style="display: flex;">
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-xs-12 col-md-3 form-group">
                            <a class="home-sale-index" href="{{{route('marketing-TN')}}}"> 
                                <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblModuleTitle" class="text">Marketing dashboard</span>
                            </a>
                        </div>
                        
                        <div class="col-sm-12 col-md-9 form-group">
                            <div class="row">
                                <div class="col-xs-12 col-md-4 form-group ">
                                    <select id="mkt_user" name="mkt_user">
                                        <option selected="selected" value="-1">--Chọn Marketing--</option>
                                        @foreach ($listMktUser as $user)
                                            <option value="{{$user->id}}">{{$user->real_name}} </option>
                                        @endforeach
                                    </select>
                                </div>

                                
                                <div class="col-xs-12 col-md-4 form-group">
                                    <select id="type_customer" name="type_customer">
                                        <option selected="selected" value="-1">--Tất cả khách--</option>
                                       <option value="0">Khách mới</option>
                                       <option value="1">Khách cũ</option>
                                    </select>
                                </div>
                                {{-- <div class="col-sm-3 form-group">
                                    <select id="group" name="group">
                                        <option selected="selected" value="">--Chọn nhóm--</option>
                                        @foreach ($listGroup->get() as $group)
                                            <option value="{{$group->id}}">{{$group->name}} </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-sm-2 form-group" style="min-height: 40px;">
                    <button class="btn btn-sm btn-primary">
                        <i class="fa fa-search"></i>Tìm kiếm
                    </button>
                </div>
            </div>
        </div>

        <div class="box-body" style="background: #fff;">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                    <input id="daterange" class="btn" type="text" name="daterange" />
                </div>
                <div class="col-xs-12 col-sm-6 col-md-2 form-group">
                    <select name="src" id="src-filter" class="hidden">       
                        <option value="-1">--Chọn nguồn--</option>

                        @foreach ($listSrc as $page) 
                        <option value="{{$page['id']}}">{{($page['name']) ? : $page['name']}}</option>
                        @endforeach

                    </select>
                </div>
                
            </div>
        </div>
    </form>
<div style="clear: both; border-bottom: 1px solid #ddd;"></div>


<div class="box">
    <div class="box-body">
        <div class="dragscroll1 tableFixHead" style="height: 874px;">
            @if ($list)
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center chk_advanced_ttnguon" colspan="4" style="top: 0.5px;">THÔNG TIN NGUỒN DỮ LIỆU</th>
                        <th class="text-center chk_advanced_tthieuqua" colspan="5" style="top: 0.5px;">THÔNG TIN HIỆU QUẢ MARKETING</th>
                    </tr>
                    <tr style="cursor: grab;" class="drags-area">
                        <th class="text-center" style="width: 40px; top: 28.6406px;">STT</th>
                        <th class="text-center hidden" style="width: 150px; top: 0px;">MKT</th>
                        <th class="text-center" style="top: 28.6406px;">Tên Nguồn dữ liệu</th>

                        <th class="text-center" style="width: 110px; top: 28.6406px;">Số contact</th>
                        <th class="text-center" style="width: 90px; top: 28.6406px;">Chốt đơn</th>
                        <th class="text-center" style="width: 110px; top: 28.6406px;">Tỷ lệ chốt đơn (%)</th>
                        <th class="text-center" style="width: 110px; top: 28.6406px;">Số sản phẩm </th>
                        <th class="text-center" style=" top: 28.6406px;"><span>Doanh số</span> </th>
                        <th class="text-center" style=" top: 28.6406px;"><span>Trung bình đơn</span> </th>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center font-weight-bold">Tổng: </td>
                      
                        <td class="text-center font-weight-bold">
                            <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblTongSoContact">{{$sumContact}}</span></td>
                        <td class="text-center font-weight-bold">
                            <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblTongChotDon">{{$sumOrder}}</span></td>
                        <td class="text-center font-weight-bold">
                            <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblTongTyLeChotDon">{{$sumRate}} %</span></td>
                        <td class="text-center font-weight-bold">
                            <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblTongSoSanPham">{{$sumProduct}}</span></td>
                        <td class="text-center font-weight-bold">
                            <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblTongDoanhSo">{{number_format($sumTotal)}}</span></td>
                            <td class="text-center font-weight-bold">
                                <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblTongDoanhSo">{{number_format($sumAvg)}}</span></td>
                    </tr>
                </thead>
                
                <tbody>

                    <?php $i = 1; 
                   
                    $firstData = reset( $list);
                    // dd($firstData);
                    $maxTotal = $firstData['total'];
                    $maxAvg = $firstData['avg'];
                    foreach ($list as $item) {
                        if ($item['total'] > $maxTotal) {
                            $maxTotal = $item['total'];
                        }

                        if ($item['avg'] > $maxAvg) {
                            $maxAvg = $item['avg'];
                        }
                    }
                    
                    ?>
                    @foreach ($list as $item)
                    
                    <tr class="item360145 level1  ">
                        <td class="text-center">
                            {{$i}}
                        </td>
                        <td style="position: relative;" title=" Di_Ladi_Uudaihot">
                                {{$item['name']}}
                        </td>
                        
                        <td class="text-center">{{$item['contact']}}</td>
                        
                        <td class="text-center">{{$item['order']}}</td>
                        
                        <td class="tdProgress tdTiLeChotDon">
                            <div class="box-progress">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: {{$item['rate']}}%"></div>
                                </div>
                                <span class="progress-text">{{$item['rate']}}% </span>
                            </div>
                        </td>
                        
                        <td class="text-center">{{$item['product']}}</td>
                        
                        <td class="tdProgress tdDoanhSo">
                            <div class="box-progress">

                                <?php $perCentTotal = ($maxTotal != 0) ? ($item['total'] / $maxTotal * 100) : 0;?>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: {{$perCentTotal}}%"></div>
                                </div>
                                <span class="progress-text">{{number_format($item['total'])}}</span>
                            </div>
                        </td>
                        <td class="tdProgress tdDoanhSo">
                            <div class="box-progress">

                                <?php $perCentAvg = ($maxAvg != 0) ? ($item['avg'] / $maxAvg * 100) : 0;?>

                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: {{$perCentAvg}}%"></div>
                                </div>
                                <span class="progress-text">{{number_format($item['avg'])}}</span>
                            </div>
                        </td>
                    </tr>

                    <?php $i++; ?>
                    @endforeach
                   
                </tbody>
            </table>

            {{-- {{ $list->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
            @endif
        </div>
    </div>
</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        $('#mkt_user').select2();
        $('#group').select2();
        $('#src-filter').select2();
        $('#type_customer').select2();
        
    });

    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results) {
            return results[1];
        }
        return 0;
    }

    let search = $.urlParam('search') 
    if (search) {
        $('input[name="search"]').val(search)
    }

    let mkt = $.urlParam('mkt_user') 
    if (mkt) {
        $('#mkt_user option[value="' + mkt +'"]').attr('selected','selected');
    }

    let type = $.urlParam('type_customer') 
    if (type) {
        $('#type_customer option[value="' + type +'"]').attr('selected','selected');
    }

    let group = $.urlParam('group') 
    if (group) {
        $('#group option[value="' + group +'"]').attr('selected','selected');
    }

    let time = $.urlParam('daterange') 
    if (time) {
        time = decodeURIComponent(time)
        time = time.replace('+-+', ' - ') //loại bỏ khoảng trắng
        $('input[name="daterange"]').val(time)
    }

    let src = $.urlParam('src') 
    if (src && src != 999) {
        $('#src-filter option[value=' + src +']').attr('selected','selected');
        $('#src-filter').parent().addClass('selectedClass');
    }
</script>

<script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>
<script>
    $('input[name="daterange"]').daterangepicker({
      ranges: {
        'Hôm nay': [moment(), moment()],
        'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '7 ngày gần đây': [moment().subtract(6, 'days'), moment()],
        '30 ngày gần đây': [moment().subtract(29, 'days'), moment()],
        'Tháng này': [moment().startOf('month'), moment().endOf('month')],
        'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      locale: {
        "format": 'DD/MM/YYYY',
        "applyLabel": "OK",
        "cancelLabel": "Huỷ",
        "fromLabel": "Từ",
        "toLabel": "Đến",
        "daysOfWeek": [
          "CN", "Hai", "Ba", "Tư", "Năm", "Sáu", "Bảy" 
        ],
        "monthNames": [
          "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
	        "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12" 
        ],
      }
    });
    $('[data-range-key="Custom Range"]').text('Tuỳ chỉnh');
</script>