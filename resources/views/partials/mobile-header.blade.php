{{-- resources/views/partials/mobile-header.blade.php --}}

<style>
/* ==== Mobile Header CSS ==== */
.mobile-header {
  position: fixed;
    z-index: 2;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
}
.mobile-header .mh-top { height:56px; }
.mobile-header .mh-offcanvas {
  position:fixed;
  top:0; right:-100%; left:auto;
  width:80%; height:100%;
  background:#fff;
  transition:right .3s ease;
  overflow-y:auto;
  overflow-x: hidden;    
  touch-action: pan-y;
}
.mobile-header .mh-offcanvas.open { right:0; }
.mobile-header .mh-oc-header {
  height:56px;
  border-bottom:1px solid #eee;
}

/* ==== Slide giữa Level 1 & 2 ==== */
#mh-level1,
#mh-level2 {
  position:absolute; top:56px; left:0;
  width:100%;
  transition:left .3s ease;
  font-size: 1rem;
}
#mh-level2 {
  left:100%;
}
.mh-offcanvas.show-lvl2 #mh-level1 { left:-100%; }
.mh-offcanvas.show-lvl2 #mh-level2 { left:0; }

/* ==== Group li flex-column & sản phẩm slide ==== */
.mobile-header .mh-nav-lvl2 li {
  display: flex;
  flex-direction: column;
  border-bottom:1px solid #f1f1f1;
  margin:0;
  padding:0;
}
.mobile-header .mh-nav-lvl2 .group-header {
  padding:.75rem 1rem;
  cursor: pointer;
  display:flex;
  justify-content:space-between;
  align-items:center;

  font-size: 1rem;
}
.mobile-header .mh-products {
  overflow: hidden;
  max-height: 0;
  transition: max-height .3s ease;
}
.mobile-header .mh-products.expand {
  max-height: 500px; /* đủ lớn để chứa sản phẩm */
}

/* Back button */
.mobile-header .mh-back {
  padding:.75rem 1rem;
  border-bottom:1px solid #f1f1f1;
  cursor:pointer;
  color: white;
  font-size: 0.9rem;
    font-weight: 100;
    -webkit-text-stroke: 1px #4ab3af;
}
.so-trong-gio {
    font-size:0.7rem;
    font-weight: 100;
    padding-top: 8px;
}
.mh-icons i {
    font-size:1.3rem;

}
/* ==== Search panel slide-in ==== */
.mh-search-panel {
  position: fixed;
  top: 0; right: -100%; left: auto;
  width: 100%; height: 100%;
  background: #fff;
  z-index: 1100;
  transition: right .3s ease;
  overflow-y: auto;
}
.mh-search-panel.open {
  right: 0;
}
.mh-search-panel .search-header {
  height: 56px;
  border-bottom: 1px solid #eee;
  display: flex;
  align-items: center;
  padding: 0 .75rem;
}
.mh-search-panel .search-header input {
  border: none;
  box-shadow: none;
  outline: none;
  font-size: 1rem;
  margin-left: .5rem;
  flex: 1;
}
.icon-cont {
    width: 10rem;
    justify-content: space-between;
}
.fa-magnifying-glass {
    color: #fe3b27;
}
.bi-bag-fill {
    color: #ffba26;
}
.fa-user {
    color: #4ab3af;
}
.fa-bars {
    color: #333333;
    background-color: transparent;
}
.fa-heart {
    color: #b83232 !important;
}
.fa-times {
    font-size: 1.5rem;
    -webkit-text-stroke: 2px #b83232;
    color: white;
}

#mh-sec-title {
    font-size: 1.5rem;
    color: #b83232;
    -webkit-text-stroke: 1px #b83232;
}

.dropdown-menu {
  transform: translate3d(80px, 32px, 0px) !important;
}

.dropdown-menu-mobile {
  position: fixed !important;
  top: 56px;
  right: 5px;
  left: auto;
  width: 260px;
  transform: translateY(-10px);
  opacity: 0;
  visibility: hidden;
  transition:
    transform 0.3s ease,
    opacity 0.3s ease,
    visibility 0.3s;
}
.fa-question-circle {
  color: #4ab3af;
}
.fa-box-open {
  color: #78b865;
}
.dropdown-menu-mobile.open {
  transform: translateY(0);
  opacity: 1;
  visibility: visible;
  background-color: #ffffff;
  border-radius: 5px;
  border: 1px solid #ffba26;
}
.nut-gio-hang {
  padding: 0.3rem 1rem;
  font-size: 0.8rem;
}
.input-group {
  border: 1px solid #4ab3af;
  border-radius: 100px;
}
.input-group input {
  border-radius: 100px;

}
.input-group button {
  border-radius: 100px;
    background-color: #4ab3af;
    border-color: #4ab3af;
    border: 1px solid #4ab3af;

}
.input-group button i {
  display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}
#mh-search-input::placeholder {
  color: #4ab3af;
  font-style: italic;
}
.text-goi-y {
  color: white;
    background: #4ab3af;
    border-radius: 50px;
}

#mh-btn-close {
  background-color: transparent;
}
.quick-access {
  margin-top:5rem;
}
.quick-access i {
  width: 2rem;
  font-size: 1.5rem;
}
.quick-access a {
  color: #4ab3af;
    font-size: 1.1rem;
    font-weight: 600;
}
.quick-access .fa-heart {
  color: #fe3b27 !important;
}
.quick-access .bi-bag-fill {
  color: #ffba26;
}
.danh-sach-header {
  font-size: 1.2rem;
    font-weight: 900;
    color: #333333; 
}
.list-tang-2 {
  font-weight: 900;
  color: #333333;
}
.list-tang-2-a {
  color: #4ab3af;
  font-weight: 700;
}
.nut-aside {
  padding: 0.2rem 1rem;
    text-align: center;
    align-items: center;
    height: 2.3rem;
    width: 8rem;
    font-weight: 800;
}
.nut-dang-xuat {
  position: absolute;
    top: -7%;
    left: 0.5rem;
    -webkit-text-stroke: 2px #ffba26;
    color: white
}
/* ==== Overlay cho aside ==== */
.mh-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;          /* Thấp hơn aside nhưng cao hơn nội dung */
  display: none;
}
.mh-overlay.show {
  display: block;
}
/* Đẩy aside lên trên overlay */
.mh-offcanvas {
  z-index: 1001;
}

</style>

<div class="mobile-header d-block d-lg-none">
  {{-- Top bar --}}
  <div class="mh-top d-flex align-items-center justify-content-between px-3">
    <a href="{{ url('/') }}"><img style="height:2.5rem;"  src="https://tiemhoamimi.com/image/mimi-logo.webp" height="24" alt="Logo"></a>
    <div class="mh-icons d-flex align-items-center icon-cont">
      {{-- 1) Search --}}
      <button id="mh-search-toggle"
                class="d-flex justify-content-center align-items-center border-0 bg-transparent p-0 m-0">
            <i class="fa-solid fa-magnifying-glass"></i>
      </button>

      {{-- 2) Yêu thích --}}
      <a href="{{ route('favorites.index') }}" class="d-flex justify-content-center align-items-center "><i class="fas fa-heart"></i></a>
      {{-- 3) Giỏ hàng --}}
      <div class="dropdown">
        <a href="#" id="cartDropdownMobile" class="position-relative d-flex justify-content-center align-items-center">
          <i class="bi bi-bag-fill d-flex justify-content-center align-items-center"></i>
           <span id="cart-count-mobile"
                class="badge rounded-pill position-absolute top-50 start-50 translate-middle so-trong-gio"
                style="{{ $cartCount ? '' : 'display:none' }}">
            {{ $cartCount }}
          </span>
        </a>
        <div id="cartMenuMobile" class="dropdown-menu-mobile p-3" style="z-index:1200">
          @include('partials.mobile-cart-menu')
        </div>

      </div>
      {{-- 4) Tài khoản --}}
      @guest
        <a href="{{ route('login') }}" class="d-flex justify-content-center align-items-center"><i class="fa-solid fa-user"></i></a>
      @else
        <a href="{{ route('profile.edit') }}" class="d-flex justify-content-center align-items-center"><i class="fa-solid fa-user"></i></a>
      @endguest
      {{-- 5) Hamburger --}}
      <button style= "background-color: transparent;" id="mh-btn-toggle" class="d-flex justify-content-center align-items-center border-0 bg-transparen p-0 m-0"><i  class="fa-solid fa-bars"></i></button>
    </div>
  </div>

  {{-- Offcanvas --}}
  {{-- Lớp phủ tối mờ khi mở menu --}}
  <div id="mh-overlay" class="mh-overlay"></div>
  <aside id="mh-offcanvas" class="mh-offcanvas">
    <div class="mh-oc-header d-flex align-items-center justify-content-end">
      <button id="mh-btn-close" class="d-flex align-items-center px-3 justify-content-center border-0"><i class="fa fa-times fa-lg"></i></button>
      <!-- <h5 class="mb-0">Menu</h5> -->
    </div>
    

    {{-- Level 1 --}}
    <ul id="mh-level1" class="mh-nav-lvl1 list-unstyled mb-0">
      <div class="ms-3 me-2 mt-4 mb-4 d-flex justify-content-center align-items-center gap-3 flex-column">
            @guest
              <!-- <i class="fa-solid fa-user-plus"></i> -->
              <div class="d-flex gap-2">
                <a href="{{ route('register') }}"
                  class="nut-vang nut-aside">Đăng ký</a>
                <a href="{{ route('login') }}"
                  class="nut-xanh nut-aside">Đăng nhập</a>
              </div>
            @else
              <p class="m-0 p-0">Hi, <strong style="color: #4ab3af;">{{ Auth::user()->name }}</strong></p>
              @auth
                <form method="POST" action="{{ route('logout') }}" class="">
                  @csrf
                  <button type="submit"
                          class="border-0 bg-transparent nut-dang-xuat">
                          <i class="fa-solid fa-right-from-bracket fs-4 "></i>
                  </button>
                </form>
              @endauth
            @endguest

           

      </div>
      @foreach($sections as $sec)
        <li data-id="{{ $sec->id }}" class="d-flex justify-content-between align-items-center px-3 py-2 danh-sach-header">
          <span>{{ $sec->name }}</span>
          <i class="fa fa-chevron-right"></i>
        </li>
      @endforeach
      {{-- Thêm mục TOÀN BỘ --}}
        <li class="px-3 py-2 danh-sach-header">
          <a href="{{ route('products.index') }}"
            class="d-flex justify-content-between align-items-center text-dark text-decoration-none mau-den-rong">
            <span>Toàn bộ sản phẩm</span>
            <i class="fa fa-chevron-right"></i>
          </a>
        </li>
        {{-- ==== User CTA & Quick Links ==== --}}
          <div class="px-3 py-3 quick-access">
            

            <ul class="list-unstyled mb-0">
              <li class="d-flex align-items-center py-2">
                <i class="fa fa-question-circle me-2"></i>
                <a href="/help" class="text-decoration-none">Trợ giúp</a>
              </li>
              <li class="d-flex align-items-center py-2">
                <i class="bi bi-bag-fill me-2"></i>
                <a href="{{ route('cart.index') }}" class="text-decoration-none">
                  Giỏ hàng
                  <!-- @if($cartCount)
                    <span class="badge bg-danger ms-2">{{ $cartCount }}</span>
                  @endif -->
                </a>
              </li>
              <li class="d-flex align-items-center py-2">
                <i class="fas fa-heart me-2"></i>
                <a href="{{ route('favorites.index') }}" class="text-decoration-none">
                  Yêu thích
                </a>
              </li>
              @auth
              <li class="d-flex align-items-center py-2">
                <i class="fa fa-box-open me-2"></i>
                <a href="{{ route('profile.edit') }}#orders-m" class="text-decoration-none">
                  Đơn hàng
                </a>
              </li>
              @endauth
            </ul>
          </div>
          {{-- ==== end User CTA & Quick Links ==== --}}
    </ul>

    {{-- Level 2 --}}
    <div id="mh-level2" class="mh-nav-lvl2">
      <div id="mh-btn-back" class="mh-back"><i class="fa fa-chevron-left me-2"></i>Quay lại</div>
      <h6 id="mh-sec-title" class="px-3 py-2 pb-2"></h6>
      <ul id="mh-groups" class="list-unstyled mb-0"></ul>
    </div>
  </aside>
  {{-- Search Offcanvas --}}
    <aside id="mh-search-panel" class="mh-search-panel">
        <form id="mh-search-form" action="{{ route('products.index') }}" method="GET">
            <div class="search-header d-flex align-items-center px-3 justify-content-center">
            <button id="mh-search-close" type="button" class="btn btn-link p-0 me-2 d-flex justify-content-center align-items-center text-decoration-none">
                <i class="fa fa-times fa-lg"></i>
            </button>
            <div class="input-group w-100">
                <input type="text"
                    name="q"
                    id="mh-search-input"
                    class="form-control"
                    placeholder="Search…"
                    value="{{ request('q') }}">
                <button type="submit" class="">
                <i class="bi bi-search"></i>
                </button>
            </div>
            </div>

            <div class="p-3">
            <p class="mb-2 fw-bold">Từ Khóa Phổ Biến</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge text-goi-y">Đèn Ngủ Hoa Tulyp</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge text-goi-y">Gấu Bông</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge text-goi-y">Hoa Gấu Bông</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge text-goi-y">Đèn Ngủ Đám Mây</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge text-goi-y">Quạt Mini Tích Điện</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge text-goi-y">Phụ Kiện Cute</a>
            </div>
            </div>
        </form>
    </aside>


</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sections     = @json($sections);
  const offcanvas    = document.getElementById('mh-offcanvas');
  const overlay      = document.getElementById('mh-overlay');
  const lvl1         = document.getElementById('mh-level1');
  const lvl2         = document.getElementById('mh-level2');
  const groupsEl     = document.getElementById('mh-groups');
  const secTitle     = document.getElementById('mh-sec-title');
  const btnToggle    = document.getElementById('mh-btn-toggle');
  const btnClose     = document.getElementById('mh-btn-close');
  const btnBack      = document.getElementById('mh-btn-back');
  const searchToggle = document.getElementById('mh-search-toggle');
  const searchPanel  = document.getElementById('mh-search-panel');
  const searchClose  = document.getElementById('mh-search-close');
  const cartToggle   = document.getElementById('cartDropdownMobile');
  const cartMenu     = document.getElementById('cartMenuMobile');

  // --- Hàm điều khiển menu ---
  function openMenu() {
    offcanvas.classList.add('open');
    offcanvas.classList.remove('show-lvl2');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    offcanvas.classList.remove('open', 'show-lvl2');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
  }

  // --- Hàm điều khiển search panel ---
  function openSearch() {
    closeMenu();
    searchPanel.classList.add('open');
  }
  function closeSearch() {
    searchPanel.classList.remove('open');
  }

  // --- Sự kiện mở/đóng menu & overlay ---
  btnToggle.addEventListener('click', e => {
    e.stopPropagation();
    openMenu();
  });
  btnClose.addEventListener('click', e => {
    e.stopPropagation();
    closeMenu();
  });
  overlay.addEventListener('click', closeMenu);

  // --- Sự kiện mở/đóng search panel ---
  searchToggle.addEventListener('click', e => {
    e.stopPropagation();
    openSearch();
  });
  searchClose.addEventListener('click', closeSearch);

  // --- Click ngoài để đóng menu & search ---
  document.addEventListener('click', e => {
    if (!offcanvas.contains(e.target) && !btnToggle.contains(e.target)) {
      closeMenu();
    }
    if (!searchPanel.contains(e.target) && !searchToggle.contains(e.target)) {
      closeSearch();
    }
  });

  // --- Chuyển Level 1 → Level 2 ---
  lvl1.querySelectorAll('li[data-id]').forEach(li => {
    li.addEventListener('click', () => {
      const sec = sections.find(s => s.id == li.dataset.id);
      secTitle.textContent = sec.name;

      // Build nhóm + sản phẩm
      groupsEl.innerHTML = sec.groups.map(g => `
        <li>
          <div class="group-header d-flex justify-content-between align-items-center py-2 px-3 list-tang-2">
            <span>${g.title}</span>
            <i class="fa fa-chevron-down"></i>
          </div>
          <ul class="mh-products list-unstyled mb-0">
            ${g.products.map(p =>
              `<li class="py-1 px-4"><a class="list-tang-2-a" href="/products/${p.slug}">${p.name}</a></li>`
            ).join('')}
          </ul>
        </li>
      `).join('');

      // Toggle slide sản phẩm trong mỗi nhóm
      groupsEl.querySelectorAll('.group-header').forEach(header => {
        header.addEventListener('click', () => {
          const icon = header.querySelector('i');
          const prodUl = header.nextElementSibling;
          const expanded = prodUl.classList.toggle('expand');
          icon.classList.toggle('fa-chevron-up', expanded);
          icon.classList.toggle('fa-chevron-down', !expanded);
        });
      });

      offcanvas.classList.add('show-lvl2');
    });
  });

  // Back button quay lại Level 1
  btnBack.addEventListener('click', () => {
    offcanvas.classList.remove('show-lvl2');
  });

  // --- Cart dropdown trên mobile ---
  cartToggle.addEventListener('click', e => {
    e.preventDefault();
    e.stopPropagation();
    cartMenu.classList.toggle('open');
  });
  document.addEventListener('click', e => {
    if (!cartToggle.contains(e.target) && !cartMenu.contains(e.target)) {
      cartMenu.classList.remove('open');
    }
  });
});
</script>
