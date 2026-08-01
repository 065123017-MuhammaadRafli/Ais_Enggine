@extends('layouts.main')

@push('styles')
<style>
    .form-control:focus { box-shadow: none; border-color: #3b82f6; }
    .avatar-lg { width: 100px; height: 100px; font-size: 2.5rem; }
</style>
@endpush

@section('content')
    <!-- HEADER PREMIUM DENGAN TOMBOL BACK -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-4" style="border-bottom: 2px solid #f1f5f9;">
        <div class="d-flex align-items-center">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff;">
                <i class="bi bi-person-bounding-box fs-3"></i>
            </div>
            <div>
                <h2 class="fw-bolder mb-1" style="color: #0f172a; letter-spacing: -0.5px; font-size: 1.8rem;">Profil Pengguna</h2>
                <p class="mb-0" style="color: #64748b; font-size: 0.95rem; font-weight: 500;">Kelola informasi pribadi dan kredensial akses Anda.</p>
            </div>
        </div>

        <!-- TOMBOL KEMBALI -->
        <a href="/dashboard" class="btn btn-white border shadow-sm px-4 py-2 rounded-pill fw-medium text-slate-700 d-flex align-items-center" style="transition: all 0.2s; background: white;">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- NOTIFIKASI BERHASIL/GAGAL -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- KARTU KIRI: Ringkasan Profil -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                @php
                    $initials = strtoupper(substr(Auth::user()->name, 0, 2));
                @endphp
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow mx-auto mb-3 avatar-lg"
                     style="background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);">
                    {{ $initials }}
                </div>

                <h4 class="fw-bold text-slate-800 mb-1">{{ Auth::user()->name }}</h4>
                <p class="text-secondary mb-3">{{ Auth::user()->email }}</p>

                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-4 py-2 rounded-pill fw-semibold mb-4">
                    Command Officer
                </span>

                <hr class="text-secondary opacity-25">

                <div class="d-flex justify-content-between text-start mt-3">
                    <span class="text-secondary" style="font-size: 0.85rem;">Status Akun</span>
                    <span class="text-success fw-bold" style="font-size: 0.85rem;"><i class="bi bi-patch-check-fill me-1"></i> Aktif</span>
                </div>
                <div class="d-flex justify-content-between text-start mt-2">
                    <span class="text-secondary" style="font-size: 0.85rem;">Terdaftar Sejak</span>
                    <span class="fw-medium text-slate-700" style="font-size: 0.85rem;">{{ Auth::user()->created_at->format('d M Y') ?? 'Baru saja' }}</span>
                </div>
            </div>
        </div>

        <!-- KARTU KANAN: Form Edit Detail -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-slate-800 border-bottom pb-3 mb-4">Informasi Pribadi</h5>

                <form action="/profile/update" method="POST">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-semibold" style="font-size: 0.85rem;">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-semibold" style="font-size: 0.85rem;">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-slate-800 border-bottom pb-3 mb-4 mt-5">Keamanan Akun</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-semibold" style="font-size: 0.85rem;">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-semibold" style="font-size: 0.85rem;">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-semibold" style="font-size: 0.85rem;">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-medium shadow-sm" style="border-radius: 8px;">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
