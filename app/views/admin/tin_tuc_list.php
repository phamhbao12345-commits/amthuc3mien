<?php 
/** 
 * @var array $tinTucList 
 * @var array $danhMucList
 * @var string $currentDanhMuc
 * @var string $keyword
 */ 
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="flex flex-col lg:flex-row items-center justify-between p-6 border-b border-gray-100 gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Danh sách Tin tức & Blog</h2>
            <p class="text-xs text-gray-500 mt-0.5">Quản lý toàn bộ bài viết, công thức và cẩm nang ẩm thực trên hệ thống</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Form Lọc & Tìm kiếm -->
            <form action="<?php echo BASE_URL; ?>admin/tin-tuc" method="GET" class="flex flex-wrap items-center gap-2 flex-1 sm:flex-initial">
                <!-- Dropdown lọc danh mục -->
                <select name="danh_muc" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 bg-white">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($danhMucList as $dm): ?>
                        <option value="<?php echo htmlspecialchars($dm); ?>" <?php echo $currentDanhMuc === $dm ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dm); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Ô tìm kiếm bài viết -->
                <div class="relative">
                    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword ?? ''); ?>" placeholder="Tìm tiêu đề, tác giả..." class="border border-gray-300 rounded-lg pl-8 pr-3 py-2 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 w-44 sm:w-56">
                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                </div>

                <?php if (!empty($currentDanhMuc) || !empty($keyword)): ?>
                    <a href="<?php echo BASE_URL; ?>admin/tin-tuc" class="text-xs text-red-600 hover:underline px-1 py-2 font-medium" title="Xóa bộ lọc">
                        <i class="fas fa-times-circle"></i> Đặt lại
                    </a>
                <?php endif; ?>
            </form>

            <!-- Nút Thêm bài viết mới -->
            <a href="<?php echo BASE_URL; ?>admin/tin-tuc/add" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap flex items-center shadow-xs">
                <i class="fas fa-plus mr-2"></i> Thêm bài viết mới
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm">
                    <th class="px-6 py-3 font-semibold w-16">ID</th>
                    <th class="px-6 py-3 font-semibold w-24">Hình ảnh</th>
                    <th class="px-6 py-3 font-semibold">Tiêu đề bài viết</th>
                    <th class="px-6 py-3 font-semibold w-40">Danh mục</th>
                    <th class="px-6 py-3 font-semibold w-36">Tác giả</th>
                    <th class="px-6 py-3 font-semibold w-28 text-center">Lượt xem</th>
                    <th class="px-6 py-3 font-semibold w-32">Ngày đăng</th>
                    <th class="px-6 py-3 font-semibold text-right w-28">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($tinTucList as $item): ?>
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 text-gray-500 font-medium">#<?php echo $item['id']; ?></td>
                        
                        <td class="px-6 py-4">
                            <img src="<?php echo htmlspecialchars($item['hinh_anh'] ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=200'); ?>" 
                                 alt="Thumbnail" 
                                 class="w-14 h-12 rounded-lg object-cover border border-gray-200 shadow-xs" 
                                 onerror="this.src='https://via.placeholder.com/150';">
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo BASE_URL; ?>tin-tuc/<?php echo htmlspecialchars($item['slug']); ?>" target="_blank" class="font-bold text-gray-800 hover:text-red-600 transition-colors line-clamp-1" title="<?php echo htmlspecialchars($item['tieu_de']); ?>">
                                    <?php echo htmlspecialchars($item['tieu_de']); ?>
                                </a>
                                <?php if (!empty($item['noi_bat'])): ?>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 shrink-0">
                                        <i class="fas fa-star text-[9px] mr-0.5"></i> Nổi bật
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="text-xs text-gray-400 font-normal mt-0.5 flex items-center gap-1">
                                <i class="fas fa-link text-[10px]"></i> /tin-tuc/<?php echo htmlspecialchars($item['slug']); ?>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold whitespace-nowrap">
                                <?php echo htmlspecialchars($item['danh_muc'] ?? 'Khám phá'); ?>
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-gray-700 font-medium">
                            <i class="fas fa-user-edit text-xs text-gray-400 mr-1"></i> <?php echo htmlspecialchars($item['tac_gia'] ?? 'Admin'); ?>
                        </td>
                        
                        <td class="px-6 py-4 text-center text-gray-600 font-medium">
                            <span class="inline-flex items-center gap-1 text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">
                                <i class="far fa-eye text-gray-400"></i> <?php echo number_format($item['luot_xem'] ?? 0); ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                            <?php echo !empty($item['created_at']) ? date('d/m/Y H:i', strtotime($item['created_at'])) : '--'; ?>
                        </td>
                        
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="<?php echo BASE_URL; ?>admin/tin-tuc/edit/<?php echo $item['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="deleteTinTuc(<?php echo $item['id']; ?>, '<?php echo BASE_URL; ?>admin/tin-tuc/delete/<?php echo $item['id']; ?>', this)" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors" title="Xóa bài viết">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if(empty($tinTucList)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-newspaper text-3xl text-gray-300 mb-2 block"></i>
                            Chưa có bài viết nào phù hợp. Bấm nút <strong>"Thêm bài viết mới"</strong> ở trên để tạo bài viết đầu tiên!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteTinTuc(id, url, btn) {
    if (confirm('Bạn có chắc chắn muốn xóa bài viết này không? Hành động này không thể hoàn tác.')) {
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = btn.closest('tr');
                row.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                row.style.opacity = 0;
                row.style.transform = "translateX(20px)";
                setTimeout(() => row.remove(), 300);
            } else {
                alert(data.message || "Không thể xóa bài viết này.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Có lỗi xảy ra, vui lòng tải lại trang và thử lại.");
        });
    }
}
</script>

