<!DOCTYPE html>
<html lang="en">
  <head> @include('includes.head')  </head>
  <body>
    @include('notify::components.notify')
    <div class="sidebar sidebar-dark sidebar-fixed hide" id="sidebar">
        @include('includes.sidebar')
    </div>
    <div id="content-right" class="wrapper d-flex flex-column min-vh-100">
      <header class="header header-sticky mb-4"> 
        @include('includes.header')
        </header>
        @yield('content')
      {{-- <footer class="footer"> @include('includes.footer')</footer> --}}
    </div>
    
    @include('includes.foot')

  </body>
</html>
