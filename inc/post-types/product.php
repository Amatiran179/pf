<?php
/**
 * Product Custom Post Type (FIXED)
 *
 * @package PutraFiber
 * @version 1.3.1
 * - FIX: after_switch_theme rewrite flush
 * - ADD: admin enqueue jquery-ui-sortable + media untuk galeri
 * - CLEAN: sanitasi & guard data meta
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Product Post Type
 */
function putrafiber_register_product_post_type() {
  $labels = array(
    'name'               => _x('Produk', 'post type general name', 'putrafiber'),
    'singular_name'      => _x('Produk', 'post type singular name', 'putrafiber'),
    'menu_name'          => _x('Produk', 'admin menu', 'putrafiber'),
    'name_admin_bar'     => _x('Produk', 'add new on admin bar', 'putrafiber'),
    'add_new'            => _x('Tambah Produk', 'product', 'putrafiber'),
    'add_new_item'       => __('Tambah Produk Baru', 'putrafiber'),
    'new_item'           => __('Produk Baru', 'putrafiber'),
    'edit_item'          => __('Edit Produk', 'putrafiber'),
    'view_item'          => __('Lihat Produk', 'putrafiber'),
    'all_items'          => __('Semua Produk', 'putrafiber'),
    'search_items'       => __('Cari Produk', 'putrafiber'),
    'not_found'          => __('Produk tidak ditemukan.', 'putrafiber'),
    'not_found_in_trash' => __('Produk tidak ditemukan di Trash.', 'putrafiber'),
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'rewrite'            => array('slug' => 'produk'),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 6,
    'menu_icon'          => 'dashicons-cart',
    'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments'),
    'show_in_rest'       => true,
    'taxonomies'         => array('product_category', 'product_tag'),
  );

  register_post_type('product', $args);
}
add_action('init', 'putrafiber_register_product_post_type');

/**
 * Register Product Taxonomies
 */
function putrafiber_register_product_taxonomies() {
  $category_labels = array(
    'name'              => _x('Kategori Produk', 'taxonomy general name', 'putrafiber'),
    'singular_name'     => _x('Kategori Produk', 'taxonomy singular name', 'putrafiber'),
    'search_items'      => __('Cari Kategori', 'putrafiber'),
    'all_items'         => __('Semua Kategori', 'putrafiber'),
    'edit_item'         => __('Edit Kategori', 'putrafiber'),
    'update_item'       => __('Update Kategori', 'putrafiber'),
    'add_new_item'      => __('Tambah Kategori Baru', 'putrafiber'),
    'new_item_name'     => __('Nama Kategori Baru', 'putrafiber'),
    'menu_name'         => __('Kategori', 'putrafiber'),
  );
  $category_args = array(
    'hierarchical'      => true,
    'labels'            => $category_labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'rewrite'           => array('slug' => 'kategori-produk'),
    'show_in_rest'      => true,
  );
  register_taxonomy('product_category', array('product'), $category_args);

  $tag_labels = array(
    'name'              => _x('Tag Produk', 'taxonomy general name', 'putrafiber'),
    'singular_name'     => _x('Tag Produk', 'taxonomy singular name', 'putrafiber'),
    'search_items'      => __('Cari Tag', 'putrafiber'),
    'all_items'         => __('Semua Tag', 'putrafiber'),
    'edit_item'         => __('Edit Tag', 'putrafiber'),
    'update_item'       => __('Update Tag', 'putrafiber'),
    'add_new_item'      => __('Tambah Tag Baru', 'putrafiber'),
    'new_item_name'     => __('Nama Tag Baru', 'putrafiber'),
    'menu_name'         => __('Tag', 'putrafiber'),
  );
  $tag_args = array(
    'hierarchical'      => false,
    'labels'            => $tag_labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'rewrite'           => array('slug' => 'tag-produk'),
    'show_in_rest'      => true,
  );
  register_taxonomy('product_tag', array('product'), $tag_args);
}
add_action('init', 'putrafiber_register_product_taxonomies');

/**
 * Admin assets khusus editor Produk (galeri)
 */
function putrafiber_product_admin_assets($hook) {
  global $typenow;
  if ($typenow !== 'product') return;

  // Media uploader untuk PDF & gambar
  wp_enqueue_media();

  // Drag & drop urutan galeri
  wp_enqueue_script('jquery-ui-sortable');

  // Skrip admin terpadu (galeri, PDF uploader, dsb.)
  wp_enqueue_script(
    'putrafiber-admin',
    PUTRAFIBER_URI . '/assets/js/admin.js',
    array('jquery', 'jquery-ui-sortable'),
    pf_asset_version('assets/js/admin.js'),
    true
  );

}
add_action('admin_enqueue_scripts', 'putrafiber_product_admin_assets');

/**
 * Add Product Meta Boxes
 */
function putrafiber_product_meta_boxes() {
  add_meta_box(
    'putrafiber_product_details',
    __('Detail Produk', 'putrafiber'),
    'putrafiber_product_details_callback',
    'product',
    'normal',
    'high'
  );

  add_meta_box(
    'putrafiber_product_gallery',
    __('Galeri Produk', 'putrafiber'),
    'putrafiber_product_gallery_callback',
    'product',
    'side',
    'default'
  );

  add_meta_box(
    'putrafiber_schema_options',
    __('Schema SEO Options', 'putrafiber'),
    'putrafiber_schema_metabox_callback',
    'product',
    'normal',
    'default'
  );
}
add_action('add_meta_boxes', 'putrafiber_product_meta_boxes');

/**
 * Product Details Meta Box Callback
 */
function putrafiber_product_details_callback($post) {
  wp_nonce_field('putrafiber_product_nonce', 'putrafiber_product_nonce_field');
  wp_nonce_field('pf_save_meta', 'pf_meta_nonce');

  $price       = get_post_meta($post->ID, '_product_price', true);
  $price_type  = get_post_meta($post->ID, '_product_price_type', true) ?: 'price';
  $stock       = get_post_meta($post->ID, '_product_stock', true) ?: 'ready';
  $sku         = get_post_meta($post->ID, '_product_sku', true) ?: 'PF-' . $post->ID;
  $sizes       = get_post_meta($post->ID, '_product_sizes', true);
  $colors      = get_post_meta($post->ID, '_product_colors', true);
  $models      = get_post_meta($post->ID, '_product_models', true);
  $material    = get_post_meta($post->ID, '_product_material', true);
  $warranty    = get_post_meta($post->ID, '_product_warranty', true);
  $catalog_pdf = get_post_meta($post->ID, '_product_catalog_pdf', true);
  $short_desc  = get_post_meta($post->ID, '_product_short_description', true);
  $specs       = get_post_meta($post->ID, '_product_specifications', true);
  $features    = get_post_meta($post->ID, '_product_features', true);
  ?>
  <style>
    .product-meta-table{width:100%;border-collapse:collapse}
    .product-meta-table th{width:200px;text-align:left;padding:15px 10px;font-weight:600;vertical-align:top}
    .product-meta-table td{padding:15px 10px}
    .product-meta-table tr{border-bottom:1px solid #e0e0e0}
    .product-meta-input{width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:4px}
    .product-meta-textarea{width:100%;min-height:100px;padding:8px 12px;border:1px solid #ddd;border-radius:4px}
    .price-type-toggle{display:flex;gap:20px;margin-bottom:15px}
    .price-type-toggle label{display:flex;align-items:center;gap:8px;cursor:pointer}
    .price-input-wrapper{display:none}
    .price-input-wrapper.active{display:block}
    .help-text{font-size:12px;color:#666;margin-top:5px;font-style:italic}
  </style>

  <table class="product-meta-table">
    <tr>
      <th><label for="product_short_description"><?php _e('Deskripsi Singkat', 'putrafiber'); ?></label></th>
      <td>
        <textarea id="product_short_description" name="product_short_description" class="product-meta-textarea" rows="3" maxlength="200"><?php echo esc_textarea($short_desc); ?></textarea>
        <p class="help-text"><?php _e('Deskripsi singkat yang tampil di listing produk (max 200 karakter)', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_sku"><?php _e('SKU Produk', 'putrafiber'); ?></label></th>
      <td>
        <input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr($sku); ?>" class="product-meta-input">
        <p class="help-text"><?php _e('Kode unik produk (Stock Keeping Unit)', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label><?php _e('Tipe Harga', 'putrafiber'); ?></label></th>
      <td>
        <div class="price-type-toggle">
          <label><input type="radio" name="product_price_type" value="price" <?php checked($price_type, 'price'); ?>> <strong><?php _e('Tampilkan Harga', 'putrafiber'); ?></strong></label>
          <label><input type="radio" name="product_price_type" value="whatsapp" <?php checked($price_type, 'whatsapp'); ?>> <strong><?php _e('CTA WhatsApp (Hubungi Kami)', 'putrafiber'); ?></strong></label>
        </div>
        <div class="price-input-wrapper <?php echo ($price_type === 'price') ? 'active' : ''; ?>" id="price-input-box">
          <label for="product_price"><?php _e('Harga Produk (Rp)', 'putrafiber'); ?></label>
          <input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($price); ?>" class="product-meta-input" placeholder="1000" min="0" step="1000">
          <p class="help-text"><?php _e('⚠️ Kosongkan atau isi 0 akan otomatis jadi Rp.1.000 untuk schema (anti Google penalty)', 'putrafiber'); ?></p>
        </div>
        <div class="price-input-wrapper <?php echo ($price_type === 'whatsapp') ? 'active' : ''; ?>" id="whatsapp-cta-box">
          <p style="color:#00BCD4;font-weight:600;">✅ <?php _e('Tombol "Hubungi Kami" akan tampil menggantikan harga', 'putrafiber'); ?></p>
          <p class="help-text"><?php _e('Harga tetap Rp.1.000 di schema untuk SEO', 'putrafiber'); ?></p>
        </div>
      </td>
    </tr>
    <tr>
      <th><label for="product_stock"><?php _e('Status Stok', 'putrafiber'); ?></label></th>
      <td>
        <select id="product_stock" name="product_stock" class="product-meta-input">
          <option value="ready"       <?php selected($stock, 'ready'); ?>><?php _e('Ready Stock', 'putrafiber'); ?></option>
          <option value="pre-order"   <?php selected($stock, 'pre-order'); ?>><?php _e('Pre-Order', 'putrafiber'); ?></option>
          <option value="out-of-stock"<?php selected($stock, 'out-of-stock'); ?>><?php _e('Stok Habis', 'putrafiber'); ?></option>
        </select>
      </td>
    </tr>
    <tr>
      <th><label for="product_sizes"><?php _e('Ukuran Tersedia', 'putrafiber'); ?></label></th>
      <td>
        <input type="text" id="product_sizes" name="product_sizes" value="<?php echo esc_attr($sizes); ?>" class="product-meta-input" placeholder="Contoh: S, M, L, XL atau 100x50cm, 200x100cm">
        <p class="help-text"><?php _e('Pisahkan dengan koma. Contoh: S, M, L atau custom ukuran', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_colors"><?php _e('Warna Tersedia', 'putrafiber'); ?></label></th>
      <td>
        <input type="text" id="product_colors" name="product_colors" value="<?php echo esc_attr($colors); ?>" class="product-meta-input" placeholder="Contoh: Biru, Merah, Hijau, Kuning">
        <p class="help-text"><?php _e('Pisahkan dengan koma', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_models"><?php _e('Model/Varian', 'putrafiber'); ?></label></th>
      <td>
        <input type="text" id="product_models" name="product_models" value="<?php echo esc_attr($models); ?>" class="product-meta-input" placeholder="Contoh: Model A, Model B, Model Custom">
        <p class="help-text"><?php _e('Pisahkan dengan koma', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_material"><?php _e('Material/Bahan', 'putrafiber'); ?></label></th>
      <td>
        <input type="text" id="product_material" name="product_material" value="<?php echo esc_attr($material); ?>" class="product-meta-input" placeholder="Contoh: Fiberglass Premium Grade A">
        <p class="help-text"><?php _e('Bahan utama pembuatan produk', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_warranty"><?php _e('Garansi', 'putrafiber'); ?></label></th>
      <td>
        <input type="text" id="product_warranty" name="product_warranty" value="<?php echo esc_attr($warranty); ?>" class="product-meta-input" placeholder="Contoh: 1 Tahun Garansi Resmi">
        <p class="help-text"><?php _e('Info garansi produk', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_specifications"><?php _e('Spesifikasi Detail', 'putrafiber'); ?></label></th>
      <td>
        <textarea id="product_specifications" name="product_specifications" class="product-meta-textarea" placeholder="Contoh:&#10;• Dimensi: 100x50x30cm&#10;• Berat: 5kg&#10;• Material: Fiberglass Premium"><?php echo esc_textarea($specs); ?></textarea>
        <p class="help-text"><?php _e('Detail spesifikasi teknis (satu baris per spesifikasi)', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_features"><?php _e('Fitur & Keunggulan', 'putrafiber'); ?></label></th>
      <td>
        <textarea id="product_features" name="product_features" class="product-meta-textarea" placeholder="Contoh:&#10;• Tahan cuaca ekstrem&#10;• Anti karat&#10;• Mudah dibersihkan"><?php echo esc_textarea($features); ?></textarea>
        <p class="help-text"><?php _e('Fitur dan keunggulan produk (satu baris per fitur)', 'putrafiber'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="product_catalog_pdf"><?php _e('Katalog PDF (Optional)', 'putrafiber'); ?></label></th>
      <td>
        <input type="text" id="product_catalog_pdf" name="product_catalog_pdf" value="<?php echo esc_url($catalog_pdf); ?>" class="product-meta-input" placeholder="https://example.com/catalog.pdf">
        <button type="button" class="button upload-pdf-button"><?php _e('Upload PDF', 'putrafiber'); ?></button>
        <p class="help-text"><?php _e('Link download katalog PDF produk', 'putrafiber'); ?></p>
      </td>
    </tr>
  </table>

  <script>
  jQuery(function($){
    // Toggle tipe harga
    $('input[name="product_price_type"]').on('change', function(){
      $('.price-input-wrapper').removeClass('active');
      if ($(this).val() === 'price') { $('#price-input-box').addClass('active'); }
      else { $('#whatsapp-cta-box').addClass('active'); }
    });

    // Upload PDF
    $('.upload-pdf-button').on('click', function(e){
      e.preventDefault();
      var uploader = wp.media({
        title: 'Select PDF',
        button: { text: 'Use this PDF' },
        multiple: false,
        library: { type: 'application/pdf' }
      }).on('select', function(){
        $('#product_catalog_pdf').val(uploader.state().get('selection').first().toJSON().url);
      }).open();
    });
  });
  </script>
  <?php
}

/**
 * Product Gallery Meta Box Callback - FIXED VERSION
 */
function putrafiber_product_gallery_callback($post) {
  wp_nonce_field('pf_save_meta', 'pf_meta_nonce');
  $gallery_raw   = get_post_meta($post->ID, '_product_gallery', true);
  $gallery_ids   = function_exists('putrafiber_extract_gallery_ids') ? putrafiber_extract_gallery_ids($gallery_raw) : array();
  $gallery_value = !empty($gallery_ids) ? implode(',', $gallery_ids) : '';
  ?>
  <div class="product-gallery-box">
    <input type="hidden" id="product_gallery" name="product_gallery" value="<?php echo esc_attr($gallery_value); ?>">
    <button type="button" class="button button-primary button-large" id="upload-gallery-button" style="width:100%;margin-bottom:15px;">
      <span class="dashicons dashicons-images-alt2" style="margin-top:3px;"></span> <?php _e('Upload Gambar Gallery', 'putrafiber'); ?>
    </button>
    <div id="product-gallery-preview" class="gallery-preview-grid">
      <?php
      if (!empty($gallery_ids)) {
        foreach ($gallery_ids as $img_id) {
          $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
          if ($img_url) {
            echo '<div class="gallery-item" data-id="'.esc_attr($img_id).'"><img src="'.esc_url($img_url).'" alt="Gallery image"><button type="button" class="remove-gallery-item" title="Hapus">&times;</button></div>';
          }
        }
      }
      ?>
    </div>
  </div>
  <?php
}

/**
 * Save Product Meta Data
 */
function putrafiber_save_product_meta($post_id) {
  if (!isset($_POST['pf_meta_nonce']) || !wp_verify_nonce($_POST['pf_meta_nonce'], 'pf_save_meta')) return;
  if (!current_user_can('edit_post', $post_id)) return;
  if (!isset($_POST['putrafiber_product_nonce_field']) || !wp_verify_nonce($_POST['putrafiber_product_nonce_field'], 'putrafiber_product_nonce')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (get_post_type($post_id) !== 'product') return;

  $field_mapping = array(
    'product_short_description' => '_product_short_description',
    'product_sku'               => '_product_sku',
    'product_price_type'        => '_product_price_type',
    'product_price'             => '_product_price',
    'product_stock'             => '_product_stock',
    'product_sizes'             => '_product_sizes',
    'product_colors'            => '_product_colors',
    'product_models'            => '_product_models',
    'product_material'          => '_product_material',
    'product_warranty'          => '_product_warranty',
    'product_catalog_pdf'       => '_product_catalog_pdf',
    'product_specifications'    => '_product_specifications',
    'product_features'          => '_product_features',
    'product_gallery'           => '_product_gallery',
  );

  foreach ($field_mapping as $post_field => $meta_key) {
    if (!isset($_POST[$post_field])) continue;

    $raw_value = $_POST[$post_field];
    $value     = '';

    if (in_array($post_field, array('product_short_description','product_specifications','product_features'), true)) {
      $value = pf_clean_html($raw_value);
    } elseif ($post_field === 'product_catalog_pdf') {
      $value = pf_clean_url($raw_value);
    } elseif ($post_field === 'product_price') {
      // Simpan harga asli, biarkan schema/display logic yang handle fallback
      $cleaned_price = pf_clean_float($raw_value);
      // Pastikan nilai yang disimpan tidak pernah negatif.
      $value = max(0.0, $cleaned_price);
    } elseif ($post_field === 'product_gallery') {
      $gallery_raw = pf_clean_text($raw_value);
      $value = function_exists('putrafiber_prepare_gallery_meta_value')
        ? putrafiber_prepare_gallery_meta_value($gallery_raw)
        : implode(',', array_filter(array_map('absint', explode(',', $gallery_raw))));
    } else {
      $value = pf_clean_text($raw_value);
    }

    update_post_meta($post_id, $meta_key, $value);
  }
}
add_action('save_post_product', 'putrafiber_save_product_meta');

/**
 * Get Related Products
 */
function putrafiber_get_related_products($product_id, $limit = 4) {
  $categories = wp_get_post_terms($product_id, 'product_category', array('fields' => 'ids'));
  $args = array(
    'post_type'      => 'product',
    'posts_per_page' => $limit,
    'post__not_in'   => array($product_id),
    'orderby'        => 'modified',
    'post_status'    => 'publish',
  );
  if (!empty($categories) && !is_wp_error($categories)) {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'product_category',
        'field'    => 'term_id',
        'terms'    => $categories,
      ),
    );
  }
  return new WP_Query($args);
}

/**
 * Flush rewrite rules on theme switch (bukan register_activation_hook)
 */
function putrafiber_flush_rewrite_rules_on_switch() {
  putrafiber_register_product_post_type();
  putrafiber_register_product_taxonomies();
  flush_rewrite_rules();
}
add_action('after_switch_theme', 'putrafiber_flush_rewrite_rules_on_switch');
