<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; // Wajib pakai ini khusus untuk MongoDB

class Kapal extends Model
{
    // Arahkan ke nama koneksi MongoDB di file .env
    protected $connection = 'mongodb';

    // Sesuaikan dengan nama collection yang kamu buat semalam di Atlas
    protected $collection = 'type_1_2_3';

    // Buka gerbang agar semua field bisa diambil
    protected $guarded = [];
}
