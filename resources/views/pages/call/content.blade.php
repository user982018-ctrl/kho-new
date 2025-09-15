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
                <a href="{{route('call-add')}}" class="btn btn-primary" data-toggle="modal" data-target="#myModal" role="button">+ Thêm TN</a>   
            </div>
            <div class="col-8">
                <form class ="row tool-bar d-flex justify-content-end" action="{{route('call-search')}}" method="get">
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
                @if (isset($call))
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nếu</th>
                            <th scope="col">Kết quả</th>
                            <th scope="col">Thì</th>
                            <th scope="col">Sau bao lâu</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $i = 1; ?>
                        @foreach ($call as $item)
                        <tr>
                            <td>  {{ $i }}</td>
                            <td> {{ ($item->ifCall) ? $item->ifCall->name : '' }}</td>
                            <td> {{  ($item->callResult) ? $item->callResult->name : '' }}</td>
                            <td> {{ ($item->thenCall) ? $item->thenCall->name : '' }}</td>
                            <td> {{ $item->time }} </td>
                            <td> {{ ($item->status) ? 'Bật' : 'Tắt' }} </td>
                            <td>
                                <a  title="sửa" href="{{route('call-update',['id'=>$item->id])}}" role="button">
                                
                                    <svg class="icon me-2">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                                    </svg>
                                </a>
                            
                                <?php $checkAll = isFullAccess(Auth::user()->role);?>
                                @if ($checkAll)
                                <a title="xoá" onclick="return confirm('Bạn muốn xóa TN này?')" href="{{route('call-delete',['id'=>$item->id])}}" role="button">
                                    <svg class="icon me-2">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}"></use>
                                    </svg>
                                </a>
                                @endif
                            </td>
                        </tr>
                        <?php $i++;?>
                        @endforeach

                    </tbody>
                </table>
                {!! $call->appends(request()->input())->links() !!}
                @endif

            </div>
        </div>
    </div>
</div>