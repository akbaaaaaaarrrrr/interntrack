<?php
$page_title = 'Laporan Kehadiran';
require_once __DIR__ . '/../includes/header_admin.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Laporan Kehadiran Siswa PKL</h3>
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
                    <th>Total Hari</th>
                    <th>Hadir</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpa</th>
                    <th>Persentase</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $siswa = $conn->query("SELECT id, nis, nama, kelas FROM siswa ORDER BY nama");
            $no = 1;
            while ($s = $siswa->fetch_assoc()):
                $stat = $conn->query("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN status='Alpa' THEN 1 ELSE 0 END) as alpa
                    FROM absensi WHERE siswa_id = {$s['id']}
                ")->fetch_assoc();
                $total = (int)$stat['total'];
                $hadir = (int)$stat['hadir'];
                $pct = $total > 0 ? round(($hadir/$total)*100, 1) : 0;
                $st = statusKehadiran($pct);
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($s['nis']) ?></td>
                    <td><?= e($s['nama']) ?></td>
                    <td><?= e($s['kelas']) ?></td>
                    <td><?= $total ?></td>
                    <td><?= $hadir ?></td>
                    <td><?= (int)$stat['izin'] ?></td>
                    <td><?= (int)$stat['sakit'] ?></td>
                    <td><?= (int)$stat['alpa'] ?></td>
                    <td><?= $pct ?>%</td>
                    <td><span class="badge badge-<?= strtolower(str_replace(' ','',$st)) ?>"><?= $st ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <p style="margin-top:1rem;font-size:0.85rem;color:#718096">
            Aturan: ≥90% Sangat Baik | 75-89% Baik | 60-74% Perlu Perhatian | &lt;60% Perlu Pembinaan<br>
            Dicetak pada: <?= date('d/m/Y H:i') ?> | InternTrack - SMK Hidayah Semarang
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
