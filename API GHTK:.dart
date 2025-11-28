API GHTK:
Tạo đơn:
   if ($totalWeight >= 20) {
        $data['order']['3pl'] = 1;
    }

    // dd(json_encode($data));
    $token = '1L0DDGVPfiJwazxVW0s7AQiUhRH1hb7E1s63rtd';
    $endpoint = "https://services.giaohangtietkiem.vn/services/shipment/order";
    $response = Http::withHeaders([
        'token' => $token,
        'X-Client-Source' => 'S21178843',
        'Content-Type' => 'application/json'
    ])->withBody(
        json_encode($data)
    )->post($endpoint);
    
Data param:
    {
  "products": [
    {
      "name": "Xô Tricho 10L",
      "quantity": 1,
      "length": 20,
      "width": 20,
      "height": 20,
      "weight": 10
    },
    {
      "name": "Fulvic 500ml",
      "quantity": 1,
      "length": 20,
      "width": 20,
      "height": 20,
      "weight": 0.5
    }
  ],
  "order": {
    "id": "34173",
    "pick_name": "Phân bón MN",
    "pick_tel": "0986987791",
    "pick_address": "19/1c Nguyễn Thị Chiên",
    "pick_province": "TP Hồ Chí Minh",
    "pick_district": "Huyện Củ Chi",
    "pick_ward": "Xã Tân An Hội",
    "tel": "0939609644",
    "name": "Hồ Minh Thắng Thằng",
    "address": "chợ hòa tân, Hòa Tân, Huyện Châu Thành, Tỉnh Đồng Tháp",
    "province": "Đồng Tháp",
    "district": "Huyện Châu Thành",
    "ward": "Xã Hòa Tân",
    "hamlet": "Khác",
    "is_freeship": "1",
    "value": 1440000,
    "transport": "road",
    "pick_option": "cod",
    "pick_money": 1440000,
    "total_weight": 10.5,
    "total_box": 2
  }
}