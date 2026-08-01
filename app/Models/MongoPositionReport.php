<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MongoPositionReport extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'type_1_2_3';

    // Tambahkan baris ini agar Laravel mengizinkan insert data
    protected $guarded = [];
}
