<?php
/**
 * Performance Optimization
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Enable Gzip Compression
 */
function putrafiber_enable_gzip() {
    if (headers_sent() || !extension_loaded('zlib') || ini_get('zlib.output_compression')) {
        return;
    }

    $encoding_header = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? strtolower((string) $_SERVER['HTTP_ACCEPT_ENCODING']) : '';

    if ($encoding_header !== '' && strpos($encoding_header, 'gzip') !== false) {
        ob_start('ob_gzhandler');
    }
}
add_action('init', 'putrafiber_enable_gzip');

/**
 * Add Browser Caching Headers
 */
function putrafiber_browser_cache_headers() {
    if (headers_sent() || is_admin()) {
        return;
    }

    if (is_user_logged_in() || (function_exists('is_preview') && is_preview()) || (function_exists('is_customize_preview') && is_customize_preview())) {
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        return;
    }

    $max_age = apply_filters('putrafiber_browser_cache_max_age', 3600);
    header('Cache-Control: public, max-age=' . (int) $max_age . ', stale-while-revalidate=86400');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (int) $max_age) . ' GMT');
}
add_action('send_headers', 'putrafiber_browser_cache_headers');

/**
 * Lazy Load Images
 */
function putrafiber_lazy_load_images($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }
    
    $content = preg_replace_callback('/<img\b[^>]*>/i', function($matches) {
        $img = $matches[0];

        if (stripos($img, 'loading=') !== false || stripos($img, 'data-src') !== false) {
            return $img;
        }

        if (preg_match('/class=("|\")(.*?)\1/i', $img, $class_match)) {
            $existing_classes = $class_match[2];
            if (!preg_match('/\blazy-load\b/', $existing_classes)) {
                $new_classes = trim($existing_classes . ' lazy-load');
                $img = str_replace($class_match[0], 'class="' . esc_attr($new_classes) . '"', $img);
            }
            // Since we returned early if `loading=` exists, we can always add it here.
            $img = preg_replace('/<img\s+/i', '<img loading="lazy" ', $img, 1);
        } else {
            // No class attribute, so add both `loading` and `class`.
            $img = preg_replace('/<img\s+/i', '<img loading="lazy" class="lazy-load" ', $img, 1);
        }

        return $img;
    }, $content);

    return $content;
}
add_filter('the_content', 'putrafiber_lazy_load_images');
add_filter('post_thumbnail_html', 'putrafiber_lazy_load_images');

/**
 * Defer JavaScript
 */
function putrafiber_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array('jquery', 'jquery-core', 'jquery-migrate');
    
    if (in_array($handle, $defer_scripts)) {
        return $tag;
    }
    
    return str_replace(' src', ' defer src', $tag);
}
// add_filter('script_loader_tag', 'putrafiber_defer_scripts', 10, 3);

/**
 * Remove WordPress Version from Head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Disable Embeds
 */
function putrafiber_disable_embeds() {
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
}
add_action('init', 'putrafiber_disable_embeds');

/**
 * Remove Query Strings from Static Resources
 */
function putrafiber_remove_script_version($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'putrafiber_remove_script_version', 15, 1);
add_filter('style_loader_src', 'putrafiber_remove_script_version', 15, 1);

/**
 * Limit Post Revisions
 */
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

/**
 * Increase Autosave Interval
 */
if (!defined('AUTOSAVE_INTERVAL')) {
    define('AUTOSAVE_INTERVAL', 300);
}

/**
 * Optimize Database on Theme Activation
 */
function putrafiber_optimize_database() {
    global $wpdb;
    $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
    
    foreach ($tables as $table) {
        $wpdb->query("OPTIMIZE TABLE {$table[0]}");
    }
}

/**
 * Clean Head
 */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

/**
 * Prefetch DNS
 */
function putrafiber_dns_prefetch() {
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
}
add_action('wp_head', 'putrafiber_dns_prefetch', 0);

function putrafiber_preload_primary_fonts() {
    $font_stylesheet = 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="preload" as="style" href="' . esc_url($font_stylesheet) . '">' . "\n";
}
add_action('wp_head', 'putrafiber_preload_primary_fonts', 1);

/**
 * Defer CSS Loading (Critical CSS)
 */
function putrafiber_defer_css() {
    ?>
    <script>
    function loadCSS(href) {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
    }
    
    window.addEventListener('load', function() {
        loadCSS('<?php echo PUTRAFIBER_URI; ?>/assets/css/animations.css');
    });
    </script>
    <?php
}
add_action('wp_footer', 'putrafiber_defer_css');

/**
 * Inline Critical CSS
 */
function putrafiber_critical_css() {
    ?>
    <style id="critical-css">
        body{margin:0;padding:0;font-family:var(--font-primary)}
        .header{background:#fff;position:sticky;top:0;z-index:1000;box-shadow:var(--shadow-sm)}
        .hero{min-height:80vh;display:flex;align-items:center;justify-content:center}
        img{max-width:100%;height:auto}
    </style>
    <?php
}
add_action('wp_head', 'putrafiber_critical_css', 1);
