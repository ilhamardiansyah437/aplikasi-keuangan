<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $type = $_POST['transaction_type'];
    $desc = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];

    if ($amount <= 0) {
        $error = "Jumlah harus lebih dari 0.";
    } elseif (!in_array($type, ['in', 'out'])) {
        $error = "Tipe transaksi tidak valid.";
    } else {
        $sql = "INSERT INTO transactions (user_id, amount, transaction_type, description) VALUES ($user_id, $amount, '$type', '$desc')";
        if ($conn->query($sql)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menyimpan transaksi: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tambah Transaksi</title>
    <style>
        /* Reset dan font */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 40px;
        }

        .container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        a.back-link {
            display: inline-block;
            margin-bottom: 25px;
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        a.back-link:hover {
            color: #1d6fa5;
        }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
        }

        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1.8px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            resize: vertical;
        }

        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        textarea {
            min-height: 80px;
        }

        button {
            width: 100%;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 700;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1d6fa5;
        }

        .error-message {
            background-color: #ffe6e6;
            border: 1px solid #ff4d4d;
            color: #b30000;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Transaksi Baru</h2>
        <a href="index.php" class="back-link">&larr; Kembali ke Dashboard</a>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="amount">Jumlah:</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required placeholder="Masukkan jumlah transaksi" />

            <label for="transaction_type">Tipe Transaksi:</label>
            <select id="transaction_type" name="transaction_type" required>
                <option value="in">Masuk (Pemasukan)</option>
                <option value="out">Keluar (Pengeluaran)</option>
            </select>

            <label for="description">Deskripsi:</label>
            <textarea id="description" name="description" placeholder="Deskripsi transaksi (opsional)"></textarea>

            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
