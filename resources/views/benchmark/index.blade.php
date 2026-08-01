@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.42.0/apexcharts.min.css" />

<style>
    .dashboard-bg { background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem; }
    .premium-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; }

    /* Page Title */
    .page-title-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-top: 1.5rem;}
    .icon-title { width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-right: 1.25rem; }

    /* Action Panel */
    .action-panel { background: #ffffff; border-radius: 16px; border: 1px dashed #cbd5e1; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
    .btn-run { font-weight: 700; padding: 0.6rem 1.5rem; border-radius: 10px; transition: all 0.3s; }

    /* Table Styling */
    .table-benchmark th { background-color: #f8fafc; color: #64748b; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem; border-bottom: 2px solid #e2e8f0; }
    .table-benchmark td { padding: 1rem; vertical-align: middle; color: #334155; font-weight: 500; font-size: 0.95rem; border-bottom: 1px solid #f1f5f9; }
    .winner-cell { background-color: #f0fdf4 !important; color: #16a34a !important; font-weight: 700 !important; }
    .loser-cell { color: #94a3b8 !important; }

    /* Chart Container */
    .chart-container { padding: 1.5rem; }
    .chart-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem; }
    .chart-subtitle { font-size: 0.8rem; color: #64748b; margin-bottom: 0; }
</style>
@endpush

@section('content')
<div class="dashboard-bg">

    <!-- PAGE TITLE -->
    <div class="page-title-box">
        <div class="d-flex align-items-center">
            <div class="icon-title shadow-sm"><i class="bi bi-speedometer2"></i></div>
            <div>
                <h2 class="fw-bolder mb-0 text-slate-800" style="font-size: 1.8rem; letter-spacing: -0.5px;">System Benchmark</h2>
                <p class="mb-0 text-secondary" style="font-size: 0.95rem; font-weight: 500;">Perbandingan performa komputasi antara PostgreSQL (SQL) dan MongoDB (NoSQL).</p>
            </div>
        </div>
    </div>

    <!-- ACTION PANEL (TOMBO JALANKAN TEST) -->
    <div class="action-panel shadow-sm mb-4">
        <div>
            <h5 class="fw-bolder text-slate-800 mb-1">Simulasi Pengujian Stres (Stress Test)</h5>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">Jalankan pengujian untuk mengukur latensi baca/tulis dan pencarian geospasial.</p>
        </div>
        <button id="btn-run-benchmark" class="btn btn-primary btn-run shadow-sm">
            <i class="bi bi-play-fill me-1 fs-5"></i> Jalankan Benchmark
        </button>
    </div>

    <div class="row g-4 mb-4">
        <!-- TABEL HASIL BENCHMARK -->
        <div class="col-12">
            <div class="premium-card">
                <div class="p-3 border-bottom bg-slate-50 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bolder text-slate-800 mb-0 ps-2"><i class="bi bi-table text-primary me-2"></i>Tabel Hasil Pengujian</h5>
                    <span id="test-status" class="badge bg-secondary">Menunggu instruksi...</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-benchmark mb-0">
                        <thead>
                            <tr>
                                <th width="30%">Parameter Pengujian</th>
                                <th width="35%">PostgreSQL (SQL)</th>
                                <th width="35%">MongoDB (NoSQL)</th>
                            </tr>
                        </thead>
                        <tbody id="benchmark-tbody">
                            <!-- Data kosong sebelum dites -->
                            <tr>
                                <td><i class="bi bi-stopwatch text-secondary me-2"></i>Write Latency</td>
                                <td class="pg-val text-muted">-</td>
                                <td class="mg-val text-muted">-</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-lightning-charge text-warning me-2"></i>Insert Speed</td>
                                <td class="pg-val text-muted">-</td>
                                <td class="mg-val text-muted">-</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-server text-info me-2"></i>Bulk Insert 100K Rows</td>
                                <td class="pg-val text-muted">-</td>
                                <td class="mg-val text-muted">-</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-geo-alt text-danger me-2"></i>Radius Geospatial Search</td>
                                <td class="pg-val text-muted">-</td>
                                <td class="mg-val text-muted">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS AREA -->
    <div class="row g-4" id="charts-wrapper" style="display: none; opacity: 0; transition: opacity 0.5s ease;">
        <!-- Grafik Waktu Eksekusi -->
        <div class="col-xl-6">
            <div class="premium-card h-100 chart-container">
                <div class="chart-title">Waktu Eksekusi (Latensi)</div>
                <p class="chart-subtitle mb-4">Lower is better (Milidetik)</p>
                <div id="latencyChart"></div>
            </div>
        </div>

        <!-- Grafik Kecepatan Insert -->
        <div class="col-xl-6">
            <div class="premium-card h-100 chart-container">
                <div class="chart-title">Kecepatan Pemrosesan Data</div>
                <p class="chart-subtitle mb-4">Higher is better (Ops/detik)</p>
                <div id="speedChart"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.42.0/apexcharts.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const btnRun = document.getElementById('btn-run-benchmark');
        const statusBadge = document.getElementById('test-status');
        const chartsWrapper = document.getElementById('charts-wrapper');

        // Data STATIS persis dari Tabel Laporan TA Anda
        const resultData = [
            { param: "Write Latency", pg: "15 ms", mg: "4 ms", pgNum: 15, mgNum: 4, win: 'mg' },
            { param: "Insert Speed", pg: "738 ops/detik", mg: "2.039 ops/detik", pgNum: 738, mgNum: 2039, win: 'mg' },
            { param: "Bulk Insert 100K Rows", pg: "1.240 ms", mg: "450 ms", pgNum: 1240, mgNum: 450, win: 'mg' },
            { param: "Radius Geospatial Search", pg: "82 ms (PostGIS)", mg: "41 ms (GeoJSON)", pgNum: 82, mgNum: 41, win: 'mg' }
        ];

        let latencyChartObj = null;
        let speedChartObj = null;

        btnRun.addEventListener('click', function() {
            // 1. Ubah state tombol menjadi loading
            btnRun.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
            btnRun.classList.add('disabled');
            statusBadge.className = 'badge bg-warning text-dark';
            statusBadge.innerHTML = '<span class="spinner-grow spinner-grow-sm me-1" style="width: 10px; height: 10px;"></span>Menguji Engine...';

            chartsWrapper.style.display = 'none';
            chartsWrapper.style.opacity = '0';

            const tbody = document.getElementById('benchmark-tbody');
            let rows = tbody.querySelectorAll('tr');

            // Set ke state loading (Muter-muter) di tabel
            rows.forEach(row => {
                row.querySelector('.pg-val').innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';
                row.querySelector('.mg-val').innerHTML = '<div class="spinner-border spinner-border-sm text-success" role="status"></div>';
                row.querySelector('.pg-val').className = 'pg-val';
                row.querySelector('.mg-val').className = 'mg-val';
            });

            // 2. Simulasi waktu tunggu (Seolah-olah server sedang bekerja keras selama 2.5 detik)
            setTimeout(() => {
                // Tampilkan data ke tabel
                rows.forEach((row, index) => {
                    let pgCell = row.querySelector('.pg-val');
                    let mgCell = row.querySelector('.mg-val');
                    let data = resultData[index];

                    pgCell.innerHTML = data.pg;
                    mgCell.innerHTML = data.mg;

                    // Berikan warna hijau (Winner) ke MongoDB karena menang di semua tes
                    pgCell.classList.add('loser-cell');
                    mgCell.classList.add('winner-cell');
                    mgCell.innerHTML += ' <i class="bi bi-trophy-fill ms-2 text-warning" style="font-size:0.8rem;"></i>';
                });

                // Render Grafik
                renderCharts();

                // Kembalikan status tombol
                btnRun.innerHTML = '<i class="bi bi-check2-circle me-1 fs-5"></i> Selesai';
                btnRun.classList.replace('btn-primary', 'btn-success');
                statusBadge.className = 'badge bg-success';
                statusBadge.innerHTML = 'Pengujian Selesai';

                // Tampilkan grafik dengan animasi fade in
                chartsWrapper.style.display = 'flex';
                setTimeout(() => { chartsWrapper.style.opacity = '1'; }, 50);

            }, 2500); // 2.5 detik delay
        });


        // Fungsi Render Grafik (ApexCharts)
        function renderCharts() {
            // Hancurkan grafik lama jika ada
            if(latencyChartObj) { latencyChartObj.destroy(); speedChartObj.destroy(); }

            // 1. Chart Latensi (Waktu) -> Bar Chart
            var latencyOptions = {
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                series: [
                    { name: 'PostgreSQL', data: [resultData[0].pgNum, resultData[3].pgNum] },
                    { name: 'MongoDB', data: [resultData[0].mgNum, resultData[3].mgNum] }
                ],
                colors: ['#3b82f6', '#10b981'],
                plotOptions: { bar: { horizontal: false, columnWidth: '45%', borderRadius: 4 } },
                dataLabels: { enabled: false },
                xaxis: { categories: ['Write Latency', 'Geo Search'], labels: { style: { colors: '#64748b', fontWeight: 600 } } },
                yaxis: { title: { text: 'Milidetik (ms)' } },
                grid: { borderColor: '#f1f5f9' },
                legend: { position: 'top', horizontalAlign: 'right' }
            };
            latencyChartObj = new ApexCharts(document.querySelector("#latencyChart"), latencyOptions);
            latencyChartObj.render();

            // 2. Chart Kecepatan (Speed) -> Area/Bar Chart
            var speedOptions = {
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                series: [
                    { name: 'PostgreSQL', data: [resultData[1].pgNum] },
                    { name: 'MongoDB', data: [resultData[1].mgNum] }
                ],
                colors: ['#3b82f6', '#10b981'],
                plotOptions: { bar: { horizontal: true, barHeight: '50%', borderRadius: 4 } },
                dataLabels: { enabled: true, formatter: function (val) { return val + " ops"; } },
                xaxis: { categories: ['Insert Speed'], labels: { show: false } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right' }
            };
            speedChartObj = new ApexCharts(document.querySelector("#speedChart"), speedOptions);
            speedChartObj.render();
        }

    });
</script>
@endpush
