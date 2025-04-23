<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ config('app.name','MimiMain') }} – @yield('title')</title>
  @vite([
  'resources/css/app.css',
  'resources/css/header.css',   {{-- nếu dùng --}}
  'resources/css/swiper.css',   {{-- nếu dùng --}}
  'resources/js/app.js',
])
</head>
<body class="antialiased">
  <div class="container py-5">
    @yield('content')
  </div>
</body>
</html>
