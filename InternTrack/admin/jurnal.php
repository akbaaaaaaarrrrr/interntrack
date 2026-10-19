<?php
$page_title = 'Jurnal Kegiatan';
require_once __DIR__ . '/../includes/header_admin.php';

$search = trim($_GET['search'] ?? '');
$filter_siswa = (int)($_GET['siswa'] ?? 0);

$siswa_opt = $conn->query("SELECT id, nama, nis FROM siswa ORDER BY nama");
?>

<div class="card">
    <div class="card-header">
        <h3>Jurnal Kegiatan Siswa</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-group">
            <input type="text" name="search" class="form-control" placeholder="Cari kegiatan..." value="<?= e($search) ?>" style="max-width:250px">
            <select name="siswa" class="form-control" style="max-width:250px">
                <option value="">Semua Siswa</option>
                <?php while ($s = $siswa_opt->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>" <?= $filter_siswa==$s['id']?'selected':'' ?>><?= e($s['nis'].' - '.$s['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="jurnal.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kegiatan</th>
                        <th>Hasil</th>
                        <th>Kendala</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT j.*, s.nama as nama_siswa, s.nis
                        FROM jurnal j JOIN siswa s ON j.siswa_id = s.id WHERE 1=1";
                if ($search) $sql .= " AND (j.kegiatan LIKE '%$search%' OR j.hasil LIKE '%$search%')";
                if ($filter_siswa) $sql .= " AND j.siswa_id = $filter_siswa";
                $sql .= " ORDER BY j.tanggal DESC";
                $res = $conn->query($sql);
                $no = 1;
                while ($r = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= formatTanggal($r['tanggal']) ?></td>
                        <td><?= e($r['nis'].' - '.$r['nama_siswa']) ?></td>
                        <td><?= e($r['kegiatan']) ?></td>
                        <td><?= e($r['hasil']) ?></td>
                        <td><?= e($r['kendala'] ?: '-') ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($res->num_rows == 0): ?>
                    <tr><td colspan="6" style="text-align:center;color:#718096">Data tidak ditemukan</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
