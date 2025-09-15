<?php

namespace App\Http\Controllers;

use App\Models\CategoryCall;
use App\Models\SaleCareDataCountAction;
use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\HomeController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\SaleCare;
use App\Helpers\Helper;
use App\Models\CallResult;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Product;
use App\Models\SaleCareHistoryTN;
use App\Models\ShippingOrder;
use App\Models\TypeDate;
use PHPUnit\TextUI\Help;
use Illuminate\Support\Facades\Route;
use App\Models\SrcPage;
use Illuminate\Support\Facades\File as File2;
use Image;
use Session;

use Illuminate\Support\Facades\Log;
class SaleController extends Controller
{
    public function seachSaleCareAPi(Request $request)
    {
        $search = $request->q;
        $list  = SaleCare::orWhere('full_name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhere('full_name', 'like', '%' . $search . '%')
            ->get();

        return response()->json($list);
    }

    public function processViewRank($dataFilter)
    {
        $listSale = Helper::getListSaleV2(Auth::user(), true)
            ->select('id', 'real_name', 'profile_image');

        $list = $dataSort = [];
        $homeCtl = new HomeController();
        foreach ($listSale->get() as $sale) {
            // dd($sale);
            $data = $homeCtl->getReportUserSaleV2($sale, $dataFilter, true);
            if ($data) {
                $list[] = $data;
            }
        }

        $dataSort = $this->selection_sort($list);
        return $dataSort;
    }

    public function ajaxViewRank(Request $r)
    {
        $dataFilter['daterange'] = $r->date;
        return $this->processViewRank($dataFilter);

        //prepare clean
        $listSale = Helper::getListSaleV2(Auth::user(), true);
        $list = $dataSort = [];
        $homeCtl = new HomeController();
        foreach ($listSale->get() as $sale) {
            $data = $homeCtl->getReportUserSaleV2($sale, $dataFilter, true);
            $list[] = $data;
        }
       
        $dataSort = $this->selection_sort($list);

        return $dataSort;
    }

    public function viewRankSale()
    {
        $isMkt = Helper::isMkt(Auth::user());
        if ($isMkt) {
            return redirect()->route('home');
        }

        $toMonth      = date("d/m/Y", time());
        $dataFilter['daterange'] = [$toMonth, $toMonth];
        $dataSort = $this->processViewRank($dataFilter);

        return view('pages.sale.rank')->with('dataSort', $dataSort);

        //prepare clean
        $homeCtl = new HomeController();
        /**set tmp */
        // $toMonth = '01/12/2024';
       
        $checkAll = isFullAccess(Auth::user()->role);
        $dataFilter['daterange'] = [$toMonth, $toMonth];
        // return $this->ajaxViewRank();
        if ($checkAll) {
            $listGroup = GroupUser::where('status', 1)->get();
            
            foreach ($listGroup as $gr) {
                $listSale =  Helper::getListSaleV3(Auth::user());
                foreach ($listSale as $sale) {
                    $data = $homeCtl->getReportUserSaleV2($sale, $dataFilter);
                    $result[] = $data;   
                }
            }
            $dataSale = $result;
        } else if (Helper::isCskhDT(Auth::user())) {
            $dataSale = $homeCtl->getReportCskhDamTom($toMonth, false, true);
        } else {
            $dataSale = $homeCtl->getReportUserSaleV2(Auth::user(), $dataFilter);
        }

        $dataSort = [];
        if ($dataSale) {
            $dataSort = $this->selection_sort($dataSale);
        }
       
        return view('pages.sale.rank')->with('dataSort', $dataSort);
    }

    private function swap_positions($data1, $left, $right) {  
        $backup_old_data_right_value = $data1[$right];  
        $data1[$right] = $data1[$left];  
        $data1[$left] = $backup_old_data_right_value;  
        return $data1;  
    } 

    private function selection_sort($data)  
	{  
		for($i=0; $i < count($data)-1; $i++) {  
			$min = $i;  
			for($j=$i+1; $j < count($data); $j++) {  
				if ($data[$j]['summary_total']['total'] > $data[$min]['summary_total']['total']) {  
					$min = $j;  
				}  
			}  
			$data = $this->swap_positions($data, $i, $min);  
		}  
		return $data;  
	}  

    public function viewlistDuplicateByPhone($phone)
    {
        $result = [];
        $list = SaleCare::where('phone', $phone)
            ->orderBy('id', 'desc');
        foreach ($list->get() as $item) {
            if ($item->issetDuplicate && $item->duplicate_id) {
                $result[] = SaleCare::find($item->duplicate_id);
            }
            $result[] = $item;
        }
        
        return view('pages.sale.duplicate')->with('list', $result);
    }

    public function saveBoxTN(Request $req)
    {
        $input = $req->all();
        $validator      = Validator::make($input, [
            'note'      => 'required',
            ],[
                'name.required' => 'Nhập ghi chú cho tác nghiệp',
            ]
        );

        if ($validator->passes()) {
            $files = $files_remove = [];
            // dd($req->file('filenames'));
            if ($req->id) {
                $his = SaleCareHistoryTN::find($req->id); 
                if (isset($input['images_uploaded'])) {
                    $files_remove = array_diff(json_decode($input['images_uploaded_origin']), $input['images_uploaded']);
                    $files = array_merge($input['images_uploaded'], $files);
                } else if (isset($input['images_uploaded_origin'])) {
                    $files_remove = json_decode($input['images_uploaded_origin']);
                }
            } else {
                $his = new SaleCareHistoryTN();
                $his->sale_id = $req->sale_id;
            }

           
            if($req->hasfile('filenames'))
            {
                foreach($req->file('filenames') as $file)
                {
                    $name = time().rand(1,100).'.'.$file->extension();
                    // $file->move(public_path('files'), $name);  
                    $path = public_path('files') . "/" . $name;
                    Image::make($file->getRealPath())->resize(300, 500)->save($path);
                    $files[] = $name;
                }
            }

            $his->img = json_encode($files);
            $his->note = $req->note;
            if ($his->save()) {
                foreach ($files_remove as $file_name) {
                    File2::delete(public_path("files/" . $file_name));
                }
            }

            // setDataTNLogHelper($req->sale_id, 'Ghi chú TN');
            // notify()->success('Lưu TN hôm nay thành công', 'Thành công!');
            return redirect()->back();
            // return redirect()->route('sale-view-TN-box', ['id' => $req->sale_id]);
        } else {
            // dd($validator->errors());
            // notify()->error('Đã xảy ra lỗi khi lưu tác nghiệp hôm nay', 'Thất bại!');
            return back()->withErrors($validator->errors());
        }
    }
    public function saleViewSaveTNBox($id)
    {
        $saleCare = SaleCare::find($id);
        $history = SaleCareHistoryTN::where('sale_id', $id)
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->first();
        $listHistory = $saleCare->listHistory;
            // notify()->success('Lưu TN', 'Thành công!');
        return view('pages.sale.addBoxTN')->with('history', $history)
            ->with('saleId', $id)->with('saleCare', $saleCare)
            ->with('listHistory', $listHistory);
    }
    public function saleViewListTNBox($id)
    {
        $saleCare = SaleCare::find($id);

        if ($saleCare) {
            $listHistory = $saleCare->listHistory;
            return view('pages.sale.historyBoxTN')->with('saleId', $id)
                ->with('listHistory', $listHistory)->with('saleCare', $saleCare);
        }
        
        return redirect('/');
    }

    public function getReportCountTNByType($listSaleCare, $listCateCall)
    {
        $result = [];   
        $listId = $listSaleCare->pluck('id')->toArray();

        foreach ($listCateCall as $cate) {
            $sum = $this->getCountTNByType($listId, $cate->id);
            $yetTN = $this->getCountTNByType($listId, $cate->id, false);
            $result[] = [
                'data' => $cate,
                'sum' => $sum,
                'yetTN' => $yetTN,
            ];        
        }

        return $result;
    }

    public function getCountTNByType($listIdSaleCare, $idTypeCall, $all = true)
    {
        $count = 0;

        $data = SaleCare::whereIn('id', $listIdSaleCare)->where('type_TN', $idTypeCall);
        if (!$all) {
            $data = $data->where('has_TN', 0);
        }

        $count = $data->count();
        return $count;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $r)
    {
        if (count($r->all())) {
            return $this->filterSalesByDate($r);
        }

        $helper     = new Helper();
        $listCall   = $helper->getListCall()->get();
        
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $checkAllAdmin = isFullAccess(Auth::user()->role);
        $sales = [];
        if ($checkAllAdmin) {
            $sales      = Helper::getListSale()->get();
        } else if (!$checkAllAdmin && $isLeadSale) {
            $sales = Helper::getListSaleOfLeader()->get();
        }
       
        // $time       = date('d/m/Y') . '-' . date('d/m/Y');
        // $time = '30/07/2025 - 30/07/2025';
        // $dataFilter['daterange'] = explode("-",$time); 
        $dataFilter['daterange']  = [date('d/m/Y'), date('d/m/Y')];
        $saleCare   = $this->getListSalesByPermisson(Auth::user(), $dataFilter);

        $listSrc    = SrcPage::select('id', 'name')->get();
        $groups     = Group::select('id', 'name')->get();
        $callResults = CallResult::select('id', 'name')->get();
        $typeDate = TypeDate::select('id', 'name')->get();
        $listMktUser = Helper::getListMktUser()->select('id', 'name');
        $listTypeTN = CategoryCall::get();
        $listProduct = Product::select('product.id', 'product.name')
            ->join('detail_product_group','detail_product_group.id_product', '=', 'product.id')
            ->where('product.status', 1)->distinct()->get();
        $dataCountByType = $this->getReportCountTNByType($saleCare, $listTypeTN);
        $saleCare   = $saleCare->paginate(50);

        return view('pages.sale.index')->with('listSrc', $listSrc)
            ->with('groups', $groups)
            ->with('callResults', $callResults)
            ->with('typeDate', $typeDate)
            ->with('listMktUser', $listMktUser)
            ->with('listTypeTN', $dataCountByType)
            ->with('listProduct', $listProduct)
            ->with('sales', $sales)->with('saleCare', $saleCare)->with('listCall', $listCall);
    }

    public function add()
    { 
        $helper = new Helper();
        $listSale = $helper->getListSale()->get();

        $src = new SrcPage();
        $listSrc = $src::orderBy('id', 'desc')->get();
        return view('pages.sale.add')->with('listSale', $listSale)->with('listSrc', $listSrc);
    }

    public function saveUI(Request $r)
    {
        // Log::channel('ladi')->info('run');
        $linkPage = $namePage = '';
        $is_duplicate = $hasOldOrder = $isOldOrder = 0;
        $phone = $r->phone;
        $messages = $r->messages;
        $name = $r->name;
        $typeTN = $r->old_customer;
        $src_id = $r->src_id;
        $srcPage = SrcPage::find($src_id);
        $shareDataSale = $r->shareDataSale;
        // dd($name);
        if ($srcPage) {
            $linkPage = $srcPage->link;
            $namePage = $srcPage->name;
            $id_page = $srcPage->id_page;
            $group = $srcPage->group;
            if (!$group) {
                return back();
            }
        }
        
        if (SaleCare::where('phone', $phone)->first()) {
            $is_duplicate = 1;
        }

        $cskh = Helper::isOldCustomerV2($phone);
        if ($cskh) {
            $hasOldOrder = 1;
        }

        $oldCustomerByGroup = Helper::isOldCustomerByGroup($phone, $group->id);
        if ($oldCustomerByGroup) {
            $isOldOrder = 1;
        }

        if ($shareDataSale && $shareDataSale == 2){
            if (!$is_duplicate) {
                /** khách mới hoàn toàn */
                $assignSale = Helper::getAssignSaleByGroup($group)->user;
            } else {
                /** khách cũ */
                $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeTN, $isOldOrder);
            }
            $assgin_user = $assignSale->id;
        } else {
            $assgin_user = $r->assgin;
        }
        
        // dd($assgin_user);
        $chatId = $group->tele_hot_data;
        if ($isOldOrder == 1) {
            $chatId = $group->tele_cskh_data;
        }

        $data = [
            'page_link' => $linkPage,
            'page_name' => $namePage,
            'sex'       => 0,
            'old_customer' => $isOldOrder,
            'address'   => '',
            'messages'  => $messages,
            'name'      => $name,
            'phone'     => $phone,
            'page_id'   => $id_page,
            'text'      => $messages,
            'chat_id'   => $chatId,
            'm_id'      => 'mId',
            'assgin' => $assgin_user,
            'is_duplicate' => $is_duplicate,
            'group_id'  => $group->id,
            'has_old_order'  => $hasOldOrder,
            'src_id' => $src_id,
            'type_TN' => 1, 
        ];

        $r->replace($data);
        $save = $this->save($r);
        return back();
    }

    /**
     * old_customer"
     *  0: khách mới - data nóng
     *  1: khách cũ - cskh
     *  2: sale tự tạo data - hotline
     * 
     */
    public function save(Request $req) 
    {
        $validator      = Validator::make($req->all(), [
            'phone'     =>  ['required', 'regex:/^(?:(03[0-9]|05[0-9]|07[0-9]|08[0-9]|09[0-9])\d{7}|02\d{9})$/']
            
        ],[
            'phone.required' => 'Nhập số điện thoại',
            'phone.regex' => 'Định dạng số điện thoại chưa đúng',
        ]);

        if (!$req->access && Helper::isSeeding($req->phone)) {
            notify()->error('Số điện thoại đã nằm trong danh sách spam/seeding..', 'Thất bại!');
            return back();
        }

        if ($validator->passes()) {
            if (isset($req->id)) {
                $saleCare = SaleCare::find($req->id);
                $text = 'Cập nhật tác nghiệp thành công.';
            } else {
                $saleCare = new SaleCare();
                $text = 'Tạo tác nghiệp thành công.';
                $saleCare->type_TN = ($req->type_TN) ? $req->type_TN : 1;
            }

            $saleCare->id_order             = $req->id_order;
            $saleCare->sex                  = ($req->sex) ?: 0;
            $saleCare->full_name            = $req->name;
            $saleCare->phone                = $req->phone;
            $saleCare->address              = $req->address;
            $saleCare->type_tree            = $req->type_tree;
            $saleCare->product_request      = $req->product_request;
            $saleCare->reason_not_buy       = $req->reason_not_buy;
            $saleCare->note_info_customer   = $req->note_info_customer;

            if ($req->assgin) {
                $saleCare->assign_user       = $req->assgin;
            }
            
            $saleCare->messages             = $req->messages;
            $saleCare->old_customer         = ($req->old_customer) ?: 0;
            $saleCare->m_id                 = $req->m_id;
            $saleCare->is_duplicate         = ($req->is_duplicate) ?: 0;
            $saleCare->group_id             = $req->group_id;
            $saleCare->has_old_order        = ($req->has_old_order) ?: 0;
            
            $srcId = $req->src_id;
            
            if ($srcId) {
                $src = Helper::getSrcById($srcId);
                $saleCare->src_id            = $req->src_id;
                $saleCare->page_name         = $src->name;
                $saleCare->page_id           = $src->id_page;
                $saleCare->page_link         = $src->link;

                if ($src->group) {

                    $saleCare->group_id = $src->id_group;
                    $group = $src->group;
                    if (!$req->chat_id) {
                        $chatId = $group->tele_hot_data;
                    }

                    if ($req->shareDataSale && $req->shareDataSale == 1) {
                        $saleCare->assign_user       = $req->assgin;
                    } else if ($req->shareDataSale && $req->shareDataSale == 2){
                        $assgin_user = 0;
                        $is_duplicate = false;
                        $phone = Helper::getCustomPhoneNum($req->phone);
                        $hasOldOrder = 0;
                        $checkSaleCareOld = Helper::checkOrderSaleCarebyPhoneV4($phone, 'null', $is_duplicate, $assgin_user, $group, $hasOldOrder);

                        if ($assgin_user == 0 && $checkSaleCareOld) {

                            $assignSale = Helper::getAssignSaleByGroup($group);
                            if (!$assignSale) {
                              return;
                            }

                            $saleCare->assign_user       = $assignSale->id_user;
                        } else {
                            $saleCare->assign_user       = $assgin_user;
                        }

                        $saleCare->is_duplicate         = $is_duplicate;
                        $saleCare->has_old_order        = $hasOldOrder;
                    } 
                } 
            
            } else {
                $saleCare->page_name            = $req->page_name;
                $saleCare->page_id              = $req->page_id;
                $saleCare->page_link            = $req->page_link;
            }

            $saleCare->save();

            $routeName = \Request::route();

            // return response()->json(['success'=>$text]);
            // $req->session()->put('success', 'Tạo tác nghiệp sale thành công.');

            if ($routeName && $routeName->getName() == 'sale-care-save') {
                notify()->success($text, 'Thành công!');
            }

            // Session::forget('name');
            // Session::forget('phone');
            // Session::forget('address');
            // Session::forget('messages');
        } else {
            notify()->error('Lỗi khi tạo tác nghiệp mới', 'Thất bại!');
            
            // Session::put('name', $req->name);
            // Session::put('phone', $req->phone);
            // Session::put('address', $req->address);
            // Session::put('messages', $req->messages);
            foreach ($validator->errors()->messages() as $mes) {
                // notify()->error($mes[], 'Thất bại!');
                notify()->error($mes[0], 'Thất bại!');
                // dd($mes[0]);
                return false;
            }
        }

        return back();
    }

    public function update(Request $request)
    {
        $saleCare = SaleCare::find($request->id);
        if ($saleCare) {
            $saleCare->phone = $request->phone;
            $saleCare->full_name = $request->name;
            $saleCare->address = $request->address;
            $saleCare->messages = $request->note_info_customer;
            $saleCare->issetDuplicate = ($request->issetDuplicate) ? 1 : 0;
            if ($request->issetDuplicate) {
                $saleCare->duplicate_id = $request->duplicate_id;

                if (!$saleCare->is_duplicate) {
                    $saleCare->is_duplicate = 1;
                }
            } else {
                $isOldCustomer = Helper::isOldCustomerV2($saleCare->phone);
                if (!$isOldCustomer) {
                    $saleCare->is_duplicate = 0;
                }

                $saleCare->duplicate_id = null;
            }
            
            $saleCare->save();
            notify()->success('Lưu Data thành công', 'Thành công!');
           
        } else {
           notify()->error('Đã xảy ra lỗi khi lưu data', 'Thất bại!');
        }

        return redirect()->back();
    }

    public function updateView($id) {
        $saleCare   = SaleCare::find($id);
      
        if($saleCare) {
            return view('pages.sale.update')
                ->with('saleCare', $saleCare);
        } 

        return redirect('/');
    }

    public function saveAjax(Request $req) {
        $saleCare = SaleCare::find($req->itemId);

        if (isset($saleCare->id)) {
            $nextStep = $saleCare->next_step;
            if ($nextStep < 7) {
                if ($nextStep) {
                    $nextStep++;
                } else {
                    $nextStep = 1;
                }
                $saleCare->next_step = $nextStep;
                $saleCare->is_runjob = 0;
            }
            $saleCare->result_call = $req->id;
            $saleCare->save();
            return response()->json(['data' => $saleCare]);
        }

        return response()->json(['error' => true]);
    }

    public function searchInSaleCare($dataFilter)
    {
        $seach = $dataFilter['search'];
        $listIdHasHis = SaleCareHistoryTN::join('sale_care', 'sale_care.id', '=', 'sale_care_history_tn.sale_id')
            ->orwhere('sale_care_history_tn.note', 'like', '%' .  $seach. '%')
            ->pluck('sale_care.id')->toArray();
        $listId = SaleCare::orWhere('full_name', 'like', '%' . $seach . '%')
            ->orWhere('phone', 'like', '%' . $seach . '%')
            ->orWhere('full_name', 'like', '%' . $seach . '%')
            ->pluck('id')->toArray();

        $list = SaleCare::orWhereIn('id', $listIdHasHis)
            ->orWhereIn('id', $listId)
            ->orderBy('created_at', 'desc');

        $ids = $newList = [];
        foreach ($list->get() as $sc) {
            $ids[] = $sc->id;
            if ($sc->orderNew && $sc->orderNew->phone != $sc->phone) {
                $newList = SaleCare::where('phone', 'like', '%' . $sc->orderNew->phone. '%')
                    ->pluck('id')->toArray();

                foreach ($newList as $item) {
                    if ($item != $sc->id) {
                        $ids[] = $item;
                    }
                }
            }
        }

         /** tìm theo mã vận đợn nếu có */
        $IdTrackings = ShippingOrder::where('order_code', 'like', '%' . $seach . '%');
        foreach ($IdTrackings->get() as $track) {
            if (!empty($track->order->saleCare->id)) {
                $ids[] = $track->order->saleCare->id;
            }
        }
 
        $ids = array_unique($ids);
        $list = SaleCare::whereIn('id', $ids)->orderBy('created_at', 'desc');

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
                            // dd('aa');
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

        if (isset($dataFilter['type_customer'])) {
            $list->where('old_customer', $dataFilter['type_customer']);   
        }

        $routeName = Route::currentRouteName();
        if (isset($dataFilter['status']) && $routeName != 'filter-total-sales') {
            $list->whereNotNull('id_order_new');
            $newSCare = [];
            foreach ($list->get() as $scare) {
                $order = $scare->orderNew;
                if ($order && $order->status == $dataFilter['status']) {
                    $newSCare[] = $scare->id;
                }
            }

            $list   = SaleCare::orderBy('created_at', 'desc')->whereIn('id', $newSCare);
        }
        
        if (isset($dataFilter['sale'])) {
            $list = $list->where('assign_user', $dataFilter['sale']);
        }
        return $list;
    }

    public function getListSalesByPermisson($user, $dataFilter = null, $getJson = false) 
    {
        $roles  = $user->role;
        $list   = SaleCare::orderBy('created_at', 'desc');
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $checkAllAdmin = isFullAccess(Auth::user()->role);
        $isLeadDigital = Helper::isLeadDigital(Auth::user()->role);
        if (isset($dataFilter['search'])) {
            return $this->searchInSaleCare($dataFilter);
        } 

        if ($dataFilter) {
            if (isset($dataFilter['typeDate'])) {
               
                /* 
                * 2: ngày sale chốt đơn
                * 1: ngày data về hệ thống
                */
                if ($dataFilter['typeDate'] == 1) {
                    $time       = $dataFilter['daterange'];
                    $timeBegin  = str_replace('/', '-', $time[0]);
                    $timeEnd    = str_replace('/', '-', $time[1]);
                    $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
                    $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

                    $list->whereDate('created_at', '>=', $dateBegin)
                        ->whereDate('created_at', '<=', $dateEnd);
                } else if ($dataFilter['typeDate'] == 2) {
                   
                    $ordersCtl = new OrdersController();
                    $listOrder = $ordersCtl->getListOrderByPermisson(Auth::user(), $dataFilter);
                   
                    $listIdSale = [];
                    foreach ($listOrder->get() as $order) {
                        $listIdSale[] = $order->sale_care;
                    }

                    $list = SaleCare::orderBy('created_at', 'desc')
                        ->whereIn('id', $listIdSale);
                }
            }

            
            if (isset($dataFilter['daterange']) && !isset($dataFilter['typeDate'])) {

                $ordersCtl = new OrdersController();
                $tmpDataFilter = $dataFilter;

                $listOrder = $ordersCtl->getListOrderByPermisson(Auth::user(), $tmpDataFilter);

                $listIdSale = [];
                foreach ($listOrder->get() as $order) {
                    $listIdSale[] = $order->sale_care;
                }

                $time       = $dataFilter['daterange'];
                $timeBegin  = str_replace('/', '-', $time[0]);
                $timeEnd    = str_replace('/', '-', $time[1]);
                $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
                $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

                $list->whereDate('created_at', '>=', $dateBegin)
                    ->whereDate('created_at', '<=', $dateEnd);

                // id ngày data về hệ thống
                $listIdSale2 = $list->pluck('id')->toArray();
                // gộp mảng và loại bỏ phần tử trùng => sắp xếp
                $listId = array_unique(array_merge($listIdSale, $listIdSale2));
                sort($listId);

                $list = SaleCare::orderBy('created_at', 'desc')->whereIn('id', $listId);
            }

           
            /** có chọn 1 nguồn */
            if (isset($dataFilter['src'])) {
                /*if (is_numeric($dataFilter['src'])) {
                    $list->where('page_id', 'like', '%' . $dataFilter['src'] . '%');
                } else {
                    $list->where('page_link', 'like', '%' . $dataFilter['src'] . '%');
                }*/

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
                                // dd('aa');
                                $query->orWhere('page_id', $src->id_page);
                            } else if  ($src->type == 'old') {
                                $query->orWhere('page_name', $src->name);
                            } else {
                                $query->orWhere('page_id', 'tricho');
                            }
                        }
                    }
                });

                // $src = SrcPage::find($dataFilter['src']);
                // if (!$src) {
                //     return ;
                // }

                // if ($src->type == 'pc') {
                //     $list = $list->where('page_id', $src->id_page);
                // } else if ($src->type == 'ladi') {
                //     $list = $list->where('page_link', $src->link);
                // } else if ($src->type == 'hotline') {
                //     dd('aa');
                //     $list = $list->where('page_id', 'like', '%' . $src->id_page .'%');
                // } else if  ($src->type == 'old') {
                //     $list = $list->where('page_name', $src->name);
                // } else {
                //     $list = $list->where('page_id', 'tricho');
                // }
            }

            /* mkt ko có quyền admin, lead mkt => gán thêm lọc theo mkt */
            if (!$checkAllAdmin && !$isLeadDigital && Auth::user()->is_digital) {
                $dataFilter['mkt'] = Auth::user()->id;
            }

            if (isset($dataFilter['mkt'])) {
                $listSrcByMkt = SrcPage::where('user_digital', $dataFilter['mkt']);
                $srcIDs = $listSrcByMkt->get()->pluck('id')->toArray();

                if ($srcIDs) {
                    $list->whereIn('src_id', $srcIDs);
                }
               
            }

            if (isset($dataFilter['type_customer'])) {

                $route = \Request::route();
                if ($route->getName() == 'sale-index' || $dataFilter['type_customer'] == 1) {
                    $list->where('old_customer', $dataFilter['type_customer']);  
                } else if ($dataFilter['type_customer'] == 0) {
                    $typeCustomerTmp = [0, 2];
                    $list->whereIn('old_customer',  $typeCustomerTmp);  
                }
            }

            if (isset($dataFilter['statusTN'])) {
                $paramFilter = [-1, 'null'];
                if ($dataFilter['statusTN'] == 1) { //chưa tác nghiệp
                    // $list =   $list->whereIn('result_call', [-1, 'null']);
                    $list = $list->where(function($query) use ($paramFilter) {
                        foreach ($paramFilter as $paramFilter) {
                            if ($paramFilter == -1) {
                                $query->orWhere('result_call', -1);
                            } else {
                                $query->orWhereNull('result_call');
                            }
                        }
                    });
                } else {
                    $list = $list->whereNotNull('result_call');
                }
            }

            if (isset($dataFilter['resultTN'])) {

                $idSaleCares = $list->pluck('id')->toArray();
                $listInFilter = SaleCare:: join('call', 'call.id', '=', 'sale_care.result_call')
                    ->whereIn('sale_care.id', $idSaleCares)
                    ->where('call.result_call',$dataFilter['resultTN']);
                /**
                 * lấy tất cả data từ list sđt lọc ra trong đó có lần 1 lần 2 lần n
                 * 0961630479
                 */
                $newPhone = $listInFilter->pluck('sale_care.phone')->toArray();
                $list = SaleCare::whereIn('phone', $newPhone)->orderBy('created_at', 'desc');
            }

            $routeName = Route::currentRouteName();
            if (isset($dataFilter['status']) && $routeName != 'filter-total-sales') {
                $list->whereNotNull('id_order_new');
                $newSCare = [];
                foreach ($list->get() as $scare) {
                    $order = $scare->orderNew;
                    if ($order && $order->status == $dataFilter['status']) {
                        $newSCare[] = $scare->id;
                    }
                }

                $list   = SaleCare::orderBy('created_at', 'desc')->whereIn('id', $newSCare);
            }

            if (isset($dataFilter['cateCall']) ) {
                if ($dataFilter['cateCall'] == 7) {
                    $cancelSaleC = [];

                    foreach ($list->get() as $saleC) {
                        if ($saleC->result_call && $saleC->result_call != -1 && $saleC->call->then_call == $dataFilter['cateCall']) {
                            $cancelSaleC[] = $saleC->id;
                        }
                    }

                    $list   = SaleCare::orderBy('created_at', 'desc')->whereIn('id', $cancelSaleC);
                } else {
                    $list   = $list->where('type_TN', $dataFilter['cateCall']);
                }
            }

            if (isset($dataFilter['group'])) {
                $list   = $list->where('group_id', $dataFilter['group']);
            }

            if (isset($dataFilter['product'])) {
                $ids = [];
                $list->whereNotNull('id_order_new');
                $newSCare = [];
                foreach ($list->get() as $scare) {
                    $order = $scare->orderNew;

                    $products = json_decode($order->id_product);
                    foreach ($products as $product) {
                        if ($product->id == $dataFilter['product']) {
                            $newSCare[] = $scare->id;
                            break;
                        }
                    }
                }

                $list   = SaleCare::orderBy('created_at', 'desc')->whereIn('id', $newSCare);
            }
        }

        $checkAll   = false;
        $listRole   = [];
        $roles      = json_decode($roles);
        
        $routeName = Route::currentRouteName();
        if ($roles) {
            foreach ($roles as $key => $value) {
                /**
                 * value: 4 = lead sale ko áp dụng cho filter/index dashboard
                 */
                if ($value == 1 || ($value == 4 && $routeName != 'filter-total-sales' && $routeName != 'home' && $routeName != 'sale-index')) {
                    $checkAll = true;
                    break;
                } else {
                    $listRole[] = $value;
                }
            }
        }

        if ((isset($dataFilter['sale']) && $dataFilter['sale'] != 999) && ($checkAll || $isLeadSale)) {
            /** user đang login = full quyền và đang lọc 1 sale */
            $list = $list->where('assign_user', $dataFilter['sale']);     
        } else if ($isLeadSale && !$checkAll) {
            /** lead sale*/
            /* $idUser = Auth::user()->id;
             $groupsUser = Helper::getSaleGroupByLeader($idUser);
             $list = $list->whereIn('group_id', $groups);  
             */
            $groupsUserCollection = Helper::getListSaleV3(Auth::user());
            $groupsUser = $groupsUserCollection->pluck('id')->toArray();
            $list = $list->whereIn('assign_user', $groupsUser);
        } else if ((!$checkAll || !$isLeadSale ) && !$user->is_digital) {
            $list = $list->where('assign_user', $user->id);
        }

        if ($getJson) {
            return $list->count();
        }
        return $list;
    }

    public function search(Request $req)
    {
        if ($req->search) {
            $helper     = new Helper();
            $sales      = Helper::getListSale()->get();
            $listCall   = $helper->getListCall()->get();
            $saleCare = SaleCare::where('full_name', 'like', '%' . $req->search . '%')
                ->orWhere('phone', 'like', '%' . $req->search . '%')
                ->orderBy('created_at', 'desc');
            $count      = $saleCare->count();
            $saleCare   = $saleCare->paginate(10);
            return view('pages.sale.index')->with('count', $count)->with('sales', $sales)->with('saleCare', $saleCare)->with('listCall', $listCall);
        } else {
            return redirect()->route('sale-index');
        }
    }

    public function filterSalesByDate(Request $req) 
    {
        $dataFilter = [];
        if ($req->search) {
            $dataFilter['search'] = $req->search;
        }

        if ($req->daterange) {
            $time       = $req->daterange;
            $arrTime    = explode("-",$time); 
            $dataFilter['daterange'] = $arrTime;
        } else {
            $dataFilter['daterange']  = [date('d/m/Y'), date('d/m/Y')];
        }

        $typeDate = $req->typeDate;
        if ($typeDate && $typeDate != 999) {
            $dataFilter['typeDate'] = $typeDate;
        }

        $sale = $req->sale;
        if ($req->sale && $sale != 999) {
            $dataFilter['sale'] = $sale;
        }

        $mkt = $req->mkt;
        if ($req->mkt && $mkt != 999) {
            $dataFilter['mkt'] = $mkt;
        }     

        $src = $req->src;
        if ($req->src && $src != 999) {
            $dataFilter['src'] = $src;
        }

        $group = $req->group;
        if ($req->group && $group != 999) {
            $dataFilter['group'] = $group;
        }

        $typeCustomer = $req->type_customer;
        if ($typeCustomer != 999) {
            $dataFilter['type_customer'] = $typeCustomer;
        }

        $resultTN = $req->resultTN;
        if ($resultTN != 999) {
            $dataFilter['resultTN'] = $resultTN;
        }

        $status = $req->status;
        if ($status && $status != 999 ||  $status == 0) {
            $dataFilter['status'] = $status;
        }

        if ($req->cateCall && $req->cateCall != 999 ) {
            $dataFilter['cateCall'] = $req->cateCall;
        }

        if ($req->statusTN && $req->statusTN != 999) {
            $dataFilter['statusTN'] = $req->statusTN;
        }

        if ($req->product && $req->product != 999) {
            $dataFilter['product'] = $req->product;
        }

        try {
            $data       = $this->getListSalesByPermisson(Auth::user(), $dataFilter);
            $helper     = new Helper();
            $listCall   = $helper->getListCall()->get();
            $isLeadSale = Helper::isLeadSale(Auth::user()->role);
            $checkAllAdmin = isFullAccess(Auth::user()->role);
            $sales = [];
            if ($checkAllAdmin) {
                $sales      = Helper::getListSale()->get();
            } else if (!$checkAllAdmin && $isLeadSale) {
                $sales = Helper::getListSaleOfLeader()->get();
            }

            $listSrc    = SrcPage::orderBy('id', 'desc')->get();
            $groups     = Group::orderBy('id', 'desc')->get();
            $callResults = CallResult::orderBy('id', 'desc')->get();
            $typeDate = TypeDate::orderBy('id', 'desc')->get();
            $listMktUser = Helper::getListMktUser();
            $listTypeTN = CategoryCall::orderBy('id', 'asc')->get();
            $listProduct = Product::select('product.*')->orderBy('product.id', 'desc')
                ->join('detail_product_group','detail_product_group.id_product', '=', 'product.id')
                ->where('product.status', 1)->distinct()->get();

            $dataTmp = $data;
            if (isset($dataFilter['cateCall'])) {
                unset($dataFilter['cateCall']);
                $dataTmp = $this->getListSalesByPermisson(Auth::user(), $dataFilter);
            }

            $dataCountByType = $this->getReportCountTNByType($dataTmp, $listTypeTN);
            $saleCare   = $data->paginate(50);
            
            if ($req->isAjax) {
                $tmp = [];
                foreach ($saleCare as $sale) {

                    $his = $sale->TN_can;
                    $typeTn = '';
                    if ($sale->listHistory->count()) {
                        foreach ($sale->listHistory as $key => $value) {
                            $his .= date_format($value->created_at,"d/m") . ' ' . $value->note . "<br>";
                        } 
                        $sale->history = $his;
                    }

                    if ($sale->typeTN) {
                        $typeTn = $sale->typeTN;
                        $sale->typeTN = $typeTn;
                    }

                    if ($sale->orderNew) {
                        $orderNew = $sale->orderNew;
                        $listProduct = $orderNew->id_product;
                        $listProductTmp = [];

                        foreach (json_decode($orderNew->id_product) as $product) {
                            $productModel = getProductByIdHelper($product->id);
                            if ($productModel) {
                                $productModel->cartQty = $product->val;
                                $listProductTmp[] = $productModel;
                            }
                        }

                        $orderNew->listProduct = $listProductTmp;
                        $sale->orderNew = $orderNew;
                        if ($sale->orderNew->shippingOrder) {
                            $orderNew = $sale->orderNew->shippingOrder;
                            $sale->orderNew = $orderNew;
                        }
                        // element.orderNew.shippingOrder
                        // json_decode($order->id_product) as $product)
                        // $productModel = getProductByIdHelper($product->id)
                    }

                    if ($sale->type_TN) {
                        $listCallByTypeTN = Helper::listCallByTypeTN($sale->type_TN);
                        $tmpListcall = [];
                        foreach ($listCallByTypeTN as $call) {
                            $call->name = $call->callResult->name;
                            $call->thenCallName = $call->thenCall->name;
                            $tmpListcall[] = $call;
                        }

                        $sale->listCallByTypeTN = $tmpListcall;
                    }
                   
                    $tmp[] = $sale;
                }

                $listProduct = Product::orderBy('id', 'desc');

                return response()->json([
                    'dataSale' => $tmp,
                    'listProduct' => $listProduct,
                    'listSale' => Helper::getListSale()->get(),
                ]);
            }

            return view('pages.sale.index')->with('listSrc', $listSrc)
                ->with('sales', $sales)->with('groups', $groups)
                ->with('callResults', $callResults)
                ->with('typeDate', $typeDate)
                ->with('listMktUser', $listMktUser)
                ->with('listTypeTN', $dataCountByType)
                ->with('listProduct', $listProduct)
                ->with('saleCare', $saleCare)->with('listCall', $listCall);
        } catch (\Exception $e) {
            // return $e;
            // dd($e);
            return redirect()->route('home');
        }
    }

    public function updateTNcan(Request $r) 
    {
        $id = $r->id;
        $saleCare = SaleCare::find($id);
        $history = SaleCareHistoryTN::where('sale_id', $id)
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->first();

        if (!$saleCare) {
            return response()->json(['error'=>'Đã có lỗi xảy ra trong quá trình cập nhật']);
        }
        if (!$history ) {
            $history = new SaleCareHistoryTN();
            $history->sale_id = $id;
        }


        $history->note = $r->textTN;
        $history->save();
        return response()->json([
            'success' => 'Cập nhật TN thành công!',
            // 'id_his' => $history->id,
            // 'text_his' => date_format($history->created_at,"d/m") . ' ' . $history->note,
        ]);

        /*$saleCare = SaleCare::find($r->id);
        if ($saleCare) {
            $saleCare->TN_can = $r->textTN;
            $saleCare->save();
            return response()->json(['success' => 'Cập nhật TN thành công!']);
        }

        return response()->json(['error'=>'Đã có lỗi xảy ra trong quá trình cập nhật']);
        */
    }
    
    public function delete($id)
    {
        if (isFullAccess(Auth::user()->role)) {
            $saleCare = SaleCare::find($id);
            // dd($saleCare);
            if($saleCare){
                if ($saleCare->listHistory->count() > 0) {
                    foreach ($saleCare->listHistory as $item) {

                        $listImgJson = $item->img;
                        $listImg = json_decode($listImgJson, true);

                        if ($listImg) {
                            foreach ($listImg as $img) {
                                $image_path = public_path("files/" . $img);  // Value is not URL but directory file path
                                if(File2::exists($image_path)) {
                                    File2::delete($image_path);
                                }
                            }
                        }
                        
                    }
                }

                $saleCare->delete();
                return response()->json(['success' => 'Xoá TN thành công!']);          
            } else {
                return response()->json(['error'=>'Xoá TN thất bại']);
            }
        }

        return back();
    }

    public function updateAssignTNSale(Request $r) 
    {
        $saleCare = SaleCare::find($r->id);
        if ($saleCare) {
            $saleCare->assign_user = $r->assignSale;
            $saleCare->save();
            return response()->json(['success' => 'Cập nhật TN thành công!']);
        }

        return response()->json(['error'=>'Đã có lỗi xảy ra trong quá trình cập nhật']);
    }

    public function getIdOrderNewTNSale(Request $r)
    {
        $saleCare = SaleCare::find($r->TNSaleId);

        if ($saleCare && $saleCare->id_order_new) {
            $link = route('view-order', $saleCare->id_order_new);
            return response()->json([
                'id_order_new' => $saleCare->id_order_new,
                'link' => $link
            ]);
        }
    }

    public function updateTNresult(Request $r) {
        $saleCare = SaleCare::find($r->id);
        $nextTN = '';

        if ($saleCare) {
            $saleCare->result_call = $r->value;

            if ($r->value == -1) {
                $saleCare->has_TN = 0;
            } else {
                $saleCare->has_TN = 1;
                $nextTN = $saleCare->resultCall->thenCall->name;
            }
            
            $saleCare->is_runjob = 0;
            $saleCare->time_update_TN = date('Y-m-d H:i:s');
            $updatedAt =  $saleCare->time_update_TN;

            $call = $saleCare->call;
            if ($call && $time = $call->time) {
                $newDateInt = strtotime("+$time hours", strtotime($updatedAt));
                $saleCare->time_wakeup_TN = date('Y-m-d H:i:s', $newDateInt);
            }

            $saleCare->save();
            setDataTNLogHelper($saleCare->id, 'Cập nhật kết quả TN');

            return response()->json([
                'success' => 'Cập nhật kết quả TN thành công!',
                'classHasTN' => $saleCare->has_TN,
                'nextTN' => $nextTN,
            ]);
        }

        return response()->json(['error' => 'Đã có lỗi xảy ra trong quá trình cập nhật kết quả TN']);
    }

    public function deleteListSC(Request $r)
    {
        $listIdJson = $r->list_id;
        
        if ($listIdJson) {
            $listId = json_decode($listIdJson);
            $listSc = SaleCare::whereIn('id', $listId)->pluck('id');
            foreach ($listSc as $id) {
                $this->delete($id);
            }

            // return response()->json(['error' => 'Đã có lỗi xảy ra trong quá trình cập nhật kết quả TN']);
        }

        // return response()->json(['error' => 'Đã có lỗi xảy ra trong quá trình cập nhật kết quả TN']);
        
    }
}
