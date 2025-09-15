<!-- toastr css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- toastr js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
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
{{ csrf_field() }}
<script>
function updateStatus(id) {
  let checkbox = document.getElementById("toggle-checkbox-" + id);
  let status = checkbox.checked ? 1 : 0;
  let name = checkbox.getAttribute('data-name');
  var _token      = $("input[name='_token']").val();

  // Hỏi confirm trước khi gửi AJAX
  let confirmMsg = status === 1 
    ? `${name}: Bạn có chắc muốn bật tài khoản này không?` 
    : `${name}: Bạn có chắc muốn tắt tài khoản này không?`;

  if (!confirm(confirmMsg)) {
    // Nếu hủy thì trả checkbox về trạng thái cũ
    checkbox.checked = !checkbox.checked;
    return;
  }

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
      } else if ($.isEmptyObject(data.errors)) {
          toastr.success("Cập nhật trạng thái thành công!");
      } 
    }
    });
}
</script>
