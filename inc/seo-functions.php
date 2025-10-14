<?php
/**
 * SEO Functions
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Output SEO Meta Tags
 */
function putrafiber_seo_meta_tags() {
    $title = '';
    $description = '';
    $image = '';
    $url = '';
    
    if (is_singular()) {
        $post_id = get_the_ID();
        $custom_title = get_post_meta($post_id, '_meta_title', true);
        $custom_desc = get_post_meta($post_id, '_meta_description', true);
        
        $title = $custom_title ? $custom_title : get_the_title() . ' - ' . get_bloginfo('name');
        $description = $custom_desc ? $custom_desc : wp_trim_words(get_the_excerpt(), 25);
        $image = get_the_post_thumbnail_url($post_id, 'full');
        $url = get_permalink();
    } elseif (is_front_page()) {
        $title = get_bloginfo('name') . ' - ' . get_bloginfo('description');
        $description = putrafiber_get_option('hero_description', get_bloginfo('description'));
        $image = putrafiber_get_option('hero_image', '');
        $url = home_url('/');
    } elseif (is_category()) {
        $title = single_cat_title('', false) . ' - ' . get_bloginfo('name');
        $description = category_description();
        $url = get_category_link(get_queried_object_id());
    } elseif (is_archive()) {
        $title = get_the_archive_title() . ' - ' . get_bloginfo('name');
        $description = get_the_archive_description();
        $url = get_post_type_archive_link(get_post_type());
    }
    
    // Default image
    if (!$image) {
        $image = get_template_directory_uri() . '/assets/images/default-og.jpg';
    }
    
    // Output Meta Tags
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    
    // Open Graph
    echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    echo '<meta property="og:image:width" content="1200">' . "\n";
    echo '<meta property="og:image:height" content="630">' . "\n";
    echo '<meta property="og:locale" content="id_ID">' . "\n";
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    
    // Canonical
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
}
add_action('wp_head', 'putrafiber_seo_meta_tags', 1);

/**
 * Add Custom Meta Boxes for SEO
 */
function putrafiber_add_seo_meta_boxes() {
    $post_types = array('post', 'page', 'portfolio');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'putrafiber_seo_meta',
            __('SEO Settings', 'putrafiber'),
            'putrafiber_seo_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'putrafiber_add_seo_meta_boxes');

/**
 * SEO Meta Box Callback
 */
function putrafiber_seo_meta_box_callback($post) {
    wp_nonce_field('putrafiber_seo_nonce', 'putrafiber_seo_nonce_field');
    
    $meta_title = get_post_meta($post->ID, '_meta_title', true);
    $meta_desc = get_post_meta($post->ID, '_meta_description', true);
    $focus_keyword = get_post_meta($post->ID, '_focus_keyword', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="meta_title"><?php _e('Meta Title', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="meta_title" name="meta_title" value="<?php echo esc_attr($meta_title); ?>" class="large-text">
                <p class="description">
                    <?php _e('Current length:', 'putrafiber'); ?> <span id="title-length">0</span> <?php _e('characters. Recommended: 50-60', 'putrafiber'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="meta_description"><?php _e('Meta Description', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="meta_description" name="meta_description" rows="3" class="large-text"><?php echo esc_textarea($meta_desc); ?></textarea>
                <p class="description">
                    <?php _e('Current length:', 'putrafiber'); ?> <span id="desc-length">0</span> <?php _e('characters. Recommended: 150-160', 'putrafiber'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="focus_keyword"><?php _e('Focus Keyword', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="focus_keyword" name="focus_keyword" value="<?php echo esc_attr($focus_keyword); ?>" class="regular-text">
                <p class="description"><?php _e('Main keyword for this content', 'putrafiber'); ?></p>
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        function updateLength() {
            $('#title-length').text($('#meta_title').val().length);
            $('#desc-length').text($('#meta_description').val().length);
        }
        
        $('#meta_title, #meta_description').on('keyup', updateLength);
        updateLength();
    });
    </script>
    <?php
}

/**
 * Save SEO Meta
 */
function putrafiber_save_seo_meta($post_id) {
    if (!isset($_POST['putrafiber_seo_nonce_field'])) return;
    if (!wp_verify_nonce($_POST['putrafiber_seo_nonce_field'], 'putrafiber_seo_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    $fields = array('meta_title', 'meta_description', 'focus_keyword');
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'putrafiber_save_seo_meta');

/**
 * Add robots meta tag
 */
function putrafiber_robots_meta() {
    if (is_search() || is_404()) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    } else {
        echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
    }
}
add_action('wp_head', 'putrafiber_robots_meta', 1);
