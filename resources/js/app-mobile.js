// resources/js/app-mobile.js

import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
window.bootstrap = bootstrap;

console.log('🔥 app-mobile.js loaded');

document.addEventListener('DOMContentLoaded', () => {
  console.log('🛠️ DOMContentLoaded mobile');

  // 1) Collection slider
  new Swiper('.collection-swiper', {
    slidesPerView: 1,
    spaceBetween: 12,
    loop: true,
    navigation: {
      prevEl: '.slider-full-width .swiper-button-prev',
      nextEl: '.slider-full-width .swiper-button-next',
    },
  });

  // 2) Product slider
  new Swiper('.product-swiper', {
    slidesPerView: 1,
    spaceBetween: 12,
    loop: true,
    navigation: {
      prevEl: '.slider-product .product-slider-prev',
      nextEl: '.slider-product .product-slider-next',
    },
  });
});
