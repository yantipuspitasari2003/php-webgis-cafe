<?php
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

$query = "SELECT id, name, address, ST_X(coordinate) AS lat, ST_Y(coordinate) AS lng FROM cafe";
$cafes = send_query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Cafe</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
    crossorigin=""/>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
    crossorigin=""></script>

    <!-- Custom fonts from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* General styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        h2 {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        /* Container and layout */
        .container {
            margin-top: 30px;
        }

        /* cafe Information Cards */
        .cafe-info {
            background-color: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cafe-info:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        .cafe-info strong {
            font-size: 1.4rem;
            font-weight: 500;
            color: #007bff;
        }

        .cafe-info small {
            font-size: 0.9rem;
            color: #777;
        }

        /* Buttons with rounded corners and hover effects */
        .btn-sm {
            border-radius: 20px;
            font-size: 0.9rem;
            padding: 5px 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .btn-primary.btn-sm:hover {
            background-color: #0062cc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-danger.btn-sm:hover {
            background-color: #d9534f;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-success {
            border-radius: 25px;
            padding: 10px 20px;
            width: 100%;
        }

        .btn-success:hover {
            background-color: #28a745;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Map styles */
        #map {
            height: 500px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Card for the "Add New" button */
        .add-btn {
            margin-top: 40px;
            text-align: center;
        }

    </style>
</head>
<body>

    <div class="container">
        <div class="row">
            <!-- cafe Information on the left -->
            <div class="col-md-4">
                <h2>Data Cafe</h2>

                <?php foreach($cafes as $cafe): ?>
                    <div class="cafe-info">
                        <strong><?= $cafe['name']; ?></strong><br>
                        <small><?= $cafe['address']; ?></small><br>

                        <!-- Update and Delete buttons -->
                        <div class="mt-3">
                            <a href="update.php?id=<?= $cafe['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                            <a href="delete.php?id=<?= $cafe['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Add new cafe button -->
                <div class="add-btn">
                    <a href="create.html" class="btn btn-success">Tambahkan Data</a>
                </div>
            </div>

            <!-- Map on the right -->
            <div class="col-md-8">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map').setView([0.01645239124004665, 110.88843126442208], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 17,
        }).addTo(map);

        // Pass PHP cafe data to JavaScript
        var cafes = <?php echo json_encode($cafes); ?>;
        cafes.forEach(function(row) {
            L.marker([row.lat, row.lng]).addTo(map)
                .bindPopup("<strong>" + row.name + "</strong><br>" + row.address);
        });
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
