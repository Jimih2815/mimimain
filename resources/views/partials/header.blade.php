@php
    $menuSections = \App\Models\MenuSection::with('groups.products')
                      ->orderBy('sort_order')
                      ->get();
    $cart      = session('cart', []);
    $cartCount = array_sum(array_column($cart, 'quantity'));
@endphp

<header>
  {{-- 1) Top bar --}}
  <div class="header-top bg-light py-1 an-des">
    <div class="container d-flex justify-content-end small">
      <a href="{{ asset('help') }}" class="me-3 text-decoration-none text-dark" target="_blank">Trợ giúp</a>

      @guest
        <a href="{{ route('register') }}" class="me-3 text-decoration-none text-dark">Đăng ký</a>
        <a href="{{ route('login') }}"    class="text-decoration-none text-dark">Đăng nhập</a>
      @else
        <div class="dropdown">
          <a class="dropdown-toggle text-dark text-decoration-none"
             href="#"
             id="userMenuTop"
             data-bs-toggle="dropdown">
            {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuTop">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Hồ sơ</a></li>
            <li>
              <a class="dropdown-item" href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Đăng xuất
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
          </ul>
        </div>
      @endguest
    </div>
  </div>

  {{-- 2) Main header --}}
  <div class="header-main bg-white py-3 an-des">
    <div class="container position-relative d-flex align-items-center">

      {{-- Logo trái --}}
      <a href="{{ route('home') }}" class="me-4 logo-cong-ty">
        <img src="https://tiemhoamimi.com/image/mimi-logo.webp" alt="Logo MiMi" height="50">
      </a>

      {{-- NAV – Mega menu --}}
      <nav class="position-absolute start-50 translate-middle-x">
        <ul class="nav">
          @foreach($menuSections as $section)
            <li class="nav-item dropdown position-static">
              <a class="nav-link px-3 fw-semibold text-dark"
                 href="{{ $section->collection_id 
                           ? route('collections.show', $section->collection->slug) 
                           : '#' }}"
                 id="sec-{{ $section->id }}">
                {{ $section->name }}
              </a>
              <div class="dropdown-menu p-4 mega-menu" aria-labelledby="sec-{{ $section->id }}">
                <div class="row">
                  @forelse($section->groups as $group)
                    <div class="group-block col-6 col-md-3 mb-3">
                      <h6 class="fw-bold danh-muc-drop-down-header mb-2">{{ $group->title }}</h6>
                      <ul class="list-unstyled">
                        @forelse($group->products as $p)
                          <li>
                            <a class="dropdown-item px-0"
                               href="{{ route('products.show', $p->slug ?? $p->id) }}">
                              {{ $p->name }}
                            </a>
                          </li>
                        @empty
                          <li><small class="text-muted">Chưa có SP</small></li>
                        @endforelse
                      </ul>
                    </div>
                  @empty
                    <div class="col-12"><small class="text-muted">Chưa có mục nào</small></div>
                  @endforelse
                </div>
              </div>
            </li>
          @endforeach
          <li class="nav-item">
            <a class="nav-link px-3 text-dark" href="{{ route('products.index') }}">TOÀN BỘ</a>
          </li>
        </ul>
      </nav>

      {{-- Search + Wishlist + Cart --}}
      <div class="ms-auto d-flex align-items-center gap-3 gio-hang-tim-kiem">
        {{-- Tìm kiếm --}}
        <form action="{{ route('products.index') }}" method="GET" class="d-flex me-2" style="max-width:200px;">
          <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm..." value="{{ request('q') }}">
            <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
          </div>
        </form>

        {{-- Yêu thích --}}
        <a href="{{ route('favorites.index') }}"
          class="text-dark fs-5 position-relative text-decoration-none"
          title="Yêu thích">
          <i style="color:#b83232 !important;" class="fas fa-heart text-danger"></i>
        </a>

        {{-- Giỏ hàng --}}
  <div class="dropdown">
      <a href="#"
      class="text-dark position-relative fs-5"
      id="cartDropdown"
      data-bs-toggle="dropdown">
      <i class="bi bi-bag-fill"></i>
      <span id="cart-count"
            class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
            style="{{ $cartCount ? '' : 'display:none' }}">
        {{ $cartCount }}
      </span>
    </a>

    {{-- Nội dung dropdown giỏ hàng --}}
    <div class="dropdown-menu dropdown-menu-end p-3"
          aria-labelledby="cartDropdown"
          style="min-width:300px; z-index:1200; border-color:#d1a029;">

        {{-- scroll container với overflow --}}
        <div class="scrollable-cart" style="max-height:500px; overflow-y:auto;">
          <ul id="header-cart-list" class="list-unstyled mb-0">
            @forelse($cart as $item)
              <li data-key="{{ md5($item['product_id'].'|'.json_encode($item['options'] ?? [])) }}"
                  class="d-flex align-items-center mb-2">
                @if(!empty($item['image']))
                  <img src="{{ asset('storage/'.$item['image']) }}"
                      width="50" class="me-2 rounded" alt="{{ $item['name'] }}">
                @endif
                <div class="flex-grow-1">
                  <div class="fw-semibold">{{ $item['name'] }}</div>
                  <small class="text-muted">
                    {{ number_format($item['price'],0,',','.') }}₫ × {{ $item['quantity'] }}
                  </small>
                </div>
              </li>
            @empty
              <li class="text-center mb-0 empty-cart">Giỏ hàng trống! 😊</li>
            @endforelse
          </ul>
        </div>

        <div class="text-center mt-2 view-cart-li"
            style="{{ $cartCount ? '' : 'display:none' }}">
          <a href="{{ route('cart.index') }}"
            class="btn-mimi nut-dropdown-gio-hang w-100 text-decoration-none"
            style="color:white!important; font-size:1rem;">
            Xem toàn bộ giỏ hàng
          </a>
        </div>

    </div>


    {{-- Notification “Đã thêm” --}}
    <div id="cart-notification"
         class="dropdown-menu dropdown-menu-end p-3 position-fixed"
         style="
           top: 8rem;
           right: 1rem;
           display: none;
           min-width: 240px;
           z-index: 2000;
           border: 2px solid #d1a029;
         ">
      <button type="button"
              class="float-end"
              aria-label="Close"
              id="cart-notif-close"
              style="color: #b83232; 
                    background: none;
                    border: none;
                    outline: none;">
              <i class="fa-solid fa-xmark"></i>
      </button>
      <ul id="cart-notification-list" class="list-unstyled mb-0"></ul>
    </div>
  </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const notif     = document.getElementById('cart-notification');
  const notifList = document.getElementById('cart-notification-list');
  const btnClose  = document.getElementById('cart-notif-close');
  let timeoutHide;

  // show notification
  window.showCartNotification = (msg, imgUrl) => {
    clearTimeout(timeoutHide);
    notifList.innerHTML = `
      <li class="d-flex align-items-center">
        <img src="${imgUrl}"
             style="width:50px;height:50px;object-fit:cover;border-radius:4px;"
             class="me-2" alt="">
        <span>${msg}</span>
      </li>`;
    notif.style.display = 'block';
    notif.classList.add('show');
    timeoutHide = setTimeout(window.hideCartNotification, 3000);
  };

  // hide notification
  window.hideCartNotification = () => {
    notif.style.display = 'none';
    notif.classList.remove('show');
    clearTimeout(timeoutHide);
  };

  btnClose.addEventListener('click', window.hideCartNotification);
  document.addEventListener('click', e => {
    if (!notif.contains(e.target) && !e.target.closest('#cartDropdown')) {
      window.hideCartNotification();
    }
  });
});
</script>
@endpush


