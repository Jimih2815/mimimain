import 'bootstrap';
import './bootstrap';        // nếu bạn có
import { Navigation, Pagination } from 'swiper';
import Swiper from 'swiper';

window.initSwipers = () => {
  new Swiper('.collection-swiper', {
    modules: [Navigation, Pagination],
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    pagination: { el: '.swiper-pagination', clickable: true },
  });
  new Swiper('.product-swiper', {
    modules: [Navigation, Pagination],
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    pagination: { el: '.swiper-pagination', clickable: true },
  });
};

document.addEventListener('DOMContentLoaded', () => {
  if (typeof initSwipers === 'function') initSwipers();
});
