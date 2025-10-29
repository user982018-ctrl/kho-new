<!DOCTYPE html>
    <html lang="en">
      <head>
        <base href="./">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta name="description" content="Đổi mật khẩu">
        <title>Đổi mật khẩu - Hệ thống</title>
       

        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="theme-color" content="#ffffff">
        <!-- Vendors styles-->
        <link rel="stylesheet" href="{{asset('public/vendors/simplebar/css/simplebar.css')}}">
        <link rel="stylesheet" href="{{asset('public/css/vendors/simplebar.css')}}">
        <!-- Main styles for this application-->
        <link href="{{asset('public/css/style.css')}}" rel="stylesheet">
        <!-- We use those styles to show code examples, you should remove them in your application.-->
        <link href="{{asset('public/css/examples.css')}}" rel="stylesheet">
      </head>
      <body>
        <div class="bg-light min-vh-100 d-flex flex-row align-items-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body p-5">
                    <div class="text-center mb-4">
                      <h1><i class="fa fa-lock text-warning"></i> Đổi mật khẩu</h1>
                      <p class="text-medium-emphasis">Vui lòng đổi mật khẩu để tiếp tục sử dụng hệ thống</p>
                    </div>

                    @if ($message = Session('warning'))
                      <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-triangle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif

                    @if ($message = Session('error'))
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-times-circle"></i> {{ $message }}
                        <button type="button dial" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif

                    @if ($message = Session('success'))
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif

                    @if ($errors->any())
                      <div class="alert alert-danger">
                        <ul class="mb-0">
                          @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif

                    <form action="{{route('change-password-post')}}" method="POST">
                        {{ csrf_field() }}
                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-lock-locked')}}"></use>
                                </svg>
                            </span>
                            <input value="{{old('current_password')}}" name="current_password" class="form-control @error('current_password') is-invalid @enderror" type="password" placeholder="Mật khẩu hiện tại (tên đăng nhập)" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-lock-locked')}}"></use>
                                </svg>
                            </span>
                            <input name="new_password" class="form-control @error('new_password') is-invalid @enderror" type="password" placeholder="Mật khẩu mới (tối thiểu 6 ký tự)" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-lock-locked')}}"></use>
                                </svg>
                            </span>
                            <input name="new_password_confirmation" class="form-control" type="password" placeholder="Xác nhận mật khẩu mới" required>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="fa fa-save"></i> Đổi mật khẩu
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> 
                                Mật khẩu mới không được trùng với tên đăng nhập của bạn
                            </small>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- CoreUI and necessary plugins-->
        <script src="{{asset('public/vendors/@coreui/coreui/js/coreui.bundle.min.js')}}"></script>
        <script src="{{asset('public/vendors/simplebar/js/simplebar.min.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      </body>
    </html>

