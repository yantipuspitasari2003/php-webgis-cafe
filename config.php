<?php
$host = 'localhost';  // Your database host
$user = 'root';       // Your database username
$password = 'yanti123'; // Your database password
$dbname = 'cafe_skd'; // Your database name

// Create connection
$connection = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
