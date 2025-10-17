<?php
/**
 * Offline Page Template
 * 
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<main id="primary" class="site-main offline-page">
    <div class="container">
        <div class="offline-content">
            <div class="offline-icon">
                <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2">
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                    <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path>
                    <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path>
                    <path d="M10.71 5.05A16 16 0 0 1 22.58 9"></path>
                    <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <line x1="12" y1="20" x2="12.01" y2="20"></line>
                </svg>
            </div>
            
            <h1><?php _e('You are Offline', 'putrafiber'); ?></h1>
            <p><?php _e('It looks like you\'re not connected to the internet. Please check your connection and try again.', 'putrafiber'); ?></p>
            
            <button onclick="window.location.reload()" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                <?php _e('Try Again', 'putrafiber'); ?>
            </button>
        </div>
    </div>
</main>

<?php
get_footer();
