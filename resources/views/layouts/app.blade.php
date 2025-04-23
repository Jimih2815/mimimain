<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title','MimiMain')</title>

  {{-- CSS build bởi Vite (Tailwind + style riêng) --}}
  @vite([
      'resources/css/app.css',
      'resources/css/header.css',
  ])

  {{-- ️🛡️ Chốt hạ: Bootstrap CSS gốc – đặt SAU Tailwind để khỏi bị ghi đè --}}
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
  >
</head>
<body class="antialiased">

  {{-- Header --}}
  @include('partials.header')

  <main class="py-4">
    @yield('content')
  </main>

  {{-- JS build bởi Vite (Swiper, script riêng, …) --}}
  @vite('resources/js/app.js')

  {{-- Bootstrap JS (tab, dropdown, modal…) – defer để chắc chắn DOM đã sẵn sàng --}}
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    defer
  ></script>

  {{-- Chỗ để view con @push('scripts') --}}
  @stack('scripts')

  {{-- Footer --}}
  @include('partials.footer')
</body>
</html>
