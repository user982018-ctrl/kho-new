<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AddressController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Helpers\Helper;
use App\Models\CallResult;
use App\Models\GroupUser;
use App\Models\SaleCare;
use App\Models\SrcPage;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function getDataMkt($req)
    {
        $list = SrcPage::query();
        if ($req->mkt_user && $req->mkt_user != -1) {
            $list = SrcPage::where('user_digital', $req->mkt_user);
        }

        if ($req->src && $req->src != -1) {
            $list = SrcPage::where('id', $req->src);
        }

         /*1 => array:4 [▼
            "contact" => 0
            "name" => "Organic Rice Siêu Rước Đòng - Dùng Là X3 Năng Suất"
            "type" => "pc"
            "id" => 1
        ]*/
        /** lấy data report(contact) từ list nguồn */
        $listFiltrSrc = $this->getListMktReportByListSrc($list, $req);
        dd($listFiltrSrc);
        $listFiltrSrc = $this->transferKey($listFiltrSrc);

        // dd($listFiltrSrc);
        $rs = $this->getListMktReportOrder($req, $listFiltrSrc);
        if ($rs) {
            $rs = $this->cleanDataMktReport($rs);
        }

        return $rs;
    }

    public function getDataMktV2($req)
    {
        $reqParams = $req->all();
        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role); 
        $checkAll = isFullAccess(Auth::user()->role);
        $str = '';
        if (isset($reqParams['daterange'])) {
            $time = $reqParams['daterange'];
            $str .= " WHERE sc.created_at BETWEEN '$time[0] 00:00:00' AND '$time[1] 23:59:59'";
        }

        if (isset($reqParams['mkt_user']) && ($isLeadDigital || $checkAll)) {
            $mkt = $reqParams['mkt_user'];
            $str .=  " AND src.user_digital = $mkt";
        } else if ($isLeadDigital && !$checkAll) {
            $listUser = Helper::getListMktUser(Auth::user());
            $ids = $listUser->pluck('id')->toArray();
            if ($ids) {
                $strIds = json_encode($ids);
                $strIds  = str_replace('[', '(', $strIds);
                $strIds  = str_replace(']', ')', $strIds);

                $str .= " AND src.user_digital IN $strIds";
            }
        } else if (!$checkAll) {
            $mkt = Auth::user()->id;
            $str .=  " AND src.user_digital = $mkt";
        }
        
        if (isset($reqParams['type_customer'])) {
            $type = $reqParams['type_customer'];
            $str .=  " AND sc.old_customer = $type";
        } if (isset($reqParams['src'])) {
            $src = $reqParams['src'];
            $str .=  " AND src.id = $src";
        }

        $sql = 
            "SELECT 
                src.name,
                COUNT( sc.id) AS contact,
                COUNT( o.id) AS count_order,
                ROUND(COUNT(DISTINCT o.id) * 100.0 / NULLIF(COUNT(DISTINCT sc.id), 0), 2) AS rate,
                SUM(o.total) AS total,
                SUM(o.qty) AS product,
                ROUND(SUM(o.total) / NULLIF(COUNT(DISTINCT o.id), 0), 2) AS avg
            FROM src_page src
            JOIN sale_care sc on sc.src_id = src.id
            LEFT JOIN orders o on o.sale_care = sc.id
            $str
            GROUP BY src.name
            ORDER BY total DESC";

        $result = DB::select($sql);
        return json_decode(json_encode($result), true);
    }

    public function getDataMktV3($req)
    {
        $reqParams = $req->all();
        $query = DB::table('src_page as src')
            ->join('sale_care as sc', 'sc.src_id', '=', 'src.id')
            ->leftJoin('orders as o', 'o.sale_care', '=', 'sc.id');

        if (isset($reqParams['daterange'])) {
            $time = $reqParams['daterange'];
            $query->whereBetween('sc.created_at', [$time[0], $time[1]]);
        }
        $results = $query
            ->select(
                'src.name',
                DB::raw('COUNT(sc.id) AS contact'),
                DB::raw('COUNT(o.id) AS count_order'),
                DB::raw('ROUND(COUNT(DISTINCT o.id) * 100.0 / NULLIF(COUNT(DISTINCT sc.id), 0), 2) AS rate'),
                DB::raw('SUM(o.total) AS total'),
                DB::raw('SUM(o.qty) AS product'),
                DB::raw('ROUND(SUM(o.total) / NULLIF(COUNT(DISTINCT o.id), 0), 2) AS avg')
            )
            ->groupBy('src.name')
            ->orderByDesc('total')
            ->get();
            // ->simplePaginate(10);
        // return $results;
            dd($results);
        // $result =    $results->toArray();
        return   json_decode(json_encode($results), true);
    }

    public function marketingSearchV2($req)
    {
        // $rs = $this->getDataMktV2($req);
        $rs = $this->getDataMkt($req);

        $listMktUser = Helper::getListMktUser(Auth::user());
        $listGroup = Helper::getListGroup();
        $listSrc = SrcPage::orderBy('id', 'desc')->get();
        if (!isFullAccess(Auth::user()->role)) {
            $listSrc = $listSrc->where('user_digital', $req->mkt_user);
        }
    
        return view('pages.marketing.index')->with('list', $rs)
            ->with('listMktUser', $listMktUser)
            ->with('listSrc', $listSrc)
            ->with('listGroup', $listGroup);
    }

    public function transferKey($data)
    {
        /* 
        [ 4 => [
                "contact" => 3
                "name" => "A Plus - Dinh Dưỡng Đậm Đặc Siêu Kích Hoạt 0986.987.791"
                "type" => "pc"
                "id" => 15
                ]
        ]
        =>   [ 15 => [
                "contact" => 3
                "name" => "A Plus - Dinh Dưỡng Đậm Đặc Siêu Kích Hoạt 0986.987.791"
                "type" => "pc"
                "id" => 15
                ]
        ]
        */

        // dd($data);
        $newData = [];
        foreach ($data as $key => $item) {
            $newData[$item['id']] = $item;
        }
        // dd($newData);
        return $newData;
    }

    public function cleanDataMktReport($data)
    {
        foreach ($data as $key => $item) {
            // if (isset($item['order']) && $item['order'] == 0 && $item['contact'] == 0) {
            //     unset($data[$key]);
            // } else if ($item['contact'] == 0) {
            // // if ($item['contact'] == 0) {
            //     unset($data[$key]);
            // }
            if ((isset($item['order']) && $item['order'] == 0 && $item['contact'] == 0)
                || ($item['contact'] == 0 && !isset($item['order']))){
                unset($data[$key]);
            }
        }

        return $data;
    }
    public function marketingSrcSearch(Request $req)
    {

        $list = SrcPage::orderBy('id', 'desc');

        if ($req->search) {
            $list = $list->where('name', 'like', '%' . $req->search . '%');
        }
        
        if (($req->mkt_user && $req->mkt_user != -1)) {
            $list = $list->where('user_digital', $req->mkt_user);
        } else if (!isFullAccess(Auth::user()->role)) {
            $list = $list->where('user_digital', Auth::user()->id);
        }
        
        if ($req->group) {
            $list = $list->where('id_group', $req->group);
        }

        $listMktUser = Helper::getListMktUser(Auth::user());   
        $listGroup = Helper::getListGroup();
        $list = $list->paginate(30);

        return view('pages.marketing.src.index')->with('list', $list)
            ->with('listMktUser', $listMktUser)
            ->with('listGroup', $listGroup);
    }

    public function getListMktReport()
    {
        $sumContact = $sumOrder = $sumRateSuccess = $sumProduct = $sumTotal = $sumAvg = 0;
        $list = SrcPage::orderBy('id', 'desc');
        $data = [];

        $toMonth = date("Y/m/d", time());
        // dd("$toMonth - $toMonth");
        $reqData = [ 'daterange' => "$toMonth - $toMonth"];
        $req = new \Illuminate\Http\Request();
        $req->replace($reqData);
       
        foreach ($list->get() as $item) {
            $dataReport = $this->getDataReportBySrcId($item, $req);
            $data[]= $dataReport;
        }

        if ($sumContact > 0) {
            $newRate = $sumOrder / $sumContact * 100;
            $sumRateSuccess = round($newRate, 2);
        }

        if ($sumOrder > 0) {
            $sumAvg = $sumTotal / $sumOrder;
        }

        $data['sum'] = [
            'contact' => $sumContact,
            'order' => $sumOrder,
            'rate' => $sumRateSuccess,
            'product' => $sumProduct,
            'total' => $sumTotal,
            'avg' => $sumAvg
        ];

        return $data;
    }

    public function getListMktReportOrder($req, $listSrc)
    {
        if (!$listSrc) {
            return;
        }

        $ordersController = new OrdersController();
        $userAdmin = User::find(1);
        $dataFilter['daterange'] = $req->daterange;

        $status = $req->status;
        if (($status || $status == 0) && $status != 999) {
            $dataFilter['status'] = $status;
        }

        $category = $req->category;
        if ($category && $category != 999) {
            $dataFilter['category'] = $category;
        }

        $product = $req->product;
        if ($req->product && $product != 999) {
            $dataFilter['product'] = $product;
        }

        $group = $req->group;
        if ($req->group && $group != 999) {
            $dataFilter['group'] = $group;
        }

        $groupUser = $req->groupUser ;
        if ($req->groupUser && $groupUser != 999) {
            $dataFilter['groupUser'] = $groupUser;
        }

        $type_customer = $req->type_customer;
        if (isset($type_customer) && $type_customer != 999 && $type_customer != -1) {
            $dataFilter['type_customer'] = $type_customer;
        }

        $listOrders = $ordersController->getListOrderByPermisson($userAdmin, $dataFilter);
        $listOrderKeySrc = [];
       
        foreach ($listOrders->get() as $order) {
            // dd($order->saleCare)
            // if ($order->id != 3231) {
            //     continue;
            // }

            if ($req->type_customer && $req->type_customer != -1 
                && $order->saleCare->old_customer != $req->type_customer) {
                continue;
            }

            if (!empty($order->saleCare)) {
                $srcPage = $this->getSrcPageFromSaleCare($order->saleCare);
                if ($srcPage) {
                    $listOrderKeySrc[$srcPage->id][] = [
                        'total' => $order->total,
                        'qty' => $order->qty,
                        'id' => $order->id,
                    ];
                }
            }
            
        }

        /** lọc loại khách hàng */
        if ($req->type_customer && $req->type_customer != -1) {
            //khách mới
            if ($req->type_customer == 0) {
               
            }
        }

        /* gộp 2 mảng: 1 mảng src chỉ có số contact trong thời gian chỉ định và
        1 mảng có data order thuộc src
        */
        $result = [];
        foreach ($listSrc as $k => $src) {
            $countOrder = $total = $qty = $avg = $rate = 0;
            // dd($src);
            // if ($src['id'] != 32) {
            //     continue;
            // }
            // echo $k . '<br>';
            if (array_key_exists($k, $listOrderKeySrc)) {
                // echo "<pre>";
                // print_r($listOrderKeySrc[$k]);
                // echo "</pre>";

                // echo $src['name'];

                
                foreach ($listOrderKeySrc[$k] as $item) {
                    $total += $item['total'];
                    $qty += $item['qty'];
                }

                $countOrder = count($listOrderKeySrc[$k]);
                if ($countOrder > 0) {
                    $avg =  $total / $countOrder;
                }

                if ($src['contact'] > 0) {
                    $rate =  round($countOrder / $src['contact'] * 100, 2);
                } else {
                    $rate =  round($countOrder * 100, 2);
                } 
            }

            $result[] = [
                'order' => $countOrder,
                'total' => $total,
                'product' => $qty,
                'avg' => $avg,
                'contact' => $src['contact'],
                'name' => $src['name'],
                'type' => $src['type'],
                'rate' => $rate,
            ];
        }

        // dd($result);
        // die();
        
        return $result;
    }

    public function getSrcPageFromSaleCare($saleCare)
    {
        // $src = SrcPage::orderBy('id', 'desc');
        if ($saleCare && empty($saleCare->id_src) || !$saleCare->id_src) {

            /** đơn hàng đc tạo từ data cskh ko có  page_id/page_name/page_link
             * => lấy data TN ban đầu
             */
            if (!$saleCare->page_id && !$saleCare->page_name && !$saleCare->page_link) {
                $saleCare = SaleCare::orderBy('id', 'asc')->where('phone', $saleCare->phone)->first();
            }

            if ($saleCare->page_id && $saleCare->page_id != 'tricho' && $saleCare->page_id != 'ladi') {
                $pageId = $saleCare->page_id;
                $src = SrcPage::where('id_page', $pageId);
            } else if ($saleCare->page_link) {
                $link = $saleCare->page_link;
                $src = SrcPage::where('link',  $link);
            } else if ($saleCare->page_name == 'hotline') {
                $src = SrcPage::where('page_name', $saleCare->page_name);
            } else {
                $src = SrcPage::where('id_page', 'tricho');
            }

            if ($first = $src->first()) {
                return $first;
            }
        }
    }
    public function getListMktReportByListSrc($list, $req)
    {
        $data = [];
        foreach ($list->get() as $item) {
            $dataReport = $this->getDataReportBySrcId($item, $req);
            $data[]= $dataReport;
        }

        return $data;
    }

    public function getListSaleCareBySrcId($item, $req)
    {
        $list   = SaleCare::orderBy('id', 'desc');
        if (isset($req['daterange']) || !empty($req->daterange)) {
            $dateRange = (isset($req['daterange'])) ? $req['daterange'] : $req->daterange;

            $time = $dateRange;
            if (!is_array($dateRange)) {
                $time = explode("-",$dateRange); 
            }

            // dd($time);
            // $toMonth    = date("Y-m-d", time());
            // $toMonth    = '2024-09-14';
            // $time = ['2024-09-01', '2024-09-30'];

            $timeBegin  = str_replace('/', '-', $time[0]);
            $timeEnd    = str_replace('/', '-', $time[1]);
            $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
            $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

            $list = $list->whereDate('created_at', '>=', $dateBegin)
                ->whereDate('created_at', '<=', $dateEnd);
        }

        if (isset($req['group']) || !empty($req->group)) {
            $groupId = (isset($req['group'])) ? $req['group'] : $req->group;
            $list = $list->where('group_id', $groupId);
        }

        if (isset($req['groupUser']) || !empty($req->groupUser)) {
            $groupUS = GroupUser::find($req['groupUser']);
            if ($groupUS) {
                $listSale = $groupUS->users;
                $list = $list->whereIn('assign_user', $listSale->pluck('id')->toArray());
            }
        }

        if (isset($req->type_customer) && (int)$req->type_customer != -1) {
            $list = $list->where('old_customer', $req->type_customer);
        }

        foreach ($list->get() as $sale) {
            if (!$sale->page_id && !$sale->page_name && !$sale->page_link) {
                $scNoSrc[] = $sale;
            }
        }

        $srcType = [
            'filterByIdSrc' => $item->id,
            'getAll'  => $item->id
        ];

        $list = $list->where(function($query) use ($srcType) {
            foreach ($srcType as $k => $term) {
                if ($k == 'filterByIdSrc') {
                    $query->orWhere('src_id', $term);
                } else {
                    $src = SrcPage::find($term);
                    if (!$src) {
                        return ;
                    }

                    if ($src->type == 'pc') {
                        $query->orWhere('page_id', $src->id_page);
                    } else if ($src->type == 'ladi') {
                        $query->orWhere('page_link', $src->link);
                    } else if ($src->type == 'hotline') {
                        $query->orWhere('page_id', $src->id_page);
                    } else if  ($src->type == 'old') {
                        $query->orWhere('page_name', $src->name);
                    } else {
                        $query->orWhere('page_id', 'tricho');
                    }
                }
            }
        });

        // if ($item->type == 'pc') {
        //     $list = $list->where('page_id', $item->id_page);
        // } else if ($item->type == 'ladi') {
        //     $list = $list->where('page_link', $item->link);
        // } else if ($item->type == 'hotline') {
        //     // $saleCare = $saleCare->where('page_name', $item->type);
        //     $list = $list->where('page_id', 'like', '%' . $item->id_page .'%');
        // } else if  ($item->type == 'old') {
        //     $list = $list->where('page_name', $item->name);
        // } else {
        //     $list = $list->where('page_id', 'tricho');
        // }

        return $list;
    }

    public function getDataReportBySrcId($item, $req)
    {
        $countSaleCare = 0;
        $saleCare = $this->getListSaleCareBySrcId($item, $req);
        $countSaleCare   = $saleCare->count();
        $result = [
            'contact' => $countSaleCare,
            'name' => $item->name,
            'type' => $item->type,
            'id' => $item->id,
        ];

        return $result;
    }

    public function marketingSrcUpdate($id)
    {
        $dataSrc = SrcPage::find($id);
        if ($dataSrc) {
            return view('pages.marketing.src.add')->with('dataSrc', $dataSrc);
        }
    }

    public function marketingSrcSave(Request $req)
    {
        $validator      = Validator::make($req->all(), [
            'name'  => 'required',
            'id_group'   => 'required',
            'type'  => 'required',
            'user_digital'  => "required|not_in:-1",
        ],[
            'name.required' => 'Nhập tên nguồn',
            'idGroup.required' => 'Chọn nhóm',
            'type.required' => 'Chọn loại kết nối',
            'userDigital.required' => 'Chọn người quảng cáo',
        ]);

        if ($validator->passes()) {
            
            if (isset($req->id)) {
                $srcPage = SrcPage::find($req->id);
                $text = 'Cập nhật nguồn marketing thành công.';
            } else {
                $srcPage = new SrcPage();
                $text = 'Tạo nguồn marketing thành công.';
            }

            $srcPage->type = $req->type;
            $srcPage->name = $req->name;
            $srcPage->user_digital = $req->user_digital;
            $srcPage->link = $req->link;
            $srcPage->id_page = $req->id_page;
            $srcPage->id_group = $req->id_group;
            $srcPage->token = $req->token;
            $srcPage->status = ($req->status == 'on') ? 1 : 0;

            $srcPage->save();
            notify()->success($text, 'Thành công!');
        } else {
            // dd($validator->errors()->first());
            notify()->error('Lỗi khi lưu nguồn marketing', 'Thất bại!');
        }

        return back();
    }
    public function marketingSrcAdd()
    {
        return view('pages.marketing.src.add');
        // ->with('listSale', $listSale)->with('listSrc', $listSrc);
    }

    public function srcPage()
    {
        $list = SrcPage::orderBy('id', 'desc')->get();
        $checkAll = isFullAccess(Auth::user()->role);
        if (!$checkAll) {
            $list = $list->where('user_digital', Auth::user()->id);
        }
        $listMktUser = Helper::getListMktUser(Auth::user());
        
        $listGroup = Helper::getListGroup();
        return view('pages.marketing.src.index')->with('list', $list)
            ->with('listMktUser', $listMktUser)
            ->with('listGroup', $listGroup);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $r)
    {
        if (count($r->all()) == 0) {
            $r['daterange'] = [date("Y/m/d"), date("Y/m/d")];
        } else {
            $r['daterange'] = Helper::converDateSql($r->daterange);
        }

        return $this->marketingSearchV2($r);
    }
}
