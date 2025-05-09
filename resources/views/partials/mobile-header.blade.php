{{-- resources/views/partials/mobile-header.blade.php --}}

<style>
/* ==== Mobile Header CSS ==== */
.mobile-header {
  position: relative; z-index:1000;
  background:#fff;
  box-shadow:0 2px 4px rgba(0,0,0,0.1);
}
.mobile-header .mh-top { height:56px; }
.mobile-header .mh-offcanvas {
  position:fixed;
  top:0; right:-100%; left:auto;
  width:80%; height:100%;
  background:#fff;
  transition:right .3s ease;
  overflow-y:auto;
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
  border-bottom:1px solid #f1f1f1;
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
    color: #d1a029;
}
.fa-user {
    color: #4ab3af;
}
.fa-bars {
    color: #333333;
}
.fa-heart {
    color: #b83232 !important;
}
#mh-btn-close .fa-times {
    font-size: 1.5rem;
    -webkit-text-stroke: 2px #b83232;
    color: white;
}
.list-unstyled {
    padding-left: 0;
    list-style: none;
    font-size: 1.5rem;
    font-weight: 300;
    color: white;                     /* Màu chữ chính */
  -webkit-text-stroke: 2px #333333; /* Viền xanh (Bootstrap blue) */
  text-shadow: none;  
}
#mh-sec-title {
    font-size: 1.5rem;
    color: #b83232;
    -webkit-text-stroke: 1px #b83232;
}
.list-unstyled a {
    color: #b83232;
    font-size: 1rem;
    -webkit-text-stroke: 1px #4ab3af;
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
        <a href="#" id="cartDropdownMobile" data-bs-toggle="dropdown" class="position-relative d-flex justify-content-center align-items-center">
          <i class="bi bi-bag-fill d-flex justify-content-center align-items-center"></i>
           <span id="cart-count-mobile"
                class="badge rounded-pill position-absolute top-50 start-50 translate-middle so-trong-gio"
                style="{{ $cartCount ? '' : 'display:none' }}">
            {{ $cartCount }}
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cartDropdownMobile" style="min-width:260px; z-index:1200">
          @forelse($cart as $item)
            <li class="d-flex align-items-center mb-2">
              <img src="{{ asset('storage/'.$item['image']) }}"
                   style="width:40px;height:40px;object-fit:cover;border-radius:4px" class="me-2">
              <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
            </li>
          @empty
            <p class="text-center mb-0">Giỏ hàng trống</p>
          @endforelse
          <div class="mt-3 text-center">
            <a href="{{ route('cart.index') }}" class="btn btn-sm btn-primary">Xem giỏ hàng</a>
          </div>
        </div>
      </div>
      {{-- 4) Tài khoản --}}
      @guest
        <a href="{{ route('login') }}" class="d-flex justify-content-center align-items-center"><i class="fa-solid fa-user"></i></a>
      @else
        <a href="{{ route('profile.edit') }}" class="d-flex justify-content-center align-items-center"><i class="fa-solid fa-user"></i></a>
      @endguest
      {{-- 5) Hamburger --}}
      <button id="mh-btn-toggle" class="d-flex justify-content-center align-items-center border-0 bg-transparent"><i class="fa-solid fa-bars"></i></button>
    </div>
  </div>

  {{-- Offcanvas --}}
  <aside id="mh-offcanvas" class="mh-offcanvas">
    <div class="mh-oc-header d-flex align-items-center justify-content-end">
      <button id="mh-btn-close" class="d-flex align-items-center px-3 justify-content-center border-0"><i class="fa fa-times fa-lg"></i></button>
      <!-- <h5 class="mb-0">Menu</h5> -->
    </div>

    {{-- Level 1 --}}
    <ul id="mh-level1" class="mh-nav-lvl1 list-unstyled mb-0">
      @foreach($sections as $sec)
        <li data-id="{{ $sec->id }}" class="d-flex justify-content-between align-items-center px-3 py-2">
          <span>{{ $sec->name }}</span>
          <i class="fa fa-chevron-right"></i>
        </li>
      @endforeach
    </ul>

    {{-- Level 2 --}}
    <div id="mh-level2" class="mh-nav-lvl2">
      <div id="mh-btn-back" class="mh-back"><i class="fa fa-chevron-left me-2"></i>Quay lại</div>
      <h6 id="mh-sec-title" class="px-3 py-2 pb-2 border-bottom"></h6>
      <ul id="mh-groups" class="list-unstyled mb-0"></ul>
    </div>
  </aside>
  {{-- Search Offcanvas --}}
    <aside id="mh-search-panel" class="mh-search-panel">
        <form id="mh-search-form" action="{{ route('products.index') }}" method="GET">
            <div class="search-header d-flex align-items-center px-3">
            <button id="mh-search-close" type="button" class="btn btn-link p-0 me-2">
                <i class="fa fa-times fa-lg"></i>
            </button>
            <div class="input-group w-100">
                <input type="text"
                    name="q"
                    id="mh-search-input"
                    class="form-control"
                    placeholder="Search…"
                    value="{{ request('q') }}">
                <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i>
                </button>
            </div>
            </div>

            <div class="p-3">
            <p class="mb-2 fw-bold">Từ Khóa Phổ Biến</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge bg-light text-dark">Đèn Ngủ Hoa Tulyp</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge bg-light text-dark">Gấu Bông</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge bg-light text-dark">Hoa Gấu Bông</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge bg-light text-dark">Đèn Ngủ Đám Mây</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge bg-light text-dark">Quạt Mini Tích Điện</a>
                <a href="{{ route('products.index', ['q'=>'air max 90 lv8']) }}"
                class="badge bg-light text-dark">Phụ Kiện Cute</a>
            </div>
            </div>
        </form>
    </aside>


</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sections      = @json($sections);
  const offcanvas     = document.getElementById('mh-offcanvas');
  const lvl1          = document.getElementById('mh-level1');
  const lvl2          = document.getElementById('mh-level2');
  const groupsEl      = document.getElementById('mh-groups');
  const secTitle      = document.getElementById('mh-sec-title');
  const searchToggle  = document.getElementById('mh-search-toggle');
  const searchPanel   = document.getElementById('mh-search-panel');
  const searchClose   = document.getElementById('mh-search-close');

  // Mở search panel
  searchToggle.onclick = () => {
    // đóng menu đang mở nếu có
    offcanvas.classList.remove('open', 'show-lvl2');
    // mở panel tìm kiếm
    searchPanel.classList.add('open');
  };
  // Đóng search panel
  searchClose.onclick = () => {
    searchPanel.classList.remove('open');
  };

  // Mở menu
  document.getElementById('mh-btn-toggle').onclick = () => {
    offcanvas.classList.add('open');
    offcanvas.classList.remove('show-lvl2');
  };
  // Đóng menu
  document.getElementById('mh-btn-close').onclick = () => {
    offcanvas.classList.remove('open', 'show-lvl2');
  };

  // Chọn Section → show Groups (Level 1 → Level 2)
  lvl1.querySelectorAll('li').forEach(li => {
    li.onclick = () => {
      const sec = sections.find(s => s.id == li.dataset.id);
      secTitle.textContent = sec.name;

      // Build nhóm + sản phẩm
      groupsEl.innerHTML = sec.groups.map(g => `
        <li>
          <div class="group-header d-flex justify-content-between align-items-center py-2 px-3">
            <span>${g.title}</span>
            <i class="fa fa-chevron-down"></i>
          </div>
          <ul class="mh-products list-unstyled mb-0">
            ${g.products.map(p =>
              `<li class="py-1 px-4"><a href="/products/${p.slug}">${p.name}</a></li>`
            ).join('')}
          </ul>
        </li>
      `).join('');

      // Toggle slide sản phẩm trong group
      groupsEl.querySelectorAll('.group-header').forEach(header => {
        const icon  = header.querySelector('i');
        const prodUl = header.nextElementSibling;
        header.onclick = () => {
          const expanded = prodUl.classList.toggle('expand');
          icon.classList.toggle('fa-chevron-up', expanded);
          icon.classList.toggle('fa-chevron-down', !expanded);
        };
      });

      // Hiệu ứng slide sang Level 2
      offcanvas.classList.add('show-lvl2');
    };
  });

  // Back → quay lại Level 1
  document.getElementById('mh-btn-back').onclick = () => {
    offcanvas.classList.remove('show-lvl2');
  };
});
</script>

