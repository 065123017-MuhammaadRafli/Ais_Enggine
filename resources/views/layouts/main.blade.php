<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIS Engine - Command Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        /* Tipografi & Background Global */
        body { font-family: 'Inter', -apple-system, sans-serif; background-color: #f8fafc; overflow-x: hidden; color: #334155; }

        /* Premium Sidebar - Bersih, Hanya untuk Navigasi */
        .sidebar {
            background-color: #0f172a;
            min-height: 100vh;
            padding-top: 0;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        /* --- Styling Khusus Bagian Brand (Logo) --- */
        .sidebar-brand-container {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .brand-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
            flex-shrink: 0;
        }
        .brand-text-wrapper {
            margin-left: 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .brand-title { color: #ffffff; font-size: 1.2rem; font-weight: 800; letter-spacing: 0.5px; margin: 0; line-height: 1.1; }
        .brand-subtitle { color: #94a3b8; font-size: 0.75rem; font-weight: 500; margin-top: 4px; letter-spacing: 0.3px; }

        /* Menu Navigasi */
        .nav-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin: 1.5rem 0 0.5rem 1.5rem; }
        .nav-link { color: #94a3b8; font-weight: 500; padding: 0.6rem 1.25rem; border-radius: 8px; margin: 0.2rem 1rem; transition: all 0.2s ease; }
        .nav-link:hover, .nav-link.active { color: #38bdf8; background-color: rgba(56, 189, 248, 0.1); }
        .nav-link i { font-size: 1.1rem; margin-right: 10px; }

        /* --- PROFIL USER DI BAWAH SIDEBAR --- */
        .sidebar-user-container {
            margin-top: auto;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            cursor: pointer;
            transition: background 0.2s;
        }
        .sidebar-user-container:hover { background-color: rgba(255, 255, 255, 0.03); }
        .sidebar-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: bold; font-size: 0.9rem; flex-shrink: 0;
        }
        .sidebar-user-info { margin-left: 12px; overflow: hidden; }
        .sidebar-user-name { color: #f8fafc; font-size: 0.85rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { color: #64748b; font-size: 0.75rem; }
        .dropdown-menu-dark .dropdown-item:hover { background-color: rgba(255,255,255,0.1); }

        /* --- TOP HEADER (Khusus Dashboard) --- */
        .top-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 5;
        }
        .profile-dropdown-top .dropdown-toggle::after { display: none; }
        .profile-dropdown-top .dropdown-menu { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border: 1px solid #f1f5f9; padding: 0.5rem; }
        .profile-dropdown-top .dropdown-item { border-radius: 8px; padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 500; color: #475569; transition: all 0.2s; }
        .profile-dropdown-top .dropdown-item:hover { background-color: #f8fafc; color: #0f172a; }
        .profile-dropdown-top .dropdown-item.text-danger:hover { background-color: #fef2f2; color: #dc2626 !important; }

        /* Scrollbar Halus */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>

    @stack('styles')
</head>
<body>

@php
    // Siapkan data user agar bisa dipakai di Sidebar dan Header
    $name = Auth::check() ? Auth::user()->name : 'Muhammad Raflii';
    $initials = strtoupper(substr($name, 0, 2));
    $email = Auth::check() ? Auth::user()->email : 'admin@ais-engine.com';
@endphp

<div class="container-fluid p-0">
    <div class="d-flex">

        <!-- KIRI: SIDEBAR -->
        <nav class="sidebar col-auto" style="width: 260px;">

            <!-- LOGO -->
            <div class="sidebar-brand-container">
                <div class="brand-icon-wrapper">
                    <i class="bi bi-radar"></i>
                </div>
                <div class="brand-text-wrapper">
                    <h3 class="brand-title">AIS Engine</h3>
                    <span class="brand-subtitle">Magang Project v1.0</span>
                </div>
            </div>

           <!-- MENU -->
           <ul class="nav flex-column w-100">
                <div class="nav-label">Data Ingestion</div>
                <a class="nav-link {{ Request::is('import') ? 'active' : '' }}" href="{{ route('import') ?? '/import' }}">
                    <i class="bi bi-cloud-arrow-down"></i> Import Data Raw
                </a>
                <a class="nav-link {{ Request::is('parser') ? 'active' : '' }}" href="{{ route('parser') ?? '/parser' }}">
                    <i class="bi bi-code-slash"></i> Parser & Decoder
                </a>

                <div class="nav-label">Core System</div>
                <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') ?? '/dashboard' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a class="nav-link {{ Request::is('map') ? 'active' : '' }}" href="{{ route('map') ?? '/map' }}">
                    <i class="bi bi-globe-americas"></i> Geospatial Map
                </a>

                <div class="nav-label">System Testing</div>
                <a class="nav-link {{ Request::is('storage') ? 'active' : '' }}" href="{{ route('storage') ?? '/storage' }}">
                    <i class="bi bi-database"></i> SQL vs NoSQL
                </a>
                <a class="nav-link {{ Request::is('benchmark') ? 'active' : '' }}" href="{{ route('benchmark') ?? '/benchmark' }}">
                    <i class="bi bi-speedometer2"></i> Benchmark
                </a>
            </ul>

            <!-- PROFIL KIRI BAWAH (Mode 1: Selalu Muncul) -->
            <div class="sidebar-user-container dropdown dropup">
                <div class="d-flex align-items-center w-100" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="sidebar-user-avatar">{{ $initials }}</div>
                    <div class="sidebar-user-info flex-grow-1">
                        <div class="sidebar-user-name">{{ $name }}</div>
                        <div class="sidebar-user-role">Command Officer</div>
                    </div>
                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                </div>

                <!-- Isi Dropup Menu -->
                <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-0 mb-2 ms-3" style="width: 210px; background-color: #1e293b; border-radius: 12px;">
                    <li><h6 class="dropdown-header text-light">{{ $email }}</h6></li>
                    <li><hr class="dropdown-divider border-secondary"></li>
                    <li><a class="dropdown-item text-light py-2" href="/profilee"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                    <li><a class="dropdown-item text-light py-2" href="/settings"><i class="bi bi-gear me-2"></i> Pengaturan Sistem</a></li>
                    <li><hr class="dropdown-divider border-secondary"></li>
                    <li><a class="dropdown-item text-danger fw-bold py-2" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Keluar Sistem</a></li>
                </ul>
            </div>
        </nav>

        <!-- KANAN: KONTEN UTAMA -->
        <div class="flex-grow-1 d-flex flex-column" style="max-height: 100vh; overflow: hidden;">

            <!-- TOP NAVBAR KANAN ATAS (Mode 2: HANYA MUNCUL DI DASHBOARD) -->
            @if(Request::is('dashboard'))
                <header class="top-header">
                    <!-- Search Bar / Tanggal (Kiri) -->
                    <div class="d-flex align-items-center text-secondary" style="font-size: 0.85rem;">
                        <i class="bi bi-calendar3 me-2"></i>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>

                    <!-- Profil Dropdown (Kanan Atas) -->
                    <div class="dropdown profile-dropdown-top">
                        <div class="d-flex align-items-center dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <div class="text-end me-3 d-none d-md-block">
                                <div class="fw-bold text-slate-800" style="font-size: 0.85rem; line-height: 1.2;">
                                    {{ $name }}
                                </div>
                                <small class="text-secondary" style="font-size: 0.75rem;">Command Officer</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%); font-size: 0.9rem;">
                                {{ $initials }}
                            </div>
                        </div>

                        <!-- Isi Dropdown Kanan Atas -->
                        <ul class="dropdown-menu dropdown-menu-end mt-2" style="min-width: 220px;">
                            <li class="px-3 py-2 mb-1 border-bottom">
                                <div class="fw-bold text-slate-800" style="font-size: 0.85rem;">{{ $name }}</div>
                                <div class="text-secondary text-truncate" style="font-size: 0.75rem;">{{ $email }}</div>
                            </li>
                            <li><a class="dropdown-item mt-1" href="/profilee"><i class="bi bi-person me-2 text-secondary"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item" href="/settings"><i class="bi bi-gear me-2 text-secondary"></i> Pengaturan Sistem</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item text-danger fw-bold" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Keluar Sistem</a></li>
                        </ul>
                    </div>
                </header>
            @endif

            <!-- AREA KONTEN (Dashboard, Peta, dll) -->
            <main class="p-4 flex-grow-1" style="overflow-y: auto;">
                @yield('content')
            </main>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

@stack('scripts')
</body>
</html>
