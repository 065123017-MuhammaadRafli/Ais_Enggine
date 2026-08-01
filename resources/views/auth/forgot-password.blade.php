@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <!-- Tombol Back -->
    <a href="/login" class="d-inline-flex align-items-center text-secondary text-decoration-none mb-4 fw-medium" style="font-size: 0.85rem; transition: color 0.2s;">
        <i class="bi bi-arrow-left me-2"></i> Kembali ke Login
    </a>

    <div class="mb-4 pb-2">
        <div class="bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle mb-3 text-primary" style="width: 50px; height: 50px;">
            <i class="bi bi-key fs-4"></i>
        </div>
        <h3 class="fw-bolder text-slate-800 mb-2">Lupa Password?</h3>
        <p class="text-secondary" style="font-size: 0.9rem; line-height: 1.6;">
            Jangan khawatir. Masukkan email yang terdaftar, dan kami akan mengirimkan instruksi untuk mereset kata sandi Anda.
        </p>
    </div>

    <form action="#" method="POST">

        <div class="mb-4">
            <label class="form-label">Alamat Email</label>
            <input type="email" class="form-control form-control-lg" style="font-size: 0.95rem;" placeholder="nama@perusahaan.com" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-4">Kirim Link Reset Password</button>

    </form>
@endsection
