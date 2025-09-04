<?php
include "koneksi.php";

// Ambil bulan & tahun dari GET
$filter_bulan = $_GET['bulan'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';

// --- Hitung data untuk Pie Chart ---
$where = "1=1";
if ($filter_bulan && $filter_tahun) {
    $where = "MONTH(tanggal)='$filter_bulan' AND YEAR(tanggal)='$filter_tahun'";
} elseif ($filter_tahun) {
    $where = "YEAR(tanggal)='$filter_tahun'";
}

$total_makan = $koneksi->query("SELECT SUM(makan) as total FROM transaksi WHERE $where")->fetch_assoc()['total'] ?? 0;
$total_kopi = $koneksi->query("SELECT SUM(kopi) as total FROM transaksi WHERE $where")->fetch_assoc()['total'] ?? 0;
$total_bensin = $koneksi->query("SELECT SUM(bensin) as total FROM transaksi WHERE $where")->fetch_assoc()['total'] ?? 0;

$total_tambahan = $koneksi->query("
    SELECT SUM(jumlah) as total 
    FROM pengeluaran p 
    JOIN transaksi t ON t.id = p.transaksi_id
    WHERE $where
")->fetch_assoc()['total'] ?? 0;

// --- Ambil data Gaji vs Pengeluaran per bulan ---
$chart_q = $koneksi->query("
    SELECT 
        DATE_FORMAT(tanggal, '%Y-%m') as periode,
        SUM(gaji) as total_gaji,
        SUM(makan + kopi + bensin) as total_pengeluaran
    FROM transaksi
    WHERE $where
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY periode ASC
");

$bulan = [];
$gaji_per_bulan = [];
$pengeluaran_per_bulan = [];
$sisa_per_bulan = [];

while ($row = $chart_q->fetch_assoc()) {
    $periode = $row['periode'];

    // Tambahkan pengeluaran tambahan
    $extra_q = $koneksi->query("
        SELECT SUM(jumlah) as tambahan 
        FROM pengeluaran p
        JOIN transaksi t ON t.id = p.transaksi_id
        WHERE DATE_FORMAT(t.tanggal, '%Y-%m') = '$periode'
    ");
    $extra_row = $extra_q->fetch_assoc();
    $tambahan = $extra_row['tambahan'] ?? 0;

    $bulan[] = $periode;
    $gaji_per_bulan[] = (int)$row['total_gaji'];
    $pengeluaran = (int)$row['total_pengeluaran'] + $tambahan;
    $pengeluaran_per_bulan[] = $pengeluaran;
    $sisa_per_bulan[] = (int)$row['total_gaji'] - $pengeluaran;
}

// --- Ambil list tahun untuk dropdown ---
$list_tahun_q = $koneksi->query("SELECT DISTINCT YEAR(tanggal) as th FROM transaksi ORDER BY th DESC");
$list_tahun = [];
while ($t = $list_tahun_q->fetch_assoc()) {
    $list_tahun[] = $t['th'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Chart Keuangan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-6">
  <h1 class="text-3xl font-bold mb-6">Laporan Keuangan - Chart</h1>

  <!-- Filter -->
  <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-end">
    <div>
      <label class="block mb-1">Bulan</label>
      <select name="bulan" class="border p-2 rounded">
        <option value="">Semua</option>
        <?php for ($m=1; $m<=12; $m++): ?>
          <option value="<?= $m ?>" <?= ($filter_bulan == $m ? 'selected' : '') ?>>
            <?= date("F", mktime(0,0,0,$m,1)) ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>
    <div>
      <label class="block mb-1">Tahun</label>
      <select name="tahun" class="border p-2 rounded">
        <option value="">Semua</option>
        <?php foreach ($list_tahun as $th): ?>
          <option value="<?= $th ?>" <?= ($filter_tahun == $th ? 'selected' : '') ?>><?= $th ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Filter
      </button>
    </div>
  </form>

  <!-- Grid Chart -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Pie Chart -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Distribusi Pengeluaran</h2>
      <canvas id="pengeluaranChart" class="w-full h-64"></canvas>
    </div>

    <!-- Bar Chart -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Gaji vs Pengeluaran per Bulan</h2>
      <canvas id="gajiPengeluaranChart" class="w-full h-64"></canvas>
    </div>

    <!-- Line Chart -->
    <div class="bg-white p-6 rounded-lg shadow md:col-span-2">
      <h2 class="text-xl font-semibold mb-4">Perkembangan Sisa Tabungan</h2>
      <canvas id="sisaChart" class="w-full h-72"></canvas>
    </div>

  </div>
</div>

<script>
  // Pie Chart
  const ctx = document.getElementById('pengeluaranChart').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Makan', 'Kopi', 'Bensin', 'Tambahan'],
      datasets: [{
        data: [<?= $total_makan ?>, <?= $total_kopi ?>, <?= $total_bensin ?>, <?= $total_tambahan ?>],
        backgroundColor: ['#f87171', '#60a5fa', '#34d399', '#fbbf24'],
      }]
    }
  });

  // Bar Chart
  const ctx2 = document.getElementById('gajiPengeluaranChart').getContext('2d');
  new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: <?= json_encode($bulan) ?>,
      datasets: [
        {
          label: 'Gaji',
          data: <?= json_encode($gaji_per_bulan) ?>,
          backgroundColor: '#60a5fa'
        },
        {
          label: 'Pengeluaran',
          data: <?= json_encode($pengeluaran_per_bulan) ?>,
          backgroundColor: '#f87171'
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString();
            }
          }
        }
      }
    }
  });

  // Line Chart
  const ctx3 = document.getElementById('sisaChart').getContext('2d');
  new Chart(ctx3, {
    type: 'line',
    data: {
      labels: <?= json_encode($bulan) ?>,
      datasets: [{
        label: 'Sisa Tabungan',
        data: <?= json_encode($sisa_per_bulan) ?>,
        borderColor: '#34d399',
        backgroundColor: 'rgba(52,211,153,0.2)',
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
</script>

</body>
</html>
