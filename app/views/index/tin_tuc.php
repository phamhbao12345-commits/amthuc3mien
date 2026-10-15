<?php
/**
 * @var array $danhSachTinTuc
 * @var array $tinNoiBat
 * @var array $tinXemNhieu
 * @var array $danhMucList
 * @var string $currentDanhMuc
 * @var string $keyword
 */
?>
<div class="bg-gray-50 min-h-screen pb-16">
    <!-- Hero Banner -->
    <div class="w-full relative bg-gradient-to-r from-red-950 via-stone-900 to-red-950 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=1920&h=400" 
             class="w-full h-[220px] md:h-[320px] object-cover opacity-50" 
             alt="Banner Tin tức & Blog"
             onerror="this.style.display='none';">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
        
        <div class="absolute inset-0 flex items-center justify-center container mx-auto px-4">
            <div class="text-white text-center max-w-2xl px-4">
                <span class="inline-block bg-red-600 text-white text-xs uppercase font-extrabold px-3.5 py-1 rounded-full mb-3 tracking-widest shadow-sm">
                    Góc Ẩm Thực & Cẩm Nang
                </span>
                <h2 class="text-2xl md:text-4xl font-black mb-3 tracking-tight drop-shadow-md">
                    Khám phá Tin tức, Món ngon & Xu hướng F&B
                </h2>
                <p class="text-xs md:text-sm text-gray-200 font-light max-w-xl mx-auto leading-relaxed">
                    Chia sẻ công thức nấu nướng chuẩn vị, kinh nghiệm mở quán và địa điểm ẩm thực hấp dẫn nhất 3 miền.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px] mt-8 flex flex-col lg:flex-row gap-8">
        
        <!-- Left Column (Main Content) -->
        <div class="flex-1 min-w-0">


            <!-- Header thông báo khi tìm kiếm hoặc lọc -->
            <?php if (!empty($keyword) || !empty($currentDanhMuc)): ?>
                <div class="mb-6 bg-red-50/80 border border-red-200 rounded-xl p-4 flex items-center justify-between shadow-sm">
                    <div class="text-sm text-gray-800">
                        Đang lọc bài viết: 
                        <?php if(!empty($currentDanhMuc)): ?><span class="font-bold text-primary mr-2">Danh mục: <?php echo htmlspecialchars($currentDanhMuc); ?></span><?php endif; ?>
                        <?php if(!empty($keyword)): ?><span class="font-bold text-primary">Từ khóa: "<?php echo htmlspecialchars($keyword); ?>"</span><?php endif; ?>
                    </div>
                    <a href="<?php echo BASE_URL; ?>tin-tuc" class="text-xs text-red-600 hover:underline font-bold flex items-center gap-1">
                        <i class="fas fa-times-circle"></i> Xóa bộ lọc
                    </a>
                </div>
            <?php endif; ?>

            <!-- Bài viết Xem nhiều nhất / Nổi bật -->
            <?php if (!empty($tinNoiBat) && empty($keyword) && empty($currentDanhMuc)): ?>
            <div class="bg-gradient-to-b from-red-50/60 to-white rounded-2xl p-5 sm:p-6 mb-8 border border-red-100 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-2.5 h-6 bg-primary rounded-full inline-block"></span>
                        Bài viết nổi bật & Đọc nhiều nhất
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($tinNoiBat as $nb): ?>
                    <a href="<?php echo BASE_URL; ?>tin-tuc/<?php echo htmlspecialchars($nb['slug']); ?>" class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all group flex flex-col border border-gray-200 hover:border-red-300">
                        <div class="h-48 overflow-hidden relative bg-gray-100">
                            <img src="<?php echo htmlspecialchars($nb['hinh_anh']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                                 alt="<?php echo htmlspecialchars($nb['tieu_de']); ?>" 
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=600';">
                            <span class="absolute top-3 left-3 bg-red-600 text-white text-[11px] font-bold px-2.5 py-1 rounded-md shadow-sm">
                                <?php echo htmlspecialchars($nb['danh_muc']); ?>
                            </span>
                        </div>
                        <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-[15px] sm:text-[16px] text-gray-900 leading-snug group-hover:text-primary transition-colors mb-2 line-clamp-2">
                                    <?php echo htmlspecialchars($nb['tieu_de']); ?>
                                </h4>
                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mb-3">
                                    <?php echo htmlspecialchars($nb['tom_tat']); ?>
                                </p>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-gray-400 pt-3 border-t border-gray-100">
                                <span><i class="far fa-user mr-1"></i> <?php echo htmlspecialchars($nb['tac_gia']); ?></span>
                                <span><i class="far fa-eye mr-1"></i> <?php echo number_format($nb['luot_xem']); ?></span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Danh sách bài viết chính -->
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    <?php echo !empty($currentDanhMuc) ? htmlspecialchars($currentDanhMuc) : 'Tất cả bài viết mới nhất'; ?>
                    <span class="text-xs font-normal text-gray-500 ml-1">(<?php echo count($danhSachTinTuc); ?> bài)</span>
                </h3>
            </div>

            <?php if (!empty($danhSachTinTuc)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($danhSachTinTuc as $art): ?>
                <a href="<?php echo BASE_URL; ?>tin-tuc/<?php echo htmlspecialchars($art['slug']); ?>" class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all group flex flex-col border border-gray-200 hover:border-red-300">
                    <div class="h-44 overflow-hidden relative bg-gray-100">
                        <img src="<?php echo htmlspecialchars($art['hinh_anh']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                             alt="<?php echo htmlspecialchars($art['tieu_de']); ?>" 
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=600';">
                        <span class="absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">
                            <?php echo htmlspecialchars($art['danh_muc']); ?>
                        </span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-[14px] text-gray-900 leading-snug group-hover:text-primary transition-colors mb-2 line-clamp-2">
                                <?php echo htmlspecialchars($art['tieu_de']); ?>
                            </h4>
                            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mb-3">
                                <?php echo htmlspecialchars($art['tom_tat']); ?>
                            </p>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 pt-3 border-t border-gray-100 mt-auto">
                            <span class="truncate mr-2"><i class="far fa-calendar-alt mr-1"></i> <?php echo !empty($art['created_at']) ? date('d/m/Y', strtotime($art['created_at'])) : ''; ?></span>
                            <span class="shrink-0"><i class="far fa-eye mr-1"></i> <?php echo number_format($art['luot_xem']); ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-xl p-12 text-center border border-gray-100 shadow-sm">
                <i class="fas fa-search text-4xl text-gray-300 mb-3 block"></i>
                <h4 class="text-base font-bold text-gray-700 mb-1">Không tìm thấy bài viết phù hợp</h4>
                <p class="text-xs text-gray-500 mb-4">Hãy thử tìm kiếm với từ khóa khác hoặc duyệt theo chuyên mục ẩm thực bên trên.</p>
                <a href="<?php echo BASE_URL; ?>tin-tuc" class="inline-block bg-primary text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-primaryDark transition-colors shadow-sm">Xem tất cả bài viết</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column (Sidebar) -->
        <div class="w-full lg:w-[320px] shrink-0 space-y-6">
            <!-- Search Box -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <form action="<?php echo BASE_URL; ?>tin-tuc" method="GET" class="relative">
                    <?php if (!empty($currentDanhMuc)): ?>
                        <input type="hidden" name="danh_muc" value="<?php echo htmlspecialchars($currentDanhMuc); ?>">
                    <?php endif; ?>
                    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword ?? ''); ?>" 
                           placeholder="Tìm công thức, kinh nghiệm, mẹo..." 
                           class="w-full border border-gray-300 rounded-lg pl-3 pr-10 py-2.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-red-100 transition-all">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors" aria-label="Tìm kiếm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Bài viết đọc nhiều nhất -->
            <?php if (!empty($tinXemNhieu)): ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 font-bold text-gray-800 text-[14px] flex items-center justify-between">
                    <span><i class="fas fa-fire text-orange-500 mr-1.5"></i> Đọc nhiều nhất</span>
                </div>
                <div class="p-3 divide-y divide-gray-100">
                    <?php foreach ($tinXemNhieu as $idx => $xm): ?>
                    <a href="<?php echo BASE_URL; ?>tin-tuc/<?php echo htmlspecialchars($xm['slug']); ?>" class="flex gap-3 py-3 group items-center hover:bg-red-50/40 px-2 rounded-lg transition-colors">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-primary flex items-center justify-center text-xs font-black shrink-0">
                            <?php echo $idx + 1; ?>
                        </span>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-xs font-bold text-gray-800 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                <?php echo htmlspecialchars($xm['tieu_de']); ?>
                            </h5>
                            <span class="text-[10px] text-gray-400 mt-1 block">
                                <i class="far fa-eye mr-0.5"></i> <?php echo number_format($xm['luot_xem']); ?> lượt xem
                            </span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tags Widget -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="bg-primary text-white font-bold px-4 py-3 text-[14px]">
                    <i class="fas fa-tags mr-1"></i> Chủ đề phổ biến:
                </div>
                <div class="p-4 flex flex-wrap gap-2">
                    <?php
                        $sidebarTags = [
                            'Kinh doanh nhà hàng', 'Kinh nghiệm mở quán', 'Thực đơn tiệc', 
                            'Đặc sản miền Tây', 'Mẹo nấu lẩu ngon', 'Bí quyết pha nước chấm',
                            'Quán ăn Sài Gòn', 'Công thức món ngon', 'Rượu vang & ẩm thực'
                        ];
                        foreach ($sidebarTags as $tag):
                    ?>
                    <a href="<?php echo BASE_URL; ?>tin-tuc?keyword=<?php echo urlencode($tag); ?>" class="bg-gray-100 text-gray-700 hover:bg-red-50 hover:text-primary transition-colors px-2.5 py-1 text-[11px] rounded-md font-medium border border-gray-200">
                        #<?php echo $tag; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Banner Đặt Bàn Khuyến Mãi -->
            <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-xl p-5 text-white text-center shadow-sm">
                <i class="fas fa-utensils text-3xl mb-3 text-red-200"></i>
                <h4 class="font-bold text-base mb-1">Khám phá Đặc sản 3 Miền</h4>
                <p class="text-xs text-red-100 mb-4">Hàng trăm quán ăn ngon chuẩn vị trên toàn quốc đang chờ bạn khám phá!</p>
                <a href="<?php echo BASE_URL; ?>gan-ban" class="inline-block bg-white text-primary font-bold text-xs px-5 py-2 rounded-full hover:bg-gray-100 transition-colors shadow-sm">
                    Tìm quán gần bạn
                </a>
            </div>
        </div>
        
    </div>
</div>
