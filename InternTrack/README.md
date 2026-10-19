# InternTrack - Sistem Monitoring Kegiatan PKL Siswa

**Skema:** Junior Coder  
**Program Keahlian:** Rekayasa Perangkat Lunak  
**Platform:** Web (PHP + MySQL + HTML/CSS/JS)

---

## Cara Instalasi

### 1. Persyaratan
- XAMPP / Laragon / WAMP (PHP 7.4+ & MySQL/MariaDB)
- Browser modern (Chrome, Firefox, Edge)

### 2. Setup Database
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Buat database baru bernama `interntrack` (atau import langsung)
3. Import file: `database/interntrack.sql`
   - Klik tab **Import** → pilih file `interntrack.sql` → Go

### 3. Konfigurasi Koneksi
Edit file `config/database.php` jika perlu:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // sesuaikan password MySQL Anda
define('DB_NAME', 'interntrack');
```

### 4. Menjalankan Aplikasi
1. Copy folder `InternTrack` ke `htdocs` (XAMPP) atau `www` (Laragon)
2. Buka browser: `http://localhost/InternTrack`
3. Login dengan akun demo:

| Role  | Username  | Password   |
|-------|-----------|------------|
| Admin | `admin`   | `admin123` |
| Siswa | `siswa01` | `siswa123` |
| Siswa | `siswa02` | `siswa123` |
| ...   | ...       | ...        |
| Siswa | `siswa25` | `siswa123` |

---

## Struktur Folder

```
InternTrack/
├── config/
│   └── database.php          # Koneksi database
├── assets/
│   └── css/style.css         # Styling
├── includes/
│   ├── functions.php         # Helper functions
│   ├── header_admin.php      # Layout admin
│   ├── header_siswa.php      # Layout siswa
│   └── footer.php
├── auth/
│   ├── login.php
│   └── logout.php
├── admin/
│   ├── dashboard.php
│   ├── siswa.php             # CRUD Siswa + Search + Filter
│   ├── perusahaan.php        # CRUD Perusahaan
│   ├── pembimbing.php        # CRUD Pembimbing
│   ├── penempatan.php        # CRUD Penempatan PKL + Filter
│   ├── jurnal.php            # Lihat jurnal siswa
│   ├── absensi.php           # Lihat kehadiran + rekap %
│   └── monitoring.php        # CRUD Monitoring
├── siswa/
│   ├── dashboard.php
│   ├── profil.php
│   ├── jurnal.php            # Input jurnal
│   ├── absensi.php           # Input absensi
│   └── monitoring.php        # Lihat hasil monitoring
├── laporan/
│   ├── siswa.php
│   ├── kehadiran.php
│   └── monitoring.php
├── database/
│   └── interntrack.sql       # SQL lengkap + data dummy
├── index.php
└── README.md
```

---

## Fitur yang Tersedia

### Admin / Pembimbing
- ✅ Login & Dashboard (statistik dinamis dari DB)
- ✅ CRUD Data Siswa (Tambah, Lihat, Edit, Hapus, Cari, Detail, Filter Kelas)
- ✅ CRUD Data Perusahaan
- ✅ CRUD Data Pembimbing
- ✅ CRUD Penempatan PKL (Siswa → Perusahaan → Pembimbing + Status)
- ✅ Lihat Jurnal Kegiatan Siswa
- ✅ Lihat Kehadiran + Hitung Persentase Otomatis + Status
- ✅ CRUD Monitoring (Catatan, Nilai, Rekomendasi)
- ✅ Laporan (Siswa, Kehadiran, Monitoring) + Print
- ✅ Search & Filter

### Siswa
- ✅ Login & Dashboard
- ✅ Lihat Profil & Tempat PKL
- ✅ Input Jurnal Kegiatan
- ✅ Input Kehadiran (Hadir/Izin/Sakit/Alpa)
- ✅ Lihat Hasil Monitoring
- ✅ Status Kehadiran otomatis (Sangat Baik / Baik / Perlu Perhatian / Perlu Pembinaan)

### Validasi
- NIS tidak boleh kosong & tidak boleh duplikat
- Nama, Tanggal, Kegiatan wajib diisi
- Username & Password wajib di login
- Status absensi wajib dipilih

### Data Dummy
- 25 Siswa
- 10 Perusahaan
- 10 Pembimbing
- 10 Penempatan
- 30 Jurnal
- 30 Absensi
- 10 Monitoring

---

## Aturan Status Kehadiran

| Persentase     | Status            |
|----------------|-------------------|
| ≥ 90%          | Sangat Baik       |
| 75% – 89%      | Baik              |
| 60% – 74%      | Perlu Perhatian   |
| < 60%          | Perlu Pembinaan   |

---

## Catatan
- Password di-hash menggunakan MD5 (untuk kesederhanaan UKK)
- Aplikasi menggunakan pure PHP (tanpa framework) agar mudah dipahami
- Responsive design sederhana
- Siap digunakan untuk dokumentasi UKK (screenshot, test case, dll)
