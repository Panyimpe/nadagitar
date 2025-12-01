<?php
// ======================================================
//  KONEKSI DATABASE
// ======================================================
$conn = mysqli_connect("localhost", "root", "", "stockbarang");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ======================================================
//  FUNGSI AMAN
// ======================================================
function esc($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

// ======================================================
//  FUNGSI LOGIN & REGISTER
// ======================================================
function login_user($conn, $email, $password) {
    $stmt = mysqli_prepare($conn, "SELECT iduser, nama, email, password FROM login WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data && password_verify($password, $data['password'])) {
        return $data;
    }
    return false;
}

function register_user($conn, $nama, $email, $password) {
    $stmt = mysqli_prepare($conn, "SELECT iduser FROM login WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        return "Email sudah terdaftar!";
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmtInsert = mysqli_prepare($conn, "INSERT INTO login (nama, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmtInsert, "sss", $nama, $email, $hash);

    if (mysqli_stmt_execute($stmtInsert)) {
        return true;
    } else {
        return "Gagal registrasi: " . mysqli_error($conn);
    }
}

// ======================================================
//  LOGOUT
// ======================================================
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location: login.php");
    exit;
}

// ======================================================
//  SISTEM STOK - TAMBAH BARANG
// ======================================================
if (isset($_POST['addnewbarang'])) {
    session_start();
    $iduser = $_SESSION['iduser'];

    $namabarang = esc($_POST['namabarang']);
    $deskripsi  = esc($_POST['deskripsi']);
    $stock      = (int)$_POST['stock'];

    $query = "INSERT INTO stock (namabarang, deskripsi, stock, iduser) 
              VALUES ('$namabarang','$deskripsi','$stock','$iduser')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Barang berhasil ditambahkan!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal tambah barang: " . mysqli_error($conn) . "'); window.location='index.php';</script>";
        exit;
    }
}

// ======================================================
//  EDIT BARANG (FIX TANPA iduser)
// ======================================================
if (isset($_POST['updatebarang'])) {

    $idbarang   = (int)$_POST['idbarang'];
    $namabarang = esc($_POST['namabarang']);
    $deskripsi  = esc($_POST['deskripsi']);
    $stock      = (int)$_POST['stock'];

    $query = "UPDATE stock 
              SET namabarang='$namabarang', 
                  deskripsi='$deskripsi', 
                  stock='$stock' 
              WHERE idbarang='$idbarang'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Barang berhasil diupdate!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update barang!'); window.location='index.php';</script>";
        exit;
    }
}

// ======================================================
//  HAPUS BARANG
// ======================================================
if (isset($_POST['hapusbarang'])) {
    session_start();

    $idbarang = (int)$_POST['idbarang'];
    
    $query = "DELETE FROM stock WHERE idbarang='$idbarang'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Barang berhasil dihapus!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal hapus barang!'); window.location='index.php';</script>";
        exit;
    }
}

// ======================================================
//  BARANG MASUK - TAMBAH
// ======================================================
if (isset($_POST['barangmasuk'])) {
    session_start();
    $iduser = $_SESSION['iduser'];

    $idbarang = (int)$_POST['barangnya'];
    $qty      = (int)$_POST['qty'];
    $penerima = esc($_POST['penerima']);

    $cekstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idbarang'");
    $data = mysqli_fetch_assoc($cekstock);
    
    if (!$data) {
        echo "<script>alert('Barang tidak ditemukan!'); window.location='masuk.php';</script>";
        exit;
    }

    $stockbaru = $data['stock'] + $qty;

    mysqli_query($conn, "INSERT INTO masuk (idbarang, qty, keterangan, iduser) 
                         VALUES ('$idbarang','$qty','$penerima','$iduser')");
    
    mysqli_query($conn, "UPDATE stock SET stock='$stockbaru' WHERE idbarang='$idbarang'");

    echo "<script>alert('Barang masuk berhasil dicatat!'); window.location='masuk.php';</script>";
    exit;
}

// ======================================================
//  BARANG MASUK - EDIT
// ======================================================
if (isset($_POST['updatemasuk'])) {
    session_start();
    $iduser = $_SESSION['iduser'];

    $idmasuk  = (int)$_POST['idmasuk'];
    $idbarang = (int)$_POST['idbarang'];
    $qtybaru  = (int)$_POST['qty'];
    $penerima = esc($_POST['penerima']);

    $ambil = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk='$idmasuk'");
    $data = mysqli_fetch_assoc($ambil);
    $qtylama = $data['qty'];

    $stok = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idbarang'");
    $datastok = mysqli_fetch_assoc($stok);
    
    $stockupdate = $datastok['stock'] - $qtylama + $qtybaru;

    mysqli_query($conn, "UPDATE masuk 
                         SET qty='$qtybaru', keterangan='$penerima' 
                         WHERE idmasuk='$idmasuk'");
    
    mysqli_query($conn, "UPDATE stock SET stock='$stockupdate' WHERE idbarang='$idbarang'");

    echo "<script>alert('Data barang masuk berhasil diupdate!'); window.location='masuk.php';</script>";
    exit;
}

// ======================================================
//  BARANG MASUK - HAPUS
// ======================================================
if (isset($_POST['hapusmasuk'])) {
    session_start();

    $idmasuk = (int)$_POST['idmasuk'];

    $ambil = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk='$idmasuk'");
    $data = mysqli_fetch_assoc($ambil);

    if (!$data) {
        echo "<script>alert('Data tidak ditemukan!'); window.location='masuk.php';</script>";
        exit;
    }

    $idbarang = $data['idbarang'];
    $qty = $data['qty'];

    $stok = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idbarang'");
    $datastok = mysqli_fetch_assoc($stok);
    
    $stockupdate = $datastok['stock'] - $qty;

    mysqli_query($conn, "UPDATE stock SET stock='$stockupdate' WHERE idbarang='$idbarang'");
    
    mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idmasuk'");

    echo "<script>alert('Data barang masuk berhasil dihapus!'); window.location='masuk.php';</script>";
    exit;
}

// ======================================================
//  BARANG KELUAR - TAMBAH
// ======================================================
if (isset($_POST['addbarangkeluar'])) {
    session_start();
    $iduser = $_SESSION['iduser'];

    $idbarang = (int)$_POST['barangnya'];
    $qty      = (int)$_POST['qty'];
    $penerima = esc($_POST['penerima']);

    $cekstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idbarang'");
    $data = mysqli_fetch_assoc($cekstock);

    if (!$data) {
        echo "<script>alert('Barang tidak ditemukan!'); window.location='keluar.php';</script>";
        exit;
    }

    if ($data['stock'] < $qty) {
        echo "<script>alert('Stock tidak cukup! Stock tersedia: " . $data['stock'] . "'); window.location='keluar.php';</script>";
        exit;
    }

    $stockbaru = $data['stock'] - $qty;

    mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty, iduser) 
                         VALUES ('$idbarang','$penerima','$qty','$iduser')");
    
    mysqli_query($conn, "UPDATE stock SET stock='$stockbaru' WHERE idbarang='$idbarang'");

    echo "<script>alert('Barang keluar berhasil dicatat!'); window.location='keluar.php';</script>";
    exit;
}

// ======================================================
//  BARANG KELUAR - EDIT
// ======================================================
if (isset($_POST['updatekeluar'])) {
    session_start();
    $iduser = $_SESSION['iduser'];

    $idkeluar = (int)$_POST['idkeluar'];
    $idbarang = (int)$_POST['idbarang'];
    $qtybaru  = (int)$_POST['qty_baru'];
    $qtylama  = (int)$_POST['qty_lama'];
    $penerima = esc($_POST['penerima']);

    $stok = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idbarang'");
    $datastok = mysqli_fetch_assoc($stok);
    
    $stockupdate = $datastok['stock'] + $qtylama - $qtybaru;

    if ($stockupdate < 0) {
        echo "<script>alert('Stock tidak cukup!'); window.location='keluar.php';</script>";
        exit;
    }

    mysqli_query($conn, "UPDATE keluar 
                         SET qty='$qtybaru', penerima='$penerima' 
                         WHERE idkeluar='$idkeluar'");
    
    mysqli_query($conn, "UPDATE stock SET stock='$stockupdate' WHERE idbarang='$idbarang'");

    echo "<script>alert('Data barang keluar berhasil diupdate!'); window.location='keluar.php';</script>";
    exit;
}

// ======================================================
//  BARANG KELUAR - HAPUS
// ======================================================
if (isset($_POST['hapuskeluar'])) {
    session_start();

    $idkeluar = (int)$_POST['idkeluar'];
    $idbarang = (int)$_POST['idbarang'];
    $qty      = (int)$_POST['qty'];

    $stok = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idbarang'");
    $datastok = mysqli_fetch_assoc($stok);
    
    $stockupdate = $datastok['stock'] + $qty;

    mysqli_query($conn, "UPDATE stock SET stock='$stockupdate' WHERE idbarang='$idbarang'");
    
    mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idkeluar'");

    echo "<script>alert('Data barang keluar berhasil dihapus!'); window.location='keluar.php';</script>";
    exit;
}
?>
