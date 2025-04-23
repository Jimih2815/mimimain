<header>
  {{-- Tầng 1 --}}
  <div class="header-top d-flex align-items-center justify-content-between px-4 py-2">
    <a href="{{ route('home') }}" class="d-flex align-items-center">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" height="50">
    </a>

    {{-- Search --}}
    <form action="{{ route('products.index') }}" method="GET" class="input-group mx-4 flex-grow-1">
      <input
        type="text"
        name="q"
        class="form-control"
        placeholder="Tìm kiếm..."
        value="{{ request('q') }}"
      >
      <button class="btn btn-primary">
        <i class="bi bi-search"></i>
      </button>
    </form>

    {{-- Cart --}}
    @php
      $cart = session('cart', []);
      $cartCount = array_sum(array_column($cart, 'quantity'));
    @endphp
    <div class="dropdown">
      <a href="#"
         class="text-primary position-relative dropdown-toggle"
         id="cartDropdown"
         data-bs-toggle="dropdown"
         aria-expanded="false">
        <i class="bi bi-cart-fill fs-4"></i>
        @if($cartCount>0)
          <span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-1">
            {{ $cartCount }}
          </span>
        @endif
      </a>
      <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cartDropdown" style="min-width:300px; z-index:1200;">
        @if(count($cart))
          @foreach($cart as $id=>$item)
          <li class="d-flex align-items-center mb-2">
            @if($item['image'])
              <img src="{{ $item['image'] }}" width="50" class="me-2 rounded">
            @endif
            <div class="flex-grow-1">
              <div class="fw-semibold">{{ $item['name'] }}</div>
              <small class="text-muted">
                {{ number_format($item['price'],0,',','.') }}₫ × {{ $item['quantity'] }}
              </small>
            </div>
          </li>
          @endforeach
          <li class="text-center mt-2">
            <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm w-100">
              Xem toàn bộ giỏ hàng
            </a>
          </li>
        @else
          <li class="text-center mb-0">Giỏ hàng trống! 😊</li>
        @endif
      </ul>
    </div>
  </div>

  {{-- Tầng 2: Mega‑menu dynamic --}}
  <nav class="header-nav bg-light">
    <div class="container">
      <ul class="nav justify-content-center">
        @foreach($menuCategories as $cat)
        <li class="nav-item dropdown position-static">
          <a class="nav-link" href="#" data-bs-toggle="dropdown">{{ $cat->name }}</a>
          <div class="dropdown-menu w-100 mega-menu p-4">
            <div class="container">
              {{-- Hàng trên: Header Boxes --}}
              <div class="row mb-3">
                @foreach($cat->headers as $header)
                <div class="col-md-3 text-center">
                  <button class="mega-header-box btn btn-outline-secondary w-100 py-3 mb-2">
                    {{ $header->title }}
                  </button>
                </div>
                @endforeach
              </div>

              {{-- Hàng dưới: Products --}}
              <div class="row">
                @foreach($cat->headers as $header)
                <div class="col-md-3">
                  <ul class="list-unstyled mega-list">
                    @forelse($header->products as $prod)
                      <li>
                        <a href="{{ route('products.show',['slug'=>$prod->slug]) }}">
                          {{ $prod->name }}
                        </a>
                      </li>
                    @empty
                      <li><small class="text-muted">Chưa có SP</small></li>
                    @endforelse
                  </ul>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </li>
        @endforeach

        {{-- Link “Tất cả sản phẩm” --}}
        <li class="nav-item">
          <a class="nav-link" href="{{ route('products.index') }}">TẤT CẢ SẢN PHẨM</a>
        </li>
      </ul>
    </div>
  </nav>
</header>
