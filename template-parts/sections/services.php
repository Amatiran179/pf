<?php
/**
 * Services Section
 *
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

$services_title = putrafiber_frontpage_text('services', 'title', __('Solusi Water Attraction Lengkap', 'putrafiber'));
$services_desc  = putrafiber_frontpage_text('services', 'description', __('Dari masterplan, fabrikasi, hingga instalasi turn-key untuk wahana air dan playground.', 'putrafiber'));
$services_settings = function_exists('putrafiber_frontpage_card_settings') ? putrafiber_frontpage_card_settings('services') : array(
    'layout' => 'grid',
    'style' => 'glass',
    'animation' => 'auto',
    'columns' => 3,
    'background_effect' => 'none',
    'size' => 'regular',
);

$services_default = array(
    array('title' => 'Masterplan Waterpark', 'description' => 'Perencanaan 360° mencakup flow pengunjung, wahana, hingga F&B zone.', 'icon' => 'compass'),
    array('title' => 'Fabrikasi Fiberglass', 'description' => 'Produksi wahana air, slider, dan permainan custom sesuai standar internasional.', 'icon' => 'gear'),
    array('title' => 'Instalasi & Commissioning', 'description' => 'Tim onsite memastikan pemasangan rapi, aman, dan siap operasi.', 'icon' => 'shield'),
    array('title' => 'Maintenance & Retrofit', 'description' => 'Layanan perawatan berkala dan upgrade wahana untuk memperpanjang umur investasi.', 'icon' => 'spark'),
    array('title' => 'Indoor Playground', 'description' => 'Desain tematik lengkap dengan softplay, trampoline, dan area edukasi.', 'icon' => 'wave'),
    array('title' => 'Water Adventure', 'description' => 'Wahana arus malas, kolam ombak, hingga river tubing untuk destinasi wisata.', 'icon' => 'drop'),
);

$services_items = function_exists('putrafiber_frontpage_cards')
    ? putrafiber_frontpage_cards('services', 'front_services_items', $services_default)
    : putrafiber_frontpage_parse_repeater('front_services_items', $services_default);

$services_effect = isset($services_settings['background_effect']) ? $services_settings['background_effect'] : 'none';
$section_classes = array('services-section', 'section');
if ($services_effect && $services_effect !== 'none') {
    $section_classes[] = 'section-has-effect';
    $section_classes[] = 'section-effect--' . $services_effect;
}

$grid_classes = array('services-grid', 'card-collection');
$grid_classes[] = 'card-layout--' . (isset($services_settings['layout']) ? $services_settings['layout'] : 'grid');
$grid_classes[] = 'card-style--' . (isset($services_settings['style']) ? $services_settings['style'] : 'glass');
$grid_classes[] = 'card-size--' . (isset($services_settings['size']) ? $services_settings['size'] : 'regular');
$grid_classes[] = 'card-columns--' . max(1, (int) $services_settings['columns']);

$services_animation = isset($services_settings['animation']) ? $services_settings['animation'] : 'auto';
?>

<section class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $section_classes))); ?> bg-light" id="services">
    <?php if ($services_effect && $services_effect !== 'none'): ?>
        <div class="section-decor" aria-hidden="true">
            <span class="section-decor__layer section-decor__layer--<?php echo esc_attr($services_effect); ?>"></span>
        </div>
    <?php endif; ?>
    <div class="container-wide">
        <div class="section-title fade-in">
            <h2><?php echo esc_html($services_title); ?></h2>
            <?php if ($services_desc): ?>
                <p><?php echo esc_html($services_desc); ?></p>
            <?php endif; ?>
        </div>

        <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $grid_classes))); ?>">
            <?php foreach ($services_items as $index => $service): ?>
                <?php
                $card_animation = isset($service['animation']) && $service['animation'] !== '' ? $service['animation'] : $services_animation;
                if ($card_animation === 'auto') {
                    $sequence = array('rise', 'zoom', 'tilt', 'float');
                    $card_animation = $sequence[$index % count($sequence)];
                }
                $animation_class = ($card_animation && $card_animation !== 'none') ? 'card-animate--' . $card_animation : '';
                $delay_value = number_format($index * 0.12, 2, '.', '');

                $card_classes = array('card', 'service-card');
                $card_classes[] = 'card--style-' . (isset($services_settings['style']) ? $services_settings['style'] : 'glass');
                $card_classes[] = 'card--size-' . (isset($services_settings['size']) ? $services_settings['size'] : 'regular');
                if ($animation_class) {
                    $card_classes[] = $animation_class;
                }
                if (!empty($service['custom_class'])) {
                    $card_classes[] = $service['custom_class'];
                }

                $style_tokens = array();
                if (!empty($service['background'])) {
                    $style_tokens[] = '--card-bg:' . $service['background'];
                }
                if (!empty($service['text_color'])) {
                    $style_tokens[] = '--card-text:' . $service['text_color'];
                }
                if (!empty($service['accent_color'])) {
                    $style_tokens[] = '--card-accent:' . $service['accent_color'];
                }
                $style_attr = !empty($style_tokens) ? ' style="' . esc_attr(implode(';', $style_tokens)) . ';--animation-delay:' . esc_attr($delay_value) . 's"' : ' style="--animation-delay:' . esc_attr($delay_value) . 's"';

                $media_type = isset($service['icon_type']) ? $service['icon_type'] : 'icon';
                $media_class = array('card-media');
                $media_class[] = 'card-media--' . $media_type;
                $media_class[] = 'card-media--' . (isset($service['image_size']) ? $service['image_size'] : 'auto');

                $cta_url = !empty($service['link_url']) ? $service['link_url'] : putrafiber_whatsapp_link('Halo, saya tertarik dengan layanan ' . $service['title']);
                $cta_label = !empty($service['link_text']) ? $service['link_text'] : (!empty($service['button_label']) ? $service['button_label'] : __('Konsultasi Sekarang', 'putrafiber'));
                $cta_rel = !empty($service['link_url']) ? '' : 'noopener';
                $cta_target = !empty($service['link_url']) ? '_self' : '_blank';
                ?>
                <article class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $card_classes))); ?>"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                    <?php if (!empty($service['badge'])): ?>
                        <span class="card-badge"><?php echo esc_html($service['badge']); ?></span>
                    <?php endif; ?>
                    <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $media_class))); ?>">
                        <?php if ($media_type === 'icon' || empty($service['image'])): ?>
                            <span class="card-media__icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <?php echo putrafiber_frontpage_icon_svg($service['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </svg>
                            </span>
                        <?php else: ?>
                            <span class="card-media__image">
                                <img src="<?php echo esc_url($service['image']); ?>" alt="<?php echo esc_attr($service['image_alt']); ?>" loading="lazy" decoding="async">
                            </span>
                        <?php endif; ?>
                    </div>
                    <header class="card-header">
                        <?php if (!empty($service['highlight'])): ?>
                            <span class="card-highlight"><?php echo esc_html($service['highlight']); ?></span>
                        <?php endif; ?>
                        <h3><?php echo esc_html($service['title']); ?></h3>
                        <?php if (!empty($service['subtitle'])): ?>
                            <p class="card-subtitle"><?php echo esc_html($service['subtitle']); ?></p>
                        <?php endif; ?>
                    </header>
                    <?php if (!empty($service['description'])): ?>
                        <div class="card-body">
                            <?php echo wp_kses_post(wpautop($service['description'])); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($service['list'])): ?>
                        <?php $list_class = array('card-list');
                        if (!empty($service['list_effect'])) {
                            $list_class[] = 'card-list--' . $service['list_effect'];
                        }
                        ?>
                        <ul class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $list_class))); ?>">
                            <?php foreach ($service['list'] as $bullet): ?>
                                <li><?php echo esc_html($bullet); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="card-link" target="<?php echo esc_attr($cta_target); ?>" <?php echo $cta_rel ? 'rel="' . esc_attr($cta_rel) . '"' : ''; ?>>
                        <?php echo esc_html($cta_label); ?>
                        <span class="card-link__icon" aria-hidden="true">→</span>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
