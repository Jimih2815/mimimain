import Swiper, { Navigation, Pagination } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

document.addEventListener('DOMContentLoaded', () => {
  Swiper.use([Navigation, Pagination]);

  new Swiper('.collection-swiper', {
    slidesPerView: 2,
    spaceBetween: 16,
    loop: true,
    navigation: {
      prevEl: '.slider-full-width .swiper-button-prev',
      nextEl: '.slider-full-width .swiper-button-next',
    },
    breakpoints: {
      640: { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });

  new Swiper('.product-swiper', {
    slidesPerView: 1,
    spaceBetween: 16,
    loop: true,
    navigation: {
      prevEl: '.product-swiper .swiper-button-prev',
      nextEl: '.product-swiper .swiper-button-next',
    },
    pagination: {
      el: '.product-swiper .swiper-pagination',
      clickable: true,
    },
    breakpoints: {
      640: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    },
  });
});
