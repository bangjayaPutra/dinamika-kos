# Cara Menjalankan DinamiKA CMS

Panduan dari nol sampai website bisa dibuka di browser. Ikuti **berurutan**,
jangan loncat langkah. Estimasi waktu: ±15 menit (di luar download).

---

## 1. Siapkan alat yang dibutuhkan

| Alat                  | Versi minimal | Cek dengan              |
| --------------------- | ------------- | ----------------------- |
| PHP                   | 8.3 (disarankan 8.4) | `php -v`          |
| Composer              | 2.x           | `composer -V`           |
| Node.js               | 20.x          | `node -v`               |
| Docker Desktop        | terbaru       | `docker -v`             |
| Git                   | terserah      | `git --version`         |

> **Catatan untuk pengguna Laravel Herd (Mac):** pastikan `php -v` mengeluarkan
> PHP 8.4. Kalau upload foto gagal, naikkan batas di file `php.ini` Herd lalu
> restart Herd:
>
> ```
> memory_limit=256M
> upload_max_filesize=10M
> post_max_size=70M
> ```

---

## 2. Clone project lalu install dependency

```bash
git clone <url-repo-ini>
cd DinamiKA-CMS

composer install
npm install
```

Atau sekali jalan (otomatis: install + copy `.env` + migrate + build):

```bash
composer setup
```

> Kalau pakai `composer setup`, tetap lanjut ke **langkah 3** (database Docker)
> sebelum migrate bisa sukses.

---

## 3. Jalankan database MySQL di Docker

Project ini memakai MySQL bernama `dinamika-kos`. Jalankan satu perintah ini:

```bash
docker run -d \
  --name dinamika-mysql \
  -e MYSQL_ROOT_PASSWORD=admin \
  -e MYSQL_DATABASE=dinamika-kos \
  -e MYSQL_USER=user \
  -e MYSQL_PASSWORD=user \
  -v dinamika-mysql-data:/var/lib/mysql \
  -p 3306:3306 \
  mysql:8
```

Cek statusnya:

```bash
docker ps --filter name=dinamika-mysql
```

Perintah Docker yang berguna nanti:

```bash
docker start dinamika-mysql   # nyalakan lagi setelah laptop restart
docker stop dinamika-mysql    # matikan
```

---

## 4. Atur file `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Pastikan isi bagian database di `.env` seperti ini:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dinamika-kos
DB_USERNAME=user
DB_PASSWORD=user

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

> Tidak perlu Redis — session, cache, dan antrean memang sengaja pakai database.

---

## 5. Migrasi + isi data awal + symlink storage

Jalankan **berurutan**:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

- `migrate` → membuat semua tabel.
- `db:seed` → mengisi pengaturan situs (nama kos, nomor WA, alamat, dll).
- `storage:link` → **wajib**, supaya foto yang di-upload bisa tampil di browser.

---

## 6. Jalankan websitenya

```bash
composer run dev
```

Perintah ini menyalakan semuanya sekaligus (server Laravel, antrean database,
dan Vite). Tunggu sampai lognya tenang, lalu buka:

| Alamat                  | Isi                          |
| ----------------------- | ---------------------------- |
| http://localhost:8000   | Halaman publik (pengunjung)  |
| http://localhost:8000/dashboard | Dashboard (harus login) |
| http://localhost:8000/admin/tipe-kamar, dsb. | Halaman CMS (khusus admin) |
| http://localhost:8000/register | Daftar akun baru      |

> Kalau halaman terlihat kosong/tidak ada gaya (CSS tidak kepasang),
> matikan dev server (`Ctrl+C`) lalu jalankan `npm run build` sekali,
> kemudian `composer run dev` lagi.

---

## 7. Buat akun admin pertama

Akun yang daftar lewat `/register` otomatis ber-role `user` biasa.
Untuk menjadikan admin, ubah lewat terminal:

```bash
php artisan tinker --execute 'App\Models\User::where("email", "email-kamu@example.com")->update(["role" => "admin"]);'
```

Ganti `email-kamu@example.com` dengan email yang tadi didaftarkan.
Logout lalu login lagi, menu CMS akan muncul di sidebar.

Setelah punya satu admin, akun admin berikutnya bisa dibuat lewat
halaman **Pengguna** di CMS (langsung bisa login, tanpa verifikasi email).

---

## 8. Perintah harian (pengembangan)

```bash
composer run dev          # kerja harian: server + vite sekaligus
npm run build             # WAJIB tiap selesai tambah/ubah halaman Vue,
                          # supaya Wayfinder regenerate + aset terbaru
php artisan test --compact         # jalanin semua test otomatis
npx vue-tsc --noEmit               # cek error tipe TypeScript
vendor/bin/pint --dirty            # rapikan gaya kode PHP yang diubah
php artisan route:list --name=admin  # lihat daftar route CMS
```

---

## 9. Kalau ada masalah (troubleshooting)

| Gejala | Solusi |
| ------ | ------ |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL Docker belum jalan → `docker start dinamika-mysql`, tunggu ±10 detik, ulangi perintah |
| `Access denied for user 'user'` | Password/user di `.env` tidak cocok dengan perintah `docker run` langkah 3 → samakan, atau hapus container dan ulangi langkah 3 |
| Foto upload tidak tampil / 404 | Belum `php artisan storage:link` → jalankan (langkah 5) |
| Halaman Vue baru error `Unable to locate file in Vite manifest` | Jalankan `npm run build` |
| Halaman tidak berubah setelah edit Vue | Pastikan `composer run dev` (Vite) sedang jalan, atau `npm run build` ulang lalu hard-refresh browser (`Cmd+Shift+R` / `Ctrl+Shift+R`) |
| Error `VITE ... manifest` setelah `git pull` | Jalankan `npm install && npm run build` (mungkin ada dependency baru) |
| Login gagal padahal akun benar | Cek `users.role` di database harus `admin` untuk masuk CMS; akun baru default-nya `user` (lihat langkah 7) |
| `php artisan test` gagal di test tertentu saja | Jalankan satu file-nya saja, misal `php artisan test --compact --filter=NamaTest`, lalu baca pesan errornya |

---

## Ringkasan satu halaman (cheat sheet)

```bash
# Pertama kali
docker run -d --name dinamika-mysql -e MYSQL_ROOT_PASSWORD=admin \
  -e MYSQL_DATABASE=dinamika-kos -e MYSQL_USER=user -e MYSQL_PASSWORD=user \
  -v dinamika-mysql-data:/var/lib/mysql -p 3306:3306 mysql:8
cp .env.example .env && php artisan key:generate
php artisan migrate --force && php artisan db:seed --force && php artisan storage:link
composer run dev   # buka http://localhost:8000

# Tiap buka laptop
docker start dinamika-mysql
composer run dev

# Selesai ngoding
npm run build && php artisan test --compact
```
