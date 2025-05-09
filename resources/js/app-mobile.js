// resources/js/app-mobile.js

import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
window.bootstrap = bootstrap;

console.log('🔥 app-mobile.js loaded');

document.addEventListener('DOMContentLoaded', () => {
  console.log('🛠️ DOMContentLoaded mobile');

  // 1) Collection slider với peek next
  new Swiper('.collection-swiper', {
    // hiển thị 1.0 slide + 0.15 slide kế bên
    slidesPerView: 1.15,
    // khoảng cách giữa các slide
    spaceBetween: 12,
    // giữ loop nếu bạn muốn vòng lặp vô tận
    loop: true,
    navigation: {
      prevEl: '.slider-full-width .swiper-button-prev',
      nextEl: '.slider-full-width .swiper-button-next',
    },
  });


    // 2) Product slider: show 1 slide + peek next
    new Swiper('.product-swiper', {
      slidesPerView: 1.15,      // hiển thị 1.0 slide + 0.15 slide kế
      spaceBetween: 12,         // khoảng cách giữa các slide
      loop: false,              // thường peek thì không loop
      navigation: {
        prevEl: '.slider-product .product-slider-prev',
        nextEl: '.slider-product .product-slider-next',
      },
    });
});
