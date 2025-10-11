@extends('layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="{{ asset('public/css/pages/sale.css'); }}" rel="stylesheet">
<?php 
    $projectManager = $checkAll = $checkPaulo = $checkFertilizer = $checkLeadSale = $other = $checkLeadDigital = '';
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
                if ($value == 7) {
                    $projectManager = 'checked';
                    // break;
                }
            }
        }
    }
?>
<style>
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
                                <div class="row">
                                    <div class="mb-3 col-md-3 col-sm-12">
                                        <hr>
                                        <div>
                                            <label class="form-label" for="emailIP">Email</label>
                                            <input <?php if (!$checkAll) echo "readonly"; ?> value="{{$user->email}}" class="form-control" name="email" id="emailIP" type="email">
                                            <p class="error_msg" id="email"></p>    
                                        </div>
                                        <div>
                                            <label class="form-label" for="realNameIP">Họ và Tên</label>
                                            <input value="{{$user->real_name}}" class="form-control" name="real_name" id="realNameIP" type="text">
                                            <p class="error_msg" id="real_name"></p>
                                        </div>
                                        <div>
                                            <label class="form-label" for="nameIP">Tên đăng nhập</label>
                                            <input <?php if (!$checkAll) echo "readonly"; ?> value="{{$user->name}}" class="form-control" name="name" id="nameIP" type="text">
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div>
                                            <label class="form-label" for="passwwordlIP">Mật khẩu</label>
                                            <input value="{{$user->password}}" class="form-control" name="password" id="passwwordlIP" type="password">
                                            <p class="error_msg" id="password"></p>
                                        </div>
                                        <div>
                                            <label class="form-label" for="rePasswwordIP">Nhập lại Mật khẩu</label>
                                            <input value="{{$user->password}}" class="form-control" name="rePassword" id="rePasswwordIP" type="password">
                                            <p class="error_msg" id="rePassword"></p>
                                        </div>
                                       
                                    </div>
                                    @if ($checkAll)
                                    <div class="mb-3 col-md-9 col-sm-12">
                                        <hr>
                                        
                                        <label class="form-label" for="qtyIP">Vai trò</label>
                                        <div class="row">
                                            <div class="mb-3 col-4">
                                                <div class="form-check form-switch">
                                                    <input {{$checkAll}} id="role-all" name="roles[]" type="checkbox" class="form-check-input" value="1">
                                                    <label class="form-check-label" for="role-all">
                                                    Tất cả
                                                    </label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input id="paulo" {{$checkPaulo}} name="roles[]" type="checkbox" class="form-check-input" value="2">
                                                    <label for="paulo" class="form-check-label">
                                                        Paulo
                                                    </label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input id="fertilizer" {{$checkFertilizer}} name="roles[]" type="checkbox" class="form-check-input" value="3">
                                                    <label for="fertilizer" class="form-check-label">
                                                    Phân bón
                                                    </label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input id="leadSale" {{$checkLeadSale}} name="roles[]" type="checkbox" class="form-check-input" value="4">
                                                    <label for="leadSale" class="form-check-label">
                                                    Lead Sale
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-3 col-4">
                                               
                                                <div class="form-check form-switch">
                                                    <input id="leadDigital" {{$checkLeadDigital}} name="roles[]" type="checkbox" class="form-check-input" value="6">
                                                    <label for="leadDigital" class="form-check-label">
                                                    Lead Digital
                                                    </label>
                                                </div>
                                                
                                                <div class="form-check form-switch">
                                                    <input {{$projectManager}}  id="projectManager" name="roles[]" type="checkbox" class="form-check-input" value="7">
                                                    <label for="projectManager" class="form-check-label">
                                                        Project Manager
                                                    </label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input id="other" {{$other}} name="roles[]" type="checkbox" class="form-check-input" value="5">
                                                    <label for="other" class="form-check-label">
                                                    Khác
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <label class="form-label" for="qtyIP">Chức vụ</label>
                                            <div class="mb-3 col-4">
                                                <div class="form-check form-switch">
                                                    <input <?=  $user->is_sale == 1 ? 'checked' : '' ?> name="is_sale" class="form-check-input" id="is_sale" type="checkbox">
                                                    <label class="form-check-label" for="is_sale">Sale</label>
                                                </div> 
                                                <div class="form-check form-switch">
                                                    <input <?=  $user->is_CSKH == 1 ? 'checked' : '' ?> name="is_CSKH" class="form-check-input" id="is_CSKH" type="checkbox" >
                                                    <label class="form-check-label" for="is_CSKH">CSKH</label>
                                                </div> 
                                                <div class="form-check form-switch">
                                                    <input <?=  $user->is_digital == 1 ? 'checked' : '' ?> name="is_digital" class="form-check-input" id="is_digital" type="checkbox" >
                                                    <label class="form-check-label" for="is_digital">Digital Marketing</label>
                                                </div> 
                                                <div class="form-check form-switch">
                                                    <input <?=  $user->is_kho == 1 ? 'checked' : '' ?> name="is_kho" class="form-check-input" id="is_kho" type="checkbox">
                                                    <label class="form-check-label" for="is_kho">Nhân viên Kho</label>
                                                </div> 
                                            </div>
                                            <div class="mb-3 col-4">
                                                <div class="form-check form-switch">
                                                    <input <?=  $user->is_accountant == 1 ? 'checked' : '' ?> name="is_accountant" class="form-check-input" id="is_accountant" type="checkbox">
                                                    <label class="form-check-label" for="is_accountant">Kế Toán</label>
                                                </div> 
                                                <div class="form-check form-switch">
                                                    <input <?=  $user->is_hr == 1 ? 'checked' : '' ?> name="is_hr" class="form-check-input" id="is_hr" type="checkbox">
                                                    <label class="form-check-label" for="is_hr">Nhân sự</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    @endif
                                    
                                </div>
                                <div class="row">
                                    @if ($checkAll)
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
                                <button id="submit" class="btn btn-primary">
                                    <svg class="icon me-2">
                                        <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-save')}}"></use>
                                      </svg>Cập nhật</button>
                                
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
                            <div class="row">
                                <div class="mb-3 col-md-3 col-sm-12">
                                    <hr>
                                    <div>
                                        <label class="form-label" for="emailIP">Email</label>
                                        <input class="form-control" name="email" id="emailIP" type="email">
                                        <p class="error_msg" id="email"></p>
                                    </div>
                                    
                                    <div>
                                        <label class="form-label" for="realNameIP">Họ và Tên</label>
                                        <input class="form-control" name="real_name" id="realNameIP" type="text">
                                        <p class="error_msg" id="real_name"></p>
                                    </div> 
                                    <div>
                                        <label class="form-label" for="nameIP">Tên đăng nhập  <button 
                                        style="text-decoration: underline;font-style: italic;color: blue;" type="button" class="btn" id="checkUsername">check trùng</button></label>
                                       
                                        <div style="position: relative;">
                                            <input class="form-control" name="name" id="nameIP" type="text" style="padding-right: 40px;">
                                            <i class="fa fa-check-circle" id="username-check-icon" style="display: none;"></i>
                                            <i class="fa fa-times-circle" id="username-error-icon" style="display: none;"></i>
                                        </div>
                                        <p class="error_msg" id="name"></p>
                                        
                                    </div>
                                    <div>
                                        <label class="form-label" for="passwwordlIP">Mật khẩu</label>
                                        <input class="form-control" name="password" id="passwwordlIP" type="password">
                                        <p class="error_msg" id="password"></p>
                                    </div>
                                    <div>
                                        <label class="form-label" for="rePasswwordIP">Nhập lại Mật khẩu</label>
                                        <input class="form-control" name="rePassword" id="rePasswwordIP" type="password">
                                        <p class="error_msg" id="rePassword"></p>
                                    </div>
                                   

                                </div>
                                <div class="mb-3 col-md-9 col-sm-12">
                                    <hr>
                                    <div class="row">
                                        <label class="form-label" for="qtyIP">Vai trò</label>
                                        <div class="mb-3 col-4">
                                            <div class="form-check form-switch">
                                                <input id="role-all" name="roles[]" type="checkbox" class="form-check-input" value="1">
                                                <label for="role-all" class="form-check-label">
                                                    Tất cả
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input id="paulo" name="roles[]" type="checkbox" class="form-check-input" value="2" checked="">
                                                <label for="paulo" class="form-check-label">
                                                    Paulo
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input id="fertilize" name="roles[]" type="checkbox" class="form-check-input" value="3" checked="">
                                                <label for="fertilize" class="form-check-label">
                                                    Phân bón
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-switch">
                                                <input id="leadSale" name="roles[]" type="checkbox" class="form-check-input" value="4">
                                                <label for="leadSale" class="form-check-label">
                                                    Leader Sale
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-4">
                                            <div class="form-check form-switch">
                                                <input id="leadDigital" name="roles[]" type="checkbox" class="form-check-input" value="6">
                                                <label for="leadDigital" class="form-check-label">
                                                    Leader Digital
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input id="projectManager" name="roles[]" type="checkbox" class="form-check-input" value="7">
                                                <label for="projectManager" class="form-check-label">
                                                    Project Manager
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input id="other" name="roles[]" type="checkbox" class="form-check-input" value="5">
                                                <label for="other" class="form-check-label">
                                                    Khác
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <label class="form-label" for="qtyIP">Chức vụ</label>
                                        <div class="mb-3 col-4">
                                            <div class="form-check form-switch">
                                                <input name="is_sale" class="form-check-input" id="is_sale" type="checkbox" checked="">
                                                <label class="form-check-label" for="is_sale">Sale</label>
                                            </div> 
                                            <div class="form-check form-switch">
                                                <input name="is_CSKH" class="form-check-input" id="is_CSKH" type="checkbox" checked="">
                                                <label class="form-check-label" for="is_CSKH">CSKH</label>
                                            </div> 
                                            <div class="form-check form-switch">
                                                <input name="is_digital" class="form-check-input" id="is_digital" type="checkbox" >
                                                <label class="form-check-label" for="is_digital">Digital Marketing</label>
                                            </div> 
                                            <div class="form-check form-switch">
                                                <input name="is_kho" class="form-check-input" id="is_kho" type="checkbox">
                                                <label class="form-check-label" for="is_kho">Nhân viên Kho</label>
                                            </div> 
                                        </div>
                                        <div class="mb-3 col-4">
                                            <div class="form-check form-switch">
                                                <input name="is_accountant" class="form-check-input" id="is_accountant" type="checkbox">
                                                <label class="form-check-label" for="is_accountant">Kế Toán</label>
                                            </div> 
                                            <div class="form-check form-switch">
                                                <input name="is_hr" class="form-check-input" id="is_hr" type="checkbox">
                                                <label class="form-check-label" for="is_hr">Nhân sự</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row">
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
                                        <input class="form-check-input" type="radio" name="is_receive_data" value="1"
                                            id="isReceiveTrueIP">
                                        <label class="form-check-label" for="isReceiveTrueIP">
                                            Có
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input checked class="form-check-input" type="radio" name="is_receive_data" value="0"
                                            id="isReceiveFalseIP" >
                                        <label  class="form-check-label" for="isReceiveFalseIP">
                                            Không
                                        </label>
                                    </div>
                                </div>
                                
                            </div>
                            <div id="loader-overlay">
                                <div class="loader"></div>
                            </div>
                            <button id="submit" class="btn btn-primary"><svg class="icon me-2">
                                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-save')}}"></use>
                              </svg>Tạo</button>
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
        var is_sale     = $("input[name='is_sale']").is(':checked') ? 1 : 0;
        var is_receive_data     = $("input[name='is_receive_data']").is(':checked') ? 1 : 0;
        var is_digital  = $("input[name='is_digital']").is(':checked') ? 1 : 0;
        var is_CSKH     = $("input[name='is_CSKH']").is(':checked') ? 1 : 0;
        var is_kho      = $("input[name='is_kho']").is(':checked') ? 1 : 0;
        var is_accountant = $("input[name='is_accountant']").is(':checked') ? 1 : 0;
        var is_hr       = $("input[name='is_hr']").is(':checked') ? 1 : 0;

        let roles = [];
        $("input[name='roles[]']:checked").each(function() {
            roles.push($(this).val());
        });
        
        // Reset all error messages
        $('.error_msg').text('');
        
        // Validation
        var hasError = false;
        
        if (name == "" || name == null) {
            $('#name').text('Vui lòng nhập tên đăng nhập');
            hasError = true;
        }
        
        if (real_name == "" || real_name == null) {
            $('#real_name').text('Vui lòng nhập họ và tên');
            hasError = true;
        }
        
        if (email == "" || email == null) {
            $('#email').text('Vui lòng nhập email');
            hasError = true;
        } else {
            // Validate email format
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $('#email').text('Email không đúng định dạng');
                hasError = true;
            }
        }

        if (password == "" || password == null) {
            $('#password').text('Vui lòng nhập mật khẩu');
            hasError = true;
        }

        if (rePassword == "" || rePassword == null) {
            $('#rePassword').text('Vui lòng nhập lại mật khẩu');
            hasError = true;
        }
        
        if (password != rePassword) {
            $('#password').text('Mật khẩu không khớp');
            hasError = true;
        }
        
        // If has error, stop and hide loader
        if (hasError) {
            $('#loader-overlay').hide();
            return false;
        }
        
        // Continue if no error
        if (true) {
           
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
            formData.append('is_kho', is_kho);
            formData.append('is_accountant', is_accountant);
            formData.append('is_hr', is_hr);

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
                    $('#loader-overlay').hide();
                    if (!$.isEmptyObject(data.error)) {
                        // Hiển thị toastr error
                        toastr.error(data.error);
                        $('#loader-overlay').hide();
                    } else if ($.isEmptyObject(data.errors)) {
                        if (data.link) {
                            $('#avatar img').attr('src', data.link);
                        }
                        
                        // Hiển thị toastr success
                        toastr.success(data.success);
                        if (data.success == 'Tạo thành viên thành công.') {
                             //Delay reload để hiển thị toastr
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }

                        
                        // Delay reload để hiển thị toastr
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 1500);
                    } else {
                        let resp = data.errors;
                        console.log(resp);
                        for (index in resp) {
                            $("#" + index).html(resp[index]);
                        }
                        $('#loader-overlay').hide();
                    }
                },
                error: function(xhr, status, error) {
                    // Hiển thị toastr error cho lỗi AJAX
                    toastr.error('Có lỗi xảy ra khi lưu dữ liệu. Vui lòng thử lại!');
                    $('#loader-overlay').hide();
                    console.log('AJAX Error:', error);
                }
            });
        }
        
    });

    $("input[name='roles[]']").click(function () {
        var values = [];
        
        // Nếu click vào checkbox "Tất cả"
        if ($(this).val() == 1) {
            if ($(this).is(':checked') ) {
                // Check tất cả các checkbox
                $("input[name='roles[]']").prop('checked', true);
            } else {
                // Uncheck tất cả các checkbox
                $("input[name='roles[]']").prop('checked', false);
            }
            
        } else {
            // Click vào các checkbox khác (không phải "Tất cả")
            // Đếm tổng số checkbox (không bao gồm checkbox "Tất cả")
            var totalCheckboxes = $("input[name='roles[]']").not("#role-all").length;
            var checkedCount = $("input[name='roles[]']:checked").not("#role-all").length;
            
            if (!$(this).is(':checked') ) {
                // Nếu uncheck bất kỳ checkbox nào → uncheck "Tất cả"
                $("#role-all").prop('checked', false);
            } else {
                // Nếu tất cả checkbox đều được check → check "Tất cả"
                if (checkedCount == totalCheckboxes) {
                    $("#role-all").prop('checked', true);
                }
            }
        }
    });

    $("#checkUsername").click(function() {
        checkUsernameAvailability();
    });
    
    $("input[name='name']").on('blur', function() {
        checkUsernameAvailability();
    });

    // Xóa icon khi người dùng typing
    $("input[name='name']").on('input', function() {
        $('#username-check-icon').hide();
        $('#username-error-icon').hide();
        $('#name').text('');
        $(this).removeClass('is-valid is-invalid');
    });

    function checkUsernameAvailability() {
        // Call API và hiển thị kết quả
        var name = $("input[name='name']").val();
        var id = $("input[name='id']").val();
        var _token = $("input[name='_token']").val();
        
        // Reset error message và icons
        $('#name').text('');
        $('#username-check-icon').hide();
        $('#username-error-icon').hide();
        
        if (name == "" || name == null) {
            return; // Không check nếu chưa nhập
        }
        
        // Call API check username
        $.ajax({
            url: "{{ route('api-check-username') }}",
            type: 'POST',
            data: {
                _token: _token,
                name: name,
                id: id
            },
            beforeSend: function() {
                // Có thể thêm loading spinner ở đây nếu cần
                $('#loader-overlay').show();
                $('#loader-overlay').css('display','flex')
            },
            success: function(data) {
                $('#loader-overlay').hide();
                if (data.exists) {
                    // Username đã tồn tại - hiển thị icon X đỏ
                    $('#name').text(data.message);
                    $("input[name='name']").addClass('is-invalid');
                } else {
                    // Username khả dụng - hiển thị icon tick xanh
                    // $('#nameSuccess').text(data.message);
                    $("input[name='name']").removeClass('is-invalid').addClass('is-valid');
                }
            },
            error: function() {
                console.log('Error checking username');
            }
        });
    }


});
</script>
@stop