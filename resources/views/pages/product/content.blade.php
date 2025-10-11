
<div class="tab-content rounded-bottom">
  <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">
                    
    <div class="row ">
      <?php $checkAll = isFullAccess(Auth::user()->role);
      $isKho = Helper::isKho(Auth::user());
      ?>
      @if ($checkAll)
        <div class="col col-4">
          <a class="btn btn-primary" href="{{route('add-product')}}" role="button">+ Thêm mới</a>
          {{-- <a class="btn btn-primary" href="{{route('add-combo')}}" role="button">+ Thêm combo</a> --}}
        </div>
      @endif

      <div class="col-8 ">
        <form class ="row tool-bar d-flex justify-content-end" action="{{route('search-product')}}" method="get">
          <div class="col-3">
            <input class="form-control" name="search" placeholder="Tìm sản phẩm..." type="text">
          </div>
          <div class="col-3 " style="padding-left:0;">
            <button type="submit" class="btn btn-primary"><svg class="icon me-2">
                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-search')}}"></use>
                      </svg>Tìm</button>
        </form>
      </div>
    </div>
  </div>
  <div class="example mt-0">
    <div class="tab-content rounded-bottom">
      <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1002">
        <table class="table table-bordered table-line">
          <thead>
            <tr>
              <th>#</th>
              
              <th colspan="1" class="text-center no-wrap col-spname">Tên sản phẩm</th>
              <th colspan="1" class="text-center no-wrap">Tên khai thuế</th>
              <th colspan="1" class="text-center no-wrap">Giá</th>
              <th colspan="1" class="text-center no-wrap">Số lượng</th>
              <th colspan="1" class="text-center no-wrap">Đơn vị tính</th>
              <th colspan="1" class="text-center no-wrap">Trạng thái</th>
              <th colspan="1" class="text-center no-wrap">Ngày nhập</th>
              <th colspan="1" class="text-center no-wrap"></th>
              <th colspan="1" class="text-center no-wrap"></th>
            </tr>
          </thead>
          <tbody>

          @foreach ($list as $item)
    
            <tr>
              <td>{{ $item->id }}</td>
              <td>  {{ $item->name }}</td>
              <td >  {{ $item->tax_name }}</td>
              <td>  {{ number_format($item->price)}} đ</td>
              <td>  {{ $item->qty }}</td>
                <td>  {{ $item->unit }}</td>
              <td>  {{ ($item->status == 1) ? 'Bật' : 'Tắt'; }}</td>
              <td>  {{ date_format($item->created_at,"H:i:s d-m-Y ")}}</td>
              <td >
                
              @if ($checkAll || $isKho)

              <a href="{{route('update-product',['id'=>$item->id])}}" role="button">
                
                  <svg class="icon me-2">
                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                  </svg>
              </a>
              
              @endif

              </td>
              <td scope="col-1">

                @if ($checkAll)

                <a onclick="return confirm('Xoá sản phẩm?')" href="{{route('delete-product',['id'=>$item->id])}}" role="button">
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
        {{ $list->appends(request()->input())->links() }}
      </div>
    </div>
  </div>
</div>
  
