import defaultTheme from 'tailwindcss/defaultTheme'
import forms        from '@tailwindcss/forms'
import aspectRatio  from '@tailwindcss/aspect-ratio'

export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,vue,ts}',
    './resources/scss/**/*.scss',
    './storage/framework/views/*.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  safelist: ['tab-pane','fade','show','active','nav-link'],
  theme: {
    extend: { fontFamily: { sans: ['Poppins', ...defaultTheme.fontFamily.sans] } },
  },
  plugins: [
    forms,
    aspectRatio,      // ← thêm plugin này
  ],
}
