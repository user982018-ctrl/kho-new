<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Spam;
use Hash;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Helpers\Helper;

class SpamController extends Controller
{
    public function delete($id)  
    {
        $spam = Spam::find($id);
        if($spam){
            $spam->delete();
            notify()->success('Xoá thành công', 'Thành công!');
            return redirect('/danh-sach-spam')->with('success', 'Xoá thành công!');            
        } 

        return redirect('/danh-sach-spam') ->with('error', 'Đã xảy ra lỗi khi xoá data!');
    }
    public function viewAddUpdate() {
        $list = Spam::orderBy('id', 'desc')->paginate(55);
        return view('pages.spam.addUpdate')->with('list', $list);
    }
    public function index() {
        $list = Spam::orderBy('id', 'desc')->paginate(55);
        return view('pages.spam.index')->with('list', $list);
    }

    public function add() {
        return view('pages.users.addOrUpdate');
    }

    public function findNumberSeeding($phone)
    {
        return Spam::where('phone', $phone)->first();
    }

    public function save(Request $req) 
    {
        $validator = Validator::make($req->all(), [
            'phone'     =>  ['required', 'regex:/^(03[0-9]|05[0-9]|07[0-9]|08[0-9]|09[0-9])\d{7}$/']
            
        ],[
            'phone.required' => 'Nhập số điện thoại',
            'phone.regex' => 'Định dạng số điện thoại chưa đúng',
        ]);

        if (!$validator->passes()) {
            return response()->json(['errors'=> $validator->errors()]);
        }

        $spam = new Spam();
        try {
            $phone = Helper::getCustomPhoneNum($req->phone);
            $spam->phone = $phone;
            if ($this->findNumberSeeding($phone)) {
                return response()->json(['success'=>'Lưu thành công.']);
            }

            $spam->user_add = Auth::user()->id;
            $spam->save();
        } catch (\Exception $e) {
            return response()->json(['errors'=> 'Đã xảy ra lỗi!']);
        }
        
        return response()->json(['success'=>'Lưu thành công.']);
    }

     /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function viewUpdate($id)
    {
        $user = User::find($id);
        if($user){
            return view('pages.users.addOrUpdate')->with('user', $user);
        } 

        return redirect('/');
      
    }

    public function search(Request $str)
    {
        $list = Spam::where('phone',  'like', '%' . $str->search . '%')->paginate(15);
        return view('pages.spam.index')->with('list', $list);
    }
}
