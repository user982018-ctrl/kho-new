<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AddressController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\SaleCare;
use App\Helpers\Helper;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\SrcPage;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function viewReportMkt()
    {
        $isDigital = Auth::user()->is_digital;
        $checkAll = isFullAccess(Auth::user()->role);
        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
        $dataDigital = [];       

        if ($isDigital) {
            if (($checkAll || $isLeadDigital)) {
                $dataDigital = Helper::getListDigital()->get();
                $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
                if ($isLeadDigital) {
                    $groupDi = GroupUser::where('lead_team', Auth::user()->id)->first();
                    if ($groupDi) {
                        $dataDigital = $groupDi->users;
                    }
                }
                
            } else {
                $dataDigital[] = User::find(Auth::user()->id);   
            }
        }

        $category = Category::where('status', 1)->get();
        $groups = Group::orderBy('id', 'desc')->get();
        $groupDigital = GroupUser::orderBy('id', 'desc')->where('type', 'mkt')->get();

        return view('pages.marketing.reportMkt')->with('category', $category)
            ->with('groups', $groups)
            ->with('groupDigital', $groupDigital)
            ->with('dataDigital', $dataDigital);
    }
    
    public function viewReportSale()
    {
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $isCskhDt = Helper::isCskhDt(Auth::user());
        $isDigital = Auth::user()->is_digital;
        $checkAll = isFullAccess(Auth::user()->role);

        $dataSale = $dataSaleCSKH = [];
        $groupSale = GroupUser::where('status', 1)
            ->where('type', 'sale')->get();
        if (!$isCskhDt && !$isDigital) {
            if (($checkAll || $isLeadSale)) {
                foreach ($groupSale as $gr) {
                    if ($gr->id != 5) {
                        $listIdSale[] = $gr->users->pluck('id')->toArray();
                    }
                }
                $listIdSale = array_merge(...$listIdSale);
                foreach ($listIdSale as $sale) {
                    $dataSale[] = User::find($sale);
                }

            } else {
                $dataSale[] = User::find(Auth::user()->id);   
            }
        }        

        if ($checkAll || ($isLeadSale && $isCskhDt)) {
                // id cskh đạm tôm - team Trinh
            $dataSaleCSKH = GroupUser::find(5)->users;
        } else if ($isCskhDt) {
            $dataSaleCSKH[] = User::find(Auth::user()->id);   
        }

        $category = Category::where('status', 1)->get();
        // $sales = User::where('status', 1)->where('is_sale', 1)->orWhere('is_cskh', 1)->get();
        $sales = [];
        if ($checkAll) {
            $sales      = Helper::getListSale()->get();
        } else if (!$checkAll && $isLeadSale) {
            $sales = Helper::getListSaleOfLeader()->get();
        }
        $groups = Helper::getListGroupByLeadSale(Auth::user());
        $groupUser = GroupUser::orderBy('id', 'desc')->where('type', 'sale')->get();

        return view('pages.sale.reportSale')->with('category', $category)->with('sales', $sales)
            ->with('dataSale', $dataSale)
            ->with('groups', $groups)
            ->with('groupUser', $groupUser)
            ->with('dataSaleCSKH', $dataSaleCSKH);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $toMonth = date("d/m/Y", time());
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $isCskhDt = Helper::isCskhDt(Auth::user());
        $isDigital = Auth::user()->is_digital;
        $checkAll = isFullAccess(Auth::user()->role);
        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);

        $isSale = Auth::user()->is_sale || Auth::user()->is_cskh;
        $isDigital = Auth::user()->is_digital;
        if ($isSale) {
            return redirect()->route('view-sale-report');
        }

        if ($isDigital) {
            return redirect()->route('view-mkt-report');
        }
        /**set tmp */
        // $toMonth = '10/05/2025';

        $dataSale = $dataSaleCSKH = $dataDigital = [];
        $groupSale = GroupUser::where('status', 1)
            ->where('type', 'sale')->get();
        if (!$isCskhDt && !$isDigital) {
            if (($checkAll || $isLeadSale)) {
                foreach ($groupSale as $gr) {
                    if ($gr->id != 5) {
                        $listIdSale[] = $gr->users->pluck('id')->toArray();
                    }
                }
                $listIdSale = array_merge(...$listIdSale);
                foreach ($listIdSale as $sale) {
                    $dataSale[] = User::find($sale);
                }

            } else {
                $dataSale[] = User::find(Auth::user()->id);   
            }
        }        

        if ($checkAll || ($isLeadSale && $isCskhDt)) {
                // id cskh đạm tôm - team Trinh
            $dataSaleCSKH = GroupUser::find(5)->users;
        } else if ($isCskhDt) {
            $dataSaleCSKH[] = User::find(Auth::user()->id);   
        }

        if ($isDigital) {
            if (($checkAll || $isLeadDigital)) {
                $dataDigital = Helper::getListDigital()->get();
                $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
                if ($isLeadDigital) {
                    $groupDi = GroupUser::where('lead_team', Auth::user()->id)->first();
                    if ($groupDi) {
                        $dataDigital = $groupDi->users;
                    }
                }
                
            } else {
                $dataDigital[] = User::find(Auth::user()->id);   
            }
        }

        $category = Category::where('status', 1)->get();
        $sales = User::where('status', 1)->where('is_sale', 1)->orWhere('is_cskh', 1)->get();
        $groups = Helper::getListGroupByLeadSale(Auth::user());
        $groupUser = GroupUser::orderBy('id', 'desc')->where('type', 'sale')->get();
        $groupDigital = GroupUser::orderBy('id', 'desc')->where('type', 'mkt')->get();

        return view('pages.home')->with('category', $category)->with('sales', $sales)
            ->with('dataSale', $dataSale)
            ->with('groups', $groups)
            ->with('groupUser', $groupUser)
            ->with('groupDigital', $groupDigital)
            ->with('dataSaleCSKH', $dataSaleCSKH)
            ->with('dataDigital', $dataDigital);
    }

    // public function index()
    // {
    //     return view('pages.home');
    // }

    public function getReportCskhDamTom($time, $checkAll = false, $isLeadSale = false)
    {
        $dataFilter['daterange'] = [$time, $time];
        $result = [];
        if (!$checkAll) {
            $checkAll = isFullAccess(Auth::user()->role);
        }

        $isLeadSale = $isLeadSale ? : Helper::isLeadSale(Auth::user()->role);
        
        if ($checkAll || $isLeadSale) {
            
            $listSale =  Helper::getListSaleV3(Auth::user(), $isLeadSale, 5);
            foreach ($listSale as $sale) {
                $data = $this->getReportUserCskhDT($sale, $dataFilter);
                $result[] = $data;   
            }

        } else if ((Auth::user()->is_CSKH || Auth::user()->is_sale) && Helper::isCskhDt(Auth::user())){
            $result[] = $this->getReportUserCskhDT(Auth::user(), $dataFilter);
        }
       
        return $result;
    }

    public function getReportUserCskhDTV2($user, $dataFilter)
    {

    }
    
    public function getReportHomeDigital($time)
    {
        $dataFilter['daterange'] = [$time, $time];
        $listDigital = [
            [
                'name' => 'Mr Nguyên',
                'mkt' => 1,
            ],
            [
                'name' => 'Mr Tiễn',
                'mkt' => 2,
            ],
            [
                'name' => 'Di Di',
                'mkt' => 3,
            ],
        ];

        $result = [];

        $req = new Request();
        $req->merge(['date' => $dataFilter['daterange']]);

        $checkAll = isFullAccess(Auth::user()->role);
  
        if (Auth::user()->is_digital) {
            if (Auth::user()->name == 'digital.tien') {
                $req->merge(['mkt' => 2]);
            } else if (Auth::user()->name == 'digital.di') {
                $req->merge(['mkt' => 3]);
            }
            
        }

        $data = $this->ajaxFilterDashboardDigital($req);

        if (count($data['data_digital']['data'])) {
            $result = $data['data_digital']['data'];
        }

        return $result;
    }

      /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function filterTotal(Request $req)
    {
        if ($req->type == 'day') {
            $today      = date('Y-m-d', time());
            $ordersSum  = Orders::where('created_at', '>=', $today)->get()->sum('total');

            $yesterday  = date('Y-m-d',strtotime("-1 days"));
            $ordersYesterdaySum = Orders::where('created_at', '>=', $yesterday)
                ->where('created_at', '<', $today)
                ->get()->sum('total');
            
            $totalDay           = $ordersSum + $ordersYesterdaySum;
            $percentTotalDay    = round(($ordersSum - $ordersYesterdaySum) * 100 / $totalDay, 2);

            $countOrders    = Orders::where('created_at', '>=', $today)->get()->count();
            $countOrdersYes =  Orders::where('created_at', '>=', $yesterday)
                ->where('created_at', '<', $today)
                ->get()->count();
            $countOrdersDay     = $countOrders + $countOrdersYes;
            $percentCountDay    = round(($countOrders - $countOrdersYes) * 100 / $countOrdersDay, 2);

            // $sumTotal = Orders::get()->sum('total');
            $avgOrders = $ordersSum / $countOrders;
            $avgOrdersYes = $ordersYesterdaySum / $countOrdersYes;
            $totalAvgDay           = $ordersSum + $ordersYesterdaySum;
            $percentAvg    = round(($avgOrders - $avgOrdersYes) * 100 / $totalAvgDay, 2);

            return response()->json([
                'totalSum'          => number_format($ordersSum) . ' đ',
                'today'             => date('d-m-Y', time()),
                'percentTotalDay'   => '(' . $percentTotalDay .'%)',
                'countOrders'       => $countOrders,
                'percentCountDay'   => '(' . $percentCountDay .'%)',
                'avgOrders'         => number_format($avgOrders) . ' đ',
                'percentAvg'        => '(' . $percentAvg .'%)',
            ]);
        }
    }

    public function filterByDate($type, $date)
    {
        $result = [];
        switch ($type) {
            case "day":
                $countOrders = $ordersSum = $countSaleCare = $rateSuccess = $avgOrders = 0;
                $ordersCtl = new OrdersController();
                $dataFilter['daterange'] = [$date, $date];
                // $dataFilter['daterange'] = ['2024-05-25', '2024-05-25'];

                $listOrder      = $ordersCtl->getListOrderByPermisson(Auth::user(),$dataFilter);
                $countOrders    = $listOrder->count();
                $ordersSum      = $listOrder->sum('total');

                if ($countOrders > 0) {
                    $avgOrders = $ordersSum / $countOrders;
                }

                $ordersCtl = new SaleController();
                $saleCare  = $ordersCtl->getListSalesByPermisson(Auth::user(), $dataFilter)
                    ->where('old_customer', 0);
                $countSaleCare = $saleCare->count();

                /** tỷ lệ chốt = số đơn/số data */
                if ($countSaleCare == 0) {
                    $rateSuccess = $countOrders * 100;
                } else {
                    $rateSuccess = $countOrders / $countSaleCare * 100;
                }

                $rateSuccess = round($rateSuccess, 2);
                
                $result = [
                    'totalSum'      => number_format($ordersSum) . 'đ',
                    // 'percentTotal'  => '(' . (($percentTotalDay > 0) ? '+' : '' ) . $percentTotalDay  .'%)',
                    'countOrders'   => $countOrders,
                    // 'percentCount'  => '(' . (($percentCountDay > 0) ? '+' : '' ) . $percentCountDay .'%)',
                    'avgOrders'     => number_format($avgOrders) . 'đ',
                    // 'percentAvg'    => '(' . (($percentAvg > 0) ? '+' : '' ) . $percentAvg  .'%)',
                    'rateSuccess'   =>  $rateSuccess . '%',
                    'countSaleCare' =>  $countSaleCare,
                    ];
                break;
            case "month":
                //lấy tháng trong chuỗi '2024/03/24' => 03
                $month      = date('m', strtotime($date));
                $ordersMonthSum  = Orders::whereMonth('created_at', '=', $month)
                    // ->where('status', 3)
                    ->get()->sum('total');
                
                //lấy tháng trước của tháng được chọn => 02
                $lastMonth          = date('m',strtotime("$date -1 month"));
                $ordersLastMonthSum = Orders::whereMonth('created_at', '=', $lastMonth)
                    // ->where('status', 3)
                    ->get()->sum('total');
                
                /* tính phần trăm tăng giảm của tháng được chọn so với tháng trước dựa trên 'total'
                    round() : chỉ lấy và làm tròn 2 chữ số thập phân
                */
                $totalMonth         = $ordersMonthSum + $ordersLastMonthSum;
                $percentTotalMonth  = $percentCountMonth = 0;
                if ($ordersMonthSum > 0) {
                    $percentTotalMonth    = round(($ordersMonthSum - $ordersLastMonthSum) * 100 / $totalMonth, 2);
                }
                
                /* tính phần trăm tăng giảm của tháng được chọn so với tháng trước dựa trên số lượng
                    round() : chỉ lấy và làm tròn 2 chữ số thập phân
                */
                $countOrders            = Orders::whereMonth('created_at', '=', $month)
                    // ->where('status', 3)
                    ->get()->count();
                $countOrdersLast        =  Orders::whereMonth('created_at', '=', $lastMonth)
                    // ->where('status', 3)
                    ->get()->count();
                $countOrdersMonth       = $countOrders + $countOrdersLast;
                if ($countOrdersMonth > 0) {
                    $percentCountMonth      = round(($countOrders - $countOrdersLast) * 100 / $countOrdersMonth, 2);
                }
                
                // trung bình đơn = tổng tiền / số đơn
                $avgOrders = $avgOrdersLastMonth = $percentAvg = 0;
                if ($ordersMonthSum > 0) {
                    $avgOrders = $ordersMonthSum / $countOrders;
                }

                if ($countOrdersLast > 0) {
                    $avgOrdersLastMonth = $ordersLastMonthSum / $countOrdersLast;
                }

                if ($avgOrders > 0) {
                    $totalAvgMonthLastMonth = $avgOrders + $avgOrdersLastMonth;
                    $percentAvg    = round(($avgOrders - $avgOrdersLastMonth) * 100 / $totalAvgMonthLastMonth, 2);    
                }
               
                $result = [
                        'totalSum'      => number_format($ordersMonthSum) . 'đ',
                        'percentTotal'  => '(' . (($percentTotalMonth > 0) ? '+' : '-' ) . $percentTotalMonth  .'%)',
                        'countOrders'   => $countOrders,
                        'percentCount'  => '(' . (($percentCountMonth > 0) ? '+' : '-' ) . $percentCountMonth .'%)',
                        'avgOrders'     => number_format($avgOrders) . 'đ',
                        'percentAvg'    => '(' . (($percentAvg > 0) ? '+' : '-' ) . $percentAvg  .'%)',
                    ];
              break;

            case "year":
                    //lấy năm trong chuỗi '2024/03/24' => 2024
                    $year      = date('Y', strtotime($date));
                    $ordersYearSum  = Orders::whereYear('created_at', '=', $year)
                        // ->where('status', 3)
                        ->get()->sum('total');
                   
                    //lấy năm trước của date được chọn => 2023
                    $lastYear          = date('Y',strtotime("$year -1 year"));
                    $ordersLastYearSum = Orders::whereYear('created_at', '=', $lastYear)
                        // ->where('status', 3)
                        ->get()->sum('total');
                    
                    /* tính phần trăm tăng giảm của năm được chọn so với năm trước dựa trên 'total' */
                    $totalYear        = $ordersYearSum + $ordersLastYearSum;
                    $percentTotalYear  = $percentCountYear = 0;
                    if ($ordersYearSum > 0) {
                        $percentTotalYear    = round(($ordersYearSum - $ordersLastYearSum) * 100 / $totalYear, 2);
                    }
                
                    /* tính phần trăm tăng giảm của năm được chọn so với năm trước dựa trên số lượng
                        round() : chỉ lấy và làm tròn 2 chữ số thập phân
                    */
                    $countOrders            = Orders::whereYear('created_at', '=', $year)
                        // ->where('status', 3)
                        ->get()->count();
                    $countOrdersLast        =  Orders::whereYear('created_at', '=', $lastYear)
                        // ->where('status', 3)
                        ->get()->count();
                    $countOrdersYear       = $countOrders + $countOrdersLast;
                    if ($countOrdersYear > 0) {
                        $percentCountYear      = round(($countOrders - $countOrdersLast) * 100 / $countOrdersYear, 2);
                    }
        
                    // trung bình đơn = tổng tiền / số đơn
                    $avgOrders = $avgOrdersLastYear = $percentAvg = 0;
                    if ($ordersYearSum > 0) {
                        $avgOrders = $ordersYearSum / $countOrders;
                    }

                    if ($countOrdersLast > 0) {
                        $avgOrdersLastYear = $ordersLastYearSum / $countOrdersLast;
                    }

                    if ($avgOrders > 0) {
                        $totalAvgYear = $avgOrders + $avgOrdersLastYear;
                        $percentAvg    = round(($avgOrders - $avgOrdersLastYear) * 100 / $totalAvgYear, 2);    
                    }
                
                    $result = [
                            'totalSum'      => number_format($ordersYearSum) . 'đ',
                            'percentTotal'  => '(' . (($percentTotalYear > 0) ? '+' : '-' ) . $percentTotalYear  .'%)',
                            'countOrders'   => $countOrders,
                            'percentCount'  => '(' . (($percentCountYear > 0) ? '+' : '-' ) . $percentCountYear .'%)',
                            'avgOrders'     => number_format($avgOrders) . 'đ',
                            'percentAvg'    => '(' . (($percentAvg > 0) ? '+' : '-' ) . $percentAvg  .'%)',
                        ];
                break;

            case "daterange":
                $startString    = str_replace('/', '-', $date[0]);
                $startDate      = date('Y-m-d', strtotime($startString));
                $endString      = str_replace('/', '-', $date[1]);
                $endDate        = date('Y-m-d', strtotime($endString));
                $queryOrder     = Orders::whereDate('created_at', '<=', $endDate)
                    ->whereDate('created_at', '>=', $startDate);
                    // ->where('status', 3);
                $totalSum       = $queryOrder->get()->sum('total');
                
                $countOrders    = $queryOrder->get()->count();

                // trung bình đơn = tổng tiền / số đơn
                $avgOrders = 0;
                if ($totalSum > 0) {
                    $avgOrders = $totalSum / $countOrders;
                }
             
                $result = [
                        'totalSum'      => number_format($totalSum) . 'đ',
                        'percentTotal'  => '',
                        'countOrders'   => $countOrders,
                        'percentCount'  => '',
                        'avgOrders'     => number_format($avgOrders) . 'đ',
                        'percentAvg'    => '',
                    ];
                break;
            default: break;    
        }
        
        return $result;
    }

    public function getOrdersReport($user, $dataFilter = null, $checkAllAPI = false) 
    {
        // Chỉ rõ table name ngay từ đầu để tránh ambiguous khi có JOIN sau này
        $list   = Orders::select('orders.qty', 'orders.total', 'orders.id', 'orders.sale_care', 'orders.created_at');
        if ($dataFilter) {
            if (isset($dataFilter['daterange'])) {
                $time       = $dataFilter['daterange'];
                $timeBegin  = str_replace('/', '-', $time[0]);
                $timeEnd    = str_replace('/', '-', $time[1]);

                $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
                $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

                // Chỉ rõ table name để tránh ambiguous khi có JOIN
                $list->whereDate('orders.created_at', '>=', $dateBegin)
                ->whereDate('orders.created_at', '<=', $dateEnd);
            }

            if (isset($dataFilter['status'])) {
                $list->whereStatus($dataFilter['status']);
            }

            if (isset($dataFilter['category'])) {
                // Tối ưu: chỉ select id_product và id, không cần get tất cả field
                $orders = $list->select('orders.id', 'orders.id_product')->get();
                $ids = [];
                foreach ($orders as $order) {
                    $products = json_decode($order->id_product);
                    if ($products) {
                        $isProductOfCategory = Helper::checkProductsOfCategory($products, $dataFilter['category']);
                        if ($isProductOfCategory) {
                            $ids[] = $order->id;
                        }
                    }
                }

                if (empty($ids)) {
                    // Nếu không có order nào match, return empty query
                    $list = Orders::whereRaw('1 = 0');
                } else {
                    // Chỉ rõ table name để tránh ambiguous
                    $list = Orders::select('orders.qty', 'orders.total', 'orders.id', 'orders.sale_care', 'orders.phone', 'orders.created_at')->whereIn('orders.id', $ids);
                }
            }

            if (isset($dataFilter['product'])) {
                // Tối ưu: chỉ select id_product và id
                $orders = $list->select('orders.id', 'orders.id_product')->get();
                $ids = [];
                
                foreach ($orders as $order) {
                    $products = json_decode($order->id_product);
                    if ($products) {
                        foreach ($products as $product) {
                            if (isset($product->id) && $product->id == $dataFilter['product']) {
                                $ids[] = $order->id;
                                break;
                            }
                        }
                    }
                }

                if (empty($ids)) {
                    // Nếu không có order nào match, return empty query
                    $list = Orders::whereRaw('1 = 0');
                } else {
                    // Chỉ rõ table name để tránh ambiguous
                    $list = Orders::select('orders.qty', 'orders.total', 'orders.id', 'orders.sale_care', 'orders.phone', 'orders.created_at')->whereIn('orders.id', $ids);
                }
            }

            if (isset($dataFilter['group'])) {
                // Tối ưu: join trực tiếp thay vì pluck rồi whereIn
                // Lưu ý: sau JOIN, cần chỉ rõ table name cho các column
                $list = $list->join('sale_care', 'orders.sale_care', '=', 'sale_care.id')
                    ->where('sale_care.group_id', $dataFilter['group'])
                    ->select('orders.qty', 'orders.total', 'orders.id', 'orders.sale_care', 'orders.phone', 'orders.created_at');
            }

            if (isset($dataFilter['groupUser'])) {
                $groupUS = GroupUser::find($dataFilter['groupUser']);
                if ($groupUS) {
                    // Tối ưu: eager load users và chỉ lấy ids một lần
                    $groupUS->load('users:id');
                    $saleIds = $groupUS->users->pluck('id')->toArray();
                    if (empty($saleIds)) {
                        $list = Orders::whereRaw('1 = 0');
                    } else {
                        // Chỉ rõ table name để tránh ambiguous
                        $list = $list->whereIn('orders.assign_user', $saleIds);
                    }
                }
            }

            if (isset($dataFilter['src'])) {
                // Tối ưu: eager load saleCare để tránh N+1 query
                $orders = $list->with('saleCare:id,src_id,page_id,page_link,page_name')->get();
                $idTmps = [];
                $mktCtl = new MarketingController();
                
                foreach ($orders as $order) {
                    if ($order->saleCare) {
                        $srcPage = $mktCtl->getSrcPageFromSaleCare($order->saleCare);
                        if ($srcPage) {
                            $idTmps[] = $order->id;
                        }
                    }
                }

                if (empty($idTmps)) {
                    // Nếu không có order nào match, return empty query
                    $list = Orders::whereRaw('1 = 0');
                } else {
                    // Chỉ rõ table name để tránh ambiguous
                    $list = Orders::select('orders.qty', 'orders.total', 'orders.id', 'orders.sale_care', 'orders.phone', 'orders.created_at')->whereIn('orders.id', $idTmps);
                }
            } 
        }

        $checkAll = $checkAllAPI;
        if (!$checkAll) {
            $checkAll = isFullAccess(Auth::user()->role);
        }
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);

        if ((isset($dataFilter['sale']) && $dataFilter['sale'] != 999) && ($checkAll || $isLeadSale)) {
            /** user đang login = full quyền và đang lọc 1 sale */
            // Chỉ rõ table name để tránh ambiguous sau JOIN
            $list = $list->where('orders.assign_user', $dataFilter['sale']);
        } else if ((!$checkAll || !$isLeadSale) && !$user->is_digital && $user->is_sale) {
            /** sale đag xem report của mình */
            $list = $list->where('orders.assign_user', $user->id);
        }

        return $list;
    }

    public function getTypeOfOrder($orderId, $saleCare)
    {
        $orderId = $saleCare->id_order_new;
        $phone = $saleCare->phone;
        $type = 0;
        $orders = Orders::select('id')->where('phone', 'like', '%' . $phone . '%');

        foreach ($orders as $order) {
            if ($order->id != $orderId) {
                $type = 1;
                break;
            }
        }

        return $type;
    }

    public function getSalesReport($user, $dataFilter = null) 
    {
        $list   = SaleCare::select('id', 'src_id');
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $checkAll = isFullAccess(Auth::user()->role);
        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);

        if ($dataFilter) {
            if (isset($dataFilter['daterange'])) {
                $time       = $dataFilter['daterange'];
                $timeBegin  = str_replace('/', '-', $time[0]);
                $timeEnd    = str_replace('/', '-', $time[1]);
                $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
                $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

                $list->whereDate('created_at', '>=', $dateBegin)
                    ->whereDate('created_at', '<=', $dateEnd);
            }

            /** có chọn 1 nguồn */
            if (isset($dataFilter['src'])) {
                $srcType = [
                    'filterByIdSrc' => $dataFilter['src'],
                    'getAll'  => $dataFilter['src']
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
            }

            /* mkt ko có quyền admin, lead mkt => gán thêm lọc theo mkt */
            if (!$checkAll && !$isLeadDigital && Auth::user()->is_digital) {
                $dataFilter['mkt'] = Auth::user()->id;
            }

             if (isset($dataFilter['mkt'])) {
                // Tối ưu: chỉ select id, không cần orderBy khi chỉ lấy ids
                $srcIDs = SrcPage::where('user_digital', $dataFilter['mkt'])->pluck('id')->toArray();
                if (!empty($srcIDs)) {
                    $list->whereIn('src_id', $srcIDs);
                } else {
                    // Nếu không có src nào, return empty query
                    $list = SaleCare::whereRaw('1 = 0');
                }
            }

            if (isset($dataFilter['group'])) {
                $list   = $list->where('group_id', $dataFilter['group']);
            }

            if (isset($dataFilter['product'])) {
                $list->whereNotNull('id_order_new');
                // Tối ưu: eager load orderNew để tránh N+1 query
                $saleCares = $list->with('orderNew:id,id_product')->get();
                $newSCare = [];
                
                foreach ($saleCares as $scare) {
                    if ($scare->orderNew) {
                        $products = json_decode($scare->orderNew->id_product);
                        if ($products) {
                            foreach ($products as $product) {
                                if (isset($product->id) && $product->id == $dataFilter['product']) {
                                    $newSCare[] = $scare->id;
                                    break;
                                }
                            }
                        }
                    }
                }

                if (empty($newSCare)) {
                    // Nếu không có sale care nào match, return empty query
                    $list = SaleCare::whereRaw('1 = 0');
                } else {
                    $list = SaleCare::select('id', 'src_id')->whereIn('id', $newSCare);
                }
            }
        }

        if ((isset($dataFilter['sale']) && $dataFilter['sale'] != 999) && ($checkAll || $isLeadSale)) {
            /** user đang login = full quyền và đang lọc 1 sale */
            // Chỉ rõ table name để tránh ambiguous
            $list = $list->where('sale_care.assign_user', $dataFilter['sale']);     
        } else if ((!$checkAll || !$isLeadSale ) && !$user->is_digital && $user->is_sale) {
            $list = $list->where('sale_care.assign_user', $user->id);
        }

        return $list;
    }

   /**
     * contact: số data sale đc nhận
     * order: số đơn đc tạo
     * rate: tỉ lệ chốt
     * product: tổng số lượng sp 
     * total: doanh thu
     * avg: trung bình đơn
    * Lưu ý: hiện tại logic là khách cũ - khách mới chỉ định cho 2 sale riêng biệt
    * nên get/set theo thuộc tính is_sale cho khách mới và is_cskh cho khách cũ
    * và if else cho new old tuọng trưng để sau này 1 sale có thể TN cả khách cũ và khách mới
    */
    public function getSaleByTypeV2($dataFilter, $checkAllAPI)
    {
        $result = $newCustomer = $oldCustomer = []; 
        $countSaleCareOld = $countSaleCareNew = $countOrderNew = $countOrderOld = $avgOrders = 0;

        $listOrder = $this->getOrdersReport(Auth::user(), $dataFilter, $checkAllAPI);
        
        // Eager load saleCare relationship để tránh N+1 query
        $listOrder->with('saleCare:id,old_customer,phone');
        
        // Lấy orders với các field cần thiết
        // Đảm bảo chỉ rõ table name để tránh ambiguous sau JOIN
        $orders = $listOrder->select('orders.id', 'orders.qty', 'orders.total', 'orders.phone', 'orders.sale_care', 'orders.created_at')->get();
        $countOrders = $orders->count();

        if($countOrders == 0) {
            $newCustomer['order'] = 0;
            $oldCustomer['order'] = 0;
        } else {
            // Tính toán trực tiếp thay vì query lại
            $newTotal = $newQty = $newCountOrder = 0;
            $oldTotal = $oldQty = $oldCountOrder = 0;
            $phonesToCheck = [];
            
            // Thu thập tất cả phones cần check (type = 2) để query một lần
            foreach ($orders as $order) {
                $saleCare = $order->saleCare;
                if ($saleCare && $saleCare->old_customer == 2) {
                    $phone = $order->phone ?? $saleCare->phone ?? null;
                    if ($phone) {
                        $phonesToCheck[] = $phone;
                    }
                }
            }
            
            // Load tất cả old customers theo phone một lần
            $oldCustomerPhones = [];
            if (!empty($phonesToCheck)) {
                $oldCustomerPhones = SaleCare::whereIn('phone', array_unique($phonesToCheck))
                    ->where('old_customer', 1)
                    ->pluck('phone')
                    ->flip()
                    ->toArray(); // Dùng làm hash map để check nhanh O(1)
            }

            // Phân loại và tính toán trong một vòng lặp
            foreach ($orders as $k => $order) {
                $typeCutomer = 0;
                $saleCare = $order->saleCare;
                $qty = (float)($order->qty ?? 0);
                $total = (float)($order->total ?? 0);

                if ($saleCare) {
                    $typeCutomer = $saleCare->old_customer ?? 0;
                }
                
                if ($typeCutomer == 2) {
                    /** check khách cũ/khách mới khi type = 2 (hotline) */
                    $phone = $order->phone ?? $saleCare->phone ?? null;
                    if ($phone && isset($oldCustomerPhones[$phone])) {
                        $typeCutomer = 1;
                    }
                }
                
                if ($typeCutomer == 1) {
                    $oldCountOrder++;
                    $oldTotal += $total;
                    $oldQty += $qty;
                } else if ($typeCutomer == 0) {
                    $newCountOrder++;
                    $newTotal += $total;
                    $newQty += $qty;
                } 
            }

            /** new */
            if ($newCountOrder == 0) {
                $newCustomer['order'] = 0;
                $newCustomer['total'] = 0;
                $newCustomer['product'] = 0;
                $newCustomer['avg'] = 0;
                $newCustomer['rate'] = 0;
            } else {
                $newCustomer['order'] = $newCountOrder;
                $newCustomer['total'] = round($newTotal, 0);
                $newCustomer['product'] = $newQty;
                $newCustomer['avg'] = round($newTotal / $newCountOrder, 0);
            }

            /** old */
            if ($oldCountOrder == 0) {
                $oldCustomer['order'] = 0;
                $oldCustomer['total'] = 0;
                $oldCustomer['product'] = 0;
                $oldCustomer['avg'] = 0;
                $oldCustomer['rate'] = 0;
            } else {
                $oldCustomer['order'] = $oldCountOrder;
                $oldCustomer['total'] = round($oldTotal, 0);
                $oldCustomer['product'] = $oldQty;
                $oldCustomer['avg'] = round($oldTotal / $oldCountOrder, 0);
            }
        }

        $saleCare  = $this->getSalesReport(Auth::user(), $dataFilter);
        $countSaleCare = $saleCare->count();

        if ($countSaleCare == 0) {
            $newCustomer['contact'] = 0;
            $oldCustomer['contact'] = 0;
        } else {
            /** có contact nhưng order 0 */
            if ($countOrders == 0) {
                $newCustomer['order'] = $newCustomer['total'] = $newCustomer['product'] = $newCustomer['avg'] = $newCustomer['rate'] = 0;
                $oldCustomer['order'] = $oldCustomer['total'] = $oldCustomer['product'] = $oldCustomer['avg'] = $oldCustomer['rate'] = 0;
                
            }
            // Tối ưu: query trực tiếp với điều kiện thay vì get->pluck rồi query lại
            $saleCareOldQuery = clone $saleCare;
            $countSaleCareOld = $saleCareOldQuery->where('old_customer', 1)->count();
            $oldCustomer['contact'] = $countSaleCareOld;
           
            $saleCareNewQuery = clone $saleCare;
            $countSaleCareNew = $saleCareNewQuery->whereIn('old_customer', [0,2])->count();
            $newCustomer['contact'] = $countSaleCareNew;
        }             

        // Đảm bảo biến đếm đơn đã được khởi tạo trước khi tính rate
        $newCountOrder = $newCustomer['order'] ?? 0;
        $oldCountOrder = $oldCustomer['order'] ?? 0;

        /** tỷ lệ chốt = số đơn/số data */
        /** new */
        if ($countSaleCareNew == 0) {
            $rateSuccessNew = $newCountOrder * 100;
        } else {
            $rateSuccessNew = $newCountOrder / $countSaleCareNew * 100;
        }
        $newCustomer['rate'] = round($rateSuccessNew, 2);

        /** old */
        if ($countSaleCareOld == 0) {
            $rateSuccessOld = $oldCountOrder * 100;
        } else {
            $rateSuccessOld = $oldCountOrder / $countSaleCareOld * 100;
        }
        $oldCustomer['rate'] = round($rateSuccessOld, 2);
       
        $result = [
            'new_customer' => $newCustomer, 
            'old_customer' => $oldCustomer
        ];
        
        return $result;
    }

    public function getReportUserSaleV2($user, $dataFilter, $checkAllAPI = false, $isCskhDt = false)
    {
        $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
        $dataFilter['sale'] = $user['id'];
        $dataFilter['typeDate'] = 1; //ngày data vè hệ thống

        $data = $this->getSaleByTypeV2($dataFilter, $checkAllAPI);
        $newCustomer = $data['new_customer'];
        $oldCustomer = $data['old_customer'];
        
        // Tối ưu: check số keys thay vì count toàn bộ array
        $newKeys = array_keys($newCustomer);
        $oldKeys = array_keys($oldCustomer);
        // Nếu cả 2 đều chỉ có 3 keys (order, total, product = empty data), return false
        if (count($newKeys) == 3 && count($oldKeys) == 3 && 
            !isset($newCustomer['contact']) && !isset($oldCustomer['contact'])) {
            return false;
        }

        // Tối ưu: newCustomer['total'] đã là số từ getSaleByTypeV2, chỉ cần convert nếu là string
        $newTotal = isset($newCustomer['total']) ? 
            (is_numeric($newCustomer['total']) ? (float)$newCustomer['total'] : Helper::stringToNumberPrice($newCustomer['total'])) : 0;
        $oldTotal = isset($oldCustomer['total']) ? 
            (is_numeric($oldCustomer['total']) ? (float)$oldCustomer['total'] : Helper::stringToNumberPrice($oldCustomer['total'])) : 0;
        
        $newCountOrder = $newCustomer['order'] ?? 0;
        $oldCountOrder = $oldCustomer['order'] ?? 0;
        $totalSum = $newTotal + $oldTotal;
        
        if ($newCountOrder != 0 || $oldCountOrder != 0) {
            $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
        } else {
            $avgSum = 0;
        }

        $rateSum = 0;
        if ($isCskhDt) {
            $contactSum = $data['old_customer']['contact'];
        } else {
            $contactSum = $data['new_customer']['contact'];
        }

        $orderSum = $data['old_customer']['order'] + $data['new_customer']['order'];
        if ($contactSum > 0) {
            $rateSum = $orderSum / $contactSum * 100;
        } else {
            $rateSum = $orderSum * 100;
        }

        $profileImg = '/public/assets/img/avatars/8.png';
        if (isset($user['profile_image'])) {
            $profileImg = '/storage/app/public/' . $user['profile_image'];
        }
        $result = [
            'profile_image' => $profileImg,
            'name' => ($user['real_name']) ?: '',
            'new_customer' => $newCustomer,
            'old_customer' => $oldCustomer,
            'summary_total' => [
                'total' => round($totalSum, 0),
                'avg' => round($avgSum, 0),
                'rate' => round($rateSum, 2)
            ]
        ];

        return $result;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function filterTotalSales(Request $req) {
        return response()->json($this->filterByDate($req->type, $req->date));
    }

    public function ajaxFilterDashboardCskhDT(Request $req) 
    {
        $result =  $dataFilter = $list = [];
        if ($req->date && getType($req->date) == 'string') {
            $dataFilter['daterange'] = explode('-', $req->date);
        }

        if (isset($req->status) && $req->status != 999) {
            $dataFilter['status'] = $req->status;
        }

        $category = $req->category;
        if ($category && $category != 999) {
            $dataFilter['category'] = $category;
        }

        $product = $req->product;
        if ($req->product && $product != 999) {
            $dataFilter['product'] = $product;
        }

        $sale = $req->sale;
        if ($sale && $sale != 999) {
            $dataFilter['sale'] = $req->sale;
        }

        $mkt = $req->mkt;
        if ($mkt && $mkt != 999) {
            $dataFilter['mkt'] = $mkt;
        }

        $src = $req->src;
        if ($src && $src != 999) {
            $dataFilter['src'] = $src;
            $newFilter['src'] = $src;
        } 

        $group = $req->group;
        if ($group && $group != 999) {
            $dataFilter['group'] = $group;
        }

        $show = $req->show ?? 20;
        $groupUser = $req->groupUser;
        $list = [];
        
        if ($groupUser && $groupUser != 999) {
            $groupUs = GroupUser::find($groupUser);
            
            if ($groupUs) {
                // Tối ưu: eager load và chỉ select fields cần thiết
                $listSale = $groupUs->users()->select('id', 'real_name', 'profile_image')->get();
                
                foreach ($listSale as $sale) {
                    $saleArray = is_object($sale) ? $sale->toArray() : $sale;
                    $saleId = $saleArray['id'] ?? null;
                    $cacheKey = 'cskhdt:v2:' . $saleId . ':' . md5(json_encode($dataFilter));
                    
                    $data = Cache::remember($cacheKey, 120, function () use ($saleArray, $dataFilter) {
                        return $this->getReportUserSaleV2($saleArray, $dataFilter, false, true);
                    });
                    
                    if ($data) {
                        $list[] = $data;
                    }
                }
            }
        } else if (isset($dataFilter['sale'])) {
            /**
             * bắt đầu lọc 
             * chọn 1 sale
            */
            $sale = Helper::getSaleById($dataFilter['sale']);
            $saleId = is_array($sale) ? ($sale['id'] ?? null) : ($sale->id ?? null);
            $cacheKey = 'cskhdt:v2:' . $saleId . ':' . md5(json_encode($dataFilter));
            
            $data = Cache::remember($cacheKey, 120, function () use ($sale, $dataFilter) {
                return $this->getReportUserSaleV2($sale, $dataFilter, false, true);
            });
            
            if ($data) {
                $list[] = $data;
            }
        } else {
            /** chọn tất cả sale */
            $checkAll = isFullAccess(Auth::user()->role);
            $isLeadSale = Helper::isLeadSale(Auth::user()->role);
            if ($checkAll || $isLeadSale) {
                $listSale = Helper::getListSaleByGroupWork(5);
                // Xử lý cả Query Builder và Collection
                if ($listSale instanceof \Illuminate\Database\Eloquent\Builder) {
                    $listSale = $listSale->select('id', 'real_name', 'profile_image')->get();
                }
                
                foreach ($listSale as $sale) {
                    $saleArray = is_object($sale) ? $sale->toArray() : $sale;
                    $saleId = $saleArray['id'] ?? null;
                    $cacheKey = 'cskhdt:v2:' . $saleId . ':' . md5(json_encode($dataFilter));
                    
                    $data = Cache::remember($cacheKey, 120, function () use ($saleArray, $dataFilter) {
                        return $this->getReportUserSaleV2($saleArray, $dataFilter, false, true);
                    });
                    
                    if ($data) {
                        $list[] = $data;
                    }
                }
            } else {
                /**sale đang xem thông tin */
                $sale = Auth::user();
                $saleId = $sale->id ?? null;
                $cacheKey = 'cskhdt:v2:' . $saleId . ':' . md5(json_encode($dataFilter));
                
                $data = Cache::remember($cacheKey, 120, function () use ($sale, $dataFilter) {
                    return $this->getReportUserSaleV2($sale, $dataFilter, false, true);
                });
                
                if ($data) {
                    $list[] = $data;
                }
            }
        }

        // Lọc bỏ các record có tất cả giá trị = 0
        $list = array_filter($list, function($item) {
            $newCustomer = $item['new_customer'] ?? [];
            $oldCustomer = $item['old_customer'] ?? [];
            $summaryTotal = $item['summary_total'] ?? [];
            
            $newOrder = $newCustomer['order'] ?? 0;
            $newContact = $newCustomer['contact'] ?? 0;
            $newRate = $newCustomer['rate'] ?? 0;
            
            $oldOrder = $oldCustomer['order'] ?? 0;
            $oldContact = $oldCustomer['contact'] ?? 0;
            $oldRate = $oldCustomer['rate'] ?? 0;
            
            $total = $summaryTotal['total'] ?? 0;
            $avg = $summaryTotal['avg'] ?? 0;
            $rate = $summaryTotal['rate'] ?? 0;
            
            return ($newOrder > 0 || $newContact > 0 || $newRate > 0 || 
                    $oldOrder > 0 || $oldContact > 0 || $oldRate > 0 || 
                    $total > 0 || $avg > 0 || $rate > 0);
        });
        
        // Sắp xếp các record giảm dần theo summary_total.total
        usort($list, function($a, $b) {
            $totalA = $a['summary_total']['total'] ?? 0;
            $totalB = $b['summary_total']['total'] ?? 0;
            return $totalB <=> $totalA;
        });
        
        // Tính tổng TRƯỚC trên TẤT CẢ records (không bị giới hạn bởi $show)
        $totalSum = $avgSum = $newContact = $newOrder = $newRate = $newProduct = $newTotal  = $oldTotal = $oldProduct = $oldRate  = $oldContact = $oldOrder= 0;
        $sumNewCustomer = $sumOldCustomer = [
            'contact' => 0,
            'order' => 0,
            'rate' => 0,
            'product' => 0,
            'total' => 0,
            'avg' => 0,
        ];
        
        $newProduct = $newTotal = $oldProduct = $oldTotal = $oldRate = 0;
        foreach ($list as $k => $data) {
            if (isset($data['new_customer'])) {
                $newContact += $data['new_customer']['contact'];
                $newOrder += $data['new_customer']['order'];

                if ($data['new_customer']['contact'] > 0 || $data['new_customer']['order'] > 0) {
                    $newProduct += $data['new_customer']['product'];
                    $newTotal += ($data['new_customer']['total']);
                }
            }
           
             if (isset($data['old_customer'])) {
                $oldContact += $data['old_customer']['contact'];
                $oldOrder += $data['old_customer']['order'];

                if ($data['old_customer']['contact'] > 0 || $data['old_customer']['order'] > 0) {
                    $oldRate += $data['old_customer']['rate'];
                    $oldProduct += $data['old_customer']['product'];
                    $oldTotal += ($data['old_customer']['total']);
                }
            }
        }
    
        $sumNewCustomer['contact'] = $newContact;
        $sumNewCustomer['order'] = $newOrder;
        if ($newContact > 0) {
            $newRate = $newOrder / $newContact * 100;
            $sumNewCustomer['rate'] = round($newRate, 2);
        }
    
        $sumNewCustomer['product'] = $newProduct;
        $sumNewCustomer['total'] = $newTotal;
        $sumNewCustomer['avg'] = ($newOrder != 0) ? round($newTotal/$newOrder, 0) : 0;
        $sumOldCustomer['contact'] = $oldContact;
        $sumOldCustomer['order'] = $oldOrder;
        if ($oldContact > 0) {
            $oldRate = $oldOrder / $oldContact * 100;
            $sumOldCustomer['rate'] = round($oldRate, 2);
        }
    
        $sumOldCustomer['rate'] = round($oldRate, 2);
        $sumOldCustomer['product'] = $oldProduct;
        $sumOldCustomer['total'] = $oldTotal;
        $sumOldCustomer['avg'] = ($oldOrder != 0) ?  round($oldTotal/$oldOrder, 0) : 0;
        $totalSum = $oldTotal + $newTotal;
        if ($oldOrder + $newOrder) {
            $avgSum = round(($totalSum / ($oldOrder + $newOrder)), 0);
        }

        $rateSumX = 0;
        $sumContactX =  $sumOldCustomer['contact'];
        $sumOrderX =  $sumNewCustomer['order'] + $sumOldCustomer['order'];
        if ($sumContactX > 0) {
            $rateSumX = $sumOrderX / $sumContactX * 100;
        } else {
            $rateSumX = $sumOrderX * 100;
        }
       
        $rateSumX = round($rateSumX, 2);
        $result['trSum'] = [
            'new_customer' => $sumNewCustomer,
            'old_customer' => $sumOldCustomer,
            'sumary_total' => [
                'total' => $totalSum,
                'avg' => $avgSum,
                'rate' => $rateSumX,
            ]
        ];
        
        // Giới hạn số record hiển thị theo tham số $show (sau khi đã tính tổng tất cả)
        $result['data'] = array_slice($list, 0, $show);
        
        if (count($result['data']) == 0) {
            return $result['data'];
        }

        return $result;
    }

    public function ajaxFilterDashboard(Request $req) 
    {
        $result = $dataFilter = $list = [];
        if ($req->date && getType($req->date) == 'string') {
            $dataFilter['daterange'] = explode('-', $req->date);
        }

        if (isset($req->status)) {
            $dataFilter['status'] = $req->status;
        }

        $category = $req->category;
        if ($category) {
            $dataFilter['category'] = $category;
        }

        $product = $req->product;
        if ($req->product && $product != 999) {
            $dataFilter['product'] = $product;
        }

        $sale = $req->sale;
        if ($sale && $sale != 999) {
            $dataFilter['sale'] = $req->sale;
        }

        $mkt = $req->mkt;
        if ($mkt) {
            $dataFilter['mkt'] = $mkt;
        }

        $src = $req->src;
        if ($src) {
            $dataFilter['src'] = $src;
        } 

        $group = $req->group;
        if ($group) {
            $dataFilter['group'] = $group;
        }

        $show = $req->show;
        if ($show) {
            $dataFilter['show'] = $show;
        } else {
            $show = 20;
        }

        $groupUser = $req->groupUser;
        $list = [];
        if ($groupUser && $groupUser != 999) {
            $groupUs = GroupUser::find($groupUser);
            
            if ($groupUs) {
                $listSale = $groupUs->users()
                    ->select('id', 'real_name')
                    // ->limit($show)
                    ->get()
                    ->toArray();

                foreach ($listSale as $sale) {
                    $cacheKey = 'dash:v2:' . ($sale['id'] ?? null) . ':' . md5(json_encode($dataFilter));
                    $data = Cache::remember($cacheKey, 120, function () use ($sale, $dataFilter) {
                        return $this->getReportUserSaleV2($sale, $dataFilter);
                    });
                    if ($data) {
                        $list[] = $data;
                    }
                }
            }
        } else if (isset($dataFilter['sale'])) {
            /**
             * bắt đầu lọc, chọn 1 sale
            */
            $sale = Helper::getSaleById($dataFilter['sale']);
            $saleId = is_array($sale) ? ($sale['id'] ?? null) : ($sale->id ?? null);
            $cacheKey = 'dash:v2:' . $saleId . ':' . md5(json_encode($dataFilter));
            $data = Cache::remember($cacheKey, 120, function () use ($sale, $dataFilter) {
                return $this->getReportUserSaleV2($sale, $dataFilter);
            });
            if ($data) {
                $list[] = $data;
            }

        } else {
            /** chọn tất cả sale */         
            $checkAll = isFullAccess(Auth::user()->role);
            $isLeadSale = Helper::isLeadSale(Auth::user()->role);
            if ($checkAll || $isLeadSale) {
                $listGroup = GroupUser::where('status', 1)->where('type', 'sale')->get();
                $listSaleAllow = [];
                foreach ($listGroup as $gr) {
                    /** ko lấy list nhóm cskh */
                    if ($gr->id == 5) {
                        continue;
                    }
                    // Bỏ limit để lấy TẤT CẢ sales (sẽ limit khi hiển thị sau)
                    $listSaleResult = Helper::getListSaleV3(Auth::user(), $isLeadSale, $gr->id);
                    
                    // Xử lý cả Query Builder và Collection/Array
                    if ($listSaleResult instanceof \Illuminate\Database\Eloquent\Builder) {
                        // Query Builder - lấy tất cả không limit
                        $listSale = $listSaleResult
                            ->select('id', 'real_name')
                            ->get()
                            ->toArray();
                    } else {
                        // Collection hoặc Array - lấy tất cả
                        $listSaleArray = $listSaleResult instanceof \Illuminate\Support\Collection 
                            ? $listSaleResult->toArray() 
                            : $listSaleResult;
                        
                        // Chỉ lấy các field cần thiết, không limit
                        $listSale = [];
                        foreach ($listSaleArray as $sale) {
                            if (is_object($sale)) {
                                $listSale[] = ['id' => $sale->id ?? null, 'real_name' => $sale->real_name ?? ''];
                            } else {
                                $listSale[] = ['id' => $sale['id'] ?? null, 'real_name' => $sale['real_name'] ?? ''];
                            }
                        }
                    }
                    
                    $listSaleAllow = array_merge($listSaleAllow, $listSale);
                }

                foreach ($listSaleAllow as $sale) {
                    $cacheKey = 'dash:v2:' . ($sale['id'] ?? null) . ':' . md5(json_encode($dataFilter));
                    $data = Cache::remember($cacheKey, 120, function () use ($sale, $dataFilter) {
                        return $this->getReportUserSaleV2($sale, $dataFilter);
                    });

                    if ($data) {
                        $list[] = $data;
                    }
                }
            } else if ((Auth::user()->is_CSKH || Auth::user()->is_sale) && !Helper::isCskhDt(Auth::user())) {

                /**sale đang xem thông tin */
                $sale = Auth::user();
                $saleId = $sale->id ?? null;
                $cacheKey = 'dash:v2:' . $saleId . ':' . md5(json_encode($dataFilter));
                $data = Cache::remember($cacheKey, 120, function () use ($sale, $dataFilter) {
                    return $this->getReportUserSaleV2($sale, $dataFilter);
                });
                if ($data) {
                    $list[] = $data;
                }
            }
        }
  
        // Lọc bỏ các record có tất cả giá trị = 0
        $list = array_filter($list, function($item) {
            $newCustomer = $item['new_customer'] ?? [];
            $oldCustomer = $item['old_customer'] ?? [];
            $summaryTotal = $item['summary_total'] ?? [];
            
            // Kiểm tra nếu tất cả giá trị quan trọng đều = 0
            $newOrder = $newCustomer['order'] ?? 0;
            $newContact = $newCustomer['contact'] ?? 0;
            $newRate = $newCustomer['rate'] ?? 0;
            
            $oldOrder = $oldCustomer['order'] ?? 0;
            $oldContact = $oldCustomer['contact'] ?? 0;
            $oldRate = $oldCustomer['rate'] ?? 0;
            
            $total = $summaryTotal['total'] ?? 0;
            $avg = $summaryTotal['avg'] ?? 0;
            $rate = $summaryTotal['rate'] ?? 0;
            
            // Giữ lại nếu có ít nhất 1 giá trị > 0
            return ($newOrder > 0 || $newContact > 0 || $newRate > 0 || 
                    $oldOrder > 0 || $oldContact > 0 || $oldRate > 0 || 
                    $total > 0 || $avg > 0 || $rate > 0);
        });
        
        // Sắp xếp các record giảm dần theo summary_total.total
        usort($list, function($a, $b) {
            $totalA = $a['summary_total']['total'] ?? 0;
            $totalB = $b['summary_total']['total'] ?? 0;
            return $totalB <=> $totalA; // Sắp xếp giảm dần
        });
        
        // Tính tổng TRƯỚC trên TẤT CẢ records (không bị giới hạn bởi $show)
        $totalSum = $avgSum = $newContact = $newOrder = $newRate = $newProduct = $newTotal  = $oldTotal = $oldProduct = $oldRate  = $oldContact = $oldOrder= 0;
        $sumNewCustomer = $sumOldCustomer = [
            'contact' => 0,
            'order' => 0,
            'rate' => 0,
            'product' => 0,
            'total' => 0,
            'avg' => 0,
        ];
        
        $newProduct = $newTotal = $oldProduct = $oldTotal = $oldRate = 0;
        foreach ($list as $data) {
            if (isset($data['new_customer'])) {
                $newContact += $data['new_customer']['contact'];
                $newOrder += $data['new_customer']['order'];

                if ($data['new_customer']['contact'] > 0 || $data['new_customer']['order'] > 0) {
                    $newProduct += $data['new_customer']['product'];
                    $newTotal += ($data['new_customer']['total']);
                }
            } if (isset($data['old_customer'])) {
                $oldContact += $data['old_customer']['contact'];
                $oldOrder += $data['old_customer']['order'];

                if ($data['old_customer']['contact'] > 0 || $data['old_customer']['order'] > 0) {
                    $oldRate += $data['old_customer']['rate'];
                    $oldProduct += $data['old_customer']['product'];
                    $oldTotal += ($data['old_customer']['total']);
                }
            }
        }
    
        $sumNewCustomer['contact'] = $newContact;
        $sumNewCustomer['order'] = $newOrder;
        if ($newContact > 0) {
            $newRate = $newOrder / $newContact * 100;
            $sumNewCustomer['rate'] = round($newRate, 2);
        }
    
        $sumNewCustomer['product'] = $newProduct;
        $sumNewCustomer['total'] = $newTotal;
        $sumNewCustomer['avg'] = ($newOrder != 0) ? round($newTotal/$newOrder, 0) : 0;
        $sumOldCustomer['contact'] = $oldContact;
        $sumOldCustomer['order'] = $oldOrder;
        if ($oldContact > 0) {
            $oldRate = $oldOrder / $oldContact * 100;
            $sumOldCustomer['rate'] = round($oldRate, 2);
        }
    
        $sumOldCustomer['rate'] = round($oldRate, 2);
        $sumOldCustomer['product'] = $oldProduct;
        $sumOldCustomer['total'] = $oldTotal;
        $sumOldCustomer['avg'] = ($oldOrder != 0) ?  round($oldTotal/$oldOrder, 0) : 0;
        $totalSum = $oldTotal + $newTotal;
        if ($oldOrder + $newOrder) {
            $avgSum = round(($totalSum / ($oldOrder + $newOrder)), 0);
        }

        $rateSumX = 0;
        $sumContactX =  $sumNewCustomer['contact'];
        $sumOrderX =  $sumNewCustomer['order'] + $sumOldCustomer['order'];
        if ($sumContactX > 0) {
            $rateSumX = $sumOrderX / $sumContactX * 100;
        } else {
            $rateSumX = $sumOrderX * 100;
        }
       
        $rateSumX = round($rateSumX, 2);
        $result['trSum'] = [
            'new_customer' => $sumNewCustomer,
            'old_customer' => $sumOldCustomer,
            'sumary_total' => [
                'total' => $totalSum,
                'avg' => $avgSum,
                'rate' => $rateSumX,
            ]
        ];
        
        // Giới hạn số record hiển thị theo tham số $show (sau khi đã tính tổng tất cả)
        $result['data'] = array_slice($list, 0, $show);
        
        if (count($result['data']) == 0) {
            return $result['data'];
        }

        return $result;
    }

    public function ajaxFilterDashboardDigitalV3(Request $req)
    {
        $result = $dataFilter = [];
        if ($req->date) {
            $dataFilter['daterange'] = $req->date;
        }

        // Tối ưu: xử lý filter parameters một cách gọn gàng
        $filters = [
            'status' => $req->status,
            'category' => $req->category,
            'product' => $req->product,
            'sale' => $req->sale,
            'mkt' => $req->mkt,
            'src' => $req->src,
            'group' => $req->group,
            'groupUser' => $req->groupUser,
            'groupDigital' => $req->groupDigital,
        ];
        
        foreach ($filters as $key => $value) {
            if ($value && $value != 999) {
                $dataFilter[$key] = $value;
                if ($key === 'src') {
                    $newFilter['src'] = $value;
                }
            }
        }
        
        $show = $req->show ?? 20;
        // if ($groupDigital && $groupDigital != 999) {
        //     $groupDi = GroupUser::find($groupDigital);
        //     if ($groupDi) {
        //         $listDigital = $groupDi->users->pluck('id')->toArray();
        //     }
        // } else {
        //     $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
        //     $checkAll = isFullAccess(Auth::user()->role);

        //     if ($checkAll) {
        //         $listDigital = Helper::getListDigital()->pluck('id')->toArray();
        //     } else if ($isLeadDigital) {
        //         $listDigital =  Helper::getListMktByLeadTeam(Auth::user(), $isLeadDigital)->pluck('id')->get()->toArray();
        //     } else if (!$checkAll && !$isLeadDigital && Auth::user()->is_digital == 1 && !Auth::user()->is_sale) {
        //         $listDigital[] = Auth::user()->id;
        //     }
        // }

        // Thêm cache cho getReportUserDigitalV3
        // $cacheKey = 'digital:v3:' . md5(json_encode($dataFilter));
        // $listResult = Cache::remember($cacheKey, 120, function () use ($dataFilter) {
        //     return $this->getReportUserDigitalV3($dataFilter);
        // });
        // dd($listResult);
        // Lọc bỏ các record có tất cả giá trị = 0
        // Nhưng giữ lại nếu record có 'name' (có thể là digital user cần hiển thị)
        $listResult = $this->getReportUserDigitalV3($dataFilter);

        $listResult = array_filter($listResult, function($item) {
            // Nếu có name, luôn giữ lại (có thể là digital user cần hiển thị)
            if (isset($item['name']) && !empty($item['name'])) {
                return true;
            }
            
            $newCustomer = $item['new_customer'] ?? [];
            $oldCustomer = $item['old_customer'] ?? [];
            
            $newOrder = $newCustomer['count_order'] ?? 0;
            $newContact = $newCustomer['contact'] ?? 0;
            $newTotal = $newCustomer['total'] ?? 0;
            $newProduct = $newCustomer['product'] ?? 0;
            $newRate = $newCustomer['rate'] ?? 0;
            $newAvg = $newCustomer['avg'] ?? 0;
            
            $oldOrder = $oldCustomer['count_order'] ?? 0;
            $oldContact = $oldCustomer['contact'] ?? 0;
            $oldTotal = $oldCustomer['total'] ?? 0;
            $oldProduct = $oldCustomer['product'] ?? 0;
            $oldRate = $oldCustomer['rate'] ?? 0;
            $oldAvg = $oldCustomer['avg'] ?? 0;
            
            // Giữ lại nếu có ít nhất 1 giá trị > 0
            return ($newOrder > 0 || $newContact > 0 || $newTotal > 0 || $newProduct > 0 || $newRate > 0 || $newAvg > 0 ||
                    $oldOrder > 0 || $oldContact > 0 || $oldTotal > 0 || $oldProduct > 0 || $oldRate > 0 || $oldAvg > 0);
        });
        
        // Reset keys để đảm bảo array có index liên tục (cần thiết cho array_slice)
        $listResult = array_values($listResult);
        
        // Sắp xếp các record giảm dần theo total
        usort($listResult, function($a, $b) {
            $totalA = ($a['new_customer']['total'] ?? 0) + ($a['old_customer']['total'] ?? 0);
            $totalB = ($b['new_customer']['total'] ?? 0) + ($b['old_customer']['total'] ?? 0);
            return $totalB <=> $totalA;
        });
        
        // Tính tổng TRƯỚC trên TẤT CẢ records (không bị giới hạn bởi $show)
        $totalSum = $avgSum = $newContact = $newOrder = $newRate = $newProduct = $newTotal = 0;
        $oldTotal = $oldProduct = $oldRate = $oldContact = $oldOrder = 0;
        $sumNewCustomer = $sumOldCustomer = [
            'contact' => 0,
            'count_order' => 0,
            'rate' => 0,
            'product' => 0,
            'total' => 0,
            'avg' => 0,
        ];
        $checkAll = isFullAccess(Auth::user()->role);
        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
        if ($checkAll || $isLeadDigital) {
            foreach ($listResult as $data) {
                if (isset($data['new_customer'])) {
                    $newContact += $data['new_customer']['contact'] ?? 0;
                    $newOrder += $data['new_customer']['count_order'] ?? 0;
    
                    if (($data['new_customer']['contact'] ?? 0) > 0 || ($data['new_customer']['count_order'] ?? 0) > 0) {
                        $newProduct += $data['new_customer']['product'] ?? 0;
                        $newTotal += $data['new_customer']['total'] ?? 0;
                    }
                }
                
                if (isset($data['old_customer'])) {
                    $oldContact += $data['old_customer']['contact'] ?? 0;
                    $oldOrder += $data['old_customer']['count_order'] ?? 0;
    
                    if (($data['old_customer']['contact'] ?? 0) > 0 || ($data['old_customer']['count_order'] ?? 0) > 0) {
                        $oldRate += $data['old_customer']['rate'] ?? 0;
                        $oldProduct += $data['old_customer']['product'] ?? 0;
                        $oldTotal += $data['old_customer']['total'] ?? 0;
                    }
                }
            }
        
            $sumNewCustomer['contact'] = $newContact;
            $sumNewCustomer['count_order'] = $newOrder;
            if ($newContact > 0) {
                $newRate = $newOrder / $newContact * 100;
                $sumNewCustomer['rate'] = round($newRate, 2);
            }
        
            $sumNewCustomer['product'] = $newProduct;
            $sumNewCustomer['total'] = $newTotal;
            $sumNewCustomer['avg'] = ($newOrder != 0) ? round($newTotal/$newOrder, 0) : 0;
    
            $sumOldCustomer['contact'] = $oldContact;
            $sumOldCustomer['count_order'] = $oldOrder;
            if ($oldContact > 0) {
                $oldRate = $oldOrder / $oldContact * 100;
                $sumOldCustomer['rate'] = round($oldRate, 2);
            }
        
            $sumOldCustomer['rate'] = round($oldRate, 2);
            $sumOldCustomer['product'] = $oldProduct;
            $sumOldCustomer['total'] = $oldTotal;
            $sumOldCustomer['avg'] = ($oldOrder != 0) ?  round($oldTotal/$oldOrder, 0) : 0;
            $totalSum = $oldTotal + $newTotal;
            if ($oldOrder + $newOrder) {
                $avgSum = round(($totalSum / ($oldOrder + $newOrder)), 0);
            }
    
            $rateSumX = 0;
            $sumContactX =  $sumNewCustomer['contact'];
            $sumOrderX =  $sumNewCustomer['count_order'] + $sumOldCustomer['count_order'];
            if ($sumContactX > 0) {
                $rateSumX = $sumOrderX / $sumContactX * 100;
            } else {
                $rateSumX = $sumOrderX * 100;
            }
    
            $rateSumX = round($rateSumX, 2);
            $result['trSum'] = [
                'new_customer' => $sumNewCustomer,
                'old_customer' => $sumOldCustomer,
                'sumary_total' => [
                    'total' => $totalSum,
                    'avg' => $avgSum,
                    'rate' => $rateSumX,
                ]
            ];
        }
        // Giới hạn số record hiển thị theo tham số $show (sau khi đã tính tổng tất cả)
        $result['data'] = array_values(array_slice($listResult, 0, (int)$show));
        
        if (count($result['data']) == 0) {
            return $result['data'];
        }

        return $result;
    }

    public function getListMktReportOrder($req, $listSrc)
    {
        if (!$listSrc) {
            return [];
        }

        $ordersController = new OrdersController();
        $userAdmin = User::find(1);

        if (isset($req['daterange'])) {
            $date = $req['daterange'];
            if (getType($req['daterange']) == 'string') {
                $req['daterange'] = explode('-', $date);
            }
        }

        $listOrders = $ordersController->getListOrderByPermisson($userAdmin, $req);
        
        // Tối ưu: eager load relationships để tránh N+1 queries
        $listOrders = $listOrders->with(['saleCare.getSrcPage.userDigital:id,real_name']);
        
        foreach ($listOrders->get() as $order) {
            if (empty($order->saleCare) || empty($order->saleCare->getSrcPage) || empty($order->saleCare->getSrcPage->userDigital)) {
                Log::channel('c')->info('Mã đơn hàng - data ko xác định data/ nguồn: ' . $order->id . '-' . $order->sale_care);
                continue;
            }
            
            $sc = $order->saleCare;
            $srcPageOfOrder = $sc->getSrcPage;
            $srcId = $srcPageOfOrder->id;
            $digitalSrc = $srcPageOfOrder->userDigital;
            if (isset($listSrc[$digitalSrc->id])) {
                    if (($sc->old_customer == 0 || $sc->old_customer == 2) && isset($listSrc[$digitalSrc->id]['new_customer'])) {
                        $listSrc[$digitalSrc->id]['new_customer']['total'] += $order->total;
                        $listSrc[$digitalSrc->id]['new_customer']['product'] += $order->qty;
                        $listSrc[$digitalSrc->id]['new_customer']['count_order'] ++;
                    } else if (isset($listSrc[$digitalSrc->id]['old_customer'])) {
                        $listSrc[$digitalSrc->id]['old_customer']['total'] += $order->total;
                        $listSrc[$digitalSrc->id]['old_customer']['product'] += $order->qty;
                        $listSrc[$digitalSrc->id]['old_customer']['count_order'] ++;
                    }

                } else {
                    // Tối ưu: cache listSrcIds để tránh gọi Helper nhiều lần
                    if (!isset($listSrcIdsCache)) {
                        $listSrcIdsCache = Helper::getSrcByPermission(Auth::user(), $req);
                    }
                    
                    if (in_array($srcId, $listSrcIdsCache)) {
                        $listSrc[$digitalSrc->id]['name'] = $digitalSrc->real_name ?? '';
                        if ($sc->old_customer == 0 || $sc->old_customer == 2) {
                            $listSrc[$digitalSrc->id]['new_customer'] = [
                                'total' => $order->total,
                                'count_order' => 1,
                                'product' => $order->qty,
                                'contact' => 0,
                                'id' => $digitalSrc->id
                            ];
                        } else {
                            $listSrc[$digitalSrc->id]['old_customer'] = [
                                'total' => $order->total,
                                'count_order' => 1,
                                'product' => $order->qty,
                                'contact' => 0,
                                'id' => $digitalSrc->id
                            ];
                        }
                    } else {
                        Log::channel('c')->info('Mã đơn hàng - data ko xác định data/ nguồn: ' . $order->id . '-' . $order->sale_care);
                    }
                }
        }
 
        // Bỏ logic array_slice ở đây vì đã xử lý ở ajaxFilterDashboardDigitalV3
        foreach ($listSrc as $k => $data) {
            $orderNew = $totalNew = $contactNew = $avgNew = $rateNew = 0;
            $orderOld = $totalOld = $contactOld = $avgOld = $rateOld = 0;
            /** new */
            if (!isset($data['new_customer'])) {
                $new = $listSrc[$k]['new_customer'] = [
                    'count_order' => 0,
                    'total' => 0,
                    'contact' => 0,
                    'qty' => 0,
                    'product' => 0,
                ];
            } else {
                $new = $data['new_customer'];
            }
            
            $orderNew = $new['count_order'];
            $totalNew = $new['total'];
            $contactNew = $new['contact'];
            if ($orderNew > 0) {
                $avgNew =  $totalNew / $orderNew;
            }
            if ($contactNew > 0) {
                $rateNew =  round($orderNew / $contactNew * 100, 2);
            } else {
                $rateNew =  round($orderNew * 100, 2);
            }
            $listSrc[$k]['new_customer']['avg'] = round($avgNew, 0);
            $listSrc[$k]['new_customer']['rate'] = round($rateNew, 2);

            /** old */
            $old = (isset($data['old_customer'])) ? $data['old_customer'] : [];
            $orderOld = $old['count_order'] ?? 0;
            $totalOld = $old['total'] ?? 0;
            $contactOld = $old['contact'] ?? 0;
            if ($orderOld > 0) {
                $avgOld =  $totalOld / $orderOld;
            }
            if ($contactOld > 0) {
                $rateOld = round($orderOld / $contactOld * 100, 2);
            } else {
                $rateOld = round($orderOld * 100, 2);
            }
            $listSrc[$k]['old_customer']['avg'] = round($avgOld, 0);
            $listSrc[$k]['old_customer']['rate'] = round($rateOld, 2);

            $avgSum = $rateSum = $totalSum = 0;
            $totalSum = $totalNew + $totalOld;
            $orderSum = $orderOld + $orderNew;
            if ($orderSum > 0) {
                $avgSum = $totalSum / $orderSum;
            } else {
                $avgSum = $totalSum;
            }

            $rateSum = 0;
            if ($contactNew > 0) {
                $rateSum = $orderSum / $contactNew * 100;
            } else {
                $rateSum = $orderSum * 100;
            }
            $listSrc[$k]['summary_total'] = [
                'total' => round($totalSum, 0),
                'avg' => round($avgSum, 0),
                'rate' => round($rateSum, 2)
            ];
        }

        return $listSrc;
    }

    public function getReportUserDigitalV3($dataFilter) 
    {
        $listFiltrSrc = $this->getListSaleCare($dataFilter);
        // dd($listFiltrSrc);
        return $this->getListMktReportOrder($dataFilter, $listFiltrSrc);
    }

    public function getDataDigitalV4($dataFilter)
    {
        $countOrder = $countOrderOld = $countOrderNew = $countSaleCareOld = $countSaleCareNew = $avgOrders = 0;
        $result = $newCustomer = $oldCustomer = []; 
        
        $filterNew = $filterOld = [];
        $listOrder = $this->getOrdersReport(Auth::user(), $dataFilter, true);
        $countOrder = $listOrder->count();

        if ($countOrder) {
            $listSrcIdsOfMKT = SrcPage::select('id')->where('user_digital', $dataFilter['mkt'])->get()->pluck('id')->toArray();
            foreach ($listOrder->get() as $k => $order) {
                $salecare = $order->saleCare;
                if ($salecare) {
                    $srcId = $salecare->src_id;
                    if (in_array($srcId, $listSrcIdsOfMKT)) {
                        $typeCutomer = 0;
                        $typeCutomer = $salecare->old_customer;
                        if ($typeCutomer == 2) {
                            /** check khách cũ/khách mới khi type = 2 (hotline) */
                            $phone = $order->phone;
                            $isOldCustomer = SaleCare::where('phone', $phone)->where('old_customer', 1)->first();
                            if ($isOldCustomer) {
                                $typeCutomer = 1;
                            }
                        } if ($typeCutomer == 1) {
                            $filterOld[] = $order->id;
                        } if ($typeCutomer == 0) {
                            $filterNew[] = $order->id;
                        } 
                    }
                }
            }
        }
       
        if(count($filterNew) == 0 && count($filterOld) == 0) {
            $newCustomer['order'] = 0;
            $oldCustomer['order'] = 0;
        } else {
            /** new */
            if (count($filterNew) == 0) {
                $newCustomer['order'] = 0;
                $newCustomer['total'] = 0;
                $newCustomer['product'] = 0;
                $newCustomer['avg'] = 0;
                $newCustomer['rate'] = 0;
            } else {
                $listOrderNew = Orders::select('qty', 'total')->whereIn('id', $filterNew);
                $countOrderNew = $listOrderNew->count();
                $newCustomer['order'] = $countOrderNew;
                $newCustomer['total'] = round($listOrderNew->sum('total'), 0);
                $newCustomer['product'] = $listOrderNew->sum('qty');

                if ($countOrderNew > 0) {
                    $avgOrders = $newCustomer['total'] / $countOrderNew;
                    $newCustomer['avg'] = round($avgOrders, 0);
                }
            }

            /** old */
            if (count($filterOld) == 0) {
                $oldCustomer['order'] = 0;
                $oldCustomer['total'] = 0;
                $oldCustomer['product'] = 0;
                $oldCustomer['avg'] = 0;
                $oldCustomer['rate'] = 0;
            } else {
                $listOrderOld = Orders::select('qty', 'total')->whereIn('id', $filterOld);
                $countOrderOld = $listOrderOld->count();
                $oldCustomer['order'] = $countOrderOld;
                $oldCustomer['total'] = round($listOrderOld->sum('total'), 0);
                $oldCustomer['product'] = $listOrderOld->sum('qty');

                if ($countOrderOld > 0) {
                    $avgOrdersOld = round($oldCustomer['total'] / $countOrderOld, 0);
                    $oldCustomer['avg'] = round($avgOrdersOld, 0);
                }
            }
        }

        $saleCare = $this->getSalesReport(Auth::user(), $dataFilter);
        $countSaleCare = $saleCare->count();
        
        if ($countSaleCare == 0) {
            $newCustomer['contact'] = 0;
            $oldCustomer['contact'] = 0;
        } else {
            if ($countOrder == 0) {
                $newCustomer['order'] = 0;
                $newCustomer['total'] = 0;
                $newCustomer['product'] = 0;
                $newCustomer['avg'] = 0;
                $newCustomer['rate'] = 0;

                $oldCustomer['order'] = 0;
                $oldCustomer['total'] = 0;
                $oldCustomer['product'] = 0;
                $oldCustomer['avg'] = 0;
                $oldCustomer['rate'] = 0;
            }

            $saleCareIDs = $saleCare->get()->pluck('id')->toArray();
            $saleCareOld = SaleCare::select('id')->whereIn('id', $saleCareIDs)->where('old_customer', 1);  
            $countSaleCareOld = $saleCareOld->count();
            $oldCustomer['contact'] = $countSaleCareOld;
           
            $saleCareNew = SaleCare::select('id')->whereIn('id', $saleCareIDs)->whereIn('old_customer', [0,2]);
            $countSaleCareNew = $saleCareNew->count();
            $newCustomer['contact'] = $countSaleCareNew;
        }

        /** tỷ lệ chốt = số đơn/số data */
        /** new */
        if ($countSaleCareNew == 0) {
            $rateSuccessNew = $countOrderNew * 100;
        } else {
            $rateSuccessNew = $countOrderNew / $countSaleCareNew * 100;
        }
        $newCustomer['rate'] = round($rateSuccessNew, 2);

        /** old */
        if ($countSaleCareOld == 0) {
            $rateSuccessOld = $countOrderOld * 100;
        } else {
            $rateSuccessOld = $countOrderOld / $countSaleCareOld * 100;
        }
        $oldCustomer['rate'] = round($rateSuccessOld, 2);
       
        $result = [
            'new_customer' => $newCustomer, 
            'old_customer' => $oldCustomer
        ];

       return $result;
    }

    public function getReportUserDigital($digital, $dataFilter) 
    {
        $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
        $dataFilter['mkt'] = $digital['id'];
        $result = $this->getDataDigitalV3($dataFilter);
        $result['name'] = $digital['real_name'];
        $newCustomer = $result['new_customer'];
        $oldCustomer = $result['old_customer'];

        if (count($newCustomer) == 3 && count($oldCustomer) == 3) {
            return false;
        }

        if (isset($newCustomer['total'])) {
            $newTotal = Helper::stringToNumberPrice($newCustomer['total']);
        } if (isset($oldCustomer['total'])) {
            $oldTotal = Helper::stringToNumberPrice($oldCustomer['total']);
        }
        
        $oldCountOrder = $oldCustomer['order'];
        $newCountOrder = $newCustomer['order'];
        $totalSum = $newTotal + $oldTotal;
        if ($newCountOrder != 0 || $oldCountOrder != 0) {
            $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
        }

        $rateSum = 0;
        $contactSum = $newCustomer['contact'];
        $orderSum = $oldCustomer['order'] + $newCustomer['order'];
        if ($contactSum > 0) {
            $rateSum = $orderSum / $contactSum * 100;
        } else {
            $rateSum = $orderSum * 100;
        }

        $result['summary_total'] = [
            'total' => round($totalSum, 0),
            'avg' => round($avgSum, 0),
            'rate' => round($rateSum, 2)
        ];

        return $result;
    }

        public function getDataDigitalV3($dataFilter)
    {
        $countOrder = $countOrderOld = $countOrderNew = $countSaleCareOld = $countSaleCareNew = $avgOrders = 0;
        $result = $newCustomer = $oldCustomer = []; 
        
        $filterNew = $filterOld = [];
        $listOrder = $this->getOrdersReport(Auth::user(), $dataFilter, true);
        $countOrder = $listOrder->count();

        if ($countOrder) {
            $listSrcIdsOfMKT = SrcPage::select('id')->where('user_digital', $dataFilter['mkt'])->get()->pluck('id')->toArray();
            foreach ($listOrder->get() as $k => $order) {
                $salecare = $order->saleCare;
                if ($salecare) {
                    $srcId = $salecare->src_id;
                    if (in_array($srcId, $listSrcIdsOfMKT)) {
                        $typeCutomer = 0;
                        $typeCutomer = $salecare->old_customer;
                        if ($typeCutomer == 2) {
                            /** check khách cũ/khách mới khi type = 2 (hotline) */
                            $phone = $order->phone;
                            $isOldCustomer = SaleCare::where('phone', $phone)->where('old_customer', 1)->first();
                            if ($isOldCustomer) {
                                $typeCutomer = 1;
                            }
                        } if ($typeCutomer == 1) {
                            $filterOld[] = $order->id;
                        } if ($typeCutomer == 0) {
                            $filterNew[] = $order->id;
                        } 
                    }
                }
            }
        }
       
        if(count($filterNew) == 0 && count($filterOld) == 0) {
            $newCustomer['order'] = 0;
            $oldCustomer['order'] = 0;
        } else {
            /** new */
            if (count($filterNew) == 0) {
                $newCustomer['order'] = 0;
                $newCustomer['total'] = 0;
                $newCustomer['product'] = 0;
                $newCustomer['avg'] = 0;
                $newCustomer['rate'] = 0;
            } else {
                $listOrderNew = Orders::select('qty', 'total')->whereIn('id', $filterNew);
                $countOrderNew = $listOrderNew->count();
                $newCustomer['order'] = $countOrderNew;
                $newCustomer['total'] = round($listOrderNew->sum('total'), 0);
                $newCustomer['product'] = $listOrderNew->sum('qty');

                if ($countOrderNew > 0) {
                    $avgOrders = $newCustomer['total'] / $countOrderNew;
                    $newCustomer['avg'] = round($avgOrders, 0);
                }
            }

            /** old */
            if (count($filterOld) == 0) {
                $oldCustomer['order'] = 0;
                $oldCustomer['total'] = 0;
                $oldCustomer['product'] = 0;
                $oldCustomer['avg'] = 0;
                $oldCustomer['rate'] = 0;
            } else {
                $listOrderOld = Orders::select('qty', 'total')->whereIn('id', $filterOld);
                $countOrderOld = $listOrderOld->count();
                $oldCustomer['order'] = $countOrderOld;
                $oldCustomer['total'] = round($listOrderOld->sum('total'), 0);
                $oldCustomer['product'] = $listOrderOld->sum('qty');

                if ($countOrderOld > 0) {
                    $avgOrdersOld = round($oldCustomer['total'] / $countOrderOld, 0);
                    $oldCustomer['avg'] = round($avgOrdersOld, 0);
                }
            }
        }

        $saleCare = $this->getSalesReport(Auth::user(), $dataFilter);
        $countSaleCare = $saleCare->count();
        
        if ($countSaleCare == 0) {
            $newCustomer['contact'] = 0;
            $oldCustomer['contact'] = 0;
        } else {
            if ($countOrder == 0) {
                $newCustomer['order'] = 0;
                $newCustomer['total'] = 0;
                $newCustomer['product'] = 0;
                $newCustomer['avg'] = 0;
                $newCustomer['rate'] = 0;

                $oldCustomer['order'] = 0;
                $oldCustomer['total'] = 0;
                $oldCustomer['product'] = 0;
                $oldCustomer['avg'] = 0;
                $oldCustomer['rate'] = 0;
            }

            $saleCareIDs = $saleCare->get()->pluck('id')->toArray();
            $saleCareOld = SaleCare::select('id')->whereIn('id', $saleCareIDs)->where('old_customer', 1);  
            $countSaleCareOld = $saleCareOld->count();
            $oldCustomer['contact'] = $countSaleCareOld;
           
            $saleCareNew = SaleCare::select('id')->whereIn('id', $saleCareIDs)->whereIn('old_customer', [0,2]);
            $countSaleCareNew = $saleCareNew->count();
            $newCustomer['contact'] = $countSaleCareNew;
        }

        /** tỷ lệ chốt = số đơn/số data */
        /** new */
        if ($countSaleCareNew == 0) {
            $rateSuccessNew = $countOrderNew * 100;
        } else {
            $rateSuccessNew = $countOrderNew / $countSaleCareNew * 100;
        }
        $newCustomer['rate'] = round($rateSuccessNew, 2);

        /** old */
        if ($countSaleCareOld == 0) {
            $rateSuccessOld = $countOrderOld * 100;
        } else {
            $rateSuccessOld = $countOrderOld / $countSaleCareOld * 100;
        }
        $oldCustomer['rate'] = round($rateSuccessOld, 2);
       
        $result = [
            'new_customer' => $newCustomer, 
            'old_customer' => $oldCustomer
        ];

       return $result;
    }

    public function getListSaleCare($req)
    {
        $result = [];
        $list = SaleCare::select('src_id', 'id', 'created_at', 'group_id', 'assign_user', 'old_customer');
        
        if (isset($req['daterange']) || !empty($req->daterange)) {
            $dateRange = (isset($req['daterange'])) ? $req['daterange'] : $req->daterange;

            $time = $dateRange;
            if (!is_array($dateRange)) {
                $time = explode("-",$dateRange); 
            }

            $timeBegin  = str_replace('/', '-', $time[0]);
            $timeEnd    = str_replace('/', '-', $time[1]);
            $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
            $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

            // Chỉ rõ table name để tránh ambiguous (sale_care là table chính trong function này)
            $list = $list->whereDate('sale_care.created_at', '>=', $dateBegin)
                ->whereDate('sale_care.created_at', '<=', $dateEnd);
        }
       
        if (isset($req['group']) || !empty($req->group)) {
            $groupId = (isset($req['group'])) ? $req['group'] : $req->group;
            // Chỉ rõ table name để tránh ambiguous
            $list = $list->where('sale_care.group_id', $groupId);
        }

        if (isset($req['groupUser']) || !empty($req->groupUser)) {
            $groupUS = GroupUser::find($req['groupUser']);
            if ($groupUS) {
                // Tối ưu: eager load users và chỉ lấy IDs một lần
                $saleIds = $groupUS->users()->pluck('id')->toArray();
                if (!empty($saleIds)) {
                    // Chỉ rõ table name để tránh ambiguous
                    $list = $list->whereIn('sale_care.assign_user', $saleIds);
                } else {
                    $list = $list->whereRaw('1 = 0'); // Return empty query
                }
            }
        }
         
        if (isset($req->type_customer) && (int)$req->type_customer != -1) {
            // Chỉ rõ table name để tránh ambiguous
            $list = $list->where('sale_care.old_customer', $req->type_customer);
        }

        $listSrcId = Helper::getSrcByPermission(Auth::user(), $req);
        if ($listSrcId) {
            // Chỉ rõ table name để tránh ambiguous
            $list = $list->whereIn('sale_care.src_id', $listSrcId);
        } else {
            // Nếu không có permission thì return empty result
            return [];
        }

        // Tối ưu: eager load relationships để tránh N+1 queries
        $list = $list->with(['getSrcPage.userDigital:id,real_name']);
        
        foreach ($list->get() as $s) {          
            if (!$s->getSrcPage || !$s->getSrcPage->userDigital) {
                Log::channel('c')->info('Mã đơn hàng - data ko xác định data/ nguồn:' . $s->id);
                continue;
            }
     
            $digital = $s->getSrcPage->userDigital;
            if (!isset($result[$digital->id])) {
                if (($s->old_customer == 0 || $s->old_customer == 2)) {
                    $result[$digital->id] = [
                        'name' => $digital->real_name ?? '',
                        'new_customer' => [
                            'contact' => 1,
                            'total' => 0,
                            'product' => 0,
                            'count_order' => 0
                        ],
                        'old_customer' => [
                            'contact' => 0,
                            'total' => 0,
                            'product' => 0,
                            'count_order' => 0
                        ]
                    ];
                } else if ($s->old_customer == 1) {
                    $result[$digital->id] = [
                        'name' => $digital->real_name ?? '',
                        'old_customer' => [
                            'contact' => 1,
                            'total' => 0,
                            'product' => 0,
                            'count_order' => 0
                        ],
                        'new_customer' => [
                            'contact' => 0,
                            'total' => 0,
                            'product' => 0,
                            'count_order' => 0
                        ],
                    ];
                }
                
            } else {
                if (($s->old_customer == 0 || $s->old_customer == 2)) {
                    $result[$digital->id]['new_customer']['contact']++;
                } else if ($s->old_customer == 1 ) {
                    $result[$digital->id]['old_customer']['contact']++;
                }
            }    
        }

        return $result;
    }
}

