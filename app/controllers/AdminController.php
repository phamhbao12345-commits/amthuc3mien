<?php
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/MonAnModel.php';
require_once __DIR__ . '/../models/RestaurantModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/TinTucModel.php';

class AdminController extends Controller {
    private $monAnModel;
    private $restaurantModel;
    private $userModel;
    private $tinTucModel;
    private $vungMienModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            $this->redirect('login');
        }

        $this->monAnModel = new MonAnModel();
        $this->restaurantModel = new RestaurantModel();
        $this->userModel = new UserModel();
        $this->tinTucModel = new TinTucModel();
        $this->vungMienModel = new VungMienModel();
    }

    private function renderAdmin($view, $data = []) {
        $data['username'] = $_SESSION['username'] ?? 'Admin';
        $this->render('admin/' . $view, $data, 'admin/layout');
    }

    public function index() {
        $countMonAn = $this->monAnModel->countAll();
        $countUsers = $this->userModel->countAll();
        $countNhaHang = $this->restaurantModel->countAll();
        $countTinTuc = $this->tinTucModel->countAll();
        
        $recentUsers = $this->userModel->getAll(5);

        $this->renderAdmin('dashboard', [
            'title' => 'Dashboard - Admin Panel',
            'countMonAn' => $countMonAn,
            'countUsers' => $countUsers,
            'countNhaHang' => $countNhaHang,
            'countTinTuc' => $countTinTuc,
            'recentUsers' => $recentUsers
        ]);
    }

    // =====================================
    // QUẢN LÝ MÓN ĂN
    // =====================================
    public function monAn() {
        $filter_vung_mien = $_GET['vung_mien'] ?? '';
        
        $monAnList = $this->monAnModel->getAllFiltered($filter_vung_mien);
        $vungMienList = $this->vungMienModel->getAll();
        
        $this->renderAdmin('mon_an_list', [
            'title' => 'Quản lý Món ăn',
            'monAnList' => $monAnList,
            'vungMienList' => $vungMienList,
            'currentFilter' => $filter_vung_mien
        ]);
    }

    public function monAnAdd() {
        $vungMien = $this->vungMienModel->getAll();
        $this->renderAdmin('mon_an_form', [
            'title' => 'Thêm món ăn mới',
            'vungMien' => $vungMien,
            'monAn' => null
        ]);
    }

    public function monAnStore() {
        $ten_mon = trim($_POST['ten_mon'] ?? '');
        $slug = $this->createSlug($ten_mon);
        $mo_ta_ngan = $_POST['mo_ta_ngan'] ?? '';
        $id_vung_mien = $_POST['id_vung_mien'] ?? 1;
        $gia_tien = $_POST['gia_tien'] ?? 0;

        $hinh_anh = $this->handleImageUpload() ?? ($_POST['hinh_anh'] ?? '');

        $this->monAnModel->create([
            'ten_mon' => $ten_mon,
            'slug' => $slug,
            'hinh_anh' => $hinh_anh,
            'mo_ta_ngan' => $mo_ta_ngan,
            'id_vung_mien' => $id_vung_mien,
            'gia_tien' => $gia_tien
        ]);

        $_SESSION['flash_success'] = 'Thêm món ăn thành công!';
        $this->redirect('admin/mon-an');
    }

    public function monAnEdit($id) {
        $monAn = $this->monAnModel->getById($id);

        if (!$monAn) {
            $this->redirect('admin/mon-an');
        }

        $vungMien = $this->vungMienModel->getAll();
        
        $this->renderAdmin('mon_an_form', [
            'title' => 'Chỉnh sửa: ' . $monAn['ten_mon'],
            'vungMien' => $vungMien,
            'monAn' => $monAn
        ]);
    }

    public function monAnUpdate($id) {
        $ten_mon = trim($_POST['ten_mon'] ?? '');
        $mo_ta_ngan = $_POST['mo_ta_ngan'] ?? '';
        $id_vung_mien = $_POST['id_vung_mien'] ?? 1;
        $gia_tien = $_POST['gia_tien'] ?? 0;

        $hinh_anh_new = $this->handleImageUpload();
        $hinh_anh = $hinh_anh_new ? $hinh_anh_new : ($_POST['hinh_anh'] ?? '');

        $this->monAnModel->update($id, [
            'ten_mon' => $ten_mon,
            'hinh_anh' => $hinh_anh,
            'mo_ta_ngan' => $mo_ta_ngan,
            'id_vung_mien' => $id_vung_mien,
            'gia_tien' => $gia_tien
        ]);

        $_SESSION['flash_success'] = 'Cập nhật món ăn thành công!';
        $this->redirect('admin/mon-an');
    }

    public function monAnDelete($id) {
        $this->monAnModel->delete($id);
        
        if ($this->isAjax()) {
            return $this->json(['success' => true]);
        }

        $_SESSION['flash_success'] = 'Xóa món ăn thành công!';
        $this->redirect('admin/mon-an');
    }

    // =====================================
    // QUẢN LÝ NHÀ HÀNG
    // =====================================
    public function nhaHang() {
        $nhaHangList = $this->restaurantModel->getAll();
        
        $this->renderAdmin('nha_hang_list', [
            'title' => 'Quản lý Nhà hàng',
            'nhaHangList' => $nhaHangList
        ]);
    }

    public function nhaHangAdd() {
        $this->renderAdmin('nha_hang_form', [
            'title' => 'Thêm Nhà hàng mới',
            'nhaHang' => null
        ]);
    }

    public function nhaHangStore() {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $rating = $_POST['rating'] ?? 0;
        
        $image = $this->handleImageUpload() ?? ($_POST['image'] ?? '');

        $this->restaurantModel->create([
            'name' => $name,
            'address' => $address,
            'district' => $district,
            'rating' => $rating,
            'image' => $image
        ]);

        $_SESSION['flash_success'] = 'Thêm nhà hàng thành công!';
        $this->redirect('admin/nha-hang');
    }

    public function nhaHangEdit($id) {
        $nhaHang = $this->restaurantModel->getById($id);

        if (!$nhaHang) {
            $this->redirect('admin/nha-hang');
        }

        $this->renderAdmin('nha_hang_form', [
            'title' => 'Chỉnh sửa: ' . $nhaHang['name'],
            'nhaHang' => $nhaHang
        ]);
    }

    public function nhaHangUpdate($id) {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $rating = $_POST['rating'] ?? 0;

        $image_new = $this->handleImageUpload();
        $image = $image_new ? $image_new : ($_POST['image'] ?? '');

        $this->restaurantModel->update($id, [
            'name' => $name,
            'address' => $address,
            'district' => $district,
            'rating' => $rating,
            'image' => $image
        ]);

        $_SESSION['flash_success'] = 'Cập nhật nhà hàng thành công!';
        $this->redirect('admin/nha-hang');
    }

    public function nhaHangDelete($id) {
        $this->restaurantModel->delete($id);

        if ($this->isAjax()) {
            return $this->json(['success' => true]);
        }

        $_SESSION['flash_success'] = 'Xóa nhà hàng thành công!';
        $this->redirect('admin/nha-hang');
    }

    // =====================================
    // QUẢN LÝ NGƯỜI DÙNG
    // =====================================
    public function nguoiDung() {
        $usersList = $this->userModel->getAll();
        
        $this->renderAdmin('nguoi_dung_list', [
            'title' => 'Quản lý Người dùng',
            'usersList' => $usersList
        ]);
    }

    public function nguoiDungAdd() {
        $this->renderAdmin('nguoi_dung_form', [
            'title' => 'Thêm Người dùng mới',
            'user' => null
        ]);
    }

    public function nguoiDungStore() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $fullname = trim($_POST['fullname'] ?? '');
        $role = $_POST['role'] ?? 'user';

        // Check exists
        if ($this->userModel->findByUsername($username)) {
            $_SESSION['flash_error'] = 'Tên đăng nhập đã tồn tại!';
            $this->redirect('admin/nguoi-dung/add');
        }

        if (!empty($username) && !empty($password)) {
            $this->userModel->create($username, $password, $fullname, $role);
            $_SESSION['flash_success'] = 'Thêm người dùng thành công!';
            $this->redirect('admin/nguoi-dung');
        } else {
            $_SESSION['flash_error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
            $this->redirect('admin/nguoi-dung/add');
        }
    }

    public function nguoiDungEdit($id) {
        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->redirect('admin/nguoi-dung');
        }

        $this->renderAdmin('nguoi_dung_form', [
            'title' => 'Chỉnh sửa Người dùng: ' . $user['username'],
            'user' => $user
        ]);
    }

    public function nguoiDungUpdate($id) {
        $fullname = trim($_POST['fullname'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';

        $this->userModel->update($id, $fullname, $role, !empty($password) ? $password : null);

        $_SESSION['flash_success'] = 'Cập nhật người dùng thành công!';
        $this->redirect('admin/nguoi-dung');
    }

    public function nguoiDungDelete($id) {
        if ($_SESSION['user_id'] != $id) { 
            $this->userModel->delete($id);

            if ($this->isAjax()) {
                return $this->json(['success' => true]);
            }

            $_SESSION['flash_success'] = 'Xóa người dùng thành công!';
        } else {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Bạn không thể tự xóa tài khoản của chính mình!']);
            }
            $_SESSION['flash_error'] = 'Bạn không thể tự xóa tài khoản của chính mình!';
        }
        $this->redirect('admin/nguoi-dung');
    }

    // =====================================
    // QUẢN LÝ TIN TỨC & BLOG
    // =====================================
    public function tinTuc() {
        $danh_muc = $_GET['danh_muc'] ?? '';
        $keyword = trim($_GET['keyword'] ?? '');

        $tinTucList = $this->tinTucModel->getAll($danh_muc, $keyword);
        $danhMucList = $this->tinTucModel->getCategories();

        $this->renderAdmin('tin_tuc_list', [
            'title' => 'Quản lý Tin tức & Blog',
            'tinTucList' => $tinTucList,
            'danhMucList' => $danhMucList,
            'currentDanhMuc' => $danh_muc,
            'keyword' => $keyword
        ]);
    }

    public function tinTucAdd() {
        $this->renderAdmin('tin_tuc_form', [
            'title' => 'Thêm bài viết mới',
            'tinTuc' => null,
            'danhMucList' => $this->tinTucModel->getCategories()
        ]);
    }

    public function tinTucStore() {
        $tieu_de = trim($_POST['tieu_de'] ?? '');
        $slug = !empty($_POST['slug']) ? $this->createSlug($_POST['slug']) : $this->createSlug($tieu_de);
        $danh_muc = $_POST['danh_muc'] ?? 'Khám phá';
        $tac_gia = trim($_POST['tac_gia'] ?? 'Ban biên tập');
        $tom_tat = trim($_POST['tom_tat'] ?? '');
        $noi_dung = $_POST['noi_dung'] ?? '';
        $noi_bat = isset($_POST['noi_bat']) ? 1 : 0;

        $hinh_anh = $this->handleImageUpload() ?? ($_POST['hinh_anh'] ?? '');

        if (!empty($tieu_de)) {
            $this->tinTucModel->create([
                'tieu_de' => $tieu_de,
                'slug' => $slug,
                'danh_muc' => $danh_muc,
                'tac_gia' => $tac_gia,
                'tom_tat' => $tom_tat,
                'noi_dung' => $noi_dung,
                'hinh_anh' => $hinh_anh,
                'noi_bat' => $noi_bat,
                'trang_thai' => 1
            ]);
            $_SESSION['flash_success'] = 'Thêm bài viết mới thành công!';
        } else {
            $_SESSION['flash_error'] = 'Vui lòng nhập tiêu đề bài viết!';
            $this->redirect('admin/tin-tuc/add');
        }

        $this->redirect('admin/tin-tuc');
    }

    public function tinTucEdit($id) {
        $tinTuc = $this->tinTucModel->getById($id);
        if (!$tinTuc) {
            $this->redirect('admin/tin-tuc');
        }

        $this->renderAdmin('tin_tuc_form', [
            'title' => 'Chỉnh sửa: ' . $tinTuc['tieu_de'],
            'tinTuc' => $tinTuc,
            'danhMucList' => $this->tinTucModel->getCategories()
        ]);
    }

    public function tinTucUpdate($id) {
        $tieu_de = trim($_POST['tieu_de'] ?? '');
        $danh_muc = $_POST['danh_muc'] ?? 'Khám phá';
        $tac_gia = trim($_POST['tac_gia'] ?? 'Ban biên tập');
        $tom_tat = trim($_POST['tom_tat'] ?? '');
        $noi_dung = $_POST['noi_dung'] ?? '';
        $noi_bat = isset($_POST['noi_bat']) ? 1 : 0;

        $hinh_anh_new = $this->handleImageUpload();
        $hinh_anh = $hinh_anh_new ? $hinh_anh_new : ($_POST['hinh_anh'] ?? '');

        $data = [
            'tieu_de' => $tieu_de,
            'danh_muc' => $danh_muc,
            'tac_gia' => $tac_gia,
            'tom_tat' => $tom_tat,
            'noi_dung' => $noi_dung,
            'hinh_anh' => $hinh_anh,
            'noi_bat' => $noi_bat,
            'trang_thai' => 1
        ];

        if (!empty($_POST['slug'])) {
            $data['slug'] = $this->createSlug($_POST['slug']);
        }

        $this->tinTucModel->update($id, $data);
        $_SESSION['flash_success'] = 'Cập nhật bài viết thành công!';
        $this->redirect('admin/tin-tuc');
    }

    public function tinTucDelete($id) {
        $this->tinTucModel->delete($id);

        if ($this->isAjax()) {
            return $this->json(['success' => true]);
        }

        $_SESSION['flash_success'] = 'Xóa bài viết thành công!';
        $this->redirect('admin/tin-tuc');
    }

    // =====================================
    // TIỆN ÍCH HỖ TRỢ
    // =====================================
    private function createSlug($string) {
        $search = array(
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
            '#(ì|í|ị|ỉ|ĩ)#',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#',
            '#(đ)#',
            '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
            '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
            '#(Ì|Í|Ị|Ỉ|Ĩ)#',
            '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
            '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
            '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
            '#(Đ)#',
            '/[^a-zA-Z0-9\-\_]/',
        );
        $replace = array('a','e','i','o','u','y','d','A','E','I','O','U','Y','D','-');
        $string = preg_replace($search, $replace, $string);
        $string = preg_replace('/(-)+/', '-', $string);
        $string = strtolower($string);
        return $string . '-' . time();
    }

    private function handleImageUpload() {
        if (isset($_FILES['hinh_anh_file']) && $_FILES['hinh_anh_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../asset/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['hinh_anh_file']['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . rand(1000, 9999) . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['hinh_anh_file']['tmp_name'], $targetPath)) {
                return BASE_URL . 'asset/uploads/' . $fileName;
            }
        }
        return null;
    }
}
