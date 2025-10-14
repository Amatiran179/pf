<?php
/**
 * Portfolio Custom Post Type
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Portfolio Post Type
 */
function putrafiber_register_portfolio() {
    $labels = array(
        'name'               => _x('Portofolio', 'post type general name', 'putrafiber'),
        'singular_name'      => _x('Portofolio', 'post type singular name', 'putrafiber'),
        'menu_name'          => _x('Portofolio', 'admin menu', 'putrafiber'),
        'name_admin_bar'     => _x('Portofolio', 'add new on admin bar', 'putrafiber'),
        'add_new'            => _x('Add New', 'portofolio', 'putrafiber'),        'add_new_item'       => __('Add New Portofolio', 'putrafiber'),
        'new_item'           => __('New Portofolio', 'putrafiber'),
        'edit_item'          => __('Edit Portofolio', 'putrafiber'),
        'view_item'          => __('View Portofolio', 'putrafiber'),
        'all_items'          => __('All Portofolio', 'putrafiber'),
        'search_items'       => __('Search Portofolio', 'putrafiber'),
        'parent_item_colon'  => __('Parent Portofolio:', 'putrafiber'),
        'not_found'          => __('No portofolio found.', 'putrafiber'),
        'not_found_in_trash' => __('No portofolio found in Trash.', 'putrafiber')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Portfolio projects for PutraFiber', 'putrafiber'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'portofolio'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
    );

    register_post_type('portfolio', $args);
}
add_action('init', 'putrafiber_register_portfolio');

/**
 * Register Portfolio Taxonomies
 */
function putrafiber_register_portfolio_taxonomies() {
    $labels = array(
        'name'              => _x('Portfolio Categories', 'taxonomy general name', 'putrafiber'),
        'singular_name'     => _x('Portfolio Category', 'taxonomy singular name', 'putrafiber'),
        'search_items'      => __('Search Categories', 'putrafiber'),
        'all_items'         => __('All Categories', 'putrafiber'),
        'parent_item'       => __('Parent Category', 'putrafiber'),
        'parent_item_colon' => __('Parent Category:', 'putrafiber'),
        'edit_item'         => __('Edit Category', 'putrafiber'),
        'update_item'       => __('Update Category', 'putrafiber'),
        'add_new_item'      => __('Add New Category', 'putrafiber'),
        'new_item_name'     => __('New Category Name', 'putrafiber'),
        'menu_name'         => __('Categories', 'putrafiber'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'portfolio-category'),
        'show_in_rest'      => true,
    );

    register_taxonomy('portfolio_category', array('portfolio'), $args);
}
add_action('init', 'putrafiber_register_portfolio_taxonomies');

/**
 * Add Portfolio Meta Boxes
 */
function putrafiber_portfolio_meta_boxes() {
    add_meta_box(
        'putrafiber_portfolio_details',
        __('Portfolio Details', 'putrafiber'),
        'putrafiber_portfolio_details_callback',
        'portfolio',
        'normal',
        'high'
    );
    
    add_meta_box(
        'putrafiber_portfolio_schema',
        __('SEO & Schema Settings', 'putrafiber'),
        'putrafiber_portfolio_schema_callback',
        'portfolio',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'putrafiber_portfolio_meta_boxes');

/**
 * Portfolio Details Callback
 */
function putrafiber_portfolio_details_callback($post) {
    wp_nonce_field('putrafiber_portfolio_nonce', 'putrafiber_portfolio_nonce_field');
    
    $location = get_post_meta($post->ID, '_portfolio_location', true);
    $project_date = get_post_meta($post->ID, '_portfolio_date', true);
    $client = get_post_meta($post->ID, '_portfolio_client', true);
    $video_url = get_post_meta($post->ID, '_portfolio_video', true);
    $gallery = get_post_meta($post->ID, '_portfolio_gallery', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="portfolio_location"><?php _e('Project Location', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_location" name="portfolio_location" value="<?php echo esc_attr($location); ?>" class="regular-text">
                <p class="description"><?php _e('e.g., Jakarta, Bandung, Surabaya', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="portfolio_date"><?php _e('Project Date', 'putrafiber'); ?></label></th>
            <td>
                <input type="date" id="portfolio_date" name="portfolio_date" value="<?php echo esc_attr($project_date); ?>">
            </td>
        </tr>
        <tr>
            <th><label for="portfolio_client"><?php _e('Client Name', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_client" name="portfolio_client" value="<?php echo esc_attr($client); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="portfolio_video"><?php _e('Video URL (Optional)', 'putrafiber'); ?></label></th>
            <td>
                <input type="url" id="portfolio_video" name="portfolio_video" value="<?php echo esc_url($video_url); ?>" class="regular-text">
                <p class="description"><?php _e('YouTube or Vimeo URL', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="portfolio_gallery"><?php _e('Gallery Images', 'putrafiber'); ?></label></th>
            <td>
                <input type="hidden" id="portfolio_gallery" name="portfolio_gallery" value="<?php echo esc_attr($gallery); ?>">
                <button type="button" class="button portfolio-gallery-upload"><?php _e('Upload Gallery', 'putrafiber'); ?></button>
                <div class="portfolio-gallery-preview">
                    <?php
                    if ($gallery) {
                        $gallery_ids = explode(',', $gallery);
                        foreach ($gallery_ids as $img_id) {
                            echo wp_get_attachment_image($img_id, 'thumbnail');
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Portfolio Schema Callback
 */
function putrafiber_portfolio_schema_callback($post) {
    $enable_tourist = get_post_meta($post->ID, '_enable_tourist_schema', true);
    $service_area = get_post_meta($post->ID, '_service_area', true);
    $meta_title = get_post_meta($post->ID, '_meta_title', true);
    $meta_desc = get_post_meta($post->ID, '_meta_description', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="meta_title"><?php _e('Meta Title', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="meta_title" name="meta_title" value="<?php echo esc_attr($meta_title); ?>" class="large-text">
                <p class="description"><?php _e('Leave empty to use post title', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="meta_description"><?php _e('Meta Description', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="meta_description" name="meta_description" rows="3" class="large-text"><?php echo esc_textarea($meta_desc); ?></textarea>
                <p class="description"><?php _e('Recommended: 150-160 characters', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="enable_tourist_schema"><?php _e('Tourist Attraction Schema', 'putrafiber'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="enable_tourist_schema" name="enable_tourist_schema" value="1" <?php checked($enable_tourist, '1'); ?>>
                    <?php _e('Enable Tourist Attraction Schema for this project', 'putrafiber'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="service_area"><?php _e('Service Area Override', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="service_area" name="service_area" value="<?php echo esc_attr($service_area); ?>" class="regular-text">
                <p class="description"><?php _e('Auto-detected from location. Override if needed.', 'putrafiber'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Portfolio Meta
 */
function putrafiber_save_portfolio_meta($post_id) {
    if (!isset($_POST['putrafiber_portfolio_nonce_field'])) return;
    if (!wp_verify_nonce($_POST['putrafiber_portfolio_nonce_field'], 'putrafiber_portfolio_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array(
        'portfolio_location' => '_portfolio_location',
        'portfolio_date' => '_portfolio_date',
        'portfolio_client' => '_portfolio_client',
        'portfolio_video' => '_portfolio_video',
        'portfolio_gallery' => '_portfolio_gallery',
        'meta_title' => '_meta_title',
        'meta_description' => '_meta_description',
        'service_area' => '_service_area',
    );

    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
        }
    }
    
    $enable_tourist = isset($_POST['enable_tourist_schema']) ? '1' : '0';
    update_post_meta($post_id, '_enable_tourist_schema', $enable_tourist);
}
add_action('save_post_portfolio', 'putrafiber_save_portfolio_meta');

/**
 * Flush Rewrite Rules on Activation
 */
function putrafiber_rewrite_flush() {
    putrafiber_register_portfolio();
    putrafiber_register_portfolio_taxonomies();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'putrafiber_rewrite_flush');
