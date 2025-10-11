<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingOrder;
use App\Helpers\Helper;


class ShippingOrderController extends Controller
{
    public function createOrderGHTK(Request $req)
    {
        $dataReq = $req->all();
        $orderId = $dataReq['id'];
        $validator      = Validator::make($dataReq, [
            'phone'      => 'required',
            'name'     => 'required',
            'address'     => 'required',
            'district'     => 'required|not_in',
            'ward'     => 'required|not_in',
            'cod_amount'     => 'required',
            'products'     => 'required',
        ],[
            'phone.required' => 'Nhập số điện thoại',
            'name.required' => 'Nhập tên khách hàng',
            'address.required' => 'Nhập địa chỉ nhận hàng',
            'district.not_in' => 'Chọn quận huyện',
            'ward.not_in' => 'Chọn xã phường',
            'cod_amount.required' => 'Nhập số COD',
            'products.required' => 'Thêm sản phẩm',
        ]);
        
        if (!isset($dataReq['products'])) {
            notify()->error('Thiếu sản phẩm', 'Thất bại!');
            return back();
        }

        if ($validator->passes()) {    
            $totalWeight = 0;
            $items = [];

            foreach ($dataReq['products'] as $product) {
                $weight = (int) str_replace(",", "", $product['weight']);
                $weight = $weight / 1000; //đổi ra kg
                $totalWeight += $weight;
                
                $items[] = [
                    "name" => $product['name'],
                    "quantity" => 1,
                    "length" => 20,
                    "width" => 20,
                    "height" =>20,
                    "weight" => $weight
                ];
            }

            $cutStringProvince = explode('-', $dataReq['district_label']);
            $district = trim($cutStringProvince[0]);
            $province = trim($cutStringProvince[1]);
            $ward = $dataReq['ward_label'];

            $codAmount = (int) str_replace(",", "", $dataReq['cod_amount']);

            $data = [
                'products' => $items,
                'order' => [
                    'id' => $orderId,
                    "pick_name" => "Phân bón MN",
                    "pick_tel" =>  "0986987791",
                    "pick_address" =>  "19/1c Nguyễn Thị Chiên",
                    "pick_province" =>  "TP Hồ Chí Minh",
                    "pick_district" =>  "Huyện Củ Chi",
                    "pick_ward" =>  "Xã Tân An Hội",

                    "tel" => $dataReq['phone'],
                    "name" => $dataReq['name'],
                    "address" => $dataReq['address'],
                    "province" => $province,
                    "district" => $district,
                    "ward" => $ward,

                    "hamlet" => "Khác",
                    "is_freeship" => "1",
                    "value" => $codAmount,
                    "transport" => "road",
                    "pick_option" => 'cod',
                    "pick_money" => $codAmount,
                    // "customer_ship_money" => $codAmount,
                    "total_weight" => $totalWeight,
                    "total_box" => count($items)
                    // "tags" => [
                    //     10, //cho xem hàng
                    //     // 11, //cho thử hàng, đồng kiểm
                    //     // 13, //Gọi cho shop khi không giao được, //Gọi cho shop khi khách không nhận được hàng, không liên lạc được, sai thông tin 
                    // ]
                ]
            ];
            
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

            $response = $response->json();
            if (isset($response['success']) && $response['success']) {
                $orderCode = $response['order']['tracking_id'];
                $this->saveShippingCodeGHTK($orderCode, $orderId);
                notify()->success('Thêm vận đơn thành công', 'Thành công!');
                
            } else {
                notify()->error('Đã xảy ra lỗi!', 'Thất bại!');
                notify()->error($response['message'], 'Thất bại!');
                return back();
            }

        } else {
            foreach ($validator->errors()->messages() as $mes) {
                notify()->error($mes[0], 'Thất bại!');
            }
            return back();
        }

        return redirect('chi-tiet-don-hang/' . $orderId);
    }

    public function viewCreateShippingGHTK($id)
    {
        $order = Orders::find($id);
        if ($order) {
            $ship = ShippingOrder::whereOrderId($id)->first();
            if ($ship) {
                // notify()->error('Vận đơn đã được tạo', 'Cảnh báo!'); 
                return redirect('chi-tiet-don-hang/' . $id);
            }

            $addressCtl = new AddressController();
            $listProvince = $addressCtl->getListProvince();
            $listWard = $addressCtl->getListWardById($order->district);
            
            return view('pages.orders.shipping.ghtk')->with('order', $order)
                ->with('listWard', $listWard)
                ->with('listProvince', $listProvince);
        } else {
            notify()->error('Không tìm thấy đơn hàng!', 'Thử lại!');
        }
        
        return redirect()->route('order');
    }

    public function removeShipingOrderCode($id)
    {
        $ship = ShippingOrder::find($id);

        if($ship) {
            $ship->delete();
            notify()->success('Gỡ vận đơn thành công', 'Thành công!');
            
        } else {
            notify()->error('Không tìm thấy vận đơn trong hệ thống', 'Thất bại!'); 
        }

        return back();
    }
    public function createOrderGHN(Request $req)
    {
        $dataReq = $req->all();
        $orderId = $dataReq['id'];
        $validator      = Validator::make($dataReq, [
            'phone'      => 'required',
            'name'     => 'required',
            'address'     => 'required',
            'district'     => 'required|not_in',
            'ward'     => 'required|not_in',
            'cod_amount'     => 'required',
            'products'     => 'required',
        ],[
            'phone.required' => 'Nhập số điện thoại',
            'name.required' => 'Nhập tên khách hàng',
            'address.required' => 'Nhập địa chỉ nhận hàng',
            'district.not_in' => 'Chọn quận huyện',
            'ward.not_in' => 'Chọn xã phường',
            'cod_amount.required' => 'Nhập số COD',
            'products.required' => 'Thêm sản phẩm',
        ]);

        if (!isset($dataReq['products'])) {
            notify()->error('Thiếu sản phẩm', 'Thất bại!');
            return back();
        }

        if ($validator->passes()) {    
            $totalWeight = 0;
            $items = [];

            foreach ($dataReq['products'] as $product) {
                $weight = (int) str_replace(",", "", $product['weight']);
                $totalWeight += $weight;
                
                $items[] = [
                        "name" => $product['name'],
                        "quantity" => 1,
                        "length" => 20,
                        "width" => 20,
                        "height" =>20,
                        "weight" => $weight
                ];
                
            }

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

            $codAmount = (int) str_replace(",", "", $dataReq['cod_amount']);

            $data = [
                "payment_type_id" => 1, //người bán thanh toán phí ship
                "note" => $dataReq['note'],
                "required_note" => "CHOXEMHANGKHONGTHU",
                "to_name" => $dataReq['name'],
                "to_phone" => $dataReq['phone'],
                "to_address" => $dataReq['address'],
                "to_ward_code" =>  $dataReq['ward'],
                "to_district_id" => $dataReq['district'],
                "cod_amount" => $codAmount,
                "weight" => $totalWeight,
                "cod_failed_amount" => 50000, 
                // "deliver_station_id" => null,
                "service_type_id" => $serviceTypeId,
                // "coupon" => null,
                // "pick_shift" => [2],
                "items" => $items,
            ];

            /* token test
            * $shopId = '190998';
            * token 
            */
            // $shopId = '190998';
            try {
                $token = '180d1134-e9fa-11ee-8529-6a2e06bbae55';
                $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create";
                $response = Http::timeout(30)->withHeaders([
                    'token' => $token,
                    'ShopId' => $shopId,
                ])->withBody(
                    json_encode($data)
                )->post($endpoint);
    
                // dd($response->body());
                if ($response->status() == 200) {
                    $content  = json_decode($response->body());
                    $mess = $content->message_display;
                    $data = $content->data;
                    $orderCode = $data->order_code;
                    $this->saveShippingCodeGHN($orderCode, $orderId);
                    notify()->success($mess, 'Thành công!');
                    
                } else {
                    // dd($response);
                    notify()->error('Đã xảy ra lỗi!', 'Thất bại!');
                }
            } catch (Exception $e) {
                // dd($e);
                notify()->error('Đã xảy ra lỗi!', 'Thất bại!');
            }
            
            return back();

        } else {
            foreach ($validator->errors()->messages() as $mes) {
                notify()->error($mes[0], 'Thất bại!');
            }
            return back();
        }

        return redirect('chi-tiet-don-hang/' . $orderId);
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $list = $this->getListOrderByPermisson(Auth::user())->paginate(15);
        return view('pages.orders.index')->with('list', $list);
    }

    public function getListOrderByPermisson($user) {
        $roles      = $user->role;
        $list       = Orders::orderBy('id', 'desc');
        $checkAll   = false;
        $listRole   = [];
        // $roles      = json_decode(Auth::user()->role);
        $roles      = json_decode($roles);

        if ($roles) {
            foreach ($roles as $key => $value) {
                if ($value == 1) {
                    $checkAll = true;
                    break;
                } else {
                    $listRole[] = $value;
                }
            }
        }

        if (!$checkAll) {
            $list = $list->where('assign_user', $user->id);
        }

        return $list;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function add()
    { 
        $provinces      = $this->getProvince();
       // $listProduct    =  Product::all()->where('qty', '>', 0)->where('status', '=', 1);
        $listProduct    = $this->getListProductByPermisson(Auth::user()->role)->get();
        $listSale       = $this->getListSale()->get();

        return view('pages.orders.addOrUpdate')->with('listProduct', $listProduct)
            ->with('provinces', $provinces)->with('listSale', $listSale);
    }

    public function getListProductByPermisson($roles) {
        $list       = Product::orderBy('id', 'desc')->where('status', '=', 1);

        
        $checkAll   = false;
        $listRole   = [];
        // $roles      = json_decode(Auth::user()->role);
        $roles      = json_decode($roles);

        if ($roles) {
            foreach ($roles as $key => $value) {
                if ($value == 1) {
                    $checkAll = true;
                    break;
                } else {
                    $listRole[] = $value;
                }
            }
        }

        if (!$checkAll) {
            $list = $list->where('roles', $listRole);
        }

        return $list;
    }

    public function getProvince(){
        $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province";
        $response = Http::withHeaders([
            'token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897',
        ])->post($endpoint);
  
        $provinces  = [];
        if ( $response->status() == 200) {
            $content    = json_decode($response->body());
            $provinces  = $content->data;
        }

        return $provinces;
    }
    
     /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWardById(Request $request)
    {
        if(isset($request->id)){
            print ($request->id);
        }
    }

    /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $validator      = Validator::make($request->all(), [
            'name'      => 'required',
            'price'     => 'required',
            'qty'       => 'required|numeric|min:1',
            'address'   => 'required',
            // 'products'  => 'required',
            'sex'       => 'required',
            'phone'     => 'required',
        ],[
            'name.required' => 'Nhập tên khách hàng',
            'price.required' => 'Nhập tổng tiền',
            // 'price.numeric' => 'Chỉ được nhập số',
            'qty.required' => 'Nhập số lượng',
            // 'qty.numeric' => 'Chỉ được nhập số',
            'address.required' => 'Nhập địa chỉ',
            // 'products.required' => 'Chọn sản phẩm',
            'sex.required' => 'Chọn giới tính',
            'phone.required' => 'Nhập số lượng',
            'qty.min' => 'Vui lòng chọn sản phẩm',
        ]);
       
        if ($validator->passes()) {
            if(isset($request->id)){
                $order = Orders::find($request->id);
                $text = 'Cập nhật đơn hàng thành công.';
            } else {
                $order = new Orders();
                $text = 'Tạo đơn hàng thành công.';
            }

            $order->id_product      = $request->products;
            $order->phone           = $request->phone;
            $order->address         = $request->address;
            $order->name            = $request->name;
            $order->sex             = $request->sex;
            $order->total           = $request->price;
            $order->province        = $request->province;
            $order->district        = $request->district;
            $order->ward            = $request->ward;
            $order->qty             = $request->qty;
            $order->assign_user     = $request->assignSale;
            $order->is_price_sale   = $request->isPriceSale;
            $order->note            = $request->note;
            $order->status          = $request->status;
            $order->save();

            foreach (json_decode($order->id_product) as $item) {
                $product = Product::find($item->id);
                $product->qty = $product->qty - $item->val;
                $product->save();
            }

            return response()->json(['success'=>$text]);
        }
     
        return response()->json(['errors'=>$validator->errors()]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function viewUpdate($id)
    {
        $order          = Orders::find($id);
        if($order){
            // $provinces      = $this->getProvince();
            $listProduct    =  Product::all();
            // $listDistrict   =  $this->getListDistrictByProvinceId($order->province);
            // $listWard       =  $this->getListWardByDistrictId($order->district);
            $listSale       = $this->getListSale()->get();

            return view('pages.orders.addOrUpdate')->with('order', $order)
                ->with('listSale', $listSale)
                // ->with('provinces', $provinces)
                // ->with('listDistrict', $listDistrict)
                // ->with('listWard', $listWard)
                ->with('listProduct', $listProduct);
        } 

        return redirect('/');
    }

    public function getListDistrictByProvinceId($id) {
        $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district?province_id=" . $id;
        $response = Http::withHeaders(['token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897'])->get($endpoint);

        $district = [];
        if ($response->status() == 200) {
            $content   = json_decode($response->body());
            $district  = $content->data;
            return $district;
        }
    }

    public function getListWardByDistrictId($id) {
        $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=" . $id;
        $response = Http::withHeaders(['token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897'])->get($endpoint);
        $wards = [];
        if ($response->status() == 200) {
            $content    = json_decode($response->body());
            $wards  = $content->data;
            return $wards;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function delete($id)
    {
        $order = Orders::find($id);
        if($order){
            $order->delete();
            return redirect('/don-hang')->with('success', 'Đơn hàng đã xoá thành công!');            
        } 

        return redirect('/don-hang') ->with('error', 'Đã xảy ra lỗi khi xoá đơn hàng!');
    }

    
      /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search(Request $request)
    {
        // $list = $this->getListOrderByPermisson(Auth::user());
       
        $orders = Orders::where('name', 'like', '%' . $request->search . '%')
            ->orWhere('phone', 'like', '%' . $request->search . '%')
            ->orderBy('id', 'desc')->paginate(10);

        if($orders){
            return view('pages.orders.index')->with('list', $orders);           
        } 

        return redirect('/');
    }

    public function getListSale() {
        return User::where('status', 1)->where('is_sale', 1);
    }

    // public function getNameDistrictSystem($id)
    // {
    //     $json = file_get_contents(public_path('json/local.json'));
    //     $data = json_decode($json, true);
    //     $name  = "";

    //     foreach ($data as $kProvince => $item) {
    //         foreach ($item as $k => $v) {
    //             if ($k == 'District' || $k == 'districts') {
    //                 foreach ($v as $kDistric => $disctrict) {
    //                     if ($disctrict["id"] == $id) {
    //                         $name = $disctrict["name"];
    //                         break;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return $name;
    // }

    public function indexCreateShipping($id)
    {
        $order = Orders::find($id);
        if ($order) {
            $ship = ShippingOrder::whereOrderId($id)->first();
            if ($ship) {
                // notify()->error('Vận đơn đã được tạo', 'Cảnh báo!'); 
                return redirect('chi-tiet-don-hang/' . $id);
            }
            return view('pages.orders.shipping.index')->with('order', $order); 
        } 
        
        return redirect()->route('home');
    }

    public function viewCreateShippingGHN($id) {
        $order = Orders::find($id);
        if ($order) {
            $ship = ShippingOrder::whereOrderId($id)->first();
            if ($ship) {
                // notify()->error('Vận đơn đã được tạo', 'Cảnh báo!'); 
                return redirect('chi-tiet-don-hang/' . $id);
            }

            $addressCtl = new AddressController();
            $listProvince = $addressCtl->getListProvince();
            $listWard = $addressCtl->getListWardById($order->district);
            
            // $addressDataGHN = $addressCtl->getDistrictGHNByName($id);

            // $listDistrictGhn = $addressDataGHN['listDistricGhn'];
            // dd($listDistrictGhn);
            return view('pages.orders.shipping.ghn')->with('order', $order)
                ->with('listWard', $listWard)
                ->with('listProvince', $listProvince);

                // ->with('listDistrictGhn', $listDistrictGhn);

        } else {
            notify()->error('Không tìm thấy đơn hàng!', 'Thử lại!');
        }
        
        return redirect()->route('order');
    } 

    public function saveShippingCodeGHTK($orderCode, $orderId)
    {
        $orderCode = trim($orderCode);
        $ship = ShippingOrder::whereOrderCode($orderCode)->whereOrderId($orderId)
            ->first();
        // dd($ship);
        if (!$ship) {
            $link = "https://services.giaohangtietkiem.vn/services/shipment/v2/";
            $token = '1L0DDGVPfiJwazxVW0s7AQiUhRH1hb7E1s63rtd';

            $endpoint = $link . $orderCode;
            $response = Http::withHeaders(['token' => $token])->get($endpoint);
           
           
            if ($response->status() == 200) {
                // dd(json_decode($response->body()));
                $body = json_decode($response->body());
                if (!$body->success) {
                    return false;
                }

                $shippingNew = new ShippingOrder();
                $shippingNew->order_code = $orderCode;
                $shippingNew->order_id = $orderId;
                $shippingNew->vendor_ship = 'GHTK';
                $shippingNew->save();
                return true;
            }
        }

        return false;
    }

    public function saveShippingCodeGHN($orderCode, $orderId)
    {
        $orderCode = trim($orderCode);
        $ship = ShippingOrder::whereOrderCode($orderCode)->whereOrderId($orderId)
            ->first();

        if (!$ship) {
            $link = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail?order_code=";
            $token = '180d1134-e9fa-11ee-8529-6a2e06bbae55';

            // $link = "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail?order_code=";
            // $token = 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897';
            $endpoint = $link . $orderCode;
            $response = Http::withHeaders(['token' => $token])->get($endpoint);
           
            if ($response->status() == 200) {
                $shippingNew = new ShippingOrder();
                $shippingNew->order_code = $orderCode;
                $shippingNew->order_id = $orderId;
                $shippingNew->vendor_ship = 'GHN';
                $shippingNew->save();
                return true;
            }
        }

        return false;
    }

    public function createShippingHasGHTK(Request $req) 
    {
        // dd($this->saveShippingCodeGHTK($req->id_shipping_has, $req->order_id));
        if ($this->saveShippingCodeGHTK($req->id_shipping_has, $req->order_id)) { 
            notify()->success('Thêm vận đơn thành công', 'Thành công!');
            return redirect()->route('order');
        } else {
            notify()->error('Mã vận đơn GHTK không tồn tại!', 'Thử lại!');
        }

        return back();
    }

    public function createShippingHas(Request $req) 
    {
        if ($this->saveShippingCodeGHN($req->id_shipping_has, $req->order_id)) { 
            notify()->success('Thêm vận đơn thành công', 'Thành công!');
            return redirect()->route('order');
        } else {
            notify()->error('Mã vận đơn GHN không tồn tại!', 'Thử lại!');
        }

        return back();
    }

    public function getShippingLog($endpoint) {
        $response   = Http::withHeaders(['token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55'])->get($endpoint);
        $data       = [];

        if ($response->status() == 200) {
            $content    = json_decode($response->body());
            $data       = $content->data;
        }

        return $data;
    }

    public function detailDataGHTK($orderCode)
    {
        $printLog = $result = $deliveryLog = $package = [];
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzaG9wX2NvZGUiOiJTMjExNzg4NDMiLCJzaG9wX2lkIjoiNjIyODc1ZTktNjMyMC00ZTlhLTljY2MtNGJlYzBhNmU0ZDU5Iiwic2hvcF9vcmRlciI6MjExNzg4NDMsInN0YWZmX2lkIjoxNzY2Nzg0LCJzb3VyY2UiOiJwbGF0Zm9ybSIsInJvbGUiOiJhZG1pbiIsInNob3Bfc3RhdHVzX2lkIjoxLCJzaG9wX3R5cGUiOjEsImFjY2Vzc190b2tlbiI6IjRhMzk5YTUxLTUwMDgtNDM4NC05ZmI0LTM0NmNmOGI3NzQyZSIsImp3dCI6bnVsbCwiaW52YWxpZF9hdCI6eyJkYXRlIjoiMjAyNS0xMS0wMyAwOTo1NzoyMS43NjEzNjQiLCJ0aW1lem9uZV90eXBlIjozLCJ0aW1lem9uZSI6IkFzaWFcL0hvX0NoaV9NaW5oIn0sImxvZ2luX2FzX2lkIjpudWxsLCJsb2dpbl9hc19zZXNzaW9uX2lkIjpudWxsLCJsb2dpbl9hc190eXBlIjpudWxsLCJzZXNzaW9uIjpudWxsLCJtb3Nob3BfdXNlcl9pZCI6bnVsbCwic2hvcF90b2tlbiI6ImJkMTkzRTkyMGI3RTQwM2U4ZDVFNUI3Qzk5YkFiQWJjY2MyMjQzQ2YiLCJjcmVhdGVkX2F0Ijp7ImRhdGUiOiIyMDI1LTEwLTA0IDA5OjU3OjIxLjc1ODY2MSIsInRpbWV6b25lX3R5cGUiOjMsInRpbWV6b25lIjoiQXNpYVwvSG9fQ2hpX01pbmgifSwic2NvcGVzIjpbInNob3AudmlldyIsInNob3AudGVsLnZpZXcuIiwic2hvcC5lbWFpbC52aWV3Iiwic2hvcC5pZF9jYXJkLnZpZXciLCJzaG9wLnBpY2tfYWRkcmVzc2VzLnZpZXciLCJzaG9wLmJhbmtfYWNjb3VudC52aWV3Iiwic2hvcC51cGRhdGUiLCJzaG9wLmJhc2ljX2luZm8udXBkYXRlIiwic2hvcC5hdmF0YXIudXBkYXRlIiwic2hvcC5waWNrX2FkZHJlc3Nlcy51cGRhdGUiLCJzaG9wLnRlbC51cGRhdGUiLCJzaG9wLmVtYWlsLnVwZGF0ZSIsInNob3AuYmFua19hY2NvdW50LnVwZGF0ZSIsInNob3AuaWRfY2FyZC51cGRhdGUiLCJzaG9wLnN0YWZmLnZpZXciLCJzaG9wLnN0YWZmLmNyZWF0ZSIsInNob3Auc3RhZmYudXBkYXRlIiwic2hvcC5zdGFmZi5kZWxldGUiLCJzaG9wLmJyYW5jaC52aWV3Iiwic2hvcC5icmFuY2gubGlzdCIsInNob3AuYnJhbmNoLmNyZWF0ZSIsInNob3AuYnJhbmNoLnVwZGF0ZSIsInNob3AuYnJhbmNoLmRlbGV0ZSIsImNvbmZpZy5hcGlfdG9rZW4udmlldyIsImNvbmZpZy5hcGlfdG9rZW4ucmVxdWVzdCIsImNvbmZpZy5zeXN0ZW0udXBkYXRlIiwiY29uZmlnLmF1ZGl0X3RpbWUudmlldyIsImNvbmZpZy5hdWRpdF90aW1lLnVwZGF0ZSIsImNvbmZpZy5zaG9wLnVwZGF0ZSIsInNob3AuZGFzaGJvYXJkIiwic2hvcC5yZXBvcnQubW9uZXlfZmxvdyIsInNob3AucmVwb3J0LmRhaWx5LnZpZXciLCJzaG9wLnJlcG9ydC5kYWlseS5kb3dubG9hZCIsIm9yZGVyLmxpc3QiLCJvcmRlci5leHBvcnRfZmlsZSIsIm9yZGVyLmRldGFpbCIsIm9yZGVyLmNyZWF0ZSIsIm9yZGVyLmV4Y2hhbmdlLmNyZWF0ZSIsIm9yZGVyLmRlbGl2ZXJ5LmNyZWF0ZSIsIm9yZGVyLnVwZGF0ZSIsIm9yZGVyLnJlcXVlc3RfY2FuY2VsIiwib3JkZXIucHJpbnQiLCJvcmRlci5kcmFmdC52aWV3Iiwib3JkZXIuZHJhZnQubGlzdCIsIm9yZGVyLmRyYWZ0LmNyZWF0ZSIsIm9yZGVyLmRyYWZ0LnVwZGF0ZSIsIm9yZGVyLmRyYWZ0LmRlbGV0ZSIsInRpY2tldC5hZGQiLCJ0aWNrZXQub3JkZXIucGlja190ZWwudXBkYXRlIiwidGlja2V0Lm9yZGVyLnBpY2tfYWRkcmVzcy51cGRhdGUiLCJ0aWNrZXQub3JkZXIuY3VzdG9tZXJfdGVsLnVwZGF0ZSIsInRpY2tldC5vcmRlci5jdXN0b21lcl9hZGRyZXNzLnVwZGF0ZSIsInRpY2tldC5vcmRlci5waWNrX21vbmV5LnVwZGF0ZSIsImN1c3RvbWVyLnZpZXciLCJjdXN0b21lci51cGRhdGUiLCJjdXN0b21lci5uYW1lLnZpZXciLCJjdXN0b21lci50ZWwudmlldyIsInByb2R1Y3Quc2VhcmNoIiwicHJvZHVjdC52aWV3IiwicHJvZHVjdC5jcmVhdGUiLCJwcm9kdWN0LnVwZGF0ZSIsInByb2R1Y3QuZGVsZXRlIiwid2FsbGV0LmxvZ2luIiwicmV2aWV3LnZpZXciLCJyZXZpZXcudXBkYXRlIiwiY2hhdC5jdXN0b21lciIsInNob3AuZGlzYWJsZSJdLCJkZXZpY2UiOiJjYmM5ZTI1N2RjNzE3OTQ2YjQ0ZTk2MGMwMjIxZWRmOCIsImlzX3dlYWtfcHciOmZhbHNlLCJ1bmlxX2RldmljZSI6ImRkNTA0NzY5ODg0ZDhkNDdlZmM0NjZmNmEyYzY0NTdhIiwibG9naW5fbWV0aG9kIjpudWxsfQ.Ifhg1xWyTu22fsWHwMCIbU3gH9mId_ZzhPPJ17bxh0U';
        $link = 'https://web.giaohangtietkiem.vn/api/v1/package/package-detail?alias=' . $orderCode;
        $response = Http::withToken($token)
            ->get($link);
        // dd($link);
        $response = $response->json();
        if ($response['success']) {
            $data = $response['data'];
            $package = $data['Package'];
            $printLog = $data['PrintLog'];
            
            $deliveryLog = array_merge($data['CreateLog'], $data['PickLog'], $data['DeliverLog'], $data['PrintLog'], $data['OtherLog']);

            usort($deliveryLog, function ($a, $b) {
                return strtotime($b['created']) - strtotime($a['created']);
            });
        }

        if (count($package) > 0) {
            $result = [
                'package' => $package,
                'deliveryLog' => $deliveryLog,
            ];
            
            if (count($printLog) > 0 ) {
                $result['printLog'] = $printLog;
            }
        }

        return $result;
    }

    public function detailShippingOrder($id) {
        
        $ship = ShippingOrder::find($id);
        if(!$ship) {
            return back();
        }

        if ($ship['vendor_ship'] == 'GHTK') {
            $dataGHTK = $this->detailDataGHTK($ship['order_code']);
            if (!$dataGHTK) {
                return view('pages.noti.ghtk');
            }

            return view('pages.orders.shipping.detailGHTK')->with('data' , $dataGHTK);
        }
       
        // $orderCode  = $ship->order_code;

        // $endpointTracking   = "https://fe-online-gateway.ghn.vn/order-tracking/public-api/client/tracking-logs?order_code=" . $orderCode;
        // $endpointCall       = "https://fe-online-gateway.ghn.vn/order-tracking/public-api/client/call-logs?order_code=" . $orderCode;
        // $orderLog           = $this->getShippingLog($endpointTracking);
        // $callLogs           = $this->getShippingLog($endpointCall);
        // $orderInfo          = $orderLog->order_info;
        // $trackingLogs       = $orderLog->tracking_logs;
        return view('pages.orders.detailshipping')->with('ship' , $ship);
        // if ($trackingLogs) {
        //     $str = '';

        //     $reversedKeys = array_reverse(array_keys($trackingLogs));
        //     $date = $trackingLogs[$reversedKeys[0]]->action_at;
        //     $dateToTime = strtotime($date);
        //     $dateNewFormat = date('d', $dateToTime);
        //     // dd ($dateNewFormat);
        //     $str .= "<div class=\"table-row first\">"
        //     . "<div class=\"table-col block-center-between\" aria-controls=\"collapse-text0\" aria-expanded=\"true\"><span>" . Helper::getDaysOfWek($date) . ', Ngày' . Helper::getDateFromStringGHN($date). "</span></div>"
        //     . "<div class=\"table-col mobile-hidden\">Chi tiết</div>"
        //     . "<div class=\"table-col mobile-hidden\">Thời gian</div>"
        //     . "</div>";
        //     foreach ($reversedKeys as $key) {
        //         $dateLogToTime = strtotime($trackingLogs[$key]->action_at);
        //         $dateLogNewFormat = date('d', $dateLogToTime);

        //         // dd( $dateLogNewFormat);
        //         if ($dateNewFormat !=  $dateLogNewFormat) {
        //             $str .= "<div class=\"table-row first\">"
        //                 . "<div class=\"table-col block-center-between\" aria-controls=\"collapse-text0\" aria-expanded=\"true\"><span>" 
        //                 . Helper::getDaysOfWek($trackingLogs[$key]->action_at) . ', Ngày ' . Helper::getDateFromStringGHN($trackingLogs[$key]->action_at). "</span></div>"
        //                 . "<div class=\"table-col mobile-hidden\">Chi tiết</div>"
        //                 . "<div class=\"table-col mobile-hidden\">Thời gian</div>"
        //                 . "</div>";

        //             $dateNewFormat = $dateLogNewFormat;
        //         } else {
        //             $atTime = date('H:s', strtotime($trackingLogs[$key]->action_at));
        //             $str .= "<div id=\"collapse-text0\" class=\"collapse show\">"
        //                 . "<div class=\"table-log-item\">"
        //                 . "   <div class=\"table-row block-align-top\">"
        //                 . "        <div class=\"table-col \">". $trackingLogs[$key]->status_name ."</div>"
        //                 . "        <div class=\"table-col\">"
        //                 . "            <div>". $trackingLogs[$key]->location->address ."</div>"
        //                 . "        </div>"
        //                 . "        <div class=\"table-col\">". $atTime ."</div>"
        //                 . "    </div>"
                        
        //                 . "</div>"
        //                 . "</div>";
        //         }
        //     }
        //     // dd($str);
           
        //     // return view('pages.orders.detailshipping')->with('data', $data)->with('type', $ship->vendor_ship)->with('strLogs', $str); 
        //     return $view->with('strLogs', $str);
        // }

        return redirect()->route('home');
    }
        
}
