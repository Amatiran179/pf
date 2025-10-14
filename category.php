<?php
/**
 * Category Archive Template
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

get_header();

$category = get_queried_object();
?>

<main id="primary" class="site-main category-archive">
    <header class="archive-header category-header">
        <div class="container">
            <?php putrafiber_breadcrumbs(); ?>
            
            <div class="category-header-content">
                <div class="category-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                </div>
                
                <h1 class="category-title">
                    <?php echo esc_html($category->name); ?>
                </h1>
                
                <?php if ($category->description): ?>
                    <div class="category-description">
                        <?php echo wp_kses_post($category->description); ?>
                    </div>
                <?php endif; ?>
                
                <div class="category-meta">
                    <span class="category-count">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <strong><?php echo $category->count; ?></strong> 
                        <?php echo _n('Article', 'Articles', $category->count, 'putrafiber'); ?>
                    </span>
                </div>
            </div>
            
            <!-- Category Navigation -->
            <?php
            $categories = get_categories(array(
                'hide_empty' => true,
                'exclude' => array($category->term_id),
                'number' => 10,
            ));
            
            if ($categories):
            ?>
                <div class="category-navigation">
                    <h3><?php _e('Other Categories:', 'putrafiber'); ?></h3>
                    <div class="category-tags">
                        <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="category-tag">
                            <?php _e('All Categories', 'putrafiber'); ?>
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?php echo get_category_link($cat->term_id); ?>" class="category-tag">
                                <?php echo esc_html($cat->name); ?>
                                <span class="count">(<?php echo $cat->count; ?>)</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="archive-content section">
        <div class="container">
            <div class="content-wrapper">
                <div class="posts-container">
                    <?php if (have_posts()): ?>
                        
                        <div class="posts-grid">
                            <?php
                            while (have_posts()):
                                the_post();
                            ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post-card fade-in'); ?>>
                                    <?php if (has_post_thumbnail()): ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('putrafiber-thumb'); ?>
                                            </a>
                                            
                                            <?php
                                            $post_categories = get_the_category();
                                            if ($post_categories):
                                            ?>
                                                <span class="post-category-badge">
                                                    <?php echo esc_html($post_categories[0]->name); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content">
                                        <div class="post-meta">
                                            <span class="post-date">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <?php echo get_the_date(); ?>
                                            </span>
                                            
                                            <span class="post-author">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                                <?php the_author(); ?>
                                            </span>
                                            
                                            <?php if (get_comments_number() > 0): ?>
                                                <span class="post-comments">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                    </svg>
                                                    <?php comments_number('0', '1', '%'); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <span class="reading-time">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                                </svg>
                                                <?php echo putrafiber_reading_time(); ?> min
                                            </span>
                                        </div>
                                        
                                        <h2 class="post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <div class="post-excerpt">
                                            <?php the_excerpt(); ?>
                                        </div>
                                        
                                        <?php
                                        $tags = get_the_tags();
                                        if ($tags):
                                        ?>
                                            <div class="post-tags-mini">
                                                <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                                    <a href="<?php echo get_tag_link($tag->term_id); ?>" class="tag-mini">
                                                        #<?php echo esc_html($tag->name); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a href="<?php the_permalink(); ?>" class="read-more">
                                            <?php _e('Read Article', 'putrafiber'); ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg>
                                        </a>
                                    </div>
                                </article>
                            <?php
                            endwhile;
                            ?>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="category-pagination">
                            <?php
                            the_posts_pagination(array(
                                'mid_size' => 2,
                                'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> ' . __('Previous', 'putrafiber'),
                                'next_text' => __('Next', 'putrafiber') . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                            ));
                            ?>
                        </div>
                        
                    <?php else: ?>
                        
                        <div class="no-posts-found">
                            <div class="no-posts-icon">
                                <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                                    <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                    <line x1="15" y1="9" x2="15.01" y2="9"></line>
                                </svg>
                            </div>
                            <h2><?php _e('No Posts in This Category', 'putrafiber'); ?></h2>
                            <p><?php _e('We haven\'t published any articles in this category yet.', 'putrafiber'); ?></p>
                            <a href="<?php echo home_url('/blog/'); ?>" class="btn btn-primary">
                                <?php _e('View All Articles', 'putrafiber'); ?>
                            </a>
                        </div>
                        
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <?php if (is_active_sidebar('sidebar-1')): ?>
                    <aside class="sidebar-container">
                        <?php get_sidebar(); ?>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Related Categories (Optional) -->
    <?php if ($categories && count($categories) > 0): ?>
        <section class="related-categories-section section bg-light">
            <div class="container">
                <div class="section-title">
                    <h2><?php _e('Explore More Topics', 'putrafiber'); ?></h2>
                    <p><?php _e('Discover other interesting categories', 'putrafiber'); ?></p>
                </div>
                
                <div class="categories-showcase grid grid-4">
                    <?php
                    $showcase_cats = array_slice($categories, 0, 4);
                    foreach ($showcase_cats as $cat):
                        $cat_image = get_term_meta($cat->term_id, 'category_image', true);
                    ?>
                        <div class="category-showcase-card card fade-in">
                            <?php if ($cat_image): ?>
                                <div class="category-showcase-image">
                                    <img src="<?php echo esc_url($cat_image); ?>" alt="<?php echo esc_attr($cat->name); ?>">
                                </div>
                            <?php else: ?>
                                <div class="category-showcase-icon">
                                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2">
                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <h3><?php echo esc_html($cat->name); ?></h3>
                            <?php if ($cat->description): ?>
                                <p><?php echo wp_trim_words($cat->description, 15); ?></p>
                            <?php endif; ?>
                            <p class="category-count"><?php echo $cat->count; ?> <?php echo _n('article', 'articles', $cat->count, 'putrafiber'); ?></p>
                            <a href="<?php echo get_category_link($cat->term_id); ?>" class="btn btn-outline btn-sm">
                                <?php _e('Explore', 'putrafiber'); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<style>
/* Category Archive Styles */
.category-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 60px 0;
    border-bottom: 3px solid var(--primary-color);
}

.category-header-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.category-icon {
    margin-bottom: 20px;
}

.category-title {
    font-size: 48px;
    font-weight: 800;
    margin-bottom: 20px;
    color: var(--text-dark);
}

.category-description {
    font-size: 18px;
    color: var(--text-light);
    line-height: 1.8;
    margin-bottom: 30px;
}

.category-meta {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
}

.category-count {    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 25px;
    background: white;
    border-radius: 50px;
    box-shadow: var(--shadow-sm);
    font-size: 16px;
    color: var(--text-dark);
}

.category-count strong {
    color: var(--primary-color);
    font-size: 20px;
}

/* Category Navigation */
.category-navigation {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid var(--border-color);
}

.category-navigation h3 {
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

/* Content Wrapper */
.content-wrapper {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 40px;
    margin-top: 40px;
}

/* Posts Grid */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.post-card {
    background: var(--bg-white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
    display: flex;
    flex-direction: column;
}

.post-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

/* Post Thumbnail */
.post-thumbnail {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.post-card:hover .post-thumbnail img {
    transform: scale(1.1);
}

.post-category-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: var(--primary-color);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Post Content */
.post-content {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 13px;
    color: var(--text-light);
}

.post-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.post-title {
    font-size: 20px;
    margin-bottom: 12px;
    line-height: 1.4;
}

.post-title a {
    color: var(--text-dark);
    transition: var(--transition);
}

.post-title a:hover {
    color: var(--primary-color);
}

.post-excerpt {
    color: var(--text-light);
    line-height: 1.7;
    margin-bottom: 15px;
    flex: 1;
}

.post-tags-mini {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
}

.tag-mini {
    font-size: 12px;
    color: var(--primary-color);
    background: rgba(0, 188, 212, 0.1);
    padding: 4px 12px;
    border-radius: 12px;
    transition: var(--transition);
}

.tag-mini:hover {
    background: var(--primary-color);
    color: white;
}

.read-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary-color);
    font-weight: 600;
    transition: var(--transition);
}

.read-more:hover {
    gap: 12px;
}

/* Pagination */
.category-pagination {
    display: flex;
    justify-content: center;
}

.category-pagination .pagination {
    display: flex;
    gap: 10px;
}

.category-pagination .page-numbers {
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

.category-pagination .page-numbers:hover,
.category-pagination .page-numbers.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* No Posts Found */
.no-posts-found {
    text-align: center;
    padding: 100px 20px;
}

.no-posts-icon {
    margin-bottom: 30px;
}

.no-posts-found h2 {
    font-size: 32px;
    margin-bottom: 15px;
    color: var(--text-dark);
}

.no-posts-found p {
    font-size: 18px;
    color: var(--text-light);
    margin-bottom: 30px;
}

/* Category Showcase */
.categories-showcase {
    margin-top: 40px;
}

.category-showcase-card {
    text-align: center;
    padding: 30px;
}

.category-showcase-image {
    width: 100%;
    height: 150px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
}

.category-showcase-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-showcase-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    background: rgba(0, 188, 212, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-showcase-card h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: var(--text-dark);
}

.category-showcase-card p {
    color: var(--text-light);
    margin-bottom: 15px;
    line-height: 1.6;
}

.category-count {
    font-weight: 600;
    color: var(--primary-color);
}

/* Responsive */
@media (max-width: 992px) {
    .content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .sidebar-container {
        order: 2;
    }
}

@media (max-width: 768px) {
    .category-title {
        font-size: 36px;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .category-meta {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 576px) {
    .category-title {
        font-size: 28px;
    }
    
    .category-description {
        font-size: 16px;
    }
}
</style>

<?php
get_footer();