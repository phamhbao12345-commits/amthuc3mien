CREATE DATABASE IF NOT EXISTS am_thuc_3_mien DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE am_thuc_3_mien;

DROP TABLE IF EXISTS `mon_an`;
DROP TABLE IF EXISTS `vung_mien`;

CREATE TABLE `vung_mien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_vung` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `vung_mien` (`id`, `ten_vung`) VALUES
(1, 'Bắc'),
(2, 'Trung'),
(3, 'Nam');

CREATE TABLE `mon_an` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_mon` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `mo_ta_ngan` text DEFAULT NULL,
  `nguyen_lieu` text DEFAULT NULL,
  `cach_lam` text DEFAULT NULL,
  `id_vung_mien` int(11) NOT NULL,
  `gia_tien` decimal(10,2) DEFAULT '0.00',
  `thoi_gian_nau` int(11) DEFAULT '0',
  `danh_gia` decimal(2,1) DEFAULT '0.0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `id_vung_mien` (`id_vung_mien`),
  CONSTRAINT `mon_an_ibfk_1` FOREIGN KEY (`id_vung_mien`) REFERENCES `vung_mien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `mon_an` (`id`, `ten_mon`, `slug`, `hinh_anh`, `mo_ta_ngan`, `nguyen_lieu`, `cach_lam`, `id_vung_mien`, `gia_tien`, `thoi_gian_nau`, `danh_gia`) VALUES
(1, 'Phở Hà Nội', 'pho-ha-noi', 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&q=80&w=1000', 'Hương vị truyền thống tinh tế, nước dùng thanh tao ninh từ xương ống bò cùng các gia vị thảo mộc đặc trưng của mảnh đất kinh kỳ.', 'Bánh phở mềm, thịt bò tươi, xương ống, quế, hồi, thảo quả, hành lá.', 'Ninh xương bò 8-10 tiếng. Thái mỏng thịt bò. Trần bánh phở, thêm thịt và chan nước dùng nóng hổi.', 1, 65000.00, 480, 4.9),
(2, 'Bún Bò Huế', 'bun-bo-hue', 'https://images.unsplash.com/photo-1594922119156-f00e572ac065?auto=format&fit=crop&q=80&w=1000', 'Bản giao hưởng của vị giác với nước dùng đậm đà mùi mắm ruốc, sả ớt, mang đậm cốt cách ẩm thực cố đô.', 'Bắp bò, Nạm bò, Giò heo, Chả cua, Mắm ruốc Huế, Sả, Hành tây, Bún sợi to', 'Ninh xương và bắp bò với sả đập dập. Pha mắm ruốc chắt lấy nước trong. Nêm nếm gia vị vừa ăn. Trình bày ra tô cùng bún và rau sống.', 2, 75000.00, 150, 4.8),
(3, 'Cơm Tấm Sài Gòn', 'com-tam-sai-gon', 'https://images.unsplash.com/photo-1626804475297-41609ea004eb?auto=format&fit=crop&q=80&w=1000', 'Món ăn dân dã vươn tầm nghệ thuật, sườn nướng mỡ hành thơm nức mũi kết hợp cùng nước mắm chua ngọt đặc sánh quyến rũ.', 'Gạo tấm, sườn cốt lết, bì heo, trứng, nước mắm ngon, mỡ hành.', 'Nấu cơm từ hạt tấm. Ướp sườn với mật ong và sả rồi nướng than hoa. Pha mắm kẹo chua ngọt.', 3, 85000.00, 45, 5.0);

-- ==========================================
-- BỔ SUNG BẢNG RESTAURANTS & FULLTEXT INDEX
-- ==========================================

-- 1. Tạo bảng restaurants (Nếu chưa có)
CREATE TABLE IF NOT EXISTS `restaurants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `district` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT '0.0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Giả định bảng mon_an cần thêm khóa ngoại restaurant_id để LEFT JOIN
-- (Tránh lỗi nếu bảng chưa có cột này)
ALTER TABLE `mon_an` ADD COLUMN IF NOT EXISTS `restaurant_id` int(11) DEFAULT NULL;

-- 2. Thêm FULLTEXT INDEX cho bảng restaurants (quét tên, địa chỉ, quận)
ALTER TABLE `restaurants` ADD FULLTEXT INDEX `ft_restaurant` (`name`, `address`, `district`);

-- 3. Thêm FULLTEXT INDEX cho bảng mon_an (quét tên món)
ALTER TABLE `mon_an` ADD FULLTEXT INDEX `ft_mon_an` (`ten_mon`);

-- ==========================================
-- BẢNG USERS CHO TRANG ADMIN
-- ==========================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `role` enum('admin', 'user') DEFAULT 'user',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `users` (`username`, `password`, `fullname`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'); -- password is 'password'
