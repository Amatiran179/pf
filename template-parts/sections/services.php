<?php
/**
 * Services Section
 * 
 * @package PutraFiber
 */

$services = array(
    array(
        'title' => 'Waterpark',
        'description' => 'Desain dan konstruksi waterpark lengkap dengan berbagai wahana air yang aman dan menyenangkan.',
        'icon' => '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>'
    ),
    array(
        'title' => 'Waterboom',
        'description' => 'Pembangunan waterboom dengan standar keamanan internasional dan desain menarik.',
        'icon' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>'
    ),
    array(
        'title' => 'Playground Indoor',
        'description' => 'Playground indoor dengan material fiberglass berkualitas, aman untuk anak-anak segala usia.',
        'icon' => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line><line x1="15" y1="9" x2="9" y2="15"></line>'
    ),
    array(
        'title' => 'Playground Outdoor',
        'description' => 'Playground outdoor tahan cuaca dengan berbagai permainan edukatif dan menyenangkan.',
        'icon' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>'
    ),
    array(
        'title' => 'Perosotan Fiberglass',
        'description' => 'Berbagai jenis perosotan fiberglass dari spiral, lurus, hingga custom design sesuai kebutuhan.',
        'icon' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>'
    ),
    array(
        'title' => 'Kolam Ikan Fiberglass',
        'description' => 'Kolam ikan fiberglass berbagai ukuran, tahan lama dan mudah perawatan.',
        'icon' => '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>'
    ),
    array(
        'title' => 'Sepeda Air Fiberglass',
        'description' => 'Sepeda air fiberglass dengan desain ergonomis dan safety features lengkap.',
        'icon' => '<circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>'
    ),
    array(
        'title' => 'Kolam Renang Fiberglass',
        'description' => 'Kolam renang fiberglass prefabrikasi, instalasi cepat dengan kualitas terjamin.',
        'icon' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>'
    ),
    array(
        'title' => 'Perahu Fiber & Canoe',
        'description' => 'Produksi perahu fiber dan canoe untuk wisata air, kuat dan tahan lama.',
        'icon' => '<path d="M2 16.1A5 5 0 0 1 5.9 20M2 12.05A9 9 0 0 1 9.95 20M2 8V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v2"></path>'
    ),
    array(
        'title' => 'Bak Sampah Fiberglass',
        'description' => 'Bak sampah fiberglass berbagai ukuran, anti karat dan mudah dibersihkan.',
        'icon' => '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>'
    ),
);
?>

<section class="services-section section bg-light" id="services">
    <div class="container-wide">
        <div class="section-title fade-in">
            <h2>Layanan Kami</h2>
            <p>Solusi lengkap untuk kebutuhan waterpark, playground, dan produk fiberglass berkualitas</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($services as $index => $service): ?>
                <div class="card service-card fade-in" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="service-icon">
                        <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?php echo $service['icon']; ?>
                        </svg>
                    </div>
                    <h3><?php echo esc_html($service['title']); ?></h3>
                    <p><?php echo esc_html($service['description']); ?></p>
                    <a href="<?php echo esc_url(putrafiber_whatsapp_link('Halo, saya tertarik dengan layanan ' . $service['title'])); ?>" class="service-link" target="_blank" rel="noopener">
                        Konsultasi Sekarang
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
