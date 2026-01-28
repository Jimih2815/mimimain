{{-- resources/views/partials/mobile-contact-fab.blade.php --}}
<div class="mcfab" data-auto-close="8000" aria-label="Liên hệ nhanh">
  <div class="mcfab__menu" aria-hidden="true">
    <a class="mcfab__item mcfab__item--phone"
       href="tel:0354235669"
       title="Gọi: 0354 235 669"
       aria-label="Gọi điện">
      <i class="fas fa-phone"></i>
    </a>

    <a class="mcfab__item mcfab__item--zalo"
       href="https://zalo.me/0354235669"
       target="_blank" rel="noopener"
       title="Chat Zalo: 0354 235 669"
       aria-label="Chat Zalo">
      <img src="/logochat/logo-zalo-chat.webp" alt="Zalo">
    </a>

    <a class="mcfab__item mcfab__item--messenger"
       href="https://m.me/61560867710445"
       target="_blank" rel="noopener"
       title="Chat Messenger"
       aria-label="Chat Messenger">
      <i class="fab fa-facebook-messenger"></i>
    </a>
  </div>

  <button type="button" class="mcfab__main" aria-label="Mở menu liên hệ">
    <i class="fas fa-comment-dots"></i>
  </button>
</div>
