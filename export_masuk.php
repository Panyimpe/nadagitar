<?php
require 'function.php';
session_start();

// Validasi login
$iduser = $_SESSION['iduser'] ?? null;
if (!$iduser) {
    die("Akses ditolak");
}

// Header untuk file Word
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=barang_masuk.doc");
header("Pragma: no-cache");
header("Expires: 0");

// Mulai output dokumen Word
echo "<html>";
echo "<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial; }
        h2 { text-align: center; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; }
    </style>
</head>";
echo "<body>";

echo "<h2>Data Barang Masuk</h2>";

echo "<table>
<tr>
    <th>Tanggal</th>
    <th>Nama Barang</th>
    <th>Jumlah</th>
    <th>Stock Sekarang</th>
</tr>";

// Query data barang masuk
$sql = "
SELECT m.*, s.namabarang, s.stock 
FROM masuk m
JOIN stock s ON m.idbarang = s.idbarang
WHERE m.iduser = '$iduser'
ORDER BY m.tanggal DESC
";

$result = mysqli_query($conn, $sql);

// Tampilkan data
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>" . htmlspecialchars($row['tanggal']) . "</td>
        <td>" . htmlspecialchars($row['namabarang']) . "</td>
        <td>" . htmlspecialchars($row['qty']) . "</td>
        <td>" . htmlspecialchars($row['stock']) . "</td>
    </tr>";
}

echo "</table>";

echo "</body></html>";
?>
