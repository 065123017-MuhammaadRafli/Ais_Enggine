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

    public function getLiveShips()
    {
        // Gunakan table() bukan collection()
        $data = DB::connection('mongodb')->table('data_uji_kapal')->get();

        return response()->json($data);
    }
}
