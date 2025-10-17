
<?php
/**
 * Blog Section
 *
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

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
        'numberposts'         => $needed,
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'orderby'             => 'date',
        'order'               => 'DESC',
        'post__not_in'        => $exclude_ids,
        'ignore_sticky_posts' => true,
        'fields'              => 'ids',
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

$blog_settings = function_exists('putrafiber_frontpage_card_settings') ? putrafiber_frontpage_card_settings('blog') : array(
    'layout'            => 'grid',
    'style'             => 'glass',
    'animation'         => 'auto',
    'columns'           => 3,
    'background_effect' => 'glass',
    'size'              => 'comfortable',
);

$post_map = array();
$post_ids = array();
if ($blog_query->have_posts()) {
    foreach ($blog_query->posts as $post_obj) {
        $post_map[$post_obj->ID] = $post_obj;
        $post_ids[] = $post_obj->ID;
    }
}

$blog_deck = function_exists('putrafiber_frontpage_blog_deck') ? putrafiber_frontpage_blog_deck($post_ids) : array();

$blog_effect = isset($blog_settings['background_effect']) ? $blog_settings['background_effect'] : 'glass';
$section_classes = array('blog-section', 'section');
if ($blog_effect && $blog_effect !== 'none') {
    $section_classes[] = 'section-effect--' . $blog_effect;
}

$grid_classes = array('blog-grid', 'card-collection');
$grid_classes[] = 'card-layout--' . (isset($blog_settings['layout']) ? $blog_settings['layout'] : 'grid');
$grid_classes[] = 'card-style--' . (isset($blog_settings['style']) ? $blog_settings['style'] : 'glass');
$grid_classes[] = 'card-size--' . (isset($blog_settings['size']) ? $blog_settings['size'] : 'comfortable');
$grid_classes[] = 'card-columns--' . max(1, (int) $blog_settings['columns']);
$blog_animation = isset($blog_settings['animation']) ? $blog_settings['animation'] : 'auto';
?>

<section class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $section_classes))); ?>" id="blog">
    <?php if ($blog_effect && $blog_effect !== 'none'): ?>
        <div class="section-background section-background--<?php echo esc_attr($blog_effect); ?>" aria-hidden="true">
            <?php if ($blog_effect === 'glass'): ?>
                <span class="section-ripple"></span>
                <span class="section-ripple section-ripple--delay"></span>
                <span class="section-spark section-spark--left"></span>
                <span class="section-spark section-spark--right"></span>
            <?php elseif ($blog_effect === 'waves'): ?>
                <span class="section-wave"></span>
                <span class="section-wave section-wave--delay"></span>
            <?php elseif ($blog_effect === 'aurora'): ?>
                <span class="section-aurora"></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="container-wide section-content">
        <div class="section-title fade-in">
            <div class="section-pretitle"><?php esc_html_e('Wawasan Terbaru', 'putrafiber'); ?></div>
            <h2><?php echo esc_html($blog_title); ?></h2>
            <?php if ($blog_desc): ?>
                <div class="section-lead"><?php echo wp_kses_post($blog_desc); ?></div>
            <?php endif; ?>
        </div>

        <?php if (!empty($blog_deck)): ?>
            <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $grid_classes))); ?>">
                <?php
                $delay = 0;
                global $post;
                foreach ($blog_deck as $entry):
                    $entry_type = isset($entry['type']) ? $entry['type'] : 'post';
                    $style_tokens = array();
                    $card_classes = array('card', 'blog-card', 'fade-in');
                    $card_classes[] = 'card--style-' . (isset($blog_settings['style']) ? $blog_settings['style'] : 'glass');
                    $card_classes[] = 'card--size-' . (isset($blog_settings['size']) ? $blog_settings['size'] : 'comfortable');

                    if ($blog_settings['layout'] === 'magazine' && $delay === 0) {
                        $card_classes[] = 'blog-card--hero';
                    }

                    $card_animation = $blog_animation;
                    $card_data = array();

                    if ($entry_type === 'custom' && !empty($entry['data'])) {
                        $card_data = $entry['data'];
                        if (!empty($card_data['animation'])) {
                            $card_animation = $card_data['animation'];
                        }
                        if (!empty($card_data['background'])) {
                            $style_tokens[] = '--card-bg:' . $card_data['background'];
                        }
                        if (!empty($card_data['text_color'])) {
                            $style_tokens[] = '--card-text:' . $card_data['text_color'];
                        }
                        if (!empty($card_data['accent_color'])) {
                            $style_tokens[] = '--card-accent:' . $card_data['accent_color'];
                        }
                        if (!empty($card_data['custom_class'])) {
                            $card_classes[] = $card_data['custom_class'];
                        }
                        $card_classes[] = 'blog-card--custom';
                    }

                    if ($card_animation === 'auto') {
                        $sequence = array('rise', 'zoom', 'tilt', 'float');
                        $card_animation = $sequence[$delay % count($sequence)];
                    }
                    $animation_class = ($card_animation && $card_animation !== 'none') ? 'card-animate--' . $card_animation : '';
                    if ($animation_class) {
                        $card_classes[] = $animation_class;
                    }

                    $delay_value = number_format($delay * 0.12, 2, '.', '');
                    $style_attr = !empty($style_tokens)
                        ? ' style="' . esc_attr(implode(';', $style_tokens)) . ';--animation-delay:' . esc_attr($delay_value) . 's"'
                        : ' style="--animation-delay:' . esc_attr($delay_value) . 's"';

                    if ($entry_type === 'post') {
                        $post_id = isset($entry['post_id']) ? (int) $entry['post_id'] : 0;
                        if (!$post_id || !isset($post_map[$post_id])) {
                            continue;
                        }

                        $post = $post_map[$post_id];
                        setup_postdata($post);
                        $categories = get_the_category($post_id);
                        ?>
                        <article class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $card_classes))); ?>"<?php echo $style_attr; ?>>
                            <?php if (has_post_thumbnail($post_id)): ?>
                                <div class="blog-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo get_the_post_thumbnail($post_id, 'putrafiber-thumb', array('loading' => 'lazy', 'decoding' => 'async')); ?>
                                    </a>
                                    <?php if (!empty($slot_labels[$post_id])): ?>
                                        <span class="blog-slot-badge"><?php echo esc_html($slot_labels[$post_id]); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($categories)): ?>
                                        <span class="blog-category"><?php echo esc_html($categories[0]->name); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php elseif (!empty($slot_labels[$post_id])): ?>
                                <span class="blog-slot-badge blog-slot-badge--floating"><?php echo esc_html($slot_labels[$post_id]); ?></span>
                            <?php endif; ?>

                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span class="blog-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <?php echo esc_html(get_the_date('', $post)); ?>
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

                                <div class="blog-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></div>

                                <a href="<?php the_permalink(); ?>" class="card-link">
                                    <?php esc_html_e('Baca Selengkapnya', 'putrafiber'); ?>
                                    <span class="card-link__icon" aria-hidden="true">→</span>
                                </a>
                            </div>
                        </article>
                        <?php
                        $delay++;
                        continue;
                    }

                    // Custom landing page article
                    ?>
                    <article class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $card_classes))); ?>"<?php echo $style_attr; ?>>
                        <?php if (!empty($card_data['badge'])): ?>
                            <span class="card-badge"><?php echo esc_html($card_data['badge']); ?></span>
                        <?php endif; ?>
                        <?php
                        $media_type = isset($card_data['icon_type']) ? $card_data['icon_type'] : '';
                        $media_class = array('card-media');
                        if ($media_type) {
                            $media_class[] = 'card-media--' . $media_type;
                        }
                        $media_class[] = 'card-media--' . (isset($card_data['image_size']) ? $card_data['image_size'] : 'auto');
                        ?>
                        <?php if (!empty($card_data['image']) || ($media_type === 'icon' && !empty($card_data['icon']))): ?>
                            <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $media_class))); ?>">
                                <?php if ($media_type === 'icon' || empty($card_data['image'])): ?>
                                    <span class="card-media__icon">
                                        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <?php echo putrafiber_frontpage_icon_svg(isset($card_data['icon']) ? $card_data['icon'] : 'spark'); ?>
                                        </svg>
                                    </span>
                                <?php else: ?>
                                    <span class="card-media__image">
                                        <img src="<?php echo esc_url($card_data['image']); ?>" alt="<?php echo esc_attr($card_data['image_alt']); ?>" loading="lazy" decoding="async">
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="blog-content">
                            <div class="blog-meta">
                                <?php if (!empty($card_data['date_label'])): ?>
                                    <span class="blog-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <?php echo esc_html($card_data['date_label']); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($card_data['reading_time'])): ?>
                                    <span class="blog-reading-time">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                        </svg>
                                        <?php echo esc_html($card_data['reading_time']); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($card_data['author_label'])): ?>
                                    <span class="blog-author"><?php echo esc_html($card_data['author_label']); ?></span>
                                <?php endif; ?>
                            </div>

                            <h3 class="blog-title"><?php echo esc_html($card_data['title']); ?></h3>
                            <?php if (!empty($card_data['subtitle'])): ?>
                                <p class="card-subtitle"><?php echo esc_html($card_data['subtitle']); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($card_data['excerpt'])): ?>
                                <div class="blog-excerpt"><?php echo wp_kses_post(wpautop($card_data['excerpt'])); ?></div>
                            <?php endif; ?>

                            <?php if (!empty($card_data['list'])): ?>
                                <?php $list_class = array('card-list');
                                if (!empty($card_data['list_effect'])) {
                                    $list_class[] = 'card-list--' . $card_data['list_effect'];
                                }
                                ?>
                                <ul class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $list_class))); ?>">
                                    <?php foreach ($card_data['list'] as $bullet): ?>
                                        <li><?php echo esc_html($bullet); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if (!empty($card_data['link_url'])): ?>
                                <?php $custom_label = !empty($card_data['link_text']) ? $card_data['link_text'] : (!empty($card_data['button_label']) ? $card_data['button_label'] : __('Selengkapnya', 'putrafiber')); ?>
                                <a href="<?php echo esc_url($card_data['link_url']); ?>" class="card-link">
                                    <?php echo esc_html($custom_label); ?>
                                    <span class="card-link__icon" aria-hidden="true">→</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php
                    $delay++;
                endforeach;
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
