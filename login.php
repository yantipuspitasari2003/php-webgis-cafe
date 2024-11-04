<?php
session_start();
require 'config.php'; // Ensure your database connection is set up correctly

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim and sanitize user input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare query to select user based on email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($connection, $query);
    
    // Check if the statement was prepared correctly
    if ($stmt === false) {
        die('Error preparing statement: ' . mysqli_error($connection));
    }

    // Bind parameter and execute
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verify user credentials
    if ($user) {
        // User found, now verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables for logged-in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            // Password is incorrect
            $error = "Password salah."; // Incorrect password
        }
    } else {
        // Email not found
        $error = "Email tidak terdaftar."; // Email not registered
    }

    // Close prepared statement
    mysqli_stmt_close($stmt);
}

// Close database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafe SKD</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Add Font Awesome -->
    <style>
        body {
            background-image: url('gambar_cafe.jpg'); /* Background image */
            background-size: cover; /* Ensure full cover */
            background-position: center; /* Center the image */
            background-repeat: no-repeat; /* Avoid repeating */
            background-attachment: fixed; /* Fixed background */
            display: flex; /* Flexbox for centering */
            align-items: center; /* Vertical centering */
            justify-content: center; /* Horizontal centering */
            height: 100vh; /* Full height */
            margin: 0; /* Remove default margin */
            font-family: 'Montserrat', sans-serif; /* Montserrat font */
        }
        .login-form {
            width: 400px; /* Form width */
            padding: 30px; /* Inner padding */
            background-color: rgba(255, 255, 255, 0.3); /* Background color with transparency */
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5); /* Shadow effect */
            backdrop-filter: blur(10px); /* Blur effect */
        }
        .login-form h2 {
            margin-bottom: 20px; /* Space below heading */
            color: #fff; /* Heading color */
            text-align: center; /* Center heading */
            font-weight: 600; /* Bold weight */
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.8); /* Form control background */
            border: 1px solid #ccc; /* Border color */
            border-radius: 8px; /* Rounded corners */
        }
        .form-control:focus {
            border-color: #007bff; /* Focus border color */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Shadow on focus */
        }
        .btn-primary {
            background-color: #007bff; /* Button background */
            border: none; /* No border */
            border-radius: 8px; /* Rounded corners */
            font-weight: bold; /* Bold font */
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade on hover */
            transform: translateY(-2px); /* Slight lift effect */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Shadow on hover */
        }
        .footer-text {
            text-align: center; /* Center footer text */
            color: #fff; /* Footer text color */
        }
        .alert {
            border-radius: 8px; /* Rounded corners for alerts */
            margin-bottom: 15px; /* Space below alert */
        }
    </style>
</head>
<body>

<div class="login-form">
    <h2>Login</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email" class="text-white">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="text-white">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <p class="text-center footer-text">
            <a href="forgot_password.php" style="color: #007bff;">forgot password?</a> 
        </p>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
        <p class="text-center mt-3 footer-text">
            Belum punya akun? <a href="register.php" style="color: #007bff;">Daftar</a>
        </p>
    </form>
</div>

</body>
</html>
