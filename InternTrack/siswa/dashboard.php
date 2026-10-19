<?php
$page_title = 'Dashboard Siswa';
require_once __DIR__ . '/../includes/header_siswa.php';

$siswa_id = $_SESSION['siswa_id'] ?? 0;

// Data penempatan
$penempatan = $conn->query("
    SELECT p.*, pr.nama_perusahaan, pr.alamat as alamat_perusahaan, pr.bidang,
           pb.nama as nama_pembimbing, pb.no_hp as hp_pembimbing
    FROM penempatan p
    JOIN perusahaan pr ON p.perusahaan_id = pr.id
    JOIN pembimbing pb ON p.pembimbing_id = pb.id
    WHERE p.siswa_id = $siswa_id
    LIMIT 1
")->fetch_assoc();

$pct = hitungPersentaseKehadiran($siswa_id, $conn);
$status_kh = statusKehadiran($pct);

$total_jurnal = $conn->query("SELECT COUNT(*) as jml FROM jurnal WHERE siswa_id=$siswa_id")->fetch_assoc()['jml'];
$total_absensi = $conn->query("SELECT COUNT(*) as jml FROM absensi WHERE siswa_id=$siswa_id")->fetch_assoc()['jml'];
$total_monitoring = $conn->query("SELECT COUNT(*) as jml FROM monitoring WHERE siswa_id=$siswa_id")->fetch_assoc()['jml'];
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Jurnal</div>
        <div class="value"><?= $total_jurnal ?></div>
    </div>
    <div class="stat-card blue">
        <div class="label">Total Absensi</div>
        <div class="value"><?= $total_absensi ?></div>
    </div>
    <div class="stat-card green">
        <div class="label">Kehadiran</div>
        <div class="value"><?= $pct ?>%</div>
    </div>
    <div class="stat-card orange">
        <div class="label">Hasil Monitoring</div>
        <div class="value"><?= $total_monitoring ?></div>
    </div>
</div>

<?php if ($penempatan): ?>
<div class="card">
    <div class="card-header"><h3>Tempat PKL Saya</h3></div>
    <div class="card-body">
        <table>
            <tr><th width="180">Perusahaan</th><td><?= e($penempatan['nama_perusahaan']) ?></td></tr>
            <tr><th>Bidang</th><td><?= e($penempatan['bidang']) ?></td></tr>
            <tr><th>Alamat</th><td><?= e($penempatan['alamat_perusahaan']) ?></td></tr>
            <tr><th>Pembimbing</th><td><?= e($penempatan['nama_pembimbing']) ?> (<?= e($penempatan['hp_pembimbing']) ?>)</td></tr>
            <tr><th>Periode</th><td><?= formatTanggal($penempatan['tanggal_mulai']) ?> s/d <?= formatTanggal($penempatan['tanggal_selesai']) ?></td></tr>
            <tr><th>Status PKL</th>
                <td><span class="badge badge-<?= $penempatan['status']=='Aktif'?'aktif':($penempatan['status']=='Selesai'?'selesai':'belum') ?>"><?= e($penempatan['status']) ?></span></td>
            </tr>
            <tr><th>Status Kehadiran</th>
                <td><?= $pct ?>% — <span class="badge badge-<?= strtolower(str_replace(' ','',$status_kh)) ?>"><?= $status_kh ?></span></td>
            </tr>
        </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">Anda belum ditempatkan di perusahaan manapun. Hubungi admin/pembimbing.</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3>Jurnal Terbaru</h3></div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Kegiatan</th><th>Hasil</th></tr>
            </thead>
            <tbody>
            <?php
            $jurnal = $conn->query("SELECT * FROM jurnal WHERE siswa_id=$siswa_id ORDER BY tanggal DESC LIMIT 5");
            while ($j = $jurnal->fetch_assoc()):
            ?>
                <tr>
                    <td><?= formatTanggal($j['tanggal']) ?></td>
                    <td><?= e($j['kegiatan']) ?></td>
                    <td><?= e($j['hasil']) ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if ($jurnal->num_rows == 0): ?>
                <tr><td colspan="3" style="text-align:center;color:#718096">Belum ada jurnal</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
