<?php

namespace App\Services;

use App\Models\Table;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TableService
{
    /**
     * Creates collection of tables available (have no reservations) in given time interval
     *
    * @param CarbonInterface $from Interval start time
     * @param int $durationInMinutes Interval duration in minutes
     */
    public function getAvaliableTables(CarbonInterface $from, int $durationInMinutes): Collection
    {
        $to = $from->copy()->addMinutes($durationInMinutes);

        return Table::whereDoesntHave('reservations', function (Builder $query) use ($from, $to) {
                $query->where('to', '>', $from)
                    ->where('from', '<', $to);
            })
            ->orderBy('number_of_seats', 'asc')
            ->get();
    }

}
