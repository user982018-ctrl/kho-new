<!DOCTYPE html>
<html>
    <head>
        <title>Tạo Vận Đơn</title>
        <!--Google Font-->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
        <!--Linking Stylesheet-->
        
        <link rel="stylesheet" href="{{asset("public/css/pages/shiping.css")}}">

    </head>
<?php
$orderId = $order->id;
    ?>
    <body>
        <div style="padding: 20px;">
            <p>Địa chỉ chi tiết: {{$order->address}}</p>
        </div>
        
        <form action="">
            <a href="{{URL::to('tao-van-don-ghn/'. $orderId)}}">
                <div class="card-shipping">
                    <input type="radio" onclick="clickToHrefShippingBy('ghn')">
                    <label>
                        <h5>Giao Hàng Nhanh</h5>
                        <img src="{{asset('public/images/ghn.png')}}" class="card-img-top">
                    </label>
                </div>
            </a>
            <a href="">
                <div class="card-shipping">
                    <input type="radio" name="pricing" id="card2" onclick="clickToHrefShippingBy('ghtk')">
                    <label for="card2">
                        <h5>Giao Hàng Tiết Kiệm</h5>
                        <img src="{{asset('public/images/ghtk.png')}}" class="card-img-top">
                    </label>
                </div>
            </a>
            <a href="">
                <div class="card-shipping">
                    <input type="radio" name="pricing" id="card3">
                    <label for="card3">
                        <h5>Nhất Tín</h5>
                        <img src="{{asset('public/images/nhatin.jpeg')}}" class="card-img-top">
                    </label>
                </div>
            </a>
        </form>

<script>
function clickToHrefShippingBy(e) {
    if (e === 'ghn') {
        window.location = "{{URL::to('tao-van-don-ghn/'. $orderId)}}";
    } else if (e === 'ghtk') {
        window.location = "{{URL::to('tao-van-don-ghtk/'. $orderId)}}";
    }
}
</script>
    </body>
</html>





