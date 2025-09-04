<?php
include "koneksi.php";

if (isset($_POST['simpan'])) {
    $jumlah_total = $_POST['jumlah'];
    $bulan_mulai  = $_POST['bulan_mulai'];
    $tahun_mulai  = $_POST['tahun_mulai'];
    $bulan_selesai = $_POST['bulan_selesai'];
    $tahun_selesai = $_POST['tahun_selesai'];

    // hitung jumlah bulan
    $start = new DateTime("$tahun_mulai-$bulan_mulai-01");
    $end   = new DateTime("$tahun_selesai-$bulan_selesai-01");
    $end->modify('+1 month'); // inclusive

    $interval = new DateInterval('P1M');
    $periode = new DatePeriod($start, $interval, $end);

    $total_bulan = iterator_count($periode);
    $target_bulanan = floor($jumlah_total / $total_bulan);

    // simpan per bulan
    foreach ($periode as $date) {
        $bulan = $date->format("n");
        $tahun = $date->format("Y");

        $cek = $koneksi->query("SELECT * FROM target_tabungan WHERE bulan='$bulan' AND tahun='$tahun'");
        if ($cek->num_rows > 0) {
            $koneksi->query("UPDATE target_tabungan SET jumlah='$target_bulanan' WHERE bulan='$bulan' AND tahun='$tahun'");
        } else {
            $koneksi->query("INSERT INTO target_tabungan (jumlah, bulan, tahun) VALUES ('$target_bulanan','$bulan','$tahun')");
        }
    }

    header("Location: target.php");
}

// Ambil semua target
$target = $koneksi->query("SELECT * FROM target_tabungan ORDER BY tahun DESC, bulan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Target Tabungan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl p-6">
    <h1 class="text-2xl font-bold mb-4 text-green-600">🎯 Atur Target Tabungan</h1>

    <!-- Form periode -->
    <form method="POST" class="grid grid-cols-2 gap-3 mb-6">
      <input type="number" name="jumlah" placeholder="Total Target (Rp)" required class="border rounded-lg p-2 col-span-2">

      <label class="col-span-2 font-semibold">Periode:</label>
      <div>
        <select name="bulan_mulai" class="border rounded-lg p-2" required>
          <?php for($i=1; $i<=12; $i++): ?>
            <option value="<?= $i ?>"><?= date("F", mktime(0,0,0,$i,1)) ?></option>
          <?php endfor; ?>
        </select>
        <input type="number" name="tahun_mulai" value="<?= date('Y') ?>" required class="border rounded-lg p-2 w-24">
      </div>
      <div>
        <select name="bulan_selesai" class="border rounded-lg p-2" required>
          <?php for($i=1; $i<=12; $i++): ?>
            <option value="<?= $i ?>"><?= date("F", mktime(0,0,0,$i,1)) ?></option>
          <?php endfor; ?>
        </select>
        <input type="number" name="tahun_selesai" value="<?= date('Y') ?>" required class="border rounded-lg p-2 w-24">
      </div>

      <button type="submit" name="simpan" class="col-span-2 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Simpan Target</button>
    </form>

    <!-- List target -->
    <table class="w-full border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-200">
          <th class="border px-3 py-2">Bulan</th>
          <th class="border px-3 py-2">Tahun</th>
          <th class="border px-3 py-2">Target Bulanan (Rp)</th>
          <th class="border px-3 py-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $target->fetch_assoc()): ?>
          <tr>
            <td class="border px-3 py-2"><?= date("F", mktime(0,0,0,$row['bulan'],1)) ?></td>
            <td class="border px-3 py-2"><?= $row['tahun'] ?></td>
            <td class="border px-3 py-2 font-semibold text-blue-700">Rp <?= number_format($row['jumlah'],0,",",".") ?></td>
            <td class="border px-3 py-2 text-center">
              <a href="hapus_target.php?id=<?= $row['id'] ?>" 
                 onclick="return confirm('Yakin ingin hapus target bulan ini?')" 
                 class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="mt-4">
      <a href="laporan.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">⬅ Kembali</a>
    </div>
  </div>
</body>
</html>
