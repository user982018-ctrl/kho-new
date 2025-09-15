<style>
    .example-custom {
      font-size: 13px;
    }
    /* .header.header-sticky {
      display: none;
    } */
  
    .green span {
      width: 75px;
      display: inline-block; 
      color: #fff;
      background: #0f0;
      padding: 3px;
      border-radius: 8px;
      border: 1px solid #0f0;
      font-weight: 700;
    }
  
    .red span {
      width: 75px;
      display: inline-block; 
      color: #ff0000;
      background: #fff;
      padding: 3px;
      border-radius: 8px;
      border: 1px solid #ff0000;
      font-weight: 700;
    }
  
    .orange span {
      width: 75px;
      display: inline-block;
      color: #fff;
      background: #ffbe08;
      padding: 3px;
      border-radius: 8px;
      border: 1px solid #fff;
      font-weight: 700;
    }
    #myModal .modal-dialog {
      /* margin-top: 5px;
      width: 1280px; */
      /* margin: 10px; */
      height: 90%;
      /* background: #0f0; */
    }
    #myModal .modal-dialog iframe {
      /* 100% = dialog height, 120px = header + footer */
      height: 100%;
      overflow-y: scroll;
    }
  
    #myModal .modal-dialog .modal-content {
      height: 100%;
      /* overflow: scroll; */
    }
    .filter-order .daterange {
      min-width: 230px;
    }
  
    .add-order {
      position: fixed;
      right: 10px;
      bottom: 10px;
    }
  
    input#daterange {
      color: #000;
      width: 100%;
    }
  
    .link-name {
      text-decoration: none;
      color: unset;
    }
   
</style>
  
  <?php 
    $checkAll = isFullAccess(Auth::user()->role);
  ?>
  
<script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

<div class="tab-content rounded-bottom">
    <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">

    <div class="example-custom example mt-0">
    <div class="tab-content rounded-bottom">
        <div style="overflow-x: auto;" class="tab-pane p-0 pt-1 active preview" role="tabpanel" id="preview-1002">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">#</th>
                
                <th scope="col">Sđt</th>
                <th class="mobile-col-tbl" scope="col" >Tên</th>
                <!-- <th scope="col">Địa chỉ</th> -->
                <th scope="col">Số lượng</th>
                <th scope="col">Tổng tiền</th>
                <th scope="col">Giới tính</th>
                <th class="mobile-col-tbl" scope="col">Ngày lên đơn</th>
                <th class="text-center" scope="col">Trạng thái</th>
                <th scope="col">Mã vận đơn</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>

            
            
            </tbody>
        </table>
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