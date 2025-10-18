<?php
/**
 * Single Portfolio Template - ENHANCED WITH CTA SYSTEM & PRODUCT-STYLE GALLERY
 *
 * @package PutraFiber
 * @version 2.2.1 - Patched gallery logic for robust data handling
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<main id="primary" class="site-main single-portfolio-page">
    <?php
    while (have_posts()):
    the_post();
        
        $portfolio_id = get_the_ID();
        
        // Get meta values
        $cta_type = get_post_meta($portfolio_id, '_portfolio_cta_type', true) ?: 'detail';
        $location = get_post_meta($portfolio_id, '_portfolio_location', true);
        $project_date = get_post_meta($portfolio_id, '_portfolio_date', true);
        $completion_date = get_post_meta($portfolio_id, '_portfolio_completion_date', true);
        $client = get_post_meta($portfolio_id, '_portfolio_client', true);
        $project_value = get_post_meta($portfolio_id, '_portfolio_value', true);
        $project_duration = get_post_meta($portfolio_id, '_portfolio_duration', true);
        $project_size = get_post_meta($portfolio_id, '_portfolio_size', true);
        $project_type = get_post_meta($portfolio_id, '_portfolio_type', true);
        $services = get_post_meta($portfolio_id, '_portfolio_services', true);
        $materials = get_post_meta($portfolio_id, '_portfolio_materials', true);
        $team_size = get_post_meta($portfolio_id, '_portfolio_team_size', true);
        $challenges = get_post_meta($portfolio_id, '_portfolio_challenges', true);
        $solutions = get_post_meta($portfolio_id, '_portfolio_solutions', true);
        $video_url = get_post_meta($portfolio_id, '_portfolio_video', true);
        // Note: $gallery and $gallery_ids are now handled inside the gallery logic block below.
        
        // Format dates
        $start_date_formatted = $project_date ? date('F Y', strtotime($project_date)) : '';
        $completion_date_formatted = $completion_date ? date('F Y', strtotime($completion_date)) : '';
        ?>
        
        <article id="portfolio-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <section class="breadcrumbs-section">
                <div class="container">
                    <?php putrafiber_breadcrumbs(); ?>
                </div>
            </section>

            <section class="portfolio-main-section">
                <div class="container">
                    <div class="portfolio-layout">
                        
                        <div class="portfolio-gallery">
                            <?php
                            $portfolio_title = get_the_title();
                            $featured_id     = has_post_thumbnail() ? get_post_thumbnail_id($portfolio_id) : 0;

                            $gallery_meta    = get_post_meta($portfolio_id, '_portfolio_gallery', true);
                            $gallery_ids     = function_exists('putrafiber_extract_gallery_ids') ? putrafiber_extract_gallery_ids($gallery_meta) : array();

                            if ($featured_id) {
                                $gallery_ids = array_values(array_filter($gallery_ids, function ($attachment_id) use ($featured_id) {
                                    return (int) $attachment_id !== (int) $featured_id;
                                }));
                            }

                            $all_images = array();

                            if ($featured_id && function_exists('putrafiber_build_gallery_items')) {
                                $featured_items = putrafiber_build_gallery_items(array($featured_id), array(
                                    'image_size'   => 'putrafiber-portfolio',
                                    'thumb_size'   => 'thumbnail',
                                    'fallback_alt' => $portfolio_title,
                                ));
                                if (!empty($featured_items)) {
                                    $all_images[] = $featured_items[0];
                                }
                            }

                            if (!empty($gallery_ids) && function_exists('putrafiber_build_gallery_items')) {
                                $gallery_items = putrafiber_build_gallery_items($gallery_ids, array(
                                    'image_size'   => 'putrafiber-portfolio',
                                    'thumb_size'   => 'thumbnail',
                                    'fallback_alt' => $portfolio_title,
                                ));
                                if (!empty($gallery_items)) {
                                    $all_images   = array_merge($all_images, $gallery_items);
                                }
                            }

                            if (!empty($all_images)):
                                $gallery_group = 'pf-portfolio-' . $portfolio_id;
                            ?>
                                <?php
                                $no_zoom_anchor_style = 'transform: none !important; animation: none !important;';
                                $no_zoom_image_style  = $no_zoom_anchor_style . ' transition: opacity 0.3s ease !important; will-change: auto !important;';
                                ?>
                                <div class="gallery-container" data-gallery-group="<?php echo pf_output_attr($gallery_group); ?>">
                                    <div class="swiper portfolio-gallery-slider" data-gallery-group="<?php echo pf_output_attr($gallery_group); ?>">
                                        <div class="swiper-wrapper">
                                            <?php foreach ($all_images as $index => $image): ?>
                                                <div class="swiper-slide">
                                                    <a href="<?php echo pf_output_url($image['full']); ?>"
                                                       data-lightbox="portfolio-<?php echo $portfolio_id; ?>"
                                                       data-title="<?php echo pf_output_attr($image['alt']); ?>"
                                                       class="gallery-item"
                                                       data-gallery-group="<?php echo pf_output_attr($gallery_group); ?>"
                                                       data-gallery-index="<?php echo pf_output_attr($index); ?>"
                                                       style="<?php echo pf_output_attr($no_zoom_anchor_style); ?>">
                                                        <img src="<?php echo pf_output_url($image['url']); ?>"
                                                             alt="<?php echo pf_output_attr($image['alt']); ?>"
                                                             class="gallery-image"
                                                             style="<?php echo pf_output_attr($no_zoom_image_style); ?>"
                                                             <?php if (!empty($image['width']) && !empty($image['height'])): ?>
                                                                 width="<?php echo (int) $image['width']; ?>" height="<?php echo (int) $image['height']; ?>"
                                                             <?php endif; ?>
                                                             <?php echo $index === 0 ? 'fetchpriority="high"' : 'loading="lazy" fetchpriority="low"'; ?>
                                                             decoding="async">
                                                        <span class="zoom-icon">🔍</span>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <?php if (count($all_images) > 1): ?>
                                            <div class="swiper-button-next"></div>
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-pagination"></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (count($all_images) > 1): ?>
                                        <div class="swiper portfolio-gallery-thumbs">
                                            <div class="swiper-wrapper">
                                                <?php foreach ($all_images as $image):
                                                    $thumb_url = !empty($image['thumb']) ? $image['thumb'] : $image['url'];
                                                    if ($thumb_url):
                                                ?>
                                                    <div class="swiper-slide">
                                                        <img src="<?php echo pf_output_url($thumb_url); ?>"
                                                             alt="<?php echo pf_output_attr($image['alt']); ?>"
                                                             loading="lazy" decoding="async" fetchpriority="low">
                                                    </div>
                                                <?php 
                                                    endif;
                                                endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="no-portfolio-image">
                                    <img src="<?php echo defined('PUTRAFIBER_URI') ? PUTRAFIBER_URI : get_template_directory_uri(); ?>/assets/images/no-image.svg" alt="No Image" loading="lazy" decoding="async" fetchpriority="low">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="portfolio-info">
                            <div class="portfolio-meta-header">
                                <?php 
                                $terms = get_the_terms($portfolio_id, 'portfolio_category');
                                if ($terms && !is_wp_error($terms)): 
                                ?>
                                    <span class="portfolio-category">
                                        <a href="<?php echo get_term_link($terms[0]); ?>">
                                            <?php echo pf_output_html($terms[0]->name); ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($project_type): ?>
                                    <span class="portfolio-type"><?php echo pf_output_html($project_type); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <h1 class="portfolio-title"><?php the_title(); ?></h1>
                            
                            <?php if (get_the_excerpt()): ?>
                                <div class="portfolio-short-description">
                                    <p><?php echo get_the_excerpt(); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="project-status in-progress">
                                <span class="status-icon">✅</span>
                                <span class="status-text">Project Selesai</span>
                            </div>
                            
                            <?php if ($cta_type === 'detail'): ?>
                                <div class="portfolio-detail-cta">
                                    <a href="#project-details" class="btn-portfolio-detail-large">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        Lihat Detail Project
                                    </a>
                                    <p class="cta-note">Pelajari detail lengkap project ini</p>
                                </div>
                            <?php else: ?>
                                <div class="portfolio-whatsapp-cta">
                                    <a href="<?php echo putrafiber_whatsapp_link('Halo, saya tertarik dengan project ' . get_the_title() . ' dan ingin konsultasi project serupa'); ?>" 
                                       class="btn-whatsapp-cta"
                                       target="_blank"
                                       rel="nofollow noopener">
                                        <?php echo function_exists('putrafiber_get_svg_icon') ? putrafiber_get_svg_icon('whatsapp') : '💬'; ?>
                                        Konsultasi Project Serupa
                                    </a>
                                    <p class="cta-note">Hubungi kami via WhatsApp untuk konsultasi gratis</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($location || $client || $project_duration || $project_size || $team_size): ?>
                                <div class="portfolio-attributes">
                                    <h3>📊 Informasi Project</h3>
                                    <?php if ($location): ?>
                                        <div class="attribute-item">
                                            <strong>📍 Lokasi:</strong>
                                            <span><?php echo pf_output_html($location); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($client): ?>
                                        <div class="attribute-item">
                                            <strong>👤 Klien:</strong>
                                            <span><?php echo pf_output_html($client); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($project_duration): ?>
                                        <div class="attribute-item">
                                            <strong>⏱️ Durasi:</strong>
                                            <span><?php echo pf_output_html($project_duration); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($project_size): ?>
                                        <div class="attribute-item">
                                            <strong>📐 Luas Area:</strong>
                                            <span><?php echo pf_output_html($project_size); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($team_size): ?>
                                        <div class="attribute-item">
                                            <strong>👥 Tim:</strong>
                                            <span><?php echo pf_output_html($team_size); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($start_date_formatted && $completion_date_formatted): ?>
                                        <div class="attribute-item">
                                            <strong>📅 Periode:</strong>
                                            <span><?php echo pf_output_html($start_date_formatted); ?> - <?php echo pf_output_html($completion_date_formatted); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="portfolio-share">
                                <strong>Bagikan Project:</strong>
                                <div class="share-buttons">
                                    <?php
                                    $share_url = get_permalink();
                                    $share_title = get_the_title();
                                    ?>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>"
                                       target="_blank" class="share-btn facebook" title="Share on Facebook">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode($share_title); ?>"
                                       target="_blank" class="share-btn twitter" title="Share on Twitter">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($share_title . ' - ' . $share_url); ?>"
                                       target="_blank" class="share-btn whatsapp" title="Share on WhatsApp">
                                        <?php echo function_exists('putrafiber_get_svg_icon') ? putrafiber_get_svg_icon('whatsapp') : '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </section>

            <section id="project-details" class="project-details-section">
                <div class="container">
                    <div class="section-header">
                        <h2>📋 Detail Project</h2>
                        <p>Informasi lengkap tentang pelaksanaan project</p>
                    </div>
                    
                    <div class="project-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <?php if ($services || $materials): ?>
                    <div class="project-specs-grid">
                        <?php if ($services): ?>
                            <div class="specs-box">
                                <h3>🛠️ Layanan yang Diberikan</h3>
                                <ul>
                                    <?php
                                    $services_lines = explode("\n", $services);
                                    foreach ($services_lines as $line) {
                                        if (trim($line)) {
                                            echo '<li>' . pf_output_html(trim($line)) . '</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($materials): ?>
                            <div class="specs-box">
                                <h3>📦 Material yang Digunakan</h3>
                                <ul>
                                    <?php
                                    $materials_lines = explode("\n", $materials);
                                    foreach ($materials_lines as $line) {
                                        if (trim($line)) {
                                            echo '<li>' . pf_output_html(trim($line)) . '</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($challenges || $solutions): ?>
                        <div class="challenges-solutions-grid">
                            <?php if ($challenges): ?>
                                <div class="challenges-box">
                                    <h3>🚧 Tantangan yang Dihadapi</h3>
                                    <div class="content-box">
                                        <?php echo wpautop(pf_output_html($challenges)); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($solutions): ?>
                                <div class="solutions-box">
                                    <h3>💡 Solusi yang Diterapkan</h3>
                                    <div class="content-box">
                                        <?php echo wpautop(pf_output_html($solutions)); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php if (count($all_images) > 6): ?>
                <section class="portfolio-gallery-section">
                    <div class="container">
                        <div class="section-header">
                            <h2>🖼️ Gallery Project Lengkap</h2>
                            <p>Dokumentasi visual selama pengerjaan project</p>
                        </div>
                        
                        <div class="full-gallery-grid">
                            <?php foreach ($all_images as $image): ?>
                                <a href="<?php echo pf_output_url($image['full']); ?>" 
                                   class="gallery-item" 
                                   data-lightbox="portfolio-<?php echo $portfolio_id; ?>"
                                   data-title="<?php echo pf_output_attr($image['alt']); ?>">
                                    <img src="<?php echo pf_output_url($image['url']); ?>"
                                         alt="<?php echo pf_output_attr($image['alt']); ?>"
                                         loading="lazy" decoding="async" fetchpriority="low">
                                    <div class="gallery-overlay">
                                        <span class="zoom-icon">🔍</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($video_url): ?>
                <section class="portfolio-video-section">
                    <div class="container">
                        <div class="section-header">
                            <h2>🎥 Video Project</h2>
                            <p>Video dokumentasi project</p>
                        </div>
                        
                        <div class="video-wrapper">
                            <?php
                            // YouTube or Vimeo embed
                            if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                                preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $video_url, $matches);
                                $video_id = $matches[1] ?? '';
                                if (!$video_id && strpos($video_url, 'youtu.be') !== false) {
                                    $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
                                }
                                if ($video_id):
                                    echo '<div class="video-container"><iframe width="100%" height="450" src="https://www.youtube.com/embed/' . pf_output_attr($video_id) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>';
                                endif;
                            } elseif (strpos($video_url, 'vimeo.com') !== false) {
                                $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
                                if ($video_id):
                                    echo '<div class="video-container"><iframe src="https://player.vimeo.com/video/' . pf_output_attr($video_id) . '" width="100%" height="450" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>';
                                endif;
                            } else {
                                echo '<div class="video-container"><video controls width="100%"><source src="' . pf_output_url($video_url) . '" type="video/mp4">Your browser does not support the video tag.</video></div>';
                            }
                            ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <section class="portfolio-final-cta">
                <div class="container">
                    <div class="cta-box">
                        <h2>Tertarik dengan Project Serupa?</h2>
                        <p>Konsultasikan kebutuhan project Anda dengan tim ahli kami</p>
                        <a href="<?php echo putrafiber_whatsapp_link('Halo, saya tertarik dengan project ' . get_the_title() . ' dan ingin konsultasi project serupa'); ?>" 
                           class="btn-whatsapp-large"
                           target="_blank"
                           rel="nofollow noopener">
                            <?php echo function_exists('putrafiber_get_svg_icon') ? putrafiber_get_svg_icon('whatsapp') : '💬'; ?>
                            Konsultasi Gratis via WhatsApp
                        </a>
                    </div>
                </div>
            </section>

            <?php
            $terms = get_the_terms($portfolio_id, 'portfolio_category');
            if ($terms && !is_wp_error($terms)) {
                $term_ids = array();
                foreach ($terms as $term) {
                    $term_ids[] = $term->term_id;
                }
                
                $related_args = array(
                    'post_type' => 'portfolio',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'portfolio_category',
                            'field' => 'term_id',
                            'terms' => $term_ids
                        )
                    ),
                    'post__not_in' => array($portfolio_id),
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish'
                );
                
                $related_query = new WP_Query($related_args);
                
                if ($related_query->have_posts()):
                ?>
                    <section class="related-portfolio-section">
                        <div class="container">
                            <div class="section-header">
                                <h2>Project Lainnya</h2>
                                <p>Lihat project serupa yang telah kami kerjakan</p>
                            </div>
                            <div class="portfolio-grid">
                                <?php
                                while ($related_query->have_posts()):
                                    $related_query->the_post();
                                    $rel_location = get_post_meta(get_the_ID(), '_portfolio_location', true);
                                    $rel_type = get_post_meta(get_the_ID(), '_portfolio_type', true);
                                ?>
                                    <article class="portfolio-card">
                                        <?php if (has_post_thumbnail()): ?>
                                            <div class="portfolio-thumbnail">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('putrafiber-portfolio', array(
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async'
                                                    )); ?>
                                                </a>
                                                <?php if ($rel_type): ?>
                                                    <span class="portfolio-type-badge"><?php echo pf_output_html($rel_type); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="portfolio-card-content">
                                            <h3 class="portfolio-card-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            <?php if ($rel_location): ?>
                                                <p class="portfolio-card-location">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                        <circle cx="12" cy="10" r="3"></circle>
                                                    </svg>
                                                    <?php echo pf_output_html($rel_location); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (get_the_excerpt()): ?>
                                                <div class="portfolio-card-excerpt">
                                                    <?php echo get_the_excerpt(); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="portfolio-card-footer">
                                                <a href="<?php the_permalink(); ?>" class="btn-portfolio-detail">
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    </section>
                <?php
                endif;
            }
            ?>
            
        </article>
        
    <?php
    endwhile;
    ?>
</main>

<?php
// Output Portfolio Schema for SEO
if (function_exists('putrafiber_generate_portfolio_schema')) {
    echo putrafiber_generate_portfolio_schema($portfolio_id);
}

get_footer();