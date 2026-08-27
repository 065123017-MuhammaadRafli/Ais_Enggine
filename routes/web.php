<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\MapController;

// ------------------------------------------------------------------------
// HALAMAN UTAMA
// ------------------------------------------------------------------------
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/tes-rute', function () {
    return "BERHASIL! Rute baru terbaca oleh Laravel.";
});

// ========================================================================
// JALUR API PETA (KITA LETAKKAN DI LUAR AGAR TIDAK DIBLOKIR LOGIN)
// ========================================================================
// Jalur MongoDB
Route::get('/api/ships-data', [MapController::class, 'getLiveShips']);

// Jalur PostgreSQL
Route::get('/sql-ships-data', [MapController::class, 'getSqlShips']);

// ------------------------------------------------------------------------
// AUTENTIKASI (GUEST)
// ------------------------------------------------------------------------
Route::middleware('guest')->group(function () {

    Route::get('/login', function () { return view('auth.login'); })->name('login');
    Route::get('/register', function () { return view('auth.register'); })->name('register');
    Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('rememberMe'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    });

    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:4', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    });
});

// ------------------------------------------------------------------------
// SISTEM UTAMA (AUTH) - Proteksi: Hanya bisa diakses jika sudah login
// ------------------------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Proses Keluar
    Route::get('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Core System
    Route::get('/dashboard', function () { return view('dashboard.index'); })->name('dashboard');
    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/geospatial', function() { return view('geospatial.index'); })->name('geospatial');

    // Data Ingestion
    Route::get('/import', function() { return view('import.index'); })->name('import');
    Route::get('/parser', function() { return view('parser.index'); })->name('parser');

    // Database Testing
    Route::get('/storage', function() { return view('storage.index'); })->name('storage');
    Route::get('/benchmark', function() { return view('benchmark.index'); })->name('benchmark');

    // --- Menu Profil & Pengaturan Sistem ---
    Route::get('/profilee', function() { return view('profilee.index'); })->name('profilee');
    Route::get('/settings', function() { return view('settings.index'); })->name('settings');

    // --- Proses Update Pengaturan ---
    Route::post('/settings/update', function (Request $request) {
        // Ini adalah simulasi penyimpanan. Nantinya bisa disambungkan ke database.
        return back()->with('success', 'Preferensi sistem berhasil disimpan dan diterapkan!');
    });

    // --- Proses Update Profil ---
    Route::post('/profile/update', function (Request $request) {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'min:4', 'confirmed'],
        ]);

        // Jika user mengisi password baru, cek password lamanya dulu
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }
            $user->password = Hash::make($request->password);
        }

        // Update nama dan email
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Data profil berhasil diperbarui!');
    });
});
