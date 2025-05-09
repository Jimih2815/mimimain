import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import purgecssPlugin from '@fullhuman/postcss-purgecss';

const purgecss = purgecssPlugin.default ?? purgecssPlugin;

export default defineConfig({
  plugins: [
    laravel({
      // ⚡ GỌI ĐỦ 4 ENTRY: desktop + mobile
      input: [
        'resources/scss/app.scss',
        'resources/js/app.js',
        'resources/scss/app-mobile.scss',
        'resources/js/app-mobile.js',
      ],
      refresh: true, // tự reload khi blade/js thay đổi
    }),
    splitVendorChunkPlugin(), // tách vendor chunks
  ],

  resolve: {
    alias: {
      // đảm bảo import 'swiper' ko lỗi
      swiper: 'swiper',
    },
  },

  optimizeDeps: {
    // pre‐bundle Swiper cho dev nhanh hơn
    include: [
      'swiper',
      'swiper/bundle',
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
                safelist: [/^swiper-/, /^swiper$/, /^swiper\/bundle/],
                defaultExtractor: (content) =>
                  content.match(/[\w-/:]+(?<!:)/g) || [],
              }),
            ]
          : []),
      ],
    },
  },

  build: {
    rollupOptions: {
      output: {
        // tách riêng bundle Swiper và TinyMCE
        manualChunks(id) {
          if (id.includes('node_modules/swiper')) {
            return 'swiper';
          }
          if (id.includes('node_modules/tinymce')) {
            return 'editor';
          }
        },
      },
    },
  },

  server: {
    host: '127.0.0.1',
    port: 5173,
    hmr: {
      protocol: 'ws',
      host: '127.0.0.1',
      port: 5173,
    },
    watch: {
      usePolling: true,
      interval: 100,
      ignored: ['!**/node_modules/**', 'storage/**'],
    },
  },
});
