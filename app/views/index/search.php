<?php 
/** 
 * @var string $keyword 
 * @var string $location 
 * @var array $restaurants 
 * @var array $monAnList 
 */ 
$totalMonAn = isset($monAnList) ? count($monAnList) : 0;
$totalRestaurants = isset($restaurants) ? count($restaurants) : 0;
$totalResults = $totalMonAn + $totalRestaurants;
$locationLabel = empty($location) || $location === 'Tất cả' || $location === 'Tất cả khu vực' ? 'Toàn quốc' : htmlspecialchars($location);
?>
<div class="py-8 lg:py-12 bg-gray-50/70 min-h-screen">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-4">
            <a href="<?php echo BASE_URL; ?>" class="hover:text-primary transition-colors">Trang chủ</a>
            <i class="fas fa-chevron-right text-[9px] text-gray-400"></i>
            <span class="text-gray-800 font-semibold">Tìm kiếm</span>
            <?php if (!empty($keyword)): ?>
                <i class="fas fa-chevron-right text-[9px] text-gray-400"></i>
                <span class="text-primary font-semibold truncate max-w-[200px]">"<?php echo htmlspecialchars($keyword); ?>"</span>
            <?php endif; ?>
        </nav>

        <!-- Search Header -->
        <div class="bg-white rounded-2xl p-6 lg:p-8 shadow-xs border border-gray-100 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight mb-2">
                    <?php if (!empty($keyword)): ?>
                        Kết quả tìm kiếm: <span class="text-primary">"<?php echo htmlspecialchars($keyword); ?>"</span>
                    <?php else: ?>
                        Khám phá ẩm thực <span class="text-primary"><?php echo $locationLabel; ?></span>
                    <?php endif; ?>
                </h1>
                <p class="text-sm lg:text-base text-gray-600 flex items-center flex-wrap gap-2">
                    <span>Khu vực: <span class="font-bold text-gray-800 bg-gray-100 px-2.5 py-0.5 rounded-full"><i class="fas fa-map-marker-alt text-primary text-xs mr-1"></i><?php echo $locationLabel; ?></span></span>
                    <span class="text-gray-300">•</span>
                    <span>Tìm thấy <strong class="text-primary font-bold"><?php echo $totalResults; ?></strong> kết quả (<strong class="text-gray-800"><?php echo $totalMonAn; ?></strong> món ăn đặc sản & <strong class="text-gray-800"><?php echo $totalRestaurants; ?></strong> nhà hàng)</span>
                </p>
            </div>

            <!-- Quick Filter Tabs -->
            <div class="flex items-center gap-1.5 sm:gap-2 bg-gray-100/80 p-1 sm:p-1.5 rounded-xl shrink-0 self-stretch sm:self-start md:self-auto overflow-x-auto hide-scroll max-w-full">
                <button type="button" class="search-tab-btn active px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-bold transition-all bg-white text-primary shadow-xs whitespace-nowrap" data-target="all">
                    Tất cả (<?php echo $totalResults; ?>)
                </button>
                <button type="button" class="search-tab-btn px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-bold transition-all text-gray-600 hover:text-gray-900 whitespace-nowrap" data-target="dishes">
                    Món đặc sản (<?php echo $totalMonAn; ?>)
                </button>
                <button type="button" class="search-tab-btn px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-bold transition-all text-gray-600 hover:text-gray-900 whitespace-nowrap" data-target="restaurants">
                    Nhà hàng & Quán ăn (<?php echo $totalRestaurants; ?>)
                </button>
            </div>
        </div>

        <?php if ($totalResults > 0): ?>

            <!-- SECTION 1: MÓN ĂN ĐẶC SẢN -->
            <?php if ($totalMonAn > 0): ?>
            <div id="section-dishes" class="search-section mb-12">
                <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-primary flex items-center justify-center font-bold text-lg">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Món ăn đặc sản & Công thức nấu</h2>
                            <p class="text-xs text-gray-500">Các món ăn truyền thống chuẩn vị 3 miền</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200"><?php echo $totalMonAn; ?> món ăn</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($monAnList as $dish): ?>
                        <div class="bg-white rounded-2xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col h-full hover:-translate-y-1">
                            <!-- Image Container -->
                            <div class="relative h-48 overflow-hidden bg-gray-100">
                                <?php 
                                    $sImg = $dish['hinh_anh'] ?? 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=600';
                                    if (strpos($sImg, 'http') !== 0 && strpos($sImg, '/') !== 0) {
                                        $sImg = (defined('BASE_URL') ? BASE_URL : '/') . $sImg;
                                    }
                                ?>
                                <img src="<?php echo htmlspecialchars($sImg); ?>" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                                     alt="<?php echo htmlspecialchars($dish['ten_mon']); ?>" 
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=600';">
                                
                                <!-- Regional Badge -->
                                <?php if (!empty($dish['ten_vung'])): ?>
                                    <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-gray-800 text-[11px] font-black px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt text-primary text-[10px]"></i> <?php echo htmlspecialchars($dish['ten_vung']); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Rating badge -->
                                <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-yellow-400 text-xs font-black px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <i class="fas fa-star text-[11px]"></i>
                                    <span class="text-white"><?php echo htmlspecialchars($dish['danh_gia'] ?? '4.5'); ?></span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-grow">
                                <h3 class="text-base font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-1 mb-2">
                                    <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($dish['slug']); ?>">
                                        <?php echo htmlspecialchars($dish['ten_mon']); ?>
                                    </a>
                                </h3>

                                <p class="text-xs text-gray-500 line-clamp-2 mb-4 leading-relaxed flex-grow">
                                    <?php echo htmlspecialchars(strip_tags($dish['mo_ta_ngan'] ?? 'Đặc sản thơm ngon nức tiếng')); ?>
                                </p>

                                <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100 mt-auto">
                                    <span class="font-bold text-primary flex items-center gap-1">
                                        <i class="fas fa-tag text-[10px]"></i>
                                        <?php echo isset($dish['gia_tien']) && $dish['gia_tien'] > 0 ? number_format($dish['gia_tien']) . ' đ' : 'Bình dân'; ?>
                                    </span>
                                    <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($dish['slug']); ?>" 
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-700 hover:text-primary transition-colors">
                                        Xem chi tiết <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- SECTION 2: NHÀ HÀNG & QUÁN ĂN -->
            <?php if ($totalRestaurants > 0): ?>
            <div id="section-restaurants" class="search-section mb-12">
                <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-lg">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Nhà hàng & Địa điểm ăn uống</h2>
                            <p class="text-xs text-gray-500">Đặt bàn giữ chỗ nhanh chóng - Ưu đãi hấp dẫn</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200"><?php echo $totalRestaurants; ?> nhà hàng</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($restaurants as $res): ?>
                        <div class="bg-white rounded-2xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col h-full hover:-translate-y-1">
                            <div class="relative h-52 overflow-hidden bg-gray-100">
                                <img src="<?php echo htmlspecialchars($res['image'] ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=600'); ?>" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                                     alt="<?php echo htmlspecialchars($res['name']); ?>" 
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=600';">
                                
                                <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                    <i class="fa-solid fa-map-location-dot text-primary"></i> <?php echo htmlspecialchars($res['district']); ?>
                                </div>

                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-md text-yellow-600 text-xs font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-xs">
                                    <i class="fa-solid fa-star text-yellow-500 text-[11px]"></i> <?php echo htmlspecialchars($res['rating']); ?>
                                </div>
                            </div>

                            <div class="p-5 flex flex-col flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-1 mb-2">
                                    <?php echo htmlspecialchars($res['name']); ?>
                                </h3>

                                <p class="text-xs text-gray-500 mb-5 flex-grow line-clamp-2 leading-relaxed">
                                    <i class="fa-solid fa-location-dot text-primary w-4 text-center mr-1"></i>
                                    <?php echo htmlspecialchars($res['address']); ?>
                                </p>

                                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Đang mở cửa
                                    </span>
                                    <button type="button" class="btn-book-now bg-primary hover:bg-primaryDark text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-xs hover:shadow flex items-center gap-1.5">
                                        <i class="fas fa-calendar-check text-[11px]"></i> Đặt bàn giữ chỗ
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- NO RESULTS STATE -->
            <div class="py-16 px-4 text-center bg-white rounded-3xl shadow-xs border border-gray-100 max-w-3xl mx-auto my-8">
                <div class="w-24 h-24 bg-red-50 text-primary rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner">
                    <i class="fa-solid fa-magnifying-glass-minus"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Không tìm thấy kết quả phù hợp!</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto leading-relaxed">
                    Không có món ăn hoặc nhà hàng nào khớp với từ khóa 
                    <?php if (!empty($keyword)): ?><strong class="text-gray-800">"<?php echo htmlspecialchars($keyword); ?>"</strong><?php endif; ?>
                    <?php if (!empty($location)): ?> tại khu vực <strong class="text-gray-800">"<?php echo $locationLabel; ?>"</strong><?php endif; ?>.
                </p>

                <!-- Suggested Keywords -->
                <div class="mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Gợi ý từ khóa phổ biến:</p>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <?php 
                        $suggestions = ['Bánh xèo', 'Lẩu mắm', 'Cơm tấm', 'Bún quậy', 'Bò tơ Tây Ninh', 'Phở', 'Ốc', 'Gỏi cá trích'];
                        foreach ($suggestions as $sug):
                        ?>
                            <a href="<?php echo BASE_URL; ?>search?keyword=<?php echo urlencode($sug); ?>" 
                               class="bg-gray-100 hover:bg-red-50 hover:text-primary text-gray-700 text-xs font-semibold px-3.5 py-1.5 rounded-full transition-colors border border-gray-200 hover:border-red-200">
                                <i class="fas fa-search text-[10px] mr-1 opacity-60"></i><?php echo $sug; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="<?php echo BASE_URL; ?>" class="inline-flex items-center gap-2 text-white bg-primary hover:bg-primaryDark font-bold px-7 py-3 rounded-full shadow-xs transition-all hover:scale-105 text-sm">
                    <i class="fas fa-home"></i> Quay lại trang chủ
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Tab Switch Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.search-tab-btn');
    const secDishes = document.getElementById('section-dishes');
    const secRestaurants = document.getElementById('section-restaurants');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            tabBtns.forEach(b => {
                b.classList.remove('active', 'bg-white', 'text-primary', 'shadow-xs');
                b.classList.add('text-gray-600');
            });
            this.classList.add('active', 'bg-white', 'text-primary', 'shadow-xs');
            this.classList.remove('text-gray-600');

            const target = this.getAttribute('data-target');
            if (target === 'all') {
                if (secDishes) secDishes.style.display = 'block';
                if (secRestaurants) secRestaurants.style.display = 'block';
            } else if (target === 'dishes') {
                if (secDishes) secDishes.style.display = 'block';
                if (secRestaurants) secRestaurants.style.display = 'none';
            } else if (target === 'restaurants') {
                if (secDishes) secDishes.style.display = 'none';
                if (secRestaurants) secRestaurants.style.display = 'block';
            }
        });
    });
});
</script>
