<?php
/**
 * Custom Gutenberg blocks for PutraFiber theme.
 *
 * @package PutraFiber
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ensure block category is registered.
 *
 * @param array<int,array<string,mixed>> $categories
 * @return array<int,array<string,mixed>>
 */
function putrafiber_block_category($categories, $post = null) {
    $exists = wp_list_filter($categories, array('slug' => 'putrafiber'));
    if (!empty($exists)) {
        return $categories;
    }

    $categories[] = array(
        'slug'  => 'putrafiber',
        'title' => __('PutraFiber', 'putrafiber'),
        'icon'  => null,
    );

    return $categories;
}
add_filter('block_categories_all', 'putrafiber_block_category', 10, 2);

/**
 * Register editor assets for custom blocks.
 *
 * @return void
 */
function putrafiber_register_block_assets() {
    if (!function_exists('register_block_type')) {
        return;
    }

    wp_register_script(
        'putrafiber-block-editor',
        get_template_directory_uri() . '/assets/js/block-editor.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n'),
        function_exists('pf_asset_version') ? pf_asset_version('assets/js/block-editor.js') : PUTRAFIBER_VERSION,
        true
    );

    wp_register_style(
        'putrafiber-blocks',
        get_template_directory_uri() . '/assets/css/blocks.css',
        array(),
        function_exists('pf_asset_version') ? pf_asset_version('assets/css/blocks.css') : PUTRAFIBER_VERSION
    );

    wp_register_style(
        'putrafiber-blocks-editor',
        get_template_directory_uri() . '/assets/css/block-editor.css',
        array('wp-edit-blocks'),
        function_exists('pf_asset_version') ? pf_asset_version('assets/css/block-editor.css') : PUTRAFIBER_VERSION
    );
}
add_action('init', 'putrafiber_register_block_assets');

/**
 * Prepare shared block supports array.
 *
 * @return array<string,mixed>
 */
function putrafiber_block_default_supports() {
    return array(
        'align'  => array('wide', 'full'),
        'anchor' => true,
        'color'  => array(
            'text'  => false,
            'background' => false,
        ),
    );
}

/**
 * Register all custom blocks.
 *
 * @return void
 */
function putrafiber_register_custom_blocks() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('putrafiber/hero-highlight', array(
        'editor_script'   => 'putrafiber-block-editor',
        'editor_style'    => 'putrafiber-blocks-editor',
        'style'           => 'putrafiber-blocks',
        'render_callback' => 'putrafiber_render_block_hero_highlight',
        'attributes'      => array(
            'align'          => array('type' => 'string'),
            'anchor'         => array('type' => 'string'),
            'layout'         => array('type' => 'string', 'default' => 'left'),
            'highlight'      => array('type' => 'string', 'default' => __('20+ Tahun Menghadirkan Wahana Air Spektakuler', 'putrafiber')),
            'title'          => array('type' => 'string', 'default' => __('Kontraktor Waterpark & Playground Fiberglass', 'putrafiber')),
            'description'    => array('type' => 'string', 'default' => __('Spesialis water attraction dan playground fiberglass premium.', 'putrafiber')),
            'primaryText'    => array('type' => 'string', 'default' => __('Konsultasi Gratis', 'putrafiber')),
            'primaryUrl'     => array('type' => 'string', 'default' => ''),
            'secondaryText'  => array('type' => 'string', 'default' => __('Lihat Portofolio', 'putrafiber')),
            'secondaryUrl'   => array('type' => 'string', 'default' => ''),
            'backgroundImage'=> array('type' => 'string', 'default' => ''),
            'backgroundAlt'  => array('type' => 'string', 'default' => ''),
            'overlayStrength'=> array('type' => 'number', 'default' => 60),
            'badges'         => array('type' => 'array', 'default' => array()),
        ),
        'supports'        => putrafiber_block_default_supports(),
    ));

    register_block_type('putrafiber/cta-banner', array(
        'editor_script'   => 'putrafiber-block-editor',
        'editor_style'    => 'putrafiber-blocks-editor',
        'style'           => 'putrafiber-blocks',
        'render_callback' => 'putrafiber_render_block_cta_banner',
        'attributes'      => array(
            'align'          => array('type' => 'string'),
            'anchor'         => array('type' => 'string'),
            'layout'         => array('type' => 'string', 'default' => 'split'),
            'eyebrow'        => array('type' => 'string', 'default' => __('Siap Membangun Water Attraction Impian?', 'putrafiber')),
            'title'          => array('type' => 'string', 'default' => __('Ajak tim PutraFiber merancang proyek Anda berikutnya.', 'putrafiber')),
            'description'    => array('type' => 'string', 'default' => __('Tim insinyur kami siap membantu dari konsep hingga instalasi selesai.', 'putrafiber')),
            'primaryText'    => array('type' => 'string', 'default' => __('Hubungi Kami', 'putrafiber')),
            'primaryUrl'     => array('type' => 'string', 'default' => ''),
            'secondaryText'  => array('type' => 'string', 'default' => __('Download Company Profile', 'putrafiber')),
            'secondaryUrl'   => array('type' => 'string', 'default' => ''),
            'backgroundImage'=> array('type' => 'string', 'default' => ''),
            'backgroundAlt'  => array('type' => 'string', 'default' => ''),
            'backgroundColor'=> array('type' => 'string', 'default' => ''),
            'textColor'      => array('type' => 'string', 'default' => ''),
            'overlayStrength'=> array('type' => 'number', 'default' => 55),
        ),
        'supports'        => putrafiber_block_default_supports(),
    ));

    register_block_type('putrafiber/testimonial-showcase', array(
        'editor_script'   => 'putrafiber-block-editor',
        'editor_style'    => 'putrafiber-blocks-editor',
        'style'           => 'putrafiber-blocks',
        'render_callback' => 'putrafiber_render_block_testimonial_showcase',
        'attributes'      => array(
            'align'          => array('type' => 'string'),
            'anchor'         => array('type' => 'string'),
            'heading'        => array('type' => 'string', 'default' => __('Testimoni Klien', 'putrafiber')),
            'subheading'     => array('type' => 'string', 'default' => __('Cerita keberhasilan proyek bersama PutraFiber.', 'putrafiber')),
            'layout'         => array('type' => 'string', 'default' => 'grid'),
            'columns'        => array('type' => 'number', 'default' => 3),
            'backgroundColor'=> array('type' => 'string', 'default' => ''),
            'textColor'      => array('type' => 'string', 'default' => ''),
            'accentColor'    => array('type' => 'string', 'default' => ''),
            'testimonials'   => array('type' => 'array', 'default' => array()),
        ),
        'supports'        => putrafiber_block_default_supports(),
    ));
}
add_action('init', 'putrafiber_register_custom_blocks');

/**
 * Normalise block alignment class.
 *
 * @param string $align
 * @return string
 */
function putrafiber_block_align_class($align) {
    if (!$align) {
        return '';
    }

    $align = sanitize_key($align);
    return in_array($align, array('wide', 'full', 'left', 'right', 'center'), true) ? 'align' . $align : '';
}

/**
 * Render Hero block.
 *
 * @param array<string,mixed> $attributes
 * @return string
 */
function putrafiber_render_block_hero_highlight($attributes) {
    $defaults = array(
        'align'          => '',
        'anchor'         => '',
        'layout'         => 'left',
        'highlight'      => '',
        'title'          => '',
        'description'    => '',
        'primaryText'    => '',
        'primaryUrl'     => '',
        'secondaryText'  => '',
        'secondaryUrl'   => '',
        'backgroundImage'=> '',
        'backgroundAlt'  => '',
        'overlayStrength'=> 60,
        'badges'         => array(),
    );

    $attributes = wp_parse_args($attributes, $defaults);

    $layout = in_array($attributes['layout'], array('left', 'center'), true) ? $attributes['layout'] : 'left';
    $overlay = (int) $attributes['overlayStrength'];
    $overlay = max(0, min(100, $overlay));

    $primary = putrafiber_frontpage_color('front_primary_color', '#0f75ff');
    $gold    = putrafiber_frontpage_color('front_gold_color', '#f9c846');

    $badges = array();
    if (!empty($attributes['badges']) && is_array($attributes['badges'])) {
        foreach ($attributes['badges'] as $badge) {
            if (!is_array($badge)) {
                continue;
            }

            $title = isset($badge['title']) ? sanitize_text_field($badge['title']) : '';
            $desc  = isset($badge['description']) ? sanitize_text_field($badge['description']) : '';
            $icon  = isset($badge['icon']) ? sanitize_key($badge['icon']) : '';

            if ($title === '' && $desc === '') {
                continue;
            }

            if ($icon === '') {
                $icon = 'spark';
            }

            $badges[] = array(
                'title'       => $title,
                'description' => $desc,
                'icon'        => $icon,
            );
        }
    }

    if (empty($attributes['primaryUrl']) && function_exists('putrafiber_whatsapp_link')) {
        $attributes['primaryUrl'] = putrafiber_whatsapp_link();
    }

    $classes = array('pf-block', 'pf-block-hero', 'pf-block-hero--layout-' . $layout);
    $align_class = putrafiber_block_align_class($attributes['align']);
    if ($align_class) {
        $classes[] = $align_class;
    }

    $anchor_attr = '';
    if (!empty($attributes['anchor'])) {
        $anchor_attr = ' id="' . esc_attr($attributes['anchor']) . '"';
    }

    $background_markup = '';
    if (!empty($attributes['backgroundImage'])) {
        $background_markup = '<div class="pf-block-hero__background"><img src="' . esc_url($attributes['backgroundImage']) . '" alt="' . esc_attr($attributes['backgroundAlt']) . '" loading="lazy" decoding="async"></div>';
    }

    $overlay_style = $overlay > 0 ? ' style="opacity:' . esc_attr(number_format($overlay / 100, 2, '.', '')) . ';"' : '';

    ob_start();
    echo putrafiber_frontpage_palette_style_once(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
    <section<?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-layout="<?php echo esc_attr($layout); ?>">
        <?php if ($background_markup) : ?>
            <?php echo $background_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else : ?>
            <div class="pf-block-hero__background pf-block-hero__background--gradient" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="pf-block-hero__overlay"<?php echo $overlay_style; ?>></div>
        <div class="pf-block-hero__inner">
            <?php if (!empty($attributes['highlight'])) : ?>
                <span class="pf-block-hero__highlight"><?php echo esc_html($attributes['highlight']); ?></span>
            <?php endif; ?>
            <?php if (!empty($attributes['title'])) : ?>
                <h2 class="pf-block-hero__title"><?php echo esc_html($attributes['title']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($attributes['description'])) : ?>
                <p class="pf-block-hero__description"><?php echo esc_html($attributes['description']); ?></p>
            <?php endif; ?>
            <div class="pf-block-hero__actions">
                <?php if (!empty($attributes['primaryText']) && !empty($attributes['primaryUrl'])) : ?>
                    <a class="btn btn-primary" href="<?php echo esc_url($attributes['primaryUrl']); ?>" target="_blank" rel="noopener">
                        <?php echo esc_html($attributes['primaryText']); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($attributes['secondaryText']) && !empty($attributes['secondaryUrl'])) : ?>
                    <a class="btn btn-outline" href="<?php echo esc_url($attributes['secondaryUrl']); ?>">
                        <?php echo esc_html($attributes['secondaryText']); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php if (!empty($badges)) : ?>
                <div class="pf-block-hero__badges" data-count="<?php echo esc_attr(count($badges)); ?>">
                    <?php foreach ($badges as $badge) : ?>
                        <div class="pf-block-badge" style="--pf-badge-accent: <?php echo esc_attr($gold); ?>;">
                            <?php if (function_exists('putrafiber_frontpage_icon_svg')) : ?>
                                <span class="pf-block-badge__icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <?php echo putrafiber_frontpage_icon_svg($badge['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </svg>
                                </span>
                            <?php endif; ?>
                            <span class="pf-block-badge__title"><?php echo esc_html($badge['title']); ?></span>
                            <?php if (!empty($badge['description'])) : ?>
                                <span class="pf-block-badge__description"><?php echo esc_html($badge['description']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <span class="pf-block-hero__wave" style="--pf-hero-wave: <?php echo esc_attr($primary); ?>" aria-hidden="true"></span>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Render CTA banner block.
 *
 * @param array<string,mixed> $attributes
 * @return string
 */
function putrafiber_render_block_cta_banner($attributes) {
    $defaults = array(
        'align'          => '',
        'anchor'         => '',
        'layout'         => 'split',
        'eyebrow'        => '',
        'title'          => '',
        'description'    => '',
        'primaryText'    => '',
        'primaryUrl'     => '',
        'secondaryText'  => '',
        'secondaryUrl'   => '',
        'backgroundImage'=> '',
        'backgroundAlt'  => '',
        'backgroundColor'=> '',
        'textColor'      => '',
        'overlayStrength'=> 55,
    );

    $attributes = wp_parse_args($attributes, $defaults);
    $layout = in_array($attributes['layout'], array('split', 'stacked'), true) ? $attributes['layout'] : 'split';
    $overlay = max(0, min(100, (int) $attributes['overlayStrength']));

    $accent  = putrafiber_frontpage_color('front_primary_color', '#0f75ff');
    $text    = putrafiber_frontpage_sanitize_color_value($attributes['textColor']);
    $bg_color= putrafiber_frontpage_sanitize_color_value($attributes['backgroundColor']);

    $classes = array('pf-block', 'pf-block-cta', 'pf-block-cta--layout-' . $layout);
    $align_class = putrafiber_block_align_class($attributes['align']);
    if ($align_class) {
        $classes[] = $align_class;
    }

    $style_rules = array();
    if ($text) {
        $style_rules[] = '--pf-cta-text:' . $text;
    }
    if ($bg_color) {
        $style_rules[] = '--pf-cta-background:' . $bg_color;
    }
    $style_rules[] = '--pf-cta-accent:' . $accent;
    $style_attr = $style_rules ? ' style="' . esc_attr(implode(';', $style_rules)) . '"' : '';

    $anchor_attr = '';
    if (!empty($attributes['anchor'])) {
        $anchor_attr = ' id="' . esc_attr($attributes['anchor']) . '"';
    }

    $background_markup = '';
    if (!empty($attributes['backgroundImage'])) {
        $background_markup = '<div class="pf-block-cta__background"><img src="' . esc_url($attributes['backgroundImage']) . '" alt="' . esc_attr($attributes['backgroundAlt']) . '" loading="lazy" decoding="async"></div>';
    }

    $overlay_style = $overlay > 0 ? ' style="opacity:' . esc_attr(number_format($overlay / 100, 2, '.', '')) . ';"' : '';

    ob_start();
    echo putrafiber_frontpage_palette_style_once(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
    <section<?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $style_attr; ?> data-layout="<?php echo esc_attr($layout); ?>">
        <?php if ($background_markup) : ?>
            <?php echo $background_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endif; ?>
        <div class="pf-block-cta__overlay"<?php echo $overlay_style; ?>></div>
        <div class="pf-block-cta__inner">
            <div class="pf-block-cta__content">
                <?php if (!empty($attributes['eyebrow'])) : ?>
                    <span class="pf-block-cta__eyebrow"><?php echo esc_html($attributes['eyebrow']); ?></span>
                <?php endif; ?>
                <?php if (!empty($attributes['title'])) : ?>
                    <h2 class="pf-block-cta__title"><?php echo esc_html($attributes['title']); ?></h2>
                <?php endif; ?>
                <?php if (!empty($attributes['description'])) : ?>
                    <p class="pf-block-cta__description"><?php echo esc_html($attributes['description']); ?></p>
                <?php endif; ?>
            </div>
            <div class="pf-block-cta__actions">
                <?php if (!empty($attributes['primaryText']) && !empty($attributes['primaryUrl'])) : ?>
                    <a class="btn btn-primary" href="<?php echo esc_url($attributes['primaryUrl']); ?>">
                        <?php echo esc_html($attributes['primaryText']); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($attributes['secondaryText']) && !empty($attributes['secondaryUrl'])) : ?>
                    <a class="btn btn-outline" href="<?php echo esc_url($attributes['secondaryUrl']); ?>">
                        <?php echo esc_html($attributes['secondaryText']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Render testimonial showcase block.
 *
 * @param array<string,mixed> $attributes
 * @return string
 */
function putrafiber_render_block_testimonial_showcase($attributes) {
    $defaults = array(
        'align'          => '',
        'anchor'         => '',
        'heading'        => '',
        'subheading'     => '',
        'layout'         => 'grid',
        'columns'        => 3,
        'backgroundColor'=> '',
        'textColor'      => '',
        'accentColor'    => '',
        'testimonials'   => array(),
    );

    $attributes = wp_parse_args($attributes, $defaults);
    $layout = in_array($attributes['layout'], array('grid', 'carousel'), true) ? $attributes['layout'] : 'grid';
    $columns = (int) $attributes['columns'];
    if ($columns < 1) {
        $columns = 1;
    }
    if ($columns > 4) {
        $columns = 4;
    }

    $background = putrafiber_frontpage_sanitize_color_value($attributes['backgroundColor']);
    $text       = putrafiber_frontpage_sanitize_color_value($attributes['textColor']);
    $accent     = putrafiber_frontpage_sanitize_color_value($attributes['accentColor']);

    if ($accent === '') {
        $accent = putrafiber_frontpage_color('front_gold_color', '#f9c846');
    }

    $cards = array();
    if (!empty($attributes['testimonials']) && is_array($attributes['testimonials'])) {
        foreach ($attributes['testimonials'] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $quote = isset($item['quote']) ? wp_kses_post($item['quote']) : '';
            $name  = isset($item['name']) ? sanitize_text_field($item['name']) : '';
            $role  = isset($item['role']) ? sanitize_text_field($item['role']) : '';
            $rating= isset($item['rating']) ? (int) $item['rating'] : 0;

            if ($quote === '' && $name === '') {
                continue;
            }

            $rating = max(0, min(5, $rating));

            $cards[] = array(
                'quote'  => $quote,
                'name'   => $name,
                'role'   => $role,
                'rating' => $rating,
            );
        }
    }

    if (empty($cards)) {
        return '';
    }

    $classes = array('pf-block', 'pf-block-testimonials', 'pf-block-testimonials--layout-' . $layout, 'pf-block-testimonials--cols-' . $columns);
    $align_class = putrafiber_block_align_class($attributes['align']);
    if ($align_class) {
        $classes[] = $align_class;
    }

    $style_rules = array('--pf-testimonial-accent:' . $accent);
    if ($background) {
        $style_rules[] = '--pf-testimonial-background:' . $background;
    }
    if ($text) {
        $style_rules[] = '--pf-testimonial-text:' . $text;
    }

    $style_attr = $style_rules ? ' style="' . esc_attr(implode(';', $style_rules)) . '"' : '';

    $anchor_attr = '';
    if (!empty($attributes['anchor'])) {
        $anchor_attr = ' id="' . esc_attr($attributes['anchor']) . '"';
    }

    ob_start();
    echo putrafiber_frontpage_palette_style_once(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
    <section<?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $style_attr; ?> data-columns="<?php echo esc_attr($columns); ?>">
        <div class="pf-block-testimonials__header">
            <?php if (!empty($attributes['heading'])) : ?>
                <h2 class="pf-block-testimonials__title"><?php echo esc_html($attributes['heading']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($attributes['subheading'])) : ?>
                <p class="pf-block-testimonials__subtitle"><?php echo esc_html($attributes['subheading']); ?></p>
            <?php endif; ?>
        </div>
        <div class="pf-block-testimonials__grid">
            <?php foreach ($cards as $card) : ?>
                <article class="pf-testimonial-card" itemscope itemtype="https://schema.org/Review">
                    <div class="pf-testimonial-card__quote" itemprop="reviewBody">
                        <?php echo wpautop($card['quote']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                    <footer class="pf-testimonial-card__footer">
                        <div class="pf-testimonial-card__meta">
                            <?php if (!empty($card['name'])) : ?>
                                <span class="pf-testimonial-card__name" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                    <span itemprop="name"><?php echo esc_html($card['name']); ?></span>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($card['role'])) : ?>
                                <span class="pf-testimonial-card__role"><?php echo esc_html($card['role']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($card['rating'] > 0) : ?>
                            <div class="pf-testimonial-card__rating" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating" aria-label="<?php echo esc_attr(sprintf(__('Rating %1$d dari %2$d', 'putrafiber'), $card['rating'], 5)); ?>">
                                <meta itemprop="worstRating" content="1" />
                                <meta itemprop="bestRating" content="5" />
                                <meta itemprop="ratingValue" content="<?php echo esc_attr($card['rating']); ?>" />
                                <?php for ($i = 0; $i < 5; $i++) : ?>
                                    <span class="pf-testimonial-card__star<?php echo $i < $card['rating'] ? ' is-active' : ''; ?>" aria-hidden="true"></span>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
