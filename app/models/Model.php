<?php
require_once __DIR__ . '/Database.php';

/**
 * Lớp Model cơ sở cho toàn bộ ứng dụng
 */
class Model {
    protected $conn;
    protected $table = '';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function count($table = null, $where = '', $params = []) {
        $tbl = $table ?: $this->table;
        $sql = "SELECT COUNT(*) FROM " . $tbl;
        if (!empty($where)) {
            $sql .= " WHERE " . $where;
        }
        return (int)$this->query($sql, $params)->fetchColumn();
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}

/**
 * Lớp Controller cơ sở xử lý render view, layout, json response và chuyển hướng
 */
class Controller {
    public function render($view, $data = [], $layout = 'index/layout') {
        extract($data);

        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "<h1>Error</h1><p>View file not found: " . htmlspecialchars($view) . "</p>";
            exit;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutFile = __DIR__ . '/../views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
                return;
            }
        }

        echo $content;
    }

    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function redirect($path) {
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            header('Location: ' . $path);
        } else {
            $path = ltrim($path, '/');
            header('Location: ' . BASE_URL . $path);
        }
        exit;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

/**
 * Lớp Router điều hướng URL ứng dụng
 */
class Router {
    private static $routes = [];

    public static function get($route, $action) {
        self::$routes['GET'][$route] = $action;
    }

    public static function post($route, $action) {
        self::$routes['POST'][$route] = $action;
    }

    public static function dispatch($url) {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = '/' . trim($url, '/');

        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $route => $action) {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $route);
                $pattern = "@^" . $pattern . "$@D";

                if (preg_match($pattern, $url, $matches)) {
                    array_shift($matches);

                    if (is_array($action)) {
                        $controllerName = $action[0];
                        $methodName = $action[1];

                        if (class_exists($controllerName)) {
                            $controller = new $controllerName();
                            if (method_exists($controller, $methodName)) {
                                call_user_func_array([$controller, $methodName], $matches);
                                return;
                            }
                        }
                    }
                }
            }
        }
        
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1><p>Trang bạn yêu cầu không tồn tại.</p>";
    }
}

/**
 * Lớp Seeder tự động bổ sung dữ liệu mẫu khi CSDL chưa có
 */
class DatabaseSeeder {
    private static $seeded = false;

    public static function run() {
        if (self::$seeded) {
            return;
        }
        self::$seeded = true;

        try {
            $database = new Database();
            $conn = $database->getConnection();

            // Sửa ảnh lỗi & Đồng bộ chuẩn tỉnh thành
            $conn->query("UPDATE mon_an SET hinh_anh = 'https://images.unsplash.com/photo-1594922119156-f00e572ac065?auto=format&fit=crop&q=80&w=1000' WHERE slug = 'bun-bo-hue' AND hinh_anh LIKE '%1627042633145%'");
            $conn->query("UPDATE mon_an SET hinh_anh = 'https://images.unsplash.com/photo-1626804475297-41609ea004eb?auto=format&fit=crop&q=80&w=1000' WHERE slug = 'com-tam-sai-gon' AND hinh_anh LIKE '%1615486171448%'");
            
            // Đồng bộ tỉnh thành chính xác cho toàn bộ món ăn
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Đồng Tháp', id_vung_mien = 3 WHERE slug IN ('lau-mam', 'hu-tieu-sa-dec', 'lau-ca-linh-bong-dien-dien', 'nem-lai-vung-dong-thap', 'ca-loc-nuong-la-sen-dong-thap', 'com-sen-dong-thap', 'banh-phong-tom-sa-giang')");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Hà Nội', id_vung_mien = 1 WHERE slug IN ('pho-ha-noi', 'bun-cha-ha-noi', 'lau-bo-nhung-dam', 'lau-cua-dong-mong-toi', 'cha-ca-la-vong-ha-noi', 'bun-thang-ha-noi', 'lau-ech-mang-cay') OR ten_mon LIKE '%Hà Nội%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Hải Phòng', id_vung_mien = 1 WHERE slug IN ('pate-cot-den-hai-phong', 'banh-da-cua-hai-phong', 'nem-cua-be-hai-phong', 'banh-mi-cay-hai-phong') OR ten_mon LIKE '%Hải Phòng%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Quảng Ninh', id_vung_mien = 1 WHERE slug IN ('cha-muc-ha-long', 'bun-be-be-ha-long', 'ga-doi-tien-yen', 'sam-bien-quang-ninh') OR ten_mon LIKE '%Hạ Long%' OR ten_mon LIKE '%Quảng Ninh%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Ninh Bình', id_vung_mien = 1 WHERE slug IN ('lau-de-ninh-binh', 'oc-nui-ninh-binh', 'com-chay-ninh-binh', 'thit-de-tai-chanh-ninh-binh') OR ten_mon LIKE '%Ninh Bình%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Lào Cai', id_vung_mien = 1 WHERE slug IN ('lau-ca-tam-sapa', 'thang-co-bac-ha', 'thit-lon-cap-nach-sapa', 'ca-hoi-sapa-nuong') OR ten_mon LIKE '%Sapa%' OR ten_mon LIKE '%Bắc Hà%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Nam Định', id_vung_mien = 1 WHERE slug IN ('nem-nam-giao-thuy', 'pho-bo-nam-dinh', 'keo-siu-chau-nam-dinh') OR ten_mon LIKE '%Nam Định%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Thừa Thiên Huế', id_vung_mien = 2 WHERE slug IN ('bun-bo-hue', 'com-hen-hue', 'banh-bot-loc-hue', 'nem-lui-hue', 'che-heo-quay-hue', 'banh-beo-chen-hue') OR ten_mon LIKE '%Huế%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Đà Nẵng', id_vung_mien = 2 WHERE slug IN ('cha-bo-da-nang', 'banh-trang-cuon-thit-heo-da-nang', 'bun-cha-ca-da-nang', 'goi-ca-nam-o-da-nang') OR ten_mon LIKE '%Đà Nẵng%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Quảng Nam', id_vung_mien = 2 WHERE slug IN ('mi-quang', 'cao-lau-hoi-an', 'banh-dap-hen-xao', 'be-thui-cau-mong') OR ten_mon LIKE '%Hội An%' OR ten_mon LIKE '%Quảng Nam%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Bình Định', id_vung_mien = 2 WHERE slug IN ('banh-hoi-long-heo', 'tre-binh-dinh', 'nem-cho-huyen-binh-dinh', 'bun-cha-ca-quy-nhon') OR ten_mon LIKE '%Bình Định%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Phú Yên', id_vung_mien = 2 WHERE slug IN ('banh-canh-he-phu-yen', 'lau-ga-la-e', 'mat-ca-ngu-dai-duong-phu-yen', 'goi-ca-mai-phu-yen') OR ten_mon LIKE '%Phú Yên%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Khánh Hòa', id_vung_mien = 2 WHERE slug IN ('bun-ca-dam-nha-trang', 'bun-sua-nha-trang', 'nem-nuong-ninh-hoa', 'banh-can-nha-trang') OR ten_mon LIKE '%Nha Trang%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Lâm Đồng', id_vung_mien = 2 WHERE slug IN ('lau-nam-thao-moc', 'banh-trang-nuong-da-lat', 'lau-bo-ba-toa-da-lat', 'kem-bo-da-lat') OR ten_mon LIKE '%Đà Lạt%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'TP. Cần Thơ', id_vung_mien = 3 WHERE slug IN ('banh-xeo-can-tho', 'ca-loc-nuong-trui-can-tho', 'banh-duc-la-dua-can-tho', 'banh-cong-can-tho', 'chuot-dong-nuong-lu', 'vit-nau-chao-can-tho') OR ten_mon LIKE '%Cần Thơ%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'An Giang', id_vung_mien = 3 WHERE slug IN ('bun-ca-chau-doc', 'banh-canh-tep-chau-doc', 'ga-dot-tri-ton', 'du-du-dam-tri-ton', 'lau-mam-chau-doc', 'bo-bay-mon-nui-sam') OR ten_mon LIKE '%Châu Đốc%' OR ten_mon LIKE '%Tri Tôn%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'TP. Hồ Chí Minh', id_vung_mien = 3 WHERE slug IN ('com-tam', 'com-tam-sai-gon', 'banh-trang-tron-sai-gon', 'sui-cao-tai-sai-gon', 'pha-lau-sai-gon', 'cac-mon-oc', 'banh-bong-lan-trung-muoi-sai-gon', 'banh-mi-sai-gon', 'pha-lau-bo', 'bot-chien', 'sui-cao-nguoi-hoa', 'goi-cuon') OR ten_mon LIKE '%Sài Gòn%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Đồng Nai', id_vung_mien = 3 WHERE slug IN ('goi-buoi-tan-trieu-dong-nai', 'ga-hap-buoi-dong-nai', 'canh-chua-la-giang-long-bo', 'lau-kho-qua-rung-dong-nai') OR ten_mon LIKE '%Đồng Nai%' OR ten_mon LIKE '%Tân Triều%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Tây Ninh', id_vung_mien = 3 WHERE slug IN ('bo-to-tay-ninh', 'banh-canh-trang-bang-tay-ninh', 'than-lan-nui-ba-den-tay-ninh', 'theo-leo-tay-ninh', 'nem-buoi-tay-ninh') OR ten_mon LIKE '%Tây Ninh%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Vĩnh Long', id_vung_mien = 3 WHERE slug IN ('khoai-lang-mam-song-vinh-long', 'ca-tai-tuong-chien-xu-vinh-long', 'thanh-tra-vinh-long') OR ten_mon LIKE '%Vĩnh Long%'");
            $conn->query("UPDATE mon_an SET tinh_thanh = 'Cà Mau', id_vung_mien = 3 WHERE slug IN ('banh-tam-ca-mau', 'cua-da-rang-muoi-ca-mau', 'lau-ca-thoi-loi-ca-mau', 'vop-nuong-mo-hanh-ca-mau') OR ten_mon LIKE '%Cà Mau%'");

            // Bổ sung thêm món nếu chưa có để đủ gợi ý
            $extraDongThap = [
                ['Cơm sen Đồng Tháp', 'com-sen-dong-thap', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image28.jpg', 'Cơm gói lá sen thơm lừng hương sen Đồng Tháp Mười, hạt sen bùi béo kết hợp nấm đông cô thanh ngọt.', 'Gạo thơm, hạt sen tươi, nấm đông cô, lá sen', 'Hấp chín cơm cùng hạt sen và gia vị trong lá sen tươi giữ trọn hương thơm thanh mát.', 3, 'Đồng Tháp', 85000, 45, 4.8],
                ['Bánh phồng tôm Sa Giang', 'banh-phong-tom-sa-giang', 'https://images.unsplash.com/photo-1541592102781-c715053e1be0?auto=format&fit=crop&q=80&w=800', 'Đặc sản trứ danh Sa Đéc (Đồng Tháp) giòn rụm, đậm đà vị ngọt tự nhiên của tôm đất nước ngọt.', 'Tôm tươi, bột năng, tiêu đen', 'Quết tôm mịn, hấp chín thái mỏng phơi nắng rồi chiên giòn tan trong dầu nóng.', 3, 'Đồng Tháp', 45000, 20, 4.7],
                ['Gà Đèo Le Quế Sơn', 'ga-deo-le-que-son', 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&q=80&w=800', 'Gà tre thả đồi xứ Quảng thịt chắc ngọt lịm, luộc hoặc nướng than hoa chấm muối tiêu chanh ớt sim cay xé lưỡi.', 'Gà tre Quế Sơn, ớt sim rừng, lá chanh, tiêu sọ, muối hầm', 'Luộc gà chín tới cùng gừng hành giữ da vàng óng giòn sần sật, xé phay chấm muối ớt sim rừng.', 2, 'Quảng Nam', 180000, 40, 4.9],
                ['Chả chìa Hạ Lũng Hải Phòng', 'cha-chia-ha-lung-hai-phong', 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=800', 'Món chả nướng độc đáo làng nghề Hạ Lũng cuốn quanh thanh mía ngọt lịm, nướng than hoa thơm nức mũi.', 'Mực một nắng, thịt nạc mông, mỡ phần, mía lau, hạt tiêu', 'Quết nhuyễn thịt và mực một nắng, bọc quanh dọng mía lau rồi đem hấp sơ và nướng vàng than hoa.', 1, 'Hải Phòng', 90000, 45, 4.8]
            ];
            $stmtDt = $conn->prepare("INSERT INTO mon_an (ten_mon, slug, hinh_anh, mo_ta_ngan, nguyen_lieu, cach_lam, id_vung_mien, tinh_thanh, gia_tien, thoi_gian_nau, danh_gia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $chkDt = $conn->prepare("SELECT id FROM mon_an WHERE slug = ?");
            foreach ($extraDongThap as $dt) {
                $chkDt->execute([$dt[1]]);
                if (!$chkDt->fetch()) {
                    $stmtDt->execute($dt);
                }
            }

            // Bổ sung món ăn
            $countStmt = $conn->query("SELECT COUNT(*) FROM mon_an");
            $count = $countStmt ? (int)$countStmt->fetchColumn() : 0;
            if ($count < 35) {
                self::seedDishes($conn);
            }

            // Bổ sung món lẩu
            $lauCheck = $conn->query("SELECT COUNT(*) as count FROM mon_an WHERE ten_mon LIKE '%Lẩu%'");
            $lauCount = $lauCheck ? (int)$lauCheck->fetchColumn() : 0;
            if ($lauCount < 10) {
                self::seedHotpots($conn);
            }
        } catch (Exception $e) {
            error_log("DatabaseSeeder error: " . $e->getMessage());
        }
    }

    private static function seedDishes($conn) {
        $newDishes = [
            ['Bún Chả Hà Nội', 'bun-cha-ha-noi', 'https://images.unsplash.com/photo-1661601633519-2423bb0b40eb?auto=format&fit=crop&q=80&w=1000', 'Thịt nướng thơm lừng ăn kèm bún và nước mắm chua ngọt đặc trưng của thủ đô.', 'Thịt heo, Bún, Nước mắm, Đu đủ xanh', 'Nướng thịt bằng than hoa, pha nước mắm chua ngọt mặn vừa vặn.', 1, 55000, 45, 4.8],
            ['Mì Quảng', 'mi-quang', 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=1000', 'Đặc sản miền Trung với sợi mì nghệ vàng ươm, nước lèo đậm đà tôm thịt.', 'Mì Quảng, Tôm, Thịt ba chỉ, Trứng cút, Bánh tráng', 'Nấu nước nhân tôm thịt đậm đà, chan xâm xấp lên sợi mì và rau sống.', 2, 45000, 60, 4.7],
            ['Lẩu Mắm Miền Tây', 'lau-mam-mien-tay', 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=1000', 'Hương vị đồng quê mộc mạc với đủ loại rau sông và nước dùng mắm cá linh đậm vị.', 'Mắm cá sặc, Hải sản, Thịt heo quay, Bông điên điển', 'Nấu rã mắm cá, lược lấy nước trong, nấu cùng thịt và hải sản, ăn kèm rau sống.', 3, 250000, 120, 4.9],
            ['Pate Cột Đèn - Hải Phòng', 'pate-cot-den-hai-phong', 'https://images.unsplash.com/photo-1608855238293-a8e53e028b8e?auto=format&fit=crop&q=80&w=1000', 'Pate thơm nức, ngậy béo ăn kèm bánh mì que giòn rụm - đặc sản đất Cảng.', 'Gan heo, thịt mỡ, hành tím, gia vị', 'Hấp cách thủy gan và thịt mỡ xay nhuyễn trong nhiều giờ.', 1, 40000, 180, 4.9],
            ['Bánh ngõa Lũng Ngoại', 'banh-ngoa-lung-ngoai', 'https://images.unsplash.com/photo-1541592102781-c715053e1be0?auto=format&fit=crop&q=80&w=1000', 'Món bánh dân dã truyền thống của Vĩnh Phúc với hương vị bùi béo đặc trưng.', 'Gạo nếp, đậu xanh, mật mía', 'Đồ nếp chín, giã nhuyễn cùng đậu xanh và mật mía.', 1, 30000, 120, 4.7],
            ['Bánh phu thê - Bắc Ninh', 'banh-phu-the-bac-ninh', 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=1000', 'Bánh phu thê Đình Bảng dẻo thơm, nhân đậu xanh dừa nạo ngọt ngào.', 'Bột lọc, đậu xanh, dừa nạo, hạt sen', 'Gói bột lọc nhân đậu xanh dừa trong lá dong, đem luộc chín.', 1, 50000, 60, 4.8],
            ['Cá kho Làng Vũ Đại', 'ca-kho-lang-vu-dai', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&q=80&w=1000', 'Niêu cá kho Hà Nam thịt chắc xương nhừ, kho củi nhãn suốt 12 tiếng.', 'Cá trắm đen, riềng, chanh, tương cua', 'Kho cá trong niêu đất với củi nhãn trong 12 tiếng liên tục.', 1, 500000, 720, 5.0],
            ['Bánh đậu xanh Hải Dương', 'banh-dau-xanh-hai-duong', 'https://images.unsplash.com/photo-1582294154941-8f5eb23b0906?auto=format&fit=crop&q=80&w=1000', 'Khối bánh vuông vức, vàng ươm, tan ngay trong miệng, nhâm nhi cùng trà mạn.', 'Đậu xanh, đường, mỡ lợn', 'Đậu xanh rang chín giã mịn, trộn đường và mỡ rồi nén chặt.', 1, 45000, 90, 4.8],
            ['Gà Đông Tảo', 'ga-dong-tao-hung-yen', 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&q=80&w=1000', 'Giống gà tiến vua Hưng Yên với cặp chân to đặc trưng, thịt giòn dai sần sật.', 'Gà Đông Tảo, gia vị thảo mộc', 'Hấp lá chanh hoặc hầm thuốc bắc để giữ nguyên vị ngọt của thịt gà.', 1, 800000, 120, 4.9],
            ['Nem nắm Giao Thủy', 'nem-nam-giao-thuy', 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?auto=format&fit=crop&q=80&w=1000', 'Đặc sản Nam Định cuốn lá sung, chấm nước mắm chua ngọt cực kỳ tốn bia.', 'Thịt heo, bì heo, thính gạo', 'Thịt luộc chín tái, thái mỏng trộn cùng bì thái chỉ và thính gạo.', 1, 60000, 45, 4.8],
            ['Ốc núi Ninh Bình', 'oc-nui-ninh-binh', 'https://images.unsplash.com/photo-1559132104-583eb531d0db?auto=format&fit=crop&q=80&w=1000', 'Ốc sinh sống trên núi đá vôi, thịt giòn ngọt, thơm mùi thảo dược tự nhiên.', 'Ốc núi, sả, ớt, gừng', 'Hấp sả hoặc xào me, ăn kèm nước mắm gừng ớt.', 1, 120000, 30, 4.7],
            ['Gỏi nhệch Thái Bình', 'goi-nhech-thai-binh', 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&q=80&w=1000', 'Món gỏi trứ danh ăn kèm với đủ loại lá chát, chấm chẻo mẻ đậm đà.', 'Cá nhệch, thính, chẻo mẻ, lá sung', 'Thái phi lê cá nhệch, trộn thính, ăn kèm các loại lá và chẻo.', 1, 150000, 60, 4.8],
            ['Thắng cố Bắc Hà', 'thang-co-bac-ha', 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=1000', 'Đặc sản vùng cao Lào Cai mang hương vị nồng nàn của núi rừng Tây Bắc.', 'Thịt ngựa, nội tạng ngựa, thảo quả, quế', 'Ninh nhừ thịt và nội tạng cùng các gia vị đặc trưng của Tây Bắc.', 1, 100000, 180, 4.6],
            ['Mèn Mén Hà Giang', 'men-men-ha-giang', 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&q=80&w=1000', 'Món ăn từ ngô tẻ của người Mông, thường ăn kèm với thắng cố hoặc canh.', 'Ngô tẻ', 'Xay ngô thành bột mịn, rây lấy phần ngon rồi đồ chín 2 lần.', 1, 20000, 90, 4.5],
            ['Cao Lầu Hội An', 'cao-lau-hoi-an', 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=1000', 'Sợi mì vàng dai giòn đặc trưng ăn cùng thịt xíu, tóp mỡ và rau thơm Trà Quế.', 'Sợi cao lầu, thịt xíu, tóp mỡ, húng lủi', 'Nấu nước tương xíu thịt đậm đà, chan xâm xấp lên sợi mì và ăn kèm rau sống.', 2, 40000, 60, 4.8],
            ['Cơm Hến Huế', 'com-hen-hue', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&q=80&w=1000', 'Món ăn dân dã cay nồng với hến xào, mắm ruốc, khế chua và tóp mỡ.', 'Cơm nguội, hến, mắm ruốc, tóp mỡ, khế chua', 'Xào hến với hành răm, chan nước hến luộc nóng hổi ăn cùng cơm nguội và rau thơm.', 2, 25000, 30, 4.9],
            ['Bánh Bột Lọc Huế', 'banh-bot-loc-hue', 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&q=80&w=1000', 'Bánh trong vắt dẻo dai, lộ nhân tôm thịt rim mặn ngọt hấp dẫn.', 'Bột lọc, tôm đất, thịt ba chỉ, lá dong', 'Rim tôm thịt đậm đà, bọc bằng bột lọc, gói lá dong rồi đem hấp cách thủy.', 2, 35000, 45, 4.7],
            ['Nem Lụi Huế', 'nem-lui-hue', 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?auto=format&fit=crop&q=80&w=1000', 'Thịt quết nướng lụi trên cành sả, cuộn rau sống chấm nước lèo béo ngậy.', 'Thịt heo quết nhuyễn, sả, đậu phộng, gan heo', 'Quết thịt dẻo quánh, nướng than hoa. Làm nước chấm lèo từ gan heo và đậu phộng.', 2, 50000, 60, 4.8],
            ['Bánh Đập Hến Xào', 'banh-dap-hen-xao', 'https://images.unsplash.com/photo-1559132104-583eb531d0db?auto=format&fit=crop&q=80&w=1000', 'Sự kết hợp hoàn hảo giữa bánh tráng nướng giòn và bánh ướt dẻo cùng hến xào đậm đà.', 'Bánh tráng nướng, bánh ướt, hến, hành phi', 'Xào hến với sả ớt. Lấy bánh tráng ướt dán lên bánh nướng, đập vỡ rồi xúc hến.', 2, 30000, 30, 4.6],
            ['Chả Bò Đà Nẵng', 'cha-bo-da-nang', 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=1000', 'Chả bò giòn dai, thơm nức mùi tiêu sọ và nước mắm nguyên chất.', 'Thịt bò đùi, tiêu sọ, tỏi, lá chuối', 'Quết thịt bò nhuyễn mịn, ướp tiêu sọ, tỏi, gói chặt tay bằng lá chuối rồi luộc chín.', 2, 250000, 90, 4.9],
            ['Bánh Hỏi Lòng Heo', 'banh-hoi-long-heo', 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=1000', 'Đặc sản Bình Định với đĩa bánh hỏi thoa mỡ hành ăn cùng lòng heo luộc ngọt lịm.', 'Bánh hỏi, lòng heo, hẹ, mắm nhĩ', 'Luộc lòng heo chín tới cho giòn ngọt. Bánh hỏi rưới mỡ hẹ, chấm mắm nhỉ tỏi ớt.', 2, 45000, 45, 4.7],
            ['Tré Bình Định', 'tre-binh-dinh', 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=1000', 'Món nem chả dạng hình cán chổi, lên men tự nhiên, giòn sần sật cực tốn mồi.', 'Thịt đầu heo, tai heo, riềng, thính, lá ổi', 'Thái nhỏ tai heo, trộn riềng thính, bọc lá ổi rồi bó bằng rơm để lên men.', 2, 40000, 15, 4.8],
            ['Bánh Canh Hẹ', 'banh-canh-he-phu-yen', 'https://images.unsplash.com/photo-1582294154941-8f5eb23b0906?auto=format&fit=crop&q=80&w=1000', 'Món ăn bình dị của Phú Yên với màu xanh rợp mắt của hẹ và chả cá dai ngon.', 'Sợi bánh canh gạo, chả cá, hẹ xắt nhỏ, trứng cút', 'Nấu nước lèo cá ngọt thanh, bỏ sợi bánh canh, chả cá và rắc thật nhiều hẹ.', 2, 25000, 40, 4.5],
            ['Bún Cá Dầm Nha Trang', 'bun-ca-dam-nha-trang', 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&q=80&w=1000', 'Nước lèo trong vắt, ngọt thanh nấu từ cá bò, cá ngừ dầm nát không hề tanh.', 'Bún, cá bò, chả cá, sứa, cà chua', 'Luộc cá lấy nước, dầm nát thịt cá hòa vào nước lèo. Ăn cùng bún, chả cá và sứa.', 2, 40000, 60, 4.7],
            ['Bánh Xèo Miền Tây', 'banh-xeo-mien-tay', 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=1000', 'Chiếc bánh khổng lồ, vỏ mỏng giòn tan, nhân tôm thịt giá đỗ, cuốn cùng rau rừng.', 'Bột gạo, cốt dừa, tôm, thịt ba rọi, giá', 'Pha bột gạo với cốt dừa và bột nghệ. Đổ bánh trên chảo lớn nóng già.', 3, 40000, 30, 4.8],
            ['Hủ Tiếu Nam Vang', 'hu-tieu-nam-vang', 'https://images.unsplash.com/photo-1594922119156-f00e572ac065?auto=format&fit=crop&q=80&w=1000', 'Sợi hủ tiếu dai ngon, nước dùng ngọt thanh từ xương ống và khô mực, topping đa dạng.', 'Hủ tiếu, tôm, thịt băm, gan heo, trứng cút', 'Trụng sợi hủ tiếu dai. Nấu nước lèo xương ống khô mực. Bày topping phong phú.', 3, 50000, 60, 4.7],
            ['Cá Lóc Nướng Trui', 'ca-loc-nuong-trui', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&q=80&w=1000', 'Món nhậu miệt vườn dân dã, cá lóc ngọt thịt cuốn bánh tráng chấm mắm nêm.', 'Cá lóc đồng, rơm rạ, lá sen non', 'Dùng que tre xiên cá lóc cắm xuống đất, phủ rơm kín rồi đốt nướng trui.', 3, 120000, 45, 4.9],
            ['Gỏi Cuốn Tôm Thịt', 'goi-cuon-tom-thit', 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=1000', 'Gỏi cuốn thanh mát, tôm luộc đỏ au cuốn trong bánh tráng, chấm tương đen béo ngậy.', 'Bánh tráng, tôm, thịt ba chỉ, bún tươi, hẹ', 'Luộc tôm thịt thái mỏng. Cuốn chặt tay cùng bún và rau sống. Pha tương chấm.', 3, 10000, 20, 4.6],
            ['Bún Mắm Miền Tây', 'bun-mam-mien-tay', 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=1000', 'Phiên bản bún của lẩu mắm, nước dùng đậm đà, topping ngập tràn hải sản và heo quay.', 'Mắm sặc, tôm, mực, heo quay, cà tím', 'Nấu nước lèo mắm linh và mắm sặc. Thêm sả băm, cà tím. Ăn kèm bông điên điển.', 3, 55000, 90, 4.8],
            ['Canh Chua Cá Lóc', 'canh-chua-ca-loc', 'https://images.unsplash.com/photo-1582294154941-8f5eb23b0906?auto=format&fit=crop&q=80&w=1000', 'Bát canh thanh mát, chua chua ngọt ngọt giải nhiệt hoàn hảo cho những ngày nắng gắt.', 'Cá lóc, me, thơm, bạc hà, giá, đậu bắp', 'Nấu nước dùng chua từ me và thơm. Thêm cá lóc và các loại rau. Rắc ngò om.', 3, 60000, 30, 4.8],
            ['Bò Tơ Tây Ninh', 'bo-to-tay-ninh', 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=1000', 'Thịt bò tơ non mềm, nướng hoặc hấp cuốn bánh tráng phơi sương chấm mắm nêm.', 'Bò tơ non, bánh tráng phơi sương, rau rừng', 'Thui bì bò cho vàng. Thái lát mỏng rồi đem nướng hoặc hấp gừng.', 3, 200000, 45, 4.9],
            ['Gà Nướng Đất Sét', 'ga-nuong-dat-set', 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&q=80&w=1000', 'Món ăn đậm chất miền Tây, gà bọc đất sét nướng củi giúp thịt giữ trọn độ ngọt.', 'Gà ta, đất sét bùn nhão, lá chuối', 'Bọc gà trong lá chuối và đất sét kín, nướng trong đống lửa rơm rạ.', 3, 300000, 120, 4.7],
            ['Bánh Khọt Vũng Tàu', 'banh-khot-vung-tau', 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=1000', 'Bánh nhỏ bằng đồng xu, vỏ giòn rụm màu nghệ, trên mặt đính con tôm to đùng.', 'Bột gạo, bột nghệ, tôm tươi, tôm cháy', 'Đổ bột vào khuôn gang nóng mỡ, cho tôm vào giữa, chiên giòn rụm rắc tôm cháy.', 3, 45000, 40, 4.8],
            ['Bún Nước Lèo Sóc Trăng', 'bun-nuoc-leo-soc-trang', 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=1000', 'Sự giao thoa ẩm thực Kinh - Hoa - Khmer, nước lèo ngải bún thơm nức mũi.', 'Mắm bò hóc, cá lóc, tôm, heo quay, ngải bún', 'Nấu mắm bò hóc với ngải bún và sả đập dập. Thêm thịt cá, tôm luộc và heo quay.', 3, 50000, 90, 4.7]
        ];

        $insertStmt = $conn->prepare("INSERT INTO mon_an (ten_mon, slug, hinh_anh, mo_ta_ngan, nguyen_lieu, cach_lam, id_vung_mien, gia_tien, thoi_gian_nau, danh_gia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $existStmt = $conn->prepare("SELECT id FROM mon_an WHERE slug = ?");

        foreach ($newDishes as $d) {
            $existStmt->execute([$d[1]]);
            if (!$existStmt->fetch()) {
                $insertStmt->execute($d);
            }
        }
    }

    private static function seedHotpots($conn) {
        $newLaus = [
            ['Lẩu Thái Chua Cay', 'lau-thai-chua-cay', 'Lẩu mang đậm hương vị chua cay đặc trưng của Thái Lan hòa quyện cùng hải sản tươi sống.', 'Hải sản', 'Nấu nước dùng', 180000, 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=800', 4.8, 3],
            ['Lẩu Cá Tầm Sapa', 'lau-ca-tam-sapa', 'Cá tầm tươi ngon kết hợp với nước lẩu thanh ngọt, mang đến trải nghiệm khó quên.', 'Cá tầm', 'Nấu nước dùng', 250000, 'https://images.unsplash.com/photo-1574484284002-952d92456975?auto=format&fit=crop&q=80&w=800', 4.9, 1],
            ['Lẩu Bò Nhúng Dấm', 'lau-bo-nhung-dam', 'Thịt bò tươi thái mỏng nhúng qua nước dấm thanh mát, cuốn kèm rau sống.', 'Thịt bò', 'Nhúng dấm', 150000, 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&q=80&w=800', 4.7, 1],
            ['Lẩu Cua Đồng Mồng Tơi', 'lau-cua-dong-mong-toi', 'Món lẩu dân dã với gạch cua béo ngậy, nước lẩu ngọt thanh đặc trưng.', 'Cua đồng', 'Lọc cua', 120000, 'https://images.unsplash.com/photo-1594921677334-738914ed0dc3?auto=format&fit=crop&q=80&w=800', 4.8, 1],
            ['Lẩu Nấm Thảo Mộc', 'lau-nam-thao-moc', 'Lẩu nấm thanh đạm, bổ dưỡng với đa dạng các loại nấm tươi và thảo mộc.', 'Nấm', 'Ninh nước', 160000, 'https://images.unsplash.com/photo-1627012046890-50f968bd0a56?auto=format&fit=crop&q=80&w=800', 4.9, 2],
            ['Lẩu Gà Lá É Phú Yên', 'lau-ga-la-e', 'Món ăn đặc sản, kết hợp thịt gà dai ngon và hương vị lá é đặc trưng thơm nồng.', 'Gà', 'Nấu gà', 180000, 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&q=80&w=800', 4.9, 2],
            ['Lẩu Mực Nhúng Nước Dừa', 'lau-muc-nhung-nuoc-dua', 'Mực tươi rói nhúng trong nước dừa ngọt thanh, món ngon miền Tây sông nước.', 'Mực', 'Nấu nước dừa', 170000, 'https://images.unsplash.com/photo-1574484284002-952d92456975?auto=format&fit=crop&q=80&w=800', 4.8, 3],
            ['Lẩu Hải Sản Tomyum', 'lau-hai-san-tomyum', 'Sự kết hợp hoàn hảo giữa hải sản tươi và cốt tomyum chua cay đậm vị.', 'Hải sản', 'Nấu cốt', 190000, 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=800', 4.9, 3],
            ['Lẩu Ếch Măng Cay', 'lau-ech-mang-cay', 'Ếch đồng thịt chắc, xào măng chua cay, nhúng vào nước lẩu đỏ au đậm đà.', 'Ếch', 'Xào ếch', 150000, 'https://images.unsplash.com/photo-1594921677334-738914ed0dc3?auto=format&fit=crop&q=80&w=800', 4.7, 1],
            ['Lẩu Dê Ninh Bình', 'lau-de-ninh-binh', 'Đặc sản núi non với thịt dê núi mềm thơm, nước lẩu hầm thuốc bắc bồi bổ sinh lực.', 'Thịt dê', 'Hầm thuốc bắc', 220000, 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&q=80&w=800', 4.8, 1]
        ];

        $stmt = $conn->prepare("INSERT INTO mon_an (ten_mon, slug, mo_ta_ngan, nguyen_lieu, cach_lam, gia_tien, hinh_anh, danh_gia, id_vung_mien) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $existStmt = $conn->prepare("SELECT id FROM mon_an WHERE ten_mon = ?");
        foreach ($newLaus as $l) {
            $existStmt->execute([$l[0]]);
            if (!$existStmt->fetch()) {
                $stmt->execute([$l[0], $l[1], $l[2], $l[3], $l[4], $l[5], $l[6], $l[7], $l[8]]);
            }
        }
    }
}
