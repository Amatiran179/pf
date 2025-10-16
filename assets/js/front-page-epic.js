(function ($) {
  'use strict';

  function initBubbles() {
    var main = document.querySelector('main.front-page');
    var layer = document.querySelector('.frontpage-water-bubbles');
    if (!main || !layer) {
      return;
    }

    var total = parseInt(main.getAttribute('data-water-intensity'), 10);
    if (!total || total < 3) {
      total = 6;
    }
    if (total > 32) {
      total = 32;
    }

    for (var i = 0; i < total; i++) {
      var bubble = document.createElement('span');
      var left = Math.random() * 100;
      var delay = Math.random() * 8;
      var duration = 8 + Math.random() * 6;
      var size = 40 + Math.random() * 60;

      bubble.style.left = left.toFixed(2) + '%';
      bubble.style.animationDelay = delay.toFixed(2) + 's';
      bubble.style.animationDuration = duration.toFixed(2) + 's';
      bubble.style.width = size.toFixed(0) + 'px';
      bubble.style.height = size.toFixed(0) + 'px';

      layer.appendChild(bubble);
    }
  }

  function initParallax() {
    var main = document.querySelector('main.front-page');
    var hero = document.querySelector('.hero-section[data-parallax-layer]');
    if (!main || !hero) {
      return;
    }

    var enabled = main.getAttribute('data-parallax') === '1';
    if (!enabled) {
      return;
    }

    var rafId = null;

    function updateParallax() {
      rafId = null;
      var rect = hero.getBoundingClientRect();
      var center = rect.top + rect.height / 2;
      var delta = center - window.innerHeight / 2;
      hero.style.setProperty('--pf-parallax-y', delta.toFixed(2));
    }

    function requestUpdate() {
      if (rafId === null) {
        rafId = window.requestAnimationFrame(updateParallax);
      }
    }

    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
    requestUpdate();
  }

  function initHeroParticles() {
    var particles = document.querySelectorAll('.hero-ornaments .hero-particle');
    if (!particles.length) {
      return;
    }

    particles.forEach(function (particle, index) {
      var delay = Math.random() * 6;
      var offsetX = (index % 2 === 0 ? 1 : -1) * (5 + Math.random() * 12);
      var offsetY = (Math.random() * 18) - 9;
      particle.style.animationDelay = delay.toFixed(2) + 's';
      particle.style.setProperty('--pf-drift-x', offsetX.toFixed(2) + 'px');
      particle.style.setProperty('--pf-drift-y', offsetY.toFixed(2) + 'px');
    });
  }

  function initSectionObserver() {
    var sections = document.querySelectorAll('.section--glass');
    if (!sections.length || !('IntersectionObserver' in window)) {
      sections.forEach(function (section) {
        section.classList.add('is-visible');
      });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.25
    });

    sections.forEach(function (section) {
      observer.observe(section);
    });
  }

  function initSectionTilt() {
    var sections = document.querySelectorAll('.section--glass');
    if (!sections.length) {
      return;
    }

    sections.forEach(function (section) {
      var background = section.querySelector('.section-background');
      if (!background) {
        return;
      }

      section.addEventListener('mousemove', function (event) {
        var rect = section.getBoundingClientRect();
        var relX = (event.clientX - rect.left) / rect.width;
        var relY = (event.clientY - rect.top) / rect.height;
        var tiltX = (relY - 0.5) * 6;
        var tiltY = (relX - 0.5) * -6;

        section.style.setProperty('--pf-tilt-x', tiltX.toFixed(2) + 'deg');
        section.style.setProperty('--pf-tilt-y', tiltY.toFixed(2) + 'deg');
      });

      section.addEventListener('mouseleave', function () {
        section.style.setProperty('--pf-tilt-x', '0deg');
        section.style.setProperty('--pf-tilt-y', '0deg');
      });
    });
  }

  function initSmoothScroll() {
    $(document).on('click', '.scroll-to', function (event) {
      var target = this.getAttribute('href');
      if (!target || target.charAt(0) !== '#') {
        return;
      }
      var el = document.querySelector(target);
      if (!el) {
        return;
      }
      event.preventDefault();
      $('html, body').animate({ scrollTop: $(el).offset().top - 80 }, 700);
    });
  }

  $(document).ready(function () {
    initBubbles();
    initParallax();
    initHeroParticles();
    initSectionObserver();
    initSectionTilt();
    initSmoothScroll();
  });
})(jQuery);
