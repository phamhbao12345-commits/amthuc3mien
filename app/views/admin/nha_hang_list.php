<?php 
/** 
 * @var array $nhaHangList 
 */ 
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="flex items-center justify-between p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">Danh sách Nhà hàng</h2>
        <a href="<?php echo BASE_URL; ?>admin/nha-hang/add" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i> Thêm nhà hàng mới
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm">
                    <th class="px-6 py-3 font-semibold">ID</th>
                    <th class="px-6 py-3 font-semibold">Hình ảnh</th>
                    <th class="px-6 py-3 font-semibold">Tên nhà hàng</th>
                    <th class="px-6 py-3 font-semibold">Khu vực</th>
                    <th class="px-6 py-3 font-semibold">Đánh giá</th>
                    <th class="px-6 py-3 font-semibold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($nhaHangList as $nh): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-500 font-medium">#<?php echo $nh['id']; ?></td>
                        <td class="px-6 py-4">
                            <img src="<?php echo htmlspecialchars($nh['image'] ?? 'https://via.placeholder.com/150'); ?>" alt="Img" class="w-12 h-12 rounded object-cover border border-gray-200" onerror="this.src='https://via.placeholder.com/150'">
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            <?php echo htmlspecialchars($nh['name']); ?>
                            <div class="text-xs text-gray-400 font-normal mt-1 max-w-[200px] truncate" title="<?php echo htmlspecialchars($nh['address']); ?>"><?php echo htmlspecialchars($nh['address']); ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">
                                <?php echo htmlspecialchars($nh['district'] ?? 'Khác'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-yellow-500">
                            <i class="fas fa-star mr-1"></i><?php echo number_format($nh['rating'], 1); ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="<?php echo BASE_URL; ?>admin/nha-hang/edit/<?php echo $nh['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="deleteNhaHang(<?php echo $nh['id']; ?>, '<?php echo BASE_URL; ?>admin/nha-hang/delete/<?php echo $nh['id']; ?>', this)" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($nhaHangList)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Chưa có nhà hàng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteNhaHang(id, url, btn) {
    if (confirm('Bạn có chắc chắn muốn xóa nhà hàng này?')) {
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
            }
        })
        .catch(err => {
            console.error(err);
            alert("Có lỗi xảy ra, vui lòng tải lại trang và thử lại.");
        });
    }
}
</script>
