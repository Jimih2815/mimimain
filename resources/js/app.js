import '../scss/app.scss';
import 'bootstrap';
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

document.addEventListener('DOMContentLoaded', () => {
  // Collection slider (free‐mode, auto width)
  new Swiper('.collection-swiper', {
    slidesPerView: 'auto',
    spaceBetween: 0,     // use CSS margin-right on slides instead
    freeMode: true,
    grabCursor: true,
    navigation: {
      nextEl: '.collection-swiper .swiper-button-next',
      prevEl: '.collection-swiper .swiper-button-prev',
    },
    pagination: {
      el: '.collection-swiper .swiper-pagination',
      clickable: true,
    },
  });

  // Product slider (fixed slidesPerView with breakpoints)
  new Swiper('.product-swiper', {
    slidesPerView: 4,
    spaceBetween: 15,
    loop: true,
    grabCursor: true,
    navigation: {
      nextEl: '.product-swiper .swiper-button-next',
      prevEl: '.product-swiper .swiper-button-prev',
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
});
