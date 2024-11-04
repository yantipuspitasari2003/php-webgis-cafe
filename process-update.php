<?php
// Mendapatkan data yang dikirim melalui POST
$id = $_POST['id']; // Pastikan form mengirim ID sebagai hidden input
$name = $_POST['name'];
$address = $_POST['address'];
$nohp = intval($_POST['no_hp']); 
$kategori =  $_POST['kategori'];
$lat = $_POST['latitude'];
$lng = $_POST['longitude'];

// Validasi data input (misalnya memastikan bahwa ID, nama, dan alamat tidak kosong)

// Membuat koneksi ke database
$connection = mysqli_connect("localhost", "root", "yanti123", "cafe_skd");

// Cek apakah koneksi berhasil
if (!$connection) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Membuat query dengan prepared statement untuk menghindari SQL injection
$query = "
    UPDATE cafe
    SET
        name = ?,
        address = ?,
        no_hp = ?,
        kategori_ruang = ?,
        coordinate = ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')'), 4326)
    WHERE id = ?
";

// Menyiapkan statement
$stmt = mysqli_prepare($connection, $query);


mysqli_stmt_bind_param($stmt, 'ssisddi', $name, $address, $nohp, $kategori, $lat, $lng, $id);


// Menjalankan statement
if (mysqli_stmt_execute($stmt)) {
    // Redirect ke read.php setelah data berhasil diupdate
    header("Location: dashboard.php");
    exit(); // Pastikan untuk menghentikan script setelah redirect
} else {
    // Menampilkan pesan error jika terjadi kesalahan
    echo "Error: " . mysqli_error($connection);
}

// Menutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($connection);
?>