import { build, mergeConfig } from 'vite';
import path from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { readFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');
const mode = process.env.NODE_ENV ?? 'production';

const sanitizedEnvKeys = [
  'npm_config_http_proxy',
  'npm_config_https_proxy',
  'npm_config_proxy'
];

for (const key of sanitizedEnvKeys) {
  if (process.env[key]) {
    console.warn(`[build] Removing incompatible npm config ${key} to keep Vite output deterministic.`);
    delete process.env[key];
  }
}

const configFile = path.resolve(rootDir, 'vite.config.mjs');
let userConfigModule;
try {
  userConfigModule = await import(pathToFileURL(configFile).href);
} catch (error) {
  throw new Error(`[build] Unable to import Vite config: ${error instanceof Error ? error.message : error}`);
}

const userConfig = (userConfigModule?.default ?? userConfigModule) ?? {};

const finalConfig = mergeConfig(userConfig, {
  configFile,
  mode,
  root: rootDir
});

await build(finalConfig);

const manifestPath = path.resolve(rootDir, 'assets/dist/manifest.json');
if (!existsSync(manifestPath)) {
  throw new Error('[build] Missing Vite manifest at assets/dist/manifest.json after build.');
}

const manifestRaw = await readFile(manifestPath, 'utf8');
let manifest;
try {
  manifest = JSON.parse(manifestRaw);
} catch (error) {
  throw new Error(`[build] Failed to parse manifest.json: ${error instanceof Error ? error.message : error}`);
}

const requiredEntries = {
  'assets/src/js/main.js': 'pf-main core script',
  'assets/src/js/front-page.js': 'front page enhancements',
  'assets/src/js/pwa.js': 'PWA module',
  'assets/src/css/main.css': 'core styles',
  'assets/src/css/front-page.css': 'front page styles',
  'assets/src/css/product.css': 'product styles',
  'assets/src/css/portfolio.css': 'portfolio styles'
};

const missingEntries = Object.entries(requiredEntries)
  .filter(([entry]) => !manifest[entry] || !manifest[entry].file)
  .map(([, label]) => label);

if (missingEntries.length > 0) {
  throw new Error(`[build] Manifest is missing required bundles: ${missingEntries.join(', ')}`);
}

console.log(`[build] Manifest validated with ${Object.keys(manifest).length} entries.`);
console.log('[build] Production bundle ready for WordPress theme.');
