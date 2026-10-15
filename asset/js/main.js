document.addEventListener('DOMContentLoaded', () => {
    // 1. THANH TÌM KIẾM VÀ DROPDOWN KHU VỰC (HEADER SEARCH)
    const searchForm = document.getElementById('global-search-form');
    const searchInput = document.getElementById('search-keyword');
    const btnLocationDropdown = document.getElementById('btn-location-dropdown');
    const locationMenu = document.getElementById('location-menu');
    const locationChevron = document.getElementById('location-chevron');
    const locationText = document.getElementById('search-location-text');
    const locationInput = document.getElementById('search-location-input');
    const locationOptions = document.querySelectorAll('.location-option');

    // Toggle dropdown danh sách khu vực
    if (btnLocationDropdown && locationMenu) {
        btnLocationDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = locationMenu.classList.contains('hidden');
            if (isHidden) {
                locationMenu.classList.remove('hidden');
                if (locationChevron) locationChevron.classList.add('rotate-180');
            } else {
                locationMenu.classList.add('hidden');
                if (locationChevron) locationChevron.classList.remove('rotate-180');
            }
        });

        // Chọn khu vực từ danh sách
        locationOptions.forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const val = opt.getAttribute('data-value') || '';
                const label = opt.querySelector('span') ? opt.querySelector('span').innerText.trim() : val;

                if (locationText) {
                    locationText.innerText = val !== '' ? label : 'Tất cả khu vực';
                }
                if (locationInput) {
                    locationInput.value = val;
                }

                locationMenu.classList.add('hidden');
                if (locationChevron) locationChevron.classList.remove('rotate-180');
            });
        });

        // Click ngoài dropdown để đóng
        document.addEventListener('click', (e) => {
            const wrapper = document.getElementById('location-dropdown-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                locationMenu.classList.add('hidden');
                if (locationChevron) locationChevron.classList.remove('rotate-180');
            }
        });
    }

    // Xử lý gửi Form tìm kiếm
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', (e) => {
            const keyword = searchInput.value.trim();
            const location = locationInput ? locationInput.value.trim() : '';

            // Nếu cả từ khóa và khu vực đều không có thì nhắc nhở người dùng
            if (keyword === '' && (location === '' || location === 'Tất cả' || location === 'Tất cả khu vực')) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Vui lòng nhập món ăn hoặc chọn khu vực cần tìm!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
                searchInput.focus();
            }
        });
    }

    // 2. SCROLL MƯỢT CHO "KHÁM PHÁ THỰC ĐƠN"
    const exploreBtn = document.getElementById('btn-explore');
    if (exploreBtn) {
        exploreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.getElementById('menu');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    // 3. LOGIC MODAL ĐẶT BÀN
    const modal = document.getElementById('booking-modal');
    const btnBookNowList = document.querySelectorAll('.btn-book-now'); // Dùng class để bắt được nhiều nút
    const btnCloseModal = document.getElementById('close-modal');
    const bookingForm = document.getElementById('booking-form');

    if (modal && bookingForm) {
        // Mở modal
        btnBookNowList.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        // Đóng modal
        if (btnCloseModal) {
            btnCloseModal.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }
        
        // Đóng khi click bên ngoài
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });

        // Xử lý Form Submit qua AJAX
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(bookingForm);
            
            // Hiện loading
            const submitBtn = bookingForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
            submitBtn.disabled = true;

            fetch(`${BASE_URL}booking`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message,
                        confirmButtonColor: '#dc2626'
                    }).then(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        bookingForm.reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                        confirmButtonColor: '#dc2626'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi kết nối máy chủ!',
                    confirmButtonColor: '#dc2626'
                });
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

