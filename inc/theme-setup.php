<?php
/**
 * Theme Setup Functions
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Content Width
 */
if (!isset($content_width)) {
    $content_width = 1200;
}

/**
 * Add Editor Styles
 */
function putrafiber_add_editor_styles() {
    add_editor_style('assets/css/editor-style.css');
}
add_action('admin_init', 'putrafiber_add_editor_styles');

/**
 * Register Custom Post Status for Products
 */
function putrafiber_register_post_status() {
    register_post_status('pre-order', array(
        'label'                     => _x('Pre-Order', 'post status', 'putrafiber'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Pre-Order <span class="count">(%s)</span>', 'Pre-Order <span class="count">(%s)</span>', 'putrafiber'),
    ));
}
add_action('init', 'putrafiber_register_post_status');

/**
 * Add Custom Mime Types
 */
function putrafiber_custom_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'putrafiber_custom_mime_types');

/**
 * Security Headers
 */
function putrafiber_security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
add_action('send_headers', 'putrafiber_security_headers');
