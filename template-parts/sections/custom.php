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
?>
<section id="<?php echo esc_attr($slug); ?>" class="custom-section"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="container">
        <?php if (!empty($subtitle)) : ?>
            <span class="custom-section__subtitle"><?php echo esc_html($subtitle); ?></span>
        <?php endif; ?>

        <?php if (!empty($title)) : ?>
            <h2 class="custom-section__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if (!empty($content_html)) : ?>
            <div class="custom-section__content"><?php echo wp_kses_post($content_html); ?></div>
        <?php endif; ?>

        <?php if (!empty($button_text) && !empty($button_url)) : ?>
            <div class="custom-section__actions">
                <a class="btn btn-primary" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>
