@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="{{ asset('public/css/pages/sale.css'); }}" rel="stylesheet">
<?php 
    $checkAll = $checkPaulo = $checkFertilizer = $checkLeadSale = $other = $checkLeadDigital = '';
    $checkAll = isFullAccess(Auth::user()->role);
    if (isset($user)) {
        $roles = json_decode($user->role, true);
        if ( is_array($roles)) {
            foreach ($roles as $key => $value) {
                if ($value == 1) {
                    $checkAll = $checkPaulo = $checkFertilizer = $checkOther = $checkLeadSale = $checkLeadDigital = 'checked';
                    break;
                } 
                if ($value == 2) {
                    $checkPaulo = 'checked';
                    // break;
                } 
                if ($value == 3) {
                    $checkFertilizer = 'checked';
                    // break;
                } 
                if ($value == 4) {
                    $checkLeadSale = 'checked';
                    // break;
                }
                if ($value == 5) {
                    $other = 'checked';
                    // break;
                }
                if ($value == 6) {
                    $checkLeadDigital = 'checked';
                    // break;
                }
            }
        }
    }
?>
<style>
     .modal-backdrop-notify.show {
        opacity: 0;
    }
    #notify-modal .modal-header {
        border: unset;
        border-radius: unset;
        background: #4df54dcc;
    }

    #notify-modal .modal-content  {
        background: none;
        border: unset;
        border-radius: unset;
    }

    #notify-modal .modal-dialog {
        margin-right: 10px;
        width: 300px;
    }
</style>
<div class="body flex-grow-1 px-3">
    <div class="container-lg">
        <div class="row">
            <div id="notifi-box" class="hidden alert alert-success print-error-msg">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            </div>

            <div class="col-12">
                <div class="card mb-4">
                    
            @if(isset($user))
            <div class="card-header"><strong>Cập nhật thành viên: {{$user->real_name}} #{{$user->id}}</span></div>
                <div class="card-body">
                    <div class="body flex-grow-1">
                        <div class="tab-content rounded-bottom">
                            <form>
                                {{ csrf_field() }}
                                <input value="{{$user->id}}" name="id" type="hidden">
                                <div class="tab-pane p-3 active preview" role="tabpanel">
                                    <div class="row">
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="emailIP">Email</label>
                                            <input <?php if (!$checkAll) echo "readonly"; ?> value="{{$user->email}}" class="form-control" name="email" id="emailIP" type="email">
                                            <p class="error_msg" id="email"></p>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="nameIP">Tên đăng nhập</label>
                                            <input <?php if (!$checkAll) echo "readonly"; ?> value="{{$user->name}}" class="form-control" name="name" id="nameIP" type="text">
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="passwwordlIP">Mật khẩu</label>
                                            <input value="{{$user->password}}" class="form-control" name="password" id="passwwordlIP" type="password">
                                            <p class="error_msg" id="password"></p>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="rePasswwordIP">Nhập lại Mật khẩu</label>
                                            <input value="{{$user->password}}" class="form-control" name="rePassword" id="rePasswwordIP" type="password">
                                        </div>
                                        
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="realNameIP">Tên</label>
                                            <input value="{{$user->real_name}}" class="form-control" name="real_name" id="realNameIP" type="text">
                                            <p class="error_msg" id="real_name"></p>
                                        </div>

                                        @if ($checkAll)
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="qtyIP">Quyền truy cập</label>
                                            <div class="form-check">
                                                <input {{$checkAll}} id="role-all" name="roles[]" type="checkbox" class="form-check-input" value="1">
                                                <label class="form-check-label" for="role-all">
                                                Tất cả
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input id="paulo" {{$checkPaulo}} name="roles[]" type="checkbox" class="form-check-input" value="2">
                                                <label for="paulo" class="form-check-label">
                                                    Paulo
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input id="fertilizer" {{$checkFertilizer}} name="roles[]" type="checkbox" class="form-check-input" value="3">
                                                <label for="fertilizer" class="form-check-label">
                                                Phân bón
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input id="leadSale" {{$checkLeadSale}} name="roles[]" type="checkbox" class="form-check-input" value="4">
                                                <label for="leadSale" class="form-check-label">
                                                Lead Sale
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input id="leadDigital" {{$checkLeadDigital}} name="roles[]" type="checkbox" class="form-check-input" value="6">
                                                <label for="leadDigital" class="form-check-label">
                                                Lead Digital
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input id="other" {{$other}} name="roles[]" type="checkbox" class="form-check-input" value="5">
                                                <label for="other" class="form-check-label">
                                                Khác
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="qtyIP">Trạng Thái</label>
                                            <div class="form-check">
                                                <input <?=  $user->status == 1 ? 'checked' : '' ?> class="form-check-input" type="radio" name="status" value="1"
                                                    id="flexRadioDefault1">
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    Bật
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input <?=  $user->status == 0 ? 'checked' : '' ?> class="form-check-input" type="radio" name="status" value="0"
                                                    id="flexRadioDefault2" >
                                                <label  class="form-check-label" for="flexRadioDefault2">
                                                    Tắt
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label">Sale</label>
                                            <div class="form-check">
                                                <input <?=  $user->is_sale == 1 ? 'checked' : '' ?>  class="form-check-input" type="radio" name="is_sale" value="1"
                                                    id="isSaleTrueIP">
                                                <label class="form-check-label" for="isSaleTrueIP">
                                                    Có
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input <?=  $user->is_sale == 0 ? 'checked' : '' ?> class="form-check-input" type="radio" name="is_sale" value="0"
                                                    id="isSaleFalseIP" >
                                                <label  class="form-check-label" for="isSaleFalseIP">
                                                    Không
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label">Chia data</label>
                                            <div class="form-check">
                                                <input <?=  $user->is_receive_data == 1 ? 'checked' : '' ?>  class="form-check-input" type="radio" name="is_receive_data" value="1"
                                                    id="isReceiveTrueIP">
                                                <label class="form-check-label" for="isReceiveTrueIP">
                                                    Có
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input <?=  $user->is_receive_data == 0 ? 'checked' : '' ?> class="form-check-input" type="radio" name="is_receive_data" value="0"
                                                    id="isReceiveFalseIP" >
                                                <label  class="form-check-label" for="isReceiveFalseIP">
                                                    Không
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <label class="form-label">CSKH</label>
                                            <div class="form-check">
                                                <input <?=  $user->is_CSKH == 1 ? 'checked' : '' ?>  class="form-check-input" type="radio" name="is_CSKH" value="1"
                                                    id="is_CSKHTrueIP">
                                                <label class="form-check-label" for="is_CSKHTrueIP">
                                                    Có
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input <?=  $user->is_CSKH == 0 ? 'checked' : '' ?> class="form-check-input" type="radio" name="is_CSKH" value="0"
                                                    id="is_CSKHFalseIP" >
                                                <label  class="form-check-label" for="is_CSKHFalseIP">
                                                    Không
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 col-4">
                                            <label class="form-label">Digital</label>
                                            <div class="form-check">
                                                <input <?=  $user->is_digital == 1 ? 'checked' : '' ?>  class="form-check-input" type="radio" name="is_digital" value="1"
                                                    id="is_digitalTrueIP">
                                                <label class="form-check-label" for="is_digitalTrueIP">
                                                    Có
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input <?=  $user->is_digital == 0 ? 'checked' : '' ?> class="form-check-input" type="radio" name="is_digital" value="0"
                                                    id="is_digitalFalseIP" >
                                                <label  class="form-check-label" for="is_digitalFalseIP">
                                                    Không
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-4">
                                            
                                            <label class="form-label" for="passwwordlIP">Hình ảnh</label>
                                            <div class="profile-picture"
                                                @if ($user->profile_image) 
                                                style="background-image: url('{{ asset('storage/app/public/'.$user->profile_image) }}');"
                                                @endif
                                                
                                            >
                                                <h1 class="upload-icon">
                                                    <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                                                </h1>
                                                <input
                                                    id="image"
                                                    class="file-uploader"
                                                    type="file"
                                                    onchange="upload()"
                                                    accept="image/*"
                                                    enctype="multipart/form-data"
                                                />
                                               
                                            </div>
                                             <div style="padding: 5px;">(jpg, jpeg, png, gif)</div>
                                        </div>
                                    </div>
                                    
                                    <div id="loader-overlay">
                                        <div class="loader"></div>
                                    </div>
                                    <button id="submit" class="btn btn-primary">Cập nhật</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            @else
            <div class="card-header"><strong>Thêm thành viên mới </span></div>
            <div class="card-body">
                <div class="body flex-grow-1">
                    <div class="tab-content rounded-bottom">
                        <form>
                            {{ csrf_field() }}
                            <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                                <div class="row">
                                    <div class="mb-3 col-4">
                                        <label class="form-label" for="emailIP">Email</label>
                                        <input class="form-control" name="email" id="emailIP" type="email">
                                        <p class="error_msg" id="email"></p>
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label class="form-label" for="nameIP">Tên đăng nhập</label>
                                        <input class="form-control" name="name" id="nameIP" type="text">
                                        <p class="error_msg" id="name"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-4">
                                        <label class="form-label" for="passwwordlIP">Mật khẩu</label>
                                        <input class="form-control" name="password" id="passwwordlIP" type="password">
                                        <p class="error_msg" id="password"></p>
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label class="form-label" for="rePasswwordIP">Nhập lại Mật khẩu</label>
                                        <input class="form-control" name="rePassword" id="rePasswwordIP" type="password">
                                    </div>
                                    
                                </div>
                                
                            <div class="row">
                                <div class="mb-3 col-4">
                                    <label class="form-label" for="realNameIP">Tên</label>
                                    <input class="form-control" name="real_name" id="realNameIP" type="text">
                                    <p class="error_msg" id="real_name"></p>
                                </div>
                                <div class="mb-3 col-4">
                                    <label class="form-label" for="qtyIP">Quyền truy cập</label>
                                
                                    <div class="form-check">
                                        <input id="role-all" name="roles[]" type="checkbox" class="form-check-input" value="1">
                                        <label for="role-all" class="form-check-label">
                                            Tất cả
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="paulo" name="roles[]" type="checkbox" class="form-check-input" value="2">
                                        <label for="paulo" class="form-check-label">
                                            Paulo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="fertilize" name="roles[]" type="checkbox" class="form-check-input" value="3">
                                        <label for="fertilize" class="form-check-label">
                                            Phân bón
                                        </label>
                                    </div>
                                    {{-- <div class="form-check">
                                            <label class="form-check-label">
                                                <input name="roles[]" type="checkbox" class="form-check-input" value="4">Khác
                                            </label>
                                    </div> --}}
                                    <div class="form-check">
                                        <input id="leadSale" name="roles[]" type="checkbox" class="form-check-input" value="4">
                                            <label for="leadSale" class="form-check-label">
                                                Lead Sale
                                            </label>
                                    </div>
                                    {{-- <div class="form-check">
                                            <label class="form-check-label">
                                                <input name="roles[]" type="checkbox" class="form-check-input" value="4">
                                            </label>
                                    </div> --}}
                                </div>
                                <div class="mb-3 col-4">
                                    <label class="form-label" for="qtyIP">Trạng Thái</label>
                                    <div class="form-check">
                                        <input checked class="form-check-input" type="radio" name="status" value="1"
                                            id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Bật
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="0"
                                            id="flexRadioDefault2" >
                                        <label  class="form-check-label" for="flexRadioDefault2">
                                            Tắt
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3 col-4">
                                    <label class="form-label">Chia data</label>
                                    <div class="form-check">
                                        <input  checked class="form-check-input" type="radio" name="is_receive_data" value="1"
                                            id="isReceiveTrueIP">
                                        <label class="form-check-label" for="isReceiveTrueIP">
                                            Có
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_receive_data" value="0"
                                            id="isReceiveFalseIP" >
                                        <label  class="form-check-label" for="isReceiveFalseIP">
                                            Không
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3 col-4">
                                    <label class="form-label">Sale</label>
                                    <div class="form-check">
                                        <input checked  class="form-check-input" type="radio" name="is_sale" value="1"
                                            id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Có
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_sale" value="0"
                                            id="flexRadioDefault2" >
                                        <label  class="form-check-label" for="flexRadioDefault2">
                                            Không
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3 col-4">
                                    <label class="form-label">CSKH</label>
                                    <div class="form-check">
                                        <input checked  class="form-check-input" type="radio" name="is_CSKH" value="1"
                                            id="is_CSKHTrueIP">
                                        <label class="form-check-label" for="is_CSKHTrueIP">
                                            Có
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_CSKH" value="0"
                                            id="is_CSKHFalseIP" >
                                        <label  class="form-check-label" for="is_CSKHFalseIP">
                                            Không
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3 col-4">
                                    <label class="form-label">Digital</label>
                                    <div class="form-check">
                                        <input checked  class="form-check-input" type="radio" name="is_digital" value="1"
                                            id="is_digitalRadioDefault1">
                                        <label class="form-check-label" for="is_digitalRadioDefault1">
                                            Có
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_digital" value="0"
                                            id="is_digitalRadioDefault2" >
                                        <label  class="form-check-label" for="is_digitalRadioDefault2">
                                            Không
                                        </label>
                                    </div>
                                </div>
                            </div>
                                <div id="loader-overlay">
                                    <div class="loader"></div>
                                </div>
                                <button id="submit" class="btn btn-primary">Tạo</button>
                            </div>
                        </form>
                    </div>
                </div>           
            </div>
        </div>
    </div>
    @endif
</div>
</div>
</div>

<div class="modal fade" id="notify-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" style="color: seagreen;"><p style="margin:0">Lưu data thành công</p></h6>
            <button style="border: none;" type="button" id="close-modal-notify" class="close" data-dismiss="modal" >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('public/js/page/uploadPicture.js')}}"></script>
<script>
$(document).ready(function() {
    $("#submit").click(function(e) {
        e.preventDefault();
        $('#loader-overlay').show();
        $('#loader-overlay').css('display','flex')
        var _token      = $("input[name='_token']").val();
        var name        = $("input[name='name']").val();
        var real_name   = $("input[name='real_name']").val();
        var email       = $("input[name='email']").val();
        var password    = $("input[name='password']").val();
        var rePassword  = $("input[name='rePassword']").val();
        var id          = $("input[name='id']").val();
        var status      = $("input[name='status']:checked").val();
        var is_sale     = $("input[name='is_sale']:checked").val();
        var is_receive_data     = $("input[name='is_receive_data']:checked").val();
        var is_digital  = $("input[name='is_digital']:checked").val();
        var is_CSKH     = $("input[name='is_CSKH']:checked").val();

        let roles = [];
        $("input[name='roles[]']:checked").each(function() {
            roles.push($(this).val());
        });
        
        if (password != rePassword) {
            var err = 'Mật khẩu không khớp';
            $('#password').text(err);
            $('#loader-overlay').hide();
        } else {
           
            var formData = new FormData();
            if ($('#image').length > 0) {
                var file = $('#image')[0].files[0];
                if (file) {
                    formData.append('image', file);
                }
            }

            formData.append('_token', _token);
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('id', id);
            formData.append('roles', JSON.stringify(roles));
            formData.append('status', status);
            formData.append('is_sale', is_sale);
            formData.append('real_name', real_name);
            formData.append('is_receive_data', is_receive_data);
            formData.append('is_digital', is_digital);
            formData.append('is_CSKH', is_CSKH);

            $.ajax({
                url: "{{ route('save-user') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                contentType: false,
                processData: false,
                data: formData,
                success: function(data) {
                    if (!$.isEmptyObject(data.error)) {
                        // $("#notifi-box").removeClass('alert-success'); 
                        // $("#notifi-box").addClass('alert-danger');
                        // $(".error_msg").html('');
                        // $("#notifi-box").show();
                        // $("#notifi-box").html(data.error);
                        // $("#notifi-box").slideDown('fast').delay(5000).hide(0);

                        $('#notify-modal').modal('show');
                        if ($('.modal-backdrop-notify').length === 0) {
                            $('.modal-backdrop').addClass('modal-backdrop-notify');  
                        } 

                        $('#notify-modal .modal-title').text('Cập nhật hồ sơ thất bại!');

                        setTimeout(function() {
                            $('#notify-modal .modal-title').text('');
                            $('#notify-modal').modal("hide");
                        }, 2000);
                    } else if ($.isEmptyObject(data.errors)) {
                        if (data.link) {
                            $('#avatar img').attr('src', data.link);
                        }
                        

                        // $("#notifi-box").addClass('alert-success'); 
                        // $("#notifi-box").removeClass('alert-danger');
                        // $(".error_msg").html('');
                        // $("#notifi-box").show();
                        // $("#notifi-box").html(data.success);
                        // $("#notifi-box").slideDown('fast').delay(5000).hide(0);

                        $('#notify-modal').modal('show');
                        if ($('.modal-backdrop-notify').length === 0) {
                            $('.modal-backdrop').addClass('modal-backdrop-notify');  
                        } 

                        $('#notify-modal .modal-title').text('Cập nhật  hồ sơ thành công!');
                    } else {
                        let resp = data.errors;
                        console.log(resp);
                        for (index in resp) {
                            $("#" + index).html(resp[index]);
                        }
                    }
                    location.reload();
                    // $('#loader-overlay').hide();
                }
            });
        }
        
    });

    $("input[name='roles[]']").click(function () {
        var values = [];
        
        if ($(this).val() == 1) {
            if ($(this).is(':checked') ) {
                $("input[name='roles[]']").prop('checked', true);
            } else {
                $("input[name='roles[]']").prop('checked', false);
            }
            
        } else {
            if (!$(this).is(':checked') ) {
                console.log('unchecked');
                $("#role-all").prop('checked', false);
            } else {
                let values = [];
                $("input[name='roles[]']:checked").each(function() {
                    values.push($(this).val());
                });
                console.log(values);
                
                if (values.length == 4) {
                    $("#role-all").prop('checked', true);
                }
            }
        }
    });

});
    $("#close-modal-notify").click(function() {
        $('#notify-modal').modal("hide");
    });
</script>
@stop