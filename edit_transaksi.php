<?php
session_start();
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
    die("Anda tidak memiliki hak akses untuk mengedit transaksi ini.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $type = $_POST['transaction_type'];
    $desc = $conn->real_escape_string($_POST['description']);
    $created_at_input = $_POST['created_at'];

    // Ubah format datetime-local ke format MySQL DATETIME
    $created_at = date('Y-m-d H:i:s', strtotime($created_at_input));

    if ($amount <= 0) {
        $error = "Jumlah harus lebih dari 0.";
    } elseif (!in_array($type, ['in', 'out'])) {
        $error = "Tipe transaksi tidak valid.";
    } elseif (!$created_at) {
        $error = "Tanggal dan waktu tidak valid.";
    } else {
        $sql_update = "UPDATE transactions SET amount = $amount, transaction_type = '$type', description = '$desc', created_at = '$created_at' WHERE id = $id";
        if ($conn->query($sql_update)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal update transaksi: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Transaksi</title>
    <style>
        /* Reset dan font */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg,rgb(133, 29, 29) 0%, #fda085 100%);
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background:rgb(28, 130, 57);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(171, 16, 29, 0.4);
            width: 400px;
            max-width: 100%;
        }

        h2 {
            color:rgb(0, 0, 0);
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
        }

        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            color:rgb(72, 153, 60);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        a.back-link:hover {
            color:rgb(0, 0, 0);
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color:rgb(0, 0, 0);
        }

        input[type="number"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.8px solid #fbc02d;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 20px;
            transition: border-color 0.3s ease;
            font-family: inherit;
            resize: vertical;
        }

        input[type="number"]:focus,
        input[type="datetime-local"]:focus,
        select:focus,
        textarea:focus {
            border-color: #ffca28;
            outline: none;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.6);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #fbc02d;
            border: none;
            border-radius: 8px;
            color: #5d4037;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color:rgb(23, 171, 136);
            color: #3e2723;
        }

        .error {
            background-color: #fff3e0;
            border-left: 6px solidrgb(65, 148, 26);
            padding: 12px 15px;
            margin-bottom: 20px;
            color: #e65100;
            font-weight: 600;
            border-radius: 6px;
            text-align: left;
        }

        @media (max-width: 450px) {
            .container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Transaksi</h2>
    <a href="index.php" class="back-link">&larr; Kembali ke Dashboard</a>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="amount">Jumlah:</label>
        <input type="number" id="amount" name="amount" step="0.01" required value="<?= htmlspecialchars($transaksi['amount']) ?>">

        <label for="transaction_type">Tipe Transaksi:</label>
        <select id="transaction_type" name="transaction_type" required>
            <option value="in" <?= $transaksi['transaction_type'] == 'in' ? 'selected' : '' ?>>Masuk (Pemasukan)</option>
            <option value="out" <?= $transaksi['transaction_type'] == 'out' ? 'selected' : '' ?>>Keluar (Pengeluaran)</option>
        </select>

        <label for="created_at">Tanggal & Waktu:</label>
        <input type="datetime-local" id="created_at" name="created_at" required value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($transaksi['created_at']))) ?>">

        <label for="description">Deskripsi:</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($transaksi['description']) ?></textarea>

        <button type="submit">Update</button>
    </form>
</div>

</body>
</html>