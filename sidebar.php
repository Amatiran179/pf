<?php
/**
 * Sidebar Template
 * 
 * @package PutraFiber
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar">
    <div class="sidebar-inner">
        
        <!-- Search Widget -->
        <div class="widget widget-search">
            <h3 class="widget-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <?php _e('Search', 'putrafiber'); ?>
            </h3>
            <?php get_search_form(); ?>
        </div>

        <!-- Recent Posts Widget -->
        <div class="widget widget-recent-posts">
            <h3 class="widget-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M2 12h20"></path>
                </svg>
                <?php _e('Recent Articles', 'putrafiber'); ?>
            </h3>
            <ul class="recent-posts-list">
                <?php
                $recent_posts = new WP_Query(array(
                    'posts_per_page' => 5,
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1
                ));

                if ($recent_posts->have_posts()):
                    while ($recent_posts->have_posts()):
                        $recent_posts->the_post();
                        ?>
                        <li class="recent-post-item">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="recent-post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="recent-post-content">
                                <h4 class="recent-post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <span class="recent-post-date">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?php echo get_the_date(); ?>
                                </span>
                            </div>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </ul>
        </div>

        <!-- Categories Widget -->
        <div class="widget widget-categories">
            <h3 class="widget-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <?php _e('Categories', 'putrafiber'); ?>
            </h3>
            <ul class="categories-list">
                <?php
                wp_list_categories(array(
                    'title_li' => '',
                    'show_count' => true,
                    'hide_empty' => true,
                ));
                ?>
            </ul>
        </div>

        <!-- Tags Widget -->
        <?php
        $tags = get_tags(array('orderby' => 'count', 'order' => 'DESC', 'number' => 20));
        if ($tags):
        ?>
            <div class="widget widget-tags">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    </svg>
                    <?php _e('Popular Tags', 'putrafiber'); ?>
                </h3>
                <div class="tags-cloud">
                    <?php
                    foreach ($tags as $tag):
                        ?>
                        <a href="<?php echo get_tag_link($tag->term_id); ?>" class="tag-item" title="<?php echo $tag->count . ' articles'; ?>">
                            <?php echo $tag->name; ?>
                        </a>
                        <?php
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- CTA Widget -->
        <div class="widget widget-cta">
            <div class="widget-cta-content">
                <div class="cta-icon">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h3><?php _e('Need Help?', 'putrafiber'); ?></h3>
                <p><?php _e('Konsultasikan kebutuhan project Anda dengan tim ahli kami', 'putrafiber'); ?></p>
                <a href="<?php echo esc_url(putrafiber_whatsapp_link()); ?>" class="btn btn-primary btn-block" target="_blank" rel="noopener">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <?php _e('Chat WhatsApp', 'putrafiber'); ?>
                </a>
            </div>
        </div>

        <?php dynamic_sidebar('sidebar-1'); ?>

    </div>
</aside>

<style>
/* Sidebar Styles */
.sidebar {
    background: var(--bg-white);
    padding: 30px;
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
}

.sidebar-inner {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.widget {
    padding: 25px;
    background: var(--bg-light);
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.widget-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-dark);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
}

/* Recent Posts */
.recent-posts-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-post-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
}

.recent-post-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.recent-post-thumbnail {
    flex-shrink: 0;
    width: 70px;
    height: 70px;
    overflow: hidden;
    border-radius: 8px;
}

.recent-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.recent-post-thumbnail:hover img {
    transform: scale(1.1);
}

.recent-post-content {
    flex: 1;
}

.recent-post-title {
    font-size: 15px;
    margin: 0 0 8px;
    line-height: 1.4;
}

.recent-post-title a {
    color: var(--text-dark);
    transition: var(--transition);
}

.recent-post-title a:hover {
    color: var(--primary-color);
}

.recent-post-date {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: var(--text-light);
}

/* Categories */
.categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.categories-list li {
    margin-bottom: 12px;
}

.categories-list li:last-child {
    margin-bottom: 0;
}

.categories-list a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background: var(--bg-white);
    border-radius: 8px;
    color: var(--text-dark);
    transition: var(--transition);
}

.categories-list a:hover {
    background: var(--primary-color);
    color: white;
}

.categories-list .count {
    background: var(--primary-color);
    color: white;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.categories-list a:hover .count {
    background: white;
    color: var(--primary-color);
}

/* Tags Cloud */
.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag-item {
    display: inline-block;
    padding: 6px 15px;
    background: var(--bg-white);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    font-size: 13px;
    color: var(--text-dark);
    transition: var(--transition);
}

.tag-item:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* CTA Widget */
.widget-cta {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    text-align: center;
}

.widget-cta .widget-title {
    border-bottom-color: rgba(255, 255, 255, 0.3);
    color: white;
}

.widget-cta-content {
    color: white;
}

.widget-cta .cta-icon {
    margin-bottom: 15px;
}

.widget-cta h3 {
    font-size: 22px;
    margin-bottom: 10px;
}

.widget-cta p {
    margin-bottom: 20px;
    opacity: 0.95;
}

.btn-block {
    display: flex;
    width: 100%;
    justify-content: center;
    background: white;
    color: var(--primary-color);
}

.btn-block:hover {
    background: var(--bg-light);
    color: var(--primary-dark);
}

@media (max-width: 991px) {
    .sidebar {
        margin-top: 40px;
    }
}
</style>