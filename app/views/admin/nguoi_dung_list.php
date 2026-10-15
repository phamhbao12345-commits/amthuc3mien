<?php
/**
 * @var string $title
 * @var array $usersList
 */
?>
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($title) ?></h2>
        <a href="<?= BASE_URL ?>admin/nguoi-dung/add" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            + Thêm Người dùng
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-700 text-sm">
                    <th class="p-4 font-semibold border-b">ID</th>
                    <th class="p-4 font-semibold border-b">Tên đăng nhập</th>
                    <th class="p-4 font-semibold border-b">Họ và tên</th>
                    <th class="p-4 font-semibold border-b">Vai trò</th>
                    <th class="p-4 font-semibold border-b text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                <?php foreach ($usersList as $user): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-0 transition-colors">
                        <td class="p-4"><?= htmlspecialchars($user['id']) ?></td>
                        <td class="p-4 font-medium text-gray-900"><?= htmlspecialchars($user['username']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($user['fullname'] ?? '') ?></td>
                        <td class="p-4">
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-xs font-semibold">Admin</span>
                            <?php else: ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">User</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <a href="<?= BASE_URL ?>admin/nguoi-dung/edit/<?= $user['id'] ?>" class="inline-block text-blue-600 hover:text-blue-800 transition-colors">
                                Sửa
                            </a>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['id']): ?>
                                <span class="text-gray-300">|</span>
                                <a href="javascript:void(0);" 
                                   onclick="deleteNguoiDung(<?= $user['id'] ?>, '<?= BASE_URL ?>admin/nguoi-dung/delete/<?= $user['id'] ?>', this)"
                                   class="inline-block text-red-600 hover:text-red-800 transition-colors">
                                    Xóa
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($usersList)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Chưa có người dùng nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteNguoiDung(id, url, btn) {
    if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
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
                alert(data.message || 'Không thể xóa người dùng này!');
            }
        })
        .catch(err => {
            console.error(err);
            alert("Có lỗi xảy ra, vui lòng tải lại trang và thử lại.");
        });
    }
}
</script>

