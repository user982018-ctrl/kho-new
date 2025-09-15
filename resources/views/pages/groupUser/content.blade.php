
<?php 
  $checkAll = isFullAccess(Auth::user()->role);
  $isLeadSale = Helper::isLeadSale(Auth::user()->role);

  $listStatus = Helper::getListStatus();
  $styleStatus = [
    0 => 'red',
    1 => 'white',
    2 => 'orange',
    3 => 'green',
  ];
  $listSale = Helper::getListSale(); 
  $checkAll = isFullAccess(Auth::user()->role);
  // $isLeadSale = Helper::isLeadSale(Auth::user()->role);      
  $flag = false;

  if (($listSale->count() > 0 &&  $checkAll)) {
      $flag = true;
  }

?>

<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

<div class="tab-content rounded-bottom">
  <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">
    <div class="row">

      @if ($checkAll)
      <div class="col col-4">
        <a class="add-order btn btn-primary" href="{{route('add-group-user')}}" role="button">+ Thêm nhóm</a>
      </div>
      

      <div class="col-8 ">
        <form class ="row tool-bar" action="{{route('search-order')}}" method="get">
          <div class="col-3">
            <input class="form-control" value="{{ isset($search) ? $search : null}}" name="search" placeholder="Tìm..." type="text">
          </div>
          <div class="col-3 " style="padding-left:0;">
            <button type="submit" class="btn btn-primary"><svg class="icon me-2">
              <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-search')}}"></use>
            </svg>Tìm</button>
        </form>
      </div>
      @endif

      </div>
    </div>
    <div class="example-custom example mt-0">
      <div class="tab-content rounded-bottom">
        <div style="overflow-x: auto;" class="tab-pane p-0 pt-1 active preview" role="tabpanel" id="preview-1002">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Tên nhóm</th>
                <th class="col" scope="col" >Thành viên</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col"></th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              @if (isset($list))
              <?php $i = 1; ?>
              @foreach ($list as $gr)
              <tr>
                <td><?= $i ?></td>
                <td>{{$gr->name}}</td>
                <td>
                  @if ($gr->sales)
                    @foreach ($gr->sales as $mem)
                      &nbsp; {{$mem->user->real_name}} <br>
                    @endforeach

                  @endif
                </td>
                <td>{{($gr->status ? 'Bật' : 'Tắt')}}</td>
                <td>{{$gr->created_at}}</td>
                <td scope="col-1">
                  <a class="btn btn-warning" href="{{route('update-group-user',['id'=>$gr->id])}}" role="button">
                      <svg class="icon me-2">
                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                      </svg>Cập nhật
                  </a>
                </td>
                <td scope="col-1">
                  @if ($checkAll)
                  <a class="btn btn-danger active" onclick="return confirm('Bạn muốn xóa nhóm này?')" href="{{route('delete-group-user',['id'=>$gr->id])}}" role="button">
                    <svg class="icon me-2">
                      <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}"></use>
                    </svg>Xoá
                  </a>
                  @endif
                </td>
              </tr>
              <?php $i++; ?>
              @endforeach
              @endif
            </tbody>
           
          </table>
          {{-- {{$list->links('pagination::bootstrap-5')}} --}}
         
         
        </div>
      </div>
    </div>
</div>
  
<script>
  $.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results) {
      return results[1];
    }
    return 0;
  }
</script>