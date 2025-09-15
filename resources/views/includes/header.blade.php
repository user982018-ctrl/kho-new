<div class="container-fluid">
  <button class="header-toggler px-md-0 me-md-3" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
    <svg class="icon icon-lg">
      <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-menu')}}"></use>
    </svg>
  </button><a class="header-brand d-md-none" href="{{route('home')}}">
    {{-- <svg width="118" height="46" alt="CoreUI Logo">
      <use xlink:href="{{asset('public/assets/brand/coreui.svg#full')}}"></use>
    </svg> --}}
    <img style="height: 80px;" src="{{ asset('public/img/logo/Logo.png')}}" alt="logo">
  </a>
  <ul class="header-nav d-none d-md-flex">
    <li class="nav-item"><a class="nav-link" href="{{route('home')}}">Tổng quan</a></li>
    <!-- <li class="nav-item"><a class="nav-link" href="#">Users</a></li>
    <li class="nav-item"><a class="nav-link" href="#">Settings</a></li> -->
  </ul>
  <ul class="header-nav ms-auto">
    <li class="nav-item"><a class="nav-link" href="#">
        <svg class="icon icon-lg">
          <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-bell')}}"></use>
        </svg></a></li>
    <li class="nav-item"><a class="nav-link" href="#">
        <svg class="icon icon-lg">
          <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-list-rich')}}"></use>
        </svg></a></li>
    <li class="nav-item"><a class="nav-link" href="#">
        <svg class="icon icon-lg">
          <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-envelope-open')}}"></use>
        </svg></a></li>
  </ul>
  <ul class="header-nav ms-3">
    <li class="nav-item dropdown">
      <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        <div class="avatar avatar-md" id="avatar">
          <?php $src = 'public/assets/img/avatars/8.jpg';
          if (Auth::user()->profile_image) {
            $src = 'storage/app/public/' . Auth::user()->profile_image;
          }
          ?>
          <img class="avatar-img" src="{{asset($src)}}">
        </div>
      </a>
      <div class="dropdown-menu dropdown-menu-end pt-0">
        <div class="dropdown-header bg-light py-2">
          <div class="fw-semibold">Tài Khoản</div>
        </div>
        
        <a class="dropdown-item" href="{{route('update-user', Auth::user()->id)}}">
          <svg class="icon me-2">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-user')}}"></use>
          </svg> Hồ sơ
        </a>
          <a class="dropdown-item" href="{{route('log-out')}}">
          <svg class="icon me-2">
            <use xlink:href="{{asset('public/vendors/@coreui/icons/svg/free.svg#cil-account-logout')}}"></use>
          </svg> Đăng xuất</a>
      </div>
      <a style="color: rgba(44, 56, 74, 0.681);;" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        <span style="width:100%; display: inline-block; text-align: center">{{Auth::user()->real_name ?: Auth::user()->name }}</span>
      </a>
    </li>
  </ul>
</div>