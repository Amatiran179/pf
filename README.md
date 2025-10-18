# PutraFiber Enterprise Theme

Tema WordPress premium untuk brand PutraFiber, dikembangkan khusus untuk kebutuhan kontraktor waterpark, waterboom, dan playground fiberglass. Versi ini menghadirkan landing page modern bernuansa biru elektrik dengan aksen emas, pengalaman admin yang dipoles, serta fondasi SEO yang siap bersaing.

## Pembaruan 2.1.0 – Siap WordPress 6.8 & PHP 8.2

- Standar minimum dinaikkan: tema telah diuji penuh pada WordPress 6.8 dan PHP 8.2 dengan penyesuaian theme.json versi 3 serta dukungan fitur Appearance Tools terbaru.
- Pipeline galeri produk & portofolio diperkuat dengan normalisasi data attachment, caching `wp_cache`, dukungan `srcset`/`sizes`, serta atribut loading adaptif berbasis `wp_get_loading_optimization_attributes()` sehingga tampilan slider selalu sinkron dengan Gutenberg dan Core Web Vitals.
- Helper `putrafiber_get_bool_option()` memastikan seluruh toggle Theme Options (PWA, schema, parallax, builder section) konsisten meskipun nilai database berupa string, angka, maupun boolean murni.
- Tema kini otomatis memanfaatkan palette spacing baru (unit px/rem/em/%) dan tipografi fluid untuk blok Gutenberg agar styling bawaan WordPress 6.8 langsung serasi dengan desain PutraFiber.
- Penyesuaian front page mengatasi bug data-parallax yang selalu aktif serta menyiapkan fallback aman jika helper baru tidak tersedia.
- Workflow build kini memakai skrip Node custom yang membersihkan konfigurasi proxy npm bermasalah, memanggil API ESM Vite secara langsung, serta memverifikasi manifest agar enqueue PHP tidak pernah kehilangan bundle wajib.
- Tata letak responsif diperhalus: hero landing, kartu, grid layanan/produk, dan galeri kini otomatis menyesuaikan padding, radius, serta efek air pada layar tablet/HP; sticky gallery produk/portofolio dinonaktifkan di perangkat kecil dengan rasio slider yang lebih proporsional.

## Fitur Utama

### Landing Page & UI
- Desain hero parallax dengan efek air, gradien elektrik, dan animasi berbeda pada setiap elemen penting.
- Kartu fitur, layanan, produk, portofolio, dan blog dengan glassmorphism, highlight emas, serta animasi yang dapat diatur.
- Grid blog tanpa paginasi dengan slot artikel manual (Artikel 1, Artikel 2, dst) yang dapat dikurasi dari Theme Options.
- CTA section baru dengan latar dramatis, badge interaktif, dan tombol ganda yang konsisten.
- Padding container adaptif dan kartu responsif memastikan tampilan rapih di perangkat apa pun tanpa konten terpotong.
- Builder visual landing page berbasis drag & drop lengkap dengan pratinjau media dan kontrol real-time untuk section kustom.
- Navigasi mobile yang berpindah otomatis ke sisi kanan pada tablet & smartphone agar akses menu lebih intuitif.

### Manajemen Konten & CRUD
- Custom Post Type: **Portfolio** & **Product** lengkap dengan uploader galeri, PDF, dan meta SEO.
- Pengaturan landing page fleksibel: urutan section, copywriting, warna, CTA, hero, layanan, dan produk.
- Sistem preset warna yang dapat disimpan & dipanggil ulang; semua warna landing page otomatis mengikuti kombinasi aktif.
- Blok Gutenberg kustom (Hero, CTA premium, Testimoni) agar tampilan halaman lain konsisten dengan gaya landing page.
- Editor deskripsi blog menggunakan TinyMCE mini (teeny) untuk copy yang tetap bersih.
- Dukungan dark mode di front end.

### SEO & Schema.org
- Output schema otomatis (Organization, WebSite, Article, Product, LocalBusiness, BreadcrumbList) dengan toggle per konten.
- Penambahan schema baru untuk menjaga struktur SEO modern:
  - `WPHeader`
  - `SiteNavigationElement`
  - `WebPage` (landing page & halaman umum)
- Konfigurasi warna & struktur HTML tetap kompatibel dengan plugin SEO populer; bisa dinonaktifkan melalui Theme Options jika dibutuhkan.
- Meta box SEO dengan penghitung karakter realtime untuk judul dan deskripsi.

### Analitik & Dashboard
- Widget statistik dashboard diperbarui dengan tampilan kartu metrik, tabel responsif, dan tombol **Reset Statistik**.
- Tombol reset terhubung ke AJAX aman (nonce + capability check) agar data pengunjung dan klik WhatsApp dapat dibersihkan kapan saja.
- Highlight cepat: Total kunjungan, link dilihat, dan klik WhatsApp langsung pada header widget.

### Performa & PWA
- Lazy loading gambar, konversi WebP otomatis, dan preloading resource penting untuk produk & portofolio.
- Paket PWA opsional (manifest, service worker, icon) dengan toggle satu klik, kini memakai helper boolean baru agar kompatibel dengan penyimpanan opsi lama.
- Pengaturan warna kustom memanfaatkan CSS variables untuk meminimalkan stylesheet tambahan.

## Pengalaman Admin
- Theme Options panel dengan tab, color picker, uploader, dan validasi otomatis.
- UI admin untuk builder section visual dan manajemen preset warna (rename, duplikasi, hapus, satu-klik terapkan).
- Library media terintegrasi untuk logo, hero image, ikon schema, hingga dokumen PDF.
- Dukungan sortable untuk galeri produk serta section-section tertentu.
- Tombol reset analytics dengan status loading, pesan konfirmasi, dan alert keberhasilan.

## Cara Memulai
1. Unggah folder `putrafiber-enterprise` ke `/wp-content/themes/`.
2. Aktifkan tema melalui **Appearance → Themes**.
3. Buka **Theme Options** untuk mengatur warna, konten landing page, kontak, dan opsi SEO.
4. Atur menu utama di **Appearance → Menus** dan hubungkan ke lokasi *Primary Menu*.
5. Tambahkan konten Portfolio dan Product melalui Custom Post Type yang tersedia.
6. Tentukan slot blog manual di **Theme Options → Landing Page** untuk mengatur urutan Artikel 1, Artikel 2, dst.

## Pipeline Build Aset
- Install dependensi terlebih dahulu dengan `npm install`.
- Jalankan `npm run build` untuk memicu skrip `scripts/build.mjs` yang:
  - membersihkan konfigurasi proxy npm yang berpotensi mengacaukan cache build,
  - mengeksekusi `vite` melalui API ESM sehingga bebas peringatan CJS,
  - memvalidasi `assets/dist/manifest.json` dan memastikan seluruh entry (`main`, `front-page`, `product`, `portfolio`, `pwa`) tersedia untuk fungsi enqueue tema.
- Jika ada entry yang hilang, build akan gagal sehingga issue dapat diperbaiki sebelum dirilis ke server WordPress.

## Kustomisasi Schema Penting
- **WPHeader**: menggambarkan header situs beserta relasi ke organisasi.
- **SiteNavigationElement**: mengekspor struktur menu primary untuk crawler.
- **WebPage**: tersedia untuk landing page & halaman standar; terhubung dengan WebSite/Organization schema.
- Filter `putrafiber_schema_skip_common` dapat digunakan untuk menonaktifkan schema core jika plugin SEO eksternal mengambil alih.

## Potensi Pengembangan Berikutnya
- Integrasi analytics tambahan (mis. data device/OS) dan ekspor CSV.
- Widget dashboard tambahan untuk konversi WhatsApp per halaman.
- Mode multi-bahasa (WPML/Polylang) dengan string siap diterjemahkan penuh.
- Blok editor khusus untuk galeri interaktif (Swiper + Fancybox) agar dapat dipakai di halaman selain produk/portofolio.
- Dukungan pengaturan sumber CDN gambar (Cloudflare Images/S3) langsung dari Theme Options.

## Hardening Sweep
- Guard ABSPATH dan linting ulang seluruh file PHP memastikan tidak ada fatal error baru.
- Audit CRUD/AJAX: tambah verifikasi nonce, pengecekan kapabilitas kondisional, serta sanitasi input memakai helper `pf_clean_*`.
- Schema Advanced kini mendeteksi output legacy/SEO plugin lain sehingga hanya satu JSON-LD yang dirender.
- Enqueue & PWA: fallback manifest, versi aset dinamis, service worker dengan cache versioning dan pengecualian admin.
- Performance kecil: atribut `loading="lazy"` + `decoding="async"` untuk gambar non-hero, guard breadcrumb di konteks non-singular, dan catatan i18n yang konsisten.
- Semua perubahan bersifat additive dan mempertahankan kompatibilitas CTA, CRUD, serta schema lama.

## Struktur Folder Penting
- `assets/css/` – stylesheet utama (global, komponen, header, responsive, admin).
- `assets/js/` – skrip front end (animasi, lazyload, PWA) dan admin.
- `template-parts/` – partial template untuk section landing page dan konten.
- `inc/` – helper PHP: schema generator, analytics, enqueue, options, dsb.

## Lisensi
Tema ini bersifat proprietary untuk internal PutraFiber. Silakan hubungi tim pengembang PutraFiber apabila memerlukan dukungan tambahan atau penyesuaian lebih lanjut.
