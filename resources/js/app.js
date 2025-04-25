// resources/js/app.js

// 1) Import SCSS chính (Tailwind + Bootstrap + Icon + custom)
import '../scss/app.scss'

// 2) Bootstrap JS (dropdown, modal, tab…)
import 'bootstrap'

// 3) Swiper bundle + styles
import Swiper from 'swiper/bundle'
import 'swiper/css/bundle'

document.addEventListener('DOMContentLoaded', () => {
  // Collection slider (free-mode, auto width)
  new Swiper('.collection-swiper', {
    slidesPerView: 'auto',
    spaceBetween: 0,     // margin-right sẽ xử lý bằng CSS
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
  })

  // Product slider (fixed slidesPerView với breakpoints)
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
      640:  { slidesPerView: 2 },
      1024: { slidesPerView: 4 },
    },
  })
})
