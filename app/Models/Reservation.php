<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'from', 'to'];

    protected function casts()
    {
        return [
            'from' => 'datetime'
        ];
    }

    // relationships
    public function tables(): BelongsToMany
    {
        return $this->belongsToMany(Table::class, 'reserved_tables');
    }
}
