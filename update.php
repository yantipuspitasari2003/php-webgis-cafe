<?php 
$id = $_GET['id'];

// Mengambil semua data yang dikirim dari Update.php
$query = "
    SELECT
    name, address, no_hp, kategori_ruang, ST_X(coordinate) AS lat, ST_Y(coordinate) AS lng
    FROM cafe
    WHERE id = $id    
";

$connection = mysqli_connect(
    "localhost",
    "root",
    "yanti123",
    "cafe_skd"
);

function send_query($query) {
    global $connection;
    $result = mysqli_query($connection, $query);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
$cafe = send_query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Cafe</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

    <style>
        body {
            background-image: url('gambar_cafes.jpg'); /* Background image */
            background-size: cover; /* Ensures the background covers the entire page */
            background-position: center; /* Center the background image */
            min-height: 100vh; /* Full viewport height */
            display: flex; /* Flexbox for centering */
            align-items: center; /* Center content vertically */
        }
        .container {
            margin-top: 40px;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.8); /* Slightly transparent white background */
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .card {
            border-radius: 12px;
        }
        .card-header {
            background-color: #5a6268;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 1rem 1.5rem;
            text-align: center;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
        }
        #map {
            height: 500px;
            border-radius: 8px;
        }
        .btn-primary {
            border-radius: 8px;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #4e555b;
            border-color: #4e555b;
        }
        .instructions {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .btn-danger {
            padding: 5px 10px; /* Smaller padding for the button */
            font-size: 0.85rem; /* Smaller font size */
            border-radius: 8px; /* Consistent border radius */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            UPDATE DATA CAFE
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Form Section -->
                <div class="col-md-4">
                    <form action="process-update.php" method="post">
                        <input type="hidden" name="id" value="<?= $id; ?>">

                        <div class="form-group">
                            <label for="name">Nama Cafe</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= $cafe[0]['name']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Alamat Cafe</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required><?= $cafe[0]['address']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="no_hp">No Hp</label>
                            <input type="number" class="form-control" name="no_hp" id="no_hp" rows="2" value="<?= $cafe[0]['no_hp']; ?>" required></input>
                        </div>

                        <div class="form-group">
                            <label for="kategori">Kategori Ruang</label>
                            <select name="kategori" id="kategori" class="form-control" required>
                                <option value="indoor" <?= $cafe[0]['kategori_ruang'] == 'indoor' ? 'selected' : ''; ?>>Indoor</option>
                                <option value="outdoor" <?= $cafe[0]['kategori_ruang'] == 'outdoor' ? 'selected' : ''; ?>>Outdoor</option>
                                <option value="dll" <?= $cafe[0]['kategori_ruang'] == 'dll' ? 'selected' : ''; ?>>DLL</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="latitude">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="<?= $cafe[0]['lat']; ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="longitude">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="<?= $cafe[0]['lng']; ?>" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Update</button>
                    </form>
                    <p class="instructions">* Klik lokasi pada peta untuk mengisi latitude dan longitude.</p>
                    
                    <!-- Button to return to Dashboard -->
                    <a href="dashboard.php" class="btn btn-danger btn-block mt-3">Close</a>
                </div>

                <!-- Map Section -->
                <div class="col-md-8">
                    <div id="map"></div> 
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
    var map = L.map('map').setView([<?= $cafe[0]['lat']; ?>, <?= $cafe[0]['lng']; ?>], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 17 }).addTo(map);

    var lat = document.getElementById('latitude');
    var lng = document.getElementById('longitude');
    var marker = L.marker([<?= $cafe[0]['lat']; ?>, <?= $cafe[0]['lng']; ?>]).addTo(map);

    map.on('click', function(e) {
        if (marker) { map.removeLayer(marker); }
        marker = L.marker(e.latlng).addTo(map);

        lat.value = e.latlng.lat;
        lng.value = e.latlng.lng;
    });
</script>

<!-- Bootstrap JS and dependencies (Optional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
