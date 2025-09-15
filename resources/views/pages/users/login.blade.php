<!DOCTYPE html>
    <html lang="en">
      <head>
        <base href="./">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta name="description" content="Đăng nhập vào kho Công Nghệ Cao">
        <title>Đăng nhập vào hệ thống Usu</title>
       

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
              <div class="col-lg-8">
                <div class="card-group d-block d-md-flex row">
                  <div class="card col-md-7 p-4 mb-0">
                    <div class="card-body">
                      <h1>Đăng nhập</h1>
                      <p class="text-medium-emphasis">Đăng nhập vào tài khoản của bạn</p>
                      <form action="{{route('login-post')}}" method="POST">
                        {{ csrf_field() }}
                        <div class="input-group mb-3"><span class="input-group-text">
                            <svg class="icon">
                                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-use')}}r"></use>
                            </svg></span>
                            <input value="{{Session('email')}}" name="name" class="form-control" type="text" placeholder="user" required>
                        </div>
                        <div class="input-group mb-4"><span class="input-group-text">
                            <svg class="icon">
                                <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-lock-locked')}}"></use>
                            </svg></span>
                            <input name="password" class="form-control" type="password" placeholder="Password" required>
                        </div>
                        <div class="row">
                            <div class="col-6">
                            <button class="btn btn-primary px-4" type="submit">Đăng nhập</button>
                            </div>
                            <div class="col-6 text-end">
                            <button class="btn btn-link px-0" type="button">Quên mật khẩu?</button>
                            </div>
                        </div>
                    </form>

                    @if ($message = Session('error'))
                    
                    <script type="text/javascript" >
                        alert('{{ $message }}');
                    </script>

                    @endif
                    </div>
                  </div>
                  <div class="card col-md-5 text-white bg-primary py-5">
                    <div class="card-body text-center">
                      <div>
                        <h2>Đăng ký</h2>
                        <p>Liên hệ admin hoặc tự tạo tài khoản để đăng nhập.</p>
                        <button class="btn btn-lg btn-outline-light mt-3" type="button">Đăng ký ngay!</button>
                      </div>
                    </div>
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
    
      </body>
    </html>