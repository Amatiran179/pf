<?php
/**
 * Product Archive Template
 *
 * @package PutraFiber
 * @version 1.1.0 - FIXED (Sorting form optimized for backend logic)
 * @since 1.0.0
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<main class="product-archive-page">

    <section class="page-header">
        <div class="container">
            <div class="header-content">
                <h1 class="page-title">
                    <?php
                    if (is_tax()) {
                        single_term_title();
                    } else {
                        post_type_archive_title();
                    }
                    ?>
                </h1>

                <?php if (is_tax() && term_description()): ?>
                    <div class="archive-description">
                        <?php echo term_description(); ?>
                    </div>
                <?php elseif (!is_tax()): ?>
                    <p class="archive-subtitle">Katalog produk fiberglass berkualitas tinggi</p>
                <?php endif; ?>
            </div>

            <?php putrafiber_breadcrumbs(); ?>
        </div>
    </section>

    <section class="products-archive-section">
        <div class="container">

            <div class="archive-controls">
                <div class="showing-results">
                    <span>Menampilkan <?php echo $wp_query->post_count; ?> dari <?php echo $wp_query->found_posts; ?> produk</span>
                </div>

                <div class="archive-filter">
                    <form method="get" class="filter-form">
                        <?php
                        // Perbaikan: Set default ke 'date' agar dropdown konsisten saat halaman pertama kali dimuat.
                        $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
                        ?>
                        <select name="orderby" onchange="this.form.submit()">
                            <option value="date" <?php selected($current_orderby, 'date'); ?>>Urutkan berdasarkan terbaru</option>
                            <option value="popularity" <?php selected($current_orderby, 'popularity'); ?>>Urutkan berdasarkan popularitas</option>
                            <option value="title_asc" <?php selected($current_orderby, 'title_asc'); ?>>Urutkan berdasarkan nama: A-Z</option>
                            <option value="title_desc" <?php selected($current_orderby, 'title_desc'); ?>>Urutkan berdasarkan nama: Z-A</option>
                        </select>
                        <?php
                        // Pertahankan query string yang ada (misal: kategori, pencarian) saat sorting.
                        // Hapus parameter 'paged' untuk kembali ke halaman pertama setelah sorting.
                        foreach ($_GET as $key => $value) {
                            if ('orderby' === $key || 'paged' === $key) continue;
                            echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr(stripslashes($value)) . '" />';
                        }
                        ?>
                        <noscript><button type="submit">Urutkan</button></noscript>
                    </form>
                </div>
            </div>

            <?php if (have_posts()): ?>
                <div class="products-grid">
                    <?php while (have_posts()): the_post(); ?>
                        <?php get_template_part('template-parts/content', 'product-card'); ?>
                    <?php endwhile; ?>
                </div>

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
                        <p>Maaf, tidak ada produk yang cocok dengan kriteria Anda.</p>
                        <a href="<?php echo get_post_type_archive_link('product'); ?>" class="btn-primary">Lihat Semua Produk</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <section class="archive-cta-section">
        <div class="container">
            <div class="cta-box">
                <h2>Butuh Produk Custom?</h2>
                <p>Kami menerima pembuatan produk fiberglass sesuai kebutuhan Anda</p>
                <a href="<?php echo putrafiber_whatsapp_link('Halo, saya ingin konsultasi produk custom'); ?>"
                   class="btn-whatsapp-large"
                   target="_blank" rel="noopener">
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
