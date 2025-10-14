<?php
/**
 * Products Section
 * 
 * @package PutraFiber
 */

$products_query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => 6,
    'category_name' => 'produk',
    'orderby' => 'date',
    'order' => 'DESC'
));
?>

<?php if ($products_query->have_posts()): ?>
<section class="products-section section bg-light" id="products">
    <div class="container-wide">
        <div class="section-title fade-in">
            <h2>Produk Unggulan</h2>
            <p>Koleksi produk fiberglass berkualitas tinggi untuk berbagai kebutuhan</p>
        </div>
        
        <div class="grid grid-3">
            <?php
            $delay = 0;
            while ($products_query->have_posts()):
                $products_query->the_post();
                $price = get_post_meta(get_the_ID(), '_product_price', true);
                $stock = get_post_meta(get_the_ID(), '_product_stock', true);
            ?>
                <div class="card product-card fade-in" style="animation-delay: <?php echo $delay; ?>s;">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="product-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('putrafiber-product'); ?>
                            </a>
                            <?php if ($stock === 'pre-order'): ?>
                                <span class="product-badge badge-preorder">Pre-Order</span>
                            <?php else: ?>
                                <span class="product-badge badge-ready">Ready Stock</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="product-content">
                        <h3 class="product-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <p class="product-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                        
                        <div class="product-footer">
                            <?php if ($price && $price != '1000'): ?>
                                <span class="product-price">Rp <?php echo number_format($price, 0, ',', '.'); ?></span>
                            <?php endif; ?>
                            
                            <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya tertarik dengan produk: ' . get_the_title())); ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                Order WhatsApp
                            </a>
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
            <a href="<?php echo home_url('/category/produk/'); ?>" class="btn btn-outline btn-lg">
                Lihat Semua Produk
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>
