<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header_admin.php';

// Statistik dari database
$total_siswa = $conn->query("SELECT COUNT(*) as jml FROM siswa")->fetch_assoc()['jml'];
$total_perusahaan = $conn->query("SELECT COUNT(*) as jml FROM perusahaan")->fetch_assoc()['jml'];
$total_pembimbing = $conn->query("SELECT COUNT(*) as jml FROM pembimbing")->fetch_assoc()['jml'];
$pkl_aktif = $conn->query("SELECT COUNT(*) as jml FROM penempatan WHERE status='Aktif'")->fetch_assoc()['jml'];
$pkl_selesai = $conn->query("SELECT COUNT(*) as jml FROM penempatan WHERE status='Selesai'")->fetch_assoc()['jml'];
$jurnal_hari_ini = $conn->query("SELECT COUNT(*) as jml FROM jurnal WHERE tanggal = CURDATE()")->fetch_assoc()['jml'];

// Siswa perlu perhatian (persentase kehadiran < 75%)
$siswa_perhatian = 0;
$siswa_list = $conn->query("SELECT id FROM siswa");
while ($s = $siswa_list->fetch_assoc()) {
    $pct = hitungPersentaseKehadiran($s['id'], $conn);
    if ($pct > 0 && $pct < 75) $siswa_perhatian++;
}
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="label">Total Siswa PKL</div>
        <div class="value"><?= $total_siswa ?></div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-building"></i></div>
        <div class="label">Perusahaan</div>
        <div class="value"><?= $total_perusahaan ?></div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="label">Pembimbing</div>
        <div class="value"><?= $total_pembimbing ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
        <div class="label">PKL Aktif</div>
        <div class="value"><?= $pkl_aktif ?></div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-flag-checkered"></i></div>
        <div class="label">PKL Selesai</div>
        <div class="value"><?= $pkl_selesai ?></div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-book"></i></div>
        <div class="label">Jurnal Hari Ini</div>
        <div class="value"><?= $jurnal_hari_ini ?></div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="label">Siswa Perlu Perhatian</div>
        <div class="value"><?= $siswa_perhatian ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list-alt"></i> Penempatan PKL Terbaru</h3>
        <a href="penempatan.php" class="btn btn-sm btn-outline">Lihat Semua</a>
    </div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Perusahaan</th>
                    <th>Pembimbing</th>
                    <th>Periode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = $conn->query("
                    SELECT p.*, s.nama as nama_siswa, pr.nama_perusahaan, pb.nama as nama_pembimbing
                    FROM penempatan p
                    JOIN siswa s ON p.siswa_id = s.id
                    JOIN perusahaan pr ON p.perusahaan_id = pr.id
                    JOIN pembimbing pb ON p.pembimbing_id = pb.id
                    ORDER BY p.id DESC LIMIT 5
                ");
                while ($row = $q->fetch_assoc()):
                ?>
                <tr>
                    <td><?= e($row['nama_siswa']) ?></td>
                    <td><?= e($row['nama_perusahaan']) ?></td>
                    <td><?= e($row['nama_pembimbing']) ?></td>
                    <td><?= formatTanggal($row['tanggal_mulai']) ?> - <?= formatTanggal($row['tanggal_selesai']) ?></td>
                    <td><span class="badge <?= badgeStatus($row['status']) ?>"><?= e($row['status']) ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
