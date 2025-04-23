{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard</title>

  {{-- Bootstrap CSS CDN (hoặc link tới public/css/app.css nếu bạn biên tài sản riêng) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  {{-- Navbar đơn giản --}}
  <nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('admin.products.index') }}">
        My Admin
      </a>
      <ul class="navbar-nav flex-row">
        <li class="nav-item me-3">
          <a class="nav-link text-white" href="{{ route('admin.products.index') }}">Sản phẩm</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="{{ route('admin.orders.index') }}">Đơn hàng</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-fluid">
    {{-- Hiện flash message nếu có --}}
    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    {{-- Nội dung chuyên biệt page sẽ điền vào đây --}}
    @yield('content')
  </div>

  {{-- Bootstrap Bundle JS (Popper + JS) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Nơi để đưa thêm script riêng từng page --}}
  @stack('scripts')
</body>
</html>
