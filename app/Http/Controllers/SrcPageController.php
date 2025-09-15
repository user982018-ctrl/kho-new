<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AddressController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\SaleCare;
use App\Helpers\Helper;
use PHPUnit\TextUI\Help;
use Illuminate\Support\Facades\Route;

class SrcPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $r)
    {
        return view('pages.src.index');
    }

    public function add()
    { 
        $list = Helper::getListDigital();
        return view('pages.src.add')->with('list', $list);
    }

    public function save(Request $req) {
        // dd($req->all());
        $validator      = Validator::make($req->all(), [
            'name'      => 'required',
            'address'   => 'required',
            'phone'     => 'required',
            // 'id_order'  => 'numeric',
        ],[
            'name.required' => 'Nhập tên khách hàng',
            'address.required' => 'Nhập địa chỉ',
            'phone.required' => 'Nhập số điện thoại',
            // 'id_order.numeric' => 'Chỉ được nhập số',
        ]);

        // dd($validator->errors());
        if ($validator->passes()) {
            if (isset($req->id)) {
                $saleCare = SaleCare::find($req->id);
                $text = 'Cập nhật tác nghiệp thành công.';
            } else {
                $saleCare = new SaleCare();
                $text = 'Tạo tác nghiệp thành công.';
            }
            // $req->old_customer = 9;
            // dd($req->all());
            $saleCare->id_order             = $req->id_order;
            $saleCare->sex                  = ($req->sex) ?: 0;
            $saleCare->full_name            = $req->name;
            $saleCare->phone                = $req->phone;
            $saleCare->address              = $req->address;
            $saleCare->type_tree            = $req->type_tree;
            $saleCare->product_request      = $req->product_request;
            $saleCare->reason_not_buy       = $req->reason_not_buy;
            $saleCare->note_info_customer   = $req->note_info_customer;
            $saleCare->assign_user          = $req->assgin;
            $saleCare->page_name            = $req->page_name;
            $saleCare->page_id              = $req->page_id;
            $saleCare->messages             = $req->messages;
            $saleCare->old_customer         = ($req->old_customer) ?: 0;
            $saleCare->page_link            = $req->page_link;
            $saleCare->m_id                 = $req->m_id;
            $saleCare->is_duplicate         = ($req->is_duplicate) ?: 0;
            
            $saleCare->save();

            if (!isset($req->id)) {
                $tProduct = Helper::getListProductByOrderId( $saleCare->id_order);
                //gửi thông báo qua telegram
                $telegram = Helper::getConfigTelegram();
                if ($telegram && $telegram->status == 1) {
                    $tokenGroupChat = $telegram->token;
                    $chatId = $req->chat_id;

                    if ($chatId &&  $chatId == 'id_VUI') {
                        $chatId = $telegram->id_VUI;
                    } else {
                        $chatId = $telegram->id_CSKH;
                    }

                    if ($req->phone == '0973409613') {
                        $chatId = '-4286962864';
                    }
                    // echo 'chat id: ' . $chatId;
                    
                    $endpoint       = "https://api.telegram.org/bot$tokenGroupChat/sendMessage";
                    $client         = new \GuzzleHttp\Client();

                    // $userAssign     = Helper::getUserByID($order->assign_user)->real_name;
                    // $nameUserOrder  = ($order->sex == 0 ? 'anh' : 'chị') ;

                    $notiText       = "Khách hàng: $saleCare->full_name"
                        . "\nSố điện thoại: $saleCare->phone"
                        . "\nNội dung: $saleCare->messages";
                       
                       
                    $name =  $saleCare->user->real_name ?: $saleCare->user->name;
                    if ($saleCare->old_customer) {
                        $notiText .= "Đã nhận được hàng."  . "\nĐơn mua: " . $tProduct; 
                        $notiText .= "\nCSKH nhận data: " . $name;    
                    } else {
                        $notiText .= "\nNguồn data: " . $req->text;
                        $notiText .= "\nSale nhận data: " . $name;
                    }
                    // dd($notiText);
                    // $name =  $saleCare->user->real_name ?: $saleCare->user->name;
                    
                    // $notiText .= "\nCSKH nhận data: " . $name;
                    
                    // dd ($chatId);
                    // . ($req->text) ? $req->text : "\nĐã nhận được hàng."
                   
                    // $response = $client->request('GET', $endpoint, ['query' => [
                    //     'chat_id' => $chatId, 
                    //     'text' => $notiText,
                    // ]]);
                }
            }
            $routeName = \Request::route();

            // return response()->json(['success'=>$text]);
            // $req->session()->put('success', 'Tạo tác nghiệp sale thành công.');

            if ($routeName && $routeName->getName() == 'sale-care-save') {
                notify()->success($text, 'Thành công!');
            }
           
           
        } else {
            // dd($validator->errors()->getMessages());
            notify()->error('Lỗi khi tạo tác nghiệp mới', 'Thất bại!');

            // foreach ($validator->errors()->getMessages() as $kE => $err) {
            //     if ($kE == 'id_order') {
            //         $kE = 'Mã đơn hàng';
            //     }
            //     foreach ($err as $k => $val) {
            //         echo $kE . ' ' . $val;

            //     }
            // }
            // echo die();
            // notify()->error('Lỗi khi tạo tác nghiệp mới', 'Thất bại!');
            //  return response()->json(['errors'=>$validator->errors()]);
            return back()->withErrors($validator->errors());
        }

        return back();
    }

    public function update($id) {
        $saleCare   = SaleCare::find($id);
        $helper     = new Helper();
        $listSale   = $helper->getListSale()->get();

        if($saleCare) {
            return view('pages.sale.add')->with('saleCare', $saleCare)
                ->with('listSale', $listSale);
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

    public function getListSalesByPermisson($user, $dataFilter = null) 
    {
        $roles  = $user->role;
        $list   = SaleCare::orderBy('id', 'desc');

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
                if (is_numeric($dataFilter['src'])) {
                    $list->where('page_id', 'like', '%' . $dataFilter['src'] . '%');
                } else {
                    $list->where('page_link', 'like', '%' . $dataFilter['src'] . '%');
                }
            } 

            if (isset($dataFilter['mkt'])) {
                /** mrNguyen = 1
                 *  mrTien = 2
                 */
                if ($dataFilter['mkt'] == 1) {
                    /** tất cả nguồn */
                    $src = ['332556043267807', '318167024711625', '341850232325526', 'ruoc-dong', 'mua4tang2', 'giamgia45'];
                    $list = $list->where(function($query) use ($src) {
                        foreach ($src as $term) {
                            if (is_numeric($term)) {
                                $query->orWhere('page_id', 'like', '%' . $term . '%');
                            } else {
                                $query->orWhere('page_link', 'like', '%' . $term . '%');
                            }
                            // $query->orWhere('page_id', 'like', '%' . $term . '%');
                        }
                    });
                } else if ($dataFilter['mkt'] == 2 || $user->is_digital) {
                    $src = ['mua4-tang2', '335902056281917'];
                    $list = $list->where(function($query) use ($src) {
                        foreach ($src as $term) {
                            if (is_numeric($term)) {
                                $query->orWhere('page_id', 'like', '%' . $term . '%');
                            } else {
                                $query->orWhere('page_link', 'like', '%' . $term . '%');
                            }
                            // $query->orWhere('page_id', 'like', '%' . $term . '%');
                        }
                    });
                }
            } else if ($user->is_digital) {
                $src = ['mua4-tang2', '335902056281917'];
                $list = $list->where(function($query) use ($src) {
                    foreach ($src as $term) {
                        if (is_numeric($term)) {
                            $query->orWhere('page_id', 'like', '%' . $term . '%');
                        } else {
                            $query->orWhere('page_link', 'like', '%' . $term . '%');
                        }
                        // $query->orWhere('page_id', 'like', '%' . $term . '%');
                    }
                });
               
            }

            // if (isset($dataFilter['src'])) {
            //     if (is_numeric($dataFilter['src'])) {
            //         $list->where('page_id', 'like', '%' . $dataFilter['src'] . '%');
            //     } else {
            //         $list->where('page_link', 'like', '%' . $dataFilter['src'] . '%');
            //     }   
            // }

            if (isset($dataFilter['type_customer'])) {
                $list->where('old_customer', $dataFilter['type_customer']);   
            }

        }

        $checkAll   = false;
        $listRole   = [];
        $roles      = json_decode($roles);

        $routeName = Route::currentRouteName();
        if ($roles ) {
            foreach ($roles as $key => $value) {
                /**
                 * value: 4 = lead sale ko áp dụng cho filter/index dashboard
                 */
                if ($value == 1 || ($value == 4 && $routeName != 'filter-total-sales' && $routeName != 'home')) {
                    $checkAll = true;
                    break;
                } else {
                    $listRole[] = $value;
                }
            }
        }


        $isLeadSale = Helper::isLeadSale(Auth::user()->role);

        // if ($user->is_digital) {
        //     // $list =  $list->where('page_link', 'like', '%' . 'mua4-tang2' . '%');
        //     $list =  $list->where('page_link', 'like', '%' . 'mua4-tang2' . '%');
        // } else 
        if ((isset($dataFilter['sale']) && $dataFilter['sale'] != 999) && ($checkAll || $isLeadSale)) {
            /** user đang login = full quyền và đang lọc 1 sale */
            $sale = Helper::getSaleById($dataFilter['sale']);

            if ($sale->is_CSKH == 1) {
                $list = $list->where('old_customer', 1);
            } else {
                $list = $list->where('old_customer', 0);
            }
            $list = $list->where('assign_user', $dataFilter['sale']);
        } else if ((!$checkAll || !$isLeadSale ) && !$user->is_digital) {
            $list = $list->where('assign_user', $user->id);
        }  

        // dd($list->get());
        return $list;
    }

    public function search(Request $req)
    {
        if ($req->search) {
            $helper     = new Helper();
            $sales      = User::where('status', 1)->where('is_sale', 1)->get();
            $listCall   = $helper->getListCall()->get();
            $saleCare = SaleCare::where('full_name', 'like', '%' . $req->search . '%')
                ->orWhere('phone', 'like', '%' . $req->search . '%')
                ->orderBy('id', 'desc');
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

        // dd($req->all());
        if ($req->daterange) {
            $time       = $req->daterange;
            $arrTime    = explode("-",$time); 
            $dataFilter['daterange'] = $arrTime;
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

        $typeCustomer = $req->type_customer;
        
        // echo $req->type_customer;
        // die();
        if ($typeCustomer != 999) {
            // dd($req->type_customer);
            $dataFilter['type_customer'] = $typeCustomer;
        }
        // dd($dataFilter['type_customer']);
        try {
            $data       = $this->getListSalesByPermisson(Auth::user(), $dataFilter);
            $count      = $data->count();
            $saleCare   = $data->paginate(50);
            $sales      = User::where('status', 1)->where('is_sale', 1)
            ->orWhere('is_cskh', 1)->get();

            $helper     = new Helper();
            $listCall   = $helper->getListCall()->get();

            // dd($data->get());
            return view('pages.sale.index')->with('count', $count)->with('sales', $sales)
                ->with('saleCare', $saleCare)->with('listCall', $listCall);
        } catch (\Exception $e) {
            // return $e;
            dd($e);
            return redirect()->route('home');
        }
    }

    public function updateTNcan(Request $r) {
        // dd($r->all());
        $saleCare = SaleCare::find($r->id);
        // dd( $saleCare);
        if ($saleCare) {
            $saleCare->TN_can = $r->textTN;
            $saleCare->save();
            return response()->json(['success' => 'Cập nhật TN thành công!']);
        }

        return response()->json(['error'=>'Đã có lỗi xảy ra trong quá trình cập nhật']);
    }
    
    public function delete($id)
    {
        $saleCare = SaleCare::find($id);
        if($saleCare){
            $saleCare->delete();
            notify()->success('Xoá data thành công.', 'Thành công!');            
        } else {
            notify()->error('Xoá loại TN thất bại!', 'Thất bại!');
        }
        
        return back();
    }

    public function updateAssignTNSale(Request $r) {
        // dd($r->all());
        $saleCare = SaleCare::find($r->id);
        // dd( $saleCare);
        if ($saleCare) {
            $saleCare->assign_user = $r->assignSale;
            $saleCare->save();
            return response()->json(['success' => 'Cập nhật TN thành công!']);
        }

        return response()->json(['error'=>'Đã có lỗi xảy ra trong quá trình cập nhật']);
    }
}
