# Deploy SAP.HRIS ke Vercel

Project ini sudah memiliki entrypoint Vercel di `api/index.php` dan routing di `vercel.json`.

## Langkah deploy

1. Push repository ke GitHub.
2. Di Vercel pilih **Add New Project** lalu import repository.
3. Biarkan framework preset kosong atau pilih **Other**.
4. Tambahkan environment variables berikut di Project Settings:

```text
APP_NAME=SAP.HRIS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_DENGAN_PHP_ARTISAN_KEY_GENERATE
APP_URL=https://domain-vercel-kamu.vercel.app
LOG_CHANNEL=stderr
SESSION_DRIVER=cookie
CACHE_STORE=array
FILESYSTEM_DISK=local
```

5. Isi variable database sesuai database MySQL/PostgreSQL eksternal yang bisa diakses internet:

```text
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

6. Deploy.

## Catatan penting

- Vercel bersifat serverless. Jangan mengandalkan file lokal untuk upload foto, session file, atau database SQLite.
- Gunakan database eksternal seperti Neon, PlanetScale, Railway, Aiven, atau MySQL managed lainnya.
- Untuk foto presensi gunakan object storage seperti S3, Cloudinary, atau Supabase Storage. Atur `FILESYSTEM_DISK` dan konfigurasi disk sesuai provider.
- Generate `APP_KEY` secara lokal dengan:

```bash
php artisan key:generate --show
```

- Setelah deploy, jalankan migration dari komputer lokal menggunakan environment database production atau gunakan pipeline migration terpisah:

```bash
php artisan migrate --force
```

- Jangan upload file `.env` ke GitHub. Isi secrets melalui Vercel Project Settings.
