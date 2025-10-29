<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hành trình đơn hàng ViettelPost</title>
  <link rel="icon" type="image/x-icon" href="{{asset('public/images/vietelpost.png')}}">
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
      /* height: 90vh; */
      /* overflow-y: auto; */
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .header {
      font-weight: bold;
      color: white;
      background: linear-gradient(135deg, #e61111 0%, #ff5252 100%);
      padding: 15px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 20px;
      font-size: 18px;
      box-shadow: 0 2px 8px rgba(230, 17, 17, 0.3);
    }
    .order-code {
      font-size: 20px;
      font-weight: bold;
      margin-top: 5px;
    }
    .status-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      margin-top: 8px;
    }
    .status-success {
      background: #4caf50;
      color: white;
    }
    .status-processing {
      background: #ff9800;
      color: white;
    }
    .status-pending {
      background: #2196f3;
      color: white;
    }
    .recipient-info {
      font-size: 15px;
      line-height: 1.8;
      padding: 15px;
      background: #fff5f5;
      border-left: 4px solid #e61111;
      border-radius: 5px;
      margin-bottom: 25px;
    }
    .recipient-info div {
      margin-bottom: 8px;
    }
    .recipient-info strong {
      color: #e61111;
      min-width: 120px;
      display: inline-block;
    }
    .section-title {
      font-size: 16px;
      font-weight: bold;
      color: #e61111;
      margin: 20px 0 15px 0;
      padding-bottom: 8px;
      border-bottom: 2px solid #e61111;
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
      background: #ddd;
    }
    .timeline li {
      position: relative;
      padding-left: 37px;
      margin-bottom: 20px;
    }
    .timeline li:first-child::before {
      content: "";
      width: 14px;
      height: 14px;
      background: #e61111;
      border: 3px solid white;
      box-shadow: 0 0 0 2px #e61111;
      border-radius: 50%;
      position: absolute;
      left: 9px;
      top: 6px;
    }

    .timeline li::before {
      content: "";
      width: 14px;
      height: 14px;
      background: #868181;
      border: 3px solid white;
      box-shadow: 0 0 0 2px #868181;
      border-radius: 50%;
      position: absolute;
      left: 9px;
      top: 6px;
    }
    /* Style cho li khi có class success-delivery */
    .timeline li.success-delivery::before {
      background: #4caf50;
      box-shadow: 0 0 0 2px #4caf50;
      width: 16px;
      height: 16px;
      left: 8px;
      top: 5px;
    }
    
    /* Style cho status text của đơn giao thành công */
    .timeline li.success-delivery .status {
      color: #4caf50;
      font-weight: bold;
    }
    
    /* CSS hiện đại: tự động detect span.done bên trong li (cho trình duyệt mới) */
    @supports selector(:has(*)) {
      .timeline li:has(span.done)::before {
        background: #4caf50;
        box-shadow: 0 0 0 2px #4caf50;
        width: 16px;
        height: 16px;
        left: 8px;
        top: 5px;
      }
      
      .timeline li:has(span.done) .status {
        color: #4caf50;
        font-weight: bold;
      }
    }
    
    .timeline .time {
      color: #666;
      font-size: 13px;
      margin: 6px 0;
      font-weight: 500;
      padding: 10px;
    }
    .timeline .status {
      color: #e61111;
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 4px;
      padding-top: 5px;
    }
    .timeline .desc {
      color: #555;
      line-height: 1.5;
      font-size: 14px;
    }
    .timeline .location {
      color: #888;
      font-size: 13px;
      margin-top: 4px;
      font-style: italic;
    }

    @media (max-width: 480px) {
      .modal {
        padding: 15px;
      }
      .header {
        font-size: 16px;
        padding: 12px;
      }
      .order-code {
        font-size: 18px;
      }
      .timeline li {
        padding-left: 37px;
      }
      .timeline::before {
        left: 12px;
      }
    }

    .timeline.shiping li::before {
      width: 8px;
      height: 8px;
    }
  </style>
</head>
<body>

<?php $checkAll = isFullAccess(Auth::user()->role); ?>
@if (isset($data) && $data)
<?php 
    $order = $data['order'];
    $statusLogs = $data['statusLogs'];
    // $orderCode = ($order->shippingOrder) ? $order->shippingOrder->order_code : '';
?>
<div class="modal">
    <div class="header">
        <div>Đơn hàng ViettelPost</div>
        <div class="order-code">{{$order['ORDER_NUMBER']}}</div>
        @if (isset($order['ORDER_STATUS_NAME']))
        <span class="status-badge status-processing">{{$order['ORDER_STATUS_NAME']}}</span>
        @endif
    </div>
    
    <div class="recipient-info">
        <div><strong>👤 Người nhận:</strong> <span id="name-order"> 
          <?php $name = $order['RECEIVER_FULLNAME'];
          if (!$checkAll && strlen($name) >= 4) {
              $lastThree = substr($name, -4);
              $maskedName = str_repeat('-', strlen($name) - 4) . $lastThree;
              echo $maskedName;
          } else {
              echo $name;
          }
          ?> </span></div>
        <div><strong>📞 Số điện thoại:</strong> <span id="phone-order"> 
          <?php $phone = $order['RECEIVER_PHONE'];
          if (!$checkAll) {
              $lastThree = substr($phone, -2);
              $maskedName = str_repeat('-', strlen($phone) - 2) . $lastThree;
              echo $maskedName;
          } else {
              echo $phone;
          }
          ?> </span></div>
        <div><strong>📍 Địa chỉ:</strong> {{$order['RECEIVER_ADDRESS'] ?? 'N/A'}}</div>
        @if (isset($order['PRODUCT_WEIGHT']))
        <div><strong>⚖️ Khối lượng:</strong> {{$order['PRODUCT_WEIGHT']}}g</div>
        @endif
        @if (isset($order['MONEY_COLLECTION']))
        <div><strong>💰 Thu hộ (COD):</strong> {{number_format($order['MONEY_COLLECTION'])}} đ</div>
        @endif
    </div>

    @if (count($statusLogs) > 0)
    <div class="section-title">📦 Hành trình vận chuyển</div>
    <ul class="timeline"> 
      {{-- <?php dd($statusLogs); ?> --}}
        @foreach ($statusLogs as $log)
          <li class="{{ $log['STATUS_NAME'] == 'Giao thành công' ? 'success-delivery' : '' }}">
            <div class="status">{{$log['STATUS_NAME'] ?? 'Cập nhật trạng thái'}}</div>
            <div class="time">
              <?php $tracking = $log['TRACKINGS'][0];
              $str = $tracking['THOI_GIAN'] . ': ';
              if ($log['STATUS_NAME'] == 'Tạo đơn hàng') {
                $str .= $tracking['QUAN_HUYEN_TAO_DON'] . " - " . $tracking['TINH_TAO_DON'];
              } else if ($log['STATUS_NAME'] == 'Đã nhận hàng') {
                $str .= "Nhận thành công - Nhân viên " . $tracking['NGUOI_NHAP_MAY_DETAIL']['NAME'] . " - " . $tracking['NGUOI_NHAP_MAY_DETAIL']['PHONE'];
              } else if ($log['STATUS_NAME'] == 'Đã kết nối'){
                $str .= "Kết nối từ " . $tracking['TEN_BUUCUC_DI'] . " - " . $tracking['QUAN_HUYEN_BUU_CUC_DI'] . " - " . $tracking['TINH_TAO_DON'] . " đến " 
                  . $tracking['TEN_BUUCUC_DEN'] . " - " . $tracking['QUAN_HUYEN_BUU_CUC_DEN'] . " - " . $tracking['TINH_BUU_CUC_DEN'] ;
              } else if ($log['STATUS_NAME'] == 'Đang vận chuyển') {
                $strShipping = '<ul class="timeline shiping">';
                // dd($log['TRACKINGS']);
                foreach ($log['TRACKINGS'] as $tracking) {
                  if ($tracking['TEN_BUUCUC_DEN'] == 'Đội vận chuyển') {
                    $strShipping .= '<li>' . $tracking['THOI_GIAN'] . ': Nhận tại ' . $tracking['TEN_BUUCUC_DEN'] . '</li>';
                  } else {
                    $strShipping .= '<li>' . $tracking['THOI_GIAN'] . ': Kết nối từ ' . $tracking['TEN_BUUCUC_DEN'] .  ' - ' . $tracking['QUAN_HUYEN_BUU_CUC_DEN'] . ' - ' . $tracking['TINH_BUU_CUC_DEN'] . '</li>';
                  }
                }
                $strShipping .= '</ul>';
                echo $strShipping;
                continue;
                
              } else if ($log['STATUS_NAME'] == 'Vận chuyển') {
                $str .= "Nhận tại " . $tracking['TEN_BUUCUC_DI'] . " - " . $tracking['QUAN_HUYEN_BUU_CUC_DI'] . " - " . $tracking['TINH_BUU_CUC_DI'] . " - " . $tracking['SDT_BUU_CUC_DI'] ;
              } else if ($log['STATUS_NAME'] == 'Giao hàng') {
                $str .= "Nhân viên giao hàng " . $tracking['NGUOI_NHAP_MAY_DETAIL']['NAME'] . " - " . $tracking['NGUOI_NHAP_MAY_DETAIL']['PHONE'];
              } else if ($log['STATUS_NAME'] == 'Giao thành công') {
                $str .= '<span class="done"> ' . 'Người nhận: ' . $tracking['RECEIVER_FULLNAME'].'</span>';
              } else {
                $str .= $tracking['NOI_DUNG'];
              }
             
              // dd($str);
              echo $str;
              ?>
            </div>
          </li>
        @endforeach
    </ul>
    @else
    <div class="section-title">📦 Hành trình vận chuyển</div>
    <p style="text-align: center; color: #999; padding: 20px;">Chưa có thông tin vận chuyển</p>
    @endif
</div>
@else
<div class="modal">
    <div class="header">Không tìm thấy thông tin đơn hàng</div>
    <p style="text-align: center; color: #999; padding: 20px;">Vui lòng kiểm tra lại mã vận đơn</p>
</div>
@endif

</body>
</html>

