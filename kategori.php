<?php
include "koneksi.php";

// Tambah kategori
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $koneksi->query("INSERT INTO kategori (nama) VALUES ('$nama')");
    header("Location: kategori.php");
}

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM kategori WHERE id=$id");
    header("Location: kategori.php");
}

// Ambil semua kategori
$result = $koneksi->query("SELECT * FROM kategori ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Kategori</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-6">
    <h1 class="text-2xl font-bold mb-4 text-blue-600">⚙️ Kelola Kategori Pengeluaran</h1>

    <form method="POST" class="flex space-x-2 mb-4">
      <input type="text" name="nama" placeholder="Nama Kategori" required class="border rounded-lg p-2 w-full">
      <button type="submit" name="tambah" class="bg-green-600 text-white px-4 rounded-lg hover:bg-green-700">Tambah</button>
    </form>

    <table class="w-full border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-200">
          <th class="border px-3 py-2">ID</th>
          <th class="border px-3 py-2">Nama</th>
          <th class="border px-3 py-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="border px-3 py-2"><?= $row['id'] ?></td>
            <td class="border px-3 py-2"><?= $row['nama'] ?></td>
            <td class="border px-3 py-2">
              <a href="kategori.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus kategori ini?')" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="mt-4">
      <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">⬅ Kembali</a>
    </div>
  </div>
</body>
</html>
