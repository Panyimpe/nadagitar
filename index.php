<?php
error_reporting(0);
ini_set('display_errors', 0);

require 'function.php';
require 'cek.php';
session_start();

// Ambil ID user yang login
$iduser = $_SESSION['iduser'] ?? null;

// Jika user belum login, arahkan ke login
if (!$iduser) {
    header("Location: login.php");
    exit;
}

// Query untuk statistik dashboard
$totalBarang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM stock WHERE iduser='$iduser'"))['total'];
$totalStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stock) as total FROM stock WHERE iduser='$iduser'"))['total'] ?? 0;
$stockRendah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM stock WHERE stock < 10 AND iduser='$iduser'"))['total'];
$masukHariIni = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM masuk WHERE DATE(tanggal) = CURDATE() AND idbarang IN (SELECT idbarang FROM stock WHERE iduser='$iduser')"))['total'];
$keluarHariIni = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM keluar WHERE DATE(tanggal) = CURDATE() AND idbarang IN (SELECT idbarang FROM stock WHERE iduser='$iduser')"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem Manajemen Stock Barang NadaGitar Premium" />
    <meta name="author" content="Janorius Dedo" />
    <title>Stok Barang - NadaGitar</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" /> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --gradient-danger: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            --gradient-info: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            
            --dark-bg: #0f0c29;
            --dark-secondary: #1a1636;
            --dark-card: #252041;
            
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
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
            color: #2d3748;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,197.3C1248,203,1344,149,1392,122.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat;
            opacity: 0.3;
            z-index: 0;
            pointer-events: none;
        }

        .sb-topnav {
            background: rgba(15, 12, 41, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1000;
        }

        .sb-topnav::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .sb-topnav .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
        }

        .sb-sidenav-light {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border-right: none;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
        }

        .sb-sidenav-menu-heading {
            color: #a0aec0;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 1.5rem;
        }

        .sb-sidenav-menu .nav-link {
            color: #4a5568;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 8px 12px;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sb-sidenav-menu .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: -1;
            opacity: 0.1;
        }

        .sb-sidenav-menu .nav-link:hover::before,
        .sb-sidenav-menu .nav-link.active::before {
            left: 0;
        }

        .sb-sidenav-menu .nav-link:hover,
        .sb-sidenav-menu .nav-link.active {
            color: #667eea;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }

        .sb-sidenav-menu .nav-link.active {
            font-weight: 700;
            border-left: 4px solid #667eea;
            padding-left: 16px;
        }

        .sb-sidenav-footer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: 600;
        }

        #layoutSidenav_content {
            position: relative;
            z-index: 1;
        }

        .container-fluid {
            position: relative;
            z-index: 1;
        }

        h1 {
            background: linear-gradient(135deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 1px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 20px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            transform: rotate(45deg);
            transition: all 0.6s ease;
        }

        .stat-card:hover::before {
            top: -60%;
            right: -60%;
        }

        .stat-card .card-body {
            padding: 30px;
            position: relative;
            z-index: 1;
        }

        .stat-card .card-body i {
            font-size: 3.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        }

        .stat-card .card-body h5 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card .card-body p {
            font-size: 0.95rem;
            font-weight: 600;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card.card-1 {
            background: var(--gradient-primary);
            color: white;
        }

        .stat-card.card-2 {
            background: var(--gradient-success);
            color: white;
        }

        .stat-card.card-3 {
            background: var(--gradient-warning);
            color: white;
        }

        .stat-card.card-4 {
            background: var(--gradient-secondary);
            color: white;
        }

        .stat-card.card-5 {
            background: var(--gradient-danger);
            color: white;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none;
            padding: 25px 30px;
            border-radius: 20px 20px 0 0 !important;
        }

        .card-header h4 {
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 28px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
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

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        .btn-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.6);
        }
        .btn-warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white !important;
            box-shadow: 0 4px 15px rgba(250, 112, 154, 0.4);
        }
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(250, 112, 154, 0.6);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
        }
        .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 25px 30px;
        }
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%) !important;
        }
        .modal-header h5 {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .modal-body {
            padding: 30px;
            background: #fafafa;
        }
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        .table {
            border-radius: 12px;
            overflow: hidden;
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            padding: 18px 15px;
            border: none;
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .table tbody td {
            padding: 18px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        .badge.bg-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%) !important;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .stat-card {
            animation: slideInUp 0.6s ease-out backwards;
        }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.8rem;
        }
        .text-white {
            color: white !important;
        }
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #f093fb 100%);
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <a class="navbar-brand ps-3 fw-bold" href="index.php">
            <i class="fas fa-gem me-2"></i>NadaGitar
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-white" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Dashboard</div>
                        <a class="nav-link active" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line fa-fw"></i></div>
                            Stok Barang
                        </a>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-down fa-fw"></i></div>
                            Barang Masuk
                        </a>
                        <a class="nav-link" href="keluar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-up fa-fw"></i></div>
                            Barang Keluar
                        </a>
                        <div class="sb-sidenav-menu-heading">Akun</div>
                         <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt fa-fw"></i></div>
                            Keluar
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <strong>Janorius Dedo</strong>
                    <div class="small mt-1">NIM: 222102455</div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 pt-4 pb-5">
                    <h1 class="mt-4 mb-5">
                        <i class="fas fa-chart-pie me-3"></i>Analisis Dasbor
                    </h1>
                    <div class="row mb-5 justify-content-center g-4">
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card card-1">
                                <div class="card-body text-center">
                                    <i class="fas fa-cubes"></i>
                                    <h5><?php echo $totalBarang; ?></h5>
                                    <p>Total Item</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card card-2">
                                <div class="card-body text-center">
                                    <i class="fas fa-warehouse"></i>
                                    <h5><?php echo $totalStock; ?></h5>
                                    <p>Unit Stok</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card card-3">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h5><?php echo $stockRendah; ?></h5>
                                    <p>Stok Rendah</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card card-4">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-down"></i>
                                    <h5><?php echo $masukHariIni; ?></h5>
                                    <p>Masuk</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card card-5">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-up"></i>
                                    <h5><?php echo $keluarHariIni; ?></h5>
                                    <p>Keluar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="m-0"><i class="fas fa-box-open me-2"></i>Daftar Inventaris</h4>
                            <div>
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#myModal">
                                    <i class="fas fa-plus me-2"></i>Tambah Item
                                </button>
                                <a href="export_stock.php" class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>Ekspor
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <table id="datatablesSimple" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Item</th>
                                        <th>Deskripsi</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $ambilsemuadatastock = mysqli_query($conn,"SELECT * FROM stock WHERE iduser='$iduser'");
                                    $i = 1;
                                    while($data=mysqli_fetch_array($ambilsemuadatastock)){
                                        $idbarang = $data['idbarang'];
                                        $namabarang = $data['namabarang'];
                                        $deskripsi = $data['deskripsi'];
                                        $stock = $data['stock'];
                                    ?>
                                    <tr>
                                        <td><strong><?=$i++;?></strong></td>
                                        <td>
                                            <?php if ($stock < 10): ?>
                                                <span class="badge bg-danger me-2">
                                                    <i class="fas fa-exclamation-circle"></i> Rendah
                                                </span>
                                            <?php endif; ?>
                                            <strong><?=$namabarang;?></strong>
                                        </td>
                                        <td><?=$deskripsi;?></td>
                                        <td>
                                            <span class="badge <?php echo ($stock < 10) ? 'bg-danger' : 'bg-success'; ?>" style="font-size: 1rem; padding: 10px 20px;">
                                                <?=$stock;?> Unit
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm me-1 text-white" data-bs-toggle="modal" data-bs-target="#editModal<?=$idbarang;?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?=$idbarang;?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Item Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="function.php">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">Nama Item</label>
                            <input type="text" name="namabarang" placeholder="Masukkan nama item" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" placeholder="Deskripsi singkat" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" name="stock" placeholder="Kuantitas" class="form-control" required>
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" name="addnewbarang">
                                <i class="fas fa-save me-2"></i>Simpan Item
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php 
    $ambilsemuadatastock2 = mysqli_query($conn,"SELECT * FROM stock WHERE iduser='$iduser'");
    while($data=mysqli_fetch_array($ambilsemuadatastock2)){
        $idbarang = $data['idbarang'];
        $namabarang = $data['namabarang'];
        $deskripsi = $data['deskripsi'];
        $stock = $data['stock'];
    ?>
    <div class="modal fade" id="editModal<?=$idbarang;?>" tabindex="-1" aria-labelledby="editModalLabel<?=$idbarang;?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel<?=$idbarang;?>">
                        <i class="fas fa-edit me-2"></i>Edit Item: <?=$namabarang;?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="function.php">
                    <div class="modal-body">
                        <input type="hidden" name="idbarang" value="<?=$idbarang;?>">
                        <div class="mb-4">
                            <label class="form-label">Nama Item</label>
                            <input type="text" name="namabarang" value="<?=$namabarang;?>" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" value="<?=$deskripsi;?>" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" value="<?=$stock;?>" class="form-control" required>
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" name="updatebarang">
                                <i class="fas fa-save me-2"></i>Perbarui
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php 
    $ambilsemuadatastock3 = mysqli_query($conn,"SELECT * FROM stock WHERE iduser='$iduser'");
    while($data=mysqli_fetch_array($ambilsemuadatastock3)){
        $idbarang = $data['idbarang'];
        $namabarang = $data['namabarang'];
    ?>
    <div class="modal fade" id="deleteModal<?=$idbarang;?>" tabindex="-1" aria-labelledby="deleteModalLabel<?=$idbarang;?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="deleteModalLabel<?=$idbarang;?>">
                        <i class="fas fa-trash me-2"></i>Hapus Item
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="function.php">
                    <div class="modal-body">
                        <p class="fs-5">Apakah Anda yakin ingin menghapus <strong><?=$namabarang;?></strong>?</p>
                        <input type="hidden" name="idbarang" value="<?=$idbarang;?>">
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger" name="hapusbarang">
                                <i class="fas fa-trash me-2"></i>Hapus
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script>
    window.addEventListener('DOMContentLoaded', event => {
        const datatablesSimple = document.getElementById('datatablesSimple');
        if (datatablesSimple) {
            new simpleDatatables.DataTable(datatablesSimple, {
                perPage: 10,
                labels: {
                    placeholder: "Cari item...",
                    perPage: "item per halaman",
                    noRows: "Tidak ada data untuk ditampilkan",
                    info: "Menampilkan {start} sampai {end} dari {rows} entri",
                }
            });
        }
    });
</script>
</body>
</html>