<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
class AddressController extends Controller
{
    public function getNameAddressSystem($districId, $wardId)
    {
        $json = file_get_contents(public_path('json/local.json'));
        $data = json_decode($json, true);
        $districtName = $wardName = "";

        foreach ($data as $kProvince => $item) {
            foreach ($item as $k => $v) {
                if ($k == 'District' || $k == 'districts') {
                    foreach ($v as $kDistric => $disctrict) {
                        if ($disctrict["id"] == $districId) {
                            $districtName = $disctrict["name"];
                            foreach ($disctrict['wards'] as $ward) {
                                if ($ward['id'] == $wardId) {
                                    $wardName = $ward['name'];
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        return [$districtName, $wardName];
    }

    public function getDistrictGHNByName($id)
    {
        $order = Orders::find($id);
        $listDistricGhn = $this->getListDistrictGHN();
        $nameAddress = $this->getNameAddressSystem($order->district, $order->ward);
        $nameDistrictSystem = $nameAddress[0];
        $nameWardSystem = $nameAddress[1];
        $idDistrictToGetWardsGHN = $idWardToGetWardsGHN = 0;
        $listWardGHN = [];

        if ($listDistricGhn) {
            foreach ($listDistricGhn as $distric) {
                if(strpos($distric['DistrictName'], $nameDistrictSystem) !== FALSE) {  
                    $idDistrictToGetWardsGHN = $distric['DistrictID'];
                    break;
                }
            }

            if ($idDistrictToGetWardsGHN > 0) {
                $listWardGHN = $this->getListWardGHNById($idDistrictToGetWardsGHN);
            }

            // dd($listWardGHN);
            return [
                'idDistrictToGetWardsGHN' => $idDistrictToGetWardsGHN,
                'idWardToGetWardsGHN' => $idWardToGetWardsGHN,
                'nameWardSystem' => $nameWardSystem,
                'nameDistrictSystem' => $nameDistrictSystem,
                'listWardGHN' => $listWardGHN,
                'listDistricGhn' => $listDistricGhn,
            ];
        } 
    }
    public function apiGetDistrictGHNByName(Request $req)
    {
        $data = $this->getDistrictGHNByName($req->id);
        return response()->json($data);
    }
    
    public function getWardByIdDicstricGHN(Request $req)
    {
        if(isset($req->id)){
            $result = $this->getListWardGHNById($req->id);
            return response()->json($result);
        }
    }

    public function getListWardGHNById($id)
    {
        $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=" . $id;
        $response = Http::withHeaders([
            'token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55',
        ])->get($endpoint);
  
        $wards  = [];
        if ( $response->status() == 200) {
            $content    = json_decode($response->body());
            $wards  = $content->data;
        }

        return $wards;
    }

    public function getListDistrictGHN()
    {
        // $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/master-data/district";
        // $response = Http::withHeaders([
        //     'token' => '180d1134-e9fa-11ee-8529-6a2e06bbae55',
        // ])->get($endpoint);
  
        // $districts  = [];
        // if ( $response->status() == 200) {
        //     $content    = json_decode($response->body());
        //     $districts  = $content->data;
        // }

        $result = [];
        $json = file_get_contents(public_path('json/district_ghn.json'));
        $listDistrictGHN = json_decode($json, true);

        $json = file_get_contents(public_path('json/province_ghn.json'));
        $listProvinceGHN = json_decode($json, true);

        foreach ($listDistrictGHN as $dis) {
            foreach ($listProvinceGHN as $pro) {
                if ($dis['ProvinceID'] == $pro['ProvinceID']) {
                    $dis['DistrictName'] = $dis['DistrictName'] . ' - ' . $pro['ProvinceName'];
                    $result[] = $dis;
                    continue;
                }
            }
        }

        return $result;
    }

    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return Response
    //  */
    // public function index()
    // {
    //     $list = Orders::orderBy('id', 'desc')->paginate(5);
    //     return view('pages.orders.index')->with('list', $list);
    // }

    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return Response
    //  */
    // public function add()
    // {
    //     $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province";
    //     $response = Http::withHeaders([
    //         'token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897',
    //     ])->post($endpoint);
  
    //     $statusCode = $response->status();
    //     $provinces  = [];
    //     if ( $response->status() == 200) {
    //         $content    = json_decode($response->body());
    //         $provinces  = $content->data;
    //     }

    //     $listProduct =  Product::all();

    //     return view('pages.orders.addOrUpdate')->with('listProduct', $listProduct)
    //         ->with('provinces', $provinces);
    // }

    
     /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWardByIdDicstric(Request $request)
    {
        if(isset($request->id)){
            $result = $this->getListWardById($request->id);
            return response()->json($result);
        }
    }

    public function getListProvince(){
        /** lấy danh sách quận cả nước */
        // $json = file_get_contents(public_path('json/simplified_json_generated_data_vn_units.json'));
        $json = file_get_contents(public_path('json/local.json'));
        $data = json_decode($json, true);

        $result  = [];
        foreach ($data as $kProvince => $item) {
            foreach ($item as $k => $v) {
                if ($k == 'District' || $k == 'districts') {

                    foreach ($v as $kDistric => $disctrict) {
                        $item[$k][$kDistric]['name'] .= ' - ' . $data[$kProvince]['name'];
                        // $item[$k][$kDistric]['FullName'] .= ' - ' . $data[$kProvince]['Name'];
                    }

                    $result = array_merge($result, $item[$k]);
                }
            }
        }

        return $result;
    }

    public function getListWardById($id)
    {
        $result = [];
        // $json = file_get_contents(public_path('json/simplified_json_generated_data_vn_units.json'));
        $json = file_get_contents(public_path('json/local.json'));
        $data = json_decode($json, true);
        
        foreach ($data as $item) {
            foreach ($item as $k => $v) {
                if ($k == 'District' || $k == 'districts') {
                    // dd($v);
                    foreach ($v as $distric) {
                        if ($distric['id'] == $id) {
                            $result = array_merge($result, $distric['wards']);
                        }
                    }
                }
            }
        }

        return $result;
    }

     /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDistrictById(Request $request)
    {
        if(isset($request->id)){
            // print ($request->id);
            $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district?province_id=" . $request->id;
            $response = Http::withHeaders(['token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897'])->get($endpoint);

            $district = [];
            if ($response->status() == 200) {
                $content   = json_decode($response->body());
                $district  = $content->data;
                return $district;
            }
        }
    }
    

    // /**
    //  * Display a listing of the myformPost.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function save(Request $request)
    // {
    //     $validator      = Validator::make($request->all(), [
    //         'name'      => 'required',
    //         'price'     => 'required',
    //         'qty'       => 'required|numeric|min:1',
    //         'address'   => 'required',
    //         // 'products'  => 'required',
    //         'sex'       => 'required',
    //         'phone'     => 'required',
    //     ],[
    //         'name.required' => 'Nhập tên khách hàng',
    //         'price.required' => 'Nhập tổng tiền',
    //         // 'price.numeric' => 'Chỉ được nhập số',
    //         'qty.required' => 'Nhập số lượng',
    //         // 'qty.numeric' => 'Chỉ được nhập số',
    //         'address.required' => 'Nhập địa chỉ',
    //         // 'products.required' => 'Chọn sản phẩm',
    //         'sex.required' => 'Chọn giới tính',
    //         'phone.required' => 'Nhập số lượng',
    //         'qty.min' => 'Vui lòng chọn sản phẩm',
    //     ]);
       
    //     if ($validator->passes()) {
    //         if(isset($request->id)){
    //             $order = Orders::find($request->id);
    //             $text = 'Cập nhật đơn hàng thành công.';
    //         } else {
    //             $order = new Orders();
    //             $text = 'Tạo đơn hàng thành công.';
    //         }
           
    //         $order->id_product  = $request->products;
    //         $order->phone       = $request->phone;
    //         $order->address     = $request->price;
    //         $order->name        = $request->name;
    //         $order->sex         = $request->sex;
    //         $order->total       = $request->price;
    //         $order->qty         = $request->qty;

            
    //         // echo "<pre>";
    //         // print_r($request->products);
    //         // echo "</pre>";
    //         $order->save();
    //         return response()->json(['success'=>$text]);
    //     }
     
    //     return response()->json(['errors'=>$validator->errors()]);
    // }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function viewUpdate($id)
    {
        // $product = Product::find($id);
        // $listCategory =  Category::all();
        // if($product){
        //     return view('pages.product.addOrUpdate')->with('product', $product)
        //         ->with('listCategory', $listCategory);
        // } 

        // return redirect('/');
      
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function delete($id)
    {
        // $product = Product::find($id);
        // if($product){
        //     $product->delete();
        //     return redirect('/danh-sach-san-pham')->with('success', 'Sản phẩm đã xoá thành công!');            
        // } 

        // return redirect('/danh-sach-san-pham') ->with('error', 'Đã xảy ra lỗi khi xoá sản phẩm!');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search(Request $request)
    {
        // $product = Product::where('name', 'like', '%' . $request->search . '%')->orderBy('id', 'desc')->paginate(5);
        // if($product){
        //     return view('pages.product.index')->with('list', $product);           
        // } 

        // return redirect('/');
    }

    public function setProducts(){
        // $list = Product::orderBy('id', 'desc')->paginate(5);

        // return view('pages.product.index')->with('list', $list);
    }

    public function setProductsByMonth(Request $request){
        // $month  = $request->month;
        // $list   = Product::orderBy('id', 'desc')
        //     ->whereMonth('created_at', '=', $month)
        //     ->paginate(5);

        // return view('pages.product.index')->with('list', $list);
    }

    public function setProductsByYear(Request $request){
        // $year  = $request->year;
        // $list   = Product::orderBy('id', 'desc')
        //     ->whereYear('created_at', '=', $year)
        //     ->paginate(5);

        // return view('pages.product.index')->with('list', $list);
    }

    public function getProvinceNameById($id) {
       
    }
    
}
