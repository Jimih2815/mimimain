{{-- resources/views/partials/header.blade.php --}}
@php
    $sections  = \App\Models\MenuSection::with('groups.products')->orderBy('sort_order')->get();
    $cart      = session('cart', []);
    $cartCount = array_sum(array_column($cart, 'quantity'));
@endphp

<header class="shadow-sm">
  {{-- Top bar --}}
  <div class="header-top bg-light py-1">
    <div class="container d-flex justify-content-end small">
      <a href="https://www.google.com" class="me-3 text-decoration-none text-dark" target="_blank">Trợ giúp</a>
      @guest
        <a href="{{ route('register') }}" class="me-3 text-decoration-none text-dark">Đăng ký</a>
        <a href="{{ route('login') }}" class="text-decoration-none text-dark">Đăng nhập</a>
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

  {{-- Main header --}}
  <div class="header-main bg-white py-3">
    <div class="container position-relative d-flex align-items-center">
      
      {{-- Logo trái --}}
      <a href="{{ route('home') }}" class="me-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo MiMi" height="50">
      </a>

      {{-- Nav chính: position absolute để luôn đúng giữa --}}
      <nav class="position-absolute start-50 translate-middle-x">
        <ul class="nav">
          @foreach($sections as $sec)
            <li class="nav-item dropdown position-static">
              <a class="nav-link px-3 fw-semibold text-dark"
                 href="#"
                 id="sec-{{ $sec->id }}"
                 data-bs-toggle="dropdown">
                 {{ $sec->name }}
              </a>
              <div class="dropdown-menu w-100 p-4 mega-menu" aria-labelledby="sec-{{ $sec->id }}">
                <div class="row">
                  @foreach($sec->groups as $grp)
                    <div class="col-6 col-md-3 mb-3">
                      <h6 class="fw-bold text-primary mb-2">{{ $grp->title }}</h6>
                      <ul class="list-unstyled">
                        @forelse($grp->products as $p)
                          <li><a class="dropdown-item px-0"
                                 href="{{ route('products.show', $p->slug ?? $p->id) }}">
                                 {{ $p->name }}
                                </a>
                          </li>
                        @empty
                          <li><small class="text-muted">Chưa có SP</small></li>
                        @endforelse
                      </ul>
                    </div>
                  @endforeach
                  @if($sec->groups->isEmpty())
                    <div class="col-12"><small class="text-muted">Chưa có mục nào</small></div>
                  @endif
                </div>
              </div>
            </li>
          @endforeach
          <li class="nav-item">
            <a class="nav-link px-3 text-dark" href="{{ route('products.index') }}">TẤT CẢ SẢN PHẨM</a>
          </li>
        </ul>
      </nav>

      {{-- Search + Wishlist + Cart (luôn dạt phải) --}}
      <div class="d-flex align-items-center ms-auto">
        <form action="{{ route('products.index') }}" method="GET" class="d-flex me-4" style="max-width:200px;">
          <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm..." value="{{ request('q') }}">
            <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
          </div>
        </form>

        <a href="https://www.google.com" class="text-dark me-4 fs-5" title="Yêu thích" target="_blank">
          <i class="bi bi-heart"></i>
        </a>

        <div class="dropdown">
          <a href="#"
             class="text-dark position-relative fs-5"
             id="cartDropdown"
             data-bs-toggle="dropdown"
             aria-expanded="false">
            <i class="bi bi-bag-fill"></i>
            @if($cartCount)
              <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                {{ $cartCount }}
              </span>
            @endif
          </a>
          <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cartDropdown" style="min-width:300px; z-index:1200;">
            @forelse($cart as $item)
              <li class="d-flex align-items-center mb-2">
                @if(!empty($item['image']))
                  <img src="{{ asset('storage/'.$item['image']) }}" width="50" class="me-2 rounded" alt="{{ $item['name'] }}">
                @endif
                <div class="flex-grow-1">
                  <div class="fw-semibold">{{ $item['name'] }}</div>
                  <small class="text-muted">{{ number_format($item['price'],0,',','.') }}₫ × {{ $item['quantity'] }}</small>
                </div>
              </li>
            @empty
              <li class="text-center mb-0">Giỏ hàng trống! 😊</li>
            @endforelse

            @if($cartCount)
              <li class="text-center mt-2">
                <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm w-100">Xem toàn bộ giỏ hàng</a>
              </li>
            @endif
          </ul>
        </div>
      </div>

    </div>
  </div>
</header>
