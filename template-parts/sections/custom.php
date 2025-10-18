<?php
/**
 * Custom Section generated via builder.
 *
 * @package PutraFiber
 */

if (!defined('ABSPATH')) {
    exit;
}

$section = isset($args['section']) && is_array($args['section']) ? $args['section'] : array();

$slug        = isset($section['id']) ? sanitize_title($section['id']) : 'custom-section';
$title       = isset($section['title']) ? $section['title'] : '';
$subtitle    = isset($section['subtitle']) ? $section['subtitle'] : '';
$content     = isset($section['content']) ? $section['content'] : '';
$background  = isset($section['background']) ? $section['background'] : '';
$text_color  = isset($section['text_color']) ? $section['text_color'] : '';
$button_text = isset($section['button_text']) ? $section['button_text'] : '';
$button_url  = isset($section['button_url']) ? $section['button_url'] : '';
$media       = isset($section['media']) ? $section['media'] : '';
$media_alt   = isset($section['media_alt']) ? $section['media_alt'] : '';
$anchor      = isset($section['anchor']) ? $section['anchor'] : '';
$layout      = isset($section['layout']) && function_exists('putrafiber_frontpage_normalise_layout') ? putrafiber_frontpage_normalise_layout($section['layout'], 'full') : 'full';
$heading_tag = isset($section['heading_tag']) && function_exists('putrafiber_frontpage_normalise_heading_tag') ? putrafiber_frontpage_normalise_heading_tag($section['heading_tag'], 'h2') : 'h2';

if (function_exists('putrafiber_frontpage_sanitize_anchor')) {
    $anchor = putrafiber_frontpage_sanitize_anchor($anchor);
}

if (function_exists('putrafiber_frontpage_sanitize_color_value')) {
    $background = putrafiber_frontpage_sanitize_color_value($background);
    $text_color = putrafiber_frontpage_sanitize_color_value($text_color);
}

$style_rules = array();

if (!empty($background)) {
    $style_rules[] = '--pf-custom-bg:' . esc_attr($background);
}

if (!empty($text_color)) {
    $style_rules[] = '--pf-custom-text:' . esc_attr($text_color);
}

$style_attr = '';
if (!empty($style_rules)) {
    $style_attr = ' style="' . esc_attr(implode(';', $style_rules)) . '"';
}

$content_html = '';
if (!empty($content)) {
    $content_html = wpautop($content);
}

$section_id       = !empty($anchor) ? $anchor : $slug;
$section_classes  = array('custom-section', 'custom-section--' . $layout);
$section_attr     = array();
$inner_classes    = array('custom-section__inner', 'custom-section__inner--' . $layout);
$section_label_id = $section_id . '-title';

if (empty($media)) {
    $inner_classes[] = 'custom-section__inner--no-media';
}

$aria_labelledby = '';
if (!empty($title)) {
    $section_attr[] = 'aria-labelledby="' . esc_attr($section_label_id) . '"';
}

$section_attr[] = 'data-layout="' . esc_attr($layout) . '"';

$media_markup = '';
if (!empty($media)) {
    $media_classes = array('custom-section__media', 'custom-section__media--' . $layout);
    $effective_alt = $media_alt !== '' ? $media_alt : $title;

    ob_start();
    ?>
    <figure class="<?php echo esc_attr(implode(' ', $media_classes)); ?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
        <img src="<?php echo esc_url($media); ?>" alt="<?php echo esc_attr($effective_alt); ?>" loading="lazy" decoding="async" />
        <meta itemprop="url" content="<?php echo esc_url($media); ?>" />
        <?php if (!empty($effective_alt)) : ?>
            <meta itemprop="caption" content="<?php echo esc_attr($effective_alt); ?>" />
        <?php endif; ?>
    </figure>
    <?php
    $media_markup = ob_get_clean();
}
?>
<section id="<?php echo esc_attr($section_id); ?>" class="<?php echo esc_attr(implode(' ', $section_classes)); ?>" <?php echo implode(' ', $section_attr); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> itemscope itemtype="https://schema.org/CreativeWork">
    <div class="container">
        <div class="<?php echo esc_attr(implode(' ', $inner_classes)); ?>">
            <?php if ($layout === 'split-left' && !empty($media_markup)) : ?>
                <?php echo $media_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>

            <div class="custom-section__body" itemprop="text">
                <?php if (!empty($subtitle)) : ?>
                    <span class="custom-section__subtitle" itemprop="alternativeHeadline"><?php echo esc_html($subtitle); ?></span>
                <?php endif; ?>

                <?php if (!empty($title)) : ?>
                    <?php printf('<%1$s class="custom-section__title" id="%3$s" itemprop="headline">%2$s</%1$s>', esc_html($heading_tag), esc_html($title), esc_attr($section_label_id)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endif; ?>

                <?php if (!empty($content_html)) : ?>
                    <div class="custom-section__content" itemprop="articleBody"><?php echo wp_kses_post($content_html); ?></div>
                <?php endif; ?>

                <?php if (!empty($button_text) && !empty($button_url)) : ?>
                    <div class="custom-section__actions">
                        <a class="btn btn-primary" href="<?php echo esc_url($button_url); ?>" itemprop="url" rel="noopener">
                            <?php echo esc_html($button_text); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($layout === 'split-right' && !empty($media_markup)) : ?>
                <?php echo $media_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>

            <?php if ($layout === 'full' && !empty($media_markup)) : ?>
                <div class="custom-section__media-wrapper">
                    <?php echo $media_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
