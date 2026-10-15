<div class="bg-white rounded-lg shadow-sm max-w-2xl mx-auto">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($title) ?></h2>
    </div>
    
    <div class="p-6">
        <form action="<?= BASE_URL ?>admin/nguoi-dung/<?= $user ? 'update/' . $user['id'] : 'store' ?>" method="POST">
            
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Tên đăng nhập</label>
                <input type="text" name="username" id="username" 
                       value="<?= htmlspecialchars($user['username'] ?? '') ?>" 
                       <?= $user ? 'readonly' : 'required' ?>
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?= $user ? 'bg-gray-100 cursor-not-allowed' : '' ?>">
                <?php if ($user): ?>
                    <p class="text-xs text-gray-500 mt-1">Không thể thay đổi tên đăng nhập.</p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                    Mật khẩu <?= $user ? '<span class="text-gray-500 font-normal">(Để trống nếu không muốn đổi)</span>' : '<span class="text-red-500">*</span>' ?>
                </label>
                <input type="password" name="password" id="password" 
                       <?= !$user ? 'required' : '' ?>
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="fullname" class="block text-gray-700 text-sm font-bold mb-2">Họ và tên</label>
                <input type="text" name="fullname" id="fullname" 
                       value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Vai trò</label>
                <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-white">
                    <option value="user" <?= ($user['role'] ?? '') === 'user' ? 'selected' : '' ?>>Người dùng (User)</option>
                    <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="flex items-center justify-between mt-8">
                <a href="<?= BASE_URL ?>admin/nguoi-dung" class="text-gray-500 hover:text-gray-700 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition-colors">
                    <?= $user ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
            </div>
        </form>
    </div>
</div>

