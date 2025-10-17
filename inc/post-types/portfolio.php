<?php
/**
 * Portfolio Custom Post Type - ENHANCED VERSION WITH CTA SYSTEM
 *
 * @package PutraFiber
 * @version 2.1.0 - Enhanced with CTA system similar to products
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
        'add_new'            => _x('Add New', 'portofolio', 'putrafiber'),
        'add_new_item'       => __('Add New Portofolio', 'putrafiber'),
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
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
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
        __('📋 Project Information', 'putrafiber'),
        'putrafiber_portfolio_details_callback',
        'portfolio',
        'normal',
        'high'
    );

    add_meta_box(
        'putrafiber_portfolio_gallery',
        __('🖼️ Project Gallery', 'putrafiber'),
        'putrafiber_portfolio_gallery_callback',
        'portfolio',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'putrafiber_portfolio_meta_boxes');

/**
 * Ensure media and sortable assets are ready inside portfolio editor.
 */
function putrafiber_portfolio_admin_assets($hook_suffix) {
    global $typenow;
    if ($typenow !== 'portfolio') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-sortable');
}
add_action('admin_enqueue_scripts', 'putrafiber_portfolio_admin_assets');

/**
 * Portfolio Details Callback - ENHANCED WITH CTA SYSTEM
 */
function putrafiber_portfolio_details_callback($post) {
    wp_nonce_field('putrafiber_portfolio_nonce', 'putrafiber_portfolio_nonce_field');
    wp_nonce_field('pf_save_meta', 'pf_meta_nonce');

    // Get meta values
    $cta_type = get_post_meta($post->ID, '_portfolio_cta_type', true) ?: 'detail';
    $location = get_post_meta($post->ID, '_portfolio_location', true);
    $project_date = get_post_meta($post->ID, '_portfolio_date', true);
    $completion_date = get_post_meta($post->ID, '_portfolio_completion_date', true);
    $client = get_post_meta($post->ID, '_portfolio_client', true);
    $project_value = get_post_meta($post->ID, '_portfolio_value', true);
    $project_duration = get_post_meta($post->ID, '_portfolio_duration', true);
    $project_size = get_post_meta($post->ID, '_portfolio_size', true);
    $project_type = get_post_meta($post->ID, '_portfolio_type', true);
    $services = get_post_meta($post->ID, '_portfolio_services', true);
    $materials = get_post_meta($post->ID, '_portfolio_materials', true);
    $team_size = get_post_meta($post->ID, '_portfolio_team_size', true);
    $challenges = get_post_meta($post->ID, '_portfolio_challenges', true);
    $solutions = get_post_meta($post->ID, '_portfolio_solutions', true);
    $video_url = get_post_meta($post->ID, '_portfolio_video', true);
    ?>

    <style>
    .portfolio-meta-table { width: 100%; border-collapse: collapse; }
    .portfolio-meta-table th { width: 200px; text-align: left; padding: 15px 10px; font-weight: 600; vertical-align: top; background: #f9f9f9; }
    .portfolio-meta-table td { padding: 15px 10px; }
    .portfolio-meta-table tr { border-bottom: 1px solid #e0e0e0; }
    .portfolio-meta-input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
    .portfolio-meta-textarea { width: 100%; min-height: 80px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
    .help-text { font-size: 12px; color: #666; margin-top: 5px; font-style: italic; }
    .section-header { background: #0073aa; color: white; padding: 10px 15px; font-weight: 600; margin: 20px 0 0 0; }

    /* CTA Type Toggle Styles */
    .cta-type-toggle { display: flex; gap: 20px; margin-bottom: 15px; }
    .cta-type-toggle label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .cta-detail-wrapper { display: none; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-top: 10px; }
    .cta-detail-wrapper.active { display: block; }
    </style>

    <table class="portfolio-meta-table">

        <!-- CTA Configuration -->
        <tr>
            <td colspan="2" class="section-header">🎯 Call-to-Action Configuration</td>
        </tr>

        <tr>
            <th><label><?php _e('CTA Type', 'putrafiber'); ?></label></th>
            <td>
                <div class="cta-type-toggle">
                    <label>
                        <input type="radio" name="portfolio_cta_type" value="detail" <?php checked($cta_type, 'detail'); ?>>
                        <strong><?php _e('Lihat Detail Project', 'putrafiber'); ?></strong>
                    </label>
                    <label>
                        <input type="radio" name="portfolio_cta_type" value="whatsapp" <?php checked($cta_type, 'whatsapp'); ?>>
                        <strong><?php _e('Konsultasi Langsung (WhatsApp)', 'putrafiber'); ?></strong>
                    </label>
                </div>

                <div class="cta-detail-wrapper <?php echo ($cta_type === 'detail') ? 'active' : ''; ?>" id="detail-cta-box">
                    <p style="color: #00BCD4; font-weight: 600;">✅ <?php _e('Tombol "Lihat Detail Project" akan tampil', 'putrafiber'); ?></p>
                    <p class="help-text"><?php _e('Pengunjung akan melihat detail project lengkap terlebih dahulu', 'putrafiber'); ?></p>
                </div>

                <div class="cta-detail-wrapper <?php echo ($cta_type === 'whatsapp') ? 'active' : ''; ?>" id="whatsapp-cta-box">
                    <p style="color: #25D366; font-weight: 600;">📞 <?php _e('Tombol "Konsultasi Project Serupa" akan tampil langsung', 'putrafiber'); ?></p>
                    <p class="help-text"><?php _e('Pengunjung langsung diarahkan ke WhatsApp untuk konsultasi', 'putrafiber'); ?></p>
                </div>
            </td>
        </tr>

        <!-- Basic Information -->
        <tr>
            <td colspan="2" class="section-header">📍 Basic Information</td>
        </tr>

        <tr>
            <th><label for="portfolio_location"><?php _e('Project Location', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_location" name="portfolio_location" value="<?php echo esc_attr($location); ?>" class="portfolio-meta-input" placeholder="Jakarta, Bandung, Surabaya">
                <p class="help-text"><?php _e('City or location where the project is located', 'putrafiber'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_client"><?php _e('Client Name', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_client" name="portfolio_client" value="<?php echo esc_attr($client); ?>" class="portfolio-meta-input" placeholder="PT. Example Company">
                <p class="help-text"><?php _e('Client or company name (optional if confidential)', 'putrafiber'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_type"><?php _e('Project Type', 'putrafiber'); ?></label></th>
            <td>
                <select id="portfolio_type" name="portfolio_type" class="portfolio-meta-input">
                    <option value="">-- Select Type --</option>
                    <option value="Waterpark" <?php selected($project_type, 'Waterpark'); ?>>Waterpark</option>
                    <option value="Waterboom" <?php selected($project_type, 'Waterboom'); ?>>Waterboom</option>
                    <option value="Playground Indoor" <?php selected($project_type, 'Playground Indoor'); ?>>Playground Indoor</option>
                    <option value="Playground Outdoor" <?php selected($project_type, 'Playground Outdoor'); ?>>Playground Outdoor</option>
                    <option value="Kolam Renang" <?php selected($project_type, 'Kolam Renang'); ?>>Kolam Renang</option>
                    <option value="Perosotan Fiberglass" <?php selected($project_type, 'Perosotan Fiberglass'); ?>>Perosotan Fiberglass</option>
                    <option value="Custom Fiberglass" <?php selected($project_type, 'Custom Fiberglass'); ?>>Custom Fiberglass</option>
                    <option value="Renovasi" <?php selected($project_type, 'Renovasi'); ?>>Renovasi</option>
                </select>
            </td>
        </tr>

        <!-- Project Timeline -->
        <tr>
            <td colspan="2" class="section-header">📅 Timeline</td>
        </tr>

        <tr>
            <th><label for="portfolio_date"><?php _e('Start Date', 'putrafiber'); ?></label></th>
            <td>
                <input type="date" id="portfolio_date" name="portfolio_date" value="<?php echo esc_attr($project_date); ?>" class="portfolio-meta-input">
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_completion_date"><?php _e('Completion Date', 'putrafiber'); ?></label></th>
            <td>
                <input type="date" id="portfolio_completion_date" name="portfolio_completion_date" value="<?php echo esc_attr($completion_date); ?>" class="portfolio-meta-input">
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_duration"><?php _e('Project Duration', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_duration" name="portfolio_duration" value="<?php echo esc_attr($project_duration); ?>" class="portfolio-meta-input" placeholder="e.g., 3 Bulan, 6 Minggu">
                <p class="help-text"><?php _e('Duration from start to completion', 'putrafiber'); ?></p>
            </td>
        </tr>

        <!-- Project Details -->
        <tr>
            <td colspan="2" class="section-header">📊 Project Details</td>
        </tr>

        <tr>
            <th><label for="portfolio_size"><?php _e('Project Size/Area', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_size" name="portfolio_size" value="<?php echo esc_attr($project_size); ?>" class="portfolio-meta-input" placeholder="e.g., 500 m², 1 Hektar">
                <p class="help-text"><?php _e('Total project area or size', 'putrafiber'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_value"><?php _e('Project Value (Optional)', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_value" name="portfolio_value" value="<?php echo esc_attr($project_value); ?>" class="portfolio-meta-input" placeholder="e.g., Rp 500 Juta - Rp 1 Miliar">
                <p class="help-text"><?php _e('Project budget range (optional)', 'putrafiber'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_team_size"><?php _e('Team Size', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="portfolio_team_size" name="portfolio_team_size" value="<?php echo esc_attr($team_size); ?>" class="portfolio-meta-input" placeholder="e.g., 15 People">
                <p class="help-text"><?php _e('Number of team members involved', 'putrafiber'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_services"><?php _e('Services Provided', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="portfolio_services" name="portfolio_services" class="portfolio-meta-textarea" placeholder="e.g., Design, Fabrication, Installation, Maintenance"><?php echo esc_textarea($services); ?></textarea>
                <p class="help-text"><?php _e('Services provided for this project (comma separated)', 'putrafiber'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_materials"><?php _e('Materials Used', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="portfolio_materials" name="portfolio_materials" class="portfolio-meta-textarea" placeholder="e.g., Fiberglass Premium, Resin Polyester, Gelcoat"><?php echo esc_textarea($materials); ?></textarea>
                <p class="help-text"><?php _e('Key materials used in this project', 'putrafiber'); ?></p>
            </td>
        </tr>

        <!-- Challenges & Solutions -->
        <tr>
            <td colspan="2" class="section-header">💡 Challenges & Solutions</td>
        </tr>

        <tr>
            <th><label for="portfolio_challenges"><?php _e('Challenges Faced', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="portfolio_challenges" name="portfolio_challenges" class="portfolio-meta-textarea" placeholder="Describe main challenges..."><?php echo esc_textarea($challenges); ?></textarea>
            </td>
        </tr>

        <tr>
            <th><label for="portfolio_solutions"><?php _e('Solutions Implemented', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="portfolio_solutions" name="portfolio_solutions" class="portfolio-meta-textarea" placeholder="Describe solutions..."><?php echo esc_textarea($solutions); ?></textarea>
            </td>
        </tr>

        <!-- Media -->
        <tr>
            <td colspan="2" class="section-header">🎥 Media</td>
        </tr>

        <tr>
            <th><label for="portfolio_video"><?php _e('Video URL (Optional)', 'putrafiber'); ?></label></th>
            <td>
                <input type="url" id="portfolio_video" name="portfolio_video" value="<?php echo esc_url($video_url); ?>" class="portfolio-meta-input" placeholder="https://youtube.com/watch?v=...">
                <p class="help-text"><?php _e('YouTube or Vimeo URL', 'putrafiber'); ?></p>
            </td>
        </tr>

    </table>

    <script>
    jQuery(document).ready(function($){
        // CTA Type toggle
        $('input[name="portfolio_cta_type"]').on('change', function(){
            $('.cta-detail-wrapper').removeClass('active');
            if ($(this).val() === 'detail') {
                $('#detail-cta-box').addClass('active');
            } else {
                $('#whatsapp-cta-box').addClass('active');
            }
        });
    });
    </script>
    <?php
}

/**
 * Portfolio Gallery Callback - ENHANCED (Similar to Product)
 */
function putrafiber_portfolio_gallery_callback($post) {
    $gallery_raw   = get_post_meta($post->ID, '_portfolio_gallery', true);
    $gallery_ids   = function_exists('putrafiber_extract_gallery_ids') ? putrafiber_extract_gallery_ids($gallery_raw) : array();
    $gallery_value = !empty($gallery_ids) ? implode(',', $gallery_ids) : '';
    ?>
    <div class="portfolio-gallery-box">
        <input type="hidden" id="portfolio_gallery" name="portfolio_gallery" value="<?php echo esc_attr($gallery_value); ?>">
        <button type="button" class="button button-primary button-large" id="upload-portfolio-gallery-button" style="width: 100%; margin-bottom: 15px;">
            <span class="dashicons dashicons-images-alt2" style="margin-top: 3px;"></span> <?php _e('Upload Gallery Images', 'putrafiber'); ?>
        </button>
        <div id="portfolio-gallery-preview" class="gallery-preview-grid">
            <?php
            if (!empty($gallery_ids)) {
                foreach ($gallery_ids as $img_id) {
                    if ($img_url = wp_get_attachment_image_url($img_id, 'thumbnail')) {
                        echo '<div class="gallery-item" data-id="'.esc_attr($img_id).'"><img src="'.esc_url($img_url).'" alt="Gallery image"><button type="button" class="remove-gallery-item" title="Remove">&times;</button></div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <?php
    // The JavaScript logic is now unified in admin.js, so no inline script is needed here.
}

/**
 * Save Portfolio Meta - ENHANCED WITH CTA TYPE
 */
function putrafiber_save_portfolio_meta($post_id) {
    if (!isset($_POST['pf_meta_nonce']) || !wp_verify_nonce($_POST['pf_meta_nonce'], 'pf_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['putrafiber_portfolio_nonce_field'])) return;
    if (!wp_verify_nonce($_POST['putrafiber_portfolio_nonce_field'], 'putrafiber_portfolio_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = array(
        'portfolio_cta_type' => '_portfolio_cta_type',
        'portfolio_location' => '_portfolio_location',
        'portfolio_date' => '_portfolio_date',
        'portfolio_completion_date' => '_portfolio_completion_date',
        'portfolio_client' => '_portfolio_client',
        'portfolio_value' => '_portfolio_value',
        'portfolio_duration' => '_portfolio_duration',
        'portfolio_size' => '_portfolio_size',
        'portfolio_type' => '_portfolio_type',
        'portfolio_services' => '_portfolio_services',
        'portfolio_materials' => '_portfolio_materials',
        'portfolio_team_size' => '_portfolio_team_size',
        'portfolio_challenges' => '_portfolio_challenges',
        'portfolio_solutions' => '_portfolio_solutions',
        'portfolio_video' => '_portfolio_video',
        'portfolio_gallery' => '_portfolio_gallery',
    );

    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            $raw_value = $_POST[$field];

            if (in_array($field, array('portfolio_services', 'portfolio_materials', 'portfolio_challenges', 'portfolio_solutions'))) {
                update_post_meta($post_id, $meta_key, pf_clean_html($raw_value));
            } elseif ($field === 'portfolio_video') {
                update_post_meta($post_id, $meta_key, pf_clean_url($raw_value));
            } elseif ($field === 'portfolio_gallery') {
                $gallery_raw = pf_clean_text($raw_value);
                $value = function_exists('putrafiber_prepare_gallery_meta_value')
                    ? putrafiber_prepare_gallery_meta_value($gallery_raw)
                    : implode(',', array_filter(array_map('absint', explode(',', $gallery_raw))));
                update_post_meta($post_id, $meta_key, $value);
            } else {
                update_post_meta($post_id, $meta_key, pf_clean_text($raw_value));
            }
        }
    }
}
add_action('save_post_portfolio', 'putrafiber_save_portfolio_meta');

/**
 * Flush Rewrite Rules on Activation
 */
function putrafiber_portfolio_flush_rewrite_rules() {
    putrafiber_register_portfolio();
    putrafiber_register_portfolio_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'putrafiber_portfolio_flush_rewrite_rules');
