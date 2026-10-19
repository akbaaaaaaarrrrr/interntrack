<?php
$page_title = 'Monitoring PKL';
require_once __DIR__ . '/../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $conn->query("DELETE FROM monitoring WHERE id = $id");
    setFlash('success', 'Data monitoring berhasil dihapus.');
    header('Location: monitoring.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $pembimbing_id = (int)($_POST['pembimbing_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');
    $nilai = (int)($_POST['nilai'] ?? 0);
    $rekomendasi = trim($_POST['rekomendasi'] ?? '');
    $edit_id = (int)($_POST['id'] ?? 0);

    $errors = [];
    if (!$siswa_id) $errors[] = 'Siswa wajib dipilih.';
    if (!$pembimbing_id) $errors[] = 'Pembimbing wajib dipilih.';
    if (empty($tanggal)) $errors[] = 'Tanggal wajib diisi.';
    if ($nilai < 0 || $nilai > 100) $errors[] = 'Nilai harus antara 0-100.';

    if (empty($errors)) {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE monitoring SET siswa_id=?, pembimbing_id=?, tanggal=?, catatan=?, nilai=?, rekomendasi=? WHERE id=?");
            $stmt->bind_param("iissisi", $siswa_id, $pembimbing_id, $tanggal, $catatan, $nilai, $rekomendasi, $edit_id);
            $stmt->execute();
            setFlash('success', 'Data monitoring berhasil diperbarui.');
        } else {
            $stmt = $conn->prepare("INSERT INTO monitoring (siswa_id, pembimbing_id, tanggal, catatan, nilai, rekomendasi) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("iissis", $siswa_id, $pembimbing_id, $tanggal, $catatan, $nilai, $rekomendasi);
            $stmt->execute();
            setFlash('success', 'Data monitoring berhasil ditambahkan.');
        }
        header('Location: monitoring.php');
        exit;
    } else {
        $error_msg = implode('<br>', $errors);
    }
}

$siswa_opt = $conn->query("SELECT id, nama, nis FROM siswa ORDER BY nama");
$pembimbing_opt = $conn->query("SELECT id, nama FROM pembimbing ORDER BY nama");
?>

<?php if ($action === 'tambah' || $action === 'edit'):
    $d = ['siswa_id'=>0,'pembimbing_id'=>0,'tanggal'=>date('Y-m-d'),'catatan'=>'','nilai'=>0,'rekomendasi'=>''];
    if ($action === 'edit' && $id > 0) $d = $conn->query("SELECT * FROM monitoring WHERE id=$id")->fetch_assoc();
?>
<div class="card">
    <div class="card-header">
        <h3><?= $action==='tambah'?'Tambah':'Edit' ?> Monitoring</h3>
        <a href="monitoring.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label>Siswa *</label>
                <select name="siswa_id" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php $siswa_opt->data_seek(0); while ($s = $siswa_opt->fetch_assoc()): ?>
                        <option value="<?= $s['id'] ?>" <?= ($d['siswa_id']==$s['id'])?'selected':'' ?>><?= e($s['nis'].' - '.$s['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Pembimbing *</label>
                <select name="pembimbing_id" class="form-control" required>
                    <option value="">-- Pilih Pembimbing --</option>
                    <?php $pembimbing_opt->data_seek(0); while ($pb = $pembimbing_opt->fetch_assoc()): ?>
                        <option value="<?= $pb['id'] ?>" <?= ($d['pembimbing_id']==$pb['id'])?'selected':'' ?>><?= e($pb['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Monitoring *</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= e($d['tanggal']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Nilai (0-100) *</label>
                    <input type="number" name="nilai" class="form-control" min="0" max="100" value="<?= e($d['nilai']) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control"><?= e($d['catatan']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Rekomendasi</label>
                <textarea name="rekomendasi" class="form-control"><?= e($d['rekomendasi']) ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="monitoring.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3>Data Monitoring</h3>
        <a href="?action=tambah" class="btn btn-sm btn-primary">+ Tambah Monitoring</a>
    </div>
    <div class="card-body table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Siswa</th>
                    <th>Pembimbing</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                    <th>Rekomendasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = $conn->query("
                SELECT m.*, s.nama as nama_siswa, s.nis, pb.nama as nama_pembimbing
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
                    <td><?= e($r['nis'].' - '.$r['nama_siswa']) ?></td>
                    <td><?= e($r['nama_pembimbing']) ?></td>
                    <td><strong><?= $r['nilai'] ?></strong></td>
                    <td><?= e(mb_strimwidth($r['catatan']??'', 0, 50, '...')) ?></td>
                    <td><?= e(mb_strimwidth($r['rekomendasi']??'', 0, 40, '...')) ?></td>
                    <td style="white-space:nowrap">
                        <a href="?action=edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?action=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
