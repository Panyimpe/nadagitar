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
?>


<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <meta name="description" content="Sistem Manajemen Barang Keluar NadaGitar Premium" />

    <meta name="author" content="Janorius Dedo" />

    <title>Barang Keluar - NadaGitar Premium</title>

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

            transform: translateY(-5px);

            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);

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



        .modal-header.bg-warning {

            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;

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



        .text-danger {

            color: #ff6b6b !important;

            font-weight: 700;

        }



        .btn-sm {

            padding: 8px 16px;

            font-size: 0.8rem;

        }



        .btn-close-white {

            filter: brightness(0) invert(1);

        }



        /* Scrollbar Custom */

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



        @keyframes fadeIn {

            from {

                opacity: 0;

                transform: translateY(20px);

            }

            to {

                opacity: 1;

                transform: translateY(0);

            }

        }



        .card {

            animation: fadeIn 0.6s ease-out;

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

                        <a class="nav-link" href="index.php">

                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line fa-fw"></i></div>

                            Stok Barang

                        </a>

                        <a class="nav-link" href="masuk.php">

                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-down fa-fw"></i></div>

                            Barang Masuk

                        </a>

                        <a class="nav-link active" href="keluar.php">

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

                        <i class="fas fa-shipping-fast me-3"></i>Inventaris Keluar

                    </h1>



                    <div class="card mb-4">

                        <div class="card-header d-flex justify-content-between align-items-center">

                            <h4 class="m-0"><i class="fas fa-file-export me-2"></i>Data Barang Keluar</h4>

                            <div>

                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#myModal">

                                    <i class="fas fa-plus me-2"></i>Tambah Data

                                </button>

                                <a href="export_keluar.php" class="btn btn-success">

                                    <i class="fas fa-download me-2"></i>Ekspor

                                </a>

                            </div>

                        </div>

                        <div class="card-body p-4">

                            <table id="datatablesSimple" class="table table-hover">

                                <thead>

                                    <tr>

                                        <th>Tanggal</th>

                                        <th>Nama Barang</th>

                                        <th>Jumlah Keluar</th>

                                        <th>Penerima</th>

                                        <th>Aksi</th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php 

                                // Ambil barang keluar hanya untuk stock milik user login

                                $ambil = mysqli_query(

                                    $conn,

                                    "SELECT k.*, s.namabarang

                                    FROM keluar k

                                    JOIN stock s ON s.idbarang = k.idbarang

                                    WHERE s.iduser='$iduser'

                                    ORDER BY k.idkeluar DESC"

                                );



                                while ($data = mysqli_fetch_array($ambil)) {

                                    $idkeluar   = $data['idkeluar'];

                                    $idbarang   = $data['idbarang'];

                                    $tanggal    = date('d M Y', strtotime($data['tanggal']));

                                    $namabarang = $data['namabarang'];

                                    $qty        = $data['qty'];

                                    $penerima   = $data['penerima'];

                                ?>

                                <tr>

                                    <td><strong><?= $tanggal; ?></strong></td>

                                    <td><strong><?= $namabarang; ?></strong></td>

                                    <td class="text-danger">-<?= $qty; ?> Unit</td>

                                    <td><?= $penerima; ?></td>

                                    <td>

                                        <button type="button" class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editKeluar<?=$idkeluar;?>">

                                            <i class="fas fa-edit"></i>

                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteKeluar<?=$idkeluar;?>">

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



    <div class="modal fade" id="myModal" tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="fas fa-plus-circle me-2"></i>Tambah Data Barang Keluar

                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                </div>

                <form method="post" action="function.php">

                    <div class="modal-body">

                        <div class="mb-4">

                            <label class="form-label">Pilih Barang</label>

                            <select name="barangnya" class="form-control" required>

                                <option value="">-- Pilih Barang (Stok Tersedia) --</option>

                                <?php

                                // Ambil stock milik user login yang stoknya lebih dari 0

                                $stok = mysqli_query($conn, "SELECT * FROM stock WHERE iduser='$iduser' AND stock > 0 ORDER BY namabarang ASC");

                                while ($row = mysqli_fetch_array($stok)) {

                                ?>

                                    <option value="<?= $row['idbarang']; ?>">

                                        <?= $row['namabarang']; ?> (Stok: <?= $row['stock']; ?>)

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">Jumlah Keluar</label>

                            <input type="number" name="qty" placeholder="Masukkan jumlah" class="form-control" required min="1">

                        </div>

                        <div class="mb-4">

                            <label class="form-label">Penerima</label>

                            <input type="text" name="penerima" placeholder="Nama penerima" class="form-control" required>

                        </div>

                        <div class="text-end mt-4">

                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>

                            <button type="submit" class="btn btn-primary" name="addbarangkeluar">

                                <i class="fas fa-save me-2"></i>Simpan Data

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <?php 

    // Query ulang untuk modal

    $ambil2 = mysqli_query(

        $conn,

        "SELECT k.*, s.namabarang

        FROM keluar k

        JOIN stock s ON s.idbarang = k.idbarang

        WHERE s.iduser='$iduser'

        ORDER BY k.idkeluar DESC"

    );



    while ($data = mysqli_fetch_array($ambil2)) {

        $idkeluar   = $data['idkeluar'];

        $idbarang   = $data['idbarang'];

        $tanggal    = date('d M Y', strtotime($data['tanggal']));

        $namabarang = $data['namabarang'];

        $qty        = $data['qty'];

        $penerima   = $data['penerima'];

    ?>

    <div class="modal fade" id="editKeluar<?=$idkeluar;?>" tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header bg-warning">

                    <h5 class="modal-title">

                        <i class="fas fa-edit me-2"></i>Edit Data Barang Keluar

                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                </div>

                <form method="post" action="function.php">

                    <div class="modal-body">

                        <input type="hidden" name="idkeluar" value="<?=$idkeluar;?>">

                        <input type="hidden" name="idbarang" value="<?=$idbarang;?>">

                        <input type="hidden" name="qty_lama" value="<?=$qty;?>">

                        

                        <div class="mb-4">

                            <label class="form-label">Nama Barang</label>

                            <input type="text" value="<?=$namabarang;?>" class="form-control" disabled>

                        </div>



                        <div class="mb-4">

                            <label class="form-label">Jumlah Keluar</label>

                            <input type="number" name="qty_baru" value="<?=$qty;?>" placeholder="Jumlah keluar baru" class="form-control" required min="1">

                        </div>



                        <div class="mb-4">

                            <label class="form-label">Penerima</label>

                            <input type="text" name="penerima" value="<?=$penerima;?>" placeholder="Nama penerima" class="form-control" required>

                        </div>

                        <div class="text-end mt-4">

                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>

                            <button type="submit" class="btn btn-primary" name="updatekeluar">

                                <i class="fas fa-save me-2"></i>Perbarui

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <div class="modal fade" id="deleteKeluar<?=$idkeluar;?>" tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header bg-danger">

                    <h5 class="modal-title">

                        <i class="fas fa-trash me-2"></i>Hapus Data Barang Keluar

                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                </div>

                <form method="post" action="function.php">

                    <div class="modal-body">

                        <p class="fs-5">Apakah Anda yakin ingin menghapus data barang keluar ini?</p>

                        <div class="alert alert-warning">

                            <strong><?= $namabarang; ?></strong><br>

                            Jumlah: <strong><?= $qty; ?> Unit</strong><br>

                            Penerima: <strong><?= $penerima; ?></strong><br>

                            Tanggal: <strong><?= $tanggal; ?></strong>

                        </div>

                        <small class="text-danger">

                            <i class="fas fa-exclamation-triangle me-1"></i>

                            Tindakan ini akan mengembalikan <strong><?= $qty; ?> Unit</strong> ke stok.

                        </small>

                        <input type="hidden" name="idkeluar" value="<?=$idkeluar;?>">

                        <input type="hidden" name="idbarang" value="<?=$idbarang;?>">

                        <input type="hidden" name="qty" value="<?=$qty;?>">

                        <div class="text-end mt-4">

                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>

                            <button type="submit" class="btn btn-danger" name="hapuskeluar">

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

                        placeholder: "Cari data...",

                        perPage: "entri per halaman",

                        noRows: "Tidak ada entri ditemukan",

                        info: "Menampilkan {start} sampai {end} dari {rows} entri",

                    }

                });

            }

        });

    </script>

</body>

</html>