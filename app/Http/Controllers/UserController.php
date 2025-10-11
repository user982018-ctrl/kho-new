<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Validator;


class UserController extends Controller
{
    public function checkCustomer(Request $r)
    {
        $phone = $r->phone;
        $params = [
            "client_id" => "3780854",
            "client_phone" => $phone,
            "cod" => null,
            "token" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3NjAwMTM0NTR9.EPyDlhC2ILUGQ_pREEmB2THm6Vd0r0MhUA3jilZoW80",
            "source" => '5sao',
        ];

        $link = "https://fe-online-gateway.ghn.vn/shiip/public-api/ops/data/api/online/probability-reject-order";
        $response = Http::withBody(json_encode($params))->post($link);
        dd($response->status());
        if ($response->status() == 200) {
            $dataApiJson = $response->body();
            dd($dataApiJson);
        }
        
        return response()->json(['text' => $phone]);
    }

    public function updateStatus(Request $r)
    {
        $id = $r->id;
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error'=>'Lỗi']);
        }

        $user->status = $r->status;
        $user->save();
        return response()->json(['success'=>'OK']);
    }

    public function checkUsername(Request $r)
    {
        $name = $r->name;
        $id = $r->id; // ID người dùng hiện tại (nếu là update)
        
        // Kiểm tra tên đăng nhập đã tồn tại chưa
        $query = User::where('name', $name);
        
        // Nếu đang update (có ID), loại trừ user hiện tại
        if ($id && $id != 'undefined') {
            $query->where('id', '!=', $id);
        }
        
        $exists = $query->exists();

        if ($exists) {
            return response()->json(['exists' => true, 'message' => 'Tên đăng nhập đã tồn tại']);
        }
        
        return response()->json(['exists' => false , 'message' => 'Tên đăng nhập khả dụng']);
    }

    public function login() {
        if (Auth::check()) {
            return redirect()->route('home');
        } 
        return view('pages.users.login');
    }

    public function postLogin(Request $r) {
        if (Auth::attempt(['name' => $r->name, 'password' => $r->password, 'status' => 1])) {
            return redirect()->route('home');
        } 

        return redirect()->back()->with('error', 'Đã có lỗi xảy ra khi đăng nhập.')
            ->with('email',  $r->email)->with('password',  $r->password);
    }

    public function logOut() {
        Auth::logout();
        return redirect()->route('login');
    }

    public function index() {
        $list = User::orderBy('id', 'desc')->paginate(50);
        return view('pages.users.index')->with('list', $list);
    }

    public function add() {
        $checkAll = isFullAccess(Auth::user()->role);
        if (!$checkAll) {
            return redirect('/');
        }

        return view('pages.users.addOrUpdate');
    }

    public function save(Request $req) {
        $checkAll = isFullAccess(Auth::user()->role);
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'password' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,gif|max:2048'
        ],[
            'name.required' => 'Nhập tên thành viên',
            'password.required' => 'Nhập mật khẩu',
        ]);

        if ($validator->passes()) {
            if(isset($req->id) && $req->id != 'undefined') {
                $user           = User::find($req->id);
                if ($checkAll)  {
                    $user->status   = $req->status;
                }

                // dd( $req->status);
                if ($user->password != $req->password) {
                    $pass = Hash::make($req->password);
                    $user->password = $pass;
                } else {
                    $user->password = $req->password;
                }
               
                $text = 'Cập nhật thành viên thành công.';

                // dd($req->all());
                if ($req->file('image')) {
                    $file = $req->file('image');
                    $filename = time().'_'.$file->getClientOriginalName();
                    $newFilePath = 'uploads/'.$filename;

                    $oldFilePath = $user->profile_image; 

                    // Nếu đã có ảnh
                    if ($oldFilePath && \Storage::disk('public')->exists($oldFilePath)) {
                        // So sánh file mới và file cũ theo checksum/md5
                        $newFileHash = md5_file($file->getRealPath());
                        $oldFileHash = md5_file(storage_path('app/public/'.$oldFilePath));

                        if ($newFileHash !== $oldFileHash) {
                            // Ảnh mới khác ảnh cũ => xoá ảnh cũ
                            \Storage::disk('public')->delete($oldFilePath);
                            $file->storeAs('uploads', $filename, 'public');
                        }
                    } else {
                        $file->storeAs('uploads', $filename, 'public');
                    }

                    $user->profile_image = $newFilePath;
                }
            } else{
                $user = new User();
                // $user->status = 1;
                $pass = Hash::make($req->password);
                $user->password =  $pass;
                
                $text = 'Tạo thành viên thành công.';
            }

            try {
                $user->name         = $req->name;
                $user->real_name    = $req->real_name;
                $user->email        = $req->email;

                if ($checkAll) {
                    $user->is_sale      = $req->is_sale ?? 0;
                    $user->is_digital   = $req->is_digital ?? 0;
                    $user->is_CSKH      = $req->is_CSKH ?? 0;
                    $user->is_receive_data = $req->is_receive_data ?? 0;
                    $user->is_kho       = $req->is_kho ?? 0;
                    $user->is_accountant = $req->is_accountant ?? 0;
                    $user->is_hr        = $req->is_hr ?? 0;
                    $user->role = $req->roles;
                }

                $user->save();

            } catch (\Throwable $th) {
                dd($th);
                $text = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
                return response()->json(['error'=>$text]);
            }

            return response()->json([
                'success'=> $text
            ]);
        }
     
        return response()->json(['errors'=>$validator->errors()]);
    }

     /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function viewUpdate($id)
    {
        $user = User::find($id);
        $checkAll = isFullAccess(Auth::user()->role);
        $allow = $id == Auth::user()->id;
        if($allow || $checkAll){
            return view('pages.users.addOrUpdate')->with('user', $user);
        } 

        return redirect('/');
      
    }

    public function delete($id)  
    {
        $product = User::find($id);
        if($product){
            $product->delete();
            return redirect('/quan-ly-thanh-vien')->with('success', 'Thành viên xoá thành công!');            
        } 

        return redirect('/danh-sach-san-pham') ->with('error', 'Đã xảy ra lỗi khi xoá thành viên!');
    }

    public function search(Request $str)
    {
        $list = User::where('real_name',  'like', '%' . $str->search . '%')->paginate(50);
        return view('pages.users.index')->with('list', $list);
    }
}
