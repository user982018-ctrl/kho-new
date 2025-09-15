<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


<style>
  .nav-tabs {
      border-bottom: 1px solid #ddd;
  }
  .nav {
      padding-left: 0;
      margin-bottom: 0;
      list-style: none;
  }
  .btn-primary {
      color: #fff;
      background-color: #337ab7;
      border-color: #2e6da4;
  }
  a {
      color: #337ab7;
      text-decoration: none;
  }
  .btn {
      display: inline-block;
      margin-bottom: 0;
      font-weight: 400;
      text-align: center;
      white-space: nowrap;
      vertical-align: middle;
      -ms-touch-action: manipulation;
      touch-action: manipulation;
      cursor: pointer;
      background-image: none;
      border: 1px solid transparent;
      padding: 6px 12px;
      font-size: 14px;
      line-height: 1.42857143;
      border-radius: 4px;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
  }
    .dropdown, .dropup {
      position: relative;
  }
    .open>.dropdown-menu {
      display: block;
  }
  .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 1000;
      display: none;
      float: left;
      min-width: 160px;
      padding: 5px 0;
      margin: 2px 0 0;
      font-size: 14px;
      text-align: left;
      list-style: none;
      background-color: #fff;
      background-clip: padding-box;
      border: 1px solid #ccc;
      border: 1px solid rgba(0, 0, 0, .15);
      border-radius: 4px;
      -webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
      box-shadow: 0 6px 12px rgba(0,0,0,.175);
  }
  .dropdown-filter {
    padding-bottom: 5px;
  }
  .caret {
      display: inline-block;
      width: 0;
      height: 0;
      margin-left: 2px;
      vertical-align: middle;
      border-top: 4px dashed;
      border-top: 4px solid\9;
      border-right: 4px solid transparent;
      border-left: 4px solid transparent;
  }
  .dropdown-menu>li>a {
      display: block;
      padding: 3px 20px;
      clear: both;
      font-weight: 400;
      line-height: 1.42857143;
      color: #333;
      white-space: nowrap;
  }
  .dropdown-menu>li>a:focus, .dropdown-menu>li>a:hover {
      color: #262626;
      text-decoration: none;
      background-color: #f5f5f5;
  }
</style>

  <div class="tab-content rounded-bottom">
    <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">

        <div class="row ">
            <div class="col col-3">
                <div class="dropdown dropdown-filter" >
                    <button id="filter-month-year-btn" class="btn btn-primary" type="button" data-toggle="dropdown">
                        Lọc theo tháng / Năm
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a id="month">Tháng</a></li>
                        <li><a id="year">Năm</a></li>
                    </ul>
                </div>
           
                <div id="filter-by-month" class="dropdown hidden">
                    <button class="btn btn-primary" type="button" data-toggle="dropdown">
                        --Tất cả 12 tháng--
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">

                      <?php for ($i = 1; $i <= 12; $i++) :?>
                        <li><a href="{{URL::to('nhap-hang-theo-thang?month='.$i)}}" id="month-<?= $i ?>">Tháng <?= $i ?></a></li>
                      <?php endfor ?> 

                    </ul>
                </div>

                <div id="filter-by-year" class="dropdown hidden">
                    <button class="btn btn-primary" type="button" data-toggle="dropdown">
                        --Tất cả--
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">

                      <?php for ($i = 2022; $i <= 2025; $i++) :?>
                        <li><a href="{{URL::to('nhap-hang-theo-nam?year='.$i)}}" id="year-<?= $i ?>">Năm <?= $i ?></a></li>
                      <?php endfor ?> 

                    </ul>
                </div>
            </div>
            <div class="col-8 ">
                <form class="row d-flex justify-content-end" action="{{route('search-product')}}" method="get">
                    <div class="col-3">
                        <input class="form-control" name="search" placeholder="Tìm sản phẩm..." type="text">
                    </div>
                    <div class="col-3 " style="padding-left:0;">
                        <button type="submit" class="btn btn-primary"><svg class="icon me-2">
                                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-search')}}">
                                </use>
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

                            <th scope="col" style="width:30%">Tên Sản Phẩm</th>
                            <th scope="col">Giá</th>
                            <th scope="col">Số Lượng</th>
                            <th scope="col">Ngày nhập</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($list as $item)

                        <tr>
                            <th scope="row col-1">{{ $item->id }}</th>
                            <td scope="col-7"> {{ $item->name }}</td>
                            <td scope="col-1"> {{ $item->price }} đ</td>
                            <td scope="col-1"> {{ $item->qty }} xô</td>
                            <td scope="col-1"> {{ date_format($item->created_at,"H:i:s d-m-Y ")}}</td>
                            <td scope="col-1">
                                <a class="btn btn-warning" href="{{route('update-product',['id'=>$item->id])}}"
                                    role="button">

                                    <svg class="icon me-2">
                                        <use
                                            xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}">
                                        </use>
                                    </svg>Sửa
                                </a>
                            </td>
                            <td scope="col-1">
                                <a class="btn btn-danger active" href="{{route('delete-product',['id'=>$item->id])}}"
                                    role="button">
                                    <svg class="icon me-2">
                                        <use
                                            xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}">
                                        </use>
                                    </svg>Xoá
                                </a>
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
<script>
$( document ).ready(function() {
  let queryString = window.location.search;
  let urlParams   = new URLSearchParams(queryString);
  let month       = urlParams.get('month');
  let year        = urlParams.get('year');

  if (month > 0) {
    $('#filter-month-year-btn').removeClass('btn-primary');
    $('#filter-month-year-btn').html('Lọc theo tháng <span class="caret"></span>');

    $('#filter-by-month').removeClass('hidden');
    $('#filter-by-month .btn').removeClass('btn-primary');
    $('#filter-by-month .btn').html('Tháng ' + month + ' <span class="caret"></span>')
  }

  if (year > 0) {
    $('#filter-month-year-btn').removeClass('btn-primary');
    $('#filter-month-year-btn').html('Lọc theo năm <span class="caret"></span>');

    $('#filter-by-year').removeClass('hidden');
    $('#filter-by-year .btn').removeClass('btn-primary');
    $('#filter-by-year .btn').html('Năm ' + year + ' <span class="caret"></span>')
  }

  $('#month').on( "click", function() {
    $('#filter-by-year').addClass('hidden');
    $('#filter-by-month').removeClass('hidden');
    $('#filter-month-year-btn').html('Lọc theo tháng <span class="caret"></span>')
      .removeClass('btn-primary');
      // $('#filter-by-month').text('hidden');
  });

  $('#year').on( "click", function() {
    $('#filter-by-month').addClass('hidden');
    $('#filter-by-year').removeClass('hidden');
    $('#filter-month-year-btn').html('Lọc theo năm <span class="caret"></span>')
      .removeClass('btn-primary');
  });
 
});
</script>