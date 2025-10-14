<?php
/**
 * Single Product Template
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

get_header();

while (have_posts()) : the_post();
    $product_id = get_the_ID();
    
    // Get meta data
    $price_raw = get_post_meta($product_id, '_product_price', true);
	$price_display = !empty($price_raw) && $price_raw > 0 ? $price_raw : 0;
	$price_schema = !empty($price_raw) && $price_raw > 0 ? $price_raw : 1000; 
    $price_type = get_post_meta($product_id, '_product_price_type', true) ?: 'price';
    $stock = get_post_meta($product_id, '_product_stock', true) ?: 'ready';
    $sku = get_post_meta($product_id, '_product_sku', true);
    $brand = get_option('putrafiber_options')['company_name'] ?? 'PutraFiber';
    $sizes = get_post_meta($product_id, '_product_sizes', true);
    $colors = get_post_meta($product_id, '_product_colors', true);
    $models = get_post_meta($product_id, '_product_models', true);
    $material = get_post_meta($product_id, '_product_material', true);
    $warranty = get_post_meta($product_id, '_product_warranty', true);
    $short_desc = get_post_meta($product_id, '_product_short_description', true);
    $specs = get_post_meta($product_id, '_product_specifications', true);
    $features = get_post_meta($product_id, '_product_features', true);
    $catalog_pdf = get_post_meta($product_id, '_product_catalog_pdf', true);
    $gallery = get_post_meta($product_id, '_product_gallery', true);
    $gallery_ids = !empty($gallery) ? explode(',', $gallery) : [];
	$formatted_price = $price_display > 0 ? 'Rp ' . number_format($price_display, 0, ',', '.') : '';
    
    // Stock status
    $stock_labels = [
        'ready' => ['text' => 'Ready Stock', 'class' => 'in-stock', 'schema' => 'InStock'],
        'pre-order' => ['text' => 'Pre-Order', 'class' => 'pre-order', 'schema' => 'PreOrder'],
        'out-of-stock' => ['text' => 'Stok Habis', 'class' => 'out-stock', 'schema' => 'OutOfStock'],
    ];
    $stock_info = $stock_labels[$stock] ?? $stock_labels['ready'];
    
    // Categories
    $categories = get_the_terms($product_id, 'product_category');
    $category_name = $categories ? $categories[0]->name : 'Produk';
?>

<main class="product-single-page">
    
    <!-- Breadcrumbs -->
    <section class="breadcrumbs-section">
        <div class="container">
            <?php putrafiber_breadcrumbs(); ?>
        </div>
    </section>
    
    <!-- Product Main Section -->
    <section class="product-main-section">
        <div class="container">
            <div class="product-layout">
                
                <!-- LEFT: Gallery (160x160 slider with zoom & lightbox) -->
                <div class="product-gallery">
                    <?php if (!empty($gallery_ids) || has_post_thumbnail()): ?>
                        <div class="gallery-container">
                            <!-- Main Slider -->
                            <div class="swiper product-gallery-slider">
                                <div class="swiper-wrapper">
                                    <?php 
                                    // Featured image first
                                    if (has_post_thumbnail()): 
                                        $featured_url = get_the_post_thumbnail_url($product_id, 'putrafiber-product');
                                        $featured_full = get_the_post_thumbnail_url($product_id, 'full');
                                    ?>
                                        <div class="swiper-slide">
                                            <a href="<?php echo esc_url($featured_full); ?>" 
                                               data-lightbox="product-<?php echo $product_id; ?>"
                                               data-title="<?php echo esc_attr(get_the_title()); ?>"
                                               class="gallery-item">
                                                <img src="<?php echo esc_url($featured_url); ?>" 
                                                     alt="<?php echo esc_attr(get_the_title()); ?>"
                                                     class="gallery-image">
                                                <span class="zoom-icon">🔍</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($gallery_ids as $img_id): 
                                        $image_url = wp_get_attachment_image_url($img_id, 'putrafiber-product');
                                        $image_full = wp_get_attachment_image_url($img_id, 'full');
                                        $image_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: get_the_title();
                                    ?>
                                        <div class="swiper-slide">
                                            <a href="<?php echo esc_url($image_full); ?>" 
                                               data-lightbox="product-<?php echo $product_id; ?>"
                                               data-title="<?php echo esc_attr($image_alt); ?>"
                                               class="gallery-item">
                                                <img src="<?php echo esc_url($image_url); ?>" 
                                                     alt="<?php echo esc_attr($image_alt); ?>"
                                                     class="gallery-image">
                                                <span class="zoom-icon">🔍</span>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Navigation -->
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                            
                            <!-- Thumbnail Navigation -->
                            <?php if (count($gallery_ids) > 1 || (count($gallery_ids) >= 1 && has_post_thumbnail())): ?>
                                <div class="swiper product-gallery-thumbs">
                                    <div class="swiper-wrapper">
                                        <?php if (has_post_thumbnail()): ?>
                                            <div class="swiper-slide">
                                                <img src="<?php echo get_the_post_thumbnail_url($product_id, 'thumbnail'); ?>" 
                                                     alt="<?php echo esc_attr(get_the_title()); ?>">
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php foreach ($gallery_ids as $img_id): ?>
                                            <div class="swiper-slide">
                                                <img src="<?php echo wp_get_attachment_image_url($img_id, 'thumbnail'); ?>" 
                                                     alt="<?php echo get_post_meta($img_id, '_wp_attachment_image_alt', true); ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-product-image">
                            <img src="<?php echo PUTRAFIBER_URI; ?>/assets/images/no-image.svg" alt="No Image">
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- RIGHT: Product Info -->
                <div class="product-info">
                    <div class="product-meta-header">
                        <?php if ($categories): ?>
                            <span class="product-category">
                                <a href="<?php echo get_term_link($categories[0]); ?>">
                                    <?php echo esc_html($categories[0]->name); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($sku): ?>
                            <span class="product-sku">SKU: <?php echo esc_html($sku); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    
                    <?php if ($short_desc): ?>
                        <div class="product-short-description">
                            <p><?php echo esc_html($short_desc); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Stock Status -->
                    <div class="product-stock <?php echo esc_attr($stock_info['class']); ?>">
                        <span class="stock-icon">
                            <?php 
                            echo $stock === 'ready' ? '✅' : ($stock === 'pre-order' ? '⏳' : '❌');
                            ?>
                        </span>
                        <?php echo esc_html($stock_info['text']); ?>
                    </div>
                    
                    <!-- Price or CTA -->
<?php if ($price_type === 'price'): ?>
    <?php if ($price_display > 0): ?>
        <!-- Jika user ISI HARGA, tampilkan harga -->
        <div class="product-price">
            <span class="price-label">Harga:</span>
            <span class="price-amount"><?php echo esc_html($formatted_price); ?></span>
        </div>
    <?php else: ?>
        <!-- Jika user TIDAK ISI HARGA, tampilkan CTA Tanya Harga -->
        <div class="product-cta-price">
            <a href="<?php echo putrafiber_whatsapp_link('Halo, saya ingin tanya harga produk: ' . get_the_title()); ?>" 
               class="btn-whatsapp-cta" 
               target="_blank" 
               rel="nofollow noopener">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                💬 Tanya Harga
            </a>
            <p class="cta-note">Hubungi kami untuk info harga terbaik</p>
        </div>
    <?php endif; ?>
<?php else: ?>
    <!-- Jika user PILIH CTA WhatsApp -->
    <div class="product-cta-price">
        <a href="<?php echo putrafiber_whatsapp_link('Halo, saya tertarik dengan produk: ' . get_the_title()); ?>" 
           class="btn-whatsapp-cta" 
           target="_blank" 
           rel="nofollow noopener">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            📞 Hubungi Kami
        </a>
        <p class="cta-note">Tanya harga & ketersediaan stok</p>
    </div>
<?php endif; ?>
                    
                    <!-- Product Attributes -->
                    <?php if ($sizes || $colors || $models || $material || $warranty): ?>
                        <div class="product-attributes">
                            <?php if ($sizes): ?>
                                <div class="attribute-item">
                                    <strong>Ukuran:</strong>
                                    <span><?php echo esc_html($sizes); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($colors): ?>
                                <div class="attribute-item">
                                    <strong>Warna:</strong>
                                    <span><?php echo esc_html($colors); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($models): ?>
                                <div class="attribute-item">
                                    <strong>Model:</strong>
                                    <span><?php echo esc_html($models); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($material): ?>
                                <div class="attribute-item">
                                    <strong>Material:</strong>
                                    <span><?php echo esc_html($material); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($warranty): ?>
                                <div class="attribute-item">
                                    <strong>Garansi:</strong>
                                    <span><?php echo esc_html($warranty); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Share Buttons -->
                    <div class="product-share">
                        <strong>Bagikan:</strong>
                        <div class="share-buttons">
                            <?php
                            $share_url = get_permalink();
                            $share_title = get_the_title();
                            ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>" 
                               target="_blank" class="share-btn facebook" title="Share on Facebook">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode($share_title); ?>" 
                               target="_blank" class="share-btn twitter" title="Share on Twitter">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($share_title . ' - ' . $share_url); ?>" 
                               target="_blank" class="share-btn whatsapp" title="Share on WhatsApp">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($catalog_pdf): ?>
                        <div class="product-catalog">
                            <a href="<?php echo esc_url($catalog_pdf); ?>" 
                               class="btn-download-catalog" 
                               target="_blank" 
                               download>
                                📄 Download Katalog PDF
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </section>
    
    <!-- Product Description (Full Width) -->
    <?php if (get_the_content()): ?>
        <section class="product-description-section">
            <div class="container">
                <div class="section-header">
                    <h2>Deskripsi Produk</h2>
                </div>
                <div class="product-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Specifications & Features -->
    <?php if ($specs || $features): ?>
        <section class="product-specs-section">
            <div class="container">
                <div class="specs-grid">
                    <?php if ($specs): ?>
                        <div class="specs-box">
                            <h3>📋 Spesifikasi</h3>
                            <ul>
                                <?php 
                                $specs_lines = explode("\n", $specs);
                                foreach ($specs_lines as $line) {
                                    if (trim($line)) {
                                        echo '<li>' . esc_html(trim($line)) . '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($features): ?>
                        <div class="features-box">
                            <h3>⭐ Fitur & Keunggulan</h3>
                            <ul>
                                <?php 
                                $features_lines = explode("\n", $features);
                                foreach ($features_lines as $line) {
                                    if (trim($line)) {
                                        echo '<li>' . esc_html(trim($line)) . '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Related Products -->
    <?php
    $related = putrafiber_get_related_products($product_id, 4);
    if ($related->have_posts()):
    ?>
        <section class="related-products-section">
            <div class="container">
                <div class="section-header">
                    <h2>Produk Terkait</h2>
                    <p>Produk lain yang mungkin Anda sukai</p>
                </div>
                <div class="products-grid">
                    <?php while ($related->have_posts()): $related->the_post(); ?>
                        <?php get_template_part('template-parts/content', 'product-card'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Comments/Reviews -->
    <?php if (comments_open() || get_comments_number()): ?>
        <section class="product-reviews-section">
            <div class="container">
                <?php comments_template(); ?>
            </div>
        </section>
    <?php endif; ?>
    
</main>

<?php
// Output Product Schema
if (function_exists('putrafiber_generate_product_schema')) {
    echo putrafiber_generate_product_schema($product_id);
}
?>

<?php endwhile; ?>

<?php get_footer(); ?>
