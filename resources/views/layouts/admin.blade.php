{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard – MimiMain</title>

  {{-- Bootstrap CSS CDN (hoặc bạn đổi thành link tới public/css/app.css) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- Bootstrap Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

  {{-- Navbar Admin --}}
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
        Mimi Admin
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav"
              aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="adminNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          {{-- Dashboard --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
              <i class="bi bi-speedometer2"></i> Bảng điều khiển
            </a>
          </li>

          
          {{-- Collections --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.collections.index') }}">
              <i class="bi bi-images"></i> Collections
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.collection-sliders.index') }}">
              <i class="bi bi-easel"></i> Collection Sliders
            </a>
          </li>

          {{-- Home Page --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.home.edit') }}">
              <i class="bi bi-house"></i> Trang chủ
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.home-section-images.index') }}">
              <i class="bi bi-image-alt"></i> Home Section Images
            </a>
          </li>

          {{-- Mega-menu --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.menu.index') }}">
              <i class="bi bi-menu-button-wide"></i> Menu Sections
            </a>
          </li>

          {{-- Sản phẩm --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.products.index') }}">
              <i class="bi bi-box-seam"></i> Sản phẩm
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.product-sliders.index') }}">
              <i class="bi bi-sliders"></i> Product Sliders
            </a>
          </li>

          {{-- Đơn hàng --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.orders.index') }}">
              <i class="bi bi-receipt"></i> Đơn hàng
            </a>
          </li>

          {{-- Người dùng --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
              <i class="bi bi-people"></i> Người dùng
            </a>
          </li>

          {{-- Widgets --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.widgets.index') }}">
              <i class="bi bi-puzzle"></i> Widgets
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.placements.index') }}">
              <i class="bi bi-pin-angle"></i> Widget Placements
            </a>
          </li>
        </ul>

        {{-- Các link phụ (xem site, logout) --}}
        <ul class="navbar-nav">
          <li class="nav-item me-3">
            <a class="nav-link" href="{{ route('home') }}" target="_blank">
              <i class="bi bi-box-arrow-up-right"></i> Xem site
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="bi bi-door-closed"></i> Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    {{-- Flash message --}}
    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    {{-- Nội dung page --}}
    @yield('content')
  </div>

  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
