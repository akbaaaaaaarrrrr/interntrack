<?php
$page_title = 'Laporan Data Siswa PKL';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Laporan Data Siswa PKL</h3>
        <button onclick="window.print()" class="btn btn-sm btn-primary">🖨️ Cetak</button>
    </div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>No. HP</th>
                    <th>Tempat PKL</th>
                    <th>Status PKL</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = $conn->query("
                SELECT s.*, pr.nama_perusahaan, p.status
                FROM siswa s
                LEFT JOIN penempatan p ON s.id = p.siswa_id
                LEFT JOIN perusahaan pr ON p.perusahaan_id = pr.id
                ORDER BY s.nama
            ");
            $no = 1;
            while ($r = $res->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($r['nis']) ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['kelas']) ?></td>
                    <td><?= e($r['jurusan']) ?></td>
                    <td><?= e($r['no_hp']) ?></td>
                    <td><?= e($r['nama_perusahaan'] ?: '-') ?></td>
                    <td><?= e($r['status'] ?: '-') ?></td>
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
