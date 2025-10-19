<?php
/**
 * PutraFiber Enterprise Theme Functions
 *
 * @package PutraFiber
 * @version 2.3.0
 * - CLEANUP: struktur rapi, guard constants, require aman
 * - FIX: anti-zoom galeri + hard-fix mobile (tidak ganggu Swiper)
 * - STABLE: breadcrumbs & sorting, helper WhatsApp, async/defer, dsb.
 */

if (!defined('ABSPATH')) exit;

/** ==========================================================================
 * Constants
 * ========================================================================== */
if (!defined('PUTRAFIBER_VERSION')) define('PUTRAFIBER_VERSION', '2.1.0');
if (!defined('PUTRAFIBER_DIR'))     define('PUTRAFIBER_DIR', get_template_directory());
if (!defined('PUTRAFIBER_URI'))     define('PUTRAFIBER_URI', get_template_directory_uri());

require_once get_template_directory() . '/inc/helpers-sanitize.php';
require_once get_template_directory() . '/inc/core/versioning.php';
require_once get_template_directory() . '/inc/blocks.php';

function pf_enqueue_assets() {
  $manifest_loaded = false;
  $manifest_path   = get_template_directory() . '/assets/dist/manifest.json';

  $is_front_page          = is_front_page();
  $is_product_context     = is_singular('product') || is_post_type_archive('product') || is_tax(array('product_category', 'product_tag'));
  $is_portfolio_context   = is_singular('portfolio') || is_post_type_archive('portfolio') || is_tax('portfolio_category');
  $requires_gallery_assets = $is_product_context || $is_portfolio_context;

  $pwa_enabled = true;
  if (function_exists('putrafiber_is_pwa_enabled')) {
    $pwa_enabled = putrafiber_is_pwa_enabled();
  } elseif (function_exists('putrafiber_get_bool_option')) {
    $pwa_enabled = putrafiber_get_bool_option('enable_pwa', true);
  } elseif (function_exists('putrafiber_get_option')) {
    $raw_pwa_value = putrafiber_get_option('enable_pwa', '1');
    $normalized    = strtolower(trim((string) $raw_pwa_value));
    $pwa_enabled   = !in_array($normalized, array('0', 'false', 'no', 'off'), true);
  }

  // Font selalu dimuat agar konsisten antara build dan fallback.
  wp_enqueue_style(
    'pf-fonts',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap',
    array(),
    null
  );

  if (file_exists($manifest_path)) {
    $manifest_content = file_get_contents($manifest_path);
    if (!empty($manifest_content)) {
      $manifest = json_decode($manifest_content, true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($manifest)) {
        $dist_uri      = trailingslashit(get_template_directory_uri()) . 'assets/dist/';
        $dist_relative = 'assets/dist/';

        $entries = array(
          'core_css'       => 'assets/src/css/main.css',
          'front_css'      => 'assets/src/css/front-page.css',
          'product_css'    => 'assets/src/css/product.css',
          'portfolio_css'  => 'assets/src/css/portfolio.css',
          'core_js'        => 'assets/src/js/main.js',
          'front_js'       => 'assets/src/js/front-page.js',
          'pwa_js'         => 'assets/src/js/pwa.js',
        );

        $resolved = array();
        foreach ($entries as $key => $entry) {
          if (!empty($manifest[$entry]['file'])) {
            $resolved[$key] = ltrim($manifest[$entry]['file'], '/');
          }
        }

        $has_core_css = !empty($resolved['core_css']);
        $has_core_js  = !empty($resolved['core_js']);

        if ($has_core_css) {
          $css_uri = $dist_uri . $resolved['core_css'];
          wp_enqueue_style('pf-main', $css_uri, array(), pf_asset_version($dist_relative . $resolved['core_css']));
          $manifest_loaded = true;
        }

        if ($has_core_js) {
          wp_enqueue_script('jquery');
          $js_uri = $dist_uri . $resolved['core_js'];
          wp_enqueue_script('pf-main', $js_uri, array('jquery'), pf_asset_version($dist_relative . $resolved['core_js']), true);
          wp_script_add_data('pf-main', 'type', 'module');
          $manifest_loaded = true;
        } elseif ($manifest_loaded) {
          // Pastikan jQuery tersedia untuk skrip lain meskipun bundle utama tidak ditemukan.
          wp_enqueue_script('jquery');
        }

        $style_dependency  = $has_core_css ? array('pf-main') : array();
        $script_dependency = $has_core_js ? array('pf-main') : array('jquery');

        if ($is_front_page && !empty($resolved['front_css'])) {
          $css_uri = $dist_uri . $resolved['front_css'];
          wp_enqueue_style('pf-front-epic', $css_uri, $style_dependency, pf_asset_version($dist_relative . $resolved['front_css']));
        }

        if ($is_front_page && !empty($resolved['front_js'])) {
          $js_uri = $dist_uri . $resolved['front_js'];
          wp_enqueue_script('pf-front-epic', $js_uri, $script_dependency, pf_asset_version($dist_relative . $resolved['front_js']), true);
          wp_script_add_data('pf-front-epic', 'type', 'module');
        }

        if ($is_product_context && !empty($resolved['product_css'])) {
          $css_uri = $dist_uri . $resolved['product_css'];
          wp_enqueue_style('pf-product', $css_uri, $style_dependency, pf_asset_version($dist_relative . $resolved['product_css']));
        }

        if ($is_portfolio_context && !empty($resolved['portfolio_css'])) {
          $css_uri = $dist_uri . $resolved['portfolio_css'];
          wp_enqueue_style('pf-portfolio', $css_uri, $style_dependency, pf_asset_version($dist_relative . $resolved['portfolio_css']));
        }

        if ($pwa_enabled && !empty($resolved['pwa_js'])) {
          $js_uri = $dist_uri . $resolved['pwa_js'];
          wp_enqueue_script('pf-pwa', $js_uri, $script_dependency, pf_asset_version($dist_relative . $resolved['pwa_js']), true);
          wp_script_add_data('pf-pwa', 'type', 'module');
        }
      }
    }
  }

  if (!$manifest_loaded) {
    // Stylesheet dasar tema.
    wp_enqueue_style('pf-style', get_stylesheet_uri(), array(), pf_asset_version('style.css'));
    wp_enqueue_style('pf-header', PUTRAFIBER_URI . '/assets/css/header.css', array('pf-style'), pf_asset_version('assets/css/header.css'));
    wp_enqueue_style('pf-footer', PUTRAFIBER_URI . '/assets/css/footer.css', array('pf-style'), pf_asset_version('assets/css/footer.css'));
    wp_enqueue_style('pf-components', PUTRAFIBER_URI . '/assets/css/components.css', array('pf-style'), pf_asset_version('assets/css/components.css'));
    wp_enqueue_style('pf-animations', PUTRAFIBER_URI . '/assets/css/animations.css', array('pf-style'), pf_asset_version('assets/css/animations.css'));

    wp_enqueue_style('pf-responsive', PUTRAFIBER_URI . '/assets/css/responsive.css', array('pf-style'), pf_asset_version('assets/css/responsive.css'));
    if ($is_front_page) {
      wp_enqueue_style('pf-front-epic', PUTRAFIBER_URI . '/assets/css/front-page-epic.css', array('pf-components', 'pf-animations'), pf_asset_version('assets/css/front-page-epic.css'));
    }

    if ($is_product_context) {
      wp_enqueue_style('pf-product', PUTRAFIBER_URI . '/assets/css/product.css', array('pf-style'), pf_asset_version('assets/css/product.css'));
    }

    if ($is_portfolio_context) {
      wp_enqueue_style('pf-portfolio', PUTRAFIBER_URI . '/assets/css/portfolio.css', array('pf-style'), pf_asset_version('assets/css/portfolio.css'));
    }

    // Skrip inti tema (non-module).
    wp_enqueue_script('jquery');
    wp_enqueue_script('pf-main', PUTRAFIBER_URI . '/assets/js/main.js', array('jquery'), pf_asset_version('assets/js/main.js'), true);
    wp_enqueue_script('pf-search', PUTRAFIBER_URI . '/assets/js/search.js', array(), pf_asset_version('assets/js/search.js'), true);
    wp_enqueue_script('pf-lazyload', PUTRAFIBER_URI . '/assets/js/lazyload.js', array(), pf_asset_version('assets/js/lazyload.js'), true);
    wp_enqueue_script('pf-animations', PUTRAFIBER_URI . '/assets/js/animations.js', array(), pf_asset_version('assets/js/animations.js'), true);

    if ($pwa_enabled) {
      wp_enqueue_script('pf-pwa', PUTRAFIBER_URI . '/assets/js/pwa.js', array(), pf_asset_version('assets/js/pwa.js'), true);
    }

    if ($is_front_page) {
      wp_enqueue_script('pf-front-epic', PUTRAFIBER_URI . '/assets/js/front-page-epic.js', array('jquery'), pf_asset_version('assets/js/front-page-epic.js'), true);
    }
  }

  if ($requires_gallery_assets) {
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
    wp_enqueue_style('simplelightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css', array(), '2.14.2');

    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.5', true);
    wp_enqueue_script('simplelightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.js', array('jquery'), '2.14.2', true);

    wp_enqueue_script(
      'pf-gallery-unified',
      PUTRAFIBER_URI . '/assets/js/gallery-unified.js',
      array('jquery', 'swiper-js', 'simplelightbox-js'),
      pf_asset_version('assets/js/gallery-unified.js'),
      true
    );

    wp_script_add_data('simplelightbox-js', 'defer', true);
    wp_script_add_data('swiper-js', 'defer', true);
  }

  if (wp_script_is('pf-gallery-unified', 'enqueued')) {
    wp_localize_script('pf-gallery-unified', 'pfGalleryConfig', array(
      'autoplayDelay'          => 4000,
      'slideSpeed'             => 600,
      'enableLoop'             => true,
      'enableAutoplay'         => true,
      'lightboxAutoplay'       => true,
      'lightboxAutoplayDelay'  => 5200,
      'lightboxAnimationSpeed' => 280,
      'debug'                  => defined('WP_DEBUG') && WP_DEBUG,
    ));
  }

  if (wp_script_is('pf-main', 'enqueued')) {
    wp_localize_script('pf-main', 'putrafiber_vars', array(
      'ajax_url'        => admin_url('admin-ajax.php'),
      'nonce'           => wp_create_nonce('putrafiber_nonce'),
      'analytics_nonce' => wp_create_nonce('putrafiber_analytics'),
      'theme_url'       => PUTRAFIBER_URI,
      'whatsapp_number' => function_exists('putrafiber_whatsapp_number') ? putrafiber_whatsapp_number() : '',
      'copied_text'     => esc_html__('Copied to clipboard!', 'putrafiber'),
    ));
  }
}
add_action('wp_enqueue_scripts', 'pf_enqueue_assets', 5);

/** ==========================================================================
 * Require files (aman)
 * ========================================================================== */
$pf_requires = array(
  '/inc/theme-setup.php',
  '/inc/helpers-gallery.php',
  '/inc/helpers-frontpage.php',
  '/inc/customizer.php',
  '/inc/post-types/portfolio.php',
  '/inc/post-types/product.php',
  '/inc/admin/theme-options.php',
  '/inc/schema/schema-generator.php',
  '/inc/metabox-schema.php',
  '/inc/analytics.php',
  '/inc/seo-functions.php',
  '/inc/performance.php',
  '/inc/pwa.php',
  '/inc/webp-converter.php',
  '/inc/sitemap-generator.php',
);
foreach ($pf_requires as $rel) {
  $path = PUTRAFIBER_DIR . $rel;
  if (file_exists($path)) require_once $path;
}

require_once get_template_directory() . '/inc/schema/schema-helpers.php';
require_once get_template_directory() . '/inc/schema/schema-registry.php';
require_once get_template_directory() . '/inc/schema/schema-manager.php';
require_once get_template_directory() . '/inc/admin/cta-validator.php';

add_action('after_setup_theme', function () {
  add_theme_support('editor-styles');
  add_theme_support('wp-block-styles');
  add_theme_support('responsive-embeds');
  add_theme_support('align-wide');
});

add_action('after_setup_theme', array('PutraFiber_Schema_Manager', 'init'));
add_action('add_meta_boxes', array('PutraFiber_CTA_Validator', 'init'));

/** ==========================================================================
 * Theme Setup
 * ========================================================================== */
function putrafiber_setup() {
  load_theme_textdomain('putrafiber', get_template_directory() . '/languages');
  add_theme_support('automatic-feed-links');
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption','style','script'));
  add_theme_support('custom-logo', array(
    'height' => 100, 'width' => 300, 'flex-height' => true, 'flex-width' => true,
  ));

  register_nav_menus(array(
    'primary' => __('Primary Menu', 'putrafiber'),
    'footer'  => __('Footer Menu', 'putrafiber'),
  ));

  add_image_size('putrafiber-hero',      1920, 1080, true);
  add_image_size('putrafiber-portfolio', 800,  600,  true);
  add_image_size('putrafiber-product',   600,  600,  true);
  add_image_size('putrafiber-thumb',     400,  300,  true);
}
add_action('after_setup_theme', 'putrafiber_setup');

/** ==========================================================================
 * Widget Areas
 * ========================================================================== */
function putrafiber_widgets_init() {
  register_sidebar(array(
    'name' => __('Sidebar', 'putrafiber'), 'id' => 'sidebar-1',
    'description' => __('Add widgets here.', 'putrafiber'),
    'before_widget' => '<section id="%1$s" class="widget %2$s">', 'after_widget' => '</section>',
    'before_title'  => '<h3 class="widget-title">', 'after_title'  => '</h3>',
  ));
  register_sidebar(array(
    'name'=>__('Footer Column 1','putrafiber'),'id'=>'footer-1',
    'before_widget'=>'<div class="footer-widget">','after_widget'=>'</div>',
    'before_title'=>'<h4 class="footer-widget-title">','after_title'=>'</h4>',
  ));
  register_sidebar(array(
    'name'=>__('Footer Column 2','putrafiber'),'id'=>'footer-2',
    'before_widget'=>'<div class="footer-widget">','after_widget'=>'</div>',
    'before_title'=>'<h4 class="footer-widget-title">','after_title'=>'</h4>',
  ));
  register_sidebar(array(
    'name'=>__('Footer Column 3','putrafiber'),'id'=>'footer-3',
    'before_widget'=>'<div class="footer-widget">','after_widget'=>'</div>',
    'before_title'=>'<h4 class="footer-widget-title">','after_title'=>'</h4>',
  ));
}
add_action('widgets_init', 'putrafiber_widgets_init');

/** ==========================================================================
 * Helpers: Theme Options, WhatsApp, Links
 * ========================================================================== */
function putrafiber_get_option($key, $default = '') {
  static $options = null;

  if ($options === null) {
    $stored = get_option('putrafiber_options', array());
    $options = is_array($stored) ? $stored : array();
  }

  if (!array_key_exists($key, $options)) {
    return $default;
  }

  $value = $options[$key];

  if (is_array($default) && !is_array($value)) {
    return $default;
  }

  if ($value === '' && $default !== '') {
    return $default;
  }

  return apply_filters('putrafiber_get_option', $value, $key, $default);
}

function putrafiber_get_bool_option($key, $default = false) {
  $raw_default = $default ? '1' : '0';
  $value = putrafiber_get_option($key, $raw_default);

  if (is_bool($value)) {
    return $value;
  }

  if (is_numeric($value)) {
    return (int) $value === 1;
  }

  if (is_string($value)) {
    $normalized = strtolower(trim($value));
    if ($normalized === '') {
      return (bool) $default;
    }

    return in_array($normalized, array('1', 'true', 'yes', 'on', 'enabled'), true);
  }

  return (bool) $default;
}

function putrafiber_whatsapp_number() {
  $number = putrafiber_get_option('whatsapp_number', '085642318455');
  if (substr($number, 0, 1) === '0') $number = '62' . substr($number, 1);
  return preg_replace('/[^0-9]/', '', $number);
}

function putrafiber_whatsapp_link($message = '') {
  $number  = putrafiber_whatsapp_number();
  $message = $message ? urlencode($message) : urlencode('Halo, saya tertarik dengan produk PutraFiber');
  return "https://wa.me/{$number}?text={$message}";
}

/** ==========================================================================
 * SVG Icon helper (contoh WhatsApp)
 * ========================================================================== */
function putrafiber_get_svg_icon($icon_name, $width = 24, $height = 24) {
  if ($icon_name === 'whatsapp') {
    return '<svg width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>';
  }
  return '';
}

/** ==========================================================================
 * Breadcrumbs
 * ========================================================================== */
function putrafiber_breadcrumbs() {
  if (is_front_page()) {
    return;
  }

  $items   = array();
  $context = array(
    'object_id'   => isset($GLOBALS['post']) && $GLOBALS['post'] instanceof WP_Post ? $GLOBALS['post']->ID : 0,
    'is_archive'  => is_archive(),
    'is_search'   => is_search(),
    'is_404'      => is_404(),
    'queried_obj' => get_queried_object(),
  );

  $items[] = array(
    'title' => esc_html__('Home', 'putrafiber'),
    'url'   => home_url('/'),
  );

  $append_page_ancestors = function($post_id) use (&$items) {
    if (!$post_id) {
      return;
    }

    $ancestors = get_post_ancestors($post_id);
    if (empty($ancestors)) {
      return;
    }

    $ancestors = array_reverse($ancestors);
    foreach ($ancestors as $ancestor_id) {
      $title = get_the_title($ancestor_id);
      if (!$title) {
        continue;
      }

      $items[] = array(
        'title' => $title,
        'url'   => get_permalink($ancestor_id),
      );
    }
  };

  $append_term_lineage = function($term, $taxonomy) use (&$items) {
    if (!$term || is_wp_error($term)) {
      return;
    }

    if ($term->parent) {
      $ancestors = get_ancestors($term->term_id, $taxonomy);
      $ancestors = array_reverse($ancestors);
      foreach ($ancestors as $ancestor_id) {
        $ancestor = get_term($ancestor_id, $taxonomy);
        if ($ancestor && !is_wp_error($ancestor)) {
          $url = get_term_link($ancestor);
          if (!is_wp_error($url)) {
            $items[] = array(
              'title' => $ancestor->name,
              'url'   => $url,
            );
          }
        }
      }
    }

    $term_link = get_term_link($term);
    if (!is_wp_error($term_link)) {
      $items[] = array(
        'title' => $term->name,
        'url'   => $term_link,
      );
    } else {
      $items[] = array('title' => $term->name);
    }
  };

  if (is_home() && !is_front_page()) {
    $posts_page_id = (int) get_option('page_for_posts');
    if ($posts_page_id) {
      $append_page_ancestors($posts_page_id);
      $items[] = array('title' => get_the_title($posts_page_id));
    } else {
      $items[] = array('title' => esc_html__('Blog', 'putrafiber'));
    }
  } elseif (is_search()) {
    $search_query = wp_strip_all_tags(get_search_query());
    $items[] = array('title' => sprintf(__('Search results for "%s"', 'putrafiber'), $search_query));
  } elseif (is_404()) {
    $items[] = array('title' => __('Not Found', 'putrafiber'));
  } elseif (is_post_type_archive('product')) {
    $product_obj = get_post_type_object('product');
    $items[]     = array('title' => $product_obj ? $product_obj->labels->name : __('Products', 'putrafiber'));
  } elseif (is_post_type_archive('portfolio')) {
    $portfolio_obj = get_post_type_object('portfolio');
    $items[]       = array('title' => $portfolio_obj ? $portfolio_obj->labels->name : __('Portfolio', 'putrafiber'));
  } elseif (is_post_type_archive()) {
    $items[] = array('title' => post_type_archive_title('', false));
  } elseif (is_tax('product_category')) {
    $product_obj = get_post_type_object('product');
    if ($product_obj) {
      $items[] = array(
        'title' => $product_obj->labels->name,
        'url'   => get_post_type_archive_link('product'),
      );
    }

    $term = get_queried_object();
    if ($term instanceof WP_Term) {
      $append_term_lineage($term, 'product_category');
    }
  } elseif (is_tax('portfolio_category')) {
    $portfolio_obj = get_post_type_object('portfolio');
    if ($portfolio_obj) {
      $items[] = array(
        'title' => $portfolio_obj->labels->name,
        'url'   => get_post_type_archive_link('portfolio'),
      );
    }

    $term = get_queried_object();
    if ($term instanceof WP_Term) {
      $append_term_lineage($term, 'portfolio_category');
    }
  } elseif (is_category()) {
    $term = get_queried_object();
    if ($term instanceof WP_Term) {
      $append_term_lineage($term, 'category');
    }
  } elseif (is_singular('portfolio')) {
    $portfolio_obj = get_post_type_object('portfolio');
    if ($portfolio_obj) {
      $items[] = array(
        'title' => $portfolio_obj->labels->name,
        'url'   => get_post_type_archive_link('portfolio'),
      );
    }

    $terms = get_the_terms(get_the_ID(), 'portfolio_category');
    if ($terms && !is_wp_error($terms)) {
      $sorted_terms = wp_list_sort($terms, 'parent', 'ASC');
      $primary_term = $sorted_terms ? reset($sorted_terms) : null;
      if ($primary_term) {
        $append_term_lineage($primary_term, 'portfolio_category');
      }
    }

    $items[] = array('title' => get_the_title());
  } elseif (is_singular('product')) {
    $product_obj = get_post_type_object('product');
    if ($product_obj) {
      $items[] = array(
        'title' => $product_obj->labels->name,
        'url'   => get_post_type_archive_link('product'),
      );
    }

    $terms = get_the_terms(get_the_ID(), 'product_category');
    if ($terms && !is_wp_error($terms)) {
      $sorted_terms = wp_list_sort($terms, 'parent', 'ASC');
      $primary_term = $sorted_terms ? reset($sorted_terms) : null;
      if ($primary_term) {
        $append_term_lineage($primary_term, 'product_category');
      }
    }

    $items[] = array('title' => get_the_title());
  } elseif (is_singular('post')) {
    $categories = get_the_category();
    if (!empty($categories)) {
      $primary_category = $categories[0];
      $append_term_lineage($primary_category, 'category');
    }
    $items[] = array('title' => get_the_title());
  } elseif (is_page()) {
    $append_page_ancestors(get_the_ID());
    $items[] = array('title' => get_the_title());
  } elseif (is_archive()) {
    $items[] = array('title' => wp_strip_all_tags(get_the_archive_title()));
  }

  $items     = array_values(array_filter($items, function($item) {
    return !empty($item['title']);
  }));
  $items     = apply_filters('putrafiber_breadcrumb_items', $items, $context);
  $separator = function_exists('putrafiber_breadcrumb_separator')
    ? putrafiber_breadcrumb_separator(' / ')
    : ' / ';

  if (empty($items)) {
    return;
  }

  $last_index = count($items) - 1;

  echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'putrafiber') . '">';
  echo '<ol class="breadcrumbs__list">';

  foreach ($items as $index => $item) {
    $is_last = ($index === $last_index);
    $title   = esc_html($item['title']);
    $url     = isset($item['url']) ? $item['url'] : '';

    echo '<li class="breadcrumb-item' . ($is_last ? ' active' : '') . '">';

    if (!$is_last && !empty($url)) {
      echo '<a href="' . esc_url($url) . '">' . $title . '</a>';
    } else {
      echo '<span>' . $title . '</span>';
    }

    if (!$is_last) {
      echo '<span class="breadcrumbs__separator">' . esc_html($separator) . '</span>';
    }

    echo '</li>';
  }

  echo '</ol>';
  echo '</nav>';
}

/** ==========================================================================
 * Sorting arsip produk: ?orderby=popularity|title_asc|title_desc|date
 * ========================================================================== */
function putrafiber_product_archive_sorting($query) {
  if (is_admin() || !$query->is_main_query()) return;

  if (is_post_type_archive('product') || is_tax(array('product_category','product_tag'))) {
    $orderby = isset($_GET['orderby']) ? pf_clean_text($_GET['orderby']) : 'date';
    switch ($orderby) {
      case 'popularity':
        $query->set('orderby', 'comment_count');
        $query->set('order', 'DESC');
        break;
      case 'title_asc':
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        break;
      case 'title_desc':
        $query->set('orderby', 'title');
        $query->set('order', 'DESC');
        break;
      case 'date':
      default:
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
        break;
    }
  }
}
add_action('pre_get_posts', 'putrafiber_product_archive_sorting');

/** ==========================================================================
 * Excerpt helpers
 * ========================================================================== */
add_filter('excerpt_length', function($l){ return 25; });
add_filter('excerpt_more',   function($m){ return '...'; });

/** ==========================================================================
 * Body classes
 * ========================================================================== */
function putrafiber_body_classes($classes) {
  if (is_singular())   $classes[] = 'single-page';
  if (is_front_page()) $classes[] = 'home-page';
  return $classes;
}
add_filter('body_class', 'putrafiber_body_classes');

/** ==========================================================================
 * Preload resources
 * ========================================================================== */
function putrafiber_preload_resources() {
  echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
  echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
  echo '<link rel="dns-prefetch" href="//www.google-analytics.com">';
}
add_action('wp_head', 'putrafiber_preload_resources', 1);

/** ==========================================================================
 * Matikan emoji
 * ========================================================================== */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/** ==========================================================================
 * Ekstrak kota dari judul (untuk schema)
 * ========================================================================== */
function putrafiber_extract_city($title) {
  $cities = array(
    'Jakarta','Bandung','Surabaya','Medan','Semarang','Makassar','Palembang',
    'Tangerang','Depok','Bekasi','Bogor','Batam','Pekanbaru','Bandar Lampung',
    'Padang','Malang','Yogyakarta','Denpasar','Pontianak','Samarinda','Manado',
    'Balikpapan','Jambi','Cirebon','Sukabumi','Tasikmalaya','Serang','Mataram',
    'Banjarmasin','Palu','Kendari','Kupang','Jayapura','Ambon','Ternate',
    'Cikarang','Karawang','Purwakarta','Subang','Indramayu','Kuningan','Majalengka'
  );
  foreach ($cities as $city) {
    if (stripos($title, $city) !== false) return $city;
  }
  return '';
}

/** ==========================================================================
 * Reading time
 * ========================================================================== */
function putrafiber_reading_time() {
  $content = get_post_field('post_content', get_the_ID());
  $word_count = str_word_count(strip_tags($content));
  return max(1, ceil($word_count / 200));
}

/** ==========================================================================
 * Async/Defer injector
 * ========================================================================== */
function putrafiber_add_async_defer($tag, $handle) {
  $async = array('pf-main');
  $defer = array(); // 'putrafiber-animations' tidak lagi di-enqueue secara terpisah.
  if (in_array($handle, $async, true)) return str_replace(' src', ' async src', $tag);
  if (in_array($handle, $defer, true)) return str_replace(' src', ' defer src', $tag);
  return $tag;
}
add_filter('script_loader_tag', 'putrafiber_add_async_defer', 10, 2);
