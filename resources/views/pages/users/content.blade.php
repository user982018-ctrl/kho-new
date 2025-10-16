<!-- toastr css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- toastr js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

<style>
  .form-control {
    line-height: unset;
  }
  .select2-container {
    width: 100% !important;
  }
  /* .select2-selection__rendered { */
  .result-TN-col .select-assign, .result-TN-col .select2-container--default .select2-selection--single , .result-TN {
      background-color: inherit !important;
      border: none;
  }

  .selectedClass .select2-container {
      box-shadow: rgb(0, 123, 255) 0px 1px 1px 1px;
  }
  /* Custom Modal Styles */
  .custom-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
  }

  .custom-modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 0;
    border: none;
    border-radius: 12px;
    width: 400px;
    max-width: 90%;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease;
    overflow: hidden;
  }

  .custom-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
  }

  .custom-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
  }

  .custom-modal-body {
    padding: 30px 20px;
    text-align: center;
    color: #333;
    font-size: 16px;
    line-height: 1.5;
  }

  .custom-modal-actions {
    padding: 0 20px 20px;
    display: flex;
    gap: 10px;
    justify-content: center;
  }

  .custom-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 100px;
  }

  .custom-btn-secondary {
    background: #6c757d;
    color: white;
  }

  .custom-btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
  }

  .custom-btn-danger {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    color: white;
  }

  .custom-btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(255, 65, 108, 0.4);
  }

  .custom-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }

  .custom-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
  }

  .modal-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 15px;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  @keyframes slideIn {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 24px;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
    }

  .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  input:checked + .slider {
    background-color: #2196F3;
  }

  input:checked + .slider:before {
    transform: translateX(16px);
  }
</style>

<?php
  $listSale = Helper::getListSaleOfLeaderGroup(); 
  $checkAll = isFullAccess(Auth::user()->role);
  $isLeadSale = Helper::isLeadSale(Auth::user()->role);  
  $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);     
  $flag = false;
  $flagAccess = false;
  $isDigital = Auth::user()->is_digital;
  $listSaleJson = '';
?>

<div class="tab-content rounded-bottom">
  <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">                  
    <div class="row ">
      <form id="userForm" action="{{route('search-user')}}" method="get">
        {{ csrf_field() }}
        <div class="maintain-filter-main">
          <div class="m-header-wrap">
            <div class="m-header" style="top:150px;">
              <div class="row header-top-filter">
                @if ($checkAll)
                <div class="col-12 col-sm-3 col-md-3 col-lg-2 form-group" style="padding:0 15px;"> 
                    <select name="group" id="group-filter" class="border-select-box-se">
                        {{-- <option selected="selected" value="-1" >--Tất cả sale--</option> --}}
                        <option value="999">--Nhóm Hàng--</option>
                        @if (isset($groups))
                            @foreach($groups as $group)
                            <option value="{{$group->id}}">{{$group->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @endif

                @if ($checkAll)
                <div class="col-12 col-sm-3 col-md-3 col-lg-2 form-group" style="padding:0 15px;"> 
                    <select name="sale" id="sale-filter" class="border-select-box-se">
                        <option value="999">--Nhóm Sale--</option>
                        @if (isset($groupSale))
                            @foreach($groupSale as $sale)
                            <option value="{{$sale->id}}">{{($sale->real_name) ? : $sale->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @endif

                @if ($checkAll)
                <div class="col-12 col-sm-3 col-md-3 col-lg-2 form-group" style="padding:0 15px;"> 
                    <select name="digital" id="digital-filter" class="border-select-box-se">
                        <option value="999">--Nhóm Digital--</option>
                        @if (isset($groupDigital))
                            @foreach($groupDigital as $digital)
                            <option value="{{$digital->id}}">{{($digital->real_name) ? : $digital->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @endif

                <div class="col-12 col-sm-6 col-md-3 form-group">
                    <input name="search" type="text"  value="{{ isset($search) ? $search : null}}" class="form-control" placeholder="Họ tên">
                </div>
            
                <div class="col-12 col-sm-6 col-md-3 col-lg-3 form-group" style="max-width: 180px;" >
                    <button class="btn btn-sm btn-primary" type="submit">
                        <i class="fa fa-search"></i>Tìm kiếm
                    </button>
                </div>

                <div style="clear: both;"></div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="example mt-0">
    <div class="tab-content rounded-bottom">
      <div class=" tab-pane p-3 active preview" role="tabpanel" id="preview-1002">
        <table class="table table-bordered table-line">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Họ và tên</th>
              <th scope="col">Tên đăng nhập</th>
              <th scope="col">Email</th>
              <th scope="col">Ngày tạo</th>
              <th scope="col" class="text-center">Trạng thái</th>
              <th scope="col"><a class="btn btn-primary" href="{{route('add-user')}}" role="button">+ Thêm</a></th>
            </tr>
          </thead>
          <tbody>

          @foreach ($list as $item)
          <?php $teamName = '';
          if ($item->groupUser) {
            $teamName = $item->groupUser->name . ' _ ';
          }
          ?>
            <tr>
              <td scope="row col-1">{{ $item->id }}</td>
              <td scope="col-7"> {{$teamName}} {{ ($item->real_name) ? $item->real_name : $item->name }}</td>
              <td scope="row col-1">{{ $item->name }}</td>
              <td scope="row col-1">{{ $item->email }}</td>
              <td scope="col-1">  {{ date_format($item->created_at,"d-m-Y H:i")}}</td>
              <td scope="col-1" class="text-center">
                <label class="toggle-switch">
                  <input id="toggle-checkbox-<?= $item->id ?>" 
                  data-name="<?= ($item->real_name) ? $item->real_name : $item->name ?>"
                    onclick="updateStatus(<?= $item->id ?>)"
                    type="checkbox" id="toggle-checkbox" name="status" <?= ($item->status == 1) ? 'checked' : '' ?>>
                  <span class="slider"></span>
                </label>
              </td>
              <td scope="col-1">
              <a class="btn btn-warning" href="{{route('update-user',['id'=>$item->id])}}" role="button">
                  <svg class="icon me-2">
                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-color-border')}}"></use>
                  </svg>Sửa
              </a>
                <a onclick="return confirm('Xoá thành viên?')" class="btn btn-danger active" href="{{route('delete-user',['id'=>$item->id])}}" role="button">
                  <svg class="icon me-2">
                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-backspace')}}"></use>
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

<!-- Custom Confirm Modal -->
<div id="customModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h3 id="modalTitle">Xác nhận</h3>
        </div>
        <div class="custom-modal-body">
            <span id="modalIcon" class="modal-icon">⚠️</span>
            <p id="modalMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
        </div>
        <div class="custom-modal-actions">
            <button id="modalCancel" class="custom-btn custom-btn-secondary">Không</button>
            <button id="modalConfirm" class="custom-btn custom-btn-primary">Có</button>
        </div>
    </div>
</div>

{{ csrf_field() }}
<script>
// Custom Modal Functions
function showCustomModal(title, message, icon, confirmCallback, confirmText = 'Có', confirmClass = 'custom-btn-primary', cancelCallback = null) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modalIcon').textContent = icon;
    document.getElementById('modalConfirm').textContent = confirmText;
    document.getElementById('modalConfirm').className = `custom-btn ${confirmClass}`;
    
    const modal = document.getElementById('customModal');
    modal.style.display = 'block';
    
    // Store callbacks
    modal._confirmCallback = confirmCallback;
    modal._cancelCallback = cancelCallback;
}

function hideCustomModal() {
    document.getElementById('customModal').style.display = 'none';
}

// Modal event listeners
document.getElementById('modalCancel').addEventListener('click', function() {
    const modal = document.getElementById('customModal');
    if (modal._cancelCallback) {
        modal._cancelCallback();
    }
    hideCustomModal();
});

document.getElementById('modalConfirm').addEventListener('click', function() {
    const modal = document.getElementById('customModal');
    if (modal._confirmCallback) {
        modal._confirmCallback();
    }
    hideCustomModal();
});

// Close modal when clicking outside
document.getElementById('customModal').addEventListener('click', function(e) {
    if (e.target === this) {
        const modal = document.getElementById('customModal');
        if (modal._cancelCallback) {
            modal._cancelCallback();
        }
        hideCustomModal();
    }
});

function updateStatus(id) {
  let checkbox = document.getElementById("toggle-checkbox-" + id);
  let status = checkbox.checked ? 1 : 0;
  let name = checkbox.getAttribute('data-name');
  var _token = $("input[name='_token']").val();

  // Xác định nội dung modal
  let modalTitle = status === 1 ? 'Bật tài khoản' : 'Tắt tài khoản';
  let modalMessage = status === 1 
    ? `${name}: Bạn có chắc muốn bật tài khoản này không?` 
    : `${name}: Bạn có chắc muốn tắt tài khoản này không?`;
  let modalIcon = status === 1 ? '✅' : '🔒';
  let confirmClass = status === 1 ? 'custom-btn-primary' : 'custom-btn-danger';
  let confirmText = status === 1 ? 'Có, bật!' : 'Có, tắt!';

  // Hiển thị custom modal
  showCustomModal(
    modalTitle,
    modalMessage,
    modalIcon,
    function() {
      // Nếu OK thì gọi AJAX
      $.ajax({
        url: "{{ route('api-update-status-user') }}",
        type: 'POST',
        data: {
          id,
          status,
          _token
        },
        success: function(data) {
          if (!$.isEmptyObject(data.error)) {
            toastr.error("Cập nhật thất bại!");
            // Trả checkbox về trạng thái cũ nếu thất bại
            checkbox.checked = !checkbox.checked;
          } else if ($.isEmptyObject(data.errors)) {
            toastr.success("Cập nhật trạng thái thành công!");
          }
        },
        error: function() {
          toastr.error("Có lỗi xảy ra!");
          // Trả checkbox về trạng thái cũ nếu lỗi
          checkbox.checked = !checkbox.checked;
        }
      });
    },
    confirmText,
    confirmClass,
    function() {
      // Callback khi user hủy - trả checkbox về trạng thái cũ
      checkbox.checked = !checkbox.checked;
    }
  );
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
  $(function() {
      $('#sale-filter').select2();
      $('#group-filter').select2();
      $('#digital-filter').select2();
  });
</script>


<script>
  document.getElementById('userForm').addEventListener('submit', function (e) {
    const inputs = this.querySelectorAll('input');
    inputs.forEach(input => {
        if (input.value === '') {
            input.disabled = true; // loại bỏ khỏi dữ liệu gửi đi
        }
    });
  
    const selects = this.querySelectorAll('select');
    selects.forEach(select => {
      if (select.value === '999') {
        select.disabled = true; // không gửi giá trị này
      }
    });
    return;
  });
  </script>
  <script>
    $.urlParam = function(name){
      var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
      if (results) {
        return results[1];
      }
      return 0;
    }

    let group = $.urlParam('group')
    if (group && group != 999) {
      $('#group-filter option[value=' + group +']').attr('selected','selected');
      $('#group-filter').parent().addClass('selectedClass');
    }

    let sale = $.urlParam('sale')
    if (sale && sale != 999) {
      $('#sale-filter option[value=' + sale +']').attr('selected','selected');
      $('#sale-filter').parent().addClass('selectedClass');
    }

    let digital = $.urlParam('digital')
    if (digital && digital != 999) {
      $('#digital-filter option[value=' + digital +']').attr('selected','selected');
      $('#digital-filter').parent().addClass('selectedClass');
    }
    
    let search = $.urlParam('search')
    if (search) {
      search = decodeURIComponent(search);
      search = search.replaceAll('+', " ");
      $('input[name="search"]').val(search);
    }

    let status = $.urlParam('status')
    if (status && status != 999) {
      $('#status-filter option[value=' + status +']').attr('selected','selected');
      $('#status-filter').parent().addClass('selectedClass');
    }
  </script>