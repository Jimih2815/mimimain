<header>
{{-- Tầng 1 --}}
  <div class="header-top d-flex align-items-center justify-content-between px-4 py-2">
    <a href="/" class="d-flex align-items-center">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" height="50">
    </a>

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

    {{-- Cart dropdown --}}
    @php
      $cart = session('cart', []);
      $cartCount = array_sum(array_column($cart, 'quantity'));
    @endphp
    <div class="dropdown">
      <a 
        href="#" 
        class="text-primary position-relative dropdown-toggle" 
        id="cartDropdown" 
        data-bs-toggle="dropdown" 
        aria-expanded="false"
      >
        <i class="bi bi-cart-fill fs-4"></i>
        @if($cartCount > 0)
          <span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-1">
            {{ $cartCount }}
          </span>
        @endif
      </a>

      <ul 
        class="dropdown-menu dropdown-menu-end p-3" 
        aria-labelledby="cartDropdown" 
        style="min-width: 300px;"
      >
        @if(count($cart) > 0)
          @foreach($cart as $id => $item)
            <li class="d-flex align-items-center mb-2">
              @if(!empty($item['image']))
                <img src="{{ $item['image'] }}" alt="" width="50" class="me-2 rounded">
              @endif
              <div class="flex-grow-1">
                <div class="fw-semibold">{{ $item['name'] }}</div>
                <small class="text-muted">
                  {{ number_format($item['price'], 0, ',', '.') }}₫ × {{ $item['quantity'] }}
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


  {{-- Tầng 2: Mega‑menu --}}
  <nav class="header-nav bg-light">
    <div class="container">
      <ul class="nav justify-content-center">
        @foreach(['ĐÈN NGỦ','HOA GẤU BÔNG','GẤU BÔNG','SET QUÀ TẶNG','KHÁC'] as $cat)
        <li class="nav-item dropdown position-static">
          <a class="nav-link" href="#" data-bs-toggle="dropdown">{{ $cat }}</a>
          <div class="dropdown-menu w-100 mega-menu p-4">
            <div class="container">
              {{-- Hàng trên: 4 ô placeholder --}}
              <div class="row mb-3">
                @for($i=1; $i<=4; $i++)
                <div class="col-md-3 text-center">
                  <div class="mega-header-box py-3 rounded mb-2">
                    <!-- bạn đổi thành dữ liệu dynamic sau -->
                    {{ $cat }} Header {{ $i }}
                  </div>
                </div>
                @endfor
              </div>
              {{-- Hàng dưới: tương ứng 4 cột list --}}
              <div class="row">
                @for($i=1; $i<=4; $i++)
                <div class="col-md-3">
                  <ul class="list-unstyled mega-list">
                    {{-- placeholder list --}}
                    <li><a href="#">Liên kết {{ $i }}A</a></li>
                    <li><a href="#">Liên kết {{ $i }}B</a></li>
                    <li><a href="#">Liên kết {{ $i }}C</a></li>
                  </ul>
                </div>
                @endfor
              </div>
            </div>
          </div>
        </li>
        @endforeach

        <li class="nav-item">
          <a class="nav-link" href="{{ route('products.index') }}">TẤT CẢ SẢN PHẨM</a>
        </li>
      </ul>
    </div>
  </nav>
</header>
