<link href="{{ asset('public/css/pages/notify.css'); }}" rel="stylesheet">
@extends('layouts.default')
@section('content')

@include('notify::components.notify')
<style>
    #laravel-notify .notify {
        z-index: 2;
    }
    .header.header-sticky {
        z-index: unset;
    }

</style>
<link rel="stylesheet" href="{{asset('public/css/pages/setting.css')}}">
<div class="body flex-grow-1 px-3">
  <div class="container-lg">
    <div class="row">
      <div id="notifi-box" class="hidden alert alert-success print-error-msg" >
          <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
      </div>
    
      <div class="accordion " id="accordionExample">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-coreui-toggle="collapse" data-coreui-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Tích hợp Telegram
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse">
            <div class="accordion-body">
              <div class="card-body">
                <div class="body flex-grow-1">
                  <div class="tab-content rounded-bottom">
                    <form action="{{route('telegram-save')}}" method="POST">
                      {{ csrf_field() }}
                      <input value="<?= ($telegram) ? $telegram->id : ''; ?>" name="id" type="hidden">
                      <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1000">
                        <div class="mb-3 row">
                          <label for="staticEmail" class="col-sm-2 col-form-label">Token</label>
                          <div class="col-lg-6 col-sm-10 ">
                            <input value="<?= ($telegram) ? $telegram->token : ''; ?>" required type="text" name="token_telegram" class="form-control" id="staticEmail">
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label for="inputPassword" class="col-sm-2 col-form-label">Chat ID Niềm vui tới rồi</label>
                          <div class="col-lg-6  col-sm-10">
                            <input value="<?= ($telegram) ? $telegram->id_NVTR : ''; ?>" required type="text" name="id_NVTR" class="form-control" id="inputPassword">
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label for="inputPassword" class="col-sm-2 col-form-label">Chat ID CSKH</label>
                          <div class="col-lg-6  col-sm-10">
                            <input value="<?= ($telegram) ? $telegram->id_CSKH : ''; ?>" required type="text" name="id_CSKH" class="form-control" id="inputPassword">
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label for="inputPassword" class="col-sm-2 col-form-label">Chat ID VUI</label>
                          <div class="col-lg-6  col-sm-10">
                            <input value="<?= ($telegram) ? $telegram->id_VUI : ''; ?>" required type="text" name="id_VUI" class="form-control" id="inputPassword">
                          </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="form-label col-sm-2 " for="qtyIP">Trạng Thái</label>
                            <div class="col-lg-6  col-sm-10">
                                <div class="form-check ">
                                    <input  <?= ($telegram && $telegram->status == 1) ? 'checked' : ''; ?> checked class="form-check-input" type="radio" name="status" value="1"
                                        id="flexRadioDefault1">
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Bật
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input <?= ($telegram && $telegram->status == 0) ? 'checked' : ''; ?> class="form-check-input" type="radio" name="status" value="0"
                                        id="flexRadioDefault2" >
                                    <label  class="form-check-label" for="flexRadioDefault2">
                                        Tắt
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                        <button type="submit" id="submit" class="btn btn-primary">Lưu</button>
                      </div>
                    </form>

                    <div class="row p-3 ">
                        <div class="col-12 tele-desc">
                            <p><strong>Production:</strong></p>
    
                            <p>&ensp; Gửi thông báo khi tạo đơn hàng mới đến 'nhóm Niềm vui tới rồi': -4126333554</p>
                                <p>&ensp; Gửi thông báo khi khách hàng nhận được hàng đến 'nhóm CSKH': -4128471334</p>
                                <p>&ensp; Gửi thông báo khi tạo đơn hàng mới đến 'nhóm VUI': -4195890963</p>
                            <p><strong>Dev/Local:</strong></p>
                            <p> &ensp; Gửi thông báo khi tạo đơn hàng mới đến 'nhóm Testbot': -4286962864</p>
                            <p>&ensp;  Gửi thông báo khi khách hàng nhận được hàng đến 'nhóm Testbot': -4286962864</p>
                        </pre></div>
                    </div>
                  </div>
                    
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-coreui-toggle="collapse" data-coreui-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              Tích hợp Pancake
            </button>
          </h2>
          <div id="collapseTwo" class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="card-body">
                    <div class="body flex-grow-1">
                        <div class="tab-content rounded-bottom">

                            <form action="{{route('pancake-save')}}" method="POST">
                                {{ csrf_field() }}
                                <input value="<?= ($pancake) ? $pancake->id : ''; ?>" name="id" type="hidden">
                                <div class="tab-pane p-3 active preview" role="tabpanel">
                                    <div class="mb-3 row">
                                        <label for="staticEmail" class="col-sm-2 col-form-label">Token</label>
                                        <div class="col-lg-6 col-sm-10 ">
                                          <input value="<?= ($pancake) ? $pancake->token : ''; ?>"  required type="text" name="token_pancake" class="form-control">
                                        </div>
                                      </div>
                                      <div class="mb-3 row">
                                        <label for="inputPassword" class="col-sm-2 col-form-label">Page Id</label>
                                        <div class="col-lg-6  col-sm-10">
                                          {{-- <input value="<?= ($pancake) ? $pancake->page_id : ''; ?>" required type="text" name="page_id" class="form-control"> --}}
                                            <textarea required name="page_id" id="" cols="100" rows="8">{{($pancake) ? $pancake->page_id : '';}}</textarea>
                                          </div>
                                      </div>
                                        <div class="mb-3 row">
                                            <label class="form-label col-sm-2 " for="qtyIP">Trạng Thái</label>
                                            <div class="col-lg-6  col-sm-10">
                                                <div class="form-check ">
                                                    <input  <?= ($pancake && $pancake->status == 1) ? 'checked' : ''; ?> checked class="form-check-input" type="radio" name="status" value="1"
                                                        id="radioPancake">
                                                    <label class="form-check-label" for="radioPancake">
                                                        Bật
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input <?= ($pancake && $pancake->status == 0) ? 'checked' : ''; ?> class="form-check-input" type="radio" name="status" value="0"
                                                        id="radioPancake2" >
                                                    <label  class="form-check-label" for="radioPancake2">
                                                        Tắt
                                                    </label>
                                                </div>
                                            </div>
                                            
                                      </div>
                                    <button type="submit" id="submit" class="btn btn-primary">Lưu</button>
                                </div>
                            </form>

                            <div class="row p-3 ">
                                <div class="col-12 tele-desc">
                                    <p><strong>Production:</strong></p>
            
                                    <p>&ensp; Gửi thông báo khi tạo đơn hàng mới đến 'nhóm Niềm vui tới rồi': -4126333554</p>
                                        <p>&ensp; Gửi thông báo khi khách hàng nhận được hàng đến 'nhóm CSKH': -4128471334</p>
                                    <p><strong>Dev/Local:</strong></p>
                                    <p> &ensp; Gửi thông báo khi tạo đơn hàng mới đến 'nhóm Testbot': -4140296352</p>
                                    <p>&ensp;  Gửi thông báo khi khách hàng nhận được hàng đến 'nhóm Testbot': -4140296352</p>

                                    <div class="mt-1">JSON: <code>
                                      {"Paulo":"106511079133421"}
                                      </code>
                                    </div>
                                </pre></div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-coreui-toggle="collapse" data-coreui-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
              Tích hợp Ladipage
            </button>
          </h2>
          <div id="collapse3" class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="card-body">
                    <div class="body flex-grow-1">
                        <div class="tab-content rounded-bottom">

                          <form action="{{route('ladi-save')}}" method="POST">
                              {{ csrf_field() }}
                              <input value="<?= isset($ladiPage) ? $ladiPage->id : ''; ?>" name="id" type="hidden">
                              <div class="tab-pane p-3 active preview" role="tabpanel">
                                
                                <div class="mb-3 row">
                                  <label class="form-label col-sm-2 " for="qtyIP">Trạng Thái</label>
                                  <div class="col-lg-6  col-sm-10">
                                      <div class="form-check ">
                                        <input  <?= (isset($ladiPage) && $ladiPage->status == 1) ? 'checked' : ''; ?> checked class="form-check-input" type="radio" name="status" value="1"
                                            id="radioLadipage">
                                        <label class="form-check-label" for="radioLadipage">
                                            Bật
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <input <?= (isset($ladiPage) && $ladiPage->status == 0) ? 'checked' : ''; ?> class="form-check-input" type="radio" name="status" value="0"
                                          id="radioLadipage2" >
                                        <label  class="form-check-label" for="radioLadipage2">Tắt</label>
                                      </div>
                                  </div>
                                    
                                </div>
                                  <button type="submit" id="submit" class="btn btn-primary">Lưu</button>
                              </div>
                          </form>

                          <div class="row p-3 ">
                            <div class="col-12 tele-desc">
                              <p>API: domain_url/api/ladipage</p>
                            </div>
                          </div>
                        </div>
                        
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
@stop