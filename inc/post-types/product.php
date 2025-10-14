<?php
/**
 * Product Custom Post Type
 * 
 * @package PutraFiber
 * @since 1.0.0
 * @version 1.1.0 - FIXED VERSION
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
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments'),
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
        __('Galeri Produk (Auto Slider 160x160)', 'putrafiber'),
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
    // PENTING: Nonce field untuk security
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
                    <input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($price); ?>" class="product-meta-input" placeholder="0" min="0" step="1">
                    <p class="help-text">
                        <?php _e('💡 Kosongkan atau isi 0 akan otomatis jadi Rp.1.000 untuk schema (anti Google penalty)', 'putrafiber'); ?>
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
            var custom_uploader = wp.media({
                title: '<?php _e('Select PDF Catalog', 'putrafiber'); ?>',
                button: { text: '<?php _e('Use this file', 'putrafiber'); ?>' },
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
            <?php _e('📸 Gambar akan tampil auto slider 160x160px dengan zoom hover + lightbox popup', 'putrafiber'); ?>
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
        cursor: move;
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
                title: '<?php _e('Select Gallery Images', 'putrafiber'); ?>',
                button: { text: '<?php _e('Add to Gallery', 'putrafiber'); ?>' },
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
    $video_title = get_post_meta($post->ID, '_video_title', true);
    $video_description = get_post_meta($post->ID, '_video_description', true);
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
            <th><label for="video_url"><?php _e('Video URL', 'putrafiber'); ?></label></th>
            <td>
                <input type="url" id="video_url" name="video_url" value="<?php echo esc_url($video_url); ?>" class="product-meta-input" placeholder="https://www.youtube.com/watch?v=xxxxx">
                <p class="help-text"><?php _e('URL video YouTube atau Vimeo', 'putrafiber'); ?></p>
            </td>
        </tr>
        
        <tr class="video-schema-field" style="display: <?php echo $enable_video_schema ? 'table-row' : 'none'; ?>;">
            <th><label for="video_title"><?php _e('Judul Video', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="video_title" name="video_title" value="<?php echo esc_attr($video_title); ?>" class="product-meta-input">
            </td>
        </tr>
        
        <tr class="video-schema-field" style="display: <?php echo $enable_video_schema ? 'table-row' : 'none'; ?>;">
            <th><label for="video_description"><?php _e('Deskripsi Video', 'putrafiber'); ?></label></th>
            <td>
                <textarea id="video_description" name="video_description" rows="3" class="product-meta-textarea"><?php echo esc_textarea($video_description); ?></textarea>
            </td>
        </tr>
        
        <tr class="video-schema-field" style="display: <?php echo $enable_video_schema ? 'table-row' : 'none'; ?>;">
            <th><label for="video_duration"><?php _e('Durasi Video', 'putrafiber'); ?></label></th>
            <td>
                <input type="text" id="video_duration" name="video_duration" value="<?php echo esc_attr($video_duration); ?>" class="product-meta-input" placeholder="PT5M30S (5 menit 30 detik)">
                <p class="help-text"><?php _e('Format ISO 8601 Duration. Contoh: PT5M30S untuk 5 menit 30 detik', 'putrafiber'); ?></p>
            </td>
        </tr>
        
        <tr style="background: #f5f5f5;">
            <td colspan="2" style="padding: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #FF9800;">
                    <span class="dashicons dashicons-format-chat"></span>
                    <?php _e('Schema FAQ (Optional)', 'putrafiber'); ?>
                </h3>
            </td>
        </tr>
        
        <tr>
            <th>
                <label for="enable_faq_schema">
                    <input type="checkbox" id="enable_faq_schema" name="enable_faq_schema" value="1" <?php checked($enable_faq_schema, '1'); ?>>
                    <?php _e('Enable FAQ Schema', 'putrafiber'); ?>
                </label>
            </th>
            <td>
                <p class="help-text"><?php _e('Centang untuk menambahkan FAQPage schema', 'putrafiber'); ?></p>
            </td>
        </tr>
        
        <tr class="faq-schema-field" style="display: <?php echo $enable_faq_schema ? 'table-row' : 'none'; ?>;">
            <td colspan="2">
                <div id="faq-items-container">
                    <?php
                    if (is_array($faq_items) && !empty($faq_items)) {
                        foreach ($faq_items as $index => $item) {
                            ?>
                            <div class="faq-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <strong><?php printf(__('FAQ #%d', 'putrafiber'), $index + 1); ?></strong>
                                    <button type="button" class="button remove-faq-item"><?php _e('Hapus', 'putrafiber'); ?></button>
                                </div>
                                <p>
                                    <label><?php _e('Pertanyaan:', 'putrafiber'); ?></label><br>
                                    <input type="text" name="faq_items[<?php echo $index; ?>][question]" value="<?php echo esc_attr($item['question']); ?>" class="product-meta-input" placeholder="Pertanyaan FAQ">
                                </p>
                                <p>
                                    <label><?php _e('Jawaban:', 'putrafiber'); ?></label><br>
                                    <textarea name="faq_items[<?php echo $index; ?>][answer]" rows="3" class="product-meta-textarea" placeholder="Jawaban FAQ"><?php echo esc_textarea($item['answer']); ?></textarea>
                                </p>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <button type="button" class="button button-secondary" id="add-faq-item">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e('Tambah FAQ', 'putrafiber'); ?>
                </button>
            </td>
        </tr>
        
        <tr style="background: #f5f5f5;">
            <td colspan="2" style="padding: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #9C27B0;">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('Schema HowTo (Optional)', 'putrafiber'); ?>
                </h3>
            </td>
        </tr>
        
        <tr>
            <th>
                <label for="enable_howto_schema">
                    <input type="checkbox" id="enable_howto_schema" name="enable_howto_schema" value="1" <?php checked($enable_howto_schema, '1'); ?>>
                    <?php _e('Enable HowTo Schema', 'putrafiber'); ?>
                </label>
            </th>
            <td>
                <p class="help-text"><?php _e('Centang untuk menambahkan HowTo schema', 'putrafiber'); ?></p>
            </td>
        </tr>
        
        <tr class="howto-schema-field" style="display: <?php echo $enable_howto_schema ? 'table-row' : 'none'; ?>;">
            <td colspan="2">
                <div id="howto-steps-container">
                    <?php
                    if (is_array($howto_steps) && !empty($howto_steps)) {
                        foreach ($howto_steps as $index => $step) {
                            ?>
                            <div class="howto-step-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <strong><?php printf(__('Step #%d', 'putrafiber'), $index + 1); ?></strong>
                                    <button type="button" class="button remove-howto-step"><?php _e('Hapus', 'putrafiber'); ?></button>
                                </div>
                                <p>
                                    <label><?php _e('Nama Step:', 'putrafiber'); ?></label><br>
                                    <input type="text" name="howto_steps[<?php echo $index; ?>][name]" value="<?php echo esc_attr($step['name']); ?>" class="product-meta-input" placeholder="Nama langkah">
                                </p>
                                <p>
                                    <label><?php _e('Deskripsi:', 'putrafiber'); ?></label><br>
                                    <textarea name="howto_steps[<?php echo $index; ?>][text]" rows="3" class="product-meta-textarea" placeholder="Deskripsi langkah"><?php echo esc_textarea($step['text']); ?></textarea>
                                </p>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <button type="button" class="button button-secondary" id="add-howto-step">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e('Tambah Step', 'putrafiber'); ?>
                </button>
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        // Character counter
        function updateCharCount() {
            $('#title-length').text($('#meta_title').val().length);
            $('#desc-length').text($('#meta_description').val().length);
        }
        
        $('#meta_title, #meta_description').on('input', updateCharCount);
        updateCharCount();
        
        // Toggle video schema fields
        $('#enable_video_schema').on('change', function() {
            $('.video-schema-field').toggle(this.checked);
        });
        
        // Toggle FAQ schema fields
        $('#enable_faq_schema').on('change', function() {
            $('.faq-schema-field').toggle(this.checked);
        });
        
        // Toggle HowTo schema fields
        $('#enable_howto_schema').on('change', function() {
            $('.howto-schema-field').toggle(this.checked);
        });
        
        // Add FAQ item
        var faqIndex = $('.faq-item').length;
        $('#add-faq-item').on('click', function() {
            var html = '<div class="faq-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 4px;">' +
                '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">' +
                '<strong>FAQ #' + (faqIndex + 1) + '</strong>' +
                '<button type="button" class="button remove-faq-item">Hapus</button>' +
                '</div>' +
                '<p><label>Pertanyaan:</label><br>' +
                '<input type="text" name="faq_items[' + faqIndex + '][question]" class="product-meta-input" placeholder="Pertanyaan FAQ"></p>' +
                '<p><label>Jawaban:</label><br>' +
                '<textarea name="faq_items[' + faqIndex + '][answer]" rows="3" class="product-meta-textarea" placeholder="Jawaban FAQ"></textarea></p>' +
                '</div>';
            $('#faq-items-container').append(html);
            faqIndex++;
        });
        
        // Remove FAQ item
        $(document).on('click', '.remove-faq-item', function() {
            $(this).closest('.faq-item').remove();
        });
        
        // Add HowTo step
        var howtoIndex = $('.howto-step-item').length;
        $('#add-howto-step').on('click', function() {
            var html = '<div class="howto-step-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 4px;">' +
                '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">' +
                '<strong>Step #' + (howtoIndex + 1) + '</strong>' +
                '<button type="button" class="button remove-howto-step">Hapus</button>' +
                '</div>' +
                '<p><label>Nama Step:</label><br>' +
                '<input type="text" name="howto_steps[' + howtoIndex + '][name]" class="product-meta-input" placeholder="Nama langkah"></p>' +
                '<p><label>Deskripsi:</label><br>' +
                '<textarea name="howto_steps[' + howtoIndex + '][text]" rows="3" class="product-meta-textarea" placeholder="Deskripsi langkah"></textarea></p>' +
                '</div>';
            $('#howto-steps-container').append(html);
            howtoIndex++;
        });
        
        // Remove HowTo step
        $(document).on('click', '.remove-howto-step', function() {
            $(this).closest('.howto-step-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * =====================================================
 * SAVE PRODUCT META DATA - FIXED VERSION
 * =====================================================
 * Bug fix: Mapping yang benar antara POST field dan meta key
 */
function putrafiber_save_product_meta($post_id) {
    // 1. Verify nonce
    if (!isset($_POST['putrafiber_product_nonce_field']) || 
        !wp_verify_nonce($_POST['putrafiber_product_nonce_field'], 'putrafiber_product_nonce')) {
        return;
    }
    
    // 2. Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 3. Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 4. Check post type
    if (get_post_type($post_id) !== 'product') {
        return;
    }
    
    // 5. FIELD MAPPING: POST field name => Meta key database
    $field_mapping = array(
        // Product Details
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
        
        // SEO Fields
        'meta_title'                => '_meta_title',
        'meta_description'          => '_meta_description',
        'focus_keyword'             => '_focus_keyword',
        
        // Video Schema
        'video_url'                 => '_video_url',
        'video_duration'            => '_video_duration',
        'video_title'               => '_video_title',
        'video_description'         => '_video_description',
    );
    
    // 6. Save each field with proper sanitization
    foreach ($field_mapping as $post_field => $meta_key) {
        if (isset($_POST[$post_field])) {
            $value = $_POST[$post_field];
            
            // Sanitize based on field type
            if (in_array($post_field, array('product_short_description', 'product_specifications', 'product_features', 'meta_description', 'video_description'))) {
                // Textarea fields
                $value = sanitize_textarea_field($value);
                
            } elseif (in_array($post_field, array('product_catalog_pdf', 'video_url'))) {
                // URL fields
                $value = esc_url_raw($value);
                
            } elseif ($post_field === 'product_price') {
                // Price field - special handling
                $value = absint($value);
                // Default to 1000 if empty or 0 (for schema anti Google penalty)
                if ($value <= 0) {
                    $value = 1000;
                }
                
            } else {
                // Text fields
                $value = sanitize_text_field($value);
            }
            
            // Update meta
            update_post_meta($post_id, $meta_key, $value);
        }
    }
    
    // 7. Save checkboxes (special handling - tidak ada di POST jika tidak dicentang)
    update_post_meta($post_id, '_enable_video_schema', isset($_POST['enable_video_schema']) ? '1' : '0');
    update_post_meta($post_id, '_enable_faq_schema', isset($_POST['enable_faq_schema']) ? '1' : '0');
    update_post_meta($post_id, '_enable_howto_schema', isset($_POST['enable_howto_schema']) ? '1' : '0');
    
    // 8. Save FAQ items (array data)
    if (isset($_POST['faq_items']) && is_array($_POST['faq_items'])) {
        $faq_items = array();
        foreach ($_POST['faq_items'] as $item) {
            if (!empty($item['question']) && !empty($item['answer'])) {
                $faq_items[] = array(
                    'question' => sanitize_text_field($item['question']),
                    'answer'   => sanitize_textarea_field($item['answer']),
                );
            }
        }
        
        if (!empty($faq_items)) {
            update_post_meta($post_id, '_faq_items', $faq_items);
        } else {
            delete_post_meta($post_id, '_faq_items');
        }
    } else {
        delete_post_meta($post_id, '_faq_items');
    }
    
    // 9. Save HowTo steps (array data)
    if (isset($_POST['howto_steps']) && is_array($_POST['howto_steps'])) {
        $howto_steps = array();
        foreach ($_POST['howto_steps'] as $step) {
            if (!empty($step['name']) && !empty($step['text'])) {
                $howto_steps[] = array(
                    'name' => sanitize_text_field($step['name']),
                    'text' => sanitize_textarea_field($step['text']),
                );
            }
        }
        
        if (!empty($howto_steps)) {
            update_post_meta($post_id, '_howto_steps', $howto_steps);
        } else {
            delete_post_meta($post_id, '_howto_steps');
        }
    } else {
        delete_post_meta($post_id, '_howto_steps');
    }
}
add_action('save_post_product', 'putrafiber_save_product_meta', 10, 1);

/**
 * Get Related Products
 * 
 * @param int $product_id Product ID
 * @param int $limit Number of products to return
 * @return WP_Query
 */
function putrafiber_get_related_products($product_id, $limit = 4) {
    $categories = wp_get_post_terms($product_id, 'product_category', array('fields' => 'ids'));
    
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $limit,
        'post__not_in'   => array($product_id),
        'orderby'        => 'rand',
    );
    
    if (!empty($categories)) {
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
