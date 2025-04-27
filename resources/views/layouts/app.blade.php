{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- CSRF token cho AJAX, Forms --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://kit.fontawesome.com/1081860f2a.js" crossorigin="anonymous"></script>

  <title>@yield('title', 'MimiMain')</title>

  {{-- 1) Google Font: Baloo 2 --}}
  <link 
    href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap" 
    rel="stylesheet"
  >

  {{-- 2) Bootstrap Icons (tùy chọn) --}}
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
  />

  {{-- 3) Vite: bundle SCSS + JS, có HMR & auto-refresh --}}
  @vite([
    'resources/scss/app.scss',
    'resources/js/app.js',
  ])
</head>
<body class="antialiased">

  {{-- Header chung --}}
  @include('partials.header')

  {{-- Nội dung chính --}}
  <main class="container mx-auto">
    @hasSection('sidebar')
      <div class="row">
        {{-- Cột Sidebar --}}
        <aside class="col-md-3 mb-4">
          @yield('sidebar')
        </aside>

        {{-- Cột Content --}}
        <section class="col-md-9-oh">
          @yield('content')
        </section>
      </div>
    @else
      {{-- Nếu view con không khai báo sidebar, content full width --}}
      @yield('content')
    @endif
  </main>

  {{-- Footer chung --}}
  @include('partials.footer')

  {{-- Nếu có các script cần push từ các view con --}}
  @stack('scripts')
</body>
</html>
