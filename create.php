<?php 

// Start session for user authentication if needed
session_start();

// Check if the user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connect to the database
$connection = mysqli_connect("localhost", "root", "yanti123", "cafe_skd");

// Ensure connection is successful
if (!$connection) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Validate and sanitize input data
$name = mysqli_real_escape_string($connection, $_POST['inputName']);
$address = mysqli_real_escape_string($connection, $_POST['inputAddress']);
$nohp =  mysqli_real_escape_string($connection, $_POST['no_hp']);
$kategori =  mysqli_real_escape_string($connection, $_POST['kategori']);

$latitude = floatval($_POST['inputLatitude']);
$longitude = floatval($_POST['inputLongitude']);

// Debugging output (remove in production)
var_dump($_POST);

// Prepare the SQL query
$query = "INSERT INTO cafe (name, address,no_hp, kategori_ruang, coordinate) 
          VALUES ('$name', '$address',$nohp,'$kategori', ST_GeomFromText('POINT($latitude $longitude)', 4326))";

// Execute the query and handle errors
if (mysqli_query($connection, $query)) {
    header("Location: dashboard.php"); // Redirect if successful
    exit();
} else {
    echo "Terjadi kesalahan: " . mysqli_error($connection);
}

// Close the database connection
mysqli_close($connection);
?>
