<?php
/**
 * Schema Advanced helper functions.
 *
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

if (!function_exists('pf_schema_sanitize_text')) {
    /**
     * Sanitize plain text values for schema usage.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf_schema_sanitize_text($value)
    {
        if (is_array($value)) {
            $value = implode(' ', $value);
        }

        $value = is_scalar($value) ? (string) $value : '';
        $value = wp_strip_all_tags($value);

        return trim($value);
    }
}

if (!function_exists('pf_schema_sanitize_url')) {
    /**
     * Sanitize URL values for schema usage.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function pf_schema_sanitize_url($value)
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        $value = is_scalar($value) ? (string) $value : '';

        return esc_url_raw(trim($value));
    }
}

if (!function_exists('pf_schema_price_fallback')) {
    /**
     * Ensure price has a safe fallback.
     *
     * @param mixed $price Raw price.
     * @return float|int
     */
    function pf_schema_price_fallback($price)
    {
        if (is_string($price)) {
            $price = preg_replace('/[^0-9\.,-]/', '', $price);
            $price = str_replace(',', '.', $price);
        }

        $numeric = is_numeric($price) ? (float) $price : 0.0;

        if ($numeric <= 0) {
            $numeric = 1000;
        }

        return $numeric;
    }
}

if (!function_exists('pf_schema_sanitize_price')) {
    /**
     * Sanitize price values and ensure numeric fallback.
     *
     * @param mixed $value Raw price.
     * @return float|int
     */
    function pf_schema_sanitize_price($value)
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        $value = is_scalar($value) ? (string) $value : '';
        $value = preg_replace('/[^0-9\.,-]/', '', $value);
        $value = str_replace(',', '.', $value);

        if ($value === '' || $value === null) {
            return pf_schema_price_fallback(0);
        }

        $numeric = is_numeric($value) ? (float) $value : 0.0;

        return pf_schema_price_fallback($numeric);
    }
}

if (!function_exists('pf_schema_minify_json')) {
    /**
     * Minify JSON safely.
     *
     * @param string $json_string JSON string.
     * @return string
     */
    function pf_schema_minify_json($json_string)
    {
        if (!is_string($json_string) || $json_string === '') {
            return '';
        }

        $decoded = json_decode($json_string, false);
        if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
            return wp_json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $json_string = preg_replace('/\s+/', ' ', $json_string);

        return trim((string) $json_string);
    }
}

if (!function_exists('pf_schema_merge_graphs')) {
    /**
     * Merge multiple @graph arrays into a flat structure.
     *
     * @param array $graphs Collection of graph fragments.
     * @return array
     */
    function pf_schema_merge_graphs(array $graphs)
    {
        $merged  = array();
        $non_ids = array();

        foreach ($graphs as $graph) {
            if (empty($graph)) {
                continue;
            }

            $nodes = array();

            if (isset($graph['@graph']) && is_array($graph['@graph'])) {
                $nodes = $graph['@graph'];
            } elseif (array_keys($graph) === range(0, count($graph) - 1)) {
                $nodes = $graph;
            } elseif (is_array($graph)) {
                $nodes = array($graph);
            }

            foreach ($nodes as $node) {
                if (!is_array($node) || empty($node)) {
                    continue;
                }

                if (isset($node['@id'])) {
                    $id = (string) $node['@id'];
                    if (isset($merged[$id])) {
                        $merged[$id] = array_replace_recursive($merged[$id], $node);
                    } else {
                        $merged[$id] = $node;
                    }
                } else {
                    $non_ids[] = $node;
                }
            }
        }

        return array_merge(array_values($merged), $non_ids);
    }
}

if (!function_exists('pf_schema_availability')) {
    /**
     * Map stored availability to Schema.org canonical values.
     *
     * @param mixed $meta Stored availability meta value.
     * @return string
     */
    function pf_schema_availability($meta)
    {
        $value = strtolower(trim(is_scalar($meta) ? (string) $meta : ''));

        $map = array(
            'ready'          => 'InStock',
            'in_stock'       => 'InStock',
            'instock'        => 'InStock',
            'available'      => 'InStock',
            'preorder'       => 'PreOrder',
            'pre-order'      => 'PreOrder',
            'pre order'      => 'PreOrder',
            'po'             => 'PreOrder',
            'outofstock'     => 'OutOfStock',
            'out_of_stock'   => 'OutOfStock',
            'out-of-stock'   => 'OutOfStock',
            'out of stock'   => 'OutOfStock',
            'soldout'        => 'OutOfStock',
            'sold_out'       => 'OutOfStock',
            'sold-out'       => 'OutOfStock',
        );

        return isset($map[$value]) ? $map[$value] : 'PreOrder';
    }
}

if (!function_exists('pf_schema_build_contact_action')) {
    /**
     * Build ContactAction markup for WhatsApp.
     *
     * @param string $wa_number  WhatsApp number digits.
     * @param array  $utm_params Query parameters appended to wa.me link.
     * @return array
     */
    function pf_schema_build_contact_action($wa_number, array $utm_params)
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $wa_number);
        if ($digits === '') {
            return array();
        }

        if (strpos($digits, '0') === 0) {
            $digits = '62' . substr($digits, 1);
        }

        $params = array();
        foreach ($utm_params as $key => $value) {
            $sanitized_key = sanitize_key($key);
            if ($sanitized_key === '') {
                continue;
            }

            $params[$sanitized_key] = pf_schema_sanitize_text($value);
        }

        $url = 'https://wa.me/' . $digits;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        return array(
            '@type'          => 'ContactAction',
            'name'           => __('Chat via WhatsApp', 'putrafiber'),
            'target'         => array(
                '@type'          => 'EntryPoint',
                'urlTemplate'    => $url,
                'actionPlatform' => array(
                    'http://schema.org/DesktopWebPlatform',
                    'http://schema.org/MobileWebPlatform',
                    'http://schema.org/AndroidPlatform',
                    'http://schema.org/IOSPlatform',
                ),
                'inLanguage'     => 'id-ID',
            ),
        );
    }
}

if (!function_exists('pf_schema_detect_service_area')) {
    /**
     * Detect areaServed data for a given post.
     *
     * @param int $post_id Post ID.
     * @return array|string
     */
    function pf_schema_detect_service_area($post_id)
    {
        $post_id = absint($post_id);
        if ($post_id <= 0) {
            return array(
                array(
                    '@type' => 'Country',
                    'name'  => 'Indonesia',
                    'identifier' => 'ID',
                ),
            );
        }

        if (function_exists('putrafiber_get_service_area')) {
            $areas = putrafiber_get_service_area($post_id);
            if (!empty($areas)) {
                return $areas;
            }
        }

        $areas = array();
        $title = get_the_title($post_id);

        if ($title && function_exists('putrafiber_extract_cities_from_text')) {
            $cities = putrafiber_extract_cities_from_text($title);
            foreach ((array) $cities as $city) {
                $city = pf_schema_sanitize_text($city);
                if ($city === '') {
                    continue;
                }

                $areas[] = array(
                    '@type' => 'Place',
                    'name'  => $city,
                );
            }
        }

        if (empty($areas)) {
            $terms = get_the_terms($post_id, 'product_category');
            if (!$terms || is_wp_error($terms)) {
                $terms = get_the_terms($post_id, 'portfolio_category');
            }

            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $areas[] = array(
                        '@type' => 'Place',
                        'name'  => pf_schema_sanitize_text($term->name),
                    );
                }
            }
        }

        if (empty($areas)) {
            $areas[] = array(
                '@type'      => 'Country',
                'name'       => 'Indonesia',
                'identifier' => 'ID',
            );
        }

        return $areas;
    }
}

if (!function_exists('pf_schema_yes')) {
    /**
     * Determine whether Schema Advanced layer is enabled via options.
     *
     * @return bool
     */
    function pf_schema_yes()
    {
        $options = get_option('putrafiber_options', array());
        return !empty($options['enable_schema_advanced']);
    }
}
