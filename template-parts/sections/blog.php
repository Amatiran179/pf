<?php
/**
 * Blog Section
 * 
 * @package PutraFiber
 */

$blog_query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => 3,
    'category__not_in' => array(get_cat_ID('produk')),
    'orderby' => 'date',
    'order' => 'DESC'
));
?>

<?php if ($blog_query->have_posts()): ?>
<section class="blog-section section" id="blog">
    <div class="container-wide">
        <div class="section-title fade-in">
            <h2>Artikel & Berita Terbaru</h2>
            <p>Tips, panduan, dan informasi terkini seputar waterpark dan playground</p>
        </div>
        
        <div class="grid grid-3">
            <?php
            $delay = 0;
            while ($blog_query->have_posts()):
                $blog_query->the_post();
                $categories = get_the_category();
            ?>
                <article class="card blog-card fade-in" style="animation-delay: <?php echo $delay; ?>s;">
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
                                <?php echo get_the_date(); ?>
                            </span>
                            <span class="blog-reading-time">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                                <?php echo putrafiber_reading_time(); ?> min
                            </span>
                        </div>
                        
                        <h3 class="blog-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <p class="blog-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            Baca Selengkapnya
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
        
        <div class="section-cta fade-in">
            <a href="<?php echo home_url('/blog/'); ?>" class="btn btn-outline btn-lg">
                Lihat Semua Artikel
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>
