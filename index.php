<?php include "koneksi.php"; ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengelolaan Keuangan Pribadi</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-blue-200 min-h-screen flex items-center justify-center">

  <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-3xl">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">💰 Pengelolaan Keuangan Pribadi</h1>

    <!-- Form Input -->
    <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium text-gray-700">Gaji</label>
        <input type="number" name="gaji" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div>
        <label class="block mb-1 font-medium text-gray-700">Uang Makan</label>
        <input type="number" name="makan" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div>
        <label class="block mb-1 font-medium text-gray-700">Uang Kopi</label>
        <input type="number" name="kopi" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div>
        <label class="block mb-1 font-medium text-gray-700">Uang Bensin</label>
        <input type="number" name="bensin" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1 font-medium text-gray-700">Tanggal</label>
        <input type="date" name="tanggal" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div class="md:col-span-2">
        <button type="submit" name="simpan" class="w-full bg-blue-600 text-white font-semibold p-3 rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
          💾 Simpan & Hitung
        </button>
      </div>
    </form>

    <?php
    if (isset($_POST['simpan'])) {
        $gaji   = $_POST['gaji'];
        $makan  = $_POST['makan'];
        $kopi   = $_POST['kopi'];
        $bensin = $_POST['bensin'];
        $tanggal= $_POST['tanggal'];

        // Simpan ke database
        $sql = "INSERT INTO transaksi (gaji, makan, kopi, bensin, tanggal)
                VALUES ('$gaji', '$makan', '$kopi', '$bensin', '$tanggal')";
        $koneksi->query($sql);

        // Hitung total pengeluaran & sisa
        $total_pengeluaran = $makan + $kopi + $bensin;
        $sisa = $gaji - $total_pengeluaran;

        // Rekomendasi (misal 60% tabungan, 40% darurat)
        $tabungan = $sisa * 0.6;
        $darurat  = $sisa * 0.4;
    ?>
        <!-- Hasil -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-green-100 p-5 rounded-xl shadow">
            <h2 class="text-xl font-semibold text-green-700 mb-2">📉 Ringkasan</h2>
            <p>Total Pengeluaran: <b>Rp <?= number_format($total_pengeluaran,0,",",".") ?></b></p>
            <p>Sisa Uang: <b>Rp <?= number_format($sisa,0,",",".") ?></b></p>
          </div>
          <div class="bg-yellow-100 p-5 rounded-xl shadow">
            <h2 class="text-xl font-semibold text-yellow-700 mb-2">💡 Rekomendasi</h2>
            <p>Tabungan: <b>Rp <?= number_format($tabungan,0,",",".") ?></b></p>
            <p>Dana Darurat: <b>Rp <?= number_format($darurat,0,",",".") ?></b></p>
          </div>
        </div>
    <?php } ?>

    <!-- Link ke Laporan -->
    <div class="mt-6 text-center">
      <a href="laporan.php" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition"> 📊lihat laporan </a>
      <a href="chart.php" 
     class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">
    📊 Laporan Chart
  </a>
    </div>
  </div>
</body>
</html>
