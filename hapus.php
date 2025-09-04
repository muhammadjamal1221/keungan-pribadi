<?php
include "koneksi.php";

$id = $_GET['id'];
$koneksi->query("DELETE FROM transaksi WHERE id=$id");

header("Location: laporan.php");
?>
