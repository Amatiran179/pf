<?php
/**
 * Features Section
 *
 * @package PutraFiber
 */

$features_title = putrafiber_frontpage_text('features', 'title', __('Kelebihan PutraFiber', 'putrafiber'));
$features_desc  = putrafiber_frontpage_text('features', 'description', __('Kami menggabungkan inovasi fiberglass dengan rekayasa konstruksi berkelas dunia.', 'putrafiber'));

$features_default = array(
    array('title' => 'Waterpark', 'description' => 'Desain dan konstruksi waterpark lengkap dengan berbagai wahana air yang aman dan menyenangkan.', 'icon' => 'wave'),
    array('title' => 'Waterboom', 'description' => 'Pembangunan waterboom dengan standar keamanan internasional dan desain menarik.', 'icon' => 'drop'),
    array('title' => 'Playground Indoor', 'description' => 'Playground indoor dengan material fiberglass berkualitas, aman untuk anak-anak segala usia.', 'icon' => 'spark'),
    array('title' => 'Playground Outdoor', 'description' => 'Playground outdoor tahan cuaca dengan berbagai permainan edukatif dan menyenangkan.', 'icon' => 'compass'),
    array('title' => 'Perosotan Fiberglass', 'description' => 'Berbagai jenis perosotan fiberglass dari spiral hingga custom design sesuai kebutuhan.', 'icon' => 'gear'),
    array('title' => 'Kolam Renang Fiberglass', 'description' => 'Kolam prefabrikasi tahan lama dengan instalasi cepat dan presisi.', 'icon' => 'shield'),
);

$features_items = putrafiber_frontpage_parse_repeater('front_features_items', $features_default);
?>

<section class="features-section section bg-light" id="features">
    <div class="container">
        <div class="section-title fade-in">
            <h2><?php echo esc_html($features_title); ?></h2>
            <?php if ($features_desc): ?>
                <p><?php echo esc_html($features_desc); ?></p>
            <?php endif; ?>
        </div>

        <div class="grid grid-3 feature-grid">
            <?php foreach ($features_items as $index => $feature): ?>
                <div class="card feature-card fade-in" style="animation-delay: <?php echo esc_attr($index * 0.08); ?>s;">
                    <div class="feature-icon">
                        <span class="feature-icon-circle">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <?php echo putrafiber_frontpage_icon_svg($feature['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </svg>
                        </span>
                    </div>
                    <h3><?php echo esc_html($feature['title']); ?></h3>
                    <?php if (!empty($feature['description'])): ?>
                        <p><?php echo esc_html($feature['description']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
