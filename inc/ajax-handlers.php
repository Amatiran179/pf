<?php
/**
 * AJAX Handlers
 * 
 * Handle all AJAX requests for the theme
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * ============================================================================
 * LOAD MORE POSTS
 * ============================================================================
 */

/**
 * Load More Posts via AJAX
 */
function putrafiber_ajax_load_more_posts() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $page = isset($_POST['page']) ? max(1, pf_clean_int($_POST['page'])) : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? max(1, pf_clean_int($_POST['posts_per_page'])) : 6;
    $category = isset($_POST['category']) ? pf_clean_text($_POST['category']) : '';
    $post_type = isset($_POST['post_type']) ? pf_clean_text($_POST['post_type']) : 'post';
    
    // Build query args
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged' => $page,
        'post_status' => 'publish',
    );
    
    if ($category) {
        $args['category_name'] = $category;
    }
    
    $query = new WP_Query($args);
    
    $response = array(
        'success' => false,
        'posts' => array(),
        'max_pages' => $query->max_num_pages,
        'found_posts' => $query->found_posts,
    );
    
    if ($query->have_posts()) {
        ob_start();
        
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/content', get_post_type());
        }
        
        $response['posts'] = ob_get_clean();
        $response['success'] = true;
    }
    
    wp_reset_postdata();
    
    wp_send_json($response);
}
add_action('wp_ajax_load_more_posts', 'putrafiber_ajax_load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'putrafiber_ajax_load_more_posts');

/**
 * ============================================================================
 * FILTER PORTFOLIO
 * ============================================================================
 */

/**
 * Filter Portfolio by Category via AJAX
 */
function putrafiber_ajax_filter_portfolio() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $category = isset($_POST['category']) ? pf_clean_text($_POST['category']) : '';
    $posts_per_page = isset($_POST['posts_per_page']) ? max(1, pf_clean_int($_POST['posts_per_page'])) : 12;
    
    $args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => $posts_per_page,
        'post_status' => 'publish',
    );
    
    if ($category && $category !== '*') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'portfolio_category',
                'field' => 'slug',
                'terms' => $category,
            ),
        );
    }
    
    $query = new WP_Query($args);
    
    $response = array(
        'success' => false,
        'html' => '',
        'count' => 0,
    );
    
    if ($query->have_posts()) {
        ob_start();
        
        while ($query->have_posts()) {
            $query->the_post();
            
            $location = get_post_meta(get_the_ID(), '_portfolio_location', true);
            ?>
            <div class="portfolio-item fade-in">
                <div class="portfolio-card">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="portfolio-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('putrafiber-portfolio'); ?>
                            </a>
                            <div class="portfolio-overlay">
                                <a href="<?php the_permalink(); ?>" class="view-btn">
                                    <?php _e('View Project', 'putrafiber'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="portfolio-content">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if ($location): ?>
                          <p class="location"><?php echo pf_output_html($location); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
        
        $response['html'] = ob_get_clean();
        $response['count'] = $query->found_posts;
        $response['success'] = true;
    }
    
    wp_reset_postdata();
    
    wp_send_json($response);
}
add_action('wp_ajax_filter_portfolio', 'putrafiber_ajax_filter_portfolio');
add_action('wp_ajax_nopriv_filter_portfolio', 'putrafiber_ajax_filter_portfolio');

/**
 * ============================================================================
 * CONTACT FORM
 * ============================================================================
 */

/**
 * Handle Contact Form Submission
 */
function putrafiber_ajax_contact_form() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $name = isset($_POST['name']) ? pf_clean_text($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? pf_clean_text($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? pf_clean_text($_POST['subject']) : '';
    $message = isset($_POST['message']) ? pf_clean_html($_POST['message']) : '';
    
    $response = array(
        'success' => false,
        'message' => '',
    );
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = __('Please fill in all required fields.', 'putrafiber');
        wp_send_json($response);
    }
    
    if (!is_email($email)) {
        $response['message'] = __('Please enter a valid email address.', 'putrafiber');
        wp_send_json($response);
    }
    
    // Prepare email
    $to = putrafiber_get_option('company_email', get_option('admin_email'));
    $email_subject = sprintf(__('[%s] New Contact Form Submission', 'putrafiber'), get_bloginfo('name'));
    
    $email_message = sprintf(
        __("You have received a new message from your website contact form.\n\n", 'putrafiber') .
        __("Name: %s\n", 'putrafiber') .
        __("Email: %s\n", 'putrafiber') .
        __("Phone: %s\n", 'putrafiber') .
        __("Subject: %s\n\n", 'putrafiber') .
        __("Message:\n%s\n\n", 'putrafiber') .
        __("---\n", 'putrafiber') .
        __("This email was sent from: %s", 'putrafiber'),
        $name,
        $email,
        $phone,
        $subject,
        $message,
        home_url()
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    
    // Send email
    $sent = wp_mail($to, $email_subject, $email_message, $headers);
    
    if ($sent) {
        // Save to database (optional)
        $contact_data = array(
            'post_title' => 'Contact from ' . $name,
            'post_content' => $message,
            'post_status' => 'private',
            'post_type' => 'contact_submission',
            'meta_input' => array(
                '_contact_name' => $name,
                '_contact_email' => $email,
                '_contact_phone' => $phone,
                '_contact_subject' => $subject,
                '_contact_date' => current_time('mysql'),
            ),
        );
        
        // Uncomment to save submissions to database
        // wp_insert_post($contact_data);
        
        $response['success'] = true;
        $response['message'] = __('Thank you for contacting us! We will get back to you soon.', 'putrafiber');
    } else {
        $response['message'] = __('Sorry, there was an error sending your message. Please try again later.', 'putrafiber');
    }
    
    wp_send_json($response);
}
add_action('wp_ajax_contact_form', 'putrafiber_ajax_contact_form');
add_action('wp_ajax_nopriv_contact_form', 'putrafiber_ajax_contact_form');

/**
 * ============================================================================
 * SEARCH SUGGESTIONS
 * ============================================================================
 */

/**
 * Get Search Suggestions
 */
function putrafiber_ajax_search_suggestions() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $search_term = isset($_POST['search']) ? pf_clean_text($_POST['search']) : '';
    
    $response = array(
        'success' => false,
        'suggestions' => array(),
    );
    
    if (strlen($search_term) < 3) {
        wp_send_json($response);
    }
    
    $args = array(
        's' => $search_term,
        'post_type' => array('post', 'page', 'portfolio'),
        'posts_per_page' => 5,
        'post_status' => 'publish',
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $response['suggestions'][] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'url' => get_permalink(),
                'type' => get_post_type(),
                'excerpt' => wp_trim_words(get_the_excerpt(), 15),
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
            );
        }
        
        $response['success'] = true;
    }
    
    wp_reset_postdata();
    
    wp_send_json($response);
}
add_action('wp_ajax_search_suggestions', 'putrafiber_ajax_search_suggestions');
add_action('wp_ajax_nopriv_search_suggestions', 'putrafiber_ajax_search_suggestions');

/**
 * ============================================================================
 * NEWSLETTER SUBSCRIPTION
 * ============================================================================
 */

/**
 * Handle Newsletter Subscription
 */
function putrafiber_ajax_newsletter_subscribe() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    
    $response = array(
        'success' => false,
        'message' => '',
    );
    
    if (!is_email($email)) {
        $response['message'] = __('Please enter a valid email address.', 'putrafiber');
        wp_send_json($response);
    }
    
    // Check if already subscribed
    $subscribers = get_option('putrafiber_newsletter_subscribers', array());
    
    if (in_array($email, $subscribers)) {
        $response['message'] = __('This email is already subscribed!', 'putrafiber');
        wp_send_json($response);
    }
    
    // Add to subscribers list
    $subscribers[] = $email;
    update_option('putrafiber_newsletter_subscribers', $subscribers);
    
    // Send confirmation email
    $to = $email;
    $subject = sprintf(__('[%s] Newsletter Subscription Confirmed', 'putrafiber'), get_bloginfo('name'));
    $message = sprintf(
        __("Thank you for subscribing to our newsletter!\n\n", 'putrafiber') .
        __("You will receive updates about our latest projects, products, and news.\n\n", 'putrafiber') .
        __("Best regards,\n%s", 'putrafiber'),
        get_bloginfo('name')
    );
    
    wp_mail($to, $subject, $message);
    
    // Notify admin
    $admin_email = get_option('admin_email');
    $admin_subject = sprintf(__('[%s] New Newsletter Subscription', 'putrafiber'), get_bloginfo('name'));
    $admin_message = sprintf(__('New newsletter subscription: %s', 'putrafiber'), $email);
    wp_mail($admin_email, $admin_subject, $admin_message);
    
    $response['success'] = true;
    $response['message'] = __('Successfully subscribed! Check your email for confirmation.', 'putrafiber');
    
    wp_send_json($response);
}
add_action('wp_ajax_newsletter_subscribe', 'putrafiber_ajax_newsletter_subscribe');
add_action('wp_ajax_nopriv_newsletter_subscribe', 'putrafiber_ajax_newsletter_subscribe');

/**
 * ============================================================================
 * LIKE POST
 * ============================================================================
 */

/**
 * Like/Unlike Post
 */
function putrafiber_ajax_like_post() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $post_id = isset($_POST['post_id']) ? pf_clean_int($_POST['post_id']) : 0;
    
    $response = array(
        'success' => false,
        'likes' => 0,
        'liked' => false,
    );
    
    if (!$post_id) {
        wp_send_json($response);
    }
    
    // Get current likes
    $likes = get_post_meta($post_id, '_post_likes', true);
    $likes = $likes ? intval($likes) : 0;
    
    // Check if user already liked
    $user_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    if (!empty($user_ip)) {
        $validated_ip = filter_var($user_ip, FILTER_VALIDATE_IP);
        $user_ip = $validated_ip ? $validated_ip : $user_ip;
    }
    $liked_posts = get_transient('putrafiber_liked_posts_' . md5($user_ip));
    $liked_posts = $liked_posts ? $liked_posts : array();
    
    if (in_array($post_id, $liked_posts)) {
        // Unlike
        $likes--;
        $liked_posts = array_diff($liked_posts, array($post_id));
        $response['liked'] = false;
    } else {
        // Like
        $likes++;
        $liked_posts[] = $post_id;
        $response['liked'] = true;
    }
    
    // Update
    update_post_meta($post_id, '_post_likes', $likes);
    set_transient('putrafiber_liked_posts_' . md5($user_ip), $liked_posts, DAY_IN_SECONDS * 30);
    
    $response['success'] = true;
    $response['likes'] = $likes;
    
    wp_send_json($response);
}
add_action('wp_ajax_like_post', 'putrafiber_ajax_like_post');
add_action('wp_ajax_nopriv_like_post', 'putrafiber_ajax_like_post');

/**
 * ============================================================================
 * GET RELATED POSTS
 * ============================================================================
 */

/**
 * Get Related Posts via AJAX
 */
function putrafiber_ajax_get_related_posts() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $post_id = isset($_POST['post_id']) ? pf_clean_int($_POST['post_id']) : 0;
    $limit = isset($_POST['limit']) ? max(1, pf_clean_int($_POST['limit'])) : 3;
    
    $response = array(
        'success' => false,
        'html' => '',
    );
    
    if (!$post_id) {
        wp_send_json($response);
    }
    
    // Get categories
    $categories = wp_get_post_categories($post_id);
    
    if (empty($categories)) {
        wp_send_json($response);
    }
    
    $args = array(
        'category__in' => $categories,
        'post__not_in' => array($post_id),
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        ob_start();
        
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="related-post-item">
                <?php if (has_post_thumbnail()): ?>
                    <div class="related-post-thumb">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="related-post-content">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <span class="related-post-date"><?php echo get_the_date(); ?></span>
                </div>
            </div>
            <?php
        }
        
        $response['html'] = ob_get_clean();
        $response['success'] = true;
    }
    
    wp_reset_postdata();
    
    wp_send_json($response);
}
add_action('wp_ajax_get_related_posts', 'putrafiber_ajax_get_related_posts');
add_action('wp_ajax_nopriv_get_related_posts', 'putrafiber_ajax_get_related_posts');

/**
 * ============================================================================
 * QUICK VIEW PORTFOLIO
 * ============================================================================
 */

/**
 * Quick View Portfolio Details
 */
function putrafiber_ajax_quick_view_portfolio() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $portfolio_id = isset($_POST['portfolio_id']) ? pf_clean_int($_POST['portfolio_id']) : 0;
    
    $response = array(
        'success' => false,
        'data' => array(),
    );
    
    if (!$portfolio_id) {
        wp_send_json($response);
    }
    
    $portfolio = get_post($portfolio_id);
    
    if (!$portfolio || $portfolio->post_type !== 'portfolio') {
        wp_send_json($response);
    }
    
    // Get meta data
    $location = get_post_meta($portfolio_id, '_portfolio_location', true);
    $client = get_post_meta($portfolio_id, '_portfolio_client', true);
    $project_date = get_post_meta($portfolio_id, '_portfolio_date', true);
    $video_url = get_post_meta($portfolio_id, '_portfolio_video', true);
    $gallery = get_post_meta($portfolio_id, '_portfolio_gallery', true);
    
    // Get gallery images
    $gallery_images = array();
    if ($gallery) {
        $gallery_ids = array_filter(array_map('absint', explode(',', (string) $gallery)));
        foreach ($gallery_ids as $img_id) {
            $image_url = wp_get_attachment_image_url($img_id, 'large');
            if ($image_url) {
                $gallery_images[] = $image_url;
            }
        }
    }
    
    $response['data'] = array(
        'id' => $portfolio_id,
        'title' => get_the_title($portfolio_id),
        'content' => apply_filters('the_content', $portfolio->post_content),
        'image' => get_the_post_thumbnail_url($portfolio_id, 'large'),
        'location' => $location,
        'client' => $client,
        'date' => $project_date ? date('F Y', strtotime($project_date)) : '',
        'video' => $video_url,
        'gallery' => $gallery_images,
        'url' => get_permalink($portfolio_id),
    );
    
    $response['success'] = true;
    
    wp_send_json($response);
}
add_action('wp_ajax_quick_view_portfolio', 'putrafiber_ajax_quick_view_portfolio');
add_action('wp_ajax_nopriv_quick_view_portfolio', 'putrafiber_ajax_quick_view_portfolio');

/**
 * ============================================================================
 * SAVE USER PREFERENCES
 * ============================================================================
 */

/**
 * Save User Preferences (Dark Mode, etc)
 */
function putrafiber_ajax_save_preferences() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $dark_mode = isset($_POST['dark_mode']) ? pf_clean_text($_POST['dark_mode']) : 'light';
    
    $response = array(
        'success' => true,
        'message' => __('Preferences saved', 'putrafiber'),
    );
    
    // Save to cookie or user meta if logged in
    if (is_user_logged_in()) {
        update_user_meta(get_current_user_id(), 'dark_mode_preference', $dark_mode);
    }
    
    wp_send_json($response);
}
add_action('wp_ajax_save_preferences', 'putrafiber_ajax_save_preferences');
add_action('wp_ajax_nopriv_save_preferences', 'putrafiber_ajax_save_preferences');

/**
 * ============================================================================
 * HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Sanitize Recursive
 * 
 * @param array $data Data to sanitize
 * @return array Sanitized data
 */
function putrafiber_sanitize_recursive($data) {
    if (is_array($data)) {
        return array_map('putrafiber_sanitize_recursive', $data);
    } else {
        return sanitize_text_field($data);
    }
}

/**
 * Validate Phone Number
 * 
 * @param string $phone Phone number
 * @return bool Valid or not
 */
function putrafiber_validate_phone($phone) {
    // Simple validation for Indonesian phone numbers
    $pattern = '/^(^\+62|62|^08)(\d{3,4}-?){2}\d{3,4}$/';
    return preg_match($pattern, $phone);
}

/**
 * Rate Limiting Check
 * 
 * Prevent spam by limiting requests
 * 
 * @param string $action Action name
 * @param int $limit Max requests
 * @param int $period Time period in seconds
 * @return bool Allowed or not
 */
function putrafiber_rate_limit_check($action, $limit = 5, $period = 3600) {
    $user_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    if (!empty($user_ip)) {
        $validated_ip = filter_var($user_ip, FILTER_VALIDATE_IP);
        $user_ip = $validated_ip ? $validated_ip : $user_ip;
    }
    $transient_key = 'putrafiber_rate_limit_' . md5($action . $user_ip);
    
    $requests = get_transient($transient_key);
    $requests = $requests ? intval($requests) : 0;
    
    if ($requests >= $limit) {
        return false;
    }
    
    $requests++;
    set_transient($transient_key, $requests, $period);
    
    return true;
}

/**
 * ============================================================================
 * SECURITY ENHANCEMENTS
 * ============================================================================
 */

/**
 * Enhanced AJAX Security Check
 * 
 * @return bool Security check passed
 */
function putrafiber_ajax_security_check() {
    if (!check_ajax_referer('pf_ajax_nonce', 'security', false)) {
        wp_send_json_error(__('Security check failed', 'putrafiber'));
        return false;
    }

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
        return false;
    }
    
    // Check if not a bot (simple honeypot)
    if (isset($_POST['website']) && !empty($_POST['website'])) {
        wp_send_json_error(__('Bot detected', 'putrafiber'));
        return false;
    }
    
    return true;
}

/**
 * Log AJAX Activity (Optional for debugging)
 * 
 * @param string $action Action name
 * @param array $data Data to log
 */
function putrafiber_log_ajax_activity($action, $data = array()) {
    if (!WP_DEBUG) {
        return;
    }
    
    $log_entry = array(
        'timestamp' => current_time('mysql'),
        'action' => $action,
        'user_ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'data' => $data,
    );
    
    $log_file = WP_CONTENT_DIR . '/putrafiber-ajax.log';
    error_log(print_r($log_entry, true) . "\n", 3, $log_file);
}

/**
 * ============================================================================
 * EXAMPLE USAGE IN JAVASCRIPT
 * ============================================================================
 * 
 * // Load More Posts
 * jQuery.ajax({
 *     url: putrafiber_vars.ajax_url,
 *     type: 'POST',
 *     data: {
 *         action: 'load_more_posts',
 *         nonce: putrafiber_vars.nonce,
 *         page: 2,
 *         posts_per_page: 6
 *     },
 *     success: function(response) {
 *         if (response.success) {
 *             jQuery('.posts-container').append(response.posts);
 *         }
 *     }
 * });
 * 
 * // Contact Form
 * jQuery.ajax({
 *     url: putrafiber_vars.ajax_url,
 *     type: 'POST',
 *     data: {
 *         action: 'contact_form',
 *         nonce: putrafiber_vars.nonce,
 *         name: 'John Doe',
 *         email: 'john@example.com',
 *         message: 'Hello!'
 *     },
 *     success: function(response) {
 *         alert(response.message);
 *     }
 * });
 * 
 * // Newsletter Subscribe
 * jQuery.ajax({
 *     url: putrafiber_vars.ajax_url,
 *     type: 'POST',
 *     data: {
 *         action: 'newsletter_subscribe',
 *         nonce: putrafiber_vars.nonce,
 *         email: 'subscriber@example.com'
 *     },
 *     success: function(response) {
 *         alert(response.message);
 *     }
 * });
 * 
 * ============================================================================
 */