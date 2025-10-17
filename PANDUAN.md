# Panduan Pemeliharaan & Pengembangan Tema PutraFiber

Dokumen ini berfungsi sebagai panduan untuk membersihkan, memelihara, dan mengembangkan tema PutraFiber Enterprise.

---

## 1. Pembersihan (Tindakan yang Harus Dilakukan)

Setelah menerapkan perbaikan terakhir, beberapa file menjadi tidak terpakai (redundant) dan aman untuk dihapus. Menghapus file-file ini akan membuat struktur tema lebih bersih dan mencegah kebingungan di masa depan.

### File yang Harus Dihapus:

1.  **`inc/enqueue.php`**
    *   **Alasan:** Semua logika untuk memuat aset (CSS & JavaScript) telah dipindahkan dan disatukan ke dalam file `functions.php`. File ini tidak lagi digunakan.

2.  **Skrip Galeri Lama (`assets/js/`)**
    *   `assets/js/portfolio-gallery.js`
    *   `assets/js/product-gallery.js`
    *   `assets/js/product-gallery2.js`
    *   **Alasan:** Ketiga file ini telah digantikan oleh satu skrip terpusat, `assets/js/gallery-unified.js`, yang menangani galeri produk dan portofolio.

3.  **File CSS Galeri Lama (`assets/css/`)**
    *   `assets/css/portfolio-gallery-fix.css`
    *   `assets/css/product-gallery-fix.css`
    *   **Alasan:** Kedua file ini telah digabungkan menjadi satu file yang lebih efisien, `assets/css/gallery-fix.css`. Pastikan file baru ini diimpor ke dalam file CSS utama Anda.

---

## 2. Penjelasan Perbaikan yang Telah Dilakukan

Berikut adalah ringkasan dari penyempurnaan utama yang telah diimplementasikan:

*   **Konsolidasi Aset:**
    *   Semua proses `wp_enqueue_script` dan `wp_enqueue_style` sekarang dikelola secara terpusat dari `functions.php`. Ini menghilangkan potensi pemuatan ganda dan memudahkan pengelolaan dependensi.
    *   Hanya satu skrip (`gallery-unified.js`) dan satu file CSS (`gallery-fix.css`) yang sekarang bertanggung jawab untuk semua galeri di frontend.

*   **Pembersihan Kode Redundan:**
    *   Blok JavaScript dan CSS inline yang besar di dalam file metabox (`inc/post-types/product.php` dan `inc/post-types/portfolio.php`) telah dihapus.
    *   Semua logika interaksi galeri di halaman admin (tambah, hapus, urutkan) sekarang ditangani secara eksklusif oleh `assets/js/admin.js`, membuatnya lebih mudah untuk diperbaiki dan dikembangkan.

*   **Optimalisasi Performa:**
    *   Fungsi `putrafiber_remove_query_strings` yang tidak lagi relevan telah dihapus, mengurangi pemrosesan yang tidak perlu.
    *   Aset pihak ketiga (Swiper, SimpleLightbox) sekarang hanya dimuat pada halaman yang benar-benar membutuhkannya.

*   **Peningkatan Fungsionalitas Admin:**
    *   Logika untuk menghapus dan mengurutkan item galeri di `admin.js` telah dibuat lebih generik, sehingga berfungsi dengan baik untuk galeri produk maupun portofolio.

---

## 3. Ide Pengembangan di Masa Depan

Tema ini memiliki fondasi yang sangat kuat. Berikut adalah beberapa ide untuk pengembangan lebih lanjut:

### Fungsionalitas & Pengalaman Pengguna

1.  **Builder Visual untuk Landing Page:**
    *   **Ide:** Ganti sistem pengaturan section di Theme Options dengan antarmuka drag-and-drop. Admin bisa menyusun ulang, menonaktifkan, atau bahkan menambahkan section baru secara visual.
    *   **Teknologi:** Bisa menggunakan library seperti `SortableJS` di halaman Theme Options atau membuat halaman khusus dengan React/Vue.

2.  **Sistem Preset Warna/Tema:**
    *   **Ide:** Izinkan admin untuk menyimpan beberapa kombinasi palet warna sebagai "preset" (misalnya, "Tema Biru Elektrik", "Tema Korporat Elegan"). Admin bisa beralih antar tema dengan satu klik.
    *   **Teknologi:** Simpan preset sebagai array di database. Saat preset dipilih, perbarui CSS variables di `:root` secara dinamis.

3.  **Integrasi Analytics yang Lebih Dalam:**
    *   **Ide:** Lacak data tambahan seperti perangkat (desktop/mobile), sistem operasi, dan negara pengunjung. Tambahkan fitur untuk mengekspor data analytics sebagai file CSV.
    *   **Teknologi:** Gunakan `$_SERVER['HTTP_USER_AGENT']` untuk parsing data perangkat dan GeoIP untuk data lokasi (memerlukan library atau layanan eksternal).

4.  **Widget Dashboard Tambahan:**
    *   **Ide:** Buat widget baru yang menampilkan "Konversi WhatsApp per Halaman" untuk melihat halaman mana yang paling efektif menghasilkan prospek.

### Pengembangan Berbasis Gutenberg

5.  **Komponen Blok Gutenberg Khusus:**
    *   **Ide:** Buat blok kustom untuk elemen yang sering digunakan seperti "Tombol CTA", "Kartu Layanan", atau "Grid Portofolio". Ini memungkinkan admin untuk membangun halaman lain dengan gaya yang konsisten seperti landing page.
    *   **Teknologi:** Gunakan `@wordpress/create-block` untuk memulai pengembangan blok.

### Internasionalisasi

6.  **Dukungan Multi-Bahasa Penuh:**
    *   **Ide:** Pastikan semua string di tema (termasuk di JavaScript dan Theme Options) dapat diterjemahkan. Sediakan file `.pot` yang lengkap.
    *   **Teknologi:** Gunakan fungsi `wp_localize_script` untuk menerjemahkan string di JS dan pastikan semua string di PHP menggunakan text domain `putrafiber`.

---

Dengan mengikuti panduan ini, tema PutraFiber akan tetap optimal, aman, dan siap untuk dikembangkan lebih lanjut sesuai kebutuhan bisnis.
