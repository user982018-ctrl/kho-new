<html lang="en-US">

<head id="Head">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <title>
        Thêm nguồn marketing
    </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('public/font-awesome/css/all.css')}}">
    <link href="{{ asset('public/css/pages/notify.css'); }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/vendors/simplebar/css/simplebar.css'); }}">
    <link rel="stylesheet" href="{{asset('public/css/vendors/simplebar.css'); }}">
    
    <!-- Main styles for this application-->
    <link href="{{ asset('public/css/style.css'); }}" rel="stylesheet">
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    <link href="{{ asset('public/css/examples.css'); }}" rel="stylesheet">
    <link href="{{ asset('public/css/customOld.css'); }}" rel="stylesheet">

    <link href="{{ asset('public/css/pages/notify.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <style>
        body {
            background: #fff;
        }

        .content-wrapper {
            background-color: white;
        }

        .select2-container-multi {
            height: auto !important;
        }

        #Form.showControlBar {
            margin-top: 0 !important;
        }

        #ControlBar {
            display: none;
        }
        .select2-container {
            height: auto !important;
        }
        .text-red {
            color: #dd4b39 !important;
        }
        .text-left {
            text-align: left;
        }
        .btn-primary {
            background-color: #3c8dbc;
            border-color: #367fa9;
        }

        .background-img-watermark {
            z-index: 999;
            opacity: 0.1;
            position: fixed;
            height: 100%;
            overflow: hidden;
            top: 0;
            right: 0;
            left: 0;
            pointer-events: none;
            background-image: url('');
            background-position: center center;
            background-repeat: no-repeat;
            /*background-size:cover;*/
            background-size: contain;
        }
        #laravel-notify .notify {
            z-index: 2;
        }
        
        .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        }

        .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        }

        .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
        }

        .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        }

        input:checked + .slider {
        background-color: #2196F3;
        }

        input:checked + .slider:before {
        transform: translateX(26px);
        }
    </style>
</head>

<?php
$listMktUser = Helper::getListMktUser();
$listGroup = Helper::getListGroup();

$data = [
    'type'  => '',
    'name'  => '',
    'user_digital'  => '',
    'link'  => '',
    'id_page'  => '',
    'id_group'  => '',
    'token'  => '',
    'id'  => '',
    'status' => 0
];

if (isset($dataSrc)) {
    $data['type'] = $dataSrc->type;
    $data['name'] = $dataSrc->name;
    if ($dataSrc->userDigital && !$dataSrc->userDigital->status) {
        $data['user_digital'] = -1;
    } else {
        $data['user_digital'] = $dataSrc->user_digital;
    }
    
    $data['link'] = $dataSrc->link;
    $data['id_page'] = $dataSrc->id_page;
    $data['id_group'] = $dataSrc->id_group;
    $data['token'] = $dataSrc->token;
    $data['id'] = $dataSrc->id;
    $data['status'] = $dataSrc->status;
}

?>

<body id="Body">
    @include('notify::components.notify')
    <form method="post" action="{{route('marketing-src-save')}}" id="Form" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="skin-blue-light hold-transition bodyfake sidebar-collapse">
            <div class="wrapper ">
                <div class="content-wrapper">
                    <div id="dnn_ContentPane" class="contentPane">
                        <div class="DnnModule DnnModule-ModuleLoader DnnModule-1427"><a name="1427"></a>
                            <input type="hidden" name='id' value="{{ $data['id']}}">
                            <div class="box-body" style="padding-bottom: 0;">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span class="h-label"> Loại kết nối <span
                                                        class="text-red"> (*)</span></span>
                                            </div>
                                            <div class="col-sm-10">
                                                <select required name="type" id="" tabindex="-1" title="">
                                                    <option <?= $data['type'] == 'pc' ? 'selected' : '' ?> value="pc">Pancake</option>
                                                    <option <?= $data['type'] == 'ladi' ? 'selected' : '' ?> value="ladi">Lading Page</option>
                                                    <option <?= $data['type'] == 'hotline' ? 'selected' : '' ?> value="hotline">Hotline</option>
                                                    <option <?= $data['type'] == 'old' ? 'selected' : '' ?> value="old">Khách cũ</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span
                                                    class="h-label">Tên nguồn dữ liệu <span
                                                        class="text-red"> (*)</span></span>
                                            </div>
                                            <div class="col-sm-10">
                                                <input required name="name"
                                                    type="text"
                                                    class="form-control validate[required]"
                                                    data-content="Tối đa 50 ký tự"
                                                    data-trigger="focus" data-toggle="popover"
                                                    value="<?= $data['name']?>">
                                            </div>
                                        </div>

                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span class="h-label">Người quảng cáo<span class="text-red"> (*)</span></span>
                                            </div>
                                            <div class="col-sm-10">
                                                <select required name="user_digital" tabindex="-1">
                                                    <option value="-1">-- Chọn người --</option>
                                                    @if ($data['user_digital'] == -1 && isset($dataSrc) && $dataSrc->userDigital)
                                                        <option selected value="{{$dataSrc->userDigital->id}}">{{$dataSrc->userDigital->real_name}}</option>
                                                    @endif
                                                    @foreach ($listMktUser->get() as $user)
                                                    <option <?= $data['user_digital'] == $user->id ? 'selected' : '' ?> value="{{$user->id}}">{{$user->real_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span
                                                    class="h-label">Link<span
                                                        class="text-red"></span></span>
                                            </div>
                                            <div class="col-sm-10">
                                                <input name="link" value="<?= $data['link']?>"
                                                    type="text"
                                                    class="form-control validate[required]"
                                                    data-content="Tối đa 50 ký tự"
                                                    data-trigger="focus" data-toggle="popover"
                                                    data-original-title="" title="">
                                            </div>
                                        </div>

                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span
                                                    id="dnn_ctr1427_Main_themnguondulieu_lblTenNguonDuLieu"
                                                    class="h-label">Page Id (nếu có)</span>
                                            </div>
                                            <div class="col-sm-10">
                                                <input
                                                    name="id_page" value="<?= $data['id_page']?>"
                                                    type="text"
                                                    class="form-control validate[required]"
                                                    data-content="Tối đa 50 ký tự"
                                                    data-trigger="focus" data-toggle="popover"
                                                    data-original-title="" title="">
                                            </div>
                                        </div>

                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span class="h-label">Nhóm<span
                                                        class="text-red"> (*)</span></span>
                                            </div>
                                            <div class="col-sm-10">
                                                <select required name="id_group"
                                                    data-errormessage-range-underflow="*Trường này bắt buộc"
                                                    tabindex="-1">
                                                   
                                                    @foreach ($listGroup->get() as $group)
                                                    <option <?= $data['id_group'] == $group->id ? 'selected' : '' ?> value="{{$group->id}}">{{$group->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span class="h-label">Token (nếu có)</span>
                                            </div>
                                            <div class="col-sm-10">
                                                <input
                                                    name="token" value="<?= $data['token']?>"
                                                    type="text"
                                                    class="form-control validate[required]"
                                                    data-content="Tối đa 50 ký tự"
                                                    data-trigger="focus" data-toggle="popover"
                                                   >
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-sm-2">
                                                <span class="h-label">Trạng thái</span>
                                            </div>
                                            <div class="col-sm-10">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" id="toggle-checkbox" name="status" <?= ($data['status'] == 1) ? 'checked' : '' ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-2">
                                                <span class="h-label">&nbsp;</span>
                                            </div>
                                            <div class="col-sm-10 text-left">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-save"></i> Lưu
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div style="height:100px;"></div>
                       
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="{{asset('public/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
</body>
</html>
