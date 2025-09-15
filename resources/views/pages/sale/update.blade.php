<!DOCTYPE html>
<html lang="en">
<head>
    <link href="{{ asset('public/css/pages/notify.css'); }}" rel="stylesheet">
    @include('includes.head')
    <link href="{{ asset('public/css/pages/styleOrders.css'); }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.call label {
    display: flex;
    justify-content: space-between;
}
#laravel-notify .notify {
    z-index: 2;
}
.search-box-container {
    position: relative;
    width: 300px;
}

.search-input {
width: 100%;
padding: 10px;
border: 1px solid #ccc;
border-radius: 5px;
}

.dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-top: 5px;
  max-height: 150px;
  overflow-y: auto;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dropdown li {
  padding: 10px;
  cursor: pointer;
}

.dropdown li:hover {
  background-color: #f0f0f0;
}

.hidden {
  display: none;
}

.Đã chọn-item {
  margin-top: 10px;
  color: #555;
  font-size: 14px;
}
</style>

</head>
<?php 
$isLeadSale = Helper::isLeadSale(Auth::user()->role);      
?>
<body>
    @include('notify::components.notify')
    <div class="body flex-grow-1 px-3 mt-2">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                @if(isset($saleCare))
                    {{-- <div class="card-header"><span><strong>Cập nhật CSKH</strong></span></div> --}}
                    <div class="card-body card-orders">
                        <div class="body flex-grow-1">
                            <div class="tab-content rounded-bottom">
                                <form method="post" action="{{route('update-sale-care')}}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{$saleCare->id}}">
                                    <div class="row" id="content-add">
                                        <div class="col-sm-12 col-lg-3">
                                            <label class="form-label" for="phoneFor">Số điện thoại</label>
                                            <input value="{{$saleCare->phone}}" class="form-control" name="phone" id="phoneFor" type="text">
                                            <p class="error_msg" id="phone"></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-3">
                                            <label class="form-label" for="nameFor">Tên khách hàng</label>
                                            <input value="{{$saleCare->full_name}}" class="form-control" name="name" id="nameFor" type="text">
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-3">
                                            <label class="form-label" for="addressFor">Địa chỉ/đường</label>
                                            <input value="{{$saleCare->address}}" class="form-control"
                                                name="address" id="addressFor" type="text">
                                            <p class="error_msg" id="address"></p>
                                        </div>
                                        <div class="col-sm-12 col-lg-12">
                                            <label for="note_info_customer" class="form-label">Ghi chú thông tin khách hàng:</label>
                                            <textarea name="note_info_customer" class="form-control" id="note_info_customer" cols="100" rows="5">{{$saleCare->messages}}</textarea>
                                            <p></p>
                                        </div>
                                        <?php $isCskhDt = Helper::isCskhDt(Auth::user());
                                        $checkAll = isFullAccess(Auth::user()->role);
                                        ?>
                                        @if ($isCskhDt || $checkAll)
                                        <div class="col-sm-12 col-lg-6">
                                            <label class="form-label" for="qtyIP">Trùng số</label>
                                            <div class="form-check">
                                                <input <?php if ($saleCare->issetDuplicate) echo 'checked';?>
                                                 class="form-check-input" type="checkbox" name="issetDuplicate" value="1"
                                                    id="toggleCheckbox">
                                                <label class="form-check-label" for="toggleCheckbox">
                                                    Trùng số khác
                                                </label>
                                            </div>
                                            <div class="form-check" id="toggleDiv" 
                                            <?php if (!$saleCare->issetDuplicate) { echo 'style=" display: none;"';
                                                } ?>
                                            >
                                                <div class="search-box-container">
                                                    <?php $phoneDup = $nameDup = '';
                                                        if ($saleCare->issetDuplicate && $saleCare->duplicate_id) {
                                                        $dataDup = Helper::getDataSaleById($saleCare->duplicate_id);
                                                        if ($dataDup) {
                                                            $phoneDup = $dataDup->phone;
                                                            $nameDup = $dataDup->full_name;
                                                        }
                                                        
                                                    } ?>
                                                    <input
                                                        type="text"
                                                        id="searchInput"
                                                        placeholder="Nhập tên hoặc số điện thoại"
                                                        class="search-input"
                                                        <?php if ($nameDup != '') echo 'value="' .$phoneDup . ' ' . $nameDup .'"'; ?>
                                                    />
                                                    <ul id="dropdown" class="dropdown hidden"></ul>
                                                    <input value="{{$saleCare->duplicate_id}}" name="duplicate_id" id="selectedItem" class="selected-item" type="hidden">
                                                    <p id="selectedItemP" class="selected-item" >Đã chọn: {{$nameDup}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                   
                                    <button type="submit" id="submit" class="btn btn-primary">Cập nhật </button>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
         
                @endif

                </div>

                <div class="row text-right">
                    <div><button class="refresh btn btn-info">Refresh</button></div>
                </div>
                <div id="loader-overlay">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
<script>

    // A $( document ).ready() block.
$( document ).ready(function() {
    $('.refresh').click(function() {
        location.reload(true)
    });

    if ($('.print-error-msg').length > 0) {
        setTimeout(function() { 
            $('.print-error-msg').hide();
        }, 3000);
    }

    $('.print-error-msg').on( "click", function() {
        $(this).hide();
    });
});
    
</script>
    @include('includes.foot')
    <script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>

</script>

<script>
    $(document).ready(function () {
            $('#toggleCheckbox').change(function () {
                if ($(this).is(':checked')) {
                    $('#toggleDiv').show(); // Hiện div nếu checkbox được chọn
                } else {
                    $('#toggleDiv').hide(); // Ẩn div nếu checkbox bị bỏ chọn
                }
            });
    });

</script>

<script>
 const searchInput = document.getElementById('searchInput');
const dropdown = document.getElementById('dropdown');
const selectedItem = document.getElementById('selectedItem');
const selectedItemP = document.getElementById('selectedItemP');


let debounceTimeout;

// Function to fetch data from the API
async function fetchData(query) {
  try {
    // const response = await fetch(`http://localhost/company/kho/api/seach-sale-care?q=${query}`);
    const response = await fetch(`https://kho.phanboncanada.online/api/seach-sale-care?q=${query}`);
    if (!response.ok) throw new Error('Error fetching data');
    return await response.json();
  } catch (error) {
    console.error(error);
    return [];
  }
}

// Function to render dropdown options
function renderDropdown(items) {
    dropdown.innerHTML = ''; // Clear previous results

    if (items.length === 0) {
        dropdown.classList.add('hidden');
        return;
    }

    items.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item.phone + ' ' + item.full_name;
        li.addEventListener('click', () => {
            // selectedItem.textContent = `Đã chọn: ${item.full_name}`;
            selectedItemP.textContent = `Đã chọn: ${item.full_name}`;
            selectedItem.value = item.id;
            dropdown.classList.add('hidden');
            searchInput.value = item.phone + ' ' + item.full_name;
        });
        dropdown.appendChild(li);
    });

    dropdown.classList.remove('hidden');
}

// Handle input changes
searchInput.addEventListener('input', () => {
  const query = searchInput.value.trim();

  // Debounce API calls
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(async () => {
    if (query) {
      const data = await fetchData(query);
      renderDropdown(data);
    } else {
      dropdown.classList.add('hidden');
    }
  }, 300);
});

// Close dropdown on outside click
document.addEventListener('click', (event) => {
  if (!event.target.closest('.search-box-container')) {
    dropdown.classList.add('hidden');
  }
});

</script>
</body>
</html>