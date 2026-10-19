<?php
$page_title = 'Kehadiran';
require_once __DIR__ . '/../includes/header_siswa.php';

$siswa_id = $_SESSION['siswa_id'] ?? 0;
$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $status = $_POST['status'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');

    $errors = [];
    if (empty($tanggal)) $errors[] = 'Tanggal wajib diisi.';
    if (empty($status)) $errors[] = 'Status wajib dipilih.';

    // Cek duplikat
    $cek = $conn->prepare("SELECT id FROM absensi WHERE siswa_id=? AND tanggal=?");
    $cek->bind_param("is", $siswa_id, $tanggal);
    $cek->execute();
    if ($cek->get_result()->num_rows > 0) {
        $errors[] = 'Absensi untuk tanggal tersebut sudah diisi.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO absensi (siswa_id, tanggal, status, keterangan) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $siswa_id, $tanggal, $status, $keterangan);
        $stmt->execute();
        setFlash('success', 'Absensi berhasil disimpan.');
        header('Location: absensi.php');
        exit;
    } else {
        $error_msg = implode('<br>', $errors);
    }
}

$pct = hitungPersentaseKehadiran($siswa_id, $conn);
$status_kh = statusKehadiran($pct);
?>

<?php if ($action === 'tambah'): ?>
<div class="card">
    <div class="card-header">
        <h3>Input Kehadiran</h3>
        <a href="absensi.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Tanggal *</label>
                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" placeholder="Opsional (wajib jika Izin/Sakit)">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                <a href="absensi.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div class="alert alert-info">
    Persentase Kehadiran Anda: <strong><?= $pct ?>%</strong> —
    Status: <span class="badge badge-<?= strtolower(str_replace(' ','',$status_kh)) ?>"><?= $status_kh ?></span>
</div>

<div class="card">
    <div class="card-header">
        <h3>Riwayat Kehadiran</h3>
        <a href="?action=tambah" class="btn btn-sm btn-primary">+ Input Absensi</a>
    </div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT * FROM absensi WHERE siswa_id=$siswa_id ORDER BY tanggal DESC");
            $no = 1;
            while ($r = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= formatTanggal($r['tanggal']) ?></td>
                    <td><span class="badge <?= badgeStatus($r['status']) ?>"><?= e($r['status']) ?></span></td>
                    <td><?= e($r['keterangan'] ?: '-') ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if ($res->num_rows == 0): ?>
                <tr><td colspan="4" style="text-align:center;color:#718096">Belum ada data absensi</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
