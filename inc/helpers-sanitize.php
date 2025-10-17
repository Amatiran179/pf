<?php
if (!defined('ABSPATH')) exit;

/**
 * Helper Sanitization & Escaping for theme CRUD
 */
if (!function_exists('pf_clean_text')) {
    function pf_clean_text($value) {
        return sanitize_text_field(wp_unslash($value ?? ''));
    }
}
if (!function_exists('pf_clean_url')) {
    function pf_clean_url($value) {
        return esc_url_raw(trim($value ?? ''));
    }
}
if (!function_exists('pf_clean_html')) {
    function pf_clean_html($value) {
        return wp_kses_post($value ?? '');
    }
}
if (!function_exists('pf_clean_int')) {
    function pf_clean_int($value) {
        return intval($value ?? 0);
    }
}
if (!function_exists('pf_clean_float')) {
    function pf_clean_float($value) {
        return floatval(preg_replace('/[^0-9.]/', '', (string)($value ?? '0')));
    }
}
if (!function_exists('pf_output_attr')) {
    function pf_output_attr($value) {
        return esc_attr($value ?? '');
    }
}
if (!function_exists('pf_output_html')) {
    function pf_output_html($value) {
        return wp_kses_post($value ?? '');
    }
}
if (!function_exists('pf_output_url')) {
    function pf_output_url($value) {
        return esc_url($value ?? '');
    }
}
