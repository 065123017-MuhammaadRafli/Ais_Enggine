@extends('layouts.main')

@push('styles')
<style>
    /* Styling Premium & Modern */
    .dashboard-bg { background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem; }
    .premium-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; }

    /* Header Title */
    .page-title-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-top: 1.5rem;}
    .icon-title { width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 1.25rem; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3); }

    /* Area Drag & Drop */
    .upload-zone { border: 2px dashed #cbd5e1; border-radius: 16px; padding: 4rem 2rem; text-align: center; background-color: #ffffff; transition: all 0.3s ease; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 350px;}
    .upload-zone.dragover { border-color: #3b82f6; background-color: #eff6ff; box-shadow: inset 0 0 20px rgba(59, 130, 246, 0.1); }
    .upload-icon { font-size: 3.5rem; color: #3b82f6; margin-bottom: 1rem; transition: transform 0.3s ease; }
    .upload-zone.dragover .upload-icon { transform: translateY(-10px); }
    .upload-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
    .upload-subtitle { font-size: 0.9rem; color: #64748b; margin-bottom: 1.5rem; }
    .btn-upload { background: #3b82f6; border: none; padding: 0.6rem 1.5rem; font-weight: 600; border-radius: 8px; color: white; transition: background 0.2s; cursor: pointer; }
    .btn-upload:hover { background: #2563eb; }

    /* Progress Bar Upload */
    .upload-progress-container { width: 80%; display: none; flex-direction: column; align-items: center; }
    .progress-bar-bg { width: 100%; height: 10px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0.75rem; }
    .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%); width: 0%; transition: width 0.1s linear; }
    .progress-text { font-weight: 700; color: #0f172a; font-size: 1rem; }
    .progress-filename { color: #64748b; font-size: 0.85rem; margin-top: 0.25rem; }

    /* Format & Storage Cards */
    .info-box { border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; display: flex; align-items: flex-start; gap: 1rem; }
    .info-box.blue { background-color: #eff6ff; border-color: #bfdbfe; }
    .info-box.yellow { background-color: #fffbeb; border-color: #fde68a; }
    .info-box.gray { background-color: #f8fafc; }
    .info-icon { font-size: 1.5rem; line-height: 1; }
    .info-box.blue .info-icon { color: #3b82f6; }
    .info-box.yellow .info-icon { color: #f59e0b; }
    .info-box.gray .info-icon { color: #94a3b8; }
    .info-title { font-weight: 700; color: #0f172a; font-size: 0.85rem; margin-bottom: 0.1rem; }
    .info-desc { font-size: 0.75rem; color: #64748b; margin-bottom: 0; }

    /* Storage Progress */
    .storage-box { padding: 1.25rem; border-top: 1px dashed #e2e8f0; margin-top: 0.5rem; }
    .storage-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
    .storage-title { font-weight: 700; color: #0f172a; font-size: 0.9rem; }
    .storage-pct { font-weight: 800; color: #3b82f6; font-size: 0.9rem; }
    .storage-bar-bg { height: 6px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 0.5rem; }
    .storage-bar-fill { height: 100%; background: #3b82f6; width: 45%; transition: width 0.5s ease; }
    .storage-desc { font-size: 0.7rem; color: #94a3b8; }

    /* Modern Table */
    .table-container { overflow-x: auto; }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th { font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; padding: 1rem; border-bottom: 2px solid #f1f5f9; text-align: left; letter-spacing: 0.5px;}
    .modern-table td { padding: 1rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; font-size: 0.85rem; }
    .modern-table tr:hover td { background: #f8fafc; }
    .file-name { font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.75rem; }
    .file-icon { color: #94a3b8; font-size: 1.1rem; }

    /* Badges & Buttons in Table */
    .status-badge { padding: 0.25rem 0.6rem; border-radius: 50px; font-size: 0.7rem; font-weight: 700; border: 1px solid; display: inline-flex; align-items: center; }
    .status-badge.saved { background: #ecfdf5; color: #10b981; border-color: #a7f3d0; }
    .status-badge.parsed { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; }

    .btn-parse { background: #ffffff; border: 1px solid #cbd5e1; color: #64748b; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.8rem; transition: all 0.2s; }
    .btn-parse:hover { border-color: #3b82f6; color: #3b82f6; }
    .btn-parse.disabled { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }

    .new-row { animation: highlightRow 2s ease-out; }
    @keyframes highlightRow { 0% { background-color: #eff6ff; } 100% { background-color: transparent; } }
</style>
@endpush

@section('content')
<div class="dashboard-bg">

    <!-- PAGE TITLE -->
    <div class="page-title-box">
        <div class="d-flex align-items-center">
            <div class="icon-title">
                <i class="bi bi-cloud-arrow-up"></i>
            </div>
            <div>
                <h2 class="fw-bolder mb-0 text-slate-800" style="font-size: 1.8rem; letter-spacing: -0.5px;">Import Data Raw</h2>
                <p class="mb-0 text-secondary" style="font-size: 0.95rem; font-weight: 500;">Unggah file data satelit mentah (NMEA, CSV, JSON) ke dalam sistem Ingestion.</p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- AREA UPLOAD (KIRI) -->
        <div class="col-xl-8">
            <div class="premium-card h-100">
                <!-- Dropzone Area -->
                <div class="upload-zone" id="drop-zone">
                    <input type="file" id="file-input" class="d-none" accept=".csv,.json,.txt" multiple>

                    <!-- Form Normal (Sebelum Upload) -->
                    <div id="upload-default-ui" class="text-center">
                        <i class="bi bi-cloud-arrow-up upload-icon"></i>
                        <h4 class="upload-title">Drag & Drop file data di sini</h4>
                        <p class="upload-subtitle">atau klik tombol di bawah untuk memilih file dari komputer Anda.</p>
                        <button type="button" class="btn-upload" onclick="document.getElementById('file-input').click()">
                            <i class="bi bi-folder2-open me-2"></i>Pilih File Data
                        </button>
                    </div>

                    <!-- Form Progress (Saat Uploading) -->
                    <div id="upload-progress-ui" class="upload-progress-container">
                        <i class="bi bi-rocket-takeoff text-primary fs-1 mb-3"></i>
                        <h5 class="fw-bolder text-slate-800 mb-3">Mengunggah File ke Server...</h5>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" id="upload-bar"></div>
                        </div>
                        <div class="d-flex justify-content-between w-100">
                            <span class="progress-filename" id="progress-filename">data_ais.csv</span>
                            <span class="progress-text" id="upload-percentage">0%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INFO FORMAT & STORAGE (KANAN) -->
        <div class="col-xl-4">
            <div class="premium-card h-100 d-flex flex-column p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                    <h6 class="fw-bolder text-slate-800 mb-0">Format Didukung</h6>
                </div>

                <div class="info-box blue">
                    <i class="bi bi-filetype-csv info-icon"></i>
                    <div>
                        <div class="info-title">File CSV (.csv)</div>
                        <p class="info-desc">Maksimal 50MB per file</p>
                    </div>
                </div>

                <div class="info-box yellow">
                    <i class="bi bi-filetype-json info-icon"></i>
                    <div>
                        <div class="info-title">File JSON (.json)</div>
                        <p class="info-desc">GeoJSON data terstruktur</p>
                    </div>
                </div>

                <div class="info-box gray">
                    <i class="bi bi-file-earmark-text info-icon"></i>
                    <div>
                        <div class="info-title">NMEA Payload (.txt)</div>
                        <p class="info-desc">Raw satelit AIS (Tipe 1, 2, 3...)</p>
                    </div>
                </div>

                <div class="mt-auto storage-box">
                    <div class="storage-header">
                        <span class="storage-title">Storage Server</span>
                        <span class="storage-pct" id="storage-pct-text">45.0%</span>
                    </div>
                    <div class="storage-bar-bg">
                        <div class="storage-bar-fill" id="storage-bar-fill"></div>
                    </div>
                    <div class="storage-desc" id="storage-desc-text">45.0GB terpakai dari total 100GB dialokasikan.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT UPLOAD -->
    <div class="premium-card mb-4">
        <div class="d-flex align-items-center p-4 border-bottom border-light">
            <i class="bi bi-clock-history text-primary me-2 fs-5"></i>
            <h5 class="fw-bolder text-slate-800 mb-0">Riwayat Upload Terakhir</h5>
        </div>
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Tanggal Upload</th>
                        <th>Uploader</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="history-table-body">
                    <!-- Data Dummy Awal -->
                    <tr>
                        <td>
                            <div class="file-name">
                                <i class="bi bi-file-earmark-spreadsheet file-icon text-primary"></i>
                                ais_data_jkt_2026.csv
                            </div>
                        </td>
                        <td>12.4 MB</td>
                        <td>Hari ini, 09:15 WIB</td>
                        <td>Muhammad Raflii</td>
                        <td><span class="status-badge saved">Tersimpan</span></td>
                        <td>
                            <button class="btn-parse" onclick="startParsing(this)">
                                <i class="bi bi-search me-1"></i> Parse
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="file-name">
                                <i class="bi bi-file-earmark-text file-icon text-secondary"></i>
                                raw_satellite_dump.txt
                            </div>
                        </td>
                        <td>45.1 MB</td>
                        <td>Kemarin, 14:30 WIB</td>
                        <td>System Automasi</td>
                        <td><span class="status-badge saved">Tersimpan</span></td>
                        <td>
                            <button class="btn-parse" onclick="startParsing(this)">
                                <i class="bi bi-search me-1"></i> Parse
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const defaultUI = document.getElementById('upload-default-ui');
        const progressUI = document.getElementById('upload-progress-ui');
        const uploadBar = document.getElementById('upload-bar');
        const uploadPct = document.getElementById('upload-percentage');
        const progressFilename = document.getElementById('progress-filename');
        const tableBody = document.getElementById('history-table-body');

        let storageUsed = 45.0; // Kapasitas awal 45%

        // --- 1. FITUR DRAG & DROP ---
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault(); e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
        });

        // Event saat file dijatuhkan (Drop)
        dropZone.addEventListener('drop', handleDrop, false);

        // Event saat file dipilih lewat tombol
        fileInput.addEventListener('change', function() {
            if(this.files.length > 0) processUpload(this.files[0]);
        });

        function handleDrop(e) {
            let dt = e.dataTransfer;
            let files = dt.files;
            if(files.length > 0) processUpload(files[0]);
        }

        // --- 2. ANIMASI UPLOAD REALISTIS ---
        function processUpload(file) {
            // Validasi sederhana
            if(!file) return;

            // Setup UI Progress
            defaultUI.style.display = 'none';
            progressUI.style.display = 'flex';
            progressFilename.innerText = file.name;
            uploadBar.style.width = '0%';
            uploadPct.innerText = '0%';

            let progress = 0;
            // Kecepatan upload acak untuk variasi
            let uploadSpeed = Math.floor(Math.random() * 10) + 5;

            let interval = setInterval(() => {
                progress += uploadSpeed;
                if(progress > 100) progress = 100;

                uploadBar.style.width = progress + '%';
                uploadPct.innerText = progress + '%';

                if(progress === 100) {
                    clearInterval(interval);

                    // Jeda sebentar sebelum selesai
                    setTimeout(() => {
                        finishUpload(file);
                    }, 500);
                }
            }, 150);
        }

        // --- 3. PROSES SETELAH UPLOAD SELESAI ---
        function finishUpload(file) {
            // Kembalikan UI Upload seperti semula
            defaultUI.style.display = 'block';
            progressUI.style.display = 'none';
            fileInput.value = ""; // Reset input file

            // Kalkulasi Ukuran File Dummy (MB)
            let fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
            if(fileSizeMB == 0.0) fileSizeMB = (Math.random() * 5 + 1).toFixed(1); // Kasih ukuran random jika file kosong

            // Tentukan icon berdasarkan ekstensi
            let iconClass = 'bi-file-earmark-text text-secondary';
            if(file.name.endsWith('.csv')) iconClass = 'bi-file-earmark-spreadsheet text-primary';
            else if(file.name.endsWith('.json')) iconClass = 'bi-filetype-json text-warning';

            // Dapatkan waktu saat ini
            let timeStr = new Date().toLocaleTimeString('id-ID', {hour12: false}).substring(0,5);

            // TAMBAH BARIS KE TABEL RIWAYAT
            let newRow = document.createElement('tr');
            newRow.className = 'new-row'; // Kelas untuk animasi highlight
            newRow.innerHTML = `
                <td>
                    <div class="file-name">
                        <i class="bi ${iconClass} file-icon"></i>
                        ${file.name}
                    </div>
                </td>
                <td>${fileSizeMB} MB</td>
                <td>Hari ini, ${timeStr} WIB</td>
                <td>Muhammad Raflii</td>
                <td><span class="status-badge saved">Tersimpan</span></td>
                <td>
                    <button class="btn-parse" onclick="startParsing(this)">
                        <i class="bi bi-search me-1"></i> Parse
                    </button>
                </td>
            `;

            // Masukkan ke paling atas (insert before baris pertama)
            tableBody.insertBefore(newRow, tableBody.firstChild);

            // UPDATE KAPASITAS STORAGE SERVER
            storageUsed += (parseFloat(fileSizeMB) / 1000); // Simulasi nambah kapasitas GB (dibagi 1000 agar naik sedikit)
            let storageStr = storageUsed.toFixed(1);

            document.getElementById('storage-pct-text').innerText = storageStr + '%';
            document.getElementById('storage-bar-fill').style.width = storageStr + '%';
            document.getElementById('storage-desc-text').innerText = storageStr + 'GB terpakai dari total 100GB dialokasikan.';
        }
    });

    // --- 4. FITUR TOMBOL PARSE (UBAH STATUS) ---
    // Harus ditaruh di luar DOMContentLoaded agar bisa dipanggil dari onclick atribut HTML
    window.startParsing = function(btn) {
        // Cegah klik dobel
        if(btn.classList.contains('disabled')) return;

        // Ubah tombol jadi loading
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Proses...';
        btn.classList.add('disabled');

        // Cari elemen badge status (Tersimpan) di baris yang sama
        let tr = btn.closest('tr');
        let badge = tr.querySelector('.status-badge');

        // Ubah warna badge jadi kuning saat proses
        badge.className = 'status-badge';
        badge.style.backgroundColor = '#fffbeb';
        badge.style.color = '#d97706';
        badge.style.borderColor = '#fde68a';
        badge.innerText = 'Mendekode...';

        // Simulasi proses dekode 3 detik
        setTimeout(() => {
            // Selesai
            btn.innerHTML = '<i class="bi bi-check2-all me-1"></i> Selesai';
            btn.style.backgroundColor = '#ecfdf5';
            btn.style.color = '#10b981';
            btn.style.borderColor = '#a7f3d0';

            // Ubah badge jadi biru (Parsed)
            badge.className = 'status-badge parsed';
            badge.style = ''; // Reset style inline
            badge.innerText = 'Terekstrak';

        }, 3000);
    }
</script>
@endpush
