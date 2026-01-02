<?php
session_start();

// Include koneksi untuk remember me
require_once __DIR__ . '/../../koneksi/koneksi.php';

// Konfigurasi timeout
$session_timeout = 3600; // 1 jam

function isLoggedIn() {
    global $session_timeout;
    
    // Cek session biasa
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // Cek session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
            // Session expired
            session_destroy();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    // Cek remember me cookie
    if (isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
        global $conn;
        
        $user_id = intval($_COOKIE['remember_user']);
        $token = $_COOKIE['remember_token'];
        $hashed_token = hash('sha256', $token);
        
        // Cari user dengan token yang valid
        $stmt = $conn->prepare("SELECT id_user, nama, email, role FROM users WHERE id_user = ? AND remember_token = ? AND token_expiry > NOW()");
        if ($stmt) {
            $stmt->bind_param("is", $user_id, $hashed_token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Set session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_name'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
    }
    
    return false;
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin($redirect_url = '/auth/login/login.php') {
    if (!isLoggedIn()) {
        // Simpan URL yang diminta untuk redirect setelah login
        if ($redirect_url === '/auth/login/login.php') {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        header("Location: $redirect_url");
        exit();
    }
}

function requireAdmin($redirect_url = '/dashboard/dashboard.php') {
    requireLogin();
    if (!isAdmin()) {
        header("Location: $redirect_url");
        exit();
    }
}

function getUserInfo() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
    return null;
}

function checkSessionTimeout() {
    global $session_timeout;
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
        session_destroy();
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, '/');
        }
        
        header("Location: /auth/login/login.php?message=" . urlencode("Session telah habis. Silakan login kembali.") . "&type=warning");
        exit();
    }
    
    // Update last activity
    if (isset($_SESSION['logged_in'])) {
        $_SESSION['last_activity'] = time();
    }
}

// Jalankan pengecekan session timeout
checkSessionTimeout();
?>