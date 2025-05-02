<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- CSRF token for AJAX uploads and TinyMCE --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Admin Dashboard') – MimiMain</title>

  {{-- Bootstrap CSS --}}
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-KE6BqgF7iO8u1h98zsIhBf/0p7Su1RXVNAp+1je56qOxjW0eEyUF0WgUrKDx6i6/" 
    crossorigin="anonymous"
  >

  {{-- Bootstrap Icons --}}
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" 
    rel="stylesheet"
  >

  {{-- Google Font: Baloo 2 --}}
  <link 
    href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap" 
    rel="stylesheet"
  >

  {{-- FontAwesome --}}
  <script 
    src="https://kit.fontawesome.com/1081860f2a.js" 
    crossorigin="anonymous">
  </script>

  {{-- TinyMCE WYSIWYG Editor --}}
  <!-- TinyMCE Cloud (đã gắn API‑key) -->
  <script
    src="https://cdn.tiny.cloud/1/wrdztyv2n38gnuvpweunqfv6jxih7qahyqgryofpcbrwqmsm/tinymce/6/tinymce.min.js"
    referrerpolicy="origin"
    defer  {{-- defer giúp không chặn parse HTML --}}
  ></script>



  {{-- Vite: biên dịch SCSS + JS --}}
  @vite([
    'resources/scss/app.scss',
    'resources/js/app.js',  {{-- nếu bạn có file JS chung --}}
  ])

  {{-- Cho phép các trang con push thêm style --}}
  @stack('styles')
</head>
<body class="bg-light">

  {{-- Header chỉ còn logo ở giữa --}}
  <nav class="navbar bg-white py-3 mb-4">
    <div class="container">
      <a href="{{ route('admin.dashboard') }}" class="mx-auto d-block">
        <img src="https://tiemhoamimi.com/image/mimi-logo.webp"
             alt="Mimi Admin"
             style="height: 60px; object-fit: contain;">
      </a>
    </div>
  </nav>

  {{-- Success Modal toàn cục --}}
  @if(session('success'))
    <div class="modal fade" id="globalSuccessModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="background: transparent;">
          <div class="modal-body text-center p-4"
               style="background: #4ab3af; color: #fff; border-radius: .5rem;">
            <h5 class="mb-0">{{ session('success') }}</h5>
          </div>
        </div>
      </div>
    </div>
  @endif

  <div class="container-fluid">
    @yield('content')
  </div>

  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Tự động show/hide modal success --}}
  @if(session('success'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('globalSuccessModal');
        if (!modalEl) return;
        var bsModal = new bootstrap.Modal(modalEl, {
          backdrop: false,
          keyboard: false
        });
        bsModal.show();
        setTimeout(function() {
          bsModal.hide();
        }, 1000);
      });
    </script>
  @endif

  @stack('scripts')
</body>
</html>
