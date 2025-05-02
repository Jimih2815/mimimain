import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import purgecssPlugin from '@fullhuman/postcss-purgecss';

const purgecss = purgecssPlugin.default ?? purgecssPlugin;

// helper chunk
const manualChunks = id => {
  if (id.includes('tinymce')) return 'editor';
  if (id.includes('swiper'))  return 'swiper';
};

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/scss/app.scss', 'resources/js/app.js'],
      refresh: true,
    }),
    splitVendorChunkPlugin(),
  ],

  // PostCSS
  css: {
    postcss: {
      plugins: [
        // Chỉ bật PurgeCSS khi build production
        ...(process.env.NODE_ENV === 'production'
          ? [
              purgecss({
                content: [
                  './resources/**/*.blade.php',
                  './resources/js/**/*.vue',
                  './resources/js/**/*.js',
                ],
                safelist: [/^swal-/, /^tox-/, /^mce-/],
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
    rollupOptions: { output: { manualChunks } },
  },

  server: {
    watch: { usePolling: true, interval: 100 },
    host: '127.0.0.1',
    port: 5173,
    hmr: { host: '127.0.0.1', protocol: 'ws', port: 5173 },
  },
});
