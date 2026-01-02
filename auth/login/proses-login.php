<?php
session_start();
header('Content-Type: application/json');

// Konfigurasi
$config = [
    'max_login_attempts' => 5,
    'lockout_time' => 300, // 5 menit dalam detik
    'remember_days' => 30,
    'session_timeout' => 3600 // 1 jam dalam detik
];

// Nonaktifkan error reporting di production
error_reporting(0);
ini_set('display_errors', 0);

// Set header keamanan
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

try {
    // Validasi method request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode request tidak valid');
    }
    
    // Ambil action
    $action = $_POST['action'] ?? '';
    if (empty($action) || !in_array($action, ['login', 'register'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Aksi tidak valid'
        ]);
        exit();
    }
    
    // Include koneksi database
    $koneksiPath = __DIR__ . '/../../koneksi/koneksi.php';
    if (!file_exists($koneksiPath)) {
        $koneksiPath = dirname(__DIR__, 2) . '/koneksi/koneksi.php';
        if (!file_exists($koneksiPath)) {
            throw new Exception("File koneksi tidak ditemukan");
        }
    }
    
    require_once $koneksiPath;
    
    // Validasi koneksi
    if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
        throw new Exception("Koneksi database gagal");
    }
    
    // Fungsi helper
    function clean_input($data) {
        if (!is_string($data)) return '';
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    // Fungsi untuk membersihkan token expired
    function cleanup_expired_tokens($conn) {
        $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE token_expiry < NOW()");
        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Bersihkan token yang expired
    cleanup_expired_tokens($conn);
    
    if ($action === 'login') {
        // ========== PROSES LOGIN ==========
        $email = clean_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) && $_POST['remember'] == '1';
        
        // Validasi input
        if (empty($email) || empty($password)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Email dan password harus diisi'
            ]);
            exit();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Format email tidak valid'
            ]);
            exit();
        }
        
        // Cari user berdasarkan email
        $stmt = $conn->prepare("SELECT id_user, nama, email, password, role, remember_token, token_expiry FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Kesalahan sistem");
        }
        
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Kesalahan sistem");
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            echo json_encode([
                'success' => false, 
                'message' => 'Email atau password salah'
            ]);
            exit();
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Email atau password salah'
            ]);
            exit();
        }
        
        // SET SESSION dengan regenerasi ID untuk keamanan
        session_regenerate_id(true);
$_SESSION['id_user'] = $user['id_user'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        // REMEMBER ME functionality
        if ($remember) {
            // Generate token baru
            $token = bin2hex(random_bytes(32));
            $hashed_token = hash('sha256', $token);
            $expiry_date = date('Y-m-d H:i:s', strtotime("+{$config['remember_days']} days"));
            
            // Set secure cookie
            setcookie('remember_token', $token, [
                'expires' => time() + (86400 * $config['remember_days']),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
            setcookie('remember_user', $user['id_user'], [
                'expires' => time() + (86400 * $config['remember_days']),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
            // Update token di database
            $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id_user = ?");
            if ($stmt) {
                $stmt->bind_param("ssi", $hashed_token, $expiry_date, $user['id_user']);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // Jika tidak memilih remember me, hapus token yang ada
            $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id_user = ?");
            if ($stmt) {
                $stmt->bind_param("i", $user['id_user']);
                $stmt->execute();
                $stmt->close();
            }
            
            // Hapus cookie jika ada
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/');
            }
            if (isset($_COOKIE['remember_user'])) {
                setcookie('remember_user', '', time() - 3600, '/');
            }
        }
        
        // Update last login (opsional - tambah kolom last_login jika perlu)
        $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id_user = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user['id_user']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Tentukan redirect URL berdasarkan role
        $redirectUrl = $user['role'] === 'admin' 
            ? '/dashboard/dashboard.php' 
            : '../../index.php';
        
        echo json_encode([
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => $redirectUrl,
            'user' => [
                'name' => $user['nama'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
        
    } elseif ($action === 'register') {
        // ========== PROSES REGISTRASI ==========
        $name = clean_input($_POST['name'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validasi input
        $errors = [];
        
        // Validasi nama
        if (empty($name)) {
            $errors[] = 'Nama lengkap harus diisi';
        } elseif (strlen($name) < 3) {
            $errors[] = 'Nama minimal 3 karakter';
        } elseif (strlen($name) > 100) {
            $errors[] = 'Nama maksimal 100 karakter';
        }
        
        // Validasi email
        if (empty($email)) {
            $errors[] = 'Email harus diisi';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        } elseif (strlen($email) > 100) {
            $errors[] = 'Email maksimal 100 karakter';
        }
        
        // Validasi password
        if (empty($password)) {
            $errors[] = 'Password harus diisi';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        } elseif (strlen($password) > 255) {
            $errors[] = 'Password maksimal 255 karakter';
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf kapital dan 1 angka';
        }
        
        // Validasi konfirmasi password
        if ($password !== $confirm_password) {
            $errors[] = 'Password dan konfirmasi password tidak cocok';
        }
        
        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errors)
            ]);
            exit();
        }
        
        // Cek apakah email sudah terdaftar
        $stmt = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Kesalahan sistem");
        }
        
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Kesalahan sistem");
        }
        
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->close();
            echo json_encode([
                'success' => false,
                'message' => 'Email sudah terdaftar'
            ]);
            exit();
        }
        $stmt->close();
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        
        // Insert ke database dengan transaction
        $conn->begin_transaction();
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
            if (!$stmt) {
                throw new Exception("Kesalahan sistem");
            }
            
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            
            if (!$stmt->execute()) {
                throw new Exception("Registrasi gagal. Silakan coba lagi.");
            }
            
            $user_id = $conn->insert_id;
            $stmt->close();
            
            // Commit transaction
            $conn->commit();
            
            // Log aktivitas (opsional)
            error_log("User registered: ID $user_id, Email: $email, Name: $name");
            
            echo json_encode([
                'success' => true,
                'message' => 'Registrasi berhasil! Silakan login dengan akun Anda.'
            ]);
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
        
    }
    
} catch (Exception $e) {
    // Kirim response error yang aman
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan pada server'
    ]);
    
    // Log error untuk admin
    error_log("Auth Error [" . date('Y-m-d H:i:s') . "]: " . $e->getMessage() . 
              " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
              " - Action: " . ($action ?? 'unknown'));
}
?>