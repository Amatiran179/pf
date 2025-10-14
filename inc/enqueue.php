<?php
/**
 * Enqueue Scripts and Styles
 * 
 * @package PutraFiber
 * @version 1.1.0 - FIXED VERSION with Inline Scripts
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue Styles
 */
function putrafiber_enqueue_styles() {
    // Google Fonts
    wp_enqueue_style('putrafiber-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap', array(), null);
    
    // Main Stylesheet
    wp_enqueue_style('putrafiber-style', get_stylesheet_uri(), array(), PUTRAFIBER_VERSION);
    
    // Custom Styles
    wp_enqueue_style('putrafiber-header', PUTRAFIBER_URI . '/assets/css/header.css', array(), PUTRAFIBER_VERSION);
    wp_enqueue_style('putrafiber-footer', PUTRAFIBER_URI . '/assets/css/footer.css', array(), PUTRAFIBER_VERSION);
    wp_enqueue_style('putrafiber-components', PUTRAFIBER_URI . '/assets/css/components.css', array(), PUTRAFIBER_VERSION);
    wp_enqueue_style('putrafiber-animations', PUTRAFIBER_URI . '/assets/css/animations.css', array(), PUTRAFIBER_VERSION);
    
    // Responsive
    wp_enqueue_style('putrafiber-responsive', PUTRAFIBER_URI . '/assets/css/responsive.css', array(), PUTRAFIBER_VERSION);
    
    // ========================================
    // Product CPT Styles (Conditional Load)
    // ========================================
    if (is_singular('product') || is_post_type_archive('product') || is_tax('product_category') || is_tax('product_tag')) {
        
        // Swiper Slider CSS (dari CDN)
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
        
        // SimpleLightbox CSS (dari CDN)
        wp_enqueue_style('simplelightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css', array(), '2.14.2');
        
        // Product Inline CSS (langsung inject, tidak perlu file terpisah)
        $product_css = "
            /* Product Gallery Container */
            .product-layout {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 40px;
                margin: 40px 0;
            }
            
            .product-gallery {
                position: sticky;
                top: 100px;
            }
            
            .gallery-container {
                width: 100%;
            }
            
            /* Swiper Slider */
            .product-gallery-slider {
                width: 100%;
                border-radius: 12px;
                overflow: hidden;
                background: #f5f5f5;
                margin-bottom: 15px;
            }
            
            .product-gallery-slider .swiper-slide {
                aspect-ratio: 1;
            }
            
            .gallery-item {
                display: block;
                position: relative;
                width: 100%;
                height: 100%;
                overflow: hidden;
            }
            
            .gallery-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }
            
            .gallery-item:hover .gallery-image {
                transform: scale(1.1);
            }
            
            /* Zoom Icon */
            .zoom-icon {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(0,0,0,0.6);
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }
            
            .gallery-item:hover .zoom-icon {
                opacity: 1;
            }
            
            /* Swiper Navigation */
            .swiper-button-next,
            .swiper-button-prev {
                color: white;
                background: rgba(0,0,0,0.5);
                width: 40px;
                height: 40px;
                border-radius: 50%;
            }
            
            .swiper-button-next:after,
            .swiper-button-prev:after {
                font-size: 18px;
            }
            
            /* Swiper Pagination */
            .swiper-pagination-bullet {
                background: white;
                opacity: 0.7;
            }
            
            .swiper-pagination-bullet-active {
                background: #00BCD4;
                opacity: 1;
            }
            
            /* Thumbnail Slider */
            .product-gallery-thumbs {
                margin-top: 15px;
            }
            
            .product-gallery-thumbs .swiper-slide {
                width: 80px;
                height: 80px;
                border-radius: 8px;
                overflow: hidden;
                opacity: 0.6;
                cursor: pointer;
                border: 2px solid transparent;
                transition: all 0.3s ease;
            }
            
            .product-gallery-thumbs .swiper-slide-thumb-active {
                opacity: 1;
                border-color: #00BCD4;
            }
            
            .product-gallery-thumbs img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            /* No Image Placeholder */
            .no-product-image {
                width: 100%;
                aspect-ratio: 1;
                background: #f0f0f0;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
            }
            
            /* Product Info */
            .product-info {
                padding: 20px 0;
            }
            
            .product-meta-header {
                display: flex;
                gap: 15px;
                margin-bottom: 15px;
                flex-wrap: wrap;
            }
            
            .product-category {
                background: #00BCD4;
                color: white;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
            }
            
            .product-category a {
                color: white;
                text-decoration: none;
            }
            
            .product-sku {
                background: #f5f5f5;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 13px;
                color: #666;
            }
            
            .product-title {
                font-size: 32px;
                font-weight: 800;
                margin: 0 0 20px 0;
                color: #222;
                line-height: 1.3;
            }
            
            .product-short-description {
                font-size: 16px;
                color: #555;
                margin-bottom: 20px;
                line-height: 1.7;
            }
            
            /* Stock Status */
            .product-stock {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                border-radius: 25px;
                font-weight: 600;
                font-size: 14px;
                margin-bottom: 25px;
            }
            
            .product-stock.in-stock {
                background: #d4edda;
                color: #155724;
            }
            
            .product-stock.pre-order {
                background: #fff3cd;
                color: #856404;
            }
            
            .product-stock.out-stock {
                background: #f8d7da;
                color: #721c24;
            }
            
            /* Price */
            .product-price {
                margin: 30px 0;
                padding: 25px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 12px;
                color: white;
            }
            
            .price-label {
                display: block;
                font-size: 14px;
                opacity: 0.9;
                margin-bottom: 8px;
            }
            
            .price-amount {
                font-size: 36px;
                font-weight: 800;
                display: block;
            }
            
            /* CTA WhatsApp */
            .product-cta-price {
                margin: 30px 0;
                text-align: center;
            }
            
            .btn-whatsapp-cta {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                background: #25D366;
                color: white;
                padding: 18px 40px;
                border-radius: 50px;
                font-size: 18px;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            }
            
            .btn-whatsapp-cta:hover {
                background: #20ba5a;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
                color: white;
                text-decoration: none;
            }
            
            .cta-note {
                margin-top: 10px;
                font-size: 13px;
                color: #666;
            }
            
            /* Product Attributes */
            .product-attributes {
                background: #f8f9fa;
                padding: 25px;
                border-radius: 12px;
                margin: 25px 0;
            }
            
            .product-attributes h3 {
                margin-top: 0;
                margin-bottom: 15px;
                font-size: 18px;
                color: #333;
            }
            
            .attribute-item {
                display: flex;
                padding: 12px 0;
                border-bottom: 1px solid #e0e0e0;
            }
            
            .attribute-item:last-child {
                border-bottom: none;
            }
            
            .attribute-item strong {
                min-width: 120px;
                color: #333;
            }
            
            .attribute-item span {
                color: #666;
            }
            
            /* Share Buttons */
            .product-share {
                margin: 30px 0;
                padding: 20px 0;
                border-top: 1px solid #eee;
                border-bottom: 1px solid #eee;
            }
            
            .product-share strong {
                display: block;
                margin-bottom: 12px;
                color: #333;
            }
            
            .share-buttons {
                display: flex;
                gap: 10px;
            }
            
            .share-btn {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                transition: all 0.3s ease;
            }
            
            .share-btn.facebook {
                background: #1877f2;
            }
            
            .share-btn.twitter {
                background: #1da1f2;
            }
            
            .share-btn.whatsapp {
                background: #25D366;
            }
            
            .share-btn:hover {
                transform: scale(1.1);
            }
            
            /* Download Catalog */
            .product-catalog {
                margin: 20px 0;
            }
            
            .btn-download-catalog {
                display: inline-block;
                background: #FF9800;
                color: white;
                padding: 14px 30px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .btn-download-catalog:hover {
                background: #F57C00;
                transform: translateY(-2px);
                color: white;
                text-decoration: none;
            }
            
            /* Product Description Section */
            .product-description-section {
                padding: 60px 0;
                background: #f8f9fa;
            }
            
            .product-content {
                max-width: 900px;
                margin: 0 auto;
                font-size: 16px;
                line-height: 1.8;
                color: #444;
            }
            
            /* Specs Section */
            .product-specs-section {
                padding: 60px 0;
            }
            
            .specs-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 40px;
                margin-top: 30px;
            }
            
            .specs-box,
            .features-box {
                background: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            }
            
            .specs-box h3,
            .features-box h3 {
                font-size: 22px;
                margin-bottom: 20px;
                color: #333;
            }
            
            .specs-box ul,
            .features-box ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .specs-box li,
            .features-box li {
                padding: 12px 0;
                border-bottom: 1px solid #eee;
                color: #555;
                position: relative;
                padding-left: 25px;
            }
            
            .specs-box li:before {
                content: '✓';
                position: absolute;
                left: 0;
                color: #00BCD4;
                font-weight: bold;
            }
            
            .features-box li:before {
                content: '⭐';
                position: absolute;
                left: 0;
            }
            
            .specs-box li:last-child,
            .features-box li:last-child {
                border-bottom: none;
            }
            
            /* Section Header */
            .section-header {
                text-align: center;
                margin-bottom: 40px;
            }
            
            .section-header h2 {
                font-size: 32px;
                font-weight: 800;
                margin-bottom: 10px;
                color: #222;
            }
            
            .section-header p {
                font-size: 16px;
                color: #666;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .product-layout {
                    grid-template-columns: 1fr;
                    gap: 20px;
                }
                
                .product-gallery {
                    position: relative;
                    top: 0;
                }
                
                .product-title {
                    font-size: 24px;
                }
                
                .price-amount {
                    font-size: 28px;
                }
                
                .btn-whatsapp-cta {
                    padding: 14px 30px;
                    font-size: 16px;
                }
            }
        ";
        
        wp_add_inline_style('putrafiber-style', $product_css);
    }
}
add_action('wp_enqueue_scripts', 'putrafiber_enqueue_styles');

/**
 * Enqueue Scripts
 */
function putrafiber_enqueue_scripts() {
    // jQuery (WordPress default)
    wp_enqueue_script('jquery');
    
    // Main JavaScript
    wp_enqueue_script('putrafiber-main-js', PUTRAFIBER_URI . '/assets/js/main.js', array('jquery'), PUTRAFIBER_VERSION, true);
    
    // Lazy Load
    wp_enqueue_script('putrafiber-lazyload', PUTRAFIBER_URI . '/assets/js/lazyload.js', array(), PUTRAFIBER_VERSION, true);
    
    // Animations
    wp_enqueue_script('putrafiber-animations', PUTRAFIBER_URI . '/assets/js/animations.js', array('jquery'), PUTRAFIBER_VERSION, true);
    
    // PWA Service Worker Registration
    wp_enqueue_script('putrafiber-pwa', PUTRAFIBER_URI . '/assets/js/pwa.js', array(), PUTRAFIBER_VERSION, true);
    
    // ========================================
    // Product CPT Scripts (Conditional Load)
    // ========================================
    if (is_singular('product') || is_post_type_archive('product') || is_tax('product_category') || is_tax('product_tag')) {
        
        // Swiper JS (dari CDN)
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.5', true);
        
        // SimpleLightbox JS (dari CDN)
        wp_enqueue_script('simplelightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.js', array('jquery'), '2.14.2', true);
        
        // Product Gallery Inline Script (langsung inject, tidak perlu file terpisah)
        $product_js = "
            jQuery(document).ready(function($) {
                console.log('🚀 Product Gallery Script Loading...');
                
                // Counter untuk retry
                var swiperAttempts = 0;
                var maxAttempts = 20;
                
                // Function untuk init Swiper
                function initProductSwiper() {
                    if (typeof Swiper !== 'undefined') {
                        console.log('✓ Swiper library found!');
                        
                        // Check if slider elements exist
                        if ($('.product-gallery-slider').length === 0) {
                            console.warn('⚠ No product gallery slider found in DOM');
                            return;
                        }
                        
                        try {
                            // Initialize Thumbnail Slider first
                            var galleryThumbs = new Swiper('.product-gallery-thumbs', {
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
                            
                            // Initialize Main Slider
                            var galleryMain = new Swiper('.product-gallery-slider', {
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
                                    delay: 3000,
                                    disableOnInteraction: false,
                                },
                                loop: true,
                                keyboard: {
                                    enabled: true,
                                },
                                on: {
                                    init: function() {
                                        console.log('✓ Swiper initialized successfully!');
                                    },
                                    slideChange: function() {
                                        // Update thumbnail active state
                                        console.log('Slide changed to: ' + this.activeIndex);
                                    }
                                }
                            });
                            
                            return true;
                            
                        } catch (error) {
                            console.error('✗ Swiper initialization error:', error);
                            return false;
                        }
                        
                    } else {
                        swiperAttempts++;
                        
                        if (swiperAttempts < maxAttempts) {
                            console.log('⏳ Waiting for Swiper... (attempt ' + swiperAttempts + '/' + maxAttempts + ')');
                            setTimeout(initProductSwiper, 200);
                        } else {
                            console.error('✗ Swiper library failed to load after ' + maxAttempts + ' attempts');
                        }
                        
                        return false;
                    }
                }
                
                // Start init Swiper
                initProductSwiper();
                
                // Initialize SimpleLightbox
                setTimeout(function() {
                    if (typeof SimpleLightbox !== 'undefined') {
                        try {
                            var lightbox = new SimpleLightbox('[data-lightbox]', {
                                captions: true,
                                captionsData: 'title',
                                captionPosition: 'bottom',
                                fadeSpeed: 300,
                                animationSpeed: 300
                            });
                            console.log('✓ SimpleLightbox initialized successfully!');
                        } catch (error) {
                            console.error('✗ SimpleLightbox error:', error);
                        }
                    } else {
                        console.warn('⚠ SimpleLightbox not loaded, using fallback');
                        
                        // Fallback: Native lightbox
                        $('[data-lightbox]').on('click', function(e) {
                            e.preventDefault();
                            var imgSrc = $(this).attr('href');
                            var title = $(this).data('title') || '';
                            
                            var lightboxHTML = 
                                '<div class=\"pf-native-lightbox\" style=\"position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);z-index:99999;display:flex;align-items:center;justify-content:center;flex-direction:column;animation:fadeIn 0.3s;\">' +
                                '<span style=\"position:absolute;top:20px;right:40px;color:white;font-size:40px;cursor:pointer;transition:0.3s;\" class=\"pf-close-lightbox\" onclick=\"this.parentElement.remove()\">&times;</span>' +
                                '<img src=\"' + imgSrc + '\" style=\"max-width:90%;max-height:80vh;object-fit:contain;animation:zoomIn 0.3s;\">' +
                                '<p style=\"color:white;margin-top:20px;font-size:16px;\">' + title + '</p>' +
                                '</div>';
                            
                            $('body').append(lightboxHTML);
                            
                            // Close on click background
                            $('.pf-native-lightbox').on('click', function(e) {
                                if (e.target === this) {
                                    $(this).remove();
                                }
                            });
                            
                            // Close on ESC key
                            $(document).on('keydown.lightbox', function(e) {
                                if (e.key === 'Escape') {
                                    $('.pf-native-lightbox').remove();
                                    $(document).off('keydown.lightbox');
                                }
                            });
                        });
                        
                        console.log('✓ Native lightbox fallback ready');
                    }
                }, 500);
                
                console.log('✓ Product Gallery Script Loaded');
            });
        ";
        
        wp_add_inline_script('jquery', $product_js);
    }
    
    // Localize Script
    wp_localize_script('putrafiber-main-js', 'putrafiber_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('putrafiber_nonce'),
        'theme_url' => PUTRAFIBER_URI,
        'whatsapp_number' => putrafiber_whatsapp_number(),
    ));
    
    // Comment Reply
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'putrafiber_enqueue_scripts');

/**
 * Admin Styles
 */
function putrafiber_admin_styles() {
    wp_enqueue_style('putrafiber-admin', PUTRAFIBER_URI . '/assets/css/admin.css', array(), PUTRAFIBER_VERSION);
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'putrafiber_admin_styles');

/**
 * Admin Scripts
 */
function putrafiber_admin_scripts() {
    wp_enqueue_script('putrafiber-admin-js', PUTRAFIBER_URI . '/assets/js/admin.js', array('jquery'), PUTRAFIBER_VERSION, true);
}
add_action('admin_enqueue_scripts', 'putrafiber_admin_scripts');

/**
 * Preload Critical Resources
 */
function putrafiber_preload_critical_resources() {
    // Preload Swiper on product pages
    if (is_singular('product')) {
        echo '<link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" as="style">' . "\n";
        echo '<link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" as="script">' . "\n";
    }
}
add_action('wp_head', 'putrafiber_preload_critical_resources', 1);
