<?php
/**
 * Schema SEO Metabox - Reusable for All Post Types
 *
 * @package PutraFiber
 * @version 2.0.0 - COMPLETE VERSION (Added TouristAttraction Tab)
 * 
 * Features:
 * - Tab-based UI
 * - ServiceArea (City + Province)
 * - Video Schema
 * - FAQ Schema
 * - HowTo Schema
 * - TouristAttraction Schema (Full Implementation)
 */

if (!defined('ABSPATH')) exit;

/**
 * Add Schema Metabox to Post Types
 */
function putrafiber_add_schema_metabox() {
    $post_types = array('product', 'portfolio', 'post', 'page');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'putrafiber_schema_options',
            __('🔍 Schema SEO Options', 'putrafiber'),
            'putrafiber_schema_metabox_callback',
            $post_type,
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'putrafiber_add_schema_metabox');

/**
 * Schema Metabox Callback - Tab-Based UI
 */
function putrafiber_schema_metabox_callback($post) {
    wp_nonce_field('putrafiber_schema_nonce', 'putrafiber_schema_nonce_field');
    wp_nonce_field('pf_save_meta', 'pf_meta_nonce');

    // Get saved data - ServiceArea
    $enable_service_area = get_post_meta($post->ID, '_enable_service_area', true);
    $service_areas = get_post_meta($post->ID, '_service_areas', true);
    $manual_service_areas = get_post_meta($post->ID, '_manual_service_areas', true);

    if (!is_array($service_areas)) {
        $service_areas = array();
    }

    if (!is_array($manual_service_areas)) {
        $manual_service_areas = array();
    }

    if (!empty($manual_service_areas)) {
        foreach ($manual_service_areas as $index => $area) {
            if (!is_array($area)) {
                $manual_service_areas[$index] = array(
                    'type' => 'Place',
                    'name' => sanitize_text_field($area),
                    'country_code' => 'ID',
                    'identifier' => '',
                    'note' => ''
                );
                continue;
            }

            $manual_service_areas[$index] = wp_parse_args($area, array(
                'type' => 'Place',
                'name' => '',
                'country_code' => 'ID',
                'identifier' => '',
                'note' => ''
            ));
        }
    }

    if (empty($manual_service_areas)) {
        $manual_service_areas[] = array(
            'type' => 'Country',
            'name' => 'Indonesia',
            'country_code' => 'ID',
            'identifier' => '',
            'note' => ''
        );
    }

    $manual_service_area_types = array(
        'Country' => __('Country', 'putrafiber'),
        'AdministrativeArea' => __('Administrative Area (Province/Region)', 'putrafiber'),
        'City' => __('City', 'putrafiber'),
        'Place' => __('Place / Landmark', 'putrafiber'),
        'PostalAddress' => __('Postal Address', 'putrafiber'),
    );

    // Get saved data - Video
    $enable_video = get_post_meta($post->ID, '_enable_video_schema', true);
    $video_url = get_post_meta($post->ID, '_video_url', true);
    $video_title = get_post_meta($post->ID, '_video_title', true);
    $video_description = get_post_meta($post->ID, '_video_description', true);
    $video_duration = get_post_meta($post->ID, '_video_duration', true);
    
    // Get saved data - FAQ
    $enable_faq = get_post_meta($post->ID, '_enable_faq_schema', true);
    $faq_items = get_post_meta($post->ID, '_faq_items', true);
    
    // Get saved data - HowTo
    $enable_howto = get_post_meta($post->ID, '_enable_howto_schema', true);
    $howto_steps = get_post_meta($post->ID, '_howto_steps', true);
    
    // Get saved data - TouristAttraction
    $enable_tourist = get_post_meta($post->ID, '_enable_tourist_schema', true);
    $tourist_street = get_post_meta($post->ID, '_tourist_street_address', true);
    $tourist_city = get_post_meta($post->ID, '_tourist_city', true);
    $tourist_province = get_post_meta($post->ID, '_tourist_province', true);
    $tourist_postal = get_post_meta($post->ID, '_tourist_postal_code', true);
    $tourist_lat = get_post_meta($post->ID, '_tourist_latitude', true);
    $tourist_lng = get_post_meta($post->ID, '_tourist_longitude', true);
    $tourist_opening_hours = get_post_meta($post->ID, '_tourist_opening_hours', true);
    $tourist_is_free = get_post_meta($post->ID, '_tourist_is_free', true);
    $tourist_fee = get_post_meta($post->ID, '_tourist_entrance_fee', true);
    $tourist_phone = get_post_meta($post->ID, '_tourist_phone', true);
    $tourist_email = get_post_meta($post->ID, '_tourist_email', true);
    $tourist_languages = get_post_meta($post->ID, '_tourist_languages', true);
    $tourist_amenities = get_post_meta($post->ID, '_tourist_amenities', true);
    $tourist_rating = get_post_meta($post->ID, '_tourist_rating', true);
    $tourist_review_count = get_post_meta($post->ID, '_tourist_review_count', true);
    $tourist_public_access = get_post_meta($post->ID, '_tourist_public_access', true);
    
    // Auto-detect cities from title
    $auto_detected_cities = putrafiber_extract_cities_from_text(get_the_title($post->ID));
    
    // Ensure arrays
    if (!is_array($service_areas)) $service_areas = array();
    if (!is_array($faq_items)) $faq_items = array();
    if (!is_array($howto_steps)) $howto_steps = array();
    if (!is_array($tourist_opening_hours)) $tourist_opening_hours = array();
    
    ?>
    <style>
    .schema-tabs { display: flex; border-bottom: 2px solid #ddd; margin-bottom: 20px; flex-wrap: wrap; }
    .schema-tab { padding: 12px 20px; cursor: pointer; background: #f5f5f5; border: 1px solid #ddd; border-bottom: none; margin-right: 5px; margin-bottom: -2px; font-weight: 600; transition: all 0.3s; }
    .schema-tab:hover { background: #e9e9e9; }
    .schema-tab.active { background: #fff; border-bottom: 2px solid #fff; color: #0073aa; }
    .schema-tab-content { display: none; padding: 20px; background: #fff; border: 1px solid #ddd; border-top: none; }
    .schema-tab-content.active { display: block; }
    .schema-field { margin-bottom: 20px; }
    .schema-field label { display: block; font-weight: 600; margin-bottom: 8px; }
    .schema-field input[type="text"], .schema-field input[type="url"], .schema-field input[type="email"], .schema-field input[type="number"], .schema-field textarea, .schema-field select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
    .schema-field textarea { min-height: 100px; }
    .schema-toggle { display: flex; align-items: center; gap: 10px; padding: 15px; background: #f0f8ff; border-left: 4px solid #0073aa; margin-bottom: 20px; border-radius: 4px; }
    .schema-toggle input[type="checkbox"] { width: 20px; height: 20px; cursor: pointer; }
    .schema-toggle label { margin: 0; font-weight: 600; cursor: pointer; }
    .schema-help { font-size: 12px; color: #666; font-style: italic; margin-top: 5px; }
    .schema-repeater-item { background: #fff; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; position: relative; }
    .schema-repeater-remove { position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px; }
    .schema-repeater-remove:hover { background: #c82333; }
    .schema-repeater-add { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; margin-top: 10px; }
    .schema-repeater-add:hover { background: #218838; }
    .auto-detected { background: #e7f3ff; padding: 10px; border-left: 4px solid #0073aa; margin-bottom: 15px; border-radius: 4px; }
    .auto-detected strong { color: #0073aa; }
    .city-province-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: start; }
    .schema-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .schema-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
    .schema-section { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e0e0e0; }
    .schema-section-title { font-size: 14px; font-weight: 700; color: #0073aa; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
    .opening-hours-row { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end; }
    </style>

    <div class="schema-metabox-wrapper">
        <!-- Tabs -->
        <div class="schema-tabs">
            <div class="schema-tab active" data-tab="servicearea">📍 Service Area</div>
            <div class="schema-tab" data-tab="video">🎥 Video</div>
            <div class="schema-tab" data-tab="faq">❓ FAQ</div>
            <div class="schema-tab" data-tab="howto">📋 How-To</div>
            <div class="schema-tab" data-tab="tourist">🏖️ Tourist Attraction</div>
        </div>

        <!-- Tab Content: Service Area -->
        <div class="schema-tab-content active" data-content="servicearea">
            <div class="schema-toggle">
                <input type="checkbox" id="enable_service_area" name="enable_service_area" value="1" <?php checked($enable_service_area, '1'); ?>>
                <label for="enable_service_area">Enable Service Area Schema</label>
            </div>

            <div id="servicearea-fields" style="<?php echo $enable_service_area !== '1' ? 'display:none;' : ''; ?>">
                
                <?php if (!empty($auto_detected_cities)) : ?>
                <div class="auto-detected">
                    <strong>🤖 Auto-Detected from Title:</strong> 
                    <?php echo esc_html(implode(', ', $auto_detected_cities)); ?>
                    <p class="schema-help">Kota terdeteksi otomatis dari judul. Anda bisa override dengan input manual di bawah.</p>
                </div>
                <?php endif; ?>

                <div class="schema-field">
                    <label>🌆 Cities Coverage (Detailed)</label>
                    <p class="schema-help">Tambahkan kota yang Anda layani. Provinsi akan otomatis terisi berdasarkan kota.</p>
                    
                    <div id="service-areas-container">
                        <?php 
                        if (empty($service_areas)) {
                            if (!empty($auto_detected_cities)) {
                                foreach ($auto_detected_cities as $city) {
                                    $province = putrafiber_get_province_from_city($city);
                                    $service_areas[] = array('city' => $city, 'province' => $province);
                                }
                            } else {
                                $service_areas = array(array('city' => '', 'province' => ''));
                            }
                        }
                        
                        foreach ($service_areas as $index => $area) : 
                            $city = isset($area['city']) ? $area['city'] : '';
                            $province = isset($area['province']) ? $area['province'] : '';
                        ?>
                        <div class="schema-repeater-item service-area-item">
                            <div class="city-province-row">
                                <div>
                                    <label>Kota/Kabupaten</label>
                                    <select name="service_areas[<?php echo $index; ?>][city]" class="city-select" data-index="<?php echo $index; ?>">
                                        <option value="">-- Pilih Kota --</option>
                                        <?php 
                                        $cities = putrafiber_get_indonesian_cities();
                                        foreach ($cities as $city_name => $province_name) {
                                            echo '<option value="' . esc_attr($city_name) . '" data-province="' . esc_attr($province_name) . '" ' . selected($city, $city_name, false) . '>' . esc_html($city_name) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label>Provinsi</label>
                                    <input type="text" name="service_areas[<?php echo $index; ?>][province]" value="<?php echo esc_attr($province); ?>" class="province-input" readonly style="background: #f5f5f5;">
                                </div>
                                <div style="padding-top: 24px;">
                                    <?php if ($index > 0) : ?>
                                    <button type="button" class="schema-repeater-remove remove-service-area">✕</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="schema-repeater-add" id="add-service-area">+ Tambah Kota</button>
                </div>

                <div class="schema-field">
                    <label>🌍 Manual Service Area (Opsional)</label>
                    <p class="schema-help">Gunakan ketika kota/provinsi tidak tersedia di daftar. Secara default terisi INDONESIA sesuai standar Schema.org.</p>

                    <div id="manual-service-areas-container">
                        <?php foreach ($manual_service_areas as $index => $manual_area) : ?>
                        <div class="schema-repeater-item manual-service-area-item">
                            <?php if ($index > 0) : ?>
                            <button type="button" class="schema-repeater-remove remove-manual-service-area">✕</button>
                            <?php endif; ?>

                            <div class="schema-grid-3 manual-area-grid">
                                <div>
                                    <label><?php esc_html_e('Tipe Area', 'putrafiber'); ?></label>
                                    <select name="manual_service_areas[<?php echo $index; ?>][type]" class="manual-area-type">
                                        <?php foreach ($manual_service_area_types as $value => $label) : ?>
                                            <option value="<?php echo esc_attr($value); ?>" <?php selected($manual_area['type'], $value); ?>><?php echo esc_html($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label><?php esc_html_e('Nama Area', 'putrafiber'); ?></label>
                                    <input type="text" name="manual_service_areas[<?php echo $index; ?>][name]" value="<?php echo esc_attr($manual_area['name']); ?>" placeholder="<?php esc_attr_e('Contoh: Indonesia', 'putrafiber'); ?>">
                                </div>

                                <div>
                                    <label><?php esc_html_e('Kode Negara (ISO)', 'putrafiber'); ?></label>
                                    <input type="text" name="manual_service_areas[<?php echo $index; ?>][country_code]" value="<?php echo esc_attr($manual_area['country_code']); ?>" class="manual-area-country-code" maxlength="2" placeholder="<?php esc_attr_e('ID', 'putrafiber'); ?>">
                                </div>
                            </div>

                            <div class="schema-grid-2 manual-area-extra">
                                <div>
                                    <label><?php esc_html_e('Identifier / URL Resmi (Opsional)', 'putrafiber'); ?></label>
                                    <input type="text" name="manual_service_areas[<?php echo $index; ?>][identifier]" value="<?php echo esc_attr($manual_area['identifier']); ?>" placeholder="<?php esc_attr_e('Contoh: ID atau https://...', 'putrafiber'); ?>">
                                </div>

                                <div>
                                    <label><?php esc_html_e('Catatan Tambahan (Opsional)', 'putrafiber'); ?></label>
                                    <textarea name="manual_service_areas[<?php echo $index; ?>][note]" placeholder="<?php esc_attr_e('Contoh: Area prioritas layanan', 'putrafiber'); ?>"><?php echo esc_textarea($manual_area['note']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="schema-repeater-add" id="add-manual-service-area">+ <?php esc_html_e('Tambah Area Manual', 'putrafiber'); ?></button>
                </div>
            </div>
        </div>

        <!-- Tab Content: Video -->
        <div class="schema-tab-content" data-content="video">
            <div class="schema-toggle">
                <input type="checkbox" id="enable_video_schema" name="enable_video_schema" value="1" <?php checked($enable_video, '1'); ?>>
                <label for="enable_video_schema">Enable Video Schema</label>
            </div>

            <div id="video-fields" style="<?php echo $enable_video !== '1' ? 'display:none;' : ''; ?>">
                <div class="schema-field">
                    <label for="video_url">🎬 Video URL</label>
                    <input type="url" id="video_url" name="video_url" value="<?php echo esc_url($video_url); ?>" placeholder="https://youtube.com/watch?v=...">
                    <p class="schema-help">YouTube, Vimeo, atau video URL lainnya.</p>
                </div>

                <div class="schema-field">
                    <label for="video_title">📝 Video Title</label>
                    <input type="text" id="video_title" name="video_title" value="<?php echo esc_attr($video_title); ?>" placeholder="Tutorial Pemasangan Kolam Renang">
                    <p class="schema-help">Kosongkan untuk menggunakan judul post.</p>
                </div>

                <div class="schema-field">
                    <label for="video_description">📄 Video Description</label>
                    <textarea id="video_description" name="video_description" placeholder="Deskripsi video..."><?php echo esc_textarea($video_description); ?></textarea>
                </div>

                <div class="schema-field">
                    <label for="video_duration">⏱️ Duration (ISO 8601 Format)</label>
                    <input type="text" id="video_duration" name="video_duration" value="<?php echo esc_attr($video_duration); ?>" placeholder="PT5M30S">
                    <p class="schema-help">Format: PT[menit]M[detik]S. Contoh: PT5M30S = 5 menit 30 detik</p>
                </div>
            </div>
        </div>

        <!-- Tab Content: FAQ -->
        <div class="schema-tab-content" data-content="faq">
            <div class="schema-toggle">
                <input type="checkbox" id="enable_faq_schema" name="enable_faq_schema" value="1" <?php checked($enable_faq, '1'); ?>>
                <label for="enable_faq_schema">Enable FAQ Schema</label>
            </div>

            <div id="faq-fields" style="<?php echo $enable_faq !== '1' ? 'display:none;' : ''; ?>">
                <div class="schema-field">
                    <label>❓ Frequently Asked Questions</label>
                    
                    <div id="faq-items-container">
                        <?php 
                        if (empty($faq_items)) {
                            $faq_items = array(array('question' => '', 'answer' => ''));
                        }
                        foreach ($faq_items as $index => $item) : 
                        ?>
                        <div class="schema-repeater-item faq-item">
                            <?php if ($index > 0) : ?>
                            <button type="button" class="schema-repeater-remove remove-faq">✕</button>
                            <?php endif; ?>
                            
                            <div style="margin-bottom: 10px;">
                                <label>Pertanyaan</label>
                                <input type="text" name="faq_items[<?php echo $index; ?>][question]" value="<?php echo esc_attr($item['question'] ?? ''); ?>" placeholder="Apakah produk tahan cuaca?">
                            </div>
                            <div>
                                <label>Jawaban</label>
                                <textarea name="faq_items[<?php echo $index; ?>][answer]" placeholder="Ya, produk kami terbuat dari fiberglass yang..."><?php echo esc_textarea($item['answer'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="schema-repeater-add" id="add-faq">+ Tambah FAQ</button>
                </div>
            </div>
        </div>

        <!-- Tab Content: HowTo -->
        <div class="schema-tab-content" data-content="howto">
            <div class="schema-toggle">
                <input type="checkbox" id="enable_howto_schema" name="enable_howto_schema" value="1" <?php checked($enable_howto, '1'); ?>>
                <label for="enable_howto_schema">Enable How-To Schema</label>
            </div>

            <div id="howto-fields" style="<?php echo $enable_howto !== '1' ? 'display:none;' : ''; ?>">
                <div class="schema-field">
                    <label>📋 Step-by-Step Instructions</label>
                    
                    <div id="howto-steps-container">
                        <?php 
                        if (empty($howto_steps)) {
                            $howto_steps = array(array('name' => '', 'text' => ''));
                        }
                        foreach ($howto_steps as $index => $step) : 
                        ?>
                        <div class="schema-repeater-item howto-step">
                            <?php if ($index > 0) : ?>
                            <button type="button" class="schema-repeater-remove remove-howto">✕</button>
                            <?php endif; ?>
                            
                            <div style="margin-bottom: 10px;">
                                <label>Step <?php echo $index + 1; ?>: Nama</label>
                                <input type="text" name="howto_steps[<?php echo $index; ?>][name]" value="<?php echo esc_attr($step['name'] ?? ''); ?>" placeholder="Persiapan Area">
                            </div>
                            <div>
                                <label>Instruksi Detail</label>
                                <textarea name="howto_steps[<?php echo $index; ?>][text]" placeholder="Bersihkan area yang akan dipasang kolam..."><?php echo esc_textarea($step['text'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="schema-repeater-add" id="add-howto-step">+ Tambah Step</button>
                </div>
            </div>
        </div>

        <!-- Tab Content: Tourist Attraction -->
        <div class="schema-tab-content" data-content="tourist">
            <div class="schema-toggle">
                <input type="checkbox" id="enable_tourist_schema" name="enable_tourist_schema" value="1" <?php checked($enable_tourist, '1'); ?>>
                <label for="enable_tourist_schema">Enable Tourist Attraction Schema</label>
            </div>

            <div id="tourist-fields" style="<?php echo $enable_tourist !== '1' ? 'display:none;' : ''; ?>">
                
                <!-- Location Section -->
                <div class="schema-section">
                    <div class="schema-section-title">📍 Location Information</div>
                    
                    <div class="schema-field">
                        <label for="tourist_street_address">Street Address</label>
                        <input type="text" id="tourist_street_address" name="tourist_street_address" value="<?php echo esc_attr($tourist_street); ?>" placeholder="Jl. Raya No. 123">
                    </div>

                    <div class="schema-grid-2">
                        <div class="schema-field">
                            <label for="tourist_city">City / Kabupaten</label>
                            <select id="tourist_city" name="tourist_city" class="tourist-city-select">
                                <option value="">-- Pilih Kota --</option>
                                <?php 
                                $cities = putrafiber_get_indonesian_cities();
                                foreach ($cities as $city_name => $province_name) {
                                    echo '<option value="' . esc_attr($city_name) . '" data-province="' . esc_attr($province_name) . '" ' . selected($tourist_city, $city_name, false) . '>' . esc_html($city_name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="schema-field">
                            <label for="tourist_province">Province</label>
                            <input type="text" id="tourist_province" name="tourist_province" value="<?php echo esc_attr($tourist_province); ?>" readonly style="background: #f5f5f5;">
                        </div>
                    </div>

                    <div class="schema-field">
                        <label for="tourist_postal_code">Postal Code</label>
                        <input type="text" id="tourist_postal_code" name="tourist_postal_code" value="<?php echo esc_attr($tourist_postal); ?>" placeholder="12345">
                    </div>
                </div>

                <!-- Geo Coordinates Section -->
                <div class="schema-section">
                    <div class="schema-section-title">🗺️ Geo Coordinates</div>
                    
                    <div class="schema-grid-2">
                        <div class="schema-field">
                            <label for="tourist_latitude">Latitude</label>
                            <input type="text" id="tourist_latitude" name="tourist_latitude" value="<?php echo esc_attr($tourist_lat); ?>" placeholder="-6.2088">
                            <p class="schema-help">Contoh: -6.2088</p>
                        </div>

                        <div class="schema-field">
                            <label for="tourist_longitude">Longitude</label>
                            <input type="text" id="tourist_longitude" name="tourist_longitude" value="<?php echo esc_attr($tourist_lng); ?>" placeholder="106.8456">
                            <p class="schema-help">Contoh: 106.8456</p>
                        </div>
                    </div>

                    <p class="schema-help">💡 Tip: Buka Google Maps → Klik kanan lokasi → Copy coordinates</p>
                </div>

                <!-- Opening Hours Section -->
                <div class="schema-section">
                    <div class="schema-section-title">🕐 Opening Hours</div>
                    
                    <div id="opening-hours-container">
                        <?php 
                        if (empty($tourist_opening_hours)) {
                            $tourist_opening_hours = array(array('days' => '', 'opens' => '', 'closes' => ''));
                        }
                        foreach ($tourist_opening_hours as $index => $schedule) : 
                        ?>
                        <div class="schema-repeater-item opening-hours-item">
                            <div class="opening-hours-row">
                                <div>
                                    <label>Days</label>
                                    <select name="tourist_opening_hours[<?php echo $index; ?>][days]" multiple style="height: 100px;">
                                        <?php 
                                        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                                        $selected_days = isset($schedule['days']) ? explode(',', $schedule['days']) : array();
                                        foreach ($days as $day) {
                                            $selected = in_array($day, $selected_days) ? 'selected' : '';
                                            echo '<option value="' . $day . '" ' . $selected . '>' . $day . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="schema-help">Hold Ctrl/Cmd untuk multiple select</p>
                                </div>
                                <div>
                                    <label>Opens</label>
                                    <input type="time" name="tourist_opening_hours[<?php echo $index; ?>][opens]" value="<?php echo esc_attr($schedule['opens'] ?? ''); ?>">
                                </div>
                                <div>
                                    <label>Closes</label>
                                    <input type="time" name="tourist_opening_hours[<?php echo $index; ?>][closes]" value="<?php echo esc_attr($schedule['closes'] ?? ''); ?>">
                                </div>
                                <div style="padding-top: 24px;">
                                    <?php if ($index > 0) : ?>
                                    <button type="button" class="schema-repeater-remove remove-opening-hours">✕</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="schema-repeater-add" id="add-opening-hours">+ Tambah Schedule</button>
                </div>

                <!-- Pricing Section -->
                <div class="schema-section">
                    <div class="schema-section-title">💰 Pricing Information</div>
                    
                    <div class="schema-field">
                        <label>
                            <input type="checkbox" id="tourist_is_free" name="tourist_is_free" value="1" <?php checked($tourist_is_free, '1'); ?>>
                            Free Entrance (Gratis)
                        </label>
                    </div>

                    <div class="schema-field" id="entrance-fee-field" style="<?php echo $tourist_is_free === '1' ? 'display:none;' : ''; ?>">
                        <label for="tourist_entrance_fee">Entrance Fee / Price Range</label>
                        <input type="text" id="tourist_entrance_fee" name="tourist_entrance_fee" value="<?php echo esc_attr($tourist_fee); ?>" placeholder="Rp 25.000 - Rp 50.000">
                        <p class="schema-help">Contoh: Rp 25.000 - Rp 50.000</p>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="schema-section">
                    <div class="schema-section-title">📞 Contact Information</div>
                    
                    <div class="schema-grid-2">
                        <div class="schema-field">
                            <label for="tourist_phone">Phone Number</label>
                            <input type="text" id="tourist_phone" name="tourist_phone" value="<?php echo esc_attr($tourist_phone); ?>" placeholder="+62812345678">
                        </div>

                        <div class="schema-field">
                            <label for="tourist_email">Email Address</label>
                            <input type="email" id="tourist_email" name="tourist_email" value="<?php echo esc_attr($tourist_email); ?>" placeholder="info@example.com">
                        </div>
                    </div>

                    <div class="schema-field">
                        <label for="tourist_languages">Available Languages (comma-separated)</label>
                        <input type="text" id="tourist_languages" name="tourist_languages" value="<?php echo esc_attr($tourist_languages); ?>" placeholder="Indonesian, English">
                    </div>
                </div>

                <!-- Amenities Section -->
                <div class="schema-section">
                    <div class="schema-section-title">✨ Amenities & Facilities</div>
                    
                    <div class="schema-field">
                        <label for="tourist_amenities">Amenities (comma-separated)</label>
                        <textarea id="tourist_amenities" name="tourist_amenities" placeholder="Parking Area, Toilet, Restaurant, WiFi, Prayer Room"><?php echo esc_textarea($tourist_amenities); ?></textarea>
                        <p class="schema-help">Pisahkan dengan koma. Contoh: Parking, Toilet, Restaurant</p>
                    </div>
                </div>

                <!-- Rating Section -->
                <div class="schema-section">
                    <div class="schema-section-title">⭐ Rating & Reviews (Optional)</div>
                    
                    <div class="schema-grid-2">
                        <div class="schema-field">
                            <label for="tourist_rating">Rating (1-5)</label>
                            <input type="number" id="tourist_rating" name="tourist_rating" value="<?php echo esc_attr($tourist_rating); ?>" min="1" max="5" step="0.1" placeholder="4.5">
                        </div>

                        <div class="schema-field">
                            <label for="tourist_review_count">Number of Reviews</label>
                            <input type="number" id="tourist_review_count" name="tourist_review_count" value="<?php echo esc_attr($tourist_review_count); ?>" placeholder="150">
                        </div>
                    </div>
                </div>

                <!-- Public Access Section -->
                <div class="schema-section">
                    <div class="schema-section-title">🌍 Accessibility</div>
                    
                    <div class="schema-field">
                        <label>
                            <input type="checkbox" id="tourist_public_access" name="tourist_public_access" value="1" <?php checked($tourist_public_access, '1'); ?>>
                            Public Access Allowed (Dapat diakses publik)
                        </label>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
    jQuery(document).ready(function($){
        
        // Tab switching
        $('.schema-tab').on('click', function(){
            var tab = $(this).data('tab');
            $('.schema-tab').removeClass('active');
            $('.schema-tab-content').removeClass('active');
            $(this).addClass('active');
            $('.schema-tab-content[data-content="'+tab+'"]').addClass('active');
        });

        // Toggle fields visibility
        $('#enable_service_area').on('change', function(){ $('#servicearea-fields').toggle(this.checked); });
        $('#enable_video_schema').on('change', function(){ $('#video-fields').toggle(this.checked); });
        $('#enable_faq_schema').on('change', function(){ $('#faq-fields').toggle(this.checked); });
        $('#enable_howto_schema').on('change', function(){ $('#howto-fields').toggle(this.checked); });
        $('#enable_tourist_schema').on('change', function(){ $('#tourist-fields').toggle(this.checked); });

        // Tourist: Toggle entrance fee field
        $('#tourist_is_free').on('change', function(){
            $('#entrance-fee-field').toggle(!this.checked);
        });

        // Service Area & Tourist: Auto-fill province
        $(document).on('change', '.city-select, .tourist-city-select', function(){
            var province = $(this).find(':selected').data('province');
            if ($(this).hasClass('tourist-city-select')) {
                $('#tourist_province').val(province);
            } else {
                var index = $(this).data('index');
                $(this).closest('.city-province-row').find('.province-input').val(province);
            }
        });

        // Add Service Area
        var serviceAreaIndex = <?php echo count($service_areas); ?>;
        $('#add-service-area').on('click', function(){
            var html = '<div class="schema-repeater-item service-area-item"><div class="city-province-row">' +
                '<div><label>Kota/Kabupaten</label><select name="service_areas['+serviceAreaIndex+'][city]" class="city-select" data-index="'+serviceAreaIndex+'">' +
                '<option value="">-- Pilih Kota --</option>' +
                <?php
                $cities = putrafiber_get_indonesian_cities();
                foreach ($cities as $city_name => $province_name) {
                    echo "'<option value=\"" . esc_js($city_name) . "\" data-province=\"" . esc_js($province_name) . "\">" . esc_js($city_name) . "</option>' + ";
                }
                ?>
                '</select></div>' +
                '<div><label>Provinsi</label><input type="text" name="service_areas['+serviceAreaIndex+'][province]" class="province-input" readonly style="background: #f5f5f5;"></div>' +
                '<div style="padding-top: 24px;"><button type="button" class="schema-repeater-remove remove-service-area">✕</button></div>' +
                '</div></div>';
            $('#service-areas-container').append(html);
            serviceAreaIndex++;
        });
        $(document).on('click', '.remove-service-area', function(){ $(this).closest('.service-area-item').fadeOut(300, function(){ $(this).remove(); }); });

        // Manual Service Area
        var manualServiceAreaIndex = <?php echo count($manual_service_areas); ?>;
        var manualServiceAreaTypes = <?php echo wp_json_encode($manual_service_area_types); ?>;

        function putrafiberRenderManualAreaOptions(selected) {
            var options = '';
            for (var key in manualServiceAreaTypes) {
                if (!Object.prototype.hasOwnProperty.call(manualServiceAreaTypes, key)) {
                    continue;
                }
                var isSelected = key === selected ? ' selected' : '';
                options += '<option value="' + key + '"' + isSelected + '>' + manualServiceAreaTypes[key] + '</option>';
            }
            return options;
        }

        $('#add-manual-service-area').on('click', function(){
            var options = putrafiberRenderManualAreaOptions('Place');
            var html = '<div class="schema-repeater-item manual-service-area-item">' +
                '<button type="button" class="schema-repeater-remove remove-manual-service-area">✕</button>' +
                '<div class="schema-grid-3 manual-area-grid">' +
                '<div><label><?php echo esc_js(__('Tipe Area', 'putrafiber')); ?></label><select name="manual_service_areas[' + manualServiceAreaIndex + '][type]" class="manual-area-type">' + options + '</select></div>' +
                '<div><label><?php echo esc_js(__('Nama Area', 'putrafiber')); ?></label><input type="text" name="manual_service_areas[' + manualServiceAreaIndex + '][name]" placeholder="<?php echo esc_js(__('Contoh: Indonesia', 'putrafiber')); ?>"></div>' +
                '<div><label><?php echo esc_js(__('Kode Negara (ISO)', 'putrafiber')); ?></label><input type="text" name="manual_service_areas[' + manualServiceAreaIndex + '][country_code]" class="manual-area-country-code" maxlength="2" placeholder="ID"></div>' +
                '</div>' +
                '<div class="schema-grid-2 manual-area-extra">' +
                '<div><label><?php echo esc_js(__('Identifier / URL Resmi (Opsional)', 'putrafiber')); ?></label><input type="text" name="manual_service_areas[' + manualServiceAreaIndex + '][identifier]" placeholder="<?php echo esc_js(__('Contoh: ID atau https://...', 'putrafiber')); ?>"></div>' +
                '<div><label><?php echo esc_js(__('Catatan Tambahan (Opsional)', 'putrafiber')); ?></label><textarea name="manual_service_areas[' + manualServiceAreaIndex + '][note]" placeholder="<?php echo esc_js(__('Contoh: Area prioritas layanan', 'putrafiber')); ?>"></textarea></div>' +
                '</div>' +
                '</div>';
            $('#manual-service-areas-container').append(html);
            manualServiceAreaIndex++;
        });

        $(document).on('click', '.remove-manual-service-area', function(){
            $(this).closest('.manual-service-area-item').fadeOut(300, function(){ $(this).remove(); });
        });

        $(document).on('input', '.manual-area-country-code', function(){
            var cleaned = $(this).val().replace(/[^a-zA-Z]/g, '').toUpperCase();
            $(this).val(cleaned.slice(0, 2));
        });

        // Add FAQ
        var faqIndex = <?php echo count($faq_items); ?>;
        $('#add-faq').on('click', function(){
            var html = '<div class="schema-repeater-item faq-item"><button type="button" class="schema-repeater-remove remove-faq">✕</button>' +
                '<div style="margin-bottom: 10px;"><label>Pertanyaan</label><input type="text" name="faq_items['+faqIndex+'][question]" placeholder="Pertanyaan..."></div>' +
                '<div><label>Jawaban</label><textarea name="faq_items['+faqIndex+'][answer]" placeholder="Jawaban..."></textarea></div></div>';
            $('#faq-items-container').append(html);
            faqIndex++;
        });
        $(document).on('click', '.remove-faq', function(){ $(this).closest('.faq-item').fadeOut(300, function(){ $(this).remove(); }); });

        // Add HowTo
        var howtoIndex = <?php echo count($howto_steps); ?>;
        $('#add-howto-step').on('click', function(){
            var stepNum = howtoIndex + 1;
            var html = '<div class="schema-repeater-item howto-step"><button type="button" class="schema-repeater-remove remove-howto">✕</button>' +
                '<div style="margin-bottom: 10px;"><label>Step ' + stepNum + ': Nama</label><input type="text" name="howto_steps['+howtoIndex+'][name]" placeholder="Nama step..."></div>' +
                '<div><label>Instruksi Detail</label><textarea name="howto_steps['+howtoIndex+'][text]" placeholder="Detail instruksi..."></textarea></div></div>';
            $('#howto-steps-container').append(html);
            howtoIndex++;
        });
        $(document).on('click', '.remove-howto', function(){ $(this).closest('.howto-step').fadeOut(300, function(){ $(this).remove(); }); });

        // Add Opening Hours
        var openingHoursIndex = <?php echo count($tourist_opening_hours); ?>;
        $('#add-opening-hours').on('click', function(){
            var html = '<div class="schema-repeater-item opening-hours-item"><div class="opening-hours-row">' +
                '<div><label>Days</label><select name="tourist_opening_hours['+openingHoursIndex+'][days]" multiple style="height: 100px;">' +
                '<option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option>' +
                '<option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option>' +
                '<option value="Sunday">Sunday</option></select><p class="schema-help">Hold Ctrl/Cmd untuk multiple select</p></div>' +
                '<div><label>Opens</label><input type="time" name="tourist_opening_hours['+openingHoursIndex+'][opens]"></div>' +
                '<div><label>Closes</label><input type="time" name="tourist_opening_hours['+openingHoursIndex+'][closes]"></div>' +
                '<div style="padding-top: 24px;"><button type="button" class="schema-repeater-remove remove-opening-hours">✕</button></div>' +
                '</div></div>';
            $('#opening-hours-container').append(html);
            openingHoursIndex++;
        });
        $(document).on('click', '.remove-opening-hours', function(){ $(this).closest('.opening-hours-item').fadeOut(300, function(){ $(this).remove(); }); });

    });
    </script>
    <?php
}

/**
 * Save Schema Meta Data
 */
function putrafiber_save_schema_meta($post_id) {
    if (!isset($_POST['pf_meta_nonce']) || !wp_verify_nonce($_POST['pf_meta_nonce'], 'pf_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['putrafiber_schema_nonce_field']) ||
        !wp_verify_nonce($_POST['putrafiber_schema_nonce_field'], 'putrafiber_schema_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Save Service Area
    update_post_meta($post_id, '_enable_service_area', isset($_POST['enable_service_area']) ? '1' : '0');

    if (isset($_POST['service_areas']) && is_array($_POST['service_areas'])) {
        $areas = array();
        foreach ($_POST['service_areas'] as $area) {
            $city     = isset($area['city']) ? pf_clean_text($area['city']) : '';
            $province = isset($area['province']) ? pf_clean_text($area['province']) : '';

            if ($city !== '') {
                $areas[] = array(
                    'city'     => $city,
                    'province' => $province,
                );
            }
        }
        update_post_meta($post_id, '_service_areas', $areas);
    } else {
        delete_post_meta($post_id, '_service_areas');
    }

    if (isset($_POST['manual_service_areas']) && is_array($_POST['manual_service_areas'])) {
        $manual_entries = array();
        foreach ($_POST['manual_service_areas'] as $entry) {
            $type = isset($entry['type']) ? pf_clean_text($entry['type']) : '';
            $name = isset($entry['name']) ? pf_clean_text($entry['name']) : '';

            if ($type === '' || $name === '') {
                continue;
            }

            $country_code = isset($entry['country_code']) ? strtoupper(pf_clean_text($entry['country_code'])) : '';
            $identifier_raw = isset($entry['identifier']) ? wp_unslash($entry['identifier']) : '';
            $identifier = '';

            if ($identifier_raw !== '') {
                $identifier = filter_var($identifier_raw, FILTER_VALIDATE_URL)
                    ? pf_clean_url($identifier_raw)
                    : pf_clean_text($identifier_raw);
            }

            $manual_entries[] = array(
                'type'         => $type,
                'name'         => $name,
                'country_code' => $country_code,
                'identifier'   => $identifier,
                'note'         => isset($entry['note']) ? pf_clean_html($entry['note']) : '',
            );
        }

        if (!empty($manual_entries)) {
            update_post_meta($post_id, '_manual_service_areas', $manual_entries);
        } else {
            delete_post_meta($post_id, '_manual_service_areas');
        }
    } else {
        delete_post_meta($post_id, '_manual_service_areas');
    }

    // Save Video
    update_post_meta($post_id, '_enable_video_schema', isset($_POST['enable_video_schema']) ? '1' : '0');
    update_post_meta($post_id, '_video_url', isset($_POST['video_url']) ? pf_clean_url($_POST['video_url']) : '');
    update_post_meta($post_id, '_video_title', isset($_POST['video_title']) ? pf_clean_text($_POST['video_title']) : '');
    update_post_meta($post_id, '_video_description', isset($_POST['video_description']) ? pf_clean_html($_POST['video_description']) : '');
    update_post_meta($post_id, '_video_duration', isset($_POST['video_duration']) ? pf_clean_text($_POST['video_duration']) : '');

    // Save FAQ
    update_post_meta($post_id, '_enable_faq_schema', isset($_POST['enable_faq_schema']) ? '1' : '0');

    if (isset($_POST['faq_items']) && is_array($_POST['faq_items'])) {
        $faq = array();
        foreach ($_POST['faq_items'] as $item) {
            $question = isset($item['question']) ? pf_clean_text($item['question']) : '';
            $answer   = isset($item['answer']) ? pf_clean_html($item['answer']) : '';

            if ($question !== '' && $answer !== '') {
                $faq[] = array(
                    'question' => $question,
                    'answer'   => $answer,
                );
            }
        }
        update_post_meta($post_id, '_faq_items', $faq);
    } else {
        delete_post_meta($post_id, '_faq_items');
    }

    // Save HowTo
    update_post_meta($post_id, '_enable_howto_schema', isset($_POST['enable_howto_schema']) ? '1' : '0');

    if (isset($_POST['howto_steps']) && is_array($_POST['howto_steps'])) {
        $steps = array();
        foreach ($_POST['howto_steps'] as $step) {
            $name        = isset($step['name']) ? pf_clean_text($step['name']) : '';
            $description = isset($step['description']) ? pf_clean_html($step['description']) : '';
            $image       = isset($step['image']) ? pf_clean_url($step['image']) : '';

            if ($name !== '' && $description !== '') {
                $steps[] = array(
                    'name'        => $name,
                    'description' => $description,
                    'image'       => $image,
                );
            }
        }
        update_post_meta($post_id, '_howto_steps', $steps);
    } else {
        delete_post_meta($post_id, '_howto_steps');
    }

    // Save TouristAttraction
    update_post_meta($post_id, '_enable_tourist_schema', isset($_POST['enable_tourist_schema']) ? '1' : '0');
    update_post_meta($post_id, '_tourist_street_address', isset($_POST['tourist_street_address']) ? pf_clean_text($_POST['tourist_street_address']) : '');
    update_post_meta($post_id, '_tourist_city', isset($_POST['tourist_city']) ? pf_clean_text($_POST['tourist_city']) : '');
    update_post_meta($post_id, '_tourist_province', isset($_POST['tourist_province']) ? pf_clean_text($_POST['tourist_province']) : '');
    update_post_meta($post_id, '_tourist_postal_code', isset($_POST['tourist_postal_code']) ? pf_clean_text($_POST['tourist_postal_code']) : '');
    update_post_meta($post_id, '_tourist_latitude', isset($_POST['tourist_latitude']) ? pf_clean_text($_POST['tourist_latitude']) : '');
    update_post_meta($post_id, '_tourist_longitude', isset($_POST['tourist_longitude']) ? pf_clean_text($_POST['tourist_longitude']) : '');

    if (isset($_POST['tourist_opening_hours']) && is_array($_POST['tourist_opening_hours'])) {
        $hours = array();
        foreach ($_POST['tourist_opening_hours'] as $schedule) {
            $days_raw   = isset($schedule['days']) ? $schedule['days'] : '';
            $opens      = isset($schedule['opens']) ? pf_clean_text($schedule['opens']) : '';
            $closes     = isset($schedule['closes']) ? pf_clean_text($schedule['closes']) : '';
            $days_value = '';

            if (is_array($days_raw)) {
                $days_value = implode(',', array_map('pf_clean_text', $days_raw));
            } else {
                $days_value = pf_clean_text($days_raw);
            }

            if ($days_value !== '' && $opens !== '' && $closes !== '') {
                $hours[] = array(
                    'days'   => $days_value,
                    'opens'  => $opens,
                    'closes' => $closes,
                );
            }
        }
        update_post_meta($post_id, '_tourist_opening_hours', $hours);
    } else {
        delete_post_meta($post_id, '_tourist_opening_hours');
    }

    update_post_meta($post_id, '_tourist_is_free', isset($_POST['tourist_is_free']) ? '1' : '0');
    update_post_meta($post_id, '_tourist_entrance_fee', isset($_POST['tourist_entrance_fee']) ? pf_clean_text($_POST['tourist_entrance_fee']) : '');
    update_post_meta($post_id, '_tourist_phone', isset($_POST['tourist_phone']) ? pf_clean_text($_POST['tourist_phone']) : '');
    update_post_meta($post_id, '_tourist_email', isset($_POST['tourist_email']) ? sanitize_email(wp_unslash($_POST['tourist_email'])) : '');
    update_post_meta($post_id, '_tourist_languages', isset($_POST['tourist_languages']) ? pf_clean_text($_POST['tourist_languages']) : '');
    update_post_meta($post_id, '_tourist_amenities', isset($_POST['tourist_amenities']) ? pf_clean_html($_POST['tourist_amenities']) : '');
    update_post_meta($post_id, '_tourist_rating', isset($_POST['tourist_rating']) ? pf_clean_text($_POST['tourist_rating']) : '');
    update_post_meta($post_id, '_tourist_review_count', isset($_POST['tourist_review_count']) ? pf_clean_int($_POST['tourist_review_count']) : '');
    update_post_meta($post_id, '_tourist_public_access', isset($_POST['tourist_public_access']) ? '1' : '0');
}
add_action('save_post', 'putrafiber_save_schema_meta');

/**
 * Get Indonesian Cities with Provinces
 */
function putrafiber_get_indonesian_cities() {
    return array(
        // Jabodetabek
        'Jakarta Pusat' => 'DKI Jakarta',
        'Jakarta Utara' => 'DKI Jakarta',
        'Jakarta Barat' => 'DKI Jakarta',
        'Jakarta Selatan' => 'DKI Jakarta',
        'Jakarta Timur' => 'DKI Jakarta',
        'Bogor' => 'Jawa Barat',
        'Depok' => 'Jawa Barat',
        'Tangerang' => 'Banten',
        'Tangerang Selatan' => 'Banten',
        'Bekasi' => 'Jawa Barat',
        'Cikarang' => 'Jawa Barat',
        'Karawang' => 'Jawa Barat',
        'Serpong' => 'Banten',
        'Bintaro' => 'Banten',
        'Serang' => 'Banten',
        'Cilegon' => 'Banten',
        
        // Jawa Barat
        'Bandung' => 'Jawa Barat',
        'Cimahi' => 'Jawa Barat',
        'Sukabumi' => 'Jawa Barat',
        'Cirebon' => 'Jawa Barat',
        'Tasikmalaya' => 'Jawa Barat',
        'Garut' => 'Jawa Barat',
        'Purwakarta' => 'Jawa Barat',
        'Subang' => 'Jawa Barat',
        'Indramayu' => 'Jawa Barat',
        'Kuningan' => 'Jawa Barat',
        'Majalengka' => 'Jawa Barat',
        'Banjar' => 'Jawa Barat',
        
        // Jawa Tengah
        'Semarang' => 'Jawa Tengah',
        'Solo' => 'Jawa Tengah',
        'Surakarta' => 'Jawa Tengah',
        'Magelang' => 'Jawa Tengah',
        'Purwokerto' => 'Jawa Tengah',
        'Tegal' => 'Jawa Tengah',
        'Pekalongan' => 'Jawa Tengah',
        'Salatiga' => 'Jawa Tengah',
        'Klaten' => 'Jawa Tengah',
        'Kudus' => 'Jawa Tengah',
        'Purwodadi' => 'Jawa Tengah',
        
        // DIY
        'Yogyakarta' => 'DI Yogyakarta',
        'Sleman' => 'DI Yogyakarta',
        'Bantul' => 'DI Yogyakarta',
        'Wonosari' => 'DI Yogyakarta',
        
        // Jawa Timur
        'Surabaya' => 'Jawa Timur',
        'Malang' => 'Jawa Timur',
        'Batu' => 'Jawa Timur',
        'Kediri' => 'Jawa Timur',
        'Madiun' => 'Jawa Timur',
        'Mojokerto' => 'Jawa Timur',
        'Pasuruan' => 'Jawa Timur',
        'Probolinggo' => 'Jawa Timur',
        'Blitar' => 'Jawa Timur',
        'Jember' => 'Jawa Timur',
        'Sidoarjo' => 'Jawa Timur',
        'Gresik' => 'Jawa Timur',
        
        // Bali
        'Denpasar' => 'Bali',
        'Badung' => 'Bali',
        'Gianyar' => 'Bali',
        'Tabanan' => 'Bali',
        'Singaraja' => 'Bali',
        
        // Sumatera Utara
        'Medan' => 'Sumatera Utara',
        'Binjai' => 'Sumatera Utara',
        'Pematangsiantar' => 'Sumatera Utara',
        'Tebing Tinggi' => 'Sumatera Utara',
        
        // Sumatera Barat
        'Padang' => 'Sumatera Barat',
        'Bukittinggi' => 'Sumatera Barat',
        'Payakumbuh' => 'Sumatera Barat',
        
        // Sumatera Selatan
        'Palembang' => 'Sumatera Selatan',
        'Prabumulih' => 'Sumatera Selatan',
        
        // Riau
        'Pekanbaru' => 'Riau',
        'Dumai' => 'Riau',
        
        // Kepulauan Riau
        'Batam' => 'Kepulauan Riau',
        'Tanjung Pinang' => 'Kepulauan Riau',
        
        // Lampung
        'Bandar Lampung' => 'Lampung',
        'Metro' => 'Lampung',
        
        // Kalimantan Timur
        'Balikpapan' => 'Kalimantan Timur',
        'Samarinda' => 'Kalimantan Timur',
        'Bontang' => 'Kalimantan Timur',
        
        // Kalimantan Selatan
        'Banjarmasin' => 'Kalimantan Selatan',
        'Banjarbaru' => 'Kalimantan Selatan',
        
        // Kalimantan Barat
        'Pontianak' => 'Kalimantan Barat',
        'Singkawang' => 'Kalimantan Barat',
        
        // Kalimantan Tengah
        'Palangkaraya' => 'Kalimantan Tengah',
        
        // Sulawesi Selatan
        'Makassar' => 'Sulawesi Selatan',
        'Parepare' => 'Sulawesi Selatan',
        'Palopo' => 'Sulawesi Selatan',
        
        // Sulawesi Utara
        'Manado' => 'Sulawesi Utara',
        'Bitung' => 'Sulawesi Utara',
        'Tomohon' => 'Sulawesi Utara',
        
        // Sulawesi Tengah
        'Palu' => 'Sulawesi Tengah',
        
        // Sulawesi Tenggara
        'Kendari' => 'Sulawesi Tenggara',
        
        // Papua
        'Jayapura' => 'Papua',
        'Sorong' => 'Papua Barat',
        
        // Maluku
        'Ambon' => 'Maluku',
        'Ternate' => 'Maluku Utara',
    );
}

/**
 * Get Province from City
 */
function putrafiber_get_province_from_city($city) {
    $cities = putrafiber_get_indonesian_cities();
    return isset($cities[$city]) ? $cities[$city] : '';
}