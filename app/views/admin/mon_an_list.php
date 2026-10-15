<?php 
/** 
 * @var array $monAnList 
 * @var array $vungMienList
 * @var string $currentFilter
 */ 
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="flex flex-col sm:flex-row items-center justify-between p-6 border-b border-gray-100 gap-4">
        <h2 class="text-lg font-bold text-gray-800">Danh sách Món ăn</h2>
        
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <form action="<?php echo BASE_URL; ?>admin/mon-an" method="GET" class="flex items-center gap-2">
                <select name="vung_mien" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    <option value="">Tất cả vùng miền</option>
                    <?php foreach ($vungMienList as $vm): ?>
                        <option value="<?php echo $vm['id']; ?>" <?php echo $currentFilter == $vm['id'] ? 'selected' : ''; ?>>
                            Miền <?php echo htmlspecialchars($vm['ten_vung']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <a href="<?php echo BASE_URL; ?>admin/mon-an/add" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i> Thêm món mới
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm">
                    <th class="px-6 py-3 font-semibold">ID</th>
                    <th class="px-6 py-3 font-semibold">Hình ảnh</th>
                    <th class="px-6 py-3 font-semibold">Tên món</th>
                    <th class="px-6 py-3 font-semibold">Vùng miền</th>
                    <th class="px-6 py-3 font-semibold">Giá tiền</th>
                    <th class="px-6 py-3 font-semibold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($monAnList as $mon): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-500 font-medium">#<?php echo $mon['id']; ?></td>
                        <td class="px-6 py-4">
                            <?php 
                                $aImg = $mon['hinh_anh'] ?? '';
                                if (strpos($aImg, 'http') !== 0 && strpos($aImg, '/') !== 0) {
                                    $aImg = (defined('BASE_URL') ? BASE_URL : '/') . $aImg;
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($aImg); ?>" alt="Img" class="w-12 h-12 rounded object-cover border border-gray-200" onerror="this.src='https://via.placeholder.com/150'">
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            <?php echo htmlspecialchars($mon['ten_mon']); ?>
                            <div class="text-xs text-gray-400 font-normal mt-1"><?php echo htmlspecialchars($mon['slug']); ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">
                                <?php echo htmlspecialchars($mon['ten_vung'] ?? 'Khác'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-red-600 font-semibold">
                            <?php echo number_format($mon['gia_tien'], 0, ',', '.'); ?> đ
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="<?php echo BASE_URL; ?>admin/mon-an/edit/<?php echo $mon['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="deleteMonAn(<?php echo $mon['id']; ?>, '<?php echo BASE_URL; ?>admin/mon-an/delete/<?php echo $mon['id']; ?>', this)" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($monAnList)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Chưa có dữ liệu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<script>
function deleteMonAn(id, url, btn) {
    if (confirm('Bạn có chắc chắn muốn xóa món này?')) {
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row with fade out effect
                const row = btn.closest('tr');
                row.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                row.style.opacity = 0;
                row.style.transform = "translateX(20px)";
                setTimeout(() => row.remove(), 300);
                
                // Optional: show a mini toast notification here if desired
            }
        })
        .catch(err => {
            console.error(err);
            alert("Có lỗi xảy ra, vui lòng tải lại trang và thử lại.");
        });
    }
}
</script>

</div>