<?php
/**
 * 404 Template
 * 
 * @package PutraFiber
 */

get_header();
?>

<main id="primary" class="site-main error-404-page">
    <div class="container">
        <div class="error-404-content">
            <div class="error-404-illustration">
                <h1 class="error-code">404</h1>
                <svg width="300" height="200" viewBox="0 0 300 200" fill="none">
                    <circle cx="150" cy="100" r="80" stroke="var(--primary-color)" stroke-width="4" opacity="0.2"/>
                    <circle cx="150" cy="100" r="60" stroke="var(--primary-color)" stroke-width="4" opacity="0.4"/>
                    <circle cx="150" cy="100" r="40" stroke="var(--primary-color)" stroke-width="4" opacity="0.6"/>
                    <path d="M150 60 L150 140" stroke="var(--primary-color)" stroke-width="6" stroke-linecap="round"/>
                    <circle cx="150" cy="160" r="4" fill="var(--primary-color)"/>
                </svg>
            </div>
            
            <div class="error-404-text">
                <h2><?php _e('Oops! Page Not Found', 'putrafiber'); ?></h2>
                <p><?php _e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'putrafiber'); ?></p>
                
                <div class="error-404-actions">
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <?php _e('Back to Home', 'putrafiber'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya butuh bantuan')); ?>" class="btn btn-outline" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <?php _e('Contact Us', 'putrafiber'); ?>
                    </a>
                </div>
                
                <div class="error-404-search">
                    <h3><?php _e('Or try searching:', 'putrafiber'); ?></h3>
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>
        
        <!-- Popular Posts -->
        <div class="error-404-suggestions">
            <h3><?php _e('Popular Articles', 'putrafiber'); ?></h3>
            <div class="grid grid-3">
                <?php
                $popular_posts = new WP_Query(array(
                    'posts_per_page' => 3,
                    'orderby' => 'comment_count',
                    'order' => 'DESC'
                ));
                
                if ($popular_posts->have_posts()):
                    while ($popular_posts->have_posts()):
                        $popular_posts->the_post();
                        ?>
                        <article class="card post-card">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('putrafiber-thumb'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="post-content">
                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                <a href="<?php the_permalink(); ?>" class="read-more"><?php _e('Read More', 'putrafiber'); ?> &rarr;</a>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
