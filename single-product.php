<?php
/**
 * Single Product Template (ANTI-ZOOM INLINE + SWIPER SAFE)
 *
 * @package PutraFiber
 * @version 1.2.0
 * - Inline anti-zoom pada <a.gallery-item> & <img.gallery-image>
 * - Tidak menyentuh transform wrapper/slide (Swiper autoplay aman)
 * - Struktur galeri sinkron dengan product-gallery.js
 */

get_header();

while (have_posts()) : the_post();
  $product_id = get_the_ID();

  // Meta
  $price_raw     = get_post_meta($product_id, '_product_price', true);
  $price_display = (!empty($price_raw) && $price_raw > 0) ? (int)$price_raw : 0;
  $price_schema  = (!empty($price_raw) && $price_raw > 0) ? (int)$price_raw : 1000;
  $price_type    = get_post_meta($product_id, '_product_price_type', true) ?: 'price';
  $stock         = get_post_meta($product_id, '_product_stock', true) ?: 'ready';
  $sku           = get_post_meta($product_id, '_product_sku', true);
  $brand         = function_exists('putrafiber_get_option') ? putrafiber_get_option('company_name', 'PutraFiber') : 'PutraFiber';

  $sizes     = get_post_meta($product_id, '_product_sizes', true);
  $colors    = get_post_meta($product_id, '_product_colors', true);
  $models    = get_post_meta($product_id, '_product_models', true);
  $material  = get_post_meta($product_id, '_product_material', true);
  $warranty  = get_post_meta($product_id, '_product_warranty', true);
  $short_desc= get_post_meta($product_id, '_product_short_description', true);
  $specs     = get_post_meta($product_id, '_product_specifications', true);
  $features  = get_post_meta($product_id, '_product_features', true);
  $catalog_pdf = get_post_meta($product_id, '_product_catalog_pdf', true);

  // Galeri (normalisasi & data attachment lengkap)
  $gallery_raw = get_post_meta($product_id, '_product_gallery', true);
  $featured_id = has_post_thumbnail() ? get_post_thumbnail_id($product_id) : 0;

  if (function_exists('putrafiber_extract_gallery_ids')) {
    $gallery_ids = putrafiber_extract_gallery_ids($gallery_raw);
  } else {
    $tmp = array_map('trim', explode(',', (string) $gallery_raw));
    $tmp = array_filter($tmp, function ($v) { return $v !== '' && ctype_digit($v); });
    $gallery_ids = array_values(array_unique(array_map('intval', $tmp)));
  }

  if ($featured_id) {
    $gallery_ids = array_values(array_filter($gallery_ids, function ($id) use ($featured_id) {
      return (int) $id !== (int) $featured_id;
    }));
  }

  $all_gallery_images = array();
  $product_title      = get_the_title();

  if (function_exists('putrafiber_build_gallery_items')) {
    if ($featured_id) {
      $featured_items = putrafiber_build_gallery_items(array($featured_id), array(
        'image_size'   => 'putrafiber-product',
        'thumb_size'   => 'thumbnail',
        'fallback_alt' => $product_title,
      ));
      if (!empty($featured_items)) {
        $all_gallery_images[] = $featured_items[0];
      }
    }

    if (!empty($gallery_ids)) {
      $additional_items = putrafiber_build_gallery_items($gallery_ids, array(
        'image_size'   => 'putrafiber-product',
        'thumb_size'   => 'thumbnail',
        'fallback_alt' => $product_title,
      ));
      if (!empty($additional_items)) {
        $all_gallery_images = array_merge($all_gallery_images, $additional_items);
      }
    }
  } else {
    if ($featured_id && has_post_thumbnail()) {
      $featured_url  = get_the_post_thumbnail_url($product_id, 'putrafiber-product');
      $featured_full = get_the_post_thumbnail_url($product_id, 'full');
      $featured_thumb= get_the_post_thumbnail_url($product_id, 'thumbnail');
      $feat_meta     = wp_get_attachment_image_src($featured_id, 'putrafiber-product');
      $all_gallery_images[] = array(
        'id'     => $featured_id,
        'url'    => $featured_url,
        'full'   => $featured_full ?: $featured_url,
        'thumb'  => $featured_thumb ?: $featured_url,
        'alt'    => $product_title,
        'width'  => $feat_meta ? (int) $feat_meta[1] : 0,
        'height' => $feat_meta ? (int) $feat_meta[2] : 0,
      );
    }

    if (!empty($gallery_ids)) {
      foreach ($gallery_ids as $img_id) {
        $image_url   = wp_get_attachment_image_url($img_id, 'putrafiber-product');
        $image_full  = wp_get_attachment_image_url($img_id, 'full');
        $thumb_url   = wp_get_attachment_image_url($img_id, 'thumbnail');
        if (!$image_url && !$image_full) {
          continue;
        }
        $img_meta = wp_get_attachment_image_src($img_id, 'putrafiber-product');
        $alt      = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: $product_title;
        $all_gallery_images[] = array(
          'id'     => $img_id,
          'url'    => $image_url ?: $image_full,
          'full'   => $image_full ?: $image_url,
          'thumb'  => $thumb_url ?: ($image_url ?: $image_full),
          'alt'    => $alt,
          'width'  => $img_meta ? (int) $img_meta[1] : 0,
          'height' => $img_meta ? (int) $img_meta[2] : 0,
        );
      }
    }
  }

  $total_gallery_images = count(array_filter($all_gallery_images, function ($image) {
    return !empty($image['url']);
  }));

  $formatted_price = $price_display > 0 ? 'Rp ' . number_format($price_display, 0, ',', '.') : '';

  // Stock labels
  $stock_labels = array(
    'ready'        => array('text' => 'Ready Stock', 'class' => 'in-stock',  'schema' => 'InStock'),
    'pre-order'    => array('text' => 'Pre-Order',   'class' => 'pre-order','schema' => 'PreOrder'),
    'out-of-stock' => array('text' => 'Stok Habis',  'class' => 'out-stock','schema' => 'OutOfStock'),
  );
  $stock_info = isset($stock_labels[$stock]) ? $stock_labels[$stock] : $stock_labels['ready'];

  // Kategori
  $categories    = get_the_terms($product_id, 'product_category');
  $category_name = $categories ? $categories[0]->name : 'Produk';

  // Inline anti-zoom style string (dipakai di <a> & <img>)
  $anti_zoom_style = 'transform:none!important;-webkit-transform:none!important;transition:none!important;animation:none!important;will-change:auto!important;';
?>

<main class="product-single-page">
  <section class="breadcrumbs-section">
    <div class="container">
      <?php if (function_exists('putrafiber_breadcrumbs')) putrafiber_breadcrumbs(); ?>
    </div>
  </section>

  <section class="product-main-section">
    <div class="container">
      <div class="product-layout">

        <!-- ====================== GALERI ====================== -->
        <div class="product-gallery">
          <?php if ($total_gallery_images > 0): ?>
            <?php $gallery_group = 'pf-product-' . $product_id; ?>
            <div class="gallery-container" data-gallery-group="<?php echo esc_attr($gallery_group); ?>">
              <!-- Main slider -->
              <div class="swiper product-gallery-slider" data-gallery-group="<?php echo esc_attr($gallery_group); ?>">
                <div class="swiper-wrapper">
                  <?php foreach ($all_gallery_images as $index => $image):
                    if (empty($image['url'])) {
                      continue;
                    }
                    $img_alt   = !empty($image['alt']) ? $image['alt'] : get_the_title();
                    $img_width = !empty($image['width']) ? (int) $image['width'] : 0;
                    $img_height= !empty($image['height']) ? (int) $image['height'] : 0;
                  ?>
                  <div class="swiper-slide">
                    <a href="<?php echo esc_url($image['full']); ?>"
                       data-lightbox="product-<?php echo (int) $product_id; ?>"
                       data-title="<?php echo esc_attr($img_alt); ?>"
                       class="gallery-item"
                       data-gallery-group="<?php echo esc_attr($gallery_group); ?>"
                       data-gallery-index="<?php echo esc_attr($index); ?>"
                       style="<?php echo esc_attr($anti_zoom_style); ?>">
                      <img
                        src="<?php echo esc_url($image['url']); ?>"
                        alt="<?php echo esc_attr($img_alt); ?>"
                        class="gallery-image"
                        <?php if ($img_width && $img_height): ?>
                          width="<?php echo (int) $img_width; ?>" height="<?php echo (int) $img_height; ?>"
                        <?php endif; ?>
                        <?php echo $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"'; ?>
                        style="<?php echo esc_attr($anti_zoom_style); ?>"
                        decoding="async"
                      />
                      <span class="zoom-icon">🔍</span>
                    </a>
                  </div>
                  <?php endforeach; ?>
                </div>

                <?php if ($total_gallery_images > 1): ?>
                  <div class="swiper-button-next"></div>
                  <div class="swiper-button-prev"></div>
                  <div class="swiper-pagination"></div>
                <?php endif; ?>
              </div>

              <?php if ($total_gallery_images > 1): ?>
                <div class="swiper product-gallery-thumbs">
                  <div class="swiper-wrapper">
                    <?php foreach ($all_gallery_images as $image):
                      if (empty($image['thumb'])) {
                        continue;
                      }
                      $thumb_alt = !empty($image['alt']) ? $image['alt'] : get_the_title();
                    ?>
                      <div class="swiper-slide">
                        <img
                          src="<?php echo esc_url($image['thumb']); ?>"
                          alt="<?php echo esc_attr($thumb_alt); ?>"
                          loading="lazy"
                        />
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="no-product-image">
              <img src="<?php echo esc_url(PUTRAFIBER_URI . '/assets/images/no-image.svg'); ?>" alt="No Image" width="400" height="300" />
            </div>
          <?php endif; ?>
        </div>
        <!-- ==================== /GALERI ==================== -->

        <!-- ==================== INFO PRODUK ==================== -->
        <div class="product-info">
          <div class="product-meta-header">
            <?php if ($categories): ?>
              <span class="product-category">
                <a href="<?php echo esc_url(get_term_link($categories[0])); ?>">
                  <?php echo esc_html($categories[0]->name); ?>
                </a>
              </span>
            <?php endif; ?>

            <?php if (!empty($sku)): ?>
              <span class="product-sku">SKU: <?php echo esc_html($sku); ?></span>
            <?php endif; ?>
          </div>

          <h1 class="product-title"><?php the_title(); ?></h1>

          <?php if (!empty($short_desc)): ?>
            <div class="product-short-description"><p><?php echo esc_html($short_desc); ?></p></div>
          <?php endif; ?>

          <div class="product-stock <?php echo esc_attr($stock_info['class']); ?>">
            <span class="stock-icon">
              <?php echo ($stock === 'ready' ? '✅' : ($stock === 'pre-order' ? '⏳' : '❌')); ?>
            </span>
            <?php echo esc_html($stock_info['text']); ?>
          </div>

          <?php if ($price_type === 'price'): ?>
            <?php if ($price_display > 0): ?>
              <div class="product-price">
                <span class="price-label">Harga:</span>
                <span class="price-amount"><?php echo esc_html($formatted_price); ?></span>
              </div>
            <?php else: ?>
              <div class="product-cta-price">
                <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya ingin tanya harga produk: ' . get_the_title())); ?>"
                   class="btn-whatsapp-cta" target="_blank" rel="nofollow noopener">
                  <?php echo function_exists('putrafiber_get_svg_icon') ? putrafiber_get_svg_icon('whatsapp') : '💬'; ?>
                  Tanya Harga
                </a>
                <p class="cta-note">Hubungi kami untuk info harga terbaik</p>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <div class="product-cta-price">
              <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya tertarik dengan produk: ' . get_the_title())); ?>"
                 class="btn-whatsapp-cta" target="_blank" rel="nofollow noopener">
                <?php echo function_exists('putrafiber_get_svg_icon') ? putrafiber_get_svg_icon('whatsapp') : '📞'; ?>
                Hubungi Kami
              </a>
              <p class="cta-note">Tanya harga & ketersediaan stok</p>
            </div>
          <?php endif; ?>

          <?php if ($sizes || $colors || $models || $material || $warranty): ?>
            <div class="product-attributes">
              <?php if ($sizes): ?>
                <div class="attribute-item"><strong>Ukuran:</strong> <span><?php echo esc_html($sizes); ?></span></div>
              <?php endif; ?>
              <?php if ($colors): ?>
                <div class="attribute-item"><strong>Warna:</strong> <span><?php echo esc_html($colors); ?></span></div>
              <?php endif; ?>
              <?php if ($models): ?>
                <div class="attribute-item"><strong>Model:</strong> <span><?php echo esc_html($models); ?></span></div>
              <?php endif; ?>
              <?php if ($material): ?>
                <div class="attribute-item"><strong>Material:</strong> <span><?php echo esc_html($material); ?></span></div>
              <?php endif; ?>
              <?php if ($warranty): ?>
                <div class="attribute-item"><strong>Garansi:</strong> <span><?php echo esc_html($warranty); ?></span></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="product-share">
            <strong>Bagikan:</strong>
            <div class="share-buttons">
              <?php $share_url = get_permalink(); $share_title = get_the_title(); ?>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>"
                 target="_blank" class="share-btn facebook" title="Share on Facebook" rel="noopener nofollow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode($share_title); ?>"
                 target="_blank" class="share-btn twitter" title="Share on X" rel="noopener nofollow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.60a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
              </a>
              <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($share_title . ' - ' . $share_url); ?>"
                 target="_blank" class="share-btn whatsapp" title="Share on WhatsApp" rel="noopener nofollow">
                 <?php echo function_exists('putrafiber_get_svg_icon') ? putrafiber_get_svg_icon('whatsapp') : '🟢'; ?>
              </a>
            </div>
          </div>

          <?php if (!empty($catalog_pdf)): ?>
            <div class="product-catalog">
              <a href="<?php echo esc_url($catalog_pdf); ?>" class="btn-download-catalog" target="_blank" rel="noopener" download>
                📄 Download Katalog PDF
              </a>
            </div>
          <?php endif; ?>
        </div>
        <!-- ================== /INFO PRODUK ================== -->

      </div>
    </div>
  </section>

  <?php if (get_the_content()): ?>
    <section class="product-description-section">
      <div class="container">
        <div class="section-header"><h2>Deskripsi Produk</h2></div>
        <div class="product-content"><?php the_content(); ?></div>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($specs || $features): ?>
    <section class="product-specs-section">
      <div class="container">
        <div class="specs-grid">
          <?php if ($specs): ?>
            <div class="specs-box">
              <h2>📋 Spesifikasi</h2>
              <ul>
                <?php foreach (explode("\n", $specs) as $line) {
                  $line = trim($line); if ($line) echo '<li>' . esc_html($line) . '</li>';
                } ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if ($features): ?>
            <div class="features-box">
              <h2>⭐ Fitur & Keunggulan</h2>
              <ul>
                <?php foreach (explode("\n", $features) as $line) {
                  $line = trim($line); if ($line) echo '<li>' . esc_html($line) . '</li>';
                } ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php
  if (function_exists('putrafiber_get_related_products')) {
    $related = putrafiber_get_related_products($product_id, 4);
    if ($related && $related->have_posts()): ?>
      <section class="related-products-section">
        <div class="container">
          <div class="section-header">
            <h2>Produk Terkait</h2>
            <p>Produk lain yang mungkin Anda sukai</p>
          </div>
          <div class="products-grid">
            <?php while ($related->have_posts()): $related->the_post();
              get_template_part('template-parts/content', 'product-card');
            endwhile; wp_reset_postdata(); ?>
          </div>
        </div>
      </section>
    <?php endif;
  } ?>

  <?php if (comments_open() || get_comments_number()): ?>
    <section class="product-reviews-section">
      <div class="container">
        <?php comments_template(); ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php
// Output Product Schema (opsional)
if (function_exists('putrafiber_generate_product_schema')) {
  echo putrafiber_generate_product_schema($product_id);
}
endwhile;

get_footer();
