<?php
/**
 * Fungsi-fungsi helper InternTrack
 */

session_start();

require_once __DIR__ . '/../config/database.php';

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Cek role admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Cek role siswa
 */
function isSiswa() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'siswa';
}

/**
 * Redirect jika belum login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /InternTrack/auth/login.php');
        exit;
    }
}

/**
 * Redirect jika bukan admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /InternTrack/siswa/dashboard.php');
        exit;
    }
}

/**
 * Redirect jika bukan siswa
 */
function requireSiswa() {
    requireLogin();
    if (!isSiswa()) {
        header('Location: /InternTrack/admin/dashboard.php');
        exit;
    }
}

/**
 * Hitung persentase kehadiran siswa
 */
function hitungPersentaseKehadiran($siswa_id, $conn) {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir
            FROM absensi WHERE siswa_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['total'] == 0) return 0;
    return round(($result['hadir'] / $result['total']) * 100, 1);
}

/**
 * Tentukan status PKL berdasarkan persentase kehadiran
 */
function statusKehadiran($persentase) {
    if ($persentase >= 90) return 'Sangat Baik';
    if ($persentase >= 75) return 'Baik';
    if ($persentase >= 60) return 'Perlu Perhatian';
    return 'Perlu Pembinaan';
}

/**
 * Escape output
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format tanggal Indonesia
 */
function formatTanggal($tanggal) {
    if (!$tanggal) return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $t = strtotime($tanggal);
    return date('d', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}


/**
 * Badge class untuk status
 */
function badgeStatus($status) {
    $map = [
        'Aktif' => 'badge-aktif',
        'Selesai' => 'badge-selesai',
        'Belum Mulai' => 'badge-belum',
        'Hadir' => 'badge-hadir',
        'Izin' => 'badge-izin',
        'Sakit' => 'badge-sakit',
        'Alpa' => 'badge-alpa',
        'Sangat Baik' => 'badge-sangatbaik',
        'Baik' => 'badge-baik',
        'Perlu Perhatian' => 'badge-perhatian',
        'Perlu Pembinaan' => 'badge-pembinaan',
    ];
    return isset($map[$status]) ? $map[$status] : 'badge-belum';
}
?>
