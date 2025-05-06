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
  color:#007bff;
}

/* Fix search-button z-index */
.input-group .btn { z-index:0 !important; }
</style>

<div class="mobile-header d-block d-lg-none">
  {{-- Top bar --}}
  <div class="mh-top d-flex align-items-center justify-content-between px-3">
    <a href="{{ url('/') }}"><img src="{{ asset('images/logo.png') }}" height="24" alt="Logo"></a>
    <div class="mh-icons d-flex align-items-center">
      {{-- 1) Search --}}
      <form action="{{ route('products.index') }}" method="GET" class="d-flex me-2" style="max-width:140px">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Tìm kiếm…" value="{{ request('q') }}">
          <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
        </div>
      </form>
      {{-- 2) Yêu thích --}}
      <a href="{{ route('favorites.index') }}" class="text-dark fs-5 me-3"><i class="fas fa-heart text-danger"></i></a>
      {{-- 3) Giỏ hàng --}}
      <div class="dropdown me-3">
        <a href="#" id="cartDropdownMobile" data-bs-toggle="dropdown" class="text-dark position-relative fs-5">
          <i class="bi bi-bag-fill"></i>
          <span id="cart-count-mobile"
                class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
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
        <a href="{{ route('login') }}" class="text-dark fs-5 me-2"><i class="bi bi-person"></i></a>
      @else
        <a href="{{ route('profile.edit') }}" class="text-dark fs-5 me-2"><i class="bi bi-person-circle"></i></a>
      @endguest
      {{-- 5) Hamburger --}}
      <button id="mh-btn-toggle" class="btn btn-link p-0"><i class="fa fa-bars fa-lg"></i></button>
    </div>
  </div>

  {{-- Offcanvas --}}
  <aside id="mh-offcanvas" class="mh-offcanvas">
    <div class="mh-oc-header d-flex align-items-center px-3">
      <button id="mh-btn-close" class="btn btn-link p-0 me-2"><i class="fa fa-times fa-lg"></i></button>
      <h5 class="mb-0">Menu</h5>
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
      <h6 id="mh-sec-title" class="px-3 py-2 mb-0 border-bottom"></h6>
      <ul id="mh-groups" class="list-unstyled mb-0"></ul>
    </div>
  </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sections  = @json($sections);
  const offcanvas = document.getElementById('mh-offcanvas');
  const lvl1      = document.getElementById('mh-level1');
  const lvl2      = document.getElementById('mh-level2');
  const groupsEl  = document.getElementById('mh-groups');
  const secTitle  = document.getElementById('mh-sec-title');

  // Mở menu
  document.getElementById('mh-btn-toggle').onclick = () => {
    offcanvas.classList.add('open');
    offcanvas.classList.remove('show-lvl2');
  };
  // Đóng menu
  document.getElementById('mh-btn-close').onclick = () => {
    offcanvas.classList.remove('open', 'show-lvl2');
  };

  // Chọn Section → show Groups
  lvl1.querySelectorAll('li').forEach(li => {
    li.onclick = () => {
      const sec = sections.find(s => s.id == li.dataset.id);
      secTitle.textContent = sec.name;

      // Build nhóm + sản phẩm
      groupsEl.innerHTML = sec.groups.map(g => `
        <li>
          <div class="group-header">
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

      // Toggle slide sản phẩm
      groupsEl.querySelectorAll('.group-header').forEach(header => {
        const icon  = header.querySelector('i');
        const prodUl = header.nextElementSibling;
        header.onclick = () => {
          const expanded = prodUl.classList.toggle('expand');
          icon.classList.toggle('fa-chevron-up', expanded);
          icon.classList.toggle('fa-chevron-down', !expanded);
        };
      });

      // Slide sang Level 2
      offcanvas.classList.add('show-lvl2');
    };
  });

  // Back → Level 1
  document.getElementById('mh-btn-back').onclick = () => {
    offcanvas.classList.remove('show-lvl2');
  };
});
</script>
