// Elemen DOM
const loginSection = document.getElementById("login-section");
const registerSection = document.getElementById("register-section");
const showRegisterLink = document.getElementById("showRegister");
const showLoginLink = document.getElementById("showLogin");
const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");

// Toggle antara form login dan registrasi
if (showRegisterLink) {
  showRegisterLink.addEventListener("click", function (e) {
    e.preventDefault();
    switchToRegister();
  });
}

if (showLoginLink) {
  showLoginLink.addEventListener("click", function (e) {
    e.preventDefault();
    switchToLogin();
  });
}

function switchToRegister() {
  if (loginSection && registerSection) {
    loginSection.classList.remove("active");
    loginSection.classList.add("hidden");

    registerSection.classList.remove("hidden");
    registerSection.classList.add("active");
    registerSection.classList.add("fade-in");

    clearMessages();

    // Update screen reader announcement
    announceScreenReader(
      "Form pendaftaran. Isi data Anda untuk membuat akun baru."
    );

    // Focus ke input pertama di form registrasi
    const registerName = document.getElementById("registerName");
    if (registerName) {
      setTimeout(() => {
        registerName.focus();
      }, 300);
    }
  }
}

function switchToLogin() {
  if (loginSection && registerSection) {
    registerSection.classList.remove("active");
    registerSection.classList.add("hidden");

    loginSection.classList.remove("hidden");
    loginSection.classList.add("active");
    loginSection.classList.add("fade-in");

    clearMessages();

    // Update screen reader announcement
    announceScreenReader(
      "Form login. Masukkan kredensial Anda untuk mengakses akun."
    );

    // Focus ke input email di form login
    const loginEmail = document.getElementById("loginEmail");
    if (loginEmail) {
      setTimeout(() => {
        loginEmail.focus();
      }, 300);
    }
  }
}

// Fungsi untuk screen reader announcements
function announceScreenReader(message) {
  const srElement = document.querySelector(".sr-only");
  if (srElement) {
    srElement.textContent = message;
    srElement.setAttribute("aria-live", "polite");

    // Clear message setelah 2 detik
    setTimeout(() => {
      srElement.textContent = "";
    }, 2000);
  }
}

// Fungsi untuk menampilkan pesan
function showMessage(element, message, type) {
  if (!element) return;

  clearMessages();

  const messageDiv = document.createElement("div");
  messageDiv.className = `message ${type}`;

  // Tambahkan icon berdasarkan type
  let icon = "";
  switch (type) {
    case "error":
      icon = '<i class="fas fa-exclamation-circle"></i>';
      break;
    case "success":
      icon = '<i class="fas fa-check-circle"></i>';
      break;
    case "warning":
      icon = '<i class="fas fa-exclamation-triangle"></i>';
      break;
  }

  messageDiv.innerHTML = `${icon} ${message}`;

  // Tempatkan pesan setelah form header
  const formHeader = element.querySelector(".form-header");
  if (formHeader && formHeader.nextElementSibling) {
    formHeader.parentNode.insertBefore(
      messageDiv,
      formHeader.nextElementSibling
    );
  }
}

// Fungsi untuk menghapus semua pesan
function clearMessages() {
  const messages = document.querySelectorAll(".message");
  messages.forEach((message) => message.remove());
}

// Validasi form login
function validateLoginForm(email, password) {
  if (!email || !password) {
    return "Harap isi semua field yang diperlukan";
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    return "Format email tidak valid";
  }

  return null;
}

// Validasi form registrasi
function validateRegisterForm(name, email, password, confirmPassword) {
  if (!name || !email || !password || !confirmPassword) {
    return "Harap isi semua field yang diperlukan";
  }

  if (name.trim().length < 3) {
    return "Nama minimal 3 karakter";
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    return "Format email tidak valid";
  }

  if (password.length < 6) {
    return "Password minimal 6 karakter";
  }

  if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
    return "Password harus mengandung huruf kapital dan angka";
  }

  if (password !== confirmPassword) {
    return "Password tidak cocok";
  }

  return null;
}

// Update password strength indicator
function updatePasswordStrength(password) {
  const strengthBar = document.querySelector(".strength-fill");
  const strengthText = document.querySelector(".strength-text");

  if (!strengthBar || !strengthText) return;

  let strength = 0;
  let color = "#d32f2f"; // merah
  let text = "Lemah";

  if (password.length >= 6) {
    strength += 25;
  }

  if (/[A-Z]/.test(password)) {
    strength += 25;
  }

  if (/[0-9]/.test(password)) {
    strength += 25;
  }

  if (/[^A-Za-z0-9]/.test(password)) {
    strength += 25;
  }

  // Update warna dan teks berdasarkan strength
  if (strength >= 75) {
    color = "#2e7d32"; // hijau
    text = "Kuat";
  } else if (strength >= 50) {
    color = "#ed6c02"; // orange
    text = "Cukup";
  } else if (strength >= 25) {
    color = "#fbc02d"; // kuning
    text = "Sedang";
  }

  strengthBar.style.width = `${strength}%`;
  strengthBar.style.backgroundColor = color;
  strengthText.textContent = `Kekuatan kata sandi: ${text}`;
  strengthText.style.color = color;
}

// Submit form login
if (loginForm) {
  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const email = document.getElementById("loginEmail")?.value || "";
    const password = document.getElementById("loginPassword")?.value || "";
    const remember = document.getElementById("remember")?.checked || false;

    // Validasi client-side
    const validationError = validateLoginForm(email, password);
    if (validationError) {
      showMessage(loginSection, validationError, "error");
      return;
    }

    // Show loading state
    const submitBtn = loginForm.querySelector(".btn-login");
    if (!submitBtn) return;

    const originalHTML = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <span class="btn-loader">
        <i class="fas fa-spinner fa-spin"></i>
      </span>
      <span class="btn-text">Memproses...</span>
    `;

    try {
      const response = await fetch("proses-login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "login",
          email: email,
          password: password,
          remember: remember ? "1" : "0",
        }),
      });

      // Check if response is JSON
      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        const text = await response.text();
        console.error("Non-JSON response:", text.substring(0, 200));
        throw new Error("Response tidak valid dari server");
      }

      const result = await response.json();

      if (result.success) {
        showMessage(loginSection, result.message, "success");

        // Add success animation
        submitBtn.innerHTML = `
          <span class="btn-icon"><i class="fas fa-check"></i></span>
          <span class="btn-text">Berhasil!</span>
        `;
        submitBtn.style.background =
          "linear-gradient(135deg, #2e7d32, #4caf50)";

        // Redirect setelah delay
        setTimeout(() => {
          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            window.location.href = "/dashboard/dashboard.php";
          }
        }, 1500);
      } else {
        showMessage(loginSection, result.message, "error");

        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHTML;

        // Focus kembali ke field password
        const passwordInput = document.getElementById("loginPassword");
        if (passwordInput) passwordInput.focus();
      }
    } catch (error) {
      console.error("Login error:", error);
      showMessage(
        loginSection,
        "Terjadi kesalahan pada server. Silakan coba lagi.",
        "error"
      );

      // Reset button state
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalHTML;
    }
  });
}

// Submit form registrasi
if (registerForm) {
  registerForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const name = document.getElementById("registerName")?.value || "";
    const email = document.getElementById("registerEmail")?.value || "";
    const password = document.getElementById("registerPassword")?.value || "";
    const confirmPassword =
      document.getElementById("registerConfirmPassword")?.value || "";
    const agreeTerms = document.getElementById("agreeTerms")?.checked || false;

    if (!agreeTerms) {
      showMessage(
        registerSection,
        "Anda harus menyetujui Syarat & Ketentuan",
        "error"
      );
      return;
    }

    // Validasi client-side
    const validationError = validateRegisterForm(
      name,
      email,
      password,
      confirmPassword
    );
    if (validationError) {
      showMessage(registerSection, validationError, "error");
      return;
    }

    // Show loading state
    const submitBtn = registerForm.querySelector(".btn-register");
    if (!submitBtn) return;

    const originalHTML = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <span class="btn-loader">
        <i class="fas fa-spinner fa-spin"></i>
      </span>
      <span class="btn-text">Mendaftarkan...</span>
    `;

    try {
      const response = await fetch("proses-login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "register",
          name: name,
          email: email,
          password: password,
          confirm_password: confirmPassword,
        }),
      });

      // Check if response is JSON
      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        const text = await response.text();
        console.error("Non-JSON response:", text.substring(0, 200));
        throw new Error("Response tidak valid dari server");
      }

      const result = await response.json();

      if (result.success) {
        showMessage(registerSection, result.message, "success");

        // Add success animation
        submitBtn.innerHTML = `
          <span class="btn-icon"><i class="fas fa-check"></i></span>
          <span class="btn-text">Berhasil Daftar!</span>
        `;
        submitBtn.style.background =
          "linear-gradient(135deg, #2e7d32, #4caf50)";

        // Reset form dan switch ke login setelah delay
        setTimeout(() => {
          registerForm.reset();
          switchToLogin();

          // Auto-fill email di form login
          const loginEmailInput = document.getElementById("loginEmail");
          if (loginEmailInput) loginEmailInput.value = email;

          const loginPasswordInput = document.getElementById("loginPassword");
          if (loginPasswordInput) loginPasswordInput.focus();
        }, 2000);
      } else {
        showMessage(registerSection, result.message, "error");

        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHTML;
      }
    } catch (error) {
      console.error("Register error:", error);
      showMessage(
        registerSection,
        "Terjadi kesalahan pada server. Silakan coba lagi.",
        "error"
      );

      // Reset button state
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalHTML;
    }
  });
}

// Link lupa password
const forgotLink = document.querySelector(".forgot-password");
if (forgotLink) {
  forgotLink.addEventListener("click", function (e) {
    e.preventDefault();
    const email = document.getElementById("loginEmail")?.value || "";
    const userEmail = prompt(
      "Masukkan email Anda untuk reset password:",
      email
    );

    if (userEmail) {
      // Validasi email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(userEmail)) {
        alert("Format email tidak valid");
        return;
      }

      // Kirim request reset password
      fetch("proses-login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "forgot_password",
          email: userEmail,
        }),
      })
        .then((response) => {
          // Check if response is JSON
          const contentType = response.headers.get("content-type");
          if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Response tidak valid dari server");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            alert(data.message);
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Forgot password error:", error);
          alert("Terjadi kesalahan. Silakan coba lagi.");
        });
    }
  });
}

// Fitur show/hide password
function setupPasswordToggle() {
  const passwordInputs = document.querySelectorAll('input[type="password"]');

  passwordInputs.forEach((input) => {
    const parent = input.parentElement;
    if (!parent) return;

    // Cek apakah toggle button sudah ada
    if (parent.querySelector(".password-toggle")) return;

    const toggleBtn = document.createElement("button");
    toggleBtn.type = "button";
    toggleBtn.className = "password-toggle";
    toggleBtn.setAttribute("aria-label", "Tampilkan/sembunyikan password");
    toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';

    toggleBtn.addEventListener("click", function (e) {
      e.preventDefault();
      const type =
        input.getAttribute("type") === "password" ? "text" : "password";
      input.setAttribute("type", type);

      // Update icon dan aria-label
      if (type === "password") {
        this.innerHTML = '<i class="fas fa-eye"></i>';
        this.setAttribute("aria-label", "Tampilkan password");
      } else {
        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
        this.setAttribute("aria-label", "Sembunyikan password");
      }
    });

    parent.style.position = "relative";
    parent.appendChild(toggleBtn);
  });
}

// Fitur remember me untuk email
function setupRememberMe() {
  const rememberCheckbox = document.getElementById("remember");
  const loginEmail = document.getElementById("loginEmail");

  if (!rememberCheckbox || !loginEmail) return;

  // Cek apakah ada email yang disimpan di localStorage
  const savedEmail = localStorage.getItem("remember_email");
  if (savedEmail) {
    loginEmail.value = savedEmail;
    rememberCheckbox.checked = true;
  }

  // Simpan email saat checkbox dicentang dan email diisi
  rememberCheckbox.addEventListener("change", function () {
    if (this.checked && loginEmail.value.trim()) {
      localStorage.setItem("remember_email", loginEmail.value.trim());
    } else {
      localStorage.removeItem("remember_email");
    }
  });

  // Update localStorage saat email berubah
  loginEmail.addEventListener("input", function () {
    if (rememberCheckbox.checked && this.value.trim()) {
      localStorage.setItem("remember_email", this.value.trim());
    }
  });
}

// Password strength real-time update
function setupPasswordStrength() {
  const passwordInput = document.getElementById("registerPassword");
  if (passwordInput) {
    passwordInput.addEventListener("input", function () {
      updatePasswordStrength(this.value);
    });
  }
}

// Fungsi untuk menangani form submission dengan Enter key
function setupEnterKeySubmit() {
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("keypress", function (e) {
      if (e.key === "Enter" && !e.shiftKey) {
        // Cek apakah element yang aktif adalah input atau textarea
        const activeElement = document.activeElement;
        if (
          activeElement.tagName === "INPUT" ||
          activeElement.tagName === "TEXTAREA"
        ) {
          e.preventDefault();
          const submitBtn = form.querySelector('button[type="submit"]');
          if (submitBtn && !submitBtn.disabled) {
            submitBtn.click();
          }
        }
      }
    });
  });
}

// Validasi real-time untuk form
function setupRealTimeValidation() {
  // Validasi email real-time
  const emailInputs = document.querySelectorAll('input[type="email"]');
  emailInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      const email = this.value.trim();
      if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          this.style.borderColor = "#c62828";
        } else {
          this.style.borderColor = "";
        }
      }
    });
  });

  // Validasi password real-time
  const passwordInputs = document.querySelectorAll('input[type="password"]');
  passwordInputs.forEach((input) => {
    if (input.id !== "registerPassword") {
      input.addEventListener("blur", function () {
        const password = this.value;
        if (password && password.length < 6) {
          this.style.borderColor = "#c62828";
        } else {
          this.style.borderColor = "";
        }
      });
    }
  });
}

// Fungsi untuk auto-clear error styling
function setupAutoClearError() {
  const inputs = document.querySelectorAll("input");
  inputs.forEach((input) => {
    input.addEventListener("input", function () {
      if (this.style.borderColor === "rgb(198, 40, 40)") {
        this.style.borderColor = "";
      }
    });
  });
}

// Inisialisasi saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
  console.log("Login page initialized");

  // Setup semua fitur
  setupPasswordToggle();
  setupRememberMe();
  setupPasswordStrength();
  setupEnterKeySubmit();
  setupRealTimeValidation();
  setupAutoClearError();

  // Auto-focus ke field pertama (login email)
  setTimeout(() => {
    const firstInput = document.getElementById("loginEmail");
    if (firstInput) {
      firstInput.focus();
    }
  }, 300);

  // Setup form switching animation
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (
        mutation.attributeName === "class" &&
        mutation.target.classList.contains("fade-in")
      ) {
        setTimeout(() => {
          mutation.target.classList.remove("fade-in");
        }, 500);
      }
    });
  });

  if (loginSection) observer.observe(loginSection, { attributes: true });
  if (registerSection) observer.observe(registerSection, { attributes: true });

  // Check URL parameters for messages
  const urlParams = new URLSearchParams(window.location.search);
  const message = urlParams.get("message");
  const messageType = urlParams.get("type");

  if (message && messageType) {
    try {
      const decodedMessage = decodeURIComponent(message);
      const targetSection = registerSection.classList.contains("active")
        ? registerSection
        : loginSection;
      showMessage(targetSection, decodedMessage, messageType);

      // Auto-hide success messages after 5 seconds
      if (messageType === "success") {
        setTimeout(() => {
          clearMessages();
        }, 5000);
      }
    } catch (e) {
      console.error("Error decoding URL message:", e);
    }
  }

  // Cek jika ada error di URL
  const error = urlParams.get("error");
  if (error) {
    const targetSection = registerSection.classList.contains("active")
      ? registerSection
      : loginSection;
    showMessage(targetSection, decodeURIComponent(error), "error");
  }

  // Prevent form submission spam
  let isProcessing = false;
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (isProcessing) {
        e.preventDefault();
        return;
      }
      isProcessing = true;

      // Reset processing flag setelah 3 detik
      setTimeout(() => {
        isProcessing = false;
      }, 3000);
    });
  });
});

// Handle browser back/forward buttons
window.addEventListener("pageshow", function (event) {
  // Jika halaman di-load dari cache, reset button states
  if (event.persisted) {
    const buttons = document.querySelectorAll('button[type="submit"]');
    buttons.forEach((button) => {
      button.disabled = false;
      const originalText = button.querySelector(".btn-text");
      if (originalText) {
        originalText.textContent = button.classList.contains("btn-login")
          ? "Masuk"
          : "Daftar";
      }
    });
  }
});

// Tambahkan juga CSS untuk class .hidden yang hilang
const style = document.createElement("style");
style.textContent = `
  .hidden {
    display: none !important;
  }
  
  .active {
    display: block !important;
  }
`;
document.head.appendChild(style);
