<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi Database
$connection = mysqli_connect("localhost", "root", "yanti123", "cafe_skd");

// Fungsi untuk query data
function send_query($query) {
    global $connection;
    $result = mysqli_query($connection, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Fungsi untuk mendapatkan jumlah cafe
function get_cafe_count() {
    global $connection;
    $result = mysqli_query($connection, "SELECT COUNT(*) AS total FROM cafe");
    $count = mysqli_fetch_assoc($result);
    return $count['total'];
}

// Menghitung jumlah cafe
$cafe_count = get_cafe_count();

// Proses Hapus Data
$success_message = '';
$error_message = '';
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM cafe WHERE id = $delete_id";
    if (mysqli_query($connection, $delete_query)) {
        $success_message = "Cafe berhasil dihapus.";
        $cafe_count--; // Update cafe count after deletion
    } else {
        $error_message = "Gagal menghapus cafe: " . mysqli_error($connection);
    }
}

// Ambil Data Cafe
$query = "SELECT id, name, address,no_hp,kategori_ruang, ST_X(coordinate) AS lat, ST_Y(coordinate) AS lng FROM cafe";
$cafes = send_query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <style>
        body { background-color: #f8f9fc; color: #495057; }
        .navbar { background-color: #6c757d; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); }
        .navbar-brand, .navbar .nav-link { color: #fff; }
        #map { height: 450px; border-radius: 8px; margin-top: 20px; position: relative; }
        .card { transition: transform 0.2s; margin-bottom: 20px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
        .card:hover { transform: scale(1.02); }
        .floating-btn { 
            background-color: #38c172; 
            color: white; 
            border-radius: 4px; 
            padding: 10px 20px; 
            text-align: center; 
            display: inline-block; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); 
            font-size: 16px; 
        }
        .footer-text { text-align: center; color: #6c757d; margin-top: 20px; }
        .total-cafes { display: flex; align-items: center; }
        .total-cafes i { margin-right: 8px; }
        .total-cafes-button {
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 38px; 
            min-width: 200px; 
            background-color: #6c757d; 
            color: white; 
            border-radius: 4px; 
            padding: 5px; 
        }
        /* Custom styles for the search box */
        .search-box {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            width: 250px; /* You can adjust the width here */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <a class="navbar-brand" href="#"><i class="fas fa-coffee"></i> Cafe_Skd</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><span class="nav-link"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['username']); ?></span></li>
            <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= $error_message ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"><?= $success_message ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 text-center">Peta Lokasi Cafe</h2>
            <div id="map">
                <!-- Search Box -->
                <div class="search-box">
                    <div class="input-group">
                        <input type="text" id="searchBox" class="form-control" placeholder="Cari Cafe..." aria-label="Search" aria-describedby="basic-addon1">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4 mb-3 total-cafes">
            <div class="total-cafes-button">
                <i class="fas fa-coffee fa-lg"></i>
                <span>Total Cafe: <?= $cafe_count; ?></span>
            </div>
        </div>

        <div class="col-md-4 mb-3 text-center">
            <!-- This column can be left empty for layout consistency -->
        </div>

        <div class="col-md-4 mb-3 text-right">
            <a href="create.html" class="floating-btn" title="Add Cafe"><i class="fas fa-plus"></i> Tambah Cafe</a>
        </div>
    </div>

    <div class="row" id="cafeList">
        <?php foreach ($cafes as $cafe): ?>
            <div class="col-md-4 cafe-item">
                <div class="card">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($cafe['name']); ?></h5>
                        <p><?= htmlspecialchars($cafe['address']); ?></p>
                        <p><?= htmlspecialchars($cafe['no_hp']); ?></p>
                        <p><?= htmlspecialchars($cafe['kategori_ruang']); ?></p>
                        <a href="update.php?id=<?= $cafe['id']; ?>" class="btn btn-info btn-sm" title="Update Cafe"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $cafe['id']; ?>)" title="Delete Cafe"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p class="footer-text">© 2024 Cafe_Skd. All rights reserved.</p>
</div>

<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus cafe ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    var map = L.map('map').setView([0.01645239124004665, 110.88843126442208], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 17 }).addTo(map);

    var cafes = <?php echo json_encode($cafes); ?>;
    cafes.forEach(function(cafe) {
        L.marker([cafe.lat, cafe.lng]).addTo(map)
            .bindPopup("<strong>" + cafe.name + "</strong><br>" + cafe.address);
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let deleteId;

    function openDeleteModal(id) {
        deleteId = id;
        $('#deleteModal').modal('show');
    }

    $('#confirmDeleteButton').click(function() {
        window.location.href = '?delete_id=' + deleteId;
    });

    $(document).ready(function() {
        // Auto close alerts
        setTimeout(function() {
            $(".alert").alert('close');
        }, 3000);

        // Search functionality
        $('#searchBox').on('keyup', function() {
            var searchValue = $(this).val().toLowerCase();
            $('.cafe-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1)
            });
        });
    });
</script>
</body>
</html>
