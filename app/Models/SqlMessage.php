<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlMessage extends Model
{
    // Terhubung ke PostgreSQL (default)
    protected $table = 'ais.message';
    protected $primaryKey = 'message_id';
    public $timestamps = false;
}
