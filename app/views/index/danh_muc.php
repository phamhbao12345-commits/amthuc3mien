<?php 
/** 
 * @var array $restaurants 
 * @var array $monAn 
 * @var string $title 
 * @var string $type 
 */ 
?>
<div class="bg-white">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
        <!-- Breadcrumb -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 border-b border-gray-100 gap-4">
            <div class="text-[15px] font-medium flex items-center gap-2">
                <span class="text-gray-900 font-bold">Ăn uống</span> 
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i> 
                <span class="text-primary font-bold"><?php echo htmlspecialchars($title); ?> - Hồ Chí Minh</span>
            </div>
            <div class="flex items-center text-[13px] text-gray-500">
                <span class="mr-2 hidden md:inline">Chọn quận, huyện tại Hồ Chí Minh:</span>
                <select class="border border-gray-300 bg-gray-100/50 rounded px-3 py-1.5 outline-none focus:border-gray-400 min-w-[180px]">
                    <option>--Chọn Quận, Huyện--</option>
                </select>
            </div>
        </div>

        <!-- Category Tags -->
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 py-5 text-[13px] border-b border-gray-100">
            <?php 
            $cats = ['nha-hang' => 'Nhà hàng', 'lau' => 'Lẩu', 'buffet' => 'Buffet', 'hai-san' => 'Hải sản', 'lau-nuong' => 'Lẩu & Nướng', 'quan-nhau' => 'Quán Nhậu', 'mon-chay' => 'Món chay', 'dat-tiec' => 'Đặt tiệc', 'han-quoc' => 'Hàn Quốc', 'nhat-ban' => 'Nhật Bản', 'mon-au' => 'Món Âu', 'mon-viet' => 'Món Việt', 'mon-thai' => 'Món Thái', 'mon-trung-hoa' => 'Món Trung Hoa', 'tiec-cuoi' => 'Tiệc cưới'];
            foreach($cats as $k => $v) {
                if ($k === $type) {
                    echo '<a href="'.BASE_URL.'danh-muc/'.$k.'" class="bg-primary text-white px-3 py-1 rounded font-bold shadow-sm">'.$v.'</a>';
                } else {
                    echo '<a href="'.BASE_URL.'danh-muc/'.$k.'" class="text-gray-500 hover:text-primary font-medium transition-colors">'.$v.'</a>';
                }
            }
            ?>
        </div>
    </div>
</div>

<!-- Content -->
<section class="py-8 bg-gray-50 min-h-[60vh]">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
        
        <?php if (!empty($restaurants)): ?>
            <!-- Restaurant Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($restaurants as $res): ?>
                    <div class="bg-white rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-all duration-300 flex flex-col group cursor-pointer">
                        <!-- Image Area -->
                        <div class="relative h-[200px] overflow-hidden">
                            <img src="<?php echo htmlspecialchars($res['img']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=800';">
                            
                            <!-- Bottom Info Gradient -->
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent pt-12 pb-3 px-4 flex flex-col">
                                <h3 class="text-white font-bold text-[15px] leading-snug mb-1 line-clamp-1"><?php echo htmlspecialchars($res['name']); ?></h3>
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="flex text-yellow-400 text-[10px]">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i>
                                    </div>
                                    <div class="text-yellow-400 text-[10px] tracking-widest font-bold">$$$$$</div>
                                </div>
                                <p class="text-gray-300 text-[11px] truncate"><i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($res['address']); ?></p>
                            </div>
                        </div>
                        
                        <!-- Details Area -->
                        <div class="p-4 flex flex-col items-center text-center flex-grow">
                            <div class="text-primary font-bold text-[15px] mb-1"><?php echo htmlspecialchars($res['discount']); ?></div>
                            <div class="text-gray-500 text-[12px] mb-4"><?php echo htmlspecialchars($res['desc']); ?></div>
                            <button class="mt-auto bg-white border border-gray-300 text-gray-700 font-bold text-[13px] px-8 py-2 rounded hover:bg-gray-50 transition-colors w-max">
                                Đặt chỗ ngay
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif (!empty($monAn)): ?>
            <!-- Product Grid -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Hiển thị <?php echo count($monAn); ?> kết quả</h2>
                <div class="flex gap-2 relative z-10">
                    <button class="bg-white border border-gray-200 px-3 py-1.5 rounded text-sm font-medium text-gray-600 hover:text-primary hover:border-primary transition-colors">
                        <i class="fas fa-filter mr-1"></i> Lọc
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($monAn as $mon): ?>
                    <div class="food-card bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col h-full relative">
                        <div class="relative h-48 overflow-hidden">
                            <?php 
                                $catImg = $mon['hinh_anh'] ?? '';
                                if (strpos($catImg, 'http') !== 0 && strpos($catImg, '/') !== 0) {
                                    $catImg = (defined('BASE_URL') ? BASE_URL : '/') . $catImg;
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($catImg); ?>" alt="<?php echo htmlspecialchars($mon['ten_mon']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800';">
                            <!-- Tag Vùng Miền (Góc trái bên trên) -->
                            <div class="card-region-tag absolute top-3 left-3 bg-black/60 backdrop-blur-sm text-white text-[11px] font-medium px-2 py-1 rounded-md flex items-center gap-1.5 z-10 pointer-events-none shadow-xs">
                                <i class="fa-solid fa-map-location-dot text-primary text-[10px]"></i> Miền <?php echo htmlspecialchars($mon['ten_vung']); ?>
                            </div>
                            <!-- Tag Tỉnh Thành (Góc phải bên trên - nếu có) -->
                            <?php if (!empty($mon['tinh_thanh'])): ?>
                            <div class="card-province-tag absolute top-3 right-3 bg-red-600/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2 py-1 rounded-md flex items-center gap-1 z-10 pointer-events-none shadow-xs">
                                <i class="fa-solid fa-location-dot text-white text-[9px]"></i> <?php echo htmlspecialchars($mon['tinh_thanh']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary transition-colors">
                                    <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($mon['slug']); ?>" class="after:absolute after:inset-0">
                                        <?php echo htmlspecialchars($mon['ten_mon']); ?>
                                    </a>
                                </h3>
                                <div class="flex items-center gap-1 bg-yellow-50 px-1.5 py-0.5 rounded text-xs font-bold text-yellow-600 z-10 shrink-0">
                                    <i class="fa-solid fa-star text-[10px]"></i>
                                    <?php echo htmlspecialchars($mon['danh_gia']); ?>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm mb-4 flex-grow line-clamp-2">
                                <?php echo htmlspecialchars($mon['mo_ta_ngan']); ?>
                            </p>
                            <div class="flex items-center justify-end mt-auto pt-3 border-t border-gray-100 z-10">
                                <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($mon['slug']); ?>" 
                                   class="w-8 h-8 rounded-full bg-red-50 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all transform hover:scale-110 active:scale-95 z-20 relative cursor-pointer shadow-xs" 
                                   title="Xem chi tiết <?php echo htmlspecialchars($mon['ten_mon']); ?>">
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 text-center max-w-lg mx-auto">
                <div class="w-32 h-32 bg-white border-4 border-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-300 text-6xl shadow-sm">
                    <i class="fa-solid fa-store-slash"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-800 mb-3 font-serif">Chưa có dữ liệu</h2>
                <p class="text-gray-500 text-[15px] mb-8">Rất tiếc, hiện tại chúng tôi chưa cập nhật dữ liệu cho danh mục <strong><?php echo htmlspecialchars($title); ?></strong>. Xin vui lòng quay lại sau!</p>
                <a href="<?php echo BASE_URL; ?>" class="bg-primary hover:bg-primaryDark text-white font-bold py-2.5 px-6 rounded transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ
                </a>
            </div>
        <?php endif; ?>
        
    </div>
</section>
