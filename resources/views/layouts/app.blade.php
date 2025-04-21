<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','MimiMain')</title>

  {{-- Chỉ load CSS ở head --}}
  @vite([
    'resources/css/app.css',
    'resources/css/header.css',
  ])
</head>
<body class="antialiased">

  @include('partials.header')

  <main class="py-4">
    @yield('content')
  </main>

  {{-- Load JS ở cuối body để chắc chắn DOM đã có --}}
  @vite('resources/js/app.js')
  @stack('scripts')
  @include('partials.footer')
  {{-- rồi mới tới các script --}}
  @vite('resources/js/app.js')

</body>
</html>
