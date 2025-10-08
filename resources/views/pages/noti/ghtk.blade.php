<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Token hết hạn</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto+Mono:400,700" rel="stylesheet">
  <style>
    body {
      background: #000;
      color: #fff;
      font-family: 'Roboto Mono', monospace;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    .container {
      text-align: center;
    }

    .copy-container {
      position: relative;
      display: inline-block;
    }

    .copy-container p {
      font-size: 2rem;
      display: inline-block;
    }

    .handle {
      display: inline-block;
      width: 3px;
      height: 2rem;
      background: #fff;
      vertical-align: bottom;
      margin-left: 3px;
    }

    #cb-replay {
      margin-top: 40px;
      cursor: pointer;
    }

    #cb-replay svg {
      width: 40px;
      height: 40px;
      fill: #fff;
      transition: fill 0.3s;
    }

    #cb-replay:hover svg {
      fill: #f39c12;
    }

    #typed {
      font-size: 1.5rem;
      position: relative;
      display: inline-block;
      white-space: pre-wrap; /* giữ xuống dòng */
      line-height: 1.5em;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="copy-container">
      {{-- <!-- 👉 Bạn có thể thay đổi dòng chữ này -->
      <p>Ngày hôm qua dạy tôi trưởng thành.</p>
      <span class="handle"></span> --}}
    </div>

    <div id="cb-replay">
      <!-- nút replay -->
      
    </div>
  </div>

  <!-- jQuery + GSAP -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/TweenMax.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/plugins/TextPlugin.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
<div id="typed"></div>
<script>
  new Typed('#typed', {
    strings: ["Đang suy nghĩ..","...","...","Token dành cho GHTK đã hết hạn.", "Làm ơn liên hệ với quản lý của bạn để khắc phục!","Token dành cho GHTK đã hết.", "Làm ơn liên hệ với quản lý của bạn để khắc phục!"],
    typeSpeed: 80,
    backSpeed: 30,
    loop: true
  });
</script>
  </script>

</body>
</html>
