{{-- resources/views/partials/header.blade.php --}}
@php
  // Lấy toàn bộ section + item đã sắp xếp
  $sections = \App\Models\MenuSection::with('items')->orderBy('sort_order')->get();
  // Cart session
  $cart      = session('cart', []);
  $cartCount = array_sum(array_column($cart, 'quantity'));
@endphp

<header class="shadow-sm">
  {{-- Tầng 1: Logo • Search • Cart • Auth --}}
  <div class="header-top d-flex align-items-center justify-content-between px-4 py-2 bg-white">
    {{-- Logo --}}
    <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" height="50">
    </a>

    {{-- Ô tìm kiếm --}}
    <form action="{{ route('products.index') }}" method="GET"
          class="input-group mx-4 flex-grow-1" style="max-width:500px;">
      <input type="text" name="q" class="form-control" placeholder="Tìm kiếm..." value="{{ request('q') }}">
      <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>

    {{-- Cart + Auth --}}
    <div class="d-flex align-items-center">
      {{-- Cart --}}
      <div class="dropdown me-3">
        <a href="#" class="text-primary position-relative" id="cartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-cart-fill fs-4"></i>
          @if($cartCount)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-1">{{ $cartCount }}</span>
          @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cartDropdown" style="min-width:300px; z-index:1200;">
          @forelse($cart as $id => $item)
            <li class="d-flex align-items-center mb-2">
              @if(!empty($item['image']))
                <img src="{{ $item['image'] }}" width="50" class="me-2 rounded">
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
            <li class="text-center mt-2"><a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm w-100">Xem toàn bộ giỏ hàng</a></li>
          @endif
        </ul>
      </div>

      {{-- Auth --}}
      @guest
        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Đăng&nbsp;nhập</a>
        <a href="{{ route('register') }}" class="btn btn-primary">Đăng&nbsp;ký</a>
      @else
        <div class="dropdown">
          <a class="nav-link dropdown-toggle px-3" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Hồ sơ</a></li>
            <li>
              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
          </ul>
        </div>
      @endguest
    </div>
  </div>

  {{-- Tầng 2: Mega‑menu Sections --}}
  <nav class="header-nav bg-light border-top">
    <div class="container">
      <ul class="nav justify-content-center">
        @foreach($sections as $sec)
          <li class="nav-item dropdown position-static">
            <a class="nav-link px-3 fw-semibold" href="#" id="sec-{{ $sec->id }}" data-bs-toggle="dropdown" aria-expanded="false">
              {{ $sec->name }}
            </a>
            <div class="dropdown-menu w-100 p-4 mega-menu" aria-labelledby="sec-{{ $sec->id }}">
              <div class="row">
                @foreach($sec->items as $item)
                  <div class="col-6 col-md-3 mb-2">
                    <a href="{{ $item->url }}" class="dropdown-item text-wrap">{{ $item->label }}</a>
                  </div>
                @endforeach
                @if($sec->items->isEmpty())
                  <div class="col-12"><small class="text-muted">Chưa có mục nào</small></div>
                @endif
              </div>
            </div>
          </li>
        @endforeach

        {{-- Link tất cả sản phẩm --}}
        <li class="nav-item"><a class="nav-link px-3" href="{{ route('products.index') }}">TẤT CẢ SẢN PHẨM</a></li>
      </ul>
    </div>
  </nav>
</header>
