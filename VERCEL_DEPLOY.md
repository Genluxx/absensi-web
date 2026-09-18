# Deploy SAP.HRIS ke Render Free

Project ini menyediakan `Dockerfile` dan `render.yaml` untuk deployment ke Render Free. Render akan memberikan URL publik seperti `https://absensi-web.onrender.com`, sehingga aplikasi dapat diakses dari jaringan mana pun.

## Langkah deploy

URL gratis Vercel berbentuk `https://nama-project.vercel.app` dan dapat dibuka dari jaringan atau IP mana pun. Komputer lokal tidak perlu menyala setelah deployment selesai.

## Deploy lewat dashboard Render

1. Push repository ini ke GitHub.
2. Buka Render Dashboard, pilih **New > Web Service**, lalu hubungkan repository.
3. Pilih **Docker** sebagai runtime dan paket **Free**.
4. Isi environment variables berikut:

```text
APP_KEY=hasil_php_artisan_key_generate
APP_URL=https://nama-service.onrender.com
DB_HOST=host-database-cloud
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database
```

5. Klik **Create Web Service**. Render akan membangun `Dockerfile` dan memberikan URL publik.
6. Setelah service aktif, jalankan migration dari komputer lokal dengan `.env` yang menunjuk ke database production:

```bash
php artisan migrate --force
php artisan db:seed --class=AbsensiSeeder --force
```

Generate `APP_KEY` secara lokal dengan:

```bash
php artisan key:generate --show
```

## Catatan penting

- Render Free dapat tidur setelah tidak ada traffic dan membutuhkan beberapa detik saat dibuka pertama kali.
- Jangan mengandalkan file lokal untuk upload foto atau database SQLite. Gunakan object storage untuk foto presensi.
- Gunakan database eksternal seperti Neon, PlanetScale, Railway, Aiven, atau MySQL managed lainnya.
- Untuk foto presensi gunakan object storage seperti S3, Cloudinary, atau Supabase Storage. Atur `FILESYSTEM_DISK` dan konfigurasi disk sesuai provider.
- Jangan memakai `DB_HOST=127.0.0.1` atau `localhost` di Vercel. Itu menunjuk ke server Vercel, bukan komputer lokal.
- Database dan object storage tetap harus berasal dari layanan cloud yang dapat diakses internet; domain gratis saja tidak membuat database lokal menjadi publik.

- Setelah deploy, jalankan migration dari komputer lokal menggunakan environment database production atau gunakan pipeline migration terpisah:

```bash
php artisan migrate --force
```

- Jangan upload file `.env` ke GitHub. Isi secrets melalui Vercel Project Settings.

## Jika halaman error

1. Buka Render **Logs** dan cari error PHP atau database.
2. Pastikan `APP_KEY` sudah diisi. Generate dengan `php artisan key:generate --show`.
3. Pastikan `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_DRIVER=cookie`, dan `CACHE_STORE=array`.
4. Pastikan `APP_URL` memakai URL deployment Render.
5. Pastikan URL database bukan `127.0.0.1` atau `localhost`; gunakan database managed yang bisa diakses internet.
6. Setelah mengubah environment variables, lakukan **Manual Deploy > Clear build cache & deploy**.
