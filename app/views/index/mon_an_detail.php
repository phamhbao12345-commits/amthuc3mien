<?php
/**
 * @var string $title
 * @var array $monAn
 * @var array $goiYQuanAn
 * @var array $monGoiY
 */

$slugHienTai = $monAn['slug'] ?? '';
$danhSachGoiY = !empty($goiYQuanAn) ? $goiYQuanAn : [];
$danhSachMonGoiY = !empty($monGoiY) ? $monGoiY : [];
$tinhThanhHienTai = !empty($monAn['tinh_thanh']) ? $monAn['tinh_thanh'] : ('Miền ' . ($monAn['ten_vung'] ?? 'Nam'));

// Làm sạch mô tả món ăn, loại bỏ văn bản rác cào từ internet
$moTaClean = $monAn['mo_ta_ngan'] ?? '';
$moTaClean = preg_replace('/(&gt;|>){2,}\s*Xem thêm:.*$/uis', '', $moTaClean);
$moTaClean = preg_replace('/\(Ngu[oồ]n:.*?\)/ui', '', $moTaClean);
$moTaClean = trim($moTaClean);

// Xử lý nguyên liệu chuẩn nếu database đang để placeholder
$rawNguyenLieu = trim($monAn['nguyen_lieu'] ?? '');
if (empty($rawNguyenLieu) || $rawNguyenLieu === 'Nguyên liệu tùy chọn' || strpos($rawNguyenLieu, 'tùy chọn') !== false) {
    if ($slugHienTai === 'banh-xeo-can-tho' || strpos($monAn['ten_mon'], 'Bánh xèo') !== false) {
        $rawNguyenLieu = "• Phần vỏ bánh: 400g bột gạo (hoặc bột bánh xèo miền Tây), 100g bột chiên giòn, 1 muỗng cà phê bột nghệ, 500ml nước cốt dừa, 400ml nước lọc, 1 quả trứng gà, hành lá cắt nhỏ, 1/2 muỗng cà phê muối.\n• Phần nhân bánh: 300g tôm sông tươi, 300g thịt ba rọi thái mỏng, 150g đậu xanh bóc vỏ (đã hấp chín), 200g giá đỗ sạch, 150g củ hũ dừa thái sợi (hoặc củ sắn), 1 củ hành tây.\n• Nước chấm & Rau rừng: Nước mắm cá cơm ngon, tỏi, ớt băm, đường, chanh, đồ chua (cà rốt, củ cải bào sợi); rổ rau rừng miền Tây ăn kèm (lá cách, đọt xoài, lá lụa, cải xanh, xà lách, rau thơm).";
    } elseif (strpos($monAn['ten_mon'], 'Lẩu mắm') !== false) {
        $rawNguyenLieu = "• Nước dùng lẩu: 200g mắm cá linh, 100g mắm cá sặc, 1 lít nước dừa tươi, 500g xương heo ninh lấy nước ngọt, sả băm, tỏi, ớt băm, đường phèn.\n• Đồ nhúng lẩu: 300g thịt ba chỉ thái mỏng, 300g cá hú (hoặc cá ba sa), 200g tôm sú tươi, 200g mực ống, 200g cà tím cắt khúc.\n• Rau ăn kèm & Bún: Bông súng, bông điên điển, rau đắng, rau nhút, kèo nèo, bắp chuối bào, bún tươi.";
    } elseif (strpos($monAn['ten_mon'], 'cá linh') !== false) {
        $rawNguyenLieu = "• Cá linh: 500g cá linh non đầu mùa tươi sống, làm sạch mang và ruột.\n• Bông điên điển: 300g bông điên điển vàng tươi, ngắt bỏ cọng già.\n• Nước lẩu me: 1 lít nước dừa tươi hoặc nước dùng xương, 3 quả me chín, ớt sừng, tỏi phi, ngò gai, ngò om.\n• Gia vị: Nước mắm nhĩ ngon, đường phèn, hạt nêm, bún tươi hoặc mì ăn kèm.";
    } else {
        $rawNguyenLieu = "• Nguyên liệu chính: " . $monAn['ten_mon'] . " tươi ngon chọn lọc theo mùa.\n• Gia vị truyền thống: Nước mắm cốt, tỏi, hành tím, ớt tươi, tiêu đen, đường phèn, hạt nêm.\n• Rau thơm & Đồ ăn kèm: Các loại rau sống theo mùa, dưa leo, thảo mộc địa phương chuẩn vị.";
    }
}

// Xử lý cách làm chuẩn nếu database đang để placeholder
$rawCachLam = trim($monAn['cach_lam'] ?? '');
if (empty($rawCachLam) || $rawCachLam === 'Cách làm đang cập nhật' || strpos($rawCachLam, 'đang cập nhật') !== false) {
    if ($slugHienTai === 'banh-xeo-can-tho' || strpos($monAn['ten_mon'], 'Bánh xèo') !== false) {
        $rawCachLam = "• Bước 1 (Pha bột giòn lâu): Trộn bột gạo, bột chiên giòn, bột nghệ và chút muối trong âu lớn. Đổ nước cốt dừa và nước lọc vào khuấy tan mịn không vón cục. Đập 1 quả trứng gà vào đánh đều cùng hành lá cắt nhỏ. Để bột nghỉ 30 phút.\n• Bước 2 (Xào sơ nhân tôm thịt): Thịt ba rọi và tôm ướp chút hành tím băm, tiêu xay, hạt nêm. Phi thơm hành trên chảo rồi xào tôm thịt vừa chín tới thì trút ra đĩa riêng.\n• Bước 3 (Đổ bánh xèo giòn rụm): Làm nóng chảo lòng sâu với 1 muỗng canh dầu ăn. Chảo thật nóng thì múc 1 vá bột láng đều thật mỏng quanh vành chảo nghe tiếng xèo giòn tan. Cho nhân tôm thịt, đậu xanh, củ hũ dừa và giá lên một nửa mặt bánh. Đậy nắp 2 - 3 phút cho chín nhân. Mở nắp, rưới nhẹ một chút dầu ăn quanh rìa chảo để vỏ bánh giòn tan không bị ỉu, sau đó gập đôi bánh lại và vớt ra đĩa có lót giấy thấm dầu.\n• Bước 4 (Pha nước mắm & Thưởng thức): Pha nước mắm chua ngọt theo tỉ lệ vàng 1 mắm : 1 đường : 2 nước ấm : 1 nước cốt chanh, thêm tỏi ớt băm và đồ chua. Thưởng thức bánh xèo nóng hổi giòn rụm, cuộn cùng bánh tráng và rau rừng miền Tây chấm ngập chén nước mắm chua ngọt đậm đà.";
    } else {
        $rawCachLam = "• Bước 1 (Sơ chế nguyên liệu): Rửa sạch nguyên liệu tươi sống với nước muối loãng. Để ráo và ướp gia vị truyền thống trong 15 - 20 phút cho thấm đều.\n• Bước 2 (Chế biến chuẩn vị): Nấu với nhiệt độ vừa phải để giữ trọn vẹn độ tươi ngon và dưỡng chất đặc trưng của món ăn.\n• Bước 3 (Hoàn thiện & Thưởng thức): Trình bày món ăn ra đĩa nóng, thêm rau thơm trang trí và thưởng thức cùng nước chấm pha riêng.";
    }
}
?>

<!-- GIẢM PADDING-TOP XUỐNG pt-4 ĐỂ XÓA BỎ HOÀN TOÀN KHOẢNG TRỐNG TỪ BANNER TỚI BREADCRUMB -->
<div class="pt-4 pb-16 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-7xl">
        
        <!-- Breadcrumb (Gọn gàng ngay sát dưới banner header) -->
        <nav class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-gray-500 mb-4 flex-wrap">
            <a href="<?php echo BASE_URL; ?>" class="hover:text-primary transition-colors flex items-center gap-1">
                <i class="fa-solid fa-house text-[11px]"></i> Trang chủ
            </a>
            <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
            <a href="<?php echo BASE_URL; ?>#menu" class="hover:text-primary transition-colors">Đặc sản 3 miền</a>
            <?php if (!empty($monAn['tinh_thanh'])): ?>
            <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
            <span class="text-gray-600 font-medium"><?php echo htmlspecialchars($monAn['tinh_thanh']); ?></span>
            <?php endif; ?>
            <i class="fa-solid fa-chevron-right text-[9px] text-gray-400"></i>
            <span class="text-gray-900 font-semibold truncate max-w-[180px] sm:max-w-none"><?php echo isset($monAn) ? htmlspecialchars($monAn['ten_mon']) : 'Chi tiết món ăn'; ?></span>
        </nav>

        <?php if (isset($monAn)): ?>
            <?php 
            $tenMonClean = class_exists('Normalizer') ? Normalizer::normalize($monAn['ten_mon'], Normalizer::FORM_C) : $monAn['ten_mon'];
            $tenMonClean = str_replace(
                ["ố", "ồ", "ổ", "ỗ", "ộ", "ô", "ú", "ù", "ủ", "ũ", "ụ", "á", "à", "ả", "ã", "ạ", "é", "è", "ẻ", "ẽ", "ẹ", "í", "ì", "ỉ", "ĩ", "ị", "ý", "ỳ", "ỷ", "ỹ", "ỵ"],
                ["ố", "ồ", "ổ", "ỗ", "ộ", "ô", "ú", "ù", "ủ", "ũ", "ụ", "á", "à", "ả", "ã", "ạ", "é", "è", "ẻ", "ẽ", "ẹ", "í", "ì", "ỉ", "ĩ", "ị", "ý", "ỳ", "ỷ", "ỹ", "ỵ"],
                $tenMonClean
            );
            ?>

            <!-- PHẦN 1: SHOWCASE MÓN ĂN & NGUYÊN LIỆU (KHỐI CHẶT CHẼ, KHÔNG KÉO GIÃN TRỐNG) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="flex flex-col lg:flex-row items-start">
                    
                    <!-- CỘT TRÁI: ẢNH MÓN ĂN & 4 THÔNG SỐ NHANH -->
                    <div class="w-full lg:w-5/12 p-4 sm:p-6 bg-gray-50/50 border-b lg:border-b-0 lg:border-r border-gray-100 flex flex-col gap-4">
                        <!-- Khung hình ảnh món ăn -->
                        <div class="relative rounded-2xl overflow-hidden shadow-sm group bg-gray-900 h-[240px] sm:h-[300px] lg:h-[320px]">
                            <?php 
                                $detailImg = $monAn['hinh_anh'] ?? '';
                                if (strpos($detailImg, 'http') !== 0 && strpos($detailImg, '/') !== 0) {
                                    $detailImg = (defined('BASE_URL') ? BASE_URL : '/') . $detailImg;
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($detailImg); ?>" 
                                 alt="<?php echo htmlspecialchars($monAn['ten_mon']); ?>" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800';">
                            
                            <!-- Tag Vùng Miền (Góc trái bên trên) -->
                            <div class="absolute top-4 left-4 bg-black/65 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-map-location-dot text-primary"></i> 
                                <span>Miền <?php echo htmlspecialchars($monAn['ten_vung'] ?? 'Nam'); ?></span>
                            </div>

                            <!-- Tag Tỉnh Thành (Góc phải bên trên - nếu có) -->
                            <?php if (!empty($monAn['tinh_thanh'])): ?>
                            <div class="absolute top-4 right-4 bg-red-600/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-md flex items-center gap-1.5 shadow-md">
                                <i class="fa-solid fa-location-dot text-white text-[11px]"></i> 
                                <span><?php echo htmlspecialchars($monAn['tinh_thanh']); ?></span>
                            </div>
                            <?php endif; ?>

                            <div class="absolute bottom-3 right-3 bg-black/60 text-white text-[11px] font-medium px-2.5 py-1 rounded-full backdrop-blur-sm flex items-center gap-1">
                                <i class="fa-solid fa-camera"></i> Hình ảnh thực tế
                            </div>
                        </div>

                        <!-- 4 Thẻ thông số xếp gọn 2x2 (Điền kín không gian rất đẹp) -->
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center gap-2.5 shadow-xs">
                                <span class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-500 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-star"></i>
                                </span>
                                <div>
                                    <div class="text-[10px] text-gray-400 font-medium">Đánh giá</div>
                                    <div class="text-xs font-bold text-gray-800">
                                        <?php echo htmlspecialchars($monAn['danh_gia'] ?? 4.8); ?> / 5.0
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center gap-2.5 shadow-xs">
                                <span class="w-8 h-8 rounded-lg bg-red-50 text-primary flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-regular fa-clock"></i>
                                </span>
                                <div>
                                    <div class="text-[10px] text-gray-400 font-medium">Thời gian nấu</div>
                                    <div class="text-xs font-bold text-gray-800">
                                        <?php echo htmlspecialchars($monAn['thoi_gian_nau'] ?? 30); ?> phút
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center gap-2.5 shadow-xs">
                                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-fire-burner"></i>
                                </span>
                                <div>
                                    <div class="text-[10px] text-gray-400 font-medium">Độ khó</div>
                                    <div class="text-xs font-bold text-gray-800">Trung bình</div>
                                </div>
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center gap-2.5 shadow-xs">
                                <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-users"></i>
                                </span>
                                <div>
                                    <div class="text-[10px] text-gray-400 font-medium">Khẩu phần</div>
                                    <div class="text-xs font-bold text-gray-800">3 - 4 người</div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút điều hướng nhanh -->
                        <div class="flex gap-2">
                            <a href="#danh-sach-quan" class="flex-1 bg-primary hover:bg-primaryDark text-white font-bold py-2.5 px-3 rounded-xl text-center text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-store"></i> Xem quán ăn chuẩn vị
                            </a>
                            <a href="<?php echo BASE_URL; ?>gan-ban" title="Tìm quán gần bạn" class="bg-white border border-gray-200 hover:border-primary hover:text-primary text-gray-700 font-bold py-2.5 px-3.5 rounded-xl text-center text-xs transition-colors flex items-center justify-center shadow-xs">
                                <i class="fa-solid fa-location-crosshairs text-primary text-sm"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- CỘT PHẢI: TÊN MÓN, GIỚI THIỆU & NGUYÊN LIỆU -->
                    <div class="w-full lg:w-7/12 p-6 lg:p-8 flex flex-col gap-5">
                        <div>
                            <!-- Header tags -->
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-primary bg-red-50 px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-award"></i> Đặc sản trứ danh
                                </span>
                                <?php if (!empty($monAn['tinh_thanh'])): ?>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-location-dot text-primary"></i> <?php echo htmlspecialchars($monAn['tinh_thanh']); ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight mb-3">
                                <?php echo htmlspecialchars($tenMonClean); ?>
                            </h1>

                            <!-- Giới thiệu món ăn (đã làm sạch văn bản) -->
                            <p class="text-gray-600 leading-relaxed text-sm bg-gray-50/70 p-4 rounded-xl border border-gray-100 mb-5">
                                <?php echo nl2br(htmlspecialchars($moTaClean)); ?>
                            </p>
                            
                            <!-- NGUYÊN LIỆU CHÍNH -->
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-basket-shopping text-primary"></i> Nguyên liệu chính
                                    </h3>
                                    <span class="text-[11px] text-gray-400 font-medium">Khẩu phần 3 - 4 người</span>
                                </div>
                                
                                <div class="bg-red-50/30 p-4 rounded-xl border border-red-100/70">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <?php 
                                        $linesNguyenLieu = explode("\n", $rawNguyenLieu);
                                        foreach ($linesNguyenLieu as $line):
                                            $line = trim($line);
                                            if (empty($line)) continue;
                                        ?>
                                            <div class="flex items-start gap-2 text-xs sm:text-sm text-gray-700 leading-relaxed bg-white/80 p-2.5 rounded-lg border border-red-50/80 shadow-2xs">
                                                <i class="fa-solid fa-circle-check text-primary text-xs mt-0.5 shrink-0"></i>
                                                <span><?php echo htmlspecialchars(ltrim($line, "•-* \t")); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3 Tiêu chí đảm bảo -->
                        <div class="grid grid-cols-3 gap-2 pt-3 border-t border-gray-100 text-center">
                            <div class="p-2 rounded-lg bg-gray-50/60">
                                <i class="fa-solid fa-shield-heart text-primary text-sm mb-1"></i>
                                <span class="text-[11px] font-semibold text-gray-700 block">Chuẩn vị truyền thống</span>
                            </div>
                            <div class="p-2 rounded-lg bg-gray-50/60">
                                <i class="fa-solid fa-map-location-dot text-primary text-sm mb-1"></i>
                                <span class="text-[11px] font-semibold text-gray-700 block">Đúng gốc địa phương</span>
                            </div>
                            <div class="p-2 rounded-lg bg-gray-50/60">
                                <i class="fa-solid fa-utensils text-primary text-sm mb-1"></i>
                                <span class="text-[11px] font-semibold text-gray-700 block">Công thức chọn lọc</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PHẦN 2: HƯỚNG DẪN CÁCH LÀM & QUÁN ĂN GỢI Ý (CÂN ĐỐI 2 BÊN, KHÔNG CÒN KHOẢNG TRỐNG) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12 items-start">
                
                <!-- CỘT TRÁI (7 PHẦN): HƯỚNG DẪN CÁCH LÀM CHI TIẾT -->
                <div class="lg:col-span-7 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-kitchen-set text-primary"></i> Hướng dẫn các bước chế biến
                        </h2>
                        <span class="text-[11px] text-emerald-600 font-semibold bg-emerald-50 px-2.5 py-1 rounded-full">
                            Bí quyết đầu bếp
                        </span>
                    </div>

                    <div class="space-y-3">
                        <?php 
                        $linesCachLam = explode("\n", $rawCachLam);
                        $stepNumber = 1;
                        foreach ($linesCachLam as $stepLine):
                            $stepLine = trim($stepLine);
                            if (empty($stepLine)) continue;
                            $cleanStep = ltrim($stepLine, "•-* \t");
                        ?>
                            <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 flex items-start gap-3 hover:border-gray-200 hover:bg-white transition-all">
                                <span class="w-6 h-6 rounded-full bg-primary text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                    <?php echo $stepNumber++; ?>
                                </span>
                                <p class="text-xs sm:text-sm text-gray-700 leading-relaxed flex-1">
                                    <?php echo htmlspecialchars($cleanStep); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Mẹo thưởng thức ăn kèm -->
                    <div class="mt-4 p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/60 text-xs text-amber-900 flex items-start gap-2.5">
                        <i class="fa-solid fa-lightbulb text-amber-500 mt-0.5 shrink-0 text-sm"></i>
                        <span class="leading-relaxed"><strong>Mẹo thưởng thức ngon nhất:</strong> Nên dùng món khi còn nóng hổi, chấm kèm nước mắm ớt hoặc nước sốt chuẩn vị địa phương cùng các loại rau thơm tươi sống để trọn vị ngon.</span>
                    </div>
                </div>

                <!-- CỘT PHẢI (5 PHẦN): GỢI Ý ĐỊA ĐIỂM QUÁN ĂN (TỰ NHIÊN, KHÔNG BỊ KÉO DÀI TRỐNG RỖNG) -->
                <div id="danh-sach-quan" class="lg:col-span-5 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base leading-tight flex items-center gap-2">
                                <i class="fa-solid fa-store text-primary"></i> Quán ăn ngon chuẩn vị
                            </h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Địa điểm phục vụ tại <?php echo htmlspecialchars($tinhThanhHienTai); ?></p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>gan-ban" class="text-xs text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1 bg-blue-50 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg transition-colors shrink-0">
                            <i class="fa-solid fa-location-dot"></i> Bản đồ
                        </a>
                    </div>

                    <!-- Danh sách quán -->
                    <div class="space-y-3 mb-4">
                        <?php if (!empty($danhSachGoiY)): ?>
                            <?php foreach ($danhSachGoiY as $idx => $quan): ?>
                            <div class="p-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/20 transition-all flex gap-3 items-center group">
                                <img src="<?php echo htmlspecialchars($quan['image']); ?>" 
                                     class="w-14 h-14 rounded-lg object-cover border border-gray-100 shrink-0 group-hover:scale-105 transition-transform" 
                                     alt="<?php echo htmlspecialchars($quan['name']); ?>"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=300&h=200';">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                        <h4 class="font-bold text-gray-900 text-xs sm:text-sm truncate group-hover:text-primary transition-colors">
                                            <?php echo htmlspecialchars($quan['name']); ?>
                                        </h4>
                                        <?php if (!empty($quan['tag'])): ?>
                                        <span class="shrink-0 bg-red-50 text-red-600 text-[10px] font-bold px-1.5 py-0.5 rounded">
                                            <?php echo htmlspecialchars($quan['tag']); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate mb-1" title="<?php echo htmlspecialchars($quan['address']); ?>">
                                        <i class="fa-solid fa-map-marker-alt text-primary/70 mr-0.5"></i> <?php echo htmlspecialchars($quan['address']); ?>
                                    </p>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-yellow-500 font-bold flex items-center gap-0.5">
                                            <i class="fa-solid fa-star text-[10px]"></i> <?php echo $quan['rating']; ?>
                                            <span class="text-gray-400 font-normal ml-1">(<?php echo htmlspecialchars($quan['price'] ?? 'Bình dân'); ?>)</span>
                                        </span>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($quan['name'] . ' ' . $quan['address']); ?>" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-bold flex items-center gap-1 text-[11px]" title="Mở chỉ đường trên Google Maps">
                                            Chỉ đường <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="py-6 text-center bg-gray-50 rounded-xl p-4">
                                <i class="fa-solid fa-utensils text-gray-300 text-2xl mb-1.5 block"></i>
                                <p class="text-xs text-gray-500">Đang cập nhật thêm địa chỉ phục vụ cho món ăn này.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-500">Tìm kiếm quanh bạn?</span>
                        <a href="<?php echo BASE_URL; ?>gan-ban" class="text-xs text-primary font-bold hover:underline flex items-center gap-1">
                            Mở bản đồ ẩm thực <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- PHẦN 3: GỢI Ý MÓN NGON THEO TỈNH THÀNH (YÊU CẦU CỦA BẠN) -->
            <?php
                $isSameProvinceOnly = true;
                foreach ($danhSachMonGoiY as $item) {
                    if (!empty($item['tinh_thanh']) && !empty($monAn['tinh_thanh']) && $item['tinh_thanh'] !== $monAn['tinh_thanh']) {
                        $isSameProvinceOnly = false;
                        break;
                    }
                }
                $tieuDeDiaPhuong = $isSameProvinceOnly 
                    ? ("Món ngon đặc sản tại " . htmlspecialchars($tinhThanhHienTai))
                    : ("Món ngon đặc sắc tại " . htmlspecialchars($tinhThanhHienTai) . " & Miền " . htmlspecialchars($monAn['ten_vung'] ?? ''));
            ?>
            <div class="mt-10 pt-8 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-[11px] font-bold text-primary bg-red-50 px-2.5 py-1 rounded-full uppercase tracking-wider mb-1.5">
                            <i class="fa-solid fa-fire text-primary"></i> Cùng địa phương
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                            <?php echo $tieuDeDiaPhuong; ?>
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Khám phá những món ăn trứ danh chuẩn vị bạn không nên bỏ lỡ khi thưởng thức ẩm thực <?php echo htmlspecialchars($tinhThanhHienTai); ?>
                        </p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>#menu" class="text-xs sm:text-sm font-bold text-primary hover:text-primaryDark flex items-center gap-1 shrink-0 group">
                        Xem tất cả món ăn <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <?php if (!empty($danhSachMonGoiY)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php foreach ($danhSachMonGoiY as $monGoiYItem): ?>
                            <div class="food-card bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col h-full relative">
                                <!-- Image Container -->
                                <div class="relative h-48 overflow-hidden bg-gray-100">
                                    <?php 
                                        $relImg = $monGoiYItem['hinh_anh'] ?? '';
                                        if (strpos($relImg, 'http') !== 0 && strpos($relImg, '/') !== 0) {
                                            $relImg = (defined('BASE_URL') ? BASE_URL : '/') . $relImg;
                                        }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($relImg); ?>" 
                                         alt="<?php echo htmlspecialchars($monGoiYItem['ten_mon']); ?>" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800';">
                                    
                                    <!-- Tag Vùng Miền (Góc trái bên trên) -->
                                    <div class="card-region-tag absolute top-3 left-3 bg-black/60 backdrop-blur-sm text-white text-[11px] font-medium px-2 py-1 rounded-md flex items-center gap-1 z-10 pointer-events-none shadow-xs">
                                        <i class="fa-solid fa-map-location-dot text-primary text-[10px]"></i> 
                                        <span>Miền <?php echo htmlspecialchars($monGoiYItem['ten_vung'] ?? ($monAn['ten_vung'] ?? '')); ?></span>
                                    </div>
                                    
                                    <!-- Tag Tỉnh Thành (Góc phải bên trên - nếu có) -->
                                    <?php if (!empty($monGoiYItem['tinh_thanh'])): ?>
                                    <div class="card-province-tag absolute top-3 right-3 bg-red-600/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2 py-1 rounded-md flex items-center gap-1 z-10 pointer-events-none shadow-xs">
                                        <i class="fa-solid fa-location-dot text-white text-[9px]"></i> 
                                        <span><?php echo htmlspecialchars($monGoiYItem['tinh_thanh']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-4 flex flex-col flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-base font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-1">
                                            <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($monGoiYItem['slug']); ?>" class="after:absolute after:inset-0">
                                                <?php echo htmlspecialchars($monGoiYItem['ten_mon']); ?>
                                            </a>
                                        </h3>
                                        <div class="flex items-center gap-1 bg-yellow-50 px-1.5 py-0.5 rounded text-xs font-bold text-yellow-600 z-10 shrink-0">
                                            <i class="fa-solid fa-star text-[10px]"></i>
                                            <?php echo htmlspecialchars($monGoiYItem['danh_gia'] ?? '4.8'); ?>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-500 text-xs mb-4 flex-grow line-clamp-2 leading-relaxed">
                                        <?php echo htmlspecialchars($monGoiYItem['mo_ta_ngan']); ?>
                                    </p>
                                    
                                    <div class="flex items-center justify-end mt-auto pt-3 border-t border-gray-100 z-10">
                                        <a href="<?php echo BASE_URL; ?>mon-an/<?php echo htmlspecialchars($monGoiYItem['slug']); ?>" 
                                           class="w-8 h-8 rounded-full bg-red-50 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all transform hover:scale-110 active:scale-95 z-20 relative cursor-pointer shadow-xs" 
                                           title="Xem chi tiết <?php echo htmlspecialchars($monGoiYItem['ten_mon']); ?>">
                                            <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-2xl p-6 text-center border border-gray-100">
                        <p class="text-xs sm:text-sm text-gray-500">Đang cập nhật thêm các đặc sản khác tại khu vực này.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 text-primary text-4xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Không tìm thấy món ăn</h2>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">Rất tiếc, món ăn bạn đang tìm kiếm không tồn tại hoặc đã được gỡ khỏi thực đơn.</p>
                <a href="<?php echo BASE_URL; ?>" class="inline-flex items-center gap-2 bg-primary hover:bg-primaryDark text-white px-8 py-3 rounded-xl font-bold shadow transition-all hover:-translate-y-1">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
