<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data transaksi
$sql = "SELECT * FROM transactions WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
    die("Transaksi tidak ditemukan.");
}

$transaksi = $result->fetch_assoc();

// Cek hak akses: admin atau pemilik transaksi
if ($role != 'admin' && $transaksi['user_id'] != $user_id) {
    die("Anda tidak memiliki hak akses untuk menghapus transaksi ini.");
}

// Hapus transaksi
$sql_delete = "DELETE FROM transactions WHERE id = $id";
if ($conn->query($sql_delete)) {
    header("Location: index.php");
    exit;
} else {
    die("Gagal menghapus transaksi: " . $conn->error);
}
?>
