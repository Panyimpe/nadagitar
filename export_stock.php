<?php
require 'function.php';
session_start();

// Validasi login
$iduser = $_SESSION['iduser'] ?? null;
if (!$iduser) {
    die("Akses ditolak");
}

// Header untuk export Word
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=stock_barang.doc");
header("Pragma: no-cache");
header("Expires: 0");

// Mulai dokumen Word
echo "<html>";
echo "<head>
<meta charset='UTF-8'>
<style>
    body { font-family: Arial; }
    h2 { text-align: center; margin-bottom: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 6px; }
    th { background-color: #f2f2f2; }
</style>
</head>";
echo "<body>";

echo "<h2>Data Stock Barang</h2>";

echo "<table>
<tr>
    <th>No</th>
    <th>Nama Barang</th>
    <th>Deskripsi</th>
    <th>Stock</th>
</tr>";

// Query data stock
$query = mysqli_query($conn, "SELECT * FROM stock WHERE iduser='$iduser' ORDER BY namabarang ASC");

$no = 1;

// Loop data
while ($r = mysqli_fetch_assoc($query)) {
    echo "<tr>
        <td>".$no."</td>
        <td>".htmlspecialchars($r['namabarang'])."</td>
        <td>".htmlspecialchars($r['deskripsi'])."</td>
        <td>".htmlspecialchars($r['stock'])."</td>
    </tr>";
    $no++;
}

echo "</table>";

echo "</body></html>";
?>
