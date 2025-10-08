
 <!-- CoreUI and necessary plugins-->
 <script src="{{asset('public/vendors/@coreui/coreui/js/coreui.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendors/simplebar/js/simplebar.min.js')}}"></script>
    <script src="{{asset('public/js/customOld.js')}}"></script>
    <script src="{{asset('public/js/drag.js')}}"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
      $(document).ready(function () {
        //drag scroll
        var ds = $('.dragscroll1');
        var da = ds.find('.drags-area');
        da.mouseenter(function () {
            enable_drag_croll();
        });
        da.mouseleave(function () {
            disable_drag_croll();
        });

        // Toastr configuration
        toastr.options = {
          "closeButton": true,
          "progressBar": true,
          "positionClass": "toast-top-right",
          "timeOut": "5000"
        };

        // Display toastr notifications from session
        @if(Session::has('success'))
          toastr.success("{{ Session::get('success') }}");
        @endif

        @if(Session::has('error'))
          toastr.error("{{ Session::get('error') }}");
        @endif

        @if(Session::has('warning'))
          toastr.warning("{{ Session::get('warning') }}");
        @endif

        @if(Session::has('info'))
          toastr.info("{{ Session::get('info') }}");
        @endif
      });
    </script>
    <!-- Plugins and scripts required by this view-->
     {{-- <script src="{{asset('public/vendors/chart.js/js/chart.min.js')}}"></script> 
    <script src="{{asset('public/vendors/@coreui/chartjs/js/coreui-chartjs.js')}}"></script>
    <script src="{{asset('public/vendors/@coreui/utils/js/coreui-utils.js')}}"></script>
   <script src="{{asset('public/js/main.js')}}"></script> --}}


   {{-- <script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script> --}}