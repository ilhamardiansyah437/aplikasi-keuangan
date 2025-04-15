<?php
// Pastikan session sudah start dan user login
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Aplikasi Keuangan</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="nav-link disabled">Halo, <strong><?= htmlspecialchars($username) ?></strong> (<?= $role ?>)</span>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tambah_transaksi.php">Tambah Transaksi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="ubah_akun.php">Ubah Akun</a>
        </li>
        <?php if ($role === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link" href="manajemen_user.php">Manajemen User</a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link" href="logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
