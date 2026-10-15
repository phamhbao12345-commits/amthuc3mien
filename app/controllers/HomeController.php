<?php
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/MonAnModel.php';
require_once __DIR__ . '/../models/RestaurantModel.php';
require_once __DIR__ . '/../models/TinTucModel.php';
require_once __DIR__ . '/../models/UserModel.php';

/**
 * HomeController - Quản lý toàn bộ giao diện và chức năng phía người dùng (Client)
 */
class HomeController extends Controller {
    private $monAnModel;
    private $restaurantModel;
    private $tinTucModel;
    private $bookingModel;
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->monAnModel = new MonAnModel();
        $this->restaurantModel = new RestaurantModel();
        $this->tinTucModel = new TinTucModel();
        $this->bookingModel = new BookingModel();
        $this->userModel = new UserModel();
    }

    // ==========================================
    // 1. TRANG CHỦ
    // ==========================================
    public function index() {
        DatabaseSeeder::run();

        $danhSachMonAn = $this->monAnModel->getAll();

        $this->render('index/home', [
            'title' => 'Trang chủ - Ẩm thực 3 miền',
            'monAn' => $danhSachMonAn
        ]);
    }

    // ==========================================
    // 2. CHI TIẾT MÓN ĂN
    // ==========================================
    public function monAnDetail($slug) {
        $chiTietMonAn = $this->monAnModel->getBySlug($slug);

        if (!$chiTietMonAn) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Không tìm thấy món ăn bạn yêu cầu.</p>";
            return;
        }

        $goiYQuanAn = $this->restaurantModel->getSuggestedRestaurantsForDish($chiTietMonAn);

        $tinhThanh = trim($chiTietMonAn['tinh_thanh'] ?? '');
        if (empty($tinhThanh) || ($chiTietMonAn['slug'] ?? '') === 'lau-mam') {
            $tenMon = $chiTietMonAn['ten_mon'] ?? '';
            $slug = $chiTietMonAn['slug'] ?? '';
            if ($slug === 'lau-mam' || stripos($tenMon, 'Lẩu mắm') !== false) $tinhThanh = 'Đồng Tháp';
            elseif (stripos($tenMon, 'Hà Nội') !== false) $tinhThanh = 'Hà Nội';
            elseif (stripos($tenMon, 'Huế') !== false) $tinhThanh = 'Thừa Thiên Huế';
            elseif (stripos($tenMon, 'Sài Gòn') !== false || stripos($tenMon, 'Hồ Chí Minh') !== false) $tinhThanh = 'TP. Hồ Chí Minh';
            elseif (stripos($tenMon, 'Cần Thơ') !== false) $tinhThanh = 'TP. Cần Thơ';
            elseif (stripos($tenMon, 'Đồng Tháp') !== false || stripos($tenMon, 'Sa Đéc') !== false || stripos($tenMon, 'Lai Vung') !== false) $tinhThanh = 'Đồng Tháp';
            elseif (stripos($tenMon, 'Tây Ninh') !== false) $tinhThanh = 'Tây Ninh';
            elseif (stripos($tenMon, 'An Giang') !== false || stripos($tenMon, 'Châu Đốc') !== false) $tinhThanh = 'An Giang';
            elseif (stripos($tenMon, 'Cà Mau') !== false) $tinhThanh = 'Cà Mau';
            elseif (stripos($tenMon, 'Vĩnh Long') !== false) $tinhThanh = 'Vĩnh Long';
            elseif (stripos($tenMon, 'Đồng Nai') !== false) $tinhThanh = 'Đồng Nai';
            elseif (stripos($tenMon, 'Đà Nẵng') !== false) $tinhThanh = 'Đà Nẵng';
            elseif (stripos($tenMon, 'Quảng Nam') !== false || stripos($tenMon, 'Hội An') !== false) $tinhThanh = 'Quảng Nam';
            elseif (stripos($tenMon, 'Bình Định') !== false) $tinhThanh = 'Bình Định';
            elseif (stripos($tenMon, 'Phú Yên') !== false) $tinhThanh = 'Phú Yên';
            elseif (stripos($tenMon, 'Hải Phòng') !== false) $tinhThanh = 'Hải Phòng';
            elseif (stripos($tenMon, 'Ninh Bình') !== false) $tinhThanh = 'Ninh Bình';
            elseif (stripos($tenMon, 'Sapa') !== false || stripos($tenMon, 'Lào Cai') !== false) $tinhThanh = 'Lào Cai';
            elseif (stripos($tenMon, 'Quảng Ninh') !== false || stripos($tenMon, 'Hạ Long') !== false || stripos($tenMon, 'Tiên Yên') !== false) $tinhThanh = 'Quảng Ninh';
            elseif (stripos($tenMon, 'Nam Định') !== false || stripos($tenMon, 'Giao Thủy') !== false) $tinhThanh = 'Nam Định';
            elseif (stripos($tenMon, 'Nha Trang') !== false || stripos($tenMon, 'Khánh Hòa') !== false || stripos($tenMon, 'Ninh Hòa') !== false) $tinhThanh = 'Khánh Hòa';
            elseif (stripos($tenMon, 'Đà Lạt') !== false || stripos($tenMon, 'Lâm Đồng') !== false) $tinhThanh = 'Lâm Đồng';
            
            if (!empty($tinhThanh)) {
                $chiTietMonAn['tinh_thanh'] = $tinhThanh;
            }
        }

        $idVungMien = $chiTietMonAn['id_vung_mien'] ?? null;
        $currentId = $chiTietMonAn['id'] ?? 0;
        $monGoiY = $this->monAnModel->getRelatedDishes($tinhThanh, $idVungMien, $currentId, 4);

        $this->render('index/mon_an_detail', [
            'title' => $chiTietMonAn['ten_mon'] . ' - Ẩm thực 3 miền',
            'monAn' => $chiTietMonAn,
            'goiYQuanAn' => $goiYQuanAn,
            'monGoiY' => $monGoiY
        ]);
    }

    // ==========================================
    // 3. ĐỊA ĐIỂM GẦN BẠN
    // ==========================================
    public function ganBan() {
        $restaurants = $this->restaurantModel->getAll();

        $this->render('index/gan_ban', [
            'title' => 'Địa điểm gần bạn - Ẩm thực 3 miền',
            'restaurants' => $restaurants
        ]);
    }

    // ==========================================
    // 4. TOP NHÀ HÀNG UY TÍN
    // ==========================================
    public function uyTin() {
        $restaurants = $this->restaurantModel->getReputable();

        $this->render('index/uy_tin', [
            'title' => 'Top nhà hàng uy tín - Ẩm thực 3 miền',
            'restaurants' => $restaurants
        ]);
    }

    // ==========================================
    // 5. ƯU ĐÃI HOT
    // ==========================================
    public function uuDai() {
        $restaurants = $this->restaurantModel->getPromotions();

        $this->render('index/uu_dai', [
            'title' => 'Ưu đãi hot - Ẩm thực 3 miền',
            'restaurants' => $restaurants
        ]);
    }

    // ==========================================
    // 6. TIN TỨC & BLOG ẨM THỰC
    // ==========================================
    public function tinTuc() {
        $danh_muc = $_GET['danh_muc'] ?? '';
        $keyword = trim($_GET['keyword'] ?? '');

        $danhSachTinTuc = $this->tinTucModel->getAll($danh_muc, $keyword);
        $tinNoiBat = $this->tinTucModel->getFeatured(2);
        $tinXemNhieu = $this->tinTucModel->getMostViewed(4);
        $danhMucList = $this->tinTucModel->getCategories();

        $this->render('index/tin_tuc', [
            'title' => 'Tin tức & Blog - Ẩm thực 3 miền',
            'isBlog' => true,
            'danhSachTinTuc' => $danhSachTinTuc,
            'tinNoiBat' => $tinNoiBat,
            'tinXemNhieu' => $tinXemNhieu,
            'danhMucList' => $danhMucList,
            'currentDanhMuc' => $danh_muc,
            'keyword' => $keyword
        ]);
    }

    public function tinTucDetail($slug) {
        $baiViet = $this->tinTucModel->getBySlug($slug);

        if (!$baiViet) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Không tìm thấy bài viết bạn yêu cầu.</p>";
            return;
        }

        $this->tinTucModel->incrementViews($baiViet['id']);
        $tinLienQuan = $this->tinTucModel->getRelated($baiViet['danh_muc'], $baiViet['id'], 3);
        $tinXemNhieu = $this->tinTucModel->getMostViewed(5);

        $this->render('index/tin_tuc_detail', [
            'title' => $baiViet['tieu_de'] . ' - Tin tức & Blog Ẩm thực 3 miền',
            'isBlog' => true,
            'baiViet' => $baiViet,
            'tinLienQuan' => $tinLienQuan,
            'tinXemNhieu' => $tinXemNhieu
        ]);
    }

    // ==========================================
    // 7. BỘ SƯU TẬP ẨM THỰC
    // ==========================================
    public function boSuuTap() {
        $collections = [
            [
                'id' => 1,
                'title' => 'Nhà hàng, quán ăn ngon QUẬN 1',
                'count' => 96,
                'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=600',
                'category' => 'quan',
                'category_name' => 'Khu vực',
                'link' => BASE_URL . 'search?location=' . urlencode('Quận 1'),
                'description' => 'Điểm hẹn ẩm thực trung tâm Sài Gòn sang trọng & đa dạng'
            ],
            [
                'id' => 2,
                'title' => 'Nhà hàng, quán ăn ngon QUẬN 3',
                'count' => 49,
                'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=600',
                'category' => 'quan',
                'category_name' => 'Khu vực',
                'link' => BASE_URL . 'search?location=' . urlencode('Quận 3'),
                'description' => 'Không gian sân vườn yên tĩnh, ẩm thực truyền thống & hiện đại'
            ],
            [
                'id' => 3,
                'title' => 'Nhà hàng, quán ăn ngon QUẬN 4',
                'count' => 28,
                'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=600',
                'category' => 'quan',
                'category_name' => 'Khu vực',
                'link' => BASE_URL . 'search?location=' . urlencode('Quận 4'),
                'description' => 'Thiên đường ốc, hải sản tươi sống và món ăn đường phố'
            ],
            [
                'id' => 4,
                'title' => 'Nhà hàng, quán ăn ngon QUẬN 5',
                'count' => 35,
                'image' => 'https://images.unsplash.com/photo-1559314809-0d155014e29e?auto=format&fit=crop&q=80&w=600',
                'category' => 'quan',
                'category_name' => 'Khu vực',
                'link' => BASE_URL . 'search?location=' . urlencode('Quận 5'),
                'description' => 'Cái nôi ẩm thực Trung Hoa, sủi cảo, dimsum và chè truyền thống'
            ],
            [
                'id' => 5,
                'title' => 'Top 10 Quán ăn gia đình ấm cúng',
                'count' => 10,
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=600',
                'category' => 'chu-de',
                'category_name' => 'Chủ đề',
                'link' => BASE_URL . 'danh-muc/nha-hang',
                'description' => 'Thực đơn thuần Việt phong phú, thích hợp sum họp cuối tuần'
            ],
            [
                'id' => 6,
                'title' => 'Top quán lẩu cua đồng thanh mát giải nhiệt',
                'count' => 14,
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&q=80&w=600',
                'category' => 'chu-de',
                'category_name' => 'Chủ đề',
                'link' => BASE_URL . 'danh-muc/lau',
                'description' => 'Gạch cua béo bùi, nước dùng thanh ngọt nấu cùng bắp bò'
            ],
            [
                'id' => 7,
                'title' => 'Đặc sản ẩm thực miền Bắc tại Sài Gòn',
                'count' => 22,
                'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&q=80&w=600',
                'category' => 'mien',
                'category_name' => 'Vùng miền',
                'link' => BASE_URL . 'search?location=' . urlencode('Hà Nội'),
                'description' => 'Hương vị bún chả, phở bò gia truyền, chả cá Lã Vọng thơm nức'
            ],
            [
                'id' => 8,
                'title' => 'Đặc sản ẩm thực miền Trung đậm đà',
                'count' => 19,
                'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=600',
                'category' => 'mien',
                'category_name' => 'Vùng miền',
                'link' => BASE_URL . 'search?location=' . urlencode('Đà Nẵng'),
                'description' => 'Bún bò Huế cay nồng, mì Quảng tôm thịt và bánh bèo chén'
            ],
            [
                'id' => 9,
                'title' => 'Đặc sản Nam Bộ phóng khoáng',
                'count' => 45,
                'image' => 'https://images.unsplash.com/photo-1594922119156-f00e572ac065?auto=format&fit=crop&q=80&w=600',
                'category' => 'mien',
                'category_name' => 'Vùng miền',
                'link' => BASE_URL . 'search?location=' . urlencode('Hồ Chí Minh'),
                'description' => 'Cơm tấm sườn bì chả, cá lóc nướng trui và lẩu mắm đậm vị'
            ]
        ];

        $banners = [
            [
                'title' => 'Đại Phú - Nguyễn Văn Quá',
                'subtitle' => 'Số 56 Nguyễn Văn Quá, Phường Đông Hưng Thuận, Quận 12, TP.HCM',
                'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=1400&h=450',
                'promo' => 'Ưu đãi đặt bàn giữ chỗ miễn phí'
            ],
            [
                'title' => 'Galbi House - Mega Market Hiệp Phú',
                'subtitle' => 'QL1A, Khu phố 4, Phường Hiệp Phú, Quận 12, TP.HCM',
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=1400&h=450',
                'promo' => 'Buffet nướng lẩu thượng hạng giảm tới 20%'
            ],
            [
                'title' => 'Cơm Niêu Làng Tre',
                'subtitle' => 'Số 78 Đường 21, Phường 8, Quận Gò Vấp, TP.HCM',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=1400&h=450',
                'promo' => 'Hương vị đồng quê chuẩn vị 3 miền'
            ]
        ];

        $this->render('index/bo_suu_tap', [
            'title' => 'Bộ sưu tập ẩm thực chọn lọc - Ẩm thực 3 miền',
            'collections' => $collections,
            'banners' => $banners
        ]);
    }

    // ==========================================
    // 8. DANH MỤC ẨM THỰC
    // ==========================================
    public function danhMuc($type) {
        DatabaseSeeder::run();

        $categories = [
            'nha-hang' => 'Nhà Hàng',
            'lau' => 'Lẩu',
            'buffet' => 'Buffet',
            'hai-san' => 'Hải Sản',
            'lau-nuong' => 'Lẩu & Nướng',
            'quan-nhau' => 'Quán Nhậu',
            'mon-chay' => 'Món Chay',
            'dat-tiec' => 'Đặt Tiệc',
            'han-quoc' => 'Món Hàn Quốc',
            'nhat-ban' => 'Món Nhật Bản',
            'mon-au' => 'Món Âu',
            'mon-viet' => 'Món Việt',
            'mon-thai' => 'Món Thái',
            'mon-trung-hoa' => 'Món Trung Hoa',
            'tiec-cuoi' => 'Tiệc Cưới'
        ];

        $title = isset($categories[$type]) ? $categories[$type] : 'Danh Mục Khác';

        $all = $this->monAnModel->getAll();
        $monAn = [];
        $restaurants = [];

        if ($type === 'mon-viet') {
            $monAn = $all;
        } elseif ($type === 'nha-hang' || $type === 'quan-nhau') {
            $items = array_slice($all, 0, 12);
            foreach ($items as $i => $item) {
                $restaurants[] = [
                    'name' => 'Nhà hàng ' . $item['ten_mon'],
                    'img' => $item['hinh_anh'],
                    'rating' => $item['danh_gia'],
                    'address' => 'Số ' . (($i + 1) * 10 + 15) . ' Lê Văn Sỹ, P.12, Q.3',
                    'discount' => 'Giảm ' . rand(10, 30) . '%',
                    'desc' => 'Đặc sản ' . $item['ten_vung'],
                    'price_tier' => '$$$$'
                ];
            }
        } elseif ($type === 'lau') {
            $monAn = array_filter($all, function($item) {
                return mb_stripos($item['ten_mon'], 'lẩu', 0, 'UTF-8') !== false || mb_stripos($item['ten_mon'], 'Lẩu', 0, 'UTF-8') !== false;
            });
            if (empty($monAn)) $monAn = array_slice($all, 0, 3);
        } else {
            $monAn = [];
        }

        $this->render('index/danh_muc', [
            'title' => $title,
            'monAn' => $monAn,
            'restaurants' => $restaurants,
            'type' => $type
        ]);
    }

    // ==========================================
    // 9. TÌM KIẾM
    // ==========================================
    public function search() {
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

        $restaurants = [];
        $monAnList = [];

        if (!empty($keyword) || (!empty($location) && $location !== 'Tất cả' && $location !== 'Tất cả khu vực')) {
            $restaurants = $this->restaurantModel->searchRestaurants($keyword, $location);
            $monAnList = $this->monAnModel->search($keyword, $location);
        }

        $pageTitle = 'Tìm kiếm';
        if (!empty($keyword)) {
            $pageTitle = 'Tìm kiếm: "' . htmlspecialchars($keyword) . '"';
        } elseif (!empty($location) && $location !== 'Tất cả' && $location !== 'Tất cả khu vực') {
            $pageTitle = 'Khám phá ẩm thực ' . htmlspecialchars($location);
        }

        $this->render('index/search', [
            'title' => $pageTitle . ' - Ẩm thực 3 miền',
            'restaurants' => $restaurants,
            'monAnList' => $monAnList,
            'keyword' => $keyword,
            'location' => $location
        ]);
    }

    // ==========================================
    // 10. ĐẶT BÀN (AJAX / API)
    // ==========================================
    public function booking() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Phương thức không hợp lệ.'], 405);
        }

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $booking_date = isset($_POST['booking_date']) ? trim($_POST['booking_date']) : '';
        $guests = isset($_POST['guests']) ? (int)$_POST['guests'] : 0;

        if (!preg_match('/^(0[3|5|7|8|9])+([0-9]{8})$/', $phone)) {
            return $this->json(['success' => false, 'message' => 'Số điện thoại không hợp lệ!']);
        }

        $bookingTime = strtotime($booking_date);
        $currentTime = time();
        if ($bookingTime < $currentTime) {
            return $this->json(['success' => false, 'message' => 'Ngày đặt bàn không được ở trong quá khứ!']);
        }

        if (empty($name) || empty($booking_date) || $guests <= 0) {
            return $this->json(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin hợp lệ!']);
        }

        $result = $this->bookingModel->createBooking($name, $phone, $booking_date, $guests);

        if ($result) {
            return $this->json(['success' => true, 'message' => 'Đặt bàn thành công! Chúng tôi sẽ liên hệ sớm.']);
        } else {
            return $this->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại!']);
        }
    }

    // ==========================================
    // 11. ĐĂNG NHẬP / ĐĂNG KÝ / ĐĂNG XUẤT
    // ==========================================
    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect(($_SESSION['role'] ?? '') === 'admin' ? 'admin' : '');
        }

        $this->render('index/login', [
            'title' => 'Đăng nhập',
            'error' => ''
        ]);
    }

    public function postLogin() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';

            $this->redirect($_SESSION['role'] === 'admin' ? 'admin' : '');
        }

        $this->render('index/login', [
            'title' => 'Đăng nhập',
            'error' => 'Tên đăng nhập hoặc mật khẩu không chính xác'
        ]);
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect(($_SESSION['role'] ?? '') === 'admin' ? 'admin' : '');
        }

        $this->render('index/register', [
            'title' => 'Đăng ký',
            'error' => ''
        ]);
    }

    public function postRegister() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $fullname = trim($_POST['fullname'] ?? '');

        if (empty($username) || empty($password)) {
            $this->render('index/register', [
                'title' => 'Đăng ký',
                'error' => 'Vui lòng nhập đủ thông tin'
            ]);
            return;
        }

        if ($this->userModel->findByUsername($username)) {
            $this->render('index/register', [
                'title' => 'Đăng ký',
                'error' => 'Tên đăng nhập đã tồn tại'
            ]);
            return;
        }

        $newUserId = $this->userModel->create($username, $password, $fullname, 'user');

        $_SESSION['user_id'] = $newUserId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user';

        $this->redirect('');
    }

    public function logout() {
        session_destroy();
        $this->redirect('login');
    }
}
