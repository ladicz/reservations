<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservedTable extends Model
{
    /** @use HasFactory<\Database\Factories\ReservedTableFactory> */
    use HasFactory;

    protected $fillable = ['reservation_id', 'table_id'];
}
