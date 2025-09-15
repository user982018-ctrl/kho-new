@extends('layouts.default')
@section('content')
<div class="body flex-grow-1 px-3">
        <div class="container-lg">
          <div class="row">
            <div id="notifi-box" class="hidden alert alert-success print-error-msg" >
              <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            </div>
          
            <div class="col-12">
              <div class="card mb-4">
                <div class="card-header"><strong>Thêm sản phẩm  mới </span></div>
            @if(isset($category))
                <div class="card-body">
                  <div class="example">
                    <div class="body flex-grow-1">
                      <div class="tab-content rounded-bottom">
                      <form>
                        {{ csrf_field() }}
                        <input value="{{$category->id}}" name="id" type="hidden">
                        <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                          <div class="row">
                          <div class="mb-3 col-8">
                            <label class="form-label" for="nameIP">Tên danh mục</label>
                            <input class="form-control" value="{{$category->name}}" name="name" id="nameIP" type="text">
                            <p class="error_msg" id="name"></p>
                          </div>
                          <div class="row">
                            <div class="mb-3 col-2">
                                <label class="form-label" for="qtyIP">Trạng Thái</label>
                                <div class="form-check">
                                    <input <?=  $category->status == 1 ? 'checked' : '' ?> class="form-check-input" type="radio" name="status" value="1"
                                        id="flexRadioDefault1">
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Bật
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input <?=  $category->status == 0 ? 'checked' : '' ?> class="form-check-input" type="radio" name="status" value="0"
                                        id="flexRadioDefault2" >
                                    <label  class="form-check-label" for="flexRadioDefault2">
                                        Tắt
                                    </label>
                                </div>
                            </div>
                          </div>
                          <button id="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                      </form>
                      </div>
                    </div>
                  </div>    
                </div>
              </div>
            </div>
            @else
                <div class="card-body">
                  <div class="example">
                    <div class="body flex-grow-1">
                      <div class="tab-content rounded-bottom">
                      <form>
                        {{ csrf_field() }}
                        <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                          <div class="row">
                          <div class="mb-3 col-8">
                            <label class="form-label" for="nameIP">Tên danh mục</label>
                            <input required class="form-control" name="name" id="nameIP" type="text">
                            <p class="error_msg" id="name"></p>
                          </div>
                          </div>
                          <button id="submit" class="btn btn-primary">Tạo</button>
                        </div>
                      </form>
                      </div>
                    </div>
                  </div>    
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
<script>
$(document).ready(function() {
  $("#submit").click(function(e){
    e.preventDefault();
  
      var _token = $("input[name='_token']").val();
      var name = $("input[name='name']").val();
      var status = $("input[name='status']:checked").val();
      // var qty = $("input[name='qty']").val();
      var id = $("input[name='id']").val();
      if (name == '') {
        $("#name").html('Chưa nhập tên danh mục.');
      } else {
        $.ajax({
            url: "{{ route('save-category') }}",
            type:'POST',
            data: {_token:_token, name:name, id, status},
            success: function(data) {
              console.log(data);
                if($.isEmptyObject(data.errors)){
                    $(".error_msg").html('');
                    $("#notifi-box").show();
                    $("#notifi-box").html(data.success);
                    $("#notifi-box").slideDown('fast').delay(5000).hide(0);
                }else{
                    let resp = data.errors;
                    for (index in resp) {
                      console.log(index);
                      console.log(resp[index]);
                        $("#" + index).html(resp[index]);
                    }
                }
            }
        });
    }
  
  }); 
});
   </script> 
@stop