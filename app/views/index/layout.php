<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Nhà Hàng Ẩm Thực 3 Miền'; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#dc2626', // Red PasGo style
                        primaryDark: '#b91c1c',
                        surface: '#f3f4f6'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Tối ưu hiển thị: Trang xuất hiện tức thì, không bị trễ hay chớp trắng */
        main.flex-grow {
            opacity: 1;
        }

        /* Định dạng nội dung bài viết tin tức & blog */
        .prose h1, .prose h2, .prose h3, .prose h4 { font-weight: 700; color: #111827; margin-top: 1.5rem; margin-bottom: 0.75rem; }
        .prose h1 { font-size: 1.875rem; line-height: 2.25rem; }
        .prose h2 { font-size: 1.5rem; line-height: 2rem; }
        .prose h3 { font-size: 1.25rem; line-height: 1.75rem; }
        .prose p { margin-bottom: 1rem; line-height: 1.75; color: #374151; }
        .prose ul, .prose ol { margin-left: 1.5rem; margin-bottom: 1rem; }
        .prose li { margin-bottom: 0.5rem; list-style-type: disc; }
        .prose img { border-radius: 0.75rem; margin-top: 1rem; margin-bottom: 1rem; width: 100%; object-fit: cover; }
    </style>
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YE6JK0BGY8"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-YE6JK0BGY8');
</script>

<body class="font-sans bg-surface text-gray-800 antialiased selection:bg-primary selection:text-white flex flex-col min-h-screen">
    
    <!-- Header Top Navigation -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="container mx-auto px-4 lg:px-6 xl:px-12 max-w-[1400px]">
            <div class="flex items-center justify-between h-[60px]">
                <!-- Logo & Nav -->
                <div class="flex items-center gap-2 sm:gap-4 xl:gap-6">
                    <!-- Mobile Hamburger Button -->
                    <button id="mobile-menu-btn" type="button" class="lg:hidden p-2 -ml-1 text-gray-700 hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary/20 rounded-lg transition-colors" aria-label="Mở menu điều hướng">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <a href="<?php echo BASE_URL; ?>" class="text-primary font-black text-xl sm:text-2xl xl:text-3xl tracking-tighter italic whitespace-nowrap shrink-0">
                        ẨmThực3Miền
                    </a>
                    
                    <nav class="hidden lg:flex items-center gap-3 xl:gap-5 text-[13px] xl:text-[14px] font-bold text-gray-800 whitespace-nowrap">
                        <a href="<?php echo BASE_URL; ?>gan-ban" class="hover:text-primary transition-colors <?php echo strpos($_SERVER['REQUEST_URI'], 'gan-ban') !== false ? 'text-primary' : ''; ?>">Gần bạn</a>
                        <a href="<?php echo BASE_URL; ?>bo-suu-tap" class="hover:text-primary transition-colors <?php echo strpos($_SERVER['REQUEST_URI'], 'bo-suu-tap') !== false ? 'text-primary' : ''; ?>">Bộ sưu tập</a>
                        <a href="<?php echo BASE_URL; ?>tin-tuc" class="hover:text-primary transition-colors <?php echo strpos($_SERVER['REQUEST_URI'], 'tin-tuc') !== false ? 'text-primary' : ''; ?>">Tin tức & Blog</a>
                    </nav>
                </div>
                
                <!-- Weather, Search & Account -->
                <div class="flex items-center gap-2 xl:gap-3 shrink-0">
                    <!-- Expanding Top-Bar Search (Prompt #027) -->
                    <?php require __DIR__ . '/components/expanding_search.php'; ?>

                    <div id="weather-widget" class="hidden sm:flex items-center gap-1.5 text-[12px] xl:text-[13px] font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 px-2 xl:px-3 py-1.5 rounded-full border border-gray-200 transition-colors cursor-pointer whitespace-nowrap" title="Thời tiết hiện tại">
                        <i id="weather-icon" class="fas fa-spinner fa-spin text-gray-400"></i>
                        <span id="weather-temp" class="text-primary">--°C</span>
                        <span id="weather-desc" class="text-gray-500 font-medium hidden xl:inline ml-1 border-l border-gray-300 pl-2">Đang tải...</span>
                    </div>

                    <?php
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    if (isset($_SESSION['user_id'])): 
                    ?>
                        <div class="relative group cursor-pointer border-l border-gray-200 pl-2 xl:pl-4">
                            <div class="flex items-center text-[13px] xl:text-[14px] font-bold text-gray-700 hover:text-primary transition-colors whitespace-nowrap">
                                <i class="fas fa-user-circle text-primary text-lg sm:mr-2"></i> 
                                <span class="max-w-[70px] sm:max-w-[100px] truncate hidden sm:inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <i class="fas fa-chevron-down text-[10px] ml-1 text-gray-400 group-hover:text-primary"></i>
                            </div>
                            <!-- Dropdown -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-2">
                                    <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                                        <p class="text-xs text-gray-400 font-medium">Tài khoản</p>
                                        <p class="text-sm font-bold text-gray-800 truncate"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                    </div>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <a href="<?php echo BASE_URL; ?>admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-primary font-medium">
                                            <i class="fas fa-tachometer-alt w-5 text-center mr-1"></i> Trang quản trị
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                    <?php endif; ?>
                                    <a href="<?php echo BASE_URL; ?>logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-primary font-medium">
                                        <i class="fas fa-sign-out-alt w-5 text-center mr-1"></i> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Nút Đăng nhập / Quản trị khi chưa đăng nhập -->
                        <div class="border-l border-gray-200 pl-2 xl:pl-4">
                            <a href="<?php echo BASE_URL; ?>login" class="text-xs xl:text-sm font-bold text-gray-700 hover:text-primary transition-colors flex items-center gap-1.5 border border-gray-200 hover:border-red-300 px-2.5 sm:px-3 py-1.5 rounded-full bg-white shadow-xs">
                                <i class="fas fa-user-circle text-primary text-base"></i>
                                <span class="hidden sm:inline">Đăng nhập</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <?php if (isset($isBlog) && $isBlog): ?>
    <!-- Blog Sub Navigation -->
    <div class="bg-white border-b border-gray-200 shadow-sm sticky top-[60px] z-40 overflow-x-auto hide-scroll">
        <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
            <nav class="flex items-center gap-6 text-[13px] font-bold text-gray-700 whitespace-nowrap h-11">
                <a href="<?php echo BASE_URL; ?>tin-tuc" class="h-full flex items-center border-b-2 transition-colors <?php echo !isset($_GET['danh_muc']) || empty($_GET['danh_muc']) ? 'text-primary border-primary' : 'text-gray-600 hover:text-primary border-transparent'; ?>">Tất cả bài viết</a>
                <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode('Kinh doanh ăn uống'); ?>" class="h-full flex items-center border-b-2 transition-colors <?php echo (isset($_GET['danh_muc']) && $_GET['danh_muc'] === 'Kinh doanh ăn uống') ? 'text-primary border-primary' : 'text-gray-600 hover:text-primary border-transparent'; ?>">Kinh doanh ăn uống</a>
                <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode('Địa Điểm Ăn Uống'); ?>" class="h-full flex items-center border-b-2 transition-colors <?php echo (isset($_GET['danh_muc']) && $_GET['danh_muc'] === 'Địa Điểm Ăn Uống') ? 'text-primary border-primary' : 'text-gray-600 hover:text-primary border-transparent'; ?>">Địa Điểm Ăn Uống</a>
                <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode('Tiệc & Tổ chức tiệc'); ?>" class="h-full flex items-center border-b-2 transition-colors <?php echo (isset($_GET['danh_muc']) && $_GET['danh_muc'] === 'Tiệc & Tổ chức tiệc') ? 'text-primary border-primary' : 'text-gray-600 hover:text-primary border-transparent'; ?>">Tiệc & Tổ chức tiệc</a>
                <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode('Món ngon & Công thức'); ?>" class="h-full flex items-center border-b-2 transition-colors <?php echo (isset($_GET['danh_muc']) && $_GET['danh_muc'] === 'Món ngon & Công thức') ? 'text-primary border-primary' : 'text-gray-600 hover:text-primary border-transparent'; ?>">Món ngon & Công thức</a>
                <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode('Khám phá'); ?>" class="h-full flex items-center border-b-2 transition-colors <?php echo (isset($_GET['danh_muc']) && $_GET['danh_muc'] === 'Khám phá') ? 'text-primary border-primary' : 'text-gray-600 hover:text-primary border-transparent'; ?>">Khám phá</a>
            </nav>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content (với padding-bottom trên mobile để không bị thanh app bar che) -->
    <main class="flex-grow pb-16 md:pb-0">
        <?php echo isset($content) ? $content : ''; ?>
    </main>

    <!-- Footer (Simplified PasGo Style) -->
    <footer class="bg-white pt-10 pb-6 border-t border-gray-200 mt-auto">
        <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <a href="<?php echo BASE_URL; ?>" class="text-primary font-black text-2xl sm:text-3xl tracking-tighter italic mb-4 block">ẨmThực3Miền</a>
                    <p class="text-sm text-gray-600 mb-4">Mạng lưới nhà hàng ngon, uy tín và chất lượng hàng đầu Việt Nam.</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">Về chúng tôi</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-primary">Giới thiệu</a></li>
                        <li><a href="#" class="hover:text-primary">Quy chế hoạt động</a></li>
                        <li><a href="#" class="hover:text-primary">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">Dành cho khách hàng</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-primary">Hướng dẫn đặt bàn</a></li>
                        <li><a href="#" class="hover:text-primary">Câu hỏi thường gặp</a></li>
                        <li><a href="#" class="hover:text-primary">Liên hệ hỗ trợ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">Tải ứng dụng</h4>
                    <div class="flex gap-2 flex-wrap">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Download_on_the_App_Store_Badge.svg/135px-Download_on_the_App_Store_Badge.svg.png" alt="App Store" class="h-9 sm:h-10 cursor-pointer">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/135px-Google_Play_Store_badge_EN.svg.png" alt="Google Play" class="h-9 sm:h-10 cursor-pointer">
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 pt-6 text-center text-xs text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> Ẩm Thực 3 Miền. Đã đăng ký bản quyền.</p>
            </div>
        </div>
    </footer>
    
    <!-- Mobile Navigation Drawer -->
    <div id="mobile-drawer-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden opacity-0 transition-opacity duration-300"></div>
    <aside id="mobile-drawer" class="fixed top-0 left-0 bottom-0 w-[82vw] max-w-[320px] bg-white z-50 shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out flex flex-col overflow-y-auto">
        <!-- Drawer Header -->
        <div class="p-4 bg-primary text-white flex items-center justify-between border-b border-red-700">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-black italic tracking-tighter">ẨmThực3Miền</span>
            </div>
            <button id="close-mobile-drawer" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-colors" aria-label="Đóng menu">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <!-- User section in drawer -->
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-primary flex items-center justify-center font-bold text-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Xin chào,</p>
                        <p class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    </div>
                </div>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>admin" class="text-xs bg-red-600 text-white font-bold px-2.5 py-1.5 rounded-md hover:bg-red-700 transition">
                        Admin
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <div class="flex items-center gap-2 w-full">
                    <a href="<?php echo BASE_URL; ?>login" class="flex-1 text-center bg-primary text-white text-xs font-bold py-2 px-3 rounded-lg hover:bg-primaryDark transition">
                        <i class="fas fa-sign-in-alt mr-1"></i> Đăng nhập
                    </a>
                    <a href="<?php echo BASE_URL; ?>register" class="flex-1 text-center bg-white border border-gray-300 text-gray-700 text-xs font-bold py-2 px-3 rounded-lg hover:bg-gray-50 transition">
                        Đăng ký
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Weather info in drawer -->
        <div class="px-4 py-2.5 bg-red-50/50 border-b border-red-100/60 flex items-center justify-between text-xs font-semibold text-gray-700">
            <span class="flex items-center gap-1.5">
                <i class="fas fa-cloud-sun text-primary"></i> Thời tiết hôm nay:
            </span>
            <span id="drawer-weather-temp" class="text-primary font-bold">--°C</span>
        </div>

        <!-- Drawer Navigation Links -->
        <nav class="p-3 flex-1 space-y-1">
            <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-bold text-sm text-gray-800 hover:bg-red-50 hover:text-primary transition-colors">
                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-primary text-xs">
                    <i class="fas fa-house"></i>
                </span>
                <span>Trang chủ</span>
            </a>
            <a href="<?php echo BASE_URL; ?>gan-ban" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-bold text-sm text-gray-800 hover:bg-red-50 hover:text-primary transition-colors">
                <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-xs">
                    <i class="fas fa-location-crosshairs"></i>
                </span>
                <span>Quán ăn gần bạn (5km)</span>
            </a>
            <a href="<?php echo BASE_URL; ?>bo-suu-tap" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-bold text-sm text-gray-800 hover:bg-red-50 hover:text-primary transition-colors">
                <span class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-xs">
                    <i class="fas fa-layer-group"></i>
                </span>
                <span>Bộ sưu tập ẩm thực</span>
            </a>
            <a href="<?php echo BASE_URL; ?>tin-tuc" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-bold text-sm text-gray-800 hover:bg-red-50 hover:text-primary transition-colors">
                <span class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 text-xs">
                    <i class="fas fa-newspaper"></i>
                </span>
                <span>Tin tức & Cẩm nang F&B</span>
            </a>
            
            <div class="border-t border-gray-100 my-2 pt-2">
                <div class="px-3.5 py-1 text-[11px] font-bold uppercase text-gray-400 tracking-wider">Khám phá vùng miền</div>
                <a href="<?php echo BASE_URL; ?>#menu" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-red-50 hover:text-primary transition-colors">
                    <i class="fas fa-bowl-rice text-red-500 w-4 text-center"></i> Đặc sản Miền Bắc
                </a>
                <a href="<?php echo BASE_URL; ?>#menu" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-red-50 hover:text-primary transition-colors">
                    <i class="fas fa-pepper-hot text-amber-500 w-4 text-center"></i> Đặc sản Miền Trung
                </a>
                <a href="<?php echo BASE_URL; ?>#menu" class="flex items-center gap-3 px-3.5 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-red-50 hover:text-primary transition-colors">
                    <i class="fas fa-fish-fins text-emerald-500 w-4 text-center"></i> Đặc sản Miền Nam
                </a>
            </div>
        </nav>

        <!-- Drawer Footer -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 text-xs text-gray-500">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>logout" class="block w-full py-2 text-center text-red-600 font-bold bg-white rounded-lg border border-red-200 hover:bg-red-50 transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
                </a>
            <?php endif; ?>
            <p class="text-center text-[11px] text-gray-400 mt-2">&copy; <?php echo date('Y'); ?> Ẩm Thực 3 Miền</p>
        </div>
    </aside>

    <!-- Mobile Bottom App Navigation Bar (Only on mobile < md) -->
    <div class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-gray-200 z-40 h-14 px-2 shadow-[0_-2px_10px_rgba(0,0,0,0.06)] flex items-center justify-around">
        <a href="<?php echo BASE_URL; ?>" class="flex flex-col items-center justify-center flex-1 py-1 <?php echo ($_SERVER['REQUEST_URI'] === '/' || strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) ? 'text-primary font-bold' : 'text-gray-500 hover:text-primary'; ?> transition-colors">
            <i class="fas fa-house text-base"></i>
            <span class="text-[10px] mt-0.5">Trang chủ</span>
        </a>
        <a href="<?php echo BASE_URL; ?>gan-ban" class="flex flex-col items-center justify-center flex-1 py-1 <?php echo strpos($_SERVER['REQUEST_URI'], 'gan-ban') !== false ? 'text-primary font-bold' : 'text-gray-500 hover:text-primary'; ?> transition-colors">
            <i class="fas fa-location-dot text-base"></i>
            <span class="text-[10px] mt-0.5">Gần bạn</span>
        </a>
        <button type="button" onclick="document.querySelector('.u027-go')?.click();" class="flex flex-col items-center justify-center flex-1 py-1 text-gray-500 hover:text-primary transition-colors">
            <div class="w-8 h-8 rounded-full bg-red-50 text-primary flex items-center justify-center -mt-3 shadow-sm border border-red-100">
                <i class="fas fa-search text-xs"></i>
            </div>
            <span class="text-[10px] mt-0.5">Tìm kiếm</span>
        </button>
        <a href="<?php echo BASE_URL; ?>tin-tuc" class="flex flex-col items-center justify-center flex-1 py-1 <?php echo strpos($_SERVER['REQUEST_URI'], 'tin-tuc') !== false ? 'text-primary font-bold' : 'text-gray-500 hover:text-primary'; ?> transition-colors">
            <i class="fas fa-newspaper text-base"></i>
            <span class="text-[10px] mt-0.5">Tin tức</span>
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? BASE_URL . 'admin' : '#'; ?>" class="flex flex-col items-center justify-center flex-1 py-1 text-gray-500 hover:text-primary transition-colors">
                <i class="fas fa-user-circle text-base"></i>
                <span class="text-[10px] mt-0.5 truncate max-w-[50px]"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>login" class="flex flex-col items-center justify-center flex-1 py-1 text-gray-500 hover:text-primary transition-colors">
                <i class="fas fa-user text-base"></i>
                <span class="text-[10px] mt-0.5">Tài khoản</span>
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Floating Notification -->
    <div class="fixed bottom-20 md:bottom-6 right-4 md:right-6 z-40 md:z-50">
        <button class="bg-primary text-white rounded-xl w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center shadow-lg hover:bg-primaryDark transition-colors relative group">
            <i class="fa-solid fa-comment-dots text-xl sm:text-2xl"></i>
        </button>
    </div>

    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-auto overflow-hidden relative animate-[fadeIn_0.3s_ease-out] max-h-[92vh] overflow-y-auto">
            <div class="bg-primary px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg"><i class="fa-regular fa-calendar-check mr-2"></i> Đặt Bàn Ngay</h3>
                <button id="close-modal" class="text-white hover:text-gray-200 transition-colors"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form id="booking-form" class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên *</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded px-3 py-2 outline-none focus:border-primary">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                    <input type="tel" name="phone" required placeholder="09xxxxxxxx" class="w-full border border-gray-300 rounded px-3 py-2 outline-none focus:border-primary">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày giờ đến *</label>
                    <input type="datetime-local" name="booking_date" required class="w-full border border-gray-300 rounded px-3 py-2 outline-none focus:border-primary">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng khách *</label>
                    <input type="number" name="guests" min="1" max="100" required value="2" class="w-full border border-gray-300 rounded px-3 py-2 outline-none focus:border-primary">
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-primaryDark text-white font-bold py-3 rounded transition-colors shadow">
                    Xác nhận đặt bàn
                </button>
            </form>
        </div>
    </div>

    <!-- Script Configuration -->
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Leaflet JS for Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>asset/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            // ==========================================
            // WEATHER WIDGET LOGIC
            // ==========================================
            const widget = document.getElementById('weather-widget');
            const iconEl = document.getElementById('weather-icon');
            const tempEl = document.getElementById('weather-temp');
            const descEl = document.getElementById('weather-desc');

            async function loadWeather(lat, lon, locationName) {
                try {
                    iconEl.className = 'fas fa-spinner fa-spin text-gray-400 text-[15px]';
                    
                    const res = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,weather_code&timezone=Asia/Bangkok`);
                    const data = await res.json();
                    
                    if (data && data.current) {
                        const temp = Math.round(data.current.temperature_2m);
                        const code = data.current.weather_code;
                        
                        tempEl.innerText = `${temp}°C`;
                        widget.title = `Nhấn để đổi địa điểm (Hiện tại: ${locationName})`;
                        
                        let desc = '';
                        // WMO Weather interpretation codes
                        if (code === 0) {
                            iconEl.className = 'fas fa-sun text-yellow-500 text-[15px]';
                            desc = 'Nắng đẹp, lý tưởng dạo phố!';
                        } else if (code === 1 || code === 2) {
                            iconEl.className = 'fas fa-cloud-sun text-yellow-400 text-[15px]';
                            desc = 'Ít mây, mát mẻ';
                        } else if (code === 3) {
                            iconEl.className = 'fas fa-cloud text-gray-400 text-[15px]';
                            desc = 'Trời râm mát';
                        } else if (code === 45 || code === 48) {
                            iconEl.className = 'fas fa-smog text-gray-400 text-[15px]';
                            desc = 'Có sương mù';
                        } else if (code >= 51 && code <= 67) {
                            iconEl.className = 'fas fa-cloud-rain text-blue-400 text-[15px]';
                            desc = 'Đang mưa, nên ăn trong nhà!';
                        } else if (code >= 71 && code <= 77) {
                            iconEl.className = 'fas fa-snowflake text-blue-200 text-[15px]';
                            desc = 'Có tuyết';
                        } else if (code >= 80 && code <= 82) {
                            iconEl.className = 'fas fa-cloud-showers-heavy text-blue-500 text-[15px]';
                            desc = 'Mưa lớn, gọi ship là chân ái!';
                        } else if (code >= 95) {
                            iconEl.className = 'fas fa-bolt text-yellow-600 text-[15px]';
                            desc = 'Có dông bão, ở nhà thôi!';
                        } else {
                            iconEl.className = 'fas fa-cloud text-gray-400 text-[15px]';
                            desc = 'Thời tiết bình thường';
                        }

                        // Hiển thị tên địa điểm kèm lời khuyên
                        descEl.innerHTML = `<span class="font-bold text-gray-700">${locationName}:</span> ${desc}`;
                    }
                } catch (err) {
                    console.error("Lỗi tải thời tiết", err);
                }
            }

            // Mặc định load thời tiết Hồ Chí Minh
            loadWeather(10.823, 106.6296, 'Hồ Chí Minh');

            // Bắt sự kiện click để tìm kiếm địa điểm
            widget.addEventListener('click', async function() {
                const { value: cityName } = await Swal.fire({
                    title: 'Tra cứu thời tiết',
                    input: 'text',
                    inputLabel: 'Nhập tên tỉnh/thành phố hoặc khu vực:',
                    inputPlaceholder: 'Ví dụ: Hà Nội, Đà Lạt, Sapa, Hội An...',
                    showCancelButton: true,
                    confirmButtonText: 'Tìm kiếm',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#dc2626'
                });

                if (cityName) {
                    Swal.fire({
                        title: 'Đang tìm kiếm...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        // Gọi API Geocoding để lấy tọa độ từ tên địa điểm
                        const geoRes = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(cityName)}&count=1&language=vi&format=json`);
                        const geoData = await geoRes.json();
                        
                        if (geoData.results && geoData.results.length > 0) {
                            const location = geoData.results[0];
                            // Tải lại thời tiết bằng tọa độ mới
                            await loadWeather(location.latitude, location.longitude, location.name);
                            Swal.close();
                        } else {
                            Swal.fire('Opps!', 'Không tìm thấy địa điểm này. Vui lòng thử lại tên khác nhé!', 'warning');
                        }
                    } catch (e) {
                        Swal.fire('Lỗi', 'Có lỗi xảy ra khi kết nối. Vui lòng thử lại!', 'error');
                    }
                }
            });

            // ==========================================
            // MOBILE DRAWER NAVIGATION LOGIC
            // ==========================================
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const closeDrawerBtn = document.getElementById('close-mobile-drawer');
            const drawer = document.getElementById('mobile-drawer');
            const drawerBackdrop = document.getElementById('mobile-drawer-backdrop');

            function openMobileDrawer() {
                if (!drawer || !drawerBackdrop) return;
                drawerBackdrop.classList.remove('hidden');
                setTimeout(() => {
                    drawerBackdrop.classList.remove('opacity-0');
                    drawer.classList.remove('-translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            }

            function closeMobileDrawer() {
                if (!drawer || !drawerBackdrop) return;
                drawer.classList.add('-translate-x-full');
                drawerBackdrop.classList.add('opacity-0');
                setTimeout(() => {
                    drawerBackdrop.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }

            if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileDrawer);
            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeMobileDrawer);
            if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeMobileDrawer);

            // Sync drawer weather with main weather
            const drawerWeatherTemp = document.getElementById('drawer-weather-temp');
            const originalTempEl = document.getElementById('weather-temp');
            if (drawerWeatherTemp && originalTempEl) {
                const observer = new MutationObserver(() => {
                    drawerWeatherTemp.innerText = originalTempEl.innerText;
                });
                observer.observe(originalTempEl, { childList: true, characterData: true, subtree: true });
            }
        });

    </script>
</body>
</html>
