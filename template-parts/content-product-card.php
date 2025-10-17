<?php
/**
 * Product Card Template Part
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

$product_id = get_the_ID();
$price_raw = get_post_meta($product_id, '_product_price', true);
$price = $price_raw !== '' ? (int) pf_clean_float($price_raw) : 1000;
$price_type = pf_clean_text(get_post_meta($product_id, '_product_price_type', true) ?: 'price');
$stock = pf_clean_text(get_post_meta($product_id, '_product_stock', true) ?: 'ready');
$short_desc = get_post_meta($product_id, '_product_short_description', true);
$categories = get_the_terms($product_id, 'product_category');
?>

<article class="product-card">
    <a href="<?php the_permalink(); ?>" class="product-card-link">
        
        <!-- Thumbnail -->
        <div class="product-thumbnail">
            <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('putrafiber-product', [
                    'alt' => get_the_title(),
                    'loading' => 'lazy'
                ]); ?>
            <?php else: ?>
                <img src="<?php echo pf_output_url(PUTRAFIBER_URI . '/assets/images/no-image.svg'); ?>" alt="No Image">
            <?php endif; ?>
            
            <!-- Stock Badge -->
            <?php if ($stock === 'out-of-stock'): ?>
                <span class="stock-badge out-stock">Stok Habis</span>
            <?php elseif ($stock === 'pre-order'): ?>
                <span class="stock-badge pre-order">Pre-Order</span>
            <?php endif; ?>
            
            <!-- Category Badge -->
            <?php if ($categories): ?>
                <span class="category-badge"><?php echo pf_output_html($categories[0]->name); ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Content -->
        <div class="product-card-content">
            <h3 class="product-card-title"><?php the_title(); ?></h3>
            
            <?php if ($short_desc): ?>
                <p class="product-card-excerpt"><?php echo pf_output_html(wp_trim_words($short_desc, 12)); ?></p>
            <?php elseif (has_excerpt()): ?>
                <p class="product-card-excerpt"><?php echo get_the_excerpt(); ?></p>
            <?php endif; ?>
            
            <div class="product-card-footer">
                <?php if ($price_type === 'price'): ?>
                    <span class="product-card-price">Rp <?php echo pf_output_html(number_format($price, 0, ',', '.')); ?></span>
                <?php else: ?>
                    <span class="product-card-cta">Hubungi Kami</span>
                <?php endif; ?>
                
                <span class="product-card-button">
                    Lihat Detail →
                </span>
            </div>
        </div>
        
    </a>
</article>