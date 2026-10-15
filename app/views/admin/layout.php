<!DOCTYPE html>
<?php 
/** 
 * @var string $title 
 * @var string $content 
 * @var string $username 
 */ 
?>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Admin Panel'; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#dc2626',
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
</head>
<body class="bg-gray-100 flex font-sans">
    
    <?php
    $adminUri = $_SERVER['REQUEST_URI'];
    $isMonAn = strpos($adminUri, '/admin/mon-an') !== false;
    $isNhaHang = strpos($adminUri, '/admin/nha-hang') !== false;
    $isNguoiDung = strpos($adminUri, '/admin/nguoi-dung') !== false;
    $isTinTuc = strpos($adminUri, '/admin/tin-tuc') !== false;
    $isDashboard = !$isMonAn && !$isNhaHang && !$isNguoiDung && !$isTinTuc;
    ?>
    <!-- Admin Sidebar Backdrop on Mobile -->
    <div id="admin-sidebar-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-40 hidden md:hidden transition-opacity opacity-0"></div>

    <!-- Sidebar -->
    <aside id="admin-sidebar" class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white min-h-screen flex flex-col -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none">
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800">
            <span class="text-xl font-bold uppercase tracking-wider text-red-500">AT3M Admin</span>
            <button id="admin-close-sidebar" class="md:hidden text-gray-400 hover:text-white p-1" aria-label="Đóng menu">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="<?php echo BASE_URL; ?>admin" class="flex items-center px-4 py-3 <?php echo $isDashboard ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'; ?> rounded-lg transition-colors">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/mon-an" class="flex items-center px-4 py-3 <?php echo $isMonAn ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'; ?> rounded-lg transition-colors">
                <i class="fas fa-utensils w-5"></i>
                <span class="ml-3 font-medium">Quản lý món ăn</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/nha-hang" class="flex items-center px-4 py-3 <?php echo $isNhaHang ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'; ?> rounded-lg transition-colors">
                <i class="fas fa-store w-5"></i>
                <span class="ml-3 font-medium">Quản lý nhà hàng</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/tin-tuc" class="flex items-center px-4 py-3 <?php echo $isTinTuc ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'; ?> rounded-lg transition-colors">
                <i class="fas fa-newspaper w-5"></i>
                <span class="ml-3 font-medium">Quản lý tin tức & blog</span>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/nguoi-dung" class="flex items-center px-4 py-3 <?php echo $isNguoiDung ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'; ?> rounded-lg transition-colors">
                <i class="fas fa-users w-5"></i>
                <span class="ml-3 font-medium">Người dùng</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-800 mt-auto">
            <a href="<?php echo BASE_URL; ?>" class="flex items-center justify-center w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                <i class="fas fa-home w-5"></i> Quay về trang chủ
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 sm:px-6 z-10">
            <div class="flex items-center md:hidden">
                <button id="admin-menu-btn" class="text-gray-600 hover:text-red-600 p-2 -ml-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-100" aria-label="Mở menu admin">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <span class="ml-2 font-bold text-gray-800 text-sm">Admin Panel</span>
            </div>
            <div class="hidden md:flex">
                <h1 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($title ?? 'Dashboard'); ?></h1>
            </div>
            <div class="flex items-center">
                <div class="relative flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-700 hidden sm:inline-block">Xin chào, <?php echo htmlspecialchars($username ?? 'Admin'); ?></span>
                    
                    <div class="flex items-center gap-2 border-l border-gray-200 pl-4 ml-2">
                        <a href="<?php echo BASE_URL; ?>" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-home"></i> <span class="hidden sm:inline">Trang chủ</span>
                        </a>
                        
                        <a href="<?php echo BASE_URL; ?>logout" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1.5 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt"></i> <span class="hidden sm:inline">Đăng xuất</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Page -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-medium"><?php echo $_SESSION['flash_success']; ?></p>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-medium"><?php echo $_SESSION['flash_error']; ?></p>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?php echo isset($content) ? $content : ''; ?>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('admin-menu-btn');
            const closeBtn = document.getElementById('admin-close-sidebar');
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');

            function openSidebar() {
                if (!sidebar || !backdrop) return;
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    sidebar.classList.remove('-translate-x-full');
                }, 10);
            }

            function closeSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }

            if (menuBtn) menuBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
        });
    </script>
</body>
</html>
