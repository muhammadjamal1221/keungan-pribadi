<?php
include "koneksi.php";

// Ambil semua transaksi
$query = "SELECT * FROM transaksi ORDER BY tanggal DESC";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Keuangan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-6">
    <h1 class="text-3xl font-bold text-center mb-6 text-blue-600">📊 Laporan Keuangan</h1>

    <!-- Tombol navigasi -->
    <div class="flex space-x-2 mb-4">
      <a href="index.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">+ Tambah Data</a>
      <a href="kategori.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">⚙️ Kelola Kategori</a>
      <a href="target.php" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">🎯 Atur Target</a>
     <a href="export_pdf.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">⬇ Export PDF</a>
      
    </div>

    <table class="w-full border-collapse border border-gray-300 text-sm">
      <thead>
        <tr class="bg-gray-200 text-left">
          <th class="border border-gray-300 px-3 py-2">Tanggal</th>
          <th class="border border-gray-300 px-3 py-2">Gaji</th>
          <th class="border border-gray-300 px-3 py-2">Makan</th>
          <th class="border border-gray-300 px-3 py-2">Kopi</th>
          <th class="border border-gray-300 px-3 py-2">Bensin</th>
          <th class="border border-gray-300 px-3 py-2">Pengeluaran Lain</th>
          <th class="border border-gray-300 px-3 py-2">Total Pengeluaran</th>
          <th class="border border-gray-300 px-3 py-2">Sisa</th>
          <th class="border border-gray-300 px-3 py-2">Target (Rp)</th>
          <th class="border border-gray-300 px-3 py-2">Status</th>
          <th class="border border-gray-300 px-3 py-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            // Hitung pengeluaran tambahan
            $pengeluaran_q = $koneksi->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE transaksi_id={$row['id']}");
            $pengeluaran_lain = $pengeluaran_q->fetch_assoc()['total'] ?? 0;

            // Hitung total & sisa
            $total_pengeluaran = $row['makan'] + $row['kopi'] + $row['bensin'] + $pengeluaran_lain;
            $sisa = $row['gaji'] - $total_pengeluaran;

            // Cek target tabungan
            $bulan = date("n", strtotime($row['tanggal']));
            $tahun = date("Y", strtotime($row['tanggal']));
            $target_q = $koneksi->query("SELECT jumlah FROM target_tabungan WHERE bulan='$bulan' AND tahun='$tahun'");
            $target_row = $target_q->fetch_assoc();
            $target = $target_row['jumlah'] ?? 0;

            $status_target = ($sisa >= $target && $target > 0) 
              ? "<span class='text-green-700 font-bold'>✅ Tercapai</span>" 
              : (($target > 0) ? "<span class='text-red-600 font-bold'>❌ Belum</span>" : "-");

            echo "<tr>
                    <td class='border px-3 py-2'>{$row['tanggal']}</td>
                    <td class='border px-3 py-2'>Rp " . number_format($row['gaji'],0,",",".") . "</td>
                    <td class='border px-3 py-2'>Rp " . number_format($row['makan'],0,",",".") . "</td>
                    <td class='border px-3 py-2'>Rp " . number_format($row['kopi'],0,",",".") . "</td>
                    <td class='border px-3 py-2'>Rp " . number_format($row['bensin'],0,",",".") . "</td>
                    <td class='border px-3 py-2 text-purple-700 font-semibold'>Rp " . number_format($pengeluaran_lain,0,",",".") . "</td>
                    <td class='border px-3 py-2'>Rp " . number_format($total_pengeluaran,0,",",".") . "</td>
                    <td class='border px-3 py-2 font-bold " . ($sisa >= 0 ? "text-green-700" : "text-red-600") . "'>Rp " . number_format($sisa,0,",",".") . "</td>
                    <td class='border px-3 py-2 font-semibold text-blue-700'>Rp " . number_format($target,0,",",".") . "</td>
                    <td class='border px-3 py-2 text-center'>{$status_target}</td>
                    <td class='border px-3 py-2 space-x-2'>
                      <a href='edit.php?id={$row['id']}' class='bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600'>Edit</a>
                      <a href='hapus.php?id={$row['id']}' onclick=\"return confirm('Yakin mau hapus data ini?');\" class='bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700'>Hapus</a>
                      <a href='tambah_pengeluaran.php?id={$row['id']}' class='bg-purple-600 text-white px-3 py-1 rounded-md hover:bg-purple-700'>+ Pengeluaran</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='11' class='text-center py-4 text-gray-500'>Belum ada data</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
