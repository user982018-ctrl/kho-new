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
use App\Models\ResultCall;


class CategoryCallController extends Controller
{
    public function result()
    {
        $rs = ResultCall::orderBy('id', 'desc')->paginate(15);
        return view('pages.call.category.index')->with('resultCall', $rs);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $calls = CategoryCall::orderBy('id', 'desc')->paginate(15);
        return view('pages.call.category.index')->with('categoryCall', $calls);
    }

    public function add()
    { 
        // $helper = new Helper();
        // $listSale = $helper->getListSale()->get();
        return view('pages.call.category.add');
    }

    public function save(Request $req) {
        $validator      = Validator::make($req->all(), [
            'name'       => 'required',
        ],[
            'name.required' => 'Vui lòng nhập loại TN',
        ]);

        if ($validator->passes()) {
            if (isset($req->id)) {
                $call = categoryCall::find($req->id);
                
                $text = 'Cập nhật call thành công.';
            } else {
                $call = new categoryCall();
                $text = 'Tạo loại TN thành công.';
            }
            // dd($request->products);
            $call->name = $req->name;
            $call->class = $req->class;
            $call->status  = $req->status;
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

        return redirect()->route('category-call');
    }

    public function update($id) {
        $call = CategoryCall::find($id);
        if($call){
            return view('pages.call.category.add')->with('categoryCall', $call);
        } 

        return redirect('/');
    }

    public function delete($id)
    {
        $category = Categorycall::find($id);
        if($category){
            $category->delete();
            notify()->success('Xoá loại TN thành công.', 'Thành công!');
            return back();            
        } 
        notify()->error('Xoá loại TN thất bại!', 'Thất bại!');
        return back();
    }
}
