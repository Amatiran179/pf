<?php
/**
 * Schema.org JSON-LD Generator
 *
 * @package PutraFiber
 * @version 3.0.0 - ENHANCED VERSION
 * 
 * NEW FEATURES:
 * - LocalBusiness Schema (configurable pages)
 * - Auto ServiceArea extraction from title
 * - Enhanced TouristAttraction Schema (full support)
 * - All schemas can be toggled on/off per post/page
 */

if (!defined('ABSPATH')) exit;

/**
 * Output Schema in Head
 */
function putrafiber_output_schema() {
    if (!putrafiber_get_option('enable_schema', '1')) return;

    if (function_exists('pf_schema_yes') && pf_schema_yes()) {
        return;
    }

    $schemas = array();

    // ===================================================================
    // PRODUCT PAGES: Standalone schema (already complete)
    // ===================================================================
    if (is_singular('product')) {
        echo putrafiber_generate_product_schema(get_the_ID());
        return;
    }

    // ===================================================================
    // ORGANIZATION SCHEMA: Always present
    // ===================================================================
    $schemas[] = putrafiber_organization_schema();

    // ===================================================================
    // LOCAL BUSINESS SCHEMA: Configurable per page/post
    // ===================================================================
    $local_business_schema = putrafiber_local_business_schema();
    if ($local_business_schema) {
        $schemas[] = $local_business_schema;
    }

    // ===================================================================
    // PAGE-SPECIFIC SCHEMAS
    // ===================================================================
    if (is_front_page()) {
        $schemas[] = putrafiber_website_schema();
        
    } elseif (is_singular('post')) {
        $schemas[] = putrafiber_article_schema();
        $schemas[] = putrafiber_breadcrumb_schema();
        
    } elseif (is_singular('portfolio')) {
        
        // Check if TouristAttraction enabled for this portfolio
        if (get_post_meta(get_the_ID(), '_enable_tourist_schema', true) === '1') {
            $schemas[] = putrafiber_tourist_attraction_schema();
        } else {
            $schemas[] = putrafiber_portfolio_schema();
        }
        $schemas[] = putrafiber_breadcrumb_schema();
        
    } elseif (is_page() || is_post_type_archive() || is_tax()) {
        $schemas[] = putrafiber_breadcrumb_schema();
    }

    // ===================================================================
    // OUTPUT ALL SCHEMAS
    // ===================================================================
    if (!empty($schemas)) {
        /**
         * Signal that the legacy schema layer has rendered output.
         *
         * @param array $schemas Rendered schema graph collection.
         */
        do_action('putrafiber_schema_legacy_rendered', $schemas);

        echo '<script type="application/ld+json">' . "\n";
        echo json_encode(
            array('@graph' => array_filter($schemas)),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
        echo "\n" . '</script>' . "\n";
    }
}
add_action('wp_head', 'putrafiber_output_schema', 5);

/**
 * Organization Schema
 */
function putrafiber_organization_schema() {
    $logo_url = get_theme_mod('custom_logo') 
        ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') 
        : get_template_directory_uri() . '/assets/images/logo.png';
    
    $schema = array(
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => $logo_url,
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
 * NEW: Local Business Schema
 * Displays on configured pages (Contact, About, etc.)
 */
function putrafiber_local_business_schema() {
    // Check if current page/post has local business enabled
    $post_id = get_queried_object_id();
    
    // Skip if not enabled for this specific post/page
    if (is_singular() && get_post_meta($post_id, '_enable_local_business', true) !== '1') {
        return null;
    }
    
    // Global setting: which pages to show on
    $enabled_pages = putrafiber_get_option('local_business_pages', array());
    
    // Check if current page is in enabled list
    if (is_page() && !in_array($post_id, (array)$enabled_pages)) {
        return null;
    }
    
    $logo_url = get_theme_mod('custom_logo') 
        ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') 
        : get_template_directory_uri() . '/assets/images/logo.png';
    
    $business_type = putrafiber_get_option('business_type', 'LocalBusiness');
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => $business_type, // LocalBusiness, Store, etc.
        '@id' => home_url('/#localbusiness'),
        'name' => get_bloginfo('name'),
        'description' => putrafiber_get_option('business_description', get_bloginfo('description')),
        'url' => home_url('/'),
        'telephone' => putrafiber_get_option('company_phone', ''),
        'email' => putrafiber_get_option('company_email', ''),
        'logo' => $logo_url,
        'image' => $logo_url,
    );
    
    // Address
    $schema['address'] = array(
        '@type' => 'PostalAddress',
        'streetAddress' => putrafiber_get_option('company_address', ''),
        'addressLocality' => putrafiber_get_option('company_city', ''),
        'addressRegion' => putrafiber_get_option('company_province', ''),
        'postalCode' => putrafiber_get_option('company_postal_code', ''),
        'addressCountry' => 'ID'
    );
    
    // Geo Coordinates
    $latitude = putrafiber_get_option('company_latitude', '');
    $longitude = putrafiber_get_option('company_longitude', '');
    
    if ($latitude && $longitude) {
        $schema['geo'] = array(
            '@type' => 'GeoCoordinates',
            'latitude' => $latitude,
            'longitude' => $longitude
        );
    }
    
    // Opening Hours
    $opening_hours = putrafiber_get_option('opening_hours', array());
    if (!empty($opening_hours)) {
        $schema['openingHoursSpecification'] = array();
        foreach ($opening_hours as $schedule) {
            if (!empty($schedule['days']) && !empty($schedule['opens']) && !empty($schedule['closes'])) {
                $schema['openingHoursSpecification'][] = array(
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => $schedule['days'], // e.g., "Monday", "Tuesday" or array
                    'opens' => $schedule['opens'],    // e.g., "08:00"
                    'closes' => $schedule['closes']   // e.g., "17:00"
                );
            }
        }
    }
    
    // Price Range
    if ($price_range = putrafiber_get_option('price_range', '')) {
        $schema['priceRange'] = $price_range; // e.g., "$$" or "Rp 100.000 - Rp 10.000.000"
    }
    
    // Payment Methods
    $payment_methods = putrafiber_get_option('payment_methods', array());
    if (!empty($payment_methods)) {
        $schema['paymentAccepted'] = implode(', ', $payment_methods);
    }
    
    // Aggregate Rating
    if (putrafiber_get_option('enable_aggregate_rating', '1')) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => putrafiber_get_option('company_rating', '4.8'),
            'reviewCount' => putrafiber_get_option('review_count', '150'),
            'bestRating' => '5',
            'worstRating' => '1'
        );
    }
    
    // Service Area (multiple areas support)
    $service_areas = putrafiber_get_option('service_areas', array());
    if (!empty($service_areas)) {
        $area_served = array();
        foreach ($service_areas as $area) {
            if (!empty($area)) {
                $area_served[] = array(
                    '@type' => 'City',
                    'name' => $area
                );
            }
        }
        if (!empty($area_served)) {
            $schema['areaServed'] = $area_served;
        }
    }
    
    // Social Media
    $schema['sameAs'] = array_filter(array(
        putrafiber_get_option('facebook_url', ''),
        putrafiber_get_option('instagram_url', ''),
        putrafiber_get_option('youtube_url', ''),
        putrafiber_get_option('linkedin_url', ''),
        putrafiber_get_option('twitter_url', ''),
    ));
    
    return $schema;
}

/**
 * Website Schema
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
 * Article Schema (Enhanced with ServiceArea)
 */
function putrafiber_article_schema() {
    if (!is_singular('post')) return null;
    
    $post_id = get_the_ID();
    $author = get_the_author_meta('display_name');
    $logo_url = get_theme_mod('custom_logo') 
        ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') 
        : get_template_directory_uri() . '/assets/images/logo.png';
    
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
                'url' => $logo_url
            )
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink()
        )
    );
    
    // ===================================================================
    // SERVICE AREA: Auto-extract or manual input
    // ===================================================================
    $service_area = putrafiber_get_service_area($post_id);
    if (!empty($service_area)) {
        $structured_area = count($service_area) === 1 ? $service_area[0] : $service_area;
        $schema['spatial'] = $structured_area;
        $schema['spatialCoverage'] = $structured_area;
        $schema['areaServed'] = $structured_area;
    }
    
    // Video
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
 * Portfolio Schema (Enhanced with ServiceArea)
 */
function putrafiber_portfolio_schema() {
    if (!is_singular('portfolio')) return null;
    
    $post_id = get_the_ID();
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => get_the_title(),
        'description' => get_the_excerpt(),
        'image' => get_the_post_thumbnail_url($post_id, 'full'),
        'url' => get_permalink()
    );
    
    // Service Area
    $service_area = putrafiber_get_service_area($post_id);
    if (!empty($service_area)) {
        $structured_area = count($service_area) === 1 ? $service_area[0] : $service_area;
        $schema['spatial'] = $structured_area;
        $schema['spatialCoverage'] = $structured_area;
        $schema['areaServed'] = $structured_area;
    }
    
    return $schema;
}

/**
 * NEW: Tourist Attraction Schema (Complete Implementation)
 */
function putrafiber_tourist_attraction_schema() {
    $post_id = get_the_ID();
    
    // Only for portfolio with tourist attraction enabled
    if (!$post_id || get_post_meta($post_id, '_enable_tourist_schema', true) !== '1') {
        return null;
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'TouristAttraction',
        'name' => get_the_title(),
        'description' => wp_strip_all_tags(get_the_content()),
        'url' => get_permalink(),
    );
    
    // Images (featured + gallery)
    $images = array();
    if (has_post_thumbnail($post_id)) {
        $images[] = get_the_post_thumbnail_url($post_id, 'full');
    }
    
    // Portfolio gallery
    $gallery_ids = get_post_meta($post_id, '_portfolio_gallery', true);
    if ($gallery_ids) {
        foreach (explode(',', $gallery_ids) as $img_id) {
            if ($img_url = wp_get_attachment_image_url(trim($img_id), 'full')) {
                $images[] = $img_url;
            }
        }
    }
    
    if (!empty($images)) {
        $schema['image'] = count($images) > 1 ? $images : $images[0];
    }
    
    // Address / Location
    $location = get_post_meta($post_id, '_tourist_location', true);
    $street_address = get_post_meta($post_id, '_tourist_street_address', true);
    $city = get_post_meta($post_id, '_tourist_city', true);
    $province = get_post_meta($post_id, '_tourist_province', true);
    $postal_code = get_post_meta($post_id, '_tourist_postal_code', true);
    
    if ($location || $city) {
        $schema['address'] = array(
            '@type' => 'PostalAddress',
            'addressLocality' => $city ?: $location,
            'addressCountry' => 'ID'
        );
        
        if ($street_address) $schema['address']['streetAddress'] = $street_address;
        if ($province) $schema['address']['addressRegion'] = $province;
        if ($postal_code) $schema['address']['postalCode'] = $postal_code;
    }
    
    // Geo Coordinates
    $latitude = get_post_meta($post_id, '_tourist_latitude', true);
    $longitude = get_post_meta($post_id, '_tourist_longitude', true);
    
    if ($latitude && $longitude) {
        $schema['geo'] = array(
            '@type' => 'GeoCoordinates',
            'latitude' => floatval($latitude),
            'longitude' => floatval($longitude)
        );
    }
    
    // Opening Hours
    $opening_hours = get_post_meta($post_id, '_tourist_opening_hours', true);
    if ($opening_hours && is_array($opening_hours)) {
        $schema['openingHoursSpecification'] = array();
        foreach ($opening_hours as $schedule) {
            if (!empty($schedule['days']) && !empty($schedule['opens']) && !empty($schedule['closes'])) {
                $schema['openingHoursSpecification'][] = array(
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => $schedule['days'],
                    'opens' => $schedule['opens'],
                    'closes' => $schedule['closes']
                );
            }
        }
    }
    
    // Is Accessible For Free
    $is_free = get_post_meta($post_id, '_tourist_is_free', true);
    if ($is_free === '1') {
        $schema['isAccessibleForFree'] = true;
    } elseif ($is_free === '0') {
        $schema['isAccessibleForFree'] = false;
    }
    
    // Entrance Fee
    if ($is_free !== '1') {
        $entrance_fee = get_post_meta($post_id, '_tourist_entrance_fee', true);
        if ($entrance_fee) {
            $schema['touristType'] = 'Paid Attraction';
            $schema['priceRange'] = $entrance_fee; // e.g., "Rp 25.000 - Rp 50.000"
        }
    }
    
    // Available Languages
    $languages = get_post_meta($post_id, '_tourist_languages', true);
    if ($languages) {
        $lang_array = is_array($languages) ? $languages : explode(',', $languages);
        $schema['availableLanguage'] = array_map('trim', $lang_array);
    }
    
    // Contact Information
    $phone = get_post_meta($post_id, '_tourist_phone', true);
    $email = get_post_meta($post_id, '_tourist_email', true);
    
    if ($phone || $email) {
        $schema['contactPoint'] = array(
            '@type' => 'ContactPoint',
            'contactType' => 'customer service'
        );
        if ($phone) $schema['contactPoint']['telephone'] = $phone;
        if ($email) $schema['contactPoint']['email'] = $email;
    }
    
    // Amenity Features
    $amenities = get_post_meta($post_id, '_tourist_amenities', true);
    if ($amenities && is_array($amenities)) {
        $schema['amenityFeature'] = array();
        foreach ($amenities as $amenity) {
            if (!empty($amenity)) {
                $schema['amenityFeature'][] = array(
                    '@type' => 'LocationFeatureSpecification',
                    'name' => $amenity
                );
            }
        }
    }
    
    // Aggregate Rating
    $rating = get_post_meta($post_id, '_tourist_rating', true);
    $review_count = get_post_meta($post_id, '_tourist_review_count', true);
    
    if ($rating || $review_count) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => $rating ?: '4.5',
            'reviewCount' => $review_count ?: '1',
            'bestRating' => '5',
            'worstRating' => '1'
        );
    }
    
    // Public Access
    $public_access = get_post_meta($post_id, '_tourist_public_access', true);
    if ($public_access) {
        $schema['publicAccess'] = ($public_access === '1');
    }
    
    return $schema;
}

/**
 * Product Schema (Preserved - Already Complete)
 */
function putrafiber_generate_product_schema($product_id) {
    if (!$product_id || get_post_type($product_id) !== 'product') {
        return '';
    }
    
    $price = get_post_meta($product_id, '_product_price', true);
    $stock = get_post_meta($product_id, '_product_stock', true) ?: 'ready';
    $sku = get_post_meta($product_id, '_product_sku', true) ?: 'PF-' . $product_id;
    $brand = get_option('putrafiber_options')['company_name'] ?? 'PutraFiber';
    $short_desc = get_post_meta($product_id, '_product_short_description', true);
    
    if (empty($price) || $price <= 0) {
        $price = '1000';
    }
    
    $stock_mapping = ['ready' => 'InStock', 'pre-order' => 'PreOrder', 'out-of-stock' => 'OutOfStock'];
    $stock_status = $stock_mapping[$stock] ?? 'InStock';
    
    $product_schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => get_the_title($product_id),
        'description' => wp_strip_all_tags($short_desc ?: get_the_excerpt($product_id)),
        'sku' => $sku,
        'brand' => ['@type' => 'Brand', 'name' => $brand],
        'offers' => [
            '@type' => 'Offer',
            'url' => get_permalink($product_id),
            'priceCurrency' => 'IDR',
            'price' => $price,
            'availability' => 'https://schema.org/' . $stock_status,
            'priceValidUntil' => date('Y') . '-12-31',
            'seller' => ['@type' => 'Organization', 'name' => $brand]
        ]
    ];
    
    // Images
    $images = [];
    if (has_post_thumbnail($product_id)) {
        $images[] = get_the_post_thumbnail_url($product_id, 'full');
    }
    $gallery_ids = array_filter(explode(',', get_post_meta($product_id, '_product_gallery', true)));
    foreach ($gallery_ids as $img_id) {
        if ($img_url = wp_get_attachment_image_url(trim($img_id), 'full')) {
            if (!in_array($img_url, $images)) {
                $images[] = $img_url;
            }
        }
    }
    if (!empty($images)) {
        $product_schema['image'] = count($images) > 1 ? $images : $images[0];
    }
    
    // Material & Color
    if ($material = get_post_meta($product_id, '_product_material', true)) {
        $product_schema['material'] = $material;
    }
    if ($colors = get_post_meta($product_id, '_product_colors', true)) {
        $product_schema['color'] = $colors;
    }
    
    // ===================================================================
    // SERVICE AREA for Product
    // ===================================================================
    $service_area = putrafiber_get_service_area($product_id);
    if (!empty($service_area)) {
        $product_schema['areaServed'] = count($service_area) === 1 ? $service_area[0] : $service_area;
    }
    
    // Aggregate Rating
    $comments_count = get_comments_number($product_id);
    if ($comments_count > 0) {
        $product_schema['aggregateRating'] = [
            '@type' => 'AggregateRating', 
            'ratingValue' => '4.7', 
            'reviewCount' => $comments_count
        ];
    }
    
    $all_schemas = [$product_schema];
    
    // Video Schema
    if (get_post_meta($product_id, '_enable_video_schema', true) === '1' && ($video_url = get_post_meta($product_id, '_video_url', true))) {
        $video_schema = [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => get_post_meta($product_id, '_video_title', true) ?: get_the_title($product_id),
            'description' => get_post_meta($product_id, '_video_description', true) ?: wp_strip_all_tags($short_desc),
            'contentUrl' => $video_url,
            'uploadDate' => get_the_date('c', $product_id),
            'thumbnailUrl' => has_post_thumbnail($product_id) ? get_the_post_thumbnail_url($product_id, 'full') : '',
        ];
        if ($duration = get_post_meta($product_id, '_video_duration', true)) {
            $video_schema['duration'] = $duration;
        }
        $all_schemas[] = $video_schema;
    }
    
    // FAQ Schema
    if (get_post_meta($product_id, '_enable_faq_schema', true) === '1' && ($faq_items = get_post_meta($product_id, '_faq_items', true))) {
        if (is_array($faq_items) && !empty($faq_items)) {
            $main_entity = [];
            foreach ($faq_items as $item) {
                if (!empty($item['question']) && !empty($item['answer'])) {
                    $main_entity[] = [
                        '@type' => 'Question', 
                        'name' => $item['question'], 
                        'acceptedAnswer' => [
                            '@type' => 'Answer', 
                            'text' => $item['answer']
                        ]
                    ];
                }
            }
            if (!empty($main_entity)) {
                $all_schemas[] = [
                    '@context' => 'https://schema.org', 
                    '@type' => 'FAQPage', 
                    'mainEntity' => $main_entity
                ];
            }
        }
    }
    
    // HowTo Schema
    if (get_post_meta($product_id, '_enable_howto_schema', true) === '1' && ($howto_steps = get_post_meta($product_id, '_howto_steps', true))) {
        if (is_array($howto_steps) && !empty($howto_steps)) {
            $howto_schema = [
                '@context' => 'https://schema.org', 
                '@type' => 'HowTo', 
                'name' => 'Cara Menggunakan ' . get_the_title($product_id), 
                'description' => get_the_excerpt($product_id), 
                'step' => []
            ];
            
            if (has_post_thumbnail($product_id)) {
                $howto_schema['image'] = get_the_post_thumbnail_url($product_id, 'full');
            }
            
            foreach ($howto_steps as $index => $step) {
                if (!empty($step['name']) && !empty($step['text'])) {
                    $howto_schema['step'][] = [
                        '@type' => 'HowToStep', 
                        'position' => $index + 1, 
                        'name' => $step['name'], 
                        'text' => $step['text']
                    ];
                }
            }
            
            if (!empty($howto_schema['step'])) {
                $all_schemas[] = $howto_schema;
            }
        }
    }
    
    $output = '<script type="application/ld+json">' . "\n";
    $output .= json_encode(
        count($all_schemas) > 1 ? ['@graph' => $all_schemas] : $all_schemas[0],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
    $output .= "\n" . '</script>' . "\n";
    
    return $output;
}

/**
 * Breadcrumb Schema
 */
function putrafiber_breadcrumb_schema() {
    if (is_front_page()) return null;
    
    $items = [];
    $position = 1;
    
    $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => 'Home', 'item' => home_url('/')];

    if (is_post_type_archive('product')) {
        $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => 'Produk', 'item' => get_post_type_archive_link('product')];
        
    } elseif (is_tax('product_category')) {
        $term = get_queried_object();
        $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => 'Produk', 'item' => get_post_type_archive_link('product')];
        $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => $term->name, 'item' => get_term_link($term)];
        
    } elseif (is_singular('product')) {
        $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => 'Produk', 'item' => get_post_type_archive_link('product')];
        $terms = get_the_terms(get_the_ID(), 'product_category');
        if ($terms && !is_wp_error($terms)) {
            $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => $terms[0]->name, 'item' => get_term_link($terms[0])];
        }
        $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => get_the_title(), 'item' => get_permalink()];
        
    } elseif (is_singular()) {
        $category = get_the_category();
        if ($category) {
            $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => $category[0]->name, 'item' => get_category_link($category[0]->term_id)];
        }
        $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => get_the_title(), 'item' => get_permalink()];
        
    } elseif (is_page()) {
        $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => get_the_title(), 'item' => get_permalink()];
    }

    if (count($items) <= 1) return null;
    
    return [
        '@context' => 'https://schema.org', 
        '@type' => 'BreadcrumbList', 
        'itemListElement' => $items
    ];
}

/**
 * Build Place schema for city/province combinations.
 */
function putrafiber_format_service_area_place($city, $province = '', $country_code = 'ID') {
    $city = trim((string) $city);
    $province = trim((string) $province);
    $country_code = strtoupper($country_code ?: 'ID');

    $address = array(
        '@type' => 'PostalAddress',
        'addressCountry' => $country_code,
    );

    if ($city !== '') {
        $address['addressLocality'] = $city;
    }

    if ($province !== '') {
        $address['addressRegion'] = $province;
    }

    $name = $city !== '' ? $city : ($province !== '' ? $province : $country_code);

    return array(
        '@type' => 'Place',
        'name' => $name,
        'address' => $address,
    );
}

/**
 * Build schema array for manual service area entry.
 */
function putrafiber_format_manual_service_area_entry($entry) {
    if (!is_array($entry)) {
        return null;
    }

    $type = isset($entry['type']) ? sanitize_text_field($entry['type']) : 'Place';
    $name = isset($entry['name']) ? sanitize_text_field($entry['name']) : '';

    if ($name === '') {
        return null;
    }

    $country_code = isset($entry['country_code']) ? strtoupper(sanitize_text_field($entry['country_code'])) : '';
    $identifier = isset($entry['identifier']) ? $entry['identifier'] : '';
    $note = isset($entry['note']) ? $entry['note'] : '';

    $item = array(
        '@type' => $type ?: 'Place',
        'name' => $name,
    );

    if ($type === 'Country' && $country_code !== '') {
        if (empty($identifier)) {
            $item['identifier'] = $country_code;
        }
    } elseif ($country_code !== '') {
        $item['addressCountry'] = $country_code;
    }

    if (!empty($identifier)) {
        $item['identifier'] = $identifier;
    }

    if (!empty($note)) {
        $item['description'] = $note;
    }

    return $item;
}

/**
 * ===================================================================
 * HELPER FUNCTION: Get Service Area (Auto-extract or Manual)
 * ===================================================================
 */
function putrafiber_get_service_area($post_id) {
    if (get_post_meta($post_id, '_enable_service_area', true) !== '1') {
        return array();
    }

    $areas = array();

    // Manual city/province selections
    $manual_city_province = get_post_meta($post_id, '_service_areas', true);
    if (is_array($manual_city_province)) {
        foreach ($manual_city_province as $entry) {
            $city = isset($entry['city']) ? $entry['city'] : '';
            $province = isset($entry['province']) ? $entry['province'] : '';

            if ($city === '' && $province === '') {
                continue;
            }

            $province_name = $province;
            if ($province_name === '' && !empty($city) && function_exists('putrafiber_get_province_from_city')) {
                $province_name = putrafiber_get_province_from_city($city);
            }

            $areas[] = putrafiber_format_service_area_place($city, $province_name);
        }
    }

    // Manual custom entries (Country, AdministrativeArea, etc)
    $manual_custom = get_post_meta($post_id, '_manual_service_areas', true);
    if (is_array($manual_custom)) {
        foreach ($manual_custom as $entry) {
            $formatted = putrafiber_format_manual_service_area_entry($entry);
            if (!empty($formatted)) {
                $areas[] = $formatted;
            }
        }
    }

    // Auto-extract from title when no manual entries
    if (empty($areas)) {
        $title = get_the_title($post_id);
        $extracted = putrafiber_extract_cities_from_text($title);

        if (!empty($extracted)) {
            foreach ($extracted as $city) {
                $province = function_exists('putrafiber_get_province_from_city')
                    ? putrafiber_get_province_from_city($city)
                    : '';
                $areas[] = putrafiber_format_service_area_place($city, $province);
            }
        }
    }

    // Fallback: legacy single field
    if (empty($areas)) {
        $single_area = get_post_meta($post_id, '_service_area', true);
        if ($single_area) {
            $areas[] = array(
                '@type' => 'Place',
                'name' => sanitize_text_field($single_area)
            );
        }
    }

    if (empty($areas)) {
        $areas[] = array(
            '@type' => 'Country',
            'name' => 'Indonesia',
            'identifier' => 'ID'
        );
    }

    return $areas;
}

/**
 * Extract Cities from Text (Auto-detection)
 */
function putrafiber_extract_cities_from_text($text) {
    // Common Indonesian cities and regions
    $cities = array(
        // Jabodetabek
        'Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi', 'Cikarang', 'Serpong', 'Bintaro',
        
        // Jawa Barat
        'Bandung', 'Cimahi', 'Sukabumi', 'Cirebon', 'Tasikmalaya', 'Garut', 'Purwakarta', 'Karawang',
        
        // Jawa Tengah
        'Semarang', 'Solo', 'Surakarta', 'Yogyakarta', 'Jogja', 'Magelang', 'Purwokerto', 'Tegal', 'Pekalongan', 'Salatiga',
        
        // Jawa Timur
        'Surabaya', 'Malang', 'Batu', 'Kediri', 'Madiun', 'Mojokerto', 'Pasuruan', 'Probolinggo', 'Blitar', 'Jember',
        
        // Bali & Nusa Tenggara
        'Denpasar', 'Bali', 'Kuta', 'Ubud', 'Sanur', 'Lombok', 'Mataram',
        
        // Sumatera
        'Medan', 'Palembang', 'Pekanbaru', 'Padang', 'Batam', 'Lampung', 'Bengkulu', 'Jambi', 'Aceh',
        
        // Kalimantan
        'Pontianak', 'Banjarmasin', 'Balikpapan', 'Samarinda', 'Palangkaraya',
        
        // Sulawesi
        'Makassar', 'Manado', 'Palu', 'Kendari', 'Gorontalo',
        
        // Maluku & Papua
        'Ambon', 'Ternate', 'Jayapura', 'Sorong', 'Manokwari'
    );
    
    $found_cities = array();
    
    foreach ($cities as $city) {
        // Case-insensitive search with word boundary
        if (preg_match('/\b' . preg_quote($city, '/') . '\b/i', $text)) {
            $found_cities[] = $city;
        }
    }
    
    // Remove duplicates and return
    return array_unique($found_cities);
}

/**
 * Legacy function for backward compatibility
 */
if (!function_exists('putrafiber_extract_city')) {
    function putrafiber_extract_city($text) {
    $cities = putrafiber_extract_cities_from_text($text);
    return !empty($cities) ? $cities[0] : null;
}
}