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

  var globalConfig = window.pfGalleryConfig || {};

  function getNumericConfig(key, fallback) {
    var value = globalConfig[key];
    if (typeof value === "number" && !isNaN(value)) {
      return value;
    }
    if (typeof value === "string" && value !== "") {
      var parsed = parseInt(value, 10);
      if (!isNaN(parsed)) {
        return parsed;
      }
    }
    return fallback;
  }

  function getBooleanConfig(key, fallback) {
    var value = globalConfig[key];
    if (typeof value === "boolean") {
      return value;
    }
    if (typeof value === "string") {
      var lowered = value.toLowerCase();
      if (lowered === "1" || lowered === "true") {
        return true;
      }
      if (lowered === "0" || lowered === "false") {
        return false;
      }
    }
    return fallback;
  }

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

  function createLightbox(root, config, options) {
    if (typeof SimpleLightbox !== "function") return null;
    var anchors = root.querySelectorAll("a.gallery-item");
    if (!anchors || anchors.length === 0) return null;

    var anchorArray = nodeListToArray(anchors);
    if (anchorArray.length === 0) return null;

    var groupId = root.getAttribute("data-gallery-group");
    if (!groupId && anchorArray[0]) {
      groupId = anchorArray[0].getAttribute("data-gallery-group");
    }
    if (!groupId) {
      groupId = "pf-gallery-" + Math.random().toString(36).slice(2, 10);
      root.setAttribute("data-gallery-group", groupId);
    }

    anchorArray.forEach(function (anchor, index) {
      if (!anchor.getAttribute("data-gallery-group")) {
        anchor.setAttribute("data-gallery-group", groupId);
      }
      anchor.setAttribute("data-slb-group", groupId);
      if (!anchor.getAttribute("data-gallery-index")) {
        anchor.setAttribute("data-gallery-index", String(index));
      }
    });

    var selector = 'a.gallery-item[data-gallery-group="' + groupId + '"]';

    var lightbox = null;
    var lightboxOptions = {
      captions: true,
      captionsData: "alt",
      captionDelay: 200,
      close: true,
      nav: true,
      loop: true,
      history: false,
      docClose: true,
      animationSpeed: getNumericConfig("lightboxAnimationSpeed", 300),
    };

    try {
      lightbox = new SimpleLightbox(selector, lightboxOptions);
    } catch (e) {
      try {
        lightbox = new SimpleLightbox(
          Object.assign(
            {
              elements: selector,
            },
            lightboxOptions
          )
        );
      } catch (err) {
        return null;
      }
    }

    if (!lightbox) return null;

    var autoplayEnabled = options && options.autoplay;
    var autoplayDelay = options && options.delay ? options.delay : 4800;
    var autoplayTimer = null;

    function stopAutoplay() {
      if (!autoplayTimer) return;
      window.clearInterval(autoplayTimer);
      autoplayTimer = null;
    }

    function restartAutoplay() {
      stopAutoplay();
      startAutoplay();
    }

    function startAutoplay() {
      if (!autoplayEnabled || autoplayTimer) return;
      autoplayTimer = window.setInterval(function () {
        try {
          lightbox.next();
        } catch (err) {
          stopAutoplay();
        }
      }, autoplayDelay);
    }

    if (autoplayEnabled) {
      lightbox.on("shown.simplelightbox", startAutoplay);
      lightbox.on("close.simplelightbox", stopAutoplay);
      lightbox.on("closed.simplelightbox", stopAutoplay);
      lightbox.on("next.simplelightbox", restartAutoplay);
      lightbox.on("prev.simplelightbox", restartAutoplay);
      lightbox.on("changed.simplelightbox", restartAutoplay);
    }

    lightbox.startAutoplay = startAutoplay;
    lightbox.stopAutoplay = stopAutoplay;

    return lightbox;
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
    var shouldLoop = enableLoop && getBooleanConfig("enableLoop", true);
    var shouldAutoplay = enableAutoplay && getBooleanConfig("enableAutoplay", true);
    var autoplayDelay = Math.max(getNumericConfig("autoplayDelay", 3500), 1800);
    var slideSpeed = Math.max(getNumericConfig("slideSpeed", 500), 240);

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
        loop: shouldLoop,
        autoplay: shouldAutoplay
          ? {
              delay: autoplayDelay,
              disableOnInteraction: false,
              pauseOnMouseEnter: true,
            }
          : false,
        effect: "slide",
        speed: slideSpeed,
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
      lightbox: createLightbox(root, config, {
        autoplay: getBooleanConfig("lightboxAutoplay", true) && slideCount > 1,
        delay: getNumericConfig("lightboxAutoplayDelay", 5200),
      }),
    };

    if (instance.lightbox && gallerySwiper && gallerySwiper.autoplay) {
      instance.lightbox.on("shown.simplelightbox", function () {
        try {
          gallerySwiper.autoplay.stop();
        } catch (err) {}
      });
      instance.lightbox.on("closed.simplelightbox", function () {
        try {
          gallerySwiper.autoplay.start();
        } catch (err) {}
      });
    }

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
