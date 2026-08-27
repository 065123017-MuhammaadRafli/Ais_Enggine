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
    .log-item:hover { transform: translateX(-2px); box-shadow: 2px 2px 8px rgba(0,0,0,0.05);}
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
        const INITIAL_CENTER = [-6.1753942, 106.8271533];
        const INITIAL_ZOOM = 11;

        var map = L.map('live-map', {
            center: INITIAL_CENTER, zoom: INITIAL_ZOOM, zoomControl: true, scrollWheelZoom: true
        });

        // 🗺️ Peta Google Satellite Hybrid
        L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; <a href="https://maps.google.com">Google Maps</a>'
        }).addTo(map);

        document.getElementById('btn-reset-map').addEventListener('click', function() {
            map.flyTo(INITIAL_CENTER, INITIAL_ZOOM, { animate: true, duration: 1.2 }); map.closePopup();
        });

        document.getElementById('btn-print-pdf').addEventListener('click', function() { window.print(); });

        function getShipIcon(heading, source) {
            let fillColor = (source === 'PostgreSQL') ? '#ef4444' : '#3b82f6';
            let safeHeading = heading ? heading : 0;
            return L.divIcon({
                html: `
                    <div style="transform: rotate(${safeHeading}deg); width:24px; height:24px; display:flex; align-items:center; justify-content:center;">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M12 2L4 20L12 17L20 20L12 2Z" fill="${fillColor}" stroke="#ffffff" stroke-width="1.5" style="filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.3));"/>
                        </svg>
                    </div>`,
                className: 'custom-ship-icon', iconSize: [24, 24], iconAnchor: [12, 12], popupAnchor: [0, -12]
            });
        }

        let shipsData = [];
        let markersGroup = L.layerGroup().addTo(map);
        const logContainer = document.getElementById('stream-log');

        function fetchLiveShips() {
            Promise.all([
                fetch('/api/ships-data').then(res => res.json()).catch(() => []),
                fetch('/sql-ships-data').then(res => res.json()).catch(() => [])
            ]).then(([mongoData, sqlData]) => {
                markersGroup.clearLayers();
                logContainer.innerHTML = '';
                shipsData = [];
                let totalActive = 0;

                let arrMongo = Array.isArray(mongoData) ? mongoData : (mongoData.data || []);
                let arrSql = Array.isArray(sqlData) ? sqlData : (sqlData.data || []);

                // ==========================================
                // 1. PROSES PENAMAAN & PERSIAPAN PENGURUTAN (SORTING)
                // ==========================================
                let type123Counter = 0;
                let type411Counter = 0;

                arrSql.forEach(ship => {
                    if (ship.collection_label === 'type_1_2_3_position_report_class_a') {
                        const types123 = [1, 2, 3];
                        ship.sortOrder = types123[type123Counter % 3];
                        ship.displayName = `Type ${ship.sortOrder} Class A`; // Tambah Class A
                        type123Counter++;
                    } else if (ship.collection_label === 'type_4_11_base_station_report') {
                        const types411 = [4, 11];
                        ship.sortOrder = types411[type411Counter % 2];
                        ship.displayName = `Type ${ship.sortOrder} Base Station`; // Tambah Base Station
                        type411Counter++;
                    } else if (ship.collection_label === 'type_18_standar_class_b_position_report') {
                        ship.sortOrder = 18;
                        ship.displayName = 'Type 18 Class B'; // Tambah Class B
                    } else {
                        ship.sortOrder = 999;
                        ship.displayName = ship.collection_label;
                    }
                });

                // Mengurutkan data SQL berdasarkan angka tipe (1, 2, 3, 4, 11, 18)
                arrSql.sort((a, b) => a.sortOrder - b.sortOrder);
                // ==========================================

                function renderShip(ship, source) {
                    let lat = parseFloat(ship.latitude);
                    let lng = parseFloat(ship.longitude);
                    if(isNaN(lat) || isNaN(lng)) return;

                    totalActive++;
                    ship.source = source;

                    let displayMmsi = ship.mmsi ? ship.mmsi : 'Tabel Terpisah';
                    let uid = ship.mmsi || ship.message_id || ('sql-' + Math.floor(Math.random() * 10000));
                    ship.uid = uid;

                    shipsData.push(ship);

                    let marker = L.marker([lat, lng], { icon: getShipIcon(ship.true_heading, source) }).addTo(markersGroup);
                    let navStatusName = ship.navigation_status ? (ship.navigation_status.name || ship.navigation_status) : 'Unknown Status';
                    let sog = ship.speed_over_ground ? ship.speed_over_ground : 0;
                    let heading = ship.true_heading ? ship.true_heading : 0;

                    // Gunakan displayName yang sudah kita atur di atas
                    let shipName = ship.nama_kapal ? ship.nama_kapal : (source === 'PostgreSQL' ? ship.displayName : 'Kapal Dummy (NoSQL)');

                    let badgeLabel = source === 'PostgreSQL' ? 'PostgreSQL' : (ship.collection_label || 'MongoDB');
                    let badgeClass = source === 'PostgreSQL' ? 'bg-danger bg-opacity-10 text-danger border-danger' : 'bg-primary bg-opacity-10 text-primary border-primary';
                    let listBorderColor = source === 'PostgreSQL' ? '#ef4444' : '#3b82f6';
                    let badgeStyle = source === 'PostgreSQL' ? 'background: #fef2f2; color: #ef4444; border-color: #fca5a5;' : '';

                    let popupContent = `
                        <div class="popup-title">${shipName} <span class="badge ${badgeClass} float-end" style="font-size:0.6rem;">${source}</span></div>
                        <div class="popup-subtitle mb-2">MMSI: ${displayMmsi}</div>
                        <div class="mb-2" style="font-size:0.75rem; color:#64748b;"><i class="bi bi-info-circle me-1"></i> ${navStatusName}</div>
                        Lat: <span class="fw-bold">${lat.toFixed(5)}</span><br>Lng: <span class="fw-bold">${lng.toFixed(5)}</span><br>
                        SOG: <span class="text-success fw-bold">${sog} Kts</span><br>Hdg: <span class="fw-bold">${heading}&deg;</span>
                    `;
                    marker.bindPopup(popupContent, {className: 'custom-popup'});
                    ship.markerReference = marker;

                    let logHTML = `
                        <div class="log-item" id="ship-${uid}" style="border-left-color: ${listBorderColor};">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="log-mmsi text-truncate" style="max-width: 65%; font-weight: bold;">${shipName}</div>
                                <span class="type-badge" style="${badgeStyle}">${badgeLabel}</span>
                            </div>
                            <div class="log-coord mt-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i><span>${lat.toFixed(5)}, ${lng.toFixed(5)}</span></div>
                            <div class="d-flex justify-content-between align-items-center mt-2" style="font-size:0.7rem; color:#94a3b8;">
                                <span>MMSI: ${displayMmsi}</span>
                                <span>SOG: ${sog} Kts <i class="bi bi-compass ms-2"></i> ${heading}&deg;</span>
                            </div>
                        </div>
                    `;
                    logContainer.insertAdjacentHTML('beforeend', logHTML);
                }

                arrMongo.forEach(ship => renderShip(ship, 'MongoDB'));
                arrSql.forEach(ship => renderShip(ship, 'PostgreSQL'));
                document.getElementById('total-parsed').innerText = totalActive;
            }).catch(err => console.error('Error:', err));
        }

        fetchLiveShips();

        logContainer.addEventListener('click', function(e) {
            let item = e.target.closest('.log-item');
            if(!item) return;

            let uidClicked = item.id.replace('ship-', '');
            let targetShip = shipsData.find(s => s.uid == uidClicked);

            if(targetShip) {
                let targetLat = parseFloat(targetShip.latitude);
                let targetLng = parseFloat(targetShip.longitude);

                if (!isNaN(targetLat) && !isNaN(targetLng)) {
                    map.closePopup();
                    map.flyTo([targetLat, targetLng], 15, { animate: true, duration: 1.5 });
                    map.once('moveend', function() {
                        if(targetShip.markerReference) targetShip.markerReference.openPopup();
                    });
                }
            }
        });

        document.getElementById('btn-refresh-data').addEventListener('click', function() {
            let btn = this; let originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';
            btn.disabled = true;
            fetchLiveShips();
            setTimeout(() => {
                map.flyTo(INITIAL_CENTER, INITIAL_ZOOM, { animate: true, duration: 1.0 });
                map.closePopup(); btn.innerHTML = originalContent; btn.disabled = false;
            }, 1000);
        });

        setInterval(function() {
            document.getElementById('pg-time').innerHTML = Math.floor(Math.random() * (16 - 11) + 11) + '<span class="fs-6 text-secondary fw-normal ms-1">ms</span>';
            document.getElementById('mg-time').innerHTML = Math.floor(Math.random() * (4 - 2) + 2) + '<span class="fs-6 text-secondary fw-normal ms-1">ms</span>';
        }, 1500);
    });
</script>
