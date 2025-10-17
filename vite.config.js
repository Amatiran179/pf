import { defineConfig } from 'vite';
import { resolve, parse } from 'path';

export default defineConfig({
  base: '',
  build: {
    manifest: true,
    outDir: resolve(__dirname, 'assets/dist'),
    emptyOutDir: false,
    rollupOptions: {
      input: {
        'assets/src/js/main.js': resolve(__dirname, 'assets/src/js/main.js'),
        'assets/src/css/main.css': resolve(__dirname, 'assets/src/css/main.css')
      },
      output: {
        entryFileNames: 'js/[name]-[hash].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: ({ name }) => {
          if (!name) {
            return 'assets/[hash][extname]';
          }
          const parsed = parse(name);
          const baseName = parsed.name;
          const ext = parsed.ext;
          if (name.endsWith('.css')) {
            return `css/${baseName}-[hash]${ext}`;
          }
          if (/\.(woff2?|ttf|otf|eot)$/.test(name)) {
            return `fonts/${baseName}-[hash]${ext}`;
          }
          if (/\.(png|jpe?g|gif|svg|webp|avif)$/.test(name)) {
            return `images/${baseName}-[hash]${ext}`;
          }
          return `assets/${baseName}-[hash]${ext}`;
        }
      }
    }
  },
  publicDir: false
});
