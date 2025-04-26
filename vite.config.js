import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/scss/app.scss',
        'resources/js/app.js',
      ],
      refresh: true,
    }),
  ],
  server: {
    // ở Windows đôi khi “lờ” file change, bật polling để chắc
    watch: {
      usePolling: true,
      interval: 100,
    },
    // ép dev-server chỉ lắng nghe đúng localhost
    host: '127.0.0.1',
    port: 5173,
    hmr: {
      host: '127.0.0.1',
      protocol: 'ws',
      port: 5173,
    },
  },
});
