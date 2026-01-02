<?php
session_start();

// Include koneksi untuk menghapus token
require_once __DIR__ . '/../../koneksi/koneksi.php';

// Hapus remember token dari database jika ada
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id_user = ?");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }
}

// Hapus semua session
$_SESSION = array();

// Hapus session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hapus remember me cookies
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/', '', true, true);
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan
header("Location: ../../index.php?message=" . urlencode("Anda telah berhasil logout.") . "&type=success");
exit();
?>