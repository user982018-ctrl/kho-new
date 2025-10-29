<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hành trình đơn hàng GHTK</title>
  <link rel="icon" type="image/x-icon" href="{{asset('public/img/icons/icon-ghtk-v2.ico')}}">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 20px;
    }
    .modal {
      background: white;
      border-radius: 8px;
      padding: 20px;
      max-width: 800px;
      width: 100%;
      height: 90vh;
      overflow-y: auto;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .header {
      font-weight: bold;
      color: white;
      background-color: #1aaf5d;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 15px;
      font-size: 16px;
      /* position: sticky; */
      top: 0;
      z-index: 10;
    }
    .recipient-info {
      font-size: 15px;
      line-height: 1.6;
      padding: 12px;
      background: #f1fdf4;
      border-left: 4px solid #1aaf5d;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .timeline {
      list-style: none;
      padding: 0;
      margin: 0;
      position: relative;
    }
    .timeline::before {
      content: "";
      position: absolute;
      left: 15px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: #ccc;
    }
    .timeline li {
      position: relative;
      padding-left: 40px;
      margin-bottom: 18px;
    }
    .timeline li::before {
      content: "";
      width: 12px;
      height: 12px;
      background: #1aaf5d;
      border-radius: 50%;
      position: absolute;
      left: 10px;
      top: 6px;
    }
    .timeline .time {
      color: #555;
      font-size: 13px;
      margin-bottom: 4px;
    }
    .timeline .desc {
      color: #333;
      line-height: 1.4;
    }
    .timeline .icon {
      margin-right: 5px;
    }
    .timeline img {
      margin-top: 5px;
      max-width: 100%;
      border-radius: 6px;
      display: block;
    }

    @media (max-width: 480px) {
      .modal {
        padding: 15px;
      }
      .header {
        font-size: 14px;
      }
      .timeline li {
        padding-left: 35px;
      }
      .timeline::before {
        left: 12px;
      }
    }
  </style>
</head>
<body>

<?php $checkAll = isFullAccess(Auth::user()->role);
// dd($data);
 ?>


@if (isset($data) && $data)
<?php $package = $data['package'];
    $deliveryLog = $data['deliveryLog'];
?>
<div class="modal">
    <div class="header">Đơn hàng: {{$package['alias']}}</div>
    <div class="recipient-info">
        <div><strong>👤 Người nhận:</strong> <span id="name-order">
          <?php 
          $name = $package['customer_fullname'];
          if (!$checkAll && strlen($name) >= 4) {
              $lastThree = substr($name, -4);
              $maskedName = str_repeat('-', strlen($name) - 4) . $lastThree;
              echo $maskedName;
          } else {
              echo $name;
          }
          ?>
        </span></div>
        <div><strong>📞 SĐT:</strong><span id="phone-order">
          <?php $phone = $package['customer_tel'];
                        if (!$checkAll && strlen($phone) >= 4) {
                            $lastThree = substr($phone, -4);
                            $maskedName = str_repeat('-', strlen($phone) - 4) . $lastThree;
                            echo $maskedName;
                        } else {
                          echo $phone;
                        }
                        ?> </span></div>
        <div><strong>📍 Địa chỉ:</strong> {{$package['customer_first_address']}}</div>
    </div>
    <ul class="timeline">
        @foreach ($deliveryLog as $log)
        <li><div class="time">{{$log['created']}}</div><div class="desc"><?php echo $log['desc']; ?></div>
            @if (isset($log['image']))
            <img src="{{$log['image']}}">
            @endif
        </li>
        @endforeach
    </ul>
</div>
@endif
</body>
</html>
