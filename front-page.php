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
?>

<main id="primary" class="site-main front-page">
    
    <?php
    /**
     * Hero Section
     * 
     * Displays main hero banner with CTA
     */
    get_template_part('template-parts/sections/hero');
    ?>
    
    <?php
    /**
     * Features Section
     * 
     * Company advantages and features
     */
    get_template_part('template-parts/sections/features');
    ?>
    
    <?php
    /**
     * Services Section
     * 
     * Main services offered
     */
    get_template_part('template-parts/sections/services');
    ?>
    
    <?php
    /**
     * Portfolio Section
     * 
     * Latest portfolio projects
     */
    get_template_part('template-parts/sections/portfolio');
    ?>
    
    <?php
    /**
     * CTA Section
     * 
     * Call to action for consultation
     */
    get_template_part('template-parts/sections/cta');
    ?>
    
    <?php
    /**
     * Products Section
     * 
     * Featured products
     */
    get_template_part('template-parts/sections/products');
    ?>
    
    <?php
    /**
     * Blog Section
     * 
     * Latest blog posts
     */
    get_template_part('template-parts/sections/blog');
    ?>
    
    <?php
    /**
     * Testimonials Section (Optional)
     * 
     * You can add testimonials section here
     */
    if (putrafiber_get_option('enable_testimonials', false)):
        get_template_part('template-parts/sections/testimonials');
    endif;
    ?>
    
    <?php
    /**
     * Partners/Clients Section (Optional)
     * 
     * Display partner logos or client list
     */
    if (putrafiber_get_option('enable_partners', false)):
        get_template_part('template-parts/sections/partners');
    endif;
    ?>

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