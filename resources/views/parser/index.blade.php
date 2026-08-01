@extends('layouts.main')

@push('styles')
<style>
    /* Styling Dasar Kartu */
    .premium-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
    .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
    .stat-value { font-size: 2.2rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin-top: 0.5rem; white-space: nowrap; }
    .stat-value-text { font-size: 1.8rem; font-weight: 700; }
    .icon-box { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.25rem; flex-shrink: 0; }

    /* Terminal UI */
    .terminal-container { background-color: #0f172a; border-radius: 16px; border: 1px solid #1e293b; display: flex; flex-direction: column; height: 650px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2); }
    .terminal-header { background-color: #1e293b; padding: 1rem 1.5rem; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; border-top-left-radius: 16px; border-top-right-radius: 16px; }

    /* Terminal Body - Bisa diedit & scroll */
    .terminal-body { padding: 1.5rem; font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 0.85rem; color: #e2e8f0; overflow-y: auto; flex-grow: 1; line-height: 1.7; scroll-behavior: smooth; outline: none; }
    .terminal-body:focus { box-shadow: inset 0 0 0 2px rgba(56, 189, 248, 0.2); }
    .terminal-body::-webkit-scrollbar { width: 8px; }
    .terminal-body::-webkit-scrollbar-track { background: #0f172a; }
    .terminal-body::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

    /* Syntax Highlighting */
    .text-nmea { color: #64748b; }
    .json-key { color: #60a5fa; }
    .json-string { color: #34d399; }
    .json-number { color: #fbbf24; }
    .log-success { color: #10b981; font-weight: bold; margin-bottom: 1.5rem; display: block; border-bottom: 1px dashed #334155; padding-bottom: 1rem;}

    .cursor-blink { animation: blink 1s step-end infinite; border-right: 2px solid #34d399; padding-right: 4px; display: inline-block; margin-top: 1rem; }
    @keyframes blink { 50% { border-color: transparent; } }
    @keyframes spin-slow { 100% { transform: rotate(360deg); } }

    .terminal-stopped { opacity: 0.7; }
</style>
@endpush

@section('content')
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-4" style="border-bottom: 2px solid #e2e8f0;">
        <div class="d-flex align-items-center">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff;">
                <i class="bi bi-code-slash fs-3"></i>
            </div>
            <div>
                <h2 class="fw-bolder mb-1 text-slate-800" style="letter-spacing: -0.5px; font-size: 1.8rem;">AIS Parser & Decoder</h2>
                <p class="mb-0 text-secondary" style="font-size: 0.95rem; font-weight: 500;">Simulasi Real-time Pemrosesan AIVDM Type 1-27 terintegrasi SQL/NoSQL.</p>
            </div>
        </div>

        <div>
            <button id="btn-start" class="btn btn-primary px-4 py-2 rounded-pill fw-medium shadow-sm me-2">
                <i class="bi bi-play-fill me-1"></i> Jalankan Mesin
            </button>
            <button id="btn-stop" class="btn btn-danger px-4 py-2 rounded-pill fw-medium shadow-sm d-none">
                <i class="bi bi-stop-fill me-1"></i> Hentikan Mesin
            </button>
        </div>
    </div>

    <!-- 4 KARTU STATUS -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="premium-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-label mt-1">Baris Diproses</div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-card-list"></i></div>
                </div>
                <div class="stat-value" id="count-processed">24,768</div>
                <div class="text-secondary mt-1" style="font-size: 0.8rem;">Data NMEA terselesaikan</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="premium-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-label mt-1">Kecepatan Parsing</div>
                    <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-lightning-charge-fill"></i></div>
                </div>
                <div class="stat-value" id="count-speed">0.0 <span class="fs-5 text-secondary fw-medium">k/sec</span></div>
                <div class="text-success fw-bold mt-1" style="font-size: 0.8rem;" id="text-speed-status">Menunggu</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="premium-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-label mt-1">Tingkat Error (Corrupt)</div>
                    <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="bi bi-bug-fill"></i></div>
                </div>
                <div class="stat-value text-danger">0.02 <span class="fs-5 fw-medium">%</span></div>
                <div class="text-secondary mt-1" style="font-size: 0.8rem;">Payload tidak valid diabaikan</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="premium-card p-4 h-100" id="card-status" style="border: 2px solid #e2e8f0;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-label mt-1" id="label-status">Status Engine</div>
                    <div class="icon-box bg-secondary bg-opacity-10 text-secondary" id="icon-status"><i class="bi bi-pause-circle"></i></div>
                </div>
                <div class="stat-value text-secondary d-flex align-items-center mt-2 stat-value-text" id="text-status">
                    Standby
                </div>
            </div>
        </div>
    </div>

    <!-- TERMINAL AREA -->
    <div class="row g-4">
        <!-- Terminal Kiri -->
        <div class="col-xl-6">
            <div class="terminal-container">
                <div class="terminal-header">
                    <div class="fw-bold text-white" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                        <i class="bi bi-terminal me-2 text-secondary"></i>Raw Input (NMEA/AIVDM)
                    </div>
                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-2 py-1 rounded-pill">Port: 10110</span>
                </div>
                <div class="terminal-body terminal-stopped" id="nmea-terminal" contenteditable="true" spellcheck="false">
                    <div id="nmea-content"></div>
                    <div class="text-white cursor-blink" id="nmea-cursor">> Menunggu perintah (Jalankan Mesin)...</div>
                </div>
            </div>
        </div>

        <!-- Terminal Kanan -->
        <div class="col-xl-6">
            <div class="terminal-container">
                <div class="terminal-header">
                    <div class="fw-bold text-white" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                        <i class="bi bi-braces me-2 text-info"></i>Decoded Output (Polymorphic)
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill"><i class="bi bi-database me-1"></i>SQL</span>
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill"><i class="bi bi-env me-1"></i>NoSQL</span>
                    </div>
                </div>
                <div class="terminal-body terminal-stopped" id="json-terminal" contenteditable="true" spellcheck="false">
                    <div id="json-content"></div>
                    <div class="text-white cursor-blink" id="json-cursor">> Menunggu aliran data parsing...</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnStart = document.getElementById('btn-start');
        const btnStop = document.getElementById('btn-stop');

        const cardStatus = document.getElementById('card-status');
        const labelStatus = document.getElementById('label-status');
        const iconStatus = document.getElementById('icon-status');
        const textStatus = document.getElementById('text-status');

        const countProcessed = document.getElementById('count-processed');
        const countSpeed = document.getElementById('count-speed');
        const textSpeedStatus = document.getElementById('text-speed-status');

        const nmeaTerminal = document.getElementById('nmea-terminal');
        const nmeaContent = document.getElementById('nmea-content');
        const nmeaCursor = document.getElementById('nmea-cursor');

        const jsonTerminal = document.getElementById('json-terminal');
        const jsonContent = document.getElementById('json-content');
        const jsonCursor = document.getElementById('json-cursor');

        let isRunning = false;
        let intervalId;
        let processedLine = 0;
        let currentTypeIndex = 0;

        // Fungsi Ajaib: Mengubah Object JSON menjadi HTML berwarna
        function syntaxHighlight(jsonObj) {
            let json = JSON.stringify(jsonObj, null, 2);
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            let formatted = json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                let cls = 'json-number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'json-key';
                    } else {
                        cls = 'json-string';
                    }
                } else if (/true|false|null/.test(match)) {
                    cls = 'json-string';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
            // Ganti spasi dengan &nbsp; dan enter dengan <br> agar rapi di div biasa
            return formatted.replace(/\n/g, '<br>').replace(/  /g, '&nbsp;&nbsp;');
        }

        // --- DATA MOCKUP SELURUH TIPE 1 SAMPAI 27 (AKURAT DARI KODE PYTHON) ---
        const aisDataTypes = [
            { type: "type_1_2_3", nmea: "> !AIVDM,1,1,,B,177KQJ5000G?tO`K>RA1wUbN0TKH,0*5C", obj: { message_type: "type_1_2_3", location: { type: "Point", coordinates: [106.87908, -6.16204] }, decoded_json: { mmsi: 123456789, nav_stat: "Under way using engine", rot: 0, sog: 14.2, pos_acc: 1, cog: 210.3, heading: 215, second: 30, maneuver: 0, raim: 0, radio: 82419 } } },
            { type: "type_4_11", nmea: "> !AIVDM,1,1,,A,402R2uQ000Htt<tSF0l4Q@100`P2,0*5B", obj: { message_type: "type_4_11", location: { type: "Point", coordinates: [106.8, -6.11] }, decoded_json: { mmsi: 3192000, year: 2026, month: 7, day: 21, hour: 14, minute: 30, second: 0, fix_quality: 1, epfd: "GPS", spare: 0, raim: 0, sotdma: 0 } } },
            { type: "type_5", nmea: "> !AIVDM,1,1,,A,53aEOU?P00PCL@>Mce?6uvv02<1;,0*1F", obj: { message_type: "type_5", decoded_json: { mmsi: 987654321, ais_version: 2, imo: 9123456, callsign: "YBD123", shipname: "KM EXPLORER", shiptype: "Cargo, all ships of this type", dimension_to_bow: 120, dimension_to_stern: 30, dimension_to_port: 10, dimension_to_starboard: 10, epfd: 1, eta_month: 7, eta_day: 22, eta_hour: 10, eta_minute: 0, draught: 8.5, destination: "TG. PRIOK", dte: 0, spare: 0 } } },
            { type: "type_6", nmea: "> !AIVDM,1,1,,A,6B?p>j000000>0000000000,2*31", obj: { message_type: "type_6", decoded_json: { mmsi: 477999999, seqno: 1, dest_mmsi: 123456789, retransmit: 0, spare: 0, dac: 235, fid: 10, payload: 1024 } } },
            { type: "type_7_13", nmea: "> !AIVDM,1,1,,A,702R2uQ000Htt<tSF0l4Q@100`P2,0*5C", obj: { message_type: "type_7_13", decoded_json: { mmsi: 123412345, spare: 0, mmsi1: 987654321, mmsiseq1: 1, mmsi2: 0, mmsiseq2: 0, mmsi3: 0, mmsiseq3: 0, mmsi4: 0, mmsiseq4: 0 } } },
            { type: "type_8", nmea: "> !AIVDM,1,1,,B,8>j8>j00000000000000000,2*73", obj: { message_type: "type_8", decoded_json: { mmsi: 525111111, spare: 0, dac: 1, fid: 11, payload: 95200 } } },
            { type: "type_9", nmea: "> !AIVDM,1,1,,A,9000000000000000000000000000,0*71", obj: { message_type: "type_9", location: { type: "Point", coordinates: [106.5, -6.0] }, decoded_json: { mmsi: 366999999, altitude: 2000, sog: 150, pos_acc: 1, cog: 90, second: 12, reserved: 0, dte: 1, spare: 0, assigned: 0, raim: 0, radio: 0 } } },
            { type: "type_10", nmea: "> !AIVDM,1,1,,B,:>j8>j00000000000000000,2*73", obj: { message_type: "type_10", decoded_json: { mmsi: 2359999, spare: 0, dest_mmsi: 123123123 } } },
            { type: "type_12", nmea: "> !AIVDM,1,1,,A,<000000000000000000000000000,0*71", obj: { message_type: "type_12", decoded_json: { mmsi: 412345678, seqno: 2, dest_mmsi: 987654321, retransmit: 0, spare: 0, text: 89324 } } },
            { type: "type_14", nmea: "> !AIVDM,1,1,,A,>000000000000000000000000000,0*71", obj: { message_type: "type_14", decoded_json: { mmsi: 111222333, spare: 0, text: 123456 } } },
            { type: "type_15", nmea: "> !AIVDM,1,1,,B,?000000000000000000000000000,0*71", obj: { message_type: "type_15", decoded_json: { mmsi: 444555666, mmsi1: 777888999, type1_1: 3, offset1_1: 100 } } },
            { type: "type_16", nmea: "> !AIVDM,1,1,,A,@000000000000000000000000000,0*71", obj: { message_type: "type_16", decoded_json: { mmsi: 123451234, mmsi1: 999888777, offset1: 50, increment1: 10 } } },
            { type: "type_17", nmea: "> !AIVDM,1,1,,A,A000000000000000000000000000,0*71", obj: { message_type: "type_17", location: { type: "Point", coordinates: [106.8, -6.1] }, decoded_json: { mmsi: 555666777, spare: 0, payload: "010101" } } },
            { type: "type_18", nmea: "> !AIVDM,1,1,,B,B>j8>j00000000000000000,2*73", obj: { message_type: "type_18", location: { type: "Point", coordinates: [107.1, -6.5] }, decoded_json: { mmsi: 525111111, reserved: 0, sog: 12.5, pos_acc: 1, cog: 180.2, heading: 185, second: 45, regional: 0, cs: 1, display: 0, dsc: 1, band: 1, msg22: 1, assigned: 0, raim: 0, radio: 12345 } } },
            { type: "type_19", nmea: "> !AIVDM,1,1,,A,C000000000000000000000000000,0*71", obj: { message_type: "type_19", location: { type: "Point", coordinates: [106.2, -5.9] }, decoded_json: { mmsi: 999888777, reserved: 0, sog: 8.0, pos_acc: 0, cog: 90, heading: 90, second: 12, regional: 0, shipname: "BINA MARITIM", shiptype: "Passenger, all ships of this type", dimension_to_bow: 50, dimension_to_stern: 10, dimension_to_port: 5, dimension_to_starboard: 5, epfd: 1, raim: 0, dte: 1, assigned: 0, spare: 0 } } },
            { type: "type_20", nmea: "> !AIVDM,1,1,,A,D000000000000000000000000000,0*71", obj: { message_type: "type_20", decoded_json: { mmsi: 111111111, reservations: [{ offset: 10, number: 2, timeout: 5, increment: 100 }] } } },
            { type: "type_21", nmea: "> !AIVDM,1,1,,B,E>j8>j00000000000000000,2*73", obj: { message_type: "type_21", location: { type: "Point", coordinates: [106.9, -6.0] }, decoded_json: { mmsi: 990000000, aid_type: "Beacon, Cardinal N", name: "BUOY 1", pos_acc: 1, dimension_to_bow: 2, dimension_to_stern: 2, dimension_to_port: 2, dimension_to_starboard: 2, epfd: 1, second: 0, off_position: 0, regional: 0, raim: 0, virtual_aid: 0, assigned: 0, name_extension: "" } } },
            { type: "type_22", nmea: "> !AIVDM,1,1,,A,F000000000000000000000000000,0*71", obj: { message_type: "type_22", location: { type: "Polygon", coordinates: [[106.85, -6.15], [106.90, -6.15], [106.90, -6.20], [106.85, -6.20], [106.85, -6.15]] }, decoded_json: { mmsi: 2359999, channel_a: 87, channel_b: 88, txrx: 0, power: 1, addressed: 0, band_a: 0, band_b: 0, zonesize: 3 } } },
            { type: "type_23", nmea: "> !AIVDM,1,1,,B,G>j8>j00000000000000000,2*73", obj: { message_type: "type_23", location: { type: "Polygon", coordinates: [[106.85, -6.15], [106.90, -6.15], [106.90, -6.20], [106.85, -6.20], [106.85, -6.15]] }, decoded_json: { mmsi: 2359999, station_type: 1, ship_type: 70, txrx: 0, interval: 2, quiet: 0 } } },
            { type: "type_24", nmea: "> !AIVDM,1,1,,A,H000000000000000000000000000,0*71", obj: { message_type: "type_24", decoded_json: { mmsi: 525119038, part_number: 0, shipname: "BINA MARITIM", spare: 0 } } },
            { type: "type_25", nmea: "> !AIVDM,1,1,,B,I>j8>j00000000000000000,2*73", obj: { message_type: "type_25", decoded_json: { mmsi: 222333444, addressed: 1, structured: 0, dest_mmsi: 987654321, payload: "010101" } } },
            { type: "type_26", nmea: "> !AIVDM,1,1,,A,J000000000000000000000000000,0*71", obj: { message_type: "type_26", decoded_json: { mmsi: 333444555, addressed: 0, structured: 1, dest_mmsi: 0, payload: "111000" } } },
            { type: "type_27", nmea: "> !AIVDM,1,1,,B,K>j8>j00000000000000000,2*73", obj: { message_type: "type_27", location: { type: "Point", coordinates: [106.8, -6.1] }, decoded_json: { mmsi: 123456789, pos_acc: 1, raim: 0, nav_stat: "Under way using engine", sog: 15, cog: 200, gnss: 0, spare: 0 } } }
        ];

        function scrollToBottom() {
            nmeaTerminal.scrollTop = nmeaTerminal.scrollHeight;
            jsonTerminal.scrollTop = jsonTerminal.scrollHeight;
        }

        function stopEngine() {
            isRunning = false;
            clearInterval(intervalId);

            btnStop.classList.add('d-none');
            btnStart.classList.remove('d-none');

            cardStatus.style.border = "2px solid #e2e8f0";
            cardStatus.style.background = "#ffffff";
            labelStatus.classList.replace('text-success', 'text-secondary');
            iconStatus.className = "icon-box bg-secondary bg-opacity-10 text-secondary";
            textStatus.className = "stat-value text-secondary d-flex align-items-center mt-2 stat-value-text";
            textStatus.innerHTML = '<i class="bi bi-pause-circle me-2"></i> Standby';

            nmeaCursor.innerText = "> Seluruh Tipe 1-27 selesai diproses. Mesin berhenti.";
            jsonCursor.innerHTML = "> Parsing selesai. <span style='color: #fbbf24'>[!] Anda bisa klik dan edit teks di terminal ini.</span>";

            countSpeed.innerHTML = '0.0 <span class="fs-5 text-secondary fw-medium">k/sec</span>';
            textSpeedStatus.innerHTML = '<i class="bi bi-dash"></i> Berhenti';
            textSpeedStatus.className = 'text-secondary mt-1';

            nmeaTerminal.classList.add('terminal-stopped');
            jsonTerminal.classList.add('terminal-stopped');
        }

        function appendData() {
            if(!isRunning) return;

            processedLine++;
            countProcessed.innerText = (24768 + processedLine).toLocaleString();
            countSpeed.innerHTML = (Math.random() * (1.4 - 1.1) + 1.1).toFixed(1) + ' <span class="fs-5 text-secondary fw-medium">k/sec</span>';

            let data = aisDataTypes[currentTypeIndex];

            // Tambah baris NMEA
            let newNmeaDiv = document.createElement('div');
            newNmeaDiv.className = 'text-nmea mb-1';
            newNmeaDiv.innerHTML = data.nmea;
            nmeaContent.appendChild(newNmeaDiv);

            // Tambah baris JSON
            let newJsonDiv = document.createElement('div');
            newJsonDiv.className = 'mb-4';

            // Sisipkan waktu real-time ke dalam Object Murni kita sebelum di Highlight
            data.obj.received_at = new Date().toISOString();
            let highlightedJson = syntaxHighlight(data.obj);

            newJsonDiv.innerHTML = `
                <div class="text-secondary mb-2 border-top border-secondary pt-3" style="font-family: 'Consolas', monospace;"># Terdeteksi: ${data.type} -> Dialirkan ke DB...</div>
                <div style="padding-left: 0.5rem; border-left: 2px solid #334155;">
                    ${highlightedJson}
                </div>
                <span class="log-success mt-3"><i class="bi bi-check-circle-fill me-1"></i> [SUCCESS] Inserted to PostgreSQL (${data.type}) & MongoDB (ais_messages)</span>
            `;
            jsonContent.appendChild(newJsonDiv);

            scrollToBottom();

            currentTypeIndex++;
            if (currentTypeIndex >= aisDataTypes.length) {
                stopEngine();
            }
        }

        // --- TOMBOL JALANKAN ---
        btnStart.addEventListener('click', function() {
            if(currentTypeIndex >= aisDataTypes.length) {
                currentTypeIndex = 0;
                let dividerLeft = document.createElement('div');
                dividerLeft.innerHTML = "<br><span class='text-secondary'>--- BATCH BARU ---</span><br><br>";
                nmeaContent.appendChild(dividerLeft);

                let dividerRight = document.createElement('div');
                dividerRight.innerHTML = "<br><span class='text-secondary'>--- BATCH BARU ---</span><br><br>";
                jsonContent.appendChild(dividerRight);
            }

            isRunning = true;
            btnStart.classList.add('d-none');
            btnStop.classList.remove('d-none');

            cardStatus.style.border = "2px solid rgba(16, 185, 129, 0.4)";
            cardStatus.style.background = "linear-gradient(180deg, #ffffff 0%, rgba(16, 185, 129, 0.03) 100%)";
            labelStatus.classList.replace('text-secondary', 'text-success');
            iconStatus.className = "icon-box bg-success bg-opacity-10 text-success";
            textStatus.className = "stat-value text-success d-flex align-items-center mt-2 stat-value-text";
            textStatus.innerHTML = '<i class="bi bi-gear-fill me-2" style="animation: spin-slow 3s linear infinite;"></i> Parsing';

            textSpeedStatus.innerHTML = '<i class="bi bi-arrow-up-short"></i> Stabil';
            textSpeedStatus.className = 'text-success fw-bold mt-1';

            nmeaTerminal.classList.remove('terminal-stopped');
            jsonTerminal.classList.remove('terminal-stopped');
            nmeaCursor.innerText = "> Engine berjalan. Menerima stream...";
            jsonCursor.innerText = "> Decode diproses...";

            intervalId = setInterval(appendData, 600);
        });

        btnStop.addEventListener('click', stopEngine);
    });
</script>
@endpush
