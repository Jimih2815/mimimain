import './sidebar';

// Bootstrap JS (dropdown, modal, tab…)
import 'bootstrap';

import './app';
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
    navigation: {
      nextEl: '.slider-full-width .swiper-button-next',
      prevEl: '.slider-full-width .swiper-button-prev',
    },
  });

  // B) Product slider (home page)
  new Swiper('.product-swiper', {
    slidesPerView: 4,
    spaceBetween: 15,
    grabCursor: true,
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
    slidesPerView: 4,
    spaceBetween: 15,
    loop: true,
    grabCursor: true,
    simulateTouch: true,
    touchReleaseOnEdges: true,
    navigation: {
      prevEl: '.related-prev',
      nextEl: '.related-next',
    },
    pagination: {
      el: '.product-swiper-related .swiper-pagination',
      clickable: true,
    },
    breakpoints: {
      320:  { slidesPerView: 1 },
      640:  { slidesPerView: 2 },
      768:  { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });
});
