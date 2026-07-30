<?php

namespace App\Services;

use App\Models\Table;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TableService
{
    /**
     * Creates collection of tables available (have no reservations) in given time interval
     *
     * @param DateTime $from Interval start time
     */
    public function getAvaliableTables(DateTime $from): Collection
    {
        $to = Carbon::parse($from)->endOfDay();

        return Table::whereDoesntHave('reservations', function (Builder $query) use($from, $to) {
                $query->where('to','>=', $from)
                    ->where('from','<=', $to);
            })
            ->orderBy('number_of_seats', 'asc')
            ->get();
    }

}
