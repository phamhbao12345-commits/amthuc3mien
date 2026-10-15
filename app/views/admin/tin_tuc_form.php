<?php 
/** 
 * @var array|null $tinTuc 
 * @var array $danhMucList 
 * @var string $title
 */ 
$action = $tinTuc ? BASE_URL . 'admin/tin-tuc/update/' . $tinTuc['id'] : BASE_URL . 'admin/tin-tuc/store';
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($title); ?></h2>
            <p class="text-xs text-gray-500 mt-0.5"><?php echo $tinTuc ? 'Cập nhật nội dung bài viết đã có' : 'Tạo bài viết mới cho chuyên mục Tin tức & Blog'; ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>admin/tin-tuc" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-1.5"></i> Quay lại danh sách
        </a>
    </div>
    
    <div class="p-6">
        <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tiêu đề bài viết -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                    <input type="text" name="tieu_de" id="tieu_de_input" required 
                           value="<?php echo htmlspecialchars($tinTuc['tieu_de'] ?? ''); ?>" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-[15px] font-medium" 
                           placeholder="Ví dụ: Bí quyết chọn rượu vang phù hợp với món steak chuẩn vị Âu">
                </div>

                <!-- Danh mục -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Chuyên mục <span class="text-red-500">*</span></label>
                    <select name="danh_muc" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all bg-white font-medium text-gray-700">
                        <?php foreach($danhMucList as $dm): ?>
                            <option value="<?php echo htmlspecialchars($dm); ?>" <?php echo (isset($tinTuc['danh_muc']) && $tinTuc['danh_muc'] === $dm) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dm); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tác giả -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tác giả / Nguồn bài viết</label>
                    <input type="text" name="tac_gia" 
                           value="<?php echo htmlspecialchars($tinTuc['tac_gia'] ?? 'Ban biên tập'); ?>" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" 
                           placeholder="Ví dụ: Ban biên tập, Chuyên gia F&B...">
                </div>

                <!-- Hình ảnh đại diện -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hình ảnh bài viết (Thumbnail)</label>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <!-- Upload File từ máy -->
                        <div class="flex-1 w-full">
                            <label class="flex flex-col items-center justify-center w-full px-4 py-6 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 hover:border-red-300 transition-colors group">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 group-hover:text-red-500 transition-colors"></i>
                                <span class="text-sm text-gray-500 font-medium group-hover:text-red-600">Click để chọn ảnh từ máy tính</span>
                                <input type="file" name="hinh_anh_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                            </label>
                        </div>
                        
                        <div class="hidden sm:flex items-center text-gray-400 font-medium text-sm px-2">HOẶC</div>
                        <div class="sm:hidden flex w-full justify-center text-gray-400 font-medium text-sm">HOẶC</div>
                        
                        <!-- Đường dẫn URL -->
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Dùng ảnh từ link mạng (URL):</label>
                            <input type="text" name="hinh_anh" id="hinh_anh_url" 
                                   value="<?php echo htmlspecialchars($tinTuc['hinh_anh'] ?? ''); ?>" 
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" 
                                   placeholder="https://images.unsplash.com/photo-...">
                        </div>
                    </div>

                    <!-- Xem trước ảnh -->
                    <div class="mt-4 <?php echo empty($tinTuc['hinh_anh']) ? 'hidden' : ''; ?>" id="image-preview-container">
                        <p class="text-xs text-gray-500 mb-2">Ảnh xem trước:</p>
                        <img id="image-preview" src="<?php echo htmlspecialchars($tinTuc['hinh_anh'] ?? ''); ?>" class="h-40 w-auto rounded-lg object-cover border border-gray-200 shadow-sm" alt="Preview">
                    </div>
                </div>

                <!-- Đánh dấu Nổi bật -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="noi_bat" value="1" <?php echo !empty($tinTuc['noi_bat']) ? 'checked' : ''; ?> class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                        <span class="text-sm font-bold text-gray-700">Đánh dấu bài viết nổi bật (Hiển thị ưu tiên ở trang chủ Blog)</span>
                    </label>
                </div>

                <!-- Tóm tắt ngắn -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tóm tắt ngắn (Sapo)</label>
                    <textarea name="tom_tat" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-sm" placeholder="Mô tả tóm tắt nội dung bài viết trong 1-2 câu ngắn gọn..."><?php echo htmlspecialchars($tinTuc['tom_tat'] ?? ''); ?></textarea>
                </div>

                <!-- Nội dung chi tiết -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-gray-700">Nội dung chi tiết bài viết <span class="text-red-500">*</span></label>
                        <span class="text-xs text-gray-400">Hỗ trợ các thẻ HTML: &lt;p&gt;, &lt;h3&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;img&gt;</span>
                    </div>
                    <textarea name="noi_dung" rows="12" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all font-mono text-sm leading-relaxed" placeholder="Nhập nội dung bài viết chi tiết..."><?php echo htmlspecialchars($tinTuc['noi_dung'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="<?php echo BASE_URL; ?>admin/tin-tuc" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i> <?php echo $tinTuc ? 'Lưu thay đổi' : 'Đăng bài viết'; ?>
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
            if (urlInput) urlInput.value = '';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('hinh_anh_url')?.addEventListener('input', function() {
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview');
    if (this.value.trim() !== '') {
        previewImg.src = this.value.trim();
        previewContainer.classList.remove('hidden');
    }
});
</script>

