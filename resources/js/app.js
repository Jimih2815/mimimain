import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

document.addEventListener('DOMContentLoaded', () => {
  // Slider bộ sưu tập
  new Swiper('.collection-swiper', {
    direction: 'horizontal',
    slidesPerView: 3,
    spaceBetween: 20,
    grabCursor: true,
    simulateTouch: true,
    navigation: {
      nextEl: '.collection-swiper .swiper-button-next',
      prevEl: '.collection-swiper .swiper-button-prev',
    },
    pagination: {
      el: '.collection-swiper .swiper-pagination',
      clickable: true,
    },
  });

  // Slider sản phẩm
  new Swiper('.product-swiper', {
    direction: 'horizontal',
    slidesPerView: 4,
    spaceBetween: 15,
    grabCursor: true,
    simulateTouch: true,
    navigation: {
      nextEl: '.product-swiper .swiper-button-next',
      prevEl: '.product-swiper .swiper-button-prev',
    },
    pagination: {
      el: '.product-swiper .swiper-pagination',
      clickable: true,
    },
  });
});
