<?php
/**
 * Blog Section
 *
 * @package PutraFiber
 */

$blog_limit = putrafiber_frontpage_limit('blog', 3);
$blog_title = putrafiber_frontpage_text('blog', 'title', __('Artikel & Insight Terbaru', 'putrafiber'));
$blog_desc  = putrafiber_frontpage_text('blog', 'description', __('Strategi operasional waterpark, tips maintenance, dan berita terbaru industri rekreasi air.', 'putrafiber'));

$blog_query = new WP_Query(array(
    'post_type'      => 'post',
    'posts_per_page' => $blog_limit,
    'category__not_in' => array(get_cat_ID('produk')),
    'orderby'        => 'date',
    'order'          => 'DESC'
));
?>

<section class="blog-section section" id="blog">
    <div class="container-wide">
        <div class="section-title fade-in">
            <h2><?php echo esc_html($blog_title); ?></h2>
            <?php if ($blog_desc): ?>
                <p><?php echo esc_html($blog_desc); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($blog_query->have_posts()): ?>
            <div class="grid grid-3">
                <?php
                $delay = 0;
                while ($blog_query->have_posts()):
                    $blog_query->the_post();
                    $categories = get_the_category();
                    ?>
                    <article class="card blog-card fade-in" style="animation-delay: <?php echo esc_attr($delay); ?>s;">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="blog-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('putrafiber-thumb'); ?>
                                </a>
                                <?php if ($categories): ?>
                                    <span class="blog-category"><?php echo esc_html($categories[0]->name); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="blog-date">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?php echo esc_html(get_the_date()); ?>
                                </span>
                                <span class="blog-reading-time">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                    <?php echo esc_html(putrafiber_reading_time()); ?> min
                                </span>
                            </div>

                            <h3 class="blog-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <p class="blog-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>

                            <a href="<?php the_permalink(); ?>" class="read-more">
                                <?php esc_html_e('Baca Selengkapnya', 'putrafiber'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>
                    <?php
                    $delay += 0.1;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else: ?>
            <p class="section-empty fade-in"><?php esc_html_e('Belum ada artikel yang diterbitkan. Mulai bagikan berita terbaru melalui menu Posts.', 'putrafiber'); ?></p>
        <?php endif; ?>

        <div class="section-cta fade-in">
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-outline btn-lg">
                <?php esc_html_e('Lihat Semua Artikel', 'putrafiber'); ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            <?php if (current_user_can('edit_posts')): ?>
                <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="btn btn-primary btn-lg">
                    <?php esc_html_e('Tambah Artikel', 'putrafiber'); ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
