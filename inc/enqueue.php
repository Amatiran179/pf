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
        pf_asset_version('style.css')
    );

    // Custom Styles
    wp_enqueue_style('putrafiber-header',      PUTRAFIBER_URI . '/assets/css/header.css',      array('putrafiber-style'), pf_asset_version('assets/css/header.css'));
    wp_enqueue_style('putrafiber-footer',      PUTRAFIBER_URI . '/assets/css/footer.css',      array('putrafiber-style'), pf_asset_version('assets/css/footer.css'));
    wp_enqueue_style('putrafiber-components',  PUTRAFIBER_URI . '/assets/css/components.css',  array('putrafiber-style'), pf_asset_version('assets/css/components.css'));
    wp_enqueue_style('putrafiber-animations',  PUTRAFIBER_URI . '/assets/css/animations.css',  array('putrafiber-style'), pf_asset_version('assets/css/animations.css'));

    if (is_front_page()) {
        wp_enqueue_style(
            'putrafiber-front-epic',
            PUTRAFIBER_URI . '/assets/css/front-page-epic.css',
            array('putrafiber-components', 'putrafiber-animations'),
            pf_asset_version('assets/css/front-page-epic.css')
        );
    }

    // Responsive (biasakan terakhir untuk layer global)
    wp_enqueue_style('putrafiber-responsive',  PUTRAFIBER_URI . '/assets/css/responsive.css',  array('putrafiber-style'), pf_asset_version('assets/css/responsive.css'));

    // ===== PRODUCT PAGES =====
    if (is_singular('product') || is_post_type_archive('product') || is_tax('product_category') || is_tax('product_tag')) {

        // Swiper + SimpleLightbox (CDN)
        wp_enqueue_style('swiper-css',          'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
        wp_enqueue_style('simplelightbox-css',  'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css', array(), '2.14.2');

        // Product CSS
        wp_enqueue_style('putrafiber-product-styles', PUTRAFIBER_URI . '/assets/css/product.css', array('putrafiber-style'), pf_asset_version('assets/css/product.css'));

        // Product Gallery Fix CSS (HANDLE UNIK + depend ke product.css)
        wp_enqueue_style(
            'putrafiber-product-gallery-fix',
            PUTRAFIBER_URI . '/assets/css/product-gallery-fix.css',
            array('putrafiber-product-styles', 'swiper-css', 'simplelightbox-css'),
            pf_asset_version('assets/css/product-gallery-fix.css')
        );
    }

    // ===== PORTFOLIO PAGES =====
    if (is_singular('portfolio') || is_post_type_archive('portfolio') || is_tax('portfolio_category')) {

        // Swiper + SimpleLightbox (CDN)
        // (Handle sama gapapa, WP akan skip enqueue kedua kalinya)
        wp_enqueue_style('swiper-css',          'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
        wp_enqueue_style('simplelightbox-css',  'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css', array(), '2.14.2');

        // Portfolio CSS
        wp_enqueue_style('putrafiber-portfolio-styles', PUTRAFIBER_URI . '/assets/css/portfolio.css', array('putrafiber-style'), pf_asset_version('assets/css/portfolio.css'));

        // Portfolio Gallery Fix CSS (HANDLE UNIK + depend ke portfolio.css)
        wp_enqueue_style(
            'putrafiber-portfolio-gallery-fix',
            PUTRAFIBER_URI . '/assets/css/portfolio-gallery-fix.css',
            array('putrafiber-portfolio-styles', 'swiper-css', 'simplelightbox-css'),
            pf_asset_version('assets/css/portfolio-gallery-fix.css')
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
    wp_enqueue_script('putrafiber-main-js', PUTRAFIBER_URI . '/assets/js/main.js', array('jquery'), pf_asset_version('assets/js/main.js'), true);

    wp_enqueue_script('putrafiber-search', PUTRAFIBER_URI . '/assets/js/search.js', array(), pf_asset_version('assets/js/search.js'), true);

    // Lazy Load
    wp_enqueue_script('putrafiber-lazyload', PUTRAFIBER_URI . '/assets/js/lazyload.js', array(), pf_asset_version('assets/js/lazyload.js'), true);

    // Animations
    wp_enqueue_script('putrafiber-animations', PUTRAFIBER_URI . '/assets/js/animations.js', array('jquery'), pf_asset_version('assets/js/animations.js'), true);

    // PWA Service Worker Registration (respect toggle)
    $pwa_enabled = true;
    if (function_exists('putrafiber_is_pwa_enabled')) {
        $pwa_enabled = putrafiber_is_pwa_enabled();
    } elseif (function_exists('putrafiber_get_option')) {
        $raw_pwa_value = putrafiber_get_option('enable_pwa', '1');
        $normalized    = strtolower(trim((string) $raw_pwa_value));
        $pwa_enabled   = !in_array($normalized, array('0', 'false', 'no', 'off'), true);
    }

    if ($pwa_enabled) {
        wp_enqueue_script('putrafiber-pwa', PUTRAFIBER_URI . '/assets/js/pwa.js', array(), pf_asset_version('assets/js/pwa.js'), true);
    }

    if (is_front_page()) {
        wp_enqueue_script('putrafiber-front-epic', PUTRAFIBER_URI . '/assets/js/front-page-epic.js', array('jquery'), pf_asset_version('assets/js/front-page-epic.js'), true);
    }

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
            pf_asset_version('assets/js/product-gallery.js'),
            true
        );

        // Optional: kirim konfigurasi ke JS (boleh diabaikan jika gak dipakai)
        wp_localize_script('putrafiber-product-gallery', 'pfGalleryConfig', array(
            'autoplayDelay' => 4000,
            'slideSpeed'    => 600,
            'enableLoop'    => true,
            'enableAutoplay'=> true,
            'lightboxAutoplay' => true,
            'lightboxAutoplayDelay' => 5200,
            'lightboxAnimationSpeed' => 280,
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
        'analytics_nonce' => wp_create_nonce('putrafiber_analytics'),
        'theme_url'       => PUTRAFIBER_URI,
        'whatsapp_number' => putrafiber_whatsapp_number(),
        'copied_text'     => esc_html__('Copied to clipboard!', 'putrafiber'),
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
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('putrafiber-admin', PUTRAFIBER_URI . '/assets/css/admin.css', array(), pf_asset_version('assets/css/admin.css'));
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'putrafiber_admin_styles');

/**
 * Admin Scripts
 */
function putrafiber_admin_scripts() {
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('putrafiber-admin-js', PUTRAFIBER_URI . '/assets/js/admin.js', array('jquery'), pf_asset_version('assets/js/admin.js'), true);
    wp_localize_script('putrafiber-admin-js', 'putrafiberAdminVars', array(
        'ajax_url'               => admin_url('admin-ajax.php'),
        'analyticsResetError'    => esc_html__('Terjadi kesalahan saat menghapus data analytics.', 'putrafiber'),
        'analyticsResetSuccess'  => esc_html__('Data analytics berhasil dihapus.', 'putrafiber'),
    ));
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
