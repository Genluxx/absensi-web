# Deploy SAP.HRIS ke Render

Aplikasi berjalan dengan Docker dan menggunakan MySQL managed eksternal. Repository tidak menyimpan kredensial database; siapkan database MySQL di provider pilihan Anda sebelum membuat service Render.

## Setup database lokal

Salin `.env.example` menjadi `.env`, lalu isi `APP_KEY`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, dan seluruh `INITIAL_ADMIN_*`. Password admin harus minimal 12 karakter. Setelah itu jalankan:

```bash
docker compose up --build
```

Aplikasi tersedia di `http://192.168.6.116:1301` dari perangkat yang dapat mengakses jaringan lokal tersebut. Compose menjalankan MySQL dan aplikasi pada port `1301`; migration dan seeder berjalan saat container aplikasi mulai.

## Setup Render

1. Buat database MySQL managed dan siapkan host, port, nama database, username, dan password. Pastikan koneksi dari Render diizinkan oleh provider database.
2. Hubungkan repository GitHub ke Render sebagai Blueprint agar konfigurasi `render.yaml` digunakan.
3. Isi environment variables yang diminta Render: `APP_KEY`, `APP_URL`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, dan seluruh `INITIAL_ADMIN_*`. Untuk Render, isi `APP_URL` dengan URL publik service Render, bukan IP privat `192.168.6.116`. Gunakan password admin unik minimal 12 karakter.
4. Deploy service. Container memvalidasi environment production, menjalankan migration dan seeder, lalu memulai Apache. Koneksi MySQL dicoba ulang jika database belum siap.

Buat `APP_KEY` secara lokal dengan `php artisan key:generate --show`. Jangan commit `.env` atau mengisi secret di file repository. `APP_DEBUG` harus `false`; pendaftaran publik dinonaktifkan kecuali `ALLOW_REGISTRATION` sengaja diubah.

## Catatan production

- Seeder default tidak membuat akun demo. Akun demo lama dengan email domain `@absensiweb.test` atau `@sawita.test` dinonaktifkan ketika seeder production berjalan.
- Jangan menjalankan `AbsensiSeeder` atau `UserSeeder` di production.
- Filesystem container Render bersifat sementara. Gunakan object storage seperti S3 atau Cloudinary untuk foto presensi sebelum menerima data production.
- Backup database secara berkala dan uji proses pemulihannya.
- Jika deployment gagal, periksa Render Logs untuk kegagalan koneksi/migrasi dan pastikan semua kredensial database benar. Jangan aktifkan `APP_DEBUG` pada service publik.
