<?php
/**
 * @var string $title
 * @var array $restaurants
 */
?>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="bg-[#f3f4f6] min-h-[calc(100vh-140px)] py-4">
    <div class="container mx-auto px-4 lg:px-8 xl:px-12 max-w-[1400px]">
        
        <!-- App Banner -->
        <div class="bg-[#f0f7ff] rounded-md p-4 mb-4 flex items-center justify-between relative shadow-sm border border-[#e1effe]">
            <div>
                <p class="text-gray-800 font-bold text-[15px] mb-1">
                    <i class="fas fa-location-crosshairs text-primary mr-1"></i> Tính năng tìm quán ăn gần bạn & Bán kính 5km
                </p>
                <p class="text-gray-600 text-xs">
                    Hệ thống tự động định vị vị trí của bạn để lọc các quán ăn trong vòng 5km. Bạn cũng có thể kéo thả ghim màu xanh trên bản đồ để đổi vị trí.
                </p>
            </div>
            <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Filter Controls Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Nút Định vị vị trí -->
                <button id="btn-locate" class="bg-primary hover:bg-primaryDark text-white rounded-full px-4 py-2 text-sm font-semibold flex items-center gap-2 shadow-sm transition-all hover:shadow">
                    <i id="locate-icon" class="fas fa-crosshairs"></i>
                    <span id="locate-text">Định vị vị trí của tôi</span>
                </button>

                <!-- Bộ lọc Bán kính 5km -->
                <button id="btn-radius-5km" class="bg-blue-600 text-white border border-blue-600 rounded-full px-4 py-2 text-sm font-semibold flex items-center gap-2 shadow-sm transition-all">
                    <i class="fas fa-bullseye"></i>
                    <span>Bán kính: 5km</span>
                    <span id="count-badge" class="bg-white text-blue-600 text-xs font-bold px-2 py-0.5 rounded-full ml-1">0</span>
                </button>

                <!-- Nút Xem tất cả -->
                <button id="btn-show-all" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-full px-4 py-2 text-sm font-medium flex items-center gap-2 transition-all">
                    <span>Xem tất cả</span>
                </button>

                <!-- Vị trí mẫu TP.HCM -->
                <button id="btn-sample-loc" class="bg-gray-100 border border-gray-200 text-gray-600 hover:bg-gray-200 rounded-full px-3 py-2 text-xs font-medium flex items-center gap-1.5 transition-all">
                    <i class="fas fa-city text-gray-500"></i> Vị trí mẫu (Q.12 / Gò Vấp)
                </button>
            </div>

            <!-- Vị trí hiện tại status -->
            <div id="user-location-status" class="text-xs text-gray-600 bg-white border border-gray-200 px-3 py-1.5 rounded-lg flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                <span id="status-text">Đang xác định vị trí...</span>
            </div>
        </div>

        <!-- Main Layout (List + Map) -->
        <div class="flex flex-col lg:flex-row gap-4 h-auto lg:h-[720px]">
            
            <!-- Left: List -->
            <div class="w-full lg:w-[35%] xl:w-[32%] bg-white rounded-xl shadow-sm border border-gray-200 overflow-y-auto flex flex-col max-h-[380px] sm:max-h-[460px] lg:max-h-none lg:h-full order-2 lg:order-1">
                
                <div class="p-3.5 bg-gray-50 border-b border-gray-200 flex items-center justify-between sticky top-0 z-10 backdrop-blur">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">
                        <i class="fas fa-utensils text-primary mr-1"></i> Danh sách quán ăn
                    </span>
                    <span id="list-summary" class="text-xs font-semibold text-primary">
                        Đang lọc...
                    </span>
                </div>

                <!-- Empty state khi không có quán nào trong 5km -->
                <div id="no-restaurant-alert" class="hidden p-8 text-center flex-col items-center justify-center my-auto">
                    <div class="w-16 h-16 bg-red-50 text-primary rounded-full flex items-center justify-center text-2xl mb-3">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 text-base mb-1">Không có quán nào trong bán kính 5km</h4>
                    <p class="text-xs text-gray-500 mb-4 max-w-xs">
                        Từ vị trí hiện tại của bạn không tìm thấy quán ăn nào cách dưới 5km. Bạn có thể mở rộng phạm vi hoặc thử vị trí mẫu tại TP.HCM.
                    </p>
                    <div class="flex flex-col gap-2 w-full max-w-xs">
                        <button id="btn-no-res-sample" class="w-full bg-primary hover:bg-primaryDark text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors">
                            Thử vị trí mẫu TP.HCM
                        </button>
                        <button id="btn-no-res-all" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold py-2 px-4 rounded-lg transition-colors">
                            Xem tất cả quán ăn
                        </button>
                    </div>
                </div>

                <!-- Danh sách quán -->
                <div id="restaurant-list-container" class="divide-y divide-gray-100">
                    <?php if (!empty($restaurants)): ?>
                        <?php foreach ($restaurants as $index => $res): ?>
                        <div class="p-4 flex gap-3.5 hover:bg-gray-50 cursor-pointer restaurant-item transition-all" 
                             data-index="<?php echo $index; ?>" 
                             data-id="<?php echo $res['id']; ?>"
                             data-lat="<?php echo !empty($res['lat']) ? $res['lat'] : '10.855'; ?>" 
                             data-lng="<?php echo !empty($res['lng']) ? $res['lng'] : '10.630'; ?>"
                             data-distance="999">
                            <img src="<?php echo htmlspecialchars($res['image'] ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=200&h=200'); ?>" 
                                 class="w-24 h-24 object-cover rounded-lg border border-gray-200 shrink-0 shadow-sm" 
                                 alt="<?php echo htmlspecialchars($res['name']); ?>"
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=200&h=200';">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-1 mb-1">
                                    <h3 class="font-bold text-[14px] text-gray-900 leading-snug line-clamp-1"><?php echo htmlspecialchars($res['name']); ?></h3>
                                    <span class="distance-badge shrink-0 bg-blue-50 text-blue-700 text-[11px] font-bold px-1.5 py-0.5 rounded border border-blue-100">
                                        ...
                                    </span>
                                </div>
                                
                                <p class="text-xs text-gray-500 mb-1.5 flex items-start gap-1">
                                    <i class="fas fa-map-marker-alt text-primary mt-0.5 shrink-0 text-[11px]"></i> 
                                    <span class="line-clamp-2"><?php echo htmlspecialchars($res['address']); ?></span>
                                </p>
                                
                                <div class="flex items-center text-xs text-gray-500 mb-2 font-medium">
                                    <span class="text-yellow-500 font-bold flex items-center gap-0.5">
                                        <i class="fas fa-star text-[10px]"></i> <?php echo number_format($res['rating'] ?? 4.0, 1); ?>
                                    </span>
                                    <span class="mx-1.5 text-gray-300">|</span>
                                    <span class="text-gray-600"><?php echo htmlspecialchars($res['district'] ?? 'TP.HCM'); ?></span>
                                </div>

                                <div class="flex items-center justify-between pt-1 gap-2">
                                    <span class="text-primary text-[11px] font-bold flex items-center gap-1 hover:underline">
                                        <i class="fas fa-store text-[11px]"></i> Xem quán
                                    </span>
                                    <button type="button" 
                                            onclick="event.stopPropagation(); navigateToRestaurant('<?php echo htmlspecialchars($res['name'], ENT_QUOTES); ?>', <?php echo !empty($res['lat']) ? $res['lat'] : '10.855'; ?>, <?php echo !empty($res['lng']) ? $res['lng'] : '10.630'; ?>)"
                                            class="border border-red-500 text-red-600 hover:bg-red-500 hover:text-white px-2.5 py-1 rounded text-xs font-semibold shadow-xs transition-colors flex items-center gap-1 shrink-0">
                                        <i class="fas fa-diamond-turn-right text-[10px]"></i> Chỉ đường đến quán
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-8 text-center text-gray-500">
                            <i class="fas fa-store-slash text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm">Chưa có nhà hàng nào trong cơ sở dữ liệu.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>

            <!-- Right: Map -->
            <div class="w-full lg:w-[65%] xl:w-[68%] bg-gray-200 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden h-[340px] sm:h-[420px] lg:h-full flex flex-col order-1 lg:order-2">
                <!-- Map Container -->
                <div id="map" class="w-full h-full z-10"></div>
                
                <!-- Loading overlay -->
                <div id="map-loading" class="absolute inset-0 bg-white/80 backdrop-blur-xs flex flex-col items-center justify-center z-30 transition-opacity">
                    <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
                    <p class="text-gray-700 font-medium text-sm">Đang định vị và vẽ bản đồ...</p>
                </div>

                <!-- Map Legend / Controls Floating -->
                <div class="absolute bottom-4 left-4 z-20 bg-white/95 backdrop-blur shadow-md border border-gray-200 rounded-lg p-2.5 text-xs text-gray-700 flex flex-col gap-1.5 max-w-xs pointer-events-auto">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-600 inline-block shadow-xs border border-white"></span>
                        <span class="font-semibold text-gray-800">Vị trí của bạn (Kéo thả để đổi)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-600 inline-block shadow-xs border border-white"></span>
                        <span>Quán ăn trong bán kính</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-gray-500 pt-1 border-t border-gray-100">
                        <i class="fas fa-circle-info text-blue-500"></i>
                        <span>Vùng tròn màu xanh là bán kính 5km</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
// Công thức Haversine tính khoảng cách giữa 2 tọa độ (km)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Bán kính trái đất tính bằng km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

document.addEventListener('DOMContentLoaded', function() {
    const loader = document.getElementById('map-loading');
    const statusText = document.getElementById('status-text');
    const statusDot = document.querySelector('#user-location-status span');
    const countBadge = document.getElementById('count-badge');
    const listSummary = document.getElementById('list-summary');
    const noResAlert = document.getElementById('no-restaurant-alert');
    const listContainer = document.getElementById('restaurant-list-container');
    const btnLocate = document.getElementById('btn-locate');
    const btnRadius5km = document.getElementById('btn-radius-5km');
    const btnShowAll = document.getElementById('btn-show-all');
    const btnSampleLoc = document.getElementById('btn-sample-loc');
    const btnNoResSample = document.getElementById('btn-no-res-sample');
    const btnNoResAll = document.getElementById('btn-no-res-all');

    // Chế độ lọc: '5km' hoặc 'all'
    let currentFilterMode = '5km';

    // Tọa độ người dùng mặc định (Quận 12/Gò Vấp TP.HCM, nơi có các quán ăn mẫu)
    let userLat = 10.855;
    let userLng = 106.635;
    let isRealGps = false;

    // Khởi tạo bản đồ Leaflet
    const map = L.map('map', {
        zoomControl: false
    }).setView([userLat, userLng], 13);

    // Zoom control
    L.control.zoom({ position: 'topleft' }).addTo(map);

    // OpenStreetMap Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    // Marker Icons
    const restaurantIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    const userIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [30, 48],
        iconAnchor: [15, 48],
        popupAnchor: [1, -40],
        shadowSize: [45, 45]
    });

    let userMarker = null;
    let radiusCircle = null;
    let restaurantMarkers = [];

    // Cập nhật vị trí người dùng trên bản đồ
    function updateUserMarker(lat, lng, labelText) {
        userLat = lat;
        userLng = lng;

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else {
            userMarker = L.marker([lat, lng], {
                icon: userIcon,
                draggable: true,
                title: "Vị trí của bạn (Kéo thả để di chuyển)"
            }).addTo(map);

            userMarker.bindPopup(`
                <div class="text-center p-1">
                    <p class="font-bold text-blue-600 text-xs mb-1">📍 Vị trí của bạn</p>
                    <p class="text-[11px] text-gray-500">Bạn có thể kéo thả ghim này để chọn vị trí mong muốn</p>
                </div>
            `);

            // Khi người dùng kéo thả ghim vị trí
            userMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                userLat = pos.lat;
                userLng = pos.lng;
                isRealGps = false;
                statusText.innerText = `Đã đổi vị trí (${userLat.toFixed(3)}, ${userLng.toFixed(3)})`;
                statusDot.className = "w-2 h-2 rounded-full bg-blue-500";
                filterAndRender();
            });
        }

        // Vẽ vòng tròn bán kính 5km (5000 mét)
        if (radiusCircle) {
            radiusCircle.setLatLng([lat, lng]);
        } else {
            radiusCircle = L.circle([lat, lng], {
                radius: 5000,
                color: '#2563eb',
                fillColor: '#3b82f6',
                fillOpacity: 0.12,
                weight: 2,
                dashArray: '6, 6'
            }).addTo(map);
        }

        if (currentFilterMode === '5km') {
            radiusCircle.setStyle({ opacity: 1, fillOpacity: 0.12 });
        } else {
            radiusCircle.setStyle({ opacity: 0.3, fillOpacity: 0.05 });
        }
    }

    // Điều hướng chỉ đường đến quán bằng Google Maps
    window.navigateToRestaurant = function(name, lat, lng) {
        let url = '';
        if (typeof userLat !== 'undefined' && typeof userLng !== 'undefined' && userLat && userLng) {
            url = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${lat},${lng}&travelmode=driving`;
        } else {
            url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
        }
        window.open(url, '_blank');
    };

    // Lọc và hiển thị danh sách quán ăn cùng các marker
    function filterAndRender() {
        // Xóa marker cũ
        restaurantMarkers.forEach(m => map.removeLayer(m));
        restaurantMarkers = [];

        const items = Array.from(document.querySelectorAll('.restaurant-item'));
        let visibleCount = 0;

        // Tính khoảng cách cho từng item
        items.forEach(item => {
            const lat = parseFloat(item.getAttribute('data-lat'));
            const lng = parseFloat(item.getAttribute('data-lng'));
            
            const dist = calculateDistance(userLat, userLng, lat, lng);
            item.setAttribute('data-distance', dist);

            const badge = item.querySelector('.distance-badge');
            if (badge) {
                badge.innerText = dist < 1 ? `${Math.round(dist * 1000)} m` : `${dist.toFixed(2)} km`;
                if (dist <= 5.0) {
                    badge.className = "distance-badge shrink-0 bg-green-50 text-green-700 text-[11px] font-bold px-1.5 py-0.5 rounded border border-green-200";
                } else {
                    badge.className = "distance-badge shrink-0 bg-gray-50 text-gray-500 text-[11px] font-medium px-1.5 py-0.5 rounded border border-gray-200";
                }
            }
        });

        // Sắp xếp quán theo khoảng cách gần nhất
        items.sort((a, b) => {
            return parseFloat(a.getAttribute('data-distance')) - parseFloat(b.getAttribute('data-distance'));
        });

        // Đưa lại vào DOM theo thứ tự đã sắp xếp
        items.forEach(item => listContainer.appendChild(item));

        // Lọc hiển thị theo chế độ
        items.forEach(item => {
            const dist = parseFloat(item.getAttribute('data-distance'));
            const isMatch = (currentFilterMode === 'all') || (dist <= 5.0);

            if (isMatch) {
                item.style.display = 'flex';
                visibleCount++;

                const lat = parseFloat(item.getAttribute('data-lat'));
                const lng = parseFloat(item.getAttribute('data-lng'));
                const name = item.querySelector('h3').innerText;
                const img = item.querySelector('img').src;
                const distText = item.querySelector('.distance-badge').innerText;

                const popupContent = `
                    <div class="p-1 min-w-[175px]">
                        <img src="${img}" class="w-full h-24 object-cover rounded mb-2">
                        <h4 class="font-bold text-xs mb-1 text-gray-900">${name}</h4>
                        <div class="flex items-center justify-between text-[11px] mb-2">
                            <span class="text-blue-600 font-bold"><i class="fas fa-location-arrow"></i> ${distText}</span>
                            <span class="text-yellow-500 font-bold">★ 4.5</span>
                        </div>
                        <button type="button" onclick="navigateToRestaurant('${name.replace(/'/g, "\\'")}', ${lat}, ${lng})" class="inline-flex items-center justify-center gap-1.5 w-full text-center bg-primary text-white text-[11px] font-bold py-1.5 rounded hover:bg-primaryDark transition-colors shadow-xs">
                            <i class="fas fa-diamond-turn-right text-[10px]"></i> Chỉ đường đến quán
                        </button>
                    </div>
                `;

                const marker = L.marker([lat, lng], { icon: restaurantIcon })
                    .bindPopup(popupContent, { className: 'custom-popup' })
                    .addTo(map);

                restaurantMarkers.push(marker);

                // Click vào item danh sách -> bay đến marker
                item.onclick = () => {
                    map.flyTo([lat, lng], 15, { duration: 1.2 });
                    setTimeout(() => marker.openPopup(), 1200);
                    items.forEach(i => i.classList.remove('bg-red-50', 'border-l-4', 'border-primary'));
                    item.classList.add('bg-red-50', 'border-l-4', 'border-primary');
                };

                // Click marker -> highlight item
                marker.on('click', () => {
                    items.forEach(i => i.classList.remove('bg-red-50', 'border-l-4', 'border-primary'));
                    item.classList.add('bg-red-50', 'border-l-4', 'border-primary');
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            } else {
                item.style.display = 'none';
            }
        });

        // Cập nhật số lượng và trạng thái
        countBadge.innerText = visibleCount;
        if (currentFilterMode === '5km') {
            listSummary.innerText = `${visibleCount} quán (≤ 5km)`;
        } else {
            listSummary.innerText = `Tất cả (${visibleCount} quán)`;
        }

        // Ẩn/hiển thị thông báo khi không có quán nào
        if (visibleCount === 0) {
            noResAlert.classList.remove('hidden');
            noResAlert.classList.add('flex');
        } else {
            noResAlert.classList.add('hidden');
            noResAlert.classList.remove('flex');
        }

        // Tự động căn chỉnh bản đồ bao gồm vị trí người dùng và các quán
        const boundsGroup = [userMarker, ...restaurantMarkers];
        if (boundsGroup.length > 0) {
            const group = new L.featureGroup(boundsGroup);
            map.fitBounds(group.getBounds().pad(0.15));
        }

        if (loader) loader.style.display = 'none';
    }

    // Hàm gọi Geolocation của trình duyệt
    function requestUserLocation() {
        if (!navigator.geolocation) {
            statusText.innerText = "Trình duyệt không hỗ trợ Geolocation (Dùng vị trí mẫu)";
            statusDot.className = "w-2 h-2 rounded-full bg-yellow-400";
            updateUserMarker(10.855, 106.635);
            filterAndRender();
            return;
        }

        statusText.innerText = "Đang xin quyền định vị GPS...";
        statusDot.className = "w-2 h-2 rounded-full bg-yellow-400 animate-ping";
        if (loader) loader.style.display = 'flex';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                isRealGps = true;
                statusText.innerText = `Đã định vị thành công (${userLat.toFixed(3)}, ${userLng.toFixed(3)})`;
                statusDot.className = "w-2 h-2 rounded-full bg-green-500";
                
                updateUserMarker(userLat, userLng);
                filterAndRender();
            },
            function(error) {
                console.warn("Geolocation error:", error);
                let msg = "Không thể lấy vị trí GPS (Dùng vị trí mẫu TP.HCM)";
                if (error.code === 1) {
                    msg = "Bạn đã từ chối quyền vị trí (Dùng vị trí mẫu TP.HCM)";
                }
                statusText.innerText = msg;
                statusDot.className = "w-2 h-2 rounded-full bg-orange-500";

                // Dùng vị trí mẫu Q.12 / Gò Vấp để hiển thị đẹp mắt
                userLat = 10.855;
                userLng = 106.635;
                isRealGps = false;
                updateUserMarker(userLat, userLng);
                filterAndRender();
            },
            {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            }
        );
    }

    // Click bản đồ để đặt vị trí người dùng tùy ý
    map.on('click', function(e) {
        userLat = e.latlng.lat;
        userLng = e.latlng.lng;
        isRealGps = false;
        statusText.innerText = `Vị trí đã chọn trên bản đồ (${userLat.toFixed(3)}, ${userLng.toFixed(3)})`;
        statusDot.className = "w-2 h-2 rounded-full bg-blue-500";
        updateUserMarker(userLat, userLng);
        filterAndRender();
    });

    // Các sự kiện nút bấm
    btnLocate.addEventListener('click', function() {
        requestUserLocation();
    });

    btnRadius5km.addEventListener('click', function() {
        currentFilterMode = '5km';
        btnRadius5km.className = "bg-blue-600 text-white border border-blue-600 rounded-full px-4 py-2 text-sm font-semibold flex items-center gap-2 shadow-sm transition-all";
        btnShowAll.className = "bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-full px-4 py-2 text-sm font-medium flex items-center gap-2 transition-all";
        filterAndRender();
    });

    btnShowAll.addEventListener('click', function() {
        currentFilterMode = 'all';
        btnShowAll.className = "bg-blue-600 text-white border border-blue-600 rounded-full px-4 py-2 text-sm font-semibold flex items-center gap-2 shadow-sm transition-all";
        btnRadius5km.className = "bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-full px-4 py-2 text-sm font-medium flex items-center gap-2 transition-all";
        filterAndRender();
    });

    function setSampleLocation() {
        userLat = 10.855;
        userLng = 106.635;
        isRealGps = false;
        statusText.innerText = "Vị trí mẫu: Quận 12 / Gò Vấp, TP.HCM";
        statusDot.className = "w-2 h-2 rounded-full bg-green-500";
        currentFilterMode = '5km';
        btnRadius5km.className = "bg-blue-600 text-white border border-blue-600 rounded-full px-4 py-2 text-sm font-semibold flex items-center gap-2 shadow-sm transition-all";
        btnShowAll.className = "bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-full px-4 py-2 text-sm font-medium flex items-center gap-2 transition-all";
        updateUserMarker(userLat, userLng);
        filterAndRender();
    }

    btnSampleLoc.addEventListener('click', setSampleLocation);
    if (btnNoResSample) btnNoResSample.addEventListener('click', setSampleLocation);
    if (btnNoResAll) btnNoResAll.addEventListener('click', function() {
        btnShowAll.click();
    });

    // Bắt đầu định vị ngay khi tải trang
    requestUserLocation();

    // Style custom cho Leaflet popup
    const customStyle = document.createElement('style');
    customStyle.innerHTML = `
        .leaflet-popup-content-wrapper { border-radius: 10px; padding: 0; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .leaflet-popup-content { margin: 8px; }
        .leaflet-container a.leaflet-popup-close-button { color: #6b7280; padding: 4px; }
        .leaflet-control-zoom { border: none !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; border-radius: 8px !important; overflow: hidden; }
    `;
    document.head.appendChild(customStyle);
});
</script>
