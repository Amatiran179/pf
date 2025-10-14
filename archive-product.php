<?php
/**
 * Product Archive Template
 * 
 * @package PutraFiber
 * @since 1.0.0
 */

get_header();
?>

<main class="product-archive-page">
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="header-content">
                <h1 class="page-title">
                    <?php
                    if (is_tax('product_category')) {
                        single_term_title();
                    } elseif (is_tax('product_tag')) {
                        echo 'Tag: ';
                        single_term_title();
                    } else {
                        echo 'Semua Produk';
                    }
                    ?>
                </h1>
                
                <?php if (is_tax()): ?>
                    <?php $term_description = term_description(); ?>
                    <?php if ($term_description): ?>
                        <div class="archive-description">
                            <?php echo $term_description; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="archive-subtitle">Katalog produk fiberglass berkualitas tinggi</p>
                <?php endif; ?>
            </div>
            
            <!-- Breadcrumbs -->
            <?php putrafiber_breadcrumbs(); ?>
        </div>
    </section>
    
    <!-- Products Grid -->
    <section class="products-archive-section">
        <div class="container">
            
            <!-- Filter & Sorting -->
            <div class="archive-controls">
                <div class="showing-results">
                    <span>Menampilkan <?php echo $wp_query->post_count; ?> dari <?php echo $wp_query->found_posts; ?> produk</span>
                </div>
                
                <div class="archive-filter">
                    <form method="get" class="filter-form">
                        <select name="orderby" onchange="this.form.submit()">
                            <option value="">Urutkan</option>
                            <option value="date" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'date'); ?>>Terbaru</option>
                            <option value="title" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'title'); ?>>Nama: A-Z</option>
                            <option value="title_desc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'title_desc'); ?>>Nama: Z-A</option>
                            <option value="popular" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'popular'); ?>>Populer</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <?php if (have_posts()): ?>
                <div class="products-grid">
                    <?php while (have_posts()): the_post(); ?>
                        <?php get_template_part('template-parts/content', 'product-card'); ?>
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <div class="archive-pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size'  => 2,
                        'prev_text' => '← Sebelumnya',
                        'next_text' => 'Selanjutnya →',
                    ));
                    ?>
                </div>
                
            <?php else: ?>
                <div class="no-products-found">
                    <div class="no-products-content">
                        <span class="no-products-icon">📦</span>
                        <h3>Produk Tidak Ditemukan</h3>
                        <p>Maaf, tidak ada produk yang tersedia saat ini.</p>
                        <a href="<?php echo home_url('/'); ?>" class="btn-primary">Kembali ke Beranda</a>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="archive-cta-section">
        <div class="container">
            <div class="cta-box">
                <h2>Butuh Produk Custom?</h2>
                <p>Kami menerima pembuatan produk fiberglass sesuai kebutuhan Anda</p>
                <a href="<?php echo putrafiber_whatsapp_link('Halo, saya ingin konsultasi produk custom'); ?>" 
                   class="btn-whatsapp-large" 
                   target="_blank">
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>
