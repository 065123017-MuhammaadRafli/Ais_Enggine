@extends('layouts.main')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Styling Premium & Modern */
    .dashboard-bg { background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem; }
    .premium-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.05); overflow: hidden; }

    /* Header Peta */
    .map-header { background: linear-gradient(90deg, #f8fafc 0%, #ffffff 100%); border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }

    /* Live Status Badge */
    .live-badge { display: inline-flex; align-items: center; padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; background-color: #ecfdf5; color: #16a34a; border: 1px solid #bbf7d0; }
    .live-dot { width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e; margin-right: 8px; box-shadow: 0 0 8px #22c55e; animation: pulse-dot 1s infinite; }

    /* Metrik Card */
    .metric-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; }
    .metric-title { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
    .metric-value { font-size: 1.8rem; font-weight: 800; color: #0f172a; line-height: 1; font-family: 'Consolas', monospace; }
    .metric-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }

    /* Peta Container */
    #live-map { height: 550px; width: 100%; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; z-index: 1; }

    /* Log Posisi Terakhir (Sebelah Kanan) */
    .log-container { height: 550px; overflow-y: auto; overflow-x: hidden; padding: 0.5rem; }
    .log-container::-webkit-scrollbar { width: 6px; }
    .log-container::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .log-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .log-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Cursor Pointer untuk list agar bisa diklik */
    .log-item { background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 0.5rem; transition: all 0.3s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.02); cursor: pointer; }
    .log-item:hover { border-left-color: #10b981; background-color: #f0fdf4; transform: translateX(-2px); box-shadow: 2px 2px 8px rgba(0,0,0,0.05);}
    .log-mmsi { font-weight: 800; color: #0f172a; font-family: 'Consolas', monospace; font-size: 0.95rem; }
    .log-coord { font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; font-family: 'Consolas', monospace; }
    .type-badge { font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 6px; background: #eff6ff; color: #2563eb; font-weight: 700; border: 1px solid #bfdbfe; letter-spacing: 0.5px; width: auto; min-width: 65px; text-align: center; }

    /* Tombol Reset View */
    .btn-reset-view { background-color: #ffffff; color: #3b82f6; border: 1px solid #bfdbfe; font-weight: 600; font-size: 0.85rem; padding: 0.4rem 1rem; border-radius: 8px; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .btn-reset-view:hover { background-color: #eff6ff; border-color: #3b82f6; transform: translateY(-1px); }

    /* Pop-up Peta Kustom */
    .custom-popup .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .custom-popup .leaflet-popup-content { font-family: 'Consolas', monospace; font-size: 0.85rem; color: #334155; margin: 13px 16px; }
    .popup-title { font-weight: 800; color: #0f172a; font-size: 1rem; border-bottom: 1px dashed #cbd5e1; padding-bottom: 6px; margin-bottom: 6px; }
    .popup-subtitle { font-size: 0.7rem; color: #64748b; font-weight: 600; }

    @keyframes pulse-dot { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.3); } 100% { opacity: 1; transform: scale(1); } }
    .value-update { background-color: #ecfdf5 !important; border-left-color: #10b981 !important; transition: all 0.2s ease; }
    .text-update { color: #10b981 !important; }

    /* CSS KHUSUS UNTUK CETAK PDF */
    @media print {
        body * { visibility: hidden; }
        .dashboard-bg, .dashboard-bg * { visibility: visible; }
        .dashboard-bg { position: absolute; left: 0; top: 0; width: 100%; background: #ffffff !important; }
        .btn, .sidebar, header { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-bg">
    <!-- HEADER UTAMA -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom pt-3">
        <div class="d-flex align-items-center">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff;">
                <i class="bi bi-globe-americas fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bolder mb-0 text-slate-800" style="font-size: 1.6rem; letter-spacing: -0.5px;">Live Monitoring Peta</h2>
                <p class="mb-0 text-secondary" style="font-size: 0.9rem; font-weight: 500;">Pemantauan entitas AIS terintegrasi Database dengan fitur klik-untuk-fokus (Click-to-Pan).</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button id="btn-print-pdf" class="btn btn-light border shadow-sm fw-medium"><i class="bi bi-printer me-2"></i>Cetak PDF</button>
            <button id="btn-refresh-data" class="btn btn-primary shadow-sm fw-medium"><i class="bi bi-arrow-repeat me-2"></i>Perbarui Data</button>
        </div>
    </div>

    <!-- 4 KARTU METRIK -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div>
                    <div class="metric-title">Waktu PostgreSQL</div>
                    <div class="metric-value text-primary" id="pg-time">14<span class="fs-6 text-secondary fw-normal ms-1">ms</span></div>
                </div>
                <div class="metric-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-server"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div>
                    <div class="metric-title">Waktu MongoDB</div>
                    <div class="metric-value text-success" id="mg-time">3<span class="fs-6 text-secondary fw-normal ms-1">ms</span></div>
                </div>
                <div class="metric-icon bg-success bg-opacity-10 text-success"><i class="bi bi-database"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div>
                    <div class="metric-title">Kapal Aktif (Database)</div>
                    <div class="metric-value text-info" id="total-parsed">0</div>
                </div>
                <div class="metric-icon bg-info bg-opacity-10 text-info"><i class="bi bi-geo-alt"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card border-success border-opacity-50">
                <div>
                    <div class="metric-title">Sistem Tracking</div>
                    <div class="live-badge mt-1 shadow-sm">
                        <div class="live-dot"></div> LIVE SYNC
                    </div>
                </div>
                <div class="metric-icon bg-success bg-opacity-10 text-success"><i class="bi bi-radar"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- PANEL KIRI: PETA GEOSPASIAL -->
        <div class="col-xl-8">
            <div class="premium-card h-100 d-flex flex-column">
                <div class="map-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-map text-primary fs-5 me-2"></i>
                        <h5 class="fw-bolder text-slate-800 mb-0">Peta Navigasi Geospasial</h5>
                        <span class="badge bg-success bg-opacity-10 text-success ms-3 border border-success border-opacity-25" style="font-size: 0.7rem;"><i class="bi bi-record-circle me-1"></i>Live Tracking</span>
                    </div>
                    <button id="btn-reset-map" class="btn-reset-view">
                        <i class="bi bi-arrows-collapse me-1"></i> Reset View
                    </button>
                </div>

                <div class="flex-grow-1 p-0 position-relative">
                    <div id="live-map"></div>
                </div>
            </div>
        </div>

        <!-- PANEL KANAN: DAFTAR KAPAL -->
        <div class="col-xl-4">
            <div class="premium-card h-100 d-flex flex-column">
                <div class="map-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-list-ul text-secondary fs-5 me-2"></i>
                        <h5 class="fw-bolder text-slate-800 mb-0">Daftar Entitas Aktif</h5>
                    </div>
                    <span class="text-secondary" style="font-size: 0.75rem;">Klik untuk fokus</span>
                </div>

                <div class="flex-grow-1 p-3 bg-slate-50">
                    <div class="log-container" id="stream-log">
                        <!-- Data kapal akan dirender di sini -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. INISIALISASI PETA LEAFLET ---
        const INITIAL_CENTER = [-6.1753942, 106.8271533];
        const INITIAL_ZOOM = 11;

        var map = L.map('live-map', {
            center: INITIAL_CENTER,
            zoom: INITIAL_ZOOM,
            zoomControl: true,
            scrollWheelZoom: true
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // --- 2. FITUR TOMBOL RESET & CETAK ---
        document.getElementById('btn-reset-map').addEventListener('click', function() {
            map.flyTo(INITIAL_CENTER, INITIAL_ZOOM, { animate: true, duration: 1.2 });
            map.closePopup();
        });

        document.getElementById('btn-print-pdf').addEventListener('click', function() {
            window.print();
        });

        // --- 3. FUNGSI GENERATE IKON KAPAL ---
        function getShipIcon(heading, typeNum) {
            let fillColor = (typeNum === 4 || typeNum === 21) ? '#64748b' : '#3b82f6';
            let safeHeading = heading ? heading : 0;

            return L.divIcon({
                html: `
                    <div style="transform: rotate(${safeHeading}deg); transform-origin: center; width:24px; height:24px; display:flex; align-items:center; justify-content:center;">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M12 2L4 20L12 17L20 20L12 2Z" fill="${fillColor}" stroke="#ffffff" stroke-width="1.5" style="filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.3));"/>
                        </svg>
                    </div>
                `,
                className: 'custom-ship-icon',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });
        }

        // --- 4. FETCH DATA API (INTEGRASI DATABASE) ---
        let shipsData = [];
        let markersGroup = L.layerGroup().addTo(map);
        const logContainer = document.getElementById('stream-log');

        function fetchLiveShips() {
            fetch('/api/ships-data')
                .then(response => response.json())
                .then(data => {
                    markersGroup.clearLayers();
                    logContainer.innerHTML = '';
                    shipsData = data;

                    let totalActive = 0;

                    data.forEach(ship => {
                        // PERBAIKAN: Ambil latitude dan longitude secara langsung dari level utama
                        let lat = ship.latitude;
                        let lng = ship.longitude;

                        if(!lat || !lng) return;

                        totalActive++;

                        let marker = L.marker([lat, lng], {
                            icon: getShipIcon(ship.true_heading, ship.actual_message_type_id)
                        }).addTo(markersGroup);

                        let navStatusName = ship.navigation_status ? ship.navigation_status.name : 'Unknown Status';
                        let sog = ship.speed_over_ground ? ship.speed_over_ground : 0;
                        let heading = ship.true_heading ? ship.true_heading : 0;
                        let shipName = ship.nama_kapal ? ship.nama_kapal : 'Kapal Unknown';

                        // Pop-up Kustom
                        let popupContent = `
                            <div class="popup-title">${shipName}</div>
                            <div class="popup-subtitle mb-2">MMSI: ${ship.mmsi} &bull; ${ship.message_name || ship.collection_label}</div>
                            <div class="mb-2" style="font-size:0.75rem; color:#64748b;">
                                <i class="bi bi-info-circle me-1"></i> ${navStatusName}
                            </div>
                            Lat: <span class="fw-bold">${lat.toFixed(5)}</span><br>
                            Lng: <span class="fw-bold">${lng.toFixed(5)}</span><br>
                            SOG: <span class="text-success fw-bold">${sog} Kts</span><br>
                            Hdg: <span class="fw-bold">${heading}&deg;</span>
                        `;
                        marker.bindPopup(popupContent, {className: 'custom-popup'});

                        ship.markerReference = marker;

                        // Log Kanan
                        let logHTML = `
                            <div class="log-item" id="ship-${ship.mmsi}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="log-mmsi text-truncate" style="max-width: 70%;">${shipName}</div>
                                    <span class="type-badge">${ship.collection_label}</span>
                                </div>
                                <div class="log-coord mt-1">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <span>${lat.toFixed(5)}, ${lng.toFixed(5)}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2" style="font-size:0.7rem; color:#94a3b8;">
                                    <span>MMSI: ${ship.mmsi}</span>
                                    <span>SOG: ${sog} Kts <i class="bi bi-compass ms-2"></i> ${heading}&deg;</span>
                                </div>
                            </div>
                        `;
                        logContainer.insertAdjacentHTML('beforeend', logHTML);
                    });

                    // Menampilkan jumlah data valid yang berhasil dirender
                    document.getElementById('total-parsed').innerText = totalActive;
                })
                .catch(error => {
                    console.error('Gagal mengambil data dari Database:', error);
                    logContainer.innerHTML = '<div class="text-center p-3 text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill fs-3 d-block mb-2"></i>Gagal memuat data dari Database.</div>';
                });
        }

        fetchLiveShips();

        // --- 5. FITUR CLICK-TO-PAN (DIOPTIMASI) ---
        logContainer.addEventListener('click', function(e) {
            let item = e.target.closest('.log-item');
            if(!item) return;

            let mmsiClicked = parseInt(item.id.replace('ship-', ''));
            let targetShip = shipsData.find(s => s.mmsi === mmsiClicked);

            if(targetShip) {
                // PERBAIKAN: Ambil koordinat target secara langsung juga
                let targetLat = targetShip.latitude;
                let targetLng = targetShip.longitude;

                if (targetLat && targetLng) {
                    map.closePopup();

                    map.flyTo([targetLat, targetLng], 15, {
                        animate: true,
                        duration: 1.5 // Animasi diperhalus
                    });

                    map.once('moveend', function() {
                        if(targetShip.markerReference) {
                            targetShip.markerReference.openPopup();
                        }
                    });
                }
            }
        });

        // --- 6. TOMBOL PERBARUI DATA ---
        document.getElementById('btn-refresh-data').addEventListener('click', function() {
            let btn = this;
            let originalContent = btn.innerHTML;

            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat...';
            btn.disabled = true;

            fetchLiveShips();

            setTimeout(() => {
                map.flyTo(INITIAL_CENTER, INITIAL_ZOOM, { animate: true, duration: 1.0 });
                map.closePopup();
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }, 1000);
        });

        // --- 7. SIMULASI INDIKATOR LATENCY ---
        setInterval(function() {
            document.getElementById('pg-time').innerHTML = Math.floor(Math.random() * (16 - 11) + 11) + '<span class="fs-6 text-secondary fw-normal ms-1">ms</span>';
            document.getElementById('mg-time').innerHTML = Math.floor(Math.random() * (4 - 2) + 2) + '<span class="fs-6 text-secondary fw-normal ms-1">ms</span>';
        }, 1500);

    });
</script>
@endpush
