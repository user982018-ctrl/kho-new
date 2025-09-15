<?php
use App\Http\Controllers\AddressController;
use App\Http\Controllers\SaleCareCountActionController;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

if (! function_exists('setDataTNLogHelper')) {
    function setDataTNLogHelper($idSaleCare, $action) {
        $scDataCount = new SaleCareCountActionController();
        return  $scDataCount->setDataTNLog( $idSaleCare, $action);
    }
}

if (! function_exists('sayHi')) {
    function sayHi()
    {
       return "hi";
    }
}

if (! function_exists('getProvinceNameHelper')) {
    function getProvinceNameHelper($provinceId) {
        $result = "";
        $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province";
        $response = Http::withHeaders([
            'token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897',
        ])->post($endpoint);
    
        if ( $response->status() == 200) {
            $content    = json_decode($response->body());
            foreach ($content->data as $province) {
                if ($province->ProvinceID == $provinceId) {
                    $result = $province->ProvinceName;
                    break;
                }
            }
        }

        return $result;
    }
}

if (! function_exists('getDistrictNameHelper')) {
    function getDistrictNameHelper($districtId, $provinceId) {
        $result = "";
        $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district?province_id=" . $provinceId;
        $response = Http::withHeaders(['token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897'])->get($endpoint);

        // echo "<pre>";
        // print_r($response->status() );
        // die();
        if ( $response->status() == 200) {
            $content    = json_decode($response->body());
            foreach ($content->data as $district) {
                if ($district->DistrictID == $districtId) {
                    $result = $district->DistrictName;
                    break;
                }
            }
        }

        return $result;
    }
}

if (! function_exists('getWardNameHelper')) {
    function getWardNameHelper($wardId, $districtId) {
        $result = "";
        $endpoint = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=" . $districtId;
        $response = Http::withHeaders(['token' => 'c0ddcdf9-df81-11ee-b1d4-92b443b7a897'])->get($endpoint);
       
        if ( $response->status() == 200) {
            $content    = json_decode($response->body());
            foreach ($content->data as $ward) {
                if ($ward->WardCode == $wardId) {
                    $result = $ward->WardName;
                    break;
                }
            }
        }

        return $result;
    }
}

if (! function_exists('getSexHelper')) {
    function getSexHelper($sex) {
        return $sex == 0 ? 'Nam' : 'Nữ';
    }
}

if (! function_exists('getProductByIdHelper')) {
    function getProductByIdHelper($id) {
        return Product::find($id);
    }
}

if (! function_exists('isFullAccess')) {
    function isFullAccess($roles) {
        $checkAll   = false;
        $roles      = json_decode($roles);
        if ($roles) {
            foreach ($roles as $key => $value) {
                if ($value == 1) {
                  $checkAll = true;
                  break;
                }
            }
        }
        return $checkAll;
    }
}
