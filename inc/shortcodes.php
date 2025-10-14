<?php
/**
 * Custom Shortcodes
 * 
 * Define custom shortcodes for the theme
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * ============================================================================
 * BUTTON SHORTCODE
 * ============================================================================
 * 
 * Usage: [button url="#" text="Click Me" style="primary" size="md" icon="yes"]
 */
function putrafiber_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '#',
        'text' => 'Button',
        'style' => 'primary', // primary, outline, secondary
        'size' => 'md', // sm, md, lg
        'icon' => 'no', // yes, no
        'target' => '_self', // _self, _blank
        'class' => '',
    ), $atts);
    
    $icon_html = '';
    if ($atts['icon'] === 'yes') {
        $icon_html = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
        </svg>';
    }
    
    $classes = array('btn', 'btn-' . $atts['style'], 'btn-' . $atts['size']);
    if ($atts['class']) {
        $classes[] = $atts['class'];
    }
    
    return sprintf(
        '<a href="%s" class="%s" target="%s">%s %s</a>',
        esc_url($atts['url']),
        esc_attr(implode(' ', $classes)),
        esc_attr($atts['target']),
        esc_html($atts['text']),
        $icon_html
    );
}
add_shortcode('button', 'putrafiber_button_shortcode');

/**
 * ============================================================================
 * WHATSAPP BUTTON SHORTCODE
 * ============================================================================
 * 
 * Usage: [whatsapp_button text="Chat with Us" message="Hello!"]
 */
function putrafiber_whatsapp_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text' => 'Chat WhatsApp',
        'message' => 'Halo, saya tertarik dengan layanan PutraFiber',
        'number' => '', // Optional custom number
    ), $atts);
    
    $number = $atts['number'] ? $atts['number'] : putrafiber_whatsapp_number();
    $url = 'https://wa.me/' . $number . '?text=' . urlencode($atts['message']);
    
    return sprintf(
        '<a href="%s" class="btn btn-primary whatsapp-btn" target="_blank" rel="noopener">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            %s
        </a>',
        esc_url($url),
        esc_html($atts['text'])
    );
}
add_shortcode('whatsapp_button', 'putrafiber_whatsapp_button_shortcode');

/**
 * ============================================================================
 * PORTFOLIO GRID SHORTCODE
 * ============================================================================
 * 
 * Usage: [portfolio_grid limit="6" category="waterpark" columns="3"]
 */
function putrafiber_portfolio_grid_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'category' => '',
        'columns' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    ), $atts);
    
    $args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    );
    
    if ($atts['category']) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'portfolio_category',
                'field' => 'slug',
                'terms' => $atts['category'],
            ),
        );
    }
    
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '<p>' . __('No portfolio items found.', 'putrafiber') . '</p>';
    }
    
    $grid_class = 'grid-' . intval($atts['columns']);
    
    ob_start();
    ?>
    <div class="portfolio-shortcode-grid grid <?php echo esc_attr($grid_class); ?>">
        <?php
        while ($query->have_posts()):
            $query->the_post();
            $location = get_post_meta(get_the_ID(), '_portfolio_location', true);
        ?>
            <div class="portfolio-item fade-in">
                <div class="card portfolio-card">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="portfolio-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('putrafiber-portfolio'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="portfolio-card-content">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if ($location): ?>
                            <p class="location">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <?php echo esc_html($location); ?>
                            </p>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            <?php _e('View Project', 'putrafiber'); ?> &rarr;
                        </a>
                    </div>
                </div>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('portfolio_grid', 'putrafiber_portfolio_grid_shortcode');

/**
 * ============================================================================
 * RECENT POSTS SHORTCODE
 * ============================================================================
 * 
 * Usage: [recent_posts limit="5" category="blog"]
 */
function putrafiber_recent_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 5,
        'category' => '',
        'show_image' => 'yes',
        'show_date' => 'yes',
    ), $atts);
    
    $args = array(
        'posts_per_page' => intval($atts['limit']),
        'post_status' => 'publish',
    );
    
    if ($atts['category']) {
        $args['category_name'] = $atts['category'];
    }
    
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '<p>' . __('No posts found.', 'putrafiber') . '</p>';
    }
    
    ob_start();
    ?>
    <div class="recent-posts-shortcode">
        <?php
        while ($query->have_posts()):
            $query->the_post();
        ?>            <div class="recent-post-item">
                <?php if ($atts['show_image'] === 'yes' && has_post_thumbnail()): ?>
                    <div class="recent-post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="recent-post-content">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php if ($atts['show_date'] === 'yes'): ?>
                        <span class="recent-post-date"><?php echo get_the_date(); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('recent_posts', 'putrafiber_recent_posts_shortcode');

/**
 * ============================================================================
 * CONTACT INFO SHORTCODE
 * ============================================================================
 * 
 * Usage: [contact_info type="phone"] or [contact_info type="email"] or [contact_info type="address"]
 */
function putrafiber_contact_info_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'phone', // phone, email, address, whatsapp
        'icon' => 'yes',
    ), $atts);
    
    $output = '';
    $icon_html = '';
    
    switch ($atts['type']) {
        case 'phone':
            $phone = putrafiber_get_option('company_phone', '');
            if ($atts['icon'] === 'yes') {
                $icon_html = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg> ';
            }
            $output = '<span class="contact-info contact-phone">' . $icon_html . '<a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a></span>';
            break;
            
        case 'email':
            $email = putrafiber_get_option('company_email', '');
            if ($atts['icon'] === 'yes') {
                $icon_html = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg> ';
            }
            $output = '<span class="contact-info contact-email">' . $icon_html . '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></span>';
            break;
            
        case 'address':
            $address = putrafiber_get_option('company_address', '');
            if ($atts['icon'] === 'yes') {
                $icon_html = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg> ';
            }
            $output = '<span class="contact-info contact-address">' . $icon_html . nl2br(esc_html($address)) . '</span>';
            break;
            
        case 'whatsapp':
            $whatsapp = putrafiber_whatsapp_number();
            if ($atts['icon'] === 'yes') {
                $icon_html = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg> ';
            }
            $output = '<span class="contact-info contact-whatsapp">' . $icon_html . '<a href="https://wa.me/' . esc_attr($whatsapp) . '" target="_blank" rel="noopener">' . esc_html($whatsapp) . '</a></span>';
            break;
    }
    
    return $output;
}
add_shortcode('contact_info', 'putrafiber_contact_info_shortcode');

/**
 * ============================================================================
 * ALERT BOX SHORTCODE
 * ============================================================================
 * 
 * Usage: [alert type="success"]Your message here[/alert]
 */
function putrafiber_alert_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'type' => 'info', // success, warning, danger, info, primary
        'dismissible' => 'no',
    ), $atts);
    
    $classes = array('alert', 'alert-' . $atts['type']);
    
    if ($atts['dismissible'] === 'yes') {
        $classes[] = 'alert-dismissible';
        $close_btn = '<button type="button" class="alert-close">&times;</button>';
    } else {
        $close_btn = '';
    }
    
    return sprintf(
        '<div class="%s">%s%s</div>',
        esc_attr(implode(' ', $classes)),
        do_shortcode($content),
        $close_btn
    );
}
add_shortcode('alert', 'putrafiber_alert_shortcode');

/**
 * ============================================================================
 * ACCORDION SHORTCODE
 * ============================================================================
 * 
 * Usage: 
 * [accordion]
 *   [accordion_item title="Question 1"]Answer 1[/accordion_item]
 *   [accordion_item title="Question 2"]Answer 2[/accordion_item]
 * [/accordion]
 */
function putrafiber_accordion_shortcode($atts, $content = null) {
    return '<div class="accordion">' . do_shortcode($content) . '</div>';
}
add_shortcode('accordion', 'putrafiber_accordion_shortcode');

function putrafiber_accordion_item_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'title' => 'Accordion Title',
        'open' => 'no',
    ), $atts);
    
    $open_class = $atts['open'] === 'yes' ? 'active' : '';
    
    return sprintf(
        '<div class="accordion-item %s">
            <div class="accordion-header">%s</div>
            <div class="accordion-content">%s</div>
        </div>',
        $open_class,
        esc_html($atts['title']),
        do_shortcode($content)
    );
}
add_shortcode('accordion_item', 'putrafiber_accordion_item_shortcode');

/**
 * ============================================================================
 * COLUMNS SHORTCODE
 * ============================================================================
 * 
 * Usage:
 * [row]
 *   [column size="6"]Content 1[/column]
 *   [column size="6"]Content 2[/column]
 * [/row]
 */
function putrafiber_row_shortcode($atts, $content = null) {
    return '<div class="row">' . do_shortcode($content) . '</div>';
}
add_shortcode('row', 'putrafiber_row_shortcode');

function putrafiber_column_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'size' => '12', // 1-12
        'class' => '',
    ), $atts);
    
    $classes = array('col', 'col-' . intval($atts['size']));
    if ($atts['class']) {
        $classes[] = $atts['class'];
    }
    
    return sprintf(
        '<div class="%s">%s</div>',
        esc_attr(implode(' ', $classes)),
        do_shortcode($content)
    );
}
add_shortcode('column', 'putrafiber_column_shortcode');

/**
 * ============================================================================
 * TESTIMONIAL SHORTCODE
 * ============================================================================
 * 
 * Usage: [testimonial name="John Doe" role="CEO" image="url"]Quote here[/testimonial]
 */
function putrafiber_testimonial_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'name' => '',
        'role' => '',
        'company' => '',
        'image' => '',
        'rating' => '5',
    ), $atts);
    
    $stars = '';
    for ($i = 0; $i < intval($atts['rating']); $i++) {
        $stars .= '★';
    }
    
    ob_start();
    ?>
    <div class="testimonial-box">
        <?php if ($atts['image']): ?>
            <div class="testimonial-image">
                <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
            </div>
        <?php endif; ?>
        <div class="testimonial-content">
            <div class="testimonial-rating"><?php echo $stars; ?></div>
            <blockquote><?php echo do_shortcode($content); ?></blockquote>
            <div class="testimonial-author">
                <strong><?php echo esc_html($atts['name']); ?></strong>
                <?php if ($atts['role']): ?>
                    <span><?php echo esc_html($atts['role']); ?></span>
                <?php endif; ?>
                <?php if ($atts['company']): ?>
                    <span><?php echo esc_html($atts['company']); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('testimonial', 'putrafiber_testimonial_shortcode');

/**
 * ============================================================================
 * ICON BOX SHORTCODE
 * ============================================================================
 * 
 * Usage: [icon_box icon="★" title="Feature Title"]Description here[/icon_box]
 */
function putrafiber_icon_box_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'icon' => '★',
        'title' => 'Title',
        'style' => 'default', // default, bordered, filled
    ), $atts);
    
    return sprintf(
        '<div class="icon-box icon-box-%s">
            <div class="icon-box-icon">%s</div>
            <h3 class="icon-box-title">%s</h3>
            <div class="icon-box-content">%s</div>
        </div>',
        esc_attr($atts['style']),
        $atts['icon'],
        esc_html($atts['title']),
        do_shortcode($content)
    );
}
add_shortcode('icon_box', 'putrafiber_icon_box_shortcode');

/**
 * ============================================================================
 * PRICING TABLE SHORTCODE
 * ============================================================================
 * 
 * Usage: [pricing_table title="Basic" price="1000000" period="bulan" features="Feature 1|Feature 2|Feature 3"]
 */
function putrafiber_pricing_table_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Plan',
        'price' => '0',
        'period' => 'bulan',
        'currency' => 'Rp',
        'features' => '',        'button_text' => 'Pilih Paket',
        'button_url' => '#',
        'featured' => 'no',
    ), $atts);
    
    $features = explode('|', $atts['features']);
    $featured_class = $atts['featured'] === 'yes' ? 'featured' : '';
    
    ob_start();
    ?>
    <div class="pricing-table <?php echo esc_attr($featured_class); ?>">
        <?php if ($atts['featured'] === 'yes'): ?>
            <span class="pricing-badge"><?php _e('Popular', 'putrafiber'); ?></span>
        <?php endif; ?>
        
        <div class="pricing-header">
            <h3 class="pricing-title"><?php echo esc_html($atts['title']); ?></h3>
            <div class="pricing-price">
                <span class="currency"><?php echo esc_html($atts['currency']); ?></span>
                <span class="amount"><?php echo number_format($atts['price'], 0, ',', '.'); ?></span>
                <span class="period">/<?php echo esc_html($atts['period']); ?></span>
            </div>
        </div>
        
        <div class="pricing-features">
            <ul>
                <?php foreach ($features as $feature): ?>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success-color)" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <?php echo esc_html(trim($feature)); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="pricing-footer">
            <a href="<?php echo esc_url($atts['button_url']); ?>" class="btn btn-primary btn-block">
                <?php echo esc_html($atts['button_text']); ?>
            </a>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('pricing_table', 'putrafiber_pricing_table_shortcode');

/**
 * ============================================================================
 * GOOGLE MAP SHORTCODE
 * ============================================================================
 * 
 * Usage: [google_map lat="-6.200000" lng="106.816666" zoom="15" height="400px"]
 */
function putrafiber_google_map_shortcode($atts) {
    $atts = shortcode_atts(array(
        'lat' => '-6.200000',
        'lng' => '106.816666',
        'zoom' => '15',
        'height' => '400px',
        'marker' => 'yes',
    ), $atts);
    
    $map_id = 'map-' . uniqid();
    
    ob_start();
    ?>
    <div id="<?php echo esc_attr($map_id); ?>" class="google-map-embed" style="height: <?php echo esc_attr($atts['height']); ?>;">
        <iframe 
            src="https://maps.google.com/maps?q=<?php echo esc_attr($atts['lat']); ?>,<?php echo esc_attr($atts['lng']); ?>&z=<?php echo esc_attr($atts['zoom']); ?>&output=embed"
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('google_map', 'putrafiber_google_map_shortcode');

/**
 * ============================================================================
 * VIDEO EMBED SHORTCODE
 * ============================================================================
 * 
 * Usage: [video url="https://www.youtube.com/watch?v=xxxxx" width="100%" height="450px"]
 */
function putrafiber_video_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'width' => '100%',
        'height' => '450px',
        'autoplay' => 'no',
    ), $atts);
    
    if (empty($atts['url'])) {
        return '<p>' . __('Please provide a video URL', 'putrafiber') . '</p>';
    }
    
    $autoplay = $atts['autoplay'] === 'yes' ? '?autoplay=1' : '';
    
    // YouTube
    if (strpos($atts['url'], 'youtube.com') !== false || strpos($atts['url'], 'youtu.be') !== false) {
        preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $atts['url'], $matches);
        $video_id = $matches[1] ?? '';
        
        if (!$video_id && strpos($atts['url'], 'youtu.be') !== false) {
            $video_id = substr(parse_url($atts['url'], PHP_URL_PATH), 1);
        }
        
        $embed_url = 'https://www.youtube.com/embed/' . $video_id . $autoplay;
    }
    // Vimeo
    elseif (strpos($atts['url'], 'vimeo.com') !== false) {
        $video_id = substr(parse_url($atts['url'], PHP_URL_PATH), 1);
        $embed_url = 'https://player.vimeo.com/video/' . $video_id . $autoplay;
    }
    else {
        return '<p>' . __('Unsupported video URL', 'putrafiber') . '</p>';
    }
    
    return sprintf(
        '<div class="video-embed-wrapper" style="position: relative; padding-bottom: 56.25%%; height: 0; overflow: hidden;">
            <iframe 
                src="%s" 
                style="position: absolute; top: 0; left: 0; width: 100%%; height: 100%%;" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen 
                loading="lazy">
            </iframe>
        </div>',
        esc_url($embed_url)
    );
}
add_shortcode('video', 'putrafiber_video_shortcode');

/**
 * ============================================================================
 * SOCIAL SHARE SHORTCODE
 * ============================================================================
 * 
 * Usage: [social_share]
 */
function putrafiber_social_share_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'default', // default, icons, buttons
        'networks' => 'facebook,twitter,whatsapp,linkedin', // comma separated
    ), $atts);
    
    $networks = explode(',', $atts['networks']);
    $url = get_permalink();
    $title = get_the_title();
    
    ob_start();
    ?>
    <div class="social-share-shortcode style-<?php echo esc_attr($atts['style']); ?>">
        <span class="share-label"><?php _e('Share:', 'putrafiber'); ?></span>
        
        <?php if (in_array('facebook', $networks)): ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>" 
               target="_blank" 
               rel="noopener" 
               class="share-facebook"
               title="<?php _e('Share on Facebook', 'putrafiber'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>
        <?php endif; ?>
        
        <?php if (in_array('twitter', $networks)): ?>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url); ?>&text=<?php echo urlencode($title); ?>" 
               target="_blank" 
               rel="noopener" 
               class="share-twitter"
               title="<?php _e('Share on Twitter', 'putrafiber'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
            </a>
        <?php endif; ?>
        
        <?php if (in_array('whatsapp', $networks)): ?>
            <a href="https://wa.me/?text=<?php echo urlencode($title . ' ' . $url); ?>" 
               target="_blank" 
               rel="noopener" 
               class="share-whatsapp"
               title="<?php _e('Share on WhatsApp', 'putrafiber'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </a>
        <?php endif; ?>
        
        <?php if (in_array('linkedin', $networks)): ?>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($url); ?>&title=<?php echo urlencode($title); ?>" 
               target="_blank" 
               rel="noopener" 
               class="share-linkedin"
               title="<?php _e('Share on LinkedIn', 'putrafiber'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('social_share', 'putrafiber_social_share_shortcode');

/**
 * ============================================================================
 * COUNTDOWN TIMER SHORTCODE
 * ============================================================================
 * 
 * Usage: [countdown date="2024-12-31 23:59:59" title="Grand Opening"]
 */
function putrafiber_countdown_shortcode($atts) {
    $atts = shortcode_atts(array(
        'date' => '',
        'title' => '',
        'format' => 'default', // default, minimal, compact
    ), $atts);
    
    if (empty($atts['date'])) {
        return '<p>' . __('Please provide a countdown date', 'putrafiber') . '</p>';
    }
    
    $countdown_id = 'countdown-' . uniqid();
    
    ob_start();
    ?>
    <div class="countdown-wrapper format-<?php echo esc_attr($atts['format']); ?>">
        <?php if ($atts['title']): ?>
            <h3 class="countdown-title"><?php echo esc_html($atts['title']); ?></h3>
        <?php endif; ?>
        
        <div id="<?php echo esc_attr($countdown_id); ?>" class="countdown-timer" data-date="<?php echo esc_attr($atts['date']); ?>">
            <div class="countdown-item">
                <span class="countdown-value days">0</span>
                <span class="countdown-label"><?php _e('Days', 'putrafiber'); ?></span>
            </div>
            <div class="countdown-item">
                <span class="countdown-value hours">0</span>
                <span class="countdown-label"><?php _e('Hours', 'putrafiber'); ?></span>
            </div>
            <div class="countdown-item">
                <span class="countdown-value minutes">0</span>
                <span class="countdown-label"><?php _e('Minutes', 'putrafiber'); ?></span>
            </div>
            <div class="countdown-item">
                <span class="countdown-value seconds">0</span>
                <span class="countdown-label"><?php _e('Seconds', 'putrafiber'); ?></span>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        var countdownEl = document.getElementById('<?php echo esc_js($countdown_id); ?>');
        var targetDate = new Date('<?php echo esc_js($atts['date']); ?>').getTime();
        
        function updateCountdown() {
            var now = new Date().getTime();
            var distance = targetDate - now;
            
            if (distance < 0) {
                countdownEl.innerHTML = '<p class="countdown-expired"><?php _e('Event has started!', 'putrafiber'); ?></p>';
                return;
            }
            
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            countdownEl.querySelector('.days').textContent = days;
            countdownEl.querySelector('.hours').textContent = hours;
            countdownEl.querySelector('.minutes').textContent = minutes;
            countdownEl.querySelector('.seconds').textContent = seconds;
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    })();
    </script>
    <?php
    
    return ob_get_clean();
}
add_shortcode('countdown', 'putrafiber_countdown_shortcode');

/**
 * ============================================================================
 * PROGRESS BAR SHORTCODE
 * ============================================================================
 * 
 * Usage: [progress_bar label="PHP" value="90" color="primary"]
 */
function putrafiber_progress_bar_shortcode($atts) {
    $atts = shortcode_atts(array(
        'label' => '',
        'value' => '0',
        'color' => 'primary', // primary, success, warning, danger
        'striped' => 'no',
        'animated' => 'no',
    ), $atts);
    
    $value = min(100, max(0, intval($atts['value'])));
    
    $classes = array('progress-bar', 'progress-bar-' . $atts['color']);
    
    if ($atts['striped'] === 'yes') {
        $classes[] = 'progress-bar-striped';
    }
    
    if ($atts['animated'] === 'yes') {
        $classes[] = 'progress-bar-animated';
    }
    
    return sprintf(
        '<div class="progress-wrapper">
            %s
            <div class="progress">
                <div class="%s" style="width: %d%%">
                    <span class="progress-value">%d%%</span>
                </div>
            </div>
        </div>',
        $atts['label'] ? '<label class="progress-label">' . esc_html($atts['label']) . '</label>' : '',
        esc_attr(implode(' ', $classes)),
        $value,
        $value
    );
}
add_shortcode('progress_bar', 'putrafiber_progress_bar_shortcode');

/**
 * ============================================================================
 * TEAM MEMBER SHORTCODE
 * ============================================================================
 * 
 * Usage: [team_member name="John Doe" role="CEO" image="url" facebook="#" twitter="#"]
 */
function putrafiber_team_member_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '',
        'role' => '',
        'image' => '',
        'bio' => '',
        'facebook' => '',
        'twitter' => '',
        'linkedin' => '',
        'instagram' => '',
        'email' => '',
    ), $atts);
    
    ob_start();
    ?>
    <div class="team-member-card">
        <?php if ($atts['image']): ?>
            <div class="team-member-image">
                <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
            </div>
        <?php endif; ?>
        
        <div class="team-member-content">
            <?php if ($atts['name']): ?>
                <h3 class="team-member-name"><?php echo esc_html($atts['name']); ?></h3>
            <?php endif; ?>
            
            <?php if ($atts['role']): ?>
                <p class="team-member-role"><?php echo esc_html($atts['role']); ?></p>
            <?php endif; ?>
            
            <?php if ($atts['bio']): ?>
                <p class="team-member-bio"><?php echo esc_html($atts['bio']); ?></p>
            <?php endif; ?>
            
            <div class="team-member-social">
                <?php if ($atts['facebook']): ?>
                    <a href="<?php echo esc_url($atts['facebook']); ?>" target="_blank" rel="noopener" class="social-facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($atts['twitter']): ?>
                    <a href="<?php echo esc_url($atts['twitter']); ?>" target="_blank" rel="noopener" class="social-twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($atts['linkedin']): ?>
                    <a href="<?php echo esc_url($atts['linkedin']); ?>" target="_blank" rel="noopener" class="social-linkedin">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($atts['instagram']): ?>
                    <a href="<?php echo esc_url($atts['instagram']); ?>" target="_blank" rel="noopener" class="social-instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($atts['email']): ?>
                    <a href="mailto:<?php echo esc_attr($atts['email']); ?>" class="social-email">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('team_member', 'putrafiber_team_member_shortcode');

/**
 * ============================================================================
 * STATISTICS/COUNTER SHORTCODE
 * ============================================================================
 * 
 * Usage: [stats number="500" label="Projects Completed" icon="✓" prefix="" suffix="+"]
 */
function putrafiber_stats_shortcode($atts) {
    $atts = shortcode_atts(array(
        'number' => '0',
        'label' => '',
        'icon' => '',
        'prefix' => '',
        'suffix' => '',
        'duration' => '2000',
        'color' => 'primary',
    ), $atts);
    
    $stats_id = 'stats-' . uniqid();
    
    ob_start();
    ?>
    <div class="stats-box color-<?php echo esc_attr($atts['color']); ?>">
        <?php if ($atts['icon']): ?>
            <div class="stats-icon"><?php echo $atts['icon']; ?></div>
        <?php endif; ?>
        
        <div class="stats-number" id="<?php echo esc_attr($stats_id); ?>" data-target="<?php echo esc_attr($atts['number']); ?>" data-duration="<?php echo esc_attr($atts['duration']); ?>">
            <?php echo esc_html($atts['prefix']); ?>
            <span class="counter">0</span>
            <?php echo esc_html($atts['suffix']); ?>
        </div>
        
        <?php if ($atts['label']): ?>
            <div class="stats-label"><?php echo esc_html($atts['label']); ?></div>
        <?php endif; ?>
    </div>
    
    <script>
    (function() {
        var statsEl = document.getElementById('<?php echo esc_js($stats_id); ?>');
        var target = parseInt(statsEl.dataset.target);
        var duration = parseInt(statsEl.dataset.duration);
        var counterEl = statsEl.querySelector('.counter');
        var started = false;
        
        function animateCounter() {
            if (started) return;
            started = true;
            
            var start = 0;
            var increment = target / (duration / 16);
            
            var timer = setInterval(function() {
                start += increment;
                if (start >= target) {
                    counterEl.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    counterEl.textContent = Math.floor(start).toLocaleString();
                }
            }, 16);
        }
        
        var observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                animateCounter();
                observer.disconnect();
            }
        });
        
        observer.observe(statsEl);
    })();
    </script>
    <?php
    
    return ob_get_clean();
}
add_shortcode('stats', 'putrafiber_stats_shortcode');

/**
 * ============================================================================
 * DIVIDER SHORTCODE
 * ============================================================================
 * 
 * Usage: [divider style="solid" height="2px" color="primary" margin="30px"]
 */
function putrafiber_divider_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'solid', // solid, dashed, dotted, double
        'height' => '1px',
        'color' => 'border',
        'margin' => '30px',
        'width' => '100%',
    ), $atts);
    
    $color_map = array(
        'primary' => 'var(--primary-color)',
        'secondary' => 'var(--secondary-color)',
        'border' => 'var(--border-color)',
        'dark' => 'var(--text-dark)',
        'light' => 'var(--text-light)',
    );
    
    $color = isset($color_map[$atts['color']]) ? $color_map[$atts['color']] : $atts['color'];
    
    return sprintf(
        '<hr class="divider" style="border: none; border-top: %s %s %s; margin: %s 0; width: %s;">',
        esc_attr($atts['height']),
        esc_attr($atts['style']),
        esc_attr($color),
        esc_attr($atts['margin']),
        esc_attr($atts['width'])
    );
}
add_shortcode('divider', 'putrafiber_divider_shortcode');

/**
 * ============================================================================
 * SPACER SHORTCODE
 * ============================================================================
 * 
 * Usage: [spacer height="50px"]
 */
function putrafiber_spacer_shortcode($atts) {
    $atts = shortcode_atts(array(
        'height' => '30px',
    ), $atts);
    
    return sprintf(
        '<div class="spacer" style="height: %s;"></div>',
        esc_attr($atts['height'])
    );
}
add_shortcode('spacer', 'putrafiber_spacer_shortcode');

/**
 * ============================================================================
 * HIGHLIGHT TEXT SHORTCODE
 * ============================================================================
 * 
 * Usage: [highlight color="yellow"]Text to highlight[/highlight]
 */
function putrafiber_highlight_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'color' => 'yellow',
        'text_color' => 'inherit',
    ), $atts);
    
    $bg_colors = array(
        'yellow' => '#FFEB3B',
        'green' => '#C8E6C9',
        'blue' => '#BBDEFB',
        'pink' => '#F8BBD0',
        'orange' => '#FFE0B2',
    );
    
    $bg_color = isset($bg_colors[$atts['color']]) ? $bg_colors[$atts['color']] : $atts['color'];
    
    return sprintf(
        '<mark class="highlight" style="background-color: %s; color: %s; padding: 2px 6px; border-radius: 3px;">%s</mark>',
        esc_attr($bg_color),
        esc_attr($atts['text_color']),
        do_shortcode($content)
    );
}
add_shortcode('highlight', 'putrafiber_highlight_shortcode');

/**
 * ============================================================================
 * TOOLTIP SHORTCODE
 * ============================================================================
 * 
 * Usage: [tooltip text="This is a tooltip"]Hover me[/tooltip]
 */
function putrafiber_tooltip_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'text' => '',
        'position' => 'top', // top, bottom, left, right
    ), $atts);
    
    return sprintf(
        '<span class="tooltip-wrapper" data-tooltip="%s" data-position="%s">%s</span>',
        esc_attr($atts['text']),
        esc_attr($atts['position']),
        do_shortcode($content)
    );
}
add_shortcode('tooltip', 'putrafiber_tooltip_shortcode');

/**
 * ============================================================================
 * YEAR SHORTCODE
 * ============================================================================
 * 
 * Usage: [year] - Outputs current year
 */
function putrafiber_year_shortcode() {
    return date('Y');
}
add_shortcode('year', 'putrafiber_year_shortcode');

/**
 * ============================================================================
 * SITE INFO SHORTCODES
 * ============================================================================
 * 
 * Usage: [site_name], [site_url], [site_description]
 */
function putrafiber_site_name_shortcode() {
    return get_bloginfo('name');
}
add_shortcode('site_name', 'putrafiber_site_name_shortcode');

function putrafiber_site_url_shortcode() {
    return home_url();
}
add_shortcode('site_url', 'putrafiber_site_url_shortcode');

function putrafiber_site_description_shortcode() {
    return get_bloginfo('description');
}
add_shortcode('site_description', 'putrafiber_site_description_shortcode');

/**
 * ============================================================================
 * CUSTOM HTML/CSS STYLING FOR SHORTCODES
 * ============================================================================
 */
function putrafiber_shortcodes_styles() {
    ?>
    <style>
    /* Shortcode Styles */
    .recent-posts-shortcode .recent-post-item {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .recent-posts-shortcode .recent-post-thumbnail {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .recent-posts-shortcode .recent-post-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .testimonial-box {
        background: var(--bg-light);
        padding: 30px;
        border-radius: 12px;
        border-left: 4px solid var(--primary-color);
    }
    
    .testimonial-rating {
        color: #FFD700;
        font-size: 20px;
        margin-bottom: 15px;
    }
    
    .icon-box {
        text-align: center;
        padding: 30px;
    }
    
    .icon-box-icon {
        font-size: 48px;
        color: var(--primary-color);
        margin-bottom: 20px;
    }
    
    .pricing-table {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 40px 30px;
        box-shadow: var(--shadow-md);
        text-align: center;
        position: relative;
        transition: var(--transition);
    }
    
    .pricing-table.featured {
        border: 3px solid var(--primary-color);
        transform: scale(1.05);
    }
    
    .pricing-badge {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--primary-color);
        color: white;
        padding: 5px 20px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .pricing-price {
        font-size: 48px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 20px 0;
    }
    
    .pricing-features ul {
        list-style: none;
        padding: 0;
        margin: 30px 0;
    }
    
    .pricing-features li {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
    }
    
    .countdown-timer {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .countdown-item {
        background: var(--bg-light);
        padding: 20px;
        border-radius: 12px;
        min-width: 100px;
        text-align: center;
    }
    
    .countdown-value {
        display: block;
        font-size: 36px;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .countdown-label {
        display: block;
        font-size: 14px;
        color: var(--text-light);
        margin-top: 5px;
    }
    
    .progress-wrapper {
        margin: 20px 0;
    }
    
    .progress-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .team-member-card {
        text-align: center;
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: var(--shadow-md);
    }
    
    .team-member-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 20px;
    }
    
    .team-member-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .team-member-social {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }
    
    .team-member-social a {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--bg-light);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }
    
    .team-member-social a:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-3px);
    }
    
    .stats-box {
        text-align: center;
        padding: 30px;
        background: var(--bg-white);
        border-radius: 12px;
        box-shadow: var(--shadow-md);
    }
    
    .stats-icon {
        font-size: 48px;
        color: var(--primary-color);
        margin-bottom: 15px;
    }
    
    .stats-number {
        font-size: 48px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
    }
    
    .stats-label {
        font-size: 16px;
        color: var(--text-light);
    }
    
    .social-share-shortcode {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .social-share-shortcode a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }
    
    .share-facebook { background: #1877F2; color: white; }
    .share-twitter { background: #1DA1F2; color: white; }
    .share-whatsapp { background: #25D366; color: white; }
    .share-linkedin { background: #0A66C2; color: white; }
    
    .social-share-shortcode a:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    
    .tooltip-wrapper {
        position: relative;
        cursor: help;
        border-bottom: 1px dotted var(--primary-color);
    }
    
    .tooltip-wrapper::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 8px 12px;
        background: var(--text-dark);
        color: white;
        font-size: 13px;
        border-radius: 6px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
        margin-bottom: 8px;
        z-index: 1000;
    }
    
    .tooltip-wrapper:hover::before {
        opacity: 1;
        visibility: visible;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .countdown-timer {
            gap: 10px;
        }
        
        .countdown-item {
            min-width: 70px;
            padding: 15px 10px;
        }
        
        .countdown-value {
            font-size: 28px;
        }
        
        .stats-number {
            font-size: 36px;
        }
        
        .pricing-table.featured {
            transform: scale(1);
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'putrafiber_shortcodes_styles');

/**
 * ============================================================================
 * SHORTCODE USAGE DOCUMENTATION
 * ============================================================================
 * 
 * AVAILABLE SHORTCODES:
 * 
 * 1. [button url="#" text="Click Me" style="primary" size="md" icon="yes"]
 * 2. [whatsapp_button text="Chat with Us" message="Hello!"]
 * 3. [portfolio_grid limit="6" category="waterpark" columns="3"]
 * 4. [recent_posts limit="5" category="blog" show_image="yes"]
 * 5. [contact_info type="phone" icon="yes"]
 * 6. [alert type="success"]Your message[/alert]
 * 7. [accordion]
 *      [accordion_item title="Question 1"]Answer 1[/accordion_item]
 *    [/accordion]
 * 8. [row]
 *      [column size="6"]Content 1[/column]
 *      [column size="6"]Content 2[/column]
 *    [/row]
 * 9. [testimonial name="John" role="CEO" image="url" rating="5"]Quote[/testimonial]
 * 10. [icon_box icon="★" title="Feature"]Description[/icon_box]
 * 11. [pricing_table title="Basic" price="1000000" period="bulan" features="Feature 1|Feature 2"]
 * 12. [google_map lat="-6.200000" lng="106.816666" zoom="15"]
 * 13. [video url="https://youtube.com/watch?v=xxxxx"]
 * 14. [social_share style="default" networks="facebook,twitter,whatsapp"]
 * 15. [countdown date="2024-12-31 23:59:59" title="Grand Opening"]
 * 16. [progress_bar label="PHP" value="90" color="primary"]
 * 17. [team_member name="John" role="CEO" image="url" facebook="#"]
 * 18. [stats number="500" label="Projects" icon="✓" suffix="+"]
 * 19. [divider style="solid" height="2px" color="primary"]
 * 20. [spacer height="50px"]
 * 21. [highlight color="yellow"]Text[/highlight]
 * 22. [tooltip text="Info"]Hover me[/tooltip]
 * 23. [year] - Current year
 * 24. [site_name] - Site name
 * 25. [site_url] - Site URL
 * 26. [site_description] - Site description
 * 
 * ============================================================================
 */