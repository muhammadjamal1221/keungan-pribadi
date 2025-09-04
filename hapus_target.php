<?php
include "koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $koneksi->query("DELETE FROM target_tabungan WHERE id='$id'");
}

header("Location: target.php");
exit;
