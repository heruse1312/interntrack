# InternTrack - Sistem Monitoring PKL Siswa

Aplikasi web monitoring Praktik Kerja Lapangan (PKL) untuk SMK.

## Cara Install (PENTING)

### 1. Extract ZIP
Extract folder `InternTrack` ke:
- **XAMPP**: `C:\xampp\htdocs\InternTrack`
- **Laragon**: `C:\laragon\www\InternTrack`

### 2. Import Database
1. Buka phpMyAdmin → `http://localhost/phpmyadmin`
2. Buat database baru bernama `interntrack` (atau biarkan SQL yang membuat)
3. Import file: `InternTrack/database/interntrack.sql`

### 3. Cek Config (jika perlu)
Edit `config/database.php` jika password MySQL Anda tidak kosong:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');     // isi password MySQL jika ada
define('DB_NAME', 'interntrack');
```

### 4. Jalankan
Buka browser: **http://localhost/InternTrack/**

---

## Akun Login

| Role  | Username  | Password     |
|-------|-----------|--------------|
| Admin | `admin`   | `password123`|
| Siswa | `2024001` | `password123`|

(Semua siswa: username = NIS, password = `password123`)

---

## Fitur
- Login Admin & Siswa
- Dashboard statistik dinamis
- CRUD Siswa, Perusahaan, Pembimbing
- Penempatan PKL + ubah status
- Jurnal kegiatan (input siswa, lihat admin)
- Absensi + hitung % & status otomatis
- Monitoring (catatan, nilai, rekomendasi)
- Search & Filter
- Laporan (Siswa, Kehadiran, Monitoring) + cetak

## Status Kehadiran
- ≥90% Sangat Baik | 75-89% Baik | 60-74% Perlu Perhatian | <60% Perlu Pembinaan

## Perbaikan di versi ini
- Path URL otomatis (tidak hardcoded `/InternTrack/`)
- Bisa dijalankan di subfolder mana pun
- Password hash benar untuk `password123`
- Pesan error database lebih jelas
