<?php
/**
 * Theme Options Panel - COMPLETELY FIXED VERSION
 * 
 * @package PutraFiber
 * @version 2.1.0 - FIXED (Checkbox unchecked now saves correctly)
 * 
 * FIXES:
 * - Checkbox unchecked values now save properly
 * - Tab navigation working correctly
 * - LocalBusiness settings added
 * - Proper sanitization for all field types
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
 * Register Settings - WITH SANITIZE CALLBACK
 */
function putrafiber_settings_init() {
    register_setting(
        'putrafiber_options_group', 
        'putrafiber_options',
        'putrafiber_sanitize_options' // ← CRITICAL: This fixes checkbox bug
    );
    
    // ===================================================================
    // LANDING PAGE SECTION
    // ===================================================================
    add_settings_section(
        'putrafiber_landing_section',
        __('Landing Page Settings', 'putrafiber'),
        'putrafiber_landing_section_callback',
        'putrafiber-landing'
    );
    
    add_settings_field('hero_title', __('Hero Title', 'putrafiber'), 'putrafiber_hero_title_render', 'putrafiber-landing', 'putrafiber_landing_section');
    add_settings_field('hero_description', __('Hero Description', 'putrafiber'), 'putrafiber_hero_description_render', 'putrafiber-landing', 'putrafiber_landing_section');
    add_settings_field('hero_image', __('Hero Background Image', 'putrafiber'), 'putrafiber_hero_image_render', 'putrafiber-landing', 'putrafiber_landing_section');
    add_settings_field('hero_cta_text', __('Hero CTA Button Text', 'putrafiber'), 'putrafiber_hero_cta_text_render', 'putrafiber-landing', 'putrafiber_landing_section');
    
    // ===================================================================
    // CONTACT SECTION
    // ===================================================================
    add_settings_section(
        'putrafiber_contact_section',
        __('Contact Settings', 'putrafiber'),
        'putrafiber_contact_section_callback',
        'putrafiber-contact'
    );
    
    add_settings_field('whatsapp_number', __('WhatsApp Number', 'putrafiber'), 'putrafiber_whatsapp_number_render', 'putrafiber-contact', 'putrafiber_contact_section');
    add_settings_field('company_address', __('Company Address', 'putrafiber'), 'putrafiber_company_address_render', 'putrafiber-contact', 'putrafiber_contact_section');
    add_settings_field('company_phone', __('Company Phone', 'putrafiber'), 'putrafiber_company_phone_render', 'putrafiber-contact', 'putrafiber_contact_section');
    add_settings_field('company_email', __('Company Email', 'putrafiber'), 'putrafiber_company_email_render', 'putrafiber-contact', 'putrafiber_contact_section');
    add_settings_field('business_hours', __('Business Hours', 'putrafiber'), 'putrafiber_business_hours_render', 'putrafiber-contact', 'putrafiber_contact_section');
    add_settings_field('google_maps_embed', __('Google Maps Embed URL', 'putrafiber'), 'putrafiber_google_maps_render', 'putrafiber-contact', 'putrafiber_contact_section');
    
    // ===================================================================
    // SOCIAL MEDIA SECTION
    // ===================================================================
    add_settings_section(
        'putrafiber_social_section',
        __('Social Media', 'putrafiber'),
        'putrafiber_social_section_callback',
        'putrafiber-social'
    );
    
    add_settings_field('facebook_url', __('Facebook URL', 'putrafiber'), 'putrafiber_facebook_url_render', 'putrafiber-social', 'putrafiber_social_section');
    add_settings_field('instagram_url', __('Instagram URL', 'putrafiber'), 'putrafiber_instagram_url_render', 'putrafiber-social', 'putrafiber_social_section');
    add_settings_field('youtube_url', __('YouTube URL', 'putrafiber'), 'putrafiber_youtube_url_render', 'putrafiber-social', 'putrafiber_social_section');
    add_settings_field('linkedin_url', __('LinkedIn URL', 'putrafiber'), 'putrafiber_linkedin_url_render', 'putrafiber-social', 'putrafiber_social_section');
    add_settings_field('twitter_url', __('Twitter URL', 'putrafiber'), 'putrafiber_twitter_url_render', 'putrafiber-social', 'putrafiber_social_section');
    
    // ===================================================================
    // SEO SECTION
    // ===================================================================
    add_settings_section(
        'putrafiber_seo_section',
        __('SEO Settings', 'putrafiber'),
        'putrafiber_seo_section_callback',
        'putrafiber-seo'
    );
    
    add_settings_field('enable_schema', __('Enable Schema.org', 'putrafiber'), 'putrafiber_enable_schema_render', 'putrafiber-seo', 'putrafiber_seo_section');
    add_settings_field('enable_aggregate_rating', __('Enable Aggregate Rating', 'putrafiber'), 'putrafiber_enable_rating_render', 'putrafiber-seo', 'putrafiber_seo_section');
    add_settings_field('company_rating', __('Company Rating (1-5)', 'putrafiber'), 'putrafiber_company_rating_render', 'putrafiber-seo', 'putrafiber_seo_section');
    add_settings_field('review_count', __('Review Count', 'putrafiber'), 'putrafiber_review_count_render', 'putrafiber-seo', 'putrafiber_seo_section');
    
    // ===================================================================
    // PWA SECTION
    // ===================================================================
    add_settings_section(
        'putrafiber_pwa_section',
        __('PWA Settings', 'putrafiber'),
        'putrafiber_pwa_section_callback',
        'putrafiber-pwa'
    );
    
    add_settings_field('enable_pwa', __('Enable PWA', 'putrafiber'), 'putrafiber_enable_pwa_render', 'putrafiber-pwa', 'putrafiber_pwa_section');
    add_settings_field('pwa_name', __('App Name', 'putrafiber'), 'putrafiber_pwa_name_render', 'putrafiber-pwa', 'putrafiber_pwa_section');
    add_settings_field('pwa_short_name', __('App Short Name', 'putrafiber'), 'putrafiber_pwa_short_name_render', 'putrafiber-pwa', 'putrafiber_pwa_section');
    add_settings_field('pwa_icon', __('PWA Icon (512x512)', 'putrafiber'), 'putrafiber_pwa_icon_render', 'putrafiber-pwa', 'putrafiber_pwa_section');
    
    // ===================================================================
    // LOCALBUSINESS SECTION
    // ===================================================================
    add_settings_section(
        'putrafiber_localbusiness_section',
        __('LocalBusiness Schema Settings', 'putrafiber'),
        'putrafiber_localbusiness_section_callback',
        'putrafiber-localbusiness'
    );
    
    add_settings_field('enable_localbusiness', __('Enable LocalBusiness Schema', 'putrafiber'), 'putrafiber_enable_localbusiness_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('business_type', __('Business Type', 'putrafiber'), 'putrafiber_business_type_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('business_description', __('Business Description', 'putrafiber'), 'putrafiber_business_description_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('company_name', __('Company Name', 'putrafiber'), 'putrafiber_company_name_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('company_city', __('City', 'putrafiber'), 'putrafiber_company_city_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('company_province', __('Province', 'putrafiber'), 'putrafiber_company_province_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('company_postal_code', __('Postal Code', 'putrafiber'), 'putrafiber_company_postal_code_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('company_latitude', __('Latitude', 'putrafiber'), 'putrafiber_company_latitude_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('company_longitude', __('Longitude', 'putrafiber'), 'putrafiber_company_longitude_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('opening_hours', __('Opening Hours', 'putrafiber'), 'putrafiber_opening_hours_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('price_range', __('Price Range', 'putrafiber'), 'putrafiber_price_range_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('payment_methods', __('Payment Methods', 'putrafiber'), 'putrafiber_payment_methods_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('service_areas', __('Service Areas', 'putrafiber'), 'putrafiber_service_areas_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
    add_settings_field('localbusiness_pages', __('Show on Pages', 'putrafiber'), 'putrafiber_localbusiness_pages_render', 'putrafiber-localbusiness', 'putrafiber_localbusiness_section');
}
add_action('admin_init', 'putrafiber_settings_init');

/**
 * ===================================================================
 * SANITIZE OPTIONS - CRITICAL FIX FOR CHECKBOX BUG
 * ===================================================================
 */
function putrafiber_sanitize_options($input) {
    $output = array();
    
    // Text fields
    $text_fields = array(
        'hero_title', 'hero_description', 'hero_image', 'hero_cta_text',
        'whatsapp_number', 'company_address', 'company_phone', 'company_email', 
        'business_hours', 'google_maps_embed',
        'facebook_url', 'instagram_url', 'youtube_url', 'linkedin_url', 'twitter_url',
        'company_rating', 'review_count',
        'pwa_name', 'pwa_short_name', 'pwa_icon',
        'business_type', 'business_description', 'company_name',
        'company_city', 'company_province', 'company_postal_code',
        'company_latitude', 'company_longitude', 'price_range'
    );
    
    foreach ($text_fields as $field) {
        if (isset($input[$field])) {
            $value = $input[$field];
            
            // URL fields
            if (in_array($field, array('hero_image', 'pwa_icon', 'facebook_url', 'instagram_url', 'youtube_url', 'linkedin_url', 'twitter_url', 'google_maps_embed'))) {
                $output[$field] = esc_url_raw($value);
            }
            // Email fields
            elseif ($field === 'company_email') {
                $output[$field] = sanitize_email($value);
            }
            // Textarea fields
            elseif (in_array($field, array('hero_description', 'company_address', 'business_description'))) {
                $output[$field] = sanitize_textarea_field($value);
            }
            // Number fields
            elseif (in_array($field, array('company_rating', 'review_count', 'company_latitude', 'company_longitude'))) {
                $output[$field] = sanitize_text_field($value);
            }
            // Regular text
            else {
                $output[$field] = sanitize_text_field($value);
            }
        } else {
            $output[$field] = ''; // Set empty if not present
        }
    }
    
    // ===================================================================
    // CHECKBOX FIELDS - CRITICAL FIX
    // Must explicitly set '0' if not checked
    // ===================================================================
    $checkbox_fields = array(
        'enable_schema',
        'enable_aggregate_rating',
        'enable_pwa',
        'enable_localbusiness'
    );
    
    foreach ($checkbox_fields as $field) {
        // If checkbox is checked, value = '1', otherwise = '0'
        $output[$field] = (isset($input[$field]) && $input[$field] === '1') ? '1' : '0';
    }
    
    // ===================================================================
    // ARRAY FIELDS
    // ===================================================================
    
    // Payment Methods (checkbox array)
    $output['payment_methods'] = array();
    if (isset($input['payment_methods']) && is_array($input['payment_methods'])) {
        foreach ($input['payment_methods'] as $method) {
            $output['payment_methods'][] = sanitize_text_field($method);
        }
    }
    
    // LocalBusiness Pages (checkbox array)
    $output['localbusiness_pages'] = array();
    if (isset($input['localbusiness_pages']) && is_array($input['localbusiness_pages'])) {
        foreach ($input['localbusiness_pages'] as $page) {
            $output['localbusiness_pages'][] = sanitize_text_field($page);
        }
    }
    
    // Opening Hours (repeater)
    $output['opening_hours'] = array();
    if (isset($input['opening_hours']) && is_array($input['opening_hours'])) {
        foreach ($input['opening_hours'] as $schedule) {
            if (!empty($schedule['opens']) && !empty($schedule['closes'])) {
                $days = '';
                if (isset($schedule['days'])) {
                    // Handle multi-select
                    $days = is_array($schedule['days']) 
                        ? implode(',', array_map('sanitize_text_field', $schedule['days']))
                        : sanitize_text_field($schedule['days']);
                }
                
                $output['opening_hours'][] = array(
                    'days' => $days,
                    'opens' => sanitize_text_field($schedule['opens']),
                    'closes' => sanitize_text_field($schedule['closes'])
                );
            }
        }
    }
    
    // Service Areas (textarea to array)
    $output['service_areas'] = array();
    if (isset($input['service_areas'])) {
        if (is_array($input['service_areas'])) {
            $output['service_areas'] = array_map('sanitize_text_field', $input['service_areas']);
        } else {
            // Convert textarea (line-separated) to array
            $lines = explode("\n", $input['service_areas']);
            $output['service_areas'] = array_values(array_filter(array_map('trim', array_map('sanitize_text_field', $lines))));
        }
    }
    
    return $output;
}

/**
 * Section Callbacks
 */
function putrafiber_landing_section_callback() {
    echo '<p>' . __('Customize your landing page hero section and main content.', 'putrafiber') . '</p>';
}

function putrafiber_contact_section_callback() {
    echo '<p>' . __('Enter your company contact information.', 'putrafiber') . '</p>';
}

function putrafiber_social_section_callback() {
    echo '<p>' . __('Add your social media profile URLs.', 'putrafiber') . '</p>';
}

function putrafiber_seo_section_callback() {
    echo '<p>' . __('Configure SEO and Schema.org settings.', 'putrafiber') . '</p>';
}

function putrafiber_pwa_section_callback() {
    echo '<p>' . __('Configure Progressive Web App settings.', 'putrafiber') . '</p>';
}

function putrafiber_localbusiness_section_callback() {
    echo '<p>' . __('Configure LocalBusiness schema for better local SEO. This will appear on selected pages.', 'putrafiber') . '</p>';
}

/**
 * Field Render Functions - Landing Page
 */
function putrafiber_hero_title_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['hero_title']) ? $options['hero_title'] : 'Kontraktor Waterpark & Playground Fiberglass Terpercaya';
    ?>
    <input type="text" name="putrafiber_options[hero_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_hero_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['hero_description']) ? $options['hero_description'] : 'Spesialis pembuatan waterpark, waterboom, playground indoor & outdoor, perosotan fiberglass, kolam renang, dan berbagai produk fiberglass berkualitas tinggi.';
    ?>
    <textarea name="putrafiber_options[hero_description]" rows="4" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_hero_image_render() {
    $options = get_option('putrafiber_options', array());
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
    $options = get_option('putrafiber_options', array());
    $value = isset($options['hero_cta_text']) ? $options['hero_cta_text'] : 'Konsultasi Gratis';
    ?>
    <input type="text" name="putrafiber_options[hero_cta_text]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

/**
 * Field Render Functions - Contact
 */
function putrafiber_whatsapp_number_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['whatsapp_number']) ? $options['whatsapp_number'] : '085642318455';
    ?>
    <input type="text" name="putrafiber_options[whatsapp_number]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <p class="description"><?php _e('Format: 628xxxxxxxxxx or 08xxxxxxxxxx', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_company_address_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_address']) ? $options['company_address'] : 'Jl. Raya Industri No. 123, Jakarta, Indonesia';
    ?>
    <textarea name="putrafiber_options[company_address]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_company_phone_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_phone']) ? $options['company_phone'] : '021-12345678';
    ?>
    <input type="text" name="putrafiber_options[company_phone]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_company_email_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_email']) ? $options['company_email'] : 'info@putrafiber.com';
    ?>
    <input type="email" name="putrafiber_options[company_email]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_business_hours_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['business_hours']) ? $options['business_hours'] : 'Senin - Sabtu: 08:00 - 17:00 WIB';
    ?>
    <input type="text" name="putrafiber_options[business_hours]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_google_maps_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['google_maps_embed']) ? $options['google_maps_embed'] : '';
    ?>
    <textarea name="putrafiber_options[google_maps_embed]" rows="3" class="large-text" placeholder="https://www.google.com/maps/embed?pb=..."><?php echo esc_textarea($value); ?></textarea>
    <p class="description"><?php _e('Paste Google Maps embed URL here', 'putrafiber'); ?></p>
    <?php
}

/**
 * Field Render Functions - Social Media
 */
function putrafiber_facebook_url_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['facebook_url']) ? $options['facebook_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[facebook_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://facebook.com/putrafiber">
    <?php
}

function putrafiber_instagram_url_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['instagram_url']) ? $options['instagram_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[instagram_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://instagram.com/putrafiber">
    <?php
}

function putrafiber_youtube_url_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['youtube_url']) ? $options['youtube_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[youtube_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://youtube.com/@putrafiber">
    <?php
}

function putrafiber_linkedin_url_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['linkedin_url']) ? $options['linkedin_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[linkedin_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://linkedin.com/company/putrafiber">
    <?php
}

function putrafiber_twitter_url_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['twitter_url']) ? $options['twitter_url'] : '';
    ?>
    <input type="url" name="putrafiber_options[twitter_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://twitter.com/putrafiber">
    <?php
}

/**
 * Field Render Functions - SEO
 */
function putrafiber_enable_schema_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_schema']) ? $options['enable_schema'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_schema]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_schema]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Enable Schema.org JSON-LD markup', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_rating_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_aggregate_rating']) ? $options['enable_aggregate_rating'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_aggregate_rating]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_aggregate_rating]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Show aggregate rating in Organization schema', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_company_rating_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_rating']) ? $options['company_rating'] : '4.8';
    ?>
    <input type="number" name="putrafiber_options[company_rating]" value="<?php echo esc_attr($value); ?>" step="0.1" min="1" max="5" class="small-text">
    <p class="description"><?php _e('Rating from 1 to 5', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_review_count_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['review_count']) ? $options['review_count'] : '150';
    ?>
    <input type="number" name="putrafiber_options[review_count]" value="<?php echo esc_attr($value); ?>" class="small-text">
    <p class="description"><?php _e('Total number of reviews', 'putrafiber'); ?></p>
    <?php
}

/**
 * Field Render Functions - PWA
 */
function putrafiber_enable_pwa_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_pwa']) ? $options['enable_pwa'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_pwa]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_pwa]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Enable Progressive Web App features', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_pwa_name_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['pwa_name']) ? $options['pwa_name'] : 'PutraFiber';
    ?>
    <input type="text" name="putrafiber_options[pwa_name]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_pwa_short_name_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['pwa_short_name']) ? $options['pwa_short_name'] : 'PutraFiber';
    ?>
    <input type="text" name="putrafiber_options[pwa_short_name]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_pwa_icon_render() {
    $options = get_option('putrafiber_options', array());
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
 * Field Render Functions - LocalBusiness
 */
function putrafiber_enable_localbusiness_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_localbusiness']) ? $options['enable_localbusiness'] : '0';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_localbusiness]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_localbusiness]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Enable LocalBusiness Schema (for Google Maps & Local SEO)', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_business_type_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['business_type']) ? $options['business_type'] : 'LocalBusiness';
    
    $types = array(
        'LocalBusiness' => 'Local Business (General)',
        'Store' => 'Store / Shop',
        'Restaurant' => 'Restaurant',
        'HomeAndConstructionBusiness' => 'Home & Construction Business',
        'ProfessionalService' => 'Professional Service',
        'AutomotiveBusiness' => 'Automotive Business',
        'FinancialService' => 'Financial Service',
        'HealthAndBeautyBusiness' => 'Health & Beauty Business',
        'EntertainmentBusiness' => 'Entertainment Business',
        'LodgingBusiness' => 'Lodging Business',
    );
    ?>
    <select name="putrafiber_options[business_type]" class="regular-text">
        <?php foreach ($types as $type_value => $type_label) : ?>
            <option value="<?php echo esc_attr($type_value); ?>" <?php selected($value, $type_value); ?>><?php echo esc_html($type_label); ?></option>
        <?php endforeach; ?>
    </select>
    <p class="description"><?php _e('Select the type that best describes your business', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_business_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['business_description']) ? $options['business_description'] : get_bloginfo('description');
    ?>
    <textarea name="putrafiber_options[business_description]" rows="3" class="large-text" placeholder="Kontraktor waterpark profesional..."><?php echo esc_textarea($value); ?></textarea>
    <p class="description"><?php _e('Short description of your business (for schema)', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_company_name_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_name']) ? $options['company_name'] : get_bloginfo('name');
    ?>
    <input type="text" name="putrafiber_options[company_name]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_company_city_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_city']) ? $options['company_city'] : '';
    
    if (function_exists('putrafiber_get_indonesian_cities')) {
        $cities = putrafiber_get_indonesian_cities();
        ?>
        <select name="putrafiber_options[company_city]" id="lb_company_city" class="regular-text">
            <option value="">-- Select City --</option>
            <?php foreach ($cities as $city => $province) : ?>
                <option value="<?php echo esc_attr($city); ?>" data-province="<?php echo esc_attr($province); ?>" <?php selected($value, $city); ?>><?php echo esc_html($city); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    } else {
        ?>
        <input type="text" name="putrafiber_options[company_city]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="Jakarta">
        <?php
    }
}

function putrafiber_company_province_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_province']) ? $options['company_province'] : '';
    ?>
    <input type="text" name="putrafiber_options[company_province]" id="lb_company_province" value="<?php echo esc_attr($value); ?>" class="regular-text" readonly style="background: #f5f5f5;">
    <p class="description"><?php _e('Auto-filled based on selected city', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_company_postal_code_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_postal_code']) ? $options['company_postal_code'] : '';
    ?>
    <input type="text" name="putrafiber_options[company_postal_code]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="12345">
    <?php
}

function putrafiber_company_latitude_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_latitude']) ? $options['company_latitude'] : '';
    ?>
    <input type="text" name="putrafiber_options[company_latitude]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="-6.2088">
    <p class="description"><?php _e('Example: -6.2088 (Get from Google Maps)', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_company_longitude_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['company_longitude']) ? $options['company_longitude'] : '';
    ?>
    <input type="text" name="putrafiber_options[company_longitude]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="106.8456">
    <p class="description"><?php _e('Example: 106.8456 (Get from Google Maps)', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_opening_hours_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['opening_hours']) ? $options['opening_hours'] : array();
    
    if (empty($value)) {
        $value = array(
            array('days' => 'Monday,Tuesday,Wednesday,Thursday,Friday', 'opens' => '08:00', 'closes' => '17:00')
        );
    }
    ?>
    <div id="opening-hours-container">
        <?php foreach ($value as $index => $schedule) : ?>
        <div class="opening-hours-row" style="margin-bottom: 15px; padding: 15px; background: #f9f9f9; border-radius: 4px;">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end;">
                <div>
                    <label>Days</label>
                    <select name="putrafiber_options[opening_hours][<?php echo $index; ?>][days][]" multiple style="height: 80px; width: 100%;">
                        <?php 
                        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                        $selected_days = isset($schedule['days']) ? explode(',', $schedule['days']) : array();
                        foreach ($days as $day) {
                            $selected = in_array($day, $selected_days) ? 'selected' : '';
                            echo '<option value="' . $day . '" ' . $selected . '>' . $day . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label>Opens</label>
                    <input type="time" name="putrafiber_options[opening_hours][<?php echo $index; ?>][opens]" value="<?php echo esc_attr($schedule['opens'] ?? ''); ?>">
                </div>
                <div>
                    <label>Closes</label>
                    <input type="time" name="putrafiber_options[opening_hours][<?php echo $index; ?>][closes]" value="<?php echo esc_attr($schedule['closes'] ?? ''); ?>">
                </div>
                <div>
                    <?php if ($index > 0) : ?>
                    <button type="button" class="button remove-opening-hours" style="background: #dc3545; color: white; border-color: #dc3545;">Remove</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button" id="add-opening-hours">+ Add Schedule</button>
    <p class="description"><?php _e('Hold Ctrl/Cmd to select multiple days', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_price_range_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['price_range']) ? $options['price_range'] : '';
    ?>
    <input type="text" name="putrafiber_options[price_range]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="$$">
    <p class="description"><?php _e('Use $ symbols ($ = cheap, $$$ = expensive) or "Rp 100.000 - Rp 10.000.000"', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_payment_methods_render() {
    $options = get_option('putrafiber_options', array());
    $saved = isset($options['payment_methods']) ? $options['payment_methods'] : array();
    
    $methods = array(
        'Cash' => 'Cash',
        'Bank Transfer' => 'Bank Transfer',
        'Credit Card' => 'Credit Card',
        'Debit Card' => 'Debit Card',
        'E-Wallet' => 'E-Wallet (GoPay, OVO, Dana, etc)',
        'Installment' => 'Installment / Kredit',
    );
    ?>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
        <?php foreach ($methods as $method_value => $method_label) : ?>
        <label>
            <input type="checkbox" name="putrafiber_options[payment_methods][]" value="<?php echo esc_attr($method_value); ?>" <?php echo in_array($method_value, (array)$saved) ? 'checked' : ''; ?>>
            <?php echo esc_html($method_label); ?>
        </label>
        <?php endforeach; ?>
    </div>
    <?php
}

function putrafiber_service_areas_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['service_areas']) ? $options['service_areas'] : array();
    $text_value = is_array($value) ? implode("\n", $value) : $value;
    ?>
    <textarea name="putrafiber_options[service_areas]" rows="4" class="large-text" placeholder="Jakarta&#10;Bogor&#10;Depok&#10;Tangerang&#10;Bekasi"><?php echo esc_textarea($text_value); ?></textarea>
    <p class="description"><?php _e('One city per line. These are global service areas for LocalBusiness schema.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_localbusiness_pages_render() {
    $options = get_option('putrafiber_options', array());
    $saved = isset($options['localbusiness_pages']) ? $options['localbusiness_pages'] : array();
    
    $pages = get_pages();
    ?>
    <div style="max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #fafafa;">
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="putrafiber_options[localbusiness_pages][]" value="homepage" <?php echo in_array('homepage', (array)$saved) ? 'checked' : ''; ?>>
            <strong>Homepage</strong>
        </label>
        <?php foreach ($pages as $page) : ?>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="putrafiber_options[localbusiness_pages][]" value="<?php echo $page->ID; ?>" <?php echo in_array($page->ID, (array)$saved) ? 'checked' : ''; ?>>
            <?php echo esc_html($page->post_title); ?>
        </label>
        <?php endforeach; ?>
    </div>
    <p class="description"><?php _e('Select pages where LocalBusiness schema should appear (typically Contact, About pages)', 'putrafiber'); ?></p>
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
            <a href="#localbusiness" class="nav-tab"><?php _e('🏢 LocalBusiness', 'putrafiber'); ?></a>
        </h2>
        
        <form action="options.php" method="post">
            <?php settings_fields('putrafiber_options_group'); ?>
            
            <div id="landing" class="tab-content active">
                <?php do_settings_sections('putrafiber-landing'); ?>
            </div>
            
            <div id="contact" class="tab-content">
                <?php do_settings_sections('putrafiber-contact'); ?>
            </div>
            
            <div id="social" class="tab-content">
                <?php do_settings_sections('putrafiber-social'); ?>
            </div>
            
            <div id="seo" class="tab-content">
                <?php do_settings_sections('putrafiber-seo'); ?>
            </div>
            
            <div id="pwa" class="tab-content">
                <?php do_settings_sections('putrafiber-pwa'); ?>
            </div>
            
            <div id="localbusiness" class="tab-content">
                <?php do_settings_sections('putrafiber-localbusiness'); ?>
            </div>
            
            <?php submit_button(__('Save All Settings', 'putrafiber')); ?>
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
        
        // Tab Switching
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').removeClass('active');
            $(target).addClass('active');
        });
        
        // Hero Image Upload
        $('.putrafiber-upload-image').on('click', function(e) {
            e.preventDefault();
            var uploader = wp.media({
                title: 'Select Hero Image',
                button: { text: 'Use this image' },
                multiple: false
            }).on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                $('#hero_image').val(attachment.url);
                $('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 300px; margin-top: 10px;">');
            }).open();
        });
        
        $('.putrafiber-remove-image').on('click', function(e) {
            e.preventDefault();
            $('#hero_image').val('');
            $('.image-preview').html('');
        });
        
        // PWA Icon Upload
        $('.putrafiber-upload-icon').on('click', function(e) {
            e.preventDefault();
            var uploader = wp.media({
                title: 'Select PWA Icon',
                button: { text: 'Use this icon' },
                multiple: false
            }).on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                $('#pwa_icon').val(attachment.url);
                $('.icon-preview').html('<img src="' + attachment.url + '" style="max-width: 150px; margin-top: 10px;">');
            }).open();
        });
        
        $('.putrafiber-remove-icon').on('click', function(e) {
            e.preventDefault();
            $('#pwa_icon').val('');
            $('.icon-preview').html('');
        });
        
        // City auto-fill province
        $('#lb_company_city').on('change', function() {
            var province = $(this).find(':selected').data('province');
            $('#lb_company_province').val(province);
        });
        
        // Opening Hours - Add Schedule
        var openingHoursIndex = <?php echo count((array)(isset($options['opening_hours']) ? $options['opening_hours'] : array())); ?>;
        $('#add-opening-hours').on('click', function() {
            var html = '<div class="opening-hours-row" style="margin-bottom: 15px; padding: 15px; background: #f9f9f9; border-radius: 4px;">' +
                '<div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end;">' +
                '<div><label>Days</label><select name="putrafiber_options[opening_hours][' + openingHoursIndex + '][days][]" multiple style="height: 80px; width: 100%;">' +
                '<option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option>' +
                '<option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option>' +
                '<option value="Sunday">Sunday</option></select></div>' +
                '<div><label>Opens</label><input type="time" name="putrafiber_options[opening_hours][' + openingHoursIndex + '][opens]"></div>' +
                '<div><label>Closes</label><input type="time" name="putrafiber_options[opening_hours][' + openingHoursIndex + '][closes]"></div>' +
                '<div><button type="button" class="button remove-opening-hours" style="background: #dc3545; color: white; border-color: #dc3545;">Remove</button></div>' +
                '</div></div>';
            $('#opening-hours-container').append(html);
            openingHoursIndex++;
        });
        
        // Remove Opening Hours
        $(document).on('click', '.remove-opening-hours', function() {
            $(this).closest('.opening-hours-row').fadeOut(300, function() {
                $(this).remove();
            });
        });
        
    });
    </script>
    <?php
}