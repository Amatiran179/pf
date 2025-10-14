<?php
/**
 * WebP Image Converter
 * 
 * @package PutraFiber
 */

if (!defined('ABSPATH')) exit;

/**
 * Convert Uploaded Images to WebP
 */
function putrafiber_convert_to_webp($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    
    if (!file_exists($file)) {
        return $metadata;
    }
    
    $info = pathinfo($file);
    $ext = strtolower($info['extension']);
    
    // Only convert jpg, jpeg, png
    if (!in_array($ext, array('jpg', 'jpeg', 'png'))) {
        return $metadata;
    }
    
    // Check if GD library supports WebP
    if (!function_exists('imagewebp')) {
        return $metadata;
    }
    
    $webp_file = $info['dirname'] . '/' . $info['filename'] . '.webp';
    
    // Don't convert if WebP already exists
    if (file_exists($webp_file)) {
        return $metadata;
    }
    
    // Create image resource
    if ($ext === 'png') {
        $image = imagecreatefrompng($file);
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    } else {
        $image = imagecreatefromjpeg($file);
    }
    
    if ($image) {
        // Convert to WebP with 85% quality
        imagewebp($image, $webp_file, 85);
        imagedestroy($image);
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'putrafiber_convert_to_webp', 10, 2);

/**
 * Serve WebP Images if Available
 */
function putrafiber_serve_webp_images($image, $attachment_id, $size, $icon) {
    $file = get_attached_file($attachment_id);
    
    if (!$file) {
        return $image;
    }
    
    $info = pathinfo($file);
    $webp_file = $info['dirname'] . '/' . $info['filename'] . '.webp';
    
    // Check if browser supports WebP
    $supports_webp = false;
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
        $supports_webp = true;
    }
    
    if ($supports_webp && file_exists($webp_file)) {
        $image[0] = str_replace($info['basename'], $info['filename'] . '.webp', $image[0]);
    }
    
    return $image;
}
add_filter('wp_get_attachment_image_src', 'putrafiber_serve_webp_images', 10, 4);

/**
 * Add WebP to Allowed Upload Types
 */
function putrafiber_add_webp_mime($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'putrafiber_add_webp_mime');

/**
 * Display WebP in Media Library
 */
function putrafiber_webp_display($result, $path) {
    if ($result === false) {
        $info = pathinfo($path);
        if (isset($info['extension']) && $info['extension'] === 'webp') {
            $result = array(
                'ext' => 'webp',
                'type' => 'image/webp',
                'proper_filename' => false
            );
        }
    }
    return $result;
}
add_filter('wp_check_filetype_and_ext', 'putrafiber_webp_display', 10, 2);
