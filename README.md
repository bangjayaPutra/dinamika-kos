# DinamiKA Kos

Website company profile kos **DinamiKA Kos** sekaligus CMS admin untuk mengelolanya.
Pengunjung bisa melihat tipe kamar, fasilitas, berita, dan ulasan, lalu memesan
via WhatsApp. Admin mengelola semua konten lewat halaman `/admin/*`.

> **Cara menjalankan dari nol?** Ikuti langkah berurutan di
> [`HOW_TO_RUN.md`](./HOW_TO_RUN.md).

---

## Fitur

### Halaman publik

| Halaman         | Alamat         | Keterangan                                                        |
| --------------- | -------------- | ----------------------------------------------------------------- |
| Beranda         | `/`            | Carousel banner, tipe kamar, fasilitas umum, ulasan, berita, kontak |
| Tipe Kamar      | `/tipe-kamar`  | Daftar + detail (galeri foto, fasilitas, harga, form ulasan)       |
| Fasilitas       | `/fasilitas`   | Fasilitas umum dan fasilitas kamar                                 |
| Berita          | `/berita`      | Daftar + detail artikel (hanya yang sudah terbit)                  |
| Kontak          | `/kontak`      | Alamat, jam operasional, tombol WhatsApp                           |

- Tombol WhatsApp mencatat dulu kliknya (`POST /wa-clicks`), baru membuka `wa.me`.
- Ulasan **langsung tampil** tanpa persetujuan admin (nama + rating wajib).
- Warna tema merah DinamiKA (`#cc0000` utama, `#ff0000` aksen, `#990000` footer).

### Halaman admin (`/admin/*`, wajib login + role `admin`)

- **Dashboard** — kartu ringkasan + grafik batang klik WhatsApp harian,
  bisa difilter per bulan dan per tahun, plus rincian klik per tipe kamar.
- **Tipe Kamar** — CRUD + galeri foto (maks 6 foto, maks 10 MB/foto).
- **Fasilitas** — CRUD, scope `kamar` (per tipe) dan `umum` (global).
- **Ulasan** — daftar + hapus.
- **Berita** — CRUD + editor teks (TipTap) + pratinjau sebelum terbit.
- **Carousel** — CRUD banner beranda.
- **Pengaturan** — nama situs, tagline, alamat, nomor WA, jam operasional, dll.
- **Pengguna** — kelola akun admin/user. Akun baru **langsung bisa login**,
  tanpa verifikasi email.

---

## Teknologi

- **Backend:** PHP 8.4, Laravel 13, Inertia v3, Fortify (auth), Wayfinder
- **Frontend:** Vue 3, Tailwind CSS v4, TipTap v3 (editor berita)
- **Database:** MySQL 8 (via Docker) untuk jalan normal,
  SQLite in-memory untuk test otomatis
- **Test:** Pest — `php artisan test --compact`

---

## Struktur penting

```
app/Http/Controllers/Admin/    Controller CMS (Tipe Kamar, Fasilitas, Ulasan, ...)
app/Http/Controllers/Public/   Controller halaman publik
app/Http/Controllers/DashboardController.php  Data grafik dashboard
app/Concerns/                  Trait aturan validasi (dipakai FormRequest)
resources/js/pages/admin/      Halaman Vue CMS
resources/js/pages/public/     Halaman Vue publik
resources/js/pages/Dashboard.vue  Dashboard + grafik WA click
resources/js/components/       Komponen reuse (WaButton, HomeCarousel, WaClickChart, ...)
resources/js/actions|routes|wayfinder/  Hasil generate Wayfinder (JANGAN edit manual)
routes/web.php                 Route publik + dashboard
routes/admin.php               Route CMS (middleware auth + admin)
database/migrations/           Skema database
tests/Feature/                 Test (Admin/*, PublicTest, DashboardTest, ...)
HOW_TO_RUN.md                  Panduan instalasi & menjalankan (lengkap)
```

---

## Perintah yang sering dipakai

```bash
composer run dev          # jalanin server + antrian + vite sekaligus
npm run build             # build aset frontend (wajib tiap tambah/ubah halaman Vue)
php artisan test --compact         # jalanin semua test
php artisan migrate --force        # jalanin migrasi
php artisan storage:link           # symlink storage (wajib untuk foto)
npx vue-tsc --noEmit               # cek tipe TypeScript
vendor/bin/pint --dirty            # rapikan gaya kode PHP yang diubah
```

Detail tiap perintah + cara setup awal ada di [`HOW_TO_RUN.md`](./HOW_TO_RUN.md).
