<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package PutraFiber
 * @version 1.5.1 - FIXED: unique gallery-fix handles + correct deps
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue Styles
 */
function putrafiber_enqueue_styles() {
    // Google Fonts
    wp_enqueue_style(
        'putrafiber-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Main Stylesheet
    wp_enqueue_style(
        'putrafiber-style',
        get_stylesheet_uri(),
        array(),
        PUTRAFIBER_VERSION
    );

    // Custom Styles
    wp_enqueue_style('putrafiber-header',      PUTRAFIBER_URI . '/assets/css/header.css',      array('putrafiber-style'), PUTRAFIBER_VERSION);
    wp_enqueue_style('putrafiber-footer',      PUTRAFIBER_URI . '/assets/css/footer.css',      array('putrafiber-style'), PUTRAFIBER_VERSION);
    wp_enqueue_style('putrafiber-components',  PUTRAFIBER_URI . '/assets/css/components.css',  array('putrafiber-style'), PUTRAFIBER_VERSION);
    wp_enqueue_style('putrafiber-animations',  PUTRAFIBER_URI . '/assets/css/animations.css',  array('putrafiber-style'), PUTRAFIBER_VERSION);

    // Responsive (biasakan terakhir untuk layer global)
    wp_enqueue_style('putrafiber-responsive',  PUTRAFIBER_URI . '/assets/css/responsive.css',  array('putrafiber-style'), PUTRAFIBER_VERSION);

    // ===== PRODUCT PAGES =====
    if (is_singular('product') || is_post_type_archive('product') || is_tax('product_category') || is_tax('product_tag')) {

        // Swiper + SimpleLightbox (CDN)
        wp_enqueue_style('swiper-css',          'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
        wp_enqueue_style('simplelightbox-css',  'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css', array(), '2.14.2');

        // Product CSS
        wp_enqueue_style('putrafiber-product-styles', PUTRAFIBER_URI . '/assets/css/product.css', array('putrafiber-style'), PUTRAFIBER_VERSION);

        // Product Gallery Fix CSS (HANDLE UNIK + depend ke product.css)
        wp_enqueue_style(
            'putrafiber-product-gallery-fix',
            PUTRAFIBER_URI . '/assets/css/product-gallery-fix.css',
            array('putrafiber-product-styles', 'swiper-css', 'simplelightbox-css'),
            PUTRAFIBER_VERSION
        );
    }

    // ===== PORTFOLIO PAGES =====
    if (is_singular('portfolio') || is_post_type_archive('portfolio') || is_tax('portfolio_category')) {

        // Swiper + SimpleLightbox (CDN)
        // (Handle sama gapapa, WP akan skip enqueue kedua kalinya)
        wp_enqueue_style('swiper-css',          'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
        wp_enqueue_style('simplelightbox-css',  'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css', array(), '2.14.2');

        // Portfolio CSS
        wp_enqueue_style('putrafiber-portfolio-styles', PUTRAFIBER_URI . '/assets/css/portfolio.css', array('putrafiber-style'), PUTRAFIBER_VERSION);

        // Portfolio Gallery Fix CSS (HANDLE UNIK + depend ke portfolio.css)
        wp_enqueue_style(
            'putrafiber-portfolio-gallery-fix',
            PUTRAFIBER_URI . '/assets/css/portfolio-gallery-fix.css',
            array('putrafiber-portfolio-styles', 'swiper-css', 'simplelightbox-css'),
            PUTRAFIBER_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'putrafiber_enqueue_styles');

/**
 * Enqueue Scripts
 */
function putrafiber_enqueue_scripts() {
    // jQuery (WordPress core)
    wp_enqueue_script('jquery');

    // Main JavaScript
    wp_enqueue_script('putrafiber-main-js', PUTRAFIBER_URI . '/assets/js/main.js', array('jquery'), PUTRAFIBER_VERSION, true);

    // Lazy Load
    wp_enqueue_script('putrafiber-lazyload', PUTRAFIBER_URI . '/assets/js/lazyload.js', array(), PUTRAFIBER_VERSION, true);

    // Animations
    wp_enqueue_script('putrafiber-animations', PUTRAFIBER_URI . '/assets/js/animations.js', array('jquery'), PUTRAFIBER_VERSION, true);

    // PWA Service Worker Registration
    wp_enqueue_script('putrafiber-pwa', PUTRAFIBER_URI . '/assets/js/pwa.js', array(), PUTRAFIBER_VERSION, true);

    // ===== PRODUCT & PORTFOLIO (gabungan) =====
    if (
        is_singular('product') || is_post_type_archive('product') || is_tax('product_category') || is_tax('product_tag') ||
        is_singular('portfolio') || is_post_type_archive('portfolio') || is_tax('portfolio_category')
    ) {
        // Swiper + SimpleLightbox (CDN)
        wp_enqueue_script('swiper-js',         'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.5', true);
        wp_enqueue_script('simplelightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.js', array('jquery'), '2.14.2', true);

        // Pastikan tidak ada double-enqueue dari tempat lain
        wp_dequeue_script('putrafiber-product-gallery');
        wp_deregister_script('putrafiber-product-gallery');

        // Single source of truth
        wp_enqueue_script(
            'putrafiber-product-gallery',
            PUTRAFIBER_URI . '/assets/js/product-gallery.js',
            array('jquery', 'swiper-js', 'simplelightbox-js'),
            PUTRAFIBER_VERSION,
            true
        );

        // Optional: kirim konfigurasi ke JS (boleh diabaikan jika gak dipakai)
        wp_localize_script('putrafiber-product-gallery', 'pfGalleryConfig', array(
            'autoplayDelay' => 4000,
            'slideSpeed'    => 600,
            'enableLoop'    => true,
            'enableAutoplay'=> true,
            'debug'         => defined('WP_DEBUG') && WP_DEBUG,
        ));
    }

    // Localize Script untuk main.js
    if (!function_exists('putrafiber_whatsapp_number')) {
        // Fallback kecil kalau helper belum ada
        function putrafiber_whatsapp_number() { return ''; }
    }
    wp_localize_script('putrafiber-main-js', 'putrafiber_vars', array(
        'ajax_url'        => admin_url('admin-ajax.php'),
        'nonce'           => wp_create_nonce('putrafiber_nonce'),
        'theme_url'       => PUTRAFIBER_URI,
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
    if (is_singular('product') || is_singular('portfolio')) {
        echo '<link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" as="style" />' . "\n";
        echo '<link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" as="script" />' . "\n";
    }
}
add_action('wp_head', 'putrafiber_preload_critical_resources', 1);
