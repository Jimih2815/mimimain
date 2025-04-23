<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title','MimiMain')</title>

  {{-- Chỉ load CSS 1 lần --}}
  @vite([
      'resources/css/app.css',
      'resources/css/header.css',
  ])

  {{-- Bootstrap JS (cho dropdown, modal…) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="antialiased">

  @include('partials.header')

  <main class="py-4">
    @yield('content')
  </main>

  {{-- Load JS cuối body --}}
  @vite('resources/js/app.js')
  @stack('scripts')

  @include('partials.footer')
</body>
</html>
