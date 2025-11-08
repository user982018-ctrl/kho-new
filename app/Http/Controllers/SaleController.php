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

        // Xử lý filter groupUser
        if (isset($dataFilter['groupUser']) && $dataFilter['groupUser'] != 999) {
            $listUserInGroup = GroupUser::find($dataFilter['groupUser']);
            if ($listUserInGroup) {
                $listUserGroupIds = $listUserInGroup->users->pluck('id')->toArray();
                // dd($listUserGroupIds);
                $listSale = $listSale->whereIn('id', $listUserGroupIds);
            }
        }

        $list = $dataSort = [];
        $homeCtl = new HomeController();

        foreach ($listSale->get() as $sale) {
            $data = $homeCtl->getReportUserSaleV2($sale, $dataFilter, true);
            if ($data) {
                $list[] = $data;
            }
        }

        $dataSort = $this->selection_sort($list);
        $dataSort = array_slice($dataSort, 0, 10);
        return $dataSort;
    }

    public function ajaxViewRank(Request $r)
    {
        $dataFilter['daterange'] = $r->date;
        
        // Xử lý filter groupUser
        if ($r->groupUser && $r->groupUser != 999) {
            $dataFilter['groupUser'] = $r->groupUser;
        }
        return $this->processViewRank($dataFilter);

        
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

        $groupUser = GroupUser::orderBy('id', 'desc')->where('type', 'sale')->get();
        return view('pages.sale.rank')->with('dataSort', $dataSort)->with('groupUser', $groupUser);
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

        $validator = Validator::make(
            $input,
            ['note' => 'required'],
            ['note.required' => 'Nhập ghi chú cho tác nghiệp']
        );

        if (!$validator->passes()) {
            return back()->withErrors($validator->errors());
        }

        $history = $req->id ? SaleCareHistoryTN::find($req->id) : new SaleCareHistoryTN();
        if (!$history) {
            $history = new SaleCareHistoryTN();
        }

        if (!$req->id) {
            $history->sale_id = $req->sale_id;
        }

        $existingImages = [];
        $filesToKeep = [];
        $filesToRemove = [];

        if ($req->id) {
            $existingImages = $history->img ? json_decode($history->img, true) : [];

            if (isset($input['images_uploaded'])) {
                $filesToKeep = $input['images_uploaded'];
                $filesToRemove = array_diff($existingImages, $filesToKeep);
            } elseif (isset($input['images_uploaded_origin'])) {
                $filesToRemove = json_decode($input['images_uploaded_origin'], true);
            }
        }

        $uploadedFiles = [];
        if ($req->hasFile('filenames')) {
            foreach ($req->file('filenames') as $file) {
                $filename = time() . rand(1, 100) . '.' . $file->extension();
                $path = public_path('files') . '/' . $filename;

                Image::make($file->getRealPath())
                    ->resize(300, 500)
                    ->save($path);

                $uploadedFiles[] = $filename;
            }
        }

        $history->img = json_encode(array_values(array_merge($filesToKeep, $uploadedFiles)));
        $history->note = $req->note;

        if ($history->save()) {
            foreach ($filesToRemove as $fileName) {
                File2::delete(public_path('files/' . $fileName));
            }
        }

        return redirect()->back();
    }
    public function saleViewSaveTNBox($id)
    {
        $saleCare = SaleCare::with(['listHistory' => function ($query) {
            $query->orderByDesc('created_at');
        }])->find($id);

        if (!$saleCare) {
            return redirect('/');
        }

        $historyToday = $saleCare->listHistory
            ->first(function ($history) {
                return $history->created_at && $history->created_at->isToday();
            });

        return view('pages.sale.addBoxTN')
            ->with('history', $historyToday)
            ->with('saleId', $id)
            ->with('saleCare', $saleCare)
            ->with('listHistory', $saleCare->listHistory);
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
        
        // Tối ưu: Lấy IDs từ query builder một lần
        // Note: Nếu query quá lớn, có thể cần limit hoặc tối ưu thêm
        $listId = $listSaleCare->pluck('id')->toArray();

        // Tối ưu: Nếu không có ID nào, trả về kết quả rỗng
        if (empty($listId)) {
            foreach ($listCateCall as $cate) {
                $result[] = [
                    'data' => $cate,
                    'sum' => 0,
                    'yetTN' => 0,
                ];
            }
            return $result;
        }

        // Tối ưu: Lấy tất cả counts trong một query duy nhất sử dụng groupBy
        // Thay vì gọi N*2 queries (N = số categories), chỉ cần 1 query
        $counts = SaleCare::whereIn('id', $listId)
            ->selectRaw('type_TN, has_TN, COUNT(*) as count')
            ->groupBy('type_TN', 'has_TN')
            ->get()
            ->groupBy('type_TN');

        foreach ($listCateCall as $cate) {
            $cateId = $cate->id;
            $sum = 0;
            $yetTN = 0;

            if (isset($counts[$cateId])) {
                foreach ($counts[$cateId] as $count) {
                    $sum += $count->count;
                    if ($count->has_TN == 0) {
                        $yetTN += $count->count;
                    }
                }
            }

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
        // Tối ưu: Kiểm tra input hiệu quả hơn
        if ($r->hasAny(['search', 'daterange', 'sale', 'mkt', 'src', 'group', 'type_customer', 'resultTN', 'status', 'cateCall', 'statusTN', 'product', 'groupUser', 'typeDate'])) {
            return $this->filterSalesByDate($r);
        }

        $user = Auth::user();
        $isLeadSale = Helper::isLeadSale($user->role);
        $checkAllAdmin = isFullAccess($user->role);
        
        // Tối ưu: Lấy sales list dựa trên quyền
        $sales = [];
        if ($checkAllAdmin) {
            $sales = Helper::getListSale()->get();
        } else if ($isLeadSale) {
            $sales = Helper::getListSaleOfLeader()->get();
        }
       
        // Tối ưu: Chuẩn bị dataFilter
        $dataFilter['daterange'] = [date('d/m/Y'), date('d/m/Y')];
        $saleCareQuery = $this->getListSalesByPermisson($user, $dataFilter);

        // Tối ưu: Chạy các query độc lập (có thể cache các query này)
        $listCall = Helper::getListCall()->get();
        $groupUser = GroupUser::orderBy('id', 'desc')->where('type', 'sale')->get();
        $listSrc = SrcPage::select('id', 'name')->get();
        $groups = Group::select('id', 'name')->where('status', 1)->get();
        $callResults = CallResult::select('id', 'name')->get();
        $typeDate = TypeDate::select('id', 'name')->get();
        $listMktUser = Helper::getListMktUser()->select('id', 'name', 'real_name');
        $listTypeTN = CategoryCall::get();
        $listProduct = Product::select('product.id', 'product.name')
            ->join('detail_product_group', 'detail_product_group.id_product', '=', 'product.id')
            ->where('product.status', 1)
            ->distinct()
            ->get();

        // Tối ưu: Tính count trên toàn bộ filtered query (không phải chỉ trang hiện tại)
        // Sử dụng clone query để không ảnh hưởng đến pagination
        $dataCountByType = $this->getReportCountTNByType($saleCareQuery, $listTypeTN);

        // Paginate sau khi đã tính count
        $saleCare = $saleCareQuery->paginate(50);
        // dd($saleCare);

        return view('pages.sale.index')->with('listSrc', $listSrc)
            ->with('groups', $groups)
            ->with('callResults', $callResults)
            ->with('typeDate', $typeDate)
            ->with('listMktUser', $listMktUser)
            ->with('listTypeTN', $dataCountByType)
            ->with('listProduct', $listProduct)
            ->with('sales', $sales)
            ->with('saleCare', $saleCare)
            ->with('listCall', $listCall)
            ->with('groupUser', $groupUser);
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
        // dd($srcPage);
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
        // dd($data);

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
                notify()->error($mes[0], 'Thất bại!');
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
        $seach = trim($dataFilter['search']);
        
        // Nếu có ký tự # ở bất kỳ vị trí nào, lấy từ sau ký tự thứ 2 kể từ #
        if (is_string($seach)) {
            $hashPos = function_exists('mb_strpos') ? mb_strpos($seach, '#') : strpos($seach, '#');
            if ($hashPos !== false) {
                $startIndex = $hashPos + 1;
                $term = function_exists('mb_substr') ? mb_substr($seach, $startIndex) : substr($seach, $startIndex);
                $term = trim((string) $term);
                $list = SaleCare::where('sale_care.id_order_new', 'like', '%' . $term . '%');
                return $list;
            }
        }
        
        // Build candidate IDs via batched queries and minimize loops
        $ids = [];

        // 1) Match by sale history notes
        $listIdHasHis = SaleCareHistoryTN::where('sale_care_history_tn.note', 'like', '%' . $seach . '%')
            ->pluck('sale_care_history_tn.sale_id')
            ->toArray();

        // 2) Direct matches on SaleCare (name/phone)
        $listIdDirect = SaleCare::where(function($q) use ($seach) {
                $q->where('full_name', 'like', '%' . $seach . '%')
                  ->orWhere('phone', 'like', '%' . $seach . '%');
            })
            ->pluck('id')
            ->toArray();

        $ids = array_merge($ids, $listIdHasHis, $listIdDirect);

        // Fetch current candidates once
        $candidateIds = array_unique($ids);
        $candidates = [];
        if (!empty($candidateIds)) {
            $candidates = SaleCare::whereIn('id', $candidateIds)->get();
        }

        // 3) IDs linked through order relationship
        $linkedIdsFromOrder = [];
        foreach ($candidates as $sc) {
            if ($sc->order && !empty($sc->order->sale_care)) {
                $linkedIdsFromOrder[] = $sc->order->sale_care;
            }
        }

        // 4) Batch phone-based expansion when orderNew phone differs
        $phonesToExpand = [];
        foreach ($candidates as $sc) {
            if ($sc->orderNew && $sc->orderNew->phone && $sc->orderNew->phone != $sc->phone) {
                $phonesToExpand[] = $sc->orderNew->phone;
            }
        }
        $phonesToExpand = array_values(array_unique(array_filter($phonesToExpand)));
        $expandedIdsByPhone = [];
        if (!empty($phonesToExpand)) {
            $expandedIdsByPhone = SaleCare::whereIn('phone', $phonesToExpand)
                ->pluck('id')
                ->toArray();
        }

        // 5) Tracking codes -> related saleCare IDs
        $trackingSaleCareIds = [];
        $IdTrackings = ShippingOrder::where('order_code', 'like', '%' . $seach . '%')->get();
        foreach ($IdTrackings as $track) {
            if (!empty($track->order->saleCare->id)) {
                $trackingSaleCareIds[] = $track->order->saleCare->id;
            }
        }

        // Merge all
        $finalIds = array_values(array_unique(array_merge(
            $candidateIds,
            $linkedIdsFromOrder,
            $expandedIdsByPhone,
            $trackingSaleCareIds
        )));

        $list = SaleCare::orderBy('created_at', 'desc');
        if (!empty($finalIds)) {
            $list = $list->whereIn('id', $finalIds);
        } else {
            // No matches, keep query that returns none efficiently
            $list = $list->whereRaw('1 = 0');
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
        // $list->where('phone', '0388074466');
        // dd($list->get());
        // Tối ưu: Cache Auth::user() để tránh gọi nhiều lần
        $authUser = Auth::user();
        $isLeadSale = Helper::isLeadSale($authUser->role);
        $checkAllAdmin = isFullAccess($authUser->role);
        $isLeadDigital = Helper::isLeadDigital($authUser->role);

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
                    $listOrder = $ordersCtl->getListOrderByPermisson($authUser, $dataFilter);
                   
                    $listIdSale = [];
                    foreach ($listOrder->get() as $order) {
                        $listIdSale[] = $order->sale_care;
                    }

                    $list = SaleCare::orderBy('created_at', 'desc')
                        ->whereIn('id', $listIdSale);
                }
            }
            
            if (isset($dataFilter['daterange']) && !isset($dataFilter['typeDate'])) {
                $time       = $dataFilter['daterange'];
                $timeBegin  = str_replace('/', '-', $time[0]);
                $timeEnd    = str_replace('/', '-', $time[1]);
                $dateBegin  = date('Y-m-d',strtotime("$timeBegin"));
                $dateEnd    = date('Y-m-d',strtotime("$timeEnd"));

                // Tối ưu: Lấy orders trước để kiểm tra có data không
                $ordersCtl = new OrdersController();
                $tmpDataFilter = $dataFilter;
                $listOrder = $ordersCtl->getListOrderByPermisson($authUser, $tmpDataFilter);
                
                $listIdSale = [];
                $orderCollection = $listOrder->get();
                
                // Tối ưu: Chỉ iterate nếu có orders
                if ($orderCollection->isNotEmpty()) {
                    foreach ($orderCollection as $order) {
                        $listIdSale[] = $order->sale_care;
                    }
                }

                // Filter theo ngày data về hệ thống
                $list->whereDate('created_at', '>=', $dateBegin)
                    ->whereDate('created_at', '<=', $dateEnd);

                // Tối ưu: Chỉ merge nếu có orders, nếu không chỉ dùng filter theo created_at
                if (!empty($listIdSale)) {
                    $listIdSale2 = $list->pluck('id')->toArray();
                    // Gộp mảng và loại bỏ phần tử trùng => sắp xếp
                    $listId = array_unique(array_merge($listIdSale, $listIdSale2));
                    sort($listId);
                    $list = SaleCare::orderBy('created_at', 'desc')->whereIn('id', $listId);
                } else {
                    // Nếu không có orders, chỉ giữ filter theo created_at
                    // $list đã được filter ở trên, không cần làm gì thêm
                }
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
            if (!$checkAllAdmin && !$isLeadDigital && $authUser->is_digital) {
                $dataFilter['mkt'] = $authUser->id;
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
                /**
                 * statusTN: 1 = chưa tác nghiệp (result_call ∈ {null, -1, 0})
                 * statusTN: 2 = đã tác nghiệp (result_call NOT IN {null, -1, 0})
                 */
                if ((int) $dataFilter['statusTN'] === 1) {
                    $list = $list->where(function ($query) {
                        $query->whereNull('result_call')
                              ->orWhere('result_call', -1)
                              ->orWhere('result_call', 0);
                    });
                } else {
                    $list = $list->whereNotNull('result_call')
                                 ->whereNotIn('result_call', [-1, 0]);
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

            if (isset($dataFilter['groupUser'])) {
                $listUserInGroup = GroupUser::find($dataFilter['groupUser']);
                if ($listUserInGroup) {
                    $listUserGroupIds = $listUserInGroup->users->pluck('id')->toArray();
                    $list = $list->whereIn('assign_user', $listUserGroupIds);
                }
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
            // Tối ưu: Sử dụng $authUser đã cache thay vì gọi Auth::user() lại
            $groupsUserCollection = Helper::getListSaleV3($authUser);
            $groupsUser = $groupsUserCollection->pluck('id')->toArray();
            // dd($groupsUser);
            $list = $list->whereIn('assign_user', $groupsUser);
            // dd($list->get());

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
        if ($typeCustomer && $typeCustomer != 999) {
            $dataFilter['type_customer'] = $typeCustomer;
        }

        $resultTN = $req->resultTN;
        if ($resultTN && $resultTN != 999) {
            $dataFilter['resultTN'] = $resultTN;
        }

        $status = $req->status;
        if (!empty($status) && ($status != 999 ||  $status == 0)) {
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

        if ($req->groupUser && $req->groupUser != 999) {
            $dataFilter['groupUser'] = $req->groupUser;
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

            $groupUser = GroupUser::orderBy('id', 'desc')->where('type', 'sale')->get();
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

            // dd($req->all());
            // die();
            return view('pages.sale.index')->with('listSrc', $listSrc)
                ->with('sales', $sales)->with('groups', $groups)
                ->with('callResults', $callResults)
                ->with('typeDate', $typeDate)
                ->with('listMktUser', $listMktUser)
                ->with('listTypeTN', $dataCountByType)
                ->with('listProduct', $listProduct)
                ->with('groupUser', $groupUser)
                ->with('saleCare', $saleCare)->with('listCall', $listCall);
        } catch (\Exception $e) {
            // return $e;
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
