<header>
  {{-- Tầng 1 --}}
  <div class="header-top d-flex align-items-center justify-content-between px-4 py-2">
    <a href="/" class="d-flex align-items-center">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" height="50">
    </a>
    <div class="input-group mx-4 flex-grow-1">
        <form action="{{ route('search') }}" method="GET" class="input-group mx-4 flex-grow-1">
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
    </div>
    <div class="contact-info text-primary">
      <i class="fas fa-phone-alt me-1"></i>0123 456 789
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
