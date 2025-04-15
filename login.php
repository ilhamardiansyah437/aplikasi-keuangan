<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = md5($_POST['password']); // Untuk produksi, gunakan password_hash()

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Aplikasi Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
    /* Background gradient with subtle animation */
    body {
        background: linear-gradient(135deg, #764ba2, #667eea);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden;
    }

    /* Card styling */
    .card-login {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        width: 380px;
        padding: 2.5rem 2rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    .card-login:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    }

    /* Title */
    .card-login h3 {
        color: #000; /* Warna hitam */
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
        letter-spacing: 1.2px;
    }

    /* Clock styling */
    #clock {
        font-size: 1.3rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 1.5rem;
        color: #764ba2;
        letter-spacing: 2px;
        font-family: 'Courier New', Courier, monospace;
        user-select: none;
    }

    /* Form controls with icon */
    .form-floating > .form-control {
        padding-left: 3rem;
        height: 3.2rem;
        font-size: 1rem;
    }
    .form-floating > .form-control:focus {
        border-color: #764ba2;
        box-shadow: 0 0 8px rgba(118, 75, 162, 0.5);
    }
    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #764ba2;
        font-size: 1.2rem;
        pointer-events: none;
    }

    /* Button styling */
    .btn-login {
        background: linear-gradient(45deg, #764ba2, #667eea);
        border: none;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 0.6rem 0;
        border-radius: 50px;
        transition: background 0.3s ease;
    }
    .btn-login:hover {
        background: linear-gradient(45deg, #5a2a83, #4a5bb8);
    }

    /* Error alert */
    .alert-danger {
        font-size: 0.9rem;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        margin-bottom: 1rem;
    }

    /* Footer */
    .footer-text {
        font-size: 0.85rem;
        color: #999;
        text-align: center;
        margin-top: 1.8rem;
        user-select: none;
    }
</style>

</head>
<body>
    <div class="card-login shadow-sm">
        <h3>Login Aplikasi Keuangan</h3>
        <div id="clock" aria-live="polite" aria-atomic="true"></div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="form-floating mb-3 position-relative">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                <label for="username">Username</label>
                <i class="fa-solid fa-user input-icon"></i>
            </div>
            <div class="form-floating mb-4 position-relative">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
                <i class="fa-solid fa-lock input-icon"></i>
            </div>
            <button type="submit" class="btn btn-login w-100">Masuk</button>
        </form>

        <p class="footer-text">© 2025 Aplikasi Keuangan</p>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const timeString = now.toLocaleTimeString('id-ID', options);
            document.getElementById('clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
