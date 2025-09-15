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
use App\Models\DetailProductGroup;
use App\Models\DetailUserGroup;
use App\Models\DetailUserGroupSale;
use App\Models\Group;
use App\Models\GroupSale;
use App\Models\GroupUser;
use App\Models\SrcPage;
use Validator;
class GroupUserController extends Controller
{
    public function indexDigital()
    {
        $checkAll = isFullAccess(Auth::user()->role);
        if ($checkAll) {
            $list = GroupUser::where('type', 'mkt')->get();
        } else {
            $list = GroupUser::where('type', 'mkt')->where('lead_team', Auth::user()->id)->get();
        }
       
        return view('pages.groupUser.index')->with('list', $list);
    }
    public function getListSaleGroup()
    {
        return User::where('status', 1)->where('is_sale', 1)
        ->get();
    }
    public function index()
    {
        $checkAll = isFullAccess(Auth::user()->role);
        if ($checkAll) {
            $list = GroupUser::where('type', 'sale')->get();
        } else {
            $list = GroupUser::where('type', 'sale')->where('lead_team', Auth::user()->id)->get();
        }
       
        return view('pages.groupUser.index')->with('list', $list);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function add()
    {
        $listSale = $this->getListSaleGroup();

        return view('pages.groupUser.addOrUpdate')->with('listSale', $listSale);
    }

    public function update($id)
    {
        $listSale = $this->getListSaleGroup();

        $group = GroupUser::find($id);
        if ($group) {
            return view('pages.groupUser.addOrUpdate')->with('listSale', $listSale)
                ->with('group', $group);
        }

        notify()->error('Lỗi không tìm thấy nhóm nào', 'Thất bại!');
        return redirect()->route('group-user');
    }

    public function save(Request $req) {
        // dd($req->all());
        $validator = Validator::make($req->all(), [
            'name' => 'required',
        ],[
            'name.required' => 'Nhập tên nhóm',
        ]);

        if ($validator->passes()) {
            $membersOld = [];
            if(isset($req->id)){
                $gr = GroupUser::find($req->id);
                $membersOld = User::where('group_user', $gr->id)->pluck('id')->toArray();
            } else {
                $gr = new GroupUser();
            }

            try {
                $gr->name = $req->name;
                $gr->status = $req->status; 
                $gr->lead_team = $req->leadSale;
                $gr->type = $req->type;
                
                $gr->save();

                $members = $req->member;
                $diff1 = array_diff($membersOld, $members);

                /** lưu thôn tin user trong nhóm */
                if ($diff1) {
                    User::whereIn('id', $diff1)->update(['group_user' => null]);
                } else {
                    User::whereIn('id', $members)->update(['group_user' => $gr->id]);
                }

            } catch (\Throwable $th) {
                dd($th);
            }

            notify()->success('Lưu thông tin nhóm thành công', 'Thành công!');
            // return redirect()->route('update-group', $gr->id);
            // return redirect()->route('group-user');
            return back();
        }
     
        return response()->json(['errors'=>$validator->errors()]);
    }

    public function updateArrayExist($newArray, $oldArray)
    {
        // $oldUserOfGroup = DetailUserGroup::where('id_group', $req->id)->pluck('id_user')->toArray();
        // $newUserOfGroup = $req->member;
        $tmp = $remove = $add = [];

        foreach ($oldArray as $user) {
            if (in_array($user, $newArray)) {
                $tmp[] = $user;
            } else {
                $remove[] = $user;
            }
        }
        
        foreach ($newArray as $user) {
            if (!in_array($user, $tmp)) {
                $add[] = $user;
            }
        }

        return [
            'remove' => $remove,
            'add' => $add
        ];
    }

    public function updateFieldOfGroupUser($id_group, $newReq)
    {
        /** clear data user + group */
        $oldUserOfGroup = DetailUserGroupsale::where('id_group_sale', $id_group);
        $oldUserOfGroup = $oldUserOfGroup->pluck('id_user')->toArray();
        $newUserOfGroup = $newReq;
        
        $updateUserOfGroup = $this->updateArrayExist($newUserOfGroup, $oldUserOfGroup);
        $remove = $updateUserOfGroup['remove'];
        $add = $updateUserOfGroup['add'];

        // dd($updateUserOfGroup);
        $classDetail = new DetailUserGroupsale();
        foreach ($remove as $id) {
            $dtUserGroup = $classDetail::where('id_group_sale', $id_group)->first();
            if ($dtUserGroup) {
                $dtUserGroup->delete();
            }
        }

        // dd($add);
        foreach ($add as $id) {
            $dtUserGroup = new DetailUserGroupsale();
            $dtUserGroup->id_user = $id;
            $dtUserGroup->id_group_sale = $id_group;
            $dtUserGroup->save();
        }
    }

    public function delete($id)
    {
        $gr = GroupUser::find($id);
        if ($gr) {
            $gr->delete();
            notify()->success('Xoá nhóm thành công', 'Thành công!');
            
        } else {
            notify()->error('Xoá nhóm thất bại', 'Thất bại!');
        }

        return back();
    }
}
