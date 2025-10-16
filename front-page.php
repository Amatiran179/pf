<?php
/**
 * Front Page Template (Homepage)
 * 
 * This template is used for the static front page.
 * More specific than index.php for homepage display.
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

get_header();

$sections      = function_exists('putrafiber_frontpage_sections') ? putrafiber_frontpage_sections() : array();
$water_bubbles = function_exists('putrafiber_frontpage_water_intensity') ? putrafiber_frontpage_water_intensity() : 8;
$parallax_on   = putrafiber_get_option('front_enable_parallax', '1');
?>

<main
    id="primary"
    class="site-main front-page"
    data-water-intensity="<?php echo esc_attr($water_bubbles); ?>"
    data-parallax="<?php echo esc_attr($parallax_on ? '1' : '0'); ?>"
>

    <div class="frontpage-water-overlay" aria-hidden="true">
        <div class="frontpage-water-layer"></div>
        <div class="frontpage-water-bubbles"></div>
    </div>

    <?php if (!empty($sections)) : ?>
        <?php foreach ($sections as $section_slug) : ?>
            <?php putrafiber_render_frontpage_section($section_slug); ?>
        <?php endforeach; ?>
    <?php else : ?>
        <?php
        // Fallback to legacy order if helper returns nothing (edge case when options deleted).
        $legacy_sections = array('hero', 'features', 'services', 'portfolio', 'cta', 'products', 'blog');
        foreach ($legacy_sections as $legacy_section) {
            putrafiber_render_frontpage_section($legacy_section);
        }
        ?>
    <?php endif; ?>

</main><!-- #primary -->

<?php
/**
 * Hook for additional content before footer
 * 
 * @since 1.0.0
 */
do_action('putrafiber_before_footer');
?>

<?php
get_footer();