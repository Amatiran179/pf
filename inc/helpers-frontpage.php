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
if (!defined('ABSPATH')) exit;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registry of available core sections with descriptive labels.
 *
 * @return array<string,array<string,string|bool>>
 */
function putrafiber_frontpage_section_catalog() {
    return array(
        'hero' => array(
            'label'       => __('Hero', 'putrafiber'),
            'description' => __('Bagian pembuka dengan CTA utama dan highlight value proposition.', 'putrafiber'),
        ),
        'features' => array(
            'label'       => __('Features', 'putrafiber'),
            'description' => __('Sorotan keunggulan yang membedakan bisnis Anda.', 'putrafiber'),
        ),
        'services' => array(
            'label'       => __('Services', 'putrafiber'),
            'description' => __('Daftar layanan utama atau paket yang ditawarkan.', 'putrafiber'),
        ),
        'portfolio' => array(
            'label'       => __('Portfolio', 'putrafiber'),
            'description' => __('Kumpulan proyek atau studi kasus terbaik.', 'putrafiber'),
        ),
        'cta' => array(
            'label'       => __('CTA', 'putrafiber'),
            'description' => __('Ajakan bertindak kuat untuk memicu konversi.', 'putrafiber'),
        ),
        'products' => array(
            'label'       => __('Products', 'putrafiber'),
            'description' => __('Katalog produk unggulan yang ingin ditonjolkan.', 'putrafiber'),
        ),
        'blog' => array(
            'label'       => __('Blog', 'putrafiber'),
            'description' => __('Artikel terbaru sebagai bukti keahlian dan SEO.', 'putrafiber'),
        ),
        'testimonials' => array(
            'label'       => __('Testimonials', 'putrafiber'),
            'description' => __('Testimoni pelanggan dan social proof lainnya.', 'putrafiber'),
        ),
        'partners' => array(
            'label'       => __('Partners', 'putrafiber'),
            'description' => __('Logo partner, klien, atau sertifikasi penting.', 'putrafiber'),
        ),
    );
}

function putrafiber_frontpage_allowed_custom_layouts() {
    return array('full', 'split-left', 'split-right');
}

function putrafiber_frontpage_normalise_layout($layout, $default = 'full') {
    $layout  = is_string($layout) ? strtolower(trim($layout)) : '';
    $allowed = putrafiber_frontpage_allowed_custom_layouts();

    return in_array($layout, $allowed, true) ? $layout : $default;
}

function putrafiber_frontpage_allowed_heading_tags() {
    return array('h2', 'h3', 'h4');
}

function putrafiber_frontpage_normalise_heading_tag($tag, $default = 'h2') {
    $tag     = is_string($tag) ? strtolower(trim($tag)) : '';
    $allowed = putrafiber_frontpage_allowed_heading_tags();

    return in_array($tag, $allowed, true) ? $tag : $default;
}

function putrafiber_frontpage_sanitize_anchor($anchor) {
    return sanitize_title($anchor);
}

function putrafiber_frontpage_sanitize_color_value($value) {
    if (!is_string($value)) {
        return '';
    }

    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $value = wp_strip_all_tags($value);

    return preg_replace('/[^#a-z0-9(),.%\s\-]/i', '', $value);
}

/**
 * Retrieve normalised colour presets from theme options.
 *
 * @return array<string,array{colors:array<string,string>,name:string}>
 */
function putrafiber_frontpage_color_presets() {
    $raw = putrafiber_get_option('front_color_presets', array());
    $presets = array();

    if (!is_array($raw)) {
        return $presets;
    }

    foreach ($raw as $preset) {
        if (!is_array($preset) || empty($preset['id'])) {
            continue;
        }

        $id   = sanitize_key($preset['id']);
        $name = isset($preset['name']) ? sanitize_text_field($preset['name']) : $id;

        $colors = array();
        if (!empty($preset['colors']) && is_array($preset['colors'])) {
            foreach ($preset['colors'] as $key => $value) {
                $color_key = sanitize_key($key);
                if ($color_key === '') {
                    continue;
                }

                $colors[$color_key] = putrafiber_frontpage_sanitize_color_value((string) $value);
            }
        }

        $presets[$id] = array(
            'name'   => $name,
            'colors' => $colors,
        );
    }

    return $presets;
}

/**
 * Return currently active colour preset.
 *
 * @return array<string,string>
 */
function putrafiber_frontpage_active_preset_colors() {
    $presets = putrafiber_frontpage_color_presets();

    if (empty($presets)) {
        return array();
    }

    $active_id = putrafiber_get_option('front_active_preset', '');
    if ($active_id && isset($presets[$active_id])) {
        return array_filter($presets[$active_id]['colors']);
    }

    $first = reset($presets);
    if ($first && isset($first['colors']) && is_array($first['colors'])) {
        return array_filter($first['colors']);
    }

    return array();
}

/**
 * Default section registry used for ordering/toggles fallback.
 *
 * @return array<string,array<string,mixed>>
 */
function putrafiber_frontpage_section_defaults() {
    $catalog = putrafiber_frontpage_section_catalog();

    $defaults = array(
        'hero'         => true,
        'features'     => true,
        'services'     => true,
        'portfolio'    => true,
        'cta'          => true,
        'products'     => true,
        'blog'         => true,
        'testimonials' => false,
        'partners'     => false,
    );

    $result = array();
    foreach ($defaults as $slug => $enabled_default) {
        $result[$slug] = array(
            'enabled' => (bool) $enabled_default,
        );

        if (isset($catalog[$slug]['label'])) {
            $result[$slug]['label'] = $catalog[$slug]['label'];
        }
        if (isset($catalog[$slug]['description'])) {
            $result[$slug]['description'] = $catalog[$slug]['description'];
        }
    }

    return $result;
}

/**
 * Legacy toggle resolver used for backwards compatibility.
 *
 * @param string $slug
 * @param bool   $default_on
 * @return bool
 */
function putrafiber_frontpage_section_enabled_legacy($slug, $default_on = true) {
    $defaults = putrafiber_frontpage_section_defaults();
    if ($default_on === null && isset($defaults[$slug])) {
        $default_on = !empty($defaults[$slug]['enabled']);
    }

    if (function_exists('putrafiber_get_bool_option')) {
        if ($slug === 'testimonials') {
            return putrafiber_get_bool_option('enable_testimonials', $default_on);
        }

        if ($slug === 'partners') {
            return putrafiber_get_bool_option('enable_partners', $default_on);
        }

        return putrafiber_get_bool_option('enable_' . $slug . '_section', $default_on);
    }

    $default_flag = $default_on ? '1' : '0';

    if ($slug === 'testimonials') {
        $legacy = putrafiber_get_option('enable_testimonials', $default_flag);
        if ($legacy !== '' && $legacy !== null) {
            return $legacy === '1' || $legacy === 1 || $legacy === true;
        }
    } elseif ($slug === 'partners') {
        $legacy = putrafiber_get_option('enable_partners', $default_flag);
        if ($legacy !== '' && $legacy !== null) {
            return $legacy === '1' || $legacy === 1 || $legacy === true;
        }
    }

    $value = putrafiber_get_option('enable_' . $slug . '_section', $default_flag);

    return $value === '1' || $value === 1 || $value === true;
}

/**
 * Retrieve the saved builder configuration, normalised with defaults.
 *
 * @return array<int,array<string,mixed>>
 */
function putrafiber_frontpage_builder_config() {
    $catalog   = putrafiber_frontpage_section_catalog();
    $defaults  = putrafiber_frontpage_section_defaults();
    $stored    = putrafiber_get_option('front_sections_builder', array());
    $config    = array();

    if (is_array($stored) && !empty($stored)) {
        foreach ($stored as $item) {
            if (!is_array($item) || empty($item['id'])) {
                continue;
            }

            $id   = sanitize_key($item['id']);
            $type = (isset($item['type']) && $item['type'] === 'custom') ? 'custom' : 'core';

            $entry = array(
                'id'      => $id,
                'type'    => $type,
                'enabled' => !empty($item['enabled']),
            );

            if ($type === 'core') {
                if (isset($catalog[$id]['label'])) {
                    $entry['label'] = $catalog[$id]['label'];
                } elseif (isset($item['label'])) {
                    $entry['label'] = sanitize_text_field($item['label']);
                }

                if (isset($catalog[$id]['description'])) {
                    $entry['description'] = $catalog[$id]['description'];
                }
            } else {
                $entry['label']       = isset($item['label']) && $item['label'] !== '' ? sanitize_text_field($item['label']) : __('Section Kustom', 'putrafiber');
                $entry['title']       = isset($item['title']) ? sanitize_text_field($item['title']) : '';
                $entry['subtitle']    = isset($item['subtitle']) ? sanitize_text_field($item['subtitle']) : '';
                $entry['content']     = isset($item['content']) ? wp_kses_post($item['content']) : '';
                $entry['background']  = isset($item['background']) ? putrafiber_frontpage_sanitize_color_value($item['background']) : '';
                $entry['text_color']  = isset($item['text_color']) ? putrafiber_frontpage_sanitize_color_value($item['text_color']) : '';
                $entry['button_text'] = isset($item['button_text']) ? sanitize_text_field($item['button_text']) : '';
                $entry['button_url']  = isset($item['button_url']) ? esc_url($item['button_url']) : '';
                $entry['layout']      = isset($item['layout']) ? putrafiber_frontpage_normalise_layout($item['layout'], 'full') : 'full';
                $entry['media']       = isset($item['media']) ? esc_url($item['media']) : '';
                $entry['media_alt']   = isset($item['media_alt']) ? sanitize_text_field($item['media_alt']) : '';
                $entry['anchor']      = isset($item['anchor']) ? putrafiber_frontpage_sanitize_anchor($item['anchor']) : '';
                $entry['heading_tag'] = isset($item['heading_tag']) ? putrafiber_frontpage_normalise_heading_tag($item['heading_tag'], 'h2') : 'h2';
            }

            $config[] = $entry;
        }
    }

    if (empty($config)) {
        $order_option = putrafiber_get_option('front_sections_order', '');
        $order        = array();

        if (!empty($order_option)) {
            $pieces = array_map('trim', explode(',', $order_option));
            foreach ($pieces as $slug) {
                if ($slug !== '' && !in_array($slug, $order, true)) {
                    $order[] = $slug;
                }
            }
        }

        if (empty($order)) {
            $order = array_keys($defaults);
        }

        foreach ($order as $slug) {
            if (!isset($defaults[$slug])) {
                continue;
            }

            $config[] = array(
                'id'          => $slug,
                'type'        => 'core',
                'enabled'     => putrafiber_frontpage_section_enabled_legacy($slug, !empty($defaults[$slug]['enabled'])),
                'label'       => isset($catalog[$slug]['label']) ? $catalog[$slug]['label'] : ucfirst($slug),
                'description' => isset($catalog[$slug]['description']) ? $catalog[$slug]['description'] : '',
            );
        }
    }

    return $config;
}

/**
 * Locate custom section payload by slug.
 *
 * @param string $slug
 * @return array<string,mixed>|null
 */
function putrafiber_frontpage_custom_section($slug) {
    $config = putrafiber_frontpage_builder_config();

    foreach ($config as $section) {
        if (!is_array($section) || empty($section['id'])) {
            continue;
        }

        if ($section['id'] !== $slug) {
            continue;
        }

        if (!isset($section['type']) || $section['type'] !== 'custom') {
            return null;
        }

        $title = isset($section['title']) && $section['title'] !== ''
            ? $section['title']
            : (isset($section['label']) ? $section['label'] : '');

        $section['title']      = $title;
        $section['label']      = isset($section['label']) && $section['label'] !== '' ? $section['label'] : $title;
        $section['subtitle']   = isset($section['subtitle']) ? $section['subtitle'] : '';
        $section['content']    = isset($section['content']) ? $section['content'] : '';
        $section['background'] = isset($section['background']) ? $section['background'] : '';
        $section['text_color'] = isset($section['text_color']) ? $section['text_color'] : '';
        $section['button_text']= isset($section['button_text']) ? $section['button_text'] : '';
        $section['button_url'] = isset($section['button_url']) ? $section['button_url'] : '';
        $section['layout']      = isset($section['layout']) ? putrafiber_frontpage_normalise_layout($section['layout'], 'full') : 'full';
        $section['media']       = isset($section['media']) ? $section['media'] : '';
        $section['media_alt']   = isset($section['media_alt']) ? $section['media_alt'] : '';
        $section['anchor']      = isset($section['anchor']) ? putrafiber_frontpage_sanitize_anchor($section['anchor']) : '';
        $section['heading_tag'] = isset($section['heading_tag']) ? putrafiber_frontpage_normalise_heading_tag($section['heading_tag'], 'h2') : 'h2';

        return $section;
    }

    return null;
}

/**
 * Get ordered list of section slugs that should render on the landing page.
 *
 * @return string[]
 */
function putrafiber_frontpage_sections() {
    $builder_config = putrafiber_frontpage_builder_config();
    $builder_order  = array();

    if (!empty($builder_config)) {
        foreach ($builder_config as $section) {
            if (!is_array($section) || empty($section['id'])) {
                continue;
            }

            if (!empty($section['enabled']) && !in_array($section['id'], $builder_order, true)) {
                $builder_order[] = $section['id'];
            }
        }

        if (!empty($builder_order)) {
            return $builder_order;
        }
    }

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
    $builder_config = putrafiber_frontpage_builder_config();

    if (!empty($builder_config)) {
        foreach ($builder_config as $section) {
            if (!is_array($section) || empty($section['id'])) {
                continue;
            }

            if ($section['id'] === $slug) {
                return !empty($section['enabled']);
            }
        }
    }

    return putrafiber_frontpage_section_enabled_legacy($slug);
}

/**
 * Render a specific section template safely.
 *
 * @param string $slug
 * @return void
 */
function putrafiber_render_frontpage_section($slug) {
    $custom = putrafiber_frontpage_custom_section($slug);
    if ($custom) {
        get_template_part('template-parts/sections/custom', null, array('section' => $custom));
        return;
    }

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
 * Normalise card layout settings for the requested section.
 *
 * @param string $section
 * @return array{layout:string,style:string,animation:string,columns:int,background_effect:string}
 */
function putrafiber_frontpage_card_settings($section) {
    $defaults = array(
        'layout'            => 'grid',
        'style'             => 'glass',
        'animation'         => 'auto',
        'columns'           => 3,
        'background_effect' => 'none',
    );

    switch ($section) {
        case 'services':
            $defaults['layout']            = 'grid';
            $defaults['style']             = 'glass';
            $defaults['animation']         = 'auto';
            $defaults['background_effect'] = 'none';
            break;
        case 'blog':
            $defaults['layout']            = 'grid';
            $defaults['style']             = 'glass';
            $defaults['animation']         = 'auto';
            $defaults['columns']           = 3;
            $defaults['background_effect'] = 'glass';
            break;
        default:
            // keep defaults for features and other contexts
            break;
    }

    if ($section === 'blog') {
        $size_raw = putrafiber_get_option('front_blog_card_density', 'comfortable');
    } else {
        $size_raw = putrafiber_get_option('front_' . $section . '_card_size', 'regular');
    }

    $layout            = putrafiber_get_option('front_' . $section . '_layout', $defaults['layout']);
    $style             = putrafiber_get_option('front_' . $section . '_card_style', $defaults['style']);
    $animation         = putrafiber_get_option('front_' . $section . '_card_animation', $defaults['animation']);
    $columns           = (int) putrafiber_get_option('front_' . $section . '_card_columns', $defaults['columns']);
    $background_effect = putrafiber_get_option('front_' . $section . '_background_effect', $defaults['background_effect']);

    $settings = array(
        'layout'            => putrafiber_frontpage_normalise_choice($layout, array('grid', 'masonry', 'list', 'stacked', 'magazine', 'carousel'), $defaults['layout']),
        'style'             => putrafiber_frontpage_normalise_choice($style, array('glass', 'solid', 'soft', 'outline'), $defaults['style']),
        'animation'         => putrafiber_frontpage_normalise_choice($animation, array('auto', 'rise', 'zoom', 'tilt', 'float', 'pulse', 'fade', 'slide', 'none'), $defaults['animation']),
        'columns'           => max(1, min(6, $columns)),
        'background_effect' => putrafiber_frontpage_normalise_choice($background_effect, array('none', 'gradient', 'bubbles', 'mesh', 'flare', 'glass', 'waves', 'aurora'), $defaults['background_effect']),
    );

    if ($section === 'blog') {
        $settings['size'] = putrafiber_frontpage_normalise_choice($size_raw, array('comfortable', 'compact', 'expanded'), 'comfortable');
    } else {
        $settings['size'] = putrafiber_frontpage_normalise_choice($size_raw, array('compact', 'regular', 'spacious'), 'regular');
    }

    return $settings;
}

/**
 * Normalise a choice value against an allowed list.
 *
 * @param string $value
 * @param array<int,string> $allowed
 * @param string $default
 * @return string
 */
function putrafiber_frontpage_normalise_choice($value, $allowed, $default) {
    $value = sanitize_key($value);
    if (!in_array($value, $allowed, true)) {
        return $default;
    }
    return $value;
}

/**
 * Retrieve advanced card configuration for a section.
 * Falls back to legacy repeater data when the builder is empty.
 *
 * @param string $section Section slug (features|services|blog).
 * @param string $legacy_key Legacy repeater option key for backwards compatibility.
 * @param array<int,array<string,string>> $fallback Default items.
 * @return array<int,array<string,mixed>>
 */
function putrafiber_frontpage_cards($section, $legacy_key, $fallback = array()) {
    $option_key = 'front_' . $section . '_cards';
    $cards      = putrafiber_get_option($option_key, array());

    if (is_array($cards) && !empty($cards)) {
        return array_values(array_filter(array_map('putrafiber_frontpage_normalise_card', $cards)));
    }

    $normalised = array();
    if ($legacy_key !== '') {
        $legacy_items = putrafiber_frontpage_parse_repeater($legacy_key, $fallback);
        foreach ($legacy_items as $item) {
            $normalised[] = putrafiber_frontpage_normalise_card(array(
                'title'       => isset($item['title']) ? $item['title'] : '',
                'description' => isset($item['description']) ? $item['description'] : '',
                'icon_type'   => 'icon',
                'icon'        => isset($item['icon']) ? $item['icon'] : '',
            ));
        }
    }

    if (empty($normalised) && !empty($fallback)) {
        foreach ($fallback as $item) {
            $normalised[] = putrafiber_frontpage_normalise_card(array(
                'title'       => isset($item['title']) ? $item['title'] : '',
                'description' => isset($item['description']) ? $item['description'] : '',
                'icon_type'   => 'icon',
                'icon'        => isset($item['icon']) ? $item['icon'] : '',
            ));
        }
    }

    return array_values(array_filter($normalised));
}

/**
 * Guarantee all expected fields exist for a card entry.
 *
 * @param array<string,mixed> $card
 * @return array<string,mixed>
 */
function putrafiber_frontpage_normalise_card($card) {
    $defaults = array(
        'title'        => '',
        'subtitle'     => '',
        'description'  => '',
        'icon_type'    => 'icon',
        'icon'         => '',
        'image'        => '',
        'image_alt'    => '',
        'image_size'   => 'auto',
        'badge'        => '',
        'highlight'    => '',
        'list'         => array(),
        'list_effect'  => '',
        'accent_color' => '',
        'background'   => '',
        'text_color'   => '',
        'link_text'    => '',
        'link_url'     => '',
        'button_label' => '',
        'animation'    => '',
        'custom_class' => '',
        'excerpt'      => '',
        'category_label' => '',
        'date_label'     => '',
        'reading_time'   => '',
        'author_label'   => '',
        'position'       => 0,
    );

    $card = wp_parse_args($card, $defaults);

    if (!is_array($card['list'])) {
        $card['list'] = array();
    } else {
        $card['list'] = array_values(array_filter(array_map('sanitize_text_field', $card['list'])));
    }

    $card['icon_type']  = putrafiber_frontpage_normalise_choice($card['icon_type'], array('icon', 'image', 'image-large'), 'icon');
    $card['image_size'] = putrafiber_frontpage_normalise_choice($card['image_size'], array('auto', 'small', 'medium', 'large', 'cover', 'contain', 'wide', 'tall', 'square', 'circle'), 'auto');
    $card['list_effect'] = putrafiber_frontpage_normalise_choice($card['list_effect'], array('', 'check', 'spark', 'wave', 'bullet', 'arrow'), '');
    $card['animation']   = putrafiber_frontpage_normalise_choice($card['animation'], array('', 'auto', 'rise', 'zoom', 'tilt', 'float', 'pulse', 'fade', 'slide', 'none'), '');

    $card['position'] = isset($card['position']) ? (int) $card['position'] : 0;
    if ($card['position'] < 0) {
        $card['position'] = 0;
    }

    if (!empty($card['custom_class'])) {
        $pieces = preg_split('/\s+/', $card['custom_class']);
        $sanitised = array();
        if (is_array($pieces)) {
            foreach ($pieces as $piece) {
                $piece = sanitize_html_class($piece);
                if ($piece !== '') {
                    $sanitised[] = $piece;
                }
            }
        }
        $card['custom_class'] = implode(' ', array_unique($sanitised));
    } else {
        $card['custom_class'] = '';
    }

    $card['icon'] = sanitize_key($card['icon']);
    $card['image'] = esc_url($card['image']);
    $card['image_alt'] = sanitize_text_field($card['image_alt']);
    $card['accent_color'] = sanitize_text_field($card['accent_color']);
    $card['background'] = sanitize_text_field($card['background']);
    $card['text_color'] = sanitize_text_field($card['text_color']);
    $card['link_text'] = sanitize_text_field($card['link_text']);
    $card['link_url'] = esc_url($card['link_url']);
    $card['button_label'] = sanitize_text_field($card['button_label']);
    $card['subtitle'] = sanitize_text_field($card['subtitle']);
    $card['title'] = sanitize_text_field($card['title']);
    $card['badge'] = sanitize_text_field($card['badge']);
    $card['highlight'] = sanitize_text_field($card['highlight']);
    $card['category_label'] = sanitize_text_field($card['category_label']);
    $card['date_label'] = sanitize_text_field($card['date_label']);
    $card['reading_time'] = sanitize_text_field($card['reading_time']);
    $card['author_label'] = sanitize_text_field($card['author_label']);

    if (!is_string($card['description'])) {
        $card['description'] = '';
    }
    if (!is_string($card['excerpt'])) {
        $card['excerpt'] = '';
    }

    $card['description'] = wp_kses_post($card['description']);
    $card['excerpt']      = wp_kses_post($card['excerpt']);

    return $card;
}

/**
 * Return manual blog cards authored via Theme Options.
 *
 * @return array<int,array<string,mixed>>
 */
function putrafiber_frontpage_blog_custom_cards() {
    $cards = putrafiber_get_option('front_blog_custom_cards', array());
    if (!is_array($cards) || empty($cards)) {
        return array();
    }

    $normalised = array_values(array_filter(array_map('putrafiber_frontpage_normalise_card', $cards)));

    if (empty($normalised)) {
        return array();
    }

    usort($normalised, function ($a, $b) {
        $position_a = isset($a['position']) ? (int) $a['position'] : 0;
        $position_b = isset($b['position']) ? (int) $b['position'] : 0;

        if ($position_a === $position_b) {
            return 0;
        }

        if ($position_a === 0) {
            return 1;
        }

        if ($position_b === 0) {
            return -1;
        }

        return ($position_a < $position_b) ? -1 : 1;
    });

    return $normalised;
}

/**
 * Build the final ordered deck of blog cards including manual entries.
 *
 * @param array<int> $post_ids Ordered list of WordPress post IDs.
 * @return array<int,array<string,mixed>>
 */
function putrafiber_frontpage_blog_deck($post_ids) {
    $post_ids = array_values(array_unique(array_map('intval', (array) $post_ids)));
    $custom_cards = putrafiber_frontpage_blog_custom_cards();

    if (empty($custom_cards)) {
        return array_map(function ($post_id) {
            return array(
                'type'    => 'post',
                'post_id' => $post_id,
            );
        }, $post_ids);
    }

    $positioned = array();
    $append = array();

    foreach ($custom_cards as $card) {
        $position = isset($card['position']) ? (int) $card['position'] : 0;
        if ($position > 0) {
            if (!isset($positioned[$position])) {
                $positioned[$position] = array();
            }
            $positioned[$position][] = $card;
        } else {
            $append[] = $card;
        }
    }

    $max_position = !empty($positioned) ? max(array_keys($positioned)) : 0;
    $max_slots    = max($max_position, count($post_ids));

    $final   = array();
    $pointer = 0;

    for ($slot = 1; $slot <= $max_slots; $slot++) {
        if (isset($positioned[$slot])) {
            foreach ($positioned[$slot] as $card) {
                $final[] = array(
                    'type' => 'custom',
                    'data' => $card,
                );
            }
        }

        if ($pointer < count($post_ids)) {
            $final[] = array(
                'type'    => 'post',
                'post_id' => $post_ids[$pointer],
            );
            $pointer++;
        }
    }

    while ($pointer < count($post_ids)) {
        $final[] = array(
            'type'    => 'post',
            'post_id' => $post_ids[$pointer],
        );
        $pointer++;
    }

    foreach ($append as $card) {
        $final[] = array(
            'type' => 'custom',
            'data' => $card,
        );
    }

    return $final;
}

/**
 * Retrieve manually curated blog slots configuration.
 *
 * Each line stored in Theme Options should follow the pattern:
 * "Artikel 1 | 123" where 123 is the post ID. The label segment is optional.
 *
 * @return array<int,array{post_id:int,label:string}>
 */
function putrafiber_frontpage_blog_slots() {
    $raw = putrafiber_get_option('front_blog_manual_posts', '');
    if (empty($raw)) {
        return array();
    }

    $lines = preg_split('/\r\n|\r|\n/', $raw);
    if (!$lines) {
        return array();
    }

    $slots = array();
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts   = array_map('trim', explode('|', $line));
        $post_id = 0;
        $label   = '';

        if (count($parts) === 1) {
            $post_id = (int) $parts[0];
        } else {
            $label   = $parts[0];
            $post_id = (int) $parts[1];
        }

        if ($post_id <= 0) {
            continue;
        }

        $status = get_post_status($post_id);
        if ($status !== 'publish') {
            continue;
        }

        if ($label === '') {
            /* translators: %d: article position on the landing page */
            $label = sprintf(__('Artikel %d', 'putrafiber'), count($slots) + 1);
        }

        $slots[] = array(
            'post_id' => $post_id,
            'label'   => $label,
        );
    }

    return $slots;
}

/**
 * Resolve the query parameter key used for section pagination.
 *
 * @param string $section
 * @return string
 */
function putrafiber_frontpage_section_query_var($section) {
    return 'pf_' . sanitize_key($section) . '_page';
}

/**
 * Determine the current paged value for a given front page section.
 *
 * @param string $section
 * @return int
 */
function putrafiber_frontpage_section_paged($section) {
    $param = putrafiber_frontpage_section_query_var($section);

    $paged = filter_input(INPUT_GET, $param, FILTER_VALIDATE_INT);
    if ($paged === null || $paged === false) {
        $paged = isset($_GET[$param]) ? (int) wp_unslash($_GET[$param]) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }

    if ($paged < 1) {
        $paged = 1;
    }

    return $paged;
}

/**
 * Render numeric pagination for a front page section query.
 *
 * @param string   $section Section slug.
 * @param WP_Query $query   Query instance.
 * @return string Pagination markup.
 */
function putrafiber_frontpage_render_pagination($section, $query) {
    if (!($query instanceof WP_Query)) {
        return '';
    }

    $total = (int) $query->max_num_pages;
    if ($total <= 1) {
        return '';
    }

    $current = putrafiber_frontpage_section_paged($section);
    $param   = putrafiber_frontpage_section_query_var($section);

    $front_id = (int) get_option('page_on_front');
    $base_url = $front_id ? get_permalink($front_id) : home_url('/');
    $base_url = esc_url_raw(remove_query_arg($param, $base_url));

    $additional_args = array();
    foreach ($_GET as $key => $value) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ($key === $param) {
            continue;
        }

        $sanitized_key = sanitize_key($key);
        if ($sanitized_key === '') {
            continue;
        }

        if (is_array($value)) {
            continue;
        }

        $additional_args[$sanitized_key] = sanitize_text_field(wp_unslash($value));
    }

    $links = paginate_links(array(
        'base'      => add_query_arg($param, '%#%', $base_url),
        'format'    => '',
        'current'   => max(1, $current),
        'total'     => $total,
        'type'      => 'array',
        'add_args'  => $additional_args,
        'prev_text' => __('← Sebelumnya', 'putrafiber'),
        'next_text' => __('Selanjutnya →', 'putrafiber'),
    ));

    if (empty($links) || !is_array($links)) {
        return '';
    }

    foreach ($links as &$link) {
        $link = str_replace(array($param . '=1&amp;', $param . '=1'), array('', ''), $link);
    }
    unset($link);

    /* translators: %s: section label. */
    $label = sprintf(__('Navigasi %s', 'putrafiber'), ucfirst($section));

    $output  = '<nav class="section-pagination" aria-label="' . esc_attr($label) . '">';
    $output .= '<ul class="pagination-list">';
    foreach ($links as $link) {
        $output .= '<li class="pagination-item">' . $link . '</li>';
    }
    $output .= '</ul>';
    $output .= '</nav>';

    return $output;
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
    $palette = putrafiber_frontpage_active_preset_colors();
    $value   = '';

    if (isset($palette[$key]) && $palette[$key] !== '') {
        $value = $palette[$key];
    } else {
        $value = putrafiber_get_option($key, '');
    }

    $value = putrafiber_frontpage_sanitize_color_value($value);
    if ($value !== '') {
        return $value;
    }

    $default = putrafiber_frontpage_sanitize_color_value($default);

    return $default !== '' ? $default : '#0f75ff';
}

/**
 * Retrieve palette colours used across landing components & custom blocks.
 *
 * @return array<string,string>
 */
function putrafiber_frontpage_palette_colors() {
    return array(
        'front_primary_color' => putrafiber_frontpage_color('front_primary_color', '#0f75ff'),
        'front_gold_color'    => putrafiber_frontpage_color('front_gold_color', '#f9c846'),
        'front_dark_color'    => putrafiber_frontpage_color('front_dark_color', '#0b142b'),
        'front_water_color'   => putrafiber_frontpage_color('front_water_color', 'rgba(15, 117, 255, 0.14)'),
    );
}

/**
 * Generate inline style tag for palette custom properties.
 *
 * @return string
 */
function putrafiber_frontpage_palette_style() {
    $palette = putrafiber_frontpage_palette_colors();

    if (empty($palette)) {
        return '';
    }

    $map = array(
        'front_primary_color' => '--pf-front-primary',
        'front_gold_color'    => '--pf-front-gold',
        'front_dark_color'    => '--pf-front-dark',
        'front_water_color'   => '--pf-front-water',
    );

    $declarations = array();
    foreach ($map as $key => $variable) {
        if (!empty($palette[$key])) {
            $declarations[] = $variable . ':' . esc_attr($palette[$key]);
        }
    }

    if (empty($declarations)) {
        return '';
    }

    return '<style id="putrafiber-frontpage-vars">:root{' . implode(';', $declarations) . ';}</style>';
}

/**
 * Ensure palette styles are only printed once per request.
 *
 * @return string
 */
function putrafiber_frontpage_palette_style_once() {
    static $printed = false;

    if ($printed) {
        return '';
    }

    $style = putrafiber_frontpage_palette_style();
    if ($style === '') {
        return '';
    }

    $printed = true;

    return $style;
}

/**
 * Print CSS custom properties for landing page palette.
 *
 * @return void
 */
function putrafiber_frontpage_print_color_vars() {
    $style = putrafiber_frontpage_palette_style_once();
    if ($style !== '') {
        echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
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
