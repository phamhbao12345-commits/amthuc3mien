<?php
require_once __DIR__ . '/app/models/Database.php';

$db = new Database();
$conn = $db->getConnection();

$data = [
    [
        'name' => 'Việt Phố - 45,47,49 Lê Quý Đôn',
        'address' => 'Số 45-47-49 Lê Quý Đôn, Phường Võ Thị Sáu, Quận 3',
        'district' => 'Quận 3',
        'image' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&q=80&w=600',
        'rating' => 5.0,
        'promo' => 'Giảm 15%',
        'categories' => 'Hải sản tươi sống, Gọi món Á-Âu'
    ],
    [
        'name' => 'Phố 79 - 120 Sương Nguyệt Ánh',
        'address' => 'Số 120-120 Bis Sương Nguyệt Ánh, Phường Bến Thành, Quận 1',
        'district' => 'Quận 1',
        'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&q=80&w=600',
        'rating' => 4.5,
        'promo' => 'Đặt bàn giữ chỗ',
        'categories' => 'Gọi món Việt'
    ],
    [
        'name' => 'Hoàng Yến Cuisine - Ngô Đức Kế',
        'address' => '07-09 Ngô Đức Kế, Phường Bến Nghé, Quận 1',
        'district' => 'Quận 1',
        'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&q=80&w=600',
        'rating' => 4.8,
        'promo' => 'Đặt bàn giữ chỗ',
        'categories' => 'Gọi món Việt'
    ],
    [
        'name' => 'Chang - Cao Thắng',
        'address' => 'Số 06 Cao Thắng, Phường 5, Quận 3',
        'district' => 'Quận 3',
        'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=600',
        'rating' => 4.2,
        'promo' => 'Giảm tới 20%',
        'categories' => 'Gọi món Thái Lan'
    ],
    [
        'name' => 'Chang - Võ Văn Ngân',
        'address' => 'Võ Văn Ngân, TP. Thủ Đức',
        'district' => 'Thủ Đức',
        'image' => 'https://images.unsplash.com/photo-1548943487-a2e4e43b4852?auto=format&fit=crop&q=80&w=600',
        'rating' => 4.6,
        'promo' => 'Giảm 10%',
        'categories' => 'Gọi món Thái Lan'
    ],
    [
        'name' => 'King BBQ Buffet - Lê Văn Sỹ',
        'address' => 'Lê Văn Sỹ, Quận Tân Bình',
        'district' => 'Tân Bình',
        'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=600',
        'rating' => 4.4,
        'promo' => 'Buffet 199k',
        'categories' => 'Lẩu Nướng Hàn Quốc'
    ],
    [
        'name' => 'Moo Beef Steak - Ngô Đức Kế',
        'address' => 'Ngô Đức Kế, Phường Bến Nghé, Quận 1',
        'district' => 'Quận 1',
        'image' => 'https://images.unsplash.com/photo-1600891964092-4316c288032e?auto=format&fit=crop&q=80&w=600',
        'rating' => 4.9,
        'promo' => 'Tặng vang Ý',
        'categories' => 'Món Âu'
    ],
    [
        'name' => 'Chang - Giga Mall Phạm Văn Đồng',
        'address' => 'Giga Mall, Phạm Văn Đồng, Thủ Đức',
        'district' => 'Thủ Đức',
        'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&q=80&w=600',
        'rating' => 4.3,
        'promo' => 'Đặt bàn giữ chỗ',
        'categories' => 'Gọi món Thái Lan'
    ]
];

// Check if promo and categories columns exist, if not add them
try {
    $conn->exec("ALTER TABLE restaurants ADD COLUMN promo VARCHAR(255) DEFAULT 'Đặt bàn giữ chỗ'");
    $conn->exec("ALTER TABLE restaurants ADD COLUMN categories VARCHAR(255) DEFAULT 'Gọi món'");
} catch (Exception $e) {
    // Columns might already exist
}

// Clear old data
$conn->exec("TRUNCATE TABLE restaurants");

$stmt = $conn->prepare("INSERT INTO restaurants (name, address, district, image, rating, promo, categories) VALUES (:name, :address, :district, :image, :rating, :promo, :categories)");

foreach ($data as $item) {
    $stmt->execute($item);
}

echo "Seeded database successfully!\n";
?>
