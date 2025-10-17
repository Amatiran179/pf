# PutraFiber Enterprise Theme

Tema WordPress premium untuk brand PutraFiber, dikembangkan khusus untuk kebutuhan kontraktor waterpark, waterboom, dan playground fiberglass. Versi ini menghadirkan landing page modern bernuansa biru elektrik dengan aksen emas, pengalaman admin yang dipoles, serta fondasi SEO yang siap bersaing.

## Fitur Utama

### Landing Page & UI
- Desain hero parallax dengan efek air, gradien elektrik, dan animasi berbeda pada setiap elemen penting.
- Kartu fitur, layanan, produk, portofolio, dan blog dengan glassmorphism, highlight emas, serta animasi yang dapat diatur.
- Grid blog tanpa paginasi dengan slot artikel manual (Artikel 1, Artikel 2, dst) yang dapat dikurasi dari Theme Options.
- CTA section baru dengan latar dramatis, badge interaktif, dan tombol ganda yang konsisten.
- Navigasi mobile yang berpindah otomatis ke sisi kanan pada tablet & smartphone agar akses menu lebih intuitif.

### Manajemen Konten & CRUD
- Custom Post Type: **Portfolio** & **Product** lengkap dengan uploader galeri, PDF, dan meta SEO.
- Pengaturan landing page fleksibel: urutan section, copywriting, warna, CTA, hero, layanan, dan produk.
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
- Paket PWA opsional (manifest, service worker, icon) dengan toggle satu klik.
- Pengaturan warna kustom memanfaatkan CSS variables untuk meminimalkan stylesheet tambahan.

## Pengalaman Admin
- Theme Options panel dengan tab, color picker, uploader, dan validasi otomatis.
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

## Kustomisasi Schema Penting
- **WPHeader**: menggambarkan header situs beserta relasi ke organisasi.
- **SiteNavigationElement**: mengekspor struktur menu primary untuk crawler.
- **WebPage**: tersedia untuk landing page & halaman standar; terhubung dengan WebSite/Organization schema.
- Filter `putrafiber_schema_skip_common` dapat digunakan untuk menonaktifkan schema core jika plugin SEO eksternal mengambil alih.

## Potensi Pengembangan Berikutnya
- Builder visual untuk section landing page (drag & drop, pratinjau real-time).
- Sistem preset warna/tema agar admin bisa menyimpan beberapa kombinasi palet.
- Integrasi analytics tambahan (mis. data device/OS) dan ekspor CSV.
- Widget dashboard tambahan untuk konversi WhatsApp per halaman.
- Mode multi-bahasa (WPML/Polylang) dengan string siap diterjemahkan penuh.
- Komponen blok Gutenberg khusus (hero, CTA, layanan) agar konsisten di halaman lain.

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
