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



 <!-- Floating Contact Panel -->
 <div class="mc-floating-panel expanded" id="mcFloatingPanel">
    <!-- 1) Toggle mở/đóng -->
    <button type="button"
            class="mc-floating-toggle"
            id="mcToggleBtn"
            style="margin-bottom: 6rem;"
            aria-label="Mở / Đóng liên hệ">
      <i class="fas fa-chevron-left"></i>
    </button>

    <!-- 2) 3 nút liên hệ -->
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
        <img src="/logochat/logo-zalo-chat.webp" alt="Zalo" class="w-100 h-100">
      </a>
      <a href="https://m.me/61560867710445"
         class="mc-floating-contact__btn mc-messenger-btn text-decoration-none"
         target="_blank" rel="noopener"
         title="Chat Facebook">
        <i class="fab fa-facebook-messenger"></i>
      </a>
    </div>
  </div>


  {{-- Footer chung --}}
  @include('partials.footer')

  {{-- Nếu có các script cần push từ các view con --}}
  @stack('scripts')


  <script>
document.addEventListener('DOMContentLoaded', () => {
  const panel    = document.getElementById('mcFloatingPanel');
  const toggle   = document.getElementById('mcToggleBtn');
  const phoneBtn = document.querySelector('.mc-phone-btn');

  // Phát hiện desktop
  const isMobile = /Mobi|Android|iPhone|iPad|iPod|Windows Phone/i
                     .test(navigator.userAgent);

  if (!isMobile && phoneBtn) {
    // 1) Click để show số
    phoneBtn.addEventListener('click', e => {
      e.preventDefault();
      phoneBtn.removeAttribute('href');
      phoneBtn.classList.add('mc-phone-text');
      phoneBtn.innerText = '0354 235 669';
    });

    // 2) Khi chuột rời khỏi nút, revert về icon
    phoneBtn.addEventListener('mouseleave', () => {
      if (phoneBtn.classList.contains('mc-phone-text')) {
        phoneBtn.classList.remove('mc-phone-text');
        phoneBtn.setAttribute('href', 'tel:0354235669');
        phoneBtn.innerHTML = '<i class="fas fa-phone"></i>';
      }
    });
  }

  // Sau 5s tự thu gọn panel
  setTimeout(() => panel.classList.remove('expanded'), 5000);

  // Toggle mở/đóng panel
  toggle.addEventListener('click', () => {
    panel.classList.toggle('expanded');
  });
});
</script>







</body>
</html>
