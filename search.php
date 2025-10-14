<?php
/**
 * Search Template
 * 
 * @package PutraFiber
 */

get_header();
?>

<main id="primary" class="site-main search-page">
    <header class="search-header">
        <div class="container">
            <h1 class="search-title">
                <?php
                printf(
                    esc_html__('Search Results for: %s', 'putrafiber'),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            
            <div class="search-form-wrapper">
                <?php get_search_form(); ?>
            </div>
        </div>
    </header>

    <div class="search-content">
        <div class="container">
            <?php if (have_posts()): ?>
                
                <p class="search-results-count">
                    <?php
                    global $wp_query;
                    printf(
                        esc_html__('Found %s results', 'putrafiber'),
                        '<strong>' . $wp_query->found_posts . '</strong>'
                    );
                    ?>
                </p>
                
                <div class="search-results">
                    <?php
                    while (have_posts()):
                        the_post();
                        ?>
                        <article class="search-result-item">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="result-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="result-content">
                                <div class="result-meta">
                                    <span class="result-type"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></span>
                                    <span class="result-date"><?php echo get_the_date(); ?></span>
                                </div>
                                
                                <h3 class="result-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="result-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                                </div>
                                
                                <a href="<?php the_permalink(); ?>" class="result-link"><?php _e('Read More', 'putrafiber'); ?> &rarr;</a>
                            </div>
                        </article>
                        <?php
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
                    <svg width="150" height="150" viewBox="0 0 150 150" fill="none">
                        <circle cx="75" cy="75" r="70" stroke="var(--primary-color)" stroke-width="3" opacity="0.3"/>
                        <path d="M55 75 L95 75" stroke="var(--primary-color)" stroke-width="4" stroke-linecap="round"/>
                        <circle cx="60" cy="60" r="8" fill="var(--primary-color)" opacity="0.5"/>
                        <circle cx="90" cy="60" r="8" fill="var(--primary-color)" opacity="0.5"/>
                    </svg>
                    
                    <h2><?php _e('No Results Found', 'putrafiber'); ?></h2>
                    <p><?php _e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'putrafiber'); ?></p>
                    
                    <div class="search-suggestions">
                        <h3><?php _e('Search Suggestions:', 'putrafiber'); ?></h3>
                        <ul>
                            <li><?php _e('Check your spelling', 'putrafiber'); ?></li>
                            <li><?php _e('Try more general keywords', 'putrafiber'); ?></li>
                            <li><?php _e('Try different keywords', 'putrafiber'); ?></li>
                        </ul>
                    </div>
                    
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary"><?php _e('Back to Home', 'putrafiber'); ?></a>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
