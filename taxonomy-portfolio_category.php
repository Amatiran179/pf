<?php
/**
 * Portfolio Category Taxonomy Template
 * 
 * @package PutraFiber
 */

get_header();

$term = get_queried_object();
?>

<main id="primary" class="site-main portfolio-taxonomy-page">
    <header class="taxonomy-header">
        <div class="container">
            <?php putrafiber_breadcrumbs(); ?>
            
            <div class="taxonomy-header-content">
                <div class="taxonomy-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                </div>
                
                <h1 class="taxonomy-title">
                    <?php echo esc_html($term->name); ?>
                </h1>
                
                <?php if ($term->description): ?>
                    <div class="taxonomy-description">
                        <?php echo wp_kses_post($term->description); ?>
                    </div>
                <?php endif; ?>
                
                <div class="taxonomy-stats">
                    <span class="stat-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <strong><?php echo $term->count; ?></strong> 
                        <?php echo _n('Project', 'Projects', $term->count, 'putrafiber'); ?>
                    </span>
                </div>
            </div>
            
            <!-- Related Categories -->
            <?php
            $all_categories = get_terms(array(
                'taxonomy' => 'portfolio_category',
                'hide_empty' => true,
                'exclude' => array($term->term_id),
            ));
            
            if ($all_categories && !is_wp_error($all_categories)):
            ?>
                <div class="related-categories">
                    <h3><?php _e('Other Categories:', 'putrafiber'); ?></h3>
                    <div class="category-tags">
                        <a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="category-tag">
                            <?php _e('All Categories', 'putrafiber'); ?>
                        </a>
                        <?php foreach ($all_categories as $cat): ?>
                            <a href="<?php echo get_term_link($cat); ?>" class="category-tag">
                                <?php echo esc_html($cat->name); ?>
                                <span class="count">(<?php echo $cat->count; ?>)</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="taxonomy-content section">
        <div class="container-wide">
            <?php if (have_posts()): ?>
                
                <div class="portfolio-masonry-grid">
                    <?php
                    while (have_posts()):
                        the_post();
                        
                        $location = get_post_meta(get_the_ID(), '_portfolio_location', true);
                        $project_date = get_post_meta(get_the_ID(), '_portfolio_date', true);
                        $client = get_post_meta(get_the_ID(), '_portfolio_client', true);
                    ?>
                        <article class="portfolio-masonry-item fade-in">
                            <div class="masonry-card">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="masonry-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('putrafiber-portfolio'); ?>
                                        </a>
                                        
                                        <div class="masonry-overlay">
                                            <div class="overlay-content">
                                                <a href="<?php the_permalink(); ?>" class="view-project-btn">
                                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <path d="m21 21-4.35-4.35"></path>
                                                    </svg>
                                                </a>
                                                
                                                <?php if (get_post_meta(get_the_ID(), '_portfolio_video', true)): ?>
                                                    <a href="<?php echo esc_url(get_post_meta(get_the_ID(), '_portfolio_video', true)); ?>" class="view-video-btn" target="_blank" rel="noopener">
                                                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="masonry-content">
                                    <h3 class="masonry-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="masonry-meta">
                                        <?php if ($location): ?>
                                            <span class="meta-location">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <?php echo esc_html($location); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($project_date): ?>
                                            <span class="meta-date">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <?php echo date('F Y', strtotime($project_date)); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($client): ?>
                                        <div class="masonry-client">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <strong><?php _e('Client:', 'putrafiber'); ?></strong> <?php echo esc_html($client); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="masonry-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="masonry-link">
                                        <?php _e('View Details', 'putrafiber'); ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php
                    endwhile;
                    ?>
                </div>
                
                <!-- Pagination -->
                <div class="taxonomy-pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> ' . __('Previous', 'putrafiber'),
                        'next_text' => __('Next', 'putrafiber') . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    ));
                    ?>
                </div>
                
            <?php else: ?>
                
                <div class="no-taxonomy-posts">
                    <div class="no-posts-icon">
                        <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                        </svg>
                    </div>
                    <h2><?php _e('No Projects in This Category', 'putrafiber'); ?></h2>
                    <p><?php _e('We haven\'t added any projects to this category yet.', 'putrafiber'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="btn btn-primary">
                        <?php _e('View All Portfolio', 'putrafiber'); ?>
                    </a>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
/* Taxonomy Header */
.taxonomy-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 60px 0;
    border-bottom: 3px solid var(--primary-color);
}

.taxonomy-header-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.taxonomy-icon {
    margin-bottom: 20px;
}

.taxonomy-title {
    font-size: 48px;
    font-weight: 800;
    margin-bottom: 20px;
    color: var(--text-dark);
}

.taxonomy-description {
    font-size: 18px;
    color: var(--text-light);
    line-height: 1.8;
    margin-bottom: 30px;
}

.taxonomy-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
}

.taxonomy-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 25px;
    background: white;
    border-radius: 50px;
    box-shadow: var(--shadow-sm);
    font-size: 16px;
    color: var(--text-dark);
}

.taxonomy-stats .stat-item strong {
    color: var(--primary-color);
    font-size: 20px;
}

/* Related Categories */
.related-categories {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid var(--border-color);
}

.related-categories h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--text-dark);
    text-align: center;
}

.category-tags {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
}

.category-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 50px;
    color: var(--text-dark);
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
}

.category-tag:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.category-tag .count {
    opacity: 0.7;
    font-size: 12px;
}

/* Portfolio Masonry Grid */
.portfolio-masonry-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.portfolio-masonry-item {
    opacity: 0;
    transform: translateY(30px);
    transition: var(--transition);
}

.portfolio-masonry-item.visible {
    opacity: 1;
    transform: translateY(0);
}

.masonry-card {
    background: var(--bg-white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.masonry-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

/* Masonry Image */
.masonry-image {
    position: relative;
    height: 260px;
    overflow: hidden;
}

.masonry-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.masonry-card:hover .masonry-image img {
    transform: scale(1.2);
}

.masonry-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.masonry-card:hover .masonry-overlay {
    opacity: 1;
}

.overlay-content {
    display: flex;
    gap: 20px;
}

.view-project-btn,
.view-video-btn {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: var(--transition);
}

.view-project-btn:hover,
.view-video-btn:hover {
    background: var(--primary-color);
    transform: scale(1.15);
}

/* Masonry Content */
.masonry-content {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.masonry-title {
    font-size: 20px;
    margin-bottom: 15px;
    line-height: 1.3;
}

.masonry-title a {
    color: var(--text-dark);
    transition: var(--transition);
}

.masonry-title a:hover {
    color: var(--primary-color);
}

.masonry-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
    font-size: 13px;
}

.masonry-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--text-light);
}

.masonry-client {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-light);
    margin-bottom: 12px;
}

.masonry-client strong {
    color: var(--text-dark);
}

.masonry-excerpt {
    color: var(--text-light);
    line-height: 1.7;
    margin-bottom: 20px;
    font-size: 14px;
    flex: 1;
}

.masonry-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary-color);
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
}

.masonry-link:hover {
    gap: 12px;
}

/* Taxonomy Pagination */
.taxonomy-pagination {
    display: flex;
    justify-content: center;
}

.taxonomy-pagination .pagination {
    display: flex;
    gap: 10px;
}

.taxonomy-pagination .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 50px;
    height: 50px;
    padding: 0 20px;
    background: var(--bg-white);
    border: 2px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-dark);
    font-weight: 600;
    transition: var(--transition);
}

.taxonomy-pagination .page-numbers:hover,
.taxonomy-pagination .page-numbers.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* No Taxonomy Posts */
.no-taxonomy-posts {
    text-align: center;
    padding: 100px 20px;
}

.no-posts-icon {
    margin-bottom: 30px;
}

.no-taxonomy-posts h2 {
    font-size: 32px;
    margin-bottom: 15px;
    color: var(--text-dark);
}

.no-taxonomy-posts p {
    font-size: 18px;
    color: var(--text-light);
    margin-bottom: 30px;
}

/* Responsive */
@media (max-width: 768px) {
    .taxonomy-title {
        font-size: 36px;
    }
    
    .taxonomy-stats {
        flex-direction: column;
        align-items: center;
    }
    
    .portfolio-masonry-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .category-tags {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .taxonomy-title {
        font-size: 28px;
    }
    
    .taxonomy-description {
        font-size: 16px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Fade in items on scroll
    function checkFadeIn() {
        $('.portfolio-masonry-item').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('visible');
            }
        });
    }

    $(window).on('scroll resize', checkFadeIn);
    checkFadeIn();
});
</script>

<?php
get_footer();