<?php 
/** 
 * @var array|null $nhaHang 
 * @var string $title
 */ 
$action = $nhaHang ? BASE_URL . 'admin/nha-hang/update/' . $nhaHang['id'] : BASE_URL . 'admin/nha-hang/store';
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($title); ?></h2>
        <a href="<?php echo BASE_URL; ?>admin/nha-hang" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>
    
    <div class="p-6">
        <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tên nhà hàng -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên nhà hàng <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($nhaHang['name'] ?? ''); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" placeholder="Ví dụ: Nhà hàng Ẩm Thực Xanh">
                </div>

                <!-- Quận/Huyện -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Quận/Huyện <span class="text-red-500">*</span></label>
                    <input type="text" name="district" required value="<?php echo htmlspecialchars($nhaHang['district'] ?? ''); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" placeholder="Quận 1, Quận Cầu Giấy...">
                </div>

                <!-- Đánh giá -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Đánh giá (1-5)</label>
                    <input type="number" step="0.1" min="1" max="5" name="rating" value="<?php echo htmlspecialchars($nhaHang['rating'] ?? 5.0); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" placeholder="5.0">
                </div>

                <!-- Địa chỉ -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
                    <input type="text" name="address" required value="<?php echo htmlspecialchars($nhaHang['address'] ?? ''); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" placeholder="Số 123 Đường ABC...">
                </div>

                <!-- Hình ảnh (Mới: Hỗ trợ upload từ thiết bị) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hình ảnh nhà hàng</label>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex-1 w-full">
                            <label class="flex flex-col items-center justify-center w-full px-4 py-6 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 hover:border-red-300 transition-colors group">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 group-hover:text-red-500 transition-colors"></i>
                                <span class="text-sm text-gray-500 font-medium group-hover:text-red-600">Click để chọn ảnh từ thiết bị</span>
                                <input type="file" name="hinh_anh_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                            </label>
                        </div>
                        
                        <div class="hidden sm:flex items-center text-gray-400 font-medium text-sm px-2">HOẶC</div>
                        <div class="sm:hidden flex w-full justify-center text-gray-400 font-medium text-sm">HOẶC</div>
                        
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Dùng ảnh từ đường dẫn mạng (URL):</label>
                            <input type="text" name="image" id="hinh_anh_url" value="<?php echo htmlspecialchars($nhaHang['image'] ?? ''); ?>" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" placeholder="https://example.com/image.jpg">
                        </div>
                    </div>

                    <!-- Khu vực xem trước ảnh -->
                    <div class="mt-4 <?php echo empty($nhaHang['image']) ? 'hidden' : ''; ?>" id="image-preview-container">
                        <p class="text-xs text-gray-500 mb-2">Ảnh xem trước:</p>
                        <img id="image-preview" src="<?php echo htmlspecialchars($nhaHang['image'] ?? ''); ?>" class="h-32 w-auto rounded-lg object-cover border border-gray-200 shadow-sm" alt="Preview">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i> Lưu dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview');
    const urlInput = document.getElementById('hinh_anh_url');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
            urlInput.value = ''; 
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('hinh_anh_url')?.addEventListener('input', function(e) {
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview');
    
    if(e.target.value.trim() !== '') {
        previewImg.src = e.target.value;
        previewContainer.classList.remove('hidden');
    }
});
</script>
