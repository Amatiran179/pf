<?php
/**
 * Schema.org JSON-LD Generator
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Output Schema in Head
 */
function putrafiber_output_schema() {
    if (!putrafiber_get_option('enable_schema', '1')) return;
    
    $schemas = array();
    
    // Organization Schema (Always)
    $schemas[] = putrafiber_organization_schema();
    
    // Page-specific schemas
    if (is_front_page()) {
        $schemas[] = putrafiber_website_schema();
    } elseif (is_singular('post')) {
        $schemas[] = putrafiber_article_schema();
        $schemas[] = putrafiber_breadcrumb_schema();
    } elseif (is_singular('portfolio')) {
        $schemas[] = putrafiber_portfolio_schema();
        $schemas[] = putrafiber_breadcrumb_schema();
    } elseif (is_singular('product') || get_post_type() == 'post') {
        $product_schema = putrafiber_product_schema();
        if ($product_schema) {
            $schemas[] = $product_schema;
        }
    }
    
    // Output all schemas
    if (!empty($schemas)) {
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode(array('@graph' => $schemas), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n" . '</script>' . "\n";
    }
}
add_action('wp_head', 'putrafiber_output_schema', 5);

/**
 * Organization Schema
 */
function putrafiber_organization_schema() {
    $schema = array(
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => get_theme_mod('putrafiber_logo') ? get_theme_mod('putrafiber_logo') : get_template_directory_uri() . '/assets/images/logo.png',
        ),
        'description' => get_bloginfo('description'),
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => putrafiber_get_option('company_address', ''),
            'addressCountry' => 'ID'
        ),
        'telephone' => putrafiber_get_option('company_phone', ''),
        'email' => putrafiber_get_option('company_email', ''),
        'sameAs' => array_filter(array(
            putrafiber_get_option('facebook_url', ''),
            putrafiber_get_option('instagram_url', ''),
            putrafiber_get_option('youtube_url', ''),
            putrafiber_get_option('linkedin_url', ''),
            putrafiber_get_option('twitter_url', ''),
        ))
    );
    
    // Add Aggregate Rating if enabled
    if (putrafiber_get_option('enable_aggregate_rating', '1')) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => putrafiber_get_option('company_rating', '4.8'),
            'reviewCount' => putrafiber_get_option('review_count', '150'),
            'bestRating' => '5',
            'worstRating' => '1'
        );
    }
    
    $schema['@context'] = 'https://schema.org';
    return $schema;
}

/**
 * WebSite Schema
 */
function putrafiber_website_schema() {
    return array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'url' => home_url('/'),
        'name' => get_bloginfo('name'),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string'
        )
    );
}

/**
 * Article Schema
 */
function putrafiber_article_schema() {
    if (!is_singular('post')) return null;
    
    $post_id = get_the_ID();
    $author = get_the_author_meta('display_name');
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'description' => get_the_excerpt(),
        'image' => get_the_post_thumbnail_url($post_id, 'full'),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => array(
            '@type' => 'Person',
            'name' => $author
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_theme_mod('putrafiber_logo') ? get_theme_mod('putrafiber_logo') : get_template_directory_uri() . '/assets/images/logo.png'
            )
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink()
        )
    );
    
    // Add ServiceArea if city detected
    $city = putrafiber_extract_city(get_the_title());
    $service_area = get_post_meta($post_id, '_service_area', true);
    
    if ($service_area || $city) {
        $schema['serviceArea'] = array(
            '@type' => 'City',
            'name' => $service_area ? $service_area : $city
        );
    }
    
    // Add TouristAttraction if enabled
    if (get_post_meta($post_id, '_enable_tourist_schema', true)) {
        $schema['@type'] = array('Article', 'TouristAttraction');
    }
    
    // Add VideoObject if video URL exists
    $video_url = get_post_meta($post_id, '_portfolio_video', true);
    if ($video_url) {
        $schema['video'] = array(
            '@type' => 'VideoObject',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'thumbnailUrl' => get_the_post_thumbnail_url($post_id, 'full'),
            'contentUrl' => $video_url,
            'uploadDate' => get_the_date('c')
        );
    }
    
    return $schema;
}

/**
 * Portfolio Schema (TouristAttraction)
 */
function putrafiber_portfolio_schema() {
    if (!is_singular('portfolio')) return null;
    
    $post_id = get_the_ID();
    $location = get_post_meta($post_id, '_portfolio_location', true);
    $enable_tourist = get_post_meta($post_id, '_enable_tourist_schema', true);
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => $enable_tourist ? 'TouristAttraction' : 'CreativeWork',
        'name' => get_the_title(),
        'description' => get_the_excerpt(),
        'image' => get_the_post_thumbnail_url($post_id, 'full'),
        'url' => get_permalink()
    );
    
    if ($location) {
        $schema['address'] = array(
            '@type' => 'PostalAddress',
            'addressLocality' => $location,
            'addressCountry' => 'ID'
        );
    }
    
    return $schema;
}

/**
 * Product Schema
 */
function putrafiber_product_schema() {
    if (!is_singular('post')) return null;
    
    $post_id = get_the_ID();
    $price = get_post_meta($post_id, '_product_price', true);
    $stock = get_post_meta($post_id, '_product_stock', true);
    
    // Default price if empty
    if (empty($price)) {
        $price = '1000';
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => get_the_title(),
        'description' => get_the_excerpt(),
        'image' => get_the_post_thumbnail_url($post_id, 'full'),
        'sku' => 'PF-' . $post_id,
        'brand' => array(
            '@type' => 'Brand',
            'name' => 'PutraFiber'
        ),
        'offers' => array(
            '@type' => 'Offer',
            'url' => get_permalink(),
            'priceCurrency' => 'IDR',
            'price' => $price,
            'availability' => $stock === 'pre-order' ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
            'priceValidUntil' => date('Y-12-31'),
            'seller' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name')
            )
        )
    );
    
    // Add aggregate rating if available
    if (putrafiber_get_option('enable_aggregate_rating', '1')) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => '4.8',
            'reviewCount' => '50'
        );
    }
    
    return $schema;
}

/**
 * Breadcrumb Schema
 */
function putrafiber_breadcrumb_schema() {
    if (is_front_page()) return null;
    
    $items = array();
    $position = 1;
    
    // Home
    $items[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Home',
        'item' => home_url('/')
    );
    
    // Category/Archive
    if (is_category() || is_single()) {
        $category = get_the_category();
        if ($category) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $category[0]->name,
                'item' => get_category_link($category[0]->term_id)
            );
        }
    } elseif (is_post_type_archive('portfolio')) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Portofolio',
            'item' => get_post_type_archive_link('portfolio')
        );
    }
    
    // Current page
    if (is_singular()) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    }
    
    return array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items
    );
}
