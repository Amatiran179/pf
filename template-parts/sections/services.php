<?php
/**
 * Services Section
 *
 * @package PutraFiber
 */

$services_title = putrafiber_frontpage_text('services', 'title', __('Solusi Water Attraction Lengkap', 'putrafiber'));
$services_desc  = putrafiber_frontpage_text('services', 'description', __('Dari masterplan, fabrikasi, hingga instalasi turn-key untuk wahana air dan playground.', 'putrafiber'));

$services_default = array(
    array('title' => 'Masterplan Waterpark', 'description' => 'Perencanaan 360° mencakup flow pengunjung, wahana, hingga F&B zone.', 'icon' => 'compass'),
    array('title' => 'Fabrikasi Fiberglass', 'description' => 'Produksi wahana air, slider, dan permainan custom sesuai standar internasional.', 'icon' => 'gear'),
    array('title' => 'Instalasi & Commissioning', 'description' => 'Tim onsite memastikan pemasangan rapi, aman, dan siap operasi.', 'icon' => 'shield'),
    array('title' => 'Maintenance & Retrofit', 'description' => 'Layanan perawatan berkala dan upgrade wahana untuk memperpanjang umur investasi.', 'icon' => 'spark'),
    array('title' => 'Indoor Playground', 'description' => 'Desain tematik lengkap dengan softplay, trampoline, dan area edukasi.', 'icon' => 'wave'),
    array('title' => 'Water Adventure', 'description' => 'Wahana arus malas, kolam ombak, hingga river tubing untuk destinasi wisata.', 'icon' => 'drop'),
);

$services_items = putrafiber_frontpage_parse_repeater('front_services_items', $services_default);
?>

<section class="services-section section bg-light" id="services">
    <div class="container-wide">
        <div class="section-title fade-in">
            <h2><?php echo esc_html($services_title); ?></h2>
            <?php if ($services_desc): ?>
                <p><?php echo esc_html($services_desc); ?></p>
            <?php endif; ?>
        </div>

        <div class="grid grid-3 services-grid">
            <?php foreach ($services_items as $index => $service): ?>
                <div class="card service-card fade-in" style="animation-delay: <?php echo esc_attr($index * 0.1); ?>s;">
                    <div class="service-icon">
                        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?php echo putrafiber_frontpage_icon_svg($service['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </svg>
                    </div>
                    <h3><?php echo esc_html($service['title']); ?></h3>
                    <?php if (!empty($service['description'])): ?>
                        <p><?php echo esc_html($service['description']); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya tertarik dengan layanan ' . $service['title'])); ?>" class="service-link" target="_blank" rel="noopener">
                        <?php esc_html_e('Konsultasi Sekarang', 'putrafiber'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
