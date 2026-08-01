@extends('layouts.main')

@push('styles')
<style>
    /* Styling Premium & Modern */
    .dashboard-bg { background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem; }
    .premium-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.05); overflow: hidden; }

    /* Header Database */
    .db-header-pg { background: linear-gradient(90deg, #eff6ff 0%, #ffffff 100%); border-bottom: 1px solid #bfdbfe; padding: 1.25rem 1.5rem; }
    .db-header-mg { background: linear-gradient(90deg, #f0fdf4 0%, #ffffff 100%); border-bottom: 1px solid #bbf7d0; padding: 1.25rem 1.5rem; }

    /* Live Status Badge */
    .live-badge { display: inline-flex; align-items: center; padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; background-color: #ecfdf5; color: #16a34a; border: 1px solid #bbf7d0; }
    .live-dot { width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e; margin-right: 8px; box-shadow: 0 0 8px #22c55e; animation: pulse-dot 1s infinite; }

    /* Metrik Server */
    .metric-box { background: #ffffff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 1rem; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .metric-title { font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.3rem; }
    .metric-value { font-size: 1.6rem; font-weight: 800; color: #0f172a; font-family: 'Consolas', monospace; }

    /* Custom Scrollbar */
    .custom-scroll { overflow-y: auto; overflow-x: hidden; }
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px;}
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Tabel Modern SQL */
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0 4px; }
    .modern-table th { font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; padding: 0 1rem 0.5rem; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; background: #ffffff; z-index: 10;}
    .modern-table td { padding: 0.5rem 1rem; background: #f8fafc; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; font-size: 0.75rem; }
    .modern-table tr td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 8px; border-bottom-left-radius: 8px; font-weight: 600; color: #0f172a; }
    .modern-table tr td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 8px; border-bottom-right-radius: 8px; text-align: right; font-family: 'Consolas', monospace; font-weight: 700; }

    /* Elemen Interaktif MongoDB */
    .mongo-collection-box { background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 1rem; cursor: pointer; transition: all 0.2s ease; user-select: none; }
    .mongo-collection-box:hover { border-color: #22c55e; background: #f0fdf4; }
    .transition-icon { transition: transform 0.3s ease; }
    .icon-rotated { transform: rotate(180deg); }

    /* Grid 2 Kolom untuk Type Mongo */
    .type-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem 1rem; }
    .type-badge { font-size: 0.65rem; padding: 0.25rem 0.5rem; border-radius: 6px; background: #e2e8f0; color: #475569; font-weight: 700; font-family: 'Consolas', monospace; display: inline-block; width: 85px; text-align: center;}

    /* Live Log Stream */
    .live-log-container { background: #0f172a; border-radius: 12px; height: 130px; overflow-y: hidden; padding: 0.75rem 1rem; font-family: 'Consolas', monospace; font-size: 0.75rem; color: #94a3b8; position: relative; }
    .live-log-container::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 30px; background: linear-gradient(to bottom, transparent, #0f172a); }
    .log-entry { margin-bottom: 0.3rem; animation: slideDown 0.3s ease-out forwards; border-bottom: 1px dashed #334155; padding-bottom: 0.3rem;}
    .log-time { color: #64748b; margin-right: 8px; }
    .log-pg { color: #3b82f6; font-weight: bold; }
    .log-mg { color: #22c55e; font-weight: bold; }
    .log-type { color: #fbbf24; }

    @keyframes pulse-dot { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.3); } 100% { opacity: 1; transform: scale(1); } }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .value-update { color: #10b981 !important; transition: color 0.2s ease; }
</style>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<div class="dashboard-bg">
    <!-- HEADER UTAMA -->
    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom pt-3">
        <div class="d-flex align-items-center">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px; background: linear-gradient(135deg, #0f172a 0%, #334155 100%); color: #ffffff;">
                <i class="bi bi-activity fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bolder mb-0 text-slate-800" style="font-size: 1.6rem; letter-spacing: -0.5px;">Live Database Telemetry</h2>
                <p class="mb-0 text-secondary" style="font-size: 0.9rem; font-weight: 500;">Sinkronisasi skema SQL & NoSQL (Data berurutan berdasarkan fungsi decoder.py).</p>
            </div>
        </div>
        <div>
            <div class="live-badge shadow-sm">
                <div class="live-dot"></div> SYNCING SEQUENCE
            </div>
        </div>
    </div>

    <!-- LOG STREAM GLOBAL -->
    <div class="mb-4">
        <div class="d-flex justify-content-between mb-1 px-1">
            <span class="fw-bold text-slate-700" style="font-size: 0.8rem; letter-spacing: 0.5px;">LIVE TRANSACTION LOG (DECODER BATCH SEQUENCE)</span>
            <span class="text-secondary" style="font-size: 0.75rem;"><i class="bi bi-broadcast"></i> Port: 5432 & 27017</span>
        </div>
        <div class="live-log-container shadow-sm" id="global-log"></div>
    </div>

    <div class="row g-4">
        <!-- ==========================================
             PANEL KIRI: POSTGRESQL (SQL AKTUAL)
        =========================================== -->
        <div class="col-xl-6">
            <div class="premium-card h-100 d-flex flex-column">
                <div class="db-header-pg d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/29/Postgresql_elephant.svg" alt="PG" style="height: 32px; margin-right: 15px;">
                        <div>
                            <h5 class="fw-bolder text-slate-800 mb-0">PostgreSQL</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary mt-1 border border-primary border-opacity-25" style="font-size: 0.65rem;">Relational + PostGIS</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 flex-grow-1 bg-slate-50 d-flex flex-column">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="metric-box shadow-sm border-primary border-opacity-25">
                                <div class="metric-title">Write Latency</div>
                                <div class="metric-value text-primary" id="pg-latency">13<span style="font-size: 0.8rem; color:#94a3b8;">ms</span></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-box shadow-sm border-primary border-opacity-25">
                                <div class="metric-title">Insert Speed</div>
                                <div class="metric-value text-primary" id="pg-speed">642<span style="font-size: 0.8rem; color:#94a3b8;">/s</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 bg-white rounded-3 border p-2 shadow-sm">
                        <div class="fw-bold text-secondary px-2 pt-1" style="font-size: 0.75rem;">THROUGHPUT (OPS/SEC)</div>
                        <div id="chart-pg" style="height: 100px;"></div>
                    </div>

                    <!-- TABEL PGSQL ASLI DARI CSV (FULL LENGKAP) -->
                    <div class="custom-scroll mb-2 bg-white p-2 border shadow-sm" style="height: 280px; border-radius: 12px;">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Nama Tabel (Sesuai CSV/DB)</th>
                                    <th class="text-end">Rows</th>
                                </tr>
                            </thead>
                            <tbody id="pg-table-body">
                                <!-- Baris Tabel di-generate via JS agar rapi -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             PANEL KANAN: MONGODB (NoSQL POLYMORPHIC)
        =========================================== -->
        <div class="col-xl-6">
            <div class="premium-card h-100 d-flex flex-column">
                <div class="db-header-mg d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="https://upload.wikimedia.org/wikipedia/en/4/45/MongoDB-Logo.svg" alt="Mongo" style="height: 26px; margin-right: 15px; filter: brightness(0) saturate(100%) invert(35%) sepia(87%) saturate(464%) hue-rotate(107deg) brightness(96%) contrast(87%);">
                        <div>
                            <h5 class="fw-bolder text-slate-800 mb-0">MongoDB</h5>
                            <span class="badge bg-success bg-opacity-10 text-success mt-1 border border-success border-opacity-25" style="font-size: 0.65rem;">NoSQL Document</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 flex-grow-1 bg-slate-50 d-flex flex-column">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="metric-box shadow-sm border-success border-opacity-25">
                                <div class="metric-title">Write Latency</div>
                                <div class="metric-value text-success" id="mg-latency">2<span style="font-size: 0.8rem; color:#94a3b8;">ms</span></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-box shadow-sm border-success border-opacity-25">
                                <div class="metric-title">Insert Speed</div>
                                <div class="metric-value text-success" id="mg-speed">2,333<span style="font-size: 0.8rem; color:#94a3b8;">/s</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 bg-white rounded-3 border p-2 shadow-sm">
                        <div class="fw-bold text-secondary px-2 pt-1" style="font-size: 0.75rem;">THROUGHPUT (OPS/SEC)</div>
                        <div id="chart-mg" style="height: 100px;"></div>
                    </div>

                    <!-- KOTAK HIJAU MONGODB (BISA DIKLIK) -->
                    <div class="mongo-collection-box mb-2 shadow-sm" onclick="toggleMongoDetails()">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-6 text-slate-800"><i class="bi bi-folder-fill text-success me-2"></i>ais_messages (1 Collection)</div>
                            <div class="d-flex align-items-center">
                                <span class="text-success fw-bolder fs-5 me-3" id="mg-total" style="font-family: monospace;">0</span>
                                <i class="bi bi-chevron-down text-secondary transition-icon fs-5" id="mongo-chevron"></i>
                            </div>
                        </div>
                    </div>

                    <!-- RINCIAN POLYMORPHIC SESUAI DECODER.PY -->
                    <div id="mongo-details" class="custom-scroll bg-white p-3 border shadow-sm" style="height: 280px; border-radius: 12px; display: block;">
                        <div class="fw-bold text-secondary mb-3" style="font-size: 0.75rem;">RINCIAN DOKUMEN ('message_type'):</div>
                        <div id="mg-details-body" class="type-grid">
                            <!-- Grid Tipe Kombinasi di-generate via JS -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- SKEMA MENGIKUTI FUNGSI DI DECODER.PY & TABEL POSTGRESQL ---
    // Type 1, 2, 3 disatukan jadi 1_2_3
    // Type 4 dan 11 disatukan jadi 4_11
    // Type 7 dan 13 disatukan jadi 7_13
    const aisSchemaGroups = [
        { t: '1_2_3', pgTable: 'type_1_2_3_position_report_class_a', init: 23482, label: 'type_1_2_3' },
        { t: '4_11', pgTable: 'type_4_11_base_station_report', init: 91, label: 'type_4_11' },
        { t: '5', pgTable: 'type_5_static_voyage_related_data', init: 1274, label: 'type_5' },
        { t: '6', pgTable: 'type_6_binary_addressed_message', init: 34, label: 'type_6' },
        { t: '7_13', pgTable: 'type_7_13_binary_acknowledgement_recipient', init: 12, label: 'type_7_13' },
        { t: '8', pgTable: 'type_8_binary_broadcast_message', init: 56, label: 'type_8' },
        { t: '9', pgTable: 'type_9_standard _aircraft_position_report', init: 8, label: 'type_9' },
        { t: '10', pgTable: 'type_10_utc_date_inquiry', init: 5, label: 'type_10' },
        { t: '12', pgTable: 'type_12_addressed_safety_message', init: 14, label: 'type_12' },
        { t: '14', pgTable: 'type_14_safety_broadcast_message', init: 22, label: 'type_14' },
        { t: '15', pgTable: 'type_15_interrogation_query', init: 18, label: 'type_15' },
        { t: '16', pgTable: 'type_16_assignment_mode_command', init: 11, label: 'type_16' },
        { t: '17', pgTable: 'type_17_dgnss_binary_broadcast_message', init: 40, label: 'type_17' },
        { t: '18', pgTable: 'type_18_standar_class_b_position_report', init: 8530, label: 'type_18' },
        { t: '19', pgTable: 'type_19_extended_class_b_position_report', init: 2145, label: 'type_19' },
        { t: '20', pgTable: 'type_20_data_link_management_message_reservation', init: 27, label: 'type_20' },
        { t: '21', pgTable: 'type_21_aid_to_navigation_report', init: 300, label: 'type_21' },
        { t: '22', pgTable: 'type_22_channel_management', init: 15, label: 'type_22' },
        { t: '23', pgTable: 'type_23_group_assignment_command', init: 8, label: 'type_23' },
        { t: '24', pgTable: 'type_24_static_data_report', init: 376, label: 'type_24' },
        { t: '25', pgTable: 'type_25_single_slot_binary_message', init: 3, label: 'type_25' },
        { t: '26', pgTable: 'type_26_multiple_slot_binary_message', init: 6, label: 'type_26' },
        { t: '27', pgTable: 'type_27_long_range_position_report', init: 182, label: 'type_27' }
    ];

    let pgTablesMap = {};
    let mgTotalCount = 0;
    let currentTypeIndex = 0; // Kunci mode sekuensial (berurutan)

    // TOGGLE ACCORDION MONGODB
    function toggleMongoDetails() {
        const details = document.getElementById('mongo-details');
        const chevron = document.getElementById('mongo-chevron');
        if (details.style.display === 'none') {
            details.style.display = 'block';
            chevron.classList.add('icon-rotated');
        } else {
            details.style.display = 'none';
            chevron.classList.remove('icon-rotated');
        }
    }

    function formatNumber(num) { return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }

    document.addEventListener("DOMContentLoaded", function() {
        const pgTableBody = document.getElementById('pg-table-body');
        const mgDetailsBody = document.getElementById('mg-details-body');

        // Master Tables untuk SQL
        pgTablesMap['vessel'] = 1302;
        pgTablesMap['message'] = 36659;
        pgTableBody.insertAdjacentHTML('beforeend', `<tr><td><i class="bi bi-database-fill text-secondary me-2"></i>vessel</td><td id="pg-vessel" class="text-primary fs-6">1,302</td></tr>`);
        pgTableBody.insertAdjacentHTML('beforeend', `<tr><td><i class="bi bi-envelope-fill text-secondary me-2"></i>message</td><td id="pg-message" class="text-primary fs-6">36,659</td></tr>`);

        // Generate UI PostgreSQL (Sesuai CSV) & MongoDB (Sesuai Decoder)
        aisSchemaGroups.forEach(item => {
            // PostgreSQL Panel (Kiri)
            if(pgTablesMap[item.pgTable] === undefined) {
                pgTablesMap[item.pgTable] = 0;
                let pgHTML = `<tr>
                    <td style="word-break: break-all;"><i class="bi bi-table text-primary me-2"></i>${item.pgTable}</td>
                    <td id="pg-${item.pgTable}" class="text-primary fs-6">0</td>
                </tr>`;
                pgTableBody.insertAdjacentHTML('beforeend', pgHTML);
            }
            pgTablesMap[item.pgTable] += item.init;
            mgTotalCount += item.init;

            // MongoDB Panel (Kanan)
            let mgHTML = `
                <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1">
                    <div><span class="type-badge">${item.label}</span></div>
                    <div class="fw-bold" style="font-family: monospace; font-size: 0.85rem; color:#334155;" id="mg-type-${item.t}">${formatNumber(item.init)}</div>
                </div>
            `;
            mgDetailsBody.insertAdjacentHTML('beforeend', mgHTML);
        });

        // Set Nilai Awal Tabel
        Object.keys(pgTablesMap).forEach(key => {
            let el = document.getElementById('pg-' + key);
            if(el) el.innerText = formatNumber(pgTablesMap[key]);
        });
        document.getElementById('mg-total').innerText = formatNumber(mgTotalCount);

        // --- SETUP APEXCHARTS ---
        let dataPG = []; let dataMG = [];
        let timeBase = new Date().getTime();
        for(let i = 0; i < 15; i++) {
            dataPG.push([timeBase - (15-i)*1000, 0]);
            dataMG.push([timeBase - (15-i)*1000, 0]);
        }

        const commonOptions = {
            chart: { type: 'area', height: 100, animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } }, toolbar: { show: false }, zoom: { enabled: false }, sparkline: { enabled: true } },
            dataLabels: { enabled: false }, stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
            xaxis: { type: 'datetime', range: 14000 }, yaxis: { min: 0, max: 3000 }, tooltip: { enabled: false }
        };

        const chartPG = new ApexCharts(document.querySelector("#chart-pg"), { ...commonOptions, series: [{ data: dataPG }], colors: ['#3b82f6'] });
        const chartMG = new ApexCharts(document.querySelector("#chart-mg"), { ...commonOptions, series: [{ data: dataMG }], colors: ['#22c55e'] });

        chartPG.render(); chartMG.render();

        const logBox = document.getElementById('global-log');

        // --- LOOP SIMULASI REAL-TIME (BERURUTAN DARI TYPE 1_2_3 SAMPAI 27) ---
        setInterval(function() {
            let now = new Date();
            let timeStr = now.toTimeString().split('T')[1].substring(0,8);

            // AMBIL TIPE SECARA BERURUTAN (Sesuai file decoder.py)
            let currentGroup = aisSchemaGroups[currentTypeIndex];

            // Maju ke tipe selanjutnya, kembali ke 0 jika sudah sampai ujung
            currentTypeIndex++;
            if (currentTypeIndex >= aisSchemaGroups.length) {
                currentTypeIndex = 0;
            }

            // POSTGRESQL SIMULATION
            let spdPG = Math.floor(Math.random() * (900 - 500) + 500);
            let latPG = Math.floor(Math.random() * (16 - 11) + 11);
            let pgAdd = Math.floor(spdPG / 100);

            // Insert ke PostgreSQL
            pgTablesMap[currentGroup.pgTable] += pgAdd;
            pgTablesMap['message'] += pgAdd;

            let elPG = document.getElementById('pg-' + currentGroup.pgTable);
            elPG.innerText = formatNumber(pgTablesMap[currentGroup.pgTable]);
            document.getElementById('pg-message').innerText = formatNumber(pgTablesMap['message']);

            elPG.classList.add('value-update'); setTimeout(() => elPG.classList.remove('value-update'), 300);

            document.getElementById('pg-latency').innerHTML = latPG + '<span style="font-size:0.8rem; color:#94a3b8;">ms</span>';
            document.getElementById('pg-speed').innerHTML = spdPG + '<span style="font-size:0.8rem; color:#94a3b8;">/s</span>';

            dataPG.push([now.getTime(), spdPG]); if(dataPG.length > 20) dataPG.shift();
            chartPG.updateSeries([{ data: dataPG }]);

            // MONGODB SIMULATION
            let spdMG = Math.floor(Math.random() * (2600 - 1900) + 1900);
            let latMG = Math.floor(Math.random() * (5 - 2) + 2);
            let mgAdd = Math.floor(spdMG / 100);

            // Insert ke MongoDB
            let elMG = document.getElementById('mg-type-' + currentGroup.t);
            let currentVal = parseInt(elMG.innerText.replace(/,/g, ''));
            elMG.innerText = formatNumber(currentVal + mgAdd);

            mgTotalCount += mgAdd;
            document.getElementById('mg-total').innerText = formatNumber(mgTotalCount);

            elMG.classList.add('value-update'); setTimeout(() => elMG.classList.remove('value-update'), 300);

            document.getElementById('mg-latency').innerHTML = latMG + '<span style="font-size:0.8rem; color:#94a3b8;">ms</span>';
            document.getElementById('mg-speed').innerHTML = formatNumber(spdMG) + '<span style="font-size:0.8rem; color:#94a3b8;">/s</span>';

            dataMG.push([now.getTime(), spdMG]); if(dataMG.length > 20) dataMG.shift();
            chartMG.updateSeries([{ data: dataMG }]);

            // UPDATE LIVE LOG STREAM (Sangat informatif, sesuai kelompok decoder)
            let shortTableName = currentGroup.pgTable.substring(0, 30) + '...';
            let logHTML = `
                <div class="log-entry">
                    <span class="log-time">[+0700 ${timeStr}]</span>
                    <span class="log-mg">MONGO</span> Upserted <span class="log-type">Type ${currentGroup.label}</span> to ais_messages <span class="text-secondary">(${latMG}ms)</span>
                    <span class="text-secondary mx-2">||</span>
                    <span class="log-pg">PGSQL</span> Inserted to <span class="text-white">${shortTableName}</span> <span class="text-secondary">(${latPG}ms)</span>
                </div>`;
            logBox.insertAdjacentHTML('afterbegin', logHTML);
            if(logBox.children.length > 6) logBox.removeChild(logBox.lastChild);

        }, 1000);
    });
</script>
@endpush
