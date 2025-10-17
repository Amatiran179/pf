<?php
/**
 * Schema / CTA preview meta box.
 *
 * @package PutraFiber
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PutraFiber_CTA_Validator')) {
    class PutraFiber_CTA_Validator
    {
        /**
         * Register meta box for supported post types.
         *
         * @return void
         */
        public static function init()
        {
            $post_types = array('product', 'portfolio', 'post');

            foreach ($post_types as $type) {
                add_meta_box(
                    'putrafiber_schema_cta_preview',
                    __('Schema / CTA Preview', 'putrafiber'),
                    array(__CLASS__, 'render'),
                    $type,
                    'advanced',
                    'default'
                );
            }
        }

        /**
         * Render preview meta box.
         *
         * @param WP_Post $post Current post object.
         * @return void
         */
        public static function render($post)
        {
            wp_nonce_field('putrafiber_cta_preview_nonce', 'putrafiber_cta_preview_nonce_field');

            echo '<p class="description">' . esc_html__('Pratinjau read-only dari Schema Advanced beserta peringatan CTA penting.', 'putrafiber') . '</p>';

            if (!pf_schema_yes()) {
                echo '<p><em>' . esc_html__('Schema Advanced belum diaktifkan. Aktifkan melalui Theme Options → Schema Advanced.', 'putrafiber') . '</em></p>';
                return;
            }

            if (!class_exists('PutraFiber_Schema_Manager')) {
                echo '<p><em>' . esc_html__('Schema Manager belum tersedia.', 'putrafiber') . '</em></p>';
                return;
            }

            $graph = PutraFiber_Schema_Manager::generate_graph($post->ID);

            if (!empty($graph)) {
                $pretty = wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                echo '<textarea readonly style="width:100%;min-height:220px;font-family:monospace;">' . esc_textarea($pretty) . '</textarea>';
            } else {
                echo '<p><em>' . esc_html__('Belum ada data schema yang dapat ditampilkan untuk konten ini.', 'putrafiber') . '</em></p>';
            }

            $warnings = self::collect_warnings($post);
            if (!empty($warnings)) {
                echo '<div class="putrafiber-cta-warnings">';
                echo '<strong>' . esc_html__('Catatan:', 'putrafiber') . '</strong>';
                echo '<ul>';
                foreach ($warnings as $warning) {
                    echo '<li>' . esc_html($warning) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
        }

        /**
         * Collect lightweight warnings for CTA related data.
         *
         * @param WP_Post $post Current post.
         * @return array
         */
        protected static function collect_warnings($post)
        {
            $warnings = array();

            if (!($post instanceof WP_Post)) {
                return $warnings;
            }

            if ($post->post_type === 'product') {
                $price = get_post_meta($post->ID, '_product_price', true);
                if (empty($price) || (float) $price <= 0) {
                    $warnings[] = __('Harga kosong atau 0. Schema akan fallback otomatis ke Rp1.000 (IDR).', 'putrafiber');
                }

                $stock = get_post_meta($post->ID, '_product_stock', true);
                if (empty($stock)) {
                    $warnings[] = __('Status stok belum diisi. Schema akan menggunakan fallback PreOrder.', 'putrafiber');
                }

                $sku = get_post_meta($post->ID, '_product_sku', true);
                if (empty($sku)) {
                    $warnings[] = __('SKU belum diisi (opsional, namun direkomendasikan).', 'putrafiber');
                }

                $brand = '';
                if (function_exists('putrafiber_get_option')) {
                    $brand = putrafiber_get_option('company_name', '');
                }
                if (empty($brand)) {
                    $warnings[] = __('Brand perusahaan belum diatur di Theme Options → Contact. Schema akan memakai nama situs.', 'putrafiber');
                }
            }

            return $warnings;
        }
    }
}
