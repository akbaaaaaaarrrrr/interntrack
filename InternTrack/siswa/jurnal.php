<?php
$page_title = 'Jurnal Kegiatan';
require_once __DIR__ . '/../includes/header_siswa.php';

$siswa_id = $_SESSION['siswa_id'] ?? 0;
$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $kegiatan = trim($_POST['kegiatan'] ?? '');
    $hasil = trim($_POST['hasil'] ?? '');
    $kendala = trim($_POST['kendala'] ?? '');

    $errors = [];
    if (empty($tanggal)) $errors[] = 'Tanggal wajib diisi.';
    if (empty($kegiatan)) $errors[] = 'Kegiatan wajib diisi.';

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO jurnal (siswa_id, tanggal, kegiatan, hasil, kendala) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issss", $siswa_id, $tanggal, $kegiatan, $hasil, $kendala);
        $stmt->execute();
        setFlash('success', 'Jurnal berhasil disimpan.');
        header('Location: jurnal.php');
        exit;
    } else {
        $error_msg = implode('<br>', $errors);
    }
}
?>

<?php if ($action === 'tambah'): ?>
<div class="card">
    <div class="card-header">
        <h3>Input Jurnal Kegiatan</h3>
        <a href="jurnal.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Tanggal *</label>
                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Kegiatan *</label>
                <textarea name="kegiatan" class="form-control" placeholder="Apa yang dikerjakan hari ini?" required></textarea>
            </div>
            <div class="form-group">
                <label>Hasil</label>
                <textarea name="hasil" class="form-control" placeholder="Hasil yang dicapai"></textarea>
            </div>
            <div class="form-group">
                <label>Kendala</label>
                <textarea name="kendala" class="form-control" placeholder="Kendala yang dihadapi (jika ada)"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Jurnal</button>
                <a href="jurnal.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3>Jurnal Kegiatan Saya</h3>
        <a href="?action=tambah" class="btn btn-sm btn-primary">+ Input Jurnal</a>
    </div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kegiatan</th>
                    <th>Hasil</th>
                    <th>Kendala</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT * FROM jurnal WHERE siswa_id=$siswa_id ORDER BY tanggal DESC");
            $no = 1;
            while ($r = $res->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= formatTanggal($r['tanggal']) ?></td>
                    <td><?= e($r['kegiatan']) ?></td>
                    <td><?= e($r['hasil'] ?: '-') ?></td>
                    <td><?= e($r['kendala'] ?: '-') ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if ($res->num_rows == 0): ?>
                <tr><td colspan="5" style="text-align:center;color:#718096">Belum ada jurnal. Silakan input jurnal kegiatan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
