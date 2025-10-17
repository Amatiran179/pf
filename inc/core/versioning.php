<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('pf_theme_version')) {
    function pf_theme_version() {
        $theme = wp_get_theme();
        return $theme && $theme->get('Version') ? $theme->get('Version') : '1.0.0';
    }
}

if (!function_exists('pf_asset_version')) {
    function pf_asset_version($relative_path) {
        $path = get_template_directory() . '/' . ltrim($relative_path, '/');
        if (file_exists($path)) {
            $mtime = @filemtime($path);
            if ($mtime) return (string) $mtime;
        }
        return pf_theme_version();
    }
}
