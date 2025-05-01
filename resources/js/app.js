// resources/js/app.js

import './sidebar';
// Bootstrap
import 'bootstrap';
// Swiper bundle + styles
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

document.addEventListener('DOMContentLoaded', () => {
  // A) Collection slider (free-mode, auto width)
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

  // B) Product slider (home page)
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
      640:  { slidesPerView: 2 },
      1024: { slidesPerView: 4 },
    },
  });

  // C) Related Products slider (product detail page)
  new Swiper('.product-swiper-related', {
    direction: 'horizontal',
    slidesPerView: 4,
    spaceBetween: 15,

    // Không loop vô tận, nhưng khi về cuối nhấn Next sẽ quay về đầu
    loop: false,
    rewind: false,

    // Cho phép drag chuột / touch
    grabCursor: true,
    simulateTouch: true,

    // Chặn browser tự kéo scroll khi bắt đầu touch/mouse
    touchStartPreventDefault: false,
    touchMoveStopPropagation: true,

    // Giảm lực “đẩy” khi kéo vượt biên
    resistance: true,
    resistanceRatio: 0.5,

    navigation: {
      prevEl: '.related-prev',
      nextEl: '.related-next',
    },

    breakpoints: {
      320:  { slidesPerView: 1 },
      640:  { slidesPerView: 2 },
      768:  { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });
});
