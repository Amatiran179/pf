<?php
/**
 * CTA (Call To Action) Section
 *
 * @package PutraFiber
 */

$cta_title        = putrafiber_frontpage_text('cta', 'title', __('Siap Memulai Proyek Ikonik?', 'putrafiber'));
$cta_description  = putrafiber_frontpage_text('cta', 'description', __('Tim konsultan kami siap membantu menghitung kebutuhan, estimasi biaya, hingga timeline pembangunan.', 'putrafiber'));
$cta_primary_text = putrafiber_frontpage_text('cta', 'primary_text', __('Konsultasi Sekarang', 'putrafiber'));
$cta_primary_url  = putrafiber_get_option('front_cta_primary_url', putrafiber_whatsapp_link('Halo, saya ingin konsultasi project waterpark/playground'));
$cta_secondary_text = putrafiber_frontpage_text('cta', 'secondary_text', __('Download Company Profile', 'putrafiber'));
$cta_secondary_url  = putrafiber_get_option('front_cta_secondary_url', home_url('/company-profile.pdf'));
?>

<section class="cta-section section" id="cta">
    <div class="cta-background">
        <div class="cta-pattern"></div>
    </div>

    <div class="container">
        <div class="cta-content fade-in animate-zoom-in" style="--animation-delay: 0.2s;">
            <div class="cta-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>

            <h2><?php echo esc_html($cta_title); ?></h2>
            <?php if ($cta_description): ?>
                <p><?php echo esc_html($cta_description); ?></p>
            <?php endif; ?>

            <div class="cta-actions">
                <?php if ($cta_primary_text && $cta_primary_url): ?>
                    <a href="<?php echo esc_url($cta_primary_url); ?>" class="btn btn-primary btn-lg cta-btn" target="_blank" rel="noopener">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <?php echo esc_html($cta_primary_text); ?>
                    </a>
                <?php endif; ?>

                <?php if ($cta_secondary_text && $cta_secondary_url): ?>
                    <a href="<?php echo esc_url($cta_secondary_url); ?>" class="btn btn-outline btn-lg cta-btn-secondary" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <?php echo esc_html($cta_secondary_text); ?>
                    </a>
                <?php endif; ?>

                <div class="cta-contact-info">
                    <p>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <strong>Telepon:</strong> <?php echo esc_html(putrafiber_get_option('company_phone', '021-12345678')); ?>
                    </p>
                    <p>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <strong>Jam Kerja:</strong> <?php echo esc_html(putrafiber_get_option('business_hours', 'Senin - Sabtu: 08:00 - 17:00 WIB')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
