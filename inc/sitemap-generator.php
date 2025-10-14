<?php
/**
 * Sitemap Generator
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Generate XML Sitemap
 */
function putrafiber_generate_sitemap() {
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'sitemap.xml') !== false) {
        header('Content-Type: application/xml; charset=utf-8');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Homepage
        echo '<url>';
        echo '<loc>' . home_url('/') . '</loc>';
        echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
        echo '<changefreq>daily</changefreq>';
        echo '<priority>1.0</priority>';
        echo '</url>';
        
        // Posts
        $posts = get_posts(array(
            'numberposts' => -1,
            'post_type' => array('post', 'page', 'portfolio'),
            'post_status' => 'publish'
        ));
        
        foreach ($posts as  $post) {
            echo '<url>';
            echo '<loc>' . get_permalink($post->ID) . '</loc>';
            echo '<lastmod>' . date('Y-m-d', strtotime($post->post_modified)) . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.8</priority>';
            echo '</url>';
        }
        
        // Categories
        $categories = get_categories(array('hide_empty' => true));
        foreach ($categories as $category) {
            echo '<url>';
            echo '<loc>' . get_category_link($category->term_id) . '</loc>';
            echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.6</priority>';
            echo '</url>';
        }
        
        // Portfolio Categories
        $portfolio_cats = get_terms(array(
            'taxonomy' => 'portfolio_category',
            'hide_empty' => true
        ));
        
        if (!is_wp_error($portfolio_cats)) {
            foreach ($portfolio_cats as $cat) {
                echo '<url>';
                echo '<loc>' . get_term_link($cat) . '</loc>';
                echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
                echo '<changefreq>weekly</changefreq>';
                echo '<priority>0.6</priority>';
                echo '</url>';
            }
        }
        
        echo '</urlset>';
        exit;
    }
}
add_action('init', 'putrafiber_generate_sitemap');

/**
 * Generate robots.txt
 */
function putrafiber_generate_robots() {
    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/robots.txt') {
        header('Content-Type: text/plain');
        
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /wp-admin/\n";
        echo "Disallow: /wp-includes/\n";
        echo "Disallow: /wp-content/plugins/\n";
        echo "Disallow: /wp-content/themes/\n";
        echo "Disallow: /trackback/\n";
        echo "Disallow: /feed/\n";
        echo "Disallow: /comments/\n";
        echo "Disallow: /xmlrpc.php\n";
        echo "\n";
        echo "Sitemap: " . home_url('/sitemap.xml') . "\n";
        
        exit;
    }
}
add_action('init', 'putrafiber_generate_robots');

/**
 * Ping Search Engines on Post Publish
 */
function putrafiber_ping_search_engines($post_id) {
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    $sitemap_url = home_url('/sitemap.xml');
    
    // Ping Google
    wp_remote_get('http://www.google.com/ping?sitemap=' . urlencode($sitemap_url));
    
    // Ping Bing
    wp_remote_get('http://www.bing.com/ping?sitemap=' . urlencode($sitemap_url));
}
add_action('publish_post', 'putrafiber_ping_search_engines');
add_action('publish_page', 'putrafiber_ping_search_engines');
add_action('publish_portfolio', 'putrafiber_ping_search_engines');
