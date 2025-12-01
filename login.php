<?php
require 'function.php';
session_start();

// Redirect jika sudah login
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$message = "";

if (isset($_POST['login'])) {
    $email    = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan email (TABEL: login)
    $query = mysqli_query($conn, "SELECT * FROM login WHERE email = '$email'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        
        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Set session
            $_SESSION['login'] = true;
            $_SESSION['iduser'] = $row['iduser'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['email'] = $row['email'];
            
            // Redirect ke dashboard
            header("Location: index.php");
            exit;
        } else {
            $message = "Email atau kata sandi salah!";
        }
    } else {
        $message = "Email atau kata sandi salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Masuk - NadaGitar Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 15px;
        }

        /* Animated Background Pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,197.3C1248,203,1344,149,1392,122.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat;
            opacity: 0.3;
            animation: wave 15s linear infinite;
            pointer-events: none;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 15s infinite;
        }

        .particle:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 60px; height: 60px; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 40px; height: 40px; left: 60%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 50px; height: 50px; left: 80%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 70px; height: 70px; left: 40%; animation-delay: 3s; }

        @keyframes float {
            0%, 100% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            50% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-100vh) scale(1);
            }
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .card {
            border: none;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 80px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 25px 25px 25px;
            text-align: center;
            border: none;
        }

        .card-header h3 {
            color: white;
            font-weight: 800;
            font-size: 1.6rem;
            margin: 0;
            letter-spacing: 1px;
        }

        .card-header p {
            color: rgba(255,255,255,0.9);
            margin: 8px 0 0 0;
            font-size: 0.88rem;
            font-weight: 400;
        }

        .logo-icon {
            font-size: 2.8rem;
            color: white;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .card-body {
            padding: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            color: #2d3748;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 10;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .input-icon.text-primary {
            color: #667eea !important;
        }

        .form-control.with-icon {
            padding-left: 45px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 600;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 400px;
            height: 400px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            font-weight: 500;
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
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #a0aec0;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link p {
            color: #6c757d;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #667eea;
            transition: width 0.3s ease;
        }

        .register-link a:hover::after {
            width: 100%;
        }

        .register-link a:hover {
            color: #764ba2;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }

        .footer-text p {
            margin: 0;
        }

        .footer-text p:last-child {
            margin-top: 5px;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* Loading Animation */
        .btn-primary.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-primary.loading::after {
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
        @media (max-width: 768px) {
            body { padding: 10px; }
            .card { border-radius: 24px; }
            .card-header { padding: 25px 20px 20px 20px; }
            .card-header h3 { font-size: 1.5rem; }
            .logo-icon { font-size: 2.5rem; }
            .card-body { padding: 25px 20px; }
        }

        @media (max-width: 576px) {
            body { padding: 8px; padding-top: 20px; }
            .particles { display: none; }
            .card { border-radius: 20px; }
            .card-header { padding: 20px 18px 18px 18px; }
            .card-header h3 { font-size: 1.4rem; }
            .logo-icon { font-size: 2.2rem; }
            .card-body { padding: 22px 18px; }
            .form-control { padding: 12px 14px; font-size: 0.9rem; }
            .btn-primary { padding: 12px 20px; font-size: 0.9rem; }
        }

        @media screen and (max-width: 576px) {
            input[type="email"],
            input[type="password"] {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>

<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<div class="login-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-gem logo-icon"></i>
            <h3>NadaGitar</h3>
            <p>Selamat Datang Kembali! Silakan Masuk</p>
        </div>
        
        <div class="card-body">
            <?php if ($message != "") { ?>
                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= $message ?></div>
                </div>
            <?php } ?>
            
            <form method="POST" id="loginForm">
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope me-2"></i>Alamat Email
                    </label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="email" name="email" required class="form-control with-icon" placeholder="Masukkan email Anda">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Kata Sandi
                    </label>
                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" required class="form-control with-icon" placeholder="Masukkan kata sandi Anda">
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    <span>Masuk</span>
                </button>

                <div class="divider">
                    <span>ATAU</span>
                </div>

                <div class="register-link">
                    <p>Belum punya akun?</p>
                    <a href="register.php">
                        <i class="fas fa-user-plus me-1"></i>Buat Akun Baru
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="footer-text">
        <p>© 2024 NadaGitar. Semua hak dilindungi.</p>
        <p>Dikembangkan oleh Janorius Dedo</p>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const btn = this.querySelector('.btn-primary');
        btn.classList.add('loading');
        btn.querySelector('span').textContent = 'Memuat...';
    });

    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) icon.classList.add('text-primary');
        });
        
        input.addEventListener('blur', function() {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) icon.classList.remove('text-primary');
        });
    });
</script>

</body>
</html>