<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AIS Engine Command Center</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: #f8fafc; /* Warna background abu-abu sangat muda */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Container Card Utama */
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05), 0 10px 15px -5px rgba(0,0,0,0.02);
            overflow: hidden;
            width: 100%;
            max-width: 960px;
        }

        /* Panel Kiri (Biru Gelap) */
        .left-panel {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Logo Wrap */
        .logo-wrap {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #38bdf8 0%, #3b82f6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-right: 12px;
        }

        /* Badge Program Magang (Bawah Kiri) */
        .brin-badge {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
        }

        /* Panel Kanan (Form Login) */
        .right-panel {
            padding: 4rem;
        }

        /* Custom Input Group (Kotak Input Abu-abu Lembut) */
        .custom-input-group {
            background-color: #f1f5f9;
            border-radius: 10px;
            padding: 4px;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .custom-input-group:focus-within {
            background-color: #ffffff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }

        .custom-input-group .input-group-text {
            background: transparent;
            border: none;
            padding-left: 1rem;
            color: #64748b;
        }

        .custom-input-group .form-control {
            background: transparent;
            border: none;
            box-shadow: none;
            font-size: 0.95rem;
            color: #334155;
            padding-left: 10px;
        }

        .custom-input-group .form-control::placeholder {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Tombol Login */
        .btn-login {
            background-color: #0f172a;
            color: white;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 10px;
            border: none;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background-color: #1e293b;
        }
    </style>
</head>
<body>

<div class="container px-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

            <div class="login-card row g-0">

                <!-- BAGIAN KIRI (DARK PANEL) -->
                <div class="col-md-5 left-panel d-none d-md-flex">
                    <div>
                        <!-- Logo & Brand -->
                        <div class="d-flex align-items-center mb-5">
                            <div class="logo-wrap">
                                <i class="bi bi-radar"></i>
                            </div>
                            <span class="text-white fw-bold fs-5 tracking-wide">AIS Engine</span>
                        </div>

                        <!-- Teks Marketing (Diperbarui jadi simpel) -->
                        <h1 class="text-white fw-bolder mb-3 mt-4" style="font-size: 2.2rem; letter-spacing: -0.5px;">AIS Command Center</h1>
                        <p class="text-light" style="font-size: 0.95rem; line-height: 1.6; opacity: 0.85;">
                            Platform terintegrasi untuk pemrosesan dan pemantauan lalu lintas kapal secara real-time.
                        </p>
                    </div>

                    <!-- Badge Bawah -->
                    <div class="brin-badge mt-5">
                        <i class="bi bi-shield-check text-info fs-3 me-3"></i>
                        <div>
                            <div class="text-white fw-bold" style="font-size: 0.85rem;">Program Magang MBKM</div>
                            <div class="text-light" style="font-size: 0.75rem; opacity: 0.7;">Pusat Riset Teknologi Satelit (BRIN)</div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN KANAN (FORM PANEL) -->
                <div class="col-md-7 right-panel">

                    <!-- Sambutan (Diperbarui) -->
                    <h2 class="fw-bolder text-slate-800 mb-1" style="color: #0f172a;">Selamat Datang</h2>
                    <p class="text-secondary mb-5" style="font-size: 0.95rem;">Silakan masuk ke akun Anda untuk melanjutkan.</p>

                    <!-- Form Login -->
                    <form method="POST" action="{{ route('login') ?? '#' }}">
                        @csrf

                        <!-- Input Email -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Alamat Email</label>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required autofocus>
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold mb-0" style="font-size: 0.85rem; color: #475569;">Password</label>
                                <a href="#" class="text-decoration-none" style="font-size: 0.8rem; font-weight: 600; color: #3b82f6;">Lupa password?</a>
                            </div>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <!-- Checkbox Remember Me (Diperbarui) -->
                        <div class="mb-5 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" style="cursor: pointer;">
                            <label class="form-check-label text-secondary" for="remember" style="font-size: 0.85rem; cursor: pointer;">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" class="btn btn-login w-100 shadow-sm">
                            Masuk ke Sistem
                        </button>

                        <!-- Link Daftar -->
                        <div class="text-center mt-4" style="font-size: 0.85rem; color: #64748b;">
                            Belum memiliki akses?
                            <a href="{{ route('register') ?? '/register' }}" class="text-decoration-none fw-bold" style="color: #3b82f6;">Buat akun baru</a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
