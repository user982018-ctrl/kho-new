<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AddressController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Helpers\HelperProduct;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\SaleCare;
use App\Models\SrcPage;
use Faker\Core\File;
use Faker\Provider\File as ProviderFile;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    const bearTokenGHTK = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzaG9wX2NvZGUiOiJTMjExNzg4NDMiLCJzaG9wX2lkIjoiNjIyODc1ZTktNjMyMC00ZTlhLTljY2MtNGJlYzBhNmU0ZDU5Iiwic2hvcF9vcmRlciI6MjExNzg4NDMsInN0YWZmX2lkIjoxNzY2Nzg0LCJzb3VyY2UiOiJwbGF0Zm9ybSIsInJvbGUiOiJhZG1pbiIsInNob3Bfc3RhdHVzX2lkIjoxLCJzaG9wX3R5cGUiOjEsImFjY2Vzc190b2tlbiI6ImU1MTU5MzNjLWM0YzMtNDJkYi1hNTE2LWRiZTQ3MGVmMmYwNCIsImp3dCI6bnVsbCwiaW52YWxpZF9hdCI6eyJkYXRlIjoiMjAyNS0xMi0wMyAxMDoxNTo0MC4yNDIyODIiLCJ0aW1lem9uZV90eXBlIjozLCJ0aW1lem9uZSI6IkFzaWFcL0hvX0NoaV9NaW5oIn0sImxvZ2luX2FzX2lkIjpudWxsLCJsb2dpbl9hc19zZXNzaW9uX2lkIjpudWxsLCJsb2dpbl9hc190eXBlIjpudWxsLCJzZXNzaW9uIjpudWxsLCJtb3Nob3BfdXNlcl9pZCI6bnVsbCwic2hvcF90b2tlbiI6ImJkMTkzRTkyMGI3RTQwM2U4ZDVFNUI3Qzk5YkFiQWJjY2MyMjQzQ2YiLCJjcmVhdGVkX2F0Ijp7ImRhdGUiOiIyMDI1LTExLTAzIDEwOjE1OjQwLjIzOTU5MiIsInRpbWV6b25lX3R5cGUiOjMsInRpbWV6b25lIjoiQXNpYVwvSG9fQ2hpX01pbmgifSwic2NvcGVzIjpbInNob3AudmlldyIsInNob3AudGVsLnZpZXcuIiwic2hvcC5lbWFpbC52aWV3Iiwic2hvcC5pZF9jYXJkLnZpZXciLCJzaG9wLnBpY2tfYWRkcmVzc2VzLnZpZXciLCJzaG9wLmJhbmtfYWNjb3VudC52aWV3Iiwic2hvcC51cGRhdGUiLCJzaG9wLmJhc2ljX2luZm8udXBkYXRlIiwic2hvcC5hdmF0YXIudXBkYXRlIiwic2hvcC5waWNrX2FkZHJlc3Nlcy51cGRhdGUiLCJzaG9wLnRlbC51cGRhdGUiLCJzaG9wLmVtYWlsLnVwZGF0ZSIsInNob3AuYmFua19hY2NvdW50LnVwZGF0ZSIsInNob3AuaWRfY2FyZC51cGRhdGUiLCJzaG9wLnN0YWZmLnZpZXciLCJzaG9wLnN0YWZmLmNyZWF0ZSIsInNob3Auc3RhZmYudXBkYXRlIiwic2hvcC5zdGFmZi5kZWxldGUiLCJzaG9wLmJyYW5jaC52aWV3Iiwic2hvcC5icmFuY2gubGlzdCIsInNob3AuYnJhbmNoLmNyZWF0ZSIsInNob3AuYnJhbmNoLnVwZGF0ZSIsInNob3AuYnJhbmNoLmRlbGV0ZSIsImNvbmZpZy5hcGlfdG9rZW4udmlldyIsImNvbmZpZy5hcGlfdG9rZW4ucmVxdWVzdCIsImNvbmZpZy5zeXN0ZW0udXBkYXRlIiwiY29uZmlnLmF1ZGl0X3RpbWUudmlldyIsImNvbmZpZy5hdWRpdF90aW1lLnVwZGF0ZSIsImNvbmZpZy5zaG9wLnVwZGF0ZSIsInNob3AuZGFzaGJvYXJkIiwic2hvcC5yZXBvcnQubW9uZXlfZmxvdyIsInNob3AucmVwb3J0LmRhaWx5LnZpZXciLCJzaG9wLnJlcG9ydC5kYWlseS5kb3dubG9hZCIsIm9yZGVyLmxpc3QiLCJvcmRlci5leHBvcnRfZmlsZSIsIm9yZGVyLmRldGFpbCIsIm9yZGVyLmNyZWF0ZSIsIm9yZGVyLmV4Y2hhbmdlLmNyZWF0ZSIsIm9yZGVyLmRlbGl2ZXJ5LmNyZWF0ZSIsIm9yZGVyLnVwZGF0ZSIsIm9yZGVyLnJlcXVlc3RfY2FuY2VsIiwib3JkZXIucHJpbnQiLCJvcmRlci5kcmFmdC52aWV3Iiwib3JkZXIuZHJhZnQubGlzdCIsIm9yZGVyLmRyYWZ0LmNyZWF0ZSIsIm9yZGVyLmRyYWZ0LnVwZGF0ZSIsIm9yZGVyLmRyYWZ0LmRlbGV0ZSIsInRpY2tldC5hZGQiLCJ0aWNrZXQub3JkZXIucGlja190ZWwudXBkYXRlIiwidGlja2V0Lm9yZGVyLnBpY2tfYWRkcmVzcy51cGRhdGUiLCJ0aWNrZXQub3JkZXIuY3VzdG9tZXJfdGVsLnVwZGF0ZSIsInRpY2tldC5vcmRlci5jdXN0b21lcl9hZGRyZXNzLnVwZGF0ZSIsInRpY2tldC5vcmRlci5waWNrX21vbmV5LnVwZGF0ZSIsImN1c3RvbWVyLnZpZXciLCJjdXN0b21lci51cGRhdGUiLCJjdXN0b21lci5uYW1lLnZpZXciLCJjdXN0b21lci50ZWwudmlldyIsInByb2R1Y3Quc2VhcmNoIiwicHJvZHVjdC52aWV3IiwicHJvZHVjdC5jcmVhdGUiLCJwcm9kdWN0LnVwZGF0ZSIsInByb2R1Y3QuZGVsZXRlIiwid2FsbGV0LmxvZ2luIiwicmV2aWV3LnZpZXciLCJyZXZpZXcudXBkYXRlIiwiY2hhdC5jdXN0b21lciIsInNob3AuZGlzYWJsZSJdLCJkZXZpY2UiOiJjYmM5ZTI1N2RjNzE3OTQ2YjQ0ZTk2MGMwMjIxZWRmOCIsImlzX3dlYWtfcHciOmZhbHNlLCJ1bmlxX2RldmljZSI6ImRkNTA0NzY5ODg0ZDhkNDdlZmM0NjZmNmEyYzY0NTdhIiwibG9naW5fbWV0aG9kIjpudWxsfQ.mxIyUkfcxJbzf0eMtAX9M9p91xNLoOs_1V-jiWWFXi4';
    
    public function printOrderByOrderCodeVTPost($orderCode)
    {
        try {
            $token = 'eyJhbGciOiJFUzI1NiJ9.eyJzdWIiOiIwODI3NTc2NTY2IiwiU1NPSWQiOiIxLTA2OTdkNDVlLWZmZjgtNDFiYS05ZGZiLTkwZjc3YTBjZjQ4OCIsImludGVybmFsIjpmYWxzZSwiVXNlcklkIjoxNjk4MjMwNiwiRnJvbVNvdXJjZSI6MywiVG9rZW4iOiJCQzA1RjE4QUUzOUFCMDRGOEQ4QTkwRjQzRDNFQzVDNiIsInNlc3Npb25JZCI6IjEwMkEwRUI4RDBBODg3QkMzQzk4QzcyRkRFM0Q2MUMxIiwiZXhwIjoxNzYwOTQ1MTc1LCJsc3RDaGlsZHJlbiI6IiIsIlBhcnRuZXIiOjAsImRldmljZUlkIjoibHAzdGRscnRpYm12bGg0a2M1NmZsIiwidmVyc2lvbiI6MX0.oNCwZxzeB1pK7TM4c_2YTD7QUNGXHIhAZROM4h4sns9lRVpv5TDa9LU3xXo6ixhKDfTnzZhUxaoDMByfTF62tw';
            
            // API ViettelPost để in đơn hàng
            $endpoint = "https://partner.viettelpost.vn/v2/order/createOrderPDF";
            
            $data = [
                "ORDER_ARRAY" => [$orderCode],
                "TYPE" => 1  // 1: In 80x80, 2: In A5
            ];
            
            $response = Http::withHeaders([
                'Token' => $token,
                'Content-Type' => 'application/json'
            ])->post($endpoint, $data);

            if ($response->status() == 200) {
                $responseData = $response->json();
                
                if (isset($responseData['status']) && $responseData['status'] == 200) {
                    // Redirect đến URL PDF của ViettelPost
                    if (isset($responseData['data']['URL'])) {
                        return redirect($responseData['data']['URL']);
                    }
                    
                    // Hoặc hiển thị PDF trực tiếp
                    if (isset($responseData['data']['PDF'])) {
                        $pdf = base64_decode($responseData['data']['PDF']);
                        return response($pdf, 200)
                            ->header('Content-Type', 'application/pdf')
                            ->header('Content-Disposition', 'inline; filename="' . $orderCode . '.pdf"');
                    }
                }
            }
            
            notify()->error('Không thể tạo phiếu in ViettelPost!', 'Lỗi!');
            return back();
            
        } catch (\Exception $e) {
            notify()->error('Lỗi: ' . $e->getMessage(), 'Lỗi!');
            return back();
        }
    }
    
    public function getDataPrintOrderVTPost($orderCode)
    {
        // Deprecated - Sử dụng printOrderByOrderCodeVTPost thay thế
        return [];
    }

    /**
     * In tất cả đơn hàng ViettelPost đã chọn
     */
    public function printOrderByOrderAllVTPost(Request $r)
    {
        try {
            $listJson = $r->q;
            $list = json_decode($listJson, true);
            $orderCodes = [];
            
            if ($list && count($list) > 0) {
                foreach ($list as $orderId) {
                    $order = Orders::find($orderId);
                    if ($order) {
                        $shippingOrder = $order->shippingOrder()->first();
                        if ($shippingOrder && $shippingOrder->vendor_ship == 'VTPOST') {
                            $orderCodes[] = $shippingOrder->order_code;
                        }
                    }
                }
            }

            if (empty($orderCodes)) {
                notify()->error('Không có đơn hàng ViettelPost nào để in!', 'Lỗi!');
                return back();
            }

            $token = 'eyJhbGciOiJFUzI1NiJ9.eyJzdWIiOiIwODI3NTc2NTY2IiwiU1NPSWQiOiIxLTA2OTdkNDVlLWZmZjgtNDFiYS05ZGZiLTkwZjc3YTBjZjQ4OCIsImludGVybmFsIjpmYWxzZSwiVXNlcklkIjoxNjk4MjMwNiwiRnJvbVNvdXJjZSI6MywiVG9rZW4iOiJCQzA1RjE4QUUzOUFCMDRGOEQ4QTkwRjQzRDNFQzVDNiIsInNlc3Npb25JZCI6IjEwMkEwRUI4RDBBODg3QkMzQzk4QzcyRkRFM0Q2MUMxIiwiZXhwIjoxNzYwOTQ1MTc1LCJsc3RDaGlsZHJlbiI6IiIsIlBhcnRuZXIiOjAsImRldmljZUlkIjoibHAzdGRscnRpYm12bGg0a2M1NmZsIiwidmVyc2lvbiI6MX0.oNCwZxzeB1pK7TM4c_2YTD7QUNGXHIhAZROM4h4sns9lRVpv5TDa9LU3xXo6ixhKDfTnzZhUxaoDMByfTF62tw';
            
            $endpoint = "https://partner.viettelpost.vn/v2/order/createOrderPDF";
            
            $data = [
                "ORDER_ARRAY" => $orderCodes,
                "TYPE" => 1  // 1: In 80x80, 2: In A5
            ];
            
            $response = Http::withHeaders([
                'Token' => $token,
                'Content-Type' => 'application/json'
            ])->post($endpoint, $data);

            if ($response->status() == 200) {
                $responseData = $response->json();
                
                if (isset($responseData['status']) && $responseData['status'] == 200) {
                    // Redirect đến URL PDF của ViettelPost
                    if (isset($responseData['data']['URL'])) {
                        return redirect($responseData['data']['URL']);
                    }
                    
                    // Hoặc hiển thị PDF trực tiếp
                    if (isset($responseData['data']['PDF'])) {
                        $pdf = base64_decode($responseData['data']['PDF']);
                        return response($pdf, 200)
                            ->header('Content-Type', 'application/pdf')
                            ->header('Content-Disposition', 'inline; filename="viettelpost_orders.pdf"');
                    }
                }
            }
            
            notify()->error('Không thể tạo phiếu in ViettelPost!', 'Lỗi!');
            return back();
            
        } catch (\Exception $e) {
            notify()->error('Lỗi: ' . $e->getMessage(), 'Lỗi!');
            return back();
        }
    }
    
    public function backOrder($id)
    {
        $order = Orders::find($id);
        if ($order && $order->status == 0) {
            $order->status = 1;
            $order->save();
            return redirect()->route('update-order', $id)->with('success', 'Đơn hàng đã quay lại thành công!');
        }
    }
    public function cancelOrder($id)
    {
        $order = Orders::find($id);
        if ($order) {
            $order->status = 0;
            $order->save();
            return redirect()->route('update-order', $id)->with('success', 'Đơn hàng đã hủy thành công!');
        } else {
            return redirect()->route('update-order', $id)->with('error', 'Đơn hàng không tồn tại!');
        }
    }
    /**
     * GHTK input list ordercode => output pdf tổng
     * GHN for cho từng ordercode
     */
    public function printOrderGHTK(Request $r)
    {
        $listCodeStr = "";
        $listJson = $r->q;
        if ($listJson != '') {
            $listOrderCode = json_decode($listJson);
            $listCodeStr = implode(",", $listOrderCode);
        }

        return $this->getDataPrintOrderGHTK($listCodeStr);
    }

    public function printOrderGHN(Request $r)
    {
        $result = $listPackage = [];
        $listJson = $r->q;
        $list = json_decode($listJson, true);
        if ($list && count($list) > 0) {
            foreach ($list as $orderCode ) {
                $result[] = $this->getDataPrintOrder($orderCode, 'GHN');
            }
        }

        if (!$result) {
            return redirect()->route('home');
        }
        
        return view('pages.orders.print.ghn')->with('list', $result);

    }

    public function printOrderByOrderAll(Request $r)
    {
        $result = $listPackage = [];
        $listJson = $r->q;
        $list = json_decode($listJson, true);
        if ($list && count($list) > 0) {
            foreach ($list as $orderId ) {
                $order = Orders::find($orderId);
                if ($order) {
                    $shippingOrder    = $order->shippingOrder()->get()->first();
                    $orderCode        = $shippingOrder->order_code;
                    if (!$orderCode) {
                        continue;
                    }

                    $vendorShip = $shippingOrder->vendor_ship;
                    if (!$vendorShip) {
                        continue;
                    }

                    if (isset($listPackage[$vendorShip])) {
                        $listPackage[$vendorShip][] = $orderCode;
                    } else {
                        $listPackage[$vendorShip][] = $orderCode;
                    }

                    // $result[] = $this->getDataPrintOrder($orderCode, $vendorShip);
                }
            }
        }

        if (!$listPackage) {
            return redirect()->route('home');
        }

        return view('pages.orders.print.index')->with('list', $listPackage);
    }

    public function getDataPrintOrderGHN($orderCode) 
    {
        $data = [];
        $token = '180d1134-e9fa-11ee-8529-6a2e06bbae55';
        $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail?order_code=$orderCode";
        $response = Http::timeout(30)->withHeaders(['token' => $token])->get($endpoint);

        if ($response->status() == 200) {
            $content = json_decode($response->body(), true);
            $data = $content["data"];
            
            if (count($data["items"]) == 0) {
                return false;
            }    
            /** cập nhật trạng thái đã in vào đơn hàng của usu */
            $this->updatePrintStatus($orderCode, 'GHN');
        }

        return $data;
    }

    public function getDataPrintOrderGHTK($orderCode) 
    {
        $params = ['pkg_orders' => $orderCode];
        $html = $cipher = "";
        $bearToken = self::bearTokenGHTK;
        $link = "https://web.giaohangtietkiem.vn/api/v2/package/pkg-print-encrypt?pkg_orders=$orderCode";
        $response = Http::withToken($bearToken)->withBody(json_encode($params))->post($link);

        if ($response->status() == 200) {
            $dataApiJson = $response->body();
            $dataAPi = json_decode($dataApiJson, true);
            if (isset($dataAPi["data"]) && isset($dataAPi["data"]["cipher"])) {
                $cipher = $dataAPi["data"]["cipher"];
            }

            $linkPrintGHTK = "https://print-service.ghtk.vn/print-order?shop_code=S21178843&cipher=";
            
            $response = Http::get($linkPrintGHTK . $cipher);
            $html = $response->body();
            // $data = file_get_contents($linkPrintGHTK . $cipher);
            // Sửa đường dẫn CSS
            $html = str_replace(
                'href="/_next/static/css/',
                'href="https://print-service.ghtk.vn/_next/static/css/',
                $html
            );

            // $html = str_replace(
            //     '7eaca4802729a7c9',
            //     '_',
            //     $html
            // );
 
            /** cập nhật trạng thái đã in vào đơn hàng của usu */
            $array = explode(",", $orderCode);
            foreach ($array as $val) {
                $this->updatePrintStatus($val, 'GHTK');
            }
            
            return view('pages.orders.print.ghtk')->with('html', $html);
        } else {
            return view('pages.noti.ghtk');
        }
    }

    public function getDataPrintOrder($orderCode, $vendor)
    {
        if ($vendor == 'GHN') {
            return $this->getDataPrintOrderGHN($orderCode);
        } else if ($vendor == 'GHTK') {
            return $this->getDataPrintOrderGHTK($orderCode);
        }

        return false;
    }

    public function printOrderByOrderCodeGHTK($orderCode)
    {
        $params = ['pkg_orders' => $orderCode];
        $html = $cipher = "";
        $bearToken = self::bearTokenGHTK;
        $link = "https://web.giaohangtietkiem.vn/api/v2/package/pkg-print-encrypt?pkg_orders=$orderCode";
        $response = Http::withToken($bearToken)->withBody(json_encode($params))->post($link);

        if ($response->status() == 200) {
            $dataApiJson = $response->body();
            $dataAPi = json_decode($dataApiJson, true);
            if (isset($dataAPi["data"]) && isset($dataAPi["data"]["cipher"])) {
                $cipher = $dataAPi["data"]["cipher"];
            }

            $linkPrintGHTK = "https://print-service.ghtk.vn/print-order?shop_code=S21178843&cipher=";
            
            $response = Http::get($linkPrintGHTK . $cipher);
            $html = $response->body();
            // $data = file_get_contents($linkPrintGHTK . $cipher);
            // Sửa đường dẫn CSS
            $html = str_replace(
                'href="/_next/static/css/',
                'href="https://print-service.ghtk.vn/_next/static/css/',
                $html
            );

            // $html = str_replace(
            //     '7eaca4802729a7c9',
            //     '_',
            //     $html
            // );
 
            /** cập nhật trạng thái đã in vào đơn hàng của usu */
            $this->updatePrintStatus($orderCode, 'GHTK');

            return view('pages.orders.print.ghtk')->with('html', $html);
        } else {
            return view('pages.noti.ghtk');
        }
    }

    public function printOrderByOrderCodeGHN($orderCode)
    {
        $result = [];
        $token = '180d1134-e9fa-11ee-8529-6a2e06bbae55';
        $endpoint = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail?order_code=$orderCode";
        $response = Http::timeout(30)->withHeaders(['token' => $token])->get($endpoint);

        if ($response->status() == 200) {
            $content = json_decode($response->body(), true);
            
            if (count($content["data"]["items"]) == 0) {
                notify()->error('Đã xảy ra lỗi!', 'Thất bại!');
                return redirect()->back();
            }
    
            $result[] = $content["data"];

            /** cập nhật trạng thái đã in vào đơn hàng của usu */
            $this->updatePrintStatus($orderCode, 'GHN');
        }

        return view('pages.orders.shipping.print')->with('list', $result);
    }

    public function updatePrintStatus($orderCode, $vendor, $checkCron = false)
    {
        $order = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
            ->where('shipping_order.order_code', $orderCode)
            ->where('shipping_order.vendor_ship', $vendor)
            ->select('orders.*')->first();
        if ($order) {
            $shippingOrder = $order->shippingOrder()->get()->first();
            $shippingOrder->print_status = 1;
            $shippingOrder->check_cron = $checkCron;
            $shippingOrder->save();
        }
    }

    public function reportProductByOrder(Request $req)
    {
        $rs = [];
       
        if ($req->search) {
            $listCart = Orders::select('orders.*')
                ->where('orders.name', 'like', '%' . $req->search . '%')
                ->orWhere('orders.phone', 'like', '%' . $req->search . '%')
                ->orderBy('orders.id', 'desc');

            if ($listCart->count() == 0) {
                $listCart = Orders::select('orders.*')->join('shipping_order', 'shipping_order.order_id','=', 'orders.id')
                ->where('shipping_order.order_code', 'like', '%' . $req->search . '%')
                ->orderBy('orders.id', 'desc');
            }
             
        } else {
            $listCart = $this->getListOrderByPermisson(Auth::user(), $req);
             // ->pluck('id_product')
            // ->toArray();
        }   

        foreach ($listCart->get() as $cart) {
            $cartArr = json_decode($cart->id_product, true);
            if (!$cartArr) {
                continue;
            }

            foreach ($cartArr as $item) {
                if ($item['id'] == 83 ) {
                    if (isset($item['variantId']) && $item['variantId'] == 0) {
                        $item['variantId'] = 3;
                    }

                    try {
                        if (isset($item['variantId']) && isset($rs[$item['id']][$item['variantId']])) {
                            $rs[$item['id']][$item['variantId']]['val'] += $item['val'];
                        } else {
                            $rs[$item['id']][$item['variantId']]['val'] = $item['val'];
                            $rs[$item['id']][$item['variantId']]['variantId'] = $item['variantId'];
                        }
                    } catch (\Exception $e) {
                        dd($item);
                        return $e;
                    }
                    
                } else {
                    try {
                        if (isset($rs[$item['id']])) {
                            $rs[$item['id']]['val'] += $item['val'];
                        } else {
                            $rs[$item['id']]['val'] = $item['val'];
                        }
                    } catch (\Exception $e) {
                        // dd($cart);
                        return $e;
                    }
                }
            }
        }
        
        $lastData = [];
        foreach ($rs as $k => $item) {
            if ($k == 83) {
                foreach ($item as $value) {
                    $tmp = [];
                    $tmp['name'] = trim(HelperProduct::getNameAttributeByVariantId($value['variantId']));
                    $tmp['qty'] = (int)$value['val'];
                    $lastData[] = $tmp;
                }
            } else {
                $tmp = [];
                $product = Helper::getNameProductById($k);
                $tmp['name'] = trim($product->name);
                $tmp['qty'] = (int)$item['val'];
                $lastData[] = $tmp;
            }
        }

        $lastData = json_encode($lastData);

        $today = date("d/m/Y", time());
        $p['daterange'] = [$today, $today];
        $category = Category::where('status', 1)->get();
        $products = Product::where('status', 1)->get();
        $data       = $this->getListOrderByPermisson(Auth::user(), $p);
        $sumProduct = $data->sum('qty');
        $totalOrder = $data->count();
        $list       = $data->paginate(50);
        $sales      = Helper::getListSale()->get();
        $prControler = new ProductController();
        $listAttribute = $prControler->getAttributesProduct();
        $listAttribute = json_encode($listAttribute);
        return view('pages.orders.reportProduct')->with('sales', $sales)->with('totalOrder', $totalOrder)->with('sumProduct', $sumProduct)
            ->with('list', $list)->with('category', $category)->with('products', $products)
            ->with('listAttribute', $listAttribute)->with('lastData', $lastData)
            ->with('search', $req->search);
    }

    public function getDetailProductsByIdOrder($order)
    {
        $result = '';
        foreach (json_decode($order->id_product) as $product)
        {
            $productModel = getProductByIdHelper($product->id);
            $price = $productModel->price;
            $name = $productModel->name;
            if ($productModel->type == 2 && !empty($product->variantId)) {
                $variantId = $product->variantId;
                $variant = HelperProduct::getProductVariantById($variantId);
                $price = $variant->price;
                $name .= HelperProduct::getNameAttributeByVariantId($variantId);  
            }

            $strGift = '';
            if (isset($product->gift) && $product->gift == 'true') {
                $strGift = ' <span style="font-style:italic;">(tặng) </span>';
            }

            if ($productModel) {
                $result .= '<p class="sp-p" style="text-overflow:ellipsis"><span class="ten-sp">' . $name . $strGift . ' </span>'
                    .'<span class="qty-span">' . $product->val . '</span></p>';
            }
        }

        return $result;
    }

    public function getOrderByIdSalecare(Request $req)
    {
        $result = '';
        $idSale = $req->id;
        $salecare = SaleCare::find($idSale);

        if ($salecare && $salecare->orderNew) {
            $order = $salecare->orderNew;
            foreach (json_decode($order->id_product) as $product)
            {
                $productModel = getProductByIdHelper($product->id);
                $price = $productModel->price;
                $name = $productModel->name;
                if ($productModel->type == 2 && !empty($product->variantId)) {
                    $variantId = $product->variantId;
                    $variant = HelperProduct::getProductVariantById($variantId);
                    $price = $variant->price;
                    $name .= HelperProduct::getNameAttributeByVariantId($variantId);  
                }

                $strGift = '';
                if (isset($product->gift) && $product->gift == 'true') {
                    $strGift = ' <span style="font-style:italic;">(tặng) </span>';
                }

                if ($productModel) {
                    $result .= '<tr><td><span class="ten-sp" style="text-overflow:ellipsis">' . $name . $strGift . '</span></td>'
                        .'<td class="text-center">&nbsp; x ' . $product->val . '&nbsp;</td><td class="text-right">' . number_format($price) . '</td></tr>';
                }
                
            }
        }

        return response()->json($result);
    }

    public function empty()
    {
        return view('pages.empty');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $req)
    {
        $checkAll = isFullAccess(Auth::user()->role);     
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $isKho = Helper::isKho(Auth::user());
        if (!$checkAll && !$isLeadSale && !$isKho) {
            return redirect()->route('home');
        }

        if (count($req->all())) {
            return $this->filterOrderByDate($req);
        }

        $today = date("d/m/Y", time());
        $p['daterange'] = [$today, $today];
        $category = Category::where('status', 1)->get();
        $products = Product::where('status', 1)->get();
        $groups     = Group::where('status', 1)->select('id', 'name')->get();
        $data       = $this->getListOrderByPermisson(Auth::user(), $p);
        $sumProduct = $data->sum('qty');
        $totalOrder = $data->count();
        $list       = $data->paginate(50);
        $sales      = Helper::getListSale()->get();
        $prControler = new ProductController();
        $listAttribute = $prControler->getAttributesProduct();
        $listAttribute = json_encode($listAttribute);
        return view('pages.orders.index')->with('sales', $sales)->with('totalOrder', $totalOrder)->with('sumProduct', $sumProduct)
        ->with('list', $list)->with('category', $category)->with('products', $products)->with('listAttribute', $listAttribute)
        ->with('groups', $groups);
    }

    public function getListOrderByPermisson($user, $dataFilter = null, $checkAll = false, $getJson = false) 
    {
        // Tối ưu: Chỉ rõ table name ngay từ đầu để tránh ambiguous khi có JOIN
        $list   = Orders::orderBy('orders.id', 'desc');
        
        // Tối ưu: Cache Auth::user() để tránh gọi nhiều lần
        $authUser = Auth::user();
        
        if ($dataFilter) {
            if (isset($dataFilter['daterange'])) {
                $time       = $dataFilter['daterange'];
                // Tối ưu: Sử dụng gettype() hoặc is_string() thay vì getType()
                if (is_string($time)) {
                    $time = explode("-", $time);
                }
                $timeBegin  = str_replace('/', '-', $time[0]);
                $timeEnd    = str_replace('/', '-', $time[1]);
                $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
                $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));
                // Tối ưu: Chỉ rõ table name để tránh ambiguous khi có JOIN
                $list->whereDate('orders.created_at', '>=', $dateBegin)
                    ->whereDate('orders.created_at', '<=', $dateEnd);
            } else {
                $timeBegin = $timeEnd = date("d-m-Y", time());
                $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
                $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));
                // Tối ưu: Chỉ rõ table name để tránh ambiguous khi có JOIN
                $list->whereDate('orders.created_at', '>=', $dateBegin)
                    ->whereDate('orders.created_at', '<=', $dateEnd);
            }

            if (isset($dataFilter['status'])) {
                // 0 1 2 3
                //4 Chờ vận đơn
                //5 Có vận đơn, đvvc chưa lấy
                $status = $dataFilter['status'];
                if ($status == 4) {
                    // Tối ưu: Fix bug - không gọi ->get() mà chỉ apply condition
                    $list->whereDoesntHave('shippingOrder');
                } else if ($status == 5) {
                    $list->whereStatus(1);
                    // Tối ưu: Fix bug - không gọi ->get() mà chỉ apply condition
                    $list->whereHas('shippingOrder');
                } else {
                    $list->whereStatus($status);
                }
            }

            if (isset($dataFilter['dvvc'])) {
                // Tối ưu: Join trực tiếp thay vì pluck rồi whereIn
                $list = $list->join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
                    ->where('shipping_order.vendor_ship', $dataFilter['dvvc'])
                    ->select('orders.*')
                    ->distinct();
            }

            if (isset($dataFilter['print_status'])) {
                // Tối ưu: Join trực tiếp thay vì pluck rồi whereIn
                // Nếu đã có join từ dvvc, chỉ thêm where condition
                if (!isset($dataFilter['dvvc'])) {
                    $list = $list->join('shipping_order', 'shipping_order.order_id', '=', 'orders.id');
                }
                $list = $list->where('shipping_order.print_status', $dataFilter['print_status'])
                    ->select('orders.*')
                    ->distinct();
            }

            if (isset($dataFilter['category'])) {
                // Tối ưu: Chỉ select id và id_product để giảm memory
                // Lưu ý: Cần select đúng table name nếu có join
                $orders = $list->select('orders.id', 'orders.id_product')->get();
                $ids = [];
                
                // Tối ưu: Batch load products để tránh N+1 query trong Helper::checkProductsOfCategory
                $productIds = [];
                foreach ($orders as $order) {
                    $products = json_decode($order->id_product);
                    if ($products) {
                        foreach ($products as $product) {
                            if (isset($product->id)) {
                                $productIds[] = $product->id;
                            }
                        }
                    }
                }
                
                // Load tất cả products một lần
                $productsMap = [];
                if (!empty($productIds)) {
                    $uniqueProductIds = array_unique($productIds);
                    $productsData = \App\Models\Product::whereIn('id', $uniqueProductIds)
                        ->select('id', 'category_id')
                        ->get();
                    foreach ($productsData as $p) {
                        $productsMap[$p->id] = $p->category_id;
                    }
                }
                
                // Check category với products đã load
                foreach ($orders as $order) {
                    $products = json_decode($order->id_product);
                    if ($products) {
                        foreach ($products as $product) {
                            if (isset($product->id) && isset($productsMap[$product->id])) {
                                if ($productsMap[$product->id] == $dataFilter['category']) {
                                    $ids[] = $order->id;
                                    break;
                                }
                            }
                        }
                    }
                }

                // Tối ưu: Giữ lại query builder gốc và chỉ filter bằng whereIn
                if (empty($ids)) {
                    // Nếu không có ID nào match, return empty query
                    $list = Orders::whereRaw('1 = 0');
                } else {
                    // Giữ lại tất cả conditions trước đó bằng cách filter theo IDs
                    $list = $list->whereIn('orders.id', $ids);
                }
            }

            if (isset($dataFilter['product'])) {
                // Tối ưu: Chỉ select id và id_product để giảm memory
                // Lưu ý: Cần select đúng table name nếu có join
                $orders = $list->select('orders.*')->get();
                $ids = [];
                
                // Tối ưu: Batch load variants và attributes để tránh N+1 query
                $variantIds = [];
                foreach ($orders as $order) {
                    $products = json_decode($order->id_product);
                    if ($products) {
                        foreach ($products as $product) {
                            if ($product->id == $dataFilter['product'] && isset($product->variantId) && $product->variantId !== 0) {
                                $variantIds[] = $product->variantId;
                            }
                        }
                    }
                }
                
                // Load tất cả variants và attributes một lần
                $variantsMap = [];
                if (!empty($variantIds)) {
                    $uniqueVariantIds = array_unique($variantIds);
                    $variants = \App\Models\ProductVariant::whereIn('id', $uniqueVariantIds)
                        ->with('attributeValues')
                        ->get();
                    foreach ($variants as $variant) {
                        $attributeIds = $variant->attributeValues->pluck('attribute_value_id')->toArray();
                        $variantsMap[$variant->id] = $attributeIds;
                    }
                }
               
                foreach ($orders as $order) {
                    $products = json_decode($order->id_product);
                    if ($products) {
                        foreach ($products as $product) {
                            if ($product->id == $dataFilter['product']) {
                                if ($product->id == 83 && isset($product->variantId) && $product->variantId !== 0) {
                                    if (!isset($variantsMap[$product->variantId])) {
                                        continue;
                                    }
                                    $listAttributeOfItem = $variantsMap[$product->variantId];
                                  
                                    if (isset($dataFilter['attr_1']) && isset($dataFilter['attr_2'])
                                        && in_array($dataFilter['attr_1'], $listAttributeOfItem) && in_array($dataFilter['attr_2'], $listAttributeOfItem)) {
                                        $ids[] = $order->id;
                                    } else if (isset($dataFilter['attr_1']) && !isset($dataFilter['attr_2']) && in_array($dataFilter['attr_1'], $listAttributeOfItem)) {
                                        $ids[] = $order->id;
                                    } else if (!isset($dataFilter['attr_1']) && isset($dataFilter['attr_2']) && in_array($dataFilter['attr_2'], $listAttributeOfItem)) {
                                        $ids[] = $order->id;
                                    } else if (!isset($dataFilter['attr_1']) && !isset($dataFilter['attr_2'])) {
                                        $ids[] = $order->id;
                                    }
                                    
                                } else {
                                    $ids[] = $order->id;
                                }
                                break;
                            }
                        }
                    }
                }

                // Tối ưu: Giữ lại query builder gốc và chỉ filter bằng whereIn
                if (empty($ids)) {
                    // Nếu không có ID nào match, return empty query
                    $list = Orders::whereRaw('1 = 0');
                } else {
                    // Giữ lại tất cả conditions trước đó bằng cách filter theo IDs
                    $list = $list->whereIn('orders.id', $ids);
                }
                
            }
        
            
            if (isset($dataFilter['group'])) {
                $group = Group::find($dataFilter['group']);
                if ($group) {
                    // Tối ưu: Join trực tiếp thay vì pluck 2 lần
                    // Kiểm tra xem đã có join với sale_care chưa bằng cách check bindings
                    $hasJoin = false;
                    try {
                        $sql = $list->toSql();
                        $hasJoin = strpos($sql, 'sale_care') !== false;
                    } catch (\Exception $e) {
                        // Nếu không thể get SQL, thử join anyway
                        $hasJoin = false;
                    }
                    
                    if (!$hasJoin) {
                        $list = $list->join('sale_care', 'orders.sale_care', '=', 'sale_care.id')
                            ->select('orders.*');
                    }
                    $list = $list->where('sale_care.group_id', $dataFilter['group'])
                        ->orderBy('orders.id', 'desc')
                        ->distinct();
                }
            }

            if (isset($dataFilter['groupUser'])) {
                $groupUS = GroupUser::find($dataFilter['groupUser']);
                if ($groupUS) {
                    $listSale = $groupUS->users;
                    // Tối ưu: Chỉ rõ table name để tránh ambiguous khi có JOIN
                    $list = $list->whereIn('orders.assign_user', $listSale->pluck('id')->toArray());
                }
            }

            if (isset($dataFilter['src'])) {
                // Tối ưu: Eager load saleCare để tránh N+1 query
                $orders = $list->with('saleCare')->get();
                $idTmps = [];
                $mktCtl = new MarketingController();
                
                foreach ($orders as $order) {
                    if ($order->saleCare) {
                        $srcPage = $mktCtl->getSrcPageFromSaleCare($order->saleCare);
                        if ($srcPage && $srcPage->id == $dataFilter['src']) {
                            $idTmps[] = $order->id;
                        }
                    }
                }

                // Tối ưu: Giữ lại query builder gốc và chỉ filter bằng whereIn
                if (empty($idTmps)) {
                    // Nếu không có ID nào match, return empty query
                    $list = Orders::whereRaw('1 = 0');
                } else {
                    // Giữ lại tất cả conditions trước đó bằng cách filter theo IDs
                    $list = $list->whereIn('orders.id', $idTmps);
                }
            } 

            if (isset($dataFilter['type_customer']) && $dataFilter['type_customer'] != -1) {
                // Tối ưu: Eager load saleCare để tránh N+1 query
                $orders = $list->with('saleCare')->get();
                $resultFilter = [];
                
                foreach ($orders as $k => $order) {
                    /** loại phần tử ko thoả khỏi list order */
                    //xử lý type 0,1,2 về 1,2 để so sánh với req->type_customer
                    $typeCutomer = 0;
                    if ($order->saleCare) {
                        $typeCutomer = $order->saleCare->old_customer;
                    }
                    
                    if ($typeCutomer == 2) {
                        /** check khách cũ/khách mới khi type = 2 (hotline) */
                        $typeCutomer = $this->getTypeOfOther($order->saleCare);
                    }

                    if ($typeCutomer == $dataFilter['type_customer']) {
                        $resultFilter[] = $order->id;
                    }
                }

                // Tối ưu: Giữ lại query builder gốc và chỉ filter bằng whereIn
                if (empty($resultFilter)) {
                    // Nếu không có ID nào match, return empty query
                    $list = Orders::whereRaw('1 = 0');
                } else {
                    // Giữ lại tất cả conditions trước đó bằng cách filter theo IDs
                    $list = $list->whereIn('orders.id', $resultFilter);
                }
            }

        }

        // Tối ưu: Sử dụng $authUser đã cache thay vì gọi Auth::user() lại
        if (!$checkAll) {
           $checkAll = isFullAccess($authUser->role);
        }
        
        $isLeadSale = Helper::isLeadSale($authUser->role);
        $isKho = Helper::isKho($authUser);
        if ((isset($dataFilter['sale']) && $dataFilter['sale'] != 999) && ($checkAll || $isLeadSale || $isKho)) {
            /** user đang login = full quyền và đang lọc 1 sale */
            // dd($list->get());
            // dd($dataFilter);
            // Tối ưu: Chỉ rõ table name để tránh ambiguous khi có JOIN
            $list = $list->where('orders.assign_user', $dataFilter['sale']);
        } else if ((!$checkAll || !$isLeadSale) && !$user->is_digital && $user->is_sale) {
            /** sale đag xem report của mình */
            // Tối ưu: Chỉ rõ table name để tránh ambiguous khi có JOIN
            $list = $list->where('orders.assign_user', $user->id);
        }

        return $list;
    }

    public function getTypeOfOther($saleCare)
    {
        $orderId = $saleCare->id_order_new;
        $phone = $saleCare->phone;
        $type = 0;
        $orders = Orders::where('phone', 'like', '%' . $phone . '%');

        foreach ($orders as $order) {
            if ($order->id != $orderId) {
                $type = 1;
                break;
            }
        }

        return $type;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function add()
    {

        $saleCareId = request()->get('saleCareId');
        $listProduct = $listSale = [];
        $saleCare = SaleCare::find($saleCareId);

        if ($saleCare) {
            if ($saleCare->id_order_new && $saleCare->id_order_new != 0) {
                return redirect()->route('update-order', $saleCare->id_order_new);
            }

            if ($group = $saleCare->group) {
                $products    = $group->products;

                foreach ($products as $item) {
                    $listProductIds[] = $item->id_product;
                }

                $listProduct = Product::whereIn('id', $listProductIds)->orderBy('orderBy', 'desc')->get();
            } else {
                //data TN cũ chưa có group => hiển thị toàn bộ list ban đầu
                $listProduct    = Helper::getListProductByPermisson(Auth::user()->role);
            }

            $isLeadSale = Helper::isLeadSale(Auth::user()->role);
            $checkAllAdmin = isFullAccess(Auth::user()->role);
            if ($checkAllAdmin) {
                $listSale      = Helper::getListSale()->get();
            } else if (!$checkAllAdmin && $isLeadSale) {
                $listSale = Helper::getListSaleOfLeaderGroup()->get();
            }
        }

        $listProvince = $this->getListProvince();

        if (!$listProduct) {
            return redirect()->route('empty');
        }

        return view('pages.orders.addOrUpdate')->with('listProduct', $listProduct)
            // ->with('provinces', $provinces)
            ->with('saleCareId', $saleCareId)
            ->with('listSale', $listSale)
            ->with('listProvince', $listProvince)
            ->with('saleCare', $saleCare);
    }

    public function getListProductByPermisson($roles) {
        $list       = Product::orderBy('id', 'desc')->where('status', '=', 1);
        $checkAll   = false;
        $listRole   = [];
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
            'district'   => 'required',
            'ward'       => 'required',
            'phone'     => 'required',
        ],[
            'name.required' => 'Nhập tên khách hàng',
            'price.required' => 'Nhập tổng tiền',
            'qty.required' => 'Nhập số lượng',
            'address.required' => 'Nhập địa chỉ',
            'district.required' => 'Chọn quận huyện',
            'ward.required' => 'Chọn xã phường',
            'phone.required' => 'Nhập số điện thoại',
            'qty.min' => 'Vui lòng chọn sản phẩm',
        ]);

        if ($validator->passes()) {
            if (isset($request->id)) {
                $order = Orders::find($request->id);
                $text = 'Cập nhật đơn hàng thành công.';

                $oldPro = json_decode($order->id_product);
                $newPro = $request->products;

                // dd($oldPro);
                foreach ($oldPro as $oldItem) {
                    $flag = false;
                    foreach ($newPro as $key => $newItem) {
                        if ($newItem['id'] == $oldItem->id) {
                            $flag = true;
                            unset($newPro[$key]);
                            break;
                        }
                    }
                    
                    if ($flag) {
                        $oldItem->val = (int)$newItem['val'] - (int)$oldItem->val;
                    } else {
                        $oldItem->val = -(int)$oldItem->val;
                    }
                }
              
                /** cập nhật số lượng khi old nhiều hơn new */
                foreach ($oldPro as $item) {
                    $product        = Product::find($item->id);
                    $product->qty   = (int)$product->qty - (int)$item->val;
                    $product->save();
                }

                /** cập nhật số lượng khi new nhiều hơn old: new đã trừ, còn lại chưa update  */
                foreach ($newPro as $item) {
                    $product        = Product::find($item['id']);
                    $product->qty   = (int)$product->qty - (int)$item['val'];
                    $product->save();
                }

            } else {
                $order = new Orders();
                $text = 'Tạo đơn hàng thành công.';

                $listProductName = $tProduct = '';

                foreach ($request->products as $item) {
                    if ($tProduct != '') {
                        $tProduct .= ', ';
                    }
                    $product        = Product::find($item['id']);
                    $tProduct       .= "\n$product->name: " . $item['val'];
                    $product->qty   = (int)$product->qty - (int)$item['val'];
    
                    if ($listProductName != "") {
                        $listProductName    .= ' + ';
                    }
                    $listProductName    .= $product->name;
                    $product->save();
                }
            }

            $order->id_product      = json_encode($request->products);
            $order->phone           = $request->phone;
            $order->address         = $request->address;
            $order->name            = $request->name;
            $order->sex             = $request->sex ?? 0;
            $order->total           = $request->price;
            $order->province        = $request->province;
            $order->district        = $request->district;
            $order->ward            = $request->ward;
            $order->qty             = $request->qty;
            
            $order->is_price_sale   = $request->isPriceSale;
            $order->note            = $request->note;
            $order->status          = $request->status;
            $order->sale_care       = $request->saleCareId;
            $order->assign_user     = $request->assignSale;

            $order->save();

            if (!isset($request->id)) {
                /**cập nhật mã đơn hàng được tạo vào record sale_care
                 * workflow hiện tại đơn tạo từ TN Sale => luôn tồn tại saleCare
                 * 
                 */
                $chatId = '-4286962864'; //khởi tạo nhóm Test
                $tokenGroupChat = '';
                $saleCare = SaleCare::find($order->sale_care);
                if ($saleCare) {
                    $saleCare->id_order_new = $order->id;
                    $saleCare->save();
                    $group = $saleCare->group;
                    if ($group) {
                        // dd($saleCare);
                        /** ko xoá group đã có saleCare => luôn tồn tại group */
                        $chatId = $group->tele_create_order;
                        $tokenGroupChat = $group->tele_bot_token;
                    } else {
                        $tokenGroupChat = '7127456973:AAGyw4O4p3B4Xe2YLFMHqPuthQRdexkEmeo';
                        $chatId = '-4167465219';
                    }

                    /** nếu sale là cskh gửi riêng thông báo về nhóm của cskh */
                    $saler = $saleCare->user;
                    if ($saler->is_CSKH && $group->tele_create_order_by_cskh) {
                        $chatId = $group->tele_create_order_by_cskh;
                    }
                }

                //gửi thông báo qua telegram
                if ($chatId != '' && $tokenGroupChat != '') {
                    $endpoint       = "https://api.telegram.org/bot$tokenGroupChat/sendMessage";
                    $client         = new \GuzzleHttp\Client();

                    $userAssign     = Helper::getUserByID($order->assign_user)->real_name;
                    // $nameUserOrder  = ($order->sex == 0 ? 'anh' : 'chị');
                    $notiText       = "\nĐơn mua: $order->qty sản phẩm: $tProduct \nTổng: " . number_format($order->total) . "đ miễn phí Ship."
                        . "\nGửi về địa chỉ: $order->name - $order->phone - $order->address";
                    
                    if ($order->note) {
                        $notiText . "\nLưu ý: $order->note";
                    }

                    if ($order->phone == '0973409613') {
                        $chatId = '-4280564587';
                    }

                    //tạo mới order
                    // try {
                    //     $client->request('GET', $endpoint, ['query' => [
                    //         'chat_id' => $chatId, 
                    //         'text' => $userAssign . ' ' . $text . $notiText,
                    //     ]]);
                    // } catch (\Exception $e) {
                    //     return $e;
                    // }
                }
                
            } else {
                //câp nhật order
                //chỉ áp dụng cho đơn phân bón
                $isFertilizer = Helper::checkFertilizer($order->id_product);

                //check đơn này đã có data chưa
                $issetOrder = Helper::checkOrderSaleCare($order->id);
                // status = 'hoàn tất', tạo data tác nghiệp sale

                // dd($order->status == 3 && $isFertilizer && !$issetOrder);
                if ($order->status == 3 && $isFertilizer && !$issetOrder) {

                    $pageName = $order->saleCare->page_name;
                    $pageId = $order->saleCare->page_id;
                    $pageLink = $order->saleCare->page_link;

                    $group = $order->saleCare->group;
                    $groupId = $group->id;
                    $chatId = $group->tele_cskh_data;

                    if ($group->is_share_data_cskh && $order->saleCare->old_customer != 1) {
                        $assgin_user = Helper::getAssignCskhByGroup($group, 'cskh')->id_user;
                    } else {
                        $assgin_user = $order->saleCare->assign_user;
                        $user = $order->saleCare->user;

                        //tài khoản đã khoá hoặc chặn nhận data => tìm sale khác trong nhóm
                        if (!$user->is_receive_data || !$user->status) {
                            $assgin_user = Helper::getAssignSaleByGroup($group, 'cskh')->id_user;
                        }
                    }

                    $typeCSKH = Helper::getTypeCSKH($order);
                    $sale = new SaleController();
                    $data = [
                        'id_order' => $order->id,
                        'sex' => $order->sex,
                        'name' => $order->name,
                        'phone' => $order->phone,
                        'address' => $order->address,
                        'assgin' => $assgin_user,
                        'page_name' => $pageName,
                        'page_id' => $pageId,
                        'page_link' => $pageLink,
                        'group_id' => $groupId,
                        'chat_id' => $chatId,
                        'type_TN' => $typeCSKH, 
                        'old_customer' => 1
                    ];
                    if ($order->saleCare->src_id) {
                        $data['src_id'] = $order->saleCare->src_id;
                    } else if ($order->saleCare->type != 'ladi') {
                        $pageSrc = SrcPage::where('id_page', $order->saleCare->page_id)->first();
                        if ($pageSrc) {
                            $data['src_id'] = $pageSrc->id;
                        }
                    }

                    // dd($data);
                    $request = new \Illuminate\Http\Request();
                    $request->replace($data);

                    $sale->save($request);
                }
            }

            if ($order->assign_user && $order->sale_care) {
                setDataTNLogHelper( $order->sale_care, 'Thao tác với đơn hàng');
            }

            $link = route('update-order', $order->id);
            return response()->json([
                'success' => $text,
                'link' => $link,
        ]);
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
            if ($saleCare = $order->saleCare) {
                
                if ($group = $saleCare->group) {
                    $products    = $group->products;
    
                    foreach ($products as $item) {
                        $listProduct[] = $item->product;
                    }
    
                } else {
                    //data TN cũ chưa có group => hiển thị toàn bộ list ban đầu
                    $listProduct    = Helper::getListProductByPermisson(Auth::user()->role)->get();
                }
            
                // $listProduct    =  Product::all();
                $listSale       = Helper::getListSale()->get();
                $listProvince = $this->getListProvince();
                $addressCtl = new AddressController();
                $listWard = $addressCtl->getListWardById($order->district);
                if (!$listProduct) {
                    return redirect()->route('empty');
                }
                return view('pages.orders.addOrUpdate')->with('order', $order)
                    ->with('listSale', $listSale)
                    ->with('listWard', $listWard)
                    ->with('listProvince', $listProvince)
                    ->with('listProduct', $listProduct);
            }
        } 

        return redirect()->route('empty');
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
            return redirect()->back()->with('success', 'Đơn hàng đã xoá thành công!');            
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
        $rawSearch = (string) $request->input('search', '');
        $searchTerm = trim($rawSearch);

        if ($searchTerm === '') {
            return redirect()->back();
        }

        $orderIdTerm = null;
        if (Str::contains($searchTerm, '#')) {
            $orderIdTerm = trim(Str::afterLast($searchTerm, '#'));
            $orderIdTerm = $orderIdTerm !== '' ? $orderIdTerm : null;
        }

        $escapedSearch = str_replace(['%', '_'], ['\%', '\_'], $searchTerm);
        $likeTerm = '%' . $escapedSearch . '%';

        $ordersQuery = Orders::query()
            ->select('orders.*')
            ->with('shippingOrder')
            ->where(function ($query) use ($likeTerm, $orderIdTerm) {
                $query->where(function ($inner) use ($likeTerm) {
                    $inner->where('orders.name', 'like', $likeTerm)
                        ->orWhere('orders.phone', 'like', $likeTerm);
                })
                ->orWhereHas('shippingOrder', function ($shippingQuery) use ($likeTerm) {
                    $shippingQuery->where('order_code', 'like', $likeTerm);
                });

                if ($orderIdTerm !== null) {
                    $query->orWhere('orders.id', 'like', '%' . $orderIdTerm . '%');
                }
            })
            ->orderByDesc('orders.id');

        $countQuery = clone $ordersQuery;
        $sumQuery = clone $ordersQuery;

        $list = $ordersQuery->paginate(50)->appends(['search' => $searchTerm]);
        $totalOrder = $countQuery->count();
        $sumProduct = $sumQuery->sum('orders.qty');

        $productController = new ProductController();
        $listAttribute = json_encode($productController->getAttributesProduct());
        $products = Product::where('status', 1)->get();

        return view('pages.orders.index', [
            'list' => $list,
            'search' => $searchTerm,
            'totalOrder' => $totalOrder,
            'sumProduct' => $sumProduct,
            'listAttribute' => $listAttribute,
            'products' => $products,
        ]);
    }

    public function getListSale() {
        return User::where('status', 1)->where('is_sale', 1)
            ->orWhere('is_cskh', 1);
    }

    public function createShipping($id) {
        return view('pages.orders.shipping'); 
    }

    public function view($id) {
        $order = Orders::find($id);

        // notify()->success('Gỡ vận đơn thành công', 'Thành công!');
        if($order){
            return view('pages.orders.detail')->with('order', $order); 
        } 
        return redirect('/don-hang') ->with('error', 'Đã xảy ra lỗi hoặc đơn hàng không tồn tại!');
    }

    public function filterOrderByDate(Request $req) 
    {
        if ($req->search) {
            return $this->search($req);
        }

        try {
            $data       = $this->getListOrderByPermisson(Auth::user(), $req);
            $totalOrder = $data->count();
          
            $sumProduct = $data->sum('qty');
            $category   = Category::where('status', 1)->get();
            $list       = $data->paginate(50);
            $sales      = Helper::getListSale()->get();
            $products = Product::where('status', 1)->get();
            $prControler = new ProductController();
            $listAttribute = $prControler->getAttributesProduct();
            $listAttribute = json_encode($listAttribute);
            $groups     = Group::where('status', 1)->select('id', 'name')->get();

            return view('pages.orders.index')->with('list', $list)->with('category', $category)
                ->with('sumProduct', $sumProduct)->with('sales', $sales)->with('totalOrder', $totalOrder)
                ->with('products', $products)->with('listAttribute', $listAttribute)->with('groups', $groups);
        } catch (\Exception $e) {
            // return $e;
            dd($e);
            return redirect()->route('home');
        }
    }

    /**
     * input:
     *  +84973409613
     *  84973409613
     *  0973409613
     *  973409613
     * 
     * output: 973409613
     */
    public function getCustomPhone9Num($phone)
    {
        $length = strlen($phone);
        $pos = $length - 9;
        return substr($phone, $pos);
    }
}