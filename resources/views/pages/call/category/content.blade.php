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
                <a href="{{route('category-call-add')}}" class="btn btn-primary" data-toggle="modal" data-target="#myModal" role="button">+ Thêm Loại TN</a>   
            </div>
        </div>
        <div class="tab-content rounded-bottom">
            <div style="overflow-x: auto;" class="tab-pane p-0 pt-1 active preview" role="tabpanel">
                @if (isset($categoryCall))
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Loại</th>
                            <th scope="col">Class</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($categoryCall as $item)
                        <tr>
                            <th>{{ $item->id }}</th>
                            <td>  {{ $item->name }}</td>
                            <td>  {{ $item->class }}</td>
                            <td>  {{ ($item->status) ? 'Bật' : 'Tắt' }} </td>
                            <td>
                                <a  title="sửa" href="{{route('category-call-update',['id'=>$item->id])}}" role="button">
                                
                                    <svg class="icon me-2">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                                    </svg>
                                </a>
                            
                                <?php $checkAll = isFullAccess(Auth::user()->role);?>
                                @if ($checkAll)
                                <a title="xoá" onclick="return confirm('Bạn muốn xóa loại TN này?')" href="{{route('category-call-delete',['id'=>$item->id])}}" role="button">
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
                {!! $categoryCall->links() !!}
                @endif

            </div>
        </div>
    </div>
</div>