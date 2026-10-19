<?php
/**
 * InternTrack - Entry Point
 * Redirect ke login atau dashboard sesuai role
 */
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: siswa/dashboard.php');
    }
} else {
    header('Location: auth/login.php');
}
exit;
?>
