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

    add_settings_field('hero_highlight', __('Hero Highlight Text', 'putrafiber'), 'putrafiber_hero_highlight_render', 'putrafiber-landing', 'putrafiber_landing_section');
    add_settings_field('hero_secondary_cta', __('Hero Secondary CTA Label', 'putrafiber'), 'putrafiber_hero_secondary_cta_render', 'putrafiber-landing', 'putrafiber_landing_section');
    add_settings_field('hero_secondary_url', __('Hero Secondary CTA URL', 'putrafiber'), 'putrafiber_hero_secondary_url_render', 'putrafiber-landing', 'putrafiber_landing_section');

    add_settings_section(
        'putrafiber_landing_layout_section',
        __('Layout & Effects', 'putrafiber'),
        'putrafiber_landing_layout_section_callback',
        'putrafiber-landing'
    );

    add_settings_field('enable_hero_section', __('Tampilkan Hero', 'putrafiber'), 'putrafiber_enable_hero_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_features_section', __('Tampilkan Features', 'putrafiber'), 'putrafiber_enable_features_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_services_section', __('Tampilkan Services', 'putrafiber'), 'putrafiber_enable_services_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_portfolio_section', __('Tampilkan Portfolio', 'putrafiber'), 'putrafiber_enable_portfolio_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_products_section', __('Tampilkan Products', 'putrafiber'), 'putrafiber_enable_products_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_blog_section', __('Tampilkan Blog', 'putrafiber'), 'putrafiber_enable_blog_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_cta_section', __('Tampilkan CTA', 'putrafiber'), 'putrafiber_enable_cta_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_testimonials_section', __('Tampilkan Testimonials', 'putrafiber'), 'putrafiber_enable_testimonials_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('enable_partners_section', __('Tampilkan Partners', 'putrafiber'), 'putrafiber_enable_partners_section_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');

    add_settings_field('front_sections_order', __('Section Order', 'putrafiber'), 'putrafiber_front_sections_order_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('front_enable_parallax', __('Enable Hero Parallax', 'putrafiber'), 'putrafiber_front_enable_parallax_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('front_water_intensity', __('Water Bubble Intensity', 'putrafiber'), 'putrafiber_front_water_intensity_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('front_primary_color', __('Primary Accent Colour', 'putrafiber'), 'putrafiber_front_primary_color_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('front_gold_color', __('Gold Accent Colour', 'putrafiber'), 'putrafiber_front_gold_color_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('front_dark_color', __('Dark Accent Colour', 'putrafiber'), 'putrafiber_front_dark_color_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');
    add_settings_field('front_water_color', __('Water Overlay Colour', 'putrafiber'), 'putrafiber_front_water_color_render', 'putrafiber-landing', 'putrafiber_landing_layout_section');

    add_settings_section(
        'putrafiber_landing_copy_section',
        __('Section Headlines & Content', 'putrafiber'),
        'putrafiber_landing_copy_section_callback',
        'putrafiber-landing'
    );

    add_settings_field('front_features_title', __('Features Title', 'putrafiber'), 'putrafiber_front_features_title_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_features_description', __('Features Description', 'putrafiber'), 'putrafiber_front_features_description_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_features_items', __('Feature Highlights', 'putrafiber'), 'putrafiber_front_features_items_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');

    add_settings_field('front_services_title', __('Services Title', 'putrafiber'), 'putrafiber_front_services_title_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_services_description', __('Services Description', 'putrafiber'), 'putrafiber_front_services_description_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_services_items', __('Services Items', 'putrafiber'), 'putrafiber_front_services_items_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');

    add_settings_field('front_portfolio_title', __('Portfolio Title', 'putrafiber'), 'putrafiber_front_portfolio_title_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_portfolio_description', __('Portfolio Description', 'putrafiber'), 'putrafiber_front_portfolio_description_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_portfolio_limit', __('Portfolio Items Limit', 'putrafiber'), 'putrafiber_front_portfolio_limit_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');

    add_settings_field('front_products_title', __('Product Title', 'putrafiber'), 'putrafiber_front_products_title_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_products_description', __('Product Description', 'putrafiber'), 'putrafiber_front_products_description_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_products_limit', __('Product Items Limit', 'putrafiber'), 'putrafiber_front_products_limit_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');

    add_settings_field('front_blog_title', __('Blog Title', 'putrafiber'), 'putrafiber_front_blog_title_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_blog_description', __('Blog Description', 'putrafiber'), 'putrafiber_front_blog_description_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_blog_limit', __('Blog Posts Limit', 'putrafiber'), 'putrafiber_front_blog_limit_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_blog_manual_posts', __('Blog Slot Manual Order', 'putrafiber'), 'putrafiber_front_blog_manual_posts_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');

    add_settings_field('front_cta_title', __('CTA Title', 'putrafiber'), 'putrafiber_front_cta_title_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_cta_description', __('CTA Description', 'putrafiber'), 'putrafiber_front_cta_description_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_cta_primary_text', __('CTA Primary Button Text', 'putrafiber'), 'putrafiber_front_cta_primary_text_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_cta_primary_url', __('CTA Primary Button URL', 'putrafiber'), 'putrafiber_front_cta_primary_url_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_cta_secondary_text', __('CTA Secondary Button Text', 'putrafiber'), 'putrafiber_front_cta_secondary_text_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    add_settings_field('front_cta_secondary_url', __('CTA Secondary Button URL', 'putrafiber'), 'putrafiber_front_cta_secondary_url_render', 'putrafiber-landing', 'putrafiber_landing_copy_section');
    
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
    add_settings_field('meta_description', __('Default Meta Description', 'putrafiber'), 'putrafiber_meta_description_render', 'putrafiber-seo', 'putrafiber_seo_section');
    add_settings_field('meta_keywords', __('Default Meta Keywords', 'putrafiber'), 'putrafiber_meta_keywords_render', 'putrafiber-seo', 'putrafiber_seo_section');
    add_settings_field('og_image', __('Default Social Share Image', 'putrafiber'), 'putrafiber_og_image_render', 'putrafiber-seo', 'putrafiber_seo_section');
    add_settings_field('twitter_username', __('Twitter Username', 'putrafiber'), 'putrafiber_twitter_username_render', 'putrafiber-seo', 'putrafiber_seo_section');
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

    add_settings_section(
        'putrafiber_schema_advanced_section',
        __('Schema Advanced', 'putrafiber'),
        'putrafiber_schema_advanced_section_callback',
        'putrafiber-schema-advanced'
    );

    add_settings_field('enable_schema_advanced', __('Enable Schema Advanced Layer', 'putrafiber'), 'putrafiber_enable_schema_advanced_render', 'putrafiber-schema-advanced', 'putrafiber_schema_advanced_section');
    add_settings_field('cta_priority_order', __('CTA Priority Order', 'putrafiber'), 'putrafiber_cta_priority_order_render', 'putrafiber-schema-advanced', 'putrafiber_schema_advanced_section');
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
    $url_fields = array(
        'hero_image',
        'hero_secondary_url',
        'google_maps_embed',
        'facebook_url', 'instagram_url', 'youtube_url', 'linkedin_url', 'twitter_url',
        'pwa_icon',
        'front_cta_primary_url', 'front_cta_secondary_url',
        'og_image'
    );

    foreach ($url_fields as $field) {
        $output[$field] = isset($input[$field]) ? esc_url_raw($input[$field]) : '';
    }

    $email_fields = array('company_email');
    foreach ($email_fields as $field) {
        $output[$field] = isset($input[$field]) ? sanitize_email($input[$field]) : '';
    }

    $textarea_fields = array(
        'hero_description', 'company_address', 'business_description',
        'front_features_description', 'front_services_description', 'front_portfolio_description',
        'front_products_description', 'front_cta_description',
        'front_features_items', 'front_services_items', 'front_blog_manual_posts', 'meta_description'
    );
    foreach ($textarea_fields as $field) {
        $output[$field] = isset($input[$field]) ? sanitize_textarea_field($input[$field]) : '';
    }

    $output['front_blog_description'] = isset($input['front_blog_description'])
        ? wp_kses_post($input['front_blog_description'])
        : '';

    $color_fields = array('front_primary_color', 'front_gold_color', 'front_dark_color');
    foreach ($color_fields as $field) {
        $output[$field] = isset($input[$field]) ? sanitize_hex_color($input[$field]) : '';
    }

    $number_fields = array(
        'company_rating', 'review_count', 'company_latitude', 'company_longitude',
        'front_water_intensity', 'front_portfolio_limit', 'front_products_limit', 'front_blog_limit'
    );
    foreach ($number_fields as $field) {
        $output[$field] = isset($input[$field]) ? sanitize_text_field($input[$field]) : '';
    }

    $text_fields = array(
        'hero_title', 'hero_cta_text', 'hero_highlight', 'hero_secondary_cta',
        'whatsapp_number', 'company_phone', 'business_hours',
        'pwa_name', 'pwa_short_name',
        'business_type', 'company_name', 'company_city', 'company_province', 'company_postal_code', 'price_range',
        'front_sections_order',
        'front_features_title', 'front_services_title', 'front_portfolio_title', 'front_products_title', 'front_blog_title', 'front_cta_title',
        'front_cta_primary_text', 'front_cta_secondary_text',
        'front_water_color', 'meta_keywords', 'twitter_username'
    );

    foreach ($text_fields as $field) {
        $output[$field] = isset($input[$field]) ? sanitize_text_field($input[$field]) : '';
    }
    
    // ===================================================================
    // CHECKBOX FIELDS - CRITICAL FIX
    // Must explicitly set '0' if not checked
    // ===================================================================
    $checkbox_fields = array(
        'enable_schema',
        'enable_aggregate_rating',
        'enable_pwa',
        'enable_localbusiness',
        'enable_schema_advanced',
        'front_enable_parallax',
        'enable_hero_section',
        'enable_features_section',
        'enable_services_section',
        'enable_portfolio_section',
        'enable_products_section',
        'enable_blog_section',
        'enable_cta_section',
        'enable_testimonials_section',
        'enable_partners_section'
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

    // CTA Priority Order (multi select)
    $output['cta_priority_order'] = array();
    if (isset($input['cta_priority_order']) && is_array($input['cta_priority_order'])) {
        foreach ($input['cta_priority_order'] as $slug) {
            $sanitized = sanitize_key($slug);
            if ($sanitized !== '') {
                $output['cta_priority_order'][] = $sanitized;
            }
        }

        $output['cta_priority_order'] = array_values(array_unique($output['cta_priority_order']));
    }

    return $output;
}

/**
 * Section Callbacks
 */
function putrafiber_landing_section_callback() {
    echo '<p>' . __('Customize your landing page hero section and main content.', 'putrafiber') . '</p>';
}

function putrafiber_landing_layout_section_callback() {
    echo '<p>' . __('Control landing page ordering, parallax and signature colour palette.', 'putrafiber') . '</p>';
}

function putrafiber_landing_copy_section_callback() {
    echo '<p>' . __('Update every headline, description, and repeater item that appears on the homepage sections.', 'putrafiber') . '</p>';
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

function putrafiber_schema_advanced_section_callback() {
    echo '<p>' . __('Kelola Schema Advanced Layer, termasuk prioritas CTA dan output JSON-LD tunggal.', 'putrafiber') . '</p>';
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

function putrafiber_hero_highlight_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['hero_highlight']) ? $options['hero_highlight'] : '20+ Tahun Menghadirkan Wahana Air Spektakuler';
    ?>
    <input type="text" name="putrafiber_options[hero_highlight]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <p class="description"><?php _e('Teks pendek untuk menonjolkan kredibilitas pada hero.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_hero_secondary_cta_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['hero_secondary_cta']) ? $options['hero_secondary_cta'] : __('Lihat Portofolio', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[hero_secondary_cta]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <p class="description"><?php _e('Label tombol sekunder untuk mengarahkan pengunjung ke portofolio atau katalog.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_hero_secondary_url_render() {
    $options = get_option('putrafiber_options', array());
    $default = function_exists('get_post_type_archive_link') ? get_post_type_archive_link('portfolio') : home_url('/');
    $value = isset($options['hero_secondary_url']) ? $options['hero_secondary_url'] : $default;
    ?>
    <input type="url" name="putrafiber_options[hero_secondary_url]" value="<?php echo esc_url($value); ?>" class="large-text">
    <p class="description"><?php _e('URL tujuan tombol sekunder. Contoh: halaman portofolio.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_sections_order_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_sections_order']) ? $options['front_sections_order'] : 'hero,features,services,portfolio,cta,products,blog';
    ?>
    <input type="text" name="putrafiber_options[front_sections_order]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <p class="description"><?php _e('Masukkan slug section dipisah koma untuk menentukan urutan. Slug tersedia: hero, features, services, portfolio, cta, products, blog, testimonials, partners.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_enable_parallax_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_enable_parallax']) ? $options['front_enable_parallax'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[front_enable_parallax]" value="0">
        <input type="checkbox" name="putrafiber_options[front_enable_parallax]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Aktifkan animasi parallax lembut pada hero untuk efek dinamis.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_hero_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_hero_section']) ? $options['enable_hero_section'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_hero_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_hero_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan section hero di landing page.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_features_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_features_section']) ? $options['enable_features_section'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_features_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_features_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan highlight keunggulan perusahaan.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_services_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_services_section']) ? $options['enable_services_section'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_services_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_services_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan daftar layanan utama.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_portfolio_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_portfolio_section']) ? $options['enable_portfolio_section'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_portfolio_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_portfolio_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan project portofolio terbaru.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_products_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_products_section']) ? $options['enable_products_section'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_products_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_products_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan katalog produk unggulan.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_blog_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_blog_section']) ? $options['enable_blog_section'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_blog_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_blog_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan artikel dan berita terbaru.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_cta_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_cta_section']) ? $options['enable_cta_section'] : '1';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_cta_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_cta_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Aktifkan call-to-action strategis.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_testimonials_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_testimonials_section']) ? $options['enable_testimonials_section'] : '0';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_testimonials_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_testimonials_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan testimoni pelanggan pada landing page.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_enable_partners_section_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['enable_partners_section']) ? $options['enable_partners_section'] : '0';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_partners_section]" value="0">
        <input type="checkbox" name="putrafiber_options[enable_partners_section]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Tampilkan logo mitra/klien pada landing page.', 'putrafiber'); ?>
    </label>
    <?php
}

function putrafiber_front_water_intensity_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_water_intensity']) ? (int) $options['front_water_intensity'] : 8;
    ?>
    <input type="number" name="putrafiber_options[front_water_intensity]" value="<?php echo esc_attr($value); ?>" min="3" max="24" class="small-text">
    <p class="description"><?php _e('Jumlah gelembung air dekoratif (3 - 24). Semakin besar semakin ramai.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_primary_color_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_primary_color']) ? $options['front_primary_color'] : '#0f75ff';
    ?>
    <input type="text" name="putrafiber_options[front_primary_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#0f75ff">
    <p class="description"><?php _e('Warna biru elektrik untuk nuansa air yang modern.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_gold_color_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_gold_color']) ? $options['front_gold_color'] : '#f9c846';
    ?>
    <input type="text" name="putrafiber_options[front_gold_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#f9c846">
    <p class="description"><?php _e('Aksen emas mencolok untuk highlight premium.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_dark_color_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_dark_color']) ? $options['front_dark_color'] : '#0b142b';
    ?>
    <input type="text" name="putrafiber_options[front_dark_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#0b142b">
    <p class="description"><?php _e('Sentuhan gelap untuk menciptakan kedalaman dan kontras.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_water_color_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_water_color']) ? $options['front_water_color'] : 'rgba(15, 76, 129, 0.12)';
    ?>
    <input type="text" name="putrafiber_options[front_water_color]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <p class="description"><?php _e('Nilai warna (menerima rgba) untuk overlay efek air.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_features_title_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_features_title']) ? $options['front_features_title'] : __('Kelebihan PutraFiber', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_features_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_front_features_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_features_description']) ? $options['front_features_description'] : __('Kami menggabungkan inovasi fiberglass dengan rekayasa konstruksi berkelas dunia.', 'putrafiber');
    ?>
    <textarea name="putrafiber_options[front_features_description]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_front_features_items_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_features_items']) ? $options['front_features_items'] : "Garansi 5 Tahun|Jaminan kualitas dan layanan purna jual responsif.|shield\nTim Berpengalaman|Didukung insinyur dan artisan fiberglass bersertifikat.|trophy\nTeknologi Mutakhir|Produksi modern dengan standar keamanan internasional.|gear";
    ?>
    <textarea name="putrafiber_options[front_features_items]" rows="6" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <p class="description"><?php _e('Gunakan format: Judul|Deskripsi|Icon. Icon opsional (shield, wave, spark, drop, star, globe, gear, trophy, compass). Satu item per baris.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_services_title_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_services_title']) ? $options['front_services_title'] : __('Solusi Water Attraction Lengkap', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_services_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_front_services_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_services_description']) ? $options['front_services_description'] : __('Dari masterplan, fabrikasi, hingga instalasi turn-key untuk wahana air dan playground.', 'putrafiber');
    ?>
    <textarea name="putrafiber_options[front_services_description]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_front_services_items_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_services_items']) ? $options['front_services_items'] : "Waterpark Design|Masterplan dan konsep kreatif wahana air.|wave\nPlayground Indoor|Area bermain aman dengan material premium.|drop\nFiberglass Custom|Produksi custom sesuai kebutuhan proyek Anda.|spark";
    ?>
    <textarea name="putrafiber_options[front_services_items]" rows="6" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <p class="description"><?php _e('Format sama dengan fitur: Judul|Deskripsi|Icon (opsional).', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_portfolio_title_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_portfolio_title']) ? $options['front_portfolio_title'] : __('Portofolio Unggulan', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_portfolio_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_front_portfolio_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_portfolio_description']) ? $options['front_portfolio_description'] : __('Lihat bagaimana kami mentransformasi area kosong menjadi destinasi air spektakuler.', 'putrafiber');
    ?>
    <textarea name="putrafiber_options[front_portfolio_description]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_front_portfolio_limit_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_portfolio_limit']) ? (int) $options['front_portfolio_limit'] : 6;
    ?>
    <input type="number" name="putrafiber_options[front_portfolio_limit]" value="<?php echo esc_attr($value); ?>" min="3" max="12" class="small-text">
    <?php
}

function putrafiber_front_products_title_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_products_title']) ? $options['front_products_title'] : __('Produk Terlaris', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_products_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_front_products_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_products_description']) ? $options['front_products_description'] : __('Pilihan wahana dan perosotan fiberglass yang siap dikirim ke lokasi Anda.', 'putrafiber');
    ?>
    <textarea name="putrafiber_options[front_products_description]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_front_products_limit_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_products_limit']) ? (int) $options['front_products_limit'] : 6;
    ?>
    <input type="number" name="putrafiber_options[front_products_limit]" value="<?php echo esc_attr($value); ?>" min="3" max="12" class="small-text">
    <?php
}

function putrafiber_front_blog_title_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_blog_title']) ? $options['front_blog_title'] : __('Artikel & Insight Terbaru', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_blog_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_front_blog_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_blog_description']) ? $options['front_blog_description'] : __('Strategi operasional waterpark, tips maintenance, dan berita terbaru industri rekreasi air.', 'putrafiber');

    wp_editor(
        $value,
        'putrafiber_front_blog_description',
        array(
            'textarea_name' => 'putrafiber_options[front_blog_description]',
            'textarea_rows' => 6,
            'media_buttons' => false,
            'teeny'         => true,
            'tinymce'       => array(
                'toolbar1' => 'formatselect,bold,italic,underline,link,unlink,bullist,numlist,blockquote',
                'toolbar2' => '',
            ),
            'quicktags'     => array(
                'buttons' => 'strong,em,link,ul,ol,li,code',
            ),
        )
    );

    echo '<p class="description">' . esc_html__('Gunakan HTML untuk mengatur struktur konten artikel di landing page.', 'putrafiber') . '</p>';
}

function putrafiber_front_blog_limit_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_blog_limit']) ? (int) $options['front_blog_limit'] : 3;
    ?>
    <input type="number" name="putrafiber_options[front_blog_limit]" value="<?php echo esc_attr($value); ?>" min="3" max="9" class="small-text">
    <p class="description"><?php _e('Jumlah artikel yang ditampilkan pada landing page.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_front_blog_manual_posts_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_blog_manual_posts']) ? $options['front_blog_manual_posts'] : '';
    ?>
    <textarea name="putrafiber_options[front_blog_manual_posts]" rows="4" class="large-text code" placeholder="Artikel 1 | 123&#10;Artikel 2 | 456"><?php echo esc_textarea($value); ?></textarea>
    <p class="description">
        <?php _e('Satu baris per slot. Gunakan format <strong>Label | ID Artikel</strong>. Urutan baris menentukan posisi di landing page.', 'putrafiber'); ?>
    </p>
    <p class="description">
        <?php _e('Kosongkan untuk menampilkan artikel terbaru secara otomatis.', 'putrafiber'); ?>
    </p>
    <?php
}

function putrafiber_front_cta_title_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_cta_title']) ? $options['front_cta_title'] : __('Siap Memulai Proyek Ikonik?', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_cta_title]" value="<?php echo esc_attr($value); ?>" class="large-text">
    <?php
}

function putrafiber_front_cta_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_cta_description']) ? $options['front_cta_description'] : __('Tim konsultan kami siap membantu menghitung kebutuhan, estimasi biaya, hingga timeline pembangunan.', 'putrafiber');
    ?>
    <textarea name="putrafiber_options[front_cta_description]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function putrafiber_front_cta_primary_text_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_cta_primary_text']) ? $options['front_cta_primary_text'] : __('Konsultasi Sekarang', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_cta_primary_text]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_front_cta_primary_url_render() {
    $options = get_option('putrafiber_options', array());
    $default = putrafiber_whatsapp_link();
    $value = isset($options['front_cta_primary_url']) ? $options['front_cta_primary_url'] : $default;
    ?>
    <input type="url" name="putrafiber_options[front_cta_primary_url]" value="<?php echo esc_url($value); ?>" class="large-text">
    <?php
}

function putrafiber_front_cta_secondary_text_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_cta_secondary_text']) ? $options['front_cta_secondary_text'] : __('Download Company Profile', 'putrafiber');
    ?>
    <input type="text" name="putrafiber_options[front_cta_secondary_text]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function putrafiber_front_cta_secondary_url_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['front_cta_secondary_url']) ? $options['front_cta_secondary_url'] : home_url('/company-profile.pdf');
    ?>
    <input type="url" name="putrafiber_options[front_cta_secondary_url]" value="<?php echo esc_url($value); ?>" class="large-text">
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

function putrafiber_meta_description_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['meta_description']) ? $options['meta_description'] : get_bloginfo('description');
    ?>
    <textarea name="putrafiber_options[meta_description]" rows="4" class="large-text" maxlength="170"><?php echo esc_textarea($value); ?></textarea>
    <p class="description"><?php _e('Fallback meta description used when individual content does not define one.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_meta_keywords_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['meta_keywords']) ? $options['meta_keywords'] : '';
    ?>
    <input type="text" name="putrafiber_options[meta_keywords]" value="<?php echo esc_attr($value); ?>" class="large-text" placeholder="waterpark, playground fiberglass, kontraktor waterboom">
    <p class="description"><?php _e('Optional comma separated keywords as a global fallback. Individual content can override.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_og_image_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['og_image']) ? $options['og_image'] : '';
    ?>
    <input type="hidden" name="putrafiber_options[og_image]" id="og_image" value="<?php echo esc_url($value); ?>">
    <button type="button" class="button putrafiber-upload-image"><?php _e('Choose Image', 'putrafiber'); ?></button>
    <button type="button" class="button putrafiber-remove-image"><?php _e('Remove', 'putrafiber'); ?></button>
    <div class="image-preview">
        <?php if ($value): ?>
            <img src="<?php echo esc_url($value); ?>" style="max-width: 300px; margin-top: 10px;" alt="<?php esc_attr_e('Default share image preview', 'putrafiber'); ?>">
        <?php endif; ?>
    </div>
    <p class="description"><?php _e('Used as the default Open Graph/Twitter Card image when a post does not have its own.', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_twitter_username_render() {
    $options = get_option('putrafiber_options', array());
    $value = isset($options['twitter_username']) ? $options['twitter_username'] : '';
    ?>
    <input type="text" name="putrafiber_options[twitter_username]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="@putrafiber">
    <p class="description"><?php _e('Displayed in Twitter Card metadata. Include the @ symbol.', 'putrafiber'); ?></p>
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
            <a href="#schema-advanced" class="nav-tab"><?php _e('Schema Advanced', 'putrafiber'); ?></a>
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

            <div id="schema-advanced" class="tab-content">
                <?php do_settings_sections('putrafiber-schema-advanced'); ?>
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

function putrafiber_enable_schema_advanced_render() {
    $options = get_option('putrafiber_options', array());
    $value = !empty($options['enable_schema_advanced']) ? '1' : '0';
    ?>
    <label>
        <input type="hidden" name="putrafiber_options[enable_schema_advanced]" value="0">
        <input type="checkbox" id="enable_schema_advanced" name="putrafiber_options[enable_schema_advanced]" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Aktifkan Schema Advanced Layer', 'putrafiber'); ?>
    </label>
    <p class="description"><?php _e('Aktifkan sistem schema modular (anti-duplicate, CTA mapping, single JSON-LD).', 'putrafiber'); ?></p>
    <?php
}

function putrafiber_cta_priority_order_render() {
    $options = get_option('putrafiber_options', array());
    $saved   = isset($options['cta_priority_order']) && is_array($options['cta_priority_order']) ? array_values(array_unique(array_map('sanitize_key', $options['cta_priority_order']))) : array();
    $choices = array(
        'wa_primary'          => __('WA Primary', 'putrafiber'),
        'wa_secondary'        => __('WA Secondary', 'putrafiber'),
        'download_brosur'     => __('Download Brosur', 'putrafiber'),
        'kunjungi_lokasi'     => __('Kunjungi Lokasi', 'putrafiber'),
        'katalog_hubungi_cs'  => __('Katalog / Hubungi CS', 'putrafiber'),
        'portfolio_wisata'    => __('Portfolio Wisata (TouristAttraction)', 'putrafiber'),
    );

    $ordered = !empty($saved) ? $saved : array_keys($choices);
    $final   = array();
    foreach ($ordered as $key) {
        if (isset($choices[$key])) {
            $final[$key] = $choices[$key];
        }
    }
    foreach ($choices as $key => $label) {
        if (!isset($final[$key])) {
            $final[$key] = $label;
        }
    }
    ?>
    <select id="cta_priority_order" name="putrafiber_options[cta_priority_order][]" multiple size="6" class="widefat">
        <?php foreach ($final as $key => $label): ?>
            <?php $selected = empty($saved) || in_array($key, $saved, true); ?>
            <option value="<?php echo esc_attr($key); ?>" <?php selected($selected, true); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
    </select>
    <p style="margin-top:8px;">
        <button type="button" class="button" id="cta-priority-move-up"><?php esc_html_e('Naik', 'putrafiber'); ?></button>
        <button type="button" class="button" id="cta-priority-move-down"><?php esc_html_e('Turun', 'putrafiber'); ?></button>
    </p>
    <p class="description"><?php _e('Urutan prioritas CTA ketika lebih dari satu CTA aktif.', 'putrafiber'); ?></p>
    <script>
    jQuery(function($){
        $('#cta-priority-move-up').on('click', function(e){
            e.preventDefault();
            var $select = $('#cta_priority_order');
            $select.find('option:selected').each(function(){
                var $prev = $(this).prev();
                if ($prev.length) {
                    $(this).insertBefore($prev);
                }
            });
        });
        $('#cta-priority-move-down').on('click', function(e){
            e.preventDefault();
            var $select = $('#cta_priority_order');
            $($select.find('option:selected').get().reverse()).each(function(){
                var $next = $(this).next();
                if ($next.length) {
                    $(this).insertAfter($next);
                }
            });
        });
    });
    </script>
    <?php
}
