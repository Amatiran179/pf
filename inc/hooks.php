<?php
/**
 * Custom Hooks & Filters
 * 
 * Define custom actions and filters that can be used throughout the theme
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * ============================================================================
 * CUSTOM ACTION HOOKS
 * ============================================================================
 */

/**
 * Hook: Before Header
 * 
 * Fires before the header section
 * Usage: add_action('putrafiber_before_header', 'your_function');
 */
function putrafiber_before_header() {
    do_action('putrafiber_before_header');
}

/**
 * Hook: After Header
 * 
 * Fires after the header section
 */
function putrafiber_after_header() {
    do_action('putrafiber_after_header');
}

/**
 * Hook: Before Content
 * 
 * Fires before the main content area
 */
function putrafiber_before_content() {
    do_action('putrafiber_before_content');
}

/**
 * Hook: After Content
 * 
 * Fires after the main content area
 */
function putrafiber_after_content() {
    do_action('putrafiber_after_content');
}

/**
 * Hook: Before Footer
 * 
 * Fires before the footer section
 */
function putrafiber_before_footer() {
    do_action('putrafiber_before_footer');
}

/**
 * Hook: After Footer
 * 
 * Fires after the footer section
 */
function putrafiber_after_footer() {
    do_action('putrafiber_after_footer');
}

/**
 * Hook: Before Post Content
 * 
 * Fires before single post content
 */
function putrafiber_before_post_content() {
    do_action('putrafiber_before_post_content');
}

/**
 * Hook: After Post Content
 * 
 * Fires after single post content
 */
function putrafiber_after_post_content() {
    do_action('putrafiber_after_post_content');
}

/**
 * Hook: Portfolio Before Content
 * 
 * Fires before portfolio content
 */
function putrafiber_portfolio_before_content() {
    do_action('putrafiber_portfolio_before_content');
}

/**
 * Hook: Portfolio After Content
 * 
 * Fires after portfolio content
 */
function putrafiber_portfolio_after_content() {
    do_action('putrafiber_portfolio_after_content');
}

/**
 * ============================================================================
 * CUSTOM FILTER HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Excerpt Length
 * 
 * @param int $length Default excerpt length
 * @return int Modified length
 */
function putrafiber_custom_excerpt_length($length) {
    return apply_filters('putrafiber_excerpt_length', $length);
}
add_filter('excerpt_length', 'putrafiber_custom_excerpt_length', 999);

/**
 * Filter: Modify Excerpt More Text
 * 
 * @param string $more Default more text
 * @return string Modified text
 */
function putrafiber_custom_excerpt_more($more) {
    return apply_filters('putrafiber_excerpt_more', $more);
}
add_filter('excerpt_more', 'putrafiber_custom_excerpt_more');

/**
 * Filter: Modify Post Classes
 * 
 * @param array $classes Post classes
 * @return array Modified classes
 */
function putrafiber_custom_post_classes($classes) {
    return apply_filters('putrafiber_post_classes', $classes);
}
add_filter('post_class', 'putrafiber_custom_post_classes');

/**
 * Filter: Modify Body Classes
 * 
 * @param array $classes Body classes
 * @return array Modified classes
 */
function putrafiber_custom_body_classes($classes) {
    return apply_filters('putrafiber_body_classes', $classes);
}
add_filter('body_class', 'putrafiber_custom_body_classes');

/**
 * Filter: Modify Archive Title
 * 
 * @param string $title Archive title
 * @return string Modified title
 */
function putrafiber_custom_archive_title($title) {
    return apply_filters('putrafiber_archive_title', $title);
}
add_filter('get_the_archive_title', 'putrafiber_custom_archive_title');

/**
 * Filter: Modify Portfolio Query
 * 
 * @param WP_Query $query Main query object
 * @return WP_Query Modified query
 */
function putrafiber_modify_portfolio_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('portfolio')) {
        $posts_per_page = apply_filters('putrafiber_portfolio_per_page', 12);
        $query->set('posts_per_page', $posts_per_page);
    }
    return $query;
}
add_action('pre_get_posts', 'putrafiber_modify_portfolio_query');

/**
 * ============================================================================
 * CONTENT MODIFICATION HOOKS
 * ============================================================================
 */

/**
 * Add Custom Content Before Post
 * 
 * @param string $content Post content
 * @return string Modified content
 */
function putrafiber_add_before_post_content($content) {
    if (is_singular('post')) {
        $before_content = apply_filters('putrafiber_before_single_post_content', '');
        $content = $before_content . $content;
    }
    return $content;
}
add_filter('the_content', 'putrafiber_add_before_post_content', 5);

/**
 * Add Custom Content After Post
 * 
 * @param string $content Post content
 * @return string Modified content
 */
function putrafiber_add_after_post_content($content) {
    if (is_singular('post')) {
        $after_content = apply_filters('putrafiber_after_single_post_content', '');
        $content = $content . $after_content;
    }
    return $content;
}
add_filter('the_content', 'putrafiber_add_after_post_content', 15);

/**
 * ============================================================================
 * CUSTOM FILTERS FOR THEME OPTIONS
 * ============================================================================
 */

/**
 * Filter: WhatsApp Number
 * 
 * Allows modification of WhatsApp number
 * 
 * @param string $number WhatsApp number
 * @return string Modified number
 */
function putrafiber_filter_whatsapp_number($number) {
    return apply_filters('putrafiber_whatsapp_number', $number);
}

/**
 * Filter: Company Info
 * 
 * Allows modification of company information
 * 
 * @param array $info Company info array
 * @return array Modified info
 */
function putrafiber_filter_company_info($info) {
    return apply_filters('putrafiber_company_info', $info);
}

/**
 * ============================================================================
 * NAVIGATION HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Nav Menu Items
 * 
 * @param array $items Menu items
 * @param object $args Menu args
 * @return array Modified items
 */
function putrafiber_modify_nav_menu_items($items, $args) {
    return apply_filters('putrafiber_nav_menu_items', $items, $args);
}
add_filter('wp_nav_menu_items', 'putrafiber_modify_nav_menu_items', 10, 2);

/**
 * Filter: Add Custom Classes to Nav Menu Items
 * 
 * @param array $classes CSS classes
 * @param object $item Menu item
 * @param object $args Menu args
 * @return array Modified classes
 */
function putrafiber_nav_menu_item_classes($classes, $item, $args) {
    // Add custom class to menu items
    if (isset($args->theme_location)) {
        $classes[] = 'menu-item-' . $args->theme_location;
    }
    
    // Add icon class for specific menu items
    if (in_array('menu-item-has-children', $classes)) {
        $classes[] = 'has-dropdown';
    }
    
    return apply_filters('putrafiber_nav_menu_item_classes', $classes, $item, $args);
}
add_filter('nav_menu_css_class', 'putrafiber_nav_menu_item_classes', 10, 3);

/**
 * ============================================================================
 * IMAGE HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Image Sizes
 * 
 * @param array $sizes Image sizes
 * @return array Modified sizes
 */
function putrafiber_custom_image_sizes($sizes) {
    return apply_filters('putrafiber_image_sizes', $sizes);
}
add_filter('intermediate_image_sizes_advanced', 'putrafiber_custom_image_sizes');

/**
 * Filter: Add Custom Image Size Names
 * 
 * @param array $sizes Image size names
 * @return array Modified names
 */
function putrafiber_custom_image_size_names($sizes) {
    $custom_sizes = array(
        'putrafiber-hero' => __('Hero Image', 'putrafiber'),
        'putrafiber-portfolio' => __('Portfolio Image', 'putrafiber'),
        'putrafiber-product' => __('Product Image', 'putrafiber'),
        'putrafiber-thumb' => __('Thumbnail', 'putrafiber'),
    );
    
    return array_merge($sizes, apply_filters('putrafiber_image_size_names', $custom_sizes));
}
add_filter('image_size_names_choose', 'putrafiber_custom_image_size_names');

/**
 * ============================================================================
 * COMMENT HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Comment Form Fields
 * 
 * @param array $fields Comment fields
 * @return array Modified fields
 */
function putrafiber_modify_comment_form_fields($fields) {
    // Add custom classes to comment fields
    if (isset($fields['author'])) {
        $fields['author'] = str_replace(
            '<input',
            '<input class="form-control"',
            $fields['author']
        );
    }
    
    if (isset($fields['email'])) {
        $fields['email'] = str_replace(
            '<input',
            '<input class="form-control"',
            $fields['email']
        );
    }
    
    if (isset($fields['url'])) {
        $fields['url'] = str_replace(
            '<input',
            '<input class="form-control"',
            $fields['url']
        );
    }
    
    return apply_filters('putrafiber_comment_form_fields', $fields);
}
add_filter('comment_form_default_fields', 'putrafiber_modify_comment_form_fields');

/**
 * Filter: Modify Comment Form Textarea
 * 
 * @param string $comment_field Comment field HTML
 * @return string Modified HTML
 */
function putrafiber_modify_comment_form_textarea($comment_field) {
    $comment_field = str_replace(
        '<textarea',
        '<textarea class="form-control"',
        $comment_field
    );
    
    return apply_filters('putrafiber_comment_form_textarea', $comment_field);
}
add_filter('comment_form_field_comment', 'putrafiber_modify_comment_form_textarea');

/**
 * ============================================================================
 * WIDGET HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Widget Title
 * 
 * @param string $title Widget title
 * @return string Modified title
 */
function putrafiber_modify_widget_title($title) {
    if (empty($title)) {
        return $title;
    }
    
    // Add icon before title (optional)
    $icon = apply_filters('putrafiber_widget_title_icon', '');
    
    return apply_filters('putrafiber_widget_title', $icon . $title);
}
add_filter('widget_title', 'putrafiber_modify_widget_title');

/**
 * ============================================================================
 * SEARCH HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Search Query
 * 
 * @param WP_Query $query Search query
 * @return WP_Query Modified query
 */
function putrafiber_modify_search_query($query) {
    if (!is_admin() && $query->is_search() && $query->is_main_query()) {
        // Include custom post types in search
        $post_types = apply_filters('putrafiber_search_post_types', array('post', 'page', 'portfolio'));
        $query->set('post_type', $post_types);
        
        // Set posts per page
        $posts_per_page = apply_filters('putrafiber_search_posts_per_page', 10);
        $query->set('posts_per_page', $posts_per_page);
    }
    return $query;
}
add_action('pre_get_posts', 'putrafiber_modify_search_query');

/**
 * ============================================================================
 * PAGINATION HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Pagination Args
 * 
 * @param array $args Pagination arguments
 * @return array Modified args
 */
function putrafiber_modify_pagination_args($args) {
    $custom_args = array(
        'mid_size' => 2,
        'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> ' . __('Previous', 'putrafiber'),
        'next_text' => __('Next', 'putrafiber') . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
        'type' => 'list',
        'class' => 'pagination',
    );
    
    return apply_filters('putrafiber_pagination_args', array_merge($args, $custom_args));
}

/**
 * ============================================================================
 * BREADCRUMB HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Breadcrumb Items
 * 
 * @param array $items Breadcrumb items
 * @return array Modified items
 */
function putrafiber_modify_breadcrumb_items($items) {
    return apply_filters('putrafiber_breadcrumb_items', $items);
}

/**
 * Filter: Breadcrumb Separator
 * 
 * @param string $separator Separator string
 * @return string Modified separator
 */
function putrafiber_breadcrumb_separator($separator = ' / ') {
    return apply_filters('putrafiber_breadcrumb_separator', $separator);
}

/**
 * ============================================================================
 * CUSTOM POST TYPE HOOKS
 * ============================================================================
 */

/**
 * Filter: Portfolio Query Args
 * 
 * @param array $args Query arguments
 * @return array Modified args
 */
function putrafiber_portfolio_query_args($args) {
    $default_args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => 12,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    return apply_filters('putrafiber_portfolio_query_args', array_merge($default_args, $args));
}

/**
 * Action: After Portfolio Save
 * 
 * @param int $post_id Portfolio post ID
 */
function putrafiber_after_portfolio_save($post_id) {
    do_action('putrafiber_after_portfolio_save', $post_id);
}
add_action('save_post_portfolio', 'putrafiber_after_portfolio_save');

/**
 * ============================================================================
 * SCHEMA HOOKS
 * ============================================================================
 */

/**
 * Filter: Modify Organization Schema
 * 
 * @param array $schema Organization schema
 * @return array Modified schema
 */
function putrafiber_modify_organization_schema($schema) {
    return apply_filters('putrafiber_organization_schema', $schema);
}

/**
 * Filter: Modify Product Schema
 * 
 * @param array $schema Product schema
 * @param int $post_id Post ID
 * @return array Modified schema
 */
function putrafiber_modify_product_schema($schema, $post_id) {
    return apply_filters('putrafiber_product_schema', $schema, $post_id);
}

/**
 * Filter: Modify Article Schema
 * 
 * @param array $schema Article schema
 * @param int $post_id Post ID
 * @return array Modified schema
 */
function putrafiber_modify_article_schema($schema, $post_id) {
    return apply_filters('putrafiber_article_schema', $schema, $post_id);
}

/**
 * ============================================================================
 * PERFORMANCE HOOKS
 * ============================================================================
 */

/**
 * Filter: Lazy Load Threshold
 * 
 * @param int $threshold Scroll threshold
 * @return int Modified threshold
 */
function putrafiber_lazy_load_threshold($threshold = 300) {
    return apply_filters('putrafiber_lazy_load_threshold', $threshold);
}

/**
 * Filter: WebP Quality
 * 
 * @param int $quality Image quality (0-100)
 * @return int Modified quality
 */
function putrafiber_webp_quality($quality = 85) {
    return apply_filters('putrafiber_webp_quality', $quality);
}

/**
 * ============================================================================
 * SOCIAL SHARING HOOKS
 * ============================================================================
 */

/**
 * Filter: Social Share Buttons
 * 
 * @param array $buttons Social buttons
 * @return array Modified buttons
 */
function putrafiber_social_share_buttons($buttons) {
    $default_buttons = array(
        'facebook' => array(
            'name' => 'Facebook',
            'icon' => '<svg>...</svg>',
            'url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
        ),
        'twitter' => array(
            'name' => 'Twitter',
            'icon' => '<svg>...</svg>',
            'url' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
        ),
        'whatsapp' => array(
            'name' => 'WhatsApp',
            'icon' => '<svg>...</svg>',
            'url' => 'https://wa.me/?text={title} {url}',
        ),
        'linkedin' => array(
            'name' => 'LinkedIn',
            'icon' => '<svg>...</svg>',
            'url' => 'https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}',
        ),
    );
    
    return apply_filters('putrafiber_social_share_buttons', array_merge($default_buttons, $buttons));
}

/**
 * ============================================================================
 * EMAIL HOOKS
 * ============================================================================
 */

/**
 * Filter: Contact Form Email
 * 
 * @param string $email Email address
 * @return string Modified email
 */
function putrafiber_contact_email($email) {
    $default_email = putrafiber_get_option('company_email', get_option('admin_email'));
    return apply_filters('putrafiber_contact_email', $email ?: $default_email);
}

/**
 * Filter: Email From Name
 * 
 * @param string $name From name
 * @return string Modified name
 */
function putrafiber_email_from_name($name) {
    return apply_filters('putrafiber_email_from_name', $name ?: get_bloginfo('name'));
}
add_filter('wp_mail_from_name', 'putrafiber_email_from_name');

/**
 * Filter: Email From Address
 * 
 * @param string $email From email
 * @return string Modified email
 */
function putrafiber_email_from_address($email) {
    $default = 'noreply@' . parse_url(home_url(), PHP_URL_HOST);
    return apply_filters('putrafiber_email_from_address', $email ?: $default);
}
add_filter('wp_mail_from', 'putrafiber_email_from_address');

/**
 * ============================================================================
 * ADMIN HOOKS
 * ============================================================================
 */

/**
 * Filter: Admin Dashboard Widgets
 * 
 * @param array $widgets Dashboard widgets
 * @return array Modified widgets
 */
function putrafiber_dashboard_widgets($widgets) {
    return apply_filters('putrafiber_dashboard_widgets', $widgets);
}

/**
 * Action: After Theme Options Save
 * 
 * @param array $options Saved options
 */
function putrafiber_after_theme_options_save($options) {
    do_action('putrafiber_after_theme_options_save', $options);
    
    // Clear cache after options save
    wp_cache_flush();
}

/**
 * ============================================================================
 * AJAX HOOKS
 * ============================================================================
 */

/**
 * Action: Custom AJAX Actions
 */
function putrafiber_ajax_load_more_posts() {
    do_action('putrafiber_ajax_load_more_posts');
}
add_action('wp_ajax_load_more_posts', 'putrafiber_ajax_load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'putrafiber_ajax_load_more_posts');

/**
 * Action: AJAX Contact Form
 */
function putrafiber_ajax_contact_form() {
    do_action('putrafiber_ajax_contact_form');
}
add_action('wp_ajax_contact_form', 'putrafiber_ajax_contact_form');
add_action('wp_ajax_nopriv_contact_form', 'putrafiber_ajax_contact_form');

/**
 * ============================================================================
 * SECURITY HOOKS
 * ============================================================================
 */

/**
 * Filter: Allowed HTML Tags
 * 
 * @param array $allowed_tags Allowed tags
 * @return array Modified tags
 */
function putrafiber_allowed_html_tags($allowed_tags) {
    $custom_tags = array(
        'svg' => array(
            'xmlns' => true,
            'width' => true,
            'height' => true,
            'viewbox' => true,
            'fill' => true,
            'stroke' => true,
        ),
        'path' => array(
            'd' => true,
            'fill' => true,
            'stroke' => true,
        ),
    );
    
    return apply_filters('putrafiber_allowed_html_tags', array_merge($allowed_tags, $custom_tags));
}

/**
 * ============================================================================
 * LOGIN/LOGOUT HOOKS
 * ============================================================================
 */

/**
 * Filter: Login Redirect
 * 
 * @param string $redirect_to Redirect URL
 * @param string $request Requested redirect
 * @param object $user User object
 * @return string Modified URL
 */
function putrafiber_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            $redirect_to = admin_url();
        } else {
            $redirect_to = home_url();
        }
    }
    
    return apply_filters('putrafiber_login_redirect', $redirect_to, $request, $user);
}
add_filter('login_redirect', 'putrafiber_login_redirect', 10, 3);

/**
 * ============================================================================
 * CUSTOM TEMPLATE HOOKS
 * ============================================================================
 */

/**
 * Filter: Template Path
 * 
 * @param string $template Template path
 * @return string Modified path
 */
function putrafiber_template_path($template) {
    return apply_filters('putrafiber_template_path', $template);
}

/**
 * Filter: Locate Template
 * 
 * @param string $template Template file
 * @param string $name Template name
 * @return string Located template
 */
function putrafiber_locate_template($template, $name = '') {
    $located = locate_template(array(
        $name . '.php',
        $template,
    ));
    
    return apply_filters('putrafiber_locate_template', $located, $template, $name);
}

/**
 * ============================================================================
 * NOTIFICATION HOOKS
 * ============================================================================
 */

/**
 * Action: New Portfolio Published
 * 
 * @param int $post_id Portfolio ID
 */
function putrafiber_new_portfolio_published($post_id) {
    do_action('putrafiber_new_portfolio_published', $post_id);
}
add_action('publish_portfolio', 'putrafiber_new_portfolio_published');

/**
 * Action: New Comment Posted
 * 
 * @param int $comment_id Comment ID
 * @param object $comment Comment object
 */
function putrafiber_new_comment_posted($comment_id, $comment) {
    do_action('putrafiber_new_comment_posted', $comment_id, $comment);
}
add_action('comment_post', 'putrafiber_new_comment_posted', 10, 2);

/**
 * ============================================================================
 * THIRD-PARTY INTEGRATION HOOKS
 * ============================================================================
 */

/**
 * Filter: Google Analytics ID
 * 
 * @param string $ga_id Google Analytics ID
 * @return string Modified ID
 */
function putrafiber_google_analytics_id($ga_id = '') {
    return apply_filters('putrafiber_google_analytics_id', $ga_id);
}

/**
 * Filter: Facebook Pixel ID
 * 
 * @param string $fb_pixel Facebook Pixel ID
 * @return string Modified ID
 */
function putrafiber_facebook_pixel_id($fb_pixel = '') {
    return apply_filters('putrafiber_facebook_pixel_id', $fb_pixel);
}

/**
 * ============================================================================
 * MAINTENANCE MODE HOOKS
 * ============================================================================
 */

/**
 * Filter: Enable Maintenance Mode
 * 
 * @param bool $enabled Maintenance mode status
 * @return bool Modified status
 */
function putrafiber_maintenance_mode($enabled = false) {
    return apply_filters('putrafiber_maintenance_mode', $enabled);
}

/**
 * Action: Maintenance Mode Active
 */
function putrafiber_maintenance_mode_active() {
    if (putrafiber_maintenance_mode() && !current_user_can('administrator')) {
        do_action('putrafiber_maintenance_mode_active');
        
        // Load maintenance template
        get_template_part('template-parts/maintenance');
        exit;
    }
}
add_action('template_redirect', 'putrafiber_maintenance_mode_active');

/**
 * ============================================================================
 * CUSTOM QUERY HOOKS
 * ============================================================================
 */

/**
 * Filter: Related Posts Query
 * 
 * @param array $args Query arguments
 * @param int $post_id Current post ID
 * @return array Modified args
 */
function putrafiber_related_posts_args($args, $post_id) {
    $default_args = array(
        'posts_per_page' => 3,
        'post__not_in' => array($post_id),
        'orderby' => 'rand',
    );
    
    return apply_filters('putrafiber_related_posts_args', array_merge($default_args, $args), $post_id);
}

/**
 * Filter: Popular Posts Query
 * 
 * @param array $args Query arguments
 * @return array Modified args
 */
function putrafiber_popular_posts_args($args) {
    $default_args = array(
        'posts_per_page' => 5,
        'orderby' => 'comment_count',
        'order' => 'DESC',
    );
    
    return apply_filters('putrafiber_popular_posts_args', array_merge($default_args, $args));
}

/**
 * ============================================================================
 * CUSTOM EXCERPT HOOKS
 * ============================================================================
 */

/**
 * Filter: Custom Excerpt for Portfolio
 * 
 * @param string $excerpt Excerpt text
 * @param object $post Post object
 * @return string Modified excerpt
 */
function putrafiber_portfolio_excerpt($excerpt, $post) {
    if ($post->post_type === 'portfolio') {
        $excerpt = wp_trim_words($post->post_content, 20, '...');
    }
    
    return apply_filters('putrafiber_portfolio_excerpt', $excerpt, $post);
}
add_filter('get_the_excerpt', 'putrafiber_portfolio_excerpt', 10, 2);

/**
 * ============================================================================
 * CUSTOM TITLE HOOKS
 * ============================================================================
 */

/**
 * Filter: Custom Title for Archive Pages
 * 
 * @param string $title Archive title
 * @return string Modified title
 */
function putrafiber_custom_archive_title_filter($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_post_type_archive('portfolio')) {
        $title = __('Our Portfolio', 'putrafiber');
    }
    
    return apply_filters('putrafiber_custom_archive_title', $title);
}
add_filter('get_the_archive_title', 'putrafiber_custom_archive_title_filter');

/**
 * ============================================================================
 * UTILITY HOOKS
 * ============================================================================
 */

/**
 * Filter: Date Format
 * 
 * @param string $format Date format
 * @return string Modified format
 */
function putrafiber_date_format($format) {
    return apply_filters('putrafiber_date_format', $format ?: get_option('date_format'));
}

/**
 * Filter: Time Format
 * 
 * @param string $format Time format
 * @return string Modified format
 */
function putrafiber_time_format($format) {
    return apply_filters('putrafiber_time_format', $format ?: get_option('time_format'));
}

/**
 * ============================================================================
 * DEVELOPER HOOKS
 * ============================================================================
 */

/**
 * Action: Theme Loaded
 * 
 * Fires when theme is fully loaded
 */
function putrafiber_theme_loaded() {
    do_action('putrafiber_theme_loaded');
}
add_action('after_setup_theme', 'putrafiber_theme_loaded', 20);

/**
 * Action: Scripts Enqueued
 * 
 * Fires after scripts are enqueued
 */
function putrafiber_scripts_enqueued() {
    do_action('putrafiber_scripts_enqueued');
}
add_action('wp_enqueue_scripts', 'putrafiber_scripts_enqueued', 20);

/**
 * ============================================================================
 * DOCUMENTATION
 * ============================================================================
 * 
 * HOW TO USE THESE HOOKS:
 * 
 * 1. Action Hook Example:
 *    add_action('putrafiber_before_header', 'your_custom_function');
 *    function your_custom_function() {
 *        echo '<div>Custom content before header</div>';
 *    }
 * 
 * 2. Filter Hook Example:
 *    add_filter('putrafiber_excerpt_length', 'custom_excerpt_length');
 *    function custom_excerpt_length($length) {
 *        return 50;
 *    }
 * 
 * 3. Removing Hook:
 *    remove_action('putrafiber_before_header', 'your_custom_function');
 *    remove_filter('putrafiber_excerpt_length', 'custom_excerpt_length');
 * 
 * ============================================================================
 */

/**
 * Hook Reference List
 * 
 * ACTIONS:
 * - putrafiber_before_header
 * - putrafiber_after_header
 * - putrafiber_before_content
 * - putrafiber_after_content
 * - putrafiber_before_footer
 * - putrafiber_after_footer
 * - putrafiber_before_post_content
 * - putrafiber_after_post_content
 * - putrafiber_portfolio_before_content
 * - putrafiber_portfolio_after_content
 * - putrafiber_after_portfolio_save
 * - putrafiber_new_portfolio_published
 * - putrafiber_new_comment_posted
 * - putrafiber_theme_loaded
 * - putrafiber_scripts_enqueued
 * 
 * FILTERS:
 * - putrafiber_excerpt_length
 * - putrafiber_excerpt_more
 * - putrafiber_post_classes
 * - putrafiber_body_classes
 * - putrafiber_archive_title
 * - putrafiber_whatsapp_number
 * - putrafiber_company_info
 * - putrafiber_nav_menu_items
 * - putrafiber_image_sizes
 * - putrafiber_comment_form_fields
 * - putrafiber_widget_title
 * - putrafiber_organization_schema
 * - putrafiber_product_schema
 * - putrafiber_article_schema
 * - putrafiber_social_share_buttons
 * - putrafiber_login_redirect
 * - putrafiber_date_format
 * - putrafiber_time_format
 */