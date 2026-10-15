<?php /** @var array $restaurants */ ?>
<div class="py-8 bg-white min-h-screen">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
        
        <!-- Header & Area Selector -->
        <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-100 pb-4 mb-4">
            <h1 class="text-[15px] font-bold text-primary">
                Giảm giá nhiều nhất - Hồ Chí Minh
            </h1>
            
            <div class="flex items-center gap-2 mt-4 md:mt-0 text-sm">
                <span class="text-gray-600">Chọn quận, huyện tại Hồ Chí Minh:</span>
                <button class="bg-gray-100 border border-gray-200 text-gray-700 px-3 py-1.5 rounded flex items-center gap-8 hover:bg-gray-200 transition-colors">
                    --Chọn Quận, Huyện-- <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
            </div>
        </div>

        <!-- Tags List -->
        <div class="flex flex-wrap items-center gap-4 mb-8 text-[13px] text-gray-600 border-b border-gray-100 pb-4">
            <?php 
                $tags = ['Nhà hàng', 'Lẩu', 'Buffet', 'Hải sản', 'Lẩu & Nướng', 'Quán Nhậu', 'Món chay', 'Đặt tiệc', 'Hàn Quốc', 'Nhật Bản', 'Món Âu', 'Món Việt', 'Món Thái', 'Món Trung Hoa', 'Tiệc cưới'];
                foreach($tags as $tag):
            ?>
                <a href="#" class="hover:text-primary transition-colors hover:underline"><?php echo $tag; ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Restaurant Grid (4 columns) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($restaurants as $res): ?>
                <div class="bg-white rounded-md overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-200 flex flex-col group cursor-pointer relative">
                    
                    <!-- Top: Image with overlay text -->
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($res['image']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?php echo htmlspecialchars($res['name']); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=800';">
                        
                        <!-- Dark gradient overlay for text -->
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent pt-12 pb-3 px-3 text-center text-white z-10 flex flex-col items-center justify-end">
                            <h3 class="font-bold text-[14px] leading-tight mb-1"><?php echo htmlspecialchars($res['name']); ?></h3>
                            <div class="flex items-center justify-center gap-2 mb-1 text-[11px]">
                                <div class="text-yellow-400">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-gray-300">$$<span class="text-gray-500">$$$</span></span>
                            </div>
                            <p class="text-[11px] text-gray-300 line-clamp-1 w-full truncate">
                                <?php echo htmlspecialchars($res['address']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Bottom: White box info -->
                    <div class="p-4 text-center flex flex-col items-center border-t-2 border-red-500/10">
                        <p class="text-primary font-bold text-[14px] mb-1">
                            <?php echo htmlspecialchars($res['promo']); ?>
                        </p>
                        <p class="text-[12px] text-gray-600 mb-4 line-clamp-1">
                            <?php echo htmlspecialchars($res['categories']); ?>
                        </p>
                        <button class="btn-book-now border border-gray-300 text-gray-800 bg-white hover:bg-gray-50 text-[13px] font-medium py-1.5 px-6 rounded transition-colors w-auto inline-block">
                            Đặt chỗ ngay
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Float Chat Button Simulation -->
        <div class="fixed right-4 top-1/2 transform -translate-y-1/2 flex flex-col gap-2 z-50">
            <button class="w-10 h-10 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center hover:bg-gray-50 transition">
                <i class="fas fa-chevron-up text-gray-500 text-xs"></i>
            </button>
        </div>
        
    </div>
</div>
