@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.42.0/apexcharts.min.css" />

<style>
    /* Styling Premium & Modern */
    .dashboard-bg { background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem; }
    .premium-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; transition: all 0.3s ease; }

    /* Page Title Area */
    .page-title-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-top: 1.5rem;}
    .icon-title { width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-right: 1.25rem; }

    /* System Status Badge */
    .status-box { background: #0f172a; border-radius: 10px; padding: 0.6rem 1.25rem; display: flex; align-items: center; gap: 0.85rem; border: 1px solid #334155; }
    .status-icon { color: #10b981; font-size: 1.3rem; }
    .status-text-wrap { display: flex; flex-direction: column; }
    .status-label { font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.5px; }
    .status-value { font-size: 0.85rem; color: #10b981; font-weight: 700; letter-spacing: 1px; display: flex; align-items: center; }
    .live-dot { width: 6px; height: 6px; border-radius: 50%; background-color: #10b981; margin-right: 6px; box-shadow: 0 0 8px #10b981; animation: pulse-dot 1.5s infinite; }

    /* Metrik Card - Desain Asli */
    .metric-card { background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; height: 100%; transition: transform 0.2s; }
    .metric-title { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
    .metric-value { font-size: 1.8rem; font-weight: 800; color: #0f172a; line-height: 1; font-family: 'Consolas', monospace; }
    .metric-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }

    /* Efek Klik Interaktif pada Card */
    a.metric-card-clickable { text-decoration: none; color: inherit; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
    a.metric-card-clickable:hover { transform: translateY(-5px); box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important; border-color: #38bdf8; }

    /* Chart Containers */
    .chart-container { padding: 1.5rem; }
    .chart-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
    .chart-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem; }
    .chart-subtitle { font-size: 0.8rem; color: #64748b; margin-bottom: 0; }

    /* Tombol CSV */
    .btn-csv { background: #ffffff; border: 1px solid #e2e8f0; color: #64748b; font-size: 0.8rem; font-weight: 600; padding: 0.4rem 0.85rem; border-radius: 6px; transition: all 0.2s; cursor: pointer; display: flex; align-items: center;}
    .btn-csv:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

    @keyframes pulse-dot { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.5); } 100% { opacity: 1; transform: scale(1); } }
    .value-update-subtle { color: #3b82f6 !important; transition: color 0.3s ease; }
</style>
@endpush

@section('content')
<div class="dashboard-bg">

    <div class="page-title-box">
        <div class="d-flex align-items-center">
            <div class="icon-title shadow-sm"><i class="bi bi-layout-sidebar-inset"></i></div>
            <div>
                <h2 class="fw-bolder mb-0 text-slate-800" style="font-size: 1.8rem; letter-spacing: -0.5px;">Live Dashboard</h2>
                <p class="mb-0 text-secondary" style="font-size: 0.95rem; font-weight: 500;">Ringkasan performa sistem, benchmark database, dan metrik AIS.</p>
            </div>
        </div>

        <div class="status-box shadow-sm">
            <div class="status-icon"><i class="bi bi-server"></i></div>
            <div class="status-text-wrap">
                <span class="status-label">SYSTEM STATUS</span>
                <span class="status-value"><div class="live-dot"></div> ONLINE</span>
            </div>
        </div>
    </div>

    <!-- 4 KARTU METRIK -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="#modalPostgres" data-bs-toggle="modal" class="metric-card metric-card-clickable shadow-sm">
                <div>
                    <div class="metric-title">Waktu PostgreSQL</div>
                    <div class="metric-value text-primary" id="card-pg-time">45<span class="fs-6 text-secondary fw-normal ms-1">ms</span></div>
                </div>
                <div class="metric-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-database"></i></div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="#modalMongo" data-bs-toggle="modal" class="metric-card metric-card-clickable shadow-sm">
                <div>
                    <div class="metric-title">Waktu MongoDB</div>
                    <div class="metric-value text-success" id="card-mg-time">12<span class="fs-6 text-secondary fw-normal ms-1">ms</span></div>
                </div>
                <div class="metric-icon bg-success bg-opacity-10 text-success"><i class="bi bi-database-fill"></i></div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="#modalParse" data-bs-toggle="modal" class="metric-card metric-card-clickable shadow-sm">
                <div>
                    <div class="metric-title">Total Ter-Parse</div>
                    <div class="metric-value text-slate-800" id="card-total-parse">100<span class="fs-6 text-secondary fw-normal ms-1">unit</span></div>
                </div>
                <div class="metric-icon bg-info bg-opacity-10 text-info"><i class="bi bi-geo-alt-fill"></i></div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="#modalDecoder" data-bs-toggle="modal" class="metric-card metric-card-clickable shadow-sm">
                <div>
                    <div class="metric-title">Status Decoder</div>
                    <div class="metric-value text-warning d-flex align-items-center" style="font-size: 1.5rem;">
                        <div class="live-dot bg-warning shadow-none me-2" style="width:10px; height:10px;"></div> Active
                    </div>
                </div>
                <div class="metric-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-cpu"></i></div>
            </a>
        </div>
    </div>

    <!-- CHARTS AREA -->
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="premium-card h-100 chart-container">
                <div class="chart-header">
                    <div>
                        <h4 class="chart-title">Volume Lalu Lintas Data AIS</h4>
                        <p class="chart-subtitle">Jumlah pesan mentah yang diterima per jam</p>
                    </div>
                    <button class="btn-csv shadow-sm" id="btn-download-csv">
                        <i class="bi bi-download me-2"></i> CSV Unduh
                    </button>
                </div>
                <div id="trafficChart" style="min-height: 380px;"></div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card h-100 chart-container">
                <div class="chart-header">
                    <div>
                        <h4 class="chart-title">Benchmark Waktu Baca/Tulis</h4>
                        <p class="chart-subtitle">PostgreSQL vs MongoDB (Lower is better)</p>
                    </div>
                </div>
                <div id="benchmarkChart" style="min-height: 380px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL POPUP INTERAKTIF -->
<!-- ========================================== -->

<!-- Modal 1: PostgreSQL Detail -->
<div class="modal fade" id="modalPostgres" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-database me-2"></i>Detail Performa PostgreSQL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">Statistik koneksi relasional untuk rekapitulasi data historis dan tabel log sistem.</p>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Status Koneksi</span>
                        <span class="badge bg-success">Connected (Pool Active)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Rata-rata Waktu Query</span>
                        <!-- ID DITAMBAHKAN DI SINI -->
                        <strong class="font-monospace text-primary" id="modal-pg-val">45 ms</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: MongoDB Detail -->
<div class="modal fade" id="modalMongo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-success text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-database-fill me-2"></i>Detail Performa MongoDB Atlas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">Statistik penyimpanan NoSQL untuk penampungan data mentah dan geospasial real-time.</p>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Database Aktif</span>
                        <strong class="font-monospace">ais_no_sql</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Waktu Respon (Read/Write)</span>
                        <!-- ID DITAMBAHKAN DI SINI -->
                        <strong class="font-monospace text-success" id="modal-mg-val">12 ms</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Total Ter-Parse Detail -->
<div class="modal fade" id="modalParse" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-info text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt-fill me-2"></i>Ringkasan Entitas Ter-Parse</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">Jumlah total pesan AIS yang berhasil didekode dan disinkronkan ke dalam peta geospasial.</p>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Total Unit Aktif Hari Ini</span>
                        <!-- ID DITAMBAHKAN DI SINI -->
                        <strong class="font-monospace text-info" id="modal-parse-val">100 Unit</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Akurasi Koordinat</span>
                        <span class="badge bg-success">High Accuracy</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Status Decoder Detail -->
<div class="modal fade" id="modalDecoder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-warning text-dark" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-cpu me-2"></i>Status Engine Decoder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">Informasi pemrosesan latar belakang untuk penerjemahan kalimat NMEA/AIS mentah.</p>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Status Mesin</span>
                        <span class="badge bg-warning text-dark">Active & Listening</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Error Rate</span>
                        <strong class="text-success">0.00% (Normal)</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.42.0/apexcharts.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        function formatNumber(num) { return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }

        // --- 1. SETUP CHART LALU LINTAS ---
        const timeCategories = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00'];
        const trafficData = [3100, 4000, 2800, 5100, 4200, 6900, 5100];

        var trafficOptions = {
            chart: { type: 'area', height: 350, toolbar: { show: false } },
            series: [{ name: 'Volume (msg)', data: trafficData }],
            colors: ['#0ea5e9'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.02, stops: [0, 90, 100] } },
            xaxis: { categories: timeCategories, labels: { style: { colors: '#64748b', fontWeight: 500 } } },
            yaxis: { min: 2000, max: 7000, labels: { formatter: function(val) { return val + " msg"; }, style: { colors: '#64748b' } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
        };
        new ApexCharts(document.querySelector("#trafficChart"), trafficOptions).render();


        // --- 2. SETUP CHART BENCHMARK ---
        const batchLabels = ['Batch 1', 'Batch 2', 'Batch 3', 'Batch 4'];
        const pgBatchData = [45, 52, 48, 50];
        const mgBatchData = [12, 14, 11, 13];

        var benchmarkOptions = {
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            series: [{ name: 'PostgreSQL', data: pgBatchData }, { name: 'MongoDB', data: mgBatchData }],
            colors: ['#3b82f6', '#10b981'],
            plotOptions: { bar: { horizontal: false, columnWidth: '40%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            xaxis: { categories: batchLabels, labels: { style: { colors: '#64748b', fontWeight: 600 } } },
            yaxis: { max: 60, title: { text: 'Milidetik (ms)' }, labels: { style: { colors: '#64748b' } } },
            grid: { borderColor: '#f1f5f9' },
            legend: { position: 'top', horizontalAlign: 'right' }
        };
        new ApexCharts(document.querySelector("#benchmarkChart"), benchmarkOptions).render();


        // --- 3. LOGIKA LIVE METRIK KARTU & SINKRONISASI MODAL ---
        // Anda menggunakan data aktual 100 yang berhasil ditembus sebelumnya dari MongoDB,
        // namun untuk tampilan dashboard kita simulasikan berjalan progresif
        let totalParsed = 100;

        setInterval(function() {
            let pgLat = Math.floor(Math.random() * (50 - 42) + 42);
            let mgLat = Math.floor(Math.random() * (15 - 10) + 10);
            totalParsed += Math.floor(Math.random() * 3);

            // Update Card di Halaman Depan
            let elPg = document.getElementById('card-pg-time');
            let elMg = document.getElementById('card-mg-time');
            let elParse = document.getElementById('card-total-parse');

            elPg.innerHTML = pgLat + '<span class="fs-6 text-secondary fw-normal ms-1">ms</span>';
            elMg.innerHTML = mgLat + '<span class="fs-6 text-secondary fw-normal ms-1">ms</span>';
            elParse.innerHTML = formatNumber(totalParsed) + '<span class="fs-6 text-secondary fw-normal ms-1">unit</span>';

            elPg.classList.add('value-update-subtle'); setTimeout(() => elPg.classList.remove('value-update-subtle'), 300);

            // Update Teks di Dalam Modal Pop-up (Menyamakan Angka Secara Real-Time)
            document.getElementById('modal-pg-val').innerText = pgLat + ' ms';
            document.getElementById('modal-mg-val').innerText = mgLat + ' ms';
            document.getElementById('modal-parse-val').innerText = formatNumber(totalParsed) + ' Unit';

        }, 3000);

    });
</script>
@endpush
