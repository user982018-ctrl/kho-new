API GHN
Tạo đơn:
    $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create";
    $response = Http::timeout(30)->withHeaders([
        'token' => $token,
        'ShopId' => $shopId,
    ])->withBody(
        json_encode($data)
    )->post($endpoint);
Data param:
    /* service_type_id 
        5: hàng nặng
        2: hàng nhẹ

        shopID:
        4298110: shop 2kg
        5187355: shop 5kg
        5187357: shop 10kg
        190998: test
        */
    $serviceTypeId = 5;
    $shopId = '5187357';
    if ($totalWeight < 5000) {
        //set cho shop 2kg
        $shopId = '4298110';
        $serviceTypeId = 2;
    } elseif ($totalWeight < 10000) {
        //set cho shop 5kg
        $shopId = '5187355';
        $serviceTypeId = 2;
    } else if ($totalWeight < 15000) {
        $serviceTypeId = 2;
    }

Data param:
{
  "payment_type_id": 1,
  "note": null,
  "required_note": "CHOXEMHANGKHONGTHU",
  "to_name": "Hồ Minh Thắng Thằng",
  "to_phone": "0939609644",
  "to_address": "chợ hòa tân, Hòa Tân, Huyện Châu Thành, Tỉnh Đồng Tháp",
  "to_ward_code": "50116",
  "to_district_id": "3155",
  "cod_amount": 1440000,
  "weight": 10500,
  "cod_failed_amount": 50000,
  "service_type_id": 2,
  "items": [
    {
      "name": "Xô Tricho 10L",
      "quantity": 1,
      "length": 20,
      "width": 20,
      "height": 20,
      "weight": 10000
    },
    {
      "name": "Fulvic 500ml",
      "quantity": 1,
      "length": 20,
      "width": 20,
      "height": 20,
      "weight": 500
    }
  ]
}
