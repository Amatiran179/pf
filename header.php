<?php
/**
 * Header Template
 * 
 * @package PutraFiber
 * @version 1.2.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'putrafiber'); ?></a>

    <header id="masthead" class="site-header sticky-header">
        <div class="container-wide">
            <div class="header-inner">
                
                <!-- Logo -->
                <div class="site-branding">
                    <?php
                    $logo = get_theme_mod('putrafiber_logo');
                    if ($logo):
                    ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="custom-logo-link">
                            <img src="<?php echo esc_url($logo); ?>" alt="<?php bloginfo('name'); ?>" class="custom-logo">
                        </a>
                    <?php else: ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
                        </h1>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- Navigation Menu -->
                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav>

                <!-- Header Actions -->
                <div class="header-actions">
                    
                    <!-- Search Button -->
                    <button class="search-toggle" aria-label="Search">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>

                    <!-- Dark Mode Toggle -->
                    <button class="dark-mode-toggle" aria-label="Toggle Dark Mode">
                        <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        <svg class="moon-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </button>
                    
                    <!-- WhatsApp Button -->
                    <a href="<?php echo esc_url(putrafiber_whatsapp_link()); ?>" class="btn btn-primary header-cta" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>Konsultasi</span>
                    </a>
                </div>

            </div>
        </div>

    <!-- Search Overlay (Hidden by Default) -->
    <div class="search-overlay" style="display: none;">
        <div class="search-overlay-bg"></div>
        <div class="search-overlay-content">
            <button class="search-close" aria-label="Close Search">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            
            <div class="search-box">
                <h2>Cari Produk atau Layanan</h2>
                <?php get_search_form(); ?>
                
                <div class="search-suggestions">
                    <p>Pencarian populer:</p>
                    <div class="search-tags">
                        <a href="<?php echo esc_url(home_url('/?s=waterpark')); ?>">Waterpark</a>
                        <a href="<?php echo esc_url(home_url('/?s=waterboom')); ?>">Waterboom</a>
                        <a href="<?php echo esc_url(home_url('/?s=playground')); ?>">Playground</a>
                        <a href="<?php echo esc_url(home_url('/?s=fiberglass')); ?>">Fiberglass</a>
                        <a href="<?php echo esc_url(home_url('/?s=seluncuran')); ?>">Seluncuran</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
    <div id="content" class="site-content">

    