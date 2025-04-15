<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

if ($role == 'admin') {
    $sql_pemasukan = "SELECT SUM(amount) AS total FROM transactions WHERE transaction_type = 'in'";
    $sql_pengeluaran = "SELECT SUM(amount) AS total FROM transactions WHERE transaction_type = 'out'";
    $sql_transaksi = "SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 10";
} else {
    $sql_pemasukan = "SELECT SUM(amount) AS total FROM transactions WHERE transaction_type = 'in' AND user_id = $user_id";
    $sql_pengeluaran = "SELECT SUM(amount) AS total FROM transactions WHERE transaction_type = 'out' AND user_id = $user_id";
    $sql_transaksi = "SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.user_id = $user_id ORDER BY t.created_at DESC LIMIT 10";
}

$result_pemasukan = $conn->query($sql_pemasukan);
$result_pengeluaran = $conn->query($sql_pengeluaran);
$result_transaksi = $conn->query($sql_transaksi);

$total_pemasukan = $result_pemasukan->fetch_assoc()['total'] ?? 0;
$total_pengeluaran = $result_pengeluaran->fetch_assoc()['total'] ?? 0;
$saldo = $total_pemasukan - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Aplikasi Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: #f0e6ff;
            padding-bottom: 60px;
        }

        .navbar, .card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }

        .navbar-brand, .nav-link {
            color: #ffffff !important;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #ffd6ff !important;
        }

        h3, h4 {
            color: #ffffff;
            font-weight: 600;
        }

        .card-header {
            background: linear-gradient(to right, #9c27b0, #7b1fa2);
            color: #fff;
            font-weight: 600;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .card-title {
            font-size: 1.5rem;
        }

        .table-primary {
            background-color: #7b4de1 !important;
            color: #fff;
        }

        .table {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f0e6ff;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
            border-radius: 0.75rem;
        }

        .btn-warning {
            background-color: #a67cff;
            border: none;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            background-color: #7a4de1;
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: #ff6ac1;
            border: none;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #e040fb;
            transform: scale(1.05);
        }

        .badge.bg-success {
            background-color: #b9f6ca;
            color: #1b5e20;
        }

        .badge.bg-danger {
            background-color: #ffcdd2;
            color: #b71c1c;
        }

        .shadow-sm {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4 px-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Aplikasi Keuangan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <span class="nav-link disabled">Halo, <strong><?= htmlspecialchars($username) ?></strong> (<?= $role ?>)</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tambah_transaksi.php">Tambah Transaksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ubah_akun.php">Ubah Akun</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4">Dashboard Keuangan</h3>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white mb-3 shadow-sm">
                <div class="card-header">Total Pemasukan</div>
                <div class="card-body">
                    <h5 class="card-title">Rp <?= number_format($total_pemasukan, 2, ',', '.') ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white mb-3 shadow-sm">
                <div class="card-header">Total Pengeluaran</div>
                <div class="card-body">
                    <h5 class="card-title">Rp <?= number_format($total_pengeluaran, 2, ',', '.') ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white mb-3 shadow-sm">
                <div class="card-header">Saldo</div>
                <div class="card-body">
                    <h5 class="card-title">Rp <?= number_format($saldo, 2, ',', '.') ?></h5>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Transaksi Terbaru</h4>
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle mb-5">
            <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Jumlah (Rp)</th>
                <th>Tipe</th>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result_transaksi->num_rows > 0): ?>
                <?php while ($row = $result_transaksi->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= number_format($row['amount'], 2, ',', '.') ?></td>
                        <td>
                            <?php if ($row['transaction_type'] == 'in'): ?>
                                <span class="badge bg-success">Masuk</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if ($role == 'admin' || $row['user_id'] == $user_id): ?>
                                <a href="edit_transaksi.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="hapus_transaksi.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
