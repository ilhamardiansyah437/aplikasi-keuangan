<?php
if (session_id() == '') {
    session_start();
}

$host = "localhost";
$user = "root";
$password = ""; // Sesuaikan dengan password MySQL Anda
$dbname = "keuangan";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
