<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user
$sql = "SELECT password FROM users WHERE id = $user_id LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("User tidak ditemukan.");
}
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field wajib diisi.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = "Password lama salah.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi tidak cocok.";
    } else {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = '$new_password_hash' WHERE id = $user_id";
        if ($conn->query($update_sql)) {
            $success = "Password berhasil diubah.";
        } else {
            $error = "Gagal mengubah password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Ubah Password</title>
    <style>
        /* Styling sederhana */
        body { font-family: Arial, sans-serif; background: #f4f7f8; padding: 40px; }
        .container { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type=password] { width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
        button { margin-top: 20px; width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .error { color: #b30000; margin-top: 10px; }
        .success { color: #237a1a; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Ubah Password</h2>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="current_password">Password Lama</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">Password Baru</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Konfirmasi Password Baru</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Simpan</button>
    </form>
</div>
</body>
</html>
