<style>
    #laravel-notify .notify {
        z-index: 2;
    }
    .header.header-sticky {
        z-index: unset;
    }

</style>
<div class="tab-content rounded-bottom">
    <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">
        <div class="row ">
            <div class="col col-4">
                <a href="{{route('call-result-add')}}" class="btn btn-primary" data-toggle="modal" data-target="#myModal" role="button">+ Thêm Kết Quả TN</a>   
            
            </div>
            <div class="col-8">
                <form class ="row tool-bar d-flex justify-content-end" action="{{route('call-result-search')}}" method="get">
                  <div class="col-3">
                    <input class="form-control" name="search" placeholder="Tìm kết quả..." type="text">
                  </div>
                  <div class="col-3 " style="padding-left:0;">
                    <button type="submit" class="btn btn-primary"><svg class="icon me-2">
                                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-search')}}"></use>
                              </svg>Tìm</button>
                </form>
            </div>
        </div>
        <div class="tab-content rounded-bottom">
            <div style="overflow-x: auto;" class="tab-pane p-0 pt-1 active preview" role="tabpanel">
                @if (isset($callResult))
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Kết quả</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Ngày tạo</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($callResult as $item)
                        <tr>
                            <th>{{ $item->id }}</th>
                            <td>  {{ $item->name }}</td>
                            <td>  {{ ($item->status) ? 'Bật' : 'Tắt' }} </td>
                            <td> {{ $item->created_at }}</td>
                            <td>
                                <a  title="sửa" href="{{route('call-result-update',['id'=>$item->id])}}" role="button">
                                
                                    <svg class="icon me-2">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                                    </svg>
                                </a>
                            
                                <?php $checkAll = isFullAccess(Auth::user()->role);?>
                                @if ($checkAll)
                                <a title="xoá" onclick="return confirm('Bạn muốn xóa kết quả TN này?')" href="{{route('call-result-delete',['id'=>$item->id])}}" role="button">
                                    <svg class="icon me-2">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}"></use>
                                    </svg>
                                </a>
                                @endif
                            </td>
                            
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                {!! $callResult->links() !!}
                @endif

            </div>
        </div>
    </div>
</div>