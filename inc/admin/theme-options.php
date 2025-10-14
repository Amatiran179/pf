<?php
/**
 * Theme Options Panel
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Add Theme Options Menu
 */
function putrafiber_add_admin_menu() {
    add_menu_page(
        __('PutraFiber Options', 'putrafiber'),
        __('Theme Options', 'putrafiber'),
        'manage_options',
        'putrafiber-options',
        'putrafiber_options_page',
        'dashicons-admin-generic',
        61
    );
}
add_action('admin_menu', 'putrafiber_add_admin_menu');

/**
 * Register Settings
 */
function putrafiber_settings_init() {
    register_setting('putrafiber_options_group', 'putrafiber_options');
    
    // Landing Page Section
    add_settings_section(
        'putrafiber_landing_section',
        __('Landing Page Settings', 'putrafiber'),
        'putrafiber_landing_section_callback',
        'putrafiber-options'
    );
    
    add_settings_field(
        'hero_title',
        __('Hero Title', 'putrafiber'),
        'putrafiber_hero_title_render',
        'putrafiber-options',
        'putrafiber_landing_section'
    );
    
    add_settings_field(
        'hero_description',
        __('Hero Description', 'putrafiber'),
        'putrafiber_hero_description_render',
        'putrafiber-options',
        'putrafiber_landing_section'
    );
    
    add_settings_field(
        'hero_image',
        __('Hero Background Image', 'putrafiber'),
        'putrafiber_hero_image_render',
        'putrafiber-options',
        'putrafiber_landing_section'
    );
    
    add_settings_field(
        'hero_cta_text',
        __('Hero CTA Button Text', 'putrafiber'),
        'putrafiber_hero_cta_text_render',
        'putrafiber-options',
        'putrafiber_landing_section'
    );
    
    // Contact Section
    add_settings_section(
        'putrafiber_contact_section',
        __('Contact Settings', 'putrafiber'),
        'putrafiber_contact_section_callback',
        'putrafiber-options'
    );
    
    add_settings_field(
        'whatsapp_number',
        __('WhatsApp Number', 'putrafiber'),
        'putrafiber_whatsapp_number_render',
        'putrafiber-options',
        'putrafiber_contact_section'
    );
    
    add_settings_field(
        'company_address',
        __('Company Address', 'putrafiber'),
        'putrafiber_company_address_render',
        'putrafiber-options',
        'putrafiber_contact_section'
    );
    
    add_settings_field(
        'company_phone',
        __('Company Phone', 'putrafiber'),
        'putrafiber_company_phone_render',
        'putrafiber-options',
        'putrafiber_contact_section'
    );
    
    add_settings_field(
        'company_email',
        __('Company Email', 'putrafiber'),
        'putrafiber_company_email_render',
        'putrafiber-options',
        'putrafiber_contact_section'
    );
    
    add_settings_field(
        'business_hours',
        __('Business Hours', 'putrafiber'),
        'putrafiber_business_hours_render',
        'putrafiber-options',
        'putrafiber_contact_section'
    );
    
    add_settings_field(
        'google_maps_embed',
        __('Google Maps Embed URL', 'putrafiber'),
        'putrafiber_google_maps_render',
        'putrafiber-options',
        'putrafiber_contact_section'
    );
    
    // Social Media Section
    add_settings_section(
        'putrafiber_social_section',
        __('Social Media', 'putrafiber'),
        'putrafiber_social_section_callback',
        'putrafiber-options'
    );
    
    add_settings_field('facebook_url', __('Facebook URL', 'putrafiber'), 'putrafiber_facebook_url_render', 'putrafiber-options', 'putrafiber_social_section');
    add_settings_field('instagram_url', __('Instagram URL', 'putrafiber'), 'putrafiber_instagram_url_render', 'putrafiber-options', 'putrafiber_social_section');
    add_settings_field('youtube_url', __('YouTube URL', 'putrafiber'), 'putrafiber_youtube_url_render', 'putrafiber-options', 'putrafiber_social_section');
    add_settings_field('linkedin_url', __('LinkedIn URL', 'putrafiber'), 'putrafiber_linkedin_url_render', 'putrafiber-options', 'putrafiber_social_section');
    add_settings_field('twitter_url', __('Twitter URL', 'putrafiber'), 'putrafiber_twitter_url_render', 'putrafiber-options', 'putrafiber_social_section');
    
    // SEO Section
    add_settings_section(
        'putrafiber_seo_section',
        __('SEO Settings', 'putrafiber'),
        'putrafiber_seo_section_callback',
        'putrafiber-options'
    );
    
    add_settings_field('enable_schema', __('Enable Schema.org', 'putrafiber'), 'putrafiber_enable_schema_render', 'putrafiber-options', 'putrafiber_seo_section');
    add_settings_field('enable_aggregate_rating', __('Enable Aggregate Rating', 'putrafiber'), 'putrafiber_enable_rating_render', 'putrafiber-options', 'putrafiber_seo_section');
    add_settings_field('company_rating', __('Company Rating (1-5)', 'putrafiber'), 'putrafiber_company_rating_render', 'putrafiber-options', 'putrafiber_seo_section');
    add_settings_field('review_count', __('Review Count', 'putrafiber'), 'putrafiber_review_count_render', 'putrafiber-options', 'putrafiber_seo_section');
    
    // PWA Section
    add_settings_section(
        'putrafiber_pwa_section',
        __('PWA Settings', 'putrafiber'),
        'putrafiber_pwa_section_callback',
        'putrafiber-options'
    );
    
    add_settings_field('enable_pwa', __('Enable PWA', 'putrafiber'), 'putrafiber_enable_pwa_render', 'putrafiber-options', 'putrafiber_pwa_section');
    add_settings_field('pwa_name', __('App Name', 'putrafiber'), 'putrafiber_pwa_name_render', 'putrafiber-options', 'putrafiber_pwa_section');
    add_settings_field('pwa_short_name', __('App Short Name', 'putrafiber'), 'putrafiber_pwa_short_name_render', 'putrafiber-options', 'putrafiber_pwa_section');
    add_settings_field('pwa_icon', __('PWA Icon (512x512)', 'putrafiber'), 'putrafiber_pwa_icon_render', 'putrafiber-options', 'putrafiber_pwa_section');
}
add_action('admin_init', 'putrafiber_settings_init');

/**
 * Section Callbacks
 */
function putrafiber_landing_section_callback() {
    echo __('Customize your landing page hero section and main content.', 'putrafiber');
}

function putrafiber_contact_section_callback() {
    echo __('Enter your company contact information.', 'putrafiber');
}

function putrafiber_social_section_callback() {
    echo __('Add your social media profile URLs.', 'putrafiber');
}

function putrafiber_seo_section_callback() {
    echo __('Configure SEO and Schema.org settings.', 'putrafiber');
}

function putrafiber_pwa_section_callback() {
    echo __('Configure Progressive Web App settings.', 'putrafiber');
}

/**
 * Field Render Functions
 */
function putrafiber_hero_title_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['hero_title']) ? $options['hero_title'] : 'Kontraktor Waterpark & Playground Fiberglass Terpercaya';
    ?>
    <input type="text" name="putrafiber_options[hero_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_hero_description_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['hero_description']) ? $options['hero_description'] : 'Spesialis pembuatan waterpark, waterboom, playground indoor & outdoor, perosotan fiberglass, kolam renang, dan berbagai produk fiberglass berkualitas tinggi.';
    ?>
    <textarea name="putrafiber_options[hero_description]" rows="4" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_hero_image_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['hero_image']) ? $options['hero_image'] : '';
    ?>
    <input type="hidden" name="putrafiber_options[hero_image]" id="hero_image" value="<?php echo esc_url($value); ?>">
    <button type="button" class="button putrafiber-upload-image"><?php _e('Upload Image', 'putrafiber'); ?></button>
    <button type="button" class="button putrafiber-remove-image"><?php _e('Remove', 'putrafiber'); ?></button>
    <div class="image-preview">
        <?php if ($value): ?>
            <img src="<?php echo esc_url($value); ?>" style="max-width: 300px; margin-top: 10px;">
        <?php endif; ?>
    </div>
    <?php
}

function putrafiber_hero_cta_text_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['hero_cta_text']) ? $options['hero_cta_text'] : 'Konsultasi Gratis';
    ?>
    <input type="text" name="putrafiber_options[hero_cta_text]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_whatsapp_number_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['whatsapp_number']) ? $options['whatsapp_number'] : '085642318455';
    ?>
    <input type="text" name="putrafiber_options[whatsapp_number]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <p class="description"><?php _e('Format: 628xxxxxxxxxx or 08xxxxxxxxxx', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_company_address_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['company_address']) ? $options['company_address'] : 'Jl. Raya Industri No. 123, Jakarta, Indonesia';
    ?>
    <textarea name="putrafiber_options[company_address]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_company_phone_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['company_phone']) ? $options['company_phone'] : '021-12345678';
    ?>
    <input type="text" name="putrafiber_options[company_phone]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_company_email_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['company_email']) ? $options['company_email'] : 'info@putrafiber.com';
    ?>
    <input type="email" name="putrafiber_options[company_email]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_business_hours_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['business_hours']) ? $options['business_hours'] : 'Senin - Sabtu: 08:00 - 17:00 WIB';
    ?>
    <input type="text" name="putrafiber_options[business_hours]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_google_maps_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['google_maps_embed']) ? $options['google_maps_embed'] : '';
    ?>
    <textarea name="putrafiber_options[google_maps_embed]" rows="3" class="large-text" placeholder="https://www.google.com/maps/embed?pb=..."><?php echo esc_textarea($value); ?></textarea>
    <p class="description"><?php _e('Paste Google Maps embed URL here', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_facebook_url_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['facebook_url']) ? $options['facebook_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[facebook_url]" value="<?php echo esc_url($value); ?>" class="regular-text">
    <?php
}

function putrafiber_instagram_url_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['instagram_url']) ? $options['instagram_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[instagram_url]" value="<?php echo esc_url($value); ?>" class="regular-text">
    <?php
}

function putrafiber_youtube_url_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['youtube_url']) ? $options['youtube_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[youtube_url]" value="<?php echo esc_url($value); ?>" class="regular-text">
    <?php
}

function putrafiber_linkedin_url_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['linkedin_url']) ? $options['linkedin_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[linkedin_url]" value="<?php echo esc_url($value); ?>" class="regular-text">
    <?php
}

function putrafiber_twitter_url_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['twitter_url']) ? $options['twitter_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[twitter_url]" value="<?php echo esc_url($value); ?>" class="regular-text">
    <?php
}

function putrafiber_enable_schema_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['enable_schema']) ? $options['enable_schema'] : '1';
    ?>
    <label>
        <input type="checkbox" name="putrafiber_options[enable_schema]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Enable Schema.org JSON-LD markup', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_rating_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['enable_aggregate_rating']) ? $options['enable_aggregate_rating'] : '1';
    ?>
    <label>
        <input type="checkbox" name="putrafiber_options[enable_aggregate_rating]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Show aggregate rating in Organization schema', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_company_rating_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['company_rating']) ? $options['company_rating'] : '4.8';
    ?>
    <input type="number" name="putrafiber_options[company_rating]" value="<?php echo esc_attr($value); ?>" step="0.1" min="1" max="5" class="small-text">
    <?php
}

function putrafiber_review_count_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['review_count']) ? $options['review_count'] : '150';
    ?>
    <input type="number" name="putrafiber_options[review_count]" value="<?php echo esc_attr($value); ?>" class="small-text">
    <?php
}

function putrafiber_enable_pwa_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['enable_pwa']) ? $options['enable_pwa'] : '1';
    ?>
    <label>
        <input type="checkbox" name="putrafiber_options[enable_pwa]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Enable Progressive Web App features', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_pwa_name_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['pwa_name']) ? $options['pwa_name'] : 'PutraFiber';
    ?>
    <input type="text" name="putrafiber_options[pwa_name]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_pwa_short_name_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['pwa_short_name']) ? $options['pwa_short_name'] : 'PutraFiber';
    ?>
    <input type="text" name="putrafiber_options[pwa_short_name]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_pwa_icon_render() {
    $options = get_option('putrafiber_options');
    $value = isset($options['pwa_icon']) ? $options['pwa_icon'] : '';
    ?>
    <input type="hidden" name="putrafiber_options[pwa_icon]" id="pwa_icon" value="<?php echo esc_url($value); ?>">
    <button type="button" class="button putrafiber-upload-icon"><?php _e('Upload Icon', 'putrafiber'); ?></button>
    <button type="button" class="button putrafiber-remove-icon"><?php _e('Remove', 'putrafiber'); ?></button>
    <div class="icon-preview">
        <?php if ($value): ?>
            <img src="<?php echo esc_url($value); ?>" style="max-width: 150px; margin-top: 10px;">
        <?php endif; ?>
    </div>
    <p class="description"><?php _e('Recommended size: 512x512px', 'putrafiber'); ?></p>
    <?php
}

/**
 * Options Page HTML
 */
function putrafiber_options_page() {
    ?>
    <div class="wrap putrafiber-admin-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="#landing" class="nav-tab nav-tab-active"><?php _e('Landing Page', 'putrafiber'); ?></a>
            <a href="#contact" class="nav-tab"><?php _e('Contact', 'putrafiber'); ?></a>
            <a href="#social" class="nav-tab"><?php _e('Social Media', 'putrafiber'); ?></a>
            <a href="#seo" class="nav-tab"><?php _e('SEO', 'putrafiber'); ?></a>
            <a href="#pwa" class="nav-tab"><?php _e('PWA', 'putrafiber'); ?></a>
        </h2>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('putrafiber_options_group');
            
            echo '<div id="landing" class="tab-content active">';
            do_settings_sections('putrafiber-options');
            echo '</div>';
            
            submit_button(__('Save Settings', 'putrafiber'));
            ?>
        </form>
    </div>
    
    <style>
        .putrafiber-admin-wrap { max-width: 1200px; }
        .nav-tab-wrapper { margin-bottom: 20px; }
        .tab-content { display: none; background: white; padding: 20px; border: 1px solid #ccc; border-top: none; }
        .tab-content.active { display: block; }
        .form-table th { width: 250px; }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').removeClass('active');
            $($(this).attr('href')).addClass('active');
        });
        
        // Media Upload for Hero Image
        $('.putrafiber-upload-image').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var custom_uploader = wp.media({
                title: 'Select Hero Image',
                button: { text: 'Use this image' },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#hero_image').val(attachment.url);
                button.siblings('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 300px; margin-top: 10px;">');
            }).open();
        });
        
        $('.putrafiber-remove-image').on('click', function(e) {
            e.preventDefault();
            $('#hero_image').val('');
            $(this).siblings('.image-preview').html('');
        });
        
        // Media Upload for PWA Icon
        $('.putrafiber-upload-icon').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var custom_uploader = wp.media({
                title: 'Select PWA Icon',
                button: { text: 'Use this icon' },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#pwa_icon').val(attachment.url);
                button.siblings('.icon-preview').html('<img src="' + attachment.url + '" style="max-width: 150px; margin-top: 10px;">');
            }).open();
        });
        
        $('.putrafiber-remove-icon').on('click', function(e) {
            e.preventDefault();
            $('#pwa_icon').val('');
            $(this).siblings('.icon-preview').html('');
        });
        
        // Gallery Upload for Portfolio
        $('.portfolio-gallery-upload').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var custom_uploader = wp.media({
                title: 'Select Gallery Images',
                button: { text: 'Add to Gallery' },
                multiple: true
            }).on('select', function() {
                var attachments = custom_uploader.state().get('selection').toJSON();
                var ids = [];
                var html = '';
                
                $.each(attachments, function(index, attachment) {
                    ids.push(attachment.id);
                    html += '<img src="' + attachment.url + '" style="max-width: 100px; margin: 5px;">';
                });
                
                $('#portfolio_gallery').val(ids.join(','));
                $('.portfolio-gallery-preview').html(html);
            }).open();
        });
    });
    </script>
    <?php
}
