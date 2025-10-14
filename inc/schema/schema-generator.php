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
    } elseif (is_singular('product')) {
        // FIXED: Changed from is_singular('post') to is_singular('product')
        $product_schemas = putrafiber_generate_product_schema(get_the_ID());
        // Product schema returns already formatted JSON, so we output it separately
        // Don't add to $schemas array
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
 * Product Schema (DEPRECATED - For Old Posts)
 * Kept for backward compatibility with old posts
 * 
 * @deprecated Use putrafiber_generate_product_schema() for CPT Product
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
 * ========================================
 * NEW: Product CPT Schema Generator
 * ========================================
 * Multi Schema Support: Product + Video + FAQ + HowTo
 * 
 * @param int $product_id Product Post ID
 * @return string JSON-LD Schema HTML
 */
function putrafiber_generate_product_schema($product_id) {
    if (!$product_id || get_post_type($product_id) !== 'product') {
        return '';
    }
    
    // Get meta data
    $price = get_post_meta($product_id, '_product_price', true);
    $stock = get_post_meta($product_id, '_product_stock', true) ?: 'ready';
    $sku = get_post_meta($product_id, '_product_sku', true) ?: 'PF-' . $product_id;
    $brand = get_option('putrafiber_options')['company_name'] ?? 'PutraFiber';
    $short_desc = get_post_meta($product_id, '_product_short_description', true);
    
    // Default price: Rp.1000 (anti Google penalty)
    if (empty($price) || $price <= 0) {
        $price = '1000';
    }
    
    // Stock mapping
    $stock_mapping = array(
        'ready' => 'InStock',
        'pre-order' => 'PreOrder',
        'out-of-stock' => 'OutOfStock'
    );
    $stock_status = isset($stock_mapping[$stock]) ? $stock_mapping[$stock] : 'InStock';
    
    // Base Product Schema
    $product_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => get_the_title($product_id),
        'description' => $short_desc ?: get_the_excerpt($product_id),
        'sku' => $sku,
        'brand' => array(
            '@type' => 'Brand',
            'name' => $brand
        ),
        'offers' => array(
            '@type' => 'Offer',
            'url' => get_permalink($product_id),
            'priceCurrency' => 'IDR',
            'price' => $price,
            'availability' => 'https://schema.org/' . $stock_status,
            'priceValidUntil' => date('Y') . '-12-31',
            'seller' => array(
                '@type' => 'Organization',
                'name' => $brand
            )
        )
    );
    
    // Add main image
    if (has_post_thumbnail($product_id)) {
        $product_schema['image'] = get_the_post_thumbnail_url($product_id, 'full');
    }
    
    // Add gallery images
    $gallery = get_post_meta($product_id, '_product_gallery', true);
    if ($gallery) {
        $gallery_ids = explode(',', $gallery);
        $images = array();
        
        // Add featured image first
        if (has_post_thumbnail($product_id)) {
            $images[] = get_the_post_thumbnail_url($product_id, 'full');
        }
        
        foreach ($gallery_ids as $img_id) {
            $img_url = wp_get_attachment_image_url($img_id, 'full');
            if ($img_url) {
                $images[] = $img_url;
            }
        }
        
        if (!empty($images)) {
            $product_schema['image'] = $images;
        }
    }
    
    // Add additional product info
    $material = get_post_meta($product_id, '_product_material', true);
    if ($material) {
        $product_schema['material'] = $material;
    }
    
    $colors = get_post_meta($product_id, '_product_colors', true);
    if ($colors) {
        $product_schema['color'] = $colors;
    }
    
    // Add Aggregate Rating if comments exist
    $comments_count = get_comments_number($product_id);
    if ($comments_count > 0) {
        $product_schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => '4.7',
            'reviewCount' => $comments_count
        );
    }
    
    // Initialize schemas array
    $all_schemas = array($product_schema);
    
    // ========================================
    // VIDEO SCHEMA (Optional)
    // ========================================
    $enable_video = get_post_meta($product_id, '_enable_video_schema', true);
    if ($enable_video === '1') {
        $video_url = get_post_meta($product_id, '_video_url', true);
        $video_title = get_post_meta($product_id, '_video_title', true);
        $video_desc = get_post_meta($product_id, '_video_description', true);
        $video_duration = get_post_meta($product_id, '_video_duration', true);
        
        if ($video_url) {
            $video_schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'VideoObject',
                'name' => $video_title ?: get_the_title($product_id),
                'description' => $video_desc ?: get_the_excerpt($product_id),
                'contentUrl' => $video_url,
                'uploadDate' => get_the_date('c', $product_id),
            );
            
            if ($video_duration) {
                $video_schema['duration'] = $video_duration;
            }
            
            if (has_post_thumbnail($product_id)) {
                $video_schema['thumbnailUrl'] = get_the_post_thumbnail_url($product_id, 'full');
            }
            
            $all_schemas[] = $video_schema;
        }
    }
    
    // ========================================
    // FAQ SCHEMA (Optional)
    // ========================================
    $enable_faq = get_post_meta($product_id, '_enable_faq_schema', true);
    if ($enable_faq === '1') {
        $faq_items = get_post_meta($product_id, '_faq_items', true);
        
        if (is_array($faq_items) && !empty($faq_items)) {
            $faq_schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array()
            );
            
            foreach ($faq_items as $item) {
                if (!empty($item['question']) && !empty($item['answer'])) {
                    $faq_schema['mainEntity'][] = array(
                        '@type' => 'Question',
                        'name' => $item['question'],
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text' => $item['answer']
                        )
                    );
                }
            }
            
            if (!empty($faq_schema['mainEntity'])) {
                $all_schemas[] = $faq_schema;
            }
        }
    }
    
    // ========================================
    // HOWTO SCHEMA (Optional)
    // ========================================
    $enable_howto = get_post_meta($product_id, '_enable_howto_schema', true);
    if ($enable_howto === '1') {
        $howto_steps = get_post_meta($product_id, '_howto_steps', true);
        
        if (is_array($howto_steps) && !empty($howto_steps)) {
            $howto_schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'HowTo',
                'name' => 'Cara Menggunakan ' . get_the_title($product_id),
                'description' => get_the_excerpt($product_id),
                'step' => array()
            );
            
            if (has_post_thumbnail($product_id)) {
                $howto_schema['image'] = get_the_post_thumbnail_url($product_id, 'full');
            }
            
            foreach ($howto_steps as $index => $step) {
                if (!empty($step['name']) && !empty($step['text'])) {
                    $howto_schema['step'][] = array(
                        '@type' => 'HowToStep',
                        'position' => $index + 1,
                        'name' => $step['name'],
                        'text' => $step['text']
                    );
                }
            }
            
            if (!empty($howto_schema['step'])) {
                $all_schemas[] = $howto_schema;
            }
        }
    }
    
    // ========================================
    // OUTPUT MULTI SCHEMA
    // ========================================
    $output = '<script type="application/ld+json">' . "\n";
    
    if (count($all_schemas) > 1) {
        // Multiple schemas: use @graph
        $output .= json_encode(
            array('@graph' => $all_schemas),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    } else {
        // Single schema
        $output .= json_encode(
            $all_schemas[0],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }
    
    $output .= "\n" . '</script>' . "\n";
    
    return $output;
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
    } elseif (is_post_type_archive('product')) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Produk',
            'item' => get_post_type_archive_link('product')
        );
    } elseif (is_tax('product_category')) {
        $term = get_queried_object();
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Produk',
            'item' => get_post_type_archive_link('product')
        );
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $term->name,
            'item' => get_term_link($term)
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
