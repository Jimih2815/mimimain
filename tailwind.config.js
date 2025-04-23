import defaultTheme from 'tailwindcss/defaultTheme';
import forms        from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,vue,ts}',          // nếu bạn có JS/Vue
    './storage/framework/views/*.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],

  /*  👇 NEW: nói với Tailwind “đừng dọn sạch” các class này */
  safelist: [
    'tab-pane',   // container của mỗi pane
    'fade',       // hiệu ứng mờ
    'show',       // pane đã hiển thị
    'active',     // nav-link & pane đang chọn
    'nav-link',   // nút tab
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [forms],
};
