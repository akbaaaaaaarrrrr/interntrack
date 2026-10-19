<?php
$page_title = 'Kehadiran Siswa';
require_once __DIR__ . '/../includes/header_admin.php';

$filter_siswa = (int)($_GET['siswa'] ?? 0);
$siswa_opt = $conn->query("SELECT id, nama, nis FROM siswa ORDER BY nama");
?>

<div class="card">
    <div class="card-header">
        <h3>Data Kehadiran Siswa</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-group">
            <select name="siswa" class="form-control" style="max-width:280px">
                <option value="">Semua Siswa</option>
                <?php while ($s = $siswa_opt->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>" <?= $filter_siswa==$s['id']?'selected':'' ?>><?= e($s['nis'].' - '.$s['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="absensi.php" class="btn btn-outline btn-sm">Reset</a>
        </form>

        <?php if ($filter_siswa > 0):
            $pct = hitungPersentaseKehadiran($filter_siswa, $conn);
            $status = statusKehadiran($pct);
            $siswa_nama = $conn->query("SELECT nama FROM siswa WHERE id=$filter_siswa")->fetch_assoc()['nama'];
        ?>
        <div class="alert alert-info" style="margin-bottom:1.2rem">
            <strong><?= e($siswa_nama) ?></strong> — Persentase Kehadiran: <strong><?= $pct ?>%</strong>
            — Status: <span class="badge badge-<?= strtolower(str_replace(' ','',$status)) ?>"><?= $status ?></span>
        </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT a.*, s.nama as nama_siswa, s.nis
                        FROM absensi a JOIN siswa s ON a.siswa_id = s.id WHERE 1=1";
                if ($filter_siswa) $sql .= " AND a.siswa_id = $filter_siswa";
                $sql .= " ORDER BY a.tanggal DESC";
                $res = $conn->query($sql);
                $no = 1;
                while ($r = $res->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= formatTanggal($r['tanggal']) ?></td>
                        <td><?= e($r['nis'].' - '.$r['nama_siswa']) ?></td>
                        <td><span class="badge <?= badgeStatus($r['status']) ?>"><?= e($r['status']) ?></span></td>
                        <td><?= e($r['keterangan'] ?: '-') ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($res->num_rows == 0): ?>
                    <tr><td colspan="5" style="text-align:center;color:#718096">Data tidak ditemukan</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Rekap Persentase Semua Siswa -->
<div class="card">
    <div class="card-header">
        <h3>Rekap Persentase Kehadiran</h3>
    </div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Total Hari</th>
                    <th>Hadir</th>
                    <th>Persentase</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $siswa_all = $conn->query("SELECT id, nis, nama FROM siswa ORDER BY nama");
            $no = 1;
            while ($s = $siswa_all->fetch_assoc()):
                $stat = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) as hadir FROM absensi WHERE siswa_id={$s['id']}")->fetch_assoc();
                $total = (int)$stat['total'];
                $hadir = (int)$stat['hadir'];
                $pct = $total > 0 ? round(($hadir/$total)*100, 1) : 0;
                $st = statusKehadiran($pct);
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($s['nis']) ?></td>
                    <td><?= e($s['nama']) ?></td>
                    <td><?= $total ?></td>
                    <td><?= $hadir ?></td>
                    <td><?= $pct ?>%</td>
                    <td><span class="badge badge-<?= strtolower(str_replace(' ','',$st)) ?>"><?= $st ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
