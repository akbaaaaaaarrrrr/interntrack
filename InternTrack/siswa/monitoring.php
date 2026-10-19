<?php
$page_title = 'Hasil Monitoring';
require_once __DIR__ . '/../includes/header_siswa.php';

$siswa_id = $_SESSION['siswa_id'] ?? 0;
?>

<div class="card">
    <div class="card-header"><h3>Hasil Monitoring dari Pembimbing</h3></div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pembimbing</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                    <th>Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = $conn->query("
                SELECT m.*, pb.nama as nama_pembimbing
                FROM monitoring m
                JOIN pembimbing pb ON m.pembimbing_id = pb.id
                WHERE m.siswa_id = $siswa_id
                ORDER BY m.tanggal DESC
            ");
            $no = 1;
            while ($r = $res->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= formatTanggal($r['tanggal']) ?></td>
                    <td><?= e($r['nama_pembimbing']) ?></td>
                    <td><strong><?= $r['nilai'] ?></strong></td>
                    <td><?= e($r['catatan']) ?></td>
                    <td><?= e($r['rekomendasi']) ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if ($res->num_rows == 0): ?>
                <tr><td colspan="6" style="text-align:center;color:#718096">Belum ada hasil monitoring</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
