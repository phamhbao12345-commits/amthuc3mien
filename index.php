<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cấu hình Base URL động dựa vào vị trí thư mục hiện tại
$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', $baseDir === '/' ? '/' : $baseDir . '/');

// Nạp Model & Router cơ sở
require_once __DIR__ . '/app/models/Model.php';

// Tự động nạp class (Autoloader) theo chuẩn MVC
spl_autoload_register(function ($className) {
    $directories = [
        __DIR__ . '/app/controllers/',
        __DIR__ . '/app/models/',
    ];

    $aliases = [
        'admin_controller' => 'AdminController',
        'home_controller'  => 'HomeController',
        'homeController'   => 'HomeController',
        'adminController'  => 'AdminController',
        'VungMienModel'    => 'MonAnModel',
        'BookingModel'     => 'RestaurantModel',
        'DatabaseSeeder'   => 'Model',
    ];

    $name = $aliases[$className] ?? $className;

    foreach ($directories as $dir) {
        $file = $dir . $name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Đăng ký các Route phía người dùng (HomeController)
Router::get('/', [HomeController::class, 'index']);
Router::get('/mon-an/{slug}', [HomeController::class, 'monAnDetail']);
Router::get('/gan-ban', [HomeController::class, 'ganBan']);
Router::get('/nha-hang-uy-tin', [HomeController::class, 'uyTin']);
Router::get('/uu-dai-hot', [HomeController::class, 'uuDai']);
Router::get('/tin-tuc', [HomeController::class, 'tinTuc']);
Router::get('/tin-tuc/{slug}', [HomeController::class, 'tinTucDetail']);
Router::get('/bo-suu-tap', [HomeController::class, 'boSuuTap']);
Router::get('/danh-muc/{type}', [HomeController::class, 'danhMuc']);
Router::get('/search', [HomeController::class, 'search']);
Router::post('/booking', [HomeController::class, 'booking']);

// Authentication Routes (HomeController)
Router::get('/login', [HomeController::class, 'login']);
Router::post('/login', [HomeController::class, 'postLogin']);
Router::get('/register', [HomeController::class, 'register']);
Router::post('/register', [HomeController::class, 'postRegister']);
Router::get('/logout', [HomeController::class, 'logout']);

// Admin Routes
Router::get('/admin', [AdminController::class, 'index']);
Router::get('/admin/mon-an', [AdminController::class, 'monAn']);
Router::get('/admin/mon-an/add', [AdminController::class, 'monAnAdd']);
Router::post('/admin/mon-an/store', [AdminController::class, 'monAnStore']);
Router::get('/admin/mon-an/edit/{id}', [AdminController::class, 'monAnEdit']);
Router::post('/admin/mon-an/update/{id}', [AdminController::class, 'monAnUpdate']);
Router::get('/admin/mon-an/delete/{id}', [AdminController::class, 'monAnDelete']);

Router::get('/admin/nha-hang', [AdminController::class, 'nhaHang']);
Router::get('/admin/nha-hang/add', [AdminController::class, 'nhaHangAdd']);
Router::post('/admin/nha-hang/store', [AdminController::class, 'nhaHangStore']);
Router::get('/admin/nha-hang/edit/{id}', [AdminController::class, 'nhaHangEdit']);
Router::post('/admin/nha-hang/update/{id}', [AdminController::class, 'nhaHangUpdate']);
Router::get('/admin/nha-hang/delete/{id}', [AdminController::class, 'nhaHangDelete']);

Router::get('/admin/nguoi-dung', [AdminController::class, 'nguoiDung']);
Router::get('/admin/nguoi-dung/add', [AdminController::class, 'nguoiDungAdd']);
Router::post('/admin/nguoi-dung/store', [AdminController::class, 'nguoiDungStore']);
Router::get('/admin/nguoi-dung/edit/{id}', [AdminController::class, 'nguoiDungEdit']);
Router::post('/admin/nguoi-dung/update/{id}', [AdminController::class, 'nguoiDungUpdate']);
Router::get('/admin/nguoi-dung/delete/{id}', [AdminController::class, 'nguoiDungDelete']);

Router::get('/admin/tin-tuc', [AdminController::class, 'tinTuc']);
Router::get('/admin/tin-tuc/add', [AdminController::class, 'tinTucAdd']);
Router::post('/admin/tin-tuc/store', [AdminController::class, 'tinTucStore']);
Router::get('/admin/tin-tuc/edit/{id}', [AdminController::class, 'tinTucEdit']);
Router::post('/admin/tin-tuc/update/{id}', [AdminController::class, 'tinTucUpdate']);
Router::get('/admin/tin-tuc/delete/{id}', [AdminController::class, 'tinTucDelete']);

// Lấy URL hiện tại từ Query String (do .htaccess truyền vào)
$url = isset($_GET['url']) ? $_GET['url'] : '/';

// Chạy Router
Router::dispatch($url);

