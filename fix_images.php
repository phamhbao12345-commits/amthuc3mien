<?php
require_once __DIR__ . '/app/models/Database.php';

$db = new Database();
$conn = $db->getConnection();

$images = [
    'pho-ha-noi' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&q=80&w=800',
    'bun-bo-hue' => 'https://images.unsplash.com/photo-1594922119156-f00e572ac065?auto=format&fit=crop&q=80&w=800', // Replaced with a working noodle soup image
    'com-tam-sai-gon' => 'https://images.unsplash.com/photo-1626804475297-41609ea004eb?auto=format&fit=crop&q=80&w=800' // Replaced with a working food image
];

foreach ($images as $slug => $url) {
    $stmt = $conn->prepare("UPDATE mon_an SET hinh_anh = :url WHERE slug = :slug");
    $stmt->execute(['url' => $url, 'slug' => $slug]);
}

echo "Updated images in database!\n";
?>
