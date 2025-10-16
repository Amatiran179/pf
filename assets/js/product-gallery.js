/*!
 * PUTRAFIBER — UNIFIED PRODUCT & PORTFOLIO GALLERY
 * ------------------------------------------------------------------
 * - Shared initializer for both product (shop) & portfolio templates
 * - Anti double-init & resilient against missing Swiper/Lightbox
 * - Autoplay/loop enabled only when slides > 1
 * - Optional thumbnail square enforcement (product only)
 * - Lightbox scoped per gallery root to avoid cross-interference
 * - Graceful degradation: skips silently if requirements are missing
 */

(function () {
  "use strict";

  if (window.pfUnifiedGalleryBound) return;
  window.pfUnifiedGalleryBound = true;

  var galleryConfigs = [
    {
      type: "product",
      rootSelector: ".product-gallery",
      sliderSelector: ".product-gallery-slider",
      thumbsSelector: ".product-gallery-thumbs",
      enforceSquareThumbs: true,
      thumbSize: { desktop: 84, mobile: 72 },
    },
    {
      type: "portfolio",
      rootSelector: ".portfolio-gallery",
      sliderSelector: ".portfolio-gallery-slider",
      thumbsSelector: ".portfolio-gallery-thumbs",
      enforceSquareThumbs: false,
      thumbSize: { desktop: 84, mobile: 72 },
    },
  ];

  var instances = [];

  function createLightbox(root) {
    if (typeof SimpleLightbox !== "function") return null;
    var anchors = root.querySelectorAll("a.gallery-item");
    if (!anchors || anchors.length === 0) return null;

    try {
      return new SimpleLightbox({
        elements: anchors,
        captions: true,
        captionsData: "alt",
        captionDelay: 200,
        close: true,
        nav: true,
        loop: true,
        history: false,
        docClose: true,
      });
    } catch (e) {
      return null;
    }
  }

  function resetImageTransforms(mainEl) {
    if (!mainEl) return;
    try {
      mainEl.querySelectorAll(".gallery-image, img.gallery-image").forEach(function (img) {
        img.style.transform = "none";
        img.style.animation = "none";
        img.style.transition = "none";
        img.style.willChange = "auto";
      });
    } catch (e) {}
  }

  function nodeListToArray(list) {
    return Array.prototype.slice.call(list || []);
  }

  function enforceThumbDimensions(instance) {
    if (!instance || !instance.config || !instance.thumbsEl) return;

    var config = instance.config;
    var thumbsEl = instance.thumbsEl;
    var slides = nodeListToArray(thumbsEl.querySelectorAll(".swiper-slide"));
    var imgs = nodeListToArray(thumbsEl.querySelectorAll(".swiper-slide img"));

    if (slides.length === 0 || imgs.length === 0) return;

    if (config.enforceSquareThumbs) {
      var isMobile = false;
      try {
        isMobile = window.matchMedia && window.matchMedia("(max-width: 640px)").matches;
      } catch (e) {}

      var size = isMobile ? config.thumbSize.mobile : config.thumbSize.desktop;

      slides.forEach(function (slide) {
        slide.style.setProperty("width", size + "px", "important");
        slide.style.setProperty("min-width", size + "px", "important");
        slide.style.setProperty("height", size + "px", "important");
        slide.style.setProperty("flex", "0 0 " + size + "px", "important");
        slide.style.setProperty("box-sizing", "content-box", "important");
        slide.style.display = "inline-flex";
        slide.style.alignItems = "center";
        slide.style.justifyContent = "center";
      });

      imgs.forEach(function (img) {
        img.style.setProperty("width", size + "px", "important");
        img.style.setProperty("height", size + "px", "important");
        img.style.objectFit = "cover";
        img.style.display = "block";
      });
    } else {
      slides.forEach(function (slide) {
        slide.style.removeProperty("width");
        slide.style.removeProperty("min-width");
        slide.style.removeProperty("height");
        slide.style.removeProperty("flex");
        slide.style.removeProperty("box-sizing");
        slide.style.removeProperty("display");
        slide.style.removeProperty("align-items");
        slide.style.removeProperty("justify-content");
      });

      imgs.forEach(function (img) {
        img.style.removeProperty("width");
        img.style.removeProperty("height");
        img.style.removeProperty("object-fit");
        img.style.removeProperty("display");
      });
    }
  }

  function setupGallery(root, config) {
    if (!root || root.dataset.pfGalleryInitialized === "true") return;
    if (typeof Swiper !== "function") return;

    var mainEl = root.querySelector(config.sliderSelector);
    if (!mainEl) return;

    resetImageTransforms(mainEl);

    var slides = mainEl.querySelectorAll(".swiper-slide");
    var slideCount = slides ? slides.length : 0;
    var enableLoop = slideCount > 1;
    var enableAutoplay = slideCount > 1;

    var navPrev = root.querySelector(".swiper-button-prev");
    var navNext = root.querySelector(".swiper-button-next");
    var paginationEl = root.querySelector(".swiper-pagination");

    var thumbsEl = config.thumbsSelector ? root.querySelector(config.thumbsSelector) : null;
    var thumbsSwiper = null;

    if (thumbsEl && thumbsEl.querySelectorAll(".swiper-slide").length > 0) {
      try {
        thumbsSwiper = new Swiper(thumbsEl, {
          slidesPerView: "auto",
          spaceBetween: 8,
          freeMode: true,
          watchSlidesProgress: true,
          watchSlidesVisibility: true,
          slideToClickedSlide: true,
          breakpoints: {
            0: { slidesPerView: "auto", spaceBetween: 6 },
            480: { slidesPerView: "auto", spaceBetween: 6 },
            768: { slidesPerView: "auto", spaceBetween: 8 },
            1024: { slidesPerView: "auto", spaceBetween: 8 },
          },
        });
      } catch (e) {
        thumbsSwiper = null;
      }
    }

    var gallerySwiper = null;

    try {
      gallerySwiper = new Swiper(mainEl, {
        observer: true,
        observeParents: true,
        resizeObserver: true,
        navigation:
          navPrev && navNext
            ? {
                prevEl: navPrev,
                nextEl: navNext,
              }
            : {},
        pagination: paginationEl
          ? {
              el: paginationEl,
              clickable: true,
            }
          : {},
        loop: enableLoop,
        autoplay: enableAutoplay
          ? {
              delay: 3500,
              disableOnInteraction: false,
            }
          : false,
        effect: "slide",
        speed: 500,
        thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,
        on: {
          init: function () {
            var self = this;
            if (self.params.autoplay && self.autoplay && typeof self.autoplay.start === "function") {
              self.autoplay.start();
            }
            setTimeout(function () {
              try {
                self.update();
                thumbsSwiper && thumbsSwiper.update();
              } catch (e) {}
            }, 60);
          },
          imagesReady: function () {
            try {
              this.update();
              if (this.params.autoplay && this.autoplay && this.autoplay.start) {
                this.autoplay.start();
              }
              thumbsSwiper && thumbsSwiper.update();
            } catch (e) {}
          },
        },
      });
    } catch (e) {
      gallerySwiper = null;
    }

    var instance = {
      root: root,
      config: config,
      mainEl: mainEl,
      thumbsEl: thumbsEl,
      gallerySwiper: gallerySwiper,
      thumbsSwiper: thumbsSwiper,
      lightbox: createLightbox(root),
    };

    instance.enforceThumbDimensions = function () {
      enforceThumbDimensions(instance);
    };

    instance.enforceThumbDimensions();
    if (thumbsSwiper) {
      thumbsSwiper.on("init resize update imagesReady setTranslate transitionEnd", instance.enforceThumbDimensions);
    }
    if (gallerySwiper) {
      gallerySwiper.on("imagesReady transitionEnd", instance.enforceThumbDimensions);
    }
    window.addEventListener("resize", instance.enforceThumbDimensions);

    if (!window.pfGalleryMain) window.pfGalleryMain = gallerySwiper;
    if (!window.pfGalleryThumbs) window.pfGalleryThumbs = thumbsSwiper;
    if (!window.pfLightbox) window.pfLightbox = instance.lightbox;

    root.dataset.pfGalleryInitialized = "true";
    instances.push(instance);
  }

  function bootstrapGalleries() {
    galleryConfigs.forEach(function (config) {
      var roots = document.querySelectorAll(config.rootSelector);
      if (!roots || roots.length === 0) return;
      roots.forEach(function (root) {
        setupGallery(root, config);
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootstrapGalleries);
  } else {
    bootstrapGalleries();
  }

  window.pfGalleryInstances = instances;
})();
