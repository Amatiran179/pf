/**
 * DEDICATED Portfolio Gallery with Swiper & Lightbox
 * VERSI FINAL YANG SUDAH DIPERBAIKI
 *
 * @package PutraFiber
 * @version 1.1.0 - Switched to window.load for better layout calculation.
 */

(function($) {
    'use strict';

    // Gunakan document.ready untuk inisialisasi lebih cepat setelah DOM siap
    $(document).ready(function() {

        // Hanya jalankan jika elemen slider portofolio ada di halaman
        if ($('.portfolio-gallery-slider').length === 0) {
            return; // Keluar jika tidak ada galeri portofolio
        }

        // Pastikan Swiper sudah dimuat
        if (typeof Swiper === 'undefined') {
            console.error('[PF Portfolio Gallery] Swiper library is not loaded.');
            return;
        }

        try {
            // Inisialisasi slider thumbnail terlebih dahulu (jika ada)
            let portfolioThumbs = null;
            const totalSlides = $('.portfolio-gallery-slider .swiper-slide').length;

            if ($('.portfolio-gallery-thumbs').length > 0 && totalSlides > 1) {
                portfolioThumbs = new Swiper('.portfolio-gallery-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                });
            }

            // Konfigurasi slider utama
            const portfolioMainConfig = {
                spaceBetween: 10,
                slidesPerView: 1,
                watchOverflow: true, // Nonaktifkan navigasi jika hanya ada 1 slide

                // Selector sederhana yang dijamin bekerja dalam konteks ini
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                keyboard: {
                    enabled: true,
                },
                grabCursor: true,
                on: {
                    init: function () {
                        // Beri sedikit waktu lalu update untuk memastikan layout benar
                        setTimeout(() => {
                            if (this && typeof this.update === 'function') {
                                this.update();
                            }
                        }, 150);
                    }
                }
            };

            // Hubungkan dengan thumbnail jika ada
            if (portfolioThumbs) {
                portfolioMainConfig.thumbs = {
                    swiper: portfolioThumbs,
                };
            }

            // Aktifkan loop hanya jika ada lebih dari 1 slide
            if (totalSlides > 1) {
                portfolioMainConfig.loop = true;
                // Anda bisa aktifkan autoplay jika mau di sini
                // portfolioMainConfig.autoplay = {
                //     delay: 4000,
                //     disableOnInteraction: false,
                // };
            }

            // Inisialisasi slider utama
            const portfolioMain = new Swiper('.portfolio-gallery-slider', portfolioMainConfig);

            console.log('✅ [PF Portfolio Gallery] Initialized successfully.');

        } catch (error) {
            console.error('[PF Portfolio Gallery] Initialization error:', error);
        }

        // Inisialisasi Lightbox
        if (typeof SimpleLightbox !== 'undefined') {
            new SimpleLightbox('[data-lightbox]', {
                captionsData: 'title',
                captionPosition: 'bottom',
            });
        }

    });

})(jQuery);
