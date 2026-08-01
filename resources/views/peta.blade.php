<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring AIS - Proyek Magang</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
        /* Peta akan memenuhi seluruh layar */
        #map { height: 100vh; width: 100%; }
        .title-box {
            position: absolute; top: 10px; left: 50px; z-index: 1000;
            background: white; padding: 10px 20px; border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    <div class="title-box">
        <h3>Live AIS Monitoring (MongoDB Atlas)</h3>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // 1. Inisialisasi Peta (Titik tengah diarahkan ke area laut Indonesia)
        var map = L.map('map').setView([-6.2, 106.8], 5);

        // 2. Memasang Base Map dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // 3. Menarik data kapal dari Controller Laravel (Diubah ke format JSON)
        var dataKapal = @json($kapal_aktif);

        // 4. Melakukan Looping untuk setiap kapal dan menaruh titik di peta
        dataKapal.forEach(function(kapal) {
            // Memastikan kapal memiliki data lokasi (GeoJSON Point)
            if(kapal.location && kapal.location.coordinates) {
                // GeoJSON formatnya [longitude, latitude], tapi Leaflet butuh [latitude, longitude]
                var lon = kapal.location.coordinates[0];
                var lat = kapal.location.coordinates[1];

                // Menyiapkan teks yang muncul saat titik kapal di-klik
                var popupText = "<b>MMSI:</b> " + kapal.source_mmsi + "<br>" +
                                "<b>Pesan:</b> " + kapal.message_name + "<br>" +
                                "<b>Kecepatan:</b> " + (kapal.speed_over_ground || 0) + " knots";

                // Memasang marker ke peta
                L.marker([lat, lon]).addTo(map).bindPopup(popupText);
            }
        });
    </script>
</body>
</html>
