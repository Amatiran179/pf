(function($) {
    'use strict';

    // Document Ready
    $(document).ready(function() {
        
        // Mobile Menu Toggle
        $('.menu-toggle').on('click', function() {
            var isExpanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !isExpanded);
            $('.main-navigation').toggleClass('active');
            $('body').toggleClass('menu-open');
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.site-header').length) {
                $('.menu-toggle').attr('aria-expanded', 'false');
                $('.main-navigation').removeClass('active');
                $('body').removeClass('menu-open');
            }
        });

        // Submenu toggle for mobile
        $('.menu-item-has-children > a').on('click', function(e) {
            if ($(window).width() < 992) {
                e.preventDefault();
                $(this).parent().toggleClass('active');
            }
        });

        // Dark Mode Toggle
        const darkModeToggle = $('.dark-mode-toggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        $('html').attr('data-theme', currentTheme);
        
        darkModeToggle.on('click', function() {
            const theme = $('html').attr('data-theme') === 'light' ? 'dark' : 'light';
            $('html').attr('data-theme', theme);
            localStorage.setItem('theme', theme);
        });

        // Smooth Scroll
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                    return false;
                }
            }
        });

        // Back to Top Button
        const backToTop = $('#back-to-top');
        
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTop.addClass('show');
            } else {
                backToTop.removeClass('show');
            }

            // Sticky Header
            if ($(this).scrollTop() > 100) {
                $('.site-header').addClass('scrolled');
            } else {
                $('.site-header').removeClass('scrolled');
            }
        });

        backToTop.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
            return false;
        });

        // Fade In Animation on Scroll
        function checkFadeIn() {
            $('.fade-in').each(function() {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();

                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('visible');
                }
            });
        }

        $(window).on('scroll resize', checkFadeIn);
        checkFadeIn();

        // Tab Navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var tabId = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').removeClass('active');
            $(tabId).addClass('active');
        });

        // Form Validation
        $('form').on('submit', function() {
            var isValid = true;
            
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            return isValid;
        });

        // Lazy Load Images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        if (image.dataset.src) {
                            image.src = image.dataset.src;
                            image.classList.add('loaded');
                            imageObserver.unobserve(image);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }

        // Lightbox for Gallery
        if (typeof $.fn.magnificPopup !== 'undefined') {
            $('.gallery-item').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                },
                image: {
                    titleSrc: 'title'
                }
            });
        }

        // Share Buttons
        $('.share-buttons a').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            window.open(url, 'share', 'width=600,height=400');
        });

        // Search Form Toggle
        $('.search-toggle').on('click', function() {
            $('.search-form').toggleClass('active');
            $('.search-field').focus();
        });

        // Close search on ESC
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape') {
                $('.search-form').removeClass('active');
            }
        });

        // Auto-hide messages
        setTimeout(function() {
            $('.notice, .alert').fadeOut();
        }, 5000);

        // Prevent empty links
        $('a[href="#"]').on('click', function(e) {
            e.preventDefault();
        });

        // External Links
        $('a[href^="http"]').not('[href*="' + window.location.hostname + '"]').attr({
            target: '_blank',
            rel: 'noopener noreferrer'
        });

        // Copy to Clipboard
        $('.copy-to-clipboard').on('click', function() {
            var text = $(this).data('text');
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            });
        });

        // Print Page
        $('.print-page').on('click', function() {
            window.print();
        });

        // Counter Animation
        $('.counter').each(function() {
            var $this = $(this);
            var countTo = $this.attr('data-count');
            
            $({ countNum: $this.text() }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'linear',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });

        // Parallax Effect
        $(window).on('scroll', function() {
            var scrolled = $(window).scrollTop();
            $('.parallax').css('background-position', 'center ' + (scrolled * 0.5) + 'px');
        });

        // Video Lazy Load
        $('.video-wrapper iframe').each(function() {
            var src = $(this).data('src');
            if (src) {
                $(this).attr('src', src);
            }
        });

        // Read More Toggle
        $('.read-more-toggle').on('click', function() {
            $(this).prev('.content-excerpt').toggleClass('expanded');
            var text = $(this).text() === 'Read More' ? 'Read Less' : 'Read More';
            $(this).text(text);
        });

        // Sticky Sidebar
        if ($(window).width() > 992) {
            var sidebar = $('.sidebar');
            var sidebarTop = sidebar.offset().top;
            
            $(window).on('scroll', function() {
                var scrollTop = $(window).scrollTop();
                
                if (scrollTop > sidebarTop) {
                    sidebar.css({
                        position: 'fixed',
                        top: '100px'
                    });
                } else {
                    sidebar.css({
                        position: 'static'
                    });
                }
            });
        }

        // Accordion
        $('.accordion-header').on('click', function() {
            $(this).toggleClass('active');
            $(this).next('.accordion-content').slideToggle(300);
        });

        // Toast Notifications
        function showToast(message, type) {
            var toast = $('<div class="toast toast-' + type + '">' + message + '</div>');
            $('body').append(toast);
            
            setTimeout(function() {
                toast.addClass('show');
            }, 100);
            
            setTimeout(function() {
                toast.removeClass('show');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Form Submit via AJAX
        $('.ajax-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = new FormData(this);
            
            $.ajax({
                url: putrafiber_vars.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    form.find('.btn').prop('disabled', true).text('Sending...');
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Form submitted successfully!', 'success');
                        form[0].reset();
                    } else {
                        showToast('Something went wrong. Please try again.', 'error');
                    }
                },
                error: function() {
                    showToast('Connection error. Please try again.', 'error');
                },
                complete: function() {
                    form.find('.btn').prop('disabled', false).text('Submit');
                }
            });
        });

    });

    // Window Load
    $(window).on('load', function() {
        // Remove preloader if exists
        $('.preloader').fadeOut();
        
        // Trigger animations
        checkFadeIn();
    });

})(jQuery);
