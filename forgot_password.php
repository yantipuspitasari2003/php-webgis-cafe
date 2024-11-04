<?php
session_start();
require 'config.php'; // Pastikan ini berisi koneksi database

$message = '';
$error = '';

// Proses form saat di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Cek apakah email ada di database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($connection, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Generate token dan simpan di database
        $token = bin2hex(random_bytes(50)); // Generate token
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token valid selama 1 jam
        
        // Simpan token ke database
        $updateQuery = "UPDATE users SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'";
        if (mysqli_query($connection, $updateQuery)) {
            // Kirim email (gunakan mail() atau library seperti PHPMailer untuk email yang lebih baik)
            $resetLink = "http://yourwebsite.com/reset_password.php?token=$token"; // Ganti dengan URL Anda
            $subject = "Reset Password";
            $message = "Klik link ini untuk mereset kata sandi Anda: $resetLink";
            
            // Untuk saat ini, kita hanya akan menampilkan link. Anda bisa menggunakan fungsi mail() untuk mengirim email.
            $message = "Link reset password telah dikirim ke email Anda. Silakan periksa inbox Anda.";
        } else {
            $error = "Gagal menyimpan token. Coba lagi.";
        }
    } else {
        $error = "Email tidak terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Styling untuk latar belakang */
        body {
            background-image: url('gambar_cafe.jpg'); /* Ganti dengan URL gambar latar belakang yang Anda inginkan */
            background-size: cover; /* Pastikan gambar menutupi seluruh latar belakang */
            background-position: center; /* Posisikan gambar di tengah */
            background-repeat: no-repeat; /* Hindari pengulangan gambar */
            display: flex; /* Flexbox untuk pemusatan konten */
            align-items: center; /* Memusatkan secara vertikal */
            justify-content: center; /* Memusatkan secara horizontal */
            height: 100vh; /* Tinggi layar penuh */
            margin: 0; /* Menghilangkan margin default */
            font-family: 'Montserrat', sans-serif; /* Menggunakan font Montserrat */
        }
        .form-container {
            width: 400px; /* Lebar kotak */
            padding: 30px; /* Padding di dalam kotak */
            background-color: rgba(255, 255, 255, 0.5); /* Latar belakang putih dengan transparansi lebih tinggi */
            border-radius: 15px; /* Sudut membulat */
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5); /* Bayangan di bawah kotak */
            backdrop-filter: blur(15px); /* Efek blur di belakang kotak lebih kuat */
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Forgot Password</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
    </form>
    <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
</div>

</body>
</html>
