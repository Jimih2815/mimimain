{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>@yield('title','MimiMain')</title>

  {{-- 1) Vite bundle SCSS + JS --}}
  @vite([
    'resources/scss/app.scss',
    'resources/js/app.js',
  ])

  {{-- 2) (tùy chọn) Bootstrap Icons CDN để “chống cháy” --}}
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
  />
</head>
<body class="antialiased">
  @include('partials.header')
  <main class="py-4">@yield('content')</main>
  @include('partials.footer')

  {{-- Vite JS --}}
  @vite('resources/js/app.js')

  {{-- @push('scripts') --}}
  @stack('scripts')
</body>
</html>
