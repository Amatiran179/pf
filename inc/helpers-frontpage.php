<?php
/**
 * Front page helper utilities.
 *
 * Centralises landing page configuration so sections can be reordered,
 * toggled, and configured purely from Theme Options while still providing
 * opinionated defaults for first installs.
 *
 * @package PutraFiber
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Default section registry used for ordering/toggles fallback.
 *
 * @return array<string,array<string,mixed>>
 */
function putrafiber_frontpage_section_defaults() {
    return array(
        'hero'         => array('enabled' => true),
        'features'     => array('enabled' => true),
        'services'     => array('enabled' => true),
        'portfolio'    => array('enabled' => true),
        'cta'          => array('enabled' => true),
        'products'     => array('enabled' => true),
        'blog'         => array('enabled' => true),
        'testimonials' => array('enabled' => false),
        'partners'     => array('enabled' => false),
    );
}

/**
 * Get ordered list of section slugs that should render on the landing page.
 *
 * @return string[]
 */
function putrafiber_frontpage_sections() {
    $defaults = putrafiber_frontpage_section_defaults();
    $default_order = array_keys($defaults);

    $stored = putrafiber_get_option('front_sections_order', '');
    $order  = array();

    if (!empty($stored)) {
        $pieces = array_map('trim', explode(',', $stored));
        foreach ($pieces as $slug) {
            if ($slug !== '' && !in_array($slug, $order, true)) {
                $order[] = $slug;
            }
        }
    }

    if (empty($order)) {
        $order = $default_order;
    }

    $enabled = array();

    foreach ($order as $slug) {
        if (!isset($defaults[$slug])) {
            continue;
        }

        if (putrafiber_frontpage_section_enabled($slug)) {
            $enabled[] = $slug;
        }
    }

    return $enabled;
}

/**
 * Determine if a section is explicitly enabled.
 *
 * @param string $slug
 * @return bool
 */
function putrafiber_frontpage_section_enabled($slug) {
    $defaults   = putrafiber_frontpage_section_defaults();
    $default_on = isset($defaults[$slug]) ? !empty($defaults[$slug]['enabled']) : true;

    $key = 'enable_' . $slug . '_section';

    // Legacy support for older option keys.
    if ($slug === 'testimonials') {
        $legacy = putrafiber_get_option('enable_testimonials', $default_on ? '1' : '0');
        if ($legacy !== '' && $legacy !== null) {
            return $legacy === '1' || $legacy === 1 || $legacy === true;
        }
    } elseif ($slug === 'partners') {
        $legacy = putrafiber_get_option('enable_partners', $default_on ? '1' : '0');
        if ($legacy !== '' && $legacy !== null) {
            return $legacy === '1' || $legacy === 1 || $legacy === true;
        }
    }

    $value = putrafiber_get_option($key, $default_on ? '1' : '0');

    return $value === '1' || $value === 1 || $value === true;
}

/**
 * Render a specific section template safely.
 *
 * @param string $slug
 * @return void
 */
function putrafiber_render_frontpage_section($slug) {
    $allowed = array(
        'hero',
        'features',
        'services',
        'portfolio',
        'cta',
        'products',
        'blog',
        'testimonials',
        'partners',
    );

    if (!in_array($slug, $allowed, true)) {
        return;
    }

    $template = 'template-parts/sections/' . $slug;

    if (locate_template($template . '.php')) {
        get_template_part($template);
    }
}

/**
 * Retrieve a section specific option.
 *
 * @param string $section
 * @param string $field
 * @param string $default
 * @return string
 */
function putrafiber_frontpage_text($section, $field, $default = '') {
    $key = 'front_' . $section . '_' . $field;
    return putrafiber_get_option($key, $default);
}

/**
 * Get number of items to display for a given section.
 *
 * @param string $section
 * @param int    $default
 * @return int
 */
function putrafiber_frontpage_limit($section, $default) {
    $key   = 'front_' . $section . '_limit';
    $value = (int) putrafiber_get_option($key, $default);

    if ($value <= 0) {
        $value = $default;
    }

    return $value;
}

/**
 * Parse textarea repeater items using "Title|Description|Icon" format.
 *
 * @param string $option_key
 * @param array<int,array<string,string>> $defaults
 * @return array<int,array<string,string>>
 */
function putrafiber_frontpage_parse_repeater($option_key, $defaults = array()) {
    $raw = putrafiber_get_option($option_key, '');

    if (empty($raw)) {
        return $defaults;
    }

    $lines  = preg_split('/\r\n|\r|\n/', $raw);
    $parsed = array();

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = array_map('trim', explode('|', $line));

        $parsed[] = array(
            'title'       => isset($parts[0]) ? $parts[0] : '',
            'description' => isset($parts[1]) ? $parts[1] : '',
            'icon'        => isset($parts[2]) ? $parts[2] : '',
        );
    }

    if (empty($parsed)) {
        return $defaults;
    }

    return $parsed;
}

/**
 * Resolve icon identifier into inline SVG markup.
 *
 * @param string $icon
 * @return string
 */
function putrafiber_frontpage_icon_svg($icon) {
    $map = array(
        'shield'     => '<path d="M12 2l7 4v6c0 5-3.5 9.74-7 10-3.5-.26-7-5-7-10V6l7-4z"></path>',
        'wave'       => '<path d="M2 12s2-3 5-3 4 3 7 3 3-3 6-3 4 3 4 3v7H2z"></path>',
        'spark'      => '<polygon points="12 2 13.09 8.26 19 9 13.97 12.74 15.45 19 12 15.27 8.55 19 10.03 12.74 5 9 10.91 8.26 12 2"></polygon>',
        'drop'       => '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>',
        'star'       => '<polygon points="12 2 13.09 8.26 19 9 13.97 12.74 15.45 19 12 15.27 8.55 19 10.03 12.74 5 9 10.91 8.26 12 2"></polygon>',
        'globe'      => '<circle cx="12" cy="12" r="10"></circle><path d="M2 12h20"></path><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>',
        'gear'       => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
        'trophy'     => '<path d="M8 21h8"></path><path d="M12 17v4"></path><path d="M7 4h10"></path><path d="M17 4v3a5 5 0 0 1-5 5 5 5 0 0 1-5-5V4"></path><path d="M5 9a3 3 0 0 1-3-3V4h3"></path><path d="M19 9a3 3 0 0 0 3-3V4h-3"></path>',
        'compass'    => '<circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>',
    );

    $icon = trim((string) $icon);

    if ($icon === '') {
        return '';
    }

    if (strpos($icon, '<') !== false) {
        return $icon;
    }

    if (isset($map[$icon])) {
        return $map[$icon];
    }

    return $map['spark'];
}

/**
 * Retrieve configured accent colour.
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function putrafiber_frontpage_color($key, $default) {
    $value = putrafiber_get_option($key, $default);
    if (!empty($value) && preg_match('/^#([0-9a-fA-F]{3}){1,2}$/', $value)) {
        return $value;
    }
    return $default;
}

/**
 * Print CSS custom properties for landing page palette.
 *
 * @return void
 */
function putrafiber_frontpage_print_color_vars() {
    if (!is_front_page()) {
        return;
    }

    $primary = putrafiber_frontpage_color('front_primary_color', '#0f4c81');
    $gold    = putrafiber_frontpage_color('front_gold_color', '#f4c542');
    $dark    = putrafiber_frontpage_color('front_dark_color', '#0b1320');
    $water   = putrafiber_frontpage_color('front_water_color', 'rgba(15, 76, 129, 0.12)');

    echo '<style id="putrafiber-frontpage-vars">:root{';
    echo '--pf-front-primary:' . esc_attr($primary) . ';';
    echo '--pf-front-gold:' . esc_attr($gold) . ';';
    echo '--pf-front-dark:' . esc_attr($dark) . ';';
    echo '--pf-front-water:' . esc_attr($water) . ';';
    echo '}</style>';
}
add_action('wp_head', 'putrafiber_frontpage_print_color_vars', 20);

/**
 * Retrieve configured water bubble count for JS.
 *
 * @return int
 */
function putrafiber_frontpage_water_intensity() {
    $intensity = (int) putrafiber_get_option('front_water_intensity', 8);
    if ($intensity < 3) {
        $intensity = 3;
    }
    if ($intensity > 24) {
        $intensity = 24;
    }
    return $intensity;
}
