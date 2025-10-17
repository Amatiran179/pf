<?php
/**
 * Progressive Web App Functions
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('putrafiber_is_pwa_enabled')) {
    /**
     * Helper: Determine whether PWA features are enabled from theme options.
     * Safely handles different truthy values returned from the database.
     */
    function putrafiber_is_pwa_enabled() {
        if (!function_exists('putrafiber_get_option')) {
            return true;
        }

        $value = putrafiber_get_option('enable_pwa', '1');

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, array('1', 'true', 'yes', 'on'), true);
    }
}

/**
 * Add PWA Meta Tags
 */
function putrafiber_pwa_meta_tags() {
    if (!putrafiber_is_pwa_enabled()) return;
    
    $theme_color = get_theme_mod('putrafiber_primary_color', '#00BCD4');
    ?>
    <meta name="theme-color" content="<?php echo esc_attr($theme_color); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo esc_attr(putrafiber_get_option('pwa_short_name', 'PutraFiber')); ?>">
    <link rel="manifest" href="<?php echo esc_url(home_url('/manifest.webmanifest')); ?>">
    <?php
    
    $icon = putrafiber_get_option('pwa_icon', '');
    if ($icon) {
        ?>
        <link rel="apple-touch-icon" href="<?php echo esc_url($icon); ?>">
        <link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url($icon); ?>">
        <link rel="icon" type="image/png" sizes="512x512" href="<?php echo esc_url($icon); ?>">
        <?php
    }
}
add_action('wp_head', 'putrafiber_pwa_meta_tags');

/**
 * Generate manifest.json
 */
function putrafiber_generate_manifest() {
    if (!putrafiber_is_pwa_enabled()) {
        return;
    }

    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'manifest.json') !== false) {
        header('Content-Type: application/json');
        
        $manifest = array(
            'name' => putrafiber_get_option('pwa_name', 'PutraFiber'),
            'short_name' => putrafiber_get_option('pwa_short_name', 'PutraFiber'),
            'description' => get_bloginfo('description'),
            'start_url' => home_url('/'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => get_theme_mod('putrafiber_primary_color', '#00BCD4'),
            'orientation' => 'portrait-primary',
            'scope' => home_url('/'),
            'icons' => array()
        );
        
        $icon = putrafiber_get_option('pwa_icon', '');
        if ($icon) {
            $manifest['icons'][] = array(
                'src' => $icon,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            );
            $manifest['icons'][] = array(
                'src' => $icon,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            );
        }
        
        echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
add_action('init', 'putrafiber_generate_manifest');

function putrafiber_serve_static_pwa_files() {
    if (is_admin()) {
        return;
    }

    if (!putrafiber_is_pwa_enabled()) {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (!$request_uri) {
        return;
    }

    $theme_dir = get_template_directory();

    if (preg_match('#/(manifest\.webmanifest)$#', $request_uri)) {
        $manifest_path = $theme_dir . '/manifest.webmanifest';
        if (file_exists($manifest_path)) {
            header('Content-Type: application/manifest+json');
            readfile($manifest_path);
            exit;
        }
    }

    if (preg_match('#/(service-worker\.js)$#', $request_uri)) {
        $sw_path = $theme_dir . '/service-worker.js';
        if (file_exists($sw_path)) {
            header('Content-Type: application/javascript');
            header('Service-Worker-Allowed: /');
            readfile($sw_path);
            exit;
        }
    }
}
add_action('init', 'putrafiber_serve_static_pwa_files', 1);

/**
 * Generate Service Worker
 */
function putrafiber_generate_service_worker() {
    if (!putrafiber_is_pwa_enabled()) {
        return;
    }

    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'sw.js') !== false) {
        header('Content-Type: application/javascript');
        header('Service-Worker-Allowed: /');
        
        ?>
const CACHE_NAME = 'putrafiber-v1';
const urlsToCache = [
    '/',
    '/wp-content/themes/putrafiber-enterprise/assets/css/components.css',
    '/wp-content/themes/putrafiber-enterprise/assets/js/main.js'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
        <?php
        exit;
    }
}
add_action('init', 'putrafiber_generate_service_worker');

/**
 * Add Offline Page
 */
function putrafiber_offline_page() {
    if (!putrafiber_is_pwa_enabled()) {
        return;
    }

    add_rewrite_rule('^offline/?$', 'index.php?offline=1', 'top');
}
add_action('init', 'putrafiber_offline_page');

function putrafiber_offline_query_vars($vars) {
    if (!putrafiber_is_pwa_enabled()) {
        return $vars;
    }

    $vars[] = 'offline';
    return $vars;
}
add_filter('query_vars', 'putrafiber_offline_query_vars');

function putrafiber_offline_template($template) {
    if (get_query_var('offline')) {
        if (!putrafiber_is_pwa_enabled()) {
            status_header(404);
            $not_found = get_404_template();
            return $not_found ? $not_found : $template;
        }
        return locate_template('offline.php');
    }
    return $template;
}
add_filter('template_include', 'putrafiber_offline_template');

function putrafiber_manage_service_worker_registration() {
    if (is_admin()) {
        return;
    }

    if (!function_exists('putrafiber_is_pwa_enabled')) {
        return;
    }

    if (putrafiber_is_pwa_enabled()) {
        echo "<script>if('serviceWorker' in navigator){navigator.serviceWorker.register('/service-worker.js').catch(function(e){console.warn('PWA register failed',e);});}</script>";
        return;
    }

    echo "<script>if('serviceWorker' in navigator){navigator.serviceWorker.getRegistrations().then(function(regs){regs.forEach(function(reg){reg.unregister();});});}</script>";
}
add_action('wp_footer', 'putrafiber_manage_service_worker_registration', 100);
