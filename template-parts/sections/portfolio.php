<?php
/**
 * Portfolio Section
 *
 * @package PutraFiber
 */

$portfolio_limit = putrafiber_frontpage_limit('portfolio', 6);
$portfolio_page  = putrafiber_frontpage_section_paged('portfolio');
$portfolio_title = putrafiber_frontpage_text('portfolio', 'title', __('Portofolio Unggulan', 'putrafiber'));
$portfolio_desc  = putrafiber_frontpage_text('portfolio', 'description', __('Lihat bagaimana kami mentransformasi area kosong menjadi destinasi air spektakuler.', 'putrafiber'));

$portfolio_query = new WP_Query(array(
    'post_type'      => 'portfolio',
    'posts_per_page' => $portfolio_limit,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => $portfolio_page,
    'post_status'    => 'publish',
    'no_found_rows'  => false,
    'ignore_sticky_posts' => true,
));
?>

<section class="portfolio-section section section--glass" id="portfolio">
    <div class="section-background" aria-hidden="true">
        <span class="section-ripple"></span>
        <span class="section-ripple section-ripple--alt"></span>
        <span class="section-spark section-spark--left"></span>
        <span class="section-spark section-spark--right"></span>
    </div>

    <div class="container-wide section-content">
        <div class="section-title fade-in">
            <div class="section-pretitle"><?php esc_html_e('Project Ikonik', 'putrafiber'); ?></div>
            <h2><?php echo esc_html($portfolio_title); ?></h2>
            <?php if ($portfolio_desc): ?>
                <div class="section-lead"><?php echo wp_kses_post($portfolio_desc); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($portfolio_query->have_posts()): ?>
            <div class="portfolio-grid">
                <?php
                $delay = 0;
                while ($portfolio_query->have_posts()):
                    $portfolio_query->the_post();
                    $location    = get_post_meta(get_the_ID(), '_portfolio_location', true);
                    $projectDate = get_post_meta(get_the_ID(), '_portfolio_date', true);
                    ?>
                    <article class="portfolio-item fade-in" style="animation-delay: <?php echo esc_attr($delay); ?>s;">
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
                                    <?php if ($projectDate): ?>
                                        <p class="portfolio-date">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 4h18"></path>
                                                <path d="M8 2v4"></path>
                                                <path d="M16 2v4"></path>
                                                <rect x="3" y="8" width="18" height="13" rx="2"></rect>
                                            </svg>
                                            <?php echo esc_html($projectDate); ?>
                                        </p>
                                    <?php endif; ?>
                                    <a href="<?php the_permalink(); ?>" class="portfolio-link">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                        <?php esc_html_e('Lihat Detail', 'putrafiber'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php
                    $delay += 0.1;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>

            <?php echo putrafiber_frontpage_render_pagination('portfolio', $portfolio_query); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else: ?>
            <p class="section-empty fade-in"><?php esc_html_e('Belum ada portofolio yang ditampilkan. Tambahkan project pertama Anda melalui menu Portfolio.', 'putrafiber'); ?></p>
        <?php endif; ?>

        <div class="section-cta fade-in">
            <a href="<?php echo esc_url(home_url('/portfolio/')); ?>" class="btn btn-outline btn-lg">
                <?php esc_html_e('Lihat Semua Portofolio', 'putrafiber'); ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</section>
