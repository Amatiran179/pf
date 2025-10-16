<?php
/**
 * Products Section
 *
 * @package PutraFiber
 */

$products_limit = putrafiber_frontpage_limit('products', 6);
$products_title = putrafiber_frontpage_text('products', 'title', __('Produk Terlaris', 'putrafiber'));
$products_desc  = putrafiber_frontpage_text('products', 'description', __('Pilihan wahana dan perosotan fiberglass yang siap dikirim ke lokasi Anda.', 'putrafiber'));

$products_query = new WP_Query(array(
    'post_type'      => 'product',
    'posts_per_page' => $products_limit,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
    'no_found_rows'  => true,
    'ignore_sticky_posts' => true,
));
?>

<section class="products-section section section--glass" id="products">
    <div class="section-background" aria-hidden="true">
        <span class="section-ripple"></span>
        <span class="section-ripple section-ripple--delay"></span>
        <span class="section-spark section-spark--left"></span>
        <span class="section-spark section-spark--right"></span>
    </div>

    <div class="container-wide section-content">
        <div class="section-title fade-in">
            <div class="section-pretitle"><?php esc_html_e('Pilihan Unggulan', 'putrafiber'); ?></div>
            <h2><?php echo esc_html($products_title); ?></h2>
            <?php if ($products_desc): ?>
                <div class="section-lead"><?php echo wp_kses_post($products_desc); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($products_query->have_posts()): ?>
            <div class="grid grid-3 products-grid">
                <?php
                $index      = 0;
                $animations = array('animate-rise', 'animate-slide-left', 'animate-zoom-in', 'animate-tilt-in');
                while ($products_query->have_posts()):
                    $products_query->the_post();
                    $price = get_post_meta(get_the_ID(), '_product_price', true);
                    $stock = get_post_meta(get_the_ID(), '_product_stock', true);
                    $animation_class = $animations[$index % count($animations)];
                    $delay_value     = $index * 0.1;
                    ?>
                    <article class="card product-card fade-in <?php echo esc_attr($animation_class); ?>" style="--animation-delay: <?php echo esc_attr(number_format($delay_value, 2, '.', '')); ?>s;">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="product-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('putrafiber-product'); ?>
                                </a>
                                <?php if ($stock === 'pre-order'): ?>
                                    <span class="product-badge badge-preorder"><?php esc_html_e('Pre-Order', 'putrafiber'); ?></span>
                                <?php else: ?>
                                    <span class="product-badge badge-ready"><?php esc_html_e('Ready Stock', 'putrafiber'); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="product-badge badge-ready product-badge--floating"><?php esc_html_e('Ready Stock', 'putrafiber'); ?></span>
                        <?php endif; ?>

                        <div class="product-content">
                            <h3 class="product-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <p class="product-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>

                            <div class="product-footer">
                                <?php if ($price && $price !== '1000'): ?>
                                    <span class="product-price">Rp <?php echo esc_html(number_format((float) $price, 0, ',', '.')); ?></span>
                                <?php endif; ?>

                                <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya tertarik dengan produk: ' . get_the_title())); ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <?php esc_html_e('Order WhatsApp', 'putrafiber'); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php
                    $index++;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else: ?>
            <p class="section-empty fade-in"><?php esc_html_e('Belum ada produk yang ditampilkan. Tambahkan artikel kategori produk melalui menu Blog.', 'putrafiber'); ?></p>
        <?php endif; ?>

        <div class="section-cta fade-in">
            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="btn btn-outline btn-lg">
                <?php esc_html_e('Lihat Semua Produk', 'putrafiber'); ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</section>
