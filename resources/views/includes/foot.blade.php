
 <!-- CoreUI and necessary plugins-->
 <script src="{{asset('public/vendors/@coreui/coreui/js/coreui.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendors/simplebar/js/simplebar.min.js')}}"></script>
    <script src="{{asset('public/js/customOld.js')}}"></script>
    <script src="{{asset('public/js/drag.js')}}"></script>
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
      });
    </script>
    <!-- Plugins and scripts required by this view-->
     {{-- <script src="{{asset('public/vendors/chart.js/js/chart.min.js')}}"></script> 
    <script src="{{asset('public/vendors/@coreui/chartjs/js/coreui-chartjs.js')}}"></script>
    <script src="{{asset('public/vendors/@coreui/utils/js/coreui-utils.js')}}"></script>
   <script src="{{asset('public/js/main.js')}}"></script> --}}


   {{-- <script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script> --}}