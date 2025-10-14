<?php
/**
 * Page Template
 * 
 * @package PutraFiber
 */

get_header();
?>

<main id="primary" class="site-main page-content">
    <?php
    while (have_posts()):
        the_post();
        ?>
        
        <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="page-header">
                <div class="container">
                    <?php putrafiber_breadcrumbs(); ?>
                    <h1 class="page-title"><?php the_title(); ?></h1>
                </div>
            </header>

            <?php if (has_post_thumbnail()): ?>
                <div class="page-featured-image">
                    <?php the_post_thumbnail('full'); ?>
                </div>
            <?php endif; ?>

            <div class="page-content-wrapper">
                <div class="container">
                    <?php
                    the_content();
                    
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'putrafiber'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
            </div>
        </article>
        
    <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
