<?php
session_start();
require 'config.php'; // Ensure your database connection is correct

$error = ''; // Initialize error variable
$success_message = ''; // Initialize success message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi."; // All fields must be filled
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid."; // Invalid email format
    } elseif (strlen($password) > 8) {
        $error = "Password tidak boleh lebih dari 8 karakter."; // Password cannot exceed 8 characters
    } else {
        // Check if email or username already exists
        $checkQuery = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = mysqli_prepare($connection, $checkQuery);
        mysqli_stmt_bind_param($stmt, "ss", $email, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email atau username sudah terdaftar."; // Email or username already exists
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user into the database
            $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);
            
            if (mysqli_stmt_execute($stmt)) {
                // Set success message
                $success_message = "Registrasi berhasil! Silakan login."; // Registration successful
            } else {
                $error = "Error: " . mysqli_error($connection); // Database error message
            }
        }

        mysqli_stmt_close($stmt); // Close prepared statement
    }
}

mysqli_close($connection); // Close database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cafe SKD</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('gambar_cafe.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            color: #333;
        }
        .register-form {
            width: 400px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }
        .register-form h2 {
            margin-bottom: 20px;
            color: #fff;
            text-align: center;
            font-weight: 600;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .footer-text {
            text-align: center;
            color: #fff;
        }
        .alert-custom {
            background-color: #dc3545; /* Red background */
            color: white; /* White text */
        }
    </style>
</head>
<body>

<div class="register-form">
    <h2>Register</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="username" class="text-white">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email" class="text-white">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="text-white">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
        <p class="text-center mt-3 footer-text">Already have an account? <a href="login.php" style="color: #007bff;">Login</a></p>
    </form>
</div>

<!-- Modal for Error Notification -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorMessage" class="alert alert-custom"></div> <!-- Custom error styling -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function validateForm() {
        const password = document.querySelector('input[name="password"]').value;
        const errorMessage = document.getElementById('errorMessage');

        if (password.length > 8) {
            errorMessage.textContent = "Password tidak boleh lebih dari 8 karakter."; // Password cannot exceed 8 characters
            $('#errorModal').modal('show'); // Show modal
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
</script>

</body>
</html>
