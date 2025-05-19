// resources/js/app-mobile.js

import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
window.bootstrap = bootstrap;

console.log('🔥 app-mobile.js loaded');

document.addEventListener('DOMContentLoaded', () => {
  console.log('🛠️ DOMContentLoaded mobile');

  // 1) Collection slider với peek next (nếu bạn có dùng .collection-swiper)
  new Swiper('.collection-swiper', {
    slidesPerView: 1.15,      // hiển thị 1 slide chính + 0.15 peek tiếp
    spaceBetween: 12,         // khoảng cách giữa slide
    loop: true,               // lặp vô tận
    navigation: {
      prevEl: '.slider-full-width .swiper-button-prev',
      nextEl: '.slider-full-width .swiper-button-next',
    },
  });

  // 2) Product slider (ảnh chính sản phẩm) + pagination chấm
  new Swiper('.product-swiper', {
    slidesPerView: 1,
    spaceBetween: 2,
    loop: false,              
    navigation: {
      prevEl: '.slider-product .product-slider-prev',
      nextEl: '.slider-product .product-slider-next',
    },
    pagination: {
      el: '.product-swiper-pagination',
      clickable: true,
    },
  });

  new Swiper('.product-swiper-related', {
    slidesPerView: 3.2,
    spaceBetween: 12,
    loop: false,
    navigation: {
      prevEl: '.slider-related .related-prev',
      nextEl: '.slider-related .related-next',
    },
    
  });
});
