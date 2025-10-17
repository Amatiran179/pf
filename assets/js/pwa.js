(function() {
    'use strict';

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                console.log('ServiceWorker registration successful');
            }, function(err) {
                console.log('ServiceWorker registration failed: ', err);
            });
        });
    }

    // Install Prompt
    let deferredPrompt;
    const installButton = document.querySelector('.install-pwa');

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        
        if (installButton) {
            installButton.style.display = 'block';
        }
    });

    if (installButton) {
        installButton.addEventListener('click', function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                
                deferredPrompt.userChoice.then(function(choiceResult) {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                });
            }
        });
    }

    // Online/Offline Status
    function updateOnlineStatus() {
        const statusElement = document.querySelector('.online-status');
        
        if (navigator.onLine) {
            document.body.classList.remove('offline');
            document.body.classList.add('online');
            if (statusElement) {
                statusElement.textContent = 'Online';
            }
        } else {
            document.body.classList.remove('online');
            document.body.classList.add('offline');
            if (statusElement) {
                statusElement.textContent = 'Offline';
            }
            // Redirect to offline page if needed
            if (window.location.pathname !== '/offline') {
                // You can uncomment this to redirect to offline page
                // window.location.href = '/offline';
            }
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();

    // Cache Management
    if ('caches' in window) {
        // Clear old caches
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(cacheName) {
                    return cacheName.startsWith('putrafiber-') && cacheName !== 'putrafiber-v1';
                }).map(function(cacheName) {
                    return caches.delete(cacheName);
                })
            );
        });
    }

})();
