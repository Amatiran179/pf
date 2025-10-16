<?php
/**
 * SEO Functions - SYNCHRONIZED & CENTRALIZED
 *
 * @package PutraFiber
 * @version 3.0.0
 * @description All SEO metabox logic is now centralized in this file for consistency across all post types.
 */

if (!defined('ABSPATH')) exit;

/**
 * Output SEO Meta Tags (Unchanged from previous fix)
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
        
        if ($custom_desc) {
            $description = $custom_desc;
        } elseif (has_excerpt($post_id)) {
            $description = get_the_excerpt($post_id);
        } else {
            $description = wp_trim_words(get_the_content(), 30);
        }
        
        $image = get_the_post_thumbnail_url($post_id, 'full');
        $url = get_permalink();

    } elseif (is_front_page()) {
        $title = get_bloginfo('name') . ' - ' . get_bloginfo('description');
        $description = putrafiber_get_option('meta_description', get_bloginfo('description'));
        $image = putrafiber_get_option('og_image', '');
        $url = home_url('/');

    } elseif (is_tax() || is_category()) {
        $term = get_queried_object();
        $title = $term->name . ' - ' . get_bloginfo('name');
        $description = term_description($term->term_id);
        $url = get_term_link($term);
        
    } elseif (is_post_type_archive()) {
        $title = get_the_archive_title() . ' - ' . get_bloginfo('name');
        $description = get_the_archive_description();
        $url = get_post_type_archive_link(get_post_type());
    }
    
    if (!$image) {
        $image = get_template_directory_uri() . '/assets/images/default-og.jpg';
    }
    if (empty($description)) {
        $description = get_bloginfo('description');
    }
    
    echo '<meta name="description" content="' . esc_attr(strip_tags($description)) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr(strip_tags($description)) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    echo '<meta property="og:image:width" content="1200">' . "\n";
    echo '<meta property="og:image:height" content="630">' . "\n";
    echo '<meta property="og:locale" content="id_ID">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr(strip_tags($description)) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
}
add_action('wp_head', 'putrafiber_seo_meta_tags', 1);

/**
 * ===================================================================
 * PERUBAHAN: Add a SINGLE, ADVANCED SEO Meta Box to all post types.
 * ===================================================================
 */
function putrafiber_add_seo_meta_boxes() {
    // Terapkan metabox SEO canggih ini ke semua post type yang relevan.
    $post_types = array('post', 'page', 'portfolio', 'product');
    
    foreach ($post_types as $post_type) {
        // Hapus metabox SEO lama jika ada (untuk menghindari duplikasi)
        remove_meta_box('putrafiber_seo_meta', $post_type, 'normal');
        remove_meta_box('putrafiber_product_seo', $post_type, 'normal');

        add_meta_box(
            'putrafiber_advanced_seo_meta', // ID baru yang unik
            __('SEO & Schema Settings', 'putrafiber'), // Nama yang lebih deskriptif
            'putrafiber_advanced_seo_meta_box_callback', // Callback ke fungsi canggih
            $post_type,
            'normal',
            'high' // Prioritas tinggi agar muncul di atas
        );
    }
}
add_action('add_meta_boxes', 'putrafiber_add_seo_meta_boxes');


/**
 * ===================================================================
 * BARU: Advanced SEO Meta Box Callback (Moved from product.php)
 * Fungsi ini sekarang menjadi satu-satunya callback untuk semua metabox SEO.
 * ===================================================================
 */
function putrafiber_advanced_seo_meta_box_callback($post) {
    wp_nonce_field('putrafiber_seo_nonce', 'putrafiber_seo_nonce_field');
    
    $meta_title = get_post_meta($post->ID, '_meta_title', true);
    $meta_desc = get_post_meta($post->ID, '_meta_description', true);
    $focus_keyword = get_post_meta($post->ID, '_focus_keyword', true);
    $enable_video_schema = get_post_meta($post->ID, '_enable_video_schema', true);
    $video_url = get_post_meta($post->ID, '_video_url', true);
    $video_duration = get_post_meta($post->ID, '_video_duration', true);
    $video_title = get_post_meta($post->ID, '_video_title', true);
    $video_description = get_post_meta($post->ID, '_video_description', true);
    $enable_faq_schema = get_post_meta($post->ID, '_enable_faq_schema', true);
    $faq_items = get_post_meta($post->ID, '_faq_items', true);
    $enable_howto_schema = get_post_meta($post->ID, '_enable_howto_schema', true);
    $howto_steps = get_post_meta($post->ID, '_howto_steps', true);
    
    $faq_items = is_array($faq_items) ? $faq_items : array();
    $howto_steps = is_array($howto_steps) ? $howto_steps : array();
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="meta_title"><?php _e('Meta Title', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="meta_title" name="meta_title" value="<?php echo esc_attr($meta_title); ?>" class="large-text" maxlength="60">
                <p class="description"><?php _e('Panjang:', 'putrafiber'); ?> <span id="title-length">0</span> <?php _e('karakter (Rekomendasi: 50-60)', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="meta_description"><?php _e('Meta Description', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="meta_description" name="meta_description" rows="3" class="large-text" maxlength="160"><?php echo esc_textarea($meta_desc); ?></textarea>
                <p class="description"><?php _e('Panjang:', 'putrafiber'); ?> <span id="desc-length">0</span> <?php _e('karakter (Rekomendasi: 150-160)', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="focus_keyword"><?php _e('Focus Keyword', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="focus_keyword" name="focus_keyword" value="<?php echo esc_attr($focus_keyword); ?>" class="regular-text" placeholder="Contoh: perosotan fiberglass">
                <p class="description"><?php _e('Keyword utama untuk SEO konten ini', 'putrafiber'); ?></p>
            </td>
        </tr>
    </table>

    <hr style="margin: 20px 0;">
    <h3 style="margin-bottom: 15px; color: #0073aa;"><?php _e('Fitur Schema Tambahan (Opsional)', 'putrafiber'); ?></h3>
    
    <div class="schema-toggle-wrapper">
        <p>
            <label for="enable_video_schema">
                <input type="checkbox" id="enable_video_schema" name="enable_video_schema" value="1" <?php checked($enable_video_schema, '1'); ?>>
                <?php _e('Aktifkan <strong>Video Schema</strong>', 'putrafiber'); ?>
            </label>
        </p>
        <div class="schema-fields-container video-schema-field" style="display: <?php echo $enable_video_schema ? 'block' : 'none'; ?>; padding-left: 20px; border-left: 3px solid #00BCD4; margin-left: 5px;">
            <p><strong><?php _e('Video URL', 'putrafiber'); ?>:</strong><br><input type="url" name="video_url" value="<?php echo esc_url($video_url); ?>" class="large-text" placeholder="https://www.youtube.com/watch?v=xxxxx"></p>
            <p><strong><?php _e('Judul Video', 'putrafiber'); ?>:</strong><br><input type="text" name="video_title" value="<?php echo esc_attr($video_title); ?>" class="large-text"></p>
            <p><strong><?php _e('Deskripsi Video', 'putrafiber'); ?>:</strong><br><textarea name="video_description" rows="2" class="large-text"><?php echo esc_textarea($video_description); ?></textarea></p>
            <p><strong><?php _e('Durasi Video', 'putrafiber'); ?>:</strong><br><input type="text" name="video_duration" value="<?php echo esc_attr($video_duration); ?>" class="regular-text" placeholder="PT5M30S (5 menit 30 detik)"></p>
        </div>
    </div>

    <div class="schema-toggle-wrapper">
        <p>
            <label for="enable_faq_schema">
                <input type="checkbox" id="enable_faq_schema" name="enable_faq_schema" value="1" <?php checked($enable_faq_schema, '1'); ?>>
                <?php _e('Aktifkan <strong>FAQ Schema</strong>', 'putrafiber'); ?>
            </label>
        </p>
        <div class="schema-fields-container faq-schema-field" style="display: <?php echo $enable_faq_schema ? 'block' : 'none'; ?>; padding-left: 20px; border-left: 3px solid #FF9800; margin-left: 5px;">
            <div id="faq-items-container">
                <?php if (!empty($faq_items)) : foreach ($faq_items as $index => $item) : ?>
                    <div class="faq-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
                        <p><label><strong><?php _e('Pertanyaan:', 'putrafiber'); ?></strong><br><input type="text" name="faq_items[<?php echo $index; ?>][question]" value="<?php echo esc_attr($item['question']); ?>" class="large-text"></label></p>
                        <p><label><strong><?php _e('Jawaban:', 'putrafiber'); ?></strong><br><textarea name="faq_items[<?php echo $index; ?>][answer]" rows="3" class="large-text"><?php echo esc_textarea($item['answer']); ?></textarea></label></p>
                        <button type="button" class="button button-small remove-faq-item"><?php _e('Hapus FAQ', 'putrafiber'); ?></button>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <button type="button" class="button button-secondary" id="add-faq-item"><?php _e('Tambah FAQ', 'putrafiber'); ?></button>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        function updateCharCount(){$('#title-length').text($('#meta_title').val().length);$('#desc-length').text($('#meta_description').val().length);}
        $('#meta_title, #meta_description').on('input', updateCharCount);
        updateCharCount();
        
        $('#enable_video_schema').on('change', function(){$('.video-schema-field').toggle(this.checked);});
        $('#enable_faq_schema').on('change', function(){$('.faq-schema-field').toggle(this.checked);});
        
        var faqIndex = $('.faq-item').length;
        $('#add-faq-item').on('click', function() {
            var html = '<div class="faq-item" style="background:#f9f9f9;padding:15px;margin-bottom:10px;border-radius:4px;"><p><label><strong>Pertanyaan:</strong><br><input type="text" name="faq_items['+faqIndex+'][question]" class="large-text"></label></p><p><label><strong>Jawaban:</strong><br><textarea name="faq_items['+faqIndex+'][answer]" rows="3" class="large-text"></textarea></label></p><button type="button" class="button button-small remove-faq-item">Hapus FAQ</button></div>';
            $('#faq-items-container').append(html);
            faqIndex++;
        });
        
        $(document).on('click', '.remove-faq-item', function(){ $(this).closest('.faq-item').remove(); });
    });
    </script>
    <?php
}

/**
 * ===================================================================
 * PERUBAHAN: Save ALL SEO Meta in one central function.
 * ===================================================================
 */
function putrafiber_save_seo_meta($post_id) {
    if (!isset($_POST['putrafiber_seo_nonce_field']) || !wp_verify_nonce($_POST['putrafiber_seo_nonce_field'], 'putrafiber_seo_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Save standard text fields
    $text_fields = ['meta_title', 'meta_description', 'focus_keyword', 'video_url', 'video_duration', 'video_title', 'video_description'];
    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Save checkboxes
    $checkboxes = ['enable_video_schema', 'enable_faq_schema', 'enable_howto_schema'];
    foreach ($checkboxes as $field) {
        update_post_meta($post_id, '_' . $field, isset($_POST[$field]) ? '1' : '0');
    }
    
    // Save FAQ items (repeater field)
    if (isset($_POST['faq_items']) && is_array($_POST['faq_items'])) {
        $faq_items = [];
        foreach ($_POST['faq_items'] as $item) {
            if (!empty($item['question']) && !empty($item['answer'])) {
                $faq_items[] = [
                    'question' => sanitize_text_field($item['question']),
                    'answer'   => sanitize_textarea_field($item['answer']),
                ];
            }
        }
        if (!empty($faq_items)) {
            update_post_meta($post_id, '_faq_items', $faq_items);
        } else {
            delete_post_meta($post_id, '_faq_items');
        }
    } else {
        delete_post_meta($post_id, '_faq_items');
    }
}
add_action('save_post', 'putrafiber_save_seo_meta', 10, 1);


/**
 * Add robots meta tag (Unchanged)
 */
function putrafiber_robots_meta() {
    if (is_search() || is_404()) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    } else {
        echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
    }
}
add_action('wp_head', 'putrafiber_robots_meta', 1);