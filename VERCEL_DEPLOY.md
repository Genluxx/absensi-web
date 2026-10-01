# Deploy SAP.HRIS ke Render

Aplikasi berjalan dengan Docker dan menggunakan MySQL managed eksternal. Repository tidak menyimpan kredensial database; siapkan database MySQL di provider pilihan Anda sebelum membuat service Render.

## Setup database lokal

Salin `.env.example` menjadi `.env`, lalu isi `APP_KEY` dan `DB_PASSWORD` dengan nilai acak yang kuat. `MYSQL_ROOT_PASSWORD` boleh dikosongkan agar memakai `DB_PASSWORD` yang sama. Database Laravel dari host memakai `127.0.0.1:3307`, sedangkan container aplikasi memakai service MySQL internal. Jangan gunakan password contoh di server publik.

Untuk membuat skema baru yang sama dengan database aplikasi, jalankan:

```bash
docker compose up --build
```

Aplikasi tersedia di `http://192.168.6.116:1301/` pada jaringan yang dapat menjangkau server Ubuntu. Compose meneruskan port host `1301` ke Apache di container pada port `80`, membuat volume MySQL persisten, dan menjalankan seluruh migration Laravel saat container mulai. MySQL diakses aplikasi melalui jaringan internal Docker pada `absensi-web-mysql:3306`; port web `1301` bukan port database. Seeder demo tidak dijalankan pada mode lokal. Untuk database kosong, isi `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_USERNAME`, `INITIAL_ADMIN_EMAIL`, dan `INITIAL_ADMIN_PASSWORD` di `.env`, lalu set `RUN_SEEDERS=true` agar role dan akun superadmin dibuat.

Migration mereplikasi struktur tabel, bukan isi database lama. Untuk memindahkan data, ekspor database lama dengan `mysqldump --single-transaction`, mulai database Docker dengan `docker compose up -d absensi-web-mysql`, salin dump ke container dengan `docker cp`, lalu impor ke database Docker melalui `docker exec`. Pastikan target masih kosong sebelum import dan jangan hapus database lama sampai tabel serta jumlah data di Docker sudah diverifikasi.

## Setup Render

1. Buat database MySQL managed dan siapkan host, port, nama database, username, dan password. Pastikan koneksi dari Render diizinkan oleh provider database.
2. Hubungkan repository GitHub ke Render sebagai Blueprint agar konfigurasi `render.yaml` digunakan.
3. Isi environment variables yang diminta Render: `APP_KEY`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, dan seluruh `INITIAL_ADMIN_*`. URL publik diambil otomatis dari `RENDER_EXTERNAL_URL`. Gunakan password admin unik minimal 12 karakter.
4. Deploy service. Container memvalidasi environment production, menjalankan migration dan seeder, lalu memulai Apache. Koneksi MySQL dicoba ulang jika database belum siap.

Buat `APP_KEY` secara lokal dengan `php artisan key:generate --show`. Jangan commit `.env` atau mengisi secret di file repository. `APP_DEBUG` harus `false`; pendaftaran publik dinonaktifkan kecuali `ALLOW_REGISTRATION` sengaja diubah.

## Catatan production

- Seeder default tidak membuat akun demo. Akun demo lama dengan email domain `@absensiweb.test` atau `@sawita.test` dinonaktifkan ketika seeder production berjalan.
- Jangan menjalankan `AbsensiSeeder` atau `UserSeeder` di production.
- Filesystem container Render bersifat sementara. Gunakan object storage seperti S3 atau Cloudinary untuk foto presensi sebelum menerima data production.
- Backup database secara berkala dan uji proses pemulihannya.
- Jika deployment gagal, periksa Render Logs untuk kegagalan koneksi/migrasi dan pastikan semua kredensial database benar. Jangan aktifkan `APP_DEBUG` pada service publik.
