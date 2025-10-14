<?php
/**
 * Product Custom Post Type
 * 
 * @package PutraFiber
 * @since 1.0.0
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
        'parent_item_colon'  => __('Parent Produk:', 'putrafiber'),
        'not_found'          => __('Produk tidak ditemukan.', 'putrafiber'),
        'not_found_in_trash' => __('Produk tidak ditemukan di Trash.', 'putrafiber')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Produk Fiberglass PutraFiber', 'putrafiber'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'produk'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-products',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
        'show_in_rest'       => true,
    );

    register_post_type('product', $args);
}
add_action('init', 'putrafiber_register_product_post_type');

/**
 * Register Product Taxonomies
 */
function putrafiber_register_product_taxonomies() {
    // Product Category
    $category_labels = array(
        'name'              => _x('Kategori Produk', 'taxonomy general name', 'putrafiber'),
        'singular_name'     => _x('Kategori Produk', 'taxonomy singular name', 'putrafiber'),
        'search_items'      => __('Cari Kategori', 'putrafiber'),
        'all_items'         => __('Semua Kategori', 'putrafiber'),
        'parent_item'       => __('Parent Kategori', 'putrafiber'),
        'parent_item_colon' => __('Parent Kategori:', 'putrafiber'),
        'edit_item'         => __('Edit Kategori', 'putrafiber'),
        'update_item'       => __('Update Kategori', 'putrafiber'),
        'add_new_item'      => __('Tambah Kategori Baru', 'putrafiber'),
        'new_item_name'     => __('Nama Kategori Baru', 'putrafiber'),
        'menu_name'         => __('Kategori', 'putrafiber'),
    );

    register_taxonomy('product_category', array('product'), array(
        'hierarchical'      => true,
        'labels'            => $category_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'kategori-produk'),
        'show_in_rest'      => true,
    ));
    
    // Product Tag
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

    register_taxonomy('product_tag', array('product'), array(
        'hierarchical'      => false,
        'labels'            => $tag_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'tag-produk'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'putrafiber_register_product_taxonomies');

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
        __('Galeri Produk (Auto Slider)', 'putrafiber'),
        'putrafiber_product_gallery_callback',
        'product',
        'side',
        'default'
    );
    
    add_meta_box(
        'putrafiber_product_seo',
        __('SEO & Schema Settings', 'putrafiber'),
        'putrafiber_product_seo_callback',
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
    
    // Get saved values
    $price = get_post_meta($post->ID, '_product_price', true);
    $price_type = get_post_meta($post->ID, '_product_price_type', true) ?: 'price';
    $stock = get_post_meta($post->ID, '_product_stock', true) ?: 'ready';
    $sku = get_post_meta($post->ID, '_product_sku', true) ?: 'PF-' . $post->ID;
    $sizes = get_post_meta($post->ID, '_product_sizes', true);
    $colors = get_post_meta($post->ID, '_product_colors', true);
    $models = get_post_meta($post->ID, '_product_models', true);
    $material = get_post_meta($post->ID, '_product_material', true);
    $warranty = get_post_meta($post->ID, '_product_warranty', true);
    $catalog_pdf = get_post_meta($post->ID, '_product_catalog_pdf', true);
    $short_desc = get_post_meta($post->ID, '_product_short_description', true);
    $specs = get_post_meta($post->ID, '_product_specifications', true);
    $features = get_post_meta($post->ID, '_product_features', true);
    ?>
    
    <style>
    .product-meta-table { width: 100%; border-collapse: collapse; }
    .product-meta-table th { width: 200px; text-align: left; padding: 15px 10px; font-weight: 600; vertical-align: top; }
    .product-meta-table td { padding: 15px 10px; }
    .product-meta-table tr { border-bottom: 1px solid #e0e0e0; }
    .product-meta-input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
    .product-meta-textarea { width: 100%; min-height: 100px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
    .price-type-toggle { display: flex; gap: 20px; margin-bottom: 15px; }
    .price-type-toggle label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .price-input-wrapper { display: none; }
    .price-input-wrapper.active { display: block; }
    .help-text { font-size: 12px; color: #666; margin-top: 5px; font-style: italic; }
    .stock-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .stock-ready { background: #4CAF50; color: white; }
    .stock-preorder { background: #FFC107; color: #333; }
    </style>
    
    <table class="product-meta-table">
        <tr>
            <th><label for="product_short_description"><?php _e('Deskripsi Singkat', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="product_short_description" name="product_short_description" class="product-meta-textarea" rows="3"><?php echo esc_textarea($short_desc); ?></textarea>
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
                    <label>
                        <input type="radio" name="product_price_type" value="price" <?php checked($price_type, 'price'); ?>>
                        <strong><?php _e('Tampilkan Harga', 'putrafiber'); ?></strong>
                    </label>
                    <label>
                        <input type="radio" name="product_price_type" value="whatsapp" <?php checked($price_type, 'whatsapp'); ?>>
                        <strong><?php _e('CTA WhatsApp (Hubungi Kami)', 'putrafiber'); ?></strong>
                    </label>
                </div>
                
                <div class="price-input-wrapper <?php echo ($price_type === 'price') ? 'active' : ''; ?>" id="price-input-box">
                    <label for="product_price"><?php _e('Harga Produk (Rp)', 'putrafiber'); ?></label>
                    <input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($price); ?>" class="product-meta-input" placeholder="1000">
                    <p class="help-text">
                        <?php _e('⚠️ Kosongkan atau isi 0 akan otomatis jadi Rp.1.000 untuk schema (anti Google penalty)', 'putrafiber'); ?>
                    </p>
                </div>
                
                <div class="price-input-wrapper <?php echo ($price_type === 'whatsapp') ? 'active' : ''; ?>" id="whatsapp-cta-box">
                    <p style="color: #00BCD4; font-weight: 600;">
                        ✅ <?php _e('Tombol "Hubungi Kami" akan tampil menggantikan harga', 'putrafiber'); ?>
                    </p>
                    <p class="help-text"><?php _e('Harga tetap Rp.1.000 di schema untuk SEO', 'putrafiber'); ?></p>
                </div>
            </td>
        </tr>
        
        <tr>
            <th><label for="product_stock"><?php _e('Status Stok', 'putrafiber'); ?></label></th>
            <td>
                <select id="product_stock" name="product_stock" class="product-meta-input">
                    <option value="ready" <?php selected($stock, 'ready'); ?>><?php _e('Ready Stock', 'putrafiber'); ?></option>
                    <option value="pre-order" <?php selected($stock, 'pre-order'); ?>><?php _e('Pre-Order', 'putrafiber'); ?></option>
                    <option value="out-of-stock" <?php selected($stock, 'out-of-stock'); ?>><?php _e('Stok Habis', 'putrafiber'); ?></option>
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
                <textarea id="product_specifications" name="product_specifications" class="product-meta-textarea"><?php echo esc_textarea($specs); ?></textarea>
                <p class="help-text"><?php _e('Detail spesifikasi teknis (satu baris per spesifikasi)', 'putrafiber'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th><label for="product_features"><?php _e('Fitur & Keunggulan', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="product_features" name="product_features" class="product-meta-textarea"><?php echo esc_textarea($features); ?></textarea>
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
    jQuery(document).ready(function($) {
        // Toggle price type
        $('input[name="product_price_type"]').on('change', function() {
            var type = $(this).val();
            $('.price-input-wrapper').removeClass('active');
            if (type === 'price') {
                $('#price-input-box').addClass('active');
            } else {
                $('#whatsapp-cta-box').addClass('active');
            }
        });
        
        // Upload PDF
        $('.upload-pdf-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var custom_uploader = wp.media({
                title: 'Select PDF Catalog',
                button: { text: 'Use this file' },
                multiple: false,
                library: { type: 'application/pdf' }
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#product_catalog_pdf').val(attachment.url);
            }).open();
        });
    });
    </script>
    <?php
}

/**
 * Product Gallery Meta Box Callback
 */
function putrafiber_product_gallery_callback($post) {
    $gallery = get_post_meta($post->ID, '_product_gallery', true);
    ?>
    <div class="product-gallery-box">
        <input type="hidden" id="product_gallery" name="product_gallery" value="<?php echo esc_attr($gallery); ?>">
        
        <button type="button" class="button button-primary button-large" id="upload-gallery-button" style="width: 100%; margin-bottom: 15px;">
            <span class="dashicons dashicons-images-alt2" style="margin-top: 3px;"></span>
            <?php _e('Upload Gambar Gallery', 'putrafiber'); ?>
        </button>
        
        <p style="font-size: 12px; color: #666; margin-bottom: 15px;">
            <?php _e('📸 Gambar akan tampil auto slider 160x160px di halaman produk', 'putrafiber'); ?>
        </p>
        
        <div id="gallery-preview" class="gallery-preview-grid">
            <?php
            if ($gallery) {
                $gallery_ids = explode(',', $gallery);
                foreach ($gallery_ids as $img_id) {
                    $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                    if ($img_url) {
                        echo '<div class="gallery-item" data-id="' . esc_attr($img_id) . '">';
                        echo '<img src="' . esc_url($img_url) . '">';
                        echo '<button type="button" class="remove-gallery-item" title="Hapus">&times;</button>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    
    <style>
    .gallery-preview-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 15px;
    }
    
    .gallery-item {
        position: relative;
        border: 2px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1;
    }
    
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .gallery-item .remove-gallery-item {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 24px;
        height: 24px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
        display: none;
    }
    
    .gallery-item:hover .remove-gallery-item {
        display: block;
    }
    
    .gallery-item.sortable-ghost {
        opacity: 0.4;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        var galleryUploader;
        
        $('#upload-gallery-button').on('click', function(e) {
            e.preventDefault();
            
            if (galleryUploader) {
                galleryUploader.open();
                return;
            }
            
            galleryUploader = wp.media({
                title: 'Select Gallery Images',
                button: { text: 'Add to Gallery' },
                multiple: true
            });
            
            galleryUploader.on('select', function() {
                var attachments = galleryUploader.state().get('selection').toJSON();
                var existingIds = $('#product_gallery').val() ? $('#product_gallery').val().split(',') : [];
                var html = '';
                
                $.each(attachments, function(index, attachment) {
                    if ($.inArray(attachment.id.toString(), existingIds) === -1) {
                        existingIds.push(attachment.id);
                        html += '<div class="gallery-item" data-id="' + attachment.id + '">';
                        html += '<img src="' + attachment.sizes.thumbnail.url + '">';
                        html += '<button type="button" class="remove-gallery-item" title="Hapus">&times;</button>';
                        html += '</div>';
                    }
                });
                
                $('#gallery-preview').append(html);
                $('#product_gallery').val(existingIds.join(','));
            });
            
            galleryUploader.open();
        });
        
        // Remove gallery item
        $(document).on('click', '.remove-gallery-item', function() {
            var item = $(this).closest('.gallery-item');
            var id = item.data('id');
            var ids = $('#product_gallery').val().split(',');
            
            ids = ids.filter(function(value) {
                return value != id;
            });
            
            $('#product_gallery').val(ids.join(','));
            item.remove();
        });
        
        // Sortable gallery (optional - requires jQuery UI)
        if ($.fn.sortable) {
            $('#gallery-preview').sortable({
                update: function() {
                    var ids = [];
                    $('.gallery-item').each(function() {
                        ids.push($(this).data('id'));
                    });
                    $('#product_gallery').val(ids.join(','));
                }
            });
        }
    });
    </script>
    <?php
}

/**
 * Product SEO Meta Box Callback
 */
function putrafiber_product_seo_callback($post) {
    $meta_title = get_post_meta($post->ID, '_meta_title', true);
    $meta_desc = get_post_meta($post->ID, '_meta_description', true);
    $focus_keyword = get_post_meta($post->ID, '_focus_keyword', true);
    $enable_video_schema = get_post_meta($post->ID, '_enable_video_schema', true);
    $video_url = get_post_meta($post->ID, '_video_url', true);
    $video_duration = get_post_meta($post->ID, '_video_duration', true);
    $enable_faq_schema = get_post_meta($post->ID, '_enable_faq_schema', true);
    $faq_items = get_post_meta($post->ID, '_faq_items', true);
    $enable_howto_schema = get_post_meta($post->ID, '_enable_howto_schema', true);
    $howto_steps = get_post_meta($post->ID, '_howto_steps', true);
    ?>
    
    <table class="product-meta-table">
        <tr>
            <th><label for="meta_title"><?php _e('Meta Title', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="meta_title" name="meta_title" value="<?php echo esc_attr($meta_title); ?>" class="product-meta-input">
                <p class="help-text">
                    <?php _e('Panjang:', 'putrafiber'); ?> <span id="title-length">0</span> <?php _e('karakter (Rekomendasi: 50-60)', 'putrafiber'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <th><label for="meta_description"><?php _e('Meta Description', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="meta_description" name="meta_description" rows="3" class="product-meta-textarea"><?php echo esc_textarea($meta_desc); ?></textarea>
                <p class="help-text">
                    <?php _e('Panjang:', 'putrafiber'); ?> <span id="desc-length">0</span> <?php _e('karakter (Rekomendasi: 150-160)', 'putrafiber'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <th><label for="focus_keyword"><?php _e('Focus Keyword', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="focus_keyword" name="focus_keyword" value="<?php echo esc_attr($focus_keyword); ?>" class="product-meta-input" placeholder="Contoh: perosotan fiberglass">
                <p class="help-text"><?php _e('Keyword utama untuk SEO produk ini', 'putrafiber'); ?></p>
            </td>
        </tr>
        
        <tr style="background: #f5f5f5;">
            <td colspan="2" style="padding: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #00BCD4;">
                    <span class="dashicons dashicons-video-alt3"></span>
                    <?php _e('Schema Video (Optional)', 'putrafiber'); ?>
                </h3>
            </td>
        </tr>
        
        <tr>
            <th>
                <label for="enable_video_schema">
                    <input type="checkbox" id="enable_video_schema" name="enable_video_schema" value="1" <?php checked($enable_video_schema, '1'); ?>>
                    <?php _e('Enable Video Schema', 'putrafiber'); ?>
                </label>
            </th>
            <td>
                <p class="help-text"><?php _e('Centang untuk menambahkan VideoObject schema', 'putrafiber'); ?></p>
            </td>
        </tr>
        
        <tr class="video-schema-field" style="display: <?php echo $enable_video_schema ? 'table-row' : 'none'; ?>;">
            <th><label for="video_url"><?php _e('Video URL', 