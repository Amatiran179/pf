<?php
/**
 * Archive Template
 * 
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<main id="primary" class="site-main archive-page">
    <header class="archive-header">
        <div class="container">
            <?php putrafiber_breadcrumbs(); ?>
            
            <h1 class="archive-title">
                <?php
                if (is_post_type_archive('portfolio')) {
                    echo __('Our Portfolio', 'putrafiber');
                } else {
                    the_archive_title();
                }
                ?>
            </h1>
            
            <?php if (get_the_archive_description()): ?>
                <div class="archive-description">
                    <?php the_archive_description(); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="archive-content">
        <div class="container">
            <?php if (have_posts()): ?>
                
                <div class="grid <?php echo is_post_type_archive('portfolio') ? 'grid-3' : 'grid-2'; ?>">
                    <?php
                    while (have_posts()):
                        the_post();
                        
                        if (get_post_type() == 'portfolio') {
                            $location = get_post_meta(get_the_ID(), '_portfolio_location', true);
                            ?>
                            <article class="card portfolio-card fade-in">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="portfolio-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('putrafiber-portfolio', array(
                                                'loading' => 'lazy',
                                                'decoding' => 'async'
                                            )); ?>
                                        </a>
                                        <div class="portfolio-overlay">
                                            <a href="<?php the_permalink(); ?>" class="view-project">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="portfolio-card-content">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <?php if ($location): ?>
                                        <p class="location">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <?php echo esc_html($location); ?>
                                        </p>
                                    <?php endif; ?>
                                    <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="read-more"><?php _e('View Project', 'putrafiber'); ?> &rarr;</a>
                                </div>
                            </article>
                            <?php
                        } else {
                            ?>
                            <article class="card post-card fade-in">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('putrafiber-thumb', array(
                                                'loading' => 'lazy',
                                                'decoding' => 'async'
                                            )); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <span class="post-date"><?php echo get_the_date(); ?></span>
                                        <?php
                                        $categories = get_the_category();
                                        if ($categories):
                                        ?>
                                            <span class="post-category">
                                                <a href="<?php echo get_category_link($categories[0]->term_id); ?>">
                                                    <?php echo $categories[0]->name; ?>
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="read-more"><?php _e('Read More', 'putrafiber'); ?> &rarr;</a>
                                </div>
                            </article>
                            <?php
                        }
                    endwhile;
                    ?>
                </div>
                
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('&laquo; Previous', 'putrafiber'),
                    'next_text' => __('Next &raquo;', 'putrafiber'),
                ));
                ?>
                
            <?php else: ?>
                
                <div class="no-results">
                    <h2><?php _e('Nothing Found', 'putrafiber'); ?></h2>
                    <p><?php _e('It seems we can\'t find what you\'re looking for.', 'putrafiber'); ?></p>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary"><?php _e('Back to Home', 'putrafiber'); ?></a>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
