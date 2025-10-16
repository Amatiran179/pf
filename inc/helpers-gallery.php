<?php
/**
 * Gallery Helper Utilities
 *
 * Centralised helpers that normalise gallery meta values and build
 * attachment datasets for both front-end templates and the admin UI.
 *
 * @package PutraFiber
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('putrafiber_extract_gallery_ids')) {
    /**
     * Normalise mixed gallery meta values into a list of unique attachment IDs.
     *
     * The gallery field historically stored comma separated strings, but older
     * metaboxes (or third-party migrations) might have persisted JSON blobs or
     * associative arrays.  This helper flattens the structure while ignoring
     * invalid values.
     *
     * @param mixed $raw Gallery meta value (string|array|object).
     * @return int[] Sanitised list of attachment IDs.
     */
    function putrafiber_extract_gallery_ids($raw)
    {
        if (empty($raw) && '0' !== $raw) {
            return array();
        }

        $queue = array();

        if (is_string($raw)) {
            $raw = trim(wp_unslash($raw));
            if ($raw === '') {
                return array();
            }

            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded)) {
                $queue = is_array($decoded) ? $decoded : array($decoded);
            } else {
                $cleaned = str_replace(array('\n', '\r', '\t', ';', '|'), ',', $raw);
                $queue   = preg_split('/[\s,]+/', $cleaned);
            }
        } elseif (is_array($raw) || is_object($raw)) {
            $queue = (array) $raw;
        } else {
            $queue = array($raw);
        }

        $ids   = array();
        $seen  = 0;

        while (!empty($queue) && $seen < 1000) {
            $seen++;
            $item = array_shift($queue);

            if ($item === null || $item === '' || $item === false) {
                continue;
            }

            if (is_numeric($item)) {
                $ids[] = absint($item);
                continue;
            }

            if (is_string($item)) {
                $maybe = trim($item);
                if ($maybe === '') {
                    continue;
                }
                if (ctype_digit($maybe)) {
                    $ids[] = absint($maybe);
                    continue;
                }

                // Fallback: recursively split mixed strings ("123,456").
                $subparts = preg_split('/[\s,|]+/', $maybe);
                if (is_array($subparts) && count($subparts) > 1) {
                    foreach ($subparts as $part) {
                        if ($part !== '') {
                            $queue[] = $part;
                        }
                    }
                }
                continue;
            }

            if (is_array($item) || is_object($item)) {
                $item_arr = (array) $item;
                $keys     = array('id', 'ID', 'attachment_id', 0, 'value');
                $matched  = false;
                foreach ($keys as $key) {
                    if (isset($item_arr[$key]) && is_numeric($item_arr[$key])) {
                        $ids[]  = absint($item_arr[$key]);
                        $matched = true;
                        break;
                    }
                }

                if (!$matched) {
                    foreach ($item_arr as $value) {
                        $queue[] = $value;
                    }
                }
                continue;
            }
        }

        $ids = array_filter(array_unique(array_map('absint', $ids)));

        return array_values($ids);
    }
}

if (!function_exists('putrafiber_prepare_gallery_meta_value')) {
    /**
     * Convert gallery field input into a sanitised comma separated string.
     *
     * @param mixed $raw
     * @return string
     */
    function putrafiber_prepare_gallery_meta_value($raw)
    {
        $ids = putrafiber_extract_gallery_ids($raw);
        if (empty($ids)) {
            return '';
        }

        return implode(',', $ids);
    }
}

if (!function_exists('putrafiber_build_gallery_items')) {
    /**
     * Build a gallery data array that includes urls, alt text and dimensions.
     *
     * @param int[] $ids Attachment IDs.
     * @param array $args Optional arguments.
     *
     * @return array[]
     */
    function putrafiber_build_gallery_items($ids, $args = array())
    {
        $defaults = array(
            'image_size'    => 'large',
            'thumb_size'    => 'thumbnail',
            'fallback_alt'  => '',
            'exclude'       => array(),
            'skip_missing'  => true,
        );

        $args    = wp_parse_args($args, $defaults);
        $exclude = array_map('absint', (array) $args['exclude']);
        $items   = array();

        foreach ((array) $ids as $id) {
            $id = absint($id);
            if (!$id || ($exclude && in_array($id, $exclude, true))) {
                continue;
            }

            $image_url = wp_get_attachment_image_url($id, $args['image_size']);
            $full_url  = wp_get_attachment_image_url($id, 'full');

            if (!$image_url && !$full_url) {
                if ($args['skip_missing']) {
                    continue;
                }
            }

            $thumb_url = wp_get_attachment_image_url($id, $args['thumb_size']);
            $meta      = wp_get_attachment_image_src($id, $args['image_size']);

            $width  = $meta ? (int) $meta[1] : 0;
            $height = $meta ? (int) $meta[2] : 0;

            $alt = trim((string) get_post_meta($id, '_wp_attachment_image_alt', true));
            if ($alt === '' && $args['fallback_alt'] !== '') {
                $alt = $args['fallback_alt'];
            }
            if ($alt === '') {
                $alt = get_the_title($id);
            }

            $items[] = array(
                'id'     => $id,
                'url'    => $image_url ?: $full_url,
                'full'   => $full_url ?: $image_url,
                'thumb'  => $thumb_url ?: ($image_url ?: $full_url),
                'alt'    => $alt ?: __('Gallery image', 'putrafiber'),
                'width'  => $width,
                'height' => $height,
            );
        }

        return $items;
    }
}
