@inject('agent', 'Jenssegers\Agent\Agent')
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

  {{-- 3) Vite: chọn assets dựa trên thiết bị --}}
  @if($agent->isMobile())
  @vite([
      'resources/scss/app.scss',
      'resources/scss/app-mobile.scss',
      'resources/js/app.js',
      'resources/js/app-mobile.js',
    ])
  @else
    @vite([
      'resources/scss/app.scss',
      'resources/js/app.js',
    ])
  @endif
</head>
<body class="antialiased">

  {{-- Header: desktop hoặc mobile --}}
  @if($agent->isMobile())
    @include('partials.mobile-header')
  @else
    @include('partials.header')
  @endif

  {{-- Nội dung chính --}}
  <main class="container mx-auto">
    @hasSection('sidebar')
      <div class="row">
        <aside style="width: auto;" class="phan-side-bar">
          @yield('sidebar')
        </aside>
        <section style="width: calc(98% - 250px);" class="phan-trang">
          @yield('content')
        </section>
      </div>
    @else
      @yield('content')
    @endif
  </main>

  {{-- Floating Contact Panel --}}
  <div class="mc-floating-panel expanded text-decoration-none" id="mcFloatingPanel">

    <div class="mc-floating-contact">
      <a href="tel:0354235669"
         class="mc-floating-contact__btn mc-phone-btn text-decoration-none"
         title="Gọi: 0354 235 669">
        <i class="fas fa-phone"></i>
      </a>
      <a href="https://zalo.me/0354235669"
         class="mc-floating-contact__btn mc-zalo-btn text-decoration-none" 
         target="_blank" rel="noopener"
         title="Chat Zalo: 0354 235 669">
        <img style="width:100%" src="/logochat/logo-zalo-chat.webp" alt="Zalo">
      </a>
      <a href="https://m.me/61560867710445"
         class="mc-floating-contact__btn mc-messenger-btn text-decoration-none"
         target="_blank" rel="noopener"
         title="Chat Facebook">
        <i class="fab fa-facebook-messenger"></i>
      </a>
    </div>
        <button type="button"
            class="mc-floating-toggle mb-0 mt-3"
            id="mcToggleBtn"
            aria-label="Mở / Đóng liên hệ">
      <i class="fas fa-chevron-left"></i>
    </button>
  </div>

  {{-- Footer: desktop hoặc mobile --}}
  @if($agent->isMobile())
    @include('partials.mobile-footer')
  @else
    @include('partials.footer')
  @endif

  {{-- Scripts từ view con --}}
  @stack('scripts')

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const panel    = document.getElementById('mcFloatingPanel');
    const toggle   = document.getElementById('mcToggleBtn');
    const phoneBtn = document.querySelector('.mc-phone-btn');

    // Desktop: hiển thị số khi hover/click
    if (!/Mobi|Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent) && phoneBtn) {
      phoneBtn.addEventListener('click', e => {
        e.preventDefault();
        phoneBtn.removeAttribute('href');
        phoneBtn.classList.add('mc-phone-text');
        phoneBtn.innerText = '0354 235 669';
      });
      phoneBtn.addEventListener('mouseleave', () => {
        if (phoneBtn.classList.contains('mc-phone-text')) {
          phoneBtn.classList.remove('mc-phone-text');
          phoneBtn.setAttribute('href', 'tel:0354235669');
          phoneBtn.innerHTML = '<i class="fas fa-phone"></i>';
        }
      });
    }

    // Tự thu gọn sau 15s
    setTimeout(() => panel.classList.remove('expanded'), 15000);
    toggle.addEventListener('click', () => panel.classList.toggle('expanded'));
  });
  </script>

</body>
</html>
