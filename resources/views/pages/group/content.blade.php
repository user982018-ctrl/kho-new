
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
        <a class="add-order btn btn-primary" href="{{route('add-group')}}" role="button">+ Thêm nhớm</a>
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
          <table class="table table-bordered table-line">
            <thead>
              <tr>
                <th scope="col">#</th>
                
                <th scope="col">Tên nhóm</th>
                <th class="col" scope="col" >Thành viên</th>
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
                  <span><b>Data nóng: </b></span> 
                  @foreach ($gr->sales as $mem)
                    @if ($mem->type_sale == 1)
                    &nbsp; {{$mem->user->real_name}}, 
                    @endif
                  @endforeach

                  <br>
                  <span><b>Data CSKH: </b></span>
                  @foreach ($gr->sales as $mem)
                    @if ($mem->type_sale == 2)
                    &nbsp; {{$mem->user->real_name}} 
                    @endif
                  @endforeach
                @endif
                </td>
                
                <td scope="col-1">
                  <a class="btn btn-warning" href="{{route('update-group',['id'=>$gr->id])}}" role="button">
                    
                      <svg class="icon me-2">
                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                      </svg>Cập nhật
                  </a>
                </td>
                <td scope="col-1">
                  {{-- <a class="btn btn-danger active" href="{{route('delete-group',['id'=>$gr->id])}}" role="button">
                    <svg class="icon me-2">
                      <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}"></use>
                    </svg>Xoá
                  </a> --}}
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

  let time = $.urlParam('daterange') 
  if (time) {
    time = decodeURIComponent(time)
    time = time.replace('+-+', ' - ') //loại bỏ khoảng trắng
    $('input[name="daterange"]').val(time)
  }

  let sale = $.urlParam('sale') 
  if (sale) {
    $('#sale-filter option[value=' + sale +']').attr('selected','selected');
  }

  let status = $.urlParam('status') 
  if (status) {
    $('#status-filter option[value=' + status +']').attr('selected','selected');
  }

  let category = $.urlParam('category') 
  if (category) {
    $('#category-filter option[value=' + category +']').attr('selected','selected');
  }

  let product = $.urlParam('product') 
  console.log(product)
  if (product) {
    var _token      = $("input[name='_token']").val();
      $.ajax({
            url: "{{ route('get-products-by-category-id') }}",
            type: 'GET',
            data: {
                _token: _token,
                categoryId: category
            },
            success: function(data) {
             
              let str = '';
              str += '<div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">'
                + '<select name="product" id="product-filter" class="form-select" aria-label="Default select example">'
                + '<option value="999">--Sản phẩm (Tất cả)--</option>';
                data.forEach(item => {
                  // console.log(item['id'])
                  selected = item['id'] == product ? 'selected' : '';
                  str += '<option ' +  selected +' value="' + item['id'] + '">' + item['name'] + '</option>';
                  });
              str  += '</select>'
                + '</div>';

                $(str).appendTo(".filter-order");
            }
        });
    $('#product-filter option[value=' + product +']').attr('selected','selected');
  }

</script>
<script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>
<script>
$(document).ready(function() {
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

    $("#category-filter").change(function() {
      var selectedVal = $(this).find(':selected').val();
      var selectedText = $(this).find(':selected').text();
      
      if (selectedVal == 9) {
        var _token      = $("input[name='_token']").val();
        $.ajax({
          url: "{{ route('get-products-by-category-id') }}",
          type: 'GET',
          data: {
              _token: _token,
              categoryId: selectedVal
          },
          success: function(data) {
          
            let str = '';
            str += '<div class="col-xs-12 col-sm-6 col-md-2 form-group mb-1">'
              + '<select name="product" id="product-filter" class="form-select" aria-label="Default select example">'
              + '<option value="999">--Sản phẩm (Tất cả)--</option>';
              data.forEach(item => {
                // console.log(item['id'])
                str += '<option value="' + item['id'] + '">' + item['name'] + '</option>';
                });
            str  += '</select>'
              + '</div>';

              $(str).appendTo(".filter-order");
          }
        });
      } else if ($('#product-filter').length > 0) {
          $('#product-filter').parent().remove();
      }
  });

});
</script>

<script>
   const mrNguyen = [
    {
        id : '332556043267807',
        name_page : 'Rước Đòng Organic Rice - Tăng Đòng Gấp 3 Lần',
    },
    {
        id : '318167024711625',
        name_page : 'Siêu Rước Đòng Organic Rice- Hàm Lượng Cao X3',
    },
    {
        id : '341850232325526',
        name_page : 'Siêu Rước Đòng Organic Rice - Hiệu Quả 100%',
    },
    {
        id : 'mua4tang2',
        name_page : 'Ladipage mua4tang2',
    },
    {
        id : 'giamgia45',
        name_page : 'Ladipage giamgia45',
    }
];
const mrTien = [
    {
        id : 'mua4-tang2',
        name_page : 'Ladipage mua4-tang2',
    }
];

let mkt = $.urlParam('mkt') 
if (mkt) {
    $('#mkt-filter option[value=' + mkt +']').attr('selected','selected');
}

let src = $.urlParam('src') 
if (src) {
    // let str = '<option value="999">--Tất cả Nguồn--</option>';
    // $('.src-filter').show('slow');

    // if (mkt == 1) {
    //     mrNguyen.forEach (function(item) {
    //         // console.log(item);
    //         str += '<option value="' + item.id +'">' + item.name_page +'</option>';
    //     })
    //     $(str).appendTo("#src-filter");
    // } else if (mkt == 2) {
    //     mrTien.forEach (function(item) {
    //         // console.log(item);
    //         str += '<option value="' + item.id +'">' + item.name_page +'</option>';
    //     })
    //     $(str).appendTo("#src-filter");
    // }
    $('#src-filter option[value=' + src +']').attr('selected','selected');
}
  // $("#mkt-filter").change(function() {
  //   var selectedVal = $(this).find(':selected').val();
  //   var selectedText = $(this).find(':selected').text();
    
  //   let str = '<option value="999">--Tất cả Nguồn--</option>';
  //   $('.src-filter').show('slow');

  //   if ($('#src-filter').children().length > 0) {
  //     $('#src-filter').children().remove();
  //   }

  //   if (selectedVal == 1) {
  //     mrNguyen.forEach (function(item) {
  //         console.log(item);
  //         str += '<option value="' + item.id +'">' + item.name_page +'</option>';
  //     })
  //     $(str).appendTo("#src-filter");
  //   } else if (selectedVal == 2) {
  //     mrTien.forEach (function(item) {
  //         console.log(item);
  //         str += '<option value="' + item.id +'">' + item.name_page +'</option>';
  //     });
  //     $(str).appendTo("#src-filter");
  //   } else {
  //     $('.src-filter').hide('slow');
  //     $('#src-filter').children().remove();
  //   }
  // });
</script>