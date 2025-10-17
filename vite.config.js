import { defineConfig } from 'vite';
import { resolve, parse } from 'path';

const rootDir = __dirname;

export default defineConfig({
  base: '',
  build: {
    manifest: 'manifest.json',
    outDir: resolve(rootDir, 'assets/dist'),
    emptyOutDir: false,
    rollupOptions: {
      input: {
        'assets/src/js/main.js': resolve(rootDir, 'assets/src/js/main.js'),
        'assets/src/js/front-page.js': resolve(rootDir, 'assets/src/js/front-page.js'),
        'assets/src/js/pwa.js': resolve(rootDir, 'assets/src/js/pwa.js'),
        'assets/src/css/main.css': resolve(rootDir, 'assets/src/css/main.css'),
        'assets/src/css/front-page.css': resolve(rootDir, 'assets/src/css/front-page.css'),
        'assets/src/css/product.css': resolve(rootDir, 'assets/src/css/product.css'),
        'assets/src/css/portfolio.css': resolve(rootDir, 'assets/src/css/portfolio.css')
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
