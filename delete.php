<?php 
// Mendapatkan ID yang akan dihapus dari URL
$id = $_GET['id'];

// Membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "yanti123", "cafe_skd");

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Query untuk menghapus data berdasarkan ID
$query = "DELETE FROM cafe WHERE id = $id";

// Menjalankan query
if (mysqli_query($conn, $query)) {
    // Jika berhasil menghapus, redirect ke halaman read.php
    header("Location: dashboard.php"); // Ganti dengan URL halaman yang ingin dituju setelah delete
    exit(); // Pastikan untuk menghentikan script setelah redirect
} else {
    // Jika gagal menghapus, tampilkan pesan error
    echo "Error: " . mysqli_error($conn);
}

// Menutup koneksi
mysqli_close($conn);
?>