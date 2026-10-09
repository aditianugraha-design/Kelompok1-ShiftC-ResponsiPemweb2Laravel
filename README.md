# Sistem Pendaftaran Pasien Klinik
> Aplikasi Manajemen dan Pendaftaran Pasien Klinik Berbasis Web Menggunakan Laravel

---

## 📌 Informasi Kelompok
- **Nomor Kelompok:** Kelompok 01
- **Shift Praktikum:** Shift C

---

## 👥 Anggota Kelompok

| No | Nama Lengkap | NIM | Shift Awal | Shift Akhir | Jobdesk / Kontribusi | Link Video Penjelasan |
|---|---|---|---|---|---|---|
| 1 | Aditia Wahyu Nugraha | H1H024014 | Shift A | Shift C | CRUD Fitur Reservasi & Autentikasi, API POST, Fitur Rekam Medis| [YouTube/Drive](https://youtu.be/obqoJLyqhE8) |
| 2 | Dedi Kurniawan | H1H024022 | Shift A | Shift C | Pendaftaran Pasien] | [YouTube](https://youtu.be/KKAf-RvsheI?si=eQerEkqfylRsOhne) |
| 3 | Wisnu Satya Herlambang | H1H024033 | Shift C | Shift C | Modul Dokter & Pasien (Controller, Migration, Views Profil Pasien, dan Feature Testing) | [YouTube](https://youtu.be/sMBWM1Yw0eU) |
| 4 | Alfian Iskandar Zulkarnain | H1H024034 | [Shift Awal] | [Shift Akhir] | [Jobdesk Fitur] | [YouTube/Drive](https://...) |

---

## 📖 Deskripsi Aplikasi
Sistem Pendaftaran Pasien Klinik adalah platform berbasis web yang dirancang untuk mempermudah operasional dan manajemen layanan kesehatan di klinik. Aplikasi ini memfasilitasi pendataan dokter, pendaftaran dan pengisian profil pasien, serta pengelolaan rekam medis dan reservasi jadwal berobat secara efisien dan terstruktur.

---

## ⚙️ Penjelasan Teknis

### 1. Teknologi (Tech Stack)
- **Backend:** Laravel [Versi] (PHP [Versi])
- **Frontend:** Blade / Tailwind CSS / Bootstrap / JavaScript
- **Database:** MySQL / PostgreSQL
- **Library / Package:** [Contoh: Laravel Breeze, DomPDF, Filament, dll.]

### 2. Fitur Utama & Modul
- **Autentikasi & Otorisasi:** Sistem login, registrasi, serta manajemen peran (Role & Permission) untuk Admin, Dokter, dan Pasien.
- **Modul Dokter:** 
  - Pengelolaan data dokter (CRUD).
  - Skema migrasi database kustom untuk informasi dokter.
  - Tampilan daftar dan detail dokter (`resources/views/dokter/index.blade.php`).
  - Pengujian otomatis fungsi modul dokter (`DokterManagementTest.php`).
- **Modul Pasien:**
  - Antarmuka profil pasien lengkap (`profile`, `complete-profile`, `edit`, `show`).
  - Pengujian otomatis alur pengelolaan data pasien (`PasienManagementTest.php`).
- **[Modul Lainnya]:** [Reservasi, Rekam Medis, dll.]

### 3. Skema Data Singkat
- `users` (1 : 1) `dokters`
- `users` (1 : 1) `pasiens`
- `pasiens` (1 : N) `pendaftarans`
- `dokters` (1 : N) `pendaftarans`

---

## 🚀 Panduan Instalasi Lokal

```bash
# Clone repository
git clone <URL_REPOSITORY>
cd <NAMA_FOLDER>

# Install dependensi PHP & Node
composer install
npm install

# Konfigurasi Environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di file .env, lalu migrasi & seed
php artisan migrate --seed

# Jalankan development server
php artisan serve
npm run dev
```
