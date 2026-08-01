@extends('layouts.main')

@section('content')
    <!-- HEADER PREMIUM DENGAN TOMBOL BACK -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-4" style="border-bottom: 2px solid #f1f5f9;">
        <div class="d-flex align-items-center">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, #475569 0%, #1e293b 100%); color: #ffffff;">
                <i class="bi bi-sliders fs-3"></i>
            </div>
            <div>
                <h2 class="fw-bolder mb-1" style="color: #0f172a; letter-spacing: -0.5px; font-size: 1.8rem;">Pengaturan Sistem</h2>
                <p class="mb-0" style="color: #64748b; font-size: 0.95rem; font-weight: 500;">Sesuaikan preferensi tampilan dan notifikasi dashboard.</p>
            </div>
        </div>

        <!-- TOMBOL KEMBALI -->
        <a href="/dashboard" class="btn btn-white border shadow-sm px-4 py-2 rounded-pill fw-medium text-slate-700 d-flex align-items-center" style="transition: all 0.2s; background: white;">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- NOTIFIKASI BERHASIL -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8 col-lg-10 mx-auto">

            <!-- FORM PENGATURAN -->
            <form action="/settings/update" method="POST">
                @csrf

                <!-- SEGMEN 1: TAMPILAN & ANTARMUKA -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-slate-800 mb-4"><i class="bi bi-palette text-primary me-2"></i>Tampilan & Antarmuka</h5>

                    <div class="d-flex justify-content-between align-items-center mb-4 pb-4 border-bottom">
                        <div>
                            <h6 class="mb-1 fw-semibold text-slate-700">Tema Gelap (Dark Mode)</h6>
                            <small class="text-secondary">Ubah seluruh antarmuka sistem menjadi palet warna gelap.</small>
                        </div>
                        <div class="form-check form-switch fs-3 mb-0">
                            <input class="form-check-input" type="checkbox" name="dark_mode" role="switch" style="cursor: pointer;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 fw-semibold text-slate-700">Animasi Peta Real-time</h6>
                            <small class="text-secondary">Render pergerakan kapal (Geospatial) dengan animasi transisi 60fps.</small>
                        </div>
                        <div class="form-check form-switch fs-3 mb-0">
                            <input class="form-check-input" type="checkbox" name="map_animation" role="switch" checked style="cursor: pointer;">
                        </div>
                    </div>
                </div>

                <!-- SEGMEN 2: NOTIFIKASI -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-slate-800 mb-4"><i class="bi bi-bell text-warning me-2"></i>Notifikasi & Peringatan</h5>

                    <div class="d-flex justify-content-between align-items-center mb-4 pb-4 border-bottom">
                        <div>
                            <h6 class="mb-1 fw-semibold text-slate-700">Peringatan Anomali AIS</h6>
                            <small class="text-secondary">Kirim email otomatis jika satelit mendeteksi kapal mematikan transponder.</small>
                        </div>
                        <div class="form-check form-switch fs-3 mb-0">
                            <input class="form-check-input" type="checkbox" name="email_alert" role="switch" checked style="cursor: pointer;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 pb-4 border-bottom">
                        <div>
                            <h6 class="mb-1 fw-semibold text-slate-700">Laporan Mingguan (PDF)</h6>
                            <small class="text-secondary">Kirim rekapitulasi data parsing dan statistik database ke email Anda setiap Senin.</small>
                        </div>
                        <div class="form-check form-switch fs-3 mb-0">
                            <input class="form-check-input" type="checkbox" name="weekly_report" role="switch" style="cursor: pointer;">
                        </div>
                    </div>

                    <div class="alert alert-light border border-primary border-opacity-25 bg-primary bg-opacity-10 d-flex align-items-center mb-0" role="alert">
                        <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                        <div>
                            <small class="fw-medium text-slate-700">Notifikasi akan dikirimkan ke:</small><br>
                            <small class="text-primary fw-bold">{{ Auth::user()->email }}</small>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN -->
                <div class="d-flex justify-content-end mb-5">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-medium shadow-sm" style="border-radius: 8px;">
                        <i class="bi bi-save me-2"></i> Simpan Pengaturan
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
