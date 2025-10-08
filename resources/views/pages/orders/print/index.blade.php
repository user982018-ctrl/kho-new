<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>In đơn</title>
     <link rel="stylesheet" href="{{asset("public/css/pages/shiping.css")}}">

</head>
<body>
 <?php
    // dd($list);
 ?>

<form>
    @foreach ($list as $k => $package)
    <a target="_blank" href="{{URL::to('in-don-' . $k . '?q=' . json_encode($package))}}">
        <div class="card-shipping">
            <input type="radio" onclick="clickToHrefShippingBy('{{$k}}')">
            <label>
                <h5>{{$k}}</h5>
                <img src="{{asset('public/images/'. $k .'.png')}}" class="card-img-top">
            </label>
        </div>
    </a>
    @endforeach
</form>
        
</body>
</html>