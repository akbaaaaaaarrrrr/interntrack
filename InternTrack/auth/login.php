<?php
require_once __DIR__ . '/../includes/functions.php';

// Jika sudah login, redirect
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi
    if (empty($username) || empty($password)) {
        $error = 'Username/NIS dan password wajib diisi.';
    } else {
        $user = null;

        // 1. Coba login dengan username (admin / username biasa)
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
        } else {
            // 2. Coba login dengan NIS (siswa)
            $stmt2 = $conn->prepare("
                SELECT u.id, u.username, u.password, u.role, s.id AS siswa_id, s.nama
                FROM siswa s
                JOIN users u ON s.user_id = u.id
                WHERE s.nis = ?
            ");
            $stmt2->bind_param("s", $username);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows === 1) {
                $user = $result2->fetch_assoc();
            }
        }

        if ($user && md5($password) === $user['password']) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            if ($user['role'] === 'siswa') {
                // Ambil data siswa jika belum dari query NIS
                if (isset($user['siswa_id'])) {
                    $_SESSION['siswa_id'] = $user['siswa_id'];
                    $_SESSION['nama']     = $user['nama'];
                } else {
                    $s = $conn->query("SELECT id, nama FROM siswa WHERE user_id = {$user['id']}")->fetch_assoc();
                    $_SESSION['siswa_id'] = $s['id'] ?? null;
                    $_SESSION['nama']     = $s['nama'] ?? $user['username'];
                }
            } else {
                $_SESSION['nama'] = 'Administrator';
            }

            header('Location: ../index.php');
            exit;
        } else {
            $error = 'Username/NIS atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InternTrack</title>
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <img src="../assets/img/logo.svg" alt="InternTrack" width="72" height="72">
        </div>
        <h1>InternTrack</h1>
        <p class="subtitle">Sistem Monitoring Kegiatan PKL Siswa</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username / NIS</label>
                <input type="text" id="username" name="username" class="form-control" 
                       placeholder="Admin: username | Siswa: NIS" value="<?= e($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>

        <div class="login-demo">
            <strong><i class="fas fa-info-circle"></i> Demo Login:</strong><br>
            Admin: <code>admin</code> / <code>admin123</code><br>
            Siswa: <code>2024001</code> / <code>siswa123</code> (pakai NIS)
        </div>
    </div>
</body>
</html>
