<?php
/**
 * Schema Advanced manager.
 *
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

if (!class_exists('PutraFiber_Schema_Manager')) {
    class PutraFiber_Schema_Manager
    {
        /**
         * Flag to ensure JSON-LD is rendered once.
         *
         * @var bool
         */
        protected static $did_render = false;

        /**
         * Cached context for previews.
         *
         * @var array
         */
        protected static $last_context = array();

        /**
         * Bootstrap hooks.
         *
         * @return void
         */
        public static function init()
        {
            if (!pf_schema_yes()) {
                return;
            }

            add_action('wp_head', array(__CLASS__, 'render'), 98);
        }

        /**
         * Render JSON-LD output when allowed.
         *
         * @return void
         */
        public static function render()
        {
            if (self::$did_render || is_admin()) {
                return;
            }

            if (!pf_schema_yes()) {
                return;
            }

            $context = self::build_context();

            if (self::should_skip($context)) {
                return;
            }

            if (!apply_filters('putrafiber_schema_advanced_should_render', true, $context)) {
                return;
            }

            $graph = self::generate_graph_from_context($context);
            if (empty($graph) || empty($graph['@graph'])) {
                return;
            }

            $encoded = wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $encoded = pf_schema_minify_json($encoded);

            if ($encoded === '') {
                return;
            }

            echo '<script type="application/ld+json">' . $encoded . '</script>' . "\n";

            self::$did_render   = true;
            self::$last_context = $context;
        }

        /**
         * Generate graph for previews or other consumers.
         *
         * @param int|null $post_id Optional post ID.
         * @return array
         */
        public static function generate_graph($post_id = null)
        {
            if (!pf_schema_yes()) {
                return array();
            }

            $context = self::build_context($post_id);
            $graph   = self::generate_graph_from_context($context);

            return $graph;
        }

        /**
         * Determine whether schema should be skipped.
         *
         * @param array $context Current context.
         * @return bool
         */
        protected static function should_skip(array $context)
        {
            if (apply_filters('putrafiber_schema_skip_common', false)) {
                return true;
            }

            $seo_plugins = array(
                'WPSEO_VERSION',
                'RANK_MATH_VERSION',
                'SEOPRESS_VERSION',
                'AIOSEO_VERSION',
            );

            foreach ($seo_plugins as $constant) {
                if (defined($constant)) {
                    return true;
                }
            }

            if (did_action('putrafiber_schema_legacy_rendered')) {
                return true;
            }

            return false;
        }

        /**
         * Build schema graph from provided context.
         *
         * @param array $context Prepared context data.
         * @return array
         */
        protected static function generate_graph_from_context(array $context)
        {
            $core = self::collect_core_graph($context);
            $page = self::collect_page_graph($context);
            $cta  = self::collect_cta_graph($context);

            $nodes = pf_schema_merge_graphs(array($core, $page, $cta));
            if (empty($nodes)) {
                return array();
            }

            $graph = array(
                '@context' => 'https://schema.org',
                '@graph'   => $nodes,
            );

            $graph = apply_filters('putrafiber_schema_graph', $graph, $context);

            if (!is_array($graph) || empty($graph['@graph'])) {
                return array();
            }

            self::$last_context = $context;

            return $graph;
        }

        /**
         * Prepare base context for schema generation.
         *
         * @param int|null $post_id Optional post ID override.
         * @return array
         */
        protected static function build_context($post_id = null)
        {
            $options = get_option('putrafiber_options', array());
            if (!is_array($options)) {
                $options = array();
            }

            $context = array(
                'options'          => $options,
                'site_name'        => get_bloginfo('name'),
                'site_description' => get_bloginfo('description'),
                'language'         => get_locale(),
                'organization_id'  => trailingslashit(home_url('/')) . '#organization',
                'website_id'       => trailingslashit(home_url('/')) . '#website',
                'permalink'        => home_url(add_query_arg(array(), isset($GLOBALS['wp']) && isset($GLOBALS['wp']->request) ? $GLOBALS['wp']->request : '')),
                'webpage_id'       => '',
                'post_id'          => 0,
                'post_type'        => '',
                'post'             => null,
                'page_title'       => '',
                'page_description' => '',
                'primary_image'    => '',
            );

            if ($post_id === null) {
                if (is_singular()) {
                    $post_id = get_queried_object_id();
                } elseif (is_front_page()) {
                    $front_page = (int) get_option('page_on_front');
                    if ($front_page > 0) {
                        $post_id = $front_page;
                    }
                }
            }

            if ($post_id) {
                $post = get_post($post_id);
                if ($post instanceof WP_Post) {
                    $context['post']      = $post;
                    $context['post_id']   = (int) $post->ID;
                    $context['post_type'] = $post->post_type;
                    $context['permalink'] = get_permalink($post);
                    $context['webpage_id'] = trailingslashit($context['permalink']) . '#webpage';
                    $context['page_title'] = get_the_title($post);

                    if (has_excerpt($post)) {
                        $context['page_description'] = wp_strip_all_tags(get_the_excerpt($post));
                    } else {
                        $context['page_description'] = wp_trim_words(wp_strip_all_tags($post->post_content), 32);
                    }

                    if (has_post_thumbnail($post)) {
                        $context['primary_image'] = pf_schema_sanitize_url(get_the_post_thumbnail_url($post, 'full'));
                    }
                }
            }

            if (empty($context['webpage_id'])) {
                $context['webpage_id'] = trailingslashit($context['permalink']) . '#webpage';
            }

            if ($context['page_title'] === '') {
                $context['page_title'] = wp_get_document_title();
            }

            if ($context['page_description'] === '') {
                $context['page_description'] = !empty($context['site_description']) ? $context['site_description'] : __('Informasi lebih lanjut tentang ' . $context['site_name'], 'putrafiber');
            }

            if ($context['primary_image'] === '' && !empty($options['og_image'])) {
                $context['primary_image'] = pf_schema_sanitize_url($options['og_image']);
            }

            if ($context['primary_image'] === '') {
                $logo_id  = get_theme_mod('custom_logo');
                $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
                if ($logo_url) {
                    $context['primary_image'] = pf_schema_sanitize_url($logo_url);
                }
            }

            // Build organization address.
            $address = array(
                '@type'          => 'PostalAddress',
                'addressCountry' => 'ID',
            );

            if (!empty($options['company_address'])) {
                $address['streetAddress'] = pf_schema_sanitize_text($options['company_address']);
            }
            if (!empty($options['company_city'])) {
                $address['addressLocality'] = pf_schema_sanitize_text($options['company_city']);
            }
            if (!empty($options['company_province'])) {
                $address['addressRegion'] = pf_schema_sanitize_text($options['company_province']);
            }
            if (!empty($options['company_postal_code'])) {
                $address['postalCode'] = pf_schema_sanitize_text($options['company_postal_code']);
            }

            $context['organization_address'] = count($address) > 2 ? $address : array();

            $geo = array();
            if (!empty($options['company_latitude']) && !empty($options['company_longitude'])) {
                $geo = array(
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => (float) $options['company_latitude'],
                    'longitude' => (float) $options['company_longitude'],
                );
            }
            $context['geo'] = $geo;

            if ($context['post_id']) {
                $context['service_area'] = pf_schema_detect_service_area($context['post_id']);
            } else {
                $context['service_area'] = array(
                    array(
                        '@type'      => 'Country',
                        'name'       => 'Indonesia',
                        'identifier' => 'ID',
                    ),
                );
            }

            $context['active_ctas']     = self::resolve_active_ctas($context);
            $context['primary_cta']     = PutraFiber_Schema_Registry::resolve_primary($context['active_ctas']);
            $context['primary_cta_key'] = !empty($context['primary_cta']) ? array_key_first($context['primary_cta']) : null;

            return $context;
        }

        /**
         * Collect core graph elements.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function collect_core_graph(array $context)
        {
            $options = $context['options'];
            $nodes   = array();

            $logo_id  = get_theme_mod('custom_logo');
            $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
            if (!$logo_url) {
                $fallback_logo = get_template_directory_uri() . '/assets/images/logo.png';
                $logo_url = apply_filters('putrafiber_schema_logo_fallback', $fallback_logo);
            }

            $organization = array(
                '@type'        => 'Organization',
                '@id'          => $context['organization_id'],
                'name'         => pf_schema_sanitize_text($context['site_name']),
                'url'          => home_url('/'),
                'description'  => pf_schema_sanitize_text($context['site_description']),
                'logo'         => array(
                    '@type' => 'ImageObject',
                    'url'   => pf_schema_sanitize_url($logo_url),
                ),
            );

            if (!empty($options['company_phone'])) {
                $organization['telephone'] = pf_schema_sanitize_text($options['company_phone']);
            }

            if (!empty($options['company_email'])) {
                $organization['email'] = sanitize_email($options['company_email']);
            }

            if (!empty($context['organization_address'])) {
                $organization['address'] = $context['organization_address'];
            }

            if (!empty($context['geo'])) {
                $organization['geo'] = $context['geo'];
            }

            $same_as = array();
            $social_keys = array('facebook_url', 'instagram_url', 'youtube_url', 'linkedin_url', 'twitter_url');
            foreach ($social_keys as $key) {
                if (!empty($options[$key])) {
                    $same_as[] = pf_schema_sanitize_url($options[$key]);
                }
            }
            if (!empty($same_as)) {
                $organization['sameAs'] = array_values(array_filter($same_as));
            }

            $nodes[] = array_filter($organization, array(__CLASS__, 'filter_nulls'));

            $website = array(
                '@type'      => 'WebSite',
                '@id'        => $context['website_id'],
                'name'       => pf_schema_sanitize_text($context['site_name']),
                'url'        => home_url('/'),
                'inLanguage' => $context['language'],
                'publisher'  => array('@id' => $context['organization_id']),
                'potentialAction' => array(
                    '@type'       => 'SearchAction',
                    'target'      => home_url('/?s={search_term_string}'),
                    'query-input' => 'required name=search_term_string',
                ),
            );
            $nodes[] = array_filter($website, array(__CLASS__, 'filter_nulls'));

            $header = array(
                '@type'        => 'WPHeader',
                '@id'          => $context['webpage_id'] . '#header',
                'name'         => pf_schema_sanitize_text($context['site_name'] . ' header'),
                'description'  => pf_schema_sanitize_text($context['site_description']),
                'publisher'    => array('@id' => $context['organization_id']),
            );
            $nodes[] = array_filter($header, array(__CLASS__, 'filter_nulls'));

            $nav_items = array();
            $locations = get_nav_menu_locations();
            if (isset($locations['primary'])) {
                $menu_items = wp_get_nav_menu_items($locations['primary']);
                if (!empty($menu_items) && !is_wp_error($menu_items)) {
                    foreach ($menu_items as $item) {
                        $nav_items[] = array_filter(array(
                            '@type' => 'SiteNavigationElement',
                            'name'  => pf_schema_sanitize_text($item->title),
                            'url'   => pf_schema_sanitize_url($item->url),
                        ), array(__CLASS__, 'filter_nulls'));
                    }
                }
            }

            $navigation = array(
                '@type'  => 'SiteNavigationElement',
                '@id'    => $context['webpage_id'] . '#navigation',
                'name'   => __('Primary Navigation', 'putrafiber'),
            );
            if (!empty($nav_items)) {
                $navigation['hasPart'] = $nav_items;
            }
            $nodes[] = array_filter($navigation, array(__CLASS__, 'filter_nulls'));

            $webpage = array(
                '@type'        => 'WebPage',
                '@id'          => $context['webpage_id'],
                'url'          => $context['permalink'],
                'name'         => pf_schema_sanitize_text($context['page_title']),
                'description'  => pf_schema_sanitize_text($context['page_description']),
                'isPartOf'     => array('@id' => $context['website_id']),
                'inLanguage'   => $context['language'],
                'about'        => array('@id' => $context['organization_id']),
            );

            if ($context['post_id']) {
                $webpage['datePublished'] = get_post_time('c', true, $context['post']);
                $webpage['dateModified']  = get_post_modified_time('c', true, $context['post']);
            }

            if (!empty($context['primary_image'])) {
                $webpage['primaryImageOfPage'] = array(
                    '@type' => 'ImageObject',
                    'url'   => $context['primary_image'],
                );
            }

            $nodes[] = array_filter($webpage, array(__CLASS__, 'filter_nulls'));

            return $nodes;
        }

        /**
         * Collect page specific graph elements.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function collect_page_graph(array $context)
        {
            $nodes = array();
            $post  = isset($context['post']) ? $context['post'] : null;

            if ($post instanceof WP_Post) {
                switch ($context['post_type']) {
                    case 'post':
                        $article = self::build_article_node($context);
                        if (!empty($article)) {
                            $nodes[] = $article;
                        }
                        break;
                    case 'product':
                        $product = self::build_product_node($context);
                        if (!empty($product)) {
                            $nodes[] = $product;
                        }
                        break;
                    case 'portfolio':
                        $portfolio_nodes = self::build_portfolio_nodes($context);
                        if (!empty($portfolio_nodes)) {
                            $nodes = array_merge($nodes, $portfolio_nodes);
                        }
                        break;
                    default:
                        break;
                }
            }

            $local_business = self::build_local_business_node($context);
            if (!empty($local_business)) {
                $nodes[] = $local_business;
            }

            $breadcrumb = self::build_breadcrumb_node($context);
            if (!empty($breadcrumb)) {
                $nodes[] = $breadcrumb;
            }

            return $nodes;
        }

        /**
         * Collect CTA graph fragments.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function collect_cta_graph(array $context)
        {
            if (empty($context['active_ctas'])) {
                return array();
            }

            $map     = PutraFiber_Schema_Registry::cta_map();
            $graphs  = array();
            $primary = !empty($context['primary_cta_key']) ? $context['primary_cta_key'] : null;

            foreach ($context['active_ctas'] as $key => $cta) {
                if (!isset($map[$key]) || !is_callable($map[$key])) {
                    continue;
                }

                $subset = call_user_func($map[$key], $context, $cta, $primary === $key);

                if (empty($subset)) {
                    continue;
                }

                if (isset($subset['@type'])) {
                    $graphs[] = $subset;
                    continue;
                }

                foreach ((array) $subset as $node) {
                    if (is_array($node) && !empty($node)) {
                        $graphs[] = $node;
                    }
                }
            }

            return $graphs;
        }

        /**
         * Build Article schema node.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function build_article_node(array $context)
        {
            $post = $context['post'];
            if (!$post instanceof WP_Post) {
                return array();
            }

            $author_id = $post->post_author;
            $author    = get_userdata($author_id);

            $article = array(
                '@type'            => 'Article',
                '@id'              => $context['webpage_id'] . '#article',
                'headline'         => pf_schema_sanitize_text(get_the_title($post)),
                'description'      => pf_schema_sanitize_text($context['page_description']),
                'datePublished'    => get_post_time('c', true, $post),
                'dateModified'     => get_post_modified_time('c', true, $post),
                'mainEntityOfPage' => array('@id' => $context['webpage_id']),
                'author'           => array(
                    '@type' => 'Person',
                    'name'  => $author ? pf_schema_sanitize_text($author->display_name) : pf_schema_sanitize_text(get_bloginfo('name')),
                ),
                'publisher'        => array('@id' => $context['organization_id']),
            );

            if (!empty($context['primary_image'])) {
                $article['image'] = $context['primary_image'];
            }

            return array_filter($article, array(__CLASS__, 'filter_nulls'));
        }

        /**
         * Build Product schema node.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function build_product_node(array $context)
        {
            $post = $context['post'];
            if (!$post instanceof WP_Post) {
                return array();
            }

            $product_id = $context['post_id'];
            $options    = $context['options'];

            $price_raw  = get_post_meta($product_id, '_product_price', true);
            $price      = pf_schema_sanitize_price($price_raw);
            $stock_meta = get_post_meta($product_id, '_product_stock', true);
            $availability = pf_schema_availability($stock_meta);
            $sku = get_post_meta($product_id, '_product_sku', true);
            $brand = !empty($options['company_name']) ? pf_schema_sanitize_text($options['company_name']) : pf_schema_sanitize_text(get_bloginfo('name'));

            $offer = array(
                '@type'         => 'Offer',
                '@id'           => $context['webpage_id'] . '#offer',
                'priceCurrency' => 'IDR',
                'price'         => $price,
                'availability'  => 'https://schema.org/' . $availability,
                'url'           => get_permalink($product_id),
            );

            if (!empty($context['primary_cta']) && isset($context['primary_cta']['wa_primary'])) {
                $primary = $context['primary_cta']['wa_primary'];
                if (!empty($primary['number'])) {
                    $offer['potentialAction'] = pf_schema_build_contact_action($primary['number'], isset($primary['utm']) ? (array) $primary['utm'] : array());
                }
            }

            $product = array(
                '@type' => 'Product',
                '@id'   => $context['webpage_id'] . '#product',
                'name'  => pf_schema_sanitize_text(get_the_title($post)),
                'description' => pf_schema_sanitize_text($context['page_description']),
                'sku'   => $sku ? pf_schema_sanitize_text($sku) : null,
                'brand' => array(
                    '@type' => 'Brand',
                    'name'  => $brand,
                ),
                'offers' => $offer,
            );

            if (!empty($context['primary_image'])) {
                $product['image'] = $context['primary_image'];
            }

            $categories = wp_get_post_terms($product_id, 'product_category');
            if (!empty($categories) && !is_wp_error($categories)) {
                $product['category'] = array_map(function ($term) {
                    return pf_schema_sanitize_text($term->name);
                }, $categories);
            }

            if (!empty($context['service_area'])) {
                $product['areaServed'] = $context['service_area'];
            }

            return array_filter($product, array(__CLASS__, 'filter_nulls'));
        }

        /**
         * Build Portfolio schema nodes.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function build_portfolio_nodes(array $context)
        {
            $post = $context['post'];
            if (!$post instanceof WP_Post) {
                return array();
            }

            $nodes = array();
            $portfolio_id = $context['post_id'];
            $is_tourist = get_post_meta($portfolio_id, '_enable_tourist_schema', true) === '1';

            if ($is_tourist) {
                $node = array(
                    '@type' => 'TouristAttraction',
                    '@id'   => $context['webpage_id'] . '#tourist-attraction',
                    'name'  => pf_schema_sanitize_text(get_the_title($post)),
                    'url'   => get_permalink($portfolio_id),
                    'description' => pf_schema_sanitize_text($context['page_description']),
                    'isPartOf' => array('@id' => $context['webpage_id']),
                );

                if (!empty($context['service_area'])) {
                    $node['areaServed'] = $context['service_area'];
                }

                $nodes[] = array_filter($node, array(__CLASS__, 'filter_nulls'));
            } else {
                $node = array(
                    '@type' => 'CreativeWork',
                    '@id'   => $context['webpage_id'] . '#creative-work',
                    'name'  => pf_schema_sanitize_text(get_the_title($post)),
                    'url'   => get_permalink($portfolio_id),
                    'description' => pf_schema_sanitize_text($context['page_description']),
                    'creator' => array('@id' => $context['organization_id']),
                );

                if (!empty($context['primary_image'])) {
                    $node['image'] = $context['primary_image'];
                }

                $nodes[] = array_filter($node, array(__CLASS__, 'filter_nulls'));
            }

            return $nodes;
        }

        /**
         * Build LocalBusiness schema when enabled.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function build_local_business_node(array $context)
        {
            $options = $context['options'];

            if (empty($options['enable_localbusiness']) || $options['enable_localbusiness'] !== '1') {
                return array();
            }

            $post_id = $context['post_id'];
            $allowed_pages = isset($options['localbusiness_pages']) ? (array) $options['localbusiness_pages'] : array();
            $display_mode = isset($options['localbusiness_display_mode']) ? $options['localbusiness_display_mode'] : 'all'; // 'all' or 'selected'

            $is_on_allowed_page = false;
            if ($display_mode === 'all') {
                $is_on_allowed_page = true;
            } else { // $display_mode === 'selected'
                // If no pages are selected in theme options, do not output the schema.
                if (empty($allowed_pages)) {
                    return array();
                }

                if (is_front_page()) {
                    if (in_array('homepage', $allowed_pages, true)) {
                        $is_on_allowed_page = true;
                    }
                } elseif ($post_id) {
                    if (in_array((string) $post_id, $allowed_pages, true)) {
                        $is_on_allowed_page = true;
                    }
                }
            }

            if (!$is_on_allowed_page) {
                return array();
            }
            $business_type = !empty($options['business_type']) ? pf_schema_sanitize_text($options['business_type']) : 'LocalBusiness';

            $local_business = array(
                '@type' => $business_type,
                '@id'   => $context['webpage_id'] . '#local-business',
                'name'  => !empty($options['company_name']) ? pf_schema_sanitize_text($options['company_name']) : pf_schema_sanitize_text($context['site_name']),
                'url'   => $context['permalink'],
                'telephone' => !empty($options['company_phone']) ? pf_schema_sanitize_text($options['company_phone']) : null,
                'email'     => !empty($options['company_email']) ? sanitize_email($options['company_email']) : null,
                'priceRange'=> !empty($options['price_range']) ? pf_schema_sanitize_text($options['price_range']) : null,
                'parentOrganization' => array('@id' => $context['organization_id']),
            );

            if (!empty($context['organization_address'])) {
                $local_business['address'] = $context['organization_address'];
            }

            if (!empty($context['geo'])) {
                $local_business['geo'] = $context['geo'];
            }

            if (!empty($context['service_area'])) {
                $local_business['areaServed'] = $context['service_area'];
            }

            return array_filter($local_business, array(__CLASS__, 'filter_nulls'));
        }

        /**
         * Build breadcrumb node.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function build_breadcrumb_node(array $context)
        {
            $items = array();
            $position = 1;

            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => 'Home',
                'item'     => home_url('/'),
            );

            // Handle Post Type Archives
            if (is_post_type_archive()) {
                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => post_type_archive_title('', false),
                );
            }

            $post = isset($context['post']) ? $context['post'] : null;
            if (is_singular()) { // Handle singular pages
                if ($context['post_type'] === 'product') {
                    $product_post_type = get_post_type_object('product');
                    $items[] = array(
                        '@type'    => 'ListItem',
                        'position' => $position++,
                        'name'     => $product_post_type ? $product_post_type->labels->name : __('Produk', 'putrafiber'),
                        'item'     => get_post_type_archive_link('product'),
                    );

                    $terms = wp_get_post_terms($post->ID, 'product_category');
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $link = get_term_link($terms[0]);
                        if (!is_wp_error($link)) {
                            $items[] = array(
                                '@type'    => 'ListItem',
                                'position' => $position++,
                                'name'     => pf_schema_sanitize_text($terms[0]->name),
                                'item'     => $link,
                            );
                        }
                    }
                } elseif ($context['post_type'] === 'post') {
                    $categories = get_the_category($post->ID);
                    if (!empty($categories)) {
                        $link = get_category_link($categories[0]->term_id);
                        if (!is_wp_error($link)) {
                            $items[] = array(
                                '@type'    => 'ListItem',
                                'position' => $position++,
                                'name'     => pf_schema_sanitize_text($categories[0]->name),
                                'item'     => $link,
                            );
                        }
                    }
                } elseif ($context['post_type'] === 'portfolio') {
                    $portfolio_post_type = get_post_type_object('portfolio');
                    if ($portfolio_post_type) {
                        $items[] = array(
                            '@type'    => 'ListItem',
                            'position' => $position++,
                            'name'     => $portfolio_post_type->labels->name,
                            'item'     => get_post_type_archive_link('portfolio'),
                        );
                    }
                    // Catatan: Bisa ditambahkan logika untuk taksonomi portofolio di sini jika ada.
                }

                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => pf_schema_sanitize_text(get_the_title($post)),
                    'item'     => get_permalink($post),
                );
            } elseif (is_category() || is_tag() || is_tax()) { // Handle taxonomy archives
                $term_archive_post_type = get_queried_object()->taxonomy === 'product_category' ? 'product' : 'post';
                $post_type_obj = get_post_type_object($term_archive_post_type);
                if ($post_type_obj) {
                    $archive_link = get_post_type_archive_link($term_archive_post_type);
                    if ($archive_link) {
                        $items[] = array(
                            '@type'    => 'ListItem',
                            'position' => $position++,
                            'name'     => $post_type_obj->labels->name,
                            'item'     => $archive_link,
                        );
                    }
                }

                $term = get_queried_object();
                if ($term) {
                    if ($term->parent) {
                        $ancestors = get_ancestors($term->term_id, $term->taxonomy);
                        $ancestors = array_reverse($ancestors);
                        foreach ($ancestors as $ancestor_id) {
                            $ancestor = get_term($ancestor_id, $term->taxonomy);
                            if ($ancestor && !is_wp_error($ancestor)) {
                                $items[] = array(
                                    '@type'    => 'ListItem',
                                    'position' => $position++,
                                    'name'     => pf_schema_sanitize_text($ancestor->name),
                                    'item'     => get_term_link($ancestor),
                                );
                            }
                        }
                    }
                    $items[] = array(
                        '@type'    => 'ListItem',
                        'position' => $position++,
                        'name'     => pf_schema_sanitize_text($term->name),
                        'item'     => get_term_link($term),
                    );
                }
            }

            if (count($items) <= 1) {
                return array();
            }

            $breadcrumb = array(
                '@type'           => 'BreadcrumbList',
                '@id'             => $context['webpage_id'] . '#breadcrumb',
                'itemListElement' => $items,
            );

            return $breadcrumb;
        }

        /**
         * Resolve active CTA collection based on options and meta.
         *
         * @param array $context Schema context.
         * @return array
         */
        protected static function resolve_active_ctas(array $context)
        {
            $options   = $context['options'];
            $permalink = $context['permalink'];
            $title     = pf_schema_sanitize_text($context['page_title']);
            $ctas      = array();

            $wa_number = '';
            if (function_exists('putrafiber_whatsapp_number')) {
                $wa_number = putrafiber_whatsapp_number();
            } elseif (!empty($options['whatsapp_number'])) {
                $wa_number = preg_replace('/[^0-9]/', '', $options['whatsapp_number']);
                if (strpos($wa_number, '0') === 0) {
                    $wa_number = '62' . substr($wa_number, 1);
                }
            }

            if ($wa_number !== '') {
                $message = sprintf(__('Halo %s, saya tertarik dengan %s', 'putrafiber'), pf_schema_sanitize_text($context['site_name']), $title);
                $ctas['wa_primary'] = array(
                    'number' => $wa_number,
                    'label'  => !empty($options['front_cta_primary_text']) ? pf_schema_sanitize_text($options['front_cta_primary_text']) : __('Konsultasi Sekarang', 'putrafiber'),
                    'message'=> $message,
                    'utm'    => array(
                        'utm_source' => 'schema',
                        'utm_medium' => 'cta',
                        'utm_campaign' => 'whatsapp-primary',
                        'text' => $message,
                    ),
                    'url'    => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $wa_number),
                );
            }

            $primary_url = !empty($options['front_cta_primary_url']) ? pf_schema_sanitize_url($options['front_cta_primary_url']) : '';
            if ($primary_url !== '') {
                if (self::is_whatsapp_url($primary_url)) {
                    $number = self::extract_wa_number($primary_url);
                    if ($number) {
                        if (!isset($ctas['wa_primary'])) {
                            $message = sprintf(__('Halo %s, saya tertarik dengan %s', 'putrafiber'), pf_schema_sanitize_text($context['site_name']), $title);
                            $ctas['wa_primary'] = array(
                                'number' => $number,
                                'label'  => !empty($options['front_cta_primary_text']) ? pf_schema_sanitize_text($options['front_cta_primary_text']) : __('Hubungi Kami', 'putrafiber'),
                                'message'=> $message,
                                'utm'    => array(
                                    'utm_source' => 'schema',
                                    'utm_medium' => 'cta',
                                    'utm_campaign' => 'whatsapp-primary',
                                    'text' => $message,
                                ),
                                'url'    => $primary_url,
                            );
                        } elseif ($number !== $ctas['wa_primary']['number']) {
                            $ctas['wa_secondary'] = array(
                                'number' => $number,
                                'label'  => !empty($options['hero_secondary_cta']) ? pf_schema_sanitize_text($options['hero_secondary_cta']) : __('Hubungi Kami', 'putrafiber'),
                                'utm'    => array(
                                    'utm_source' => 'schema',
                                    'utm_medium' => 'cta',
                                    'utm_campaign' => 'whatsapp-secondary',
                                ),
                                'url'    => $primary_url,
                            );
                        }
                    }
                } else {
                    $ctas['katalog_hubungi_cs'] = array(
                        'url'   => $primary_url,
                        'label' => !empty($options['front_cta_primary_text']) ? pf_schema_sanitize_text($options['front_cta_primary_text']) : __('Hubungi CS', 'putrafiber'),
                    );
                }
            }

            $secondary_url = !empty($options['front_cta_secondary_url']) ? pf_schema_sanitize_url($options['front_cta_secondary_url']) : '';
            if ($secondary_url !== '') {
                $ctas['download_brosur'] = array(
                    'url'   => $secondary_url,
                    'label' => !empty($options['front_cta_secondary_text']) ? pf_schema_sanitize_text($options['front_cta_secondary_text']) : __('Download Brosur', 'putrafiber'),
                );
            }

            if ($context['post_type'] === 'product' && $context['post_id']) {
                $catalog_pdf = get_post_meta($context['post_id'], '_product_catalog_pdf', true);
                if (!empty($catalog_pdf)) {
                    $ctas['download_brosur'] = array(
                        'url'   => pf_schema_sanitize_url($catalog_pdf),
                        'label' => __('Download Brosur Produk', 'putrafiber'),
                    );
                }
            }

            if (!empty($options['google_maps_embed'])) {
                $map_url = self::extract_map_url($options['google_maps_embed']);
                if ($map_url) {
                    $ctas['kunjungi_lokasi'] = array(
                        'url'   => $map_url,
                        'label' => __('Kunjungi Lokasi', 'putrafiber'),
                    );
                }
            }

            if ($context['post_type'] === 'portfolio' && $context['post_id']) {
                if (get_post_meta($context['post_id'], '_enable_tourist_schema', true) === '1') {
                    $ctas['portfolio_wisata'] = array(
                        'permalink' => $permalink,
                        'title'     => get_the_title($context['post_id']),
                        'area'      => pf_schema_detect_service_area($context['post_id']),
                    );
                }
            }

            return $ctas;
        }

        /**
         * Determine if URL is a WhatsApp endpoint.
         *
         * @param string $url URL value.
         * @return bool
         */
        protected static function is_whatsapp_url($url)
        {
            return (bool) preg_match('/(wa\.me|api\.whatsapp\.com)/i', $url);
        }

        /**
         * Extract WhatsApp number from URL.
         *
         * @param string $url WhatsApp URL.
         * @return string
         */
        protected static function extract_wa_number($url)
        {
            if (preg_match('/wa\.me\/([0-9]+)/i', $url, $matches)) {
                return $matches[1];
            }

            if (preg_match('/phone=([0-9]+)/i', $url, $matches)) {
                return $matches[1];
            }

            return '';
        }

        /**
         * Extract usable map URL from embed code.
         *
         * @param string $embed Embed code or URL.
         * @return string
         */
        protected static function extract_map_url($embed)
        {
            if (strpos($embed, '<iframe') !== false && preg_match('/src="([^"]+)"/i', $embed, $matches)) {
                return pf_schema_sanitize_url(htmlspecialchars_decode($matches[1]));
            }

            return pf_schema_sanitize_url($embed);
        }

        /**
         * Helper to remove empty values.
         *
         * @param mixed $value Value to test.
         * @return bool
         */
        protected static function filter_nulls($value)
        {
            if (is_array($value)) {
                return !empty($value);
            }

            return $value !== null && $value !== '';
        }
    }
}
