<?php 
/** 
 * @var int $countMonAn 
 * @var int $countUsers 
 * @var int $countNhaHang
 * @var int $countTinTuc
 * @var array $recentUsers 
 */ 
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <a href="<?php echo BASE_URL; ?>admin/mon-an" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center hover:border-blue-200 transition-colors group">
        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4 group-hover:scale-110 transition-transform">
            <i class="fas fa-utensils text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Tổng món ăn</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo number_format($countMonAn); ?></p>
        </div>
    </a>
    
    <a href="<?php echo BASE_URL; ?>admin/nha-hang" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center hover:border-green-200 transition-colors group">
        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4 group-hover:scale-110 transition-transform">
            <i class="fas fa-store text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Nhà hàng</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo number_format($countNhaHang ?? 0); ?></p>
        </div>
    </a>
    
    <a href="<?php echo BASE_URL; ?>admin/tin-tuc" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center hover:border-purple-200 transition-colors group">
        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4 group-hover:scale-110 transition-transform">
            <i class="fas fa-newspaper text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Tin tức & Blog</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo number_format($countTinTuc ?? 0); ?></p>
        </div>
    </a>
    
    <a href="<?php echo BASE_URL; ?>admin/nguoi-dung" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center hover:border-yellow-200 transition-colors group">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4 group-hover:scale-110 transition-transform">
            <i class="fas fa-users text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Thành viên</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo number_format($countUsers); ?></p>
        </div>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Người dùng mới đăng ký</h2>
    <div class="space-y-4">
        <?php foreach ($recentUsers as $user): ?>
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-gray-200 rounded-full w-10 h-10 flex items-center justify-center font-bold text-gray-600 mr-4">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($user['username']); ?></p>
                        <p class="text-xs text-gray-500 mt-0.5"><?php echo htmlspecialchars($user['fullname'] ?: 'Chưa cập nhật tên'); ?> - Vai trò: <?php echo $user['role']; ?></p>
                    </div>
                </div>
                <div class="text-xs font-medium text-gray-400">
                    <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($recentUsers)): ?>
            <p class="text-sm text-gray-500">Chưa có người dùng nào.</p>
        <?php endif; ?>
    </div>
</div>
