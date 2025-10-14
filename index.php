<?php
/**
 * Main Template File
 * 
 * @package PutraFiber
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php if (is_front_page()): ?>
        <?php get_template_part('template-parts/sections/hero'); ?>
        <?php get_template_part('template-parts/sections/features'); ?>
        <?php get_template_part('template-parts/sections/services'); ?>
        <?php get_template_part('template-parts/sections/portfolio'); ?>
        <?php get_template_part('template-parts/sections/cta'); ?>
        <?php get_template_part('template-parts/sections/products'); ?>
        <?php get_template_part('template-parts/sections/blog'); ?>
    <?php else: ?>
        <div class="container">
            <div class="content-area">
                <?php
                if (have_posts()):
                    while (have_posts()):
                        the_post();
                        get_template_part('template-parts/content', get_post_type());
                    endwhile;
                    
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('&laquo; Previous', 'putrafiber'),
                        'next_text' => __('Next &raquo;', 'putrafiber'),
                    ));
                else:
                    get_template_part('template-parts/content', 'none');
                endif;
                ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php
get_footer();
