-- ============================================
-- Database: interntrack
-- Aplikasi: InternTrack - Sistem Monitoring PKL
-- ============================================

CREATE DATABASE IF NOT EXISTS interntrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE interntrack;

-- --------------------------------------------
-- Tabel users (login untuk admin & siswa)
-- --------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','siswa') NOT NULL DEFAULT 'siswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------
-- Tabel siswa
-- --------------------------------------------
CREATE TABLE siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    jurusan VARCHAR(50) NOT NULL,
    no_hp VARCHAR(15),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- --------------------------------------------
-- Tabel perusahaan
-- --------------------------------------------
CREATE TABLE perusahaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_perusahaan VARCHAR(150) NOT NULL,
    alamat TEXT NOT NULL,
    bidang VARCHAR(100) NOT NULL,
    kontak VARCHAR(50),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------
-- Tabel pembimbing (guru pembimbing)
-- --------------------------------------------
CREATE TABLE pembimbing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    nip VARCHAR(30) UNIQUE,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- --------------------------------------------
-- Tabel penempatan PKL
-- --------------------------------------------
CREATE TABLE penempatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    perusahaan_id INT NOT NULL,
    pembimbing_id INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    status ENUM('Belum Mulai','Aktif','Selesai') NOT NULL DEFAULT 'Belum Mulai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (perusahaan_id) REFERENCES perusahaan(id) ON DELETE CASCADE,
    FOREIGN KEY (pembimbing_id) REFERENCES pembimbing(id) ON DELETE CASCADE
);

-- --------------------------------------------
-- Tabel jurnal kegiatan
-- --------------------------------------------
CREATE TABLE jurnal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    kegiatan TEXT NOT NULL,
    hasil TEXT,
    kendala TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
);

-- --------------------------------------------
-- Tabel absensi
-- --------------------------------------------
CREATE TABLE absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('Hadir','Izin','Sakit','Alpa') NOT NULL,
    keterangan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_absensi (siswa_id, tanggal),
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
);

-- --------------------------------------------
-- Tabel monitoring
-- --------------------------------------------
CREATE TABLE monitoring (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    pembimbing_id INT NOT NULL,
    tanggal DATE NOT NULL,
    catatan TEXT,
    nilai INT CHECK (nilai BETWEEN 0 AND 100),
    rekomendasi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (pembimbing_id) REFERENCES pembimbing(id) ON DELETE CASCADE
);

-- ============================================
-- DATA DUMMY
-- ============================================

-- Users (Admin + 25 Siswa)
INSERT INTO users (username, password, role) VALUES
('admin', MD5('admin123'), 'admin'),
('siswa01', MD5('siswa123'), 'siswa'),
('siswa02', MD5('siswa123'), 'siswa'),
('siswa03', MD5('siswa123'), 'siswa'),
('siswa04', MD5('siswa123'), 'siswa'),
('siswa05', MD5('siswa123'), 'siswa'),
('siswa06', MD5('siswa123'), 'siswa'),
('siswa07', MD5('siswa123'), 'siswa'),
('siswa08', MD5('siswa123'), 'siswa'),
('siswa09', MD5('siswa123'), 'siswa'),
('siswa10', MD5('siswa123'), 'siswa'),
('siswa11', MD5('siswa123'), 'siswa'),
('siswa12', MD5('siswa123'), 'siswa'),
('siswa13', MD5('siswa123'), 'siswa'),
('siswa14', MD5('siswa123'), 'siswa'),
('siswa15', MD5('siswa123'), 'siswa'),
('siswa16', MD5('siswa123'), 'siswa'),
('siswa17', MD5('siswa123'), 'siswa'),
('siswa18', MD5('siswa123'), 'siswa'),
('siswa19', MD5('siswa123'), 'siswa'),
('siswa20', MD5('siswa123'), 'siswa'),
('siswa21', MD5('siswa123'), 'siswa'),
('siswa22', MD5('siswa123'), 'siswa'),
('siswa23', MD5('siswa123'), 'siswa'),
('siswa24', MD5('siswa123'), 'siswa'),
('siswa25', MD5('siswa123'), 'siswa');

-- Siswa (25 data)
INSERT INTO siswa (user_id, nis, nama, kelas, jurusan, no_hp, alamat) VALUES
(2, '2024001', 'Ahmad Fauzi', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567001', 'Jl. Merdeka No. 1 Semarang'),
(3, '2024002', 'Budi Santoso', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567002', 'Jl. Diponegoro No. 12 Semarang'),
(4, '2024003', 'Citra Dewi', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567003', 'Jl. Pandanaran No. 5 Semarang'),
(5, '2024004', 'Dedi Kurniawan', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567004', 'Jl. Gajah Mada No. 8 Semarang'),
(6, '2024005', 'Eka Putri', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567005', 'Jl. Imam Bonjol No. 15 Semarang'),
(7, '2024006', 'Fajar Nugroho', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567006', 'Jl. Ahmad Yani No. 20 Semarang'),
(8, '2024007', 'Gita Sari', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567007', 'Jl. Pemuda No. 3 Semarang'),
(9, '2024008', 'Hendra Wijaya', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567008', 'Jl. Sisingamangaraja No. 7 Semarang'),
(10, '2024009', 'Indah Lestari', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567009', 'Jl. Kartini No. 11 Semarang'),
(11, '2024010', 'Joko Prasetyo', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567010', 'Jl. Sudirman No. 25 Semarang'),
(12, '2024011', 'Kartika Ayu', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567011', 'Jl. Kaligawe No. 4 Semarang'),
(13, '2024012', 'Lukman Hakim', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567012', 'Jl. Tugu No. 9 Semarang'),
(14, '2024013', 'Maya Sari', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567013', 'Jl. Majapahit No. 18 Semarang'),
(15, '2024014', 'Nanda Pratama', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567014', 'Jl. Candi No. 6 Semarang'),
(16, '2024015', 'Oki Setiawan', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567015', 'Jl. Simpang Lima No. 2 Semarang'),
(17, '2024016', 'Putri Amalia', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567016', 'Jl. Gatot Subroto No. 14 Semarang'),
(18, '2024017', 'Rizki Maulana', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567017', 'Jl. Raden Saleh No. 21 Semarang'),
(19, '2024018', 'Sinta Dewi', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567018', 'Jl. Veteran No. 10 Semarang'),
(20, '2024019', 'Toni Hartono', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567019', 'Jl. Pahlawan No. 16 Semarang'),
(21, '2024020', 'Umi Kalsum', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567020', 'Jl. Dr. Cipto No. 13 Semarang'),
(22, '2024021', 'Vina Marlina', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567021', 'Jl. MT Haryono No. 19 Semarang'),
(23, '2024022', 'Wahyu Nugroho', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567022', 'Jl. Brigjen Sudiarto No. 22 Semarang'),
(24, '2024023', 'Xenia Putri', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567023', 'Jl. Kimia No. 17 Semarang'),
(25, '2024024', 'Yoga Pratama', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567024', 'Jl. Anjasmoro No. 23 Semarang'),
(26, '2024025', 'Zahra Amelia', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '081234567025', 'Jl. Tembalang No. 8 Semarang');

-- Perusahaan (10 data)
INSERT INTO perusahaan (nama_perusahaan, alamat, bidang, kontak, email) VALUES
('PT Digital Solusi Indonesia', 'Jl. Gajah Mada No. 100 Semarang', 'Software Development', '024-1234567', 'hr@digitalsolusi.co.id'),
('CV Kreatif Media', 'Jl. Pemuda No. 45 Semarang', 'Digital Marketing', '024-2345678', 'info@kreatifmedia.id'),
('PT Teknologi Nusantara', 'Jl. Pandanaran No. 88 Semarang', 'IT Consulting', '024-3456789', 'contact@teknus.co.id'),
('PT Data Analytics Pro', 'Jl. Diponegoro No. 50 Semarang', 'Data Science', '024-4567890', 'hr@dataanalytics.pro'),
('CV Web Master Indonesia', 'Jl. Ahmad Yani No. 33 Semarang', 'Web Development', '024-5678901', 'admin@webmaster.id'),
('PT Cloud Computing ID', 'Jl. Imam Bonjol No. 77 Semarang', 'Cloud Services', '024-6789012', 'info@cloudid.com'),
('PT Mobile App Studio', 'Jl. Sisingamangaraja No. 12 Semarang', 'Mobile Development', '024-7890123', 'hr@mobileapp.studio'),
('CV Design Creative', 'Jl. Kartini No. 25 Semarang', 'UI/UX Design', '024-8901234', 'hello@designcreative.id'),
('PT Network Solutions', 'Jl. Sudirman No. 90 Semarang', 'Networking', '024-9012345', 'support@netsol.co.id'),
('PT Cyber Security Indo', 'Jl. Majapahit No. 15 Semarang', 'Cyber Security', '024-0123456', 'info@cybersec.id');

-- Pembimbing (10 data) - user_id NULL karena admin shared
INSERT INTO pembimbing (nip, nama, no_hp, email) VALUES
('198001012005011001', 'Drs. Bambang Sutrisno, M.Pd', '081122334401', 'bambang@smkhidayah.sch.id'),
('198102152006021002', 'Siti Aminah, S.Kom, M.Kom', '081122334402', 'siti@smkhidayah.sch.id'),
('198203202007031003', 'Agus Setiawan, S.T', '081122334403', 'agus@smkhidayah.sch.id'),
('198304102008041004', 'Rina Marlina, S.Pd', '081122334404', 'rina@smkhidayah.sch.id'),
('198405252009051005', 'Hadi Prasetyo, S.Kom', '081122334405', 'hadi@smkhidayah.sch.id'),
('198506152010061006', 'Dewi Lestari, M.Pd', '081122334406', 'dewi@smkhidayah.sch.id'),
('198607202011071007', 'Eko Wahyudi, S.T, M.T', '081122334407', 'eko@smkhidayah.sch.id'),
('198708052012081008', 'Fitri Handayani, S.Kom', '081122334408', 'fitri@smkhidayah.sch.id'),
('198809122013091009', 'Gunawan, S.Pd, M.Kom', '081122334409', 'gunawan@smkhidayah.sch.id'),
('198910182014101010', 'Hesti Wulandari, S.T', '081122334410', 'hesti@smkhidayah.sch.id');

-- Penempatan (10 data) - sebagian Aktif, Selesai, Belum Mulai
INSERT INTO penempatan (siswa_id, perusahaan_id, pembimbing_id, tanggal_mulai, tanggal_selesai, status) VALUES
(1, 1, 1, '2026-07-01', '2026-09-30', 'Aktif'),
(2, 2, 2, '2026-07-01', '2026-09-30', 'Aktif'),
(3, 3, 3, '2026-07-01', '2026-09-30', 'Aktif'),
(4, 4, 4, '2026-07-01', '2026-09-30', 'Aktif'),
(5, 5, 5, '2026-07-01', '2026-09-30', 'Aktif'),
(6, 6, 6, '2026-07-01', '2026-09-30', 'Aktif'),
(7, 7, 7, '2026-07-01', '2026-09-30', 'Aktif'),
(8, 8, 8, '2026-07-01', '2026-09-30', 'Selesai'),
(9, 9, 9, '2026-08-01', '2026-10-31', 'Aktif'),
(10, 10, 10, '2026-08-15', '2026-11-15', 'Belum Mulai');

-- Jurnal (30 data)
INSERT INTO jurnal (siswa_id, tanggal, kegiatan, hasil, kendala) VALUES
(1, '2026-09-01', 'Membuat desain database', 'ERD selesai dibuat', 'Kesulitan menentukan relasi tabel'),
(1, '2026-09-02', 'Implementasi tabel database', 'Semua tabel berhasil dibuat', 'Tidak ada'),
(1, '2026-09-03', 'Coding modul login', 'Login berhasil berfungsi', 'Validasi password masih sederhana'),
(2, '2026-09-01', 'Analisis kebutuhan sistem', 'Dokumen SRS selesai', 'Stakeholder sulit dihubungi'),
(2, '2026-09-02', 'Membuat wireframe UI', 'Wireframe dashboard selesai', 'Tidak ada'),
(2, '2026-09-03', 'Coding halaman dashboard', 'Dashboard menampilkan statistik', 'Query optimasi masih kurang'),
(3, '2026-09-01', 'Setup project Laravel', 'Project berhasil di-setup', 'Versi PHP tidak sesuai'),
(3, '2026-09-02', 'Membuat migration database', 'Migration berhasil dijalankan', 'Tidak ada'),
(3, '2026-09-03', 'Implementasi CRUD Siswa', 'CRUD Siswa selesai', 'Validasi form perlu diperbaiki'),
(4, '2026-09-01', 'Belajar framework React', 'Memahami component dasar', 'Masih bingung state management'),
(4, '2026-09-02', 'Membuat komponen Login', 'Komponen Login selesai', 'Tidak ada'),
(4, '2026-09-03', 'Integrasi API backend', 'API berhasil dihubungkan', 'CORS error sempat muncul'),
(5, '2026-09-01', 'Desain UI dengan Figma', 'Mockup halaman utama selesai', 'Tidak ada'),
(5, '2026-09-02', 'Export asset desain', 'Asset siap digunakan', 'Resolusi gambar kurang optimal'),
(5, '2026-09-03', 'Implementasi CSS responsive', 'Tampilan mobile sudah baik', 'Breakpoint masih disesuaikan'),
(6, '2026-09-01', 'Setup Git repository', 'Repository berhasil dibuat', 'Tidak ada'),
(6, '2026-09-02', 'Membuat branch development', 'Branch berhasil dibuat', 'Tidak ada'),
(6, '2026-09-03', 'Code review dengan mentor', 'Feedback diterima dan diperbaiki', 'Tidak ada'),
(7, '2026-09-01', 'Testing modul absensi', 'Bug ditemukan dan diperbaiki', 'Edge case belum tertutup'),
(7, '2026-09-02', 'Dokumentasi API', 'Dokumentasi Swagger selesai', 'Tidak ada'),
(7, '2026-09-03', 'Deploy ke staging server', 'Aplikasi berhasil di-deploy', 'Konfigurasi env sempat error'),
(8, '2026-09-01', 'Membuat laporan mingguan', 'Laporan dikirim ke pembimbing', 'Tidak ada'),
(8, '2026-09-02', 'Presentasi progress', 'Presentasi berjalan lancar', 'Nervous saat presentasi'),
(8, '2026-09-03', 'Revisi berdasarkan feedback', 'Revisi selesai', 'Tidak ada'),
(9, '2026-09-05', 'Orientasi perusahaan', 'Memahami alur kerja perusahaan', 'Tidak ada'),
(9, '2026-09-06', 'Setup lingkungan development', 'Environment siap digunakan', 'Software yang dibutuhkan banyak'),
(9, '2026-09-07', 'Mempelajari codebase existing', 'Memahami struktur project', 'Dokumentasi kurang lengkap'),
(1, '2026-09-08', 'Membuat fitur pencarian', 'Search berfungsi dengan baik', 'Tidak ada'),
(2, '2026-09-08', 'Optimasi query database', 'Query lebih cepat', 'Index belum optimal'),
(3, '2026-09-08', 'Menulis unit test', 'Coverage mencapai 70%', 'Beberapa edge case belum dicover');

-- Absensi (30 data)
INSERT INTO absensi (siswa_id, tanggal, status, keterangan) VALUES
(1, '2026-09-01', 'Hadir', NULL),
(1, '2026-09-02', 'Hadir', NULL),
(1, '2026-09-03', 'Hadir', NULL),
(1, '2026-09-04', 'Izin', 'Urusan keluarga'),
(1, '2026-09-05', 'Hadir', NULL),
(2, '2026-09-01', 'Hadir', NULL),
(2, '2026-09-02', 'Hadir', NULL),
(2, '2026-09-03', 'Sakit', 'Demam'),
(2, '2026-09-04', 'Hadir', NULL),
(2, '2026-09-05', 'Hadir', NULL),
(3, '2026-09-01', 'Hadir', NULL),
(3, '2026-09-02', 'Hadir', NULL),
(3, '2026-09-03', 'Hadir', NULL),
(3, '2026-09-04', 'Hadir', NULL),
(3, '2026-09-05', 'Alpa', NULL),
(4, '2026-09-01', 'Hadir', NULL),
(4, '2026-09-02', 'Hadir', NULL),
(4, '2026-09-03', 'Hadir', NULL),
(4, '2026-09-04', 'Hadir', NULL),
(4, '2026-09-05', 'Hadir', NULL),
(5, '2026-09-01', 'Hadir', NULL),
(5, '2026-09-02', 'Izin', 'Keperluan mendesak'),
(5, '2026-09-03', 'Hadir', NULL),
(5, '2026-09-04', 'Hadir', NULL),
(5, '2026-09-05', 'Hadir', NULL),
(6, '2026-09-01', 'Hadir', NULL),
(6, '2026-09-02', 'Hadir', NULL),
(6, '2026-09-03', 'Hadir', NULL),
(6, '2026-09-04', 'Hadir', NULL),
(6, '2026-09-05', 'Hadir', NULL);

-- Monitoring (10 data)
INSERT INTO monitoring (siswa_id, pembimbing_id, tanggal, catatan, nilai, rekomendasi) VALUES
(1, 1, '2026-09-10', 'Siswa mampu berkomunikasi dengan baik dan dapat menyelesaikan tugas yang diberikan industri.', 85, 'Pertahankan kinerja dan tingkatkan inisiatif'),
(2, 2, '2026-09-10', 'Siswa aktif bertanya dan cepat memahami instruksi dari mentor.', 88, 'Sangat baik, lanjutkan pengembangan skill'),
(3, 3, '2026-09-11', 'Siswa masih perlu bimbingan lebih intensif dalam coding.', 72, 'Perlu perhatian khusus pada logika pemrograman'),
(4, 4, '2026-09-11', 'Siswa disiplin dan tepat waktu, hasil kerja memuaskan.', 90, 'Pertahankan dan bagikan ilmu ke rekan'),
(5, 5, '2026-09-12', 'Siswa kreatif dalam menyelesaikan masalah desain UI.', 87, 'Tingkatkan dokumentasi pekerjaan'),
(6, 6, '2026-09-12', 'Siswa kurang aktif berkomunikasi dengan tim.', 68, 'Perlu pembinaan komunikasi interpersonal'),
(7, 7, '2026-09-13', 'Siswa menguasai tools testing dengan baik.', 82, 'Lanjutkan eksplorasi automation testing'),
(8, 8, '2026-09-13', 'Siswa sudah menyelesaikan PKL dengan baik.', 91, 'Siap untuk masuk dunia kerja'),
(9, 9, '2026-09-14', 'Siswa baru mulai, masih dalam tahap adaptasi.', 75, 'Dampingi lebih intens di minggu awal'),
(10, 10, '2026-09-14', 'Siswa belum memulai PKL.', 0, 'Segera koordinasikan dengan perusahaan');

-- ============================================
-- SELESAI
-- ============================================
