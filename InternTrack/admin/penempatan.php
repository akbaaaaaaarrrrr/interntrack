<?php
$page_title = 'Penempatan PKL';
require_once __DIR__ . '/../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$filter_status = trim($_GET['status'] ?? '');
$filter_perusahaan = (int)($_GET['perusahaan'] ?? 0);
$filter_pembimbing = (int)($_GET['pembimbing'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $conn->query("DELETE FROM penempatan WHERE id = $id");
    setFlash('success', 'Data penempatan berhasil dihapus.');
    header('Location: penempatan.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $perusahaan_id = (int)($_POST['perusahaan_id'] ?? 0);
    $pembimbing_id = (int)($_POST['pembimbing_id'] ?? 0);
    $tgl_mulai = $_POST['tanggal_mulai'] ?? '';
    $tgl_selesai = $_POST['tanggal_selesai'] ?? '';
    $status = $_POST['status'] ?? 'Belum Mulai';
    $edit_id = (int)($_POST['id'] ?? 0);

    $errors = [];
    if (!$siswa_id) $errors[] = 'Siswa wajib dipilih.';
    if (!$perusahaan_id) $errors[] = 'Perusahaan wajib dipilih.';
    if (!$pembimbing_id) $errors[] = 'Pembimbing wajib dipilih.';
    if (empty($tgl_mulai)) $errors[] = 'Tanggal mulai wajib diisi.';
    if (empty($tgl_selesai)) $errors[] = 'Tanggal selesai wajib diisi.';

    if (empty($errors)) {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE penempatan SET siswa_id=?, perusahaan_id=?, pembimbing_id=?, tanggal_mulai=?, tanggal_selesai=?, status=? WHERE id=?");
            $stmt->bind_param("iiisssi", $siswa_id, $perusahaan_id, $pembimbing_id, $tgl_mulai, $tgl_selesai, $status, $edit_id);
            $stmt->execute();
            setFlash('success', 'Data penempatan berhasil diperbarui.');
        } else {
            $stmt = $conn->prepare("INSERT INTO penempatan (siswa_id, perusahaan_id, pembimbing_id, tanggal_mulai, tanggal_selesai, status) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("iiisss", $siswa_id, $perusahaan_id, $pembimbing_id, $tgl_mulai, $tgl_selesai, $status);
            $stmt->execute();
            setFlash('success', 'Data penempatan berhasil ditambahkan.');
        }
        header('Location: penempatan.php');
        exit;
    } else {
        $error_msg = implode('<br>', $errors);
    }
}

$siswa_opt = $conn->query("SELECT id, nama, nis FROM siswa ORDER BY nama");
$perusahaan_opt = $conn->query("SELECT id, nama_perusahaan FROM perusahaan ORDER BY nama_perusahaan");
$pembimbing_opt = $conn->query("SELECT id, nama FROM pembimbing ORDER BY nama");
?>

<?php if ($action === 'tambah' || $action === 'edit'):
    $d = ['siswa_id'=>0,'perusahaan_id'=>0,'pembimbing_id'=>0,'tanggal_mulai'=>'','tanggal_selesai'=>'','status'=>'Belum Mulai'];
    if ($action === 'edit' && $id > 0) $d = $conn->query("SELECT * FROM penempatan WHERE id=$id")->fetch_assoc();
?>
<div class="card">
    <div class="card-header">
        <h3><?= $action==='tambah'?'Tambah':'Edit' ?> Penempatan PKL</h3>
        <a href="penempatan.php" class="btn btn-sm btn-outline">← Kembali</a>
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
                        <option value="<?= $s['id'] ?>" <?= ($d['siswa_id']==$s['id'])?'selected':'' ?>>
                            <?= e($s['nis'].' - '.$s['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Perusahaan *</label>
                <select name="perusahaan_id" class="form-control" required>
                    <option value="">-- Pilih Perusahaan --</option>
                    <?php $perusahaan_opt->data_seek(0); while ($p = $perusahaan_opt->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>" <?= ($d['perusahaan_id']==$p['id'])?'selected':'' ?>>
                            <?= e($p['nama_perusahaan']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Pembimbing *</label>
                <select name="pembimbing_id" class="form-control" required>
                    <option value="">-- Pilih Pembimbing --</option>
                    <?php $pembimbing_opt->data_seek(0); while ($pb = $pembimbing_opt->fetch_assoc()): ?>
                        <option value="<?= $pb['id'] ?>" <?= ($d['pembimbing_id']==$pb['id'])?'selected':'' ?>>
                            <?= e($pb['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Mulai *</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= e($d['tanggal_mulai']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Selesai *</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="<?= e($d['tanggal_selesai']) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" class="form-control" required>
                    <option value="Belum Mulai" <?= $d['status']=='Belum Mulai'?'selected':'' ?>>Belum Mulai</option>
                    <option value="Aktif" <?= $d['status']=='Aktif'?'selected':'' ?>>Aktif</option>
                    <option value="Selesai" <?= $d['status']=='Selesai'?'selected':'' ?>>Selesai</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="penempatan.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3>Daftar Penempatan PKL</h3>
        <a href="?action=tambah" class="btn btn-sm btn-primary">+ Tambah Penempatan</a>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-group">
            <select name="status" class="form-control" style="max-width:160px">
                <option value="">Semua Status</option>
                <option value="Belum Mulai" <?= $filter_status=='Belum Mulai'?'selected':'' ?>>Belum Mulai</option>
                <option value="Aktif" <?= $filter_status=='Aktif'?'selected':'' ?>>Aktif</option>
                <option value="Selesai" <?= $filter_status=='Selesai'?'selected':'' ?>>Selesai</option>
            </select>
            <select name="perusahaan" class="form-control" style="max-width:220px">
                <option value="">Semua Perusahaan</option>
                <?php $perusahaan_opt->data_seek(0); while ($p = $perusahaan_opt->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>" <?= $filter_perusahaan==$p['id']?'selected':'' ?>><?= e($p['nama_perusahaan']) ?></option>
                <?php endwhile; ?>
            </select>
            <select name="pembimbing" class="form-control" style="max-width:200px">
                <option value="">Semua Pembimbing</option>
                <?php $pembimbing_opt->data_seek(0); while ($pb = $pembimbing_opt->fetch_assoc()): ?>
                    <option value="<?= $pb['id'] ?>" <?= $filter_pembimbing==$pb['id']?'selected':'' ?>><?= e($pb['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="penempatan.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Perusahaan</th>
                        <th>Pembimbing</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT p.*, s.nama as nama_siswa, s.nis, pr.nama_perusahaan, pb.nama as nama_pembimbing
                        FROM penempatan p
                        JOIN siswa s ON p.siswa_id = s.id
                        JOIN perusahaan pr ON p.perusahaan_id = pr.id
                        JOIN pembimbing pb ON p.pembimbing_id = pb.id
                        WHERE 1=1";
                if ($filter_status) $sql .= " AND p.status = '$filter_status'";
                if ($filter_perusahaan) $sql .= " AND p.perusahaan_id = $filter_perusahaan";
                if ($filter_pembimbing) $sql .= " AND p.pembimbing_id = $filter_pembimbing";
                $sql .= " ORDER BY p.id DESC";
                $res = $conn->query($sql);
                $no = 1;
                while ($r = $res->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= e($r['nis'].' - '.$r['nama_siswa']) ?></td>
                        <td><?= e($r['nama_perusahaan']) ?></td>
                        <td><?= e($r['nama_pembimbing']) ?></td>
                        <td><?= formatTanggal($r['tanggal_mulai']) ?> s/d <?= formatTanggal($r['tanggal_selesai']) ?></td>
                        <td><span class="badge <?= badgeStatus($r['status']) ?>"><?= e($r['status']) ?></span></td>
                        <td style="white-space:nowrap">
                            <a href="?action=edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?action=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($res->num_rows == 0): ?>
                    <tr><td colspan="7" style="text-align:center;color:#718096">Data tidak ditemukan</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
