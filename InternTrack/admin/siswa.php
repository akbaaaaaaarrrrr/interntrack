<?php
$page_title = 'Data Siswa';
require_once __DIR__ . '/../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$search = trim($_GET['search'] ?? '');
$filter_kelas = trim($_GET['kelas'] ?? '');

// Hapus
if ($action === 'delete' && $id > 0) {
    $conn->query("DELETE FROM siswa WHERE id = $id");
    setFlash('success', 'Data siswa berhasil dihapus.');
    header('Location: siswa.php');
    exit;
}

// Simpan (tambah/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $edit_id = (int)($_POST['id'] ?? 0);

    $errors = [];
    if (empty($nis)) $errors[] = 'NIS tidak boleh kosong.';
    if (empty($nama)) $errors[] = 'Nama tidak boleh kosong.';
    if (empty($kelas)) $errors[] = 'Kelas tidak boleh kosong.';

    // Cek duplikat NIS
    $check = $conn->prepare("SELECT id FROM siswa WHERE nis = ? AND id != ?");
    $check->bind_param("si", $nis, $edit_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $errors[] = 'NIS sudah terdaftar (tidak boleh duplikat).';
    }

    if (empty($errors)) {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE siswa SET nis=?, nama=?, kelas=?, jurusan=?, no_hp=?, alamat=? WHERE id=?");
            $stmt->bind_param("ssssssi", $nis, $nama, $kelas, $jurusan, $no_hp, $alamat, $edit_id);
            $stmt->execute();
            setFlash('success', 'Data siswa berhasil diperbarui.');
        } else {
            $stmt = $conn->prepare("INSERT INTO siswa (nis, nama, kelas, jurusan, no_hp, alamat) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $nis, $nama, $kelas, $jurusan, $no_hp, $alamat);
            $stmt->execute();
            setFlash('success', 'Data siswa berhasil ditambahkan.');
        }
        header('Location: siswa.php');
        exit;
    } else {
        $error_msg = implode('<br>', $errors);
    }
}

// Detail
if ($action === 'detail' && $id > 0) {
    $siswa = $conn->query("SELECT * FROM siswa WHERE id = $id")->fetch_assoc();
    $penempatan = $conn->query("
        SELECT p.*, pr.nama_perusahaan, pb.nama as nama_pembimbing
        FROM penempatan p
        JOIN perusahaan pr ON p.perusahaan_id = pr.id
        JOIN pembimbing pb ON p.pembimbing_id = pb.id
        WHERE p.siswa_id = $id
    ")->fetch_assoc();
    $pct = hitungPersentaseKehadiran($id, $conn);
    $status_kh = statusKehadiran($pct);
}
?>

<?php if ($action === 'tambah' || $action === 'edit'): 
    $data = ['nis'=>'','nama'=>'','kelas'=>'','jurusan'=>'Rekayasa Perangkat Lunak','no_hp'=>'','alamat'=>''];
    if ($action === 'edit' && $id > 0) {
        $data = $conn->query("SELECT * FROM siswa WHERE id = $id")->fetch_assoc();
    }
?>
<div class="card">
    <div class="card-header">
        <h3><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> Data Siswa</h3>
        <a href="siswa.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>NIS *</label>
                    <input type="text" name="nis" class="form-control" value="<?= e($data['nis']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama *</label>
                    <input type="text" name="nama" class="form-control" value="<?= e($data['nama']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Kelas *</label>
                    <select name="kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <option value="XII RPL 1" <?= ($data['kelas']??'')=='XII RPL 1'?'selected':'' ?>>XII RPL 1</option>
                        <option value="XII RPL 2" <?= ($data['kelas']??'')=='XII RPL 2'?'selected':'' ?>>XII RPL 2</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jurusan *</label>
                    <input type="text" name="jurusan" class="form-control" value="<?= e($data['jurusan']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= e($data['no_hp']??'') ?>">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?= e($data['alamat']??'') ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="siswa.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php elseif ($action === 'detail' && $siswa): ?>
<div class="card">
    <div class="card-header">
        <h3>Detail Siswa</h3>
        <a href="siswa.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <table>
            <tr><th width="180">NIS</th><td><?= e($siswa['nis']) ?></td></tr>
            <tr><th>Nama</th><td><?= e($siswa['nama']) ?></td></tr>
            <tr><th>Kelas</th><td><?= e($siswa['kelas']) ?></td></tr>
            <tr><th>Jurusan</th><td><?= e($siswa['jurusan']) ?></td></tr>
            <tr><th>No. HP</th><td><?= e($siswa['no_hp']) ?></td></tr>
            <tr><th>Alamat</th><td><?= e($siswa['alamat']) ?></td></tr>
            <tr><th>Persentase Kehadiran</th>
                <td><?= $pct ?>% — <span class="badge badge-<?= strtolower(str_replace(' ','',$status_kh)) ?>"><?= $status_kh ?></span></td>
            </tr>
            <?php if ($penempatan): ?>
            <tr><th>Tempat PKL</th><td><?= e($penempatan['nama_perusahaan']) ?></td></tr>
            <tr><th>Pembimbing</th><td><?= e($penempatan['nama_pembimbing']) ?></td></tr>
            <tr><th>Status PKL</th>
                <td><span class="badge badge-<?= $penempatan['status']=='Aktif'?'aktif':($penempatan['status']=='Selesai'?'selesai':'belum') ?>"><?= e($penempatan['status']) ?></span></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php else: ?>
<!-- LIST -->
<div class="card">
    <div class="card-header">
        <h3>Daftar Siswa PKL</h3>
        <a href="?action=tambah" class="btn btn-sm btn-primary">+ Tambah Siswa</a>
    </div>
    <div class="card-body">
        <form method="GET" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Cari nama / NIS..." value="<?= e($search) ?>">
            <select name="kelas" class="form-control" style="max-width:160px">
                <option value="">Semua Kelas</option>
                <option value="XII RPL 1" <?= $filter_kelas=='XII RPL 1'?'selected':'' ?>>XII RPL 1</option>
                <option value="XII RPL 2" <?= $filter_kelas=='XII RPL 2'?'selected':'' ?>>XII RPL 2</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            <a href="siswa.php" class="btn btn-outline btn-sm">Reset</a>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>No. HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM siswa WHERE 1=1";
                    if ($search) $sql .= " AND (nama LIKE '%$search%' OR nis LIKE '%$search%')";
                    if ($filter_kelas) $sql .= " AND kelas = '$filter_kelas'";
                    $sql .= " ORDER BY nama ASC";
                    $result = $conn->query($sql);
                    $no = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= e($row['nis']) ?></td>
                        <td><?= e($row['nama']) ?></td>
                        <td><?= e($row['kelas']) ?></td>
                        <td><?= e($row['jurusan']) ?></td>
                        <td><?= e($row['no_hp']) ?></td>
                        <td style="white-space:nowrap">
                            <a href="?action=detail&id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                            <a href="?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows == 0): ?>
                    <tr><td colspan="7" style="text-align:center;color:#718096">Data tidak ditemukan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
