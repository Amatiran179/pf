/**
 * Product Gallery with Swiper & Lightbox
 * 
 * @package PutraFiber
 */

(function($) {
    'use strict';
    
    // Init on DOM Ready
    $(document).ready(function() {
        initProductGallery();
        initLightbox();
    });
    
    /**
     * Initialize Product Gallery Slider
     */
    function initProductGallery() {
        if (typeof Swiper === 'undefined') {
            console.warn('Swiper not loaded');
            return;
        }
        
        // Thumbnail Slider
        const galleryThumbs = new Swiper('.product-gallery-thumbs', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
                320: {
                    slidesPerView: 3
                },
                768: {
                    slidesPerView: 4
                }
            }
        });
        
        // Main Slider
        const galleryMain = new Swiper('.product-gallery-slider', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            thumbs: {
                swiper: galleryThumbs
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            loop: true,
            keyboard: {
                enabled: true,
            }
        });
    }
    
    /**
     * Initialize Lightbox
     * Using SimpleLightbox or native implementation
     */
    function initLightbox() {
        // Check if SimpleLightbox is loaded
        if (typeof SimpleLightbox !== 'undefined') {
            new SimpleLightbox('[data-lightbox]', {
                captions: true,
                captionsData: 'title',
                captionPosition: 'bottom',
                fadeSpeed: 300,
                animationSpeed: 300
            });
        } else {
            // Fallback: Native Lightbox Implementation
            createNativeLightbox();
        }
    }
    
    /**
     * Native Lightbox Implementation
     */
    function createNativeLightbox() {
        const $body = $('body');
        
        // Create lightbox HTML
        if ($('#pf-lightbox').length === 0) {
            $body.append(`
                <div id="pf-lightbox" class="pf-lightbox">
                    <span class="pf-lightbox-close">&times;</span>
                    <img class="pf-lightbox-content" src="" alt="">
                    <div class="pf-lightbox-caption"></div>
                    <div class="pf-lightbox-nav">
                        <button class="pf-lightbox-prev">&#10094;</button>
                        <button class="pf-lightbox-next">&#10095;</button>
                    </div>
                </div>
            `);
            
            // Add CSS
            $('head').append(`
                <style>
                .pf-lightbox {
                    display: none;
                    position: fixed;
                    z-index: 99999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.95);
                    animation: fadeIn 0.3s;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                .pf-lightbox.active {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-direction: column;
                }
                
                .pf-lightbox-close {
                    position: absolute;
                    top: 20px;
                    right: 40px;
                    color: white;
                    font-size: 40px;
                    font-weight: bold;
                    cursor: pointer;
                    z-index: 100000;
                    transition: 0.3s;
                }
                
                .pf-lightbox-close:hover {
                    color: #ddd;
                    transform: rotate(90deg);
                }
                
                .pf-lightbox-content {
                    max-width: 90%;
                    max-height: 80vh;
                    object-fit: contain;
                    animation: zoomIn 0.3s;
                }
                
                @keyframes zoomIn {
                    from { transform: scale(0.8); }
                    to { transform: scale(1); }
                }
                
                .pf-lightbox-caption {
                    color: white;
                    text-align: center;
                    padding: 20px;
                    font-size: 16px;
                }
                
                .pf-lightbox-nav button {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    background: rgba(255,255,255,0.1);
                    color: white;
                    border: none;
                    font-size: 30px;
                    padding: 15px 20px;
                    cursor: pointer;
                    transition: 0.3s;
                }
                
                .pf-lightbox-nav button:hover {
                    background: rgba(255,255,255,0.3);
                }
                
                .pf-lightbox-prev {
                    left: 20px;
                }
                
                .pf-lightbox-next {
                    right: 20px;
                }
                </style>
            `);
        }
        
        const $lightbox = $('#pf-lightbox');
        const $lightboxImg = $lightbox.find('.pf-lightbox-content');
        const $lightboxCaption = $lightbox.find('.pf-lightbox-caption');
        let currentIndex = 0;
        let galleryImages = [];
        
        // Open lightbox on image click
        $(document).on('click', '[data-lightbox]', function(e) {
            e.preventDefault();
            
            const galleryGroup = $(this).data('lightbox');
            galleryImages = $(`[data-lightbox="${galleryGroup}"]`).toArray();
            currentIndex = galleryImages.indexOf(this);
            
            showImage(currentIndex);
            $lightbox.addClass('active');
        });
        
        // Close lightbox
        $lightbox.find('.pf-lightbox-close').on('click', function() {
            $lightbox.removeClass('active');
        });
        
        // Close on background click
        $lightbox.on('click', function(e) {
            if (e.target === this) {
                $(this).removeClass('active');
            }
        });
        
        // Navigation
        $lightbox.find('.pf-lightbox-prev').on('click', function() {
            currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
            showImage(currentIndex);
        });
        
        $lightbox.find('.pf-lightbox-next').on('click', function() {
            currentIndex = (currentIndex + 1) % galleryImages.length;
            showImage(currentIndex);
        });
        
        // Keyboard navigation
        $(document).on('keydown', function(e) {
            if (!$lightbox.hasClass('active')) return;
            
            if (e.key === 'Escape') {
                $lightbox.removeClass('active');
            } else if (e.key === 'ArrowLeft') {
                $lightbox.find('.pf-lightbox-prev').click();
            } else if (e.key === 'ArrowRight') {
                $lightbox.find('.pf-lightbox-next').click();
            }
        });
        
        // Show image function
        function showImage(index) {
            const $img = $(galleryImages[index]);
            const src = $img.attr('href');
            const title = $img.data('title') || '';
            
            $lightboxImg.attr('src', src);
            $lightboxCaption.text(title);
        }
    }
    
})(jQuery);
