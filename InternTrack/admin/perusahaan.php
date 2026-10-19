<?php
$page_title = 'Data Perusahaan';
require_once __DIR__ . '/../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$search = trim($_GET['search'] ?? '');

if ($action === 'delete' && $id > 0) {
    $conn->query("DELETE FROM perusahaan WHERE id = $id");
    setFlash('success', 'Data perusahaan berhasil dihapus.');
    header('Location: perusahaan.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_perusahaan'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $bidang = trim($_POST['bidang'] ?? '');
    $kontak = trim($_POST['kontak'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $edit_id = (int)($_POST['id'] ?? 0);

    $errors = [];
    if (empty($nama)) $errors[] = 'Nama perusahaan wajib diisi.';
    if (empty($alamat)) $errors[] = 'Alamat wajib diisi.';
    if (empty($bidang)) $errors[] = 'Bidang wajib diisi.';

    if (empty($errors)) {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE perusahaan SET nama_perusahaan=?, alamat=?, bidang=?, kontak=?, email=? WHERE id=?");
            $stmt->bind_param("sssssi", $nama, $alamat, $bidang, $kontak, $email, $edit_id);
            $stmt->execute();
            setFlash('success', 'Data perusahaan berhasil diperbarui.');
        } else {
            $stmt = $conn->prepare("INSERT INTO perusahaan (nama_perusahaan, alamat, bidang, kontak, email) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $nama, $alamat, $bidang, $kontak, $email);
            $stmt->execute();
            setFlash('success', 'Data perusahaan berhasil ditambahkan.');
        }
        header('Location: perusahaan.php');
        exit;
    } else {
        $error_msg = implode('<br>', $errors);
    }
}

if ($action === 'detail' && $id > 0) {
    $data = $conn->query("SELECT * FROM perusahaan WHERE id = $id")->fetch_assoc();
}
?>

<?php if ($action === 'tambah' || $action === 'edit'):
    $d = ['nama_perusahaan'=>'','alamat'=>'','bidang'=>'','kontak'=>'','email'=>''];
    if ($action === 'edit' && $id > 0) $d = $conn->query("SELECT * FROM perusahaan WHERE id=$id")->fetch_assoc();
?>
<div class="card">
    <div class="card-header">
        <h3><?= $action==='tambah'?'Tambah':'Edit' ?> Perusahaan</h3>
        <a href="perusahaan.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label>Nama Perusahaan *</label>
                <input type="text" name="nama_perusahaan" class="form-control" value="<?= e($d['nama_perusahaan']) ?>" required>
            </div>
            <div class="form-group">
                <label>Alamat *</label>
                <textarea name="alamat" class="form-control" required><?= e($d['alamat']) ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Bidang *</label>
                    <input type="text" name="bidang" class="form-control" value="<?= e($d['bidang']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Kontak</label>
                    <input type="text" name="kontak" class="form-control" value="<?= e($d['kontak']??'') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($d['email']??'') ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="perusahaan.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php elseif ($action === 'detail' && $data): ?>
<div class="card">
    <div class="card-header">
        <h3>Detail Perusahaan</h3>
        <a href="perusahaan.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <table>
            <tr><th width="180">Nama</th><td><?= e($data['nama_perusahaan']) ?></td></tr>
            <tr><th>Alamat</th><td><?= e($data['alamat']) ?></td></tr>
            <tr><th>Bidang</th><td><?= e($data['bidang']) ?></td></tr>
            <tr><th>Kontak</th><td><?= e($data['kontak']) ?></td></tr>
            <tr><th>Email</th><td><?= e($data['email']) ?></td></tr>
        </table>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3>Daftar Perusahaan</h3>
        <a href="?action=tambah" class="btn btn-sm btn-primary">+ Tambah Perusahaan</a>
    </div>
    <div class="card-body">
        <form method="GET" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Cari nama / bidang..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            <a href="perusahaan.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>No</th><th>Nama Perusahaan</th><th>Bidang</th><th>Kontak</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT * FROM perusahaan WHERE 1=1";
                if ($search) $sql .= " AND (nama_perusahaan LIKE '%$search%' OR bidang LIKE '%$search%')";
                $sql .= " ORDER BY nama_perusahaan";
                $res = $conn->query($sql);
                $no = 1;
                while ($r = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= e($r['nama_perusahaan']) ?></td>
                        <td><?= e($r['bidang']) ?></td>
                        <td><?= e($r['kontak']) ?></td>
                        <td style="white-space:nowrap">
                            <a href="?action=detail&id=<?= $r['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                            <a href="?action=edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?action=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
