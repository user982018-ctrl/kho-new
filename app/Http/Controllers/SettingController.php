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
use App\Models\Telegram;
use App\Models\Pancake;
use App\Models\LadiPage;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $pancake    = Pancake::first();
        $telegram   = Telegram::first();
        $ladiPage   = LadiPage::first();

        return view('pages.setting.index')->with('telegram', $telegram)->with('pancake', $pancake)->with('ladiPage', $ladiPage);
    }

    public function add()
    { 
        return view('pages.call.add');
    }

    public function telegramSave(Request $req) {
        if (isset($req->id)) {
            $tele = Telegram::find($req->id);
        } else {
            $tele = new Telegram();
        }

        $tele->token = $req->token_telegram;
        $tele->id_NVTR = $req->id_NVTR;
        $tele->id_CSKH = $req->id_CSKH;
        $tele->id_VUI = $req->id_VUI;
        $tele->status = $req->status;
        $tele->save();
        notify()->success('Cập nhật thông tin Telegram thành công.', 'Thành công!');
        

        return back();
    }

    public function pancakeSave(Request $req) {
        if (isset($req->id)) {
            $pancake = Pancake::find($req->id);
        } else {
            $pancake = new Pancake();
        }

        $pancake->token = $req->token_pancake;
        $pancake->page_id = $req->page_id;
        $pancake->status = $req->status;
        $pancake->save();
        notify()->success('Cập nhật thông tin Pancake thành công.', 'Thành công!');

        return back();
    }
    
    public function update($id) {;
        $call = Call::find($id);
        if($call){
            return view('pages.call.add')->with('call', $call);
        } 

        return redirect('/');
    }

    public function ladiSave(Request $r) 
    {
        if (isset($r->id)) {
            $ladi = LadiPage::find($r->id);
        } else {
            $ladi = new LadiPage();
        }

        $ladi->status = $r->status;
        $ladi->save();
        notify()->success('Cập nhật thông tin Ladipage thành công.', 'Thành công!');

        return back();
    }
}
