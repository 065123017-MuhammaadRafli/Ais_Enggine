<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AisMessage extends Model
{
    protected $connection = 'mongodb';

    // Pastikan namanya sesuai dengan collection yang berisi 100 data tadi
    protected $collection = 'data_uji_kapal';

    protected $guarded = [];
}
