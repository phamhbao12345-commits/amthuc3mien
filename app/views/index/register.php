<?php /** @var string $error */ ?>
<div class="min-h-[calc(100vh-150px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Ảnh nền -->
    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Restaurant Background" class="absolute inset-0 w-full h-full object-cover z-0" onerror="this.src='https://images.unsplash.com/photo-1414235077428-338988a2e8c0'" />
    
    <!-- Lớp phủ tối mờ -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm z-0"></div>
    
    <div class="relative z-10 max-w-md w-full space-y-8 bg-white/95 p-8 rounded-2xl shadow-2xl border border-white/20">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Đăng Ký
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Tham gia cộng đồng Ẩm Thực 3 Miền
            </p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="<?php echo BASE_URL; ?>register" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                    <input id="username" name="username" type="text" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm mt-1" placeholder="Tên đăng nhập viết liền không dấu">
                </div>
                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input id="fullname" name="fullname" type="text" class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm mt-1" placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm mt-1" placeholder="Mật khẩu (ít nhất 6 ký tự)">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primaryDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    Đăng Ký
                </button>
            </div>
        </form>
        
        <div class="text-center mt-4 text-sm text-gray-600">
            Đã có tài khoản? <a href="<?php echo BASE_URL; ?>login" class="font-medium text-primary hover:text-primaryDark">Đăng nhập</a>
        </div>
    </div>
</div>
