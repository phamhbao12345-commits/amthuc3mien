<div class="py-6 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm font-medium text-gray-500 mb-5">
            <a href="<?php echo BASE_URL; ?>" class="hover:text-primary transition-colors font-bold flex items-center gap-1.5 text-gray-800">
                <i class="fas fa-home text-xs"></i> Trang chủ
            </a> 
            <span class="mx-2 text-gray-300">/</span> 
            <span class="text-primary font-bold">Bộ sưu tập ẩm thực</span>
        </nav>

        <!-- Main Content Area -->
        <div class="bg-white p-4 md:p-6 lg:p-8 rounded-2xl shadow-sm border border-gray-100">
            
            <!-- Hero Carousel Slider Banner -->
            <div id="carousel-container" class="relative w-full h-[280px] sm:h-[350px] md:h-[420px] overflow-hidden rounded-2xl mb-8 group shadow-md">
                <?php foreach ($banners as $idx => $b): ?>
                <div class="banner-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?php echo $idx === 0 ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none'; ?>" data-index="<?php echo $idx; ?>">
                    <img src="<?php echo htmlspecialchars($b['image']); ?>" class="w-full h-full object-cover transform scale-100 group-hover:scale-105 transition-transform duration-1000" alt="<?php echo htmlspecialchars($b['title']); ?>">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-black/20 flex flex-col items-center justify-center text-white text-center px-4">
                        <span class="bg-primary/90 text-white text-[11px] sm:text-xs md:text-sm uppercase font-bold tracking-widest px-3 py-1 rounded-full mb-2 sm:mb-3 backdrop-blur-sm">
                            <i class="fas fa-fire mr-1"></i> <?php echo htmlspecialchars($b['promo']); ?>
                        </span>
                        <h2 class="text-xl sm:text-3xl md:text-5xl font-black mb-2 sm:mb-3 uppercase tracking-tight drop-shadow-md">
                            <?php echo htmlspecialchars($b['title']); ?>
                        </h2>
                        <p class="text-xs sm:text-sm md:text-base font-normal max-w-2xl mb-4 sm:mb-6 text-gray-200 drop-shadow line-clamp-2 sm:line-clamp-none">
                            <?php echo htmlspecialchars($b['subtitle']); ?>
                        </p>
                        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                            <button type="button" class="btn-book-now bg-primary hover:bg-primaryDark text-white font-bold py-2 sm:py-3 px-4 sm:px-8 rounded-full shadow-lg transition-all transform hover:scale-105 flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                                <i class="fas fa-calendar-check"></i> ĐẶT BÀN NGAY
                            </button>
                            <a href="<?php echo BASE_URL; ?>gan-ban" class="bg-white/20 hover:bg-white text-white hover:text-gray-900 border border-white/50 font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-full transition-all backdrop-blur-sm flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                                <i class="fas fa-map-marker-alt"></i> Xem bản đồ
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Controls -->
                <button type="button" id="slider-prev" class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-11 sm:h-11 bg-white/80 hover:bg-white text-gray-800 flex items-center justify-center rounded-full shadow-lg transition z-20 hover:scale-110">
                    <i class="fas fa-chevron-left text-xs sm:text-base"></i>
                </button>
                <button type="button" id="slider-next" class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-11 sm:h-11 bg-white/80 hover:bg-white text-gray-800 flex items-center justify-center rounded-full shadow-lg transition z-20 hover:scale-110">
                    <i class="fas fa-chevron-right text-xs sm:text-base"></i>
                </button>

                <!-- Indicator Dots -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
                    <?php foreach ($banners as $idx => $b): ?>
                    <button type="button" class="slider-dot w-3 h-3 rounded-full transition-all <?php echo $idx === 0 ? 'bg-primary w-8' : 'bg-white/60 hover:bg-white'; ?>" data-index="<?php echo $idx; ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Header Title & Description -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2">
                        <i class="fas fa-layer-group text-primary"></i> Khám phá Bộ Sưu Tập Ẩm Thực
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Tuyển chọn các địa điểm ẩm thực, quán ăn ngon và nhà hàng nổi tiếng được yêu thích nhất 3 miền.
                    </p>
                </div>
                
                <!-- Quick Search in Collections -->
                <div class="relative w-full md:w-72">
                    <input type="text" id="collection-search-input" placeholder="Tìm nhanh bộ sưu tập..." 
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 pl-10 text-sm focus:bg-white focus:border-primary focus:ring-2 focus:ring-red-100 outline-none transition-all">
                    <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                </div>
            </div>

            <!-- Filter Buttons Bar (Tags) -->
            <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-none" id="filter-tabs">
                <button type="button" class="filter-tab active-filter px-5 py-2.5 rounded-full text-xs md:text-sm font-bold transition-all shadow-sm shrink-0 bg-primary text-white" data-filter="all">
                    <i class="fas fa-th-large mr-1.5"></i> Tất cả
                </button>
                <button type="button" class="filter-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all shrink-0 bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="quan">
                    <i class="fas fa-map-marker-alt mr-1.5 text-red-500"></i> Theo Quận (TP.HCM)
                </button>
                <button type="button" class="filter-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all shrink-0 bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="lau-nuong">
                    <i class="fas fa-fire mr-1.5 text-orange-500"></i> Lẩu & Nướng
                </button>
                <button type="button" class="filter-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all shrink-0 bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="hai-san">
                    <i class="fas fa-fish mr-1.5 text-blue-500"></i> Hải Sản
                </button>
                <button type="button" class="filter-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all shrink-0 bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="chay">
                    <i class="fas fa-leaf mr-1.5 text-green-500"></i> Món Chay
                </button>
                <button type="button" class="filter-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all shrink-0 bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="buffet">
                    <i class="fas fa-utensils mr-1.5 text-purple-500"></i> Buffet
                </button>
                <button type="button" class="filter-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all shrink-0 bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="dac-san">
                    <i class="fas fa-award mr-1.5 text-amber-500"></i> Đặc Sản 3 Miền
                </button>
            </div>

            <!-- Collections Grid -->
            <div id="collections-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
                <?php foreach ($collections as $col): ?>
                <a href="<?php echo htmlspecialchars($col['link']); ?>" 
                   class="collection-card group block relative h-[250px] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1.5 border border-gray-100"
                   data-category="<?php echo htmlspecialchars($col['category']); ?>"
                   data-title="<?php echo htmlspecialchars(mb_strtolower($col['title'], 'UTF-8')); ?>">
                    
                    <!-- Background Image -->
                    <img src="<?php echo htmlspecialchars($col['image']); ?>" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                         alt="<?php echo htmlspecialchars($col['title']); ?>"
                         loading="lazy">
                    
                    <!-- Category Badge (Top Left) -->
                    <div class="absolute top-3.5 left-3.5 z-20 bg-black/60 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full border border-white/20">
                        <?php echo htmlspecialchars($col['category_name']); ?>
                    </div>

                    <!-- Red Badge with Restaurant Count (Top Right) -->
                    <div class="absolute top-3.5 right-3.5 z-20 bg-primary text-white w-12 h-12 rounded-full flex flex-col items-center justify-center shadow-lg text-center leading-tight transition-transform duration-300 group-hover:scale-110">
                        <span class="font-extrabold text-[15px]"><?php echo $col['count']; ?></span>
                        <span class="text-[9px] uppercase tracking-tighter">Địa điểm</span>
                    </div>

                    <!-- Dark Gradient Overlay -->
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/95 via-black/60 to-transparent pt-16 pb-4 px-4 text-white z-10 flex flex-col justify-end h-full transition-colors">
                        <h3 class="font-bold text-[16px] leading-snug drop-shadow-md group-hover:text-red-300 transition-colors">
                            <?php echo htmlspecialchars($col['title']); ?>
                        </h3>
                        <p class="text-xs text-gray-300 mt-1 line-clamp-1 opacity-90 group-hover:opacity-100">
                            <?php echo htmlspecialchars($col['description']); ?>
                        </p>
                        <div class="mt-2.5 flex items-center text-xs font-semibold text-white/80 group-hover:text-white">
                            <span>Khám phá ngay</span>
                            <i class="fas fa-arrow-right ml-1.5 text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Empty State when filtering finds nothing -->
            <div id="no-collections-found" class="hidden py-16 text-center">
                <i class="fas fa-search text-gray-300 text-5xl mb-3"></i>
                <h4 class="text-lg font-bold text-gray-700">Không tìm thấy bộ sưu tập phù hợp</h4>
                <p class="text-sm text-gray-500 mt-1">Vui lòng thử chọn bộ lọc khác hoặc nhập từ khóa tìm kiếm mới.</p>
                <button type="button" id="btn-reset-filters" class="mt-4 px-5 py-2 bg-primary text-white text-sm font-bold rounded-full hover:bg-primaryDark transition">
                    Xem tất cả bộ sưu tập
                </button>
            </div>

            <!-- Interactive Pagination -->
            <div id="pagination-wrapper" class="flex justify-center items-center gap-2 mb-14 pt-4 border-t border-gray-100">
                <button type="button" class="page-btn active-page w-10 h-10 rounded-xl bg-primary text-white font-bold flex items-center justify-center text-sm shadow transition" data-page="1">1</button>
                <button type="button" class="page-btn w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold flex items-center justify-center text-sm hover:bg-red-50 hover:text-primary hover:border-red-200 transition" data-page="2">2</button>
                <button type="button" id="btn-show-all-collections" class="px-4 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-sm transition ml-2">
                    Xem tất cả (12)
                </button>
            </div>

            <!-- Instruction Infographic Section -->
            <div class="border-t border-gray-100 pt-10 pb-6 text-center bg-gray-50/70 rounded-2xl p-6">
                <div class="inline-flex items-center gap-2 bg-red-100 text-primary px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fas fa-shield-alt"></i> Tiện ích thông minh
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-2">Quy trình đặt bàn nhanh chóng & Ưu đãi</h3>
                <p class="text-gray-600 mb-8 text-sm max-w-xl mx-auto">Chỉ 4 bước đơn giản để trải nghiệm ẩm thực tinh hoa 3 miền cùng hàng trăm khuyến mãi giảm giá hấp dẫn.</p>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 max-w-4xl mx-auto">
                    <!-- Step 1 -->
                    <a href="<?php echo BASE_URL; ?>" class="flex flex-col items-center p-4 bg-white rounded-xl shadow-xs hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-globe-asia"></i>
                        </div>
                        <span class="text-xs font-black text-amber-600 uppercase tracking-wider mb-1">Bước 1</span>
                        <p class="text-sm font-bold text-gray-800 text-center">Khám phá món & quán</p>
                        <p class="text-xs text-gray-500 text-center mt-1">Duyệt bộ sưu tập hoặc tìm theo khu vực</p>
                    </a>
                    
                    <!-- Step 2 -->
                    <a href="<?php echo BASE_URL; ?>gan-ban" class="flex flex-col items-center p-4 bg-white rounded-xl shadow-xs hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <span class="text-xs font-black text-blue-600 uppercase tracking-wider mb-1">Bước 2</span>
                        <p class="text-sm font-bold text-gray-800 text-center">Chọn quán gần bạn</p>
                        <p class="text-xs text-gray-500 text-center mt-1">Định vị GPS trong bán kính 5km</p>
                    </a>

                    <!-- Step 3 -->
                    <div class="btn-book-now cursor-pointer flex flex-col items-center p-4 bg-white rounded-xl shadow-xs hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-2xl bg-red-50 text-primary flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <span class="text-xs font-black text-primary uppercase tracking-wider mb-1">Bước 3</span>
                        <p class="text-sm font-bold text-gray-800 text-center">Đặt bàn online</p>
                        <p class="text-xs text-gray-500 text-center mt-1">Chọn thời gian, số người & nhận bàn</p>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex flex-col items-center p-4 bg-white rounded-xl shadow-xs hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-500 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-smile-beam"></i>
                        </div>
                        <span class="text-xs font-black text-green-600 uppercase tracking-wider mb-1">Bước 4</span>
                        <p class="text-sm font-bold text-gray-800 text-center">Thưởng thức món ngon</p>
                        <p class="text-xs text-gray-500 text-center mt-1">Đến nhà hàng giữ chỗ & hưởng ưu đãi</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JavaScript Điều Khiển Slider & Bộ Lọc Tương Tác Của Trang Bộ Sưu Tập -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. CAROUSEL SLIDER LOGIC
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.getElementById('slider-prev');
    const nextBtn = document.getElementById('slider-next');
    let currentSlide = 0;
    let slideInterval = null;

    function showSlide(index) {
        if (slides.length === 0) return;
        currentSlide = (index + slides.length) % slides.length;
        
        slides.forEach((slide, idx) => {
            if (idx === currentSlide) {
                slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                slide.classList.add('opacity-100', 'pointer-events-auto', 'z-10');
            } else {
                slide.classList.remove('opacity-100', 'pointer-events-auto', 'z-10');
                slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
            }
        });

        dots.forEach((dot, idx) => {
            if (idx === currentSlide) {
                dot.classList.add('bg-primary', 'w-8');
                dot.classList.remove('bg-white/60');
            } else {
                dot.classList.remove('bg-primary', 'w-8');
                dot.classList.add('bg-white/60');
            }
        });
    }

    function startAutoSlide() {
        stopAutoSlide();
        slideInterval = setInterval(() => {
            showSlide(currentSlide + 1);
        }, 5000);
    }

    function stopAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            showSlide(currentSlide - 1);
            startAutoSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            showSlide(currentSlide + 1);
            startAutoSlide();
        });
    }

    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const idx = parseInt(this.getAttribute('data-index') || '0');
            showSlide(idx);
            startAutoSlide();
        });
    });

    const carouselContainer = document.getElementById('carousel-container');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', stopAutoSlide);
        carouselContainer.addEventListener('mouseleave', startAutoSlide);
    }

    startAutoSlide();

    // 2. BỘ LỌC TƯƠNG TÁC (FILTER TABS)
    const filterTabs = document.querySelectorAll('.filter-tab');
    const collectionCards = document.querySelectorAll('.collection-card');
    const searchInput = document.getElementById('collection-search-input');
    const noResultEl = document.getElementById('no-collections-found');
    const paginationWrapper = document.getElementById('pagination-wrapper');

    let currentFilter = 'all';
    let searchQuery = '';

    function applyFilters() {
        let visibleCount = 0;

        collectionCards.forEach(card => {
            const category = card.getAttribute('data-category');
            const title = card.getAttribute('data-title') || '';

            const matchesCategory = (currentFilter === 'all') || (category === currentFilter);
            const matchesSearch = (searchQuery === '') || title.includes(searchQuery);

            if (matchesCategory && matchesSearch) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (visibleCount === 0) {
            if (noResultEl) noResultEl.classList.remove('hidden');
            if (paginationWrapper) paginationWrapper.classList.add('hidden');
        } else {
            if (noResultEl) noResultEl.classList.add('hidden');
            if (paginationWrapper) paginationWrapper.classList.remove('hidden');
        }
    }

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => {
                t.classList.remove('bg-primary', 'text-white', 'shadow-sm');
                t.classList.add('bg-gray-100', 'text-gray-700');
            });

            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-primary', 'text-white', 'shadow-sm');

            currentFilter = this.getAttribute('data-filter') || 'all';
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchQuery = this.value.trim().toLowerCase();
            applyFilters();
        });
    }

    const resetBtn = document.getElementById('btn-reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            currentFilter = 'all';
            searchQuery = '';
            if (searchInput) searchInput.value = '';
            
            filterTabs.forEach((t, i) => {
                if (i === 0) {
                    t.classList.remove('bg-gray-100', 'text-gray-700');
                    t.classList.add('bg-primary', 'text-white');
                } else {
                    t.classList.remove('bg-primary', 'text-white');
                    t.classList.add('bg-gray-100', 'text-gray-700');
                }
            });
            applyFilters();
        });
    }

    // 3. XỬ LÝ PHÂN TRANG (PAGINATION)
    const pageBtns = document.querySelectorAll('.page-btn');
    const showAllBtn = document.getElementById('btn-show-all-collections');

    function paginate(page) {
        const itemsPerPage = 8;
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        collectionCards.forEach((card, index) => {
            if (currentFilter === 'all' && searchQuery === '') {
                if (index >= startIndex && index < endIndex) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            }
        });
    }

    pageBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            pageBtns.forEach(b => {
                b.classList.remove('bg-primary', 'text-white', 'shadow');
                b.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            });
            this.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            this.classList.add('bg-primary', 'text-white', 'shadow');

            const page = parseInt(this.getAttribute('data-page') || '1');
            paginate(page);
        });
    });

    if (showAllBtn) {
        showAllBtn.addEventListener('click', () => {
            collectionCards.forEach(card => card.classList.remove('hidden'));
            pageBtns.forEach(b => {
                b.classList.remove('bg-primary', 'text-white');
                b.classList.add('bg-white', 'text-gray-600');
            });
        });
    }

    // Khởi tạo trang 1
    paginate(1);
});
</script>
