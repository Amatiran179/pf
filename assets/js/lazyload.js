(function() {
    'use strict';

    // Lazy Load Images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                        img.removeAttribute('data-srcset');
                    }
                    
                    img.classList.add('loaded');
                    img.classList.remove('lazy-load');
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        // Observe all lazy images
        document.querySelectorAll('img.lazy-load, img[loading="lazy"]').forEach(function(img) {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for older browsers
        const lazyImages = document.querySelectorAll('img.lazy-load');
        
        lazyImages.forEach(function(img) {
            if (img.dataset.src) {
                img.src = img.dataset.src;
            }
            img.classList.add('loaded');
        });
    }

    // Lazy Load Background Images
    const bgObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const element = entry.target;
                const bgImage = element.dataset.background;
                
                if (bgImage) {
                    element.style.backgroundImage = 'url(' + bgImage + ')';
                    element.removeAttribute('data-background');
                    bgObserver.unobserve(element);
                }
            }
        });
    });

    document.querySelectorAll('[data-background]').forEach(function(el) {
        bgObserver.observe(el);
    });

    // Lazy Load Iframes
    const iframeObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const iframe = entry.target;
                
                if (iframe.dataset.src) {
                    iframe.src = iframe.dataset.src;
                    iframe.removeAttribute('data-src');
                    iframeObserver.unobserve(iframe);
                }
            }
        });
    });

    document.querySelectorAll('iframe[data-src]').forEach(function(iframe) {
        iframeObserver.observe(iframe);
    });

})();
