<?php
/**
 * Single Portfolio Template
 * 
 * @package PutraFiber
 */

get_header();
?>

<main id="primary" class="site-main single-portfolio-page">
    <?php
    while (have_posts()):
        the_post();
        
        $location = get_post_meta(get_the_ID(), '_portfolio_location', true);
        $project_date = get_post_meta(get_the_ID(), '_portfolio_date', true);
        $client = get_post_meta(get_the_ID(), '_portfolio_client', true);
        $video_url = get_post_meta(get_the_ID(), '_portfolio_video', true);
        $gallery = get_post_meta(get_the_ID(), '_portfolio_gallery', true);
        ?>
        
        <article id="portfolio-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="portfolio-header">
                <div class="container">
                    <?php putrafiber_breadcrumbs(); ?>
                    
                    <div class="portfolio-header-content">
                        <div class="portfolio-info">
                            <h1 class="portfolio-title"><?php the_title(); ?></h1>
                            
                            <div class="portfolio-meta">
                                <?php if ($location): ?>
                                    <div class="meta-item">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        <span><?php echo esc_html($location); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($project_date): ?>
                                    <div class="meta-item">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span><?php echo date('F Y', strtotime($project_date)); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($client): ?>
                                    <div class="meta-item">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <span><?php echo esc_html($client); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php
                                $terms = get_the_terms(get_the_ID(), 'portfolio_category');
                                if ($terms && !is_wp_error($terms)):
                                ?>
                                    <div class="meta-item">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                        </svg>
                                        <span><?php echo esc_html($terms[0]->name); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="portfolio-actions">
                            <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya tertarik dengan project ' . get_the_title())); ?>" class="btn btn-primary" target="_blank" rel="noopener">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                <?php _e('Konsultasi Project Serupa', 'putrafiber'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <?php if (has_post_thumbnail()): ?>
                <div class="portfolio-featured-image">
                    <?php the_post_thumbnail('full'); ?>
                </div>
            <?php endif; ?>

            <div class="portfolio-content">
                <div class="container">
                    <div class="content-wrapper">
                        <?php the_content(); ?>
                        
                        <?php if ($gallery): ?>
                            <div class="portfolio-gallery">
                                <h3><?php _e('Project Gallery', 'putrafiber'); ?></h3>
                                <div class="gallery-grid">
                                    <?php
                                    $gallery_ids = explode(',', $gallery);
                                    foreach ($gallery_ids as $img_id):
                                        $image_url = wp_get_attachment_image_url($img_id, 'full');
                                        $image_thumb = wp_get_attachment_image_url($img_id, 'putrafiber-portfolio');
                                    ?>
                                        <a href="<?php echo esc_url($image_url); ?>" class="gallery-item" data-lightbox="portfolio-gallery">
                                            <img src="<?php echo esc_url($image_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($video_url): ?>
                            <div class="portfolio-video">
                                <h3><?php _e('Project Video', 'putrafiber'); ?></h3>
                                <div class="video-wrapper">
                                    <?php
                                    // YouTube or Vimeo embed
                                    if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                                        preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $video_url, $matches);
                                        $video_id = $matches[1] ?? '';
                                        if (!$video_id && strpos($video_url, 'youtu.be') !== false) {
                                            $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
                                        }
                                        echo '<iframe width="100%" height="450" src="https://www.youtube.com/embed/' . esc_attr($video_id) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
                                    } elseif (strpos($video_url, 'vimeo.com') !== false) {
                                        $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
                                        echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($video_id) . '" width="100%" height="450" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Related Portfolio -->
                    <?php
                    $terms = get_the_terms(get_the_ID(), 'portfolio_category');
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
                            'post__not_in' => array(get_the_ID()),
                            'posts_per_page' => 3,
                        );
                        
                        $related_query = new WP_Query($related_args);
                        
                        if ($related_query->have_posts()):
                        ?>
                            <div class="related-portfolio">
                                <h3><?php _e('Related Projects', 'putrafiber'); ?></h3>
                                <div class="grid grid-3">
                                    <?php
                                    while ($related_query->have_posts()):
                                        $related_query->the_post();
                                        $rel_location = get_post_meta(get_the_ID(), '_portfolio_location', true);
                                    ?>
                                        <article class="card portfolio-card">
                                            <?php if (has_post_thumbnail()): ?>
                                                <div class="portfolio-thumbnail">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php the_post_thumbnail('putrafiber-portfolio'); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <div class="portfolio-card-content">
                                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                                <?php if ($rel_location): ?>
                                                    <p class="location">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                            <circle cx="12" cy="10" r="3"></circle>
                                                        </svg>
                                                        <?php echo esc_html($rel_location); ?>
                                                    </p>
                                                <?php endif; ?>
                                                <a href="<?php the_permalink(); ?>" class="read-more"><?php _e('View Project', 'putrafiber'); ?> &rarr;</a>
                                            </div>
                                        </article>
                                    <?php
                                    endwhile;
                                    wp_reset_postdata();
                                    ?>
                                </div>
                            </div>
                        <?php
                        endif;
                    }
                    ?>
                </div>
            </div>
        </article>
        
    <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
