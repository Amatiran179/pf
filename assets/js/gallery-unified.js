/**
 * Product & Portfolio Gallery with Swiper & Lightbox - FINAL FIX
 *
 * @package PutraFiber
 * @version 1.6.0 - Simplified selectors for robustness
 * @description Single source of truth for gallery initialization
 */

(function($) {
    'use strict';

    if (window.pfProductGalleryInitialized) {
        console.log('⚠️ Gallery script already initialized, skipping...');
        return;
    }
    window.pfProductGalleryInitialized = true;

    let galleryMain = null;
    let galleryThumbs = null;
    let portfolioGalleryMain = null;
    let portfolioGalleryThumbs = null;
    let lightboxInstance = null;
    const zoomObservers = new WeakMap();

    const config = window.pfGalleryConfig || {
        autoplayDelay: 4000,
        slideSpeed: 600,
        enableLoop: true,
        enableAutoplay: true,
        debug: false
    };

    function log(message, type = 'info') {
        if (!config.debug) return;
        const emoji = { 'info': 'ℹ️', 'success': '✅', 'warning': '⚠️', 'error': '❌', 'loading': '⏳' };
        console.log(`${emoji[type] || ''} [PF Gallery] ${message}`);
    }

    $(document).ready(function() {
        log('DOM Ready - Starting initialization...', 'loading');
        guardAllGalleries();
        waitForLibraries();
    });

    function waitForLibraries() {
        let attempts = 0;
        const maxAttempts = 50;

        const checkLibraries = setInterval(function() {
            attempts++;
            const swiperReady = typeof Swiper !== 'undefined';

            if (swiperReady) {
                clearInterval(checkLibraries);
                log('Swiper library loaded successfully!', 'success');
                initProductGallery();
                initPortfolioGallery();
                initLightbox();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkLibraries);
                log('Swiper library failed to load.', 'error');
            }
        }, 100);
    }

    function neutraliseZoomTarget(element) {
        if (!element) return;

        element.style.setProperty('transform', 'none', 'important');
        element.style.setProperty('animation', 'none', 'important');

        if (element.classList && element.classList.contains('animate-zoom-in')) {
            element.classList.remove('animate-zoom-in');
        }

        if (element.classList && element.classList.contains('fade-in') && !element.classList.contains('visible')) {
            element.classList.add('visible');
        }
    }

    function enforceNoZoomOnHover(container) {
        if (!container) return;

        container.querySelectorAll('.gallery-item').forEach((item) => {
            const enforce = () => {
                const image = item.querySelector('.gallery-image');
                if (!image) return;

                neutraliseZoomTarget(item);
                neutraliseZoomTarget(image);
            };

            enforce();

            if (item.dataset.pfNoZoomBound === 'true') {
                return;
            }

            item.dataset.pfNoZoomBound = 'true';

            item.addEventListener('mouseenter', enforce);
            item.addEventListener('focus', enforce);
            item.addEventListener('touchstart', enforce);
        });
    }

    function resetGalleryTransforms(selector) {
        if (!selector) return;

        document.querySelectorAll(selector).forEach((container) => {
            guardGalleryContainer(container);
        });
    }

    function guardGalleryContainer(container) {
        if (!container) return;

        container.querySelectorAll('.gallery-item, .gallery-image').forEach((element) => {
            neutraliseZoomTarget(element);
        });

        enforceNoZoomOnHover(container);

        if (!zoomObservers.has(container) && typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(() => {
                container.querySelectorAll('.gallery-item, .gallery-image').forEach((element) => {
                    neutraliseZoomTarget(element);
                });
                enforceNoZoomOnHover(container);
            });

            observer.observe(container, { childList: true, subtree: true });
            zoomObservers.set(container, observer);
        }
    }

    function guardAllGalleries() {
        requestAnimationFrame(() => {
            resetGalleryTransforms('.product-gallery-slider');
            resetGalleryTransforms('.portfolio-gallery-slider');
            resetGalleryTransforms('.full-gallery-grid');
        });
    }

    function initProductGallery() {
        if (typeof Swiper === 'undefined' || $('.product-gallery-slider').length === 0) {
            if ($('.product-gallery-slider').length > 0) log('Swiper not defined for Product Gallery.', 'error');
            return;
        }

        try {
            if (galleryMain) galleryMain.destroy(true, true);
            if (galleryThumbs) galleryThumbs.destroy(true, true);

            const totalSlides = $('.product-gallery-slider .swiper-slide').length;

            if ($('.product-gallery-thumbs').length > 0 && totalSlides > 1) {
                galleryThumbs = new Swiper('.product-gallery-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                });
            }

            const mainSliderConfig = buildSwiperConfig(totalSlides, galleryThumbs, '.product-gallery-slider');
            galleryMain = new Swiper('.product-gallery-slider', mainSliderConfig);
            resetGalleryTransforms('.product-gallery-slider');
            guardAllGalleries();
            log('Product gallery initialized successfully!', 'success');

        } catch (error) {
            log('Product gallery initialization error: ' + error.message, 'error');
            console.error(error);
        }
    }

    function initPortfolioGallery() {
        if (typeof Swiper === 'undefined' || $('.portfolio-gallery-slider').length === 0) {
            if ($('.portfolio-gallery-slider').length > 0) log('Swiper not defined for Portfolio Gallery.', 'error');
            return;
        }

        try {
            if (portfolioGalleryMain) portfolioGalleryMain.destroy(true, true);
            if (portfolioGalleryThumbs) portfolioGalleryThumbs.destroy(true, true);

            const totalSlides = $('.portfolio-gallery-slider .swiper-slide').length;

            if ($('.portfolio-gallery-thumbs').length > 0 && totalSlides > 1) {
                portfolioGalleryThumbs = new Swiper('.portfolio-gallery-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                });
            }

            // MENGGUNAKAN KONFIGURASI YANG SAMA DAN KONSISTEN
            const portfolioSliderConfig = buildSwiperConfig(totalSlides, portfolioGalleryThumbs, '.portfolio-gallery-slider');
            portfolioGalleryMain = new Swiper('.portfolio-gallery-slider', portfolioSliderConfig);
            resetGalleryTransforms('.portfolio-gallery-slider');
            guardAllGalleries();
            log('Portfolio gallery initialized successfully!', 'success');

        } catch (error) {
            log('Portfolio gallery initialization error: ' + error.message, 'error');
            console.error(error);
        }
    }

    /**
     * Helper function to build a consistent Swiper configuration
     */
    function buildSwiperConfig(totalSlides, thumbsSwiper = null, sliderSelector = '') {
        const sliderConfig = {
            spaceBetween: 10,
            slidesPerView: 1,
            watchOverflow: true, // Prevents nav buttons on single slide

            // ===================================
            // PERBAIKAN UTAMA: Selector disederhanakan untuk menghindari konflik
            // ===================================
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            // ===================================

            keyboard: {
                enabled: true,
            },
            grabCursor: true,
            on: {
                init: function () {
                    // Force update to fix potential layout issues on load
                    setTimeout(() => {
                        if (this && typeof this.update === 'function') {
                            this.update();
                        }
                        if (sliderSelector) {
                            resetGalleryTransforms(sliderSelector);
                        }
                    }, 100);
                },
                slideChangeTransitionEnd: function () {
                    if (sliderSelector) {
                        resetGalleryTransforms(sliderSelector);
                    }
                }
            }
        };

        if (thumbsSwiper) {
            sliderConfig.thumbs = {
                swiper: thumbsSwiper,
            };
        }

        if (totalSlides > 1) {
            sliderConfig.loop = config.enableLoop;
            if (config.enableAutoplay) {
                sliderConfig.autoplay = {
                    delay: config.autoplayDelay,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                };
            }
        }

        sliderConfig.observer = true;
        sliderConfig.observeParents = true;
        sliderConfig.observeSlideChildren = true;

        sliderConfig.on = sliderConfig.on || {};
        ['init', 'imagesReady', 'slideChangeTransitionStart', 'slideChangeTransitionEnd', 'resize', 'observerUpdate'].forEach((eventName) => {
            const previousHandler = sliderConfig.on[eventName];
            sliderConfig.on[eventName] = function() {
                guardGalleryContainer(this && this.el ? this.el : null);
                if (typeof previousHandler === 'function') {
                    previousHandler.apply(this, arguments);
                }
            };
        });

        return sliderConfig;
    }

    function initLightbox() {
        if (typeof SimpleLightbox !== 'undefined') {
            try {
                lightboxInstance = new SimpleLightbox('[data-lightbox]', {
                    captionsData: 'title',
                    captionPosition: 'bottom',
                });
                lightboxInstance.on('shown.simplelightbox', guardAllGalleries);
                lightboxInstance.on('changed.simplelightbox', guardAllGalleries);
                lightboxInstance.on('closed.simplelightbox', guardAllGalleries);
                log('SimpleLightbox initialized.', 'success');
            } catch (error) {
                log('SimpleLightbox error: ' + error.message, 'error');
            }
        }
    }

})(jQuery);
