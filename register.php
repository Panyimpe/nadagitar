<?php
require 'function.php';
session_start();

// Redirect jika sudah login
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$message = "";
$success = false;

if (isset($_POST['register'])) {
    $email    = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $ulang    = $_POST['ulang'];

    if ($password !== $ulang) {
        $message = "Kata sandi tidak sama!";
    } else {
        // Cek apakah email sudah terdaftar (TABEL: login)
        $cek = mysqli_query($conn, "SELECT * FROM login WHERE email = '$email'");
        
        if (mysqli_num_rows($cek) > 0) {
            $message = "Email sudah terdaftar! Silakan gunakan email lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Ambil username dari email (bagian sebelum @)
            $nama = explode('@', $email)[0];
            
            // Insert user baru (KOLOM: nama, email, password)
            $query = "INSERT INTO login (nama, email, password) VALUES ('$nama', '$email', '$hashed_password')";
            $insert = mysqli_query($conn, $query);
            
            if ($insert) {
                // Set flag sukses untuk menampilkan notifikasi
                $success = true;
                $message = "Pendaftaran berhasil! Silakan login dengan akun Anda.";
            } else {
                $message = "Gagal mendaftar! Silakan coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - NadaGitar Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #00f2fe;
            --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            --gradient-success: linear-gradient(135deg, #4facfe 0%, var(--success) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px; 
        }

        .register-container {
            max-width: 400px;
            width: 100%;
        }

        .card {
            border: none;
            border-radius: 18px; 
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); 
            animation: slideUp 0.5s ease-out; 
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            background: var(--gradient-primary);
            padding: 20px 15px; 
            text-align: center;
            border: none;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .card-header h3 {
            color: white;
            font-weight: 600; 
            font-size: 1.4rem; 
            margin: 0;
        }

        .logo-icon {
            font-size: 2rem; 
            color: white;
            margin-bottom: 5px; 
            filter: drop-shadow(0 1px 3px rgba(0,0,0,0.1));
        }

        .card-body {
            padding: 18px; 
        }
        
        .form-label {
            font-weight: 500; 
            color: #4a5568;
            margin-bottom: 3px; 
            font-size: 0.85rem; 
            text-align: left; 
        }
        
        .input-group {
            position: relative; 
        }

        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 8px; 
            padding: 10px 15px; 
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1); 
        }
        
        .input-group .form-control {
            padding-left: 35px; 
        }

        .input-icon {
            position: absolute;
            left: 10px; 
            top: 50%;
            transform: translateY(-50%); 
            color: #a0aec0;
            z-index: 10;
            pointer-events: none;
            transition: color 0.2s ease;
            font-size: 0.9rem;
        }

        .form-control:focus + .input-icon {
            color: var(--primary);
        }

        .btn-success {
            background: var(--gradient-success);
            border: none;
            border-radius: 8px; 
            padding: 10px 20px; 
            font-weight: 600;
            font-size: 0.9rem; 
            box-shadow: 0 3px 8px rgba(79, 172, 254, 0.3);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(79, 172, 254, 0.4);
        }

        .alert {
            border-radius: 8px; 
            padding: 10px 12px; 
            font-size: 0.85rem; 
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            border: none;
        }

        .alert-success {
            background: linear-gradient(135deg, #6bcf7f 0%, #4facfe 100%);
            color: white;
            border: none;
        }

        .divider {
            margin: 8px 0; 
            text-align: center;
            color: #6c757d;
            font-size: 0.75rem;
        }

        .login-link {
            margin-top: 3px; 
            text-align: center;
        }

        .login-link p {
            margin-bottom: 2px; 
            font-size: 0.8rem; 
            color: #6c757d;
        }

        .login-link a {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--secondary);
        }

        .password-strength {
            height: 2px; 
            margin-top: 3px; 
            display: none;
            border-radius: 2px;
            background: #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .password-strength::before {
            content: '';
            position: absolute;
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .password-strength.weak::before {
            width: 33%;
            background: #ff6b6b;
        }

        .password-strength.medium::before {
            width: 66%;
            background: #ffd93d;
        }

        .password-strength.strong::before {
            width: 100%;
            background: #6bcf7f;
        }
        
        .footer-text {
            font-size: 0.75rem; 
            margin-top: 15px;
            text-align: center;
            color: rgba(255,255,255,0.8);
        }

        .footer-text p {
            margin: 0;
        }

        .footer-text p:last-child {
            margin-top: 5px;
        }

        /* Loading Animation */
        .btn-success.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-success.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 576px) {
            body { padding: 10px; padding-top: 20px; }
            .register-container { max-width: 100%; }
            .card { border-radius: 15px; }
            .card-header { padding: 18px 12px; }
            .card-header h3 { font-size: 1.25rem; }
            .logo-icon { font-size: 1.75rem; }
            .card-body { padding: 15px; }
            .form-control { padding: 9px 12px; padding-left: 32px; font-size: 0.85rem; }
            .btn-success { padding: 10px 16px; font-size: 0.85rem; }
        }

        @media (max-width: 375px) {
            .card-header h3 { font-size: 1.15rem; }
            .form-control { font-size: 0.8rem; padding: 8px 10px; padding-left: 30px; }
        }

        @media screen and (max-width: 576px) {
            input[type="email"],
            input[type="password"],
            input[type="text"] {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-plus logo-icon"></i>
            <h3>Daftar Akun</h3>
            <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.75rem; font-weight: 300;">
                Bergabunglah dengan NadaGitar
            </p>
        </div>
        
        <div class="card-body">
            <?php if ($message != "") { ?>
                <?php if ($success) { ?>
                    <div class="alert alert-success d-flex align-items-center mb-2" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div><?= $message ?></div>
                    </div>
                    <script>
                        // Redirect ke login setelah 2 detik
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>
                <?php } else { ?>
                    <div class="alert alert-danger d-flex align-items-center mb-2" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div><?= $message ?></div>
                    </div>
                <?php } ?>
            <?php } ?>

            <form method="POST" id="registerForm">
                <div class="mb-1">
                    <label class="form-label"><i class="fas fa-envelope me-2"></i>Alamat Email</label>
                    <div class="input-group">
                        <i class="fas fa-at input-icon"></i>
                        <input type="email" name="email" required class="form-control" placeholder="Email Anda">
                    </div>
                </div>

                <div class="mb-1">
                    <label class="form-label"><i class="fas fa-lock me-2"></i>Kata Sandi</label>
                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" id="password" required class="form-control" placeholder="Buat kata sandi">
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="mb-1">
                    <label class="form-label"><i class="fas fa-check-circle me-2"></i>Konfirmasi Sandi</label>
                    <div class="input-group">
                        <i class="fas fa-redo input-icon"></i>
                        <input type="password" name="ulang" id="confirmPassword" required class="form-control" placeholder="Ulangi kata sandi">
                    </div>
                    <small id="passwordMatch" class="d-block" style="display: none; margin-top: 3px; font-size: 0.8rem;"></small>
                </div>

                <button type="submit" name="register" class="btn btn-success w-100 mt-2 mb-0">
                    <i class="fas fa-user-check me-2"></i>
                    <span>Daftar</span>
                </button>

                <div class="divider">
                    <span>ATAU</span>
                </div>

                <div class="login-link">
                    <p>Sudah punya akun?</p>
                    <a href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Masuk Di Sini</a>
                </div>
            </form>
        </div>
    </div>

    <div class="footer-text">
        <p>© <?= date('Y') ?> NadaGitar. Semua hak dilindungi.</p>
        <p style="margin-top: 5px;">Dikembangkan oleh Janorius Dedo</p>
    </div>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const passwordInput = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (passwordInput === confirmPassword) {
            const btn = this.querySelector('.btn-success');
            btn.classList.add('loading');
            btn.querySelector('span').textContent = 'Mendaftar...';
        }
    });

    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');

    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        return strength;
    }

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        passwordStrength.style.display = password.length > 0 ? 'block' : 'none';
        
        const strength = checkPasswordStrength(password);

        passwordStrength.className = 'password-strength';
        if (strength <= 1 && password.length > 0) {
            passwordStrength.classList.add('weak');
        } else if (strength <= 2) {
            passwordStrength.classList.add('medium');
        } else if (strength > 2) {
            passwordStrength.classList.add('strong');
        }

        checkPasswordMatch();
    });

    const confirmPassword = document.getElementById('confirmPassword');
    const passwordMatch = document.getElementById('passwordMatch');

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirm = confirmPassword.value;

        if (confirm.length > 0) {
            passwordMatch.style.display = 'block';
            if (password === confirm) {
                passwordMatch.style.color = '#00c3ff';
                passwordMatch.innerHTML = '<i class="fas fa-check-circle me-1"></i>Kata sandi cocok!';
            } else {
                passwordMatch.style.color = '#ff6b6b';
                passwordMatch.innerHTML = '<i class="fas fa-times-circle me-1"></i>Kata sandi tidak cocok';
            }
        } else {
            passwordMatch.style.display = 'none';
        }
    }

    confirmPassword.addEventListener('input', checkPasswordMatch);

    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) icon.style.color = 'var(--primary)';
        });
        
        input.addEventListener('blur', function() {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) icon.style.color = '#a0aec0';
        });
    });
</script>

</body>
</html>