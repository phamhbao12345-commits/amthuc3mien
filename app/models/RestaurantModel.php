<?php
require_once __DIR__ . '/Model.php';

class RestaurantModel extends Model {
    protected $table = 'restaurants';

    public function getAll($limit = null) {
        $sql = "SELECT * FROM restaurants ORDER BY id DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->fetchAll($sql);
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM restaurants WHERE id = ?", [$id]);
    }

    public function countAll() {
        return $this->count('restaurants');
    }

    public function create($data) {
        $sql = "INSERT INTO restaurants (name, address, district, rating, image) VALUES (?, ?, ?, ?, ?)";
        $this->query($sql, [
            $data['name'] ?? '',
            $data['address'] ?? '',
            $data['district'] ?? '',
            $data['rating'] ?? 0,
            $data['image'] ?? ''
        ]);
        return $this->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE restaurants SET name = ?, address = ?, district = ?, rating = ?, image = ? WHERE id = ?";
        return $this->query($sql, [
            $data['name'],
            $data['address'],
            $data['district'],
            $data['rating'],
            $data['image'],
            $id
        ]);
    }

    public function delete($id) {
        return $this->query("DELETE FROM restaurants WHERE id = ?", [$id]);
    }

    /**
     * Get reputable restaurants with fallback
     */
    public function getReputable() {
        $restaurants = [];
        try {
            $restaurants = $this->fetchAll("SELECT * FROM restaurants ORDER BY rating DESC");
        } catch (Exception $e) {}

        if (empty($restaurants)) {
            $restaurants = [
                [
                    'name' => 'Việt Phố - 45,47,49 Lê Quý Đôn',
                    'address' => 'Số 45 - 47 - 49 Lê Quý Đôn, Phường Võ Thị Sáu, Quận 3',
                    'image' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&q=80&w=600',
                    'rating' => 5.0,
                    'promo' => 'Giảm 15%',
                    'categories' => 'Hải sản tươi sống, Gọi món Á-Âu'
                ],
                [
                    'name' => 'Phố 79 - 120 Sương Nguyệt Ánh',
                    'address' => 'Số 120 - 120 Bis Sương Nguyệt Ánh, Phường Bến Thành, Quận 1',
                    'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=600',
                    'rating' => 4.5,
                    'promo' => 'Đặt bàn giữ chỗ',
                    'categories' => 'Gọi món Việt'
                ],
                [
                    'name' => 'Hoàng Yến Cuisine - Ngô Đức Kế',
                    'address' => '07-09 Ngô Đức Kế, P. Bến Nghé, Quận 1',
                    'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&q=80&w=600',
                    'rating' => 4.8,
                    'promo' => 'Đặt bàn giữ chỗ',
                    'categories' => 'Gọi món Việt'
                ]
            ];
        }
        return $restaurants;
    }

    /**
     * Get promotion restaurants
     */
    public function getPromotions() {
        return [
            [
                'name' => 'Kingscross - Tôn Dật Tiên',
                'address' => 'CR1 - 11&12, 103 Tôn Dật Tiên, P. Tân Phú, Quận 7',
                'image' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&q=80&w=600',
                'rating' => 4.5,
                'promo' => 'Giảm 50%',
                'categories' => 'Gọi món Âu'
            ],
            [
                'name' => 'Madame Lam - Trần Ngọc Diện',
                'address' => 'Số 10 Trần Ngọc Diện, P. Thảo Điền, TP. Thủ Đức',
                'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=600',
                'rating' => 4.2,
                'promo' => 'Giảm tới 50%',
                'categories' => 'Gọi món Việt'
            ],
            [
                'name' => 'D.A Dining - Nguyễn Thị Minh Khai',
                'address' => 'Tầng 26, Tòa nhà Centec - Số 72-74 Nguyễn Thị Minh Khai, Quận 3',
                'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=600',
                'rating' => 4.1,
                'promo' => 'Giảm tới 50%',
                'categories' => 'Gọi món Trung Hoa'
            ],
            [
                'name' => 'Thai Market - Aeon Mall Bình Tân',
                'address' => 'Tầng 3, AEON Mall, Số 1 Đường 17A, P. Bình Trị Đông B, Quận Bình Tân',
                'image' => 'https://images.unsplash.com/photo-1559314809-0d155014e29e?auto=format&fit=crop&q=80&w=600',
                'rating' => 4.8,
                'promo' => 'Giảm tới 32%',
                'categories' => 'Gọi món Thái'
            ],
            [
                'name' => 'Thai Market - Cao Thắng',
                'address' => 'Số 95 Cao Thắng, Phường 3, Quận 3',
                'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=600',
                'rating' => 4.6,
                'promo' => 'Giảm tới 30%',
                'categories' => 'Gọi món Thái'
            ],
            [
                'name' => 'Thai Market - Phan Xích Long',
                'address' => '219 Phan Xích Long, P.2, Phú Nhuận',
                'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=600',
                'rating' => 4.7,
                'promo' => 'Giảm tới 30%',
                'categories' => 'Gọi món Thái'
            ]
        ];
    }

    /**
     * Tìm kiếm nhà hàng theo từ khóa và khu vực (an toàn, chính xác và không phụ thuộc Fulltext Index)
     */
    public function searchRestaurants($keyword = '', $location = '') {
        $keyword = trim($keyword);
        $location = trim($location);

        $sql = "SELECT r.* FROM restaurants r WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (r.name LIKE :kw OR r.address LIKE :kw OR r.district LIKE :kw)";
            $params[':kw'] = '%' . $keyword . '%';
        }

        if (!empty($location) && $location !== 'Tất cả' && $location !== 'Tất cả khu vực') {
            if (in_array($location, ['Hồ Chí Minh', 'TP.HCM', 'Sài Gòn', 'TP. Hồ Chí Minh'])) {
                $sql .= " AND (r.district LIKE '%Quận%' OR r.district LIKE '%Gò Vấp%' OR r.district LIKE '%Thủ Đức%' OR r.district LIKE :loc OR r.address LIKE :loc)";
                $params[':loc'] = '%' . $location . '%';
            } else {
                $sql .= " AND (r.district LIKE :loc OR r.address LIKE :loc OR r.name LIKE :loc)";
                $params[':loc'] = '%' . $location . '%';
            }
        }

        $sql .= " ORDER BY r.rating DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy danh sách quán ăn gợi ý CHUẨN XÁC theo món ăn và tỉnh thành của món ăn đó
     */
    public function getSuggestedRestaurantsForDish($monAn) {
        if (empty($monAn)) return [];

        $slug = $monAn['slug'] ?? '';
        $tenMon = $monAn['ten_mon'] ?? '';
        $tinhThanh = $monAn['tinh_thanh'] ?? '';
        $tenVung = $monAn['ten_vung'] ?? '';

        // 1. Kiểm tra danh bạ quán ăn nức tiếng theo slug
        $danhBa = self::getCuratedRestaurantsDirectory();
        if (!empty($danhBa[$slug])) {
            return $danhBa[$slug];
        }

        // 2. Tra cứu trong Database restaurants nếu có quán cùng tỉnh thành và món ăn
        if (!empty($this->conn) && !empty($tinhThanh)) {
            try {
                $stmt = $this->conn->prepare("SELECT * FROM restaurants 
                    WHERE (district LIKE :prov OR address LIKE :prov OR name LIKE :prov)
                    ORDER BY 
                        CASE WHEN name LIKE :tenMon THEN 1 ELSE 2 END,
                        rating DESC LIMIT 3");
                $stmt->execute([
                    ':prov' => '%' . $tinhThanh . '%',
                    ':tenMon' => '%' . $tenMon . '%'
                ]);
                $dbRes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($dbRes) && count($dbRes) >= 2) {
                    $resList = [];
                    foreach ($dbRes as $r) {
                        $resList[] = [
                            'name' => $r['name'],
                            'address' => $r['address'] . (!empty($r['district']) && strpos($r['address'], $r['district']) === false ? ', ' . $r['district'] : ''),
                            'rating' => $r['rating'] ?? 4.8,
                            'price' => '40.000đ - 120.000đ',
                            'desc' => 'Địa chỉ thưởng thức ' . $tenMon . ' nức tiếng tại ' . $tinhThanh,
                            'image' => $r['image'] ?? 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                            'tag' => 'Đặc sản ' . $tinhThanh
                        ];
                    }
                    return $resList;
                }
            } catch (PDOException $e) {}
        }

        // 3. Fallback thông minh: Tạo 3 địa chỉ gợi ý chuẩn tại chính tỉnh thành của món ăn
        return self::generateFallbackRestaurants($tenMon, $tinhThanh, $tenVung);
    }

    /**
     * Danh bạ các quán ăn nức tiếng thực tế tại đúng từng tỉnh thành cho các món đặc sản
     */
    public static function getCuratedRestaurantsDirectory() {
        return [
            // ==========================================
            // 1. TÂY NINH (8 MÓN)
            // ==========================================
            'banh-canh-trang-bang' => [
                [
                    'name' => 'Bánh Canh Trảng Bàng Hoàng Minh',
                    'address' => '38 Quốc Lộ 22, Thị xã Trảng Bàng, Tây Ninh',
                    'rating' => 4.9,
                    'price' => '45.000đ - 65.000đ',
                    'desc' => 'Quán bánh canh Trảng Bàng truyền thống lâu đời nổi tiếng nhất Tây Ninh',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image34.png',
                    'tag' => 'Lâu đời nhất'
                ],
                [
                    'name' => 'Bánh Canh Út Huệ Trảng Bàng',
                    'address' => '88 Nguyễn Du, Khu phố Lộc An, TX. Trảng Bàng, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '40.000đ - 60.000đ',
                    'desc' => 'Nước dùng trong veo ngọt thanh hầm xương giò, phục vụ đĩa rau rừng tươi mơn mởn',
                    'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chuẩn vị truyền thống'
                ],
                [
                    'name' => 'Bánh Canh Năm Dung',
                    'address' => '90 Quốc Lộ 22, Gia Huỳnh, Trảng Bàng, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '45.000đ - 60.000đ',
                    'desc' => 'Không gian rộng rãi, giò heo giòn ngọt chấm nước mắm ớt hiểm trứ danh',
                    'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nhiều thực khách yêu thích'
                ]
            ],
            'bo-to-tay-ninh' => [
                [
                    'name' => 'Bò Tơ Tây Ninh Năm Sánh (Cây Trường)',
                    'address' => 'Đường QL22B, Hiệp Tân, Hòa Thành, Tây Ninh',
                    'rating' => 4.9,
                    'price' => '120.000đ - 250.000đ',
                    'desc' => 'Thương hiệu bò tơ số 1 Tây Ninh, thịt tươi non nướng than hoa thơm lừng',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image23.jpg',
                    'tag' => 'Thương hiệu số 1'
                ],
                [
                    'name' => 'Bò Tơ Út Khương Tây Ninh',
                    'address' => 'Đường 30/4, Phường 3, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '100.000đ - 220.000đ',
                    'desc' => 'Bò tơ cuốn bánh tráng rau rừng, bò nhúng dấm và lẩu đuôi bò đậm đà',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đặc sản Tây Ninh'
                ],
                [
                    'name' => 'Bò Tơ Trường Lạc',
                    'address' => 'Ấp Trung Lập, Xã An Lập, Trảng Bàng, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '110.000đ - 200.000đ',
                    'desc' => 'Thịt bò tơ thui rơm vàng ươm, mềm ngọt tự nhiên chấm mắm nêm đậm vị',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Bò tơ thui rơm'
                ]
            ],
            'banh-trang-phoi-suong-trang-bang' => [
                [
                    'name' => 'Bánh Tráng Phơi Sương Hoàng Ty',
                    'address' => 'Quốc Lộ 22, Phường Trảng Bàng, TX. Trảng Bàng, Tây Ninh',
                    'rating' => 4.9,
                    'price' => '60.000đ - 120.000đ',
                    'desc' => 'Bánh tráng phơi sương dẻo mềm cuốn thịt luộc và rổ rau rừng hơn 15 loại',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image20.jpg',
                    'tag' => 'Nổi tiếng nhất'
                ],
                [
                    'name' => 'Bánh Tráng Phơi Sương Bé Năm',
                    'address' => '45 Gia Long, Khu phố Lộc Du, Trảng Bàng, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '50.000đ - 90.000đ',
                    'desc' => 'Bánh tráng lò thủ công nướng than phơi sương sớm, ăn kèm mắm nêm cá cơm nguyên chất',
                    'image' => 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Lò bánh truyền thống'
                ],
                [
                    'name' => 'Đặc Sản Tây Ninh Út Yến',
                    'address' => 'Khu phố Lộc Du, Thị xã Trảng Bàng, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '45.000đ - 85.000đ',
                    'desc' => 'Địa chỉ uy tín mua bánh tráng phơi sương chính gốc làm quà biếu',
                    'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đặc sản chính gốc'
                ]
            ],
            'muoi-tom-tay-ninh' => [
                [
                    'name' => 'Cơ Sở Muối Tôm Ý Như Tây Ninh',
                    'address' => 'Đường 30/4, Phường 1, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.9,
                    'price' => '25.000đ - 60.000đ/hũ',
                    'desc' => 'Hạt muối tôm to đỏ cam tự nhiên, vị cay mặn ngọt bùng nổ vị giác',
                    'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Lò sản xuất lớn'
                ],
                [
                    'name' => 'Muối Tôm Mỹ Vân - Chợ Long Hoa',
                    'address' => 'Khu B Chợ Long Hoa, Hòa Thành, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '30.000đ - 70.000đ/hũ',
                    'desc' => 'Muối tôm loại 1 giã tay truyền thống, vị tôm đậm đà chấm trái cây cực ngon',
                    'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chợ Long Hoa'
                ],
                [
                    'name' => 'Muối Tôm Chế Liễu Tây Ninh',
                    'address' => 'Quốc lộ 22, Thị xã Trảng Bàng, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '25.000đ - 55.000đ/hũ',
                    'desc' => 'Thương hiệu quen thuộc của du khách trên cung đường hành hương Tây Ninh',
                    'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Được tin dùng'
                ]
            ],
            'banh-trang-me' => [
                [
                    'name' => 'Quán Bánh Tráng Me Chợ Đêm Tây Ninh',
                    'address' => 'Đường Yết Kiêu, Phường 1, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '15.000đ - 30.000đ',
                    'desc' => 'Sốt me dẻo quánh chua ngọt kèm sa tế cay xé lưỡi và đậu phộng thơm bùi',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image22.jpg',
                    'tag' => 'Ăn vặt hot'
                ],
                [
                    'name' => 'Bánh Tráng Me Cô Ba Tây Ninh',
                    'address' => 'Đường 30/4, Phường 3, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '15.000đ - 25.000đ',
                    'desc' => 'Bánh tráng phơi sương dẻo cuốn sốt me gia truyền chuẩn vị',
                    'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chuẩn vị sốt me'
                ],
                [
                    'name' => 'Bánh Tráng Me Thảo Vy Trảng Bàng',
                    'address' => 'Đường Gia Long, Thị xã Trảng Bàng, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '15.000đ - 35.000đ',
                    'desc' => 'Điểm dừng chân yêu thích của giới trẻ thưởng thức đặc sản bánh tráng Tây Ninh',
                    'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Được giới trẻ yêu thích'
                ]
            ],
            'banh-trang-cuon-bo' => [
                [
                    'name' => 'Bánh Tráng Cuộn Bơ Chợ Long Hoa',
                    'address' => 'Chợ Long Hoa, Thị xã Hòa Thành, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '20.000đ - 35.000đ',
                    'desc' => 'Bánh tráng đỏ dẻo dai ngập tràn bơ trứng gà béo ngậy, khô bò đen và tép sấy',
                    'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Béo ngậy chuẩn vị'
                ],
                [
                    'name' => 'Bánh Tráng Bơ Út Diệu TP. Tây Ninh',
                    'address' => 'Đường Võ Thị Sáu, Phường 4, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '20.000đ - 30.000đ',
                    'desc' => 'Bơ trứng gà tươi đánh bông mịn béo thơm, sốt me cay ngọt hài hòa',
                    'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Quán quen Tây Ninh'
                ],
                [
                    'name' => 'Ăn Vặt Bánh Tráng Bơ Trảng Bàng',
                    'address' => 'Quốc lộ 22, Thị xã Trảng Bàng, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '15.000đ - 30.000đ',
                    'desc' => 'Topping trứng cút và hành phi giòn tan, ăn là ghiền',
                    'image' => 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Hương vị tuổi thơ'
                ]
            ],
            'than-lan-nui-ba-den' => [
                [
                    'name' => 'Quán Đặc Sản Núi Bà Đen Hồng Tiến',
                    'address' => 'Chân núi Bà Đen, Ninh Sơn, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '100.000đ - 180.000đ',
                    'desc' => 'Thằn lằn núi nướng giòn than hoa, chiên xù và nấu cháo đậu xanh bổ dưỡng',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image14.jpg',
                    'tag' => 'Đặc sản núi Bà Đen'
                ],
                [
                    'name' => 'Quán Ăn Lâm Viên Núi Bà',
                    'address' => 'Đường Bời Lời, Ninh Sơn, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '90.000đ - 160.000đ',
                    'desc' => 'Không gian thoáng mát sát chân núi, phục vụ các món thịt rừng thảo mộc độc đáo',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chân núi Bà Đen'
                ],
                [
                    'name' => 'Quán Đồng Quê Thạnh Tân',
                    'address' => 'Xã Thạnh Tân, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.7,
                    'price' => '100.000đ - 150.000đ',
                    'desc' => 'Món ăn dân dã nướng than hoa chấm muối tiêu chanh cay tê đặc trưng',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Dân dã miệt vườn'
                ]
            ],
            'mang-cau-ba-den' => [
                [
                    'name' => 'Vườn Mãng Cầu Bà Đen Minh Trung',
                    'address' => 'Chân núi Bà Đen, Xã Thạnh Tân, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.9,
                    'price' => '50.000đ - 80.000đ/kg',
                    'desc' => 'Vườn mãng cầu ta trĩu quả chín cây, múi dày trắng muốt ngọt lịm dai thơm',
                    'image' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nhà vườn chính gốc'
                ],
                [
                    'name' => 'Vựa Trái Cây Đặc Sản Núi Bà',
                    'address' => 'Đường Bời Lời, Ninh Sơn, TP. Tây Ninh, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '45.000đ - 70.000đ/kg',
                    'desc' => 'Điểm dừng chân bán mãng cầu Bà Đen đạt chuẩn OCOP Tây Ninh làm quà',
                    'image' => 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đạt chuẩn OCOP'
                ],
                [
                    'name' => 'Cửa Hàng Nông Sản Chợ Long Hoa',
                    'address' => 'Thị xã Hòa Thành, Tây Ninh',
                    'rating' => 4.8,
                    'price' => '40.000đ - 75.000đ/kg',
                    'desc' => 'Mãng cầu dai tuyển chọn quả to tròn mắt nở, phục vụ khách tham quan',
                    'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Trái cây tuyển chọn'
                ]
            ],

            // ==========================================
            // 2. TP. HỒ CHÍ MINH (8 MÓN)
            // ==========================================
            'com-tam-sai-gon' => [
                [
                    'name' => 'Cơm Tấm Ba Ghiền',
                    'address' => '84 Đặng Văn Ngữ, Phường 10, Q. Phú Nhuận, TP.HCM',
                    'rating' => 4.9,
                    'price' => '65.000đ - 110.000đ',
                    'desc' => 'Miếng sườn nướng than hoa to che kín đĩa cơm, thơm nức mũi nức lòng thực khách',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image18.jpg',
                    'tag' => 'Huyền thoại Sài Gòn'
                ],
                [
                    'name' => 'Cơm Tấm Phúc Lộc Thọ',
                    'address' => '236 Đinh Tiên Hoàng, Đa Kao, Quận 1, TP.HCM',
                    'rating' => 4.8,
                    'price' => '45.000đ - 75.000đ',
                    'desc' => 'Chuỗi cơm tấm chuẩn vị truyền thống, thịt nướng mềm mọng mỡ hành thơm ngậy',
                    'image' => 'https://images.unsplash.com/photo-1626804475297-41609ea004eb?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Hệ thống chuẩn vị'
                ],
                [
                    'name' => 'Cơm Tấm Tú Mập',
                    'address' => '812 Sư Vạn Hạnh, Phường 12, Quận 10, TP.HCM',
                    'rating' => 4.8,
                    'price' => '50.000đ - 85.000đ',
                    'desc' => 'Sườn cây nướng đậm đà, chả trứng béo bùi và canh khổ qua dồn thịt thanh mát',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nổi tiếng Quận 10'
                ]
            ],
            'banh-mi-sai-gon' => [
                [
                    'name' => 'Bánh Mì Huỳnh Hoa',
                    'address' => '26 Lê Thị Riêng, P. Bến Thành, Quận 1, TP.HCM',
                    'rating' => 4.9,
                    'price' => '58.000đ - 68.000đ',
                    'desc' => 'Chiếc bánh mì nặng trĩu với lớp pate béo ngậy, bơ tươi và ngập tràn giò chả thịt nguội',
                    'image' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Bánh mì ô môi số 1'
                ],
                [
                    'name' => 'Bánh Mì Bảy Hổ',
                    'address' => '19 Huỳnh Khương Ninh, Đa Kao, Quận 1, TP.HCM',
                    'rating' => 4.8,
                    'price' => '25.000đ - 35.000đ',
                    'desc' => 'Hơn 80 năm tuổi đời, pate và thịt luộc ướp theo bí quyết gia truyền thơm lừng',
                    'image' => 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Hơn 80 năm tuổi'
                ],
                [
                    'name' => 'Bánh Mì Như Lan',
                    'address' => '50 Hàm Nghi, Bến Nghé, Quận 1, TP.HCM',
                    'rating' => 4.8,
                    'price' => '35.000đ - 60.000đ',
                    'desc' => 'Thương hiệu ẩm thực lâu đời tại trung tâm Sài Gòn với vỏ bánh giòn ruột xốp',
                    'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Thương hiệu lâu đời'
                ]
            ],
            'pha-lau-bo' => [
                [
                    'name' => 'Phá Lấu Bò Cô Thảo - Quận 4',
                    'address' => '243/29G Tôn Đản, Phường 15, Quận 4, TP.HCM',
                    'rating' => 4.9,
                    'price' => '30.000đ - 50.000đ',
                    'desc' => 'Kinh đô phá lấu Sài Gòn, nước cốt dừa sánh béo chấm bánh mì nóng giòn ăn cùng mắm tắc',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image26.jpg',
                    'tag' => 'Kinh đô Phá lấu Q.4'
                ],
                [
                    'name' => 'Phá Lấu Dì Nủi',
                    'address' => '243/30 Tôn Đản, Phường 15, Quận 4, TP.HCM',
                    'rating' => 4.8,
                    'price' => '30.000đ - 45.000đ',
                    'desc' => 'Lòng bò sạch thơm giòn sần sật, có phá lấu nướng và phá lấu chiên lạ miệng',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Được yêu thích'
                ],
                [
                    'name' => 'Phá Lấu Bò Chú Ba',
                    'address' => '22 Dương Đình Nghệ, Phường 8, Quận 11, TP.HCM',
                    'rating' => 4.7,
                    'price' => '35.000đ - 50.000đ',
                    'desc' => 'Nồi nước phá lấu vàng óng ánh sôi sục, nước chấm mắm me tắc chua cay ngây ngất',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đậm đà gia truyền'
                ]
            ],
            'hu-tieu-nam-vang' => [
                [
                    'name' => 'Hủ Tiếu Nam Vang Nhân Quán',
                    'address' => '122 Nguyễn Thị Nhỏ, Phường 15, Quận 11, TP.HCM',
                    'rating' => 4.9,
                    'price' => '75.000đ - 95.000đ',
                    'desc' => 'Hủ tiếu Nam Vang trứ danh Sài Gòn, nước dùng xương ống mực nướng ngọt thanh đậm đà',
                    'image' => 'https://images.unsplash.com/photo-1594922119156-f00e572ac065?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chuẩn vị Nam Vang'
                ],
                [
                    'name' => 'Hủ Tiếu Nam Vang Quỳnh',
                    'address' => 'A65 Nguyễn Trãi, P. Cư Trinh, Quận 1, TP.HCM',
                    'rating' => 4.8,
                    'price' => '70.000đ - 90.000đ',
                    'desc' => 'Tôm tươi cong đỏ, thịt băm, gan heo và tỏi phi giòn rụm trong tô hủ tiếu khô sốt tương đen',
                    'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nổi tiếng Quận 1'
                ],
                [
                    'name' => 'Hủ Tiếu Nam Vang Thành Đạt',
                    'address' => '34 Cô Bắc, P. Cầu Ông Lãnh, Quận 1, TP.HCM',
                    'rating' => 4.8,
                    'price' => '55.000đ - 70.000đ',
                    'desc' => 'Mở cửa 24/7, điểm đến ấm cúng phục vụ bát hủ tiếu nóng hổi đêm khuya',
                    'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Mở cửa 24/7'
                ]
            ],
            'banh-trang-tron' => [
                [
                    'name' => 'Bánh Tráng Trộn Chú Viên',
                    'address' => '38 Nguyễn Thượng Hiền, Phường 5, Quận 3, TP.HCM',
                    'rating' => 4.9,
                    'price' => '25.000đ - 35.000đ',
                    'desc' => 'Phố bánh tráng trộn nổi tiếng nhất Sài Gòn, sốt bò sa tế gia truyền đậm đà thơm nức',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image22.jpg',
                    'tag' => 'Phố Nguyễn Thượng Hiền'
                ],
                [
                    'name' => 'Bánh Tráng Trộn Cô Long',
                    'address' => '34 Nguyễn Thượng Hiền, Phường 5, Quận 3, TP.HCM',
                    'rating' => 4.8,
                    'price' => '20.000đ - 30.000đ',
                    'desc' => 'Ngập tràn khô bò, trứng cút, xoài xanh bào sợi chua ngọt kích thích vị giác',
                    'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Quán đông khách'
                ],
                [
                    'name' => 'Bánh Tráng Trộn Nhật Quỳnh',
                    'address' => '78/46 Nguyễn Văn Trỗi, Phú Nhuận, TP.HCM',
                    'rating' => 4.7,
                    'price' => '20.000đ - 30.000đ',
                    'desc' => 'Sợi bánh tráng mềm dẻo thấm đẫm sốt me tắc và muối tôm Tây Ninh chính hiệu',
                    'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Ăn vặt sinh viên'
                ]
            ],
            'bot-chien' => [
                [
                    'name' => 'Bột Chiên Đạt Thành',
                    'address' => '277 Võ Văn Tần, Phường 5, Quận 3, TP.HCM',
                    'rating' => 4.9,
                    'price' => '35.000đ - 50.000đ',
                    'desc' => 'Miếng bột chiên giòn rụm bên ngoài mềm dẻo bên trong, trứng gà hành hoa thơm phức',
                    'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Quán ruột Sài Gòn'
                ],
                [
                    'name' => 'Bột Chiên Vạn Thành',
                    'address' => 'Hẻm 185 Võ Văn Tần, Phường 5, Quận 3, TP.HCM',
                    'rating' => 4.8,
                    'price' => '30.000đ - 45.000đ',
                    'desc' => 'Ăn kèm đu đủ bào ngâm giấm đường chua ngọt và nước tương đen pha bí truyền',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nước tương bí truyền'
                ],
                [
                    'name' => 'Bột Chiên Hải Ký Chợ Lớn',
                    'address' => '357 Hải Thượng Lãn Ông, Phường 10, Quận 5, TP.HCM',
                    'rating' => 4.8,
                    'price' => '35.000đ - 55.000đ',
                    'desc' => 'Phong cách Chợ Lớn người Hoa chiên chảo gang lớn vàng giòn rôm rốp',
                    'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Phong cách Chợ Lớn'
                ]
            ],
            'sui-cao-nguoi-hoa' => [
                [
                    'name' => 'Sủi Cảo Thiên Thiên (Phố Hà Tôn Quyền)',
                    'address' => '197 Hà Tôn Quyền, Phường 4, Quận 11, TP.HCM',
                    'rating' => 4.9,
                    'price' => '55.000đ - 85.000đ',
                    'desc' => 'Con phố sủi cảo nổi tiếng nhất Sài Gòn, nhân tôm tươi nguyên con ngọt lịm',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image24.png',
                    'tag' => 'Phố sủi cảo Q.11'
                ],
                [
                    'name' => 'Sủi Cảo Ngọc Ý',
                    'address' => '187 Hà Tôn Quyền, Phường 4, Quận 11, TP.HCM',
                    'rating' => 4.8,
                    'price' => '50.000đ - 80.000đ',
                    'desc' => 'Sủi cảo chiên giòn chấm tương đỏ và sủi cảo thập cẩm mực ngâm tro ngọt thanh',
                    'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nước lèo mực tro'
                ],
                [
                    'name' => 'Sủi Cảo Đại Ký Chợ Lớn',
                    'address' => '193 Hà Tôn Quyền, Phường 4, Quận 11, TP.HCM',
                    'rating' => 4.8,
                    'price' => '50.000đ - 75.000đ',
                    'desc' => 'Vỏ bánh mỏng dai mịn màng, tôm thịt đậm đà hương dầu mè truyền thống Hoa kiều',
                    'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Hương vị gốc Hoa'
                ]
            ],
            'goi-cuon' => [
                [
                    'name' => 'Gỏi Cuốn Tôm Nhĩ Lê Văn Sỹ',
                    'address' => '359/1/41 Lê Văn Sỹ, Phường 13, Quận 3, TP.HCM',
                    'rating' => 4.9,
                    'price' => '10.000đ - 15.000đ/cuốn',
                    'desc' => 'Tôm sú to đỏ au, thịt ba rọi luộc cuộn chặt tay chấm tương đen bơ đậu phộng béo bùi',
                    'image' => 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Gỏi cuốn nức tiếng'
                ],
                [
                    'name' => 'Gỏi Cuốn Cô Huệ Chợ Lớn',
                    'address' => '57 Châu Văn Liêm, Phường 14, Quận 5, TP.HCM',
                    'rating' => 4.8,
                    'price' => '10.000đ - 12.000đ/cuốn',
                    'desc' => 'Bánh tráng dẻo thơm rau sống tươi non, nước chấm tương xay sánh sệt chuẩn bài',
                    'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Tương đậu bùi béo'
                ],
                [
                    'name' => 'Gỏi Cuốn Cửa Tây Chợ Bến Thành',
                    'address' => 'Cửa Tây Chợ Bến Thành, Lê Thánh Tôn, Quận 1, TP.HCM',
                    'rating' => 4.7,
                    'price' => '12.000đ - 18.000đ/cuốn',
                    'desc' => 'Món ăn thanh mát nổi tiếng hấp dẫn du khách trong và ngoài nước',
                    'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chợ Bến Thành'
                ]
            ],

            // ==========================================
            // 3. ĐỒNG NAI (8 MÓN)
            // ==========================================
            'goi-buoi-tan-trieu' => [
                [
                    'name' => 'Làng Bưởi Tân Triều Năm Huệ',
                    'address' => 'Xã Tân Bình, Huyện Vĩnh Cửu, Đồng Nai',
                    'rating' => 4.9,
                    'price' => '80.000đ - 150.000đ',
                    'desc' => 'Cái nôi gỏi bưởi Tân Triều, tép bưởi đường lá cam mọng nước trộn tôm thịt đậm đà',
                    'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Cái nôi Bưởi Tân Triều'
                ],
                [
                    'name' => 'Quán Gỏi Bưởi Sông Quê Tân Triều',
                    'address' => 'Hương Lộ 9, Xã Tân Triều, Vĩnh Cửu, Đồng Nai',
                    'rating' => 4.8,
                    'price' => '75.000đ - 120.000đ',
                    'desc' => 'Không gian sân vườn bờ sông Đồng Nai thoáng mát, ăn kèm bánh phồng tôm giòn rụm',
                    'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Ẩm thực bờ sông'
                ],
                [
                    'name' => 'Nhà Hàng Cù Lao Ba Huê',
                    'address' => 'Xã Bình Lợi, Huyện Vĩnh Cửu, Đồng Nai',
                    'rating' => 4.8,
                    'price' => '85.000đ - 130.000đ',
                    'desc' => 'Đặc sản gỏi bưởi tôm sông và rượu bưởi lên men tự nhiên nức tiếng Đồng Nai',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đặc sản Vĩnh Cửu'
                ]
            ],
            'ga-hap-buoi' => [
                [
                    'name' => 'Khu Du Lịch Làng Bưởi Tân Triều',
                    'address' => 'Ấp Vĩnh Hiệp, Xã Tân Bình, Vĩnh Cửu, Đồng Nai',
                    'rating' => 4.9,
                    'price' => '250.000đ - 350.000đ/con',
                    'desc' => 'Gà ta thả vườn đặt trọn trong quả bưởi hấp cách thủy ngát hương tinh dầu bưởi',
                    'image' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Tuyệt phẩm Đồng Nai'
                ],
                [
                    'name' => 'Quán Vườn Bưởi Năm Huệ',
                    'address' => 'Xã Tân Bình, Vĩnh Cửu, Đồng Nai',
                    'rating' => 4.8,
                    'price' => '260.000đ - 320.000đ/con',
                    'desc' => 'Thịt gà ngọt mềm quyện vị the mát dịu của lá bưởi và rượu bưởi',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Quán ruột du khách'
                ],
                [
                    'name' => 'Ẩm Thực Sinh Thái Bờ Sông Đồng Nai',
                    'address' => 'Đường Cách Mạng Tháng 8, P. Quang Vinh, Biên Hòa, Đồng Nai',
                    'rating' => 4.7,
                    'price' => '240.000đ - 300.000đ/con',
                    'desc' => 'Gà hấp bưởi nguyên trái nóng hổi chấm muối ớt hột rang cay the mặn mà',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'TP. Biên Hòa'
                ]
            ],
            'lau-la-kho-qua-rung' => [
                [
                    'name' => 'Lẩu Khổ Qua Rừng Năm Thơ Long Khánh',
                    'address' => 'Đường Hùng Vương, TX. Long Khánh, Đồng Nai',
                    'rating' => 4.8,
                    'price' => '150.000đ - 220.000đ',
                    'desc' => 'Đọt và lá khổ qua rừng hái tự nhiên, nhúng chả cá thác lác dai giòn thanh nhiệt',
                    'image' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đặc sản Long Khánh'
                ],
                [
                    'name' => 'Quán Lẩu Khổ Qua Rừng Biên Hòa',
                    'address' => '45 Võ Thị Sáu, P. Thống Nhất, TP. Biên Hòa, Đồng Nai',
                    'rating' => 4.8,
                    'price' => '160.000đ - 230.000đ',
                    'desc' => 'Nước lẩu hầm sườn heo ngọt trong, vị đắng thanh dịu mát rất tốt cho sức khỏe',
                    'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Thanh nhiệt giải độc'
                ],
                [
                    'name' => 'Quán Đồng Quê Long Khánh',
                    'address' => 'Quốc lộ 1A, Phường Xuân Bình, Long Khánh, Đồng Nai',
                    'rating' => 4.7,
                    'price' => '140.000đ - 200.000đ',
                    'desc' => 'Các món rau rừng Long Khánh tươi ngon kết hợp nước lẩu sườn nấm ngọt dịu',
                    'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Hương vị rau rừng'
                ]
            ],
            'com-ga-ca-man' => [
                [
                    'name' => 'Cơm Gà Cá Mặn Cây Chàm Biên Hòa',
                    'address' => '22 Phan Trung, P. Tân Mai, TP. Biên Hòa, Đồng Nai',
                    'rating' => 4.8,
                    'price' => '55.000đ - 85.000đ',
                    'desc' => 'Món cơm thố cá mặn Biên Hòa nổi tiếng, hạt cơm săn giòn đậm đà khô cá mặn',
                    'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đặc sản Biên Hòa'
                ],
                [
                    'name' => 'Nhà Hàng Cơm Niêu Đồng Nai',
                    'address' => '188 Hà Huy Giáp, P. Quyết Thắng, TP. Biên Hòa, Đồng Nai',
                    'rating' => 4.8,
                    'price' => '65.000đ - 95.000đ',
                    'desc' => 'Cơm cháy đáy niêu giòn rụm đảo cùng thịt ức gà xé và khô cá thu mặn mà béo bùi',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Cơm niêu giòn rụm'
                ],
                [
                    'name' => 'Quán Cơm Thố Biên Hòa',
                    'address' => 'Đường Đồng Khởi, P. Tam Hiệp, TP. Biên Hòa, Đồng Nai',
                    'rating' => 4.7,
                    'price' => '50.000đ - 75.000đ',
                    'desc' => 'Hương vị gia truyền phục vụ thố đất nóng hổi giữ trọn độ thơm giòn',
                    'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Gia truyền thố đất'
                ]
            ],

            // ==========================================
            // 4. TP. CẦN THƠ (8 MÓN)
            // ==========================================
            'banh-cong-can-tho' => [
                [
                    'name' => 'Bánh Cống Cô Út Cần Thơ',
                    'address' => '86/38 Lý Tự Trọng, Phường An Cư, Ninh Kiều, Cần Thơ',
                    'rating' => 4.9,
                    'price' => '15.000đ - 35.000đ',
                    'desc' => 'Quán bánh cống lâu đời nổi tiếng nhất xứ Tây Đô, vỏ giòn tan nhân đậu xanh tôm thịt béo ngậy',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image32.jpg',
                    'tag' => 'Lâu đời nhất Cần Thơ'
                ],
                [
                    'name' => 'Bánh Cống Huê Viên',
                    'address' => '32 Đề Thám, Phường An Cư, Ninh Kiều, Cần Thơ',
                    'rating' => 4.8,
                    'price' => '15.000đ - 30.000đ',
                    'desc' => 'Bánh chiên ráo dầu vàng ruộm, cuốn xà lách cải xanh chấm nước mắm chua ngọt đồ chua',
                    'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Phố ẩm thực Đề Thám'
                ],
                [
                    'name' => 'Bánh Cống 292 Đường 30/4',
                    'address' => '292 Đường 30/4, Xuân Khánh, Ninh Kiều, Cần Thơ',
                    'rating' => 4.7,
                    'price' => '15.000đ - 30.000đ',
                    'desc' => 'Con tôm sông cong mình đỏ au trên đỉnh bánh giòn rụm, điểm hẹn xế chiều quen thuộc',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Quán ruột Tây Đô'
                ]
            ],
            'lau-vit-nau-chao' => [
                [
                    'name' => 'Vịt Nấu Chao Thành Giao (Hẻm 1 Lý Tự Trọng)',
                    'address' => 'Hẻm 1 Lý Tự Trọng, An Phú, Ninh Kiều, Cần Thơ',
                    'rating' => 4.9,
                    'price' => '180.000đ - 260.000đ',
                    'desc' => 'Quán vịt nấu chao trứ danh đón tiếp nhiều nghệ sĩ, nước dùng chao đỏ thơm lừng khoai môn dẻo bùi',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nổi tiếng hẻm 1'
                ],
                [
                    'name' => 'Vịt Nấu Chao Kim Liên',
                    'address' => 'Hẻm 1 Lý Tự Trọng, An Phú, Ninh Kiều, Cần Thơ',
                    'rating' => 4.8,
                    'price' => '170.000đ - 240.000đ',
                    'desc' => 'Thịt vịt xiêm ít mỡ chắc thịt, chén chao pha tỏi ớt đường tắc thơm ngậy chấm thịt vịt siêu ngon',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Vịt xiêm cỏ đậm đà'
                ],
                [
                    'name' => 'Vịt Nấu Chao Cẩm Tú',
                    'address' => 'Hẻm 1 Lý Tự Trọng, Ninh Kiều, Cần Thơ',
                    'rating' => 4.7,
                    'price' => '160.000đ - 230.000đ',
                    'desc' => 'Nước dừa tươi ninh ngọt đậm đà, nhúng ngập rau muống đồng tươi non ăn kèm bún sợi nhỏ',
                    'image' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Giá bình dân'
                ]
            ],
            'banh-tet-la-cam' => [
                [
                    'name' => 'Bánh Tét Lá Cẩm Huỳnh Thảo',
                    'address' => '176H Nguyễn Thị Minh Khai, Ninh Kiều, Cần Thơ',
                    'rating' => 4.9,
                    'price' => '70.000đ - 110.000đ/đòn',
                    'desc' => 'Đặc sản nức tiếng Tây Đô, nếp tím cẩm béo ngậy nước cốt dừa với nhân thịt trứng muối đỏ au',
                    'image' => 'https://images.unsplash.com/photo-1541592102781-c715053e1be0?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Thương hiệu gia truyền'
                ],
                [
                    'name' => 'Bánh Tét Chín Cẩm',
                    'address' => '14 Mậu Thân, An Phú, Ninh Kiều, Cần Thơ',
                    'rating' => 4.9,
                    'price' => '75.000đ - 120.000đ/đòn',
                    'desc' => 'Nghệ nhân khai sinh bánh tét lá cẩm Cần Thơ, đòn bánh gói lá chuối chắc nịch làm quà số 1',
                    'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nghệ nhân Chín Cẩm'
                ],
                [
                    'name' => 'Bánh Tét Tư Đẹp Cần Thơ',
                    'address' => '102 Hai Bà Trưng, Tân An, Ninh Kiều, Cần Thơ',
                    'rating' => 4.8,
                    'price' => '70.000đ - 100.000đ/đòn',
                    'desc' => 'Từng khoanh bánh dẻo mịn thơm lừng mùi nếp cẩm, bùi béo vị đậu xanh thịt mỡ trứng muối',
                    'image' => 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Bến Ninh Kiều'
                ]
            ],
            'nem-nuong-cai-rang' => [
                [
                    'name' => 'Nem Nướng Thanh Vân Cái Răng',
                    'address' => '17 Đại lộ Hòa Bình, Tân An, Ninh Kiều, Cần Thơ',
                    'rating' => 4.9,
                    'price' => '55.000đ - 85.000đ',
                    'desc' => 'Nem nướng quết dẻo than hồng tươm mỡ, chén tương nếp xay sánh béo rắc đậu phộng rang',
                    'image' => 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Thương hiệu Thanh Vân'
                ],
                [
                    'name' => 'Nem Nướng Anh Ba Cái Răng',
                    'address' => 'QL1A, Phường Lê Bình, Cái Răng, Cần Thơ',
                    'rating' => 4.8,
                    'price' => '50.000đ - 75.000đ',
                    'desc' => 'Cái nôi nem nướng Cái Răng, cuộn bánh hỏi mỡ hành và rau thơm miệt vườn',
                    'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Gốc Cái Răng'
                ],
                [
                    'name' => 'Nem Nướng Hai Hoàng Cần Thơ',
                    'address' => '248 Tầm Vu, Hưng Lợi, Ninh Kiều, Cần Thơ',
                    'rating' => 4.7,
                    'price' => '45.000đ - 70.000đ',
                    'desc' => 'Quán ven sông Hậu gió mát, nem thơm ngậy vàng ruộm ăn cùng bánh tráng nem',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Bờ kè sông Hậu'
                ]
            ],

            // ==========================================
            // 5. AN GIANG (8 MÓN)
            // ==========================================
            'bun-ca-chau-doc' => [
                [
                    'name' => 'Bún Cá Bé Hai Châu Đốc',
                    'address' => 'Đường Chi Lăng, P. Châu Phú A, TP. Châu Đốc, An Giang',
                    'rating' => 4.9,
                    'price' => '35.000đ - 50.000đ',
                    'desc' => 'Tô bún cá lóc xào nghệ vàng óng ả, nước lèo ngải bún thơm nức mũi ăn cùng bông điên điển',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image2.jpg',
                    'tag' => 'Nổi tiếng nhất Châu Đốc'
                ],
                [
                    'name' => 'Bún Cá Cô Lệ Chợ Châu Đốc',
                    'address' => 'Cổng Chợ Châu Đốc, Đường Bạch Đằng, TP. Châu Đốc, An Giang',
                    'rating' => 4.8,
                    'price' => '30.000đ - 45.000đ',
                    'desc' => 'Quán bún cá truyền thống trong lòng chợ thủ phủ mắm, nước dùng thanh ngọt đậm đà',
                    'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chợ Châu Đốc'
                ],
                [
                    'name' => 'Bún Cá Dũng Châu Đốc',
                    'address' => 'Đường Lê Lợi, TP. Châu Đốc, An Giang',
                    'rating' => 4.8,
                    'price' => '35.000đ - 50.000đ',
                    'desc' => 'Thịt cá lóc đồng phi lê dai ngọt, chấm nước mắm me ớt sền sệt đánh thức vị giác',
                    'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Cá lóc đồng tươi'
                ]
            ],
            'ga-dot-o-thum' => [
                [
                    'name' => 'Quán Gà Đốt Ô Thum Siêu Gà',
                    'address' => 'Hồ Ô Thum, Xã Ô Lâm, Tri Tôn, An Giang',
                    'rating' => 5.0,
                    'price' => '240.000đ - 300.000đ/con',
                    'desc' => 'Nơi khai sinh món gà đốt lá chúc bên hồ Ô Thum, da gà giòn rụm ngào ngạt tinh dầu lá chúc',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image12.jpg',
                    'tag' => 'Gốc hồ Ô Thum'
                ],
                [
                    'name' => 'Quán Gà Đốt Thảo Nguyên Ô Thum',
                    'address' => 'Bờ hồ Ô Thum, Huyện Tri Tôn, An Giang',
                    'rating' => 4.9,
                    'price' => '230.000đ - 280.000đ/con',
                    'desc' => 'Gà đồi thả vườn đốt trong nồi gang đáy muối sả, chấm muối ớt lá chúc đâm nhuyễn cay xé',
                    'image' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Bờ hồ thoáng mát'
                ],
                [
                    'name' => 'Quán Gà Đốt Rừng Tri Tôn',
                    'address' => 'Tỉnh lộ 948, Huyện Tri Tôn, An Giang',
                    'rating' => 4.8,
                    'price' => '220.000đ - 280.000đ/con',
                    'desc' => 'Điểm dừng chân nức tiếng của các phượt thủ khi du lịch vùng Bảy Núi An Giang',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Phượt thủ yêu thích'
                ]
            ],
            'com-tam-long-xuyen' => [
                [
                    'name' => 'Cơm Tấm Cây Điệp Long Xuyên',
                    'address' => '67 Lý Tự Trọng, Phường Mỹ Long, TP. Long Xuyên, An Giang',
                    'rating' => 4.9,
                    'price' => '35.000đ - 55.000đ',
                    'desc' => 'Thương hiệu cơm tấm nhuyễn thái sợi thịt khìa số 1 Long Xuyên nức danh Nam Bộ',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image18.jpg',
                    'tag' => 'Huyền thoại Long Xuyên'
                ],
                [
                    'name' => 'Cơm Tấm Hướng Dương',
                    'address' => '136/4 Phan Đình Phùng, TP. Long Xuyên, An Giang',
                    'rating' => 4.8,
                    'price' => '30.000đ - 50.000đ',
                    'desc' => 'Thịt khìa nước dừa thái chỉ mềm ngọt, trứng kho thái sợi và nước mắm kẹo độc quyền',
                    'image' => 'https://images.unsplash.com/photo-1626804475297-41609ea004eb?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Cơm tấm nhuyễn'
                ],
                [
                    'name' => 'Cơm Tấm Tùng Long Xuyên',
                    'address' => '200 Phan Bội Châu, Bình Khánh, TP. Long Xuyên, An Giang',
                    'rating' => 4.8,
                    'price' => '30.000đ - 45.000đ',
                    'desc' => 'Đĩa cơm tấm tơi xốp phủ ngập thịt sợi, chan muỗng mỡ hành óng ánh thơm ngậy',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chuẩn vị truyền thống'
                ]
            ],

            // ==========================================
            // 6. ĐỒNG THÁP (8 MÓN)
            // ==========================================
            'hu-tieu-sa-dec' => [
                [
                    'name' => 'Hủ Tiếu Sa Đéc Bà Sẩm',
                    'address' => '06 Trần Hưng Đạo, Phường 1, TP. Sa Đéc, Đồng Tháp',
                    'rating' => 4.9,
                    'price' => '20.000đ - 35.000đ',
                    'desc' => 'Hủ tiếu hơn 50 năm giá bình dân nức tiếng làng bột Sa Đéc, sợi hủ tiếu dai ngọt tự nhiên',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image8.jpg',
                    'tag' => 'Hơn 50 năm tuổi'
                ],
                [
                    'name' => 'Hủ Tiếu Phú Thành Sa Đéc',
                    'address' => '75 Trần Hưng Đạo, Phường 1, TP. Sa Đéc, Đồng Tháp',
                    'rating' => 4.8,
                    'price' => '35.000đ - 55.000đ',
                    'desc' => 'Hủ tiếu khô trộn nước sốt tương bí truyền Sa Đéc, tôm tươi thịt nạc gan heo ngập tràn',
                    'image' => 'https://images.unsplash.com/photo-1594922119156-f00e572ac065?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Hủ tiếu khô sốt bí truyền'
                ],
                [
                    'name' => 'Hủ Tiếu Chị Dậu Sa Đéc',
                    'address' => '292 Nguyễn Sinh Sắc, Phường 2, TP. Sa Đéc, Đồng Tháp',
                    'rating' => 4.8,
                    'price' => '30.000đ - 45.000đ',
                    'desc' => 'Nước lèo hầm xương ống ngọt thanh trong vắt, phục vụ cần tàu hẹ lá tươi non',
                    'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nước lèo ngọt trong'
                ]
            ],
            'nem-lai-vung' => [
                [
                    'name' => 'Cơ Sở Nem Lai Vung Giáo Thơ',
                    'address' => 'Quốc lộ 80, Thị trấn Lai Vung, Huyện Lai Vung, Đồng Tháp',
                    'rating' => 4.9,
                    'price' => '35.000đ - 50.000đ/chục',
                    'desc' => 'Thương hiệu nem Lai Vung trứ danh Nam Bộ, nem lên men tự nhiên đỏ hồng thơm lừng',
                    'image' => 'https://images.unsplash.com/photo-1628198751532-35a0ce5e9334?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đặc sản Giáo Thơ'
                ],
                [
                    'name' => 'Nem Lai Vung Út Thẳng',
                    'address' => 'QL80, Xã Tân Thành, Huyện Lai Vung, Đồng Tháp',
                    'rating' => 4.8,
                    'price' => '35.000đ - 50.000đ/chục',
                    'desc' => 'Bọc lá chùm ruột truyền thống, vị chua ngọt cay the chuẩn mực xứ nem Lai Vung',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Gia truyền Lai Vung'
                ],
                [
                    'name' => 'Nem Chua Chiến Ngoan',
                    'address' => 'Tân Khánh Trung, Lấp Vò, Đồng Tháp',
                    'rating' => 4.8,
                    'price' => '30.000đ - 45.000đ/chục',
                    'desc' => 'Thịt nạc quết dẻo bì heo giòn sần sật, hạt tiêu sọ cay nồng nàn',
                    'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Được tin dùng'
                ]
            ],
            'lau-ca-linh-bong-dien-dien' => [
                [
                    'name' => 'Quán Ẩm Thực Bờ Kè Sa Đéc',
                    'address' => 'Đường Nguyễn Tất Thành, Phường 1, TP. Sa Đéc, Đồng Tháp',
                    'rating' => 4.9,
                    'price' => '150.000đ - 220.000đ',
                    'desc' => 'Nồi lẩu cá linh non mùa nước nổi me dừa chua ngọt, bông điên điển vàng rực bờ sông',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image38.jpg',
                    'tag' => 'Mùa nước nổi'
                ],
                [
                    'name' => 'Nhà Hàng Sen Đồng Tháp',
                    'address' => '26 Lý Thường Kiệt, Phường 1, TP. Cao Lãnh, Đồng Tháp',
                    'rating' => 4.8,
                    'price' => '160.000đ - 250.000đ',
                    'desc' => 'Không gian sang trọng mang đậm văn hóa Sen, cá linh béo ngậy xương mềm chấm mắm ớt',
                    'image' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'TP. Cao Lãnh'
                ],
                [
                    'name' => 'Quán Lẩu Cá Linh Hồng Ngự',
                    'address' => 'Thị xã Hồng Ngự, Đồng Tháp',
                    'rating' => 4.8,
                    'price' => '140.000đ - 200.000đ',
                    'desc' => 'Vùng đầu nguồn sông Tiền đón con cá linh non đầu tiên trong năm ngọt lịm',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Đầu nguồn Hồng Ngự'
                ]
            ],

            // ==========================================
            // 7. VĨNH LONG (8 MÓN)
            // ==========================================
            'ca-tai-tuong-chien-xu' => [
                [
                    'name' => 'Nhà Hàng Nổi Vĩnh Long',
                    'address' => 'Số 1 Đường 1/5, Phường 1, TP. Vĩnh Long, Vĩnh Long',
                    'rating' => 4.9,
                    'price' => '180.000đ - 280.000đ',
                    'desc' => 'Nhà hàng nổi trên sông Cổ Chiên, cá tai tượng chiên xù dựng vảy giòn rụm cuốn bánh tráng',
                    'image' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Nhà hàng nổi sông Tiền'
                ],
                [
                    'name' => 'Khu Du Lịch Vinh Sang (Cù Lao An Bình)',
                    'address' => 'Tổ 14, Ấp An Thuận, Long Hồ, Vĩnh Long',
                    'rating' => 4.8,
                    'price' => '190.000đ - 260.000đ',
                    'desc' => 'Trải nghiệm tát mương bắt cá và thưởng thức cá tai tượng chiên xù cuốn rau vườn tươi rói',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Cù lao An Bình'
                ],
                [
                    'name' => 'Quán Ẩm Thực Đồng Quê Cổ Chiên',
                    'address' => 'Bờ kè sông Cổ Chiên, Phường 9, TP. Vĩnh Long, Vĩnh Long',
                    'rating' => 4.8,
                    'price' => '170.000đ - 240.000đ',
                    'desc' => 'Thịt cá trắng ngần chấm mắm me chua ngọt đậm đà phong vị miệt vườn sông nước',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Bờ kè Cổ Chiên'
                ]
            ],
            'tau-hu-ky-my-hoa' => [
                [
                    'name' => 'Làng Nghề Tàu Hủ Ky Mỹ Hòa Ba Ngân',
                    'address' => 'Ấp Mỹ Khánh, Xã Mỹ Hòa, TX. Bình Minh, Vĩnh Long',
                    'rating' => 4.9,
                    'price' => '40.000đ - 70.000đ/gói',
                    'desc' => 'Làng nghề trăm năm tuổi Mỹ Hòa, váng đậu nành nguyên chất thơm bùi béo ngậy',
                    'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Làng nghề trăm năm'
                ],
                [
                    'name' => 'Cơ Sở Tàu Hủ Ky Đạt Chuẩn Bình Minh',
                    'address' => 'Ven sông Bình Minh, Xã Mỹ Hòa, Bình Minh, Vĩnh Long',
                    'rating' => 4.8,
                    'price' => '40.000đ - 65.000đ/gói',
                    'desc' => 'Tàu hủ ky khô, tàu hủ ky tươi chiên giòn rụm nấu lẩu nấm lẩu cá trứ danh',
                    'image' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'TX. Bình Minh'
                ],
                [
                    'name' => 'Đại Lý Tàu Hủ Ky Chợ Vĩnh Long',
                    'address' => 'Đường 3/2, Phường 1, TP. Vĩnh Long, Vĩnh Long',
                    'rating' => 4.8,
                    'price' => '35.000đ - 60.000đ/gói',
                    'desc' => 'Điểm bán đặc sản uy tín trung tâm thành phố Vĩnh Long',
                    'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Chợ Vĩnh Long'
                ]
            ],

            // ==========================================
            // 8. CÀ MAU (8 MÓN)
            // ==========================================
            'cua-ca-mau' => [
                [
                    'name' => 'Nhà Hàng Cua Cà Mau Quạt Mo',
                    'address' => '14-16 Bùi Thị Trường, Phường 5, TP. Cà Mau, Cà Mau',
                    'rating' => 4.9,
                    'price' => '250.000đ - 450.000đ',
                    'desc' => 'Địa chỉ ẩm thực cua biển số 1 Cà Mau, cua gạch son béo ngậy, cua thịt chắc nịch ngọt lịm',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Cua số 1 Cà Mau'
                ],
                [
                    'name' => 'Quán Cua Thứ Thiệt Cà Mau',
                    'address' => '57 Ngô Gia Tự, Khóm 5, Phường 5, TP. Cà Mau, Cà Mau',
                    'rating' => 4.9,
                    'price' => '220.000đ - 400.000đ',
                    'desc' => 'Cua biển tươi sống bắt tại đầm Năm Căn, hấp bia sả và rang me muối tiêu cay nồng',
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Cua Năm Căn tươi sống'
                ],
                [
                    'name' => 'Quán Cua Biển Năm Căn Sinh Thái',
                    'address' => 'Khóm 1, TT. Năm Căn, Huyện Năm Căn, Cà Mau',
                    'rating' => 4.9,
                    'price' => '200.000đ - 380.000đ',
                    'desc' => 'Thủ phủ cua Năm Căn, càng cua to khổng lồ chắc thịt ăn cùng muối ớt tiêu chanh',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Thủ phủ Năm Căn'
                ]
            ],
            'ba-khia-rach-goc' => [
                [
                    'name' => 'Cơ Sở Ba Khía Muối Rạch Gốc Tư Đượm',
                    'address' => 'Thị trấn Rạch Gốc, Huyện Ngọc Hiển, Cà Mau',
                    'rating' => 4.9,
                    'price' => '65.000đ - 100.000đ/hũ',
                    'desc' => 'Cái nôi ba khía Rạch Gốc ăn trái mắm đen rừng đước, thịt chắc ngọt trộn tỏi ớt đường chanh',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image6.jpg',
                    'tag' => 'Cái nôi Rạch Gốc'
                ],
                [
                    'name' => 'Quán Ăn Dân Dã Ba Khía Đất Mũi',
                    'address' => 'Ấp Mũi, Xã Đất Mũi, Huyện Ngọc Hiển, Cà Mau',
                    'rating' => 4.8,
                    'price' => '50.000đ - 90.000đ',
                    'desc' => 'Điểm cực Nam tổ quốc thưởng thức đĩa ba khía muối chua cay mặn ngọt bên bờ rừng ngập mặn',
                    'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Mũi Cà Mau'
                ],
                [
                    'name' => 'Quán Ba Khía Cà Mau Hào Hiệp',
                    'address' => 'Đường Lý Thường Kiệt, Phường 6, TP. Cà Mau, Cà Mau',
                    'rating' => 4.8,
                    'price' => '55.000đ - 95.000đ',
                    'desc' => 'Hương vị ba khía xé cay the mặn mà đưa cơm nóng tuyệt đỉnh những ngày mưa',
                    'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'TP. Cà Mau'
                ]
            ],
            'lau-mam-u-minh' => [
                [
                    'name' => 'Nhà Hàng Sinh Thái Hương Tràm U Minh',
                    'address' => 'Tuyến Đan Khánh An, Xã Khánh An, Huyện U Minh, Cà Mau',
                    'rating' => 4.9,
                    'price' => '220.000đ - 320.000đ',
                    'desc' => 'Nồi lẩu mắm cá sặc rừng tràm thơm nức, nhúng ngập đọt choại và rau đồng U Minh Hạ',
                    'image' => 'https://statics.vincom.com.vn/xu-huong/0-0-0-mon-ngon-mien-nam/image15.png',
                    'tag' => 'Rừng tràm U Minh'
                ],
                [
                    'name' => 'Quán Lẩu Mắm Đồng Quê Cà Mau',
                    'address' => '289 Nguyễn Trãi, Phường 9, TP. Cà Mau, Cà Mau',
                    'rating' => 4.8,
                    'price' => '200.000đ - 290.000đ',
                    'desc' => 'Topping cá lóc đồng, tôm bạc, mực tươi và đĩa rau sông nước Cà Mau hơn 20 loại',
                    'image' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Rau đồng phong phú'
                ],
                [
                    'name' => 'Ẩm Thực Rừng U Minh Hạ',
                    'address' => 'Ấp 1, Xã Trần Hợi, Huyện Trần Văn Thời, Cà Mau',
                    'rating' => 4.8,
                    'price' => '190.000đ - 280.000đ',
                    'desc' => 'Không gian giữa tán rừng tràm bát ngát, nước dùng mắm dừa ngọt béo đậm đà',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Sinh thái U Minh Hạ'
                ]
            ],
            'tom-kho-ca-mau' => [
                [
                    'name' => 'Cơ Sở Tôm Khô Rạch Gốc Bảy Đến',
                    'address' => 'Thị trấn Rạch Gốc, Huyện Ngọc Hiển, Cà Mau',
                    'rating' => 5.0,
                    'price' => '120.000đ - 200.000đ/hũ',
                    'desc' => 'Tôm đất sinh thái rừng ngập mặn luộc phơi nắng tự nhiên, thịt dẻo quánh ngọt đậm đà',
                    'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Tôm đất sinh thái'
                ],
                [
                    'name' => 'Hợp Tác Xã Tôm Khô Năm Căn',
                    'address' => 'Quốc lộ 1A, TT. Năm Căn, Huyện Năm Căn, Cà Mau',
                    'rating' => 4.9,
                    'price' => '130.000đ - 220.000đ/hũ',
                    'desc' => 'Tôm khô đỏ au không phẩm màu, đặc sản quà biếu số một của vùng đất Mũi',
                    'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'Quà biếu thượng hạng'
                ],
                [
                    'name' => 'Đặc Sản Tôm Khô Cà Mau Ánh Nguyệt',
                    'address' => 'Đường Bùi Thị Trường, Phường 5, TP. Cà Mau, Cà Mau',
                    'rating' => 4.9,
                    'price' => '120.000đ - 190.000đ/hũ',
                    'desc' => 'Ăn kèm củ kiệu chua ngọt và trứng bắc thảo thơm ngon đúng điệu',
                    'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&q=80&w=400',
                    'tag' => 'TP. Cà Mau'
                ]
            ]
        ];
    }

    /**
     * Tự động sinh ra 3 địa chỉ quán ăn chuẩn vị tại đúng tỉnh thành của món ăn
     */
    private static function generateFallbackRestaurants($tenMon, $tinhThanh, $tenVung) {
        $tinh = !empty($tinhThanh) ? $tinhThanh : (!empty($tenVung) ? 'Miền ' . $tenVung : 'Đồng bằng');
        
        return [
            [
                'name' => 'Quán ' . $tenMon . ' Chuẩn Vị ' . $tinh,
                'address' => 'Khu phố trung tâm, ' . $tinh,
                'rating' => 4.9,
                'price' => '35.000đ - 85.000đ',
                'desc' => 'Địa chỉ uy tín lâu năm chuyên phục vụ ' . $tenMon . ' hương vị gia truyền tại ' . $tinh,
                'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=400',
                'tag' => 'Chuẩn vị ' . $tinh
            ],
            [
                'name' => 'Đặc Sản ' . $tenMon . ' ' . $tinh . ' Gia Truyền',
                'address' => 'Đường ẩm thực chính, ' . $tinh,
                'rating' => 4.8,
                'price' => '40.000đ - 90.000đ',
                'desc' => 'Nguyên liệu tươi ngon bản địa, được đông đảo người dân địa phương và du khách đánh giá cao',
                'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=400',
                'tag' => 'Gia truyền nức tiếng'
            ],
            [
                'name' => 'Ẩm Thực Miệt Vườn ' . $tinh . ' - ' . $tenMon,
                'address' => 'Tuyến du lịch sinh thái, ' . $tinh,
                'rating' => 4.8,
                'price' => '35.000đ - 75.000đ',
                'desc' => 'Không gian thoáng mát đậm chất miệt vườn, phục vụ chu đáo món ' . $tenMon,
                'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=400',
                'tag' => 'Miệt vườn sinh thái'
            ]
        ];
    }
}

/**
 * BookingModel quản lý đặt bàn tại nhà hàng
 * Được gộp chung trong file RestaurantModel do liên quan trực tiếp đến nhà hàng
 */
class BookingModel extends Model {
    protected $table = "bookings";

    public function createBooking($name, $phone, $booking_date, $guests) {
        $query = "INSERT INTO " . $this->table . " (name, phone, booking_date, guests) VALUES (:name, :phone, :booking_date, :guests)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':booking_date', $booking_date);
        $stmt->bindParam(':guests', $guests);

        return $stmt->execute();
    }
}
?>
