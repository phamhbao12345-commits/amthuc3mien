<!-- Hero Banner -->
<section class="relative bg-black h-[420px] sm:h-[500px] md:h-[650px] flex items-center group overflow-hidden">
    <div class="absolute inset-0">
        <img src="<?php echo (defined('BASE_URL') ? BASE_URL : '/'); ?>asset/uploads/hero_vietnam_dining.jpg" alt="Không gian ẩm thực Việt Nam" class="w-full h-full object-cover opacity-60 transform group-hover:scale-105 transition-transform duration-1000">
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/30"></div>
    
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-7xl relative z-10">
        <div class="max-w-2xl">
            <div class="inline-block px-3.5 py-1 sm:px-4 sm:py-1.5 rounded-full border border-primary/50 bg-primary/20 backdrop-blur-sm text-primary text-xs sm:text-sm font-bold tracking-widest uppercase mb-4 sm:mb-6">
                Hương vị nguyên bản
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-white mb-4 sm:mb-6 leading-tight">
                Khám Phá Tinh Hoa<br><span class="text-primary">Ẩm Thực Việt Nam</span>
            </h1>
            <p class="text-gray-300 text-sm sm:text-base md:text-lg mb-6 sm:mb-8 max-w-lg leading-relaxed line-clamp-3 sm:line-clamp-none">
                Trải nghiệm không gian ẩm thực sang trọng, đậm đà bản sắc truyền thống từ Bắc chí Nam trong từng món ăn được chuẩn bị tinh tế.
            </p>
            <div class="flex flex-wrap gap-3 sm:gap-4">
                <a href="#menu" id="btn-explore" class="bg-primary hover:bg-primaryDark text-white px-6 sm:px-8 py-3 sm:py-3.5 rounded-lg sm:rounded font-bold text-sm sm:text-base shadow-lg shadow-primary/30 transition-all hover:-translate-y-1">
                    Khám phá ngay
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section id="menu" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-7xl">
        
        <!-- Section Header -->
        <div class="text-center mb-16">
            <span class="text-primary font-bold tracking-widest uppercase text-sm mb-2 block">Tuyển tập đặc sắc</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-6">Món Ăn Nổi Bật</h2>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>

        <!-- Filter/Tabs -->
        <div class="flex flex-col items-center gap-5 mb-12">
            <!-- Region Filter -->
            <div class="flex justify-start sm:justify-center gap-2 sm:gap-4 overflow-x-auto hide-scroll w-full max-w-4xl px-2 sm:px-4 py-1" id="food-filter-container">
                <button data-filter="all" class="filter-btn px-4 sm:px-6 py-2 sm:py-2.5 rounded-full bg-primary text-white text-xs sm:text-sm font-medium shadow-md whitespace-nowrap shrink-0">Tất cả miền</button>
                <button data-filter="Bắc" class="filter-btn px-4 sm:px-6 py-2 sm:py-2.5 rounded-full bg-white text-gray-600 hover:text-primary hover:bg-red-50 text-xs sm:text-sm font-medium shadow-sm transition-colors whitespace-nowrap border border-gray-200 shrink-0">Đặc sản Miền Bắc</button>
                <button data-filter="Trung" class="filter-btn px-4 sm:px-6 py-2 sm:py-2.5 rounded-full bg-white text-gray-600 hover:text-primary hover:bg-red-50 text-xs sm:text-sm font-medium shadow-sm transition-colors whitespace-nowrap border border-gray-200 shrink-0">Đặc sản Miền Trung</button>
                <button data-filter="Nam" class="filter-btn px-4 sm:px-6 py-2 sm:py-2.5 rounded-full bg-white text-gray-600 hover:text-primary hover:bg-red-50 text-xs sm:text-sm font-medium shadow-sm transition-colors whitespace-nowrap border border-gray-200 shrink-0">Đặc sản Miền Nam</button>
            </div>

            <!-- Northern Province Filter (Miền Bắc) -->
            <div id="northern-province-container" class="province-container hidden transition-all duration-300 w-full max-w-4xl px-4 -mt-1">
                <div class="flex items-center justify-center gap-1.5 sm:gap-2 flex-wrap py-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-gray-500 uppercase tracking-wider mr-1 shrink-0">
                        <i class="fas fa-location-dot text-primary text-xs"></i> Tỉnh thành:
                    </span>
                    <button type="button" data-province="all" class="province-btn active px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all bg-primary text-white shadow-sm shrink-0">
                        Tất cả
                    </button>
                    <button type="button" data-province="Hà Nội" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Hà Nội
                    </button>
                    <button type="button" data-province="Hải Phòng" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Hải Phòng
                    </button>
                    <button type="button" data-province="Quảng Ninh" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Quảng Ninh
                    </button>
                    <button type="button" data-province="Ninh Bình" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Ninh Bình
                    </button>
                    <button type="button" data-province="Lào Cai" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Lào Cai (Sa Pa)
                    </button>
                    <button type="button" data-province="Nam Định" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Nam Định
                    </button>
                </div>
            </div>

            <!-- Central Province Filter (Miền Trung) -->
            <div id="central-province-container" class="province-container hidden transition-all duration-300 w-full max-w-4xl px-4 -mt-1">
                <div class="flex items-center justify-center gap-1.5 sm:gap-2 flex-wrap py-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-gray-500 uppercase tracking-wider mr-1 shrink-0">
                        <i class="fas fa-location-dot text-primary text-xs"></i> Tỉnh thành:
                    </span>
                    <button type="button" data-province="all" class="province-btn active px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all bg-primary text-white shadow-sm shrink-0">
                        Tất cả
                    </button>
                    <button type="button" data-province="Thừa Thiên Huế" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Huế
                    </button>
                    <button type="button" data-province="Đà Nẵng" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Đà Nẵng
                    </button>
                    <button type="button" data-province="Quảng Nam" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Quảng Nam
                    </button>
                    <button type="button" data-province="Bình Định" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Bình Định
                    </button>
                    <button type="button" data-province="Phú Yên" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Phú Yên
                    </button>
                    <button type="button" data-province="Khánh Hòa" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Khánh Hòa (Nha Trang)
                    </button>
                    <button type="button" data-province="Lâm Đồng" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Lâm Đồng (Đà Lạt)
                    </button>
                </div>
            </div>

            <!-- Southern Province Filter (Miền Nam) -->
            <div id="southern-province-container" class="province-container hidden transition-all duration-300 w-full max-w-4xl px-4 -mt-1">
                <div class="flex items-center justify-center gap-1.5 sm:gap-2 flex-wrap py-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-gray-500 uppercase tracking-wider mr-1 shrink-0">
                        <i class="fas fa-location-dot text-primary text-xs"></i> Tỉnh thành:
                    </span>
                    <button type="button" data-province="all" class="province-btn active px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all bg-primary text-white shadow-sm shrink-0">
                        Tất cả
                    </button>
                    <button type="button" data-province="TP. Hồ Chí Minh" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        TP.HCM
                    </button>
                    <button type="button" data-province="Đồng Nai" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Đồng Nai
                    </button>
                    <button type="button" data-province="Tây Ninh" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Tây Ninh
                    </button>
                    <button type="button" data-province="TP. Cần Thơ" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Cần Thơ
                    </button>
                    <button type="button" data-province="An Giang" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        An Giang
                    </button>
                    <button type="button" data-province="Đồng Tháp" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Đồng Tháp
                    </button>
                    <button type="button" data-province="Vĩnh Long" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Vĩnh Long
                    </button>
                    <button type="button" data-province="Cà Mau" class="province-btn px-3.5 py-1.5 rounded-full text-xs font-medium transition-all bg-white text-gray-600 hover:text-primary hover:bg-red-50 border border-gray-200 shadow-sm shrink-0">
                        Cà Mau
                    </button>
                </div>
            </div>
            
            <!-- Flavor & Search Filter -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 bg-white p-2.5 sm:py-1.5 sm:pl-5 sm:pr-2 rounded-2xl sm:rounded-full shadow-sm border border-gray-200 w-full sm:w-auto">
                <div class="flex items-center gap-2 px-1 sm:px-0">
                    <i class="fas fa-utensils text-primary text-xs sm:text-sm"></i>
                    <span class="text-xs sm:text-[14px] font-bold text-gray-700 whitespace-nowrap">Khẩu vị:</span>
                    <select id="flavor-filter" class="flex-1 sm:flex-none bg-transparent text-gray-600 border-none outline-none cursor-pointer text-xs sm:text-[14px] font-medium pr-2 sm:border-r sm:border-gray-300 sm:mr-2 py-1">
                        <option value="all">Tất cả khẩu vị</option>
                        <option value="chua-cay">Chua cay đậm vị</option>
                        <option value="thanh-dam">Thanh đạm, ngọt thanh</option>
                        <option value="dam-da">Đậm đà, mặn ngọt</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-2 bg-gray-50 rounded-xl sm:rounded-full px-3 sm:px-4 py-1.5 border border-gray-100 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/20 transition-all">
                    <i class="fas fa-search text-gray-400 text-xs sm:text-sm"></i>
                    <input type="text" id="flavor-search" placeholder="Nhập vị (VD: chua, cay...)" class="bg-transparent border-none outline-none text-xs sm:text-[14px] w-full sm:w-48 text-gray-700 placeholder-gray-400">
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 lg:gap-8" id="food-grid">
            <?php if (isset($monAn) && count($monAn) > 0): ?>
                <?php foreach ($monAn as $mon): ?>
                    <div data-region="<?php echo htmlspecialchars($mon['ten_vung'] ?? ''); ?>" 
                         data-province="<?php echo htmlspecialchars($mon['tinh_thanh'] ?? ''); ?>" 
                         class="food-card bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col h-full relative">
                        <!-- Image Container -->
                        <div class="relative h-56 sm:h-64 overflow-hidden">
                            <?php 
                                $cardImg = $mon['hinh_anh'];
                                if (strpos($cardImg, 'http') !== 0 && strpos($cardImg, '/') !== 0) {
                                    $cardImg = (defined('BASE_URL') ? BASE_URL : '/') . $cardImg;
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($cardImg); ?>" alt="<?php echo htmlspecialchars($mon['ten_mon']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800';">
                            
                            <!-- Tag Vùng Miền (Góc trái bên trên) -->
                            <div class="card-region-tag absolute top-4 left-4 bg-black/60 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-1.5 rounded-md flex items-center gap-1.5 z-10 pointer-events-none transition-all shadow-sm">
                                <i class="fa-solid fa-map-location-dot text-primary"></i> 
                                <span class="region-tag-text">Miền <?php echo htmlspecialchars($mon['ten_vung']); ?></span>
                            </div>
                            
                            <!-- Tag Tỉnh Thành (Góc phải bên trên - hiện khi lọc tỉnh thành) -->
                            <div class="card-province-tag hidden absolute top-4 right-4 bg-red-600/90 backdrop-blur-sm text-white text-xs font-semibold px-2.5 py-1.5 rounded-md flex items-center gap-1.5 z-10 pointer-events-none shadow-md transition-all">
                                <i class="fa-solid fa-location-dot text-white text-[10px]"></i> 
                                <span class="province-tag-text"><?php echo htmlspecialchars($mon['tinh_thanh'] ?? ''); ?></span>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary transition-colors">
                                    <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($mon['slug']); ?>" class="after:absolute after:inset-0">
                                        <?php echo htmlspecialchars($mon['ten_mon']); ?>
                                    </a>
                                </h3>
                                <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded text-sm font-bold text-yellow-600 z-10">
                                    <i class="fa-solid fa-star"></i>
                                    <?php echo htmlspecialchars($mon['danh_gia']); ?>
                                </div>
                            </div>
                            
                            <p class="text-gray-500 text-sm mb-6 flex-grow line-clamp-2">
                                <?php echo htmlspecialchars($mon['mo_ta_ngan']); ?>
                            </p>
                            
                            <div class="flex items-center justify-end mt-auto pt-4 border-t border-gray-100 z-10">
                                <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($mon['slug']); ?>" 
                                   class="w-10 h-10 rounded-full bg-red-50 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all transform hover:scale-110 active:scale-95 z-20 relative cursor-pointer shadow-xs" 
                                   title="Xem chi tiết <?php echo htmlspecialchars($mon['ten_mon']); ?>">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-4xl">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Chưa có món ăn nào</h3>
                    <p class="text-gray-500">Chúng tôi đang cập nhật thực đơn, vui lòng quay lại sau.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination Controls -->
        <div class="flex justify-center items-center gap-2 mt-12" id="food-pagination">
            <!-- Buttons injected via JS -->
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="text-center group">
                <div class="w-20 h-20 bg-red-50 text-primary rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:-translate-y-2 transition-transform duration-300">
                    <i class="fa-solid fa-leaf text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Nguyên liệu tươi sạch</h3>
                <p class="text-gray-500 text-sm leading-relaxed max-w-sm mx-auto">Được tuyển chọn kỹ lưỡng mỗi ngày từ các nông trại đạt chuẩn, đảm bảo vệ sinh an toàn thực phẩm.</p>
            </div>
            <div class="text-center group">
                <div class="w-20 h-20 bg-red-50 text-primary rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:-translate-y-2 transition-transform duration-300">
                    <i class="fa-solid fa-fire-burner text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Đầu bếp 5 sao</h3>
                <p class="text-gray-500 text-sm leading-relaxed max-w-sm mx-auto">Đội ngũ đầu bếp giàu kinh nghiệm, am hiểu sâu sắc tinh hoa ẩm thực truyền thống của 3 miền.</p>
            </div>
            <div class="text-center group">
                <div class="w-20 h-20 bg-red-50 text-primary rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:-translate-y-2 transition-transform duration-300">
                    <i class="fa-solid fa-wine-glass text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Không gian sang trọng</h3>
                <p class="text-gray-500 text-sm leading-relaxed max-w-sm mx-auto">Kiến trúc kết hợp hài hòa giữa nét cổ kính truyền thống và sự tiện nghi hiện đại, ấm cúng.</p>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const provinceBtns = document.querySelectorAll('.province-btn');
    const northernContainer = document.getElementById('northern-province-container');
    const centralContainer = document.getElementById('central-province-container');
    const southernContainer = document.getElementById('southern-province-container');
    const flavorSelect = document.getElementById('flavor-filter');
    const flavorSearch = document.getElementById('flavor-search');
    const foodCards = Array.from(document.querySelectorAll('.food-card'));
    const paginationContainer = document.getElementById('food-pagination');
    const foodGrid = document.getElementById('food-grid');
    
    let currentFilter = 'all';
    let currentProvince = 'all';
    let currentFlavor = 'all';
    let currentFlavorSearch = '';
    let currentPage = 1;
    const itemsPerPage = 9;

    // Hiển thị thanh tỉnh thành theo miền được chọn
    function showProvinceContainer(region) {
        if (northernContainer) northernContainer.classList.add('hidden');
        if (centralContainer) centralContainer.classList.add('hidden');
        if (southernContainer) southernContainer.classList.add('hidden');

        if (region === 'Bắc' && northernContainer) {
            northernContainer.classList.remove('hidden');
        } else if (region === 'Trung' && centralContainer) {
            centralContainer.classList.remove('hidden');
        } else if (region === 'Nam' && southernContainer) {
            southernContainer.classList.remove('hidden');
        }
    }

    // Kiểm tra khẩu vị món ăn
    function checkFlavor(card, flavor, searchKeyword) {
        const textContent = card.innerText.toLowerCase();
        
        // Kiểm tra từ khóa tìm kiếm vị
        if (searchKeyword) {
            if (!textContent.includes(searchKeyword.toLowerCase())) {
                return false;
            }
        }
        
        // Kiểm tra dropdown khẩu vị
        if (flavor === 'all') return true;
        
        if (flavor === 'chua-cay') {
            return textContent.includes('chua') || textContent.includes('cay') || textContent.includes('thái') || textContent.includes('tomyum');
        }
        if (flavor === 'thanh-dam') {
            return textContent.includes('thanh') || textContent.includes('nhẹ') || textContent.includes('nấm') || textContent.includes('chay') || textContent.includes('tươi mát') || textContent.includes('ngọt thanh');
        }
        if (flavor === 'dam-da') {
            return textContent.includes('đậm đà') || textContent.includes('mặn') || textContent.includes('ngọt') || textContent.includes('mắm') || textContent.includes('kho') || textContent.includes('nướng') || textContent.includes('béo');
        }
        return true;
    }

    // Kiểm tra tỉnh thành cho cả 3 miền
    function checkProvince(card, province) {
        if (!province || province === 'all') return true;
        
        const cardProvince = (card.getAttribute('data-province') || '').trim().toLowerCase();
        const cardText = card.innerText.toLowerCase();
        const target = province.toLowerCase();

        // Khớp trực tiếp thuộc tính data-province
        if (cardProvince === target) return true;
        if (cardProvince.includes(target) || target.includes(cardProvince)) return true;

        // Fallback nhận diện theo các từ khóa địa danh đặc trưng
        // --- MIỀN BẮC ---
        if (target.includes('hà nội')) {
            return cardProvince.includes('hà nội') || cardText.includes('hà nội') || cardText.includes('kinh kỳ') || cardText.includes('lã vọng') || cardText.includes('thủ đô');
        }
        if (target.includes('hải phòng')) {
            return cardProvince.includes('hải phòng') || cardText.includes('hải phòng') || cardText.includes('đất cảng') || cardText.includes('cột đèn') || cardText.includes('chí chương') || cardText.includes('bánh đa cua');
        }
        if (target.includes('quảng ninh')) {
            return cardProvince.includes('quảng ninh') || cardText.includes('quảng ninh') || cardText.includes('hạ long') || cardText.includes('tiên yên') || cardText.includes('chả mực') || cardText.includes('bề bề');
        }
        if (target.includes('ninh bình')) {
            return cardProvince.includes('ninh bình') || cardText.includes('ninh bình') || cardText.includes('dê') || cardText.includes('cơm cháy') || cardText.includes('ốc núi');
        }
        if (target.includes('lào cai')) {
            return cardProvince.includes('lào cai') || cardText.includes('lào cai') || cardText.includes('sa pa') || cardText.includes('sapa') || cardText.includes('bắc hà') || cardText.includes('thắng cố') || cardText.includes('cá tầm');
        }
        if (target.includes('nam định')) {
            return cardProvince.includes('nam định') || cardText.includes('nam định') || cardText.includes('giao thủy') || cardText.includes('sìu châu') || cardText.includes('thành nam');
        }

        // --- MIỀN TRUNG ---
        if (target.includes('huế')) {
            return cardProvince.includes('huế') || cardText.includes('huế') || cardText.includes('cố đô') || cardText.includes('sông hương') || cardText.includes('cơm hến') || cardText.includes('bột lọc');
        }
        if (target.includes('đà nẵng')) {
            return cardProvince.includes('đà nẵng') || cardText.includes('đà nẵng') || cardText.includes('nam ô') || cardText.includes('sông hàn') || cardText.includes('chả bò');
        }
        if (target.includes('quảng nam')) {
            return cardProvince.includes('quảng nam') || cardText.includes('quảng nam') || cardText.includes('hội an') || cardText.includes('cầu mống') || cardText.includes('cao lầu') || cardText.includes('mì quảng');
        }
        if (target.includes('bình định')) {
            return cardProvince.includes('bình định') || cardText.includes('bình định') || cardText.includes('quy nhơn') || cardText.includes('chợ huyện') || cardText.includes('bánh hỏi') || cardText.includes('tré');
        }
        if (target.includes('phú yên')) {
            return cardProvince.includes('phú yên') || cardText.includes('phú yên') || cardText.includes('tuy hòa') || cardText.includes('lá é') || cardText.includes('cá ngừ') || cardText.includes('bánh canh hẹ');
        }
        if (target.includes('khánh hòa')) {
            return cardProvince.includes('khánh hòa') || cardText.includes('khánh hòa') || cardText.includes('nha trang') || cardText.includes('ninh hòa') || cardText.includes('sứa') || cardText.includes('cá dầm');
        }
        if (target.includes('lâm đồng')) {
            return cardProvince.includes('lâm đồng') || cardText.includes('lâm đồng') || cardText.includes('đà lạt') || cardText.includes('ba toa') || cardText.includes('kem bơ');
        }

        // --- MIỀN NAM ---
        if (target.includes('hồ chí minh')) {
            return cardProvince.includes('hồ chí minh') || cardText.includes('hồ chí minh') || cardText.includes('sài gòn') || cardText.includes('tp.hcm');
        }
        if (target.includes('đồng nai')) {
            return cardProvince.includes('đồng nai') || cardText.includes('đồng nai') || cardText.includes('tân triều') || cardText.includes('biên hòa') || cardText.includes('long khánh');
        }
        if (target.includes('tây ninh')) {
            return cardProvince.includes('tây ninh') || cardText.includes('tây ninh') || cardText.includes('trảng bàng') || cardText.includes('bà đen');
        }
        if (target.includes('cần thơ')) {
            return cardProvince.includes('cần thơ') || cardText.includes('cần thơ') || cardText.includes('ninh kiều') || cardText.includes('cái răng') || cardText.includes('phong điền');
        }
        if (target.includes('an giang')) {
            return cardProvince.includes('an giang') || cardText.includes('an giang') || cardText.includes('châu đốc') || cardText.includes('tri tôn') || cardText.includes('núi sam') || cardText.includes('long xuyên') || cardText.includes('ô thum') || cardText.includes('bảy núi') || cardText.includes('thốt nốt');
        }
        if (target.includes('đồng tháp')) {
            return cardProvince.includes('đồng tháp') || cardText.includes('đồng tháp') || cardText.includes('sa đéc') || cardText.includes('lai vung') || cardText.includes('cao lãnh') || cardText.includes('tháp mười') || cardText.includes('sa giang') || cardText.includes('sen');
        }
        if (target.includes('vĩnh long')) {
            return cardProvince.includes('vĩnh long') || cardText.includes('vĩnh long') || cardText.includes('bình tân') || cardText.includes('cổ chiên') || cardText.includes('bình minh') || cardText.includes('mỹ hòa') || cardText.includes('tam bình') || cardText.includes('lục sĩ thành');
        }
        if (target.includes('cà mau')) {
            return cardProvince.includes('cà mau') || cardText.includes('cà mau') || cardText.includes('đất mũi') || cardText.includes('rạch gốc') || cardText.includes('cua cà mau') || cardText.includes('u minh') || cardText.includes('năm căn') || cardText.includes('thòi lòi') || cardText.includes('vọp') || cardText.includes('cá kèo');
        }

        return false;
    }

    function renderCards() {
        // Cập nhật hiển thị tag vùng miền và tỉnh thành trên từng thẻ món ăn
        foodCards.forEach(card => {
            const provinceTag = card.querySelector('.card-province-tag');
            const provinceText = card.querySelector('.province-tag-text');
            const cardProvince = (card.getAttribute('data-province') || '').trim();

            if (currentProvince && currentProvince !== 'all') {
                // Khi lọc thêm tỉnh thành: hiển thị CẢ 2 TAG (tag miền + tag tỉnh thành)
                if (provinceTag) {
                    const textToShow = cardProvince || currentProvince;
                    if (provinceText) provinceText.textContent = textToShow;
                    provinceTag.classList.remove('hidden');
                }
            } else {
                // Khi chỉ lọc theo miền (hoặc Tất cả): ẩn tag tỉnh thành, chỉ hiện tag vùng miền
                if (provinceTag) {
                    provinceTag.classList.add('hidden');
                }
            }
        });

        // Lọc danh sách món ăn
        const filteredCards = foodCards.filter(card => {
            const cardRegion = card.getAttribute('data-region') || '';
            const matchRegion = currentFilter === 'all' || cardRegion === currentFilter;
            const matchProvince = (currentFilter !== 'all') ? checkProvince(card, currentProvince) : true;
            const matchFlavor = checkFlavor(card, currentFlavor, currentFlavorSearch);
            return matchRegion && matchProvince && matchFlavor;
        });

        // Xóa thông báo trống nếu có
        const existingEmptyMsg = document.getElementById('food-empty-msg');
        if (existingEmptyMsg) existingEmptyMsg.remove();

        const totalPages = Math.ceil(filteredCards.length / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) currentPage = 1;

        // Ẩn toàn bộ thẻ trước
        foodCards.forEach(card => card.style.display = 'none');

        if (filteredCards.length === 0) {
            const emptyDiv = document.createElement('div');
            emptyDiv.id = 'food-empty-msg';
            emptyDiv.className = 'col-span-full py-16 text-center bg-white rounded-2xl shadow-xs border border-gray-100 p-8';
            const regionName = currentFilter === 'Bắc' ? 'Miền Bắc' : (currentFilter === 'Trung' ? 'Miền Trung' : (currentFilter === 'Nam' ? 'Miền Nam' : '3 Miền'));
            emptyDiv.innerHTML = `
                <div class="w-16 h-16 bg-red-50 text-primary rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-utensils"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800 mb-2">Chưa có món ăn nào phù hợp với bộ lọc</h4>
                <p class="text-xs text-gray-500 mb-5">Bạn có thể thử chọn tỉnh thành khác hoặc quay lại xem tất cả món ăn.</p>
                <button type="button" id="btn-reset-filters" class="bg-primary text-white text-xs font-bold px-5 py-2.5 rounded-full hover:bg-primaryDark transition-all shadow-xs">
                    Xem tất cả đặc sản ${regionName}
                </button>
            `;
            foodGrid.appendChild(emptyDiv);

            const resetBtn = document.getElementById('btn-reset-filters');
            if (resetBtn) {
                resetBtn.addEventListener('click', () => {
                    currentProvince = 'all';
                    resetProvinceButtons();
                    renderCards();
                });
            }
        } else {
            // Hiển thị các thẻ cho trang hiện tại
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageCards = filteredCards.slice(start, end);
            
            pageCards.forEach(card => {
                card.style.display = 'flex';
            });
        }

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        paginationContainer.innerHTML = '';
        if (totalPages <= 1) return;

        const goToPage = (page) => {
            if (page < 1 || page > totalPages || page === currentPage) return;
            currentPage = page;
            renderCards();
            const menuEl = document.getElementById('menu');
            if (menuEl) menuEl.scrollIntoView({ behavior: 'smooth' });
        };

        // 1. Nút Trước (Prev)
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.title = 'Trang trước';
        if (currentPage > 1) {
            prevBtn.className = 'w-10 h-10 rounded-full font-bold flex items-center justify-center text-sm bg-white border border-gray-200 text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-xs cursor-pointer';
            prevBtn.addEventListener('click', () => goToPage(currentPage - 1));
        } else {
            prevBtn.className = 'w-10 h-10 rounded-full font-bold flex items-center justify-center text-sm bg-gray-100 border border-gray-200 text-gray-300 cursor-not-allowed';
            prevBtn.disabled = true;
        }
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
        paginationContainer.appendChild(prevBtn);

        // 2. Tính toán danh sách trang hiển thị gọn gàng (dấu ...)
        const pages = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) pages.push(i);
        } else {
            if (currentPage <= 4) {
                pages.push(1, 2, 3, 4, 5, '...', totalPages);
            } else if (currentPage >= totalPages - 3) {
                pages.push(1, '...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
            } else {
                pages.push(1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages);
            }
        }

        // 3. Render các nút số trang và dấu ba chấm
        pages.forEach(p => {
            if (p === '...') {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'w-8 h-10 flex items-center justify-center text-gray-400 font-bold select-none text-sm';
                ellipsis.innerText = '...';
                paginationContainer.appendChild(ellipsis);
            } else {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerText = p;
                if (p === currentPage) {
                    btn.className = 'w-10 h-10 rounded-full font-bold flex items-center justify-center text-sm shadow-md transition-all bg-primary text-white cursor-default';
                } else {
                    btn.className = 'w-10 h-10 rounded-full font-semibold flex items-center justify-center text-sm bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-primary transition-all shadow-xs cursor-pointer';
                    btn.addEventListener('click', () => goToPage(p));
                }
                paginationContainer.appendChild(btn);
            }
        });

        // 4. Nút Sau (Next)
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.title = 'Trang sau';
        if (currentPage < totalPages) {
            nextBtn.className = 'w-10 h-10 rounded-full font-bold flex items-center justify-center text-sm bg-white border border-gray-200 text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-xs cursor-pointer';
            nextBtn.addEventListener('click', () => goToPage(currentPage + 1));
        } else {
            nextBtn.className = 'w-10 h-10 rounded-full font-bold flex items-center justify-center text-sm bg-gray-100 border border-gray-200 text-gray-300 cursor-not-allowed';
            nextBtn.disabled = true;
        }
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right text-xs"></i>';
        paginationContainer.appendChild(nextBtn);
    }

    // Sự kiện chuyển tab Miền
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => {
                b.classList.remove('bg-primary', 'text-white', 'shadow-md');
                b.classList.add('bg-white', 'text-gray-600', 'shadow-sm');
            });
            this.classList.remove('bg-white', 'text-gray-600', 'shadow-sm');
            this.classList.add('bg-primary', 'text-white', 'shadow-md');

            currentFilter = this.getAttribute('data-filter');

            // Hiển thị thanh tỉnh thành tương ứng với miền
            showProvinceContainer(currentFilter);
            currentProvince = 'all';
            resetProvinceButtons();

            currentPage = 1;
            renderCards();
        });
    });

    // Sự kiện bấm chọn Tỉnh thành
    provinceBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const parentContainer = this.closest('.province-container');
            const siblingBtns = parentContainer ? parentContainer.querySelectorAll('.province-btn') : provinceBtns;

            siblingBtns.forEach(b => {
                b.classList.remove('active', 'bg-primary', 'text-white', 'shadow-sm', 'font-semibold');
                b.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200', 'font-medium');
            });
            this.classList.add('active', 'bg-primary', 'text-white', 'shadow-sm', 'font-semibold');
            this.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200', 'font-medium');

            currentProvince = this.getAttribute('data-province');
            currentPage = 1;
            renderCards();
        });
    });

    function resetProvinceButtons() {
        provinceBtns.forEach(b => {
            if (b.getAttribute('data-province') === 'all') {
                b.classList.add('active', 'bg-primary', 'text-white', 'shadow-sm', 'font-semibold');
                b.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200', 'font-medium');
            } else {
                b.classList.remove('active', 'bg-primary', 'text-white', 'shadow-sm', 'font-semibold');
                b.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200', 'font-medium');
            }
        });
    }

    if (flavorSelect) {
        flavorSelect.addEventListener('change', function() {
            currentFlavor = this.value;
            currentPage = 1;
            renderCards();
        });
    }

    if (flavorSearch) {
        flavorSearch.addEventListener('input', function() {
            currentFlavorSearch = this.value.trim();
            currentPage = 1;
            renderCards();
        });
    }

    // Khởi tạo ban đầu
    renderCards();

    // Hỗ trợ tự động mở tab Miền nếu URL có tham số hoặc hash
    const urlParams = new URLSearchParams(window.location.search);
    const mienParam = (urlParams.get('mien') || urlParams.get('region') || '').toLowerCase();
    if (mienParam === 'bac' || mienParam === 'mien-bac') {
        const btnBac = document.querySelector('[data-filter="Bắc"]');
        if (btnBac) btnBac.click();
    } else if (mienParam === 'trung' || mienParam === 'mien-trung') {
        const btnTrung = document.querySelector('[data-filter="Trung"]');
        if (btnTrung) btnTrung.click();
    } else if (mienParam === 'nam' || mienParam === 'mien-nam') {
        const btnNam = document.querySelector('[data-filter="Nam"]');
        if (btnNam) btnNam.click();
    }
});
</script>
