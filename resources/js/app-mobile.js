// resources/js/app-mobile.js

import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
import { initMobileContactFab } from './components/mobile-contact-fab';
window.bootstrap = bootstrap;

console.log('🔥 app-mobile.js loaded');

document.addEventListener('DOMContentLoaded', () => {
  initMobileContactFab();

  console.log('🛠️ DOMContentLoaded mobile');

  // 1) Collection slider (nếu còn dùng Swiper)
if (document.querySelector('.collection-swiper')) {
  new Swiper('.collection-swiper', {
    slidesPerView: 1.15,
    spaceBetween: 12,
    loop: false,
    freeMode: {
      enabled: true,
      sticky: true,
      momentum: true,
      momentumRatio: 0.5,
      momentumBounce: true,
    },
    navigation: {
      prevEl: '.slider-full-width .swiper-button-prev',
      nextEl: '.slider-full-width .swiper-button-next',
    },
  });
}

  // 2) Product slider (ảnh chính sản phẩm) + pagination + inertia
  new Swiper('.product-swiper', {
    slidesPerView: 1,
    spaceBetween: 2,
    loop: false,
    // freeMode: {
    //   enabled: true,
    //   sticky: true,
    //   momentum: true,
    //   momentumRatio: 0.5,
    //   momentumBounce: true,
    // },
    navigation: {
      prevEl: '.slider-product .product-slider-prev',
      nextEl: '.slider-product .product-slider-next',
    },
    pagination: {
      el: '.product-swiper-pagination',
      clickable: true,
    },
  });

  // 3) Related products slider + inertia
  new Swiper('.product-swiper-related', {
    slidesPerView: 3.2,
    spaceBetween: 12,
    loop: false,
    freeMode: {
      enabled: true,
      sticky: true,
      momentum: true,
      momentumRatio: 0.5,
      momentumBounce: true,
    },
    navigation: {
      prevEl: '.slider-related .related-prev',
      nextEl: '.slider-related .related-next',
    },
  });

  // 4) Home-mobile product slider (config riêng) + inertia
  if (document.querySelector('.home-product-swiper')) {
    new Swiper('.home-product-swiper', {
  // ✅ 1 sản phẩm + ló mép sản phẩm kế bên (peek)
  slidesPerView: 'auto',
  spaceBetween: 14,
  slidesOffsetBefore: 16,
  slidesOffsetAfter: 16,
  freeMode: true,
  watchSlidesProgress: true,
  grabCursor: true,

  // Nếu bạn có pagination/navigation thì để nguyên, không có thì bỏ cũng được
  // pagination: { el: '.home-product-swiper .swiper-pagination', clickable: true },

  breakpoints: {
    480: { slidesPerView: 1.15 },
    768: { slidesPerView: 2.2, spaceBetween: 16, slidesOffsetBefore: 16, slidesOffsetAfter: 16, freeMode: false },
  },
});

  }
    // 5) Section Images slider tự động + tương tác tăng delay lên 15s
  if (document.querySelector('.section-images-swiper')) {
    let userInteracted = false;

    const sectionSwiper = new Swiper('.section-images-swiper', {
      slidesPerView: 1,
      spaceBetween: 12,
      loop: true,
      autoplay: {
        delay: 5000,               // ban đầu 5s
        disableOnInteraction: false,
      },
      on: {
        touchStart() {
          userInteracted = true;
          this.autoplay.stop();    // dừng autoplay ngay khi chạm
        },
        touchEnd() {
          // sau khi thả, set delay 15s và bật lại autoplay
          this.params.autoplay.delay = 15000;
          this.autoplay.start();
        },
        slideChangeTransitionEnd() {
          // sau lần slide tự động tiếp theo, reset về 5s
          if (userInteracted) {
            this.params.autoplay.delay = 5000;
            userInteracted = false;
          }
        },
      },
    });
  }
});