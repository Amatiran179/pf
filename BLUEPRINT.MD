# BLUEPRINT Theme Guide

## Overview
BLUEPRINT adalah tema WordPress premium yang dirancang khusus untuk kebutuhan perusahaan konstruksi fiberglass seperti PutraFiber. Tema ini menggabungkan estetika modern bernuansa biru elektrik dan aksen emas dengan fondasi teknis yang kuat mencakup kustomisasi konten yang luas, performa tinggi, dan fitur SEO terintegrasi. Dokumen ini memandu Anda memahami arsitektur tema, fitur utama, cara konfigurasi, hingga rekomendasi pengembangan selanjutnya.

## Tujuan & Filosofi Desain
- **Brand Consistency**: Mempertahankan identitas korporat lewat tipografi Poppins, palet biru–emas, dan elemen glassmorphism untuk setiap komponen UI.
- **Konversi Tinggi**: Seksi hero interaktif, kartu layanan, CTA ganda, dan jalur kontak cepat (WhatsApp) memastikan funnel penjualan singkat.
- **Maintainability**: Struktur file modular di `inc/`, `assets/css`, dan `assets/js` memudahkan penambahan fitur tanpa merusak kompatibilitas.
- **Performance & SEO**: Integrasi schema.org otomatis, caching PWA, konversi WebP, dan lazyload memastikan kecepatan sekaligus visibilitas mesin pencari.

## Struktur Folder Penting
- `style.css` – Deklarasi tema, CSS global, variabel warna/typografi.
- `functions.php` – Pusat bootstrap tema: enqueue aset, registrasi opsi, integrasi Vite manifest, dan fallback.
- `inc/` – Modul PHP terpisah (schema, analytics, helper front page, post type, PWA, dsb.).
  - `inc/post-types/` – Definisi CPT **Product** dan **Portfolio**, metabox, galeri, dan SEO meta.
  - `inc/analytics.php` – Mesin statistik kunjungan & klik WhatsApp dengan rate limiting.
  - `inc/schema/` – Generator JSON-LD (Organization, WebSite, WebPage, Product, Article, LocalBusiness, dsb.).
  - `inc/performance.php` – Optimasi: penghapusan query strings, pengaturan lazyload, WebP, ukuran gambar.
  - `inc/pwa.php` – Loader manifest & service worker.
- `assets/css/` – Stylesheet terpisah untuk landing page, produk, portofolio, komponen header/footer, admin editor.
- `assets/js/` – Skrip front end (main interaction, galeri unified, PWA register) dan admin (drag/drop galeri, reset analytics).
- `template-parts/` – Partial layout untuk section landing (hero, layanan, produk, portofolio, CTA, blog manual).

## Fitur Utama & Cara Menggunakannya

### 1. Landing Page Kustom
- **Hero Parallax**: Diatur melalui Theme Options → Landing Page. Upload background hero, copywriting, ikon badge, dan tombol CTA ganda.
- **Section Modular**: Aktif/nonaktifkan atau susun ulang section (Layanan, Produk, Portofolio, Testimoni, Blog) via Theme Options.
- **Kartu Konten**: Setiap kartu menggunakan glassmorphism dengan animasi hover. Gambar dapat dikonversi otomatis ke WebP.
- **Dark Mode Toggle**: Pengunjung dapat berpindah ke tema gelap, memanfaatkan variabel CSS di `style.css` yang telah disesuaikan.

**Langkah konfigurasi**
1. Buka **Appearance → Theme Options → Landing Page**.
2. Atur urutan section, unggah gambar hero, dan isi copywriting.
3. Lengkapi daftar layanan & produk unggulan dengan ikon, deskripsi singkat, dan tautan CTA.
4. Simpan pengaturan; tampilan front page akan menyesuaikan otomatis.

### 2. Custom Post Type (CPT) Product & Portfolio
- **Galeri Gambar Sortable**: Menggunakan `gallery-unified.js` untuk memuat slider Swiper tanpa efek zoom liar.
- **Lampiran Dokumen**: Setiap produk dapat menyertakan PDF brosur dan link CTA tambahan.
- **Meta SEO Kustom**: Tersedia meta box judul & deskripsi dengan hitung karakter real-time.

**Langkah pengelolaan**
1. Navigasi ke **Products** atau **Portfolio** di dashboard.
2. Tambah entri baru; isi detail utama, upload galeri (drag & drop untuk mengurutkan).
3. Masukkan meta SEO serta toggle schema jika diperlukan.
4. Publikasikan; halaman single otomatis menampilkan slider, tab detail, dan CTA.

### 3. Sistem Analitik & Pelacakan WhatsApp
- **Statistik Dashboard**: Widget menampilkan total kunjungan, halaman teratas, referrer utama, dan klik WhatsApp.
- **Reset Aman**: Tombol reset memanfaatkan AJAX dengan nonce agar hanya admin berwenang yang dapat mengosongkan data.
- **Rate Limiting**: Endpoint klik WhatsApp dilindungi limit 30 klik/menit per fingerprint.

**Langkah penggunaan**
1. Buka **Dashboard → PutraFiber Analytics** (widget otomatis aktif setelah tema dipasang).
2. Tinjau statistik harian; gunakan filter untuk melihat referrer atau halaman.
3. Tekan **Reset Statistik** bila ingin membersihkan data (konfirmasi diperlukan).

### 4. SEO & Schema.org Otomatis
- **Jenis Schema**: Organization, WebSite, WebPage, Article, Product, LocalBusiness, BreadcrumbList, WPHeader, SiteNavigationElement.
- **Toggle Per Konten**: Melalui metabox di CPT maupun halaman biasa.
- **Integrasi Plugin SEO**: Dapat menonaktifkan schema default via filter `putrafiber_schema_skip_common` bila menggunakan plugin eksternal.

**Langkah optimasi**
1. Pastikan informasi perusahaan lengkap di Theme Options → Identity (logo, alamat, kontak).
2. Saat membuat produk/artikel, isi meta SEO & toggle schema sesuai kebutuhan.
3. Validasi output JSON-LD via Rich Results Test Google.

### 5. Performa & PWA
- **Lazy Loading & WebP**: `inc/performance.php` memastikan gambar otomatis menggunakan `loading="lazy"`, konversi WebP, dan atribut `decoding="async"`.
- **PWA Manifest**: File `manifest.webmanifest` dan `service-worker.js` siap pakai; toggle melalui Theme Options → Advanced.
- **Vite Build**: Menggunakan `npm run build` untuk menghasilkan aset hashed di `assets/dist/` dengan manifest JSON.

**Langkah aktivasi PWA**
1. Pastikan domain menggunakan HTTPS.
2. Buka Theme Options → Advanced → aktifkan **Enable PWA**.
3. Upload ikon aplikasi jika diperlukan.
4. Jalankan `npm run build` sebelum deploy agar manifest Vite terbaru tersaji.

### 6. Keamanan & Hardening
- Penambahan header keamanan (X-Content-Type-Options, X-Frame-Options, X-XSS-Protection, Referrer-Policy).
- Sanitasi input intensif (fungsi `pf_clean_*`) untuk opsi, metabox, dan request AJAX.
- Fitur reset analytics memverifikasi capability `manage_options`.

### 7. Pengalaman Admin
- Theme Options panel berbasis tab dengan color picker dan uploader terintegrasi Media Library.
- Galeri backend menggunakan SortableJS untuk re-order.
- Editor block (TinyMCE teeny) di meta box blog manual, memudahkan copywriting ringkas.

## Alur Instalasi & Setup Lengkap
1. **Upload Tema**: Taruh folder `putrafiber-enterprise` ke `wp-content/themes/`.
2. **Aktifkan** melalui **Appearance → Themes**.
3. **Pasang Dependencies** (opsional untuk pengembangan): jalankan `npm install` lalu `npm run dev` atau `npm run build` untuk bundel Vite.
4. **Konfigurasi Theme Options**: Isi data identitas perusahaan, kontak, landing page, produk unggulan.
5. **Buat CPT**: Tambah konten di Products & Portfolio; susun galeri, CTA, dan meta SEO.
6. **Set Menu**: Masuk ke **Appearance → Menus**, pilih menu utama, lalu tempatkan ke lokasi *Primary Menu*.
7. **Verifikasi Schema** & PWA bila diaktifkan.

## Best Practices Operasional
- **Gunakan Gambar Optimal**: Upload minimal 1600px untuk hero dan 1024px untuk kartu; sistem akan mengonversi ke WebP.
- **Perbarui Analytics Berkala**: Reset statistik setiap kampanye untuk membaca performa terbaru.
- **Audit SEO**: Gunakan preview meta di metabox agar judul/deskripsi tidak melebihi batas.
- **Manfaatkan Dark Mode**: Pastikan gambar memiliki varian yang tetap kontras di mode gelap.
- **Monitoring Aset**: Setelah build baru, sinkronkan CDN dan update `service-worker.js` untuk cache busting.

## Kelebihan BLUEPRINT Theme
- **All-in-One Solution**: Menggabungkan landing page, katalog produk, dan portofolio dalam satu tema.
- **Optimasi Konversi**: CTA ganda, badge interaktif, dan highlight statistik langsung.
- **SEO-Friendly**: Schema komprehensif, struktur HTML semantik, dan dukungan plugin SEO.
- **Performa Tinggi**: Lazyload, WebP, preloading aset, dan integrasi PWA.
- **Kemudahan Admin**: Theme Options intuitif, galeri drag & drop, reset analytics aman.
- **Fleksibilitas Tampilan**: Dukungan dark mode, variabel CSS global, serta modular section.

## Rekomendasi Pengembangan di Masa Depan
1. **Visual Landing Page Builder**
   - Buat antarmuka drag & drop untuk section agar admin dapat menyusun layout tanpa masuk ke opsi tekstual.
2. **Preset Warna & Branding**
   - Simpan beberapa kombinasi warna di database dan sediakan toggle cepat di Theme Options.
3. **Integrasi Analytics Lanjutan**
   - Tambahkan pelacakan perangkat, geolokasi, serta fitur ekspor CSV untuk analisis lanjutan.
4. **Widget Konversi WhatsApp per Halaman**
   - Bantu tim marketing mengukur halaman paling efektif terhadap klik WhatsApp.
5. **Blok Gutenberg Kustom**
   - Sediakan blok hero, CTA, testimoni agar halaman lain dapat meniru style landing.
6. **Dukungan Multi-Bahasa**
   - Pastikan semua string menggunakan text domain dan siapkan file `.pot` untuk WPML/Polylang.
7. **Pipeline Deploy Otomatis**
   - Integrasikan `npm run build` pada CI/CD dan otomatisasi pembersihan cache CDN.

## Troubleshooting Singkat
- **Galeri Zoom Otomatis**: Pastikan `assets/js/gallery-unified.js` termuat dan stylesheet `product.css`/`portfolio.css` menggunakan reset transform.
- **Schema Ganda**: Gunakan filter `putrafiber_schema_skip_common` bila ada plugin SEO yang sudah menghasilkan schema serupa.
- **PWA Tidak Aktif**: Cek konsol browser untuk error service worker; jalankan ulang `npm run build` agar file terhash tersedia.
- **Analytics Tidak Mencatat**: Verifikasi nonce AJAX melalui Inspect → Network dan pastikan `putrafiber_track_whatsapp_click` tidak diblokir oleh rate limiting.

## Penutup
Dengan mengikuti panduan ini, tim pengembang maupun konten dapat memaksimalkan potensi BLUEPRINT theme. Dokumentasi ini juga menjadi pondasi untuk iterasi berikutnya sehingga setiap fitur baru tetap konsisten dengan standar performa, keamanan, dan brand experience yang sudah dibangun.
