# Deployment TravelMate: Render + Aiven

Dokumen ini disiapkan untuk deployment tugas kuliah dengan layanan gratis.

## Arsitektur

- Backend Laravel: Render Web Service, runtime Docker.
- Frontend React/Vite: Render Static Site.
- Database: Aiven for MySQL.

## A. Buat database Aiven

1. Daftar atau login ke Aiven.
2. Buat service baru dan pilih MySQL Free.
3. Tunggu status service aktif.
4. Buka Overview/Connection information.
5. Catat Host, Port, Database, User, dan Password.
6. Unduh atau salin CA certificate. Jangan commit kredensial ke GitHub.

## B. Buat backend di Render

1. Pilih **New > Web Service**.
2. Hubungkan repository `Hnf124/Travel-Mate-SWM`, branch `main`.
3. Runtime: **Docker**.
4. Region: **Singapore**.
5. Plan: **Free**.
6. Health Check Path: `/up`.
7. Tambahkan environment variables dari `.env.render.example`.
8. Buat APP_KEY di komputer lokal:

   ```bash
   php artisan key:generate --show
   ```

9. Pada Render > Environment > Secret Files, buat file:

   ```text
   ca.pem
   ```

   Lalu tempel isi CA certificate Aiven. File akan tersedia sebagai `/etc/secrets/ca.pem`.
10. Deploy dan tunggu sampai status Live.
11. Catat URL backend, contoh `https://travel-mate-api.onrender.com`.

Startup script otomatis menjalankan migration dan seeder tempat wisata. Seeder aman dijalankan berulang dan tidak membuat data wisata duplikat.

## C. Buat frontend di Render

1. Pilih **New > Static Site**.
2. Hubungkan repository dan branch yang sama.
3. Root Directory:

   ```text
   frontend/travel-web-app
   ```

4. Build Command:

   ```bash
   npm ci && npm run build
   ```

5. Publish Directory:

   ```text
   dist
   ```

6. Environment Variable:

   ```text
   VITE_API_BASE_URL=https://NAMA-BACKEND.onrender.com/api/v1
   ```

7. Deploy dan catat URL frontend.

## D. Perbarui URL frontend pada backend

Pada Render backend, ubah:

```text
FRONTEND_URL=https://NAMA-FRONTEND.onrender.com
APP_URL=https://NAMA-BACKEND.onrender.com
```

Simpan dan redeploy backend.

## E. Uji sebelum presentasi

1. Buka `https://NAMA-BACKEND.onrender.com/up`.
2. Buka frontend.
3. Register akun baru.
4. Login.
5. Cari `Banda Aceh`.
6. Buka detail dan cek cuaca.
7. Tambahkan favorit, lalu hapus.
8. Cari `Banda Aceh` lagi dan pastikan riwayat tidak duplikat.
9. Logout dan login kembali.

## Catatan

- Jangan upload `.env`, password Aiven, atau CA certificate ke GitHub.
- Frontend production tidak memakai `npm run dev`; Render membangun folder `dist`.
- Backend menggunakan `PORT` yang disediakan Render melalui `docker/start.sh`.
