<?php
/**
 * PutraFiber Enterprise Theme Functions
 * 
 * @package PutraFiber
 * @since 2.0.0
 */

if (!defined('ABSPATH')) exit;

define('PUTRAFIBER_VERSION', '1.0.0');
define('PUTRAFIBER_DIR', get_template_directory());
define('PUTRAFIBER_URI', get_template_directory_uri());

// Load required files
require_once PUTRAFIBER_DIR . '/inc/theme-setup.php';
require_once PUTRAFIBER_DIR . '/inc/enqueue.php';
require_once PUTRAFIBER_DIR . '/inc/customizer.php';
require_once PUTRAFIBER_DIR . '/inc/post-types/portfolio.php';
require_once PUTRAFIBER_DIR . '/inc/admin/theme-options.php';
require_once PUTRAFIBER_DIR . '/inc/schema/schema-generator.php';
require_once PUTRAFIBER_DIR . '/inc/seo-functions.php';
require_once PUTRAFIBER_DIR . '/inc/performance.php';
require_once PUTRAFIBER_DIR . '/inc/pwa.php';
require_once PUTRAFIBER_DIR . '/inc/webp-converter.php';
require_once PUTRAFIBER_DIR . '/inc/sitemap-generator.php';
require_once PUTRAFIBER_DIR . '/inc/post-types/product.php';

/**
 * Theme Setup
 */
function putrafiber_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'putrafiber'),
        'footer'  => __('Footer Menu', 'putrafiber'),
    ));
    
    add_image_size('putrafiber-hero', 1920, 1080, true);
    add_image_size('putrafiber-portfolio', 800, 600, true);
    add_image_size('putrafiber-product', 600, 600, true);
    add_image_size('putrafiber-thumb', 400, 300, true);
}
add_action('after_setup_theme', 'putrafiber_setup');

/**
 * Register Widget Areas
 */
function putrafiber_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'putrafiber'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here.', 'putrafiber'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Column 1', 'putrafiber'),
        'id'            => 'footer-1',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Column 2', 'putrafiber'),
        'id'            => 'footer-2',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Column 3', 'putrafiber'),
        'id'            => 'footer-3',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'putrafiber_widgets_init');

/**
 * Get Theme Option
 */
function putrafiber_get_option($key, $default = '') {
    $options = get_option('putrafiber_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * Get WhatsApp Number
 */
function putrafiber_whatsapp_number() {
    $number = putrafiber_get_option('whatsapp_number', '085642318455');
    return preg_replace('/[^0-9]/', '', $number);
}

/**
 * Generate WhatsApp Link
 */
function putrafiber_whatsapp_link($message = '') {
    $number = putrafiber_whatsapp_number();
    $message = $message ? urlencode($message) : urlencode('Halo, saya tertarik dengan produk PutraFiber');
    return "https://wa.me/{$number}?text={$message}";
}

/**
 * Get Breadcrumbs
 */
function putrafiber_breadcrumbs() {
    if (is_front_page()) return;
    
    $items = array();
    $items[] = array('url' => home_url('/'), 'title' => 'Home');
    
    if (is_category() || is_single()) {
        $category = get_the_category();
        if ($category) {
            $items[] = array('url' => get_category_link($category[0]->term_id), 'title' => $category[0]->name);
        }
    } elseif (is_page()) {
        $items[] = array('url' => '', 'title' => get_the_title());
    } elseif (is_post_type_archive('portfolio')) {
        $items[] = array('url' => '', 'title' => 'Portofolio');
    }
    
    if (is_single()) {
        $items[] = array('url' => '', 'title' => get_the_title());
    }
    
    echo '<div class="breadcrumbs">';
    foreach ($items as $index => $item) {
        if ($item['url']) {
            echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['title']) . '</a>';
        } else {
            echo '<span>' . esc_html($item['title']) . '</span>';
        }
        if ($index < count($items) - 1) {
            echo ' / ';
        }
    }
    echo '</div>';
}

/**
 * Custom Excerpt Length
 */
function putrafiber_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'putrafiber_excerpt_length');

/**
 * Custom Excerpt More
 */
function putrafiber_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'putrafiber_excerpt_more');

/**
 * Add Custom Body Classes
 */
function putrafiber_body_classes($classes) {
    if (is_singular()) {
        $classes[] = 'single-page';
    }
    if (is_front_page()) {
        $classes[] = 'home-page';
    }
    return $classes;
}
add_filter('body_class', 'putrafiber_body_classes');

/**
 * Add Preload for Critical Resources
 */
function putrafiber_preload_resources() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link rel="dns-prefetch" href="//www.google-analytics.com">';
}
add_action('wp_head', 'putrafiber_preload_resources', 1);

/**
 * Disable Emoji Scripts
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/**
 * Remove Query Strings from Static Resources
 */
function putrafiber_remove_query_strings($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'putrafiber_remove_query_strings', 15);
add_filter('style_loader_src', 'putrafiber_remove_query_strings', 15);

/**
 * Extract City from Title for ServiceArea Schema
 */
function putrafiber_extract_city($title) {
    $cities = array(
        'Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Makassar', 'Palembang',
        'Tangerang', 'Depok', 'Bekasi', 'Bogor', 'Batam', 'Pekanbaru', 'Bandar Lampung',
        'Padang', 'Malang', 'Yogyakarta', 'Denpasar', 'Pontianak', 'Samarinda', 'Manado',
        'Balikpapan', 'Jambi', 'Cirebon', 'Sukabumi', 'Tasikmalaya', 'Serang', 'Mataram',
        'Banjarmasin', 'Palu', 'Kendari', 'Kupang', 'Jayapura', 'Ambon', 'Ternate',
        'Cikarang', 'Karawang', 'Purwakarta', 'Subang', 'Indramayu', 'Kuningan', 'Majalengka'
    );
    
    foreach ($cities as $city) {
        if (stripos($title, $city) !== false) {
            return $city;
        }
    }
    
    return '';
}

/**
 * Get Reading Time
 */
function putrafiber_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    return $reading_time;
}

/**
 * Add Async/Defer to Scripts
 */
function putrafiber_add_async_defer($tag, $handle) {
    $async_scripts = array('putrafiber-main-js');
    $defer_scripts = array('putrafiber-animations');
    
    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' async src', $tag);
    }
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'putrafiber_add_async_defer', 10, 2);

