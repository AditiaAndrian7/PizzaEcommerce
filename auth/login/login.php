<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registrasi</title>
    
    <!-- Security headers meta tags -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' https://cdnjs.cloudflare.com; font-src https://cdnjs.cloudflare.com; img-src 'self' data:;">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    
    <!-- Preconnect untuk CDN -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/auth/login/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Noscript fallback -->
    <noscript>
        <style>
            .container { opacity: 1 !important; }
            .form-container { display: block !important; }
            .hidden { display: none !important; }
            .login-form { display: block !important; }
            .register-form { display: none !important; }
        </style>
        <p style="color: var(--primary-color); text-align: center; padding: 20px; background: var(--bg-light); border-radius: 10px; margin: 20px;">
            JavaScript dinonaktifkan. Harap aktifkan JavaScript untuk menggunakan fitur login.
        </p>
    </noscript>
</head>
<body>
    <!-- Background Decoration -->
    <div class="bg-decoration">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
        <div class="circle circle-4"></div>
    </div>
    
    <div class="container">
        <!-- Header Section -->
        <header class="header">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="logo-text">
                    <h1>Selamat Datang</h1>
                    <p class="tagline">Masuk atau daftar untuk mengakses akun Anda</p>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="form-container">
                <!-- Form Login -->
                <div class="form-section login-form active" id="login-section">
                    <div class="form-header">
                        <h2 class="form-title">
                            <i class="fas fa-sign-in-alt"></i>
                            Masuk ke Akun
                        </h2>
                        <p class="form-subtitle">Masukkan kredensial Anda untuk mengakses akun</p>
                    </div>
                    
                    <form id="loginForm" method="POST" autocomplete="on" novalidate>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" 
                                   id="loginEmail" 
                                   name="email" 
                                   placeholder="Alamat Email" 
                                   required
                                   autocomplete="email"
                                   maxlength="100">
                            <div class="input-focus-line"></div>
                        </div>
                        
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   id="loginPassword" 
                                   name="password" 
                                   placeholder="Kata Sandi" 
                                   required
                                   autocomplete="current-password"
                                   minlength="6"
                                   maxlength="255">
                            <div class="input-focus-line"></div>
                        </div>
                        
                        <div class="form-options">
                            <div class="remember-me">
                                <label class="checkbox-container">
                                    <input type="checkbox" 
                                           id="remember" 
                                           name="remember"
                                           value="1">
                                    <span class="checkmark"></span>
                                    <span class="checkbox-label">Ingat saya</span>
                                </label>
                            </div>

                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-login" id="loginSubmit">
                            <span class="btn-icon"><i class="fas fa-sign-in-alt"></i></span>
                            <span class="btn-text">Masuk</span>
                            <span class="btn-loader hidden">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </form>
                    

                    
                    <div class="form-switch">
                        <p>Belum punya akun? 
                            <a href="#" id="showRegister" class="switch-link">
                                <span>Daftar di sini</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </p>
                    </div>
                    
                    <div class="form-footer">
                        <p>Dengan masuk, Anda setuju dengan 
                            <a href="/terms" target="_blank" rel="noopener noreferrer">Syarat & Ketentuan</a> dan 
                            <a href="/privacy" target="_blank" rel="noopener noreferrer">Kebijakan Privasi</a> kami.
                        </p>
                    </div>
                </div>
                
                <!-- Form Registrasi -->
                <div class="form-section register-form" id="register-section">
                    <div class="form-header">
                        <h2 class="form-title">
                            <i class="fas fa-user-plus"></i>
                            Buat Akun Baru
                        </h2>
                        <p class="form-subtitle">Isi data Anda untuk membuat akun baru</p>
                    </div>
                    
                    <form id="registerForm" method="POST" autocomplete="on" novalidate>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" 
                                   id="registerName" 
                                   name="name" 
                                   placeholder="Nama Lengkap" 
                                   required
                                   autocomplete="name"
                                   minlength="3"
                                   maxlength="100">
                            <div class="input-focus-line"></div>
                        </div>
                        
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" 
                                   id="registerEmail" 
                                   name="email" 
                                   placeholder="Alamat Email" 
                                   required
                                   autocomplete="email"
                                   maxlength="100">
                            <div class="input-focus-line"></div>
                        </div>
                        
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   id="registerPassword" 
                                   name="password" 
                                   placeholder="Kata Sandi" 
                                   required
                                   autocomplete="new-password"
                                   minlength="6"
                                   maxlength="255">
                            <div class="input-focus-line"></div>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <span class="strength-text">Kekuatan kata sandi</span>
                            </div>
                        </div>
                        
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   id="registerConfirmPassword" 
                                   name="confirm_password" 
                                   placeholder="Konfirmasi Kata Sandi" 
                                   required
                                   autocomplete="new-password"
                                   minlength="6"
                                   maxlength="255">
                            <div class="input-focus-line"></div>
                        </div>
                        
                        <div class="form-options">
                            <label class="checkbox-container">
                                <input type="checkbox" 
                                       id="agreeTerms" 
                                       name="agree_terms"
                                       value="1"
                                       required>
                                <span class="checkmark"></span>
                                <span class="checkbox-label">
                                    Saya setuju dengan 
                                    <a href="/terms" target="_blank" rel="noopener noreferrer">Syarat & Ketentuan</a>
                                </span>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-register" id="registerSubmit">
                            <span class="btn-icon"><i class="fas fa-user-plus"></i></span>
                            <span class="btn-text">Daftar</span>
                            <span class="btn-loader hidden">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </form>
                    
                    <div class="form-divider">
                        <span>sudah punya akun?</span>
                    </div>
                    
                    <div class="form-switch">
                        <p>Sudah punya akun? 
                            <a href="#" id="showLogin" class="switch-link">
                                <span>Masuk di sini</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </p>
                    </div>
                    
                    <div class="form-footer">
                        <p>Dengan mendaftar, Anda setuju dengan 
                            <a href="/terms" target="_blank" rel="noopener noreferrer">Syarat & Ketentuan</a> dan 
                            <a href="/privacy" target="_blank" rel="noopener noreferrer">Kebijakan Privasi</a> kami.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Info Panel -->
            <div class="info-panel">
                <div class="info-content">
                    <div class="info-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Keamanan Terjamin</h3>
                    <p>Data Anda dilindungi dengan enkripsi tingkat tinggi dan sistem keamanan terbaru.</p>
                    
                    <div class="info-features">
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Enkripsi SSL/TLS</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Verifikasi 2 Langkah</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Privasi Terjaga</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        

    </div>

    <!-- Accessibility improvements -->
    <div class="sr-only" aria-live="polite" aria-atomic="true">
        <!-- Untuk screen reader announcements -->
    </div>

    <!-- Scripts -->
    <script src="/auth/login/login.js" defer></script>
</body>
</html>