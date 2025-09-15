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
</style>

<?php $type = [
    'pc' => 'Pancake',
    'ladi' => 'Ladi Page',
    'hotline' => 'Hotline',
];

?>
<div>		
    <div class="m-header-wrap">
        <div class="m-header">
            <form action="{{route('marketing-src-search')}}" method="get" style="display: flex;">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-xs-12 col-md-3 form-group">
                            <a class="home-sale-index" href="{{{route('marketing-src-search')}}}"> 
                            <span id="dnn_ctr1440_Main_MarketingTacNghiep_lblModuleTitle" class="text">Marketing dashboard</span>
                            </a>
                        </div>
                        <div class="col-sm-12 col-md-9 form-group">
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <select id="mkt_user" name="mkt_user">
                                        <option  value="-1">--Chọn Marketing--</option>
                                    
                                        @foreach ($listMktUser as $user)
                                        <option value="{{$user->id}}">{{$user->real_name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <select id="group" name="group">
                                        <option selected="selected" value="">--Chọn nhóm--</option>
                                        @foreach ($listGroup->get() as $group)
                                            <option value="{{$group->id}}">{{$group->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 form-group" style="min-height: 40px;">
                    
                        <div style="width: calc(100% - 145px); float: left;">
                            <input name="search" type="text"  value="{{ isset($search) ? $search : null}}" class="form-control" placeholder="Tên nguồn">
                        </div>
                        <div style="width: 125px; float: right;">
                            <button class="btn btn-sm btn-primary">
                                <i class="fa fa-search"></i>Tìm kiếm
                            </button>
                        </div>
                    
                    <div style="clear: both;"></div>
                </div>
            </form>
        </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<div class="box-body " style="padding-bottom: 0px;">
    <div class="row">
    </div>
</div>

<div style="clear: both; border-bottom: 1px solid #ddd;"></div>


<div class="box">
    <div class="box-body" style="box-shadow: 0 0 10px #ccc;">
        <div class="row">
            <div class="col-xs-12">
                <div id="addMktSrc" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content ">
                        <div class="modal-header">
                            <h5 class="modal-title">Thêm nguồn cho MKT</h5>
                            <button type="button" id="close-main" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <iframe src="{{route('marketing-src-add')}}" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>

                @if ($list)
                <div style="width: 100%; overflow: hidden; overflow-x: auto;">
                    <div class="dragscroll1 tableFixHead">
                        <table class="table table-bordered table-multi-select" id="tableReport">
                            <tbody>
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <span class="chk-all"><input id="dnn_ctr1427_Main_CauHinhNguonKetNoiDuLieu_chkAll" type="checkbox" name="dnn$ctr1427$Main$CauHinhNguonKetNoiDuLieu$chkAll"><label for="dnn_ctr1427_Main_CauHinhNguonKetNoiDuLieu_chkAll">&nbsp;</label></span>
                                        STT</th>
                                    <th class="text-center">Marketing</th>
                                    <th class="text-center">Tên nguồn kết nối<br>
                                        Url nguồn dữ liệu</th>
                                    <th class="text-center no-wrap">Loại kết nối</th>
                                    
                                    <th class="text-center" style="width: 80px;">
                                        <a style="cursor: pointer;" data-target="#addMktSrc" data-toggle="modal" title="Thêm nguồn" class="addMktSrc btn-icon">
                                            
                                        
                                        <i class="fa fa-plus"></i> <span class="text">Thêm</span>
                                        </a>
                                    </th>
                                </tr>

                                <?php $i = 1; ?>
                                @foreach ($list as $item)
                                <tr>
                                    <td class="text-center">
                                        <span>{{$i}}</span>
                                    </td>
                                    <td class="text-center td_landing200">
                                        {{ $item->userDigital ? $item->userDigital->real_name : ''}}<br>
                                        <span class="small-tip"> {{ ($item->userDigital ? $item->userDigital->name : '')}}</span>
                                    </td>
                                    <td class="text-left" style="max-width: 250px; height: 15px; overflow: auto">
                                        {{$item->name}}<br>
                                        <span> <a href="{{$item->link}}"> {{$item->link}} </a></span>
                                    </td>
                                    <td class="text-center">{{ array_key_exists($item->type, $type) ? $type[$item->type] : ''}}</td>
                                    
                                    <td class="text-center">
                                        <a data-target="#addMktSrc" data-toggle="modal" title="Sửa nguồn marketing" data-id="{{$item->id}}" class="addMktSrc btn-icon aoh">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>

                                <?php $i++; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- {{ $list->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
                @endif

            </div>
        </div>
    </div>
</div>

</div>

<script>
    $('.addMktSrc').on('click', function () {
        // alert('hii');
        var id = $(this).data('id');
        console.log(id);
        if (id) {
            var link = "{{URL::to('/marketing-cap-nhat-nguon/')}}";
            $("#addMktSrc iframe").attr("src", link + '/' + id);
        } else {
            var link = "{{URL::to('/marketing-them-nguon/')}}";
            $("#addMktSrc iframe").attr("src", link);
        }
        
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        $('#mkt_user').select2();
        $('#group').select2();
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

    let group = $.urlParam('group') 
    if (group) {
        $('#group option[value="' + group +'"]').attr('selected','selected');
    }
</script>

