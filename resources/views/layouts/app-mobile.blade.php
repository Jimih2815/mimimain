{{-- resources/views/layouts/app-mobile.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- CSRF token --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'MimiMain Mobile')</title>

  {{-- Google Font --}}
  <link 
    href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap" 
    rel="stylesheet"
  >

  {{-- Bootstrap Icons --}}
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
  />
  {{-- Font Awesome --}}
  <script src="https://kit.fontawesome.com/1081860f2a.js" crossorigin="anonymous"></script>

  {{-- Vite: chỉ load mobile bundle --}}
  @vite([
    'resources/scss/app-mobile.scss',
    'resources/js/app-mobile.js',
  ])
  <script>console.log('✅ mobile assets injected');</script>
  @stack('styles')
</head>
<body class="antialiased">
  {{-- Header (mobile) --}}
  @include('partials.mobile-header')

  {{-- Nội dung chính --}}
  <main style="padding-top: 60px;" class="mobi-cont">
    @yield('content')
  </main>

  {{-- Footer (mobile) --}}
  @include('partials.mobile-footer')

  {{-- Scripts từ @push('scripts') --}}
  @stack('scripts')
</body>
</html>
