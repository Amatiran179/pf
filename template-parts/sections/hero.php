<?php
/**
 * Hero Section
 * 
 * @package PutraFiber
 */

$hero_title         = putrafiber_get_option('hero_title', 'Kontraktor Waterpark & Playground Fiberglass Terpercaya');
$hero_desc          = putrafiber_get_option('hero_description', 'Spesialis pembuatan waterpark, waterboom, playground indoor & outdoor, perosotan fiberglass, kolam renang, dan berbagai produk fiberglass berkualitas tinggi.');
$hero_image         = putrafiber_get_option('hero_image', '');
$hero_cta_text      = putrafiber_get_option('hero_cta_text', 'Konsultasi Gratis');
$hero_highlight     = putrafiber_get_option('hero_highlight', '20+ Tahun Menghadirkan Wahana Air Spektakuler');
$hero_secondary_txt = putrafiber_get_option('hero_secondary_cta', __('Lihat Portofolio', 'putrafiber'));
$hero_secondary_url = putrafiber_get_option('hero_secondary_url', function_exists('get_post_type_archive_link') ? get_post_type_archive_link('portfolio') : home_url('/'));

$features_default = array(
    array('title' => __('Garansi 5 Tahun', 'putrafiber'), 'description' => __('Jaminan kualitas dan layanan purna jual responsif.', 'putrafiber'), 'icon' => 'shield'),
    array('title' => __('Tim Berpengalaman', 'putrafiber'), 'description' => __('Insinyur fiberglass bersertifikat dan kru terlatih.', 'putrafiber'), 'icon' => 'trophy'),
    array('title' => __('Teknologi Mutakhir', 'putrafiber'), 'description' => __('Produksi modern dengan standar keamanan internasional.', 'putrafiber'), 'icon' => 'gear'),
);
$hero_badges = array_slice(putrafiber_frontpage_parse_repeater('front_features_items', $features_default), 0, 3);

$sections = function_exists('putrafiber_frontpage_sections') ? putrafiber_frontpage_sections() : array('services');
$scroll_target = '#services';
if (!empty($sections)) {
    foreach ($sections as $slug) {
        if ($slug !== 'hero') {
            $scroll_target = '#' . sanitize_title($slug);
            break;
        }
    }
}
?>

<section class="hero-section" id="hero" data-parallax-layer>
    <?php if ($hero_image): ?>
        <div class="hero-background" style="background-image: url('<?php echo esc_url($hero_image); ?>');">
            <div class="hero-overlay"></div>
        </div>
    <?php else: ?>
        <div class="hero-background hero-gradient"></div>
    <?php endif; ?>

    <div class="hero-ornaments" aria-hidden="true">
        <?php for ($i = 0; $i < 6; $i++):
            $particle_index = $i + 1;
            ?>
            <span class="hero-particle hero-particle-<?php echo (int) $particle_index; ?>"></span>
        <?php endfor; ?>
        <span class="hero-wave hero-wave--one"></span>
        <span class="hero-wave hero-wave--two"></span>
    </div>

    <div class="container">
        <div class="hero-content fade-in animate-rise" style="--animation-delay: 0.05s;">
            <?php if ($hero_highlight): ?>
                <span class="hero-highlight"><?php echo esc_html($hero_highlight); ?></span>
            <?php endif; ?>

            <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
            <p class="hero-description"><?php echo esc_html($hero_desc); ?></p>

            <div class="hero-actions">
                <a href="<?php echo esc_url(putrafiber_whatsapp_link()); ?>" class="btn btn-primary btn-lg" target="_blank" rel="noopener">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <?php echo esc_html($hero_cta_text); ?>
                </a>

                <?php if ($hero_secondary_txt && $hero_secondary_url): ?>
                    <a href="<?php echo esc_url($hero_secondary_url); ?>" class="btn btn-outline btn-lg scroll-to">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 16 16 12 12 8"></polyline>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <?php echo esc_html($hero_secondary_txt); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="hero-badges">
                <?php foreach ($hero_badges as $badge_index => $badge): ?>
                    <?php $badge_delay = number_format(0.18 + ($badge_index * 0.12), 2, '.', ''); ?>
                    <div class="hero-badge fade-in animate-tilt-in" style="--animation-delay: <?php echo esc_attr($badge_delay); ?>s;">
                        <?php if (!empty($badge['icon'])): ?>
                            <span class="hero-badge-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <?php echo putrafiber_frontpage_icon_svg($badge['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </svg>
                            </span>
                        <?php endif; ?>
                        <span class="hero-badge-title"><?php echo esc_html($badge['title']); ?></span>
                        <?php if (!empty($badge['description'])): ?>
                            <span class="hero-badge-desc"><?php echo esc_html($badge['description']); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <a href="<?php echo esc_url($scroll_target); ?>" class="hero-scroll-indicator scroll-to" aria-label="Scroll ke section berikutnya">
        <svg width="30" height="50" viewBox="0 0 30 50">
            <rect x="5" y="5" width="20" height="40" rx="10" stroke="currentColor" stroke-width="2" fill="none"/>
            <circle cx="15" cy="15" r="3" fill="currentColor">
                <animate attributeName="cy" from="15" to="35" dur="1.5s" repeatCount="indefinite"/>
            </circle>
        </svg>
    </a>
</section>
