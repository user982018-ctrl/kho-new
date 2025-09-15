
<div class="tab-content rounded-bottom">
    <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">
    
        <div class="row ">
        <div class="col col-4">
            
            <a href="{{route('call-add')}}" class="btn btn-primary" data-toggle="modal" data-target="#myModal" role="button">+ Thêm đơn</a>   
        
        </div>
        
        </div>
        <div class="example-custom example mt-0">
        <div class="tab-content rounded-bottom">
            <div style="overflow-x: auto;" class="tab-pane p-0 pt-1 active preview" role="tabpanel" id="preview-1002">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" >Tên</th>
                    <!-- <th scope="col">Địa chỉ</th> -->
                    <th scope="col">Thời gian</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Thao tác</th>
                </tr>
                </thead>
                <tbody>
    
                @if (isset($call))
                @foreach ($call as $item)
                

                <tr>
                    
                    <th>{{ $item->id }}</th>
                    <td>  {{ $item->name }}</td>
                    <td>  {{ $item->time }} </td>
                    <td >  {{ ($item->status) ? 'Bật' : 'Tắt' }} </td>
                    <td>
                        <a  title="sửa" href="{{route('call-update',['id'=>$item->id])}}" role="button">
                        
                            <svg class="icon me-2">
                            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                            </svg>
                        </a>
                    </td>
                    
                    <td >
                    <?php $checkAll = isFullAccess(Auth::user()->role);?>
                    @if ($checkAll)
                    <a title="xoá" onclick="return confirm('Bạn muốn xóa đơn này?')" href="{{route('delete-order',['id'=>$item->id])}}" role="button">
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
            {!! $call->links() !!}
            @endif

            </div>
        </div>
    </div>
</div>