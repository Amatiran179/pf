const CACHE_VERSION = 'v3';
const CACHE_PREFIX = 'pf-cache';
const CACHE_NAME = `${CACHE_PREFIX}-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline';

const PRECACHE_URLS = [
  '/',
  OFFLINE_URL,
  '/style.css',
  '/assets/css/product.css',
  '/assets/css/portfolio.css',
  '/assets/css/utilities.css',
  '/assets/js/main.js',
  '/assets/js/gallery-unified.js'
];

self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then(cache =>
        Promise.all(
          PRECACHE_URLS.map(url => cache.add(url).catch(() => null))
        )
      )
      .catch(() => Promise.resolve())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(name => {
          if (name.startsWith(CACHE_PREFIX) && name !== CACHE_NAME) {
            return caches.delete(name);
          }
          return Promise.resolve(false);
        })
      );
    })
  );
  clients.claim();
});

self.addEventListener('fetch', event => {
  const { request } = event;

  if (request.method !== 'GET') {
    return;
  }

  const requestUrl = new URL(request.url);

  if (requestUrl.origin !== self.location.origin) {
    return;
  }

  if (requestUrl.pathname.startsWith('/wp-admin') || requestUrl.pathname.includes('admin-ajax.php')) {
    return;
  }

  if (request.destination === 'image' || /\.(png|jpe?g|gif|svg|webp|avif)$/i.test(requestUrl.pathname)) {
    event.respondWith(cacheFirst(request));
    return;
  }

  if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
    event.respondWith(networkFirst(request));
    return;
  }

  event.respondWith(staleWhileRevalidate(request));
});

function cacheFirst(request) {
  return caches.open(CACHE_NAME).then(cache =>
    cache.match(request).then(match => {
      if (match) {
        return match;
      }

      return fetch(request)
        .then(response => {
          if (response && response.status === 200) {
            cache.put(request, response.clone());
          }
          return response;
        })
        .catch(() => caches.match(OFFLINE_URL));
    })
  );
}

function networkFirst(request) {
  return caches.open(CACHE_NAME).then(cache =>
    fetch(request)
      .then(response => {
        if (response && response.status === 200) {
          cache.put(request, response.clone());
        }
        return response;
      })
      .catch(() => cache.match(request).then(match => match || cache.match(OFFLINE_URL)))
  );
}

function staleWhileRevalidate(request) {
  return caches.open(CACHE_NAME).then(cache =>
    cache.match(request).then(cachedResponse => {
      const fetchPromise = fetch(request)
        .then(response => {
          if (response && response.status === 200) {
            cache.put(request, response.clone());
          }
          return response;
        })
        .catch(() => cachedResponse || cache.match(OFFLINE_URL));

      return cachedResponse || fetchPromise;
    })
  );
}
