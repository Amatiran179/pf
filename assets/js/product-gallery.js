/*!
 * PUTRAFIBER — PRODUCT GALLERY (SQUARE VARIANT – FINAL)
 * - Anti double-init & teardown aman
 * - Autoplay/loop hanya jika slide > 1
 * - Thumbs sinkron (slidesPerView: 'auto', spaceBetween: 8→6 di mobile)
 * - Paksa frame & img thumbnail selalu KOTAK (84/72) via inline !important
 * - SimpleLightbox opsional (kalau ada)
 * - Tidak menyentuh transform wrapper (Swiper tetap normal)
 */

(function () {
  "use strict";

  // Cegah binding dobel
  if (window.pfProductGalleryBound) return;
  window.pfProductGalleryBound = true;

  // ---------- Teardown ----------
  function destroyPrevious() {
    try { if (window.pfGalleryMain && window.pfGalleryMain.destroy)   window.pfGalleryMain.destroy(true, true); } catch(e){}
    try { if (window.pfGalleryThumbs && window.pfGalleryThumbs.destroy) window.pfGalleryThumbs.destroy(true, true); } catch(e){}
    try { if (window.pfLightbox && window.pfLightbox.destroy)          window.pfLightbox.destroy(); } catch(e){}
    window.pfGalleryMain = null;
    window.pfGalleryThumbs = null;
    window.pfLightbox = null;
    window.pfProductGalleryInitialized = false;
  }

  // ---------- Helpers ----------
  function debounce(fn, wait){ let t; return function(){ clearTimeout(t); t=setTimeout(()=>fn.apply(this, arguments), wait); }; }

  function initLightbox(scopeEl) {
    if (typeof SimpleLightbox !== "function") return null;
    var selector = ".product-gallery .gallery-item";
    if (!scopeEl.querySelector(selector)) return null;
    try {
      return new SimpleLightbox(selector, {
        captions: true,
        captionsData: "alt",
        captionDelay: 200,
        close: true,
        nav: true,
        loop: true,
        history: false,
        docClose: true
      });
    } catch(e){ return null; }
  }

  // ——— Enforce thumbnail square (84 desktop / 72 mobile) ———
  function enforceThumbSquare() {
    try {
      var isMobile = window.matchMedia('(max-width: 640px)').matches;
      var sz = isMobile ? 72 : 84;
      var slides = document.querySelectorAll('.product-gallery .product-gallery-thumbs .swiper-slide');
      var imgs   = document.querySelectorAll('.product-gallery .product-gallery-thumbs .swiper-slide img');

      slides.forEach(function (sl) {
        sl.style.setProperty('width',     sz + 'px', 'important');
        sl.style.setProperty('min-width', sz + 'px', 'important');
        sl.style.setProperty('height',    sz + 'px', 'important');
        sl.style.setProperty('flex',    '0 0 ' + sz + 'px', 'important');
        sl.style.setProperty('box-sizing','content-box', 'important');
        sl.style.display = 'inline-flex';
        sl.style.alignItems = 'center';
        sl.style.justifyContent = 'center';
      });

      imgs.forEach(function (im) {
        im.style.setProperty('width',  sz + 'px', 'important');
        im.style.setProperty('height', sz + 'px', 'important');
        im.style.objectFit = 'cover';
        im.style.display = 'block';
      });
    } catch (e) {}
  }

  // ——— Init utama ———
  function initGallery() {
    var galleryRoot = document.querySelector(".product-gallery");
    var mainEl = document.querySelector(".product-gallery-slider");
    if (!galleryRoot || !mainEl || typeof Swiper !== "function") return;

    destroyPrevious();

    // Bersihkan transform/anim di IMG (bukan wrapper)
    mainEl.querySelectorAll(".gallery-image").forEach(function (img) {
      img.style.transform = "none";
      img.style.animation = "none";
      img.style.transition = "none";
      img.style.willChange = "auto";
    });

    var mainSlides = mainEl.querySelectorAll(".swiper-slide");
    var slideCount = mainSlides ? mainSlides.length : 0;
    var enableLoop = slideCount > 1;
    var enableAutoplay = slideCount > 1;

    var navPrev = mainEl.querySelector(".swiper-button-prev") || galleryRoot.querySelector(".swiper-button-prev");
    var navNext = mainEl.querySelector(".swiper-button-next") || galleryRoot.querySelector(".swiper-button-next");
    var paginationEl = mainEl.querySelector(".swiper-pagination") || galleryRoot.querySelector(".swiper-pagination");

    // Thumbs
    var thumbsEl = document.querySelector(".product-gallery-thumbs");
    var thumbsSwiper = null;

    if (thumbsEl && thumbsEl.querySelectorAll(".swiper-slide").length > 0) {
      try {
        thumbsSwiper = new Swiper(thumbsEl, {
          slidesPerView: 'auto',
          spaceBetween: 8,               // default
          freeMode: true,
          watchSlidesProgress: true,
          watchSlidesVisibility: true,
          slideToClickedSlide: true,
          breakpoints: {
            0:    { slidesPerView: 'auto', spaceBetween: 6 },
            480:  { slidesPerView: 'auto', spaceBetween: 6 },
            768:  { slidesPerView: 'auto', spaceBetween: 8 },
            1024: { slidesPerView: 'auto', spaceBetween: 8 }
          }
        });
      } catch (e) { thumbsSwiper = null; }
    }

    // Main Swiper
    var gallerySwiper = null;
    try {
      gallerySwiper = new Swiper(mainEl, {
        observer: true,
        observeParents: true,
        resizeObserver: true,

        navigation: (navPrev && navNext) ? { prevEl: navPrev, nextEl: navNext } : {},
        pagination: paginationEl ? { el: paginationEl, clickable: true } : {},

        loop: enableLoop,
        autoplay: enableAutoplay ? { delay: 3500, disableOnInteraction: false } : false,

        effect: "slide",
        speed: 500,

        thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,

        on: {
          init: function () {
            var self = this;
            if (self.params.autoplay && self.autoplay && typeof self.autoplay.start === 'function') {
              self.autoplay.start();
            }
            setTimeout(function(){
              try {
                self.update();
                thumbsSwiper && thumbsSwiper.update();
                enforceThumbSquare();
              } catch(e){}
            }, 60);
          },
          imagesReady: function () {
            try {
              this.update();
              if (this.params.autoplay && this.autoplay && this.autoplay.start) this.autoplay.start();
              thumbsSwiper && thumbsSwiper.update();
              enforceThumbSquare();
            } catch(e){}
          }
        }
      });
    } catch (e) { gallerySwiper = null; }

    // Terapkan ukuran kotak di berbagai lifecycle
    enforceThumbSquare();
    if (thumbsSwiper) {
      thumbsSwiper.on('init resize update imagesReady setTranslate transitionEnd', enforceThumbSquare);
    }
    if (gallerySwiper) {
      gallerySwiper.on('imagesReady transitionEnd', enforceThumbSquare);
    }
    window.addEventListener('resize', enforceThumbSquare);
    setTimeout(enforceThumbSquare, 100);
    setTimeout(enforceThumbSquare, 300);

    // Klik thumb → pindah slide (fallback)
    if (thumbsSwiper && gallerySwiper && thumbsEl) {
      thumbsEl.addEventListener('click', function(e){
        var slide = e.target.closest('.swiper-slide');
        if (!slide) return;
        var all = thumbsEl.querySelectorAll('.swiper-slide');
        var idx = Array.prototype.indexOf.call(all, slide);
        if (idx >= 0) { try { gallerySwiper.slideToLoop(idx); } catch(e){} }
      });
    }

    // Lightbox (opsional)
    var lightbox = initLightbox(galleryRoot);

    // Ekspor untuk debug
    window.pfGalleryMain   = gallerySwiper;
    window.pfGalleryThumbs = thumbsSwiper;
    window.pfLightbox      = lightbox;
    window.pfProductGalleryInitialized = true;

    // Debounced resize update
    var onResize = debounce(function(){
      try {
        gallerySwiper && gallerySwiper.update();
        thumbsSwiper && thumbsSwiper.update();
        enforceThumbSquare();
      } catch(e){}
    }, 120);
    if (!window._pfGalleryResizeBound) {
      window.addEventListener("resize", onResize);
      window._pfGalleryResizeBound = true;
      window._pfGalleryOnResize = onResize;
    }
  }

  function ready(fn){
    if (document.readyState === "complete" || document.readyState === "interactive") { setTimeout(fn,0); }
    else { document.addEventListener("DOMContentLoaded", fn, { once: true }); }
  }

  ready(function(){ initGallery(); });
})();
