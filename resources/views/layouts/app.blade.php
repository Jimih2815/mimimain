<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>@yield('title','MimiMain')</title>

  {{-- 1) Vite build: đúng đường dẫn SCSS & JS chỉ một lần--}}
  @vite([
    'resources/scss/app.scss',
    'resources/js/app.js',
  ])

  {{-- 2) Bootstrap CSS gốc (sau Tailwind để override nếu cần) --}}
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
  />

  {{-- 3) Bootstrap Icons CDN (nếu muốn chắc cú) --}}
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
  />
</head>

<body class="antialiased">

  {{-- Header --}}
  @include('partials.header')

  <main class="py-4">
    @yield('content')
  </main>

  {{-- Footer --}}
  @include('partials.footer')

  {{-- Bootstrap JS (tab, dropdown, modal…) --}}
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    defer
  ></script>

  {{-- Nơi để @push('scripts') --}}
  @stack('scripts')
</body>
</html>
