import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import purgecssPlugin from '@fullhuman/postcss-purgecss';

const purgecss = purgecssPlugin.default ?? purgecssPlugin;

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/scss/app.scss', 'resources/js/app.js'],
      refresh: true,
    }),
    splitVendorChunkPlugin(),
  ],

  resolve: {
    // Đảm bảo Vite hiểu import 'swiper'
    alias: {
      swiper: 'swiper',
    },
  },

  optimizeDeps: {
    // Bao gồm Swiper và CSS để Vite pre-bundle
    include: [
      'swiper',
      'swiper/css',
      'swiper/css/navigation',
    ],
  },

  css: {
    postcss: {
      plugins: [
        ...(process.env.NODE_ENV === 'production'
          ? [
              purgecss({
                content: [
                  './resources/**/*.blade.php',
                  './resources/js/**/*.{vue,js}',
                ],
                safelist: [/^swal-/, /^tox-/, /^mce-/, /^swiper-/],
                defaultExtractor: content =>
                  content.match(/[\w-/:]+(?<!:)/g) || [],
              }),
            ]
          : []),
      ],
    },
  },

  build: {
    sourcemap: false,
    rollupOptions: {
      output: {
        // Tách bundle cho Swiper và TinyMCE
        manualChunks(id) {
          if (id.includes('node_modules/swiper')) {
            return 'swiper';
          }
          if (id.includes('node_modules/tinymce')) {
            return 'editor';
          }
          // Giữ mặc định cho các vendor khác
        },
      },
    },
  },

  server: {
    watch: {
      usePolling: true,
      interval: 100,
      // có thể ignore folder storage nếu cần
      ignored: ['!**/node_modules/**', 'storage/**'],
    },
    host: '127.0.0.1',
    port: 5173,
    hmr: {
      host: '127.0.0.1',
      protocol: 'ws',
      port: 5173,
    },
  },
});
