import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import purgecssPlugin from '@fullhuman/postcss-purgecss';

const purgecss = purgecssPlugin.default ?? purgecssPlugin;

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/scss/app.scss',
        'resources/js/app.js',
        // ➟ Bổ sung mobile bundles
        'resources/scss/app-mobile.scss',
        'resources/js/app-mobile.js',
      ],
      refresh: true,
    }),
    splitVendorChunkPlugin(),
  ],

  resolve: {
    alias: {
      // để Vite hiểu import 'swiper'
      swiper: 'swiper',
    },
  },

  optimizeDeps: {
    include: [
      'swiper', // pre‐bundle core
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
                safelist: [/^swiper-/],
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
        manualChunks(id) {
          if (id.includes('node_modules/swiper')) {
            return 'swiper';
          }
        },
      },
    },
  },

  server: {
    host: '127.0.0.1',
    port: 5173,
    hmr: {
      host: '127.0.0.1',
      protocol: 'ws',
      port: 5173,
    },
    watch: {
      usePolling: true,
      interval: 100,
      ignored: ['!**/node_modules/**', 'storage/**'],
    },
  },
});
