/**
 * resources/js/app.js
 * --------------------------------------------------
 * 1) Sidebar vẫn load ngay (nhẹ, code tự viết)
 * 2) Bootstrap JS load luôn vì hầu như trang nào cũng cần dropdown/modal
 * 3) Swiper -> dynamic import: chỉ tải khi DOM có .collection-swiper
 * --------------------------------------------------
 */

import './sidebar';
// Bootstrap JS & Popper
import 'bootstrap';

document.addEventListener('DOMContentLoaded', async () => {
  /* ───────────────────────────────
     A) Nếu trang có ít nhất 1 slider
  ─────────────────────────────── */
  const needSwiper = document.querySelector(
    '.collection-swiper, .product-swiper, .product-swiper-related'
  );

  if (needSwiper) {
    // song song nạp JS & CSS (vite cho phép import CSS động)
    const [{ default: Swiper }] = await Promise.all([
      import(/* webpackChunkName: "swiper" */ 'swiper/bundle'),
      import(/* webpackChunkName: "swiper" */ 'swiper/css/bundle'),
    ]);

    /* A‑1) Collection slider (free mode) */
    if (document.querySelector('.collection-swiper')) {
      new Swiper('.collection-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 0,
        freeMode: true,
        grabCursor: true,
        simulateTouch: true,
        touchReleaseOnEdges: true,
        navigation: {
          nextEl: '.slider-full-width .swiper-button-next',
          prevEl: '.slider-full-width .swiper-button-prev',
        },
      });
    }

    /* A‑2) Product slider (home) */
    if (document.querySelector('.product-swiper')) {
      new Swiper('.product-swiper', {
        slidesPerView: 4,
        spaceBetween: 15,
        loop: false,
        grabCursor: true,
        simulateTouch: true,
        touchReleaseOnEdges: true,
        navigation: {
          prevEl: '.slider-product .product-slider-button-prev',
          nextEl: '.slider-product .product-slider-button-next',
        },
        pagination: {
          el: '.product-swiper .swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          640: { slidesPerView: 2 },
          1024: { slidesPerView: 4 },
        },
      });
    }

    /* A‑3) Related products slider (product detail) */
    if (document.querySelector('.product-swiper-related')) {
      new Swiper('.product-swiper-related', {
        direction: 'horizontal',
        slidesPerView: 4,
        spaceBetween: 15,
        loop: false,
        rewind: false,
        grabCursor: true,
        simulateTouch: true,
        touchStartPreventDefault: false,
        touchMoveStopPropagation: true,
        resistance: true,
        resistanceRatio: 0.5,
        navigation: {
          prevEl: '.related-prev',
          nextEl: '.related-next',
        },
        breakpoints: {
          320: { slidesPerView: 1 },
          640: { slidesPerView: 2 },
          768: { slidesPerView: 3 },
          1024: { slidesPerView: 4 },
        },
      });
    }
  }
});
