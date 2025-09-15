<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AddressController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\SaleCare;
use App\Helpers\Helper;
use App\Models\Group;
use App\Models\GroupUser;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $toMonth      = date("d/m/Y", time());

        /**set tmp */
        // $toMonth = '29/05/2025';

        $dataSale = $this->getReportHomeSale($toMonth);
        $dataDigital = $this->getReportHomeDigitalV2($toMonth);

        $category = Category::where('status', 1)->get();
        $sales = User::where('status', 1)->where('is_sale', 1)->orWhere('is_cskh', 1)->get();
        $groups = Group::orderBy('id', 'desc')->get();
        $groupUser = GroupUser::orderBy('id', 'desc')->get();
        return view('pages.home')->with('category', $category)->with('sales', $sales)
            ->with('dataSale', $dataSale)
            ->with('groups', $groups)
            ->with('groupUser', $groupUser)
            ->with('dataDigital', $dataDigital);
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
    public function getSaleByType($dataFilter, $type)
    {
        $result = []; 
        $avgOrders = 0;
        $ordersCtl = new OrdersController();

        if ($type == 'new') {
            $dataFilter['type_customer'] = 0;  
        } else if ($type == 'old') {
            $dataFilter['type_customer'] = 1;    
        }

        $listOrder      = $ordersCtl->getListOrderByPermisson(Auth::user(), $dataFilter, true);
        $countOrders    = $listOrder->count();
        $ordersSum      = $listOrder->sum('total');
        $sumProduct     = $listOrder->sum('qty');

        if ($countOrders > 0) {
            $avgOrders = round($ordersSum / $countOrders, 0);
        }

        $saleCtl = new SaleController();
        $saleCare  = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilter);

        if ($type == 'new') {
            $typeTmp = [0,2];
            $saleCare->whereIn('old_customer', $typeTmp);    
        } else if ($type == 'old') {
            $saleCare->where('old_customer', 1);    
        }

        $countSaleCare = $saleCare->count();
       
        /** tỷ lệ chốt = số đơn/số data */
        if ($countSaleCare == 0) {
            $rateSuccess = $countOrders * 100;
        } else {
            $rateSuccess = $countOrders / $countSaleCare * 100;
        }

        $result = [
            'contact' => $countSaleCare,
            'order' => $countOrders,
            'rate' => round($rateSuccess, 2),
            'product' => $sumProduct,
            'total' => round($ordersSum, 0),
            'avg' => round($avgOrders, 0),
        ];
        return $result;
    }

    public function getReportHomeSale($time, $checkAll = false, $isLeadSale = false)
    {
        $dataFilter['daterange'] = [$time, $time];
        $result = [];

        if (!$checkAll) {
            $checkAll = isFullAccess(Auth::user()->role);
        }

        $isLeadSale = $isLeadSale ? : Helper::isLeadSale(Auth::user()->role);
        if ($checkAll || $isLeadSale) {

            $listSale = Helper::getListSaleV2(Auth::user(), $isLeadSale);
            foreach ($listSale->get() as $sale) {
                $data = $this->getReportUserSaleV2($sale, $dataFilter);
                $result[] = $data;   
            }

        } else if (Auth::user()->is_CSKH || Auth::user()->is_sale) {
            $result[] = $this->getReportUserSaleV2(Auth::user(), $dataFilter);
        }
       
        return $result;
    }

    public function getReportUserSale($user, $dataFilter)
    {
        $data = ['name' => ($user->real_name) ?: ''];
        $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
        $dataFilter['sale'] = $user->id;

        if ($user->is_sale) {
            $newCustomer = $this->getSaleByType($dataFilter, 'new');
            $data['new_customer'] = $newCustomer;

            $newTotal = Helper::stringToNumberPrice($newCustomer['total']);
            $newCountOrder = $newCustomer['order'];

            $data['old_customer'] = [
                'contact' => 0,
                'order' => 0,
                'rate' => 0,
                'product' => 0,
                'total' => 0,
                'avg' => 0,
            ];
        } else if ($user->is_CSKH) {
            $oldCustomer = $this->getSaleByType($dataFilter, 'old');
            $data['old_customer'] = $oldCustomer;
            $oldTotal = Helper::stringToNumberPrice($oldCustomer['total']);
            $oldCountOrder = $oldCustomer['order'];

            $data['new_customer'] = [
                'contact' => 0,
                'order' => 0,
                'rate' => 0,
                'product' => 0,
                'total' => 0,
                'avg' => 0,
            ];
        }  
        
        $totalSum = $newTotal + $oldTotal;
        if ($newCountOrder != 0 || $oldCountOrder != 0) {
            $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
        }

        $data['summary_total'] = [
            'total' => round($totalSum, 0),
            'avg' => round($avgSum, 0),
        ];

        return $data;
    }

    public function getReportUserSaleV2($user, $dataFilter)
    {

        $data = ['name' => ($user->real_name) ?: ''];
        $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
        $dataFilter['sale'] = $user->id;
        $dataFilter['typeDate'] = 1; //ngày data vè hệ thống
        
≈

        
   
        $newCustomer = $this->getSaleByType($dataFilter, 'new');
        $data['new_customer'] = $newCustomer;

        $newTotal = Helper::stringToNumberPrice($newCustomer['total']);
        $newCountOrder = $newCustomer['order'];

        $oldCustomer = $this->getSaleByType($dataFilter, 'old');
        $data['old_customer'] = $oldCustomer;
        $oldTotal = Helper::stringToNumberPrice($oldCustomer['total']);
        $oldCountOrder = $oldCustomer['order'];

        // $data['new_customer'] = [
        //     'contact' => 0,
        //     'order' => 0,
        //     'rate' => 0,
        //     'product' => 0,
        //     'total' => 0,
        //     'avg' => 0,
        // ];
        
        $totalSum = $newTotal + $oldTotal;
        if ($newCountOrder != 0 || $oldCountOrder != 0) {
            $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
        }

        $rateSum = 0;
        $contactSum = $data['new_customer']['contact'];
        $orderSum = $data['old_customer']['order'] + $data['new_customer']['order'];
        if ($contactSum > 0) {
            $rateSum = $orderSum / $contactSum * 100;
        } else {
            $rateSum = $orderSum * 100;
        }

        $data['summary_total'] = [
            'total' => round($totalSum, 0),
            'avg' => round($avgSum, 0),
            'rate' => round($rateSum, 2)
        ];

        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function filterTotalSales(Request $req) {
        return response()->json($this->filterByDate($req->type, $req->date));
    }

    public function ajaxFilterDashboar(Request $req) 
    {
        $resultDigital = $result =  $dataFilter = $list = [];
        $dataFilter['daterange'] = $req->date;

        if ($req->status != 999) {
            $dataFilter['status'] = $req->status;
            $newFilter['status'] = $req->status;
        }

        $category = $req->category;
        if ($category != 999) {
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
        if ($mkt != 999) {
            $dataFilter['mkt'] = $mkt;
            $newFilter['mkt'] = $mkt;
        }

        $src = $req->src;
        if ($src != 999) {
            $dataFilter['src'] = $src;
            $newFilter['src'] = $src;
        } 

        $group = $req->group;
        if ($group != 999) {
            $dataFilter['group'] = $group;
        }

        $groupUser = $req->groupUser;
        $list = [];
        
        if ($groupUser && $groupUser != 999) {
            $groupUs = GroupUser::find($groupUser);
            
            if ($groupUs) {
                $listSale = $groupUs->users;
                foreach ($listSale as $sale) {
                    $data = $this->getReportUserSaleV2($sale, $dataFilter);
                    $list[] = $data;
                }
            }
        } else if (isset($dataFilter['sale'])) {
             /**
             * bắt đầu lọc 
             * chọn 1 sale xxxxx
            */
            $sale = Helper::getSaleById($dataFilter['sale']);
            $list[] = $this->getReportUserSaleV2($sale, $dataFilter);
        } else {
            /** chọn tất cả sale */
            // $listSale = Helper::getListSale();
            
            $checkAll = isFullAccess(Auth::user()->role);
            $isLeadSale = Helper::isLeadSale(Auth::user()->role);
            if ($checkAll || $isLeadSale) {
                $listSale = Helper::getListSaleV2(Auth::user());
                foreach ($listSale->get() as $sale) {
                    $data = $this->getReportUserSaleV2($sale, $dataFilter);
                    $list[] = $data;
                }
            } else {
                /**sale đang xem thông tin */
                $list[] = $this->getReportUserSaleV2(Auth::user(), $dataFilter);
            }
        }

        $result['data'] = $list;
        
        $totalSum = $avgSum = $newContact = $newOrder = $newRate = $newProduct = $newTotal = $oldAvg = $oldTotal = $oldProduct = $oldRate = $newAvg = $oldContact = $oldOrder= 0;
        $sumNewCustomer = $sumOldCustomer = [
            'contact' => 0,
            'order' => 0,
            'rate' => 0,
            'product' => 0,
            'total' => 0,
            'avg' => 0,
        ];
        
        foreach ($list as $data) {
            if (isset($data['new_customer'])) {
            $newContact += $data['new_customer']['contact'];
            $newOrder += $data['new_customer']['order'];
            $newProduct += $data['new_customer']['product'];
            $newTotal += ($data['new_customer']['total']);
            }
            if (isset($data['old_customer'])) {
            $oldContact += $data['old_customer']['contact'];
            $oldOrder += $data['old_customer']['order'];
            $oldRate += $data['old_customer']['rate'];
            $oldProduct += $data['old_customer']['product'];
            $oldTotal += ($data['old_customer']['total']);
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

        return $result;
    }

    public function filterDashboard(Request $req) {
        $rateSuccess = $countSaleCare = 0;
        $ordersController = new OrdersController();

        $time = $dataFilter['daterange']    = $req->date;

        // $time       = $req->date;
        $timeBegin  = str_replace('/', '-', $time[0]);
        $timeEnd    = str_replace('/', '-', $time[1]);

        if ($req->status != 999) {
            $dataFilter['status'] = $req->status;
            $newFilter['status'] = $req->status;
        }

        $category = $req->category;
        if ($category != 999) {
            $dataFilter['category'] = $category;
        }

        $product = $req->product;
        if ($req->product && $product != 999) {
            $dataFilter['product'] = $product;
        }

        if ($req->sale && $req->sale != 999) {
            $dataFilter['sale'] = $req->sale;
        }

        $mkt = $req->mkt;
        if ($mkt != 999) {
            $dataFilter['mkt'] = $mkt;
            $newFilter['mkt'] = $mkt;
        }

        $src = $req->src;
        if ($src != 999) {
            $dataFilter['src'] = $src;
            $newFilter['src'] = $src;
        } 
 
        $data = $ordersController->getListOrderByPermisson(Auth::user(), $dataFilter);
        $countOrders = $data->count();
        $sumProduct = $data->sum('qty');

        $totalSum  = $data->sum('total');
        $avgOrders = 0;
        if ($totalSum > 0) {
            $avgOrders = $totalSum / $countOrders;
        }
        
        /** tỷ lệ chốt: số đơn/ số data */
        $newFilter['daterange'] =  $req->date;
        $newFilter['sale'] = $req->sale;
      
        $countOrdersRate = $ordersController->getListOrderByPermisson(Auth::user(), $newFilter)->count();
       
        $saleCtl = new SaleController();
        // if (isset($newFilter['mkt']) || $newFilter['src']) {
        //     $saleCare  = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilter);
        // } else {
        //     $saleCare  = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilter);
        // }

        $saleCare  = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilter);
        $countSaleCare = $saleCare->count();

        /** tỷ lệ chốt = số đơn/số data */
        if ($countSaleCare == 0) {
            $rateSuccess = $countOrders * 100;
        } else {
            $rateSuccess = $countOrders / $countSaleCare * 100;
        }

        if ($countSaleCare == 0) {
            $rateSuccess = $countOrdersRate * 100;
        } else {
            $rateSuccess = $countOrdersRate / $countSaleCare * 100;
        }
       
        $rateSuccess = round($rateSuccess, 2);

        $result = [
            'totalSum'      => number_format($totalSum) . 'đ',
            'percentTotal'  => '',
            'countOrders'   => $countOrders,
            'percentCount'  => '',
            'avgOrders'     => number_format($avgOrders) . 'đ',
            'percentAvg'    => '',
            'sumProduct'    => '(' . $sumProduct . ' sản phẩm)',
            'rateSuccess'   =>  $rateSuccess . '%',
            'countSaleCare' =>  $countSaleCare
        ];
        return $result;
    }

    public function ajaxFilterDashboardDigital(Request $req)
    {
        $resultDigital = $result =  $dataFilter = $list = [];
        $dataFilter['daterange'] = $req->date;

        $status = $req->status;

        if (($status || $status == 0) && $status != 999 && $status) {
            $dataFilter['status'] = $status;
            $newFilter['status'] = $status;
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
            $newFilter['mkt'] = $mkt;
        }

        $src = $req->src;
        if ($src && $src != 999) {
            $dataFilter['src'] = $src;
            $newFilter['src'] = $src;
        } 

        $group = $req->group;
        if ($req->group && $group != 999) {
            $dataFilter['group'] = $group;
        }

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

        $checkAll = isFullAccess(Auth::user()->role);
        if (!$checkAll && Auth::user()->is_digital == 1) {
            if (Auth::user()->name == 'digital.tien') {
                $dataFilter['mkt'] = 2;
            } else if (Auth::user()->name == 'digital.tien') {
                $dataFilter['mkt'] = 3;
            } 
        } 

        if (isset($dataFilter['mkt']) ) {
            if ($dataFilter['mkt'] == 1) {
                $digital =  [
                    'name' => 'Mr Nguyên',
                    'mkt' => 1,
                ];
            } else if ($dataFilter['mkt'] == 2) {
                $digital = [
                    'name' => 'Mr Tiễn',
                    'mkt' => 2,
                ];
            } else if ($dataFilter['mkt'] == 3) {
                $digital = [
                    'name' => 'Di Di',
                    'mkt' => 3,
                ];
            } 

            $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
            $data = [];
            $data = ['name' => $digital['name']];
            $dataFilter['mkt'] = $digital['mkt'];

            /** khách mới */
            $dataFilter['type_customer'] = 0;
            $newCustomer = $this->getSaleByType($dataFilter, 'new');
            $newTotal = Helper::stringToNumberPrice($newCustomer['total']);
            $newCountOrder = $newCustomer['order'];

            // /** khách cũ */
            $dataFilter['type_customer'] = 1;
            $oldCustomer = $this->getSaleByType($dataFilter, 'old');
            $oldTotal = Helper::stringToNumberPrice($oldCustomer['total']);
            $oldCountOrder = $oldCustomer['order'];

            $data['new_customer'] = $newCustomer;
            $data['old_customer'] =  $oldCustomer;

            $totalSum = $newTotal + $oldTotal;
          
            if ($newCountOrder != 0 || $oldCountOrder != 0) {
                $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
            }

            $data['summary_total'] = [
                'total' => round($totalSum, 0),
                'avg' => round($avgSum, 0),
            ];

            $resultDigital['data'][] = $data;
        } else {
            foreach ($listDigital as $digital) {
                $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
                $data = ['name' => $digital['name']];
                $dataFilter['mkt'] = $digital['mkt'];

                /** khách mới */
                $dataFilter['type_customer'] = 0;
                $newCustomer = $this->getSaleByType($dataFilter, 'new');
                $newTotal = Helper::stringToNumberPrice($newCustomer['total']);
                $newCountOrder = $newCustomer['order'];
              
                 /** khách cũ */
                $dataFilter['type_customer'] = 1;
                $oldCustomer = $this->getSaleByType($dataFilter, 'old');
                $oldTotal = Helper::stringToNumberPrice($oldCustomer['total']);
                $oldCountOrder = $oldCustomer['order'];
                $data['new_customer'] = $newCustomer;
                $data['old_customer'] = $oldCustomer;

                $totalSum = $newTotal + $oldTotal;
                if ($newCountOrder != 0 || $oldCountOrder != 0) {
                    $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
                }

                $data['summary_total'] = [
                    'total' => round($totalSum, 0),
                    'avg' => round($avgSum, 0),
                ];

                $dataDigital[] = $data;
            }

            $resultDigital['data'] = $dataDigital;
        }

        $resultDigital['trSum'] = Helper::getSumCustomer($resultDigital['data']);
        $result['data_digital'] = $resultDigital;

        return $result;
    }

    public function ajaxFilterDashboardDigitalV2(Request $req)
    {
        $resultDigital = $result =  $dataFilter = $list = [];
        $dataFilter['daterange'] = $req->date;
        $status = $req->status;

        if (($status || $status == 0) && $status != 999) {
            $dataFilter['status'] = $status;
            $newFilter['status'] = $status;
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
            $newFilter['mkt'] = $mkt;
        }

        $src = $req->src;
        if ($src && $src != 999) {
            $dataFilter['src'] = $src;
            $newFilter['src'] = $src;
        } 

        $group = $req->group;
        if ($req->group && $group != 999) {
            $dataFilter['group'] = $group;
        }

        $groupUser = $req->groupUser;
        if ($req->groupUser && $groupUser != 999) {
            $dataFilter['groupUser'] = $groupUser;
        }

        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
        $checkAll = isFullAccess(Auth::user()->role);
        if (!$checkAll && !$isLeadDigital && Auth::user()->is_digital == 1) {
            $dataFilter['mkt'] = Auth::user()->id;
        } 

        $dataDigital = [];
        $listDigital = User::where('status', 1)->where('is_digital', 1)->orderBy('id', 'DESC');

        if (isset($dataFilter['mkt']) ) {
            $listDigital = $listDigital->where('id', $dataFilter['mkt']);
        }
   
        if (!$checkAll && !$isLeadDigital) {
            $listDigital = $listDigital->where('id', Auth::user()->id);
        }


        foreach ($listDigital->get() as $digital) {

            $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
            $data = ['name' => $digital->real_name];
            $dataFilter['mkt'] = $digital['mkt'];

            $time = $dataFilter['daterange'];
            
            // if ($digital->id != 67) {
            //     continue;
            // }

            $newCustomer = $this->getDataDigitalAjax($digital->id, 0, $time[0], $time[1], $dataFilter);

            $newTotal = Helper::stringToNumberPrice($newCustomer['total']);
            $newCountOrder = $newCustomer['order'];

            $oldCustomer = $this->getDataDigitalAjax($digital->id, 1, $time[0], $time[1], $dataFilter);
            $oldTotal = Helper::stringToNumberPrice($oldCustomer['total']);
            $oldCountOrder = $oldCustomer['order'];

            $data['new_customer'] = $newCustomer;
            $data['old_customer'] = $oldCustomer;

            $totalSum = $newTotal + $oldTotal;
            if ($newCountOrder != 0 || $oldCountOrder != 0) {
                $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
            }

            $rateSum = 0;
            $contactSum =  $data['new_customer']['contact'];
            $orderSum =  $data['new_customer']['order'] + $data['old_customer']['order'];
            if ($contactSum > 0) {
                $rateSum = $orderSum / $contactSum * 100;
            } else {
                $rateSum = $orderSum * 100;
            }

            $data['summary_total'] = [
                'total' => round($totalSum, 0),
                'avg' => round($avgSum, 0),
                'rate' => round($rateSum, 2),
            ];

            $dataDigital[] = $data;
        }

        $resultDigital['data'] = $dataDigital;
        $resultDigital['trSum'] = Helper::getSumCustomer($resultDigital['data']);
        $result['data_digital'] = $resultDigital;

        return $result;
    }

    public function getReportHomeDigitalV2($time)
    {
        $result = [];

        $checkAll = isFullAccess(Auth::user()->role);
        $listDigital = User::where('status', 1)->where('is_digital', 1)->orderBy('id', 'DESC');
        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
            
        if (!$checkAll && !$isLeadDigital) {
            $listDigital = $listDigital->where('id', Auth::user()->id);
        }

        foreach ($listDigital->get() as $k => $digital) {
            $result[$k]['name'] = $digital->real_name;
            $result[$k]['new_customer'] = $this->getDataDigitalInHome($digital->id, 0, $time);
            $result[$k]['old_customer'] = $this->getDataDigitalInHome($digital->id, 1, $time);
        }

        return $result;
    }

    public function getDataDigitalAjax($id, $typeCustomer, $begin, $after, $dataFilter)
    {
        $dataFilter['daterange'] = "$begin - $after";
        $req = new Request();
        $req->merge(['daterange' => $dataFilter['daterange']]);
        $req->merge(['mkt_user' => $id]);
        $req->merge(['type_customer' => $typeCustomer]);
        
        if (isset($dataFilter['status'])) {
            $req->merge(['status' => $dataFilter['status']]);
        }

        if (isset($dataFilter['group'])) {
            $req->merge(['group' => $dataFilter['group']]);
        }

        if (isset($dataFilter['groupUser'])) {
            $req->merge(['groupUser' => $dataFilter['groupUser']]);
        }

        return $this->getDataDigitalV2($req);
    }

    public function getDataDigitalInHome($id, $typeCustomer, $time)
    {
        $dataFilter['daterange'] = "$time - $time";
        $req = new Request();
        $req->merge(['daterange' => $dataFilter['daterange']]);
        $req->merge(['mkt_user' => $id]);
        $req->merge(['type_customer' => $typeCustomer]);

        return $this->getDataDigitalV2($req);
    }

    public function getDataDigitalV2($req)
    {
        $mktController = new MarketingController();
        $data = $mktController->getDataMkt($req);

        $contact = $countOrders = $rateSuccess = $sumProduct = $ordersSum = $avgOrders = 0;
        if ($data) {
            foreach ($data as $item) {
                $contact += $item['contact'];
                $countOrders += $item['order'];
                $sumProduct += $item['product'];
                $ordersSum += $item['total'];
            }

            if ($contact > 0) {
                $rateSuccess = $countOrders/$contact * 100;
            }

            if ($countOrders > 0) {
                $avgOrders = $ordersSum/$countOrders;
            }
        }

        $result = [
            'contact' => $contact,
            'order' => $countOrders,
            'rate' => round($rateSuccess, 2),
            'product' => $sumProduct,
            'total' => round($ordersSum, 0),
            'avg' => round($avgOrders, 0),
        ];

        return $result;
    }
}
