<!DOCTYPE html>
<html lang="en">
  <head> @include('includes.head')  </head>
  <body>
    @include('notify::components.notify')
    <div class="sidebar sidebar-dark sidebar-fixed hide" id="sidebar">
        @include('includes.sidebar')
    </div>
    <div id="content-right" class="wrapper d-flex flex-column min-vh-100">
      <header class="header header-sticky mb-4"> 
        @include('includes.header')
        </header>
        @yield('content')
      {{-- <footer class="footer"> @include('includes.footer')</footer> --}}
    </div>
    
    @include('includes.foot')

   

    <!-- Top Achievers Popup with Tabs -->
    <div id="topAchieversModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 800px;">
            <div class="modal-content" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" style="font-weight: bold;">
                        <i class="fa fa-trophy" style="color: #ffd700;"></i> Tuyên Dương Nhân Viên Xuất Sắc Tháng Này
                    </h5>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="display: flex; border-bottom: 2px solid #dee2e6; margin-bottom: 20px;">
                        <li class="nav-item" style="flex: 1;">
                            <a class="nav-link active" data-toggle="tab" href="#topSales" role="tab" style="text-align: center; font-weight: bold; color: #667eea; border: none; padding: 10px;">
                                <i class="fa fa-star"></i> Top Sales
                            </a>
                        </li>
                        <li class="nav-item" style="flex: 1;">
                            <a class="nav-link" data-toggle="tab" href="#topDigital" role="tab" style="text-align: center; font-weight: bold; color: #667eea; border: none; padding: 10px;">
                                <i class="fa fa-laptop"></i> Top Digital
                            </a>
                        </li>
                        <li class="nav-item" style="flex: 1;">
                            <a class="nav-link" data-toggle="tab" href="#topIntern" role="tab" style="text-align: center; font-weight: bold; color: #667eea; border: none; padding: 10px;">
                                <i class="fa fa-graduation-cap"></i> Thực Tập Sinh
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- Top Sales Tab -->
                        <div id="topSales" class="tab-pane fade show active" role="tabpanel">
                            <div class="top-sales-list">
                                <!-- Top 1 -->
                                <div class="sale-item" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); padding: 20px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(255,215,0,0.3);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-right: 15px;">🥇</span>
                                        <div style="flex: 1;">
                                            <h6 style="margin: 0; font-weight: bold; color: #333; font-size: 18px;">Phạm Thị Ánh Tuyết</h6>
                                            <div style="margin-top: 8px; color: #555;">
                                                <div style="font-size: 14px;"><strong>Doanh số:</strong> <span style="color: #e74c3c; font-weight: bold;background: #ffecec;
    border-radius: 5px;
    padding: 3px;">364,160,000đ</span></div>
                                                <div style="font-size: 14px;"><strong>Lương & Thưởng:</strong> <span style="color: #27ae60; font-weight: bold;">29,998,649đ</span></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <img src="{{asset('storage/app/public/uploads/1757235240_z6986294919981_44106868eaa2f00ccc94b1e6afd1291f.jpg')}}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); object-fit: cover;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Top 2 -->
                                <div class="sale-item" style="background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%); padding: 20px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(192,192,192,0.3);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-right: 15px;">🥈</span>
                                        <div style="flex: 1;">
                                            <h6 style="margin: 0; font-weight: bold; color: #333; font-size: 18px;">Nguyễn Thị Quỳnh</h6>
                                            <div style="margin-top: 8px; color: #555;">
                                                <div style="font-size: 14px;"><strong>Doanh số:</strong> <span style="color: #e74c3c; font-weight: bold;background: #ffecec;
    border-radius: 5px;
    padding: 3px;">352,970,000đ</span></div>
                                                <div style="font-size: 14px;"><strong>Lương & Thưởng:</strong> <span style="color: #27ae60; font-weight: bold;">28,771,809đ</span></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <img src="{{asset('storage/app/public/uploads/1755282528_op.png')}}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); object-fit: cover;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Top 3 -->
                                <div class="sale-item" style="background: linear-gradient(135deg, #ffa349 0%, #e6ac7d 100%); padding: 20px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(205,127,50,0.3);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-right: 15px;">🥉</span>
                                        <div style="flex: 1;">
                                            <h6 style="margin: 0; font-weight: bold; color: #333; font-size: 18px;">Nguyễn Thị Quỳnh Như</h6>
                                            <div style="margin-top: 8px; color: #555;">
                                                <div style="font-size: 14px;"><strong>Doanh số:</strong> <span style="color: #e74c3c; font-weight: bold;background: #ffecec;
    border-radius: 5px;
    padding: 3px;">388,240,000đ</span></div>
                                                <div style="font-size: 14px;"><strong>Lương & Thưởng:</strong> <span style="color: #27ae60; font-weight: bold;">27,577,865đ</span></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <img src="{{asset('storage/app/public/uploads/1761393321_3a14da4e3a4bdb4be4bf2912c1401a81.jpg')}}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Digital Tab -->
                        <div id="topDigital" class="tab-pane fade" role="tabpanel">
                            <div class="top-digital-list">
                                <!-- Top 1 -->
                                <div class="digital-item" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); padding: 20px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(255,215,0,0.3);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-right: 15px;">🥇</span>
                                        <div style="flex: 1;">
                                            <h6 style="margin: 0; font-weight: bold; color: #333; font-size: 18px;">Hoàng Thị Thanh Huyền</h6>
                                            <div style="margin-top: 8px; color: #555;">
                                                <div style="font-size: 14px;"><strong>Doanh số:</strong> <span style="color: #e74c3c; font-weight: bold;background: #ffecec;
    border-radius: 5px;
    padding: 3px;">1,389,191,500đ</span></div>
                                                <div style="font-size: 14px;"><strong>Lương & Thưởng:</strong> <span style="color: #27ae60; font-weight: bold;">32,859,708đ</span></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <img src="{{asset('public/assets/img/avatars/8.png')}}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); object-fit: cover;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Top 2 -->
                                <div class="digital-item" style="background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%); padding: 20px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(192,192,192,0.3);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-right: 15px;">🥈</span>
                                        <div style="flex: 1;">
                                            <h6 style="margin: 0; font-weight: bold; color: #333; font-size: 18px;">Nguyễn Thị Anh Luyến</h6>
                                            <div style="margin-top: 8px; color: #555;">
                                                <div style="font-size: 14px;"><strong>Doanh số:</strong> <span style="color: #e74c3c; font-weight: bold;background: #ffecec;
    border-radius: 5px;
    padding: 3px;">771,080,000đ</span></div>
                                                <div style="font-size: 14px;"><strong>Lương & Thưởng:</strong> <span style="color: #27ae60; font-weight: bold;">21,737,652đ</span></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <img src="{{asset('storage/app/public/uploads/1753430384_7688e70ba60e2f50761f.jpg')}}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); object-fit: cover;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Top 3 -->
                                <div class="digital-item" style="background: linear-gradient(135deg, #ffa349 0%, #e6ac7d 100%); padding: 20px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(205,127,50,0.3);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-right: 15px;">🥉</span>
                                        <div style="flex: 1;">
                                            <h6 style="margin: 0; font-weight: bold; color: #333; font-size: 18px;">Lang Thuý Hiền</h6>
                                            <div style="margin-top: 8px; color: #555;">
                                                <div style="font-size: 14px;"><strong>Doanh số:</strong> <span style="color: #e74c3c; font-weight: bold;background: #ffecec;
    border-radius: 5px;
    padding: 3px;">655,992,000đ</span></div>
                                                <div style="font-size: 14px;"><strong>Lương & Thưởng:</strong> <span style="color: #27ae60; font-weight: bold;">18,253,602đ</span></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <img src="{{asset('storage/app/public/uploads/1760706279_meo-than-tai-tt03.jpg')}}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Intern Tab -->
                        <div id="topIntern" class="tab-pane fade" role="tabpanel">
                            <div class="top-intern-list">
                                <!-- Top 1 Intern -->
                                <div class="intern-item" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); padding: 20px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(255,215,0,0.3);">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-right: 15px;">🏆</span>
                                        <div style="flex: 1;">
                                            <h6 style="margin: 0; font-weight: bold; color: #333; font-size: 18px;">Nguyễn Đức Thắng</h6>
                                            <div style="margin-top: 8px; color: #555;">
                                                <div style="font-size: 14px;"><strong>Doanh số:</strong> <span style="color: #e74c3c; font-weight: bold;background: #ffecec;
    border-radius: 5px;
    padding: 3px;">607,872,000đ</span></div>
                                                <div style="font-size: 14px;"><strong>Lương & Thưởng:</strong> <span style="color: #27ae60; font-weight: bold;">12,878,570đ</span></div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <img src="{{asset('storage/app/public/uploads/1759163197_dfsfsfdsdf.png')}}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px;">
                    <button type="button" class="btn btn-secondary" id="closeAchieversModal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="dontShowAchieversAgain" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fa fa-times-circle"></i> Không hiện lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Tab styling */
        .nav-tabs .nav-link {
            border: none !important;
            border-bottom: 3px solid transparent !important;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #764ba2 !important;
            border-bottom: 3px solid #764ba2 !important;
        }
        .nav-tabs .nav-link.active {
            color: #667eea !important;
            border-bottom: 3px solid #667eea !important;
            background: transparent !important;
        }
        /* Tab content styling */
        .tab-content {
            min-height: 400px;
        }
        .tab-pane {
            padding: 10px 0;
        }
        .tab-pane.active {
            display: block !important;
        }
        
        /* Animation cho dòng chữ chạy */
        @keyframes scroll-left {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        /* Đảm bảo content không bị che bởi dòng chữ chạy */
        #content-right {
            padding-bottom: 50px !important;
        }
    </style>

    <!-- Canvas Confetti Library -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <script>
        // Hàm bắn pháo hoa
        function launchFireworks() {
            var duration = 30 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                
                // Bắn từ 2 bên
                confetti(Object.assign({}, defaults, { 
                    particleCount, 
                    origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 },
                    colors: ['#ffd700', '#ff6b6b', '#4ecdc4', '#45b7d1', '#f093fb', '#667eea']
                }));
                confetti(Object.assign({}, defaults, { 
                    particleCount, 
                    origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 },
                    colors: ['#ffd700', '#ff6b6b', '#4ecdc4', '#45b7d1', '#f093fb', '#667eea']
                }));
            }, 250);
        }

        $(document).ready(function() {
            // Kiểm tra localStorage xem đã ẩn popup chưa
            if (!localStorage.getItem('hideTopAchieversModal')) {
                // Hiển thị popup sau 2 giây
                setTimeout(function() {
                    $('#topAchieversModal').modal('show');
                    
                    // Bắn pháo hoa sau khi popup hiển thị
                    setTimeout(function() {
                        launchFireworks();
                    }, 500);
                }, 2000);
            }

            // Xử lý nút "Đóng"
            $('#closeAchieversModal').on('click', function() {
                $('#topAchieversModal').modal('hide');
            });

            // Xử lý nút "Không hiện lại"
            $('#dontShowAchieversAgain').on('click', function() {
                localStorage.setItem('hideTopAchieversModal', 'true');
                $('#topAchieversModal').modal('hide');
            });

            // Khởi tạo Bootstrap tabs
            $('#topAchieversModal .nav-tabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Debug: Log khi click vào tab
            $('#topAchieversModal .nav-link').on('click', function() {
                console.log('Tab clicked:', $(this).attr('href'));
                var targetTab = $(this).attr('href');
                
                // Ẩn tất cả tabs
                $('#topAchieversModal .tab-pane').removeClass('show active');
                
                // Hiển thị tab được click
                $(targetTab).addClass('show active');
                
                // Cập nhật active class cho nav-link
                $('#topAchieversModal .nav-link').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>

  </body>
</html>
