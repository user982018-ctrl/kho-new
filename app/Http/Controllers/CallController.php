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
use App\Models\CallResult;
use App\Models\CategoryCall;
use App\Models\SaleCare;

class CallController extends Controller
{
    public function getHistoryByIdSalecare(Request $req)
    {
        $id = $req->id;
        $salecare = SaleCare::find($id);
        $result = '';

        if ($salecare && $salecare->listHistory->count() > 0) {
            $listHis = $salecare->listHistory->select('created_at', 'note');

            foreach ($listHis as $key => $value) {
                $result .= date_format($value['created_at'], "d/m") . ' ' . $value['note'] . "<br>";
            }
        }

        return response()->json($result);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $callResultIds = CategoryCall::where('name', 'like', '%'. $search . '%')->pluck('id')->toArray();
        $calls = Call::whereIn('if_call', $callResultIds)
            // ->orWhereIn('then_call', $callResultIds)
        ->paginate(50);
        return view('pages.call.index')->with('call', $calls);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $calls = Call::orderBy('id', 'desc')->paginate(50);
        // dd($calls);
        return view('pages.call.index')->with('call', $calls);
    }

    public function add()
    { 
        $categoryCall = CategoryCall::where('status', 1)->get();
        $callResult = CallResult::where('status', 1)->get();
        return view('pages.call.add')->with('categoryCall', $categoryCall)->with('callResult', $callResult);
    }

    public function save(Request $req) {
        // dd($req->all());
        $validator      = Validator::make($req->all(), [
            'if_call'       => 'required',
            'result_call'   => 'required',
            'then_call'     => 'required',
            'time'          => 'numeric|nullable',
        ],[
            'if_call.required' => 'Nhập Input Nếu',
            'result_call.required' => 'Nhập Kết quả',
            'then_call.required' => 'Nhập Input Thì',
            'time.numeric' => 'Thời gian chỉ nhập số',
        ]);

        if ($validator->passes()) {
            if (isset($req->id)) {
                $call = Call::find($req->id);
                $call->status  = $req->status;
                $text = 'Cập nhật call thành công.';
            } else {
                $call = new Call();
                $text = 'Tạo call thành công.';
            }

            $call->if_call = $req->if_call;
            $call->result_call = $req->result_call;
            $call->then_call = $req->then_call;
            $call->time = $req->time;
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
            // die();
           
            
            // notify(3)->error('Lỗi khi taaaạo call mới', 'Thất bại!');
           return back();
        }

        return redirect()->route('call-index');
    }

    public function update($id) 
    {
        $call = Call::find($id);
        if($call){
            $categoryCall = Categorycall::where('status', 1)->get();
            $callResult = CallResult::where('status', 1)->get();
            return view('pages.call.add')->with('call', $call)->with('categoryCall', $categoryCall)
            ->with('callResult', $callResult);
        } 

        return redirect('/');
    }

    public function delete($id)
    {
        $call = Call::find($id);
        if($call){
            $call->delete();
            notify()->success('Xoá TN thành công.', 'Thành công!');
            return back();            
        } 
        notify()->error('Xoá TN thất bại!', 'Thất bại!');
        return back();
    }
}
