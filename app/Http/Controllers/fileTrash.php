<?php
    public function getListOrderByPermisson($user, $dataFilter = null, $checkAll = false, $getJson = false) 
    {
        $roles  = $user->role;
        $list   = Orders::orderBy('id', 'desc');
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

            if (isset($dataFilter['status'])) {
                $list->whereStatus($dataFilter['status']);
            }

            if (isset($dataFilter['category'])) {
                $ids = [];
                foreach ($list->get() as $order) {
                    $products = json_decode($order->id_product);
                    $isProductOfCategory = Helper::checkProductsOfCategory($products, $dataFilter['category']);
                    if ($isProductOfCategory) {
                        $ids[] = $order->id;
                    }
                }

                $list       = Orders::whereIn('id', $ids)->orderBy('id', 'desc');
            }

            if (isset($dataFilter['product'])) {
                $ids = [];
                
                foreach ($list->get() as $order) {
                    $products = json_decode($order->id_product);
                    foreach ($products as $product) {
                        if ($product->id == $dataFilter['product']) {
                            $ids[] = $order->id;
                            break;
                        }
                    }
                }

                $list = Orders::whereIn('id', $ids)->orderBy('id', 'desc');
            }

            if (isset($dataFilter['group'])) {
                $group = Group::find($dataFilter['group']);
                if ($group) {

                    $listId = $list->pluck('id')->toArray();
                    $listOrder = Orders::select('orders.*')->join('sale_care', 'orders.sale_care', '=', 'sale_care.id')
                        ->where('sale_care.group_id', $dataFilter['group'])
                        ->whereIn('orders.id', $listId);
                    $list = $list->whereIn('id', $listOrder->pluck('id')->toArray());
                }
            }

            if (isset($dataFilter['groupUser'])) {
                $groupUS = GroupUser::find($dataFilter['groupUser']);
                if ($groupUS) {
                    $listSale = $groupUS->users;
                    $list = $list->whereIn('assign_user', $listSale->pluck('id')->toArray());
                }
            }

            /** mrNguyen = 1
             *  mrTien = 2
             *
             * lấy list sđt từ order
             * get sale care ( where phone = sđt và &page_id/link của mkt
             * kqua sđt này bao gồm data thuộc mkt và có đơn theo điều kiên lọc ban đầu của order
             * lấy order từ sđt vừa lọc sale care
             */
            $dataFilterSale = [];
            if (isset($dataFilter['src'])) {

                $idTmps = [];
                foreach ($list->get() as $order) {
                    $mktCtl = new MarketingController();
                    if ($order->saleCare) {
                        $srcPage = $mktCtl->getSrcPageFromSaleCare($order->saleCare);
                        if ($srcPage) {
                            $idTmps[] = $order->id;
                        }
                    }
                }

                $list = Orders::orderBy('id', 'desc')
                    ->whereIn('id', $idTmps);
            } 

            if (count($dataFilterSale) > 0 ) {
                $phoneFilter = [];
                $listPhoneOrder = $list->pluck('phone')->toArray();
                $flag = false;

                foreach ($listPhoneOrder as $phone) {
                    $saleCtl = new SaleController();
                    $listsaleCare = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilterSale);
                    
                    $cus9phone = $this->getCustomPhone9Num($phone);
                    // dd($listsaleCare->get());
                    $careFromOrderPhone = $listsaleCare->where('phone', 'like', '%' . $cus9phone . '%')
                        ->where('assign_user', '!=', 55)->first();
                    
                    // dd($careFromOrderPhone);
                    // $dataFilter['type_customer'] = 0;
                    // dd( $careFromOrderPhone);

                    if (!isset($dataFilter['type_customer']) ) {
                        $dataFilter['type_customer'] = 999; //lấy tất cả data nóng và CSKH
                    }
                    
                    // dd($dataFilter['type_customer']);

                    $flag = Helper::checkTypeOrderbyPhone($cus9phone, $dataFilter['type_customer']);

                    if ($careFromOrderPhone && $flag) {
                        $phoneFilter[] = $phone;
                    }
                }

                // dd($phoneFilter);
                $list = Orders::whereIn('phone', $phoneFilter)->whereDate('created_at', '>=', $dateBegin)
                    ->whereDate('created_at', '<=', $dateEnd)->orderBy('id', 'desc');  
                   
            } 

            if (isset($dataFilter['type_customer']) && $dataFilter['type_customer'] != -1) {

                $resultFilter = [];
                foreach ($list->get() as $k => $order) {
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

                $list = Orders::whereIn('id', $resultFilter)->orderBy('id', 'desc');
            }
        }

        if (!$checkAll) {
            $listRole   = [];
            $roles      = json_decode($roles);
            if ($roles) {
                foreach ($roles as $key => $value) {
                    if ($value == 1 || $value == 4) {
                        $checkAll = true;
                        break;
                    } else {
                        $listRole[] = $value;
                    }
                }
            }
        }
       

        
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $routeName = \Request::route();

        if ((isset($dataFilter['sale']) && $dataFilter['sale'] != 999) && ($checkAll || $isLeadSale)) {
            /** user đang login = full quyền và đang lọc 1 sale */
            $list = $list->where('assign_user', $dataFilter['sale']);
        } else if ((!$checkAll || !$isLeadSale) && !$user->is_digital && $user->is_sale) {
            /** sale đag xem report của mình */
            $list = $list->where('assign_user', $user->id);
        }

        /**old code */
        // if ((isset($dataFilter['sale']) && $dataFilter['sale'] != 999) && ($checkAll || $isLeadSale)) {
        //     /** user đang login = full quyền và đang lọc 1 sale */
        //     $list = $list->where('assign_user', $dataFilter['sale']);
        // } else if ($user->is_digital == 1 && $routeName->getName() == 'order' && $user->name == 'digital.tien') {
        //     if (!$dataFilter) {
        //         $today  = date("Y-m-d", time());
        //         $dateBegin  = date('Y-m-d',strtotime("$today"));
        //         $dateEnd    = date('Y-m-d',strtotime("$today"));
        //         $list->whereDate('created_at', '>=', $dateBegin)
        //             ->whereDate('created_at', '<=', $dateEnd);
        //     }
            
        //     $phoneFilter = [];
        //     $listPhoneOrder = $list->pluck('phone')->toArray();
        //     $dataFilterSale['mkt'] = 2; //aT
        //     foreach ($listPhoneOrder as $phone) {
        //         $saleCtl = new SaleController();
        //         $listsaleCare = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilterSale);
        //         $careFromOrderPhone = $listsaleCare->where('phone', 'like', '%' . $phone . '%')->first();

        //         if ($careFromOrderPhone) {
        //             $phoneFilter[] = $phone;
        //         } 
        //     }

        //     $list = Orders::whereIn('phone', $phoneFilter)->orderBy('id', 'desc');
        // } else if ($user->is_digital == 1 && $routeName->getName() == 'order' && $user->name == 'digital.di') {
        //     if (!$dataFilter) {
        //         $today  = date("Y-m-d", time());
        //         $dateBegin  = date('Y-m-d',strtotime("$today"));
        //         $dateEnd    = date('Y-m-d',strtotime("$today"));
        //         $list->whereDate('created_at', '>=', $dateBegin)
        //             ->whereDate('created_at', '<=', $dateEnd);
        //     }
            
        //     $phoneFilter = [];
        //     $listPhoneOrder = $list->pluck('phone')->toArray();
        //     $dataFilterSale['mkt'] = 3; //aT
        //     foreach ($listPhoneOrder as $phone) {
        //         $saleCtl = new SaleController();
        //         $listsaleCare = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilterSale);
        //         $careFromOrderPhone = $listsaleCare->where('phone', 'like', '%' . $phone . '%')->first();

        //         if ($careFromOrderPhone) {
        //             $phoneFilter[] = $phone;
        //         } 
        //     }

        //     $list = Orders::whereIn('phone', $phoneFilter)->orderBy('id', 'desc');
        // } else if ((!$checkAll || !$isLeadSale) && !$user->is_digital) {
        //     $list = $list->where('assign_user', $user->id);
        // }

        return $list;
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

    public function getReportHomeSale($time, $checkAll = false, $isLeadSale = false)
    {
        $dataFilter['daterange'] = [$time, $time];
        $result = [];
        if (!$checkAll) {
            $checkAll = isFullAccess(Auth::user()->role);
        }

        $isLeadSale = $isLeadSale ? : Helper::isLeadSale(Auth::user()->role);
        
        if ($checkAll || $isLeadSale) {
            $listGroup = GroupUser::where('status', 1)->get();
            foreach ($listGroup as $gr) {
                if ($gr->id == 5) {
                    continue;
                }
                $listSale =  Helper::getListSaleV3(Auth::user(), $isLeadSale, $gr->id);
                foreach ($listSale as $sale) {
                    $data = $this->getReportUserSaleV2($sale, $dataFilter);
                    $result[] = $data;   
                }
            }

        } else if ((Auth::user()->is_CSKH || Auth::user()->is_sale) && !Helper::isCskhDt(Auth::user())) {
            $result[] = $this->getReportUserSaleV2(Auth::user(), $dataFilter);
        }
       
        return $result;
    }

    
    public function getReportHomeSale2($time, $checkAll = false, $isLeadSale = false)
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

        
        $listOrder      = $ordersCtl->getListOrderByPermisson(Auth::user(), $dataFilter, true, true);
        $countOrders    = $listOrder['countOrders'];
        $ordersSum      = $listOrder['ordersSum'];
        $sumProduct     = $listOrder['sumProduct'];

        if ($countOrders > 0) {
            $avgOrders = round($ordersSum / $countOrders, 0);
        }

        $saleCtl = new SaleController();
        $saleCare  = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilter);
        $countSaleCare = $saleCare->count();
        if ($countSaleCare > 0) {
            if ($type == 'new') {
                $typeTmp = [0,2];
                $saleCare->whereIn('old_customer', $typeTmp);    
            } else if ($type == 'old') {
                $saleCare->where('old_customer', 1);    
            }
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
            'order' => $countOrders
        ];
        
        if ($countSaleCare > 0 && $countOrders > 0) {
            $result['rate'] = round($rateSuccess, 2);
            $result['product'] = $sumProduct;
            $result['total'] = round($ordersSum, 0);
            $result['avg'] = round($avgOrders, 0);
        }
        return $result;
    }


     public function getReportUserCskhDTOld($user, $dataFilter)
    {
        $rate = $avgOrders = 0;
        $result = ['name' => ($user->real_name) ?: ''];
        $newTotal = $oldTotal = $avgSum = $oldCountOrder= $newCountOrder = 0;
        $dataFilter['sale'] = $user->id;
        $dataFilter['typeDate'] = 1; //ngày data vè hệ thống
   
        $saleCare = SaleCare::where('assign_user', $user->id)
            ->where('is_duplicate', 0)->get();
        $listPhone = $saleCare->pluck('phone')->toArray();
        $contactCount = array_unique($listPhone);
        $contactCount = count($contactCount);
        
        $ordersCtl = new OrdersController();
        $orders = $ordersCtl->getListOrderByPermisson(Auth::user(), $dataFilter, true);
        $orderCount = $orders->count();
        $sumProduct = $orders->sum('qty');
        $ordersSumTotal = $orders->sum('total');
        $ordersSumTotal = round($ordersSumTotal, 0);

        if ($orderCount > 0) {
            $avgOrders = round($ordersSumTotal / $orderCount, 0);
        }

        if ($contactCount != 0) {
            $rate = $orderCount / $contactCount * 100;
            $rate = round($rate, 2);
        } else {
            $rate =  $orderCount * 100;
        }
        
        $time       = $dataFilter['daterange'];
        $timeBegin  = str_replace('/', '-', $time[0]);
        $timeEnd    = str_replace('/', '-', $time[1]);
        $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
        $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));
        $saleByTime = SaleCare::whereDate('created_at', '<=', $dateEnd)
            ->whereDate('created_at', '>=', $dateBegin)
            ->where('assign_user', $user->id)
            ->where( 'is_duplicate', 0)->get();
        $listPhoneByTime = $saleByTime->pluck('phone')->toArray();
        $contactCountByTime = array_unique($listPhoneByTime);
        $contactCountByTime = count($contactCountByTime);

        $result['old_customer'] = $result['summary_total']= [
            'contact' => $contactCount,
            'order' => $orderCount,
            'rate' =>$rate,
            'product' => $sumProduct,
            'total' => $ordersSumTotal,
            'avg' => $avgOrders,
            'contactByTime' => $contactCountByTime,
        ];

        return $result;
    }


    public function ajaxFilterDashboardDigitalV2(Request $req)
    {
        $resultDigital = $result =  $dataFilter = $list = [];
        if ($req->date && getType($req->date) == 'string') {
            $dataFilter['daterange'] = explode('-', $req->date);
        }
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

        $listDigital = $listDigital->get();
        $groupDigital = $req->groupDigital;
        if ($req->groupDigital && $groupDigital != 999) {
            $groupDi = GroupUser::find($groupDigital);
            if ($groupDi) {
                $listDigital = $groupDi->users;
            }
        }

        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
        if ($isLeadDigital) {
            $groupDi = GroupUser::where('lead_team', Auth::user()->id)->first();
            if ($groupDi) {
                $listDigital = $groupDi->users;
            }
        }

        foreach ($listDigital as $digital) {
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

        public function getDataDigitalInHome($id, $typeCustomer, $time)
    {
        $dataFilter['daterange'] = "$time - $time";
        $req = new Request();
        $req->merge(['daterange' => $dataFilter['daterange']]);
        $req->merge(['mkt_user' => $id]);
        $req->merge(['type_customer' => $typeCustomer]);
        return $this->getDataDigitalV2($req);
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

        public function getDataDigitalV2($req)
    {
        $mktController = new MarketingController();
        $data = $mktController->getDataMkt($req);
        // dd($data);
        // dd($req->all());
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

      public function exportTax()
  {
    $sale     = new OrdersController();

    // $req = new Request();
    $time = ['01/07/2025', '22/07/2025'];

    $timeBegin  = str_replace('/', '-', $time[0]);
    $timeEnd    = str_replace('/', '-', $time[1]);
    $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
    $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

    $list = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
      ->where('shipping_order.vendor_ship', 'GHN')
      ->where('orders.status', 3)
      ->whereDate('orders.created_at', '>=', $dateBegin)
      ->whereDate('orders.created_at', '<=', $dateEnd)
      ->orderBy('orders.id', 'desc');
      // ->whereIn('phone', ['0977705450','0392669158','0978091050','0388057609']);
      // ->where('orders.id', '9584');
    
      // ->where('phone', '0971724878')
      // ->limit(7)
      // ->get('orders.*');
      // ->sum('orders.total');;

    // $phoneNhatin = $this->phoneNhattin();
      //  $phoneGHTK = $this->phoneGHTK();
    
    // $listPhone = $this->getPhoneArray($phoneGHTK);
    // $list = Orders::whereIn('phone', $listPhone)
    //   ->whereDate('orders.created_at', '>=', $dateBegin)
    //   ->whereDate('orders.created_at', '<=', $dateEnd)
    //   ->get();

    $dataExport[] = [
      'Số thứ tự hóa đơn (*)' , 'Ngày hóa đơn', 'Tên đơn vị mua hàng', 'Mã khách hàng', 'Địa chỉ', 'Mã số thuế', 'Người mua hàng',
      'Email', 'Hình thức thanh toán', 'Loại tiền', 'Tỷ giá', 'Tỷ lệ CK(%)', 'Tiền CK', 'Tên hàng hóa/dịch vụ (*)', 'Mã hàng', 
      'ĐVT', 'Số lượng', 'Đơn giá', 'Tỷ lệ CK (%)', 'Tiền CK', '% thuế GTGT', 'Tiền thuế GTGT', 'Thành tiền(*)'
    ];

    $i = 1;
    $orderTmp = [];

    // dd(count($listPhone));
    // dd($list->count());
    // $list = $list->paginate(200, ['orders.*'], 'page', 5);
    // dd($list->get());
    // $listPhoneUsu = $list->pluck('phone')->toArray();
    // $phoneSearching = array_diff($listPhone, $listPhoneUsu);
    // dd($listPhoneUsu);
     $list = $list->get();
    //  dd($list);
    foreach ($list as $data) {
      // dd($data);
      //ghtk ko lấy
      /** nếu có thì bỏ qa */
      // if ($data->shippingOrder && $data->shippingOrder->vendor_ship == 'GHN') {
      //   continue;
      // }
      // dd($data);
      $timeday = new DateTime($data->created_at);
      $begin = new DateTime("2025-01-01 00:00:00");
      $end = new DateTime("2025-03-31 00:00:00");
      $orderTmp[] = $data->id;
      $listProduct = json_decode($data->id_product,true);
      // dd($listProduct);
      // if ($i == 4) {
      //   // dd($data);
      // }
      /**
       * 1/ 1 Đạm tôm 20l
       *    3kg humic
       * 2/ 1 Đạm tôm 20l + 3kg humic
       * 3/ 1 Đạm tôm 20l + 3kg humic
       *    1kg humic
       */

       //trường hợp đơn chỉ cho 1 sp
      //  dd($listProduct);
      if (count($listProduct) == 1) {
        $item = $listProduct[0];
        $product = getProductByIdHelper($item['id']);
        $percenTax = 'KCT';
        $totalGTGT = '';
        $total = $data->total;
        // dd($total);
        if (!$product) {
          continue;
        }

        $productName = $product->name;
        $k = $i;

        //check trường hợp sản phẩm cb và sản phẩm lẻ
        // có dấu + là sản phẩm combo
        if (strpos($productName, '+') !== false) {
        //  dd('hi');
          $tmp = [];
          if (strpos($productName, '3 xô tricho 10kg tặng 1 xô tricho 10kg') !== false) {
            $productName = $this->parseProductComboTricho($productName);
          }

          $items = $this->parseProductString($productName);
          $productTmp = [];
          $l = 0;
          foreach ($items as $key => $val)
          {
            $list = $this->listProductTmp();
            if ($key == 'xô tricho 10kg tặng 1 xô tricho 10kg') {
              // dd($productName);
            }
            if (!isset($list[$key])) {
              continue;
            }

            $productTmp = $list[$key];
            $percenTax = 'KCT';
            $totalGTGT = '';
            $total = 0;

            if (!$productTmp) {
              continue;
            }

            $totalOrder = $data->total;
            $productPrice = $productTmp['price'];

            $qty = $item['val'];
            $qty = $val * $qty;
    
            if (strpos($productTmp['real_name'], "Dung dịch đạm hữu cơ") !== false || strpos($productTmp['real_name'], "tôm") !== false) {
              $percenTax = '5';

              /* tổng tiền bao gồm VAT 5%: 3.150.000
                số lượng: 2 sản phẩm
                thuế VAT: 5%
                b1: tổng tiền chưa VAT = 3150000/ 1.05 = 3000000 (3tr)
                b2: tính giá chưa VAT mỗi sp: 3tr /2 = 1tr5
              */
              $taxBeforeTotal = $totalOrder / 1.05;
              $taxbeforeProduct = $taxBeforeTotal / $qty;
              $productPrice = $taxbeforeProduct;
              $totalGTGT = 0.05 * $taxBeforeTotal;
              $total = $totalOrder;
            } 
            if ($l == 0) {
               $total = $totalOrder;
            }
            $l++;

            if ($k != $i) {
              $tmp = [
                '',//Số thứ tự hóa đơn (*)
                '', // Ngày hóa đơn
                '',// Tên đơn vị mua hàng
                '',// Mã khách hàng
                '',// Địa chỉ
                '',// Mã số thuế
                '',// Người mua hàng
                '',// Email
                '',// Hình thức thanh toán
                '',// Loại tiền
                '',// Tỷ giá
                '',// Tỷ lệ CK(%)
                '',// Tiền CK
                $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                '',// Mã hàng
                $productTmp['unit'],// 'ĐVT',
                $qty,//  'Số lượng', 
                $productTmp['price'],//  'Đơn giá', 
                '',//  'Tỷ lệ CK (%)', 
                '',//  'Tiền CK',
                $percenTax, // '% thuế GTGT',
                $totalGTGT, //  'Tiền thuế GTGT',
                $total,   // 'Thành tiền(*)'
              ];
            } else {
              $tmp = [
                $i,//Số thứ tự hóa đơn (*)
                date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
                '',// Tên đơn vị mua hàng
                '',// Mã khách hàng
                $data->address,// Địa chỉ
                '',// Mã số thuế
                $data->name,// Người mua hàng
                '',// Email
                '',// Hình thức thanh toán
                '',// Loại tiền
                '',// Tỷ giá
                '',// Tỷ lệ CK(%)
                '',// Tiền CK
                $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                '',// Mã hàng
                $productTmp['unit'],// 'ĐVT',
                $qty,//  'Số lượng', 
                $productPrice,//  'Đơn giá', 
                '',//  'Tỷ lệ CK (%)', 
                '',//  'Tiền CK',
                $percenTax, // '% thuế GTGT',
                $totalGTGT, //  'Tiền thuế GTGT',
                $total,   // 'Thành tiền(*)'
              ];
            }
            $dataExport[] = $tmp;
            $k++;
          }
        } else {

          $totalBefore = $product->price;
          if (strpos($product->name, "Dung dịch đạm hữu cơ") !== false
            || strpos($product->name, "30.10.10") !== false  
          || strpos($product->name, "tôm") !== false) {
            $percenTax = '5';
            $totalGTGT = 5 * $product->price / 100;
            $totalBefore = $total - $totalGTGT;
            $tmp = [];
          }
          // dd($total);
          if ($k != $i) {
            $tmp = [
              '',//Số thứ tự hóa đơn (*)
              '', // Ngày hóa đơn
              '',// Tên đơn vị mua hàng
              '',// Mã khách hàng
              '',// Địa chỉ
              '',// Mã số thuế
              '',// Người mua hàng
              '',// Email
              '',// Hình thức thanh toán
              '',// Loại tiền
              '',// Tỷ giá
              '',// Tỷ lệ CK(%)
              '',// Tiền CK
              $product->name,// Tên hàng hóa/dịch vụ (*)
              '',// Mã hàng
              $product->unit,// 'ĐVT',
              $item->val,//  'Số lượng', 
              $totalBefore,//  'Đơn giá', 
              '',//  'Tỷ lệ CK (%)', 
              '',//  'Tiền CK',
              $percenTax, // '% thuế GTGT',
              $totalGTGT, //  'Tiền thuế GTGT',
              $total,   // 'Thành tiền(*)'
            ];  
          } else {
            $tmp = [
            $i,//Số thứ tự hóa đơn (*)
            date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
            '',// Tên đơn vị mua hàng
              '',// Mã khách hàng
              $data->address,// Địa chỉ
              '',// Mã số thuế
              $data->name,// Người mua hàng
              '',// Email
              '',// Hình thức thanh toán
              '',// Loại tiền
              '',// Tỷ giá
              '',// Tỷ lệ CK(%)
              '',// Tiền CK
              $product->name,// Tên hàng hóa/dịch vụ (*)
              '',// Mã hàng
              $product->unit,// 'ĐVT',
              $item['val'],//  'Số lượng', 
              $totalBefore,//  'Đơn giá', 
              '',//  'Tỷ lệ CK (%)', 
              '',//  'Tiền CK',
              $percenTax, // '% thuế GTGT',
              $totalGTGT, //  'Tiền thuế GTGT',
              $total,   // 'Thành tiền(*)'
            ];
          }
          
          $dataExport[] = $tmp;
          $k++;
          
        }
      } 
      /** số tổng sản phẩm lớn hơn 1 */
      else {
        $j = $i;
        // dd($listProduct);
        foreach ($listProduct as $item) {
          $product = getProductByIdHelper($item['id']);
          $percenTax = 'KCT';
          $totalGTGT = '';
          $total = 0;
          
          $tmp = [];
          if (!$product) {
            continue;
          }
          // if ($product->id == 69) {
          //   continue;
          // }
          // dd($product);
          $productName = $product->name;
          // dd($productName);
          if (strpos($productName, '+') !== false) {

            if (strpos($productName, '3 xô tricho 10kg tặng 1 xô tricho 10kg') !== false ) {
              $productName = $this->parseProductComboTricho($productName);
            }

            // if (strpos($productName, '3 xô tricho 10kg + 1 Aplus + 9kg Humic') !== false ) {
            //   $productName = $this->parseProductComboTrichoAplus($productName);
            // }
            $items = $this->parseProductString($productName);
            $productTmp = [];
            // dd($items);
            $l = 0;
            foreach ($items as $key => $val)
            {
              // if ($key != 'siêu lớn trái') {
              //   continue;
              // }
              $list = $this->listProductTmp();
              // dd($list);
              if ($key == 'xô tricho 10kg tặng 1 aplus') {
                dd($data->id);
              }
              $productTmp = $list[$key];
              $percenTax = 'KCT';
              $totalGTGT = '';
              $total = 0;
              $totalOrder = $data->total;
              
              if (!$productTmp) {
                continue;
              }
              $productPrice = $productTmp['price'];
              $qty = $item['val'];
              $qty = $val * $qty;
              // dd($productTmp);
              if (strpos($productTmp['real_name'], "Dung dịch đạm hữu cơ") !== false || strpos($productTmp['real_name'], "tôm") !== false) {
                $percenTax = '5';

                /* tổng tiền bao gồm VAT 5%: 3.150.000
                  số lượng: 2 sản phẩm
                  thuế VAT: 5%
                  b1: tổng tiền chưa VAT = 3150000/ 1.05 = 3000000 (3tr)
                  b2: tính giá chưa VAT mỗi sp: 3tr /2 = 1tr5
                */
                $taxBeforeTotal = $totalOrder / 1.05;
                $taxbeforeProduct = $taxBeforeTotal / $qty;
                $productPrice = $taxbeforeProduct;
                $totalGTGT = 0.05 * $taxBeforeTotal;
                $total = $totalOrder;
              } 

              if ($l == 0) {
                $total = $totalOrder;
              }
              $l++;

              if ($j != $i) {
                $tmp = [
                  '',//Số thứ tự hóa đơn (*)
                  '', // Ngày hóa đơn
                  '',// Tên đơn vị mua hàng
                  '',// Mã khách hàng
                  '',// Địa chỉ
                  '',// Mã số thuế
                  '',// Người mua hàng
                  '',// Email
                  '',// Hình thức thanh toán
                  '',// Loại tiền
                  '',// Tỷ giá
                  '',// Tỷ lệ CK(%)
                  '',// Tiền CK
                  $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                  '',// Mã hàng
                  $productTmp['unit'],// 'ĐVT',
                  $qty,//  'Số lượng', 
                  $productPrice,//  'Đơn giá', 
                  '',//  'Tỷ lệ CK (%)', 
                  '',//  'Tiền CK',
                  $percenTax, // '% thuế GTGT',
                  $totalGTGT, //  'Tiền thuế GTGT',
                  $total,   // 'Thành tiền(*)'
                ];
              } else {
                $tmp = [
                  $i,//Số thứ tự hóa đơn (*)
                  date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
                  '',// Tên đơn vị mua hàng
                  '',// Mã khách hàng
                  $data->address,// Địa chỉ
                  '',// Mã số thuế
                  $data->name,// Người mua hàng
                  '',// Email
                  '',// Hình thức thanh toán
                  '',// Loại tiền
                  '',// Tỷ giá
                  '',// Tỷ lệ CK(%)
                  '',// Tiền CK
                  $productTmp['real_name'],// Tên hàng hóa/dịch vụ (*)
                  '',// Mã hàng
                  $productTmp['unit'],// 'ĐVT',
                  $qty,//  'Số lượng', 
                  $productPrice,//  'Đơn giá', 
                  '',//  'Tỷ lệ CK (%)', 
                  '',//  'Tiền CK',
                  $percenTax, // '% thuế GTGT',
                  $totalGTGT, //  'Tiền thuế GTGT',
                  $total,   // 'Thành tiền(*)'
                ];
              }
  

              $dataExport[] = $tmp;
              $j++;
            }
              
          } else {
            // dd($data->total);
            $totalOrder = $data->total;
            $productPrice = $product->price;
            $qty = $item['val'];
            // dd(strpos($product->name, "Tôm") !== false || strpos($product->name, "tôm") !== false);
            if (strpos($product->name, "30.10.10") !== false || strpos($product->name, "Dung dịch đạm hữu cơ") !== false || strpos($product->name, "tôm") !== false) {
              $percenTax = '5';
              /* tổng tiền bao gồm VAT 5%: 3.150.000
                số lượng: 2 sản phẩm
                thuế VAT: 5%
                b1: tổng tiền chưa VAT = 3150000/ 1.05 = 3000000 (3tr)
                b2: tính giá chưa VAT mỗi sp: 3tr /2 = 1tr5
              */
              $taxBeforeTotal = $totalOrder / 1.05;
              $taxbeforeProduct = $taxBeforeTotal / $qty;
              $productPrice = $taxbeforeProduct;
              $totalGTGT = 0.05 * $taxBeforeTotal;
              $total = $totalOrder;
            } else if (strpos($product->name, "Áo mưa (hàng tặng không bán)") !== false ) {
              $percenTax = '8';
              $total = '0';
              $totalGTGT = 4720;
              $productPrice = 63720;
            }
          
            if ($j != $i) {
              $tmp = [
                '',//Số thứ tự hóa đơn (*)
                '', // Ngày hóa đơn
                '',// Tên đơn vị mua hàng
                '',// Mã khách hàng
                '',// Địa chỉ
                '',// Mã số thuế
                '',// Người mua hàng
                '',// Email
                '',// Hình thức thanh toán
                '',// Loại tiền
                '',// Tỷ giá
                '',// Tỷ lệ CK(%)
                '',// Tiền CK
                $product->name,// Tên hàng hóa/dịch vụ (*)
                '',// Mã hàng
                $product->unit,// 'ĐVT',
                $qty,//  'Số lượng', 
                $productPrice,//  'Đơn giá', 
                '',//  'Tỷ lệ CK (%)', 
                '',//  'Tiền CK',
                $percenTax, // '% thuế GTGT',
                $totalGTGT, //  'Tiền thuế GTGT',
                $total,   // 'Thành tiền(*)'
              ];  
            } else {
                // dd($product->name);
              $tmp = [
              $i,//Số thứ tự hóa đơn (*)
              date_format($data->created_at,"d-m-Y "), // Ngày hóa đơn
              '',// Tên đơn vị mua hàng
                '',// Mã khách hàng
                $data->address,// Địa chỉ
                '',// Mã số thuế
                $data->name,// Người mua hàng
                '',// Email
                '',// Hình thức thanh toán
                '',// Loại tiền
                '',// Tỷ giá
                '',// Tỷ lệ CK(%)
                '',// Tiền CK
                $product->name,// Tên hàng hóa/dịch vụ (*)
                '',// Mã hàng
                $product->unit,// 'ĐVT',
                $qty,//  'Số lượng', 
                $productPrice,//  'Đơn giá', 
                '',//  'Tỷ lệ CK (%)', 
                '',//  'Tiền CK',
                $percenTax, // '% thuế GTGT',
                $totalGTGT, //  'Tiền thuế GTGT',
                $total,   // 'Thành tiền(*)'
              ];
            }
            
            $dataExport[] = $tmp;
            $j++;
          }
        }
       
      }
      $i++;
    }

    // dd(($dataExport));
    // dd(($orderTmp));

    // echo "<pre>";
    // print_r($dataExport);
    // echo "</pre>";
    // dd($dataExport);
    
    return Excel::download(new UsersExport($dataExport), 'GHTK-thang-06.xlsx');
  }

      public function index2()
    {
        $toMonth      = date("d/m/Y", time());

        /**set tmp */
        $toMonth = '10/05/2025';

        $dataSaleCSKH = $this->getReportCskhDamTom($toMonth);
        // dd($dataSaleCSKH);
        $category = Category::where('status', 1)->get();
        $sales = User::where('status', 1)->where('is_sale', 1)->orWhere('is_cskh', 1)->get();
        $groups = Group::orderBy('id', 'desc')->get();
        $groupUser = GroupUser::orderBy('id', 'desc')->get();
        return view('pages.home2')->with('category', $category)->with('sales', $sales)
            ->with('dataSaleCSKH', $dataSaleCSKH)
            ->with('groups', $groups)
            ->with('groupUser', $groupUser);
    }


        public function getReportUserCskhDT($user, $dataFilter)
    {
        $rate = $avgOrders = 0;
        $result = ['name' => ($user['real_name']) ?: ''];
        $dataFilter['sale'] = $user['id'];
        $dataFilter['typeDate'] = 1; //ngày data vè hệ thống
   
        $saleCare = SaleCare::where('assign_user', $user['id'])
            ->where('is_duplicate', 0);

        if (isset($dataFilter['group'])) {
            $saleCare   = $saleCare->where('group_id', $dataFilter['group']);
        }
        $saleCare = $saleCare->get();
        $listPhone = $saleCare->pluck('phone')->toArray();
        $contactCount = array_unique($listPhone);
        $contactCount = count($contactCount);
        $ordersCtl = new OrdersController();
        $orders = $ordersCtl->getListOrderByPermisson(Auth::user(), $dataFilter, true);
        $orderCount = $orders->count();
        $sumProduct = $orders->sum('qty');
        $ordersSumTotal = $orders->sum('total');
        $ordersSumTotal = round($ordersSumTotal, 0);

        if ($orderCount > 0) {
            $avgOrders = round($ordersSumTotal / $orderCount, 0);
        }

        if ($contactCount != 0) {
            $rate = $orderCount / $contactCount * 100;
            $rate = round($rate, 2);
        } else {
            $rate =  $orderCount * 100;
        }
        
        $saleCtl = new SaleController();
        $saleByTime  = $saleCtl->getListSalesByPermisson(Auth::user(), $dataFilter)
            ->where( 'is_duplicate', 0);   

        $listPhoneByTime = $saleByTime->pluck('phone')->toArray();
        $contactCountByTime = array_unique($listPhoneByTime);
        $contactCountByTime = count($contactCountByTime);
        $result['old_customer'] = $result['summary_total']= [
            'contact' => $contactCount,
            'order' => $orderCount,
            'rate' =>$rate,
            'product' => $sumProduct,
            'total' => $ordersSumTotal,
            'avg' => $avgOrders,
            'contactByTime' => $contactCountByTime,
        ];

        return $result;
    }

        public function marketingSearch($req)
    {
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

        public function getDataMkt($req)
    {
        $list = SrcPage::query();
        if ($req->mkt_user && $req->mkt_user != -1) {
            $list = SrcPage::where('user_digital', $req->mkt_user);
        }

        if ($req->src && $req->src != -1) {
            $list = SrcPage::where('id', $req->src);
        }

        /** lấy data report(contact) từ list nguồn */
        $listFiltrSrc = $this->getListMktReportByListSrc($list, $req);
        $listFiltrSrc = $this->transferKey($listFiltrSrc);

        $rs = $this->getListMktReportOrder($req, $listFiltrSrc);
        if ($rs) {
            $rs = $this->cleanDataMktReport($rs);
        }

        return $rs;
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
        // dd($countOrder);
       
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
            // dd($countOrder == 0);
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

        // dd($newCustomer);
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
        $result = $this->getDataDigitalV4($dataFilter);
        // dd($result);
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

        dd($result);

        return $result;
    }
    public function getReportUserDigitalV2($digitals, $dataFilter) 
    {
        $ids = $digitals->pluck('id')->toArray();
        // $ids = [73];
        $str = "";
        if (isset($dataFilter['daterange'])) {
            $time = $dataFilter['daterange'];
            $time = Helper::converDateSql($time);
            $str .= "AND sc.created_at BETWEEN '$time[0] 00:00:00' AND '$time[1] 23:59:59'";
        }

        if ($ids) {
            $strIds = json_encode($ids);
            $strIds  = str_replace('[', '(', $strIds);
            $strIds  = str_replace(']', ')', $strIds);
            $str .= " AND src.user_digital IN $strIds";
        }

        $sqlNew = 
            "SELECT 
                u.id, u.real_name as name,
                COUNT(sc.id) AS contact,
                COUNT(o.id) AS count_order,
                COALESCE(ROUND(COUNT( o.id) * 100.0 / NULLIF(COUNT( sc.id), 0), 2), 0) AS rate,
                COALESCE(SUM(o.total), 0) AS total,
                COALESCE(SUM(o.qty), 0)AS product,
                COALESCE(ROUND(SUM(o.total) / NULLIF(COUNT( o.id), 0), 2), 0) AS avg
            FROM src_page src
            JOIN users u on u.id = src.user_digital
            JOIN sale_care sc on sc.src_id = src.id
            LEFT JOIN orders o on o.sale_care = sc.id
            WHERE sc.old_customer IN (0,2)
            $str
            GROUP BY u.id, name
            ORDER BY total DESC";
 
        $dataNew = DB::select($sqlNew);
        $dataNew = json_decode(json_encode($dataNew), true);
        $sqlOld = 
            "SELECT 
                u.id, u.real_name as name,
                COUNT(sc.id) AS contact,
                COUNT(o.id) AS count_order,
                COALESCE(ROUND(COUNT( o.id) * 100.0 / NULLIF(COUNT( sc.id), 0), 2), 0) AS rate,
                COALESCE(SUM(o.total), 0) AS total,
                COALESCE(SUM(o.qty), 0) AS product,
                COALESCE(ROUND(SUM(o.total) / NULLIF(COUNT( o.id), 0), 2), 0) AS avg
            FROM src_page src
            JOIN users u on u.id = src.user_digital
            JOIN sale_care sc on sc.src_id = src.id
            LEFT JOIN orders o on o.sale_care = sc.id
            WHERE sc.old_customer = 1
            $str
            GROUP BY u.id, name
            ORDER BY total DESC";
        $dataOld = DB::select($sqlOld);
        $dataOld = json_decode(json_encode($dataOld), true);

        $new_cusomer = [];
        foreach ($dataNew as $new) {
            $new_cusomer[] = [
                'id' => $new['id'],
                'name' => $new['name'],
                'new_customer' => $new
            ];
        }
        
        $old_cusomer = [];
        foreach ($dataOld as $old) {
            $old_cusomer[] = [
                'id' => $old['id'],
                'name' => $old['name'],
                'old_customer' => $old
            ];
        }

        $merged = array_merge($old_cusomer, $new_cusomer);
        $result = [];

        foreach ($merged as $item) {
            $id = $item['id'];
            if (!isset($result[$id])) {
                $result[$id] = [
                    'id' => $id,
                    'name' => $item['name']
                ];
            }
            if (isset($item['new_customer'])) {
                $result[$id]['new_customer'] = $item['new_customer'];
            } 

            if (isset($item['old_customer'])) {
                $oldCustomer = $item['old_customer'];
                $result[$id]['old_customer'] = $item['old_customer'];
            }
        }

        $e = [
            'contact' => 0,
            'count_order' => 0,
            'rate' => 0,
            'total' => 0,
            'product' => 0,
            'avg' => 0,
        ];

        foreach ($result as $k => $data) {
            if (!isset($data['new_customer'])) {
                $result[$k]['new_customer'] = $newCustomer = $e;
            } else {
                $newCustomer = $data['new_customer'];
            }

            if (!isset($data['old_customer'])) {
                 $result[$k]['old_customer'] = $oldCustomer = $e;
            } else {
                $oldCustomer = $data['old_customer'];
            }

            $newTotal = $oldTotal = $avgSum = 0;
            if (isset($newCustomer['total']) && $newCustomer['total'] > 0) {
                $newTotal = Helper::stringToNumberPrice($newCustomer['total']);
            } 
            if (isset($oldCustomer['total']) && $oldCustomer['total'] > 0) {
                $oldTotal = Helper::stringToNumberPrice($oldCustomer['total']);
            }
            
            $oldCountOrder = $oldCustomer['count_order'];
            $newCountOrder = $newCustomer['count_order'];
            $totalSum = $newTotal + $oldTotal;
            if ($newCountOrder != 0 || $oldCountOrder != 0) {
                $avgSum = $totalSum / ($newCountOrder + $oldCountOrder);
            }

            $rateSum = 0;
            $contactSum = $newCustomer['contact'];
            $orderSum = $oldCustomer['count_order'] + $newCustomer['count_order'];
            if ($contactSum > 0) {
                $rateSum = $orderSum / $contactSum * 100;
            } else {
                $rateSum = $orderSum * 100;
            }

            $result[$k]['summary_total'] = [
                'total' => round($totalSum, 0),
                'avg' => round($avgSum, 0),
                'rate' => round($rateSum, 2)
            ];
        }
        
        return $result;
    }

        public function index2(Request $r) 
    {
        Log::info('run api ladipag 212e');

        // dd($r->all());
        $phone = $r->phone;
        $name = $r->name;
        // $email = $r->email;
        $item = $r->form_item3209;
        $address = $r->address;
        $linkPage = $r->link;
        
        $messages = $item;
        if ( $address) {
            $messages .= "\n" . $address;
        }
        $all = json_encode($r->all());
        Log::info('sao z');
                Log::info($all);

                $ladiPage = Helper::getConfigLadiPage();
                $namePage = 'Ladi Page';
                
                $assgin_user = 0;
                $is_duplicate = 0;
                $isOldDataLadi = Helper::isOldDataLadi($phone, $linkPage, $assgin_user);

                if (!$isOldDataLadi) {
                    $assignSale = Helper::getAssignSale();
                    $assgin_user = $assignSale->id;
                } else {
                    $is_duplicate = 1;
                }

                if($ladiPage->status == 1) {
                    $sale = new SaleController();
                        $data = [
                        'page_link' => $linkPage,
                        'page_name' => $namePage,
                        'sex'       => 0,
                        'old_customer' => 0,
                        'address'   => '...',
                        'messages'  => $messages,
                        'name'      => $name,
                        'phone'     => $phone,
                        'text'      => $namePage,
                        'chat_id'   => 'id_VUI',
                        'assgin'    => $assgin_user,
                        'is_duplicate' => $is_duplicate
                        ];

                        $request = new \Illuminate\Http\Request();
                        $request->replace($data);
                        $sale->save($request);
                }
                return response()->json(['success' => 'oke'], 200);
            }
            
                public function index(Request $r) 
            {
                $all = json_encode($r->all());
                // Log::info($all);

                $phone = ($r->phone) ? $r->phone : $r->phone_number;
                $name = ($r->name) ?? 'Không để tên';

                $item = $r->form_item3209;
                $address = $r->address;
                $linkPage = $r->link;
                $linkPage = $r->ip;
                $messages = $item;
                if ( $address) {
                    $messages .= "\n" . $address;
                }

                $all = json_encode($r->all());
                $str = $all;
                $arr = json_decode($str, true);
                $linkPage = $arr['link'];

                $assgin_user = 0;
                $is_duplicate = 0;

                /** lấy list token của nguồn ladi (token = tricho-bacillus-km ....) */
                $listSrcLadi = SrcPage::where('type', 'ladi')
                    ->whereNotNull("id_page")->get();
                
                // Lấy phần path từ URL
                $path = parse_url($linkPage, PHP_URL_PATH);

                // Lấy phần cuối cùng của path
                $slug = basename($path);
                // dd($slug);
                foreach ($listSrcLadi as $src) {
                    if ($slug && $slug == $src->id_page) {
                        $group = $src->group;
                        // Log::info(json_encode($src));
                        break;
                    }
                }

                if (!$src) {
                    return;
                }
                $group = $src->group;
                
                $blockPhone = ['0963339609','0344999668', '0344411068', '0841111116', '0841265116', '0986987791', '0332783056', '0985767791',
                    '0918352409', '0841265117', '0348684430', '0777399687'];

                if ($group && !in_array($phone, $blockPhone)) {
                    $chatId = $group->tele_hot_data;
                    $phone = Helper::getCustomPhoneNum($phone);
                    $hasOldOrder = $isOldOrder = 0;
                    $typeCSKH = 1;
                    $isOldDataLadi = Helper::isOldDataLadi($phone, $assgin_user, $group, $hasOldOrder, $is_duplicate, $isOldOrder);
                    
                    $flagSpam = false;
                    if (Helper::isSeeding($phone)) {
                        Log::channel('ladi')->info('Số điện thoại đã nằm trong danh sách spam/seeding ladi..' . $phone);
                        // return;
                        $flagSpam= true;
                    }

                    if (!$flagSpam) {
                        if (!$isOldDataLadi || $assgin_user == 0) {
                        /** khách mới hoàn toàn */
                        $assignSale = Helper::getAssignSaleByGroup($group)->user;
                        
                        } else {
                            /** khách cũ */
                            $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldOrder);
                        }

                        if (!$assignSale) {
                            return;
                        }

                        if ($isOldOrder == 1) {
                            $chatId = $group->tele_cskh_data;
                        }
                    } else {
                        $assignSale = User::find(55);
                        $chatId = '-4286962864';
                    }      

                    $assgin_user = $assignSale->id;
                    $pageNameLadi = 'Ladi Page. Link: ' . $linkPage;
                    $sale = new SaleController();
                    $data = [
                        'page_link' => $linkPage,
                        'page_name' => $pageNameLadi,
                        'sex'       => 0,
                        'old_customer' => $isOldOrder,
                        'address'   => '',
                        'messages'  => $messages,
                        'name'      => $name,
                        'phone'     => $phone,
                        'page_id'   => $src->id_page,
                        'text'      => $pageNameLadi,
                        'chat_id'   => $chatId,
                        'm_id'      => 'mId',
                        'assgin' => $assgin_user,
                        'is_duplicate' => $is_duplicate,
                        'group_id'  => $group->id,
                        'has_old_order'  => $hasOldOrder,
                        'src_id' => $src->id,
                        'type_TN' => $typeCSKH, 
                        'access' => true,
                    ];

                    // dd($data);

                    $request = new \Illuminate\Http\Request();
                    $request->replace($data);
                    $sale->save($request);
                }

            return response()->json(['success' => true, 'isSpam' => $flagSpam], 200);
                
            }

            public function index_21_0_2025(Request $r) 
            {
                $all = json_encode($r->all());
        Log::channel('ladi')->info($all);
                Log::channel('ladi')->info($all);
                $phone = ($r->phone) ? $r->phone : $r->phone_number;
                $name = ($r->name) ?? 'Không để tên';

                $item = $r->form_item3209;
                $address = $r->address;
                $linkPage = $r->link;
                
                $messages = $item;
                if ( $address) {
                    $messages .= "\n" . $address;
                }

                // $all = json_encode($r->all());
                $str = $all;
                $arr = json_decode($str, true);
                $linkPage = $arr['link'];

                $assgin_user = 0;
                $is_duplicate = 0;

                /** lấy list token của nguồn ladi (token = tricho-bacillus-km ....) */
                $listSrcLadi = SrcPage::where('type', 'ladi')
                    ->whereNotNull("id_page")->get();
                
                // Lấy phần path từ URL
                $path = parse_url($linkPage, PHP_URL_PATH);

                // Lấy phần cuối cùng của path
                $slug = basename($path);
                // dd($slug);
                Log::channel('ladi')->info($slug);
                foreach ($listSrcLadi as $src) {
                    if ($slug && $slug == $src->id_page) {
                    // if (str_contains($linkPage, $src->id_page)) {
                        $group = $src->group;
                        break;
                    }
                }


                if (!$src || !$group) {
                    return;
                }
                
                $blockPhone = ['0963339609','0344999668', '0344411068', '0841111116', '0841265116', '0986987791', '0332783056', '0985767791',
                    '0918352409', '0841265117', '0348684430', '0777399687'];

                if ($group && !in_array($phone, $blockPhone)) {
                    $chatId = $group->tele_hot_data;
                    $phone = Helper::getCustomPhoneNum($phone);
                    $hasOldOrder = $isOldOrder = 0;
                    $typeCSKH = 1;
                    $isOldDataLadi = Helper::isOldDataLadi($phone, $assgin_user, $group, $hasOldOrder, $is_duplicate, $isOldOrder);
                    
                    if (Helper::isSeeding($phone)) {
                        Log::channel('ladi')->info('Số điện thoại đã nằm trong danh sách spam/seeding ladi..' . $phone);
                        return;
                    }
                    if (!$isOldDataLadi || $assgin_user == 0) {
                        /** khách mới hoàn toàn */
                        $assignSale = Helper::getAssignSaleByGroup($group)->user;
                        
                    } else {
                        /** khách cũ */
                        $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldOrder);
                    }

                    if (!$assignSale) {
                        return;
                    }
        Log::channel('ladi')->info('pass gr');
            $assgin_user = $assignSale->id;
            $pageNameLadi = 'Ladi Page. Link: ' . $linkPage;
            $sale = new SaleController();
            $data = [
                'page_link' => $linkPage,
                'page_name' => $pageNameLadi,
                'sex'       => 0,
                'old_customer' => $isOldOrder,
                'address'   => '',
                'messages'  => $messages,
                'name'      => $name,
                'phone'     => $phone,
                'page_id'   => $src->id_page,
                'text'      => $pageNameLadi,
                'chat_id'   => $chatId,
                'm_id'      => 'mId',
                'assgin' => $assgin_user,
                'is_duplicate' => $is_duplicate,
                'group_id'  => $group->id,
                'has_old_order'  => $hasOldOrder,
                'src_id' => $src->id,
                'type_TN' => $typeCSKH, 
            ];

            // dd($data);
        Log::channel('ladi')->info(json_encode($data));
            $request = new \Illuminate\Http\Request();
            $request->replace($data);
            $sale->save($request);
        }
        
        return response()->json(['success' => 'oke'], 200);
    }

     public function index_21_0_2025(Request $r) 
    {
        $all = json_encode($r->all());
        Log::channel('ladi')->info($all);
                Log::channel('ladi')->info($all);
                $phone = ($r->phone) ? $r->phone : $r->phone_number;
                $name = ($r->name) ?? 'Không để tên';

                $item = $r->form_item3209;
                $address = $r->address;
                $linkPage = $r->link;
                
                $messages = $item;
                if ( $address) {
                    $messages .= "\n" . $address;
                }

                // $all = json_encode($r->all());
                $str = $all;
                $arr = json_decode($str, true);
                $linkPage = $arr['link'];

                $assgin_user = 0;
                $is_duplicate = 0;

                /** lấy list token của nguồn ladi (token = tricho-bacillus-km ....) */
                $listSrcLadi = SrcPage::where('type', 'ladi')
                    ->whereNotNull("id_page")->get();
                
                // Lấy phần path từ URL
                $path = parse_url($linkPage, PHP_URL_PATH);

                // Lấy phần cuối cùng của path
                $slug = basename($path);
                // dd($slug);
                Log::channel('ladi')->info($slug);
                foreach ($listSrcLadi as $src) {
                    if ($slug && $slug == $src->id_page) {
                    // if (str_contains($linkPage, $src->id_page)) {
                        $group = $src->group;
                        break;
                    }
                }


                if (!$src || !$group) {
                    return;
                }
                
                $blockPhone = ['0963339609','0344999668', '0344411068', '0841111116', '0841265116', '0986987791', '0332783056', '0985767791',
                    '0918352409', '0841265117', '0348684430', '0777399687'];

                if ($group && !in_array($phone, $blockPhone)) {
                    $chatId = $group->tele_hot_data;
                    $phone = Helper::getCustomPhoneNum($phone);
                    $hasOldOrder = $isOldOrder = 0;
                    $typeCSKH = 1;
                    $isOldDataLadi = Helper::isOldDataLadi($phone, $assgin_user, $group, $hasOldOrder, $is_duplicate, $isOldOrder);
                    
                    if (Helper::isSeeding($phone)) {
                        Log::channel('ladi')->info('Số điện thoại đã nằm trong danh sách spam/seeding ladi..' . $phone);
                        return;
                    }
                    if (!$isOldDataLadi || $assgin_user == 0) {
                        /** khách mới hoàn toàn */
                        $assignSale = Helper::getAssignSaleByGroup($group)->user;
                        
                    } else {
                        /** khách cũ */
                        $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldOrder);
                    }

                    if (!$assignSale) {
                        return;
                    }
        Log::channel('ladi')->info('pass gr');
            $assgin_user = $assignSale->id;
            $pageNameLadi = 'Ladi Page. Link: ' . $linkPage;
            $sale = new SaleController();
            $data = [
                'page_link' => $linkPage,
                'page_name' => $pageNameLadi,
                'sex'       => 0,
                'old_customer' => $isOldOrder,
                'address'   => '',
                'messages'  => $messages,
                'name'      => $name,
                'phone'     => $phone,
                'page_id'   => $src->id_page,
                'text'      => $pageNameLadi,
                'chat_id'   => $chatId,
                'm_id'      => 'mId',
                'assgin' => $assgin_user,
                'is_duplicate' => $is_duplicate,
                'group_id'  => $group->id,
                'has_old_order'  => $hasOldOrder,
                'src_id' => $src->id,
                'type_TN' => $typeCSKH, 
            ];

            // dd($data);
        Log::channel('ladi')->info(json_encode($data));
            $request = new \Illuminate\Http\Request();
            $request->replace($data);
            $sale->save($request);
        }
        
        return response()->json(['success' => 'oke'], 200);
    }