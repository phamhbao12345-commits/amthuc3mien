<?php
/**
 * @var array $baiViet
 * @var array $tinLienQuan
 * @var array $tinXemNhieu
 */
?>
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1200px]">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6 flex-wrap">
            <a href="<?php echo BASE_URL; ?>" class="hover:text-primary transition-colors flex items-center gap-1">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <i class="fas fa-chevron-right text-[9px] text-gray-400"></i>
            <a href="<?php echo BASE_URL; ?>tin-tuc" class="hover:text-primary transition-colors">Tin tức & Blog</a>
            <i class="fas fa-chevron-right text-[9px] text-gray-400"></i>
            <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode($baiViet['danh_muc']); ?>" class="hover:text-primary transition-colors">
                <?php echo htmlspecialchars($baiViet['danh_muc']); ?>
            </a>
            <i class="fas fa-chevron-right text-[9px] text-gray-400"></i>
            <span class="text-gray-800 font-semibold truncate max-w-xs"><?php echo htmlspecialchars($baiViet['tieu_de']); ?></span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Cột nội dung bài viết chính -->
            <article class="flex-1 bg-white rounded-2xl p-6 sm:p-8 md:p-10 shadow-xs border border-gray-100">
                
                <!-- Category Badge -->
                <div class="mb-3">
                    <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode($baiViet['danh_muc']); ?>" class="inline-block bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full hover:bg-red-100 transition-colors">
                        <?php echo htmlspecialchars($baiViet['danh_muc']); ?>
                    </a>
                </div>

                <!-- Tiêu đề -->
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 leading-tight mb-4">
                    <?php echo htmlspecialchars($baiViet['tieu_de']); ?>
                </h1>

                <!-- Thông tin tác giả, ngày đăng, lượt xem -->
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 pb-6 mb-6 border-b border-gray-100">
                    <span class="flex items-center gap-1.5 font-medium text-gray-700">
                        <i class="fas fa-user-edit text-primary"></i> <?php echo htmlspecialchars($baiViet['tac_gia'] ?? 'Ban biên tập'); ?>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="far fa-calendar-alt text-gray-400"></i> <?php echo !empty($baiViet['created_at']) ? date('d/m/Y H:i', strtotime($baiViet['created_at'])) : ''; ?>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="far fa-eye text-gray-400"></i> <?php echo number_format($baiViet['luot_xem']); ?> lượt xem
                    </span>
                </div>

                <!-- Tóm tắt Sapo -->
                <?php if (!empty($baiViet['tom_tat'])): ?>
                <div class="bg-amber-50/70 border-l-4 border-amber-500 p-4 rounded-r-xl mb-6 text-sm text-gray-700 font-medium italic leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($baiViet['tom_tat'])); ?>
                </div>
                <?php endif; ?>

                <!-- Hình ảnh bài viết -->
                <?php if (!empty($baiViet['hinh_anh'])): ?>
                <div class="rounded-xl overflow-hidden mb-8 shadow-xs border border-gray-100">
                    <img src="<?php echo htmlspecialchars($baiViet['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($baiViet['tieu_de']); ?>" class="w-full max-h-[460px] object-cover" onerror="this.src='https://via.placeholder.com/800x450';">
                </div>
                <?php endif; ?>

                <!-- Nội dung bài viết -->
                <div class="prose max-w-none text-gray-800 text-[15px] sm:text-base leading-relaxed space-y-4 font-normal">
                    <?php 
                    // Nếu nội dung đã có chứa thẻ HTML thì hiển thị, nếu là text thuần thì nl2br
                    if (strpos($baiViet['noi_dung'], '<p') !== false || strpos($baiViet['noi_dung'], '<h') !== false) {
                        echo $baiViet['noi_dung'];
                    } else {
                        echo nl2br(htmlspecialchars($baiViet['noi_dung']));
                    }
                    ?>
                </div>

                <!-- Thẻ tag & Chia sẻ -->
                <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 uppercase">Chủ đề:</span>
                        <a href="<?php echo BASE_URL; ?>tin-tuc?danh_muc=<?php echo urlencode($baiViet['danh_muc']); ?>" class="text-xs bg-gray-100 text-gray-700 hover:text-primary px-3 py-1 rounded-md font-medium transition-colors">
                            #<?php echo htmlspecialchars($baiViet['danh_muc']); ?>
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500">Chia sẻ:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs hover:opacity-90">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <button onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết bài viết!');" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs hover:bg-gray-200" title="Sao chép link">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>

                <!-- Bài viết liên quan -->
                <?php if (!empty($tinLienQuan)): ?>
                <div class="mt-12 pt-8 border-t border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="w-2 h-5 bg-primary rounded-full inline-block"></span>
                        Bài viết cùng chuyên mục
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <?php foreach ($tinLienQuan as $lq): ?>
                        <a href="<?php echo BASE_URL; ?>tin-tuc/<?php echo htmlspecialchars($lq['slug']); ?>" class="group block">
                            <div class="h-32 rounded-lg overflow-hidden mb-2.5 bg-gray-100">
                                <img src="<?php echo htmlspecialchars($lq['hinh_anh']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="<?php echo htmlspecialchars($lq['tieu_de']); ?>" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=600';">
                            </div>
                            <h4 class="text-xs font-bold text-gray-800 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                <?php echo htmlspecialchars($lq['tieu_de']); ?>
                            </h4>
                            <span class="text-[10px] text-gray-400 mt-1 block">
                                <i class="far fa-calendar-alt mr-1"></i> <?php echo !empty($lq['created_at']) ? date('d/m/Y', strtotime($lq['created_at'])) : ''; ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </article>

            <!-- Cột bên phải: Đọc nhiều nhất & Banner -->
            <aside class="w-full lg:w-[320px] shrink-0 space-y-6">
                <!-- Đọc nhiều nhất -->
                <?php if (!empty($tinXemNhieu)): ?>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <h4 class="font-bold text-sm text-gray-800 pb-3 mb-3 border-b border-gray-100 flex items-center justify-between">
                        <span><i class="fas fa-fire text-orange-500 mr-1.5"></i> Đọc nhiều nhất</span>
                    </h4>
                    <div class="space-y-3">
                        <?php foreach ($tinXemNhieu as $idx => $xm): ?>
                        <a href="<?php echo BASE_URL; ?>tin-tuc/<?php echo htmlspecialchars($xm['slug']); ?>" class="flex gap-2.5 group items-start hover:bg-red-50/50 p-1.5 rounded-lg transition-colors">
                            <span class="w-5 h-5 rounded-full bg-red-50 text-primary flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                <?php echo $idx + 1; ?>
                            </span>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-xs font-bold text-gray-800 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                    <?php echo htmlspecialchars($xm['tieu_de']); ?>
                                </h5>
                                <span class="text-[10px] text-gray-400 mt-0.5 block">
                                    <i class="far fa-eye mr-0.5"></i> <?php echo number_format($xm['luot_xem']); ?>
                                </span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Nút quay lại Blog -->
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                    <a href="<?php echo BASE_URL; ?>tin-tuc" class="w-full inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs py-2.5 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i> Xem tất cả bài viết
                    </a>
                </div>
            </aside>
            
        </div>
    </div>
</div>

