@extends('layouts.default')
@section('content')
<div class="body flex-grow-1 px-3">
        <div class="container-lg">

        @if ($errors->any())
    <div class="col-sm-12">
        <div class="alert  alert-warning alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <span><p>{{ $error }}</p></span>
            @endforeach
        </div>
    </div>
@endif


@if (session('success'))
<div id="noti-box">
    <div class="col-sm-12">
        <div class="alert  alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

@if (session('error'))
<div id="noti-box ">
    <div class="col-sm-12">
        <div class="alert  alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
    </div>
    </div>
@endif

          <div class="card mb-4">
            <div class="card-header"><strong>Quản lý danh mục hàng đông lạnh</strong> </div>
            <div class="card-body">
              <div class="example mt-0">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item"><a class="nav-link active" data-coreui-toggle="tab" href="#preview-1001" role="tab">
                      <svg class="icon me-2">
                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-media-play')}}"></use>
                      </svg>Danh sách</a></li>
                </ul>
                <div class="tab-content rounded-bottom">
                  <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">
                    
                        <div class="row ">

                          <?php $checkAll = isFullAccess(Auth::user()->role);?>
                          @if ($checkAll)
                          <div class="col col-4">
                            <a class="btn btn-primary" href="{{route('add-category')}}" role="button">+ Thêm mới</a>
                          </div>
                          @endif
                          <div class="col-8 ">
                            <form class ="row tool-bar d-flex justify-content-end" action="{{route('search-category')}}" method="get">
                              <div class="col-3">
                                <input class="form-control" name="search" placeholder="Tìm danh mục..." type="text">
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
                              <table class="table table-striped">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    
                                    <th scope="col" style="width:30%">Tên danh muc</th>
                                    <th scope="col" style="width:30%">Trạng thái</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                  </tr>
                                </thead>
                                <tbody>

                                @foreach ($list as $item)
                                  
                               
                                  <tr>
                                    <th scope="row col-1">{{ $item->id }}</th>
                                    <td scope="col-7" >  {{ $item->name }}</td>
                                    <td scope="col-1">  {{ ($item->status == 1) ? 'Bật' : 'Tắt'; }}</td>
                                    <td scope="col-1">

                                      <?php $checkAll = isFullAccess(Auth::user()->role);?>
                                      @if ($checkAll)
                                    <a class="btn btn-warning" href="{{route('update-category',['id'=>$item->id])}}" role="button">
                                        <svg class="icon me-2">
                                          <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                                        </svg>Sửa
                                    </a>
                                    @endif

                                    </td>
                                    <td scope="col-1">

                                      <?php $checkAll = isFullAccess(Auth::user()->role);?>
                                      @if ($checkAll)
                                      <a class="btn btn-danger active" href="{{route('delete-category',['id'=>$item->id])}}" role="button">
                                        <svg class="icon me-2">
                                          <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}"></use>
                                        </svg>Xoá
                                      </a>
                                      @endif
                                      
                                    </td>
                                  </tr>
                                  @endforeach
                                  
                                </tbody>
                              </table>
                              {!! $list->links() !!}
                            </div>
                          </div>
                        </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<script>
  $(document).ready(function() {
    $("#noti-box").slideDown('fast').delay(5000).hide(0);
        
    if ($(window ).width() < 600) {
        console.log($(window ).width());
        $('.tool-bar button').text('Tìm');
    }
  });
</script>
@stop