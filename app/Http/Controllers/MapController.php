<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index()
    {
        return view('map.index');
    }

    // ==========================================
    // 1. MENGAMBIL DATA DARI MONGODB (NoSQL)
    // ==========================================
    public function getLiveShips()
    {
        // Mengambil data dari tabel/koleksi data_uji_kapal di MongoDB
        $data = DB::connection('mongodb')
                    ->table('data_uji_kapal')
                    ->orderBy('id', 'asc')
                    ->get();

        return response()->json($data);
    }

    // ==========================================
    // 2. MENGAMBIL DATA DARI POSTGRESQL (SAFE MULTI-TYPE)
    // ==========================================
    public function getSqlShips()
    {
        // Siapkan penampung kosong untuk menggabungkan semua data
        $all_positions = collect([]);

        // 1. AMBIL TYPE 1, 2, 3 (Kapal Class A)
        try {
            $type_1_2_3 = DB::connection('pgsql')
                ->table('ais.type_1_2_3_position_report_class_a')
                ->select(
                    'mmsi', // Mengambil mmsi langsung dari tabel
                    'latitude',
                    'longitude',
                    'speed_over_ground',
                    'true_heading',
                    // Label diubah menjadi nama tabel asli
                    DB::raw("'type_1_2_3_position_report_class_a' as collection_label")
                )
                ->limit(50)->get();

            $all_positions = $all_positions->concat($type_1_2_3);
        } catch (\Exception $e) {
            // Abaikan jika error (misal tabel belum siap), lanjut ke tipe berikutnya
        }

        // 2. AMBIL TYPE 18 (Kapal Class B)
        try {
            $type_18 = DB::connection('pgsql')
                ->table('ais.type_18_standar_class_b_position_report')
                ->select(
                    'mmsi',
                    'latitude',
                    'longitude',
                    'speed_over_ground',
                    'true_heading',
                    // Label diubah menjadi nama tabel asli
                    DB::raw("'type_18_standar_class_b_position_report' as collection_label")
                )
                ->limit(50)->get();

            $all_positions = $all_positions->concat($type_18);
        } catch (\Exception $e) {
            // Abaikan jika error
        }

        // 3. AMBIL TYPE 4 & 11 (Base Station / Tower)
        try {
            $type_4_11 = DB::connection('pgsql')
                ->table('ais.type_4_11_base_station_report')
                ->select(
                    'mmsi',
                    'latitude',
                    'longitude',
                    DB::raw("0 as speed_over_ground"), // Base station tidak punya kecepatan
                    DB::raw("0 as true_heading"),
                    // Label diubah menjadi nama tabel asli
                    DB::raw("'type_4_11_base_station_report' as collection_label")
                )
                ->limit(20)->get();

            $all_positions = $all_positions->concat($type_4_11);
        } catch (\Exception $e) {
            // Abaikan jika error
        }

        // Kembalikan semua data yang berhasil diambil dalam bentuk JSON
        return response()->json([
            'sumber_data' => 'PostgreSQL (Safe Multi-Type Query)',
            'total_data'  => $all_positions->count(),
            'data'        => $all_positions
        ]);
    }
}
