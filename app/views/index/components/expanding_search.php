<!-- Lucian UX/UI Prompt #027 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#027 -->
<style>
:root{
  color-scheme:light;
  --ux-page:var(--color-bg, #FCFBF9);
  --ux-surface:var(--color-surface, #FFFFFF);
  --ux-surface-2:#F5F3EF;
  --ux-surface-3:#EAE7E1;
  --ux-ink:var(--color-text-main, #16151B);
  --ux-ink-2:var(--color-text-muted, #57535F);
  --ux-ink-3:#8E8A97;
  --ux-line:rgba(22,21,27,.09);
  --ux-line-2:rgba(22,21,27,.17);
  --ux-accent:#dc2626;
  --ux-accent-2:#b91c1c;
  --ux-accent-soft:rgba(220,38,38,.12);
  --ux-accent-ink:#FFFFFF;
  --ux-mint:#119C72;
  --ux-warn:#D6731E;
  --ux-danger:#D93A55;
  --ux-glass:rgba(255,255,255,.58);
  --ux-glass-line:rgba(255,255,255,.85);
  --ux-shadow:rgba(40,30,80,.14);
  --ux-shadow-strong:rgba(40,30,80,.3);
  --ux-grad:linear-gradient(120deg,#dc2626,#f97316 55%,#eab308);
  --ux-ease:cubic-bezier(.2,.8,.2,1);
  --ux-spring:cubic-bezier(.34,1.45,.64,1);
}
@media (prefers-color-scheme:dark){
  :root:not([data-theme="light"]){
    color-scheme:dark;
    --ux-page:#07080D;
    --ux-surface:#13141C;
    --ux-surface-2:#1A1B25;
    --ux-surface-3:#252632;
    --ux-ink:#F4F0E8;
    --ux-ink-2:rgba(244,240,232,.68);
    --ux-ink-3:rgba(244,240,232,.42);
    --ux-line:rgba(255,255,255,.08);
    --ux-line-2:rgba(255,255,255,.16);
    --ux-accent:#f87171;
    --ux-accent-2:#fb923c;
    --ux-accent-soft:rgba(248,113,113,.15);
    --ux-accent-ink:#0B0C12;
    --ux-mint:#7EE8C0;
    --ux-warn:#FFB26B;
    --ux-danger:#FF7D8C;
    --ux-glass:rgba(255,255,255,.07);
    --ux-glass-line:rgba(255,255,255,.18);
    --ux-shadow:rgba(0,0,0,.45);
    --ux-shadow-strong:rgba(0,0,0,.72);
    --ux-grad:linear-gradient(120deg,#f87171,#fb923c 55%,#fde047);
  }
}
:root[data-theme="dark"]{
  color-scheme:dark;
  --ux-page:#07080D;
  --ux-surface:#13141C;
  --ux-surface-2:#1A1B25;
  --ux-surface-3:#252632;
  --ux-ink:#F4F0E8;
  --ux-ink-2:rgba(244,240,232,.68);
  --ux-ink-3:rgba(244,240,232,.42);
  --ux-line:rgba(255,255,255,.08);
  --ux-line-2:rgba(255,255,255,.16);
  --ux-accent:#f87171;
  --ux-accent-2:#fb923c;
  --ux-accent-soft:rgba(248,113,113,.15);
  --ux-accent-ink:#0B0C12;
  --ux-mint:#7EE8C0;
  --ux-warn:#FFB26B;
  --ux-danger:#FF7D8C;
  --ux-glass:rgba(255,255,255,.07);
  --ux-glass-line:rgba(255,255,255,.18);
  --ux-shadow:rgba(0,0,0,.45);
  --ux-shadow-strong:rgba(0,0,0,.72);
  --ux-grad:linear-gradient(120deg,#f87171,#fb923c 55%,#fde047);
}

.ux,.ux *,.ux *::before,.ux *::after{box-sizing:border-box}
.ux{
  font-family:inherit;
  color:var(--ux-ink);-webkit-font-smoothing:antialiased;letter-spacing:-.01em;
  -webkit-tap-highlight-color:transparent;
  word-break:keep-all;overflow-wrap:break-word;
}

:where(.ux) :is(button,input,textarea,select){font:inherit;color:inherit}
:where(.ux) button{cursor:pointer}
.ux :focus-visible{outline:2px solid var(--ux-accent);outline-offset:3px}
.ux [hidden]{display:none!important}

@media (prefers-reduced-motion:reduce){
  .ux,.ux *,.ux *::before,.ux *::after{
    animation-duration:.001ms!important;animation-iteration-count:1!important;
    transition-duration:.001ms!important;scroll-behavior:auto!important;
  }
}
</style>

<style>
.u027{
  position:relative;
  width:40px;
  height:40px;
  display:inline-flex;
  align-items:center;
  justify-content:flex-end;
  vertical-align:middle;
}
.u027-field{
  position:absolute;
  right:0;
  top:0;
  height:40px;
  width:40px;
  border-radius:999px;
  background:var(--ux-surface-2);
  border:1px solid var(--ux-line);
  overflow:hidden;
  transition:width .5s var(--ux-spring),background-color .3s,box-shadow .3s,border-color .3s;
  z-index:25;
}
.u027.is-open .u027-field{
  width:380px;
  max-width:calc(100vw - 120px);
  background:var(--ux-surface);
  border-color:var(--ux-accent);
  box-shadow:inset 0 0 0 1.5px var(--ux-accent),0 14px 30px -10px var(--ux-shadow-strong);
  border-radius:14px;
}
.u027-go{
  position:absolute;
  left:0;
  top:0;
  width:40px;
  height:40px;
  border:0;
  padding:0;
  border-radius:999px;
  background:none;
  color:var(--ux-ink);
  display:grid;
  place-items:center;
  z-index:30;
  cursor:pointer;
  transition:color .2s, background-color .2s;
  user-select:none;
  touch-action:manipulation;
  pointer-events:auto;
}
.u027-go:hover{
  color:var(--ux-accent);
}
.u027.is-open .u027-go{
  color:var(--ux-accent);
}
.u027.is-open .u027-go:hover{
  background:var(--ux-accent-soft);
}
.u027-go:focus-visible{
  outline:2px solid var(--ux-accent);
  outline-offset:2px;
}
.u027-go svg, .u027-x svg{
  width:18px;
  height:18px;
  fill:none;
  stroke:currentColor;
  stroke-width:2.2;
  stroke-linecap:round;
  pointer-events:none;
}
.u027-in{
  position:absolute;
  left:40px;
  right:40px;
  top:0;
  height:40px;
  border:0;
  outline:0;
  background:none;
  padding:0 4px;
  font-size:14px;
  color:var(--ux-ink);
  opacity:0;
  pointer-events:none;
  transition:opacity .3s var(--ux-ease) .12s;
}
.u027-in::placeholder{
  color:var(--ux-ink-3);
  font-size:13px;
}
.u027.is-open .u027-in{
  opacity:1;
  pointer-events:auto;
}
.u027-x{
  position:absolute;
  right:3px;
  top:3px;
  width:34px;
  height:34px;
  border:0;
  padding:0;
  border-radius:10px;
  background:var(--ux-surface-2);
  color:var(--ux-ink-2);
  display:grid;
  place-items:center;
  opacity:0;
  pointer-events:none;
  visibility:hidden;
  transform:scale(.6) rotate(-90deg);
  transition:opacity .25s,transform .5s var(--ux-spring),visibility 0s .25s;
  cursor:pointer;
  z-index:28;
  user-select:none;
  touch-action:manipulation;
}
.u027.is-open .u027-x{
  opacity:1;
  pointer-events:auto;
  visibility:visible;
  transform:none;
  transition:opacity .25s,transform .5s var(--ux-spring),visibility 0s;
}
.u027-x:hover{
  background:var(--ux-surface-3);
  color:var(--ux-ink);
}
.u027-x:focus-visible{
  outline:2px solid var(--ux-accent);
  outline-offset:1px;
}
.u027-pop{
  position:absolute;
  right:0;
  top:48px;
  width:380px;
  max-width:calc(100vw - 32px);
  padding:14px 12px 10px;
  border-radius:18px;
  background:var(--ux-surface);
  box-shadow:0 0 0 1px var(--ux-line),0 28px 50px -20px var(--ux-shadow-strong);
  transform-origin:90% 0;
  opacity:0;
  transform:translateY(-10px) scale(.96);
  pointer-events:none;
  visibility:hidden;
  transition:opacity .25s var(--ux-ease),transform .45s var(--ux-spring),visibility 0s .45s;
  z-index:50;
}
.u027.is-open .u027-pop{
  opacity:1;
  transform:none;
  pointer-events:auto;
  visibility:visible;
  transition:opacity .3s var(--ux-ease) .1s,transform .55s var(--ux-spring) .1s;
}
.u027-sec{
  margin:0 8px 8px;
  font-size:11.5px;
  font-weight:700;
  letter-spacing:.04em;
  color:var(--ux-ink-3);
  text-transform:uppercase;
}
.u027-recent{
  display:flex;
  gap:6px;
  flex-wrap:wrap;
  margin:0 6px 12px;
}
.u027-recent button{
  height:30px;
  padding:0 12px;
  border:0;
  border-radius:999px;
  background:var(--ux-surface-2);
  font-size:12.5px;
  font-weight:620;
  color:var(--ux-ink-2);
  cursor:pointer;
  transition:background-color .2s,color .2s;
  touch-action:manipulation;
}
.u027-recent button:hover{
  background:var(--ux-surface-3);
  color:var(--ux-ink);
}
.u027-list{
  margin:0;
  padding:0;
  list-style:none;
  max-height:180px;
  overflow-y:auto;
}
.u027-opt{
  display:flex;
  align-items:center;
  gap:12px;
  padding:8px 10px;
  border-radius:12px;
  cursor:pointer;
  opacity:0;
  transform:translateY(6px);
  transition:background-color .2s var(--ux-ease),opacity .3s var(--ux-ease),transform .45s var(--ux-spring);
  text-decoration:none;
  color:inherit;
}
.u027.is-open .u027-opt{
  opacity:1;
  transform:none;
}
.u027-opt[aria-selected="true"],.u027-opt:hover{
  background:var(--ux-surface-2);
}
.u027-opt i{
  width:34px;
  height:34px;
  flex:none;
  border-radius:10px;
  background:var(--c);
  display:inline-block;
}
.u027-opt b{
  display:block;
  font-size:13.5px;
  font-weight:650;
  color:var(--ux-ink);
}
.u027-opt b mark{
  background:none;
  color:var(--ux-accent);
  font-weight:800;
}
.u027-opt small{
  display:block;
  font-size:11.5px;
  color:var(--ux-ink-3);
}
.u027-empty{
  padding:24px 8px;
  text-align:center;
  font-size:13px;
  color:var(--ux-ink-3);
}
.u027-in:focus-visible{
  outline:none;
}
</style>

<div class="ux u027" id="expanding-search-wrapper">
  <form id="global-search-form" action="<?php echo BASE_URL; ?>search" method="GET" class="u027-field">
    <button class="u027-go" type="button" aria-label="Open search" aria-expanded="false" title="Tìm kiếm món ăn, nhà hàng"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.2 4.2"/></svg></button>
    <input class="u027-in" id="search-keyword" name="keyword" type="text" role="combobox" aria-expanded="false" aria-autocomplete="list" placeholder="Tìm món ăn, nhà hàng, đặc sản..." tabindex="-1" autocomplete="off" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
    <button class="u027-x" type="button" aria-label="Close search" tabindex="-1"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7l10 10M17 7L7 17"/></svg></button>
  </form>
  <div class="u027-pop">
    <p class="u027-sec">Tìm kiếm phổ biến</p>
    <div class="u027-recent">
      <button type="button">Phở Hà Nội</button>
      <button type="button">Bún Bò Huế</button>
      <button type="button">Cơm Tấm</button>
      <button type="button">Lẩu mắm</button>
      <button type="button">Quận 1</button>
    </div>
    <p class="u027-sec">Gợi ý cho bạn</p>
    <ul class="u027-list" role="listbox" aria-label="Search results"></ul>
  </div>
</div>

<script>
(function () {
  var root = document.getElementById('expanding-search-wrapper');
  if (!root) return;
  var go = root.querySelector('.u027-go');
  var input = root.querySelector('.u027-in');
  var x = root.querySelector('.u027-x');
  var list = root.querySelector('.u027-list');
  var form = root.querySelector('#global-search-form');
  var uid = 'u027-' + Math.random().toString(36).slice(2, 7);
  list.id = uid;
  input.setAttribute('aria-controls', uid);
  
  var DATA = [
    ['Phở Hà Nội', 'Đặc sản Miền Bắc · Hương vị truyền thống', 'linear-gradient(140deg,#ef4444,#f97316)', '<?php echo BASE_URL; ?>mon-an/pho-ha-noi'],
    ['Bún Bò Huế', 'Đặc sản Miền Trung · Đậm đà xứ Huế', 'linear-gradient(140deg,#f97316,#eab308)', '<?php echo BASE_URL; ?>mon-an/bun-bo-hue'],
    ['Cơm Tấm Sài Gòn', 'Đặc sản Miền Nam · Sườn bì chả nướng', 'linear-gradient(140deg,#10b981,#06b6d4)', '<?php echo BASE_URL; ?>mon-an/com-tam-sai-gon'],
    ['Mì Quảng', 'Đặc sản Miền Trung · Tôm thịt đậm vị', 'linear-gradient(140deg,#f59e0b,#ef4444)', '<?php echo BASE_URL; ?>mon-an/mi-quang'],
    ['Lẩu Mắm Miền Tây', 'Đặc sản Nam Bộ · Đồng quê thanh mát', 'linear-gradient(140deg,#8b5cf6,#ec4899)', '<?php echo BASE_URL; ?>mon-an/lau-mam-mien-tay'],
    ['Bún Chả Hà Nội', 'Đặc sản Miền Bắc · Than hoa thơm nức', 'linear-gradient(140deg,#ef4444,#dc2626)', '<?php echo BASE_URL; ?>mon-an/bun-cha-ha-noi'],
    ['Việt Phố', 'Nhà hàng Hải sản tươi sống · Q.3', 'linear-gradient(140deg,#3b82f6,#1d4ed8)', '<?php echo BASE_URL; ?>search?keyword=Việt+Phố'],
    ['Hoàng Yến Cuisine', 'Nhà hàng Gọi món Việt · Q.1', 'linear-gradient(140deg,#6366f1,#8b5cf6)', '<?php echo BASE_URL; ?>search?keyword=Hoàng+Yến'],
    ['Cao Lầu Hội An', 'Đặc sản Miền Trung · Phố cổ', 'linear-gradient(140deg,#f97316,#d97706)', '<?php echo BASE_URL; ?>mon-an/cao-lau-hoi-an'],
    ['Bánh Xèo Miền Tây', 'Đặc sản Miền Nam · Giòn rụm cuốn rau', 'linear-gradient(140deg,#eab308,#84cc16)', '<?php echo BASE_URL; ?>mon-an/banh-xeo-mien-tay'],
    ['King BBQ Buffet', 'Buffet Lẩu Nướng · Tân Bình', 'linear-gradient(140deg,#ec4899,#f43f5e)', '<?php echo BASE_URL; ?>search?keyword=King+BBQ'],
    ['Cá Kho Làng Vũ Đại', 'Đặc sản Hà Nam · Niêu đất củi nhãn', 'linear-gradient(140deg,#78350f,#b45309)', '<?php echo BASE_URL; ?>mon-an/ca-kho-lang-vu-dai']
  ];
  
  var shown = [], act = -1;
  function esc(s) { return s.replace(/[&<>]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c]; }); }
  function removeAccents(str) {
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  }

  function render() {
    var rawQ = input.value.trim();
    var qNorm = removeAccents(rawQ);
    shown = DATA.filter(function (d) {
      if (!rawQ) return true;
      var titleNorm = removeAccents(d[0]);
      var descNorm = removeAccents(d[1]);
      return titleNorm.indexOf(qNorm) >= 0 || descNorm.indexOf(qNorm) >= 0 || d[0].toLowerCase().indexOf(rawQ.toLowerCase()) >= 0;
    }).slice(0, 3);

    act = shown.length ? 0 : -1;
    if (!shown.length) {
      list.innerHTML = '<li class="u027-empty">Không tìm thấy kết quả cho ‘' + esc(rawQ) + '’</li>';
      input.removeAttribute('aria-activedescendant');
      return;
    }

    list.innerHTML = shown.map(function (d, i) {
      var t = esc(d[0]);
      if (rawQ) {
        var idx = removeAccents(d[0]).indexOf(qNorm);
        if (idx >= 0) {
          var matchedPart = d[0].slice(idx, idx + rawQ.length);
          t = esc(d[0].slice(0, idx)) + '<mark>' + esc(matchedPart) + '</mark>' + esc(d[0].slice(idx + rawQ.length));
        }
      }
      return '<li class="u027-opt" role="option" id="' + uid + '-' + i + '" aria-selected="' + (i === act) + '" style="transition-delay:' + (i * 40) + 'ms"><i style="--c:' + d[2] + '" aria-hidden="true"></i><span><b>' + t + '</b><small>' + esc(d[1]) + '</small></span></li>';
    }).join('');
    mark();
  }

  function mark() {
    Array.prototype.forEach.call(list.children, function (li, i) {
      if (li.setAttribute) li.setAttribute('aria-selected', String(i === act));
    });
    if (act >= 0) input.setAttribute('aria-activedescendant', uid + '-' + act);
    else input.removeAttribute('aria-activedescendant');
  }

  function openSearch(focus) {
    if (root.classList.contains('is-open')) return;
    root.classList.add('is-open');
    go.setAttribute('aria-expanded', 'true');
    go.setAttribute('title', 'Nhấn để tìm kiếm');
    input.setAttribute('aria-expanded', 'true');
    input.tabIndex = 0;
    x.tabIndex = 0;
    render();
    if (focus !== false) {
      setTimeout(function () { input.focus(); }, 120);
    }
  }

  function closeSearch() {
    if (!root.classList.contains('is-open')) return;
    root.classList.remove('is-open');
    go.setAttribute('aria-expanded', 'false');
    go.setAttribute('title', 'Tìm kiếm món ăn, nhà hàng');
    input.setAttribute('aria-expanded', 'false');
    input.tabIndex = -1;
    x.tabIndex = -1;
    input.value = '';
    render();
    go.focus();
  }

  function executeSearch() {
    var q = input.value.trim();
    if (q.length > 0) {
      window.location.href = '<?php echo BASE_URL; ?>search?keyword=' + encodeURIComponent(q);
    } else {
      window.location.href = '<?php echo BASE_URL; ?>search';
    }
  }

  // Click vào thanh tìm kiếm khi đang đóng sẽ luôn mở ra
  form.addEventListener('click', function (e) {
    if (!root.classList.contains('is-open')) {
      e.preventDefault();
      e.stopPropagation();
      openSearch(true);
    }
  });

  // Click vào nút kính lúp
  go.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (!root.classList.contains('is-open')) {
      openSearch(true);
    } else {
      executeSearch();
    }
  });

  // Nút đóng (X)
  x.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    closeSearch();
  });

  // Chặn nổi bọt bên trong component
  root.addEventListener('click', function (e) {
    e.stopPropagation();
  });

  input.addEventListener('input', render);

  // Phím tắt bàn phím
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      e.preventDefault();
      closeSearch();
    } else if (e.key === 'ArrowDown' && shown.length) {
      e.preventDefault();
      act = (act + 1) % shown.length;
      mark();
    } else if (e.key === 'ArrowUp' && shown.length) {
      e.preventDefault();
      act = (act - 1 + shown.length) % shown.length;
      mark();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (act >= 0 && shown[act]) {
        window.location.href = shown[act][3];
      } else {
        executeSearch();
      }
    }
  });

  // Xử lý gửi form
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    executeSearch();
  });

  // Chọn gợi ý từ danh sách
  list.addEventListener('click', function (e) {
    var li = e.target.closest('.u027-opt');
    if (!li) return;
    var i = Array.prototype.indexOf.call(list.children, li);
    if (shown[i]) {
      window.location.href = shown[i][3];
    }
  });

  // Chọn tag tìm kiếm phổ biến
  Array.prototype.forEach.call(root.querySelectorAll('.u027-recent button'), function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var val = b.textContent.trim();
      input.value = val;
      render();
      window.location.href = '<?php echo BASE_URL; ?>search?keyword=' + encodeURIComponent(val);
    });
  });

  // Click ra ngoài để đóng popup gợi ý
  document.addEventListener('click', function (e) {
    if (root.classList.contains('is-open') && !root.contains(e.target)) {
      closeSearch();
    }
  });
})();
</script>
