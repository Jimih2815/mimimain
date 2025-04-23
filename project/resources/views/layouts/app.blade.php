<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ config('app.name','MimiMain') }} – @yield('title')</title>

  {{-- Load CSS → app.css, header.css, plus Swiper CSS if cần --}}
  @vite([
  'resources/css/app.css',
  'resources/css/header.css',   {{-- nếu dùng --}}
  'resources/css/swiper.css',   {{-- nếu dùng --}}
  'resources/js/app.js',
])
</head>
<body class="antialiased">
  @include('partials.header')

  <main class="py-4">
    @yield('content')
  </main>

  @include('partials.footer')

  {{-- Load JS → app.js có import Swiper/Bootstrap --}}
  @vite('resources/js/app.js')
  @stack('scripts')
</body>
</html>
