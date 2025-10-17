<?php
/**
 * Template part for displaying posts
 * 
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
    <?php if (has_post_thumbnail()): ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('putrafiber-thumb', array(
                    'loading' => 'lazy',
                    'decoding' => 'async'
                )); ?>
            </a>
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
            
            <?php
            $categories = get_the_category();
            if ($categories):
            ?>
                <span class="post-category">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <a href="<?php echo get_category_link($categories[0]->term_id); ?>">
                        <?php echo $categories[0]->name; ?>
                    </a>
                </span>
            <?php endif; ?>
        </div>
        
        <h2 class="post-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        
        <div class="post-excerpt">
            <?php the_excerpt(); ?>
        </div>
        
        <a href="<?php the_permalink(); ?>" class="read-more">
            <?php _e('Read More', 'putrafiber'); ?> &rarr;
        </a>
    </div>
</article>
