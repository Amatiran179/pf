<?php
/**
 * SEO utilities and editor tooling for PutraFiber theme.
 *
 * @package PutraFiber
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retrieve default share image.
 *
 * @return string
 */
function putrafiber_get_default_social_image() {
    $fallback = function_exists('putrafiber_get_option') ? putrafiber_get_option('og_image', '') : '';
    if (!empty($fallback)) {
        return esc_url($fallback);
    }

    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
        if ($logo_url) {
            return esc_url($logo_url);
        }
    }

    return esc_url(get_template_directory_uri() . '/assets/images/default-og.jpg');
}

/**
 * Normalise stored twitter username.
 *
 * @return string
 */
function putrafiber_get_twitter_handle() {
    $handle = function_exists('putrafiber_get_option') ? putrafiber_get_option('twitter_username', '') : '';
    $handle = trim((string) $handle);

    if ($handle === '') {
        return '';
    }

    return '@' . ltrim($handle, '@');
}

/**
 * Fetch taxonomy SEO metadata as array.
 *
 * @param int $term_id
 * @return array<string,mixed>
 */
function putrafiber_get_term_seo_settings($term_id) {
    $settings = get_term_meta($term_id, '_putrafiber_seo', true);
    return is_array($settings) ? $settings : array();
}

/**
 * Output SEO meta tags with support for per-post and per-term overrides.
 */
function putrafiber_seo_meta_tags() {
    $site_name           = get_bloginfo('name');
    $default_description = get_bloginfo('description');
    $default_keywords    = function_exists('putrafiber_get_option') ? putrafiber_get_option('meta_keywords', '') : '';
    $twitter_handle      = putrafiber_get_twitter_handle();

    $title       = '';
    $description = '';
    $url         = '';
    $image       = '';
    $keywords    = '';
    $type        = 'website';

    if (is_singular()) {
        $post_id = get_the_ID();
        $type    = 'article';

        $custom_title = trim((string) get_post_meta($post_id, '_meta_title', true));
        $title        = $custom_title !== '' ? $custom_title : get_the_title() . ' - ' . $site_name;

        $custom_desc = trim((string) get_post_meta($post_id, '_meta_description', true));
        if ($custom_desc !== '') {
            $description = $custom_desc;
        } elseif (has_excerpt($post_id)) {
            $description = get_the_excerpt($post_id);
        } else {
            $description = wp_trim_words(wp_strip_all_tags(strip_shortcodes(get_the_content(null, false, $post_id))), 30);
        }

        $keywords = trim((string) get_post_meta($post_id, '_meta_keywords', true));
        if ($keywords === '') {
            $keywords = trim((string) get_post_meta($post_id, '_focus_keyword', true));
        }

        $canonical = trim((string) get_post_meta($post_id, '_canonical_url', true));
        $url       = $canonical !== '' ? $canonical : get_permalink();

        $og_image_id = (int) get_post_meta($post_id, '_meta_og_image_id', true);
        if ($og_image_id > 0) {
            $image = wp_get_attachment_image_url($og_image_id, 'full');
        }
        if (!$image) {
            $manual_image = trim((string) get_post_meta($post_id, '_meta_og_image', true));
            if ($manual_image !== '') {
                $image = $manual_image;
            }
        }
        if (!$image) {
            $image = get_the_post_thumbnail_url($post_id, 'full');
        }
    } elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $settings = putrafiber_get_term_seo_settings($term->term_id);

            $custom_title = isset($settings['title']) ? trim((string) $settings['title']) : '';
            $title        = $custom_title !== '' ? $custom_title : $term->name . ' - ' . $site_name;

            $custom_desc = isset($settings['description']) ? trim((string) $settings['description']) : '';
            if ($custom_desc !== '') {
                $description = $custom_desc;
            } else {
                $term_desc = term_description($term->term_id);
                $description = $term_desc ? wp_strip_all_tags($term_desc) : $default_description;
            }

            $keywords = isset($settings['keywords']) ? trim((string) $settings['keywords']) : '';

            $canonical = isset($settings['canonical']) ? trim((string) $settings['canonical']) : '';
            $url       = $canonical !== '' ? $canonical : get_term_link($term);

            if (!empty($settings['og_image_id'])) {
                $image = wp_get_attachment_image_url((int) $settings['og_image_id'], 'full');
            }
            if (!$image && !empty($settings['og_image'])) {
                $image = $settings['og_image'];
            }
        }
    } elseif (is_front_page()) {
        $type        = 'website';
        $title       = $site_name . ' - ' . $default_description;
        $description = function_exists('putrafiber_get_option') ? putrafiber_get_option('meta_description', $default_description) : $default_description;
        $url         = home_url('/');
        $keywords    = function_exists('putrafiber_get_option') ? putrafiber_get_option('meta_keywords', '') : '';
        $image       = function_exists('putrafiber_get_option') ? putrafiber_get_option('og_image', '') : '';
    } elseif (is_post_type_archive()) {
        $title       = get_the_archive_title() . ' - ' . $site_name;
        $description = wp_strip_all_tags(get_the_archive_description());
        $url         = get_post_type_archive_link(get_post_type());
    } elseif (is_home() && get_option('page_for_posts')) {
        $posts_page = (int) get_option('page_for_posts');
        $title       = get_the_title($posts_page) . ' - ' . $site_name;
        $description = function_exists('putrafiber_get_option') ? putrafiber_get_option('meta_description', $default_description) : $default_description;
        $url         = get_permalink($posts_page);
    }

    if ($title === '') {
        $title = $site_name . ' - ' . $default_description;
    }
    if ($description === '') {
        $description = $default_description;
    }
    if ($url === '') {
        if (isset($GLOBALS['wp']) && isset($GLOBALS['wp']->request)) {
            $url = home_url(trailingslashit($GLOBALS['wp']->request));
        } else {
            $url = home_url('/');
        }
    }
    if (!$image) {
        $image = putrafiber_get_default_social_image();
    }
    if ($keywords === '') {
        $keywords = $default_keywords;
    }

    echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($description)) . '">' . "\n";
    if ($keywords !== '') {
        echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
    }
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($description)) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    echo '<meta property="og:image:width" content="1200">' . "\n";
    echo '<meta property="og:image:height" content="630">' . "\n";
    echo '<meta property="og:locale" content="id_ID">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    if ($twitter_handle !== '') {
        echo '<meta name="twitter:site" content="' . esc_attr($twitter_handle) . '">' . "\n";
        echo '<meta name="twitter:creator" content="' . esc_attr($twitter_handle) . '">' . "\n";
    }
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr(wp_strip_all_tags($description)) . '">' . "\n";
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

    $meta_title    = get_post_meta($post->ID, '_meta_title', true);
    $meta_desc     = get_post_meta($post->ID, '_meta_description', true);
    $focus_keyword = get_post_meta($post->ID, '_focus_keyword', true);
    $meta_keywords = get_post_meta($post->ID, '_meta_keywords', true);
    $canonical_url = get_post_meta($post->ID, '_canonical_url', true);
    $noindex       = get_post_meta($post->ID, '_meta_noindex', true);
    $nofollow      = get_post_meta($post->ID, '_meta_nofollow', true);

    $og_image_id      = (int) get_post_meta($post->ID, '_meta_og_image_id', true);
    $og_image_manual  = get_post_meta($post->ID, '_meta_og_image', true);
    $og_image_preview = $og_image_id ? wp_get_attachment_image_url($og_image_id, 'medium') : $og_image_manual;
    if (!$og_image_preview && has_post_thumbnail($post)) {
        $og_image_preview = get_the_post_thumbnail_url($post, 'medium');
    }

    $enable_video_schema = get_post_meta($post->ID, '_enable_video_schema', true);
    $video_url            = get_post_meta($post->ID, '_video_url', true);
    $video_duration       = get_post_meta($post->ID, '_video_duration', true);
    $video_title          = get_post_meta($post->ID, '_video_title', true);
    $video_description    = get_post_meta($post->ID, '_video_description', true);

    $enable_faq_schema = get_post_meta($post->ID, '_enable_faq_schema', true);
    $faq_items         = get_post_meta($post->ID, '_faq_items', true);

    $enable_howto_schema = get_post_meta($post->ID, '_enable_howto_schema', true);
    $howto_steps         = get_post_meta($post->ID, '_howto_steps', true);

    $faq_items   = is_array($faq_items) ? $faq_items : array();
    $howto_steps = is_array($howto_steps) ? $howto_steps : array();

    $default_title = get_the_title($post) . ' - ' . get_bloginfo('name');
    $default_desc  = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 30);
    $default_url   = get_permalink($post);
    ?>

    <style>
        .pf-seo-snippet {background:#fff;border:1px solid #ccd0d4;border-radius:6px;padding:16px;margin:16px 0;max-width:640px;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;}
        .pf-snippet-url {display:block;color:#006621;font-size:13px;margin-bottom:4px;word-break:break-all;}
        .pf-snippet-title {display:block;color:#1a0dab;font-size:18px;line-height:1.3;font-weight:600;margin-bottom:6px;}
        .pf-snippet-description {margin:0;color:#545454;font-size:14px;line-height:1.4;}
        .pf-seo-checkboxes label {display:inline-flex;align-items:center;margin-right:20px;gap:6px;}
        .pf-seo-og-preview img {max-width:180px;height:auto;border-radius:4px;margin-top:10px;}
        .pf-schema-group {padding-left:20px;border-left:3px solid #0073aa;margin-left:5px;margin-bottom:24px;background:#f8faff;border-radius:4px;}
        .pf-schema-group .button {margin-top:8px;}
        .pf-schema-repeater-item {background:#fff;border:1px solid #dce1e6;padding:15px;border-radius:4px;margin-bottom:10px;position:relative;}
        .pf-schema-repeater-item .button-link-delete {position:absolute;top:10px;right:10px;}
    </style>

    <table class="form-table">
        <tr>
            <th><label for="meta_title"><?php esc_html_e('Meta Title', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="meta_title" name="meta_title" value="<?php echo esc_attr($meta_title); ?>" class="large-text" maxlength="70">
                <p class="description"><?php esc_html_e('Panjang:', 'putrafiber'); ?> <span id="title-length">0</span> <?php esc_html_e('karakter (rekomendasi 50-60)', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="meta_description"><?php esc_html_e('Meta Description', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="meta_description" name="meta_description" rows="3" class="large-text" maxlength="170"><?php echo esc_textarea($meta_desc); ?></textarea>
                <p class="description"><?php esc_html_e('Panjang:', 'putrafiber'); ?> <span id="desc-length">0</span> <?php esc_html_e('karakter (rekomendasi 150-160)', 'putrafiber'); ?></p>
                <div class="pf-seo-snippet" aria-hidden="true">
                    <span class="pf-snippet-url" id="pf-snippet-url" data-default="<?php echo esc_attr($default_url); ?>"><?php echo esc_html($canonical_url ? $canonical_url : $default_url); ?></span>
                    <span class="pf-snippet-title" id="pf-snippet-title" data-default="<?php echo esc_attr($default_title); ?>"><?php echo esc_html($meta_title ? $meta_title : $default_title); ?></span>
                    <p class="pf-snippet-description" id="pf-snippet-description" data-default="<?php echo esc_attr($default_desc); ?>"><?php echo esc_html($meta_desc ? $meta_desc : $default_desc); ?></p>
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="focus_keyword"><?php esc_html_e('Focus Keyword', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="focus_keyword" name="focus_keyword" value="<?php echo esc_attr($focus_keyword); ?>" class="regular-text" placeholder="<?php esc_attr_e('Contoh: perosotan fiberglass', 'putrafiber'); ?>">
                <p class="description"><?php esc_html_e('Keyword utama untuk konten ini.', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="meta_keywords"><?php esc_html_e('Meta Keywords', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo esc_attr($meta_keywords); ?>" class="large-text" placeholder="waterpark, playground fiberglass, kontraktor waterboom">
                <p class="description"><?php esc_html_e('Opsional: daftar keyword dipisahkan koma untuk mesin pencari lama.', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="canonical_url"><?php esc_html_e('Canonical URL', 'putrafiber'); ?></label></th>
            <td>
                <input type="url" id="canonical_url" name="canonical_url" value="<?php echo esc_attr($canonical_url); ?>" class="large-text" placeholder="<?php echo esc_attr($default_url); ?>">
                <p class="description"><?php esc_html_e('Kosongkan untuk memakai permalink asli.', 'putrafiber'); ?></p>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e('Robots Control', 'putrafiber'); ?></th>
            <td class="pf-seo-checkboxes">
                <label><input type="checkbox" name="meta_noindex" value="1" <?php checked($noindex, '1'); ?>> <?php esc_html_e('Noindex (sembunyikan dari mesin pencari)', 'putrafiber'); ?></label>
                <label><input type="checkbox" name="meta_nofollow" value="1" <?php checked($nofollow, '1'); ?>> <?php esc_html_e('Nofollow (abaikan tautan pada halaman ini)', 'putrafiber'); ?></label>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e('Social Share Image', 'putrafiber'); ?></th>
            <td>
                <input type="hidden" id="meta_og_image_id" name="meta_og_image_id" value="<?php echo esc_attr($og_image_id); ?>">
                <input type="hidden" id="meta_og_image" name="meta_og_image" value="<?php echo esc_url($og_image_manual); ?>">
                <button type="button" class="button pf-seo-og-upload"><?php esc_html_e('Pilih Gambar', 'putrafiber'); ?></button>
                <button type="button" class="button pf-seo-og-remove"><?php esc_html_e('Hapus', 'putrafiber'); ?></button>
                <div class="pf-seo-og-preview">
                    <?php if ($og_image_preview): ?>
                        <img src="<?php echo esc_url($og_image_preview); ?>" alt="<?php esc_attr_e('Pratinjau gambar sosial', 'putrafiber'); ?>">
                    <?php endif; ?>
                </div>
                <p class="description"><?php esc_html_e('Gunakan ukuran 1200x630 piksel untuk hasil terbaik di media sosial.', 'putrafiber'); ?></p>
            </td>
        </tr>
    </table>

    <hr style="margin: 20px 0;">
    <h3 style="margin-bottom: 15px; color: #0073aa;">
        <?php esc_html_e('Fitur Schema Tambahan (Opsional)', 'putrafiber'); ?>
    </h3>

    <div class="schema-toggle-wrapper">
        <p>
            <label for="enable_video_schema">
                <input type="checkbox" id="enable_video_schema" name="enable_video_schema" value="1" <?php checked($enable_video_schema, '1'); ?>>
                <?php echo wp_kses_post(__('Aktifkan <strong>Video Schema</strong>', 'putrafiber')); ?>
            </label>
        </p>
        <div class="pf-schema-group video-schema-field" style="display: <?php echo $enable_video_schema ? 'block' : 'none'; ?>;">
            <p><strong><?php esc_html_e('Video URL', 'putrafiber'); ?></strong><br><input type="url" name="video_url" value="<?php echo esc_url($video_url); ?>" class="large-text" placeholder="https://www.youtube.com/watch?v=xxxxx"></p>
            <p><strong><?php esc_html_e('Judul Video', 'putrafiber'); ?></strong><br><input type="text" name="video_title" value="<?php echo esc_attr($video_title); ?>" class="large-text"></p>
            <p><strong><?php esc_html_e('Deskripsi Video', 'putrafiber'); ?></strong><br><textarea name="video_description" rows="2" class="large-text"><?php echo esc_textarea($video_description); ?></textarea></p>
            <p><strong><?php esc_html_e('Durasi Video', 'putrafiber'); ?></strong><br><input type="text" name="video_duration" value="<?php echo esc_attr($video_duration); ?>" class="regular-text" placeholder="PT5M30S (5 menit 30 detik)"></p>
        </div>
    </div>

    <div class="schema-toggle-wrapper">
        <p>
            <label for="enable_faq_schema">
                <input type="checkbox" id="enable_faq_schema" name="enable_faq_schema" value="1" <?php checked($enable_faq_schema, '1'); ?>>
                <?php echo wp_kses_post(__('Aktifkan <strong>FAQ Schema</strong>', 'putrafiber')); ?>
            </label>
        </p>
        <div class="pf-schema-group faq-schema-field" style="display: <?php echo $enable_faq_schema ? 'block' : 'none'; ?>;">
            <div id="faq-items-container">
                <?php if (!empty($faq_items)) : foreach ($faq_items as $index => $item) :
                    $question = isset($item['question']) ? $item['question'] : '';
                    $answer   = isset($item['answer']) ? $item['answer'] : '';
                ?>
                    <div class="pf-schema-repeater-item faq-item">
                        <p><label><strong><?php esc_html_e('Pertanyaan', 'putrafiber'); ?></strong><br><input type="text" name="faq_items[<?php echo esc_attr($index); ?>][question]" value="<?php echo esc_attr($question); ?>" class="large-text"></label></p>
                        <p><label><strong><?php esc_html_e('Jawaban', 'putrafiber'); ?></strong><br><textarea name="faq_items[<?php echo esc_attr($index); ?>][answer]" rows="3" class="large-text"><?php echo esc_textarea($answer); ?></textarea></label></p>
                        <button type="button" class="button-link-delete remove-faq-item"><?php esc_html_e('Hapus', 'putrafiber'); ?></button>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <button type="button" class="button button-secondary" id="add-faq-item"><?php esc_html_e('Tambah FAQ', 'putrafiber'); ?></button>
        </div>
    </div>

    <div class="schema-toggle-wrapper">
        <p>
            <label for="enable_howto_schema">
                <input type="checkbox" id="enable_howto_schema" name="enable_howto_schema" value="1" <?php checked($enable_howto_schema, '1'); ?>>
                <?php echo wp_kses_post(__('Aktifkan <strong>How-To Schema</strong>', 'putrafiber')); ?>
            </label>
        </p>
        <div class="pf-schema-group howto-schema-field" style="display: <?php echo $enable_howto_schema ? 'block' : 'none'; ?>;">
            <div id="howto-steps-container">
                <?php if (!empty($howto_steps)) : foreach ($howto_steps as $index => $step) :
                    $step_name = isset($step['name']) ? $step['name'] : '';
                    $step_text = isset($step['text']) ? $step['text'] : '';
                ?>
                    <div class="pf-schema-repeater-item howto-step">
                        <p><label><strong><?php esc_html_e('Judul Langkah', 'putrafiber'); ?></strong><br><input type="text" name="howto_steps[<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($step_name); ?>" class="large-text"></label></p>
                        <p><label><strong><?php esc_html_e('Deskripsi Langkah', 'putrafiber'); ?></strong><br><textarea name="howto_steps[<?php echo esc_attr($index); ?>][text]" rows="2" class="large-text"><?php echo esc_textarea($step_text); ?></textarea></label></p>
                        <button type="button" class="button-link-delete remove-howto-step"><?php esc_html_e('Hapus', 'putrafiber'); ?></button>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <button type="button" class="button button-secondary" id="add-howto-step"><?php esc_html_e('Tambah Langkah', 'putrafiber'); ?></button>
            <p class="description"><?php esc_html_e('Gunakan minimal 3 langkah untuk hasil terbaik pada How-To rich results.', 'putrafiber'); ?></p>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        function pfUpdateCharCount() {
            $('#title-length').text($('#meta_title').val().length);
            $('#desc-length').text($('#meta_description').val().length);
        }

        function pfUpdateSnippet() {
            var $titleEl = $('#pf-snippet-title');
            var $descEl = $('#pf-snippet-description');
            var $urlEl = $('#pf-snippet-url');

            var title = $('#meta_title').val().trim();
            var desc = $('#meta_description').val().trim();
            var url = $('#canonical_url').val().trim();

            $titleEl.text(title !== '' ? title : $titleEl.data('default'));
            $descEl.text(desc !== '' ? desc : $descEl.data('default'));
            $urlEl.text(url !== '' ? url : $urlEl.data('default'));
        }

        pfUpdateCharCount();
        pfUpdateSnippet();

        $('#meta_title, #meta_description').on('input', function() {
            pfUpdateCharCount();
            pfUpdateSnippet();
        });
        $('#canonical_url').on('input', pfUpdateSnippet);

        $('#enable_video_schema').on('change', function(){ $('.video-schema-field').toggle(this.checked); });
        $('#enable_faq_schema').on('change', function(){ $('.faq-schema-field').toggle(this.checked); });
        $('#enable_howto_schema').on('change', function(){ $('.howto-schema-field').toggle(this.checked); });

        var faqIndex = $('.faq-item').length;
        $('#add-faq-item').on('click', function() {
            var questionLabel = '<?php echo esc_js(__('Pertanyaan', 'putrafiber')); ?>';
            var answerLabel = '<?php echo esc_js(__('Jawaban', 'putrafiber')); ?>';
            var removeLabel = '<?php echo esc_js(__('Hapus', 'putrafiber')); ?>';
            var html = '' +
                '<div class="pf-schema-repeater-item faq-item">' +
                    '<p><label><strong>' + questionLabel + '</strong><br><input type="text" name="faq_items[' + faqIndex + '][question]" class="large-text"></label></p>' +
                    '<p><label><strong>' + answerLabel + '</strong><br><textarea name="faq_items[' + faqIndex + '][answer]" rows="3" class="large-text"></textarea></label></p>' +
                    '<button type="button" class="button-link-delete remove-faq-item">' + removeLabel + '</button>' +
                '</div>';
            $('#faq-items-container').append(html);
            faqIndex++;
        });

        $(document).on('click', '.remove-faq-item', function(){
            $(this).closest('.faq-item').remove();
        });

        var howtoIndex = $('.howto-step').length;
        $('#add-howto-step').on('click', function() {
            var titleLabel = '<?php echo esc_js(__('Judul Langkah', 'putrafiber')); ?>';
            var descLabel = '<?php echo esc_js(__('Deskripsi Langkah', 'putrafiber')); ?>';
            var removeLabel = '<?php echo esc_js(__('Hapus', 'putrafiber')); ?>';
            var html = '' +
                '<div class="pf-schema-repeater-item howto-step">' +
                    '<p><label><strong>' + titleLabel + '</strong><br><input type="text" name="howto_steps[' + howtoIndex + '][name]" class="large-text"></label></p>' +
                    '<p><label><strong>' + descLabel + '</strong><br><textarea name="howto_steps[' + howtoIndex + '][text]" rows="2" class="large-text"></textarea></label></p>' +
                    '<button type="button" class="button-link-delete remove-howto-step">' + removeLabel + '</button>' +
                '</div>';
            $('#howto-steps-container').append(html);
            howtoIndex++;
        });

        $(document).on('click', '.remove-howto-step', function(){
            $(this).closest('.howto-step').remove();
        });

        var ogFrame;
        $('.pf-seo-og-upload').on('click', function(e) {
            e.preventDefault();
            if (ogFrame) {
                ogFrame.open();
                return;
            }

            ogFrame = wp.media({
                title: '<?php echo esc_js(__('Pilih Gambar Sosial', 'putrafiber')); ?>',
                button: { text: '<?php echo esc_js(__('Gunakan Gambar', 'putrafiber')); ?>' },
                multiple: false
            });

            ogFrame.on('select', function() {
                var attachment = ogFrame.state().get('selection').first().toJSON();
                $('#meta_og_image_id').val(attachment.id);
                $('#meta_og_image').val(attachment.url);
                $('.pf-seo-og-preview').html('<img src="' + attachment.url + '" alt="<?php echo esc_js(__('Pratinjau gambar sosial', 'putrafiber')); ?>">');
            });

            ogFrame.open();
        });

        $('.pf-seo-og-remove').on('click', function(e) {
            e.preventDefault();
            $('#meta_og_image_id').val('');
            $('#meta_og_image').val('');
            $('.pf-seo-og-preview').empty();
        });
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
    if (!isset($_POST['putrafiber_seo_nonce_field']) || !wp_verify_nonce($_POST['putrafiber_seo_nonce_field'], 'putrafiber_seo_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $text_fields = array('meta_title', 'focus_keyword', 'video_title', 'video_duration', 'meta_keywords');
    foreach ($text_fields as $field) {
        if (isset($_POST[$field]) && $_POST[$field] !== '') {
            update_post_meta($post_id, '_' . $field, sanitize_text_field(wp_unslash($_POST[$field])));
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }

    $textarea_fields = array('meta_description', 'video_description');
    foreach ($textarea_fields as $field) {
        if (isset($_POST[$field]) && $_POST[$field] !== '') {
            update_post_meta($post_id, '_' . $field, sanitize_textarea_field(wp_unslash($_POST[$field])));
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }

    $url_fields = array('video_url', 'canonical_url', 'meta_og_image');
    foreach ($url_fields as $field) {
        if (isset($_POST[$field]) && $_POST[$field] !== '') {
            update_post_meta($post_id, '_' . $field, esc_url_raw(wp_unslash($_POST[$field])));
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }

    $og_image_id = isset($_POST['meta_og_image_id']) ? (int) $_POST['meta_og_image_id'] : 0;
    if ($og_image_id > 0) {
        update_post_meta($post_id, '_meta_og_image_id', $og_image_id);
    } else {
        delete_post_meta($post_id, '_meta_og_image_id');
    }

    $checkboxes = array('enable_video_schema', 'enable_faq_schema', 'enable_howto_schema', 'meta_noindex', 'meta_nofollow');
    foreach ($checkboxes as $field) {
        $value = isset($_POST[$field]) ? '1' : '0';
        update_post_meta($post_id, '_' . $field, $value);
    }

    if (isset($_POST['faq_items']) && is_array($_POST['faq_items'])) {
        $faq_items = array();
        foreach ($_POST['faq_items'] as $item) {
            $question = isset($item['question']) ? sanitize_text_field(wp_unslash($item['question'])) : '';
            $answer   = isset($item['answer']) ? sanitize_textarea_field(wp_unslash($item['answer'])) : '';
            if ($question !== '' && $answer !== '') {
                $faq_items[] = array(
                    'question' => $question,
                    'answer'   => $answer,
                );
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

    if (isset($_POST['howto_steps']) && is_array($_POST['howto_steps'])) {
        $howto_steps = array();
        foreach ($_POST['howto_steps'] as $step) {
            $name = isset($step['name']) ? sanitize_text_field(wp_unslash($step['name'])) : '';
            $text = isset($step['text']) ? sanitize_textarea_field(wp_unslash($step['text'])) : '';
            if ($name !== '' && $text !== '') {
                $howto_steps[] = array(
                    'name' => $name,
                    'text' => $text,
                );
            }
        }
        if (!empty($howto_steps)) {
            update_post_meta($post_id, '_howto_steps', $howto_steps);
        } else {
            delete_post_meta($post_id, '_howto_steps');
        }
    } else {
        delete_post_meta($post_id, '_howto_steps');
    }
}
add_action('save_post', 'putrafiber_save_seo_meta', 10, 1);

/**
 * Determine robots directive string.
 *
 * @param bool $include_extensions
 * @return string
 */
function putrafiber_get_robots_directive($include_extensions = true) {
    if (is_search() || is_404()) {
        return 'noindex, nofollow';
    }

    $noindex = false;
    $nofollow = false;

    if (is_singular()) {
        $post_id = get_the_ID();
        $noindex = get_post_meta($post_id, '_meta_noindex', true) === '1';
        $nofollow = get_post_meta($post_id, '_meta_nofollow', true) === '1';
    } elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $settings = putrafiber_get_term_seo_settings($term->term_id);
            $noindex = isset($settings['noindex']) && $settings['noindex'] === '1';
            $nofollow = isset($settings['nofollow']) && $settings['nofollow'] === '1';
        }
    }

    $directives = array();
    $directives[] = $noindex ? 'noindex' : 'index';
    $directives[] = $nofollow ? 'nofollow' : 'follow';

    if ($include_extensions) {
        $directives[] = 'max-snippet:-1';
        $directives[] = 'max-image-preview:large';
        $directives[] = 'max-video-preview:-1';
    }

    return implode(', ', $directives);
}

/**
 * Print robots tag honouring overrides.
 */
function putrafiber_robots_meta() {
    echo '<meta name="robots" content="' . esc_attr(putrafiber_get_robots_directive()) . '">' . "\n";
}
add_action('wp_head', 'putrafiber_robots_meta', 1);

/**
 * Render taxonomy SEO fields for add form.
 *
 * @param string $taxonomy
 */
function putrafiber_taxonomy_seo_add_fields($taxonomy) {
    wp_nonce_field('putrafiber_term_seo', 'putrafiber_term_seo_nonce');
    ?>
    <div class="form-field term-meta-title-wrap">
        <label for="pf_term_meta_title"><?php esc_html_e('Meta Title', 'putrafiber'); ?></label>
        <input type="text" id="pf_term_meta_title" name="putrafiber_term_seo[title]" value="" class="regular-text">
    </div>
    <div class="form-field term-meta-description-wrap">
        <label for="pf_term_meta_description"><?php esc_html_e('Meta Description', 'putrafiber'); ?></label>
        <textarea id="pf_term_meta_description" name="putrafiber_term_seo[description]" rows="3"></textarea>
    </div>
    <div class="form-field term-meta-keywords-wrap">
        <label for="pf_term_meta_keywords"><?php esc_html_e('Meta Keywords', 'putrafiber'); ?></label>
        <input type="text" id="pf_term_meta_keywords" name="putrafiber_term_seo[keywords]" value="" class="regular-text" placeholder="waterpark, playground">
    </div>
    <div class="form-field term-canonical-wrap">
        <label for="pf_term_meta_canonical"><?php esc_html_e('Canonical URL', 'putrafiber'); ?></label>
        <input type="url" id="pf_term_meta_canonical" name="putrafiber_term_seo[canonical]" value="" class="regular-text">
    </div>
    <div class="form-field term-og-image-wrap">
        <label><?php esc_html_e('Social Share Image', 'putrafiber'); ?></label>
        <input type="hidden" class="pf-term-og-image-id" name="putrafiber_term_seo[og_image_id]" value="">
        <input type="text" class="regular-text pf-term-og-image-url" name="putrafiber_term_seo[og_image]" value="" placeholder="https://">
        <button type="button" class="button pf-term-og-upload"><?php esc_html_e('Pilih Gambar', 'putrafiber'); ?></button>
        <button type="button" class="button pf-term-og-remove"><?php esc_html_e('Hapus', 'putrafiber'); ?></button>
        <div class="pf-term-og-preview"></div>
    </div>
    <div class="form-field term-robots-wrap">
        <label><?php esc_html_e('Robots Directive', 'putrafiber'); ?></label>
        <label><input type="checkbox" name="putrafiber_term_seo[noindex]" value="1"> <?php esc_html_e('Noindex', 'putrafiber'); ?></label><br>
        <label><input type="checkbox" name="putrafiber_term_seo[nofollow]" value="1"> <?php esc_html_e('Nofollow', 'putrafiber'); ?></label>
    </div>
    <?php
}

/**
 * Render taxonomy SEO fields for edit form.
 *
 * @param WP_Term $term
 */
function putrafiber_taxonomy_seo_edit_fields($term) {
    $settings = putrafiber_get_term_seo_settings($term->term_id);
    wp_nonce_field('putrafiber_term_seo', 'putrafiber_term_seo_nonce');

    $title       = isset($settings['title']) ? $settings['title'] : '';
    $description = isset($settings['description']) ? $settings['description'] : '';
    $keywords    = isset($settings['keywords']) ? $settings['keywords'] : '';
    $canonical   = isset($settings['canonical']) ? $settings['canonical'] : '';
    $og_image    = isset($settings['og_image']) ? $settings['og_image'] : '';
    $og_image_id = isset($settings['og_image_id']) ? (int) $settings['og_image_id'] : 0;
    $noindex     = !empty($settings['noindex']);
    $nofollow    = !empty($settings['nofollow']);

    $preview = $og_image_id ? wp_get_attachment_image_url($og_image_id, 'medium') : $og_image;
    ?>
    <tr class="form-field">
        <th scope="row"><label for="pf_term_meta_title"><?php esc_html_e('Meta Title', 'putrafiber'); ?></label></th>
        <td><input type="text" id="pf_term_meta_title" name="putrafiber_term_seo[title]" value="<?php echo esc_attr($title); ?>" class="regular-text"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="pf_term_meta_description"><?php esc_html_e('Meta Description', 'putrafiber'); ?></label></th>
        <td><textarea id="pf_term_meta_description" name="putrafiber_term_seo[description]" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="pf_term_meta_keywords"><?php esc_html_e('Meta Keywords', 'putrafiber'); ?></label></th>
        <td><input type="text" id="pf_term_meta_keywords" name="putrafiber_term_seo[keywords]" value="<?php echo esc_attr($keywords); ?>" class="regular-text"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="pf_term_meta_canonical"><?php esc_html_e('Canonical URL', 'putrafiber'); ?></label></th>
        <td><input type="url" id="pf_term_meta_canonical" name="putrafiber_term_seo[canonical]" value="<?php echo esc_attr($canonical); ?>" class="regular-text"></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><?php esc_html_e('Social Share Image', 'putrafiber'); ?></th>
        <td>
            <input type="hidden" class="pf-term-og-image-id" name="putrafiber_term_seo[og_image_id]" value="<?php echo esc_attr($og_image_id); ?>">
            <input type="text" class="regular-text pf-term-og-image-url" name="putrafiber_term_seo[og_image]" value="<?php echo esc_url($og_image); ?>">
            <button type="button" class="button pf-term-og-upload"><?php esc_html_e('Pilih Gambar', 'putrafiber'); ?></button>
            <button type="button" class="button pf-term-og-remove"><?php esc_html_e('Hapus', 'putrafiber'); ?></button>
            <div class="pf-term-og-preview">
                <?php if ($preview): ?>
                    <img src="<?php echo esc_url($preview); ?>" style="max-width:160px;border-radius:4px;margin-top:8px;" alt="<?php esc_attr_e('Pratinjau gambar sosial', 'putrafiber'); ?>">
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><?php esc_html_e('Robots Directive', 'putrafiber'); ?></th>
        <td>
            <label><input type="checkbox" name="putrafiber_term_seo[noindex]" value="1" <?php checked($noindex, true); ?>> <?php esc_html_e('Noindex', 'putrafiber'); ?></label><br>
            <label><input type="checkbox" name="putrafiber_term_seo[nofollow]" value="1" <?php checked($nofollow, true); ?>> <?php esc_html_e('Nofollow', 'putrafiber'); ?></label>
        </td>
    </tr>
    <?php
}

/**
 * Persist taxonomy SEO metadata.
 *
 * @param int $term_id
 */
function putrafiber_taxonomy_seo_save($term_id) {
    if (!isset($_POST['putrafiber_term_seo_nonce']) || !wp_verify_nonce($_POST['putrafiber_term_seo_nonce'], 'putrafiber_term_seo')) {
        return;
    }
    if (!current_user_can('manage_categories')) {
        return;
    }

    $data = isset($_POST['putrafiber_term_seo']) && is_array($_POST['putrafiber_term_seo']) ? $_POST['putrafiber_term_seo'] : array();

    $clean = array();
    if (!empty($data['title'])) {
        $clean['title'] = sanitize_text_field(wp_unslash($data['title']));
    }
    if (!empty($data['description'])) {
        $clean['description'] = sanitize_textarea_field(wp_unslash($data['description']));
    }
    if (!empty($data['keywords'])) {
        $clean['keywords'] = sanitize_text_field(wp_unslash($data['keywords']));
    }
    if (!empty($data['canonical'])) {
        $clean['canonical'] = esc_url_raw(wp_unslash($data['canonical']));
    }
    if (!empty($data['og_image'])) {
        $clean['og_image'] = esc_url_raw(wp_unslash($data['og_image']));
    }
    if (!empty($data['og_image_id'])) {
        $clean['og_image_id'] = (int) $data['og_image_id'];
    }

    if (!empty($data['noindex'])) {
        $clean['noindex'] = '1';
    }
    if (!empty($data['nofollow'])) {
        $clean['nofollow'] = '1';
    }

    if (!empty($clean)) {
        update_term_meta($term_id, '_putrafiber_seo', $clean);
    } else {
        delete_term_meta($term_id, '_putrafiber_seo');
    }
}

$seo_taxonomies = array('category', 'portfolio_category', 'product_category');
foreach ($seo_taxonomies as $taxonomy) {
    add_action($taxonomy . '_add_form_fields', 'putrafiber_taxonomy_seo_add_fields');
    add_action($taxonomy . '_edit_form_fields', 'putrafiber_taxonomy_seo_edit_fields');
    add_action('created_' . $taxonomy, 'putrafiber_taxonomy_seo_save');
    add_action('edited_' . $taxonomy, 'putrafiber_taxonomy_seo_save');
}

