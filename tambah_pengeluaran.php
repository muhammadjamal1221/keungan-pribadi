<?php
include "koneksi.php";

// Ambil daftar kategori
$kategori = $koneksi->query("SELECT * FROM kategori ORDER BY nama ASC");

// Simpan pengeluaran
if (isset($_POST['simpan'])) {
    $transaksi_id = $_POST['transaksi_id'];
    $kategori_id  = $_POST['kategori_id'];
    $jumlah       = $_POST['jumlah'];

    $koneksi->query("INSERT INTO pengeluaran (transaksi_id, kategori_id, jumlah) 
                     VALUES ('$transaksi_id', '$kategori_id', '$jumlah')");

    header("Location: laporan.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Pengeluaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-lg rounded-2xl p-6 w-full max-w-md">
    <h1 class="text-2xl font-bold text-center mb-4 text-purple-600">➕ Tambah Pengeluaran</h1>
    <form method="POST" class="space-y-3">
      <input type="hidden" name="transaksi_id" value="<?= $_GET['id'] ?>">

      <div>
        <label class="block mb-1">Kategori</label>
        <select name="kategori_id" class="w-full border rounded-lg p-2" required>
          <option value="">-- Pilih Kategori --</option>
          <?php while ($row = $kategori->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div>
        <label class="block mb-1">Jumlah (Rp)</label>
        <input type="number" name="jumlah" class="w-full border rounded-lg p-2" required>
      </div>

      <button type="submit" name="simpan" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">Simpan</button>
    </form>
  </div>
</body>
</html>
