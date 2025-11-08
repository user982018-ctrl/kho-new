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

   <style>
    @media (max-width: 768px) {
        .modal-body {
            padding: unset !important;
        }
    }
   </style>

    <style>
        /* Tab styling */
        .nav-tabs .nav-link {
            border: none !important;
            border-bottom: 3px solid transparent !important;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #764ba2 !important;
            border-bottom: 3px solid #764ba2 !important;
        }
        .nav-tabs .nav-link.active {
            color: #667eea !important;
            border-bottom: 3px solid #667eea !important;
            background: transparent !important;
        }
        /* Tab content styling */
        .tab-content {
            min-height: 400px;
        }
        .tab-pane {
            padding: 10px 0;
        }
        .tab-pane.active {
            display: block !important;
        }
        
        /* Animation cho dòng chữ chạy */
        @keyframes scroll-left {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        /* Đảm bảo content không bị che bởi dòng chữ chạy */
        #content-right {
            padding-bottom: 50px !important;
        }
    </style>

  <script>
    (function() {
      function showCopyToast(message) {
        if (!message) return;
        var toast = document.createElement('div');
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.left = '50%';
        toast.style.bottom = '24px';
        toast.style.transform = 'translateX(-50%)';
        toast.style.background = 'rgba(0,0,0,0.8)';
        toast.style.color = '#fff';
        toast.style.padding = '8px 12px';
        toast.style.borderRadius = '6px';
        toast.style.fontSize = '14px';
        toast.style.zIndex = '9999';
        toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.3)';
        document.body.appendChild(toast);
        setTimeout(function() {
          if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
        }, 1500);
      }

      function copyTextToClipboard(text, onSuccess, onError) {
        if (!text) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(onSuccess).catch(function() {
            fallbackCopy(text, onSuccess, onError);
          });
        } else {
          fallbackCopy(text, onSuccess, onError);
        }
      }

      function fallbackCopy(text, onSuccess, onError) {
        try {
          var tempInput = document.createElement('input');
          tempInput.value = text;
          document.body.appendChild(tempInput);
          tempInput.select();
          document.execCommand('copy');
          document.body.removeChild(tempInput);
          if (typeof onSuccess === 'function') {
            onSuccess();
          }
        } catch (e) {
          if (typeof onError === 'function') {
            onError(e);
          }
        }
      }

      document.addEventListener('click', function(event) {
        var target = event.target.closest('[data-copy], .phone-copy');
        if (!target) return;

        event.preventDefault();

        var copyValue = (target.getAttribute('data-copy') ||
                         target.getAttribute('data-phone') ||
                         target.textContent || '').trim();

        if (!copyValue) return;

        var message = target.getAttribute('data-copy-message') ||
                      (target.classList.contains('phone-copy') ? 'Đã copy số điện thoại' : 'Đã copy');

        copyTextToClipboard(copyValue, function() {
          showCopyToast(message);
        }, function() {
          alert('Đã copy: ' + copyValue);
        });
      });
    })();
  </script>

  </body>
</html>
