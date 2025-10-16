<?php
/**
 * Blog Section
 *
 * @package PutraFiber
 */

$blog_limit = putrafiber_frontpage_limit('blog', 3);
$blog_title = putrafiber_frontpage_text('blog', 'title', __('Artikel & Insight Terbaru', 'putrafiber'));
$blog_desc  = putrafiber_get_option('front_blog_description', __('Strategi operasional waterpark, tips maintenance, dan berita terbaru industri rekreasi air.', 'putrafiber'));

$manual_slots = function_exists('putrafiber_frontpage_blog_slots') ? putrafiber_frontpage_blog_slots() : array();
$slot_labels  = array();
$manual_ids   = array();

foreach ($manual_slots as $slot) {
    $manual_ids[] = $slot['post_id'];
    $slot_labels[$slot['post_id']] = $slot['label'];
}

$manual_ids = array_values(array_unique($manual_ids));

$produk_category = get_cat_ID('produk');
$exclude_ids     = $manual_ids;

if (count($manual_ids) < $blog_limit) {
    $needed      = $blog_limit - count($manual_ids);
    $latest_args = array(
        'numberposts'      => $needed,
        'post_type'        => 'post',
        'post_status'      => 'publish',
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post__not_in'     => $exclude_ids,
        'ignore_sticky_posts' => true,
        'fields'           => 'ids',
    );

    if ($produk_category > 0) {
        $latest_args['category__not_in'] = array($produk_category);
    }

    $latest_ids = get_posts($latest_args);
    if (!empty($latest_ids)) {
        $manual_ids = array_merge($manual_ids, $latest_ids);
    }
}

if (!empty($manual_ids)) {
    $blog_args = array(
        'post_type'           => 'post',
        'post__in'            => $manual_ids,
        'orderby'             => 'post__in',
        'posts_per_page'      => count($manual_ids),
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    );
} else {
    $blog_args = array(
        'post_type'           => 'post',
        'posts_per_page'      => $blog_limit,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'post_status'         => 'publish',
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
    );
}

if ($produk_category > 0) {
    $blog_args['category__not_in'] = array($produk_category);
}

$blog_query = new WP_Query($blog_args);

$page_for_posts   = (int) get_option('page_for_posts');
$blog_archive_url = $page_for_posts ? get_permalink($page_for_posts) : home_url('/');
?>

<section class="blog-section section section--glass" id="blog">
    <div class="section-background" aria-hidden="true">
        <span class="section-ripple"></span>
        <span class="section-ripple section-ripple--delay"></span>
        <span class="section-spark section-spark--left"></span>
        <span class="section-spark section-spark--right"></span>
    </div>

    <div class="container-wide section-content">
        <div class="section-title fade-in">
            <div class="section-pretitle"><?php esc_html_e('Wawasan Terbaru', 'putrafiber'); ?></div>
            <h2><?php echo esc_html($blog_title); ?></h2>
            <?php if ($blog_desc): ?>
                <div class="section-lead"><?php echo wp_kses_post($blog_desc); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($blog_query->have_posts()): ?>
            <div class="grid grid-3 blog-grid">
                <?php
                $delay       = 0;
                $animations  = array('animate-rise', 'animate-zoom-in', 'animate-slide-left', 'animate-tilt-in');
                while ($blog_query->have_posts()):
                    $blog_query->the_post();
                    $categories = get_the_category();
                    $animation_class = $animations[$delay % count($animations)];
                    $delay_value     = $delay * 0.12;
                    ?>
                    <article class="card blog-card fade-in <?php echo esc_attr($animation_class); ?>" style="--animation-delay: <?php echo esc_attr(number_format($delay_value, 2, '.', '')); ?>s;">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="blog-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('putrafiber-thumb'); ?>
                                </a>
                                <?php if (!empty($slot_labels[get_the_ID()])): ?>
                                    <span class="blog-slot-badge"><?php echo esc_html($slot_labels[get_the_ID()]); ?></span>
                                <?php endif; ?>
                                <?php if ($categories): ?>
                                    <span class="blog-category"><?php echo esc_html($categories[0]->name); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php elseif (!empty($slot_labels[get_the_ID()])): ?>
                            <span class="blog-slot-badge blog-slot-badge--floating"><?php echo esc_html($slot_labels[get_the_ID()]); ?></span>
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
                    $delay++;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else: ?>
            <p class="section-empty fade-in"><?php esc_html_e('Belum ada artikel yang diterbitkan. Mulai bagikan berita terbaru melalui menu Posts.', 'putrafiber'); ?></p>
        <?php endif; ?>

        <div class="section-cta fade-in">
            <a href="<?php echo esc_url($blog_archive_url); ?>" class="btn btn-outline btn-lg">
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
