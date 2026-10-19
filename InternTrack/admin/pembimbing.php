<?php
$page_title = 'Data Pembimbing';
require_once __DIR__ . '/../includes/header_admin.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$search = trim($_GET['search'] ?? '');

if ($action === 'delete' && $id > 0) {
    $conn->query("DELETE FROM pembimbing WHERE id = $id");
    setFlash('success', 'Data pembimbing berhasil dihapus.');
    header('Location: pembimbing.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $edit_id = (int)($_POST['id'] ?? 0);

    $errors = [];
    if (empty($nama)) $errors[] = 'Nama wajib diisi.';

    if (empty($errors)) {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE pembimbing SET nip=?, nama=?, no_hp=?, email=? WHERE id=?");
            $stmt->bind_param("ssssi", $nip, $nama, $no_hp, $email, $edit_id);
            $stmt->execute();
            setFlash('success', 'Data pembimbing berhasil diperbarui.');
        } else {
            $stmt = $conn->prepare("INSERT INTO pembimbing (nip, nama, no_hp, email) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $nip, $nama, $no_hp, $email);
            $stmt->execute();
            setFlash('success', 'Data pembimbing berhasil ditambahkan.');
        }
        header('Location: pembimbing.php');
        exit;
    } else {
        $error_msg = implode('<br>', $errors);
    }
}
?>

<?php if ($action === 'tambah' || $action === 'edit'):
    $d = ['nip'=>'','nama'=>'','no_hp'=>'','email'=>''];
    if ($action === 'edit' && $id > 0) $d = $conn->query("SELECT * FROM pembimbing WHERE id=$id")->fetch_assoc();
?>
<div class="card">
    <div class="card-header">
        <h3><?= $action==='tambah'?'Tambah':'Edit' ?> Pembimbing</h3>
        <a href="pembimbing.php" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
    <div class="card-body">
        <?php if (!empty($error_msg)): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" class="form-control" value="<?= e($d['nip']??'') ?>">
                </div>
                <div class="form-group">
                    <label>Nama *</label>
                    <input type="text" name="nama" class="form-control" value="<?= e($d['nama']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= e($d['no_hp']??'') ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($d['email']??'') ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="pembimbing.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3>Daftar Guru Pembimbing</h3>
        <a href="?action=tambah" class="btn btn-sm btn-primary">+ Tambah Pembimbing</a>
    </div>
    <div class="card-body">
        <form method="GET" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Cari nama..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </form>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>No</th><th>NIP</th><th>Nama</th><th>No. HP</th><th>Email</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT * FROM pembimbing WHERE 1=1";
                if ($search) $sql .= " AND nama LIKE '%$search%'";
                $sql .= " ORDER BY nama";
                $res = $conn->query($sql);
                $no = 1;
                while ($r = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= e($r['nip']) ?></td>
                        <td><?= e($r['nama']) ?></td>
                        <td><?= e($r['no_hp']) ?></td>
                        <td><?= e($r['email']) ?></td>
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
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
