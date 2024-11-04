<?php
session_start();
require 'config.php'; // Pastikan ini berisi koneksi database

$error = '';
$message = '';

// Cek token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Cek apakah token valid
    $query = "SELECT * FROM users WHERE reset_token = '$token' AND token_expiry > NOW()";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $error = "Token tidak valid atau sudah kadaluarsa.";
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Proses reset password
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        
        // Update password dan hapus token
        $updateQuery = "UPDATE users SET password = '$newPassword', reset_token = NULL, token_expiry = NULL WHERE id = " . $user['id'];
        
        if (mysqli_query($connection, $updateQuery)) {
            $message = "Kata sandi berhasil diubah. Silakan <a href='login.php'>masuk</a>.";
        } else {
            $error = "Gagal mengubah kata sandi.";
        }
    }
} else {
    header("Location: forgot_password.php"); // Redirect jika token tidak ada
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling dasar untuk form */
        body {
            background-color: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            width: 400px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Reset Password</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
    </form>
    <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
</div>

</body>
</html>
