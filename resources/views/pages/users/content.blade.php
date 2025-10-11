<!-- toastr css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- toastr js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
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
<div class="tab-content rounded-bottom">
  <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1001">
                    
    <div class="row ">
      <div class="col col-4">
        <a class="btn btn-primary" href="{{route('add-user')}}" role="button">+ Thêm thành viên</a>
      </div>
      <div class="col-8 ">
        <form class ="row tool-bar d-flex justify-content-end" action="{{route('search-user')}}" method="get">
          <div class="col-3">
            <input class="form-control" name="search" placeholder="Tìm thành viên..." type="text">
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
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>

            @foreach ($list as $item)
      
              <tr>
                <td scope="row col-1">{{ $item->id }}</td>
                <td scope="col-7" >  {{ ($item->real_name) ? $item->real_name : $item->name }}</td>
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
