@extends('layouts.default')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    #laravel-notify .notify {
      z-index: 9999;
  }
  .hidden {
    display: none;
  }
  .select2.select2-container.select2-container--default{
    width: 100% !important;
  }
</style>

<?php $teleNhacTNCskh = $teleNhacTN = $leadSale = $id = $name = $member = $membersStr = $teleCskhData = $teleCreateOder = $teleHotData 
    = $teleBotToken = $teleCreateOderByCSKH = $teleNotifyCSKH = '';
    $status = 1;
    $isShareDataCSKH = 0;
    $listLeader = $members = $memberCskh = $srcs = $products = [];
    if (isset($group)) {
        $id = $group->id;
        $name = $group->name;
        $status = $group->status;
        $members = $group->sales->where('type_sale', 1)->pluck('id_user')->toArray();
        // dd($members);
        $srcs = $group->srcs->pluck('id')->toArray();
        $products = $group->products->pluck('id_product')->toArray();
        $teleCreateOderByCSKH = $group->tele_create_order_by_cskh;
        $teleHotData = $group->tele_hot_data;
        $teleCreateOder = $group->tele_create_order;
        $teleCskhData = $group->tele_cskh_data;
        $teleNhacTN = $group->tele_nhac_TN;
        $teleNhacTNCskh = $group->tele_nhac_TN_CSKH;
        $teleBotToken = $group->tele_bot_token;
        $isShareDataCSKH = $group->is_share_data_cskh;
        $listLeader = json_decode($group->lead_sale, true);
        $memberCskh = $group->sales->where('type_sale', 2)->pluck('id_user')->toArray();
    }
    $listLeadSale = Helper::getListLeadSale();

    $checkAll = isFullAccess(Auth::user()->role);
    $classNone = '';
    if (!$checkAll) {
        $classNone = 'hidden';
    }
?>
<div class="body flex-grow-1 px-3">
    <div class="row">
        <div id="notifi-box" class="hidden alert alert-success print-error-msg">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        </div>

        <div class="col-12">
            <div class="card mb-4 table">
                <div class="card-header" style="border: 1px solid #256cc2;color: #fff;background-color: #3782dc !important;"><strong>Lưu thông tin nhóm... </span></div>
                <div class="card-body">
                    <div class="body flex-grow-1">
                        <div class="tab-content rounded-bottom">
                            <form method="POST" action="{{route('save-group')}}">
                                <input type="hidden" name="id" value="{{$id}}">
                                {{ csrf_field() }}
                                <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                                    <div class="row">
                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="nameIP">Tên nhóm</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?> value="{{$name}}" class="form-control" name="name" id="nameIP" type="text" required>
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="mb-3 col-4 {{$classNone}}">
                                            
                                            <label for="leadSale">Trưởng nhóm</label>
                                            <select <?= !$checkAll ? 'readonly' : ''; ?>  required name="leadSale[]" id="list-leadSale" class="custom-select" multiple>    
                                                @foreach($listLeadSale as $sale) 
                                                    <option  <?= (is_array($listLeader) && in_array($sale->id, $listLeader)) ? 'selected' : ''; ?>  value="{{$sale->id}}">{{$sale->real_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <label for="like-color">Sale Data nóng</label>
                                            <select required name="member[]" id="list-sale" class="custom-select" multiple>
                                                @foreach($listSale as $sale) 
                                                    <option <?= (in_array($sale->id, $members)) ? 'selected' : ''; ?> value="{{$sale->id}}">{{$sale->real_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 form-group {{$classNone}}">
                                            <label for="like-color">Chọn nguồn data</label>
                                            <select <?= !$checkAll ? 'readonly' : ''; ?>  required name="src[]" id="list-src" class="custom-select" multiple> 
                                                @foreach($listSrc as $src) 
                                                    <option <?= (in_array($src->id, $srcs)) ? 'selected' : ''; ?> value="{{$src->id}}">{{$src->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 form-group {{$classNone}}">
                                            <label for="like-color">Chọn sản phẩm</label>
                                            <select <?= !$checkAll ? 'readonly' : ''; ?>  required name="product[]" id="list-product" class="custom-select" multiple>
                                                
                                                @foreach($listProduct as $product) 
                                                    <option <?= (in_array($product->id, $products)) ? 'selected' : ''; ?> value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @if ($checkAll)
                                        <div class="col-12 form-group">
                                            <label class="form-label" for="botTele">Token Bot Telegram</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?>  value="{{$teleBotToken}}" class="form-control" name="teleBotToken" id="botTele" type="text" required>
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="col-3  form-group">
                                            <label class="form-label" for="teleCreateOrderByCSKH">Chat Id Tạo đơn từ nhóm CSKH</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?>  value="{{$teleCreateOderByCSKH}}" class="form-control" name="teleCreateOrderByCSKH" id="teleCreateOrderByCSKH" type="text">
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="col-3  form-group">
                                            <label class="form-label" for="teleDataHot">Chat Id Data Nóng</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?>  value="{{$teleHotData}}" class="form-control" name="teleHotData" id="teleDataHot" type="text" required>
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="col-3 form-group">
                                            <label class="form-label" for="teleCreateOrder">Chat Id Chốt đơn</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?>  value="{{$teleCreateOder}}" class="form-control" name="teleCreateOrder" id="teleCreateOrder" type="text" required>
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="col-3 form-group">
                                            <label class="form-label" for="teleChatCskh">Chat Id CSKH</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?> value="{{$teleCskhData}}" class="form-control" name="teleCskhData" id="teleChatCskh" type="text" required>
                                            <p class="error_msg" id="name"></p>
                                        </div>
                                        <div class="col-3 form-group">
                                            <label class="form-label" for="teleChatCskh">Chat Id Nhắc TN</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?> value="{{$teleNhacTN}}" class="form-control" name="teleNhacTN" id="teleNhacTN" type="text" required>
                                            <p class="error_msg" id="teleNhacTN"></p>
                                        </div>
                                        <div class="col-3 form-group">
                                            <label class="form-label" for="teleNhacTNCskh">Chat Id Nhắc TN CSKH</label>
                                            <input <?= !$checkAll ? 'readonly' : ''; ?> value="{{$teleNhacTNCskh}}" class="form-control" name="teleNhacTNCskh" id="teleNhacTNCskh" type="text">
                                            <p class="error_msg" id="teleNhacTNCskh"></p>
                                        </div>
                                        @endif

                                        <div class="mb-3 col-4">
                                            <label class="form-label" for="qtyIP">Sale CSKH</label>
                                            <div class="form-check">
                                                <input <?= !$checkAll ? 'readonly' : ''; ?>  <?= ($isShareDataCSKH ) ? 'checked' : '';?> class="form-check-input" type="radio" name="shareDataCskh" value="1"
                                                    id="flexRadioDefaultCSKH">
                                                <label class="form-check-label" for="flexRadioDefaultCSKH">
                                                    Chia đều cho team CSKH của nhóm
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input <?= !$checkAll ? 'readonly' : ''; ?> <?= (!$isShareDataCSKH) ? 'checked' : '';?> class="form-check-input" type="radio" name="shareDataCskh" value="0"
                                                    id="flexRadioDefaultCSKH2" >
                                                <label  class="form-check-label" for="flexRadioDefaultCSKH2">
                                                    Đơn của sale nào thì người đó tự chăm
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-8 form-group">
                                            <div class="<?= ($isShareDataCSKH) ?: 'hidden' ?>" id="list-sale-cskh-div">
                                                <select name="memberCSKH[]" id="list-sale-cskh" class="hidden custom-select" multiple>
                                                    @foreach($listSale as $sale) 
                                                        <option <?= (in_array($sale->id, $memberCskh)) ? 'selected' : ''; ?> value="{{$sale->id}}">{{$sale->real_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-2 {{$classNone}}">
                                            <label class="form-label" for="qtyIP">Trạng Thái</label>
                                            <div class="form-check">
                                                <input <?= ($status) ? 'checked' : '';?> class="form-check-input" type="radio" name="status" value="1"
                                                    id="flexRadioDefault1">
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    Bật
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input <?= (!$status) ? 'checked' : '';?> class="form-check-input" type="radio" name="status" value="0"
                                                    id="flexRadioDefault2" >
                                                <label  class="form-check-label" for="flexRadioDefault2">
                                                    Tắt
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="loader hidden">
                                        <img src="{{asset('public/images/loader.gif')}}" alt="">
                                    </div>
                                    <button id="submit" class="btn btn-primary">Lưu</button>
                                </div>
                            </form>
                            
                        </div>
                    </div>           
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>
    $(function() {
        $('#list-sale').select2();
        $('#list-product').select2();
        $('#list-src').select2();
        $('#list-sale-cskh').select2();
        $('#list-leadSale').select2();
        
    });
    if ($('.flex.items-start').length) {
        console.log('tadada')
        
        setTimeout(function() { 
            $('.notify.fixed').hide();
        }, 3000);
    }
</script>

<script>
$(document).ready(function() {
    $("input[name='shareDataCskh']").click(function() {
    if ($(this).val() == 1) {
        $("#list-sale-cskh-div").show();
        $("#list-sale-cskh-div").focus();
    } else {
        $("#list-sale-cskh-div").hide();
    }
    });
});
</script>
@stop