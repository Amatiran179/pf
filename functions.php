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
if (!defined('PUTRAFIBER_VERSION')) define('PUTRAFIBER_VERSION', '1.0.0');
if (!defined('PUTRAFIBER_DIR'))     define('PUTRAFIBER_DIR', get_template_directory());
if (!defined('PUTRAFIBER_URI'))     define('PUTRAFIBER_URI', get_template_directory_uri());

require_once get_template_directory() . '/inc/helpers-sanitize.php';
require_once get_template_directory() . '/inc/core/versioning.php';

function pf_enqueue_assets() {
  $manifest_path = get_template_directory() . '/assets/dist/manifest.json';

  if (file_exists($manifest_path)) {
    $manifest_content = file_get_contents($manifest_path);
    if (!empty($manifest_content)) {
      $manifest = json_decode($manifest_content, true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($manifest)) {
        $dist_uri = trailingslashit(get_template_directory_uri()) . 'assets/dist/';
        $dist_relative = 'assets/dist/';

        $main_js = $manifest['assets/src/js/main.js']['file'] ?? '';
        $main_css = $manifest['assets/src/css/main.css']['file'] ?? '';

        if (!$main_css && !empty($manifest['assets/src/js/main.js']['css'][0])) {
          $main_css = $manifest['assets/src/js/main.js']['css'][0];
        }

        if ($main_css) {
          $css_uri = $dist_uri . ltrim($main_css, '/');
          wp_enqueue_style('pf-main', $css_uri, array(), pf_asset_version($dist_relative . ltrim($main_css, '/')));
        }

        if ($main_js) {
          $js_uri = $dist_uri . ltrim($main_js, '/');
          wp_enqueue_script('pf-main', $js_uri, array(), pf_asset_version($dist_relative . ltrim($main_js, '/')), true);
          wp_script_add_data('pf-main', 'type', 'module');
        }

        return;
      }
    }
  }

  wp_enqueue_style('pf-style', get_stylesheet_uri(), array(), pf_asset_version('style.css'));
}
add_action('wp_enqueue_scripts', 'pf_enqueue_assets', 5);

/** ==========================================================================
 * Require files (aman)
 * ========================================================================== */
$pf_requires = array(
  '/inc/theme-setup.php',
  '/inc/enqueue.php',
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
  load_theme_textdomain('putrafiber', get_template_directory() . '/languages');
  add_theme_support('editor-styles');
  add_theme_support('wp-block-styles');
  add_theme_support('responsive-embeds');
  add_theme_support('align-wide');
});

add_action('after_setup_theme', array('PutraFiber_Schema_Manager', 'init'));
add_action('add_meta_boxes', array('PutraFiber_CTA_Validator', 'init'));

add_action('wp_footer', function () {
  if (is_admin()) {
    return;
  }

  echo "<script>if('serviceWorker' in navigator){navigator.serviceWorker.register('/service-worker.js');}</script>";
}, 100);

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
  $options = get_option('putrafiber_options', array());
  return (isset($options[$key]) && $options[$key] !== '') ? $options[$key] : $default;
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
  if (is_front_page()) return;

  $items = array(array('title' => 'Home', 'url' => home_url('/')));

  if (is_post_type_archive('product')) {
    $items[] = array('title' => 'Semua Produk');
  } elseif (is_tax('product_category')) {
    $items[] = array('title' => 'Semua Produk', 'url' => get_post_type_archive_link('product'));
    $items[] = array('title' => single_term_title('', false));
  } elseif (is_singular('product')) {
    $items[] = array('title' => 'Semua Produk', 'url' => get_post_type_archive_link('product'));
    $terms = get_the_terms(get_the_ID(), 'product_category');
    if ($terms && !is_wp_error($terms)) {
      $items[] = array('title' => $terms[0]->name, 'url' => get_term_link($terms[0]));
    }
    $items[] = array('title' => get_the_title());
  } elseif (is_singular('post')) {
    $cat = get_the_category();
    if (!empty($cat)) {
      $items[] = array('title' => $cat[0]->name, 'url' => get_category_link($cat[0]->term_id));
    }
    $items[] = array('title' => get_the_title());
  } elseif (is_page()) {
    $items[] = array('title' => get_the_title());
  }

  echo '<div class="breadcrumbs">';
  $last = array_key_last($items);
  foreach ($items as $i => $it) {
    if ($i === $last) {
      echo '<span>' . esc_html($it['title']) . '</span>';
    } else {
      $url = isset($it['url']) ? $it['url'] : '#';
      echo '<a href="' . esc_url($url) . '">' . esc_html($it['title']) . '</a> / ';
    }
  }
  echo '</div>';
}

/** ==========================================================================
 * Sorting arsip produk: ?orderby=popularity|title_asc|title_desc|date
 * ========================================================================== */
function putrafiber_product_archive_sorting($query) {
  if (is_admin() || !$query->is_main_query()) return;

  if (is_post_type_archive('product') || is_tax(array('product_category','product_tag'))) {
    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
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
 * (Opsional) Hilangkan ?ver= di static resources
 * ========================================================================== */
function putrafiber_remove_query_strings($src) {
  if (strpos($src, '?ver=')) $src = remove_query_arg('ver', $src);
  return $src;
}
add_filter('script_loader_src', 'putrafiber_remove_query_strings', 15);
add_filter('style_loader_src',  'putrafiber_remove_query_strings', 15);

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
  $async = array('putrafiber-main-js');
  $defer = array('putrafiber-animations');
  if (in_array($handle, $async, true)) return str_replace(' src', ' async src', $tag);
  if (in_array($handle, $defer, true)) return str_replace(' src', ' defer src', $tag);
  return $tag;
}
add_filter('script_loader_tag', 'putrafiber_add_async_defer', 10, 2);

/** ==========================================================================
 * GALLERY CSS INJECTIONS (SATU TEMPAT) — anti zoom & mobile hard-fix
 * - Tidak menyentuh transform Swiper wrapper agar autoplay/slide aman
 * - Thumbs: penopang ukuran slide auto (ukuran final di product-gallery-fix.css/JS)
 * ========================================================================== */
function putrafiber_gallery_css_overrides() {
  if (!is_singular('product')) return;
  ?>
  <style id="pf-gallery-overrides">
    /* Matikan transform/anim/transition pada IMG di area galeri saja */
    .product-gallery img,
    .product-gallery .gallery-image {
      transform: none !important;
      -webkit-transform: none !important;
      animation: none !important;
      transition: none !important;
      will-change: auto !important;
      backface-visibility: hidden;
      transform-origin: center center !important;
    }
    /* Hover/active tetap non-zoom */
    .product-gallery *:hover > img,
    .product-gallery *:hover .gallery-image,
    .product-gallery a:hover img,
    .product-gallery figure:hover img {
      transform: none !important;
      -webkit-transform: none !important;
      animation: none !important;
      transition: none !important;
    }
    /* Tangkal utility scale-* pada IMG */
    .product-gallery img[class*="scale"],
    .product-gallery img[class*="hover:scale"],
    .product-gallery .gallery-image[class*="scale"],
    .product-gallery .gallery-image[class*="hover:scale"] {
      transform: none !important;
    }
    /* Swiper layout (aman) */
    .product-gallery .swiper-wrapper { display:flex !important; align-items:stretch; }
    .product-gallery .swiper-slide    { flex-shrink:0 !important; box-sizing:border-box; }

    /* Mobile hard-fix: kunci slide utama 100% */
    @media (max-width: 768px) {
      .product-gallery .product-gallery-slider .swiper-slide {
        flex: 0 0 100% !important;
        width: 100% !important;
        min-width: 100% !important;
        box-sizing: border-box !important;
      }
      .product-gallery,
      .product-gallery .swiper,
      .product-gallery .product-gallery-slider {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
      }
    }

    /* Penopang thumbnail: slide ikut konten (lebar auto, bukan 122px) */
    .product-gallery .product-gallery-thumbs .swiper-wrapper > .swiper-slide {
      width: auto !important;
      min-width: auto !important;
      flex: 0 0 auto !important;
      margin-right: 6px !important;
      box-sizing: content-box !important;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 0;
    }
    .product-gallery .product-gallery-thumbs .swiper-wrapper > .swiper-slide:last-child { margin-right: 0 !important; }

    /* Catatan:
       - Ukuran IMG & frame thumb (square 84/72 atau follow image) diatur di
         /assets/css/product-gallery-fix.css dan /assets/js/product-gallery.js (FINAL).
       - CSS di atas hanya memastikan Swiper tidak memaksa width 122px.
     */
  </style>
  <?php
}
add_action('wp_head', 'putrafiber_gallery_css_overrides', 9999);