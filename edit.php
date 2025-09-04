<?php
include "koneksi.php";

// Ambil data lama
$id = $_GET['id'];
$result = $koneksi->query("SELECT * FROM transaksi WHERE id=$id");
$data = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $gaji   = $_POST['gaji'];
    $makan  = $_POST['makan'];
    $kopi   = $_POST['kopi'];
    $bensin = $_POST['bensin'];
    $tanggal= $_POST['tanggal'];

    $koneksi->query("UPDATE transaksi SET 
        gaji='$gaji', makan='$makan', kopi='$kopi', bensin='$bensin', tanggal='$tanggal' 
        WHERE id=$id");

    header("Location: laporan.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Keuangan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white shadow-lg rounded-2xl p-6 w-full max-w-lg">
    <h1 class="text-2xl font-bold text-center mb-4 text-yellow-600">✏️ Edit Data</h1>
    <form method="POST" class="space-y-3">
      <div>
        <label class="block mb-1">Gaji</label>
        <input type="number" name="gaji" value="<?= $data['gaji'] ?>" class="w-full border rounded-lg p-2" required>
      </div>
      <div>
        <label class="block mb-1">Makan</label>
        <input type="number" name="makan" value="<?= $data['makan'] ?>" class="w-full border rounded-lg p-2" required>
      </div>
      <div>
        <label class="block mb-1">Kopi</label>
        <input type="number" name="kopi" value="<?= $data['kopi'] ?>" class="w-full border rounded-lg p-2" required>
      </div>
      <div>
        <label class="block mb-1">Bensin</label>
        <input type="number" name="bensin" value="<?= $data['bensin'] ?>" class="w-full border rounded-lg p-2" required>
      </div>
      <div>
        <label class="block mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" class="w-full border rounded-lg p-2" required>
      </div>
      <button type="submit" name="update" class="w-full bg-yellow-500 text-white p-2 rounded-lg hover:bg-yellow-600">Simpan Perubahan</button>
    </form>
  </div>
</body>
</html>
