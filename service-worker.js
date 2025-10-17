self.addEventListener("install", e => {
  self.skipWaiting();
});

self.addEventListener("activate", e => {
  clients.claim();
});

self.addEventListener("fetch", e => {
  e.respondWith(
    caches.open("pf-cache").then(cache => {
      return cache.match(e.request).then(res => {
        return res || fetch(e.request).then(response => {
          if (e.request.url.startsWith(self.location.origin)) {
            cache.put(e.request, response.clone());
          }
          return response;
        });
      });
    })
  );
});
