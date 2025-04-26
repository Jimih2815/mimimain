// resources/js/app.js

// 1) Import SCSS chính (Tailwind + Bootstrap + Icon + custom)
// import '../scss/app.scss'

// 2) Bootstrap JS (dropdown, modal, tab…)
import 'bootstrap'
// import './home-swiper';
// 3) Swiper bundle + styles
import Swiper from 'swiper/bundle'
import 'swiper/css/bundle'

document.addEventListener('DOMContentLoaded', () => {
  // Collection slider (free-mode, auto width)
  new Swiper('.collection-swiper', {
    slidesPerView: 'auto',
    spaceBetween: 0,
    freeMode: true,
    grabCursor: true,
    navigation: {
      // selector mới trỏ ra ngoài .swiper, nằm trong .slider-full-width
      nextEl: '.slider-full-width .swiper-button-next',
      prevEl: '.slider-full-width .swiper-button-prev',
    },
    // bỏ pagination (dots) nếu không cần
    // pagination: {
    //   el: '.collection-swiper .swiper-pagination',
    //   clickable: true,
    // },
  })

  // Product slider (fixed slidesPerView với breakpoints)
  new Swiper('.product-swiper', {
    slidesPerView: 4,
    spaceBetween: 15,
    // loop: true,
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
  })
})
