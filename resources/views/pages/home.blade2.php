@extends('layouts.default')
@section('content')
<style>
    a.link1:visited,.link1{font-size:12px;padding:3px;color:#0d9cd6;display:block;cursor:pointer;}
    .link1 i{font-size:12px;padding-right:5px;}
    a.link1:hover{color:darkorange;}

    .img-header{width:100%;height:auto;}
    .box-menu { width: 15.1%; height: auto; display: inline-block; margin-left: 3.9%; margin-top: 40px; position: relative; height: 220px; background-size: 100%; background-repeat: no-repeat; background-color: #e9e9ee; border-radius: 0 0 10px 10px;float:left; }
    .box-menu img{width:100%;height:auto;}
    .menu-container {   width: 100%;   padding-top: 20%; padding-left:10%;}
    .line1{height:220px}
    .line2{height:245px}

    .square { position: relative; width: 100%;}
    .square:after { content: ""; display: block; padding-bottom: 200%; }
    .content1 { position: absolute; width: 100%; height: 100%; }

    .da-mark{position: absolute;width: 6.6%;height: 12%;/*opacity: 0.90;background: white*/;display:block;}
    .da-source{position: absolute;width: 4.6%;height: 12%;/*opacity: 0.90;background: white*/;display:block;}
    .da-gh{position: absolute;width: 5%;height: 10%;/*opacity: 0.90;background: white*/;display:block;}

    .ss1{opacity:1;background:none;}
    .ss:hover {opacity:1;background:none;}
    .ss:hover i{opacity:1;}
    .ss i,.ss1 i{display:none;opacity:0;transition:all ease 0.5s;}

    @media (max-width: 1024px) {
        .link1{font-size:11px;}
        .line1 { height: 190px }
        .line2 { height: 250px }
    }

    @media (max-width: 767px) {
        .link1{font-size:9px;}
        .line1 { height: 230px }
        .line2 { height: 280px }
    }
</style>

<div class="row">
  <div style="max-width:1900px;">
      <div class="square">
          <div class="content1">
              <div style="position: relative;" class="">
                  <a style="position:absolute;right:10px;top:10px;cursor:pointer;display: inline-block;width: 25px;height: 25px;text-align: center;line-height: 25px;" class="btn-default" onclick="if(1==1){parent.closeModal(false)};return false;">
                      <i class="fa fa-close"></i> 
                  </a>

                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark1" class="da-mark ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark1','')" style="top: 18.8%; left: 9%;">
                      <i class="fa fa-check-circle-o"></i>
                      <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark2" class="da-mark ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark2','')" style="top: 35%; left: 9%;">
                      <i class="fa fa-check-circle-o"></i>
                      <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark3" class="da-mark ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark3','')" style="top: 51%; left: 9%;">
                      <i class="fa fa-check-circle-o"></i>
                      <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark4" class="da-mark ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark4','')" style="top: 67.5%; left: 9%;">
                      <i class="fa fa-check-circle-o"></i>
                      <i class="fa fa-circle-o"></i>
                  </a>

                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark5" class="da-source ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark5','')" style="top: 4.2%; left: 18.6%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark6" class="da-source ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark6','')" style="top: 20.4%; left: 18.6%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark7" class="da-source ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark7','')" style="top: 36.5%; left: 18.6%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark8" class="da-source ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark8','')" style="top: 51%; left: 18.6%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark9" class="da-source ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark9','')" style="top: 65.5%; left: 18.6%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark10" class="da-source ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark10','')" style="top: 82%; left: 18.6%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>

                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark11" class="da-gh ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark11','')" style="top: 12%; left: 82.8%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark12" class="da-gh ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark12','')" style="top: 25%; left: 82.8%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark13" class="da-gh ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark13','')" style="top: 37%; left: 82.8%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark14" class="da-gh ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark14','')" style="top: 49%; left: 82.8%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark15" class="da-gh ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark15','')" style="top: 61%; left: 82.8%;text-align:center;">
                      <img class="login-logo-ps" src="/data/images/hola_logo.png" style="text-align:center; margin:0 auto;width:100%; height: 110%;background-color:#fff;" alt="holaship">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <a onclick="showLoader();" id="dnn_ctr1479_Main_DashBoard2__Mark16" class="da-gh ss" href="javascript:__doPostBack('dnn$ctr1479$Main$DashBoard2$_Mark16','')" style="top: 73%; left: 82.8%;">
                  <i class="fa fa-check-circle-o"></i>
                  <i class="fa fa-circle-o"></i>
                  </a>
                  <img class="img-header" src="https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db1.png?v=2">
              </div>

              <div class="box-menu line1" style="background-image:url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db2.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/san-pham/danh-sach-san-pham" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo sản phẩm</a>
                  </div>
              </div>
              <div class="box-menu line1" style="background-image:url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db3.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/marketing/marketing-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Marketing dashboard</a>
                      <a href="/ld/marketing/cau-hinh-ket-noi-du-lieu" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối facebook</a>
                      <a href="/ld/marketing/cau-hinh-ket-noi-du-lieu" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối laddipage</a>
                      <a href="/ld/marketing/cau-hinh-ket-noi-du-lieu" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối website</a>
                      
                      <a href="/ld/unit-admin/import-contact" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Import excel</a>
                  </div>
              </div>
              <div class="box-menu line1" style="background-image:url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db4.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/sale/sale-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Tác nghiệp telesale</a>
                      <a href="/ld/sale/danh-sach-khach-hang" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Hồ sơ khách hàng</a>
                      <a href="/ld/sale/dashboard-sale" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Bảng xếp hạng doanh số</a>
                      <a href="/ld/sale/danh-sach-san-pham" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Đăng ký bán hàng</a> 
                      <a href="https://docs.pushsale.vn/3.telesale/5.-app-di-dong" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>App di động</a>
                  </div>
              </div>
              <div class="box-menu line1" style="background-image:url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db5.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/thong-ke/ty-le-chot-don-theo-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Thống kê tỉ lệ chốt</a>
                      <a href="/ld/thong-ke/bao-cao-cong-viec-sale" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Thống kê công việc</a>
                      <a href="/ld/sale/thong-ke-truong-nhom-sale" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Thống kê doanh số nhóm</a>
                  </div>
              </div>
              <div class="box-menu line1" style="background-image:url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db6.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/warehouse/warehouse-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Đăng đơn</a>
                      <a href="/ld/warehouse/quan-ly-kho" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Danh sách kho</a>
                      <a href="/ld/warehouse/quan-ly-kho/danh-sach-san-pham-kho" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Sản phẩm trong kho</a>
                      <a href="/ld/warehouse/quan-ly-kho/lich-su-nhap-xuat-kho" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Lịch sử xuất nhập</a>
                      <a href="/ld/warehouse/quan-ly-kho/bao-cao-xuat-nhap-theo-ngay" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Báo cáo xuất nhập</a>
                  </div>
              </div>

              <div class="box-menu line2" style="background-image: url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db7.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/unit-admin/ket-noi-giao-hang?tid=0" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối VN Post</a>
                      <a href="/ld/unit-admin/ket-noi-giao-hang?tid=0" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối Viettel Post</a>
                      <a href="/ld/unit-admin/ket-noi-giao-hang?tid=0" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối Giao hàng tiết kiệm</a>
                      <a href="/ld/unit-admin/ket-noi-giao-hang?tid=0" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối Giao hàng nhanh</a>
                      <a href="/ld/unit-admin/ket-noi-giao-hang?tid=0" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối J&amp;T</a>
                      <a href="/ld/unit-admin/ket-noi-giao-hang?tid=0" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kết nối Hola Ship</a>
                      
                      <a href="/ld/unit-admin/ket-noi-giao-hang?tid=0" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Tự vận chuyển</a>
                  </div>
              </div>
              <div class="box-menu line2" style="background-image: url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db8.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/accounting/accounting-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Đối soát đơn</a>
                      <a href="/ld/accounting/tong-ket-ke-hoach-thang" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Tổng kết lương thưởng</a>
                      <a href="/ld/accounting/bao-cao-kinh-doanh" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Báo cáo kinh doanh</a>
                      <a href="/ld/accounting/quan-ly-chi-phi" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo chi phí đơn vị</a>
                      <a href="/ld/accounting/nhom-chi-phi" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo nhóm chi phí</a>
                      <a href="/ld/accounting/danh-muc-chi-phi" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo danh mục chi phí</a>
                      <a href="/ld/accounting/danh-muc-don-vi-tinh" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo đơn vị tính</a>
                  </div>
              </div>
              <div class="box-menu line2" style="background-image: url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db9.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/ceo/dash-board" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>CEO dashboard</a>
                      <a href="/ld/ceo/power-dashboard" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Power dashboard</a>
                      <a href="/ld/thong-ke" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Thống kê tỉ lệ chốt theo khung giờ</a>
                      <a href="/ld/ceo/xu-huong" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Báo cáo biểu đồ</a>
                  </div>
              </div>
              <div class="box-menu line2" style="background-image: url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db10.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/unit-admin/danh-muc-kpi" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo KPIs</a>
                      <a href="/ld/unit-admin/thiet-lap-thuong-theo-doanh-so" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo thưởng</a>
                      <a href="/ld/unit-admin/thiet-lap-kpi" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kế hoạch doanh số tháng</a>
                      <a href="/ld/thong-ke/lap-ke-hoach-kinh-doanh" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Kế hoạch kinh doanh năm</a>
                  </div>
              </div>
              <div class="box-menu line2" style="background-image: url('https://pushsale.vn/Portals/_default/Skins/APP/images/dashboard/db11.png?v=2');">
                  <div class="menu-container">
                      <a href="/ld/unit-admin/quy-trinh-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Thiết lập mô hình công ty</a>
                      <a href="/ld/unit-admin/danh-sach-nhan-vien" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Danh sách nhân viên</a>
                      <a href="/ld/unit-admin/quan-ly-doi-nhom" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Quản lý nhóm sale</a>
                      <a href="/ld/unit-admin/danh-muc-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Khai báo danh mục tác nghiệp</a>
                      <a href="/ld/unit-admin/thiet-lap-tac-nghiep" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Thiết lập luồng tác nghiệp</a>
                      <a href="/ld/unit-admin/thiet-lap-chiet-khau-cod" class="link1" onclick="return parent_go_to_url(this);"><i class="fa  fa-play-circle-o"></i>Thiết lập chiết khấu, COD</a>
                  </div>
              </div>
              <div class="col-xs-12"></div>
          </div>
      </div>
  </div>
</div>
@stop