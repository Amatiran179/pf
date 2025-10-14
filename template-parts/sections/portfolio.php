<?php
/**
 * Portfolio Section
 * 
 * @package PutraFiber
 */

$portfolio_query = new WP_Query(array(
    'post_type' => 'portfolio',
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order' => 'DESC'
));
?>

<?php if ($portfolio_query->have_posts()): ?>
<section class="portfolio-section section" id="portfolio">
    <div class="container-wide">
        <div class="section-title fade-in">
            <h2>Portofolio Kami</h2>
            <p>Lihat berbagai project waterpark dan playground yang telah kami kerjakan</p>
        </div>
        
        <div class="portfolio-grid">
            <?php
            $delay = 0;
            while ($portfolio_query->have_posts()):
                $portfolio_query->the_post();
                $location = get_post_meta(get_the_ID(), '_portfolio_location', true);
                $project_date = get_post_meta(get_the_ID(), '_portfolio_date', true);
            ?>
                <div class="portfolio-item fade-in" style="animation-delay: <?php echo $delay; ?>s;">
                    <div class="portfolio-image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('putrafiber-portfolio'); ?>
                        <?php endif; ?>
                        <div class="portfolio-overlay">
                            <div class="portfolio-info">
                                <h3><?php the_title(); ?></h3>
                                <?php if ($location): ?>
                                    <p class="portfolio-location">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        <?php echo esc_html($location); ?>
                                    </p>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="portfolio-link">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                $delay += 0.1;
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        
        <div class="section-cta fade-in">
            <a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="btn btn-outline btn-lg">
                Lihat Semua Portofolio
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>
