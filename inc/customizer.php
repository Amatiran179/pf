<?php
/**
 * Theme Customizer
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

function putrafiber_customize_register($wp_customize) {
    
    // Logo Upload
    $wp_customize->add_setting('putrafiber_logo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'putrafiber_logo', array(
        'label' => __('Upload Logo', 'putrafiber'),
        'section' => 'title_tagline',
        'settings' => 'putrafiber_logo',
    )));
    
    // Primary Color
    $wp_customize->add_setting('putrafiber_primary_color', array(
        'default' => '#00BCD4',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'putrafiber_primary_color', array(
        'label' => __('Primary Color', 'putrafiber'),
        'section' => 'colors',
        'settings' => 'putrafiber_primary_color',
    )));
    
    // Footer Section
    $wp_customize->add_section('putrafiber_footer', array(
        'title' => __('Footer Settings', 'putrafiber'),
        'priority' => 120,
    ));
    
    $wp_customize->add_setting('putrafiber_footer_text', array(
        'default' => '© 2024 PutraFiber. All Rights Reserved.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('putrafiber_footer_text', array(
        'label' => __('Footer Copyright Text', 'putrafiber'),
        'section' => 'putrafiber_footer',
        'type' => 'text',
    ));
}
add_action('customize_register', 'putrafiber_customize_register');

/**
 * Output Customizer CSS
 */
function putrafiber_customizer_css() {
    $primary_color = get_theme_mod('putrafiber_primary_color', '#00BCD4');
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo esc_attr($primary_color); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'putrafiber_customizer_css');
