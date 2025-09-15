<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AddressController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\Call;
use App\Helpers\Helper;
use App\Models\CategoryCall;
use App\Models\CallResult;
use App\Models\SaleCare;

class CallResultController extends Controller
{
    public function search(Request $req)
    {
        $search = $req->search;
        $callResult = CallResult::where('name', 'like', '%' . $search .'%')->paginate(15);
        return view('pages.call.result.index')->with('callResult', $callResult);
    }

    public function saveUpdateCalendarTN(Request $req)
    {
        $saleCare = SaleCare::find($req->id);

        $time       = $req->daterange;
        $timeBegin  = str_replace('/', '-', $time);
        $date    = date('Y-m-d H:i',strtotime("$timeBegin"));

        if ($saleCare) {
            $saleCare->time_wakeup_TN = $date;
            $saleCare->save();
            return response()->json([
                'success' => 'Cập nhật TN thành công!',
            ]);
        }

        return response()->json(['error' => 'Đã có lỗi xảy ra trong quá trình cập nhật TN']);
    }

    public function viewCalendarTN($id)
    {
        $saleCare = SaleCare::find($id);
        if ($saleCare) {
            return  view('pages.call.result.calendar')->with('id', $id)
                ->with('saleCare', $saleCare);
        }
    }

    public function index()
    {
        $callResult = CallResult::orderBy('id', 'desc')->paginate(15);
        return view('pages.call.result.index')->with('callResult', $callResult);
    }

    public function add()
    { 
        // $helper = new Helper();
        // $listSale = $helper->getListSale()->get();
        return view('pages.call.result.add');
    }

    public function save(Request $req) {
    
        $validator      = Validator::make($req->all(), [
            'name'       => 'required',
        ],[
            'name.required' => 'Vui lòng nhập loại TN',
        ]);

        if ($validator->passes()) {
            if (isset($req->id)) {
                $call = CallResult::find($req->id);
                $call->status  = $req->status;
                $text = 'Cập nhật kết quả gọi thành công.';
            } else {
                $call = new CallResult();
                $text = 'Tạo kết quả gọi thành công.';
            }
            // dd($request->products);
            $call->name = $req->name;
            $call->save();
            
            notify()->success($text, 'Thành công!');
           
        } else {
            // ['errors'=>$validator->errors()]
            // echo "<pre>";
            // print_r($validator->errors()->messages());
            $resp = $validator->errors()->messages();
                    // for (index in resp) {
            foreach ($resp as $err) {
                // print_r ( $err[0]);
                notify()->error($err[0], 'Thất bại!');
                break;
            }
            // notify(3)->error('Lỗi khi taaaạo call mới', 'Thất bại!');
           return back();
        }

        return redirect()->route('call-result');
    }

    public function update($id) {
        $call = CallResult::find($id);
        if($call){
            return view('pages.call.result.add')->with('callResult', $call);
        } 

        return redirect('/');
    }

    public function delete($id)
    {
        $callRS = CallResult::find($id);
        if($callRS){
    
            if ($callRS->operational->count()) {
                notify()->error('Xoá kết quả TN thất bại vì kq này đang sử dụng!', 'Thất bại!');
                return back();
            }
            $callRS->delete();
            notify()->success('Xoá kết quả TN thành công.', 'Thành công!');
            return back();            
        } 
        notify()->error('Xoá kết quả TN thất bại!', 'Thất bại!');
        return back();
    }
}
