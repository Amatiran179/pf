<?php
/**
 * Features Section
 *
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

$features_title = putrafiber_frontpage_text('features', 'title', __('Kelebihan PutraFiber', 'putrafiber'));
$features_desc  = putrafiber_frontpage_text('features', 'description', __('Kami menggabungkan inovasi fiberglass dengan rekayasa konstruksi berkelas dunia.', 'putrafiber'));
$features_settings = function_exists('putrafiber_frontpage_card_settings') ? putrafiber_frontpage_card_settings('features') : array(
    'layout' => 'grid',
    'style' => 'glass',
    'animation' => 'auto',
    'columns' => 3,
    'background_effect' => 'none',
    'size' => 'regular',
);

$features_default = array(
    array('title' => 'Waterpark', 'description' => 'Desain dan konstruksi waterpark lengkap dengan berbagai wahana air yang aman dan menyenangkan.', 'icon' => 'wave'),
    array('title' => 'Waterboom', 'description' => 'Pembangunan waterboom dengan standar keamanan internasional dan desain menarik.', 'icon' => 'drop'),
    array('title' => 'Playground Indoor', 'description' => 'Playground indoor dengan material fiberglass berkualitas, aman untuk anak-anak segala usia.', 'icon' => 'spark'),
    array('title' => 'Playground Outdoor', 'description' => 'Playground outdoor tahan cuaca dengan berbagai permainan edukatif dan menyenangkan.', 'icon' => 'compass'),
    array('title' => 'Perosotan Fiberglass', 'description' => 'Berbagai jenis perosotan fiberglass dari spiral hingga custom design sesuai kebutuhan.', 'icon' => 'gear'),
    array('title' => 'Kolam Renang Fiberglass', 'description' => 'Kolam prefabrikasi tahan lama dengan instalasi cepat dan presisi.', 'icon' => 'shield'),
);

$features_items = function_exists('putrafiber_frontpage_cards')
    ? putrafiber_frontpage_cards('features', 'front_features_items', $features_default)
    : putrafiber_frontpage_parse_repeater('front_features_items', $features_default);

$section_classes = array('features-section', 'section');
$effect = isset($features_settings['background_effect']) ? $features_settings['background_effect'] : 'none';
if ($effect && $effect !== 'none') {
    $section_classes[] = 'section-has-effect';
    $section_classes[] = 'section-effect--' . $effect;
}

$grid_classes = array('feature-grid', 'card-collection');
$grid_classes[] = 'card-layout--' . (isset($features_settings['layout']) ? $features_settings['layout'] : 'grid');
$grid_classes[] = 'card-style--' . (isset($features_settings['style']) ? $features_settings['style'] : 'glass');
$grid_classes[] = 'card-size--' . (isset($features_settings['size']) ? $features_settings['size'] : 'regular');
$grid_classes[] = 'card-columns--' . max(1, (int) $features_settings['columns']);

$global_animation = isset($features_settings['animation']) ? $features_settings['animation'] : 'auto';
?>

<section class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $section_classes))); ?> bg-light" id="features">
    <?php if ($effect && $effect !== 'none'): ?>
        <div class="section-decor" aria-hidden="true">
            <span class="section-decor__layer section-decor__layer--<?php echo esc_attr($effect); ?>"></span>
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="section-title fade-in">
            <h2><?php echo esc_html($features_title); ?></h2>
            <?php if ($features_desc): ?>
                <p><?php echo esc_html($features_desc); ?></p>
            <?php endif; ?>
        </div>

        <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $grid_classes))); ?>">
            <?php foreach ($features_items as $index => $feature): ?>
                <?php
                $card_animation = isset($feature['animation']) && $feature['animation'] !== '' ? $feature['animation'] : $global_animation;
                $animation_class = '';
                if ($card_animation === 'auto') {
                    $sequence = array('rise', 'zoom', 'tilt', 'float');
                    $card_animation = $sequence[$index % count($sequence)];
                }
                if ($card_animation && $card_animation !== 'none') {
                    $animation_class = 'card-animate--' . $card_animation;
                }
                $delay_value = number_format($index * 0.08, 2, '.', '');

                $card_classes = array('card', 'feature-card');
                $card_classes[] = 'card--style-' . (isset($features_settings['style']) ? $features_settings['style'] : 'glass');
                $card_classes[] = 'card--size-' . (isset($features_settings['size']) ? $features_settings['size'] : 'regular');
                if ($animation_class) {
                    $card_classes[] = $animation_class;
                }
                if (!empty($feature['custom_class'])) {
                    $card_classes[] = $feature['custom_class'];
                }

                $style_tokens = array();
                if (!empty($feature['background'])) {
                    $style_tokens[] = '--card-bg:' . $feature['background'];
                }
                if (!empty($feature['text_color'])) {
                    $style_tokens[] = '--card-text:' . $feature['text_color'];
                }
                if (!empty($feature['accent_color'])) {
                    $style_tokens[] = '--card-accent:' . $feature['accent_color'];
                }
                $style_attr = !empty($style_tokens) ? ' style="' . esc_attr(implode(';', $style_tokens)) . ';--animation-delay:' . esc_attr($delay_value) . 's"' : ' style="--animation-delay:' . esc_attr($delay_value) . 's"';

                $media_type = isset($feature['icon_type']) ? $feature['icon_type'] : 'icon';
                $media_class = array('card-media');
                $media_class[] = 'card-media--' . $media_type;
                $media_class[] = 'card-media--' . (isset($feature['image_size']) ? $feature['image_size'] : 'auto');
                ?>
                <article class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $card_classes))); ?>"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                    <?php if (!empty($feature['badge'])): ?>
                        <span class="card-badge"><?php echo esc_html($feature['badge']); ?></span>
                    <?php endif; ?>
                    <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $media_class))); ?>">
                        <?php if ($media_type === 'icon' || empty($feature['image'])): ?>
                            <span class="card-media__icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <?php echo putrafiber_frontpage_icon_svg($feature['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </svg>
                            </span>
                        <?php else: ?>
                            <span class="card-media__image">
                                <img src="<?php echo esc_url($feature['image']); ?>" alt="<?php echo esc_attr($feature['image_alt']); ?>" loading="lazy" decoding="async">
                            </span>
                        <?php endif; ?>
                    </div>
                    <header class="card-header">
                        <?php if (!empty($feature['highlight'])): ?>
                            <span class="card-highlight"><?php echo esc_html($feature['highlight']); ?></span>
                        <?php endif; ?>
                        <h3><?php echo esc_html($feature['title']); ?></h3>
                        <?php if (!empty($feature['subtitle'])): ?>
                            <p class="card-subtitle"><?php echo esc_html($feature['subtitle']); ?></p>
                        <?php endif; ?>
                    </header>
                    <?php if (!empty($feature['description'])): ?>
                        <div class="card-body">
                            <?php echo wp_kses_post(wpautop($feature['description'])); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($feature['list'])): ?>
                        <?php $list_class = array('card-list');
                        if (!empty($feature['list_effect'])) {
                            $list_class[] = 'card-list--' . $feature['list_effect'];
                        }
                        ?>
                        <ul class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $list_class))); ?>">
                            <?php foreach ($feature['list'] as $bullet): ?>
                                <li><?php echo esc_html($bullet); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if (!empty($feature['link_url'])): ?>
                        <?php
                        $link_label = !empty($feature['link_text']) ? $feature['link_text'] : (!empty($feature['button_label']) ? $feature['button_label'] : __('Pelajari Selengkapnya', 'putrafiber'));
                        ?>
                        <a class="card-link" href="<?php echo esc_url($feature['link_url']); ?>">
                            <?php echo esc_html($link_label); ?>
                            <span class="card-link__icon" aria-hidden="true">→</span>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
