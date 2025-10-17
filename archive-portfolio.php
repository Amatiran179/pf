<?php
/**
 * Portfolio Archive Template
 * 
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<main id="primary" class="site-main portfolio-archive-page">
    <header class="page-header portfolio-archive-header">
        <div class="container">
            <?php putrafiber_breadcrumbs(); ?>
            
            <div class="archive-header-content">
                <h1 class="page-title"><?php _e('Our Portfolio', 'putrafiber'); ?></h1>
                <p class="page-description">
                    <?php _e('Lihat berbagai project waterpark, waterboom, dan playground yang telah kami selesaikan dengan sukses di berbagai lokasi.', 'putrafiber'); ?>
                </p>
            </div>
            
            <!-- Portfolio Filter -->
            <div class="portfolio-filters">
                <button class="filter-btn active" data-filter="*">
                    <?php _e('All Projects', 'putrafiber'); ?>
                </button>
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'portfolio_category',
                    'hide_empty' => true,
                ));
                
                if ($categories && !is_wp_error($categories)):
                    foreach ($categories as $category):
                        ?>
                        <button class="filter-btn" data-filter=".category-<?php echo esc_attr($category->slug); ?>">
                            <?php echo esc_html($category->name); ?>
                            <span class="count">(<?php echo $category->count; ?>)</span>
                        </button>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </header>

    <div class="portfolio-archive-content section">
        <div class="container-wide">
            <?php if (have_posts()): ?>
                
                <div class="portfolio-grid-archive">
                    <?php
                    while (have_posts()):
                        the_post();
                        
                        $location = get_post_meta(get_the_ID(), '_portfolio_location', true);
                        $project_date = get_post_meta(get_the_ID(), '_portfolio_date', true);
                        $client = get_post_meta(get_the_ID(), '_portfolio_client', true);
                        
                        // Get categories for filtering
                        $terms = get_the_terms(get_the_ID(), 'portfolio_category');
                        $term_classes = '';
                        if ($terms && !is_wp_error($terms)) {
                            foreach ($terms as $term) {
                                $term_classes .= ' category-' . $term->slug;
                            }
                        }
                    ?>
                        <article class="portfolio-archive-item fade-in<?php echo esc_attr($term_classes); ?>" data-category="<?php echo esc_attr($term_classes); ?>">
                            <div class="portfolio-archive-card">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="portfolio-archive-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('putrafiber-portfolio', array(
                                                'loading' => 'lazy',
                                                'decoding' => 'async'
                                            )); ?>
                                        </a>
                                        
                                        <?php if ($terms && !is_wp_error($terms)): ?>
                                            <div class="portfolio-categories">
                                                <?php foreach ($terms as $term): ?>
                                                    <span class="portfolio-category-badge">
                                                        <?php echo esc_html($term->name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="portfolio-overlay">
                                            <div class="portfolio-overlay-content">
                                                <a href="<?php the_permalink(); ?>" class="portfolio-view-btn">
                                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    <span><?php _e('View Details', 'putrafiber'); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="portfolio-archive-content">
                                    <h3 class="portfolio-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="portfolio-meta">
                                        <?php if ($location): ?>
                                            <span class="meta-item meta-location">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <?php echo esc_html($location); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($project_date): ?>
                                            <span class="meta-item meta-date">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                                <?php echo date('F Y', strtotime($project_date)); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($client): ?>
                                            <span class="meta-item meta-client">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                                <?php echo esc_html($client); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="portfolio-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="portfolio-read-more">
                                        <?php _e('View Project', 'putrafiber'); ?>
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
                <div class="portfolio-pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> ' . __('Previous', 'putrafiber'),
                        'next_text' => __('Next', 'putrafiber') . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    ));
                    ?>
                </div>
                
            <?php else: ?>
                
                <div class="no-portfolio-found">
                    <div class="no-results-icon">
                        <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <h2><?php _e('No Portfolio Found', 'putrafiber'); ?></h2>
                    <p><?php _e('We haven\'t added any portfolio projects yet. Please check back later.', 'putrafiber'); ?></p>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">
                        <?php _e('Back to Home', 'putrafiber'); ?>
                    </a>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
    
    <!-- CTA Section -->
    <section class="portfolio-cta-section">
        <div class="container">
            <div class="portfolio-cta-content">
                <h2><?php _e('Tertarik Membuat Project Serupa?', 'putrafiber'); ?></h2>
                <p><?php _e('Konsultasikan kebutuhan project waterpark atau playground Anda dengan tim ahli kami', 'putrafiber'); ?></p>
                <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya tertarik membuat project waterpark/playground')); ?>" class="btn btn-primary btn-lg" target="_blank" rel="noopener">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <?php _e('Konsultasi Gratis', 'putrafiber'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<style>
/* Portfolio Archive Styles */
.portfolio-archive-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 60px 0 40px;
}

.archive-header-content {
    text-align: center;
    margin-bottom: 40px;
}

.archive-header-content .page-title {
    font-size: 48px;
    font-weight: 800;
    margin-bottom: 15px;
    color: white;
}

.archive-header-content .page-description {
    font-size: 18px;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.95;
}

/* Portfolio Filters */
.portfolio-filters {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 10px 25px;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-btn:hover,
.filter-btn.active {
    background: white;
    color: var(--primary-color);
    border-color: white;
}

.filter-btn .count {
    font-size: 12px;
    opacity: 0.8;
}

/* Portfolio Grid Archive */
.portfolio-grid-archive {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.portfolio-archive-item {
    opacity: 0;
    transform: scale(0.9);
    transition: var(--transition);
}

.portfolio-archive-item.visible {
    opacity: 1;
    transform: scale(1);
}

.portfolio-archive-item.filtered-out {
    display: none;
}

.portfolio-archive-card {
    background: var(--bg-white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.portfolio-archive-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

/* Portfolio Archive Image */
.portfolio-archive-image {
    position: relative;
    height: 280px;
    overflow: hidden;
}

.portfolio-archive-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.portfolio-archive-card:hover .portfolio-archive-image img {
    transform: scale(1.15);
}

.portfolio-categories {
    position: absolute;
    top: 15px;
    left: 15px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    z-index: 2;
}

.portfolio-category-badge {
    background: var(--primary-color);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Portfolio Overlay */
.portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, transparent 50%);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.portfolio-archive-card:hover .portfolio-overlay {
    opacity: 1;
}

.portfolio-overlay-content {
    text-align: center;
}

.portfolio-view-btn {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    color: white;
    font-weight: 600;
    transition: var(--transition);
}

.portfolio-view-btn:hover {
    transform: scale(1.1);
}

.portfolio-view-btn svg {
    background: rgba(255, 255, 255, 0.2);
    padding: 15px;
    border-radius: 50%;
    backdrop-filter: blur(10px);
}

/* Portfolio Archive Content */
.portfolio-archive-content {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.portfolio-title {
    font-size: 22px;
    margin-bottom: 15px;
    line-height: 1.3;
}

.portfolio-title a {
    color: var(--text-dark);
    transition: var(--transition);
}

.portfolio-title a:hover {
    color: var(--primary-color);
}

.portfolio-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-light);
}

.portfolio-excerpt {
    color: var(--text-light);
    line-height: 1.7;
    margin-bottom: 20px;
    flex: 1;
}

.portfolio-read-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary-color);
    font-weight: 600;
    transition: var(--transition);
}

.portfolio-read-more:hover {
    gap: 12px;
}

/* Portfolio Pagination */
.portfolio-pagination {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.portfolio-pagination .pagination {
    display: flex;
    gap: 10px;
}

.portfolio-pagination .page-numbers {
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

.portfolio-pagination .page-numbers:hover,
.portfolio-pagination .page-numbers.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* No Portfolio Found */
.no-portfolio-found {
    text-align: center;
    padding: 80px 20px;
}

.no-results-icon {
    margin-bottom: 30px;
}

.no-portfolio-found h2 {
    font-size: 32px;
    margin-bottom: 15px;
    color: var(--text-dark);
}

.no-portfolio-found p {
    font-size: 18px;
    color: var(--text-light);
    margin-bottom: 30px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* Portfolio CTA Section */
.portfolio-cta-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    padding: 60px 0;
    margin-top: 60px;
}

.portfolio-cta-content {
    text-align: center;
    color: white;
}

.portfolio-cta-content h2 {
    font-size: 38px;
    font-weight: 700;
    margin-bottom: 15px;
    color: white;
}

.portfolio-cta-content p {
    font-size: 18px;
    margin-bottom: 30px;
    opacity: 0.95;
}

.portfolio-cta-content .btn {
    background: white;
    color: var(--primary-color);
}

.portfolio-cta-content .btn:hover {
    background: var(--bg-light);
    transform: translateY(-3px);
}

/* Responsive */
@media (max-width: 768px) {
    .archive-header-content .page-title {
        font-size: 36px;
    }
    
    .portfolio-filters {
        gap: 10px;
    }
    
    .filter-btn {
        padding: 8px 20px;
        font-size: 14px;
    }
    
    .portfolio-grid-archive {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .portfolio-cta-content h2 {
        font-size: 28px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Portfolio Filter Functionality
    $('.filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        if (filter === '*') {
            $('.portfolio-archive-item').removeClass('filtered-out').addClass('visible');
        } else {
            $('.portfolio-archive-item').each(function() {
                if ($(this).hasClass(filter.replace('.', ''))) {
                    $(this).removeClass('filtered-out').addClass('visible');
                } else {
                    $(this).addClass('filtered-out').removeClass('visible');
                }
            });
        }
    });
    
    // Show all items on load
    setTimeout(function() {
        $('.portfolio-archive-item').addClass('visible');
    }, 100);
});
</script>

<?php
get_footer();