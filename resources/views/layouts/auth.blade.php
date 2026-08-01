<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - AIS Engine</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }

        /* Premium Card & Shadow */
        .auth-card {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        /* Left Sidebar Dark Tech */
        .auth-sidebar {
            background: linear-gradient(145deg, #0f172a, #1e293b);
            color: white;
            position: relative;
        }
        .auth-sidebar::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at top right, rgba(14, 165, 233, 0.15), transparent 50%);
        }

        /* Form Styling */
        .form-label { font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.4rem; }
        .form-control { border-radius: 10px; padding: 0.8rem 1.2rem; border-color: #e2e8f0; font-size: 0.95rem; background-color: #f8fafc; transition: all 0.2s; }
        .form-control:focus { border-color: #0ea5e9; background-color: #ffffff; box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); }

        .btn-primary { background-color: #0f172a; border: none; border-radius: 10px; padding: 0.8rem 1.2rem; font-weight: 600; transition: all 0.2s; }
        .btn-primary:hover { background-color: #1e293b; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2); }

        .text-primary-custom { color: #0ea5e9; text-decoration: none; font-weight: 600; }
        .text-primary-custom:hover { color: #0284c7; text-decoration: underline; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card auth-card flex-row">

                    <!-- KIRI: Branding Section -->
                    <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 auth-sidebar">
                        <div class="z-1">
                            <div class="d-flex align-items-center mb-5">
                                <div class="rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 42px; height: 42px; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);">
                                    <i class="bi bi-radar text-white fs-5"></i>
                                </div>
                                <h5 class="fw-bolder mb-0 text-white" style="letter-spacing: 0.5px;">AIS Engine</h5>
                            </div>
                            <h1 class="fw-bolder mb-3" style="font-size: 2.2rem; line-height: 1.2;">Marine<br>Command Center</h1>
                            <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">
                                Platform pemantauan, dekoder, dan visualisasi data satelit AIS paling mutakhir untuk manajemen armada kelautan.
                            </p>
                        </div>
                        <div class="z-1 mt-5">
                            <div class="d-flex align-items-center p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); backdrop-filter: blur(10px);">
                                <i class="bi bi-shield-check fs-2 me-3" style="color: #38bdf8;"></i>
                                <div>
                                    <div class="fw-bold text-white mb-1" style="font-size: 0.85rem;">Secure Enterprise Access</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">Protokol Enkripsi Ujung-ke-Ujung</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KANAN: Form Section -->
                    <div class="col-lg-7 col-12 bg-white d-flex align-items-center">
                        <div class="w-100 p-4 p-md-5 p-xl-5" style="max-width: 520px; margin: 0 auto;">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
