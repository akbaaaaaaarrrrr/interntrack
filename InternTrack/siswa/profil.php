<?php
$page_title = 'Profil Saya';
require_once __DIR__ . '/../includes/header_siswa.php';

$siswa_id = $_SESSION['siswa_id'] ?? 0;
$siswa = $conn->query("SELECT * FROM siswa WHERE id = $siswa_id")->fetch_assoc();
$pct = hitungPersentaseKehadiran($siswa_id, $conn);
$status_kh = statusKehadiran($pct);

$penempatan = $conn->query("
    SELECT p.*, pr.nama_perusahaan, pb.nama as nama_pembimbing
    FROM penempatan p
    JOIN perusahaan pr ON p.perusahaan_id = pr.id
    JOIN pembimbing pb ON p.pembimbing_id = pb.id
    WHERE p.siswa_id = $siswa_id LIMIT 1
")->fetch_assoc();
?>

<div class="card">
    <div class="card-header"><h3>Profil Siswa</h3></div>
    <div class="card-body">
        <table>
            <tr><th width="180">NIS</th><td><?= e($siswa['nis']) ?></td></tr>
            <tr><th>Nama</th><td><?= e($siswa['nama']) ?></td></tr>
            <tr><th>Kelas</th><td><?= e($siswa['kelas']) ?></td></tr>
            <tr><th>Jurusan</th><td><?= e($siswa['jurusan']) ?></td></tr>
            <tr><th>No. HP</th><td><?= e($siswa['no_hp']) ?></td></tr>
            <tr><th>Alamat</th><td><?= e($siswa['alamat']) ?></td></tr>
            <tr><th>Username</th><td><?= e($_SESSION['username']) ?></td></tr>
            <tr><th>Persentase Kehadiran</th>
                <td><?= $pct ?>% — <span class="badge badge-<?= strtolower(str_replace(' ','',$status_kh)) ?>"><?= $status_kh ?></span></td>
            </tr>
            <?php if ($penempatan): ?>
            <tr><th>Tempat PKL</th><td><?= e($penempatan['nama_perusahaan']) ?></td></tr>
            <tr><th>Pembimbing</th><td><?= e($penempatan['nama_pembimbing']) ?></td></tr>
            <tr><th>Status PKL</th>
                <td><span class="badge badge-<?= $penempatan['status']=='Aktif'?'aktif':($penempatan['status']=='Selesai'?'selesai':'belum') ?>"><?= e($penempatan['status']) ?></span></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
