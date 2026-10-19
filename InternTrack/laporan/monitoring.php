<?php
$page_title = 'Laporan Monitoring';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Laporan Monitoring PKL</h3>
        <button onclick="window.print()" class="btn btn-sm btn-primary">🖨️ Cetak</button>
    </div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Pembimbing</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                    <th>Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = $conn->query("
                SELECT m.*, s.nis, s.nama as nama_siswa, pb.nama as nama_pembimbing
                FROM monitoring m
                JOIN siswa s ON m.siswa_id = s.id
                JOIN pembimbing pb ON m.pembimbing_id = pb.id
                ORDER BY m.tanggal DESC
            ");
            $no = 1;
            while ($r = $res->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= formatTanggal($r['tanggal']) ?></td>
                    <td><?= e($r['nis']) ?></td>
                    <td><?= e($r['nama_siswa']) ?></td>
                    <td><?= e($r['nama_pembimbing']) ?></td>
                    <td><strong><?= $r['nilai'] ?></strong></td>
                    <td><?= e($r['catatan']) ?></td>
                    <td><?= e($r['rekomendasi']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <p style="margin-top:1rem;font-size:0.85rem;color:#718096">
            Dicetak pada: <?= date('d/m/Y H:i') ?> | InternTrack - SMK Hidayah Semarang
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
