(function() {
    'use strict';

    // Intersection Observer for Scroll Animations
    const animateOnScroll = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible', 'animated');
                
                // Unobserve after animation
                if (!entry.target.classList.contains('animate-repeat')) {
                    animateOnScroll.unobserve(entry.target);
                }
            } else {
                if (entry.target.classList.contains('animate-repeat')) {
                    entry.target.classList.remove('visible', 'animated');
                }
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all elements with fade-in class
    document.querySelectorAll('.fade-in, .scroll-animate').forEach(function(el) {
        animateOnScroll.observe(el);
    });

    // Parallax Effect
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax');
        
        parallaxElements.forEach(function(el) {
            const speed = el.dataset.speed || 0.5;
            el.style.transform = 'translateY(' + (scrolled * speed) + 'px)';
        });
    });

    // Counter Animation
    function animateCounter(element) {
        const target = parseInt(element.dataset.count);
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    const counterObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    });

    document.querySelectorAll('.counter').forEach(function(counter) {
        counterObserver.observe(counter);
    });

    // Stagger Animation
    document.querySelectorAll('.stagger-animation').forEach(function(container) {
        const children = container.children;
        Array.from(children).forEach(function(child, index) {
            child.style.animationDelay = (index * 0.1) + 's';
        });
    });

    // Hover Tilt Effect
    document.querySelectorAll('.tilt-effect').forEach(function(el) {
        el.addEventListener('mousemove', function(e) {
            const rect = el.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            el.style.transform = 'perspective(1000px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
        });
        
        el.addEventListener('mouseleave', function() {
            el.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
        });
    });

    // Typewriter Effect
    function typeWriter(element, text, speed) {
        let i = 0;
        element.textContent = '';
        
        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        
        type();
    }

    document.querySelectorAll('.typewriter').forEach(function(el) {
        const text = el.dataset.text || el.textContent;
        const speed = el.dataset.speed || 100;
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    typeWriter(el, text, speed);
                    observer.unobserve(el);
                }
            });
        });
        
        observer.observe(el);
    });

    // Progress Bar Animation
    const progressObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.dataset.width || '100%';
                bar.style.width = width;
                progressObserver.unobserve(bar);
            }
        });
    });

    document.querySelectorAll('.progress-bar').forEach(function(bar) {
        progressObserver.observe(bar);
    });

})();
