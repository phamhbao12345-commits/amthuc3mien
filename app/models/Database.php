<?php
class Database {
    // 1. Cập nhật thông tin từ Aiven Console Overview
    private $host = "mysql-3d3ff37a-phamhbao12345-9b90.c.aivencloud.com";
    private $port = "15153";
    private $db_name = "am_thuc_3_mien";
    private $username = "avnadmin";
    private $password = "AVNS_Szxjd2xmXqJ6UNlh5OK"; 
    
    public $conn;
    private static $sharedConn = null;
    private static $isInitialized = false;

    public function getConnection() {
        // Tối ưu Singleton: Tái sử dụng kết nối đã có, không tạo kết nối mới
        if (self::$sharedConn !== null) {
            $this->conn = self::$sharedConn;
            return $this->conn;
        }

        try {
            // 2. Thêm $this->port vào DSN
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // 3. Cấu hình SSL bắt buộc của Aiven
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Cho phép kết nối SSL thông suốt
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            $this->conn->exec("set names utf8mb4");
            self::$sharedConn = $this->conn;

            // Tối ưu siêu tốc: Nếu cơ sở dữ liệu đã có dữ liệu, bỏ qua toàn bộ logic seed
            if (self::$isInitialized) {
                return $this->conn;
            }
            self::$isInitialized = true;

            // =========================================================================
            // TOÀN BỘ PHẦN TẠO BẢNG VÀ SEED DỮ LIỆU BÊN DƯỚI GIỮ NGUYÊN 100%
            // =========================================================================
            try {
                $checkTable = $this->conn->query("SHOW TABLES LIKE 'mon_an'");
                if ($checkTable && $checkTable->rowCount() > 0) {
                    $dishCount = $this->conn->query("SELECT COUNT(*) FROM `mon_an`")->fetchColumn();
                    if ($dishCount > 20) {
                        return $this->conn; // ThoÃ¡t ngay láº­p tá»©c, tá»‘c Ä‘á»™ truy cáº­p trang Ä‘áº¡t má»©c tá»©c thÃ¬ (1ms)!
                    }
                }
            } catch (PDOException $e) {}

            // Tá»± Ä‘á»™ng táº¡o báº£ng náº¿u chÆ°a cÃ³ (chá»‰ cháº¡y láº§n Ä‘áº§u tiÃªn khi chÆ°a cÃ³ CSDL)
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `restaurants` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `address` varchar(255) NOT NULL,
                `district` varchar(100) NOT NULL,
                                `image` varchar(255) DEFAULT NULL,
                `rating` decimal(2,1) DEFAULT '0.0',
                `lat` decimal(10,8) DEFAULT NULL,
                `lng` decimal(11,8) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


            // Add lat/lng to existing restaurants table if not exists
            try {
                $this->conn->exec("ALTER TABLE `restaurants` ADD COLUMN `lat` DECIMAL(10,8) DEFAULT NULL, ADD COLUMN `lng` DECIMAL(11,8) DEFAULT NULL");
            } catch(PDOException $e) {}

            $this->conn->exec("CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(50) NOT NULL UNIQUE,
                `password` varchar(255) NOT NULL,
                `fullname` varchar(100) DEFAULT NULL,
                `role` enum('admin', 'user') DEFAULT 'user',
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


            // Tá»± Ä‘á»™ng thÃªm nhÃ  hÃ ng náº¿u báº£ng trá»‘ng
            $stmt = $this->conn->query("SELECT COUNT(*) FROM restaurants");
            if ($stmt && $stmt->fetchColumn() == 0) {
                $this->conn->exec("INSERT INTO restaurants (name, address, district, rating, image, lat, lng) VALUES 
                ('Galbi House - Mega Market Hiá»‡p PhÃº', 'QL1A, P. Hiá»‡p PhÃº, Q.12', 'Quáº­n 12', 4.0, 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=200&h=200', 10.875, 106.635),
                ('Äáº¡i PhÃº - Nguyá»…n VÄƒn QuÃ¡', 'Sá»‘ 56 Nguyá»…n VÄƒn QuÃ¡, Q.12', 'Quáº­n 12', 4.2, 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&q=80&w=200&h=200', 10.855, 106.630),
                ('CÆ¡m NiÃªu LÃ ng Tre', 'Sá»‘ 78 ÄÆ°á»ng 21, P.8, Q.GÃ² Váº¥p', 'Quáº­n GÃ² Váº¥p', 4.5, 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=200&h=200', 10.840, 106.645),
                ('NhÃ  hÃ ng Hoa Mai', '123 LÃª Lá»£i, Q.1', 'Quáº­n 1', 4.8, 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=200&h=200', 10.776, 106.700),
                ('Láº©u BÃ² TÃ­ Chuá»™t', '456 CMT8, Q.3', 'Quáº­n 3', 4.1, 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=200&h=200', 10.785, 106.675)
                ");
            } else {
                try {
                    $this->conn->exec("UPDATE `restaurants` SET `lat` = 10.875, `lng` = 106.635 WHERE `lat` IS NULL AND `name` LIKE '%Galbi%'");
                    $this->conn->exec("UPDATE `restaurants` SET `lat` = 10.855, `lng` = 106.630 WHERE `lat` IS NULL AND `name` LIKE '%Äáº¡i PhÃº%'");
                    $this->conn->exec("UPDATE `restaurants` SET `lat` = 10.840, `lng` = 106.645 WHERE `lat` IS NULL AND `name` LIKE '%LÃ ng Tre%'");
                    $this->conn->exec("UPDATE `restaurants` SET `lat` = 10.850, `lng` = 106.635 WHERE `lat` IS NULL");
                } catch(PDOException $e) {}
            }

            // Bá»• sung cÃ¡c nhÃ  hÃ ng Ä‘áº·c trÆ°ng táº¡i cÃ¡c tá»‰nh thÃ nh khÃ¡c náº¿u chÆ°a cÃ³
            try {
                $checkOtherRes = $this->conn->query("SELECT COUNT(*) FROM `restaurants` WHERE `district` IN ('HÃ  Ná»™i', 'ÄÃ  Náºµng', 'Cáº§n ThÆ¡', 'TÃ¢y Ninh', 'PhÃº Quá»‘c')");
                if ($checkOtherRes && $checkOtherRes->fetchColumn() == 0) {
                    $this->conn->exec("INSERT INTO `restaurants` (`name`, `address`, `district`, `rating`, `image`, `lat`, `lng`) VALUES 
                    ('Phá»Ÿ BÃ¡t ÄÃ n Gia Truyá»n', '49 BÃ¡t ÄÃ n, Cá»­a ÄÃ´ng, Q. HoÃ n Kiáº¿m, HÃ  Ná»™i', 'HÃ  Ná»™i', 4.8, 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&q=80&w=400', 21.034, 105.847),
                    ('BÃºn Cháº£ Äáº¯c Kim', 'Sá»‘ 1 HÃ ng MÃ nh, Q. HoÃ n Kiáº¿m, HÃ  Ná»™i', 'HÃ  Ná»™i', 4.6, 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&q=80&w=400', 21.033, 105.848),
                    ('MÃ¬ Quáº£ng BÃ  Mua', '19 Tráº§n BÃ¬nh Trá»ng, Q. Háº£i ChÃ¢u, ÄÃ  Náºµng', 'ÄÃ  Náºµng', 4.7, 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=400', 16.065, 108.218),
                    ('BÃ¡nh TrÃ¡ng Cuá»‘n Thá»‹t Heo Äáº¡i Lá»™c', '124 Huá»³nh ThÃºc KhÃ¡ng, Q. Háº£i ChÃ¢u, ÄÃ  Náºµng', 'ÄÃ  Náºµng', 4.5, 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=400', 16.060, 108.220),
                    ('BÃ¡nh XÃ¨o 7 Tá»›i Cáº§n ThÆ¡', '45 HoÃ ng Quá»‘c Viá»‡t, An BÃ¬nh, Ninh Kiá»u, Cáº§n ThÆ¡', 'Cáº§n ThÆ¡', 4.8, 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400', 10.035, 105.772),
                    ('Vá»‹t Náº¥u Chao ThÃ nh Giao', 'Háº»m 1 LÃ½ Tá»± Trá»ng, An PhÃº, Ninh Kiá»u, Cáº§n ThÆ¡', 'Cáº§n ThÆ¡', 4.6, 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400', 10.038, 105.780),
                    ('BÃ² TÆ¡ TÃ¢y Ninh NÄƒm SÃ¡nh', 'ÄÆ°á»ng 30/4, P.3, TP. TÃ¢y Ninh', 'TÃ¢y Ninh', 4.7, 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400', 11.312, 106.098),
                    ('BÃ¡nh Canh Tráº£ng BÃ ng HoÃ ng Minh', '38 QL22, Tráº£ng BÃ ng, TÃ¢y Ninh', 'TÃ¢y Ninh', 4.6, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&q=80&w=400', 11.032, 106.358),
                    ('BÃºn Quáº­y Kiáº¿n XÃ¢y PhÃº Quá»‘c', '28 ÄÆ°á»ng Báº¡ch Äáº±ng, DÆ°Æ¡ng ÄÃ´ng, PhÃº Quá»‘c', 'PhÃº Quá»‘c', 4.9, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&q=80&w=400', 10.218, 103.959),
                    ('NhÃ  BÃ¨ BÃ© Gháº¹ HÃ m Ninh', 'LÃ ng ChÃ i HÃ m Ninh, PhÃº Quá»‘c', 'PhÃº Quá»‘c', 4.7, 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=400', 10.180, 104.045)
                    ");
                }
            } catch(PDOException $e) {}

            // ThÃªm cá»™t tinh_thanh vÃ o báº£ng mon_an náº¿u chÆ°a cÃ³
            try {
                $this->conn->exec("ALTER TABLE `mon_an` ADD COLUMN `tinh_thanh` VARCHAR(100) DEFAULT NULL");
            } catch(PDOException $e) {}

            // Cáº­p nháº­t nguyÃªn liá»‡u vÃ  cÃ¡ch lÃ m chuáº©n cho BÃ¡nh xÃ¨o Cáº§n ThÆ¡
            try {
                $this->conn->exec("UPDATE `mon_an` SET 
                    `nguyen_lieu` = 'â€¢ Pháº§n vá» bÃ¡nh: 400g bá»™t gáº¡o (hoáº·c bá»™t bÃ¡nh xÃ¨o miá»n TÃ¢y), 100g bá»™t chiÃªn giÃ²n, 1 muá»—ng cÃ  phÃª bá»™t nghá»‡, 500ml nÆ°á»›c cá»‘t dá»«a, 400ml nÆ°á»›c lá»c, 1 quáº£ trá»©ng gÃ , hÃ nh lÃ¡ cáº¯t nhá», 1/2 muá»—ng cÃ  phÃª muá»‘i.\\nâ€¢ Pháº§n nhÃ¢n bÃ¡nh: 300g tÃ´m sÃ´ng tÆ°Æ¡i, 300g thá»‹t ba rá»i thÃ¡i má»ng, 150g Ä‘áº­u xanh khÃ´ng vá» (Ä‘Ã£ háº¥p chÃ­n), 200g giÃ¡ Ä‘á»— sáº¡ch, 150g cá»§ hÅ© dá»«a thÃ¡i sá»£i (hoáº·c cá»§ sáº¯n), 1 cá»§ hÃ nh tÃ¢y.\\nâ€¢ NÆ°á»›c cháº¥m & Rau rá»«ng: NÆ°á»›c máº¯m cÃ¡ cÆ¡m ngon, tá»i, á»›t bÄƒm, Ä‘Æ°á»ng, chanh, Ä‘á»“ chua (cÃ  rá»‘t, cá»§ cáº£i bÃ o sá»£i); rá»• rau rá»«ng miá»n TÃ¢y Äƒn kÃ¨m (lÃ¡ cÃ¡ch, Ä‘á»t xoÃ i, lÃ¡ lá»¥a, cáº£i xanh, xÃ  lÃ¡ch, rau thÆ¡m).',
                    `cach_lam` = 'â€¢ BÆ°á»›c 1 (Pha bá»™t giÃ²n lÃ¢u): Trá»™n bá»™t gáº¡o, bá»™t chiÃªn giÃ²n, bá»™t nghá»‡ vÃ  chÃºt muá»‘i trong Ã¢u lá»›n. Äá»• nÆ°á»›c cá»‘t dá»«a vÃ  nÆ°á»›c lá»c vÃ o khuáº¥y tan má»‹n khÃ´ng vÃ³n cá»¥c. Äáº­p 1 quáº£ trá»©ng gÃ  vÃ o Ä‘Ã¡nh Ä‘á»u cÃ¹ng hÃ nh lÃ¡ cáº¯t nhá». Äá»ƒ bá»™t nghá»‰ 30 phÃºt.\\nâ€¢ BÆ°á»›c 2 (XÃ o sÆ¡ nhÃ¢n tÃ´m thá»‹t): Thá»‹t ba rá»i vÃ  tÃ´m Æ°á»›p chÃºt hÃ nh tÃ­m bÄƒm, tiÃªu xay, háº¡t nÃªm. Phi thÆ¡m hÃ nh trÃªn cháº£o rá»“i xÃ o tÃ´m thá»‹t vá»«a chÃ­n tá»›i thÃ¬ trÃºt ra Ä‘Ä©a riÃªng.\\nâ€¢ BÆ°á»›c 3 (Äá»• bÃ¡nh xÃ¨o giÃ²n rá»¥m): LÃ m nÃ³ng cháº£o lÃ²ng sÃ¢u vá»›i 1 muá»—ng canh dáº§u Äƒn. Cháº£o tháº­t nÃ³ng thÃ¬ mÃºc 1 vÃ¡ bá»™t lÃ¡ng Ä‘á»u tháº­t má»ng quanh vÃ nh cháº£o nghe tiáº¿ng xÃ¨o giÃ²n tan. Cho nhÃ¢n tÃ´m thá»‹t, Ä‘áº­u xanh, cá»§ hÅ© dá»«a vÃ  giÃ¡ lÃªn má»™t ná»­a máº·t bÃ¡nh. Äáº­y náº¯p 2 - 3 phÃºt cho chÃ­n nhÃ¢n. Má»Ÿ náº¯p, rÆ°á»›i nháº¹ má»™t chÃºt dáº§u Äƒn quanh rÃ¬a cháº£o Ä‘á»ƒ vá» bÃ¡nh giÃ²n tan khÃ´ng bá»‹ á»‰u, sau Ä‘Ã³ gáº­p Ä‘Ã´i bÃ¡nh láº¡i vÃ  vá»›t ra Ä‘Ä©a cÃ³ lÃ³t giáº¥y tháº¥m dáº§u.\\nâ€¢ BÆ°á»›c 4 (Pha nÆ°á»›c máº¯m & ThÆ°á»Ÿng thá»©c): Pha nÆ°á»›c máº¯m chua ngá»t theo tá»‰ lá»‡ vÃ ng 1 máº¯m : 1 Ä‘Æ°á»ng : 2 nÆ°á»›c áº¥m : 1 nÆ°á»›c cá»‘t chanh, thÃªm tá»i á»›t bÄƒm vÃ  Ä‘á»“ chua. ThÆ°á»Ÿng thá»©c bÃ¡nh xÃ¨o nÃ³ng há»•i giÃ²n rá»¥m, cuá»™n cÃ¹ng bÃ¡nh trÃ¡ng vÃ  rau rá»«ng miá»n TÃ¢y cháº¥m ngáº­p chÃ©n nÆ°á»›c máº¯m chua ngá»t Ä‘áº­m Ä‘Ã .'
                    WHERE `slug` = 'banh-xeo-can-tho'");
            } catch(PDOException $e) {}

            // Seed thÃªm Ä‘áº·c sáº£n cho 8 tá»‰nh thÃ nh miá»n Nam (Äá»“ng Nai, VÄ©nh Long, An Giang, CÃ  Mau, Äá»“ng ThÃ¡p, Cáº§n ThÆ¡, TÃ¢y Ninh, TP.HCM)
            try {
                $extraSouthernDishes = [
                    // Äá»“ng Nai
                    ['Gá»i bÆ°á»Ÿi TÃ¢n Triá»u', 'goi-buoi-tan-trieu-dong-nai', 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800', 'Gá»i bÆ°á»Ÿi trá»© danh lÃ ng bÆ°á»Ÿi TÃ¢n Triá»u (Äá»“ng Nai) vá»›i tÃ©p bÆ°á»Ÿi Ä‘Æ°á»ng lÃ¡ cam má»ng nÆ°á»›c chua ngá»t dá»‹u, tÃ´m thá»‹t Ä‘áº­m Ä‘Ã  ráº¯c Ä‘áº­u phá»™ng rang bÃ¹i thÆ¡m.', 'BÆ°á»Ÿi Ä‘Æ°á»ng TÃ¢n Triá»u, tÃ´m sÃ´ng, thá»‹t ba rá»i, rau rÄƒm, mÃ¨ rang, nÆ°á»›c máº¯m chua ngá»t', 'TÃ¡ch mÃºi bÆ°á»Ÿi cáº©n tháº­n khÃ´ng giáº­p tÃ©p, trá»™n Ä‘á»u vá»›i tÃ´m thá»‹t luá»™c thÃ¡i má»ng vÃ  nÆ°á»›c sá»‘t chua ngá»t, Äƒn kÃ¨m bÃ¡nh phá»“ng tÃ´m.', 3, 'Äá»“ng Nai', 85000, 30, 4.8],
                    ['GÃ  háº¥p bÆ°á»Ÿi TÃ¢n Triá»u', 'ga-hap-buoi-dong-nai', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image12.jpg', 'GÃ  ta tháº£ vÆ°á»n Æ°á»›p gia vá»‹ Ä‘áº·t trá»n trong quáº£ bÆ°á»Ÿi to Ä‘em háº¥p cÃ¡ch thá»§y, thá»‹t gÃ  ngá»t lá»‹m thÆ¡m ná»“ng tinh dáº§u bÆ°á»Ÿi Ä‘áº·c sáº£n Äá»“ng Nai.', 'GÃ  ta tháº£ vÆ°á»n, quáº£ bÆ°á»Ÿi TÃ¢n Triá»u to, rÆ°á»£u bÆ°á»Ÿi, lÃ¡ bÆ°á»Ÿi thÃ¡i chá»‰, á»›t hiá»ƒm', 'GÃ  Æ°á»›p gia vá»‹ tháº£o má»™c cho vÃ o trong quáº£ bÆ°á»Ÿi Ä‘Ã£ náº¡o ruá»™t, Ä‘áº­y náº¯p bÆ°á»Ÿi láº¡i rá»“i háº¥p cÃ¡ch thá»§y trong 45 phÃºt.', 3, 'Äá»“ng Nai', 280000, 50, 4.9],
                    ['Canh chua lÃ¡ giang lÃ²ng bÃ²', 'canh-chua-la-giang-long-bo', 'https://images.unsplash.com/photo-1582294154941-8f5eb23b0906?auto=format&fit=crop&q=80&w=800', 'Äáº·c sáº£n vÃ¹ng Ä‘áº¥t Long KhÃ¡nh (Äá»“ng Nai) vá»›i vá»‹ chua thanh sáº£ng khoÃ¡i cá»§a lÃ¡ giang rá»«ng vÃ  lÃ²ng bÃ² giÃ²n sáº§n sáº­t bÃ©o ngáº­y.', 'LÃ²ng bÃ² tÆ°Æ¡i, lÃ¡ giang rá»«ng, sáº£ á»›t bÄƒm, ngÃ² gai, tá»i phi', 'LÃ²ng bÃ² lÃ m sáº¡ch thÃ¡i vá»«a Äƒn, xÃ o thÆ¡m vá»›i sáº£ á»›t rá»“i náº¥u sÃ´i cÃ¹ng nÆ°á»›c dÃ¹ng, vÃ² nháº¹ lÃ¡ giang tháº£ vÃ o táº¡o vá»‹ chua dá»‹u.', 3, 'Äá»“ng Nai', 120000, 40, 4.7],
                    ['Láº©u khá»• qua rá»«ng Long KhÃ¡nh', 'lau-kho-qua-rung-dong-nai', 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=800', 'TrÃ¡i vÃ  Ä‘á»t khá»• qua rá»«ng má»c tá»± nhiÃªn táº¡i Long KhÃ¡nh - Äá»“ng Nai, nhÃºng láº©u cÃ¡ thÃ¡c lÃ¡c dai giÃ²n thanh nhiá»‡t cá»±c tá»‘t.', 'Khá»• qua rá»«ng Long KhÃ¡nh, cháº£ cÃ¡ thÃ¡c lÃ¡c náº¡o, xÆ°Æ¡ng á»‘ng ninh nÆ°á»›c dÃ¹ng, náº¥m', 'Náº¥u nÆ°á»›c dÃ¹ng ngá»t thanh tá»« xÆ°Æ¡ng, quáº¿t cháº£ cÃ¡ thÃ¡c lÃ¡c dai giÃ²n tháº£ vÃ o, nhÃºng Ä‘á»t vÃ  trÃ¡i khá»• qua rá»«ng Äƒn nÃ³ng.', 3, 'Äá»“ng Nai', 180000, 35, 4.6],
                    
                    // VÄ©nh Long
                    ['Khoai lang máº¯m sá»‘ng VÄ©nh Long', 'khoai-lang-mam-song-vinh-long', 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=800', 'MÃ³n Äƒn dÃ¢n dÃ£ ná»©c tiáº¿ng BÃ¬nh TÃ¢n - VÄ©nh Long: khoai lang tÃ­m bÃ¹i bÃ©o ngá»t Äƒn kÃ¨m máº¯m sá»‘ng chÆ°ng tá»i á»›t vÃ  dá»«a náº¡o sá»£i.', 'Khoai lang tÃ­m BÃ¬nh TÃ¢n, máº¯m cÃ¡ linh/cÃ¡ sáº·c, dá»«a náº¡o sá»£i, gá»«ng non thÃ¡i sá»£i, rau vÆ°á»n', 'Khoai lang háº¥p chÃ­n thÆ¡m ná»©c, Äƒn kÃ¨m máº¯m cÃ¡ trá»™n gá»«ng á»›t vÃ  cÆ¡m dá»«a bÃ©o ngáº­y Ä‘áº­m cháº¥t miá»n quÃª VÄ©nh Long.', 3, 'VÄ©nh Long', 50000, 25, 4.7],
                    ['CÃ¡ tai tÆ°á»£ng chiÃªn xÃ¹ VÄ©nh Long', 'ca-tai-tuong-chien-xu-vinh-long', 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=800', 'Äáº·c sáº£n trá»© danh cÃ¹ lao VÄ©nh Long, cÃ¡ tÆ°Æ¡i sÃ´ng Cá»• ChiÃªn chiÃªn ngáº­p dáº§u vÃ ng Æ°Æ¡m dá»±ng váº£y giÃ²n rá»¥m, cuá»‘n bÃ¡nh trÃ¡ng rau sá»‘ng.', 'CÃ¡ tai tÆ°á»£ng sÃ´ng Cá»• ChiÃªn, bÃ¡nh trÃ¡ng, kháº¿, chuá»‘i chÃ¡t, rau sá»‘ng miá»‡t vÆ°á»n, máº¯m chua ngá»t', 'CÃ¡ giá»¯ nguyÃªn váº£y, chiÃªn trong cháº£o ngáº­p dáº§u sÃ´i cho váº£y xÃ¹ giÃ²n tan, thá»‹t bÃªn trong tráº¯ng ngá»t cuá»‘n bÃ¡nh trÃ¡ng cháº¥m máº¯m tá»i á»›t.', 3, 'VÄ©nh Long', 220000, 30, 4.9],
                    ['Thanh trÃ  chua ngá»t VÄ©nh Long', 'thanh-tra-vinh-long', 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&q=80&w=800', 'TrÃ¡i thanh trÃ  trÃ²n má»ng vÃ ng Æ°Æ¡m xá»© BÃ¬nh Minh - VÄ©nh Long, hÆ°Æ¡ng thÆ¡m ngÃ¡t tá»± nhiÃªn, cháº¥m muá»‘i á»›t hoáº·c dáº§m Ä‘Ã¡ Ä‘Æ°á»ng thanh mÃ¡t.', 'TrÃ¡i thanh trÃ  chÃ­n má»ng, muá»‘i á»›t cay ná»“ng hoáº·c Ä‘Æ°á»ng cÃ¡t Ä‘Ã¡ láº¡nh', 'Thanh trÃ  chÃ­n bÃ³c vá» Äƒn trá»±c tiáº¿p hoáº·c dáº§m Ä‘Ã¡ Ä‘Æ°á»ng mÃ¡t láº¡nh giáº£i nhiá»‡t ngÃ y hÃ¨ miá»n TÃ¢y.', 3, 'VÄ©nh Long', 60000, 10, 4.8],

                    // CÃ  Mau
                    ['Láº©u cÃ¡ thÃ²i lÃ²i CÃ  Mau', 'lau-ca-thoi-loi-ca-mau', 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=800', 'LoÃ i cÃ¡ leo cÃ¢y Ä‘áº·c trÆ°ng vÃ¹ng rá»«ng Ä‘Æ°á»›c ngáº­p máº·n CÃ  Mau, thá»‹t dai ngá»t náº¥u láº©u chua cay báº¯p chuá»‘i Ä‘áº­m Ä‘Ã  vá»‹ Äáº¥t MÅ©i.', 'CÃ¡ thÃ²i lÃ²i Äáº¥t MÅ©i, me chÃ­n, báº¯p chuá»‘i bÃ o, ngÃ² gai, á»›t xiÃªm', 'CÃ¡ thÃ²i lÃ²i lÃ m sáº¡ch nhá»›t, nÆ°á»›c láº©u chua náº¥u tá»« me chÃ­n sÃ´i thÃ¬ tháº£ cÃ¡ vÃ  báº¯p chuá»‘i vÃ o, Äƒn kÃ¨m bÃºn tÆ°Æ¡i vÃ  nÆ°á»›c máº¯m máº·n á»›t.', 3, 'CÃ  Mau', 190000, 30, 4.8],
                    ['Vá»p nÆ°á»›ng má»¡ hÃ nh CÃ  Mau', 'vop-nuong-mo-hanh-ca-mau', 'https://images.unsplash.com/photo-1559132104-583eb531d0db?auto=format&fit=crop&q=80&w=800', 'Vá»p rá»«ng ngáº­p máº·n CÃ  Mau to bÃ©o nhiá»u thá»‹t, nÆ°á»›ng than hoa ngáº­p má»¡ hÃ nh Ä‘áº­u phá»™ng thÆ¡m ná»©c, cháº¥m muá»‘i tiÃªu chanh cay ná»“ng.', 'Vá»p tá»± nhiÃªn rá»«ng Ä‘Æ°á»›c CÃ  Mau, má»¡ hÃ nh, Ä‘áº­u phá»™ng rang giÃ£ dáº­p, muá»‘i tiÃªu chanh', 'NÆ°á»›ng vá»p trÃªn than Ä‘á» Ä‘áº¿n khi tÃ¡ch vá», chan má»¡ hÃ nh vÃ  Ä‘áº­u phá»™ng nÆ°á»›ng thÃªm 1 phÃºt, thÆ°á»Ÿng thá»©c nÃ³ng giÃ²n bÃ©o ngá»t.', 3, 'CÃ  Mau', 130000, 20, 4.7],

                    // Äá»“ng ThÃ¡p
                    ['Nem Lai Vung Äá»“ng ThÃ¡p', 'nem-lai-vung-dong-thap', 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=800', 'Lai Vung lÃ  xá»© láº¡ lÃ¹ng - Nem chua mÃ  ngá»t thÆ¡m lá»«ng tiÃªu Ä‘en. Nem Ä‘á» há»“ng bá»c lÃ¡ chÃ¹m ruá»™t truyá»n thá»‘ng trá»© danh Äá»“ng ThÃ¡p.', 'Thá»‹t náº¡c Ä‘Ã¹i heo tÆ°Æ¡i, bÃ¬ heo, tá»i á»›t, tiÃªu sá», lÃ¡ chÃ¹m ruá»™t, lÃ¡ chuá»‘i', 'Quáº¿t thá»‹t dáº»o quÃ¡nh trá»™n bÃ¬, nÃªm gia vá»‹ lÃªn men tá»± nhiÃªn gÃ³i trong lÃ¡ chÃ¹m ruá»™t vÃ  lÃ¡ chuá»‘i tá»« 3 - 4 ngÃ y.', 3, 'Äá»“ng ThÃ¡p', 45000, 60, 4.9],
                    ['CÃ¡ lÃ³c nÆ°á»›ng lÃ¡ sen non Äá»“ng ThÃ¡p', 'ca-loc-nuong-la-sen-dong-thap', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&q=80&w=800', 'CÃ¡ lÃ³c Ä‘á»“ng nÆ°á»›ng trui thÆ¡m mÃ¹i rÆ¡m ráº¡, cuá»‘n trong tá»«ng bÃºp lÃ¡ sen non ThÃ¡p MÆ°á»i cháº¥m nÆ°á»›c máº¯m me sá»n sá»‡t bÃ¹i ngá»t Ä‘á»™c Ä‘Ã¡o.', 'CÃ¡ lÃ³c Ä‘á»“ng, lÃ¡ sen non Äá»“ng ThÃ¡p, máº¯m me chua ngá»t, Ä‘áº­u phá»™ng', 'CÃ¡ lÃ³c nÆ°á»›ng trui rÆ¡m vÃ ng Æ°Æ¡m, gáº¯p thá»‹t cÃ¡ cuá»‘n trá»n trong lÃ¡ sen non cháº¥m ngáº­p máº¯m me chua cay.', 3, 'Äá»“ng ThÃ¡p', 150000, 35, 4.9],

                    // Cáº§n ThÆ¡
                    ['Vá»‹t náº¥u chao Cáº§n ThÆ¡', 'vit-nau-chao-can-tho', 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=800', 'MÃ³n láº©u vá»‹t náº¥u chao trá»© danh háº»m 1 LÃ½ Tá»± Trá»ng Ninh Kiá»u, Cáº§n ThÆ¡. NÆ°á»›c láº©u bÃ©o ngáº­y vá»‹ chao Ä‘á», khoai mÃ´n bÃ¹i dáº»o thÆ¡m lá»«ng.', 'Vá»‹t xiÃªm cá», chao Ä‘á» ngon, chao tráº¯ng, khoai mÃ´n sÃ¡p, rau muá»‘ng Ä‘á»“ng', 'Thá»‹t vá»‹t Æ°á»›p chao xÃ o sÄƒn, náº¥u cÃ¹ng nÆ°á»›c dá»«a tÆ°Æ¡i vÃ  khoai mÃ´n Ä‘áº¿n khi má»m dáº»o, cháº¥m chao pha á»›t tá»i Ä‘Æ°á»ng chanh.', 3, 'TP. Cáº§n ThÆ¡', 220000, 45, 4.9],

                    // An Giang
                    ['Láº©u máº¯m ChÃ¢u Äá»‘c - An Giang', 'lau-mam-chau-doc', 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=800', 'Thá»§ phá»§ máº¯m ChÃ¢u Äá»‘c táº¡o nÃªn ná»“i láº©u máº¯m Ä‘áº­m Ä‘Ã  tá»« máº¯m cÃ¡ linh cÃ¡ sáº·c ná»©c tiáº¿ng, Äƒn kÃ¨m hÆ¡n 20 loáº¡i rau Ä‘á»“ng Nam Bá»™.', 'Máº¯m cÃ¡ linh ChÃ¢u Äá»‘c, máº¯m sáº·c, thá»‹t ba chá»‰, tÃ´m tháº», cÃ  tÃ­m, bÃ´ng Ä‘iÃªn Ä‘iá»ƒn, kÃ¨o nÃ¨o', 'Náº¥u rÃ£ máº¯m cÃ¡ lÆ°á»£c bá» xÆ°Æ¡ng láº¥y nÆ°á»›c dÃ¹ng ngá»t Ä‘áº­m, cho thá»‹t cÃ¡ tÃ´m vÃ o Ä‘un sÃ´i, nhÃºng ngáº­p cÃ¡c loáº¡i rau Ä‘á»“ng Äƒn cÃ¹ng bÃºn tÆ°Æ¡i.', 3, 'An Giang', 250000, 60, 4.9],
                    ['BÃ² báº£y mÃ³n NÃºi Sam An Giang', 'bo-bay-mon-nui-sam', 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=800', 'Thá»‹t bÃ² tÆ¡ NÃºi Sam (ChÃ¢u Äá»‘c, An Giang) má»m ngá»t tá»± nhiÃªn, cháº¿ biáº¿n Ä‘á»§ 7 mÃ³n Ä‘áº·c sáº¯c phá»¥c vá»¥ du khÃ¡ch hÃ nh hÆ°Æ¡ng Miáº¿u BÃ  ChÃºa Xá»©.', 'BÃ² tÆ¡ NÃºi Sam, bÃ¡nh trÃ¡ng, sáº£ á»›t, máº¯m nÃªm ChÃ¢u Äá»‘c', 'Cháº¿ biáº¿n Ä‘á»§ 7 mÃ³n Ä‘áº·c sáº¯c: bÃ² nhÃºng giáº¥m, bÃ² lá»¥i sáº£, bÃ² xÃ o lÃ¡ giang, chÃ¡o bÃ², bÃ² nÃ©, bÃ² bÃ³p tháº¥u, láº©u bÃ².', 3, 'An Giang', 350000, 60, 4.8]
                ];

                $stmtDish = $this->conn->prepare("INSERT INTO `mon_an` (`ten_mon`, `slug`, `hinh_anh`, `mo_ta_ngan`, `nguyen_lieu`, `cach_lam`, `id_vung_mien`, `tinh_thanh`, `gia_tien`, `thoi_gian_nau`, `danh_gia`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($extraSouthernDishes as $dish) {
                    $chk = $this->conn->prepare("SELECT id FROM `mon_an` WHERE `slug` = ?");
                    $chk->execute([$dish[1]]);
                    if (!$chk->fetch()) {
                        $stmtDish->execute($dish);
                    }
                }
            } catch(PDOException $e) {}

            // Cáº­p nháº­t tinh_thanh chuáº©n xÃ¡c cho toÃ n bá»™ cÃ¡c mÃ³n miá»n Nam
            try {
                // 1. TP. Há»“ ChÃ­ Minh
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'TP. Há»“ ChÃ­ Minh' WHERE `slug` IN ('com-tam', 'com-tam-sai-gon', 'banh-trang-tron-sai-gon', 'sui-cao-tai-sai-gon', 'pha-lau-sai-gon', 'cac-mon-oc', 'banh-bong-lan-trung-muoi-sai-gon')");
                // 2. Äá»“ng Nai
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Äá»“ng Nai' WHERE `slug` IN ('goi-buoi-tan-trieu-dong-nai', 'ga-hap-buoi-dong-nai', 'canh-chua-la-giang-long-bo', 'lau-kho-qua-rung-dong-nai')");
                // 3. TÃ¢y Ninh
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'TÃ¢y Ninh' WHERE `slug` IN ('bo-to-tay-ninh', 'banh-canh-trang-bang-tay-ninh', 'than-lan-nui-ba-den-tay-ninh', 'theo-leo-tay-ninh', 'nem-buoi-tay-ninh')");
                // 4. TP. Cáº§n ThÆ¡
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'TP. Cáº§n ThÆ¡' WHERE `slug` IN ('banh-xeo-can-tho', 'ca-loc-nuong-trui-can-tho', 'banh-duc-la-dua-can-tho', 'banh-cong-can-tho', 'chuot-dong-nuong-lu', 'vit-nau-chao-can-tho')");
                // 5. An Giang
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'An Giang' WHERE `slug` IN ('bun-ca-chau-doc', 'banh-canh-tep-chau-doc', 'ga-dot-tri-ton', 'du-du-dam-tri-ton', 'lau-mam-chau-doc', 'bo-bay-mon-nui-sam')");
                // 6. Äá»“ng ThÃ¡p
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Äá»“ng ThÃ¡p' WHERE `slug` IN ('hu-tieu-sa-dec', 'lau-ca-linh-bong-dien-dien', 'nem-lai-vung-dong-thap', 'ca-loc-nuong-la-sen-dong-thap')");
                // 7. Vĩnh Long
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Vĩnh Long' WHERE `slug` IN ('khoai-lang-mam-song-vinh-long', 'ca-tai-tuong-chien-xu-vinh-long', 'thanh-tra-vinh-long')");
                // 8. Cà Mau
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Cà Mau' WHERE `slug` IN ('banh-tam-ca-mau', 'cua-da-rang-muoi-ca-mau', 'lau-ca-thoi-loi-ca-mau', 'vop-nuong-mo-hanh-ca-mau')");
                // 9. Hà Nội
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Hà Nội' WHERE `slug` IN ('pho-ha-noi', 'bun-cha-ha-noi', 'lau-bo-nhung-dam', 'lau-cua-dong-mong-toi', 'cha-ca-la-vong-ha-noi', 'bun-thang-ha-noi') OR `ten_mon` LIKE '%Hà Nội%'");
                // 10. Thừa Thiên Huế
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Thừa Thiên Huế' WHERE `slug` IN ('bun-bo-hue', 'com-hen-hue', 'banh-bot-loc-hue', 'nem-lui-hue') OR `ten_mon` LIKE '%Huế%'");
                // 11. Quảng Nam & Đà Nẵng
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Quảng Nam' WHERE `slug` IN ('mi-quang', 'cao-lau-hoi-an', 'banh-dap-hen-xao')");
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Đà Nẵng' WHERE `slug` IN ('cha-bo-da-nang')");
                // 12. Bình Định & Phú Yên
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Bình Định' WHERE `slug` IN ('banh-hoi-long-heo', 'tre-binh-dinh')");
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Phú Yên' WHERE `slug` IN ('banh-canh-he-phu-yen', 'lau-ga-la-e')");

                // Thêm các món đặc sản Hà Nội nếu chưa có
                $extraHanoi = [
                    ['Chả cá Lã Vọng Hà Nội', 'cha-ca-la-vong-ha-noi', 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=800', 'Đặc sản kinh kỳ nức tiếng với từng miếng cá lăng vàng ruộm xào thì là, hành hoa chấm mắm tôm.', 'Cá lăng, thì là, hành hoa, mắm tôm, bún, lạc rang', 'Phi thơm cá lăng với mỡ trên chảo nóng, thêm hành hoa và thì là, ăn kèm bún tươi và mắm tôm đánh sủi bọt.', 1, 'Hà Nội', 180000, 30, 4.9],
                    ['Bún Thang Hà Nội', 'bun-thang-ha-noi', 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&q=80&w=800', 'Được ví như bông hoa ngũ sắc của ẩm thực thủ đô với nước dùng gà thanh ngọt, tôm khô nức mũi.', 'Thịt gà ta xé, giò lụa, trứng tráng mỏng, củ cải khô, nấm hương', 'Ninh nước dùng xương gà và tôm he trong vắt, xếp các nguyên liệu thái chỉ cầu kỳ lên bát bún.', 1, 'Hà Nội', 65000, 45, 4.8]
                ];
                $stmtHanoi = $this->conn->prepare("INSERT INTO `mon_an` (`ten_mon`, `slug`, `hinh_anh`, `mo_ta_ngan`, `nguyen_lieu`, `cach_lam`, `id_vung_mien`, `tinh_thanh`, `gia_tien`, `thoi_gian_nau`, `danh_gia`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($extraHanoi as $hnDish) {
                    $chkHn = $this->conn->prepare("SELECT id FROM `mon_an` WHERE `slug` = ?");
                    $chkHn->execute([$hnDish[1]]);
                    if (!$chkHn->fetch()) {
                        $stmtHanoi->execute($hnDish);
                    }
                }
                
                // Chuáº©n hÃ³a Unicode chá»¯ tiáº¿ng Viá»‡t trÃ¡nh lá»—i tÃ¡ch dáº¥u (Quá»‘c)
                $this->conn->exec("UPDATE `mon_an` SET `ten_mon` = 'BÃºn kÃ¨n PhÃº Quá»‘c' WHERE `slug` = 'bun-ken-phu-quoc'");
                $this->conn->exec("UPDATE `mon_an` SET `ten_mon` = 'BÃºn quáº­y PhÃº Quá»‘c' WHERE `slug` = 'bun-quay-phu-quoc'");
                $this->conn->exec("UPDATE `mon_an` SET `ten_mon` = 'Gá»i cÃ¡ trÃ­ch PhÃº Quá»‘c' WHERE `slug` = 'goi-ca-trich-phu-quoc'");
            } catch(PDOException $e) {}

            // Tá»± Ä‘á»™ng táº¡o tÃ i khoáº£n admin náº¿u báº£ng users trá»‘ng
            $stmt = $this->conn->query("SELECT COUNT(*) FROM users");
            if ($stmt && $stmt->fetchColumn() == 0) {
                $password = password_hash('password', PASSWORD_DEFAULT);
                $this->conn->exec("INSERT INTO users (username, password, fullname, role) VALUES ('admin', '$password', 'Administrator', 'admin')");
            }

                        // Seed MÃ³n Ngon Miá»n Nam tá»« Vincom
            $sql_dishes = "INSERT INTO `mon_an` (`ten_mon`, `slug`, `hinh_anh`, `mo_ta_ngan`, `nguyen_lieu`, `cach_lam`, `id_vung_mien`, `gia_tien`, `thoi_gian_nau`, `danh_gia`) VALUES
            ('Láº©u cÃ¡ linh bÃ´ng Ä‘iÃªn Ä‘iá»ƒn', 'lau-ca-linh-bong-dien-dien', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image38.jpg', 'CÃ¡ linh lÃ  má»™t loáº¡i cÃ¡ Ä‘áº·c trÆ°ng chá»‰ cÃ³ á»Ÿ vÃ¹ng Ä‘áº¥t Nam bá»™, thÆ°á»ng xuáº¥t hiá»‡n nhiá»u vÃ o mÃ¹a nÆ°á»›c ná»•i. BÃªn cáº¡nh Ä‘Ã³, bÃ´ng Ä‘iÃªn Ä‘iá»ƒn cÅ©ng lÃ  loáº¡i bÃ´ng Ä‘áº·c trÆ°ng, má»c dáº¡i á»Ÿ mÃ© sÃ´ng. Sá»± káº¿t há»£p cá»§a 2 nguyÃªn liá»‡u nÃ y cÃ¹ng má»™t sá»‘ loáº¡i gia vá»‹ Ä‘Ã£ táº¡o nÃªn má»™t mÃ³n ngon miá»n Nam chÃ­nh lÃ  láº©u cÃ¡ linh bÃ´ng Ä‘iÃªn Ä‘iá»ƒn. MÃ³n láº©u cÃ³ vá»‹ chua chua ngá»t ngá»t, cÃ¹ng vá»‹ bÃ¹i thÆ¡m tá»« cÃ¡ hoÃ  quyá»‡n vá»›i vá»‹ hÆ¡i Ä‘Äƒng Ä‘áº¯ng, giÃ²n giÃ²n cá»§a bÃ´ng Ä‘iÃªn Ä‘iá»ƒn cháº¯c cháº¯n sáº½ gÃ¢y thÆ°Æ¡ng nhá»› cho ai lá»¡ má»™t láº§n thÆ°á»Ÿng thá»©c. Láº©u cÃ¡ linh bÃ´ng Ä‘iÃªn Ä‘iá»ƒn - mÃ³n ngon chá»‰ cÃ³ á»Ÿ miá»n Nam (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Láº©u máº¯m', 'lau-mam', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image15.png', 'Nháº¯c Ä‘áº¿n mÃ³n ngon miá»n Nam khÃ´ng thá»ƒ thiáº¿u láº©u máº¯m. NguyÃªn liá»‡u chÃ­nh lÃ m nÃªn hÆ°Æ¡ng vá»‹ Ä‘áº·c biá»‡t cá»§a mÃ³n Äƒn nÃ y chÃ­nh lÃ  cÃ¡ sáº·c vÃ  cÃ¡ linh. CÃ¡ sáº·c, xÆ°Æ¡ng heo, nÆ°á»›c dá»«a Ä‘Æ°á»£c ninh Ä‘á»ƒ láº¥y nÆ°á»›c dÃ¹ng, káº¿t há»£p máº¯m cÃ¡ linh vÃ  má»™t sá»‘ nguyÃªn liá»‡u khÃ¡c. VÃ¬ tháº¿, láº©u máº¯m khÃ´ng há» máº·n mÃ²i nhÆ° cÃ¡i tÃªn mÃ  ráº¥t ngá»t thanh, thÆ¡m ná»“ng. Rau Äƒn kÃ¨m láº©u máº¯m cÅ©ng lÃ  nhá»¯ng loáº¡i rau tá»± nhiÃªn cá»§a vÃ¹ng sÃ´ng nÆ°á»›c nhÆ° rau Ä‘áº¯ng, rau nhÃºt, lá»¥c bÃ¬nh, Ä‘iÃªn Ä‘iá»ƒn, kÃ¨o nÃ¨o, so Ä‘Å©a, ngÃ³ sÃºng,... NgÃ y nay láº©u máº¯m cÃ²n Ä‘Æ°á»£c káº¿t há»£p nhiá»u nguyÃªn váº­t liá»‡u hÆ¡n, nhÆ° cÃ¡ hÃº, thá»‹t ba rá»i, tÃ´m, má»±c,... cÃ ng lÃ m nÆ°á»›c láº©u thÃªm Ä‘áº­m Ä‘Ã , thÆ¡m ngá»t, khiáº¿n ai Äƒn má»™t láº§n lÃ  nhá»› mÃ£i khÃ´ng quÃªn. &gt;&gt;&gt; Xem thÃªm: MÃ“N NGON MIá»€N TRUNG CÃ“ GÃŒ? ÄIá»‚M DANH 40 Äáº¶C Sáº¢N MIá»€N TRUNG NGON, Ná»”I TIáº¾NG, AI CÅ¨NG THÃCH MÃŠ MÃ“N NGON MIá»€N Báº®C CÃ“ GÃŒ? 25 Äáº¶C Sáº¢N MIá»€N Báº®C Ná»”I TIáº¾NG NGON Ná»¨C TIáº¾NG XA Gáº¦N Láº©u máº¯m miá»n TÃ¢y ngon trá»© danh (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Chuá»™t Ä‘á»“ng nÆ°á»›ng lu', 'chuot-dong-nuong-lu', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image39.jpg', 'MÃ³n ngon miá»n Nam khÃ¡c mÃ  báº¡n Ä‘á»«ng bá» qua khi cÃ³ dá»‹p Ä‘áº¿n vá»›i vÃ¹ng Ä‘áº¥t nÃ y lÃ  chuá»™t Ä‘á»“ng nÆ°á»›ng lu. Chuá»™t Ä‘á»“ng báº¯t vá» sáº½ Ä‘Æ°á»£c lÃ m sáº¡ch, táº©m Æ°á»›p gia vá»‹ rá»“i xiÃªn que Ä‘á»ƒ nÆ°á»›ng trÃªn lu nÃ³ng. Thá»‹t sau khi nÆ°á»›ng chÃ­n vÃ ng Æ°Æ¡m, dáº­y mÃ¹i thÆ¡m phá»©c, ráº¥t dá»… gÃ¢y ghiá»n cho báº¥t ká»³ ai. Chuá»™t Ä‘á»“ng nÆ°á»›ng lu - mÃ³n Äƒn dÃ¢n dÃ£ ngÆ°á»i Cáº§n ThÆ¡ (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('ÄuÃ´ng dá»«a', 'duong-dua', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image28.jpg', 'Máº£nh Ä‘áº¥t Báº¿n Tre cÅ©ng Ä‘Ã£ gÃ³p khÃ´ng Ã­t mÃ³n Äƒn Ä‘á»ƒ danh sÃ¡ch mÃ³n ngon miá»n Nam thÃªm pháº§n phong phÃº. Má»™t trong nhá»¯ng mÃ³n ngon miá»n Nam Ä‘á»™c Ä‘Ã¡o, tháº­m chÃ­ cÃ³ pháº§n hÆ¡i â€œkinh dá»‹â€ mÃ  báº¡n nÃªn Äƒn thá»­ lÃ  Ä‘uÃ´ng dá»«a - má»™t loáº¡i sÃ¢u á»Ÿ trong thÃ¢n cÃ¢y dá»«a. ÄuÃ´ng dá»«a cÃ³ vá»‹ bÃ©o ngáº­y giá»‘ng nhÆ° nÆ°á»›c cá»‘t dá»«a, Ä‘Æ°á»£c cháº¿ biáº¿n thÃ nh nhiá»u mÃ³n Äƒn khÃ¡c nhau, phá»• biáº¿n nháº¥t lÃ  Ä‘uÃ´ng dá»«a sá»‘ng ngÃ¢m máº¯m. ÄuÃ´ng dá»«a - mÃ³n ngon Ä‘áº·c sáº£n miá»n Nam (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('CÆ¡m táº¥m', 'com-tam', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image18.jpg', 'CÆ¡m táº¥m lÃ  mÃ³n Äƒn bÃ¬nh dá»‹, quen thuá»™c vá»›i ngÆ°á»i dÃ¢n SÃ i GÃ²n vÃ  cÅ©ng lÃ  mÃ³n ngon miá»n Nam mÃ  du khÃ¡ch tháº­p phÆ°Æ¡ng khÃ´ng ai cÃ³ thá»ƒ tá»« chá»‘i. CÆ¡m táº¥m má»m dáº»o, háº¡t tÆ¡i Ä‘Æ°á»£c Äƒn kÃ¨m vá»›i cháº£ trá»©ng, trá»©ng á»‘p la, sÆ°á»n nÆ°á»›ng, láº¡p xÆ°á»Ÿng,... Äáº·c biá»‡t, nguyÃªn liá»‡u lÃ m nÃªn hÆ°Æ¡ng vá»‹ thÆ¡m ngon cá»§a mÃ³n Äƒn nÃ y chÃ­nh lÃ  chÃ©n nÆ°á»›c máº¯m máº·n ngá»t xen láº«n vá»‹ cay. CÆ¡m táº¥m - mÃ³n ngon dÃ¢n dÃ£ cá»§a ngÆ°á»i miá»n Nam (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('CÃ¡c mÃ³n á»‘c', 'cac-mon-oc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image36.jpg', 'Tá»« Báº¯c vÃ´ Nam, háº§u nhÆ° máº£nh Ä‘áº¥t nÃ o cÅ©ng Ä‘á»u cÃ³ nhá»¯ng mÃ³n á»‘c. Tháº¿ nhÆ°ng, sá»± Ä‘a dáº¡ng cá»§a nguyÃªn liá»‡u cÃ¹ng cÃ¡ch cháº¿ biáº¿n Ä‘áº·c trÆ°ng, Ä‘Ã£ khiáº¿n cÃ¡c mÃ³n á»‘c trá»Ÿ thÃ nh mÃ³n ngon miá»n Nam mÃ  ai cÅ©ng muá»‘n thÆ°á»Ÿng thá»©c. Nhá»¯ng mÃ³n á»‘c ngon SÃ i GÃ²n Ä‘Ã£ lÃ m khÃ´ng Ã­t ngÆ°á»i mÃª máº«n lÃ  á»‘c len xÃ o dá»«a, á»‘c hÆ°Æ¡ng rang muá»‘i, á»‘c cÃ  na xÃ o bÆ¡, á»‘c má»¡ chÃ¡y tá»i,... CÃ¡c mÃ³n á»‘c Ä‘áº­m Ä‘Ã , kÃ­ch thÃ­ch vá»‹ giÃ¡c (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Há»§ tiáº¿u Sa ÄÃ©c', 'hu-tieu-sa-dec', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image8.jpg', 'Há»§ tiáº¿u Sa ÄÃ©c cÅ©ng lÃ  mÃ³n ngon miá»n Nam ná»•i danh. Tháº­m chÃ­, nhiá»u ngÆ°á»i cÃ²n cho ráº±ng, ghÃ© thÄƒm Sa ÄÃ©c mÃ  chÆ°a Äƒn há»§ tiáº¿u thÃ¬ coi nhÆ° chÆ°a Ä‘áº¿n nÆ¡i nÃ y. Há»§ tiáº¿u Sa ÄÃ©c cÃ³ nÆ°á»›c dÃ¹ng trong, vá»‹ ngá»t thanh tá»« nÆ°á»›c háº§m xÆ°Æ¡ng. Sá»£i há»§ tiáº¿u tráº¯ng, ngá»t má»m, khÃ´ng bá»‹ chua. NgoÃ i ra, má»™t tÃ´ há»§ tiáº¿u cÃ²n cÃ³ Ä‘a dáº¡ng nguyÃªn liá»‡u nhÆ° tÃ´m, thá»‹t báº±m, xÃ¡ xÃ­u, gan heo,... vÃ  cÃ¡c loáº¡i rau thÆ¡m, giÃ¡,... Há»§ tiáº¿u Sa ÄÃ©c - mÃ³n ngon ná»•i tiáº¿ng miá»n Nam (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃºn kÃ¨n PhÃº Quá»‘c', 'bun-ken-phu-quoc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image10.jpg', 'Nhá»¯ng ai cÃ³ cÆ¡ há»™i du lá»‹ch PhÃº Quá»‘c, hÃ£y nhá»› thÆ°á»Ÿng thá»©c mÃ³n bÃºn kÃ¨n, Ä‘Ã¢y cÅ©ng lÃ  má»™t trong nhá»¯ng mÃ³n ngon miá»n Nam khÃ¡ ná»•i tiáº¿ng. NÆ°á»›c dÃ¹ng bÃºn Ä‘Æ°á»£c náº¥u tá»« cÃ¡ luá»™c, nÆ°á»›c cá»‘t dá»«a vÃ  cÃ¡c loáº¡i gia vá»‹ nhÆ° bá»™t cÃ  ri, ngÅ© vá»‹ hÆ°Æ¡ng. Táº¥t nhiÃªn, nguyÃªn liá»‡u khÃ´ng thá»ƒ thiáº¿u cá»§a mÃ³n bÃºn nÃ y chÃ­nh lÃ  cÃ¡ xay xÃ o sáº£ á»›t. BÃºn kÃ¨n PhÃº Quá»‘c lÃ  mÃ³n ngon chinh phá»¥c nhiá»u thá»±c khÃ¡ch (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃºn quáº­y PhÃº Quá»‘c', 'bun-quay-phu-quoc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image11.jpg', 'BÃºn quáº­y PhÃº Quá»‘c cÃ³ cÃ¡i tÃªn Ä‘á»™c láº¡, gÃ¢y áº¥n tÆ°á»£ng vá»›i nhiá»u ngÆ°á»i. Sá»Ÿ dÄ© cÃ³ tÃªn gá»i nÃ y bá»Ÿi vÃ¬ trÆ°á»›c khi Äƒn tÃ´ bÃºn thÃ¬ báº¡n tá»± &quot;quáº­y&quot;, tá»©c lÃ  tá»± láº¥y nguyÃªn liá»‡u, tá»± pha cháº¿ nÆ°á»›c cháº¥m, tá»± phá»¥c vá»¥. BÃºn quáº­y PhÃº Quá»‘c vá»‘n cÃ³ nguá»“n gá»‘c tá»« BÃ¬nh Äá»‹nh, lÃ  má»™t loáº¡i bÃºn tÆ°Æ¡i háº£i sáº£n, sau Ä‘Ã³ Ä‘Æ°á»£c ngÆ°á»i dÃ¢n Ä‘á»‹a phÆ°Æ¡ng biáº¿n táº¥u theo cÃ´ng thá»©c riÃªng vÃ  trá»Ÿ thÃ nh Ä‘áº·c sáº£n cá»§a vÃ¹ng nÃ y. BÃºn quáº­y PhÃº Quá»‘c cÃ³ Ä‘á»§ vá»‹ chua, cay, máº·n, ngá»t vá»›i cÃ¡c nguyÃªn liá»‡u tÃ´m, má»±c, cháº£ cÃ¡, rau sá»‘ng,... MÃ³n Äƒn bÃ¬nh dÃ¢n nhÆ°ng láº¡i lÃ  mÃ³n ngon miá»n Nam mÃ  báº¡n nháº¥t Ä‘á»‹nh pháº£i thá»­ khi Ä‘áº¿n Ä‘Ã¢y. BÃºn quáº­y PhÃº Quá»‘c - mÃ³n ngon ráº¥t Ä‘Ã¡ng thá»­ (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Gá»i cÃ¡ trÃ­ch PhÃº Quá»‘c', 'goi-ca-trich-phu-quoc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image20.jpg', 'Gá»i cÃ¡ trÃ­ch khÃ´ng chá»‰ lÃ  mÃ³n ngon miá»n Nam mÃ  miá»n Báº¯c hay miá»n Trung Ä‘á»u cÃ³. Tuy váº­y, gá»i cÃ¡ trÃ­ch PhÃº Quá»‘c láº¡i cÃ³ hÆ°Æ¡ng vá»‹ ráº¥t Ä‘áº·c biá»‡t. NguyÃªn liá»‡u cÃ¡ trÃ­ch Ä‘Æ°á»£c Ä‘Ã¡nh báº¯t á»Ÿ vÃ¹ng biá»ƒn PhÃº Quá»‘c, ráº¥t tÆ°Æ¡i ngon, thá»‹t cÃ¡ ngá»t. CÃ¡ sau khi sÆ¡ cháº¿ sáº½ Ä‘Æ°á»£c trá»™n Ä‘á»u vá»›i cÃ  rá»‘t thÃ¡i sá»£i, hÃ nh tÃ¢y thÃ¡i má»ng, dá»«a náº¡o sá»£i, tá»i phi vÃ ng, ngÃ² rÃ­,... NÆ°á»›c sá»‘t Ä‘áº·c biá»‡t Ä‘Æ°á»£c pha cháº¿ tá»« giáº¥m á»•i chÃ­n, Ä‘Æ°á»ng, muá»‘i, á»›t,... Gá»i cÃ¡ trÃ­ch - mÃ³n ngon miá»n Nam háº¥p dáº«n du khÃ¡ch (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh canh gháº¹ Ä‘áº·c sáº£n VÅ©ng TÃ u', 'banh-canh-ghe-dac-san-vung-tau', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image30.png', 'BÃ¡nh canh cÃ³ á»Ÿ nhiá»u vÃ¹ng miá»n, tháº¿ nhÆ°ng bÃ¡nh canh gháº¹ VÅ©ng TÃ u láº¡i cÃ³ hÆ°Æ¡ng vá»‹ ráº¥t riÃªng vÃ  lÃ  mÃ³n ngon miá»n Nam mÃ  ai cÅ©ng nÃªn Äƒn thá»­. NguyÃªn liá»‡u gháº¹ tÆ°Æ¡i sá»‘ng Ä‘Æ°á»£c Ä‘Ã¡nh báº¯t tá»« vÃ¹ng biá»ƒn VÅ©ng TÃ u. Má»™t bÃ¡t bÃ¡nh canh háº¥p dáº«n vá»›i nÆ°á»›c dÃ¹ng sÃ¡nh sá»‡t, gáº¡ch gháº¹ vÃ ng Æ°Æ¡m ráº¥t báº¯t máº¯t. Khi Äƒn, báº¡n sáº½ cáº£m nháº­n Ä‘Æ°á»£c vá»‹ Ä‘áº­m Ä‘Ã  cá»§a nÆ°á»›c dÃ¹ng, vá»‹ ngon ngá»t cá»§a thá»‹t gháº¹ hoÃ  quyá»‡n trong sá»£i bÃ¡nh canh dai má»m. BÃ¡nh canh gháº¹ lÃ  mÃ³n Äƒn Ä‘áº·c sáº£n cá»§a ngÆ°á»i dÃ¢n VÅ©ng TÃ u (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Gá»i cÃ¡ mai VÅ©ng TÃ u', 'goi-ca-mai-vung-tau', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image27.png', 'Gá»i cÃ¡ mai lÃ  mÃ³n Äƒn dÃ¢n dÃ£ cá»§a nhá»¯ng ngÆ°á»i dÃ¢n vÃ¹ng biá»ƒn vÃ  cÅ©ng lÃ  mÃ³n ngon miá»n Nam nÃ³i chung vÃ  VÅ©ng TÃ u nÃ³i riÃªng. Gá»i cÃ¡ Ä‘Æ°á»£c cháº¿ biáº¿n tá»« nhá»¯ng con cÃ¡ mai tÆ°Æ¡i xanh vá»«a má»›i Ä‘Æ°á»£c Ä‘Ã¡nh báº¯t. Thá»‹t cÃ¡ Ä‘Æ°á»£c lÃ m sáº¡ch, trá»™n vá»›i cÃ¡c loáº¡i gia vá»‹ nhÆ° chanh, tá»i, á»›t, muá»‘i, tiÃªu, thÃ­nh, Ä‘áº­u phá»™ng,... Gá»i cÃ¡ mai khi Äƒn sáº½ Ä‘Æ°á»£c cuá»‘n vá»›i bÃ¡nh trÃ¡ng kÃ¨m cÃ¡c loáº¡i rau cá»§ nhÆ° dÆ°a chuá»™t, kháº¿ chua, hÃºng quáº¿, chuá»‘i xanh,... cháº¥m vá»›i nÆ°á»›c máº¯m Ä‘áº·c trÆ°ng. Gá»i cÃ¡ mai VÅ©ng TÃ u tÆ°Æ¡i ngon, Ä‘áº·c sáº¯c (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh trÃ¡ng trá»™n SÃ i GÃ²n', 'banh-trang-tron-sai-gon', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image22.jpg', 'BÃ¡nh trÃ¡ng trá»™n lÃ  má»™t mÃ³n Äƒn váº·t SÃ i GÃ²n, giÃ¡ ráº» nhÆ°ng láº¡i ráº¥t ngon. MÃ³n ngon miá»n Nam nÃ y cÃ³ máº·t á»Ÿ háº§u háº¿t cÃ¡c Ä‘Æ°á»ng phá»‘ SÃ i GÃ²n, Ä‘Æ°á»£c ngÆ°á»i dÃ¢n Ä‘á»‹a phÆ°Æ¡ng vÃ  du khÃ¡ch yÃªu thÃ­ch. Sá»£i bÃ¡nh trÃ¡ng má»m dáº»o Ä‘Æ°á»£c trá»™n vá»›i cÃ¡c loáº¡i topping nhÆ° khÃ´ gÃ , khÃ´ bÃ², trá»©ng cÃºt, xoÃ i, rau rÄƒm vÃ  má»™t loáº¡i nÆ°á»›c sá»‘t Ä‘áº·c biá»‡t. BÃ¡nh trÃ¡ng trá»™n SÃ i GÃ²n ngon khÃ³ cÆ°á»¡ng (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Sá»§i cáº£o táº¡i SÃ i GÃ²n', 'sui-cao-tai-sai-gon', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image24.png', 'áº¢nh hÆ°á»Ÿng cá»§a áº©m thá»±c Trung Hoa, ngÆ°á»i dÃ¢n SÃ i GÃ²n Ä‘Ã£ biáº¿n táº¥u nÃªn nhiá»u mÃ³n Äƒn mang mÃ u sáº¯c riÃªng. Trong Ä‘Ã³, sá»§i cáº£o cÅ©ng Ä‘Æ°á»£c biáº¿t Ä‘áº¿n nhÆ° má»™t mÃ³n Äƒn miá»n Nam mÃ  ai Äƒn thá»­ cÅ©ng Ä‘á»u mÃª. BÃ¡nh sá»§i cáº£o má»ng dai, nhÃ¢n tÃ´m thá»‹t do ngÆ°á»i gá»‘c Hoa nhÃ o náº·n. NÆ°á»›c dÃ¹ng Ä‘á»ƒ Äƒn vá»›i sá»§i cáº£o Ä‘Æ°á»£c háº§m tá»« xÆ°Æ¡ng á»‘ng vÃ  cá»§ cáº£i tráº¯ng nÃªn cÃ³ vá»‹ ngá»t thanh, ngon nhá»©c nhá»‘i. Sá»§i cáº£o SÃ i GÃ²n - mÃ³n ngon miá»n Nam gÃ¢y ghiá»n cho nhiá»u ngÆ°á»i (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('PhÃ¡ láº¥u SÃ i GÃ²n', 'pha-lau-sai-gon', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image26.jpg', 'Dáº¡o quanh SÃ i GÃ²n, báº¡n ráº¥t dá»… báº¯t gáº·p nhá»¯ng hÃ ng quÃ¡n bÃ¡n phÃ¡ láº¥u. ÄÃ¢y cÅ©ng lÃ  mÃ³n ngon miá»n Nam mÃ  biáº¿t bao tháº¿ há»‡ Ä‘á»u Æ°a chuá»™ng. PhÃ¡ láº¥u Ä‘Æ°á»£c cháº¿ biáº¿n tá»« lÃ²ng bÃ², lÃ²ng heo, dáº¡ dÃ y, lÃ¡ sÃ¡ch,... Äá»ƒ dáº­y mÃ¹i thÆ¡m, phÃ¡ láº¥u Ä‘Æ°á»£c náº¥u vá»›i nÆ°á»›c dá»«a cÃ¹ng cÃ¡c loáº¡i gia vá»‹, thuá»‘c báº¯c,... Má»™t bÃ¡t phÃ¡ láº¥u sÃ³ng sÃ¡nh vá»›i mÃ u nÃ¢u cÃ¡nh giÃ¡n Äƒn kÃ¨m bÃ¡nh mÃ¬ nÃ³ng há»•i, giÃ²n rá»¥m khÃ´ng má»™t ai cÃ³ thá»ƒ chá»‘i tá»«. PhÃ¡ láº¥u SÃ i GÃ²n - chinh phá»¥c má»i thá»±c khÃ¡ch (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ² tÆ¡ TÃ¢y Ninh', 'bo-to-tay-ninh', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image23.jpg', 'MÃ³n ngon miá»n Nam khÃ´ng thá»ƒ thiáº¿u nhá»¯ng mÃ³n Äƒn cá»§a vÃ¹ng Ä‘áº¥t TÃ¢y Ninh, Ä‘áº·c biá»‡t nháº¥t pháº£i ká»ƒ Ä‘áº¿n lÃ  mÃ³n bÃ² tÆ¡. Thá»‹t bÃ² tÆ¡ Ä‘Æ°á»£c táº©m Æ°á»›p gia vá»‹ vÃ  nÆ°á»›ng trÃªn lá»­a há»“ng dáº­y mÃ¹i thÆ¡m khÃ³ cÆ°á»¡ng vá»›i báº¥t ká»³ ai. Thá»‹t bÃ² TÃ¢y Ninh sÄƒn cháº¯c, dai má»m. Lá»›p thá»‹t bÃªn ngoÃ i chÃ¡y xÃ©m nhÆ°ng bÃªn trong há»“ng Ä‘á», thá»‹t ngon ngá»t ráº¥t háº¥p dáº«n. BÃ² tÆ¡ TÃ¢y Ninh - mÃ³n ngon Ä‘áº·c sáº£n cá»§a ngÆ°á»i miá»n Nam (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh canh Tráº£ng BÃ ng, TÃ¢y Ninh', 'banh-canh-trang-bang-tay-ninh', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image34.png', 'CÅ©ng lÃ  mÃ³n ngon miá»n Nam á»Ÿ TÃ¢y Ninh, bÃ¡nh canh Tráº£ng BÃ ng nháº­n Ä‘Æ°á»£c khÃ´ng Ã­t lá»i khen tá»« thá»±c khÃ¡ch. NÆ°á»›c dÃ¹ng cá»§a bÃ¡nh canh Ä‘Æ°á»£c háº§m ká»¹ tá»« cÃ¡c loáº¡i rau cá»§, xÆ°Æ¡ng heo, cÃ³ thá»ƒ thÃªm má»±c khÃ´, ngÃ² gai,... Sá»£i bÃ¡nh canh Ä‘Æ°á»£c lÃ m tá»« má»™t loáº¡i gáº¡o Ä‘áº·c biá»‡t lÃ  gáº¡o MiÃªn nÃªn sá»£i bÃ¡nh canh tráº¯ng, dáº»o. Má»™t Ä‘iá»u quan trá»ng lÃ m nÃªn vá»‹ ngon cá»§a bÃ¡nh canh Tráº£ng BÃ ng chÃ­nh lÃ  mÃ³ng giÃ² heo luá»™c vÃ  nÆ°á»›c máº¯m chua cay máº·n ngá»t. BÃ¡nh canh Tráº£ng BÃ ng cÅ©ng lÃ  mÃ³n ngon miá»n Nam mÃ  báº¡n nÃªn Äƒn thá»­ (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Tháº±n láº±n nÃºi BÃ  Äen, TÃ¢y Ninh', 'than-lan-nui-ba-den-tay-ninh', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image14.jpg', 'Nháº¯c Ä‘áº¿n mÃ³n tháº±n láº±n nÃºi BÃ  Äen cÃ³ láº½ nhiá»u ngÆ°á»i sáº½ cáº£m tháº¥y e ngáº¡i khi thÆ°á»Ÿng thá»©c tháº¿ nhÆ°ng Ä‘Ã¢y láº¡i lÃ  má»™t mÃ³n ngon miá»n Nam trá»© danh. Tháº±n láº±n nÃºi BÃ  Äen ráº¥t bá»• dÆ°á»¡ng bá»Ÿi thá»©c Äƒn cá»§a chÃºng lÃ  sung, chuá»‘i, cÃ¢y thuá»‘c nam. Thá»‹t tháº±n láº±n cÃ³ thá»ƒ cháº¿ biáº¿n thÃ nh mÃ³n nÆ°á»›ng, chiÃªn hoáº·c náº¥u chÃ¡o. Náº¿u cÃ³ dá»‹p du lá»‹ch TÃ¢y Ninh báº¡n nhá»› Äƒn thá»­ mÃ³n Äƒn nÃ y Ä‘á»ƒ cáº£m nháº­n nhÃ©. Tháº±n láº±n nÃºi BÃ  Äen - mÃ³n ngon TÃ¢y Ninh (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('ThÃ¨o lÃ¨o TÃ¢y Ninh', 'theo-leo-tay-ninh', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image37.jpg', 'ThÃ¨o lÃ¨o TÃ¢y Ninh lÃ  mÃ³n Äƒn váº·t giá»‘ng nhÆ° káº¹o láº¡c. ÄÃ¢y lÃ  mÃ³n ngon miá»n Nam Ä‘Æ°á»£c cháº¿ biáº¿n tá»« cÃ¡c nguyÃªn liá»‡u nhÆ° Ä‘áº­u phá»™ng, háº¡t mÃ¨, máº¡ch nha nÃªn cÃ³ vá»‹ ngá»t thanh vÃ  ráº¥t bÃ¹i. MÃ³n thÃ¨o lÃ¨o dÃ¢n dÃ£, ngá»t ngÃ o khÃ´ng chá»‰ Ä‘Æ°á»£c ngÆ°á»i dÃ¢n thÃ­ch thÃº mÃ  cÃ²n lÃ  thá»©c quÃ  Ä‘áº·c sáº£n Ä‘Æ°á»£c nhiá»u khÃ¡ch du lá»‹ch tÃ¬m mua Ä‘á»ƒ lÃ m quÃ  biáº¿u báº¡n bÃ¨, ngÆ°á»i thÃ¢n. ThÃ¨o lÃ¨o TÃ¢y Ninh - mÃ³n ngon Ä‘áº·c sáº£n miá»n Nam (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('CÃ¡ lÃ³c nÆ°á»›ng trui Cáº§n ThÆ¡', 'ca-loc-nuong-trui-can-tho', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image21.jpg', 'MÃ³n ngon miá»n Nam khÃ´ng thá»ƒ khÃ´ng cÃ³ cÃ¡ lÃ³c nÆ°á»›ng trui. Náº¿u cÃ³ dá»‹p Ä‘áº¿n Cáº§n ThÆ¡, báº¡n nháº¥t Ä‘á»‹nh pháº£i thá»­ mÃ³n Äƒn nÃ y. CÃ¡ lÃ³c nÆ°á»›ng trui Ä‘Æ°á»£c cháº¿ biáº¿n tá»« cÃ¡ lÃ³c Ä‘á»“ng vÃ  nÆ°á»›ng báº±ng rÆ¡m. CÃ¡ lÃ³c chÃ­n dáº­y mÃ¹i thÆ¡m ná»©c. Sau khi nÆ°á»›ng chÃ­n, cÃ¡ lÃ³c Ä‘Æ°á»£c xáº» thá»‹t vÃ  rÆ°á»›i má»¡ hÃ nh, Ä‘áº­u phá»™ng lÃªn trÃªn. Thá»‹t cÃ¡ lÃ³c má»m, ngá»t tá»± nhiÃªn Ä‘Æ°á»£c cuá»‘n bÃ¡nh trÃ¡ng kÃ¨m rau sá»‘ng, cháº¥m máº¯m nÃªm, cháº¯c cháº¯n cÃ ng Äƒn cÃ ng nghiá»‡n. CÃ¡ lÃ³c nÆ°á»›ng trui lÃ  má»™t mÃ³n Äƒn Ä‘áº·c sáº¯c cá»§a ngÆ°á»i dÃ¢n Nam Bá»™ (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh Ä‘Ãºc lÃ¡ dá»©a Cáº§n ThÆ¡', 'banh-duc-la-dua-can-tho', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image4.jpg', 'Trong cÃ¡c mÃ³n ngon miá»n Nam thÃ¬ bÃ¡nh Ä‘Ãºc lÃ¡ dá»©a Cáº§n ThÆ¡ cÅ©ng Ä‘Æ°á»£c nhiá»u ngÆ°á»i biáº¿t Ä‘áº¿n. BÃ¡nh Ä‘Ãºc má»m máº¡i, cÃ³ mÃ u xanh mÃ  thoáº£ng mÃ¹i thÆ¡m cá»§a lÃ¡ dá»©a. Äá»ƒ mÃ³n Äƒn thÃªm trÃ²n vá»‹, báº¡n hÃ£y rÆ°á»›i thÃªm nÆ°á»›c cá»‘t dá»«a thÆ¡m lá»«ng, bÃ©o ngáº­y cÃ¹ng Ä‘áº­u phá»™ng giÃ£ nhuyá»…n. CÃ³ thá»ƒ tháº¥y, bÃ¡nh Ä‘Ãºc lÃ¡ dá»©a lÃ  mÃ³n Äƒn dÃ¢n dÃ£, gáº¯n liá»n vá»›i tuá»•i thÆ¡ cá»§a bao ngÆ°á»i dÃ¢n Cáº§n ThÆ¡ vÃ  cÅ©ng lÃ  mÃ³n bÃ¡nh Ä‘áº·c sáº£n mÃ  ai cÅ©ng nÃªn thá»­ má»™t láº§n. BÃ¡nh Ä‘Ãºc lÃ¡ dá»©a - mÃ³n ngon miá»n Nam ráº¥t Ä‘Ã¡ng thá»­ (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh xÃ¨o Cáº§n ThÆ¡', 'banh-xeo-can-tho', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image19.jpg', 'ThÃªm má»™t mÃ³n ngon miá»n Nam Ä‘Æ°á»£c ráº¥t nhiá»u du khÃ¡ch khÃ´ng ngá»›t lá»i khen lÃ  bÃ¡nh xÃ¨o Cáº§n ThÆ¡. BÃ¡nh xÃ¨o cÃ³ vá» vÃ ng Æ°Æ¡m, giÃ²n rá»¥m vá»›i lá»›p nhÃ¢n bÃªn trong cÃ³ thá»‹t heo, thá»‹t gÃ , tÃ´m, tÃ©p, Ä‘u Ä‘á»§, Ä‘áº­u xanh,... Äá»ƒ tÄƒng hÆ°Æ¡ng vá»‹, chá»‘ng ngÃ¡n, bÃ¡nh xÃ¨o thÆ°á»ng Ä‘Æ°á»£c Äƒn kÃ¨m vá»›i rau rá»«ng vÃ  nÆ°á»›c máº¯m chua ngá»t. BÃ¡nh xÃ¨o cÅ©ng lÃ  Ä‘áº·c sáº£n cá»§a miá»n TÃ¢y Nam Bá»™ (Nguá»“n: Internet) &gt;&gt; Xem thÃªm: TOP 9 QUÃN BÃNH XÃˆO SIÃŠU NGON á»ž SÃ€I GÃ’N Gáº¦N ÄÃ‚Y, Ä‚N LÃ€ GHIá»€N', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh cá»‘ng Cáº§n ThÆ¡', 'banh-cong-can-tho', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image32.jpg', 'BÃ¡nh cá»‘ng Cáº§n ThÆ¡ - nghe cÃ¡i tÃªn cÅ©ng tháº­t láº¡ nhÆ°ng nguyÃªn liá»‡u mÃ³n Äƒn láº¡i ráº¥t quen thuá»™c, bao gá»“m bá»™t gáº¡o, bá»™t náº¿p, Ä‘áº­u xanh, tÃ´m, thá»‹t,... BÃ¡nh cá»‘ng chiÃªn chÃ­n vÃ ng, giÃ²n Ä‘á»u Ä‘Æ°á»£c Äƒn vá»›i rau sá»‘ng cháº¥m nÆ°á»›c máº¯m chua ngá»t. Vá»›i ngÆ°á»i dÃ¢n Cáº§n ThÆ¡ thÃ¬ bÃ¡nh cá»‘ng lÃ  má»™t thá»©c quÃ  Äƒn váº·t vÃ o buá»•i chiá»u vÃ  cÅ©ng lÃ  mÃ³n ngon miá»n Nam mÃ  nhiá»u khÃ¡ch du lá»‹ch tÃ¬m kiáº¿m Ä‘á»ƒ thÆ°á»Ÿng thá»©c. BÃ¡nh cá»‘ng Cáº§n ThÆ¡ vá»›i nhÃ¢n tÃ´m thá»‹t siÃªu ngon (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Chuá»‘i quáº¿t dá»«a Tiá»n Giang', 'chuoi-quet-dua-tien-giang', 'https://statics.vincom.com.vn/xu-huong/image13-1668651252.jpg', 'Tiá»n Giang lÃ  má»™t trong nhá»¯ng tá»‰nh sÃ´ng nÆ°á»›c thu hÃºt ráº¥t nhiá»u du khÃ¡ch Ä‘áº¿n tham quan. NgoÃ i cáº£nh Ä‘áº¹p thÃ¬ Tiá»n Giang cÃ²n lÃ m phong phÃº cho danh sÃ¡ch mÃ³n ngon miá»n Nam vá»›i nhiá»u mÃ³n Ä‘áº·c sáº£n háº¥p dáº«n. Äáº§u tiÃªn pháº£i nháº¯c Ä‘áº¿n lÃ  chuá»‘i quáº¿t dá»«a. MÃ³n Äƒn váº·t nÃ y cÃ³ mÃ¹i thÆ¡m tá»± nhiÃªn cá»§a chuá»‘i hoÃ  quyá»‡n vá»›i vá»‹ bÃ©o ngáº­y cá»§a dá»«a. Nghe tÃªn chuá»‘i quáº¿t dá»«a lÃ  mÃ³n ngá»t, tháº¿ nhÆ°ng Ä‘Ã¢y láº¡i lÃ  mÃ³n máº·n, Äƒn kÃ¨m vá»›i rau sá»‘ng, cháº¥m nÆ°á»›c máº¯m chua ngá»t. CÃ³ váº» ngÆ°á»£c Ä‘á»i nhÆ°ng sá»± káº¿t há»£p nÃ y Ä‘Ã£ giÃºp chuá»‘i quáº¿t dá»«a trá»Ÿ thÃ nh mÃ³n Ä‘á»™c nháº¥t vÃ´ nhá»‹, chá»‰ cÃ³ á»Ÿ Tiá»n Giang. Chuá»‘i quáº¿t dá»«a thÆ¡m ngon cá»§a vÃ¹ng Ä‘áº¥t Tiá»n Giang (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh táº§m CÃ  Mau', 'banh-tam-ca-mau', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image9.jpg', 'CÃ  Mau lÃ  vÃ¹ng Ä‘áº¥t táº­n cÃ¹ng phÃ­a nam cá»§a tá»• quá»‘c ná»•i tiáº¿ng vá»›i mÃ³n bÃ¡nh táº§m. ChÃ­nh bá»Ÿi hÆ°Æ¡ng vá»‹ thÆ¡m ngon nÃªn mÃ³n ngon miá»n Nam nÃ y cÅ©ng ráº¥t Ä‘Æ°á»£c lÃ²ng nhiá»u du khÃ¡ch khi Ä‘áº¿n Ä‘Ã¢y. BÃ¡nh táº§m cÃ²n cÃ³ tÃªn gá»i khÃ¡c lÃ  bÃ¡nh táº±m bá»Ÿi sá»£i bÃ¡nh Ä‘Æ°á»£c lÃ m tá»« bá»™t gáº¡o, cÃ³ hÃ¬nh dáº¡ng nhÆ° nhá»¯ng con táº±m. Sá»£i bÃ¡nh dai dai má»m má»m Ä‘Æ°á»£c Äƒn vá»›i sá»‘t cÃ  ri gÃ , thá»‹t gÃ , xÃ­u máº¡i, giÃ¡ Ä‘á»—, rau hÃºng quáº¿, muá»‘i tiÃªu chanh. Sá»± káº¿t há»£p cá»§a cÃ¡c hÆ°Æ¡ng vá»‹ máº·n ngá»t, chua, cay khiáº¿n báº¡n cÃ³ thá»ƒ Äƒn no máº·c khÃ´ng há» ngÃ¡n. Äá»™c Ä‘Ã¡o bÃ¡nh táº§m CÃ  Mau (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Cua Ä‘Ã¡ rang muá»‘i CÃ  Mau', 'cua-da-rang-muoi-ca-mau', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image6.jpg', 'CÃ  Mau vá»‘n ná»•i tiáº¿ng vá»›i mÃ³n cua Ä‘Ã¡ tÆ°Æ¡i ngon. Loáº¡i cua nÃ y thÆ°á»ng sá»‘ng á»Ÿ nhá»¯ng gÃ nh Ä‘Ã¡, bÃ£i Ä‘Ã¡, cá»­a sÃ´ng thÃ´ng ra biá»ƒn. Cua Ä‘Ã¡ cÃ³ thá»ƒ cháº¿ biáº¿n thÃ nh nhiá»u mÃ³n Äƒn, trong Ä‘Ã³ cua Ä‘Ã¡ rang muá»‘i cÅ©ng Ä‘Æ°á»£c Ä‘Æ°a vÃ o danh sÃ¡ch cÃ¡c mÃ³n ngon miá»n Nam mÃ  khÃ¡ch tháº­p phÆ°Æ¡ng Ä‘áº¿n Ä‘Ã¢y Ä‘á»u muá»‘n thÆ°á»Ÿng thá»©c. Rang muá»‘i giÃºp háº¡n cháº¿ Ä‘Æ°á»£c sá»± tÃ¡c Ä‘á»™ng cá»§a nhiá»‡t vÃ o bÃªn trong nÃªn thá»‹t cua giá»¯ Ä‘Æ°á»£c vá»‹ ngá»t, thá»‹t dai cháº¯c, cÃ³ chÃºt bÃ¹i, bÃ©o. Cua Ä‘Ã¡ rang muá»‘i sáº½ trá»Ÿ nÃªn ngon hÆ¡n khi Äƒn kÃ¨m vá»›i muá»‘i tiÃªu chanh. Cua Ä‘Ã¡ rang muá»‘i CÃ  Mau tÆ°Æ¡i ngon, láº¡ miá»‡ng (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃºn cÃ¡ ChÃ¢u Äá»‘c', 'bun-ca-chau-doc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image2.jpg', 'MÃ³n ngon miá»n Nam khÃ¡c khÃ´ng thá»ƒ khÃ´ng nháº¯c Ä‘áº¿n á»Ÿ Ä‘Ã¢y chÃ­nh lÃ  bÃºn cÃ¡ ChÃ¢u Äá»‘c. Thá»±c ra, Ä‘Ã¢y lÃ  mÃ³n Äƒn cÃ³ nguá»“n gá»‘c tá»« Campuchia, sau Ä‘Ã³ Ä‘Æ°á»£c biáº¿n táº¥u Ä‘á»ƒ phÃ¹ há»£p hÆ¡n vá»›i kháº©u vá»‹ ngÆ°á»i dÃ¢n Ä‘á»‹a phÆ°Æ¡ng. BÃºn cÃ¡ ChÃ¢u Äá»‘c Ä‘Æ°á»£c náº¥u tá»« cÃ¡ lÃ³c Ä‘á»“ng, thá»‹t cÃ¡ cháº¯c, ngá»t tá»± nhiÃªn vÃ  cá»§ ngáº£i bÃºn, máº¯m ruá»‘c. Bá»Ÿi váº­y, nÆ°á»›c dÃ¹ng bÃºn ráº¥t Ä‘áº­m Ä‘Ã , cÃ³ mÃ¹i thÆ¡m Ä‘áº·c trÆ°ng mÃ  khÃ´ng tanh, Ä‘Æ°á»£c Äƒn vá»›i bÃºn vÃ  cÃ¡c loáº¡i rau nhÆ° báº¯p chuá»‘i, rau muá»‘ng bÃ o, bÃ´ng Ä‘iÃªn Ä‘iá»ƒn,... BÃºn cÃ¡ ChÃ¢u Äá»‘c - mÃ³n ngon miá»n Nam nÃªn Äƒn (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh canh tÃ©p ChÃ¢u Äá»‘c', 'banh-canh-tep-chau-doc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image35.png', 'BÃ¡nh canh tÃ©p ChÃ¢u Äá»‘c vá»›i cÃ´ng thá»©c cháº¿ biáº¿n ráº¥t Ä‘Æ¡n giáº£n nhÆ°ng Ä‘Ã£ trá»Ÿ thÃ nh mÃ³n ngon miá»n Nam Ä‘i vÃ o lÃ²ng thá»±c khÃ¡ch. NÆ°á»›c dÃ¹ng Ä‘á»ƒ náº¥u bÃ¡nh canh Ä‘Æ°á»£c ninh tá»« xÆ°Æ¡ng heo, má»±c khÃ´, tÃ´m khÃ´, náº¥m. Äáº·c biá»‡t, mÃ³n bÃ¡nh canh tÃ©p khÃ´ng Äƒn kÃ¨m thá»‹t nhÆ° bÃ¡nh canh thÃ´ng thÆ°á»ng mÃ  Ä‘Æ°á»£c thay tháº¿ báº±ng tÃ©p rang. Vá»‹ ngá»t thanh cá»§a nÆ°á»›c dÃ¹ng, vá»‹ máº·n cá»§a tÃ©p, vá»‹ cay cá»§a á»›t vÃ  vá»‹ chua cá»§a chanh hÃ²a quyá»‡n khÃ©o lÃ©o vÃ o sá»£i bÃ¡nh canh má»m dáº»o táº¡o nÃªn má»™t hÆ°Æ¡ng vá»‹ Ä‘áº·c trÆ°ng, khÃ³ láº«n. BÃ¡nh canh tÃ©p ChÃ¢u Äá»‘c - mÃ³n Äƒn dÃ¢n dÃ£ miá»n Nam (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('GÃ  Ä‘á»‘t Tri TÃ´n', 'ga-dot-tri-ton', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image12.jpg', 'Äáº¿n Tri TÃ´n (An Giang) báº¡n khÃ´ng chá»‰ Ä‘Æ°á»£c check-in nhiá»u Ä‘iá»ƒm du lá»‹ch ná»•i tiáº¿ng mÃ  cÃ²n Ä‘Æ°á»£c khÃ¡m phÃ¡ thÃªm nhiá»u mÃ³n ngon miá»n Nam, Ä‘áº·c biá»‡t nháº¥t cÃ³ láº½ lÃ  mÃ³n gÃ  Ä‘á»‘t. Äá»ƒ Æ°á»›p gÃ , ngoÃ i nhá»¯ng gia vá»‹ quen thuá»™c nhÆ° sáº£, á»›t, tá»i, muá»‘i,...thÃ¬ lÃ¡ chÃºc chÃ­nh lÃ  gia vá»‹ lÃ m nÃªn nÃ©t riÃªng cho mÃ³n Äƒn nÃ y. Thá»‹t gÃ  ta dai giÃ²n, ngon ngá»t, thÆ¡m lá»«ng cá»§a mÃ¹i lÃ¡ chÃºc Ä‘Ã£ khiáº¿n khÃ´ng Ã­t ngÆ°á»i váº¥n vÆ°Æ¡ng, lÆ°u luyáº¿n. GÃ  Ä‘á»‘t Tri TÃ´n - mÃ³n ngon khÃ³ cÃ³ thá»ƒ chá»‘i tá»« (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Äu Ä‘á»§ Ä‘Ã¢m Tri TÃ´n', 'du-du-dam-tri-ton', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image3.png', 'Vá» An Giang, báº¡n cÅ©ng Ä‘á»«ng quÃªn thÆ°á»Ÿng thá»©c mÃ³n Ä‘u Ä‘á»§ Ä‘Ã¢m - mÃ³n ngon miá»n Nam cá»§a Ä‘á»“ng bÃ o Khmer. Äu Ä‘á»§ Ä‘Ã¢m vá»‘n Ä‘Æ°á»£c biáº¿n táº¥u tá»« gá»i Ä‘u Ä‘á»§ cá»§a ngÆ°á»i Campuchia. NgoÃ i thÃ nh pháº§n Ä‘u Ä‘á»§ thÃ¬ mÃ³n Äƒn nÃ y cÃ²n cÃ³ ba khÃ­a, Ä‘áº­u Ä‘Å©a, rau muá»‘ng, cÃ  chua, hÃ nh cá»§, rau thÆ¡m cÃ¹ng cÃ¡c gia vá»‹ nhÆ° ruá»‘c, Ä‘Æ°á»ng, Ä‘áº­u phá»™ng. Sá»Ÿ dÄ© mÃ³n Äƒn cÃ³ tÃªn gá»i lÃ  Ä‘u Ä‘á»§ Ä‘Ã¢m bá»Ÿi cÃ¡c nguyÃªn liá»‡u trÆ°á»›c khi cháº¿ biáº¿n Ä‘á»u Ä‘Æ°á»£c Ä‘Ã¢m nháº¹ trong cá»‘i. Vá»›i cÃ¡ch cháº¿ biáº¿n Ä‘á»™c Ä‘Ã¡o, hÆ°Æ¡ng vá»‹ láº¡ miá»‡ng, mÃ³n Äƒn dÃ¢n dÃ£ nÃ y cÅ©ng Ä‘Ã£ Ä‘á»ƒ láº¡i áº¥n tÆ°á»£ng khÃ³ quÃªn cho nhiá»u du khÃ¡ch. Äu Ä‘á»§ Ä‘Ã¢m Tri TÃ´n - mÃ³n ngon An Giang nÃªn Äƒn thá»­ (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('QuÃ½t CÃ¡i BÃ¨ - Äáº·c sáº£n ná»•i tiáº¿ng miá»n TÃ¢y', 'quyt-cai-be-dac-san-noi-tieng-mien-tay', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image17.jpg', 'QuÃ½t CÃ¡i BÃ¨ (Tiá»n Giang) phá»• biáº¿n nháº¥t lÃ  loáº¡i quÃ½t Ä‘Æ°á»ng. QuÃ½t CÃ¡i BÃ¨ vá» má»ng, chÃ­n vÃ ng, má»ng nÆ°á»›c vÃ  cÃ³ vá»‹ ngá»t thanh, hÆ°Æ¡ng thÆ¡m lan tá»a. CÃ³ láº½ vÃ¬ hÆ°Æ¡ng vá»‹ Ä‘áº·c biá»‡t nhÆ° váº­y mÃ  giá»¯a vÆ°Æ¡ng quá»‘c trÃ¡i cÃ¢y miá»n TÃ¢y, quÃ½t CÃ¡i BÃ¨ váº«n cÃ³ vá»‹ trÃ­ vá»¯ng cháº¯c vÃ  lÃ  Ä‘áº·c sáº£n miá»n Nam Ä‘Æ°á»£c nhiá»u du khÃ¡ch tÃ¬m mua Ä‘á»ƒ lÃ m quÃ . QuÃ½t CÃ¡i BÃ¨ ná»•i tiáº¿ng kháº¯p cáº£ nÆ°á»›c (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('RÆ°á»£u sim PhÃº Quá»‘c', 'ruou-sim-phu-quoc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image1.jpg', 'Báº¡n Ä‘áº¿n PhÃº Quá»‘c nhÆ°ng chÆ°a biáº¿t mua gÃ¬ lÃ m quÃ  thÃ¬ rÆ°á»£u sim cÅ©ng lÃ  má»™t gá»£i Ã½. RÆ°á»£u sim Ä‘Æ°á»£c lÃªn men ká»¹ lÆ°á»¡ng tá»« sim rá»«ng, cÃ³ vá»‹ ngá»t chÃ¡t, thÆ¡m ná»©c. Äáº·c biá»‡t, rÆ°á»£u sim cÅ©ng ráº¥t tá»‘t cho há»‡ tiÃªu hÃ³a, cáº£i thiá»‡n chá»©ng nhá»©c má»i nÃªn sáº½ ráº¥t tuyá»‡t Ä‘á»ƒ biáº¿u táº·ng ngÆ°á»i thÃ¢n, báº¡n bÃ¨, Ä‘á»‘i tÃ¡c,... RÆ°á»£u sim - mÃ³n quÃ  tá»« PhÃº Quá»‘c (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('TiÃªu chÃ­n PhÃº Quá»‘c', 'tieu-chin-phu-quoc', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image33.jpg', 'CÃ³ thá»ƒ, báº¡n Ä‘Ã£ quÃ¡ quen vá»›i vá»‹ tiÃªu cá»§a vÃ¹ng Ä‘áº¥t tÃ¢y nguyÃªn, tháº¿ nhÆ°ng náº¿u Ä‘Ã£ Ä‘áº¿n PhÃº Quá»‘c thÃ¬ hÃ£y mua vÃ  dÃ¹ng thá»­ tiÃªu cá»§a hÃ²n Ä‘áº£o nÃ y. TiÃªu PhÃº Quá»‘c háº¡t cháº¯c, cay ná»“ng, Ä‘áº­m vá»‹ vÃ  thÆ¡m hÆ¡n tiÃªu Ä‘Æ°á»£c trá»“ng á»Ÿ nhá»¯ng vÃ¹ng Ä‘áº¥t khÃ¡c. Bá»Ÿi váº­y, tiÃªu PhÃº Quá»‘c cÅ©ng nghiá»…m nhiÃªn trá»Ÿ thÃ nh mÃ³n Ä‘áº·c sáº£n miá»n Nam Ä‘Æ°á»£c ráº¥t nhiá»u ngÆ°á»i tÃ¬m mua. TiÃªu chÃ­n PhÃº Quá»‘c - Ä‘áº·c sáº£n miá»n Nam ná»•i tiáº¿ng (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('BÃ¡nh bÃ´ng lan trá»©ng muá»‘i SÃ i GÃ²n', 'banh-bong-lan-trung-muoi-sai-gon', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image5.jpg', 'Cháº¯c cháº¯n, báº¡n sáº½ cáº£m tháº¥y ráº¥t ká»³ láº¡ bá»Ÿi bÃ´ng lan trá»©ng muá»‘i á»Ÿ Ä‘Ã¢u cÅ©ng cÃ³ tháº¿ nhÆ°ng nhiá»u ngÆ°á»i Ä‘áº¿n SÃ i GÃ²n Ä‘á»u Ä‘á»ƒ Äƒn báº±ng Ä‘Æ°á»£c mÃ³n bÃ¡nh nÃ y. Bá»Ÿi, mÃ³n ngon miá»n Nam nÃ y á»Ÿ SÃ i GÃ²n ráº¥t khÃ¡c vá»›i nhiá»u nÆ¡i. Cá»‘t bÃ¡nh thÆ¡m má»m, tan ngay trong miá»‡ng, lá»›p trÃªn cÃ¹ng cá»§a bÃ¡nh Ä‘Æ°á»£c phá»§ má»™t lá»›p trá»©ng muá»‘i bÃ©o máº·n cÃ¹ng ruá»‘c thá»‹t ráº¥t Ä‘áº­m Ä‘Ã . Äá»ƒ cáº£m nháº­n Ä‘Æ°á»£c hÆ°Æ¡ng vá»‹ khÃ¡c biá»‡t nÃ y, báº¡n hÃ£y Äƒn thá»­ má»™t láº§n nhÃ©. BÃ´ng lan trá»©ng muá»‘i - mÃ³n ngon miá»n Nam nÃªn Äƒn thá»­ (Nguá»“n: Internet)', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5),
            ('Nem bÆ°á»Ÿi TÃ¢y Ninh', 'nem-buoi-tay-ninh', 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image16.jpg', 'Má»™t mÃ³n ngon miá»n Nam khÃ¡c mÃ  báº¡n Ä‘á»«ng nÃªn bá» qua khi Ä‘áº¿n TÃ¢y Ninh Ä‘Ã³ lÃ  nem bÆ°á»Ÿi. ÄÃ¢y thá»±c sá»± lÃ  má»™t mÃ³n Äƒn thuáº§n chay vá»›i nguyÃªn liá»‡u chÃ­nh lÃ  vá» bÆ°á»Ÿi da xanh, Ä‘u Ä‘á»§, nÆ°á»›c kháº¿ chua vÃ  má»™t sá»‘ loáº¡i gia vá»‹ khÃ¡c. Nem Ä‘Æ°á»£c gÃ³i trong lÃ¡ vÃ´ng hoáº·c lÃ¡ chÃ¹m ruá»™t táº¡o thÃ nh tá»«ng xÃ¢u, ráº¥t thÃ­ch há»£p Ä‘á»ƒ lÃ m quÃ . ThÆ°á»Ÿng thá»©c má»™t miáº¿ng nem bÆ°á»Ÿi, báº¡n sáº½ cáº£m nháº­n Ä‘Æ°á»£c hÆ°Æ¡ng vá»‹ cÃ³ má»™t khÃ´ng hai, Ä‘Ã³ lÃ  vá»‹ cay tÃª cá»§a bÆ°á»Ÿi, vá»‹ ngá»t giÃ²n cá»§a Ä‘u Ä‘á»§ vÃ  vá»‹ chua nháº¹ cá»§a nÆ°á»›c kháº¿.Â Nem bÆ°á»Ÿi - mÃ³n Äƒn nghe láº¡ nhÆ°ng láº¡i ráº¥t ngon (Nguá»“n: Internet) Vincom vá»«a chia sáº» Ä‘áº¿n báº¡n 40 mÃ³n Äƒn, Ä‘áº·c sáº£n miá»n Nam, má»—i mÃ³n Äƒn á»Ÿ miá»n Nam lÃ  má»™t nÃ©t vÄƒn hÃ³a lÃ¢u Ä‘á»i cá»§a ngÆ°á»i dÃ¢n Ä‘á»‹a phÆ°Æ¡ng, Ä‘áº±ng sau Ä‘Ã³ cÃ³ thá»ƒ lÃ  nhá»¯ng cÃ¢u chuyá»‡n vÃ´ cÃ¹ng Ã½ nghÄ©a. Náº¿u cÃ³ cÆ¡ há»™i, báº¡n hÃ£y Ä‘i, tráº£i nghiá»‡m vÃ  khÃ¡m phÃ¡ háº¿t nhá»¯ng mÃ³n ngon miá»n Nam á»Ÿ má»—i vÃ¹ng Ä‘áº¥t nhÃ©. Báº®T Ká»ŠP Má»ŒI XU HÆ¯á»šNGMá»šI NHáº¤T CÃ™NG Vá»šI NHIá»€U Æ¯U ÄÃƒI Háº¤P DáºªN Má»–I NGÃ€Y! Vui lÃ²ng thá»­ láº¡i, email báº¡n vá»«a nháº­p chÆ°a Ä‘Ãºng. ÄÃ£ yÃªu thÃ­ch ÄÃ³ng ÄÃ£ bá» yÃªu thÃ­ch HoÃ n tÃ¡c ÄÃ³ng ChÃºng tÃ´i thu tháº­p cookies cho cÃ¡c má»¥c Ä‘Ã­ch khÃ¡c nhau nháº±m cáº£i thiá»‡n tráº£i nghiá»‡m khÃ¡ch hÃ ng trÃªn trang web. Báº±ng cÃ¡ch áº¥n vÃ o â€œÄá»“ng Ã½â€, báº¡n Ä‘á»“ng Ã½ vá»›i cookie lÆ°u trá»¯ trÃªn thiáº¿t bá»‹ cá»§a báº¡n. Báº¡n cÃ³ thá»ƒ tá»« chá»‘i báº±ng cÃ¡ch áº¥n vÃ o nÃºt \"KhÃ´ng, cáº£m Æ¡n\". LÆ°u Ã½ ráº±ng, viá»‡c khÃ´ng Ä‘á»“ng Ã½ cÃ³ thá»ƒ áº£nh hÆ°á»Ÿng tá»›i tráº£i nghiá»‡m cá»§a báº¡n trÃªn trang web. NgoÃ i ra, báº¡n cÃ³ thá»ƒ tÃ¬m hiá»ƒu thÃªm vá» cÃ¡ch chÃºng tÃ´i sá»­ dá»¥ng cookies táº¡i ChÃ­nh sÃ¡ch quyá»n riÃªng tÆ° cá»§a chÃºng tÃ´i.', 'NguyÃªn liá»‡u tÃ¹y chá»n', 'CÃ¡ch lÃ m Ä‘ang cáº­p nháº­t', 3, 100000, 30, 4.5)
            ON DUPLICATE KEY UPDATE 
            hinh_anh = VALUES(hinh_anh), mo_ta_ngan = VALUES(mo_ta_ngan), nguyen_lieu = VALUES(nguyen_lieu), cach_lam = VALUES(cach_lam);";
            
            $this->conn->exec($sql_dishes);

            // =========================================================================
            // CHUáº¨N HÃ“A VÃ€ THÃŠM Äáº¦Y Äá»¦ 8 MÃ“N Äáº¶C Sáº¢N TP. Há»’ CHÃ MINH THEO YÃŠU Cáº¦U
            // =========================================================================
            try {
                $hcmDishes = [
                    [
                        'ten_mon' => 'CÆ¡m táº¥m SÃ i GÃ²n',
                        'slug' => 'com-tam-sai-gon',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image18.jpg',
                        'mo_ta_ngan' => 'CÆ¡m táº¥m Äƒn cÃ¹ng sÆ°á»n nÆ°á»›ng, bÃ¬, cháº£ vÃ  nÆ°á»›c máº¯m.',
                        'nguyen_lieu' => 'Gáº¡o táº¥m thÆ¡m dáº»o, sÆ°á»n cá»‘t láº¿t nÆ°á»›ng máº­t ong sáº£, bÃ¬ heo trá»™n thÃ­nh thÆ¡m, cháº£ trá»©ng háº¥p, trá»©ng á»‘p la, dÆ°a leo, cÃ  chua, má»¡ hÃ nh tÃ©p má»¡, nÆ°á»›c máº¯m tá»i á»›t chua ngá»t.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Æ¯á»›p & NÆ°á»›ng sÆ°á»n): SÆ°á»n cá»‘t láº¿t dáº§n má»m, Æ°á»›p cÃ¹ng sáº£, máº­t ong, nÆ°á»›c máº¯m ngon, hÃ nh tÃ­m vÃ  tá»i bÄƒm. NÆ°á»›ng trÃªn than hoa Ä‘á» rá»±c cho chÃ¡y cáº¡nh vÃ ng ruá»™m thÆ¡m ná»©c mÅ©i.\nâ€¢ BÆ°á»›c 2 (Náº¥u cÆ¡m & LÃ m cháº£): Gáº¡o táº¥m ngÃ¢m ná»Ÿ náº¥u chÃ­n rÃ¡o háº¡t. Cháº£ trá»©ng trá»™n thá»‹t xay, náº¥m mÃ¨o, bÃºn tÃ u vÃ  lÃ²ng Ä‘á» trá»©ng háº¥p chÃ­n vÃ ng.\nâ€¢ BÆ°á»›c 3 (Trá»™n bÃ¬ & Má»¡ hÃ nh): BÃ¬ heo luá»™c thÃ¡i chá»‰ trá»™n thÃ­nh gáº¡o rang. Tháº¯ng má»¡ heo giÃ²n rá»¥m rÆ°á»›i hÃ nh lÃ¡ cáº¯t nhá».\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Bá»›i cÆ¡m táº¥m ra Ä‘Ä©a, xáº¿p sÆ°á»n, bÃ¬, cháº£, trá»©ng á»‘p la, dÆ°a leo cÃ  chua, rÆ°á»›i má»¡ hÃ nh vÃ  chan nÆ°á»›c máº¯m chua ngá»t Ä‘áº­m Ä‘Ã .',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 45000,
                        'thoi_gian_nau' => 40,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh mÃ¬ SÃ i GÃ²n',
                        'slug' => 'banh-mi-sai-gon',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-1-tiem-banh-mi/image18.png',
                        'mo_ta_ngan' => 'BÃ¡nh mÃ¬ giÃ²n vá»›i thá»‹t, pate, Ä‘á»“ chua vÃ  rau.',
                        'nguyen_lieu' => 'BÃ¡nh mÃ¬ vá» giÃ²n ruá»™t xá»‘p, pate gan heo bÃ©o ngáº­y, bÆ¡ trá»©ng gÃ  thÆ¡m lá»«ng, thá»‹t nguá»™i, cháº£ lá»¥a, xÃ¡ xÃ­u, Ä‘á»“ chua (cÃ  rá»‘t, cá»§ cáº£i), dÆ°a leo, ngÃ² rÃ­, á»›t hiá»ƒm, sá»‘t xÃ¡ xÃ­u Ä‘áº­m Ä‘Ã .',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Chuáº©n bá»‹ bÃ¡nh mÃ¬): NÆ°á»›ng bÃ¡nh mÃ¬ trÃªn lÃ² cho vá» nÃ³ng giÃ²n rá»¥m.\nâ€¢ BÆ°á»›c 2 (Pháº¿t pate & bÆ¡): Dá»c bá»¥ng bÃ¡nh mÃ¬, pháº¿t Ä‘á»u má»™t lá»›p bÆ¡ trá»©ng gÃ  vÃ ng Æ°Æ¡m vÃ  lá»›p pate gan heo thÆ¡m bÃ©o.\nâ€¢ BÆ°á»›c 3 (Káº¹p nhÃ¢n): Xáº¿p Ä‘á»u cÃ¡c lÃ¡t cháº£ lá»¥a, thá»‹t nguá»™i, dÆ°a leo tÆ°Æ¡i giÃ²n vÃ  Ä‘á»“ chua ngÃ¢m giáº¥m Ä‘Æ°á»ng.\nâ€¢ BÆ°á»›c 4 (NÃªm sá»‘t): RÆ°á»›i nÆ°á»›c sá»‘t thá»‹t xÃ¡ xÃ­u Ä‘áº­m Ä‘Ã , ráº¯c vÃ i cá»ng ngÃ² rÃ­ vÃ  lÃ¡t á»›t cay ná»“ng rá»“i thÆ°á»Ÿng thá»©c ngay.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 25000,
                        'thoi_gian_nau' => 15,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'PhÃ¡ láº¥u bÃ²',
                        'slug' => 'pha-lau-bo',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image26.jpg',
                        'mo_ta_ngan' => 'Ná»™i táº¡ng bÃ² náº¥u nÆ°á»›c dá»«a vÃ  gia vá»‹.',
                        'nguyen_lieu' => 'LÃ²ng bÃ² tÆ°Æ¡i (lÃ¡ sÃ¡ch, tá»• ong, khÄƒn lÃ´ng, phÃ¨o non), nÆ°á»›c dá»«a tÆ°Æ¡i, nÆ°á»›c cá»‘t dá»«a, hoa há»“i, quáº¿, Ä‘inh hÆ°Æ¡ng, ngÅ© vá»‹ hÆ°Æ¡ng, bá»™t cÃ  ri, tá»i á»›t, trÃ¡i táº¯c, nÆ°á»›c máº¯m Ä‘Æ°á»ng.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ lÃ²ng bÃ²): LÃ²ng bÃ² bÃ³p ká»¹ nhiá»u láº§n vá»›i muá»‘i háº¡t, giáº¥m vÃ  rÆ°á»£u gá»«ng rá»“i luá»™c sÆ¡ cáº¡o sáº¡ch bá»t nhá»›t.\nâ€¢ BÆ°á»›c 2 (Æ¯á»›p gia vá»‹): Æ¯á»›p lÃ²ng bÃ² cÃ¹ng ngÅ© vá»‹ hÆ°Æ¡ng, bá»™t cÃ  ri, hÃ nh tá»i bÄƒm, tiÃªu xay vÃ  nÆ°á»›c máº¯m trong 45 phÃºt cho tháº¥m Ä‘á»u.\nâ€¢ BÆ°á»›c 3 (Háº§m nÆ°á»›c dá»«a): Phi thÆ¡m hÃ nh tá»i xÃ o sÄƒn lÃ²ng bÃ², Ä‘á»• nÆ°á»›c dá»«a tÆ°Æ¡i vÃ o ninh lá»­a nhá» Ä‘áº¿n khi lÃ²ng chÃ­n má»m giÃ²n sáº§n sáº­t. ThÃªm nÆ°á»›c cá»‘t dá»«a táº¡o Ä‘á»™ bÃ©o ngáº­y sÃ¡nh má»‹n.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): MÃºc phÃ¡ láº¥u nÃ³ng há»•i ra chÃ©n kÃ¨m rau rÄƒm, cháº¥m nÆ°á»›c máº¯m táº¯c chua ngá»t cay xÃ© lÆ°á»¡i, Äƒn cÃ¹ng bÃ¡nh mÃ¬ giÃ²n tan.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 35000,
                        'thoi_gian_nau' => 60,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Há»§ tiáº¿u Nam Vang',
                        'slug' => 'hu-tieu-nam-vang',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image8.jpg',
                        'mo_ta_ngan' => 'Há»§ tiáº¿u Äƒn vá»›i thá»‹t heo, tÃ´m vÃ  nÆ°á»›c dÃ¹ng.',
                        'nguyen_lieu' => 'Sá»£i há»§ tiáº¿u dai Nam Vang, thá»‹t náº¡c heo bÄƒm, tÃ´m sÃº tÆ°Æ¡i luá»™c, tim heo, gan heo, trá»©ng cÃºt, má»±c khÃ´ nÆ°á»›ng, xÆ°Æ¡ng á»‘ng heo ninh nÆ°á»›c dÃ¹ng, cáº§n tÃ u, tá»i phi vÃ ng, háº¹, giÃ¡ sá»‘ng.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Náº¥u nÆ°á»›c lÃ¨o): Háº§m xÆ°Æ¡ng á»‘ng heo cÃ¹ng má»±c khÃ´ nÆ°á»›ng vÃ  cá»§ cáº£i tráº¯ng lá»­a riu riu Ä‘á»ƒ nÆ°á»›c dÃ¹ng trong váº¯t, ngá»t thanh Ä‘áº­m Ä‘Ã .\nâ€¢ BÆ°á»›c 2 (XÃ o thá»‹t bÄƒm): Phi tá»i tháº­t vÃ ng giÃ²n thÆ¡m ná»©c, xÃ o thá»‹t náº¡c bÄƒm chÃ­n tá»›i nÃªm chÃºt nÆ°á»›c máº¯m tiÃªu.\nâ€¢ BÆ°á»›c 3 (Luá»™c topping): Luá»™c chÃ­n tÃ´m sÃº, gan heo, tim heo rá»“i thÃ¡i lÃ¡t má»ng.\nâ€¢ BÆ°á»›c 4 (TrÃ¬nh bÃ y): Trá»¥ng sá»£i há»§ tiáº¿u dai qua nÆ°á»›c sÃ´i, xáº¿p vÃ o tÃ´ cÃ¹ng tÃ´m, gan, tim, trá»©ng cÃºt, thá»‹t bÄƒm, ráº¯c tá»i phi cáº§n tÃ u vÃ  chan ngáº­p nÆ°á»›c lÃ¨o sÃ´i sÃ¹ng sá»¥c.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 50000,
                        'thoi_gian_nau' => 45,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh trÃ¡ng trá»™n',
                        'slug' => 'banh-trang-tron',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image22.jpg',
                        'mo_ta_ngan' => 'BÃ¡nh trÃ¡ng trá»™n cÃ¹ng xoÃ i, rau rÄƒm, trá»©ng cÃºt vÃ  gia vá»‹.',
                        'nguyen_lieu' => 'BÃ¡nh trÃ¡ng cáº¯t sá»£i dÃ i, xoÃ i xanh bÃ o sá»£i chua giÃ²n, rau rÄƒm xáº¯t nhá», trá»©ng cÃºt luá»™c bÃ³c vá», khÃ´ bÃ² Ä‘en xÃ© sá»£i, khÃ´ má»±c, tÃ©p khÃ´ sáº¥y Ä‘á», Ä‘áº­u phá»™ng rang giÃ²n, hÃ nh phi thÆ¡m, muá»‘i tÃ´m TÃ¢y Ninh, sá»‘t sa táº¿ táº¯c me cay ngá»t.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Trá»™n Ä‘á»u nguyÃªn liá»‡u): Cho bÃ¡nh trÃ¡ng cáº¯t sá»£i vÃ o Ã¢u lá»›n cÃ¹ng xoÃ i xanh bÃ o, rau rÄƒm, tÃ©p khÃ´, khÃ´ bÃ², muá»‘i tÃ´m.\nâ€¢ BÆ°á»›c 2 (RÆ°á»›i sá»‘t gia vá»‹): RÆ°á»›i sá»‘t táº¯c sa táº¿ me vÃ  chÃºt dáº§u Ä‘iá»u vÃ o Ã¢u.\nâ€¢ BÆ°á»›c 3 (Trá»™n tháº¥m vá»‹): DÃ¹ng gÄƒng tay trá»™n bÃ³p nháº¹ nhÃ ng cho tá»«ng sá»£i bÃ¡nh má»m dáº»o tháº¥m Ä‘áº«m hÆ°Æ¡ng vá»‹ chua cay máº·n ngá»t.\nâ€¢ BÆ°á»›c 4 (HoÃ n thiá»‡n): BÃ y ra Ä‘Ä©a, Ä‘áº·t trá»©ng cÃºt luá»™c lÃªn trÃªn, ráº¯c Ä‘áº­u phá»™ng rang giÃ²n rá»¥m vÃ  hÃ nh phi thÆ¡m lá»«ng.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 20000,
                        'thoi_gian_nau' => 10,
                        'danh_gia' => 4.7
                    ],
                    [
                        'ten_mon' => 'Bá»™t chiÃªn',
                        'slug' => 'bot-chien',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/image11-1668161604.png',
                        'mo_ta_ngan' => 'Bá»™t gáº¡o chiÃªn cÃ¹ng trá»©ng, Äƒn vá»›i Ä‘u Ä‘á»§ bÃ o.',
                        'nguyen_lieu' => 'Khá»‘i bá»™t gáº¡o pha bá»™t nÄƒng háº¥p chÃ­n dáº»o, trá»©ng gÃ  tÆ°Æ¡i, hÃ nh lÃ¡ cáº¯t nhá», má»¡ heo/dáº§u Äƒn, Ä‘u Ä‘á»§ xanh bÃ o sá»£i ngÃ¢m giáº¥m Ä‘Æ°á»ng chua ngá»t, nÆ°á»›c tÆ°Æ¡ng ngon pha giáº¥m Ä‘á» Ä‘Æ°á»ng vÃ  á»›t tá»i bÄƒm.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Cáº¯t bá»™t): Cáº¯t khá»‘i bá»™t gáº¡o háº¥p thÃ nh tá»«ng miáº¿ng vuÃ´ng quÃ¢n cá» vá»«a Äƒn.\nâ€¢ BÆ°á»›c 2 (ChiÃªn giÃ²n): Cho má»¡ heo lÃªn cháº£o gang pháº³ng lá»›n, tháº£ bá»™t vÃ o chiÃªn trÃªn lá»­a vá»«a cho Ä‘áº¿n khi cÃ¡c cáº¡nh bÃ¡nh vÃ ng giÃ²n rá»¥m.\nâ€¢ BÆ°á»›c 3 (ChiÃªn cÃ¹ng trá»©ng): Äáº­p 2 quáº£ trá»©ng gÃ  tÆ°Æ¡i ráº£i Ä‘á»u phá»§ ngáº­p máº·t bá»™t, ráº¯c nhiá»u hÃ nh lÃ¡ thÃ¡i nhá» lÃªn trÃªn.\nâ€¢ BÆ°á»›c 4 (TrÃ¬nh bÃ y): Láº­t máº·t bá»™t cho trá»©ng chÃ­n vÃ ng thÆ¡m phá»©c, trÃºt ra Ä‘Ä©a, phá»§ má»™t nhÃºm Ä‘u Ä‘á»§ xanh bÃ o sá»£i vÃ  cháº¥m nÆ°á»›c tÆ°Æ¡ng giáº¥m Ä‘Æ°á»ng chua ngá»t cay dá»‹u.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 30000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Sá»§i cáº£o ngÆ°á»i Hoa',
                        'slug' => 'sui-cao-nguoi-hoa',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image24.png',
                        'mo_ta_ngan' => 'Sá»§i cáº£o nhÃ¢n thá»‹t, tÃ´m, thÆ°á»ng Äƒn vá»›i nÆ°á»›c dÃ¹ng.',
                        'nguyen_lieu' => 'Vá» bÃ¡nh sá»§i cáº£o bá»™t mÃ¬ má»ng dai vÃ ng Ã³ng, thá»‹t náº¡c dÄƒm heo xay nhuyá»…n, tÃ´m sÃº tÆ°Æ¡i nguyÃªn con bÃ³c vá», náº¥m Ä‘Ã´ng cÃ´ bÄƒm nhá», dáº§u mÃ¨ thÆ¡m, xÆ°Æ¡ng á»‘ng heo ninh nÆ°á»›c dÃ¹ng, má»±c ngÃ¢m tro giÃ²n, cáº£i ngá»t non.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Trá»™n nhÃ¢n & GÃ³i bÃ¡nh): Æ¯á»›p thá»‹t xay vá»›i náº¥m, dáº§u mÃ¨, tiÃªu sá» vÃ  gia vá»‹. Äáº·t 1 con tÃ´m tÆ°Æ¡i nguyÃªn váº¹n cÃ¹ng muá»—ng nhÃ¢n thá»‹t vÃ o giá»¯a vá» bÃ¡nh rá»“i tÃºm náº¿p gáº¥p hÃ¬nh bÃ¡n nguyá»‡t tháº­t khÃ©o.\nâ€¢ BÆ°á»›c 2 (Luá»™c sá»§i cáº£o): Náº¥u ná»“i nÆ°á»›c sÃ´i sÃ¹ng sá»¥c, tháº£ sá»§i cáº£o vÃ o luá»™c chÃ­n ná»•i lÃªn máº·t nÆ°á»›c thÃ¬ vá»›t ra trÃ¡ng qua nÆ°á»›c láº¡nh cho dai giÃ²n.\nâ€¢ BÆ°á»›c 3 (Náº¥u nÆ°á»›c lÃ¨o): Háº§m xÆ°Æ¡ng á»‘ng heo cÃ¹ng má»±c ngÃ¢m tro cho nÆ°á»›c dÃ¹ng ngá»t lá»‹m thanh mÃ¡t, tháº£ cáº£i ngá»t vÃ o trá»¥ng chÃ­n tá»›i.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Cho sá»§i cáº£o vÃ o tÃ´, xáº¿p cáº£i ngá»t vÃ  má»±c lÃªn trÃªn, chan ngáº­p nÆ°á»›c dÃ¹ng nÃ³ng há»•i, ráº¯c tiÃªu thÆ¡m vÃ  hÃ nh lÃ¡.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 55000,
                        'thoi_gian_nau' => 35,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Gá»i cuá»‘n',
                        'slug' => 'goi-cuon',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1594020292985-216a72a2c7ce?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'BÃ¡nh trÃ¡ng cuá»‘n tÃ´m, thá»‹t, bÃºn vÃ  rau sá»‘ng.',
                        'nguyen_lieu' => 'BÃ¡nh trÃ¡ng nem dáº»o má»ng, tÃ´m sÃº tÆ°Æ¡i luá»™c bÃ³c vá» cháº» Ä‘Ã´i, thá»‹t ba rá»i heo luá»™c thÃ¡i lÃ¡t má»ng, bÃºn tÆ°Æ¡i sá»£i nhá», háº¹ cá»ng dÃ i, xÃ  lÃ¡ch, rau thÆ¡m cÃ¡c loáº¡i (hÃºng quáº¿, diáº¿p cÃ¡, tÃ­a tÃ´), tÆ°Æ¡ng Ä‘en, bÆ¡ Ä‘áº­u phá»™ng bÃ©o bÃ¹i, á»›t bÄƒm.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ tÃ´m thá»‹t): Thá»‹t ba rá»i vÃ  tÃ´m sÃº luá»™c chÃ­n tá»›i vá»›i chÃºt hÃ nh tÃ­m cho thÆ¡m ngá»t, vá»›t ra ngÃ¢m nÆ°á»›c Ä‘Ã¡ rá»“i thÃ¡i lÃ¡t má»ng.\nâ€¢ BÆ°á»›c 2 (Cuá»™n gá»i cuá»‘n): LÃ m Æ°á»›t nháº¹ bÃ¡nh trÃ¡ng dáº»o, tráº£i Ä‘á»u lÃ¡ xÃ  lÃ¡ch, rau thÆ¡m, bÃºn tÆ°Æ¡i vÃ  thá»‹t ba rá»i lÃªn máº·t.\nâ€¢ BÆ°á»›c 3 (Káº¹p tÃ´m & háº¹): Gáº¥p má»™t náº¿p bÃ¡nh trÃ¡ng láº¡i, xáº¿p cÃ¡c lÃ¡t tÃ´m Ä‘á» au ngá»­a lÃªn cho Ä‘áº¹p máº¯t, Ä‘áº·t 1 cá»ng háº¹ thÃ² Ä‘áº§u ra ngoÃ i rá»“i cuá»™n trÃ²n tháº­t cháº·t tay.\nâ€¢ BÆ°á»›c 4 (Pha tÆ°Æ¡ng cháº¥m): Phi thÆ¡m tá»i xÃ o tÆ°Æ¡ng Ä‘en cÃ¹ng bÆ¡ Ä‘áº­u phá»™ng vÃ  nÆ°á»›c dá»«a táº¡o Ä‘á»™ sá»‡t bÃ©o bÃ¹i, mÃºc ra chÃ©n ráº¯c Ä‘áº­u phá»™ng rang giÃ£ dáº­p vÃ  á»›t bÄƒm cháº¥m gá»i cuá»‘n.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Há»“ ChÃ­ Minh',
                        'gia_tien' => 35000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.8
                    ]
                ];

                $stmtHcm = $this->conn->prepare("INSERT INTO `mon_an` (`ten_mon`, `slug`, `hinh_anh`, `mo_ta_ngan`, `nguyen_lieu`, `cach_lam`, `id_vung_mien`, `tinh_thanh`, `gia_tien`, `thoi_gian_nau`, `danh_gia`) 
                    VALUES (:ten_mon, :slug, :hinh_anh, :mo_ta_ngan, :nguyen_lieu, :cach_lam, :id_vung_mien, :tinh_thanh, :gia_tien, :thoi_gian_nau, :danh_gia)
                    ON DUPLICATE KEY UPDATE 
                    `ten_mon` = VALUES(`ten_mon`),
                    `mo_ta_ngan` = VALUES(`mo_ta_ngan`),
                    `nguyen_lieu` = VALUES(`nguyen_lieu`),
                    `cach_lam` = VALUES(`cach_lam`),
                    `tinh_thanh` = VALUES(`tinh_thanh`),
                    `id_vung_mien` = VALUES(`id_vung_mien`),
                    `hinh_anh` = VALUES(`hinh_anh`)");

                foreach ($hcmDishes as $hcm) {
                    $stmtHcm->execute([
                        ':ten_mon' => $hcm['ten_mon'],
                        ':slug' => $hcm['slug'],
                        ':hinh_anh' => $hcm['hinh_anh'],
                        ':mo_ta_ngan' => $hcm['mo_ta_ngan'],
                        ':nguyen_lieu' => $hcm['nguyen_lieu'],
                        ':cach_lam' => $hcm['cach_lam'],
                        ':id_vung_mien' => $hcm['id_vung_mien'],
                        ':tinh_thanh' => $hcm['tinh_thanh'],
                        ':gia_tien' => $hcm['gia_tien'],
                        ':thoi_gian_nau' => $hcm['thoi_gian_nau'],
                        ':danh_gia' => $hcm['danh_gia']
                    ]);
                }

                // XÃ³a cÃ¡c báº£n ghi trÃ¹ng láº·p slug cÅ© Ä‘á»ƒ giao diá»‡n hiá»ƒn thá»‹ Ä‘Ãºng 8 mÃ³n TP.HCM sáº¡ch sáº½
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` = 'com-tam'");
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` = 'pha-lau-sai-gon'");
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` = 'banh-trang-tron-sai-gon'");
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` = 'sui-cao-tai-sai-gon'");
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` = 'goi-cuon-tom-thit'");

                // Äáº£m báº£o gÃ¡n Ä‘Ãºng tinh_thanh = 'TP. Há»“ ChÃ­ Minh' cho toÃ n bá»™ 8 mÃ³n
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'TP. Há»“ ChÃ­ Minh', `id_vung_mien` = 3 WHERE `slug` IN ('com-tam-sai-gon', 'banh-mi-sai-gon', 'pha-lau-bo', 'hu-tieu-nam-vang', 'banh-trang-tron', 'bot-chien', 'sui-cao-nguoi-hoa', 'goi-cuon')");

                // =========================================================================
                // CHUáº¨N HÃ“A VÃ€ THÃŠM Äáº¦Y Äá»¦ Äáº¶C Sáº¢N Äá»’NG NAI, TÃ‚Y NINH, Cáº¦N THÆ , AN GIANG (32 MÃ“N)
                // =========================================================================
                $southernFourProvinces = [
                    // --- 1. Tá»ˆNH Äá»’NG NAI (8 MÃ“N) ---
                    [
                        'ten_mon' => 'Gá»i bÆ°á»Ÿi TÃ¢n Triá»u',
                        'slug' => 'goi-buoi-tan-trieu',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'gá»i bÆ°á»Ÿi trá»™n tÃ´m, thá»‹t vÃ  rau thÆ¡m.',
                        'nguyen_lieu' => 'MÃºi bÆ°á»Ÿi Ä‘Æ°á»ng lÃ¡ cam TÃ¢n Triá»u má»ng nÆ°á»›c, tÃ´m sÃº luá»™c bÃ³c vá» cháº» Ä‘Ã´i, thá»‹t ba rá»i heo luá»™c thÃ¡i má»ng, rau rÄƒm xáº¯t nhá», Ä‘áº­u phá»™ng rang giÃ£ dáº­p, hÃ nh phi vÃ ng giÃ²n, á»›t sá»«ng cáº¯t sá»£i, nÆ°á»›c máº¯m tá»i á»›t chua ngá»t.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (TÃ¡ch bÆ°á»Ÿi): TÃ¡ch tá»«ng mÃºi bÆ°á»Ÿi TÃ¢n Triá»u nháº¹ nhÃ ng thÃ nh tá»«ng tÃ©p nhá», trÃ¡nh lÃ m dáº­p tÃ©p bÆ°á»Ÿi cháº£y nÆ°á»›c.\nâ€¢ BÆ°á»›c 2 (SÆ¡ cháº¿ tÃ´m thá»‹t): Luá»™c tÃ´m sÃº vÃ  thá»‹t ba rá»i chÃ­n tá»›i vá»›i chÃºt muá»‘i gá»«ng, vá»›t ra ngÃ¢m nÆ°á»›c Ä‘Ã¡ rá»“i thÃ¡i lÃ¡t vá»«a Äƒn.\nâ€¢ BÆ°á»›c 3 (Trá»™n gá»i): Cho tÃ©p bÆ°á»Ÿi, tÃ´m, thá»‹t vÃ  rau rÄƒm vÃ o Ã¢u, rÆ°á»›i nÆ°á»›c máº¯m chua ngá»t pha tá»i á»›t vÃ o trá»™n Ä‘á»u nháº¹ tay.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): BÃ y gá»i ra Ä‘Ä©a, ráº¯c Ä‘áº­u phá»™ng rang vÃ  hÃ nh phi thÆ¡m lá»«ng lÃªn trÃªn, Äƒn kÃ¨m bÃ¡nh phá»“ng tÃ´m giÃ²n rá»¥m.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 85000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'GÃ  háº¥p bÆ°á»Ÿi',
                        'slug' => 'ga-hap-buoi',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image12.jpg',
                        'mo_ta_ngan' => 'gÃ  cháº¿ biáº¿n cÃ¹ng bÆ°á»Ÿi, cÃ³ hÆ°Æ¡ng thÆ¡m Ä‘áº·c trÆ°ng.',
                        'nguyen_lieu' => '1 con gÃ  ta tháº£ vÆ°á»n (khoáº£ng 1.2 - 1.4kg), 1 quáº£ bÆ°á»Ÿi Ä‘Æ°á»ng TÃ¢n Triá»u cá»¡ lá»›n dÃ y vá», rÆ°á»£u bÆ°á»Ÿi hoáº·c rÆ°á»£u tráº¯ng, lÃ¡ bÆ°á»Ÿi non thÃ¡i chá»‰, sáº£ cÃ¢y, gá»«ng tÆ°Æ¡i, á»›t hiá»ƒm, háº¡t nÃªm, tiÃªu sá».',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ vá» bÆ°á»Ÿi): Cáº¯t khÃ©o pháº§n Ä‘áº§u quáº£ bÆ°á»Ÿi lÃ m náº¯p Ä‘áº­y, náº¡o sáº¡ch ruá»™t bÆ°á»Ÿi bÃªn trong sao cho quáº£ bÆ°á»Ÿi nguyÃªn váº¹n nhÆ° chiáº¿c ná»“i tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 2 (Æ¯á»›p gÃ ): GÃ  lÃ m sáº¡ch, Æ°á»›p cÃ¹ng chÃºt rÆ°á»£u bÆ°á»Ÿi, háº¡t nÃªm, tiÃªu sá», sáº£ Ä‘áº­p dáº­p vÃ  lÃ¡ bÆ°á»Ÿi thÃ¡i sá»£i trong 30 phÃºt.\nâ€¢ BÆ°á»›c 3 (Háº¥p gÃ ): Xáº¿p gÃ  cuá»™n gá»n gÃ ng vÃ o trong ruá»™t quáº£ bÆ°á»Ÿi, ráº¯c thÃªm lÃ¡ bÆ°á»Ÿi lÃªn trÃªn rá»“i Ä‘áº­y kÃ­n náº¯p bÆ°á»Ÿi láº¡i.\nâ€¢ BÆ°á»›c 4 (Háº¥p cÃ¡ch thá»§y): Äáº·t quáº£ bÆ°á»Ÿi vÃ o xá»­ng háº¥p cÃ¡ch thá»§y khoáº£ng 45 - 50 phÃºt. Thá»‹t gÃ  chÃ­n má»m tháº¥m Ä‘áº«m tinh dáº§u bÆ°á»Ÿi ngÃ o ngáº¡t thÆ¡m mÃ¡t.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 290000,
                        'thoi_gian_nau' => 55,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Láº©u lÃ¡ khá»• qua rá»«ng',
                        'slug' => 'lau-la-kho-qua-rung',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/lau-la-kho-qua-rung.jpg',
                        'mo_ta_ngan' => 'mÃ³n láº©u mang hÆ°Æ¡ng vá»‹ rau rá»«ng Ä‘á»‹a phÆ°Æ¡ng.',
                        'nguyen_lieu' => 'Äá»t vÃ  lÃ¡ khá»• qua rá»«ng Long KhÃ¡nh tÆ°Æ¡i non, cÃ¡ thÃ¡c lÃ¡c náº¡o nguyÃªn cháº¥t, xÆ°Æ¡ng á»‘ng heo ninh nÆ°á»›c dÃ¹ng, náº¥m rÆ¡m, hÃ nh lÃ¡, á»›t hiá»ƒm, tiÃªu sá» quáº¿t cháº£ cÃ¡.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Ninh nÆ°á»›c dÃ¹ng): Háº§m xÆ°Æ¡ng á»‘ng heo vá»›i cá»§ hÃ nh tÃ­m cho nÆ°á»›c dÃ¹ng ngá»t trong thanh dá»‹u.\nâ€¢ BÆ°á»›c 2 (Quáº¿t cháº£ cÃ¡): Quáº¿t thá»‹t cÃ¡ thÃ¡c lÃ¡c cÃ¹ng hÃ nh lÃ¡, tiÃªu sá», háº¡t nÃªm tháº­t dáº»o quÃ¡nh táº¡o Ä‘á»™ dai giÃ²n sáº§n sáº­t.\nâ€¢ BÆ°á»›c 3 (Náº¥u láº©u): Äun sÃ´i nÆ°á»›c dÃ¹ng láº©u, mÃºc tá»«ng viÃªn cháº£ cÃ¡ tháº£ vÃ o chÃ­n ná»•i lÃªn máº·t nÆ°á»›c, thÃªm náº¥m rÆ¡m ngá»t vá»‹.\nâ€¢ BÆ°á»›c 4 (NhÃºng lÃ¡ khá»• qua rá»«ng): Khi Äƒn, nhÃºng tá»«ng náº¯m lÃ¡ khá»• qua rá»«ng tÆ°Æ¡i xanh vÃ o nÆ°á»›c láº©u sÃ´i lÄƒn tÄƒn, thÆ°á»Ÿng thá»©c vá»‹ Ä‘áº¯ng thanh ban Ä‘áº§u biáº¿n chuyá»ƒn thÃ nh háº­u ngá»t mÃ¡t lÃ nh.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 190000,
                        'thoi_gian_nau' => 40,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'CÆ¡m gÃ  cÃ¡ máº·n',
                        'slug' => 'com-ga-ca-man',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'cÆ¡m chiÃªn cÃ¹ng gÃ  vÃ  cÃ¡ máº·n, ná»•i tiáº¿ng á»Ÿ khu vá»±c BiÃªn HÃ²a.',
                        'nguyen_lieu' => 'CÆ¡m nguá»™i náº¥u rÃ¡o háº¡t, khÃ´ cÃ¡ sá»­u (hoáº·c cÃ¡ máº·n cÃ¡ thu) nÆ°á»›ng thÆ¡m xÃ© nhá», thá»‹t á»©c gÃ  thÃ¡i háº¡t lá»±u, trá»©ng gÃ  ta, tá»i phi thÆ¡m, hÃ nh lÃ¡, tiÃªu xay, dáº§u mÃ u Ä‘iá»u.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ cÃ¡ máº·n & gÃ ): NÆ°á»›ng chÃ­n khÃ´ cÃ¡ máº·n, gá»¡ láº¥y thá»‹t xÃ© tháº­t nhuyá»…n rá»“i xÃ o thÆ¡m trÃªn cháº£o cÃ¹ng tá»i phi. Thá»‹t gÃ  xÃ o chÃ­n tá»›i cÃ¹ng chÃºt gia vá»‹.\nâ€¢ BÆ°á»›c 2 (Äáº£o cÆ¡m sÄƒn háº¡t): Äun nÃ³ng Ã¢u thá»‘ Ä‘áº¥t hoáº·c cháº£o gang lá»›n vá»›i má»¡ gÃ , trÃºt cÆ¡m vÃ o Ä‘áº£o Ä‘á»u tay trÃªn lá»­a lá»›n cho háº¡t cÆ¡m sÄƒn giÃ²n vÃ ng ruá»™m.\nâ€¢ BÆ°á»›c 3 (Káº¿t há»£p nguyÃªn liá»‡u): Äáº­p trá»©ng gÃ  Ä‘áº£o nhanh tay vÃ o cÆ¡m, trÃºt thá»‹t gÃ  vÃ  khÃ´ cÃ¡ máº·n vÃ o rang Ä‘á»u lá»­a cho háº¡t cÆ¡m quyá»‡n vá»‹ máº·n mÃ²i bÃ©o bÃ¹i Ä‘áº·c trÆ°ng.\nâ€¢ BÆ°á»›c 4 (HoÃ n thÃ nh): Ráº¯c nhiá»u hÃ nh lÃ¡ cáº¯t nhá» vÃ  tiÃªu sá», dá»n Äƒn nÃ³ng trong thá»‘ Ä‘áº¥t giá»¯ nhiá»‡t kÃ¨m dÆ°a leo cÃ  chua.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 65000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Dáº¿ cÆ¡m chiÃªn nÆ°á»›c máº¯m',
                        'slug' => 'de-com-chien-nuoc-mam',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/de-com-chien-nuoc-mam.jpg',
                        'mo_ta_ngan' => 'mÃ³n Äƒn dÃ¢n dÃ£ á»Ÿ má»™t sá»‘ Ä‘á»‹a phÆ°Æ¡ng.',
                        'nguyen_lieu' => 'Dáº¿ cÆ¡m tÆ°Æ¡i bÃ©o ngáº­y Ä‘Æ°á»£c sÆ¡ cháº¿ sáº¡ch ruá»™t, Ä‘áº­u phá»™ng rang nguyÃªn háº¡t, bÆ¡ thá»±c váº­t, tá»i bÄƒm, á»›t bÄƒm, nÆ°á»›c máº¯m ngon nhÄ©, Ä‘Æ°á»ng cÃ¡t tráº¯ng, tiÃªu xay.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ dáº¿ cÆ¡m): RÃºt sáº¡ch ruá»™t dáº¿, rá»­a qua nÆ°á»›c muá»‘i loÃ£ng rá»“i Ä‘á»ƒ rÃ¡o nÆ°á»›c. NhÃ©t 1 háº¡t Ä‘áº­u phá»™ng rang giÃ²n vÃ o bá»¥ng tá»«ng con dáº¿.\nâ€¢ BÆ°á»›c 2 (ChiÃªn giÃ²n): Äun cháº£o ngáº­p dáº§u sÃ´i giÃ , tháº£ dáº¿ cÆ¡m vÃ o chiÃªn vÃ ng giÃ²n rá»¥m trong 3 - 4 phÃºt rá»“i vá»›t ra rÃ¡o dáº§u.\nâ€¢ BÆ°á»›c 3 (Äáº£o nÆ°á»›c máº¯m bÆ¡ tá»i): Phi thÆ¡m tá»i á»›t bÄƒm cÃ¹ng bÆ¡, hÃ²a nÆ°á»›c máº¯m vÃ  Ä‘Æ°á»ng cho sÃ¡nh káº¹o láº¡i trÃªn cháº£o.\nâ€¢ BÆ°á»›c 4 (Tháº¥m vá»‹): TrÃºt dáº¿ cÆ¡m chiÃªn giÃ²n vÃ o cháº£o sá»‘t Ä‘áº£o nhanh tay trÃªn lá»­a lá»›n cho nÆ°á»›c máº¯m bÃ¡m Ä‘á»u lá»›p vá» bÃ³ng báº©y thÆ¡m ná»©c mÅ©i.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 70000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.7
                    ],
                    [
                        'ten_mon' => 'Náº¥m má»‘i Äá»“ng Nai',
                        'slug' => 'nam-moi-dong-nai',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/nam-moi-dong-nai.jpg',
                        'mo_ta_ngan' => 'náº¥m theo mÃ¹a, thÆ°á»ng dÃ¹ng náº¥u chÃ¡o, xÃ o hoáº·c nÆ°á»›ng.',
                        'nguyen_lieu' => 'Náº¥m má»‘i tá»± nhiÃªn Ä‘áº§u mÃ¹a mÆ°a Äá»“ng Nai, tá»i bÄƒm, hÃ nh tÃ­m, má»¡ heo hoáº·c bÆ¡, tiÃªu sá» Äáº¯k Láº¯k, á»›t xiÃªm rá»«ng, gáº¡o thÆ¡m náº¥u chÃ¡o hoáº·c lÃ¡ chuá»‘i nÆ°á»›ng.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (LÃ m sáº¡ch náº¥m má»‘i): Cáº¡o sáº¡ch Ä‘áº¥t gá»‘c báº±ng dao tre/dao inox, ngÃ¢m nÆ°á»›c muá»‘i loÃ£ng 5 phÃºt rá»“i rá»­a nháº¹ tay Ä‘á»ƒ giá»¯ nguyÃªn mÅ© náº¥m giÃ²n ngá»t.\nâ€¢ BÆ°á»›c 2 (Cháº¿ biáº¿n mÃ³n xÃ o/nÆ°á»›ng): Phi thÆ¡m hÃ nh tá»i vá»›i má»¡ gÃ  hoáº·c má»¡ heo, tháº£ náº¥m má»‘i vÃ o xÃ o nhanh trÃªn lá»­a lá»›n chá»‰ 3 - 5 phÃºt Ä‘á»ƒ giá»¯ vá»‹ ngá»t lá»‹m tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 3 (Gia vá»‹ Ä‘áº­m Ä‘Ã ): Ráº¯c tiÃªu sá» giÃ£ dáº­p vÃ  hÃ nh lÃ¡, nÃªm chÃºt nÆ°á»›c máº¯m ngon vá»«a miá»‡ng.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Ä‚n cÃ¹ng cÆ¡m nÃ³ng hoáº·c bá»c lÃ¡ chuá»‘i nÆ°á»›ng than hoa thÆ¡m phá»©c ngá»t lá»‹m nhÆ° thá»‹t gÃ .',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 150000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 5.0
                    ],
                    [
                        'ten_mon' => 'ChÃ´m chÃ´m Long KhÃ¡nh',
                        'slug' => 'chom-chom-long-khanh',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/chom-chom-long-khanh.jpg',
                        'mo_ta_ngan' => 'trÃ¡i cÃ¢y ná»•i tiáº¿ng, ngá»t vÃ  má»ng nÆ°á»›c.',
                        'nguyen_lieu' => 'ChÃ´m chÃ´m nhÃ£n (chÃ´m chÃ´m trÃ³c) chÃ­n Ä‘á» Long KhÃ¡nh - Äá»“ng Nai, cÃ¹i dÃ y trong veo giÃ²n ngá»t, hÆ°Æ¡ng thÆ¡m mÃ¡t dá»‹u tá»± nhiÃªn.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Thu hoáº¡ch & PhÃ¢n loáº¡i): ChÃ´m chÃ´m chÃ­n Ä‘á» cÃ¢y Ä‘Æ°á»£c hÃ¡i vÃ o sÃ¡ng sá»›m giá»¯ trá»n Ä‘á»™ tÆ°Æ¡i giÃ²n má»ng nÆ°á»›c.\nâ€¢ BÆ°á»›c 2 (Rá»­a sáº¡ch & Báº£o quáº£n): Rá»­a nháº¹ qua nÆ°á»›c mÃ¡t, Ä‘á»ƒ rÃ¡o vá» hoáº·c báº£o quáº£n trong ngÄƒn mÃ¡t giá»¯ vá»‹ tÆ°Æ¡i ngon.\nâ€¢ BÆ°á»›c 3 (ThÆ°á»Ÿng thá»©c): BÃ³c vá» thÆ°á»Ÿng thá»©c trá»±c tiáº¿p mÃºi chÃ´m chÃ´m giÃ²n rÃ³c háº¡t ngá»t thanh mÃ¡t lá»‹m hoáº·c káº¿t há»£p lÃ m mÃ³n gá»i tÃ´m thá»‹t láº¡ miá»‡ng.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 40000,
                        'thoi_gian_nau' => 5,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'BÆ°á»Ÿi TÃ¢n Triá»u',
                        'slug' => 'buoi-tan-trieu',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/buoi-tan-trieu.jpg',
                        'mo_ta_ngan' => 'bÆ°á»Ÿi cÃ³ vá»‹ ngá»t thanh, thÆ¡m nháº¹.',
                        'nguyen_lieu' => 'BÆ°á»Ÿi Ä‘Æ°á»ng lÃ¡ cam hoáº·c bÆ°á»Ÿi da xanh cÃ¹ lao TÃ¢n Triá»u (Äá»“ng Nai), mÃºi má»ng nÆ°á»›c khÃ´ng xÆ¡, tÃ©p bÆ°á»Ÿi Ã³ng áº£ giÃ²n tan, muá»‘i á»›t há»™t Ä‘Ã¢m nhuyá»…n.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Gá»t bÆ°á»Ÿi): DÃ¹ng dao khÃ©o lÃ©o gá»t vá» xanh vÃ  lá»›p cÃ¹i tráº¯ng ngoÃ i Ä‘á»ƒ khÃ´ng pháº¡m vÃ o mÃºi bÆ°á»Ÿi.\nâ€¢ BÆ°á»›c 2 (TÃ¡ch mÃºi): KhÃ­a rÃ£nh bÃ³c tÃ¡ch tá»«ng mÃºi bÆ°á»Ÿi lá»›n nguyÃªn váº¹n bÃ y ra Ä‘Ä©a sá»©.\nâ€¢ BÆ°á»›c 3 (ThÆ°á»Ÿng thá»©c): Cháº¥m nháº¹ mÃºi bÆ°á»Ÿi vÃ o chÃ©n muá»‘i á»›t há»™t rang cay the máº±n máº·n Ä‘á»ƒ tÃ´n lÃªn vá»‹ ngá»t thanh mÃ¡t lá»‹m trá»© danh.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng Nai',
                        'gia_tien' => 70000,
                        'thoi_gian_nau' => 10,
                        'danh_gia' => 4.9
                    ],

                    // --- 2. Tá»ˆNH TÃ‚Y NINH (8 MÃ“N) ---
                    [
                        'ten_mon' => 'BÃ¡nh trÃ¡ng phÆ¡i sÆ°Æ¡ng Tráº£ng BÃ ng',
                        'slug' => 'banh-trang-phoi-suong-trang-bang',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/banh-trang-phoi-suong-trang-bang.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh trÃ¡ng má»m, thÆ°á»ng cuá»‘n thá»‹t luá»™c vÃ  rau rá»«ng.',
                        'nguyen_lieu' => 'BÃ¡nh trÃ¡ng gáº¡o Tráº£ng BÃ ng phÆ¡i sÆ°Æ¡ng Ä‘Ãªm dáº»o dai, thá»‹t Ä‘Ã¹i heo/báº¯p giÃ² luá»™c thÃ¡i lÃ¡t má»ng, rá»• rau rá»«ng TÃ¢y Ninh hÆ¡n 10 loáº¡i (sao nhÃ¡i, lÃ¡ cÃ³c, lá»™c vá»«ng, quáº¿ vá»‹, Ä‘á»t bá»©a), máº¯m nÃªm pha thÆ¡m tá»i á»›t Ä‘Æ°á»ng chua cay.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (TrÃ¡ng & PhÆ¡i sÆ°Æ¡ng): BÃ¡nh trÃ¡ng bá»™t gáº¡o nÆ°á»›ng sÆ¡ qua lá»­a than rá»“i Ä‘em phÆ¡i há»©ng sÆ°Æ¡ng Ä‘Ãªm tá»« 2 - 4 giá» sÃ¡ng cho bÃ¡nh dáº»o má»m khÃ´ng cáº§n nhÃºng nÆ°á»›c.\nâ€¢ BÆ°á»›c 2 (Luá»™c thá»‹t báº¯p giÃ²): Thá»‹t heo luá»™c chÃ­n tá»›i ngÃ¢m nÆ°á»›c Ä‘Ã¡ cho giÃ²n bÃ¬ tráº¯ng muá»‘t rá»“i thÃ¡i lÃ¡t má»ng vá»«a Äƒn.\nâ€¢ BÆ°á»›c 3 (Rau rá»«ng & Pha máº¯m nÃªm): Rá»­a sáº¡ch rá»• rau rá»«ng TÃ¢y Ninh. Pha máº¯m nÃªm cÃ¡ cÆ¡m vá»›i dá»©a bÄƒm, tá»i á»›t, chanh Ä‘Æ°á»ng Ä‘áº­m Ä‘Ã  thÆ¡m phá»©c.\nâ€¢ BÆ°á»›c 4 (Cuá»‘n bÃ¡nh & ThÆ°á»Ÿng thá»©c): Tráº£i chiáº¿c bÃ¡nh trÃ¡ng phÆ¡i sÆ°Æ¡ng dáº»o má»‹n, xáº¿p ngáº­p cÃ¡c loáº¡i rau rá»«ng, lÃ¡t thá»‹t luá»™c cuá»™n trÃ²n cháº·t tay cháº¥m ngáº­p chÃ©n máº¯m nÃªm sÃ¡nh Ä‘áº·c.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 90000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 5.0
                    ],
                    [
                        'ten_mon' => 'BÃ² tÆ¡ TÃ¢y Ninh',
                        'slug' => 'bo-to-tay-ninh',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image23.jpg',
                        'mo_ta_ngan' => 'thá»‹t bÃ² non cháº¿ biáº¿n thÃ nh nhiá»u mÃ³n nÆ°á»›ng, háº¥p hoáº·c cuá»‘n bÃ¡nh trÃ¡ng.',
                        'nguyen_lieu' => 'Thá»‹t bÃ² tÆ¡ non má»m TÃ¢y Ninh (thÄƒn, báº¯p hoa hoáº·c sÆ°á»n non), sáº£ á»›t bÄƒm nhuyá»…n, máº¯m nÃªm nguyÃªn cháº¥t, dáº§u mÃ¨, háº¡t nÃªm, tiÃªu Ä‘en, Ä‘áº­u báº¯p nÆ°á»›ng kÃ¨m.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Thui bÃ¬ & ThÃ¡i lÃ¡t): BÃ¬ bÃ² tÆ¡ Ä‘Æ°á»£c thui rÆ¡m vÃ ng Æ°Æ¡m giÃ²n sáº§n sáº­t, thá»‹t bÃªn trong mÃ u há»“ng tÆ°Æ¡i má»m máº¡i thÃ¡i lÃ¡t dÃ y vá»«a pháº£i.\nâ€¢ BÆ°á»›c 2 (Æ¯á»›p gia vá»‹ nÆ°á»›ng): Æ¯á»›p thá»‹t cÃ¹ng sáº£ á»›t, tá»i bÄƒm, máº­t ong, tiÃªu sá» vÃ  dáº§u mÃ¨ trong 30 phÃºt cho ngáº¥m sÃ¢u thá»› thá»‹t.\nâ€¢ BÆ°á»›c 3 (NÆ°á»›ng than hoa): Äáº·t tá»«ng táº£ng thá»‹t bÃ² tÆ¡ lÃªn vá»‰ nÆ°á»›ng trÃªn than hoa Ä‘á» há»“ng lÃ¡ch tÃ¡ch, trá»Ÿ Ä‘á»u tay cho chÃ¡y xÃ©m cáº¡nh ngoÃ i má»ng nÆ°á»›c bÃªn trong.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Cáº¯t lÃ¡t cuá»™n bÃ¡nh trÃ¡ng phÆ¡i sÆ°Æ¡ng hoáº·c cháº¥m trá»±c tiáº¿p máº¯m tÃ´m/máº¯m nÃªm dáº§m á»›t hiá»ƒm cay ná»“ng.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 180000,
                        'thoi_gian_nau' => 35,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh canh Tráº£ng BÃ ng',
                        'slug' => 'banh-canh-trang-bang',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image34.png',
                        'mo_ta_ngan' => 'bÃ¡nh canh vá»›i nÆ°á»›c dÃ¹ng Ä‘áº­m Ä‘Ã , thÆ°á»ng Äƒn cÃ¹ng thá»‹t heo.',
                        'nguyen_lieu' => 'Sá»£i bÃ¡nh canh bá»™t gáº¡o Tráº£ng BÃ ng tráº¯ng muá»‘t dai má»m, giÃ² heo mÃ³ng/thá»‹t báº¯p heo luá»™c giÃ²n, xÆ°Æ¡ng á»‘ng heo ninh nÆ°á»›c dÃ¹ng trong veo, hÃ nh lÃ¡, ngÃ² gai, tiÃªu xay, nÆ°á»›c máº¯m tá»i á»›t tiÃªu sá».',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Náº¥u nÆ°á»›c lÃ¨o trong váº¯t): XÆ°Æ¡ng á»‘ng heo háº§m liu riu nhiá»u giá», vá»›t bá»t liÃªn tá»¥c Ä‘á»ƒ nÆ°á»›c lÃ¨o trong veo ngá»t lá»‹m tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 2 (Luá»™c báº¯p giÃ² heo): Luá»™c báº¯p heo vÃ  mÃ³ng giÃ² vá»«a chÃ­n giÃ²n sáº§n sáº­t, vá»›t ra xáº¯t lÃ¡t má»ng bÃ y Ä‘Ä©a riÃªng.\nâ€¢ BÆ°á»›c 3 (Trá»¥ng sá»£i bÃ¡nh canh): Trá»¥ng sá»£i bÃ¡nh canh qua nÆ°á»›c sÃ´i cho nÃ³ng má»m cho vÃ o tÃ´ sá»©.\nâ€¢ BÆ°á»›c 4 (TrÃ¬nh bÃ y): Xáº¿p thá»‹t giÃ² náº¡c lÃªn máº·t, ráº¯c hÃ nh ngÃ² thÃ¡i nhá» vÃ  tiÃªu sá» thÆ¡m ná»“ng, chan ngáº­p nÆ°á»›c lÃ¨o sÃ´i sÃ¹ng sá»¥c, cháº¥m thá»‹t vÃ o chÃ©n máº¯m máº·n á»›t hiá»ƒm.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 50000,
                        'thoi_gian_nau' => 45,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Muá»‘i tÃ´m TÃ¢y Ninh',
                        'slug' => 'muoi-tom-tay-ninh',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/muoi-tom-tay-ninh.jpg',
                        'mo_ta_ngan' => 'gia vá»‹ ná»•i tiáº¿ng dÃ¹ng cháº¥m trÃ¡i cÃ¢y hoáº·c Äƒn cÃ¹ng bÃ¡nh trÃ¡ng.',
                        'nguyen_lieu' => 'TÃ´m khÃ´ loáº¡i 1 xay nhuyá»…n, muá»‘i há»™t biá»ƒn sáº¡ch rang khÃ´, á»›t Ä‘á» chÃ­n phÆ¡i rÃ¡o nÆ°á»›c, tá»i cÃ´ Ä‘Æ¡n bÄƒm nhá», Ä‘Æ°á»ng cÃ¡t, bá»™t ngá»t.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Xay nhuyá»…n nguyÃªn liá»‡u): Xay nhuyá»…n tÃ´m khÃ´ cÃ¹ng tá»i, á»›t chÃ­n tÆ°Æ¡i vÃ  muá»‘i há»™t theo tá»‰ lá»‡ chuáº©n truyá»n thá»‘ng TÃ¢y Ninh.\nâ€¢ BÆ°á»›c 2 (Rang sáº¥y háº¡t muá»‘i): Cho há»—n há»£p lÃªn cháº£o lá»›n rang sáº¥y trÃªn lá»­a nhá» Ä‘á»u tay liÃªn tá»¥c Ä‘á»ƒ muá»‘i káº¿t háº¡t mÃ u cam Ä‘á» tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 3 (Äáº£o giÃ²n dáº­y mÃ¹i): Rang Ä‘áº¿n khi háº¡t muá»‘i khÃ´ rÃ¡o giÃ²n tan, dáº­y mÃ¹i thÆ¡m lá»«ng cá»§a tÃ´m vÃ  vá»‹ cay xÃ© lÆ°á»¡i cá»§a á»›t.\nâ€¢ BÆ°á»›c 4 (Báº£o quáº£n & DÃ¹ng kÃ¨m): Äá»ƒ nguá»™i cho vÃ o hÅ© kÃ­n; dÃ¹ng cháº¥m trÃ¡i cÃ¢y chua ngá»t hoáº·c lÃ m nguyÃªn liá»‡u cá»‘t lÃµi cho bÃ¡nh trÃ¡ng trá»™n.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 35000,
                        'thoi_gian_nau' => 60,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh trÃ¡ng me',
                        'slug' => 'banh-trang-me',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image22.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh trÃ¡ng Äƒn cÃ¹ng nÆ°á»›c sá»‘t me chua ngá»t.',
                        'nguyen_lieu' => 'Xáº¥p bÃ¡nh trÃ¡ng phÆ¡i sÆ°Æ¡ng dáº»o gáº¥p vuÃ´ng, nÆ°á»›c sá»‘t me chua ngá»t sÃ¡nh má»‹n cÃ´ Ä‘áº·c, Ä‘áº­u phá»™ng rang giÃ£ nhá», hÃ nh phi giÃ²n tan, muá»‘i tÃ´m TÃ¢y Ninh, sa táº¿ á»›t cay ná»“ng.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Náº¥u sá»‘t me): Me chÃ­n lá»c láº¥y nÆ°á»›c cá»‘t chua ngá»t sÃ¡nh má»‹n, náº¥u cÃ¹ng Ä‘Æ°á»ng thá»‘t ná»‘t vÃ  nÆ°á»›c máº¯m táº¡o Ä‘á»™ dáº»o quÃ¡nh thÆ¡m ngon.\nâ€¢ BÆ°á»›c 2 (Pha chÃ©n sá»‘t cháº¥m): MÃºc sá»‘t me ra chÃ©n nhá», ráº¯c Ä‘áº­u phá»™ng rang giÃ²n, hÃ nh phi thÆ¡m lá»«ng, chÃºt sa táº¿ cay vÃ  muá»‘i tÃ´m háº¡t.\nâ€¢ BÆ°á»›c 3 (Cuá»‘n bÃ¡nh): Gá»¡ tá»«ng lá»›p bÃ¡nh trÃ¡ng phÆ¡i sÆ°Æ¡ng dáº»o dai cuá»™n trÃ²n thÃ nh que nhá».\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Cháº¥m ngáº­p que bÃ¡nh trÃ¡ng vÃ o chÃ©n sá»‘t me, cáº¯n má»™t miáº¿ng cáº£m nháº­n ngay vá»‹ chua cay máº·n ngá»t bÃ¹ng ná»• vá»‹ giÃ¡c.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 15000,
                        'thoi_gian_nau' => 15,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh trÃ¡ng cuá»‘n bÆ¡',
                        'slug' => 'banh-trang-cuon-bo',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/banh-trang-cuon-bo.jpg',
                        'mo_ta_ngan' => 'mÃ³n Äƒn váº·t vá»›i bÃ¡nh trÃ¡ng, bÆ¡ vÃ  cÃ¡c loáº¡i topping.',
                        'nguyen_lieu' => 'BÃ¡nh trÃ¡ng Ä‘á» TÃ¢y Ninh dáº»o má»m, sá»‘t bÆ¡ trá»©ng gÃ  vÃ ng Ã³ng bÃ©o ngáº­y, khÃ´ bÃ² Ä‘en xÃ© sá»£i, tÃ©p sáº¥y giÃ²n Ä‘á», hÃ nh phi thÆ¡m lá»«ng, trá»©ng cÃºt luá»™c hoáº·c chiÃªn, sá»‘t tÆ°Æ¡ng á»›t me.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Tráº£i bÃ¡nh trÃ¡ng): Äáº·t chiáº¿c bÃ¡nh trÃ¡ng Ä‘á» má»m dáº»o lÃªn máº·t pháº³ng sáº¡ch.\nâ€¢ BÆ°á»›c 2 (Ráº£i topping): Ráº£i Ä‘á»u ruá»‘c tÃ©p khÃ´ sáº¥y Ä‘á», khÃ´ bÃ² Ä‘en tÆ¡i sá»£i, hÃ nh phi giÃ²n vÃ  trá»©ng cÃºt cáº¯t Ä‘Ã´i lÃªn máº·t bÃ¡nh.\nâ€¢ BÆ°á»›c 3 (BÃ³p sá»‘t bÆ¡ trá»©ng gÃ ): BÃ³p má»™t dáº£i sá»‘t bÆ¡ vÃ ng Ã³ng ngáº­y bÃ©o cháº¡y dá»c theo thÃ¢n bÃ¡nh.\nâ€¢ BÆ°á»›c 4 (Cuá»™n cháº·t & Cáº¯t khÃºc): Cuá»™n trÃ²n tháº­t cháº·t tay, cáº¯t khÃºc vá»«a Äƒn 3 - 4cm, rÆ°á»›i sá»‘t me tÆ°Æ¡ng á»›t cay ngá»t thÆ°á»Ÿng thá»©c ngay.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 20000,
                        'thoi_gian_nau' => 10,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Tháº±n láº±n nÃºi BÃ  Äen',
                        'slug' => 'than-lan-nui-ba-den',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image14.jpg',
                        'mo_ta_ngan' => 'Ä‘áº·c sáº£n Ä‘Æ°á»£c cháº¿ biáº¿n thÃ nh mÃ³n nÆ°á»›ng hoáº·c chiÃªn á»Ÿ khu vá»±c nÃºi BÃ  Äen.',
                        'nguyen_lieu' => 'Tháº±n láº±n nÃºi BÃ  Äen sá»‘ng tá»± nhiÃªn chuyÃªn Äƒn chuá»‘i rá»«ng vÃ  cÃ¢y thuá»‘c nam, sáº£ cÃ¢y bÄƒm nhá», á»›t hiá»ƒm Ä‘á», tiÃªu sá», tá»i bÄƒm, rau sá»‘ng rá»«ng, muá»‘i tiÃªu chanh tÆ°Æ¡i.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ sáº¡ch sáº½): Tháº±n láº±n nÃºi má»• bá» ruá»™t lÃ m sáº¡ch ká»¹, giá»¯ nguyÃªn pháº§n thá»‹t sÄƒn cháº¯c thÆ¡m mÃ¹i tháº£o má»™c.\nâ€¢ BÆ°á»›c 2 (Táº©m Æ°á»›p gia vá»‹): Æ¯á»›p thá»‹t tháº±n láº±n vá»›i sáº£ bÄƒm, á»›t hiá»ƒm, tiÃªu sá» vÃ  chÃºt muá»‘i á»›t TÃ¢y Ninh trong 20 phÃºt.\nâ€¢ BÆ°á»›c 3 (NÆ°á»›ng giÃ²n than hoa): Káº¹p vá»‰ nÆ°á»›ng trÃªn than há»“ng lÃ¡ch tÃ¡ch cho Ä‘áº¿n khi lá»›p da bÃªn ngoÃ i vÃ ng giÃ²n rá»¥m, thá»‹t bÃªn trong tráº¯ng ngáº§n bá»‘c khÃ³i thÆ¡m ná»©c.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Dá»n Äƒn nÃ³ng kÃ¨m Ä‘á»t cÃ³c, xÃ  lÃ¡ch son vÃ  chÃ©n muá»‘i tiÃªu chanh cay the Ä‘áº­m Ä‘Ã .',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 120000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 4.7
                    ],
                    [
                        'ten_mon' => 'MÃ£ng cáº§u BÃ  Äen',
                        'slug' => 'mang-cau-ba-den',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/mang-cau-ba-den.jpg',
                        'mo_ta_ngan' => 'trÃ¡i cÃ¢y ná»•i tiáº¿ng cá»§a vÃ¹ng Ä‘áº¥t TÃ¢y Ninh.',
                        'nguyen_lieu' => 'TrÃ¡i mÃ£ng cáº§u ta (na) chÃ¢n nÃºi BÃ  Äen vá» má»ng máº¯t ná»Ÿ to Ä‘á»u, thá»‹t tráº¯ng muá»‘t dai thÆ¡m ngá»t Ä‘áº­m Ä‘Ã , háº¡t nhá» rÃ³c cÆ¡m.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Chá»n lá»±a trÃ¡i ngon): Chá»n nhá»¯ng quáº£ mÃ£ng cáº§u chÃ­n cÃ¢y máº¯t ná»Ÿ to, káº½ máº¯t ngáº£ vÃ ng nháº¡t bÃ¡o hiá»‡u Ä‘á»™ ngá»t lá»‹m Ä‘áº¡t Ä‘á»‰nh.\nâ€¢ BÆ°á»›c 2 (LÃ m mÃ¡t): Rá»­a sáº¡ch vá», Ä‘á»ƒ nÆ¡i khÃ´ rÃ¡o hoáº·c Æ°á»›p mÃ¡t ngÄƒn láº¡nh 20 phÃºt trÆ°á»›c khi Äƒn.\nâ€¢ BÆ°á»›c 3 (ThÆ°á»Ÿng thá»©c): TÃ¡ch mÃºi mÃ£ng cáº§u thÆ°á»Ÿng thá»©c vá»‹ ngá»t thÆ¡m Ä‘áº­m Ä‘Ã  Ä‘áº·c trÆ°ng cá»§a thá»• nhÆ°á»¡ng nÃºi lá»­a cá»• BÃ  Äen, hoáº·c dáº§m sá»¯a Ä‘Ã¡ mÃ¡t láº¡nh sáº£ng khoÃ¡i.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TÃ¢y Ninh',
                        'gia_tien' => 60000,
                        'thoi_gian_nau' => 5,
                        'danh_gia' => 4.9
                    ],

                    // --- 3. TP. Cáº¦N THÆ  (8 MÃ“N) ---
                    [
                        'ten_mon' => 'BÃ¡nh cá»‘ng Cáº§n ThÆ¡',
                        'slug' => 'banh-cong-can-tho',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image32.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh chiÃªn giÃ²n vá»›i nhÃ¢n Ä‘áº­u xanh, thá»‹t vÃ  tÃ´m.',
                        'nguyen_lieu' => 'Bá»™t gáº¡o pha bá»™t náº¿p vÃ  Ä‘áº­u nÃ nh ngÃ¢m xay má»‹n, Ä‘áº­u xanh nguyÃªn háº¡t háº¥p chÃ­n má»m, thá»‹t náº¡c dÄƒm heo Æ°á»›p gia vá»‹, tÃ´m sÃ´ng tÆ°Æ¡i nguyÃªn con, rá»• rau sá»‘ng miá»‡t vÆ°á»n, nÆ°á»›c máº¯m tá»i á»›t chua ngá»t Ä‘á»“ chua.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Pha bá»™t bÃ¡nh cá»‘ng): Trá»™n bá»™t gáº¡o xay vá»›i nÆ°á»›c cá»‘t Ä‘áº­u nÃ nh táº¡o há»—n há»£p bá»™t sÃ¡nh má»‹n.\nâ€¢ BÆ°á»›c 2 (Xáº¿p vÃ o khuÃ´n cá»‘ng): Äun cháº£o ngáº­p dáº§u sÃ´i giÃ . NhÃºng khuÃ´n cá»‘ng kim loáº¡i vÃ o dáº§u cho nÃ³ng, mÃºc 1 muá»—ng bá»™t vÃ o Ä‘Ã¡y, thÃªm nhÃ¢n thá»‹t xÃ o vÃ  Ä‘áº­u xanh háº¥p, mÃºc tiáº¿p bá»™t phá»§ kÃ­n rá»“i Ä‘áº·t 1 con tÃ´m sÃ´ng tÆ°Æ¡i cong mÃ¬nh lÃªn trÃªn.\nâ€¢ BÆ°á»›c 3 (ChiÃªn giÃ²n vÃ ng ruá»™m): NhÃºng khuÃ´n cá»‘ng ngáº­p trong dáº§u sÃ´i, chiÃªn Ä‘áº¿n khi bÃ¡nh vÃ ng giÃ²n tá»± tÃ¡ch khá»i khuÃ´n ná»•i lÃªn bá» máº·t.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Vá»›t bÃ¡nh cá»‘ng ra rÃ¡o dáº§u, cáº¯t lÃ m 4 pháº§n cuá»™n cÃ¹ng xÃ  lÃ¡ch, rau diáº¿p cÃ¡, Ä‘á»t xoÃ i cháº¥m ngáº­p chÃ©n nÆ°á»›c máº¯m chua ngá»t.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 35000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Láº©u vá»‹t náº¥u chao',
                        'slug' => 'lau-vit-nau-chao',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/lau-vit-nau-chao.jpg',
                        'mo_ta_ngan' => 'vá»‹t náº¥u chao cÃ¹ng khoai mÃ´n vÃ  rau Äƒn kÃ¨m.',
                        'nguyen_lieu' => 'Vá»‹t xiÃªm cá» Ã­t má»¡ cháº¯c thá»‹t lÃ m sáº¡ch khá»­ mÃ¹i rÆ°á»£u gá»«ng, chao Ä‘á» bÃ©o ngáº­y, chao tráº¯ng, khoai mÃ´n sÃ¡p bÃ¹i dáº»o, nÆ°á»›c dá»«a tÆ°Æ¡i, rau muá»‘ng Ä‘á»“ng, cáº£i báº¹ xanh, bÃºn tÆ°Æ¡i sá»£i nhá».',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ & Æ¯á»›p vá»‹t): Cháº·t thá»‹t vá»‹t miáº¿ng vá»«a Äƒn, Æ°á»›p cÃ¹ng chao Ä‘á», chao tráº¯ng tÃ¡n nhuyá»…n, tá»i bÄƒm, Ä‘Æ°á»ng, háº¡t nÃªm trong 45 phÃºt cho tháº¥m Ä‘áº«m vá»‹ bÃ©o ná»“ng.\nâ€¢ BÆ°á»›c 2 (XÃ o sÄƒn thá»‹t vá»‹t): Phi thÆ¡m tá»i xÃ o thá»‹t vá»‹t tháº­t sÄƒn cho dáº­y mÃ¹i thÆ¡m cá»§a chao.\nâ€¢ BÆ°á»›c 3 (Náº¥u láº©u cÃ¹ng nÆ°á»›c dá»«a & khoai): Äá»• ngáº­p nÆ°á»›c dá»«a tÆ°Æ¡i vÃ o ná»“i ninh sÃ´i lÄƒn tÄƒn Ä‘áº¿n khi vá»‹t má»m, trÃºt khoai mÃ´n chiÃªn sÆ¡ vÃ o náº¥u thÃªm 15 phÃºt cho khoai bÃ¹i dáº»o sÃ¡nh nÆ°á»›c.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Äáº·t ná»“i láº©u trÃªn báº¿p cá»“n, nhÃºng rau muá»‘ng Ä‘á»“ng non mÆ°á»›t, chan bÃºn tÆ°Æ¡i vÃ  cháº¥m thá»‹t vá»‹t vÃ o chÃ©n chao pha tá»i á»›t Ä‘Æ°á»ng táº¯c thÆ¡m ngáº­y.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 220000,
                        'thoi_gian_nau' => 50,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh tÃ©t lÃ¡ cáº©m',
                        'slug' => 'banh-tet-la-cam',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/banh-tet-la-cam.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh náº¿p mÃ u tÃ­m, thÆ°á»ng cÃ³ nhÃ¢n thá»‹t vÃ  trá»©ng muá»‘i.',
                        'nguyen_lieu' => 'Náº¿p cÃ¡i thÆ¡m ngÃ¢m nÆ°á»›c lÃ¡ cáº©m táº¡o sáº¯c tÃ­m cáº©m rá»±c rá»¡, nÆ°á»›c cá»‘t dá»«a bÃ©o ngáº­y, Ä‘áº­u xanh khÃ´ng vá» háº¥p quáº¿t nhuyá»…n, thá»‹t ba rá»i heo Æ°á»›p tiÃªu thÆ¡m, lÃ²ng Ä‘á» trá»©ng muá»‘i bÃ¹i bÃ©o, lÃ¡ chuá»‘i tÆ°Æ¡i, dÃ¢y láº¡t buá»™c.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (XÃ o náº¿p lÃ¡ cáº©m): Náº¿p ngÃ¢m nÆ°á»›c lÃ¡ cáº©m tÃ­m Ã³ng áº£ xÃ o cÃ¹ng nÆ°á»›c cá»‘t dá»«a vÃ  chÃºt muá»‘i trÃªn cháº£o lá»›n Ä‘áº¿n khi háº¡t náº¿p ngáº­m no cá»‘t dá»«a bÃ³ng báº©y.\nâ€¢ BÆ°á»›c 2 (LÃ m nhÃ¢n bÃ¡nh tÃ©t): Tráº£i lá»›p Ä‘áº­u xanh háº¥p tÃ¡n nhuyá»…n, Ä‘áº·t dáº£i thá»‹t ba rá»i Æ°á»›p tiÃªu vÃ  hÃ ng lÃ²ng Ä‘á» trá»©ng muá»‘i Ä‘á» au vÃ o giá»¯a rá»“i cuá»™n trÃ²n lÃ m nhÃ¢n.\nâ€¢ BÆ°á»›c 3 (GÃ³i bÃ¡nh tÃ©t): Tráº£i lÃ¡ chuá»‘i, ráº£i lá»›p náº¿p tÃ­m cáº©m, Ä‘áº·t nhÃ¢n bÃ¡nh vÃ o giá»¯a cuá»™n trÃ²n tháº­t khÃ©o tay rá»“i dÃ¹ng dÃ¢y láº¡t siáº¿t cháº·t Ä‘á»u Ä‘áº·n tá»«ng Ä‘Ã²n bÃ¡nh.\nâ€¢ BÆ°á»›c 4 (Luá»™c bÃ¡nh chÃ­n dá»n): Náº¥u ná»“i nÆ°á»›c sÃ´i ngáº­p bÃ¡nh luá»™c suá»‘t 6 - 8 tiáº¿ng. Khi cáº¯t báº±ng dÃ¢y láº¡t tá»«ng khoanh bÃ¡nh hiá»‡n lÃªn sáº¯c tÃ­m cáº©m tuyá»‡t Ä‘áº¹p, náº¿p dáº»o thÆ¡m bÃ©o ngáº­y.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 90000,
                        'thoi_gian_nau' => 360,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Nem nÆ°á»›ng CÃ¡i RÄƒng',
                        'slug' => 'nem-nuong-cai-rang',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/nem-nuong-cai-rang.jpg',
                        'mo_ta_ngan' => 'nem nÆ°á»›ng Äƒn vá»›i bÃ¡nh trÃ¡ng, rau sá»‘ng vÃ  nÆ°á»›c cháº¥m.',
                        'nguyen_lieu' => 'Thá»‹t náº¡c Ä‘Ã¹i heo tÆ°Æ¡i quáº¿t dáº»o nhuyá»…n, má»¡ heo xáº¯t háº¡t lá»±u Æ°á»›p Ä‘Æ°á»ng cho trong, tá»i tiÃªu gia vá»‹, bÃ¡nh trÃ¡ng nem, bÃ¡nh há»i thoa má»¡ hÃ nh, rau sá»‘ng, chuá»‘i chÃ¡t, kháº¿ chua, chÃ©n nÆ°á»›c tÆ°Æ¡ng náº¿p sÃ¡nh Ä‘áº·c.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Quáº¿t thá»‹t dáº»o quÃ¡nh): Thá»‹t heo nÃ³ng má»›i má»• quáº¿t liÃªn tá»¥c cÃ¹ng má»¡ Ä‘Æ°á»ng, tá»i bÄƒm vÃ  tiÃªu sá» Ä‘áº¿n khi quÃ¡nh dáº»o bÃ³ng má»‹n.\nâ€¢ BÆ°á»›c 2 (XiÃªn que nÆ°á»›ng nem): Vo thá»‹t thÃ nh tá»«ng viÃªn trÃ²n dÃ i ghim vÃ o xiÃªn tre mÆ°á»›t máº¯t.\nâ€¢ BÆ°á»›c 3 (NÆ°á»›ng than há»“ng): NÆ°á»›ng nem trÃªn báº¿p than Ä‘á» lá»­a, má»¡ tÆ°Æ¡m xÃ¨o xÃ¨o thÆ¡m ngÃ¡t mÃ¹i tá»i chÃ¡y cáº¡nh vÃ ng ruá»™m Ã³ng áº£.\nâ€¢ BÆ°á»›c 4 (Cuá»‘n bÃ¡nh & NÆ°á»›c cháº¥m sá»‡t): Cuá»‘n nem cÃ¹ng bÃ¡nh trÃ¡ng, bÃ¡nh há»i má»¡ hÃ nh vÃ  rau thÆ¡m, cháº¥m ngáº­p chÃ©n tÆ°Æ¡ng xay náº¥u tá»« náº¿p bÃ¹i bÃ©o ráº¯c Ä‘áº­u phá»™ng rang giÃ£ dáº­p.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 60000,
                        'thoi_gian_nau' => 35,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh há»i máº·t vÃµng Phong Äiá»n',
                        'slug' => 'banh-hoi-mat-vong-phong-dien',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/banh-hoi-mat-vong-phong-dien.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh há»i táº¡o hÃ¬nh máº·t vÃµng Ä‘áº·c trÆ°ng.',
                        'nguyen_lieu' => 'Bá»™t gáº¡o Sa ÄÃ©c thÆ¡m dáº»o quáº¥y trÃ¹ng cÃ´ng phu, má»¡ heo tháº¯ng giÃ²n, háº¹ lÃ¡ cáº¯t nhá» thoa máº·t bÃ¡nh, thá»‹t heo quay giÃ²n bÃ¬ hoáº·c thá»‹t nÆ°á»›ng kim tiá»n, nÆ°á»›c máº¯m tá»i á»›t chua ngá»t chua dá»‹u.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Quáº¥y trÃ¹ng bá»™t gáº¡o): Bá»™t gáº¡o khuáº¥y Ä‘á»u trÃªn lá»­a nhá» cho ná»­a sá»‘ng ná»­a chÃ­n dáº»o quÃ¡nh Ä‘Ã²i há»i kinh nghiá»‡m gia truyá»n cá»§a nghá»‡ nhÃ¢n Phong Äiá»n.\nâ€¢ BÆ°á»›c 2 (Ã‰p Ä‘an hÃ¬nh máº·t vÃµng): Cho bá»™t vÃ o khuÃ´n Ä‘á»“ng Ã©p lá»±c Ä‘á»u tay, láº¯c khÃ©o lÃ©o Ä‘á»ƒ tá»«ng sá»£i bÃ¡nh máº£nh mai Ä‘an cÃ i vÃ o nhau thÃ nh hÃ¬nh Ã´ máº¯t vÃµng tinh xáº£o.\nâ€¢ BÆ°á»›c 3 (Háº¥p cÃ¡ch thá»§y): Háº¥p tá»«ng miáº¿ng bÃ¡nh há»i máº·t vÃµng chÃ­n trong veo má»m mÆ°á»›t chá»‰ trong 3 - 4 phÃºt, thoa má»¡ háº¹ xanh Ã³ng Ã¡nh.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Xáº¿p bÃ¡nh há»i ra Ä‘Ä©a Äƒn kÃ¨m thá»‹t heo quay giÃ²n bÃ¬ thÃ¡i má»ng, dÆ°a chua vÃ  nÆ°á»›c máº¯m chua ngá»t máº·n mÃ  miá»‡t vÆ°á»n.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 55000,
                        'thoi_gian_nau' => 40,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Pizza há»§ tiáº¿u',
                        'slug' => 'pizza-hu-tieu',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/pizza-hu-tieu.jpg',
                        'mo_ta_ngan' => 'há»§ tiáº¿u chiÃªn giÃ²n cÃ¹ng cÃ¡c loáº¡i topping.',
                        'nguyen_lieu' => 'Sá»£i há»§ tiáº¿u tÆ°Æ¡i truyá»n thá»‘ng lÃ² SÃ¡u HoÃ i (Cáº§n ThÆ¡), trá»©ng gÃ  ta, thá»‹t náº¡c heo khÃ¬a nÆ°á»›c dá»«a thÃ¡i sá»£i, trá»©ng á»‘p la, Ä‘áº­u phá»™ng rang giÃ£ dáº­p, hÃ nh phi, ngÃ² rÃ­, nÆ°á»›c cá»‘t dá»«a sá»‘t tÆ°Æ¡ng á»›t.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Táº¡o Ä‘áº¿ bÃ¡nh há»§ tiáº¿u): Trá»™n sá»£i há»§ tiáº¿u tÆ°Æ¡i vá»›i lÃ²ng Ä‘á» trá»©ng gÃ  táº¡o Ä‘á»™ káº¿t dÃ­nh trÃ²n Ä‘á»u nhÆ° chiáº¿c Ä‘áº¿ bÃ¡nh pizza.\nâ€¢ BÆ°á»›c 2 (ChiÃªn Ä‘áº¿ giÃ²n rá»¥m): Cho Ä‘áº¿ há»§ tiáº¿u vÃ o cháº£o dáº§u sÃ´i chiÃªn vÃ ng Æ°Æ¡m giÃ²n rá»¥m hai máº·t rá»“i vá»›t ra rÃ¡o dáº§u.\nâ€¢ BÆ°á»›c 3 (Ráº£i ngáº­p topping miá»n TÃ¢y): Ráº£i Ä‘á»u thá»‹t heo khÃ¬a nÆ°á»›c dá»«a thÆ¡m bÃ©o, trá»©ng chiÃªn thÃ¡i chá»‰, hÃ nh phi vÃ ng vÃ  Ä‘áº­u phá»™ng lÃªn trÃªn máº·t Ä‘áº¿ bÃ¡nh giÃ²n tan.\nâ€¢ BÆ°á»›c 4 (RÆ°á»›i sá»‘t & ThÆ°á»Ÿng thá»©c): RÆ°á»›i sá»‘t tÆ°Æ¡ng á»›t hÃ²a quyá»‡n chÃºt nÆ°á»›c cá»‘t dá»«a bÃ©o ngáº­y, cáº¯t thÃ nh tá»«ng lÃ¡t tam giÃ¡c giÃ²n rá»¥m rÃ´m rá»‘p Ä‘á»™c nháº¥t vÃ´ nhá»‹.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 45000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.7
                    ],
                    [
                        'ten_mon' => 'á»c nÆ°á»›ng tiÃªu xanh',
                        'slug' => 'oc-nuong-tieu-xanh',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image36.jpg',
                        'mo_ta_ngan' => 'á»‘c nÆ°á»›ng thÆ¡m, cÃ³ vá»‹ cay cá»§a tiÃªu.',
                        'nguyen_lieu' => 'á»c bÆ°Æ¡u hoáº·c á»‘c lÃ¡c sÃ´ng Cáº§n ThÆ¡ máº­p máº¡p bÃ©o trÃ²n, chÃ¹m tiÃªu xanh PhÃº Quá»‘c/miá»n TÃ¢y tÆ°Æ¡i giÃ²n, nÆ°á»›c máº¯m cÃ¡ cÆ¡m nhÄ©, tá»i á»›t bÄƒm nhuyá»…n, rau rÄƒm tÆ°Æ¡i, giáº¥m Ä‘Æ°á»ng háº¡t nÃªm.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (NgÃ¢m rá»­a á»‘c bÆ°Æ¡u): NgÃ¢m á»‘c trong nÆ°á»›c vo gáº¡o cáº¯t á»›t hiá»ƒm nhiá»u giá» cho nháº£ sáº¡ch bÃ¹n nhá»›t, luá»™c sÆ¡ á»‘c cÃ¹ng sáº£ cÃ¢y Ä‘áº­p dáº­p rá»“i khá»u bá» náº¯p á»‘c.\nâ€¢ BÆ°á»›c 2 (Pha sá»‘t tiÃªu xanh Ä‘áº­m Ä‘Ã ): GiÃ£ dáº­p tiÃªu xanh hÃ²a cÃ¹ng nÆ°á»›c máº¯m ngon, tá»i á»›t bÄƒm, Ä‘Æ°á»ng vÃ  háº¡t nÃªm táº¡o thÃ nh há»—n há»£p sá»‘t thÆ¡m cay ná»“ng nÃ n.\nâ€¢ BÆ°á»›c 3 (NÆ°á»›ng than hoa): Äáº·t tá»«ng con á»‘c lÃªn vá»‰ nÆ°á»›ng trÃªn than Ä‘á» rá»±c, mÃºc tá»«ng muá»—ng sá»‘t tiÃªu xanh chan ngáº­p vÃ o trong miá»‡ng á»‘c.\nâ€¢ BÆ°á»›c 4 (SÃ´i sÃ¹ng sá»¥c thÆ°á»Ÿng thá»©c): NÆ°á»›ng Ä‘áº¿n khi nÆ°á»›c trong vá» á»‘c sÃ´i bÃ¹ng lÃªn ngÃ o ngáº¡t hÆ°Æ¡ng tiÃªu cay the, hÃºp trá»n nÆ°á»›c á»‘c nÃ³ng há»•i vÃ  cáº¯n miáº¿ng thá»‹t á»‘c giÃ²n sáº§n sáº­t bÃ©o ngá»t.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 75000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'CÃ¡ lÃ³c nÆ°á»›ng trui',
                        'slug' => 'ca-loc-nuong-trui',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image21.jpg',
                        'mo_ta_ngan' => 'cÃ¡ lÃ³c nÆ°á»›ng theo cÃ¡ch dÃ¢n dÃ£ miá»n sÃ´ng nÆ°á»›c.',
                        'nguyen_lieu' => 'CÃ¡ lÃ³c Ä‘á»“ng tÆ°Æ¡i roi rÃ³i cÃ²n sá»‘ng, rÆ¡m ráº¡ khÃ´ Ä‘á»“ng báº±ng sÃ´ng Cá»­u Long, que tre tÆ°Æ¡i xiÃªn cÃ¡, má»¡ hÃ nh tÃ©p má»¡ giÃ²n, Ä‘áº­u phá»™ng rang giÃ£ dáº­p, bÃ¡nh trÃ¡ng, bÃºn tÆ°Æ¡i, rá»• rau Ä‘á»“ng (lÃ¡ lá»‘t, Ä‘á»t sen, chuá»‘i chÃ¡t, kháº¿), máº¯m nÃªm tá»i á»›t pha dá»©a.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (XiÃªn cÃ¡ nguyÃªn con): CÃ¡ lÃ³c Ä‘á»“ng Ä‘á»ƒ nguyÃªn váº£y khÃ´ng má»• ruá»™t, dÃ¹ng thanh tre tÆ°Æ¡i xiÃªn tá»« miá»‡ng Ä‘áº¿n Ä‘uÃ´i rá»“i cáº¯m tháº³ng Ä‘á»©ng xuá»‘ng ná»n Ä‘áº¥t sáº¡ch.\nâ€¢ BÆ°á»›c 2 (Phá»§ rÆ¡m thui lá»­a Ä‘á»): Cháº¥t á»¥ rÆ¡m khÃ´ ngáº­p kÃ­n con cÃ¡ rá»“i chÃ¢m lá»­a Ä‘á»‘t bÃ¹ng, chá»¥m thÃªm rÆ¡m liÃªn tá»¥c khoáº£ng 8 - 10 phÃºt cho rÆ¡m chÃ¡y tÃ n thÃ nh tro nÃ³ng.\nâ€¢ BÆ°á»›c 3 (Cáº¡o tro lá»™ thá»‹t thÆ¡m): Nhá»• que cÃ¡ lÃªn, dÃ¹ng dao cáº¡o sáº¡ch lá»›p tro váº£y Ä‘en chÃ¡y bÃªn ngoÃ i Ä‘á»ƒ lá»™ thá»› thá»‹t cÃ¡ lÃ³c tráº¯ng ngáº§n bá»‘c khÃ³i thÆ¡m ná»©c mÃ¹i rÆ¡m ráº¡.\nâ€¢ BÆ°á»›c 4 (Xáº» lÆ°ng rÆ°á»›i má»¡ hÃ nh): Ráº¡ch dá»c sá»‘ng lÆ°ng cÃ¡, rÆ°á»›i má»¡ hÃ nh vÃ  Ä‘áº­u phá»™ng thÆ¡m phá»©c, gáº¯p tá»«ng thá»› thá»‹t cÃ¡ ngá»t lá»‹m cuá»™n bÃ¡nh trÃ¡ng rau Ä‘á»“ng cháº¥m máº¯m nÃªm sÃ¡nh Ä‘áº·c cay ná»“ng.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'TP. Cáº§n ThÆ¡',
                        'gia_tien' => 130000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 5.0
                    ],

                    // --- 4. Tá»ˆNH AN GIANG (8 MÃ“N) ---
                    [
                        'ten_mon' => 'BÃºn cÃ¡ ChÃ¢u Äá»‘c',
                        'slug' => 'bun-ca-chau-doc',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image2.jpg',
                        'mo_ta_ngan' => 'bÃºn cÃ¡ cÃ³ nÆ°á»›c dÃ¹ng thÆ¡m nghá»‡, thÆ°á»ng Äƒn kÃ¨m rau sá»‘ng.',
                        'nguyen_lieu' => 'CÃ¡ lÃ³c Ä‘á»“ng bÃ©o ngá»t luá»™c gá»¡ xÆ°Æ¡ng láº¥y thá»‹t náº¡c phi lÃª, cá»§ ngáº£i bÃºn giÃ£ láº¥y nÆ°á»›c cá»‘t thÆ¡m, cá»§ nghá»‡ tÆ°Æ¡i vÃ ng Æ°Æ¡m, sáº£ bÄƒm, xÆ°Æ¡ng á»‘ng heo háº§m nÆ°á»›c lÃ¨o, bÃºn tÆ°Æ¡i, bÃ´ng Ä‘iÃªn Ä‘iá»ƒn, rau nhÃºt, báº¯p chuá»‘i bÃ o, nÆ°á»›c máº¯m me á»›t.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ cÃ¡ lÃ³c): Luá»™c cÃ¡ lÃ³c vá»«a chÃ­n tá»›i, khÃ©o lÃ©o gá»¡ láº¥y thá»‹t phi lÃª tá»«ng miáº¿ng to khÃ´ng nÃ¡t, xÆ°Æ¡ng cÃ¡ giÃ£ nhuyá»…n lá»c nÆ°á»›c ngá»t náº¥u nÆ°á»›c dÃ¹ng.\nâ€¢ BÆ°á»›c 2 (XÃ o cÃ¡ vá»›i nghá»‡ & ngáº£i bÃºn): Phi thÆ¡m sáº£ tá»i vÃ  nghá»‡ tÆ°Æ¡i, cho thá»‹t cÃ¡ lÃ³c vÃ o xÃ o nháº¹ tay cho thá»› thá»‹t cÃ¡ sÄƒn vÃ ng ruá»™m Ã³ng áº£ tháº¥m gia vá»‹.\nâ€¢ BÆ°á»›c 3 (Náº¥u nÆ°á»›c lÃ¨o ngáº£i bÃºn): Háº§m nÆ°á»›c dÃ¹ng cÃ¹ng nÆ°á»›c cá»‘t ngáº£i bÃºn thÆ¡m ná»©c mÅ©i khá»­ sáº¡ch mÃ¹i tanh cá»§a cÃ¡, nÃªm máº¯m ruá»‘c pha loÃ£ng vÃ  gia vá»‹ vá»«a váº·n thanh dá»‹u.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c tÃ´ bÃºn cÃ¡): Cho bÃºn vÃ o tÃ´, xáº¿p thá»‹t cÃ¡ lÃ³c vÃ ng Æ°Æ¡m lÃªn trÃªn, chan ngáº­p nÆ°á»›c lÃ¨o sÃ´i sÃ¹ng sá»¥c, Äƒn kÃ¨m rá»• rau bÃ´ng Ä‘iÃªn Ä‘iá»ƒn vÃ ng tÆ°Æ¡i, rau nhÃºt vÃ  cháº¥m máº¯m me chua cay máº·n mÃ .',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 40000,
                        'thoi_gian_nau' => 45,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Máº¯m ChÃ¢u Äá»‘c',
                        'slug' => 'mam-chau-doc',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/mam-chau-doc.jpg',
                        'mo_ta_ngan' => 'cÃ¡c loáº¡i máº¯m cÃ¡ dÃ¹ng Äƒn kÃ¨m rau hoáº·c cháº¿ biáº¿n mÃ³n Äƒn.',
                        'nguyen_lieu' => 'CÃ¡ linh, cÃ¡ sáº·c, cÃ¡ lÃ³c tÆ°Æ¡i mÃ¹a nÆ°á»›c ná»•i An Giang, thÃ­nh gáº¡o rang vÃ ng thÆ¡m ná»©c, Ä‘Æ°á»ng thá»‘t ná»‘t nguyÃªn cháº¥t Báº£y NÃºi, muá»‘i biá»ƒn háº¡t tinh khiáº¿t, á»›t tá»i gá»«ng chÆ°ng kÃ¨m.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (á»¦ muá»‘i lÃ m cÃ¡): CÃ¡ sÃ´ng tÆ°Æ¡i lÃ m sáº¡ch á»§ muá»‘i háº¡t trong kháº¡p sÃ nh tá»« 1 - 3 thÃ¡ng cho ngáº¥m no muá»‘i.\nâ€¢ BÆ°á»›c 2 (Trá»™n thÃ­nh & Chao Ä‘Æ°á»ng thá»‘t ná»‘t): Vá»›t cÃ¡ rÃ¡o nÆ°á»›c trá»™n thÃ­nh gáº¡o rang thÆ¡m lá»«ng, sau Ä‘Ã³ chao qua máº­t Ä‘Æ°á»ng thá»‘t ná»‘t Báº£y NÃºi Ä‘á» nÃ¢u Ã³ng Ã¡nh.\nâ€¢ BÆ°á»›c 3 (á»¦ lÃªn men tá»± nhiÃªn): á»¦ cÃ¡ trong lu sÃ nh tá»« 2 - 3 thÃ¡ng ná»¯a cho thá»‹t cÃ¡ chÃ­n Ä‘á» má»m thÆ¡m ná»©c mÅ©i mÃ¹i Ä‘áº·c trÆ°ng thá»§ phá»§ máº¯m ChÃ¢u Äá»‘c.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c phong phÃº): DÃ¹ng Äƒn sá»‘ng trá»™n gá»«ng á»›t tá»i bÄƒm Äƒn cÃ¹ng cÆ¡m nguá»™i rau vÆ°á»n, chÆ°ng thá»‹t trá»©ng muá»‘i hoáº·c náº¥u nÆ°á»›c dÃ¹ng láº©u máº¯m trá»© danh.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 70000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Tung lÃ² mÃ²',
                        'slug' => 'tung-lo-mo',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/tung-lo-mo.jpg',
                        'mo_ta_ngan' => 'láº¡p xÆ°á»Ÿng bÃ² truyá»n thá»‘ng cá»§a ngÆ°á»i ChÄƒm.',
                        'nguyen_lieu' => 'Thá»‹t bÃ² báº¯p náº¡c tÆ°Æ¡i ngÆ°á»i ChÄƒm An Giang, má»¡ bÃ² bÃ©o ngáº­y, ruá»™t bÃ² lÃ m sáº¡ch phÆ¡i má»ng lÃ m vá», cÆ¡m nguá»™i lÃªn men chua nháº¹ tá»± nhiÃªn, hoa há»“i, quáº¿, tiÃªu sá», tá»i á»›t, rau sá»‘ng, muá»‘i tiÃªu chanh.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Trá»™n nhÃ¢n thá»‹t bÃ²): BÄƒm nhá» thá»‹t báº¯p bÃ² vÃ  má»¡ bÃ² theo tá»‰ lá»‡ chuáº©n, Æ°á»›p cÃ¹ng hoa há»“i, tiÃªu sá», tá»i vÃ  Ä‘áº·c biá»‡t lÃ  cÆ¡m nguá»™i lÃªn men táº¡o vá»‹ chua thanh Ä‘á»™c Ä‘Ã¡o cá»§a ngÆ°á»i ChÄƒm.\nâ€¢ BÆ°á»›c 2 (Dá»“n vÃ o ruá»™t bÃ²): Nhá»“i cháº·t nhÃ¢n vÃ o Ä‘oáº¡n ruá»™t bÃ² Ä‘Ã£ xá»­ lÃ½ sáº¡ch rá»“i dÃ¹ng dÃ¢y tháº¯t tá»«ng khÃºc trÃ²n cÄƒng bÃ³ng 10 - 12cm.\nâ€¢ BÆ°á»›c 3 (PhÆ¡i náº¯ng tá»± nhiÃªn): Äem tung lÃ² mÃ² phÆ¡i dÆ°á»›i náº¯ng gáº¯t miá»n TÃ¢y 2 - 3 náº¯ng cho khÃ´ rÃ¡o tÆ°Æ¡m má»¡ Ã³ng Ã¡nh.\nâ€¢ BÆ°á»›c 4 (NÆ°á»›ng than há»“ng): NÆ°á»›ng trÃªn báº¿p than hoa lÃ¡ch tÃ¡ch, lá»›p vá» giÃ²n cÄƒng ná»©t ra tÆ°Æ¡m nÆ°á»›c má»¡ thÆ¡m ngÃ o ngáº¡t, cáº¯n miáº¿ng thá»‹t chua ngá»t Ä‘áº­m Ä‘Ã  cháº¥m muá»‘i tiÃªu chanh tÃª tÃ¡i.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 110000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh bÃ² thá»‘t ná»‘t',
                        'slug' => 'banh-bo-thot-not',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/banh-bo-thot-not.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh má»m xá»‘p, thÆ¡m mÃ¹i Ä‘Æ°á»ng thá»‘t ná»‘t.',
                        'nguyen_lieu' => 'Bá»™t gáº¡o xay tÆ°Æ¡i, Ä‘Æ°á»ng thá»‘t ná»‘t nguyÃªn cháº¥t mÃ u vÃ ng Æ°Æ¡m, cÆ¡m trÃ¡i thá»‘t ná»‘t giÃ  xay nhuyá»…n táº¡o mÃ u vÃ  hÆ°Æ¡ng thÆ¡m ná»“ng nÃ n, nÆ°á»›c cá»‘t dá»«a bÃ©o ngáº­y, men cÆ¡m rÆ°á»£u á»§ ná»Ÿ phá»“ng.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (á»¦ men bá»™t gáº¡o): Trá»™n bá»™t gáº¡o cÃ¹ng men cÆ¡m rÆ°á»£u vÃ  nÆ°á»›c á»§ qua Ä‘Ãªm cho bá»™t lÃªn men sá»§i bá»t khÃ­ tÄƒm táº¯p.\nâ€¢ BÆ°á»›c 2 (Náº¥u Ä‘Æ°á»ng thá»‘t ná»‘t): Tháº¯ng tan Ä‘Æ°á»ng thá»‘t ná»‘t Báº£y NÃºi cÃ¹ng nÆ°á»›c cá»‘t dá»«a vÃ  cÆ¡m thá»‘t ná»‘t thÆ¡m lá»«ng, Ä‘á»ƒ áº¥m rá»“i hÃ²a Ä‘á»u vÃ o Ã¢u bá»™t.\nâ€¢ BÆ°á»›c 3 (Háº¥p cÃ¡ch thá»§y rá»… tre): Äá»• bá»™t vÃ o khuÃ´n lÃ³t lÃ¡ chuá»‘i, háº¥p cÃ¡ch thá»§y trong xá»­ng lá»­a lá»›n khoáº£ng 20 phÃºt cho bÃ¡nh ná»Ÿ bung phá»“ng rá»±c rá»¡ ngáº­p rá»… tre má»m xá»‘p.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): BÃ¡nh cÃ³ mÃ u vÃ ng sÃ¡p ong tuyá»‡t Ä‘áº¹p, ráº¯c dá»«a náº¡o sá»£i lÃªn máº·t bÃ¡nh, cáº¯n vÃ o má»m máº¡i bÃ¹i bÃ©o tan ngay trÃªn Ä‘áº§u lÆ°á»¡i.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 25000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Gá»i sáº§u Ä‘Ã¢u',
                        'slug' => 'goi-sau-dau',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/goi-sau-dau.jpg',
                        'mo_ta_ngan' => 'gá»i lÃ¡ sáº§u Ä‘Ã¢u káº¿t há»£p khÃ´ cÃ¡, tÃ´m hoáº·c thá»‹t.',
                        'nguyen_lieu' => 'Äá»t vÃ  hoa sáº§u Ä‘Ã¢u non xanh mÃ¡t vÃ¹ng ChÃ¢u Äá»‘c An Giang, khÃ´ cÃ¡ lÃ³c Ä‘á»“ng hoáº·c khÃ´ cÃ¡ sáº·c nÆ°á»›ng xÃ© sá»£i, thá»‹t ba rá»i luá»™c thÃ¡i má»ng, tÃ´m sÃº bÃ³c vá», dÆ°a leo bÄƒm sá»£i, xoÃ i xanh bÄƒm sá»£i, nÆ°á»›c máº¯m me Ä‘Æ°á»ng thá»‘t ná»‘t sá»n sá»‡t.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Trá»¥ng lÃ¡ sáº§u Ä‘Ã¢u): Rá»­a sáº¡ch Ä‘á»t vÃ  hoa sáº§u Ä‘Ã¢u non, cháº§n nhanh qua nÆ°á»›c sÃ´i nÃ³ng rá»“i ngÃ¢m ngay vÃ o nÆ°á»›c Ä‘Ã¡ láº¡nh Ä‘á»ƒ giá»¯ mÃ u xanh vÃ  giáº£m bá»›t vá»‹ Ä‘áº¯ng gáº¯t.\nâ€¢ BÆ°á»›c 2 (NÆ°á»›ng khÃ´ cÃ¡ & TÃ´m thá»‹t): NÆ°á»›ng khÃ´ cÃ¡ lÃ³c trÃªn than há»“ng Ä‘áº­p dáº­p xÃ© nhá» tÆ¡i xá»‘p; luá»™c chÃ­n thá»‹t ba rá»i vÃ  tÃ´m sÃº thÃ¡i lÃ¡t vá»«a Äƒn.\nâ€¢ BÆ°á»›c 3 (Trá»™n gá»i tháº¥m vá»‹): Cho Ä‘á»t sáº§u Ä‘Ã¢u, khÃ´ cÃ¡ xÃ©, thá»‹t ba rá»i, dÆ°a leo, xoÃ i xanh vÃ o thau lá»›n, rÆ°á»›i ngáº­p nÆ°á»›c máº¯m me chua ngá»t cay dá»‹u vÃ o bÃ³p Ä‘á»u nháº¹ tay.\nâ€¢ BÆ°á»›c 4 (Cáº£m nháº­n tinh hoa): ThÆ°á»Ÿng thá»©c Ä‘Ä©a gá»i Ä‘á»™c Ä‘Ã¡o: thoáº¡t Ä‘áº§u vá»‹ Ä‘áº¯ng nháº¹ cá»§a sáº§u Ä‘Ã¢u Ä‘Ã¡nh thá»©c vá»‹ giÃ¡c, ngay sau Ä‘Ã³ vá»‹ ngá»t Ä‘áº­m cá»§a khÃ´ cÃ¡ tÃ´m thá»‹t vÃ  vá»‹ chua ngá»t cá»§a me thá»‘t ná»‘t lan tá»a tuyá»‡t Ä‘á»‰nh.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 80000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'CÆ¡m táº¥m Long XuyÃªn',
                        'slug' => 'com-tam-long-xuyen',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image18.jpg',
                        'mo_ta_ngan' => 'cÆ¡m táº¥m Äƒn cÃ¹ng thá»‹t thÃ¡i sá»£i, trá»©ng vÃ  nÆ°á»›c máº¯m.',
                        'nguyen_lieu' => 'Gáº¡o táº¥m nhuyá»…n háº¡t nhá» má»‹n dáº»o thÆ¡m, thá»‹t heo khÃ¬a nÆ°á»›c dá»«a thÆ¡m má»m thÃ¡i sá»£i tÆ¡i nhuyá»…n, bÃ¬ heo thÃ¡i chá»‰ trá»™n thÃ­nh gáº¡o, cháº£ trá»©ng chÆ°ng hoáº·c trá»©ng kho cáº¯t lÃ¡t má»ng dÃ i, má»¡ hÃ nh tÃ©p má»¡ giÃ²n, nÆ°á»›c máº¯m káº¹o chua ngá»t sá»n sá»‡t Ä‘á»™c quyá»n xá»© Long XuyÃªn.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Náº¥u cÆ¡m táº¥m nhuyá»…n): Gáº¡o táº¥m nhuyá»…n ngÃ¢m rÃ¡o náº¥u chÃ­n tá»›i báº±ng ná»“i háº¥p cÃ¡ch thá»§y cho tá»«ng háº¡t cÆ¡m tÆ¡i xá»‘p khÃ´ng khÃ´ cá»©ng.\nâ€¢ BÆ°á»›c 2 (KhÃ¬a thá»‹t heo nÆ°á»›c dá»«a): Thá»‹t heo Æ°á»›p tá»i tiÃªu gia vá»‹ khÃ¬a cÃ¹ng nÆ°á»›c dá»«a xiÃªm Ä‘áº¿n khi káº¹o láº¡i ngáº£ mÃ u cÃ¡nh giÃ¡n Ä‘áº­m Ä‘Ã , vá»›t ra thÃ¡i thÃ nh tá»«ng sá»£i chá»‰ tháº­t nhuyá»…n Ä‘áº·c trÆ°ng Long XuyÃªn.\nâ€¢ BÆ°á»›c 3 (Cáº¯t sá»£i trá»©ng kho & cháº£): Trá»©ng vá»‹t kho nÆ°á»›c dá»«a hoáº·c cháº£ trá»©ng háº¥p Ä‘Æ°á»£c thÃ¡i lÃ¡t dÃ i má»ng xáº¿p Ä‘á»u cÃ¹ng bÃ¬ heo.\nâ€¢ BÆ°á»›c 4 (BÃ y Ä‘Ä©a & ThÆ°á»Ÿng thá»©c): Xá»›i cÆ¡m táº¥m ra Ä‘Ä©a pháº³ng, ráº£i Ä‘á»u thá»‹t khÃ¬a thÃ¡i sá»£i phá»§ kÃ­n máº·t cÆ¡m, thÃªm trá»©ng sá»£i, bÃ¬, chan muá»—ng má»¡ hÃ nh Ã³ng áº£ vÃ  rÆ°á»›i nÆ°á»›c máº¯m káº¹o sá»n sá»‡t chua ngá»t cay the Ä‘áº­m Ä‘Ã  khÃ³ quÃªn.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 40000,
                        'thoi_gian_nau' => 35,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'GÃ  Ä‘á»‘t Ã” Thum',
                        'slug' => 'ga-dot-o-thum',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image12.jpg',
                        'mo_ta_ngan' => 'gÃ  nÆ°á»›ng Ä‘áº­m vá»‹, gáº¯n vá»›i vÃ¹ng Tri TÃ´n.',
                        'nguyen_lieu' => '1 con gÃ  Ä‘á»“i Tri TÃ´n tháº£ vÆ°á»n thá»‹t sÄƒn cháº¯c ngá»t lá»‹m (khoáº£ng 1.2kg), lÃ¡ chÃºc (chanh rá»«ng Kaffir) tÆ°Æ¡i vÃ¹ng Báº£y NÃºi, sáº£ cÃ¢y Ä‘áº­p dáº­p, tá»i á»›t bÄƒm nhuyá»…n, muá»‘i háº¡t, dáº§u Ä‘iá»u, ná»“i gang Ä‘Ã¡y dÃ y, chÃ©n muá»‘i á»›t lÃ¡ chÃºc Ä‘Ã¢m nhuyá»…n.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Æ¯á»›p gÃ  hÆ°Æ¡ng vá»‹ lÃ¡ chÃºc): GÃ  lÃ m sáº¡ch Æ°á»›p ká»¹ tá»« trong ra ngoÃ i vá»›i sáº£ bÄƒm, tá»i á»›t, máº­t ong vÃ  Ä‘áº·c biá»‡t lÃ  lÃ¡ chÃºc thÃ¡i sá»£i dáº­p dáº­p trong 40 phÃºt cho ngáº¥m sÃ¢u.\nâ€¢ BÆ°á»›c 2 (LÃ³t Ä‘Ã¡y ná»“i Ä‘á»‘t gÃ ): Ráº£i má»™t lá»›p muá»‘i háº¡t dÃ y dÆ°á»›i Ä‘Ã¡y ná»“i gang, phá»§ kÃ­n má»™t lá»›p sáº£ cÃ¢y Ä‘áº­p dáº­p vÃ  lÃ¡ chÃºc tÆ°Æ¡i lÃªn trÃªn.\nâ€¢ BÆ°á»›c 3 (Äá»‘t gÃ  trong ná»“i gang): Äáº·t gÃ  lÃªn lá»›p sáº£ lÃ¡ chÃºc, Ä‘áº­y tháº­t kÃ­n vung rá»“i Ä‘á»‘t trÃªn lá»­a lá»›n 20 phÃºt, sau Ä‘Ã³ háº¡ lá»­a liu riu 20 phÃºt cho gÃ  chÃ­n vÃ ng giÃ²n rá»¥m báº±ng hÆ¡i nÃ³ng tinh dáº§u chÃºc tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 4 (XÃ© gÃ  cháº¥m muá»‘i á»›t chÃºc): Da gÃ  cÄƒng bÃ³ng vÃ ng ruá»™m thÆ¡m ná»“ng nÃ n mÃ¹i lÃ¡ chÃºc Ä‘á»™c nháº¥t vÃ´ nhá»‹ vÃ¹ng há»“ Ã” Thum, xÃ© tá»«ng miáº¿ng thá»‹t nÃ³ng há»•i cháº¥m chÃ©n muá»‘i á»›t lÃ¡ chÃºc chua cay cay ná»“ng quyáº¿n rÅ©.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 250000,
                        'thoi_gian_nau' => 50,
                        'danh_gia' => 5.0
                    ],
                    [
                        'ten_mon' => 'ÄÆ°á»ng thá»‘t ná»‘t Báº£y NÃºi',
                        'slug' => 'duong-thot-not-bay-nui',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'Ä‘áº·c sáº£n dÃ¹ng lÃ m bÃ¡nh, chÃ¨ vÃ  cÃ¡c mÃ³n ngá»t.',
                        'nguyen_lieu' => 'NÆ°á»›c nhá»¥y hoa thá»‘t ná»‘t tÆ°Æ¡i há»©ng tá»« trÃªn ngá»n cÃ¢y thá»‘t ná»‘t vÃ¹ng Tri TÃ´n - Tá»‹nh BiÃªn (An Giang), cháº£o gang lá»›n náº¥u Ä‘Æ°á»ng, lÃ¡ thá»‘t ná»‘t Ä‘an khuÃ´n gÃ³i.',
                        'cach_lam' => 'â€¢ BÆ°á»›c 1 (Há»©ng nÆ°á»›c hoa thá»‘t ná»‘t): NgÆ°á»i dÃ¢n trÃ¨o lÃªn ngá»n cÃ¢y thá»‘t ná»‘t cao vÃºt tá»« sÃ¡ng sá»›m cáº¯t cuá»‘ng hoa há»©ng tá»«ng giá»t nÆ°á»›c hoa ngá»t lá»‹m thanh khiáº¿t.\nâ€¢ BÆ°á»›c 2 (Náº¥u cÃ´ Ä‘áº·c trÃªn cháº£o gang): Lá»c sáº¡ch nÆ°á»›c hoa thá»‘t ná»‘t Ä‘á»• vÃ o cháº£o gang lá»›n Ä‘un sÃ´i liu riu liÃªn tá»¥c nhiá»u giá» trÃªn lÃ² cá»§i, Ä‘áº£o Ä‘á»u tay cho nÆ°á»›c bá»‘c hÆ¡i sÃ¡nh Ä‘áº·c láº¡i.\nâ€¢ BÆ°á»›c 3 (Äá»• khuÃ´n viÃªn Ä‘Æ°á»ng): Khi Ä‘Æ°á»ng chuyá»ƒn sang mÃ u vÃ ng Æ°Æ¡m sá»n sá»‡t, ngÆ°á»i thá»£ khuáº¥y liÃªn tá»¥c cho nguá»™i rá»“i rÃ³t vÃ o tá»«ng khuÃ´n lÃ¡ thá»‘t ná»‘t hoáº·c khuÃ´n trÃ²n truyá»n thá»‘ng.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c hÆ°Æ¡ng vá»‹): ÄÆ°á»ng thá»‘t ná»‘t cÃ³ vá»‹ ngá»t thanh dá»‹u mÃ¡t khÃ´ng gáº¯t cá»•, thoang thoáº£ng mÃ¹i thÆ¡m quyáº¿n rÅ© dÃ¹ng náº¥u chÃ¨, kho cÃ¡ hoáº·c lÃ m bÃ¡nh bÃ² tuyá»‡t háº£o.',
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'An Giang',
                        'gia_tien' => 50000,
                        'thoi_gian_nau' => 120,
                        'danh_gia' => 4.9
                    ]
                ];

                $stmtSouthern = $this->conn->prepare("INSERT INTO `mon_an` (`ten_mon`, `slug`, `hinh_anh`, `mo_ta_ngan`, `nguyen_lieu`, `cach_lam`, `id_vung_mien`, `tinh_thanh`, `gia_tien`, `thoi_gian_nau`, `danh_gia`) 
                    VALUES (:ten_mon, :slug, :hinh_anh, :mo_ta_ngan, :nguyen_lieu, :cach_lam, :id_vung_mien, :tinh_thanh, :gia_tien, :thoi_gian_nau, :danh_gia)
                    ON DUPLICATE KEY UPDATE 
                    `ten_mon` = VALUES(`ten_mon`),
                    `mo_ta_ngan` = VALUES(`mo_ta_ngan`),
                    `nguyen_lieu` = VALUES(`nguyen_lieu`),
                    `cach_lam` = VALUES(`cach_lam`),
                    `tinh_thanh` = VALUES(`tinh_thanh`),
                    `id_vung_mien` = VALUES(`id_vung_mien`),
                    `hinh_anh` = VALUES(`hinh_anh`),
                    `gia_tien` = VALUES(`gia_tien`),
                    `thoi_gian_nau` = VALUES(`thoi_gian_nau`),
                    `danh_gia` = VALUES(`danh_gia`)");

                foreach ($southernFourProvinces as $sp) {
                    $stmtSouthern->execute([
                        ':ten_mon' => $sp['ten_mon'],
                        ':slug' => $sp['slug'],
                        ':hinh_anh' => $sp['hinh_anh'],
                        ':mo_ta_ngan' => $sp['mo_ta_ngan'],
                        ':nguyen_lieu' => $sp['nguyen_lieu'],
                        ':cach_lam' => $sp['cach_lam'],
                        ':id_vung_mien' => $sp['id_vung_mien'],
                        ':tinh_thanh' => $sp['tinh_thanh'],
                        ':gia_tien' => $sp['gia_tien'],
                        ':thoi_gian_nau' => $sp['thoi_gian_nau'],
                        ':danh_gia' => $sp['danh_gia']
                    ]);
                }

                // XÃ³a cÃ¡c báº£n ghi cÅ© trÃ¹ng láº·p hoáº·c khÃ´ng Ä‘á»“ng nháº¥t Ä‘á»ƒ danh sÃ¡ch 8 mÃ³n má»—i tá»‰nh thÃ nh hiá»ƒn thá»‹ sáº¡ch sáº½
                // Äá»“ng Nai
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` IN ('goi-buoi-tan-trieu-dong-nai', 'ga-hap-buoi-dong-nai', 'canh-chua-la-giang-long-bo', 'lau-kho-qua-rung-dong-nai')");
                // TÃ¢y Ninh
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` IN ('banh-canh-trang-bang-tay-ninh', 'than-lan-nui-ba-den-tay-ninh', 'theo-leo-tay-ninh', 'nem-buoi-tay-ninh')");
                // TP. Cáº§n ThÆ¡
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` IN ('ca-loc-nuong-trui-can-tho', 'banh-duc-la-dua-can-tho', 'chuot-dong-nuong-lu', 'vit-nau-chao-can-tho', 'banh-xeo-can-tho')");
                // An Giang
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` IN ('banh-canh-tep-chau-doc', 'ga-dot-tri-ton', 'du-du-dam-tri-ton', 'lau-mam-chau-doc', 'bo-bay-mon-nui-sam')");

                // Äáº£m báº£o cáº­p nháº­t chÃ­nh xÃ¡c tá»‰nh thÃ nh vÃ  vÃ¹ng miá»n cho toÃ n bá»™ 32 mÃ³n
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Äá»“ng Nai', `id_vung_mien` = 3 WHERE `slug` IN ('goi-buoi-tan-trieu', 'ga-hap-buoi', 'lau-la-kho-qua-rung', 'com-ga-ca-man', 'de-com-chien-nuoc-mam', 'nam-moi-dong-nai', 'chom-chom-long-khanh', 'buoi-tan-trieu')");
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'TÃ¢y Ninh', `id_vung_mien` = 3 WHERE `slug` IN ('banh-trang-phoi-suong-trang-bang', 'bo-to-tay-ninh', 'banh-canh-trang-bang', 'muoi-tom-tay-ninh', 'banh-trang-me', 'banh-trang-cuon-bo', 'than-lan-nui-ba-den', 'mang-cau-ba-den')");
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'TP. Cáº§n ThÆ¡', `id_vung_mien` = 3 WHERE `slug` IN ('banh-cong-can-tho', 'lau-vit-nau-chao', 'banh-tet-la-cam', 'nem-nuong-cai-rang', 'banh-hoi-mat-vong-phong-dien', 'pizza-hu-tieu', 'oc-nuong-tieu-xanh', 'ca-loc-nuong-trui')");
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'An Giang', `id_vung_mien` = 3 WHERE `slug` IN ('bun-ca-chau-doc', 'mam-chau-doc', 'tung-lo-mo', 'banh-bo-thot-not', 'goi-sau-dau', 'com-tam-long-xuyen', 'ga-dot-o-thum', 'duong-thot-not-bay-nui')");

                // =========================================================================
                // CHUáº¨N HÃ“A VÃ€ THÃŠM Äáº¦Y Äá»¦ Äáº¶C Sáº¢N Äá»’NG THÃP, VÄ¨NH LONG, CÃ€ MAU (24 MÃ“N)
                // =========================================================================
                $southernThreeRemainingProvinces = [
                    // --- 6. Tá»ˆNH Äá»’NG THÃP (8 MÃ“N) ---
                    [
                        'ten_mon' => 'Há»§ tiáº¿u Sa ÄÃ©c',
                        'slug' => 'hu-tieu-sa-dec',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image8.jpg',
                        'mo_ta_ngan' => 'sá»£i há»§ tiáº¿u dai má»m, Äƒn vá»›i thá»‹t, tÃ´m vÃ  nÆ°á»›c dÃ¹ng.',
                        'nguyen_lieu' => 'Sá»£i há»§ tiáº¿u bá»™t gáº¡o Sa ÄÃ©c tÆ°Æ¡i dai má»m tráº¯ng muá»‘t, thá»‹t náº¡c heo, tÃ´m sÃº tÆ°Æ¡i, gan heo, xÃ¡ xÃ­u, thá»‹t bÄƒm, xÆ°Æ¡ng á»‘ng heo ninh nÆ°á»›c dÃ¹ng, cáº§n tÃ u, háº¹, tá»i phi thÆ¡m, tÆ°Æ¡ng Ä‘en Sa ÄÃ©c.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Ninh nÆ°á»›c lÃ¨o): XÆ°Æ¡ng á»‘ng háº§m liu riu vá»›i cá»§ cáº£i vÃ  khÃ´ má»±c nÆ°á»›ng cho nÆ°á»›c dÃ¹ng trong veo, ngá»t thanh tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 2 (Cháº¿ biáº¿n topping): Luá»™c chÃ­n tÃ´m sÃº, gan heo, thÃ¡i lÃ¡t xÃ¡ xÃ­u Ä‘á» thÆ¡m, xÃ o chÃ­n thá»‹t náº¡c bÄƒm vá»›i tá»i phi.\nâ€¢ BÆ°á»›c 3 (Trá»¥ng sá»£i há»§ tiáº¿u): Trá»¥ng sá»£i há»§ tiáº¿u Sa ÄÃ©c qua nÆ°á»›c sÃ´i vá»«a má»m tá»›i, xÃ³c Ä‘á»u chÃºt dáº§u tá»i phi cho sá»£i Ã³ng áº£ khÃ´ng dÃ­nh.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Cho há»§ tiáº¿u vÃ o tÃ´, xáº¿p ngáº­p trÃ n tÃ´m thá»‹t xÃ¡ xÃ­u gan heo, ráº¯c cáº§n tÃ u háº¹ lÃ¡ vÃ  chan nÆ°á»›c lÃ¨o nÃ³ng há»•i hoáº·c Äƒn khÃ´ vá»›i sá»‘t tÆ°Æ¡ng Sa ÄÃ©c Ä‘áº­m Ä‘Ã .",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 45000,
                        'thoi_gian_nau' => 40,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Nem Lai Vung',
                        'slug' => 'nem-lai-vung',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image16.jpg',
                        'mo_ta_ngan' => 'nem chua cÃ³ vá»‹ chua ngá»t, thÆ¡m vÃ  hÆ¡i cay.',
                        'nguyen_lieu' => 'Thá»‹t náº¡c Ä‘Ã¹i heo tÆ°Æ¡i nÃ³ng dáº»o, bÃ¬ heo thÃ¡i chá»‰ giÃ²n sáº§n sáº­t, tá»i cÃ´ Ä‘Æ¡n, tiÃªu sá» Ä‘en nguyÃªn háº¡t, á»›t hiá»ƒm Ä‘á», lÃ¡ chÃ¹m ruá»™t hoáº·c lÃ¡ vÃ´ng non, lÃ¡ chuá»‘i tÆ°Æ¡i gÃ³i ngoÃ i.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Quáº¿t thá»‹t náº¡c): Thá»‹t heo tÆ°Æ¡i vá»«a má»• quáº¿t nhuyá»…n má»‹n tháº­t dáº»o quÃ¡nh, nÃªm gia vá»‹ Ä‘Æ°á»ng phÃ¨n vÃ  muá»‘i vá»«a váº·n.\nâ€¢ BÆ°á»›c 2 (Trá»™n bÃ¬ & TiÃªu á»›t): BÃ¬ luá»™c thÃ¡i chá»‰ trá»™n Ä‘á»u vÃ o thá»‹t quáº¿t, thÃªm háº¡t tiÃªu sá» vÃ  lÃ¡t tá»i á»›t tÆ°Æ¡i.\nâ€¢ BÆ°á»›c 3 (GÃ³i nem lÃ¡ chÃ¹m ruá»™t): Vo viÃªn nem vuÃ´ng vá»©c, bá»c 1 lá»›p lÃ¡ chÃ¹m ruá»™t táº¡o vá»‹ chua thanh tá»± nhiÃªn rá»“i cuá»™n cháº·t báº±ng nhiá»u lá»›p lÃ¡ chuá»‘i xanh.\nâ€¢ BÆ°á»›c 4 (á»¦ lÃªn men tá»± nhiÃªn): Cá»™t dÃ¢y chuá»‘i thÃ nh tá»«ng chÃ¹m, á»§ nÆ¡i thoÃ¡ng mÃ¡t tá»« 3 - 4 ngÃ y cho nem lÃªn men Ä‘á» há»“ng, má»Ÿ ra dáº­y mÃ¹i thÆ¡m ná»“ng chua ngá»t cay the ná»©c tiáº¿ng.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 45000,
                        'thoi_gian_nau' => 60,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Láº©u cÃ¡ linh bÃ´ng Ä‘iÃªn Ä‘iá»ƒn',
                        'slug' => 'lau-ca-linh-bong-dien-dien',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image38.jpg',
                        'mo_ta_ngan' => 'mÃ³n láº©u chua ngá»t Ä‘áº·c trÆ°ng mÃ¹a nÆ°á»›c ná»•i.',
                        'nguyen_lieu' => 'CÃ¡ linh non Ä‘áº§u mÃ¹a nÆ°á»›c ná»•i xÆ°Æ¡ng má»m thá»‹t bÃ©o ngá»t, bÃ´ng Ä‘iÃªn Ä‘iá»ƒn vÃ ng tÆ°Æ¡i hÃ¡i mÃ© sÃ´ng, me xanh hoáº·c me chÃ­n, nÆ°á»›c dá»«a tÆ°Æ¡i, ngÃ² gai, rau om, á»›t sá»«ng, nÆ°á»›c máº¯m nhÄ©.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ cÃ¡ linh): CÃ¡ linh non mÃ³c mang bá» ruá»™t rá»­a sáº¡ch nÆ°á»›c muá»‘i loÃ£ng, Ä‘á»ƒ rÃ¡o giá»¯ nguyÃªn Ä‘á»™ tÆ°Æ¡i ngá»t bÃ©o ngáº­y.\nâ€¢ BÆ°á»›c 2 (Náº¥u nÆ°á»›c láº©u chua thanh): Náº¥u nÆ°á»›c dá»«a tÆ°Æ¡i cÃ¹ng nÆ°á»›c cá»‘t me cho vá»‹ chua ngá»t dá»‹u, nÃªm chÃºt nÆ°á»›c máº¯m cÃ¡ linh thÆ¡m lá»«ng.\nâ€¢ BÆ°á»›c 3 (Tháº£ cÃ¡ linh): Khi nÆ°á»›c láº©u sÃ´i bÃ¹ng, tháº£ cÃ¡ linh non vÃ o náº¥u nhanh 2 - 3 phÃºt cho cÃ¡ vá»«a chÃ­n tá»›i Ä‘á»ƒ khÃ´ng bá»‹ nÃ¡t.\nâ€¢ BÆ°á»›c 4 (NhÃºng bÃ´ng Ä‘iÃªn Ä‘iá»ƒn): NhÃºng tá»«ng vá»‘c bÃ´ng Ä‘iÃªn Ä‘iá»ƒn vÃ ng rá»±c vÃ  rau nhÃºt giÃ²n sáº§n sáº­t, gáº¯p cÃ¡ linh cháº¥m nÆ°á»›c máº¯m á»›t hiá»ƒm nguyÃªn cháº¥t Äƒn cÃ¹ng bÃºn tÆ°Æ¡i.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 160000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 5.0
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh phá»“ng tÃ´m Sa Giang',
                        'slug' => 'banh-phong-tom-sa-giang',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/banh-phong-tom-sa-giang.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh phá»“ng giÃ²n, thÆ¡m vá»‹ tÃ´m.',
                        'nguyen_lieu' => 'Thá»‹t tÃ´m nÆ°á»›c ngá»t tÆ°Æ¡i xay nhuyá»…n (chiáº¿m tá»‰ lá»‡ cao), tinh bá»™t khoai mÃ¬ Ä‘áº·c sáº£n Sa ÄÃ©c, háº¡t tiÃªu Ä‘en giÃ£ nhá», tá»i bÄƒm, muá»‘i, Ä‘Æ°á»ng, dáº§u Äƒn chiÃªn bÃ¡nh.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (NhÃ o bá»™t thá»‹t tÃ´m): Trá»™n thá»‹t tÃ´m xay nhuyá»…n vá»›i bá»™t mÃ¬, lÃ²ng tráº¯ng trá»©ng vÃ  tiÃªu sá» thÃ nh khá»‘i dáº»o má»‹n hÃ¬nh trá»¥ dÃ i.\nâ€¢ BÆ°á»›c 2 (Háº¥p & Cáº¯t lÃ¡t má»ng): Háº¥p chÃ­n khá»‘i bá»™t tÃ´m, Ä‘á»ƒ nguá»™i qua Ä‘Ãªm cho sÄƒn cháº¯c rá»“i dÃ¹ng mÃ¡y cáº¯t thÃ nh tá»«ng lÃ¡t má»ng Ä‘á»u tÄƒm táº¯p.\nâ€¢ BÆ°á»›c 3 (PhÆ¡i khÃ´ giÃ²n): PhÆ¡i cÃ¡c lÃ¡t bÃ¡nh dÆ°á»›i náº¯ng giÃ²n miá»n TÃ¢y cho khÃ´ cá»©ng trong veo.\nâ€¢ BÆ°á»›c 4 (ChiÃªn phá»“ng giÃ²n rá»¥m): Tháº£ bÃ¡nh vÃ o cháº£o dáº§u sÃ´i giÃ , bÃ¡nh ná»Ÿ phá»“ng gáº¥p 3 - 4 láº§n tráº¯ng xá»‘p giÃ²n tan dáº­y mÃ¹i thÆ¡m lá»«ng cá»§a tÃ´m ngá»t lá»‹m.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 35000,
                        'thoi_gian_nau' => 15,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'CÃ¡ lÃ³c nÆ°á»›ng trui',
                        'slug' => 'ca-loc-nuong-trui-dong-thap',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image21.jpg',
                        'mo_ta_ngan' => 'cÃ¡ nÆ°á»›ng dÃ¢n dÃ£, Äƒn cÃ¹ng rau vÃ  nÆ°á»›c cháº¥m.',
                        'nguyen_lieu' => 'CÃ¡ lÃ³c Ä‘á»“ng ThÃ¡p MÆ°á»i tÆ°Æ¡i sá»‘ng, bÃºp lÃ¡ sen non Äá»“ng ThÃ¡p, rÆ¡m khÃ´ Ä‘á»“ng ná»™i, máº¯m me chua ngá»t sá»n sá»‡t, Ä‘áº­u phá»™ng rang giÃ£ dáº­p, má»¡ hÃ nh tÃ©p má»¡, bÃºn tÆ°Æ¡i, bÃ¡nh trÃ¡ng gáº¡o.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (XiÃªn que nÆ°á»›ng rÆ¡m): CÃ¡ lÃ³c Ä‘á»“ng nguyÃªn con dÃ¹ng thanh tre xiÃªn tá»« Ä‘áº§u Ä‘áº¿n Ä‘uÃ´i cáº¯m tháº³ng xuá»‘ng Ä‘áº¥t phá»§ rÆ¡m khÃ´ Ä‘á»‘t rá»±c lá»­a.\nâ€¢ BÆ°á»›c 2 (Cáº¡o sáº¡ch váº£y tro): Sau khi rÆ¡m tÃ n, nhá»• que cÃ¡ cáº¡o sáº¡ch lá»›p váº£y chÃ¡y Ä‘en bÃªn ngoÃ i lÃ m lá»™ lá»›p thá»‹t cÃ¡ tráº¯ng ngá»t ngÃ o thÆ¡m ná»©c.\nâ€¢ BÆ°á»›c 3 (RÆ°á»›i má»¡ hÃ nh): Má»• dá»c lÆ°ng cÃ¡, rÆ°á»›i má»¡ hÃ nh bÃ©o ngáº­y vÃ  ráº¯c Ä‘áº­u phá»™ng rang giÃ²n thÆ¡m phá»©c.\nâ€¢ BÆ°á»›c 4 (Cuá»‘n lÃ¡ sen non cháº¥m máº¯m me): DÃ¹ng tá»«ng bÃºp lÃ¡ sen non Äá»“ng ThÃ¡p cuá»‘n trá»n thá»› thá»‹t cÃ¡ lÃ³c Ä‘á»“ng ngá»t lá»‹m cháº¥m ngáº­p chÃ©n máº¯m me chua ngá»t cay ná»“ng.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 140000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'CÆ¡m sen',
                        'slug' => 'com-sen',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/com-sen.jpg',
                        'mo_ta_ngan' => 'cÆ¡m káº¿t há»£p háº¡t sen vÃ  cÃ¡c nguyÃªn liá»‡u khÃ¡c.',
                        'nguyen_lieu' => 'Gáº¡o thÆ¡m lÃºa mÃ¹a Sa ÄÃ©c, háº¡t sen tÆ°Æ¡i Äá»“ng ThÃ¡p bÃ³c vá» bá» tim háº¥p chÃ­n bÃ¹i bÃ©o, tÃ´m tÆ°Æ¡i thÃ¡i háº¡t lá»±u, láº¡p xÆ°á»Ÿng, thá»‹t gÃ  xÃ©, náº¥m Ä‘Ã´ng cÃ´, lÃ¡ sen lá»›n tÆ°Æ¡i xanh gÃ³i cÆ¡m.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Náº¥u cÆ¡m & Háº¥p háº¡t sen): Náº¥u cÆ¡m tá»« gáº¡o má»›i dáº»o thÆ¡m rÃ¡o háº¡t. Háº¡t sen tÆ°Æ¡i Äá»“ng ThÃ¡p háº¥p chÃ­n bÃ¹i má»m khÃ´ng nÃ¡t.\nâ€¢ BÆ°á»›c 2 (XÃ o hÃ²a quyá»‡n nguyÃªn liá»‡u): XÃ o sÄƒn tÃ´m, láº¡p xÆ°á»Ÿng, gÃ  xÃ©, náº¥m Ä‘Ã´ng cÃ´ cÃ¹ng hÃ nh phi vÃ  chÃºt dáº§u hÃ o tiÃªu sá».\nâ€¢ BÆ°á»›c 3 (Trá»™n cÆ¡m háº¡t sen): Trá»™n Ä‘á»u cÆ¡m nÃ³ng vá»›i cÃ¡c nguyÃªn liá»‡u xÃ o vÃ  háº¡t sen thÆ¡m bÃ¹i cho ngáº¥m Ä‘á»u gia vá»‹.\nâ€¢ BÆ°á»›c 4 (GÃ³i lÃ¡ sen háº¥p cÃ¡ch thá»§y): Tráº£i lÃ¡ sen tÆ°Æ¡i to báº£n, mÃºc cÆ¡m vÃ o giá»¯a gÃ³i kÃ­n láº¡i thÃ nh hÃ¬nh vuÃ´ng hoáº·c bÃºp sen rá»“i háº¥p cÃ¡ch thá»§y 15 phÃºt cho hÆ°Æ¡ng thÆ¡m thanh khiáº¿t cá»§a lÃ¡ sen tháº¥m sÃ¢u tá»«ng háº¡t cÆ¡m.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 75000,
                        'thoi_gian_nau' => 40,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'ChÃ¨ háº¡t sen',
                        'slug' => 'che-hat-sen',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'mÃ³n chÃ¨ thanh ngá»t tá»« háº¡t sen.',
                        'nguyen_lieu' => 'Háº¡t sen tÆ°Æ¡i Äá»“ng ThÃ¡p bá»Ÿ bÃ¹i thÆ¡m mÃ¡t, Ä‘Æ°á»ng phÃ¨n thanh dá»‹u, long nhÃ£n, hoa lÃ i tÆ°Æ¡i táº¡o hÆ°Æ¡ng thÆ¡m thoang thoáº£ng, Ä‘Ã¡ hoa tuyáº¿t mÃ¡t láº¡nh.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ háº¡t sen): Háº¡t sen tÆ°Æ¡i lá»™t sáº¡ch vá» xanh, thÃ´ng tim sen Ä‘á»ƒ khÃ´ng bá»‹ Ä‘áº¯ng, rá»­a sáº¡ch Ä‘á»ƒ rÃ¡o.\nâ€¢ BÆ°á»›c 2 (Náº¥u háº¡t sen bá»Ÿ má»m): Cho háº¡t sen vÃ o ná»“i nÆ°á»›c luá»™c lá»­a nhá» Ä‘áº¿n khi háº¡t sen chÃ­n má»m bÃ¹i dáº»o ná»Ÿ bung nháº¹.\nâ€¢ BÆ°á»›c 3 (Náº¥u Ä‘Æ°á»ng phÃ¨n thanh khiáº¿t): Náº¥u nÆ°á»›c Ä‘Æ°á»ng phÃ¨n trong veo vá»›i long nhÃ£n, cho háº¡t sen vÃ o Ä‘un liu riu thÃªm 10 phÃºt Ä‘á»ƒ tá»«ng háº¡t sen tháº¥m Ä‘Æ°á»£m vá»‹ ngá»t thanh.\nâ€¢ BÆ°á»›c 4 (Æ¯á»›p hoa lÃ i & ThÆ°á»Ÿng thá»©c): Tháº£ vÃ i cÃ¡nh hoa lÃ i tÆ°Æ¡i cho dáº­y mÃ¹i hÆ°Æ¡ng tinh khiáº¿t, mÃºc ra chÃ©n thÆ°á»Ÿng thá»©c nÃ³ng hoáº·c thÃªm Ä‘Ã¡ bÃ o giáº£i nhiá»‡t ngÃ y hÃ¨.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 30000,
                        'thoi_gian_nau' => 35,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'XoÃ i Cao LÃ£nh',
                        'slug' => 'xoai-cao-lanh',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'Ä‘áº·c sáº£n trÃ¡i cÃ¢y ná»•i tiáº¿ng cá»§a Ä‘á»‹a phÆ°Æ¡ng.',
                        'nguyen_lieu' => 'XoÃ i cÃ¡t Chu hoáº·c xoÃ i cÃ¡t HÃ²a Lá»™c trá»“ng táº¡i vÃ¹ng Ä‘áº¥t phÃ¹ sa Cao LÃ£nh (Äá»“ng ThÃ¡p), quáº£ thon dÃ i vá» vÃ ng Æ°Æ¡m má»ng dÃ­nh, thá»‹t dÃ y háº¡t lÃ©p ngá»t lá»‹m khÃ´ng xÆ¡.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Thu hoáº¡ch & Lá»±a chá»n): Chá»n nhá»¯ng quáº£ xoÃ i chÃ­n vÃ ng tá»± nhiÃªn trÃªn cÃ nh, mÃ¹i thÆ¡m ngÃ o ngáº¡t ná»©c mÅ©i Ä‘áº·c trÆ°ng cá»§a vÃ¹ng bÃ£i bá»“i sÃ´ng Tiá»n.\nâ€¢ BÆ°á»›c 2 (LÃ m mÃ¡t): Rá»­a sáº¡ch vá», Ä‘á»ƒ nÆ¡i khÃ´ rÃ¡o hoáº·c Æ°á»›p láº¡nh ngÄƒn mÃ¡t trong 30 phÃºt Ä‘á»ƒ tÄƒng Ä‘á»™ thanh mÃ¡t.\nâ€¢ BÆ°á»›c 3 (Gá»t vá» & ThÆ°á»Ÿng thá»©c): Cáº¯t mÃ¡ xoÃ i khÃ­a Ã´ vuÃ´ng láº­t ngÆ°á»£c hoáº·c thÃ¡i lÃ¡t thÆ°á»Ÿng thá»©c trá»±c tiáº¿p cáº£m nháº­n vá»‹ ngá»t Ä‘áº­m Ä‘Ã  tan cháº£y trÃªn Ä‘áº§u lÆ°á»¡i.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'Äá»“ng ThÃ¡p',
                        'gia_tien' => 45000,
                        'thoi_gian_nau' => 5,
                        'danh_gia' => 4.9
                    ],

                    // --- 7. Tá»ˆNH VÄ¨NH LONG (8 MÃ“N) ---
                    [
                        'ten_mon' => 'CÃ¡ tai tÆ°á»£ng chiÃªn xÃ¹',
                        'slug' => 'ca-tai-tuong-chien-xu',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'cÃ¡ chiÃªn nguyÃªn con, Äƒn cuá»‘n bÃ¡nh trÃ¡ng vÃ  rau sá»‘ng.',
                        'nguyen_lieu' => '1 con cÃ¡ tai tÆ°á»£ng sÃ´ng Cá»• ChiÃªn cÃ²n sá»‘ng bÆ¡i khá»e (khoáº£ng 1.2kg), bÃ¡nh trÃ¡ng nem cÃ¹ lao, bÃºn tÆ°Æ¡i, rá»• rau vÆ°á»n (chuá»‘i chÃ¡t, kháº¿ chua, thÆ¡m, dÆ°a leo, xÃ  lÃ¡ch, rau thÆ¡m), chÃ©n nÆ°á»›c máº¯m tá»i á»›t me chua ngá»t.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ cÃ¡ giá»¯ váº£y): CÃ¡ lÃ m sáº¡ch mang vÃ  ruá»™t, giá»¯ nguyÃªn váº£y vÃ  vi cÃ¡, lau tháº­t khÃ´ rÃ¡o Ä‘á»ƒ khi chiÃªn khÃ´ng bá»‹ báº¯n dáº§u.\nâ€¢ BÆ°á»›c 2 (ChiÃªn xÃ¹ ngáº­p dáº§u): Äun cháº£o ngáº­p dáº§u sÃ´i sÃ¹ng sá»¥c, tháº£ cÃ¡ vÃ o dá»±ng Ä‘á»©ng trÃªn vá»‰ hoáº·c rÆ°á»›i dáº§u liÃªn tá»¥c lÃªn thÃ¢n cÃ¡ cho tá»«ng chiáº¿c váº£y xÃ¹ dá»±ng Ä‘á»©ng vÃ ng giÃ²n rá»¥m.\nâ€¢ BÆ°á»›c 3 (BÃ y Ä‘Ä©a Ä‘á»©ng cÃ¡): Vá»›t cÃ¡ ra rÃ¡o dáº§u, Ä‘áº·t Ä‘á»©ng uy nghi trÃªn Ä‘Ä©a cÃ³ giÃ¡ Ä‘á»¡ trÃ´ng vÃ´ cÃ¹ng báº¯t máº¯t.\nâ€¢ BÆ°á»›c 4 (Cuá»‘n bÃ¡nh trÃ¡ng thÆ°á»Ÿng thá»©c): DÃ¹ng Ä‘Å©a gá»¡ tá»«ng máº£ng thá»‹t cÃ¡ tráº¯ng ngáº§n giÃ²n rá»¥m bá»c bÃ¡nh trÃ¡ng cÃ¹ng bÃºn, rau thÆ¡m vÃ  chuá»‘i chÃ¡t, cháº¥m ngáº­p chÃ©n máº¯m me chua ngá»t Ä‘áº­m Ä‘Ã .",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 220000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'TÃ u há»§ ky Má»¹ HÃ²a',
                        'slug' => 'tau-hu-ky-my-hoa',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1546069901-d576a9a3b683?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'vÃ¡ng Ä‘áº­u truyá»n thá»‘ng dÃ¹ng trong mÃ³n chay vÃ  mÃ³n máº·n.',
                        'nguyen_lieu' => 'Äáº­u nÃ nh háº¡t trÃ²n nguyÃªn cháº¥t ngÃ¢m xay sá»¯a tÆ°Æ¡i, cháº£o gang náº¥u vÃ¡ng thá»§ cÃ´ng lÃ ng nghá» trÄƒm nÄƒm Má»¹ HÃ²a (BÃ¬nh Minh - VÄ©nh Long), dáº§u Äƒn chiÃªn giÃ²n hoáº·c dÃ¹ng cuá»™n cháº£ giÃ², náº¥u láº©u máº·n chay.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Náº¥u sá»¯a Ä‘áº­u nÃ nh): Äun sÃ´i nÆ°á»›c cá»‘t sá»¯a Ä‘áº­u nÃ nh trÃªn cháº£o gang lá»›n lá»­a liu riu cho lá»›p mÃ ng vÃ¡ng Ä‘áº­u ná»•i lÃªn trÃªn máº·t.\nâ€¢ BÆ°á»›c 2 (Vá»›t vÃ¡ng & PhÆ¡i sÃ o): NgÆ°á»i thá»£ dÃ¹ng que trÃºc khÃ©o lÃ©o gáº¡t lá»›p vÃ¡ng Ä‘áº­u má»ng tang rá»“i váº¯t lÃªn sÃ o tre phÆ¡i khÃ´ rÃ¡o trong lÃ².\nâ€¢ BÆ°á»›c 3 (Cháº¿ biáº¿n phong phÃº): TÃ u há»§ ky khÃ´ Ä‘em chiÃªn giÃ²n tan vÃ ng Æ°Æ¡m, hoáº·c ngÃ¢m má»m cuá»™n cháº£ lá»¥a chay, náº¥u cÃ  ri, nhÃºng láº©u cÃ¡ láº©u náº¥m.\nâ€¢ BÆ°á»›c 4 (Cáº£m nháº­n Ä‘á»™ bÃ©o bÃ¹i): Khi Äƒn cáº£m nháº­n trá»n váº¹n vá»‹ bÃ©o ngáº­y thÆ¡m ná»©c tá»± nhiÃªn khÃ´ng pha bá»™t cá»§a Ä‘áº­u nÃ nh nguyÃªn cháº¥t lÃ ng nghá» truyá»n thá»‘ng.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 40000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'BÆ°á»Ÿi NÄƒm Roi BÃ¬nh Minh',
                        'slug' => 'buoi-nam-roi-binh-minh',
                        'hinh_anh' => (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/buoi-nam-roi-binh-minh.jpg',
                        'mo_ta_ngan' => 'bÆ°á»Ÿi cÃ³ tÃ©p má»ng, vá»‹ ngá»t thanh.',
                        'nguyen_lieu' => 'BÆ°á»Ÿi NÄƒm Roi chÃ­nh gá»‘c thá»‹ xÃ£ BÃ¬nh Minh (VÄ©nh Long), quáº£ hÃ¬nh quáº£ lÃª vá» má»ng vÃ ng Æ°Æ¡m, tÃ©p bÆ°á»Ÿi rÃ³c mÃºi má»ng nÆ°á»›c khÃ´ng háº¡t hoáº·c ráº¥t Ã­t háº¡t, vá»‹ ngá»t thanh hÆ¡i chua dá»‹u, muá»‘i á»›t rang.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Chá»n bÆ°á»Ÿi chÃ­n rÃ³c mÃºi): Chá»n quáº£ bÆ°á»Ÿi cáº§m náº·ng tay, cuá»‘ng cÃ²n tÆ°Æ¡i, vá» chuyá»ƒn vÃ ng Ä‘á»u bÃ¡o hiá»‡u bÆ°á»Ÿi Ä‘Ã£ xuá»‘ng nÆ°á»›c ngá»t Ä‘áº­m.\nâ€¢ BÆ°á»›c 2 (Gá»t vá» & TÃ¡ch mÃºi): Gá»t sáº¡ch lá»›p vá» vÃ  cÃ¹i tráº¯ng, tÃ¡ch tá»«ng mÃºi bÆ°á»Ÿi vÃ ng ngÃ  rÃ³c háº¡t Ã³ng áº£ xáº¿p ra Ä‘Ä©a.\nâ€¢ BÆ°á»›c 3 (ThÆ°á»Ÿng thá»©c vá»‹ ngon): Cháº¥m tá»«ng tÃ©p bÆ°á»Ÿi giÃ²n má»ng nÆ°á»›c vÃ o chÃ©n muá»‘i á»›t há»™t Ä‘Ã¢m nhuyá»…n, vá»‹ ngá»t thanh mÃ¡t lá»‹m hÃ²a quyá»‡n chÃºt chua the giáº£i nhiá»‡t tuyá»‡t Ä‘á»‰nh.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 65000,
                        'thoi_gian_nau' => 10,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Khoai lang BÃ¬nh TÃ¢n',
                        'slug' => 'khoai-lang-binh-tan',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1596097635121-14b63b7a0c19?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'khoai lang bÃ¹i ngá»t, thÆ°á»ng luá»™c, háº¥p hoáº·c nÆ°á»›ng.',
                        'nguyen_lieu' => 'Khoai lang tÃ­m Nháº­t hoáº·c khoai lang sá»¯a ruá»™t vÃ ng thá»• nhÆ°á»¡ng BÃ¬nh TÃ¢n - VÄ©nh Long, cá»§ thon Ä‘á»u ruá»™t tÃ­m ngáº¯t hoáº·c vÃ ng Ã³ng, dá»«a náº¡o sá»£i bÃ©o bÃ¹i, muá»‘i mÃ¨ hoáº·c máº¯m sá»‘ng cháº¥m kÃ¨m.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (LÃ m sáº¡ch cá»§ khoai): Rá»­a sáº¡ch lá»›p Ä‘áº¥t phÃ¹ sa ngoÃ i vá», cáº¯t bá» Ä‘áº§u Ä‘uÃ´i cá»§ khoai Ä‘á»ƒ rÃ¡o nÆ°á»›c.\nâ€¢ BÆ°á»›c 2 (Háº¥p chÃ­n bá»Ÿ bÃ¹i): Xáº¿p khoai vÃ o xá»­ng háº¥p cÃ¡ch thá»§y hoáº·c luá»™c lá»­a vá»«a khoáº£ng 25 - 30 phÃºt cho cá»§ khoai chÃ­n dá»n thÆ¡m ná»©c mÅ©i.\nâ€¢ BÆ°á»›c 3 (NÆ°á»›ng than hoa hoáº·c Äƒn cÃ¹ng máº¯m): CÃ³ thá»ƒ bá»c giáº¥y báº¡c nÆ°á»›ng than Ä‘á» cho tÆ°Æ¡m máº­t ngá»t ngÃ o.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c Ä‘á»™c Ä‘Ã¡o): ThÆ°á»Ÿng thá»©c khoai lang tÃ­m bá»Ÿ tÆ¡i ngá»t bÃ¹i, Äƒn kÃ¨m cÆ¡m dá»«a náº¡o sá»£i hoáº·c cháº¥m máº¯m sá»‘ng cÃ¡ linh gá»«ng á»›t Ä‘áº·c sáº£n thÃ´n quÃª VÄ©nh Long.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 35000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Cam sÃ nh Tam BÃ¬nh',
                        'slug' => 'cam-sanh-tam-binh',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1582979512210-99b6a53386f9?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'cam nhiá»u nÆ°á»›c, cÃ³ vá»‹ chua ngá»t.',
                        'nguyen_lieu' => 'Cam sÃ nh quáº£ trÃ²n vá» sáº§n sÃ¹i mÃ u xanh bÃ³ng vÃ¹ng Ä‘áº¥t Tam BÃ¬nh (VÄ©nh Long), tÃ©p cam vÃ ng cam Ä‘áº­m ngáº­m Ä‘áº§y máº­t nÆ°á»›c chua ngá»t Ä‘áº­m Ä‘Ã , Ä‘Æ°á»ng cÃ¡t hoáº·c máº­t ong, Ä‘Ã¡ viÃªn.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Lá»±a cam má»ng nÆ°á»›c): Chá»n quáº£ cam vá» má»ng sáº§n nháº¹, Ä‘Ã¡y quáº£ ná»Ÿ vÃ ng chá»©ng tá» cam má»ng nÆ°á»›c vÃ  ngá»t thÆ¡m.\nâ€¢ BÆ°á»›c 2 (Cáº¯t & Váº¯t nÆ°á»›c): Bá»• Ä‘Ã´i quáº£ cam theo chiá»u ngang, dÃ¹ng dá»¥ng cá»¥ váº¯t kiá»‡t tá»«ng giá»t nÆ°á»›c cam vÃ ng Ã³ng sÃ¡nh má»‹n khÃ´ng bá»‹ dÃ­nh tinh dáº§u Ä‘áº¯ng.\nâ€¢ BÆ°á»›c 3 (Pha cháº¿ thanh mÃ¡t): HÃ²a thÃªm chÃºt Ä‘Æ°á»ng cÃ¡t hoáº·c muá»—ng máº­t ong nguyÃªn cháº¥t tÃ¹y kháº©u vá»‹, thÃªm Ä‘Ã¡ viÃªn mÃ¡t láº¡nh khuáº¥y Ä‘á»u.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Ly nÆ°á»›c cam sÃ nh Tam BÃ¬nh thÆ¡m ngÃ¡t, vá»‹ chua ngá»t hÃ i hÃ²a bá»• sung lÆ°á»£ng vitamin C dá»“i dÃ o sáº£ng khoÃ¡i.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 40000,
                        'thoi_gian_nau' => 10,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Thanh trÃ  VÄ©nh Long',
                        'slug' => 'thanh-tra-vinh-long',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'trÃ¡i cÃ¢y cÃ³ vá»‹ chua ngá»t, dÃ¹ng Äƒn tÆ°Æ¡i hoáº·c lÃ m nÆ°á»›c giáº£i khÃ¡t.',
                        'nguyen_lieu' => 'TrÃ¡i thanh trÃ  trÃ²n nhá» vá» vÃ ng Æ°Æ¡m xá»© BÃ¬nh Minh (VÄ©nh Long), cÆ¡m trÃ¡i má»ng nÆ°á»›c thÆ¡m ná»“ng nÃ n vá»‹ chua ngá»t tá»± nhiÃªn, Ä‘Æ°á»ng cÃ¡t tráº¯ng, muá»‘i á»›t cay, Ä‘Ã¡ bÃ o giáº£i khÃ¡t.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Rá»­a sáº¡ch & ChÃ  vá»): Rá»­a sáº¡ch trÃ¡i thanh trÃ , chÃ  nháº¹ vá» trong lÃ²ng bÃ n tay cho vá» má»m dáº»o dá»… bÃ³c tÃ¡ch.\nâ€¢ BÆ°á»›c 2 (Ä‚n tÆ°Æ¡i cháº¥m muá»‘i): BÃ³c vá» hoáº·c khÃ­a mÃºi cháº¥m ngay vÃ o chÃ©n muá»‘i á»›t cay ná»“ng Ä‘á»ƒ cáº£m nháº­n vá»‹ chua ngá»t bÃ¹ng ná»• chÃ¢n tháº­t nháº¥t.\nâ€¢ BÆ°á»›c 3 (Dáº§m Ä‘Ã¡ Ä‘Æ°á»ng giáº£i khÃ¡t): Lá»™t vá» 5 - 6 trÃ¡i thanh trÃ  cho vÃ o ly, dáº§m nÃ¡t cÃ¹ng 2 muá»—ng Ä‘Æ°á»ng vÃ  chÃºt xÃ­u muá»‘i cho sÃ¡nh káº¹o láº¡i.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c mÃ¡t láº¡nh): ThÃªm Ä‘áº§y Ä‘Ã¡ bÃ o khuáº¥y Ä‘á»u, tá»«ng há»›p nÆ°á»›c thanh trÃ  chua chua ngá»t ngá»t thÆ¡m lá»«ng Ä‘Ã¡nh tan cÆ¡n khÃ¡t ngÃ y hÃ¨ oi bá»©c.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 50000,
                        'thoi_gian_nau' => 10,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'BÃ¡nh trÃ¡ng cÃ¹ lao Lá»¥c SÄ© ThÃ nh',
                        'slug' => 'banh-trang-cu-lao-luc-si-thanh',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image20.jpg',
                        'mo_ta_ngan' => 'bÃ¡nh trÃ¡ng thá»§ cÃ´ng dÃ¹ng cuá»‘n thá»©c Äƒn.',
                        'nguyen_lieu' => 'Gáº¡o lÃºa mÃ¹a thÆ¡m dáº»o xay bá»™t tÆ°Æ¡i thá»§ cÃ´ng trÃªn cÃ¹ lao MÃ¢y (Lá»¥c SÄ© ThÃ nh - TrÃ  Ã”n - VÄ©nh Long), muá»‘i tinh sáº¡ch, mÃ¨ Ä‘en/mÃ¨ tráº¯ng, nÆ°á»›c cá»‘t dá»«a bÃ©o, vá»‰ tre phÆ¡i bÃ¡nh.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Xay bá»™t trÃ¡ng bÃ¡nh): Bá»™t gáº¡o thuáº§n khÃ´ng pha bá»™t nÄƒng Ä‘Æ°á»£c ngÃ¢m xay má»‹n cÃ¹ng chÃºt muá»‘i táº¡o Ä‘á»™ dai dáº»o tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 2 (TrÃ¡ng bÃ¡nh má»ng Ä‘á»u): NgÆ°á»i thá»£ lÃ nh nghá» mÃºc vÃ¡ bá»™t lÃ¡ng tháº­t má»ng Ä‘á»u trÃªn máº·t váº£i cÄƒng ná»“i hÆ¡i nÆ°á»›c sÃ´i sÃ¹ng sá»¥c rá»“i Ä‘áº­y náº¯p vung tre vÃ i giÃ¢y.\nâ€¢ BÆ°á»›c 3 (PhÆ¡i liáº¿p tre há»©ng giÃ³ sÃ´ng): DÃ¹ng thanh nan tre nháº¥c bÃ¡nh tráº£i Ä‘á»u lÃªn tá»«ng liáº¿p tre Ä‘em phÆ¡i dÆ°á»›i náº¯ng giÃ³ mÃ¡t lÃ nh cá»§a dÃ²ng sÃ´ng Háº­u cÃ¹ lao MÃ¢y.\nâ€¢ BÆ°á»›c 4 (DÃ¹ng cuá»‘n cÃ¡ chiÃªn xÃ¹): BÃ¡nh trÃ¡ng má»m dáº»o khÃ´ng cáº§n nhÃºng nÆ°á»›c, cuá»™n cÃ¡ tai tÆ°á»£ng chiÃªn xÃ¹ hoáº·c thá»‹t luá»™c rau sá»‘ng khÃ´ng bao giá» bá»‹ rÃ¡ch dÃ­nh.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 30000,
                        'thoi_gian_nau' => 15,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'CÃ¡ lÃ³c nÆ°á»›ng',
                        'slug' => 'ca-loc-nuong-vinh-long',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image21.jpg',
                        'mo_ta_ngan' => 'mÃ³n cÃ¡ Ä‘á»“ng nÆ°á»›ng Äƒn cÃ¹ng rau sá»‘ng vÃ  nÆ°á»›c cháº¥m.',
                        'nguyen_lieu' => 'CÃ¡ lÃ³c Ä‘á»“ng sÃ´ng Cá»• ChiÃªn tÆ°Æ¡i ngon thá»‹t cháº¯c, hÃ nh lÃ¡ lÃ m má»¡ hÃ nh, Ä‘áº­u phá»™ng rang vÃ ng giÃ£ dáº­p, rau thÆ¡m miá»‡t vÆ°á»n (lÃ¡ lá»‘t, xÃ  lÃ¡ch, diáº¿p cÃ¡, hÃºng quáº¿), bÃ¡nh trÃ¡ng Lá»¥c SÄ© ThÃ nh, nÆ°á»›c máº¯m tá»i á»›t chua cay.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ cÃ¡ Ä‘á»“ng): CÃ¡ lÃ³c lÃ m sáº¡ch váº£y hoáº·c nÆ°á»›ng má»i trÃªn vá»‰ than hoa Ä‘á» rá»±c khÃ´ng táº©m Æ°á»›p Ä‘á»ƒ giá»¯ trá»n vá»‹ ngá»t tá»± nhiÃªn cá»§a cÃ¡ Ä‘á»“ng.\nâ€¢ BÆ°á»›c 2 (NÆ°á»›ng chÃ­n Ä‘á»u tay): Trá»Ÿ Ä‘á»u vá»‰ nÆ°á»›ng trÃªn than hoa liu riu cho cÃ¡ chÃ­n vÃ ng rá»¥m tá»« trong ra ngoÃ i, da cÃ¡ tÆ°Æ¡m má»¡ xÃ¨o xÃ¨o thÆ¡m phá»©c.\nâ€¢ BÆ°á»›c 3 (RÆ°á»›i má»¡ hÃ nh Ä‘áº­u phá»™ng): Ráº¡ch sá»‘ng lÆ°ng cÃ¡, chan Ä‘áº«m má»¡ hÃ nh xanh Ã³ng áº£ vÃ  ráº¯c Ä‘áº­u phá»™ng rang giÃ£ dáº­p bÃ¹i bÃ©o.\nâ€¢ BÆ°á»›c 4 (Cuá»‘n bÃ¡nh trÃ¡ng thÆ°á»Ÿng thá»©c): Gáº¯p tá»«ng thá»› thá»‹t cÃ¡ lÃ³c tráº¯ng ngáº§n cuá»™n trÃ²n cÃ¹ng bÃ¡nh trÃ¡ng cÃ¹ lao vÃ  rau sá»‘ng, cháº¥m ngáº­p chÃ©n nÆ°á»›c máº¯m chua ngá»t Ä‘áº­m Ä‘Ã  hÆ°Æ¡ng vá»‹ miá»n quÃª VÄ©nh Long.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'VÄ©nh Long',
                        'gia_tien' => 120000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 4.8
                    ],

                    // --- 8. Tá»ˆNH CÃ€ MAU (8 MÃ“N) ---
                    [
                        'ten_mon' => 'Cua CÃ  Mau',
                        'slug' => 'cua-ca-mau',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1559742811-822873691df8?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'cua biá»ƒn cÃ³ thá»‹t cháº¯c, ngá»t vÃ  gáº¡ch bÃ©o.',
                        'nguyen_lieu' => 'Cua biá»ƒn NÄƒm CÄƒn - CÃ  Mau tÆ°Æ¡i sá»‘ng (cua thá»‹t cháº¯c ná»‹ch hoáº·c cua gáº¡ch son bÃ©o ngáº­y), bia hoáº·c sáº£ cÃ¢y háº¥p, muá»‘i tiÃªu chanh tÆ°Æ¡i hoáº·c muá»‘i á»›t xanh, á»›t hiá»ƒm.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Lá»±a cua NÄƒm CÄƒn chuáº©n): Chá»n cua biá»ƒn tá»± nhiÃªn yáº¿m cháº¯c ná»‹ch, cáº§m náº·ng trÄ©u tay Ä‘á»ƒ Ä‘áº£m báº£o thá»‹t Ä‘áº§y 100% khÃ´ng bá»‹ Ã³p nÆ°á»›c.\nâ€¢ BÆ°á»›c 2 (SÆ¡ cháº¿ lÃ m sáº¡ch): DÃ¹ng bÃ n cháº£i chÃ  sáº¡ch mai vÃ  cÃ ng cua dÆ°á»›i vÃ²i nÆ°á»›c cháº£y, dÃ¹ng mÅ©i dao nhá»n Ä‘Ã¢m nháº¹ tim cua cho cua cháº¿t Ãªm khÃ´ng rá»¥ng cÃ ng khi náº¥u.\nâ€¢ BÆ°á»›c 3 (Háº¥p bia sáº£ thÆ¡m lá»«ng): Xáº¿p sáº£ Ä‘áº­p dáº­p dÆ°á»›i Ä‘Ã¡y ná»“i, Ä‘áº·t cua lÃªn trÃªn, Ä‘á»• 1 lon bia tÆ°Æ¡i háº¥p cÃ¡ch thá»§y lá»­a lá»›n trong 15 - 18 phÃºt Ä‘áº¿n khi vá» cua Ä‘á» au rá»±c rá»¡.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c Ä‘á»‰nh cao): Báº» cÃ ng cua lá»™ thá»› thá»‹t tráº¯ng phau ngá»t lá»‹m cháº¯c nhÆ° báº¯p chuá»‘i, gáº¡ch cua bÃ©o ngáº­y dáº§m cháº¥m muá»‘i tiÃªu chanh Äƒn mÃª máº©n.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 280000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 5.0
                    ],
                    [
                        'ten_mon' => 'Ba khÃ­a Ráº¡ch Gá»‘c',
                        'slug' => 'ba-khia-rach-goc',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image6.jpg',
                        'mo_ta_ngan' => 'ba khÃ­a muá»‘i trá»™n tá»i, á»›t, Ä‘Æ°á»ng vÃ  chanh.',
                        'nguyen_lieu' => 'Ba khÃ­a sá»‘ng bÃ¡m rá»… Ä‘Æ°á»›c rá»«ng ngáº­p máº·n Ráº¡ch Gá»‘c (Ngá»c Hiá»ƒn - CÃ  Mau) muá»‘i vá»«a Äƒn, tá»i cÃ´ Ä‘Æ¡n bÄƒm nhá», á»›t hiá»ƒm Ä‘á» giÃ£ nÃ¡t, chanh tÆ°Æ¡i má»ng nÆ°á»›c, Ä‘Æ°á»ng cÃ¡t tráº¯ng, rau rÄƒm.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Rá»­a rÃ¡o ba khÃ­a muá»‘i): TÃ¡ch bá» yáº¿m vÃ  mai ba khÃ­a Ä‘Ã£ á»§ muá»‘i rÆ¡m, rá»­a qua nÆ°á»›c áº¥m cho bá»›t máº·n rá»“i xÃ© nhá» tá»«ng cÃ ng vÃ  chÃ¢n vá»«a Äƒn.\nâ€¢ BÆ°á»›c 2 (GiÃ£ nhuyá»…n gia vá»‹): GiÃ£ nhuyá»…n tá»i, á»›t hiá»ƒm cÃ¹ng Ä‘Æ°á»ng cÃ¡t táº¡o há»—n há»£p cay ná»“ng thÆ¡m phá»©c.\nâ€¢ BÆ°á»›c 3 (Trá»™n tháº¥m Ä‘á»u): Cho ba khÃ­a vÃ o thau, váº¯t nhiá»u nÆ°á»›c cá»‘t chanh tÆ°Æ¡i, trÃºt há»—n há»£p tá»i á»›t Ä‘Æ°á»ng vÃ o bÃ³p trá»™n tháº­t Ä‘á»u tay trong 30 phÃºt cho ngáº¥m sÃ¢u.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c cÃ¹ng cÆ¡m nÃ³ng): Thá»‹t ba khÃ­a Ráº¡ch Gá»‘c cháº¯c ná»‹ch tháº¥m Ä‘áº«m vá»‹ chua cay máº·n ngá»t bÃ©o bÃ¹i, Äƒn cÃ¹ng cÆ¡m nguá»™i hay cÆ¡m nÃ³ng vÃ o ngÃ y mÆ°a thÃ¬ ngon khÃ´ng gÃ¬ sÃ¡nh báº±ng.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 75000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'Láº©u máº¯m U Minh',
                        'slug' => 'lau-mam-u-minh',
                        'hinh_anh' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image15.png',
                        'mo_ta_ngan' => 'láº©u náº¥u tá»« máº¯m, Äƒn cÃ¹ng cÃ¡, tÃ´m vÃ  rau Ä‘á»“ng.',
                        'nguyen_lieu' => 'Máº¯m cÃ¡ sáº·c bÆ°á»›m rá»«ng trÃ m U Minh thÆ¡m ná»©c, cÃ¡ lÃ³c Ä‘á»“ng, tÃ´m báº¡c Ä‘áº¥t, má»±c tÆ°Æ¡i, thá»‹t ba rá»i heo, cÃ  tÃ­m, sáº£ bÄƒm, á»›t hiá»ƒm, Ä‘á»t choáº¡i rá»«ng U Minh, bÃ´ng sÃºng, bÃ¨o tai chuá»™t, bÃºn tÆ°Æ¡i.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Náº¥u rÃ£ máº¯m láº¥y nÆ°á»›c cá»‘t): Cho máº¯m cÃ¡ sáº·c vÃ o ná»“i nÆ°á»›c dá»«a náº¥u sÃ´i sÃ¹ng sá»¥c cho rÃ£ háº¿t thá»‹t cÃ¡, lá»c qua rÃ¢y bá» sáº¡ch xÆ°Æ¡ng láº¥y nÆ°á»›c dÃ¹ng thÆ¡m ná»“ng Ä‘áº­m Ä‘Ã .\nâ€¢ BÆ°á»›c 2 (Phi sáº£ á»›t xÃ o thá»‹t cÃ  tÃ­m): Phi thÆ¡m sáº£ bÄƒm á»›t hiá»ƒm, xÃ o sÄƒn thá»‹t ba rá»i vÃ  cÃ  tÃ­m cáº¯t khÃºc rá»“i trÃºt vÃ o ná»“i nÆ°á»›c dÃ¹ng máº¯m Ä‘un sÃ´i liu riu.\nâ€¢ BÆ°á»›c 3 (Tháº£ cÃ¡ Ä‘á»“ng & tÃ´m tÆ°Æ¡i): Cho cÃ¡ lÃ³c Ä‘á»“ng cáº¯t khÃºc, tÃ´m báº¡c Ä‘áº¥t vÃ o náº¥u chÃ­n tá»›i giá»¯ trá»n Ä‘á»™ ngá»t tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 4 (NhÃºng Ä‘á»t choáº¡i rau rá»«ng): Äáº·t ná»“i láº©u trÃªn báº¿p lá»­a, nhÃºng ngáº­p Ä‘á»t choáº¡i rá»«ng U Minh giÃ²n ngá»t, bÃ´ng sÃºng vÃ  rau Ä‘á»“ng, chan bÃºn tÆ°Æ¡i Äƒn Ä‘áº­m Ä‘Ã  khÃ³ quÃªn.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 250000,
                        'thoi_gian_nau' => 50,
                        'danh_gia' => 4.9
                    ],
                    [
                        'ten_mon' => 'TÃ´m khÃ´ CÃ  Mau',
                        'slug' => 'tom-kho-ca-mau',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'Ä‘áº·c sáº£n cÃ³ vá»‹ ngá»t Ä‘áº­m, thÆ°á»ng dÃ¹ng lÃ m quÃ .',
                        'nguyen_lieu' => 'TÃ´m Ä‘áº¥t sinh thÃ¡i thiÃªn nhiÃªn vÃ¹ng rá»«ng ngáº­p máº·n CÃ  Mau cÃ²n bÃºng nháº£y, muá»‘i háº¡t biá»ƒn tinh khiáº¿t, náº¯ng giÃ³ biá»ƒn Äáº¥t MÅ©i, cá»§ kiá»‡u ngÃ¢m chua ngá»t Äƒn kÃ¨m.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Luá»™c tÃ´m nÆ°á»›c sÃ´i muá»‘i): TÃ´m Ä‘áº¥t tÆ°Æ¡i luá»™c trong nÆ°á»›c muá»‘i háº¡t sÃ´i giÃ  Ä‘Ãºng Ä‘á»™ lá»­a Ä‘á»ƒ thá»‹t tÃ´m sÄƒn cháº¯c vÃ  giá»¯ mÃ u Ä‘á» tÆ°Æ¡i tá»± nhiÃªn.\nâ€¢ BÆ°á»›c 2 (PhÆ¡i náº¯ng tá»± nhiÃªn): Vá»›t tÃ´m rÃ¡o nÆ°á»›c tráº£i Ä‘á»u trÃªn vá»‰ tre phÆ¡i dÆ°á»›i 3 - 4 náº¯ng giÃ²n gáº¯t biá»ƒn CÃ  Mau cho khÃ´ dáº»o thá»‹t.\nâ€¢ BÆ°á»›c 3 (Äáº­p tÃ¡ch vá» tÃ´m): Cho tÃ´m vÃ o bao váº£i Ä‘áº­p nháº¹ tay Ä‘á»ƒ tÃ¡ch rá»i lá»›p vá» ngoÃ i lÃ m lá»™ con tÃ´m Ä‘á» au cÄƒng trÃ²n khÃ´ng pháº©m mÃ u.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c & LÃ m quÃ ): NhÃ¢m nhi tÃ´m khÃ´ dáº»o ngá»t Ä‘áº­m Ä‘Ã  tá»± nhiÃªn cÃ¹ng cá»§ kiá»‡u chua ngá»t hoáº·c náº¥u canh báº§u canh rau ngá»t lá»‹m tinh tÃºy.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 150000,
                        'thoi_gian_nau' => 30,
                        'danh_gia' => 5.0
                    ],
                    [
                        'ten_mon' => 'CÃ¡ thÃ²i lÃ²i nÆ°á»›ng muá»‘i á»›t',
                        'slug' => 'ca-thoi-loi-nuong-muoi-ot',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'cÃ¡ vÃ¹ng rá»«ng ngáº­p máº·n, thá»‹t dai ngá»t.',
                        'nguyen_lieu' => 'CÃ¡ thÃ²i lÃ²i tá»± nhiÃªn leo cÃ¢y rá»«ng Ä‘Æ°á»›c CÃ  Mau máº­p máº¡p cÃ²n sá»‘ng, muá»‘i háº¡t rang, á»›t hiá»ƒm Ä‘á» giÃ£ dáº­p, dáº§u mÃ u Ä‘iá»u, tiÃªu sá», rau sá»‘ng rá»«ng ngáº­p máº·n, chÃ©n muá»‘i tiÃªu chanh tÆ°Æ¡i.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (LÃ m sáº¡ch nhá»›t cÃ¡): CÃ¡ thÃ²i lÃ²i chÃ  sáº¡ch nhá»›t báº±ng muá»‘i háº¡t vÃ  giáº¥m, Ä‘á»ƒ nguyÃªn con rá»­a sáº¡ch rÃ¡o nÆ°á»›c.\nâ€¢ BÆ°á»›c 2 (Æ¯á»›p sá»‘t muá»‘i á»›t cay ná»“ng): HÃ²a muá»‘i háº¡t giÃ£ nhuyá»…n cÃ¹ng á»›t hiá»ƒm, tiÃªu xay vÃ  dáº§u mÃ u Ä‘iá»u quÃ©t Ä‘á»u phá»§ kháº¯p thÃ¢n cÃ¡ trong 15 phÃºt.\nâ€¢ BÆ°á»›c 3 (NÆ°á»›ng vÃ ng than hoa): Káº¹p cÃ¡ trÃªn vá»‰ than há»“ng, trá»Ÿ Ä‘á»u tay liÃªn tá»¥c cho lá»›p da cÃ¡ se láº¡i vÃ ng giÃ²n tá»a hÆ°Æ¡ng thÆ¡m ngÃ¡t ná»©c mÅ©i.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c): Thá»‹t cÃ¡ thÃ²i lÃ²i dai ngá»t nhÆ° thá»‹t gÃ , sÄƒn cháº¯c khÃ´ng há» cÃ³ xÆ°Æ¡ng dÄƒm, cháº¥m muá»‘i tiÃªu chanh cuá»‘n bÃ¡nh trÃ¡ng rau rá»«ng Äƒn cá»±c Ä‘Ã£ miá»‡ng.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 120000,
                        'thoi_gian_nau' => 25,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Vá»p nÆ°á»›ng má»¡ hÃ nh',
                        'slug' => 'vop-nuong-mo-hanh',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1559132104-583eb531d0db?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'háº£i sáº£n nÆ°á»›ng thÆ¡m, cÃ³ vá»‹ ngá»t tá»± nhiÃªn.',
                        'nguyen_lieu' => 'Vá»p tá»± nhiÃªn rá»«ng ngáº­p máº·n MÅ©i CÃ  Mau to dÃ y thá»‹t, má»¡ heo tháº¯ng giÃ²n rá»¥m, hÃ nh lÃ¡ tÆ°Æ¡i cáº¯t nhá», Ä‘áº­u phá»™ng rang giÃ£ dáº­p, á»›t bÄƒm, chÃ©n muá»‘i tiÃªu chanh tÆ°Æ¡i pha á»›t.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (NgÃ¢m rá»­a sáº¡ch vá»p): NgÃ¢m vá»p trong nÆ°á»›c vo gáº¡o cÃ³ vÃ i lÃ¡t á»›t cho nháº£ sáº¡ch bÃ¹n cÃ¡t rá»«ng Ä‘Æ°á»›c, tÃ¡ch má»Ÿ má»™t bÃªn vá» vá»p.\nâ€¢ BÆ°á»›c 2 (LÃ m má»¡ hÃ nh tÃ©p má»¡): Tháº¯ng má»¡ heo giÃ²n tan rÆ°á»›i ngay vÃ o chÃ©n hÃ nh lÃ¡ cáº¯t nhá» cÃ¹ng chÃºt gia vá»‹ cho dáº­y mÃ u xanh Ã³ng Ã¡nh.\nâ€¢ BÆ°á»›c 3 (NÆ°á»›ng than Ä‘á»): Xáº¿p tá»«ng con vá»p lÃªn vá»‰ nÆ°á»›ng trÃªn than há»“ng, khi nÆ°á»›c trong thÃ¢n vá»p sÃ´i lÄƒn tÄƒn thÃ¬ mÃºc tá»«ng muá»—ng má»¡ hÃ nh vÃ  Ä‘áº­u phá»™ng chan ngáº­p lÃªn thá»‹t vá»p.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c nÃ³ng há»•i): NÆ°á»›ng thÃªm 1 phÃºt cho sÃ´i xÃ¨o xÃ¨o thÆ¡m ngÃ o ngáº¡t, hÃºp trá»n nÆ°á»›c vá»p ngá»t lá»‹m tá»± nhiÃªn vÃ  nhai miáº¿ng thá»‹t vá»p dai giÃ²n bÃ©o ngáº­y.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 85000,
                        'thoi_gian_nau' => 20,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'ChÃ¡o cÃ¡ kÃ¨o',
                        'slug' => 'chao-ca-keo',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1547496502-affa22d38842?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'chÃ¡o cÃ¡ Äƒn nÃ³ng, thÆ°á»ng dÃ¹ng cÃ¹ng rau Ä‘áº¯ng.',
                        'nguyen_lieu' => 'CÃ¡ kÃ¨o bÃ£i bá»“i CÃ  Mau tÆ°Æ¡i roi rÃ³i cÃ²n sá»‘ng, gáº¡o táº» thÆ¡m pha gáº¡o náº¿p rang vÃ ng nháº¹ náº¥u chÃ¡o nhá»« sÃ¡nh má»‹n, náº¥m rÆ¡m bÃºp tÆ°Æ¡i, hÃ nh tÃ­m phi vÃ ng, hÃ nh lÃ¡, ngÃ² rÃ­, tiÃªu sá» xay, rá»• rau Ä‘áº¯ng Ä‘á»“ng tÆ°Æ¡i non.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (SÆ¡ cháº¿ cÃ¡ kÃ¨o): CÃ¡ kÃ¨o vuá»‘t sáº¡ch nhá»›t vá»›i tro báº¿p hoáº·c muá»‘i giáº¥m, giá»¯ nguyÃªn ruá»™t vÃ  máº­t cÃ¡ kÃ¨o vÃ¬ máº­t cÃ¡ kÃ¨o cÃ³ vá»‹ Ä‘áº¯ng háº­u ngá»t trá»© danh.\nâ€¢ BÆ°á»›c 2 (Náº¥u chÃ¡o nhá»« sÃ¡nh): Ninh gáº¡o rang cÃ¹ng nÆ°á»›c háº§m xÆ°Æ¡ng vÃ  náº¥m rÆ¡m cho háº¡t gáº¡o ná»Ÿ bung sÃ¡nh má»‹n.\nâ€¢ BÆ°á»›c 3 (Tháº£ cÃ¡ kÃ¨o sÃ´i bÃ¹ng): Khi chÃ¡o sÃ´i sÃ¹ng sá»¥c trÃªn báº¿p, tháº£ nguyÃªn rá»• cÃ¡ kÃ¨o tÆ°Æ¡i vÃ o Ä‘un thÃªm 3 - 5 phÃºt cho cÃ¡ vá»«a chÃ­n tá»›i Ä‘á»ƒ thá»‹t má»m ngá»t khÃ´ng nÃ¡t.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c vá»›i rau Ä‘áº¯ng): MÃºc chÃ¡o nÃ³ng há»•i ra tÃ´, ráº¯c nhiá»u tiÃªu sá» hÃ nh phi, gáº¯p cÃ¡ kÃ¨o cháº¥m nÆ°á»›c máº¯m máº·n á»›t hiá»ƒm vÃ  nhÃºng ngáº­p rau Ä‘áº¯ng Ä‘á»“ng cáº£m nháº­n vá»‹ Ä‘áº¯ng thanh biáº¿n chuyá»ƒn thÃ nh háº­u ngá»t thanh mÃ¡t kÃ¬ diá»‡u.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 55000,
                        'thoi_gian_nau' => 35,
                        'danh_gia' => 4.8
                    ],
                    [
                        'ten_mon' => 'Máº­t ong rá»«ng U Minh',
                        'slug' => 'mat-ong-rung-u-minh',
                        'hinh_anh' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&q=80&w=800',
                        'mo_ta_ngan' => 'máº­t ong tá»« vÃ¹ng rá»«ng trÃ m, thÆ°á»ng dÃ¹ng lÃ m quÃ  biáº¿u.',
                        'nguyen_lieu' => 'Tá»• ong gÃ¡c kÃ¨o tá»± nhiÃªn trong rá»«ng trÃ m nguyÃªn sinh U Minh Háº¡ (CÃ  Mau), máº­t ong hoa trÃ m nguyÃªn cháº¥t mÃ u vÃ ng cam Ä‘áº­m Ä‘áº·c quÃ¡nh, hÆ°Æ¡ng thÆ¡m ná»“ng nÃ n dá»‹u ngá»t khÃ´ng pha táº¡p cháº¥t.',
                        'cach_lam' => "â€¢ BÆ°á»›c 1 (Thá»£ gÃ¡c kÃ¨o láº¥y máº­t): NgÆ°á»i thá»£ rá»«ng Ä‘i gÃ¡c kÃ¨o trong rá»«ng trÃ m báº¡t ngÃ n, cáº¯t khÃ©o lÃ©o bá»ng sÃ¡p máº­t hoa trÃ m vÃ o mÃ¹a trÃ m ná»Ÿ hoa rá»±c rá»¡.\nâ€¢ BÆ°á»›c 2 (Lá»c máº­t thá»§ cÃ´ng): DÃ¹ng lÆ°á»›i váº£i lá»c sáº¡ch táº¡p cháº¥t thu láº¥y nhá»¯ng giá»t máº­t ong hoa trÃ m sÃ¡nh vÃ ng Ã³ng áº£ Ä‘áº·c quÃ¡nh.\nâ€¢ BÆ°á»›c 3 (ÄÃ³ng chai báº£o quáº£n): Cho máº­t ong nguyÃªn cháº¥t vÃ o chai thá»§y tinh kÃ­n giá»¯ nguyÃªn váº¹n dÆ°á»¡ng cháº¥t quÃ½ giÃ¡ cá»§a nÃºi rá»«ng Äáº¥t MÅ©i.\nâ€¢ BÆ°á»›c 4 (ThÆ°á»Ÿng thá»©c & LÃ m quÃ ): Pha nÆ°á»›c áº¥m chanh tÆ°Æ¡i bá»• dÆ°á»¡ng, ngÃ¢m Ä‘Ã´ng trÃ¹ng háº¡ tháº£o, cháº¥m bÃ¡nh mÃ¬ hoáº·c dÃ¹ng lÃ m quÃ  biáº¿u Ä‘áº·c sáº£n vÃ´ cÃ¹ng quÃ½ giÃ¡ cá»§a Ä‘áº¥t phÆ°Æ¡ng Nam.",
                        'id_vung_mien' => 3,
                        'tinh_thanh' => 'CÃ  Mau',
                        'gia_tien' => 190000,
                        'thoi_gian_nau' => 10,
                        'danh_gia' => 5.0
                    ]
                ];

                foreach ($southernThreeRemainingProvinces as $sp3) {
                    $stmtSouthern->execute([
                        ':ten_mon' => $sp3['ten_mon'],
                        ':slug' => $sp3['slug'],
                        ':hinh_anh' => $sp3['hinh_anh'],
                        ':mo_ta_ngan' => $sp3['mo_ta_ngan'],
                        ':nguyen_lieu' => $sp3['nguyen_lieu'],
                        ':cach_lam' => $sp3['cach_lam'],
                        ':id_vung_mien' => $sp3['id_vung_mien'],
                        ':tinh_thanh' => $sp3['tinh_thanh'],
                        ':gia_tien' => $sp3['gia_tien'],
                        ':thoi_gian_nau' => $sp3['thoi_gian_nau'],
                        ':danh_gia' => $sp3['danh_gia']
                    ]);
                }

                // XÃ³a cÃ¡c báº£n ghi cÅ© trÃ¹ng láº·p hoáº·c khÃ´ng Ä‘á»“ng nháº¥t cho Äá»“ng ThÃ¡p, VÄ©nh Long, CÃ  Mau
                // Äá»“ng ThÃ¡p
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` IN ('nem-lai-vung-dong-thap', 'ca-loc-nuong-la-sen-dong-thap')");
                // VÄ©nh Long
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` IN ('khoai-lang-mam-song-vinh-long', 'ca-tai-tuong-chien-xu-vinh-long')");
                // CÃ  Mau
                $this->conn->exec("DELETE FROM `mon_an` WHERE `slug` IN ('banh-tam-ca-mau', 'cua-da-rang-muoi-ca-mau', 'lau-ca-thoi-loi-ca-mau', 'vop-nuong-mo-hanh-ca-mau')");

                // Äáº£m báº£o gÃ¡n Ä‘Ãºng tinh_thanh vÃ  id_vung_mien = 3 cho toÃ n bá»™ 24 mÃ³n
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'Äá»“ng ThÃ¡p', `id_vung_mien` = 3 WHERE `slug` IN ('hu-tieu-sa-dec', 'nem-lai-vung', 'lau-ca-linh-bong-dien-dien', 'banh-phong-tom-sa-giang', 'ca-loc-nuong-trui-dong-thap', 'com-sen', 'che-hat-sen', 'xoai-cao-lanh')");
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'VÄ©nh Long', `id_vung_mien` = 3 WHERE `slug` IN ('ca-tai-tuong-chien-xu', 'tau-hu-ky-my-hoa', 'buoi-nam-roi-binh-minh', 'khoai-lang-binh-tan', 'cam-sanh-tam-binh', 'thanh-tra-vinh-long', 'banh-trang-cu-lao-luc-si-thanh', 'ca-loc-nuong-vinh-long')");
                $this->conn->exec("UPDATE `mon_an` SET `tinh_thanh` = 'CÃ  Mau', `id_vung_mien` = 3 WHERE `slug` IN ('cua-ca-mau', 'ba-khia-rach-goc', 'lau-mam-u-minh', 'tom-kho-ca-mau', 'ca-thoi-loi-nuong-muoi-ot', 'vop-nuong-mo-hanh', 'chao-ca-keo', 'mat-ong-rung-u-minh')");

                // Äá»“ng bá»™ vÃ  chuáº©n hÃ³a chÃ­nh xÃ¡c hÃ¬nh áº£nh Ä‘Ãºng cho táº¥t cáº£ cÃ¡c mÃ³n Äƒn
                $uploadPrefix = (defined('BASE_URL') ? BASE_URL : '/AT3M/am_thuc_3_mien/') . 'asset/uploads/';
                $correctDishImages = [
                    // CÃ  Mau
                    'mat-ong-rung-u-minh' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&q=80&w=800',
                    'chao-ca-keo' => 'https://images.unsplash.com/photo-1547496502-affa22d38842?auto=format&fit=crop&q=80&w=800',
                    'vop-nuong-mo-hanh' => 'https://images.unsplash.com/photo-1559132104-583eb531d0db?auto=format&fit=crop&q=80&w=800',
                    'ca-thoi-loi-nuong-muoi-ot' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&q=80&w=800',
                    'tom-kho-ca-mau' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&fit=crop&q=80&w=800',
                    'cua-ca-mau' => 'https://images.unsplash.com/photo-1559742811-822873691df8?auto=format&fit=crop&q=80&w=800',
                    // VÄ©nh Long
                    'thanh-tra-vinh-long' => 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?auto=format&fit=crop&q=80&w=800',
                    'cam-sanh-tam-binh' => 'https://images.unsplash.com/photo-1582979512210-99b6a53386f9?auto=format&fit=crop&q=80&w=800',
                    'khoai-lang-binh-tan' => 'https://images.unsplash.com/photo-1596097635121-14b63b7a0c19?auto=format&fit=crop&q=80&w=800',
                    'buoi-nam-roi-binh-minh' => $uploadPrefix . 'buoi-nam-roi-binh-minh.jpg',
                    'tau-hu-ky-my-hoa' => 'https://images.unsplash.com/photo-1546069901-d576a9a3b683?auto=format&fit=crop&q=80&w=800',
                    // Äá»“ng ThÃ¡p
                    'com-sen' => $uploadPrefix . 'com-sen.jpg',
                    'che-hat-sen' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&q=80&w=800',
                    'ca-loc-nuong-trui-dong-thap' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image21.jpg',
                    'banh-phong-tom-sa-giang' => $uploadPrefix . 'banh-phong-tom-sa-giang.jpg',
                    'nem-lai-vung' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image16.jpg',
                    // An Giang
                    'goi-sau-dau' => $uploadPrefix . 'goi-sau-dau.jpg',
                    'duong-thot-not-bay-nui' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&q=80&w=800',
                    'banh-bo-thot-not' => $uploadPrefix . 'banh-bo-thot-not.jpg',
                    'tung-lo-mo' => $uploadPrefix . 'tung-lo-mo.jpg',
                    'mam-chau-doc' => $uploadPrefix . 'mam-chau-doc.jpg',
                    // Cáº§n ThÆ¡
                    'pizza-hu-tieu' => $uploadPrefix . 'pizza-hu-tieu.jpg',
                    'banh-hoi-mat-vong-phong-dien' => $uploadPrefix . 'banh-hoi-mat-vong-phong-dien.jpg',
                    'nem-nuong-cai-rang' => $uploadPrefix . 'nem-nuong-cai-rang.jpg',
                    'banh-tet-la-cam' => $uploadPrefix . 'banh-tet-la-cam.jpg',
                    'lau-vit-nau-chao' => $uploadPrefix . 'lau-vit-nau-chao.jpg',
                    // TÃ¢y Ninh
                    'mang-cau-ba-den' => $uploadPrefix . 'mang-cau-ba-den.jpg',
                    'banh-trang-cuon-bo' => $uploadPrefix . 'banh-trang-cuon-bo.jpg',
                    'banh-trang-phoi-suong-trang-bang' => $uploadPrefix . 'banh-trang-phoi-suong-trang-bang.jpg',
                    'muoi-tom-tay-ninh' => $uploadPrefix . 'muoi-tom-tay-ninh.jpg',
                    // Äá»“ng Nai
                    'lau-la-kho-qua-rung' => $uploadPrefix . 'lau-la-kho-qua-rung.jpg',
                    'de-com-chien-nuoc-mam' => $uploadPrefix . 'de-com-chien-nuoc-mam.jpg',
                    'nam-moi-dong-nai' => $uploadPrefix . 'nam-moi-dong-nai.jpg',
                    'chom-chom-long-khanh' => $uploadPrefix . 'chom-chom-long-khanh.jpg',
                    'buoi-tan-trieu' => $uploadPrefix . 'buoi-tan-trieu.jpg',
                    'ga-hap-buoi' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image12.jpg',
                    'goi-buoi-tan-trieu' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=800',
                    // TP. Há»“ ChÃ­ Minh
                    'banh-mi-sai-gon' => 'https://statics.vincom.com.vn/xu-huong/0-1-tiem-banh-mi/image18.png',
                    'hu-tieu-nam-vang' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image8.jpg',
                    'bot-chien' => 'https://statics.vincom.com.vn/xu-huong/image11-1668161604.png',
                    'goi-cuon' => 'https://images.unsplash.com/photo-1594020292985-216a72a2c7ce?auto=format&fit=crop&q=80&w=800',
                ];
                $stmtSyncImg = $this->conn->prepare("UPDATE `mon_an` SET `hinh_anh` = :img WHERE `slug` = :slug");
                foreach ($correctDishImages as $slug => $imgUrl) {
                    $stmtSyncImg->execute([':img' => $imgUrl, ':slug' => $slug]);
                }
            } catch(PDOException $e) {}

            // ==========================================
            // Táº O Báº¢NG TIN Tá»¨C & BLOG VÃ€ SEED Dá»® LIá»†U MáºªU
            // ==========================================
            try {
                $this->conn->exec("CREATE TABLE IF NOT EXISTS `tin_tuc` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `tieu_de` varchar(255) NOT NULL,
                    `slug` varchar(255) NOT NULL UNIQUE,
                    `tom_tat` text DEFAULT NULL,
                    `noi_dung` longtext DEFAULT NULL,
                    `hinh_anh` varchar(500) DEFAULT NULL,
                    `danh_muc` varchar(100) DEFAULT 'KhÃ¡m phÃ¡',
                    `tac_gia` varchar(100) DEFAULT 'Ban biÃªn táº­p',
                    `luot_xem` int(11) DEFAULT 0,
                    `noi_bat` tinyint(1) DEFAULT 0,
                    `trang_thai` tinyint(1) DEFAULT 1,
                    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                // ThÃªm bÃ i viáº¿t máº«u náº¿u báº£ng chÆ°a cÃ³ dá»¯ liá»‡u
                $stmtCheckBlog = $this->conn->query("SELECT COUNT(*) FROM `tin_tuc`");
                if ($stmtCheckBlog && $stmtCheckBlog->fetchColumn() == 0) {
                    $insertBlogStmt = $this->conn->prepare("INSERT INTO `tin_tuc` 
                        (`tieu_de`, `slug`, `tom_tat`, `noi_dung`, `hinh_anh`, `danh_muc`, `tac_gia`, `luot_xem`, `noi_bat`) 
                        VALUES (:tieu_de, :slug, :tom_tat, :noi_dung, :hinh_anh, :danh_muc, :tac_gia, :luot_xem, :noi_bat)");

                    $sampleBlogs = [
                        [
                            'tieu_de' => '14 cÃ¡ch Ä‘áº·t tÃªn NhÃ  hÃ ng hay nháº¥t vÃ  nhá»¯ng Ä‘iá»u Cáº¦N TRÃNH tuyá»‡t Ä‘á»‘i',
                            'slug' => '14-cach-dat-ten-nha-hang-hay-nhat',
                            'tom_tat' => 'TÃªn nhÃ  hÃ ng khÃ´ng chá»‰ lÃ  danh xÆ°ng nháº­n diá»‡n thÆ°Æ¡ng hiá»‡u mÃ  cÃ²n quyáº¿t Ä‘á»‹nh Ä‘áº¿n 50% sá»± tÃ² mÃ² vÃ  áº¥n tÆ°á»£ng ban Ä‘áº§u cá»§a thá»±c khÃ¡ch.',
                            'noi_dung' => '<p class="mb-4">TÃªn gá»i nhÃ  hÃ ng chÃ­nh lÃ  Ä‘iá»ƒm tiáº¿p xÃºc Ä‘áº§u tiÃªn giá»¯a thÆ°Æ¡ng hiá»‡u vÃ  khÃ¡ch hÃ ng tiá»m nÄƒng. Má»™t cÃ¡i tÃªn áº¥n tÆ°á»£ng, dá»… nhá»›, mang Ã½ nghÄ©a phÃ¹ há»£p vá»›i phong cÃ¡ch áº©m thá»±c sáº½ giÃºp tÄƒng kháº£ nÄƒng nháº­n diá»‡n vÃ  thu hÃºt khÃ¡ch hÃ ng ngay tá»« Ã¡nh nhÃ¬n Ä‘áº§u tiÃªn.</p><h3 class="text-xl font-bold mb-3 text-gray-800">1. Äáº·t tÃªn gáº¯n liá»n vá»›i mÃ³n Äƒn Ä‘áº·c trÆ°ng</h3><p class="mb-4">CÃ¡ch Ä‘Æ¡n giáº£n vÃ  hiá»‡u quáº£ nháº¥t lÃ  Ä‘Æ°a tÃªn mÃ³n Äƒn chá»§ Ä‘áº¡o vÃ o thÆ°Æ¡ng hiá»‡u, vÃ­ dá»¥: Phá»Ÿ ThÃ¬n, BÃ¡nh XÃ¨o BÃ  DÆ°á»¡ng, CÆ¡m NiÃªu SÃ i GÃ²n. CÃ¡ch nÃ y giÃºp thá»±c khÃ¡ch biáº¿t ngay báº¡n phá»¥c vá»¥ mÃ³n gÃ¬.</p><h3 class="text-xl font-bold mb-3 text-gray-800">2. Äáº·t tÃªn theo Ä‘á»‹a danh xuáº¥t xá»©</h3><p class="mb-4">áº¨m thá»±c vÃ¹ng miá»n luÃ´n cÃ³ sá»©c hÃºt máº¡nh máº½. Äáº·t tÃªn theo Ä‘á»‹a phÆ°Æ¡ng nhÆ° "áº¨m Thá»±c TÃ¢y Báº¯c", "Gá»i CÃ¡ ChÃ¢u Äá»‘c", "BÃ¡nh Canh Tráº£ng BÃ ng" táº¡o niá»m tin vá» sá»± chuáº©n vá»‹ vÃ  nguyÃªn báº£n.</p><h3 class="text-xl font-bold mb-3 text-gray-800">3. Nhá»¯ng Ä‘iá»u tá»‘i ká»µ cáº§n trÃ¡nh</h3><p class="mb-4">â€¢ TrÃ¡nh tÃªn quÃ¡ dÃ i dÃ²ng, khÃ³ phÃ¡t Ã¢m hoáº·c dá»… gÃ¢y hiá»ƒu nháº§m sang Ã½ nghÄ©a tiÃªu cá»±c.<br>â€¢ TrÃ¡nh sao chÃ©p y nguyÃªn thÆ°Æ¡ng hiá»‡u Ä‘Ã£ Ä‘Äƒng kÃ½ báº£n quyá»n Ä‘á»ƒ trÃ¡nh rá»§i ro phÃ¡p lÃ½.<br>â€¢ TrÃ¡nh Ä‘áº·t tÃªn khÃ´ng Äƒn nháº­p gÃ¬ vá»›i phong cÃ¡ch kiáº¿n trÃºc vÃ  menu cá»§a quÃ¡n.</p>',
                            'hinh_anh' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=600',
                            'danh_muc' => 'Kinh doanh Äƒn uá»‘ng',
                            'tac_gia' => 'ChuyÃªn gia F&B',
                            'luot_xem' => 1250,
                            'noi_bat' => 1
                        ],
                        [
                            'tieu_de' => 'BÃ¹ng ná»• doanh sá»‘ nhá» chiáº¿n lÆ°á»£c Marketing NhÃ  HÃ ng Ä‘Ãºng cÃ¡ch',
                            'slug' => 'bung-no-doanh-so-nho-chien-luoc-marketing-nha-hang',
                            'tom_tat' => 'KhÃ¡m phÃ¡ cÃ¡c kÃªnh tiáº¿p cáº­n thá»±c khÃ¡ch hiá»‡u quáº£ nháº¥t tá»« máº¡ng xÃ£ há»™i, chÆ°Æ¡ng trÃ¬nh Æ°u Ä‘Ã£i giá» vÃ ng Ä‘áº¿n tá»‘i Æ°u hÃ³a Ä‘áº·t bÃ n trá»±c tuyáº¿n.',
                            'noi_dung' => '<p class="mb-4">Trong thá»i Ä‘áº¡i sá»‘ hÃ³a, cháº¥t lÆ°á»£ng mÃ³n Äƒn ngon lÃ  Ä‘iá»u kiá»‡n cáº§n, nhÆ°ng chiáº¿n lÆ°á»£c Marketing thÃ´ng minh má»›i lÃ  Ä‘iá»u kiá»‡n Ä‘á»§ Ä‘á»ƒ nhÃ  hÃ ng luÃ´n kÃ­n bÃ n vÃ o má»i khung giá».</p><h3 class="text-xl font-bold mb-3 text-gray-800">1. Táº­n dá»¥ng hÃ¬nh áº£nh vÃ  video ngáº¯n (Shorts/Reels/TikTok)</h3><p class="mb-4">Thá»±c khÃ¡ch "Äƒn báº±ng máº¯t" trÆ°á»›c khi thÆ°á»Ÿng thá»©c báº±ng vá»‹ giÃ¡c. Nhá»¯ng video quay cáº­n cáº£nh miáº¿ng thá»‹t nÆ°á»›ng xÃ¨o xÃ¨o, ná»“i láº©u sÃ´i sÃ¹ng sá»¥c luÃ´n cÃ³ sá»©c lan tá»a tá»± nhiÃªn cá»±c lá»›n trÃªn máº¡ng xÃ£ há»™i.</p><h3 class="text-xl font-bold mb-3 text-gray-800">2. Káº¿t ná»‘i ná»n táº£ng Ä‘áº·t bÃ n trá»±c tuyáº¿n</h3><p class="mb-4">Há»£p tÃ¡c vá»›i cÃ¡c ná»n táº£ng Ä‘áº·t bÃ n uy tÃ­n nhÆ° áº¨m Thá»±c 3 Miá»n giÃºp quÃ¡n tiáº¿p cáº­n hÃ ng trÄƒm nghÃ¬n thá»±c khÃ¡ch cÃ³ nhu cáº§u Ä‘áº·t tiá»‡c, liÃªn hoan vÃ  Äƒn uá»‘ng theo nhÃ³m má»—i ngÃ y.</p><h3 class="text-xl font-bold mb-3 text-gray-800">3. Giá»¯ chÃ¢n khÃ¡ch quen báº±ng chÆ°Æ¡ng trÃ¬nh khÃ¡ch hÃ ng thÃ¢n thiáº¿t</h3><p class="mb-4">Chi phÃ­ tÃ¬m khÃ¡ch má»›i cao gáº¥p 5 láº§n giá»¯ khÃ¡ch cÅ©. TÃ­ch Ä‘iá»ƒm, táº·ng mÃ³n trÃ¡ng miá»‡ng hoáº·c Æ°u Ä‘Ã£i sinh nháº­t lÃ  chÃ¬a khÃ³a xÃ¢y dá»±ng tá»‡p khÃ¡ch ruá»™t trung thÃ nh.</p>',
                            'hinh_anh' => 'https://images.unsplash.com/photo-1498837167922-41c543210940?auto=format&fit=crop&q=80&w=600',
                            'danh_muc' => 'Kinh doanh Äƒn uá»‘ng',
                            'tac_gia' => 'Marketing Team',
                            'luot_xem' => 980,
                            'noi_bat' => 1
                        ],
                        [
                            'tieu_de' => 'BÃ­ quyáº¿t chá»n rÆ°á»£u vang phÃ¹ há»£p vá»›i mÃ³n steak chuáº©n vá»‹ Ã‚u',
                            'slug' => 'bi-quyet-chon-ruou-vang-phu-hop-voi-mon-steak',
                            'tom_tat' => 'Nghá»‡ thuáº­t káº¿t há»£p giá»¯a cÃ¡c loáº¡i rÆ°á»£u vang Ä‘á» nhÆ° Cabernet Sauvignon, Merlot cÃ¹ng cÃ¡c pháº§n thá»‹t bÃ² háº£o háº¡ng Tenderloin, Ribeye.',
                            'noi_dung' => '<p class="mb-4">Sá»± káº¿t há»£p hÃ i hÃ²a giá»¯a rÆ°á»£u vang vÃ  mÃ³n Äƒn (Wine Pairing) cÃ³ thá»ƒ nÃ¢ng táº§m tráº£i nghiá»‡m áº©m thá»±c tá»« ngon miá»‡ng thÃ nh tuyá»‡t tÃ¡c thÄƒng hoa xÃºc cáº£m.</p><h3 class="text-xl font-bold mb-3 text-gray-800">1. Ribeye bÃ©o ngáº­y cÃ¹ng Cabernet Sauvignon Ä‘áº­m Ä‘Ã </h3><p class="mb-4">Pháº§n thá»‹t Ribeye cÃ³ lÆ°á»£ng vÃ¢n má»¡ Ä‘an xen hoÃ n háº£o. Vá»‹ chÃ¡t tannin máº¡nh máº½ cá»§a Cabernet Sauvignon sáº½ cáº¯t qua lá»›p má»¡ bÃ©o ngáº­y, lÃ m bá»«ng lÃªn hÆ°Æ¡ng vá»‹ thá»‹t bÃ² ngá»t má»m.</p><h3 class="text-xl font-bold mb-3 text-gray-800">2. Tenderloin thanh tao cÃ¹ng Pinot Noir hoáº·c Merlot</h3><p class="mb-4">Thá»‹t thÄƒn bÃ² Tenderloin náº¡c má»m, vá»‹ thanh nháº¹. Do Ä‘Ã³, loáº¡i vang Ä‘á» vá»«a váº·n nhÆ° Pinot Noir hoáº·c Merlot vá»›i ná»‘t hÆ°Æ¡ng quáº£ má»ng chÃ­n sáº½ lÃ  ngÆ°á»i báº¡n Ä‘á»“ng hÃ nh lÃ½ tÆ°á»Ÿng nháº¥t.</p>',
                            'hinh_anh' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=600',
                            'danh_muc' => 'MÃ³n ngon & CÃ´ng thá»©c',
                            'tac_gia' => 'Sommelier Tuáº¥n Anh',
                            'luot_xem' => 640,
                            'noi_bat' => 0
                        ],
                        [
                            'tieu_de' => 'Top 10 quÃ¡n Äƒn ngon ná»©c tiáº¿ng SÃ i GÃ²n nháº¥t Ä‘á»‹nh pháº£i thá»­ má»™t láº§n',
                            'slug' => 'top-10-quan-an-ngon-nuc-tieng-sai-gon',
                            'tom_tat' => 'KhÃ¡m phÃ¡ danh sÃ¡ch cÃ¡c quÃ¡n Äƒn gáº¯n liá»n vá»›i kÃ½ á»©c áº©m thá»±c SÃ i GÃ²n tá»« cÆ¡m táº¥m Ä‘Ãªm, há»§ tiáº¿u Nam Vang Ä‘áº¿n láº©u bÃ² gia truyá»n.',
                            'noi_dung' => '<p class="mb-4">SÃ i GÃ²n lÃ  nÆ¡i há»™i tá»¥ tinh hoa áº©m thá»±c tá»© phÆ°Æ¡ng vá»›i nhá»‹p sá»‘ng sÃ´i Ä‘á»™ng vÃ  nhá»¯ng quÃ¡n Äƒn má»Ÿ thÃ¢u Ä‘Ãªm suá»‘t sÃ¡ng phá»¥c vá»¥ má»i thá»±c khÃ¡ch sÃ nh Äƒn.</p><h3 class="text-xl font-bold mb-3 text-gray-800">1. CÆ¡m táº¥m sÆ°á»n bÃ¬ cháº£ SÃ i GÃ²n</h3><p class="mb-4">HÆ°Æ¡ng vá»‹ sÆ°á»n nÆ°á»›ng má»¡ hÃ nh thÆ¡m ná»©c mÅ©i trÃªn báº¿p than há»“ng, Äƒn cÃ¹ng háº¡t cÆ¡m táº¥m dáº»o bÃ¹i vÃ  chÃ©n nÆ°á»›c máº¯m káº¹o á»›t cay ná»“ng lÃ  biá»ƒu tÆ°á»£ng áº©m thá»±c khÃ´ng thá»ƒ thay tháº¿.</p><h3 class="text-xl font-bold mb-3 text-gray-800">2. PhÃ¡ láº¥u bÃ² nÆ°á»›c dá»«a bÃ©o thÆ¡m</h3><p class="mb-4">Tá»«ng miáº¿ng lÃ²ng bÃ² giÃ²n sáº§n sáº­t ninh trong nÆ°á»›c cá»‘t dá»«a vÃ  nÆ°á»›c dá»«a tÆ°Æ¡i ngá»t lá»‹m, cháº¥m bÃ¡nh mÃ¬ giÃ²n rá»¥m trong chÃ©n nÆ°á»›c máº¯m táº¯c chua ngá»t khiáº¿n ai Äƒn má»™t láº§n cÅ©ng nhá»› mÃ£i.</p>',
                            'hinh_anh' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=600',
                            'danh_muc' => 'Äá»‹a Äiá»ƒm Ä‚n Uá»‘ng',
                            'tac_gia' => 'Reviewer SÃ i GÃ²n',
                            'luot_xem' => 1530,
                            'noi_bat' => 1
                        ],
                        [
                            'tieu_de' => 'Kinh nghiá»‡m Ä‘áº·t tiá»‡c táº¥t niÃªn vÃ  liÃªn hoan cÃ´ng ty tiáº¿t kiá»‡m mÃ  sang trá»ng',
                            'slug' => 'kinh-nghiem-dat-tiec-tat-nien-va-lien-hoan-cong-ty',
                            'tom_tat' => 'Cáº©m nang chá»n khÃ´ng gian phÃ²ng riÃªng, lÃªn thá»±c Ä‘Æ¡n theo set menu vÃ  sÄƒn voucher Æ°u Ä‘Ã£i Ä‘áº·t tiá»‡c cuá»‘i nÄƒm cho doanh nghiá»‡p.',
                            'noi_dung' => '<p class="mb-4">Cuá»‘i nÄƒm lÃ  mÃ¹a cá»§a nhá»¯ng buá»•i gáº·p máº·t, tá»•ng káº¿t vÃ  liÃªn hoan gáº¯n káº¿t tÃ¬nh Ä‘á»“ng nghiá»‡p. LÃªn káº¿ hoáº¡ch Ä‘áº·t tiá»‡c sá»›m giÃºp doanh nghiá»‡p cÃ³ phÃ²ng Ä‘áº¹p vÃ  nháº­n Ä‘Æ°á»£c nhiá»u Æ°u Ä‘Ã£i nháº¥t.</p><h3 class="text-xl font-bold mb-3 text-gray-800">1. Äáº·t chá»— trÆ°á»›c Ã­t nháº¥t 2 Ä‘áº¿n 3 tuáº§n</h3><p class="mb-4">VÃ o mÃ¹a cao Ä‘iá»ƒm, cÃ¡c nhÃ  hÃ ng cÃ³ phÃ²ng VIP vÃ  sáº£nh tiá»‡c riÃªng thÆ°á»ng kÃ­n lá»‹ch ráº¥t sá»›m. Äáº·t cá»c sá»›m giÃºp báº¡n giá»¯ Ä‘Æ°á»£c vá»‹ trÃ­ Ä‘áº¹p vÃ  chá»§ Ä‘á»™ng sáº¯p xáº¿p sÃ¢n kháº¥u, Ã¢m thanh.</p><h3 class="text-xl font-bold mb-3 text-gray-800">2. Æ¯u tiÃªn thá»±c Ä‘Æ¡n Set Menu trá»n gÃ³i</h3><p class="mb-4">Chá»n Set Menu vá»«a kiá»ƒm soÃ¡t tá»‘t chi phÃ­ theo Ä‘áº§u ngÆ°á»i, vá»«a Ä‘áº£m báº£o mÃ³n Äƒn ra nhanh, nÃ³ng há»•i vÃ  Ä‘áº§y Ä‘á»§ tá»« khai vá»‹, mÃ³n chÃ­nh Ä‘áº¿n trÃ¡ng miá»‡ng.</p>',
                            'hinh_anh' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=600',
                            'danh_muc' => 'Tiá»‡c & Tá»• chá»©c tiá»‡c',
                            'tac_gia' => 'Ban biÃªn táº­p AT3M',
                            'luot_xem' => 720,
                            'noi_bat' => 0
                        ],
                        [
                            'tieu_de' => 'HÃ nh trÃ¬nh khÃ¡m phÃ¡ vÄƒn hÃ³a áº©m thá»±c miá»n TÃ¢y mÃ¹a nÆ°á»›c ná»•i',
                            'slug' => 'hanh-trinh-kham-pha-van-hoa-am-thuc-mien-tay-mua-nuoc-noi',
                            'tom_tat' => 'MÃ¹a nÆ°á»›c ná»•i vá» mang theo nguá»“n thá»§y sáº£n dá»“i dÃ o, sáº¯c vÃ ng rá»±c rá»¡ cá»§a bÃ´ng Ä‘iÃªn Ä‘iá»ƒn vÃ  nhá»¯ng mÃ³n ngon má»™c máº¡c lÃ m say lÃ²ng ngÆ°á»i lá»¯ khÃ¡ch.',
                            'noi_dung' => '<p class="mb-4">Tá»« thÃ¡ng 8 Ä‘áº¿n thÃ¡ng 11 Ã¢m lá»‹ch, phÃ¹ sa sÃ´ng MÃª KÃ´ng trÃ n vá» ÄBSCL táº¡o nÃªn mÃ¹a nÆ°á»›c ná»•i - mÃ¹a thiÃªn nhiÃªn hÃ o phÃ³ng ban táº·ng sáº£n váº­t trÃ¹ phÃº cho ngÆ°á»i dÃ¢n Nam Bá»™.</p><h3 class="text-xl font-bold mb-3 text-gray-800">1. Ná»“i láº©u cÃ¡ linh bÃ´ng Ä‘iÃªn Ä‘iá»ƒn nghi ngÃºt khÃ³i</h3><p class="mb-4">CÃ¡ linh non bÃ©o ngá»t, má»m cáº£ xÆ°Æ¡ng káº¿t há»£p cÃ¹ng vá»‹ chÃ¡t nháº¹ ngá»t háº­u cá»§a bÃ´ng Ä‘iÃªn Ä‘iá»ƒn vÃ ng Æ°Æ¡m vÃ  nÆ°á»›c láº©u me chua thanh lÃ m nÃªn hÆ°Æ¡ng vá»‹ khÃ³ quÃªn cá»§a Ä‘á»“ng báº±ng sÃ´ng Cá»­u Long.</p><h3 class="text-xl font-bold mb-3 text-gray-800">2. CÃ¡ lÃ³c Ä‘á»“ng nÆ°á»›ng trui rÆ¡m vÃ ng thÆ¡m lá»«ng</h3><p class="mb-4">CÃ¡ lÃ³c tÆ°Æ¡i vá»«a báº¯t dÆ°á»›i mÆ°Æ¡ng Ä‘Æ°á»£c xiÃªn que tre, vÃ¹i trong Ä‘á»‘ng rÆ¡m khÃ´ vÃ  Ä‘á»‘t chÃ¡y. Váº£y cÃ¡ chÃ¡y Ä‘en Ä‘Æ°á»£c cáº¡o sáº¡ch, lá»™ ra thá»› thá»‹t tráº¯ng phau, bá»‘c khÃ³i thÆ¡m ná»“ng cuá»‘n bÃ¡nh trÃ¡ng rau rá»«ng cháº¥m máº¯m me.</p>',
                            'hinh_anh' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=600',
                            'danh_muc' => 'KhÃ¡m phÃ¡',
                            'tac_gia' => 'Phan Anh QuÃ¢n',
                            'luot_xem' => 1100,
                            'noi_bat' => 1
                        ]
                    ];

                    foreach ($sampleBlogs as $sb) {
                        $insertBlogStmt->execute([
                            ':tieu_de' => $sb['tieu_de'],
                            ':slug' => $sb['slug'],
                            ':tom_tat' => $sb['tom_tat'],
                            ':noi_dung' => $sb['noi_dung'],
                            ':hinh_anh' => $sb['hinh_anh'],
                            ':danh_muc' => $sb['danh_muc'],
                            ':tac_gia' => $sb['tac_gia'],
                            ':luot_xem' => $sb['luot_xem'],
                            ':noi_bat' => $sb['noi_bat']
                        ]);
                    }
                }
            } catch(PDOException $e) {}
        } catch(PDOException $exception) {
            echo "Lá»—i káº¿t ná»‘i cÆ¡ sá»Ÿ dá»¯ liá»‡u: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>

