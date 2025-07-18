<?php

namespace App\Services;

use App\Models\Table;
use App\Traits\Traits\DateTimeFunctions;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TableService
{
    use DateTimeFunctions;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    /**
     * Funkce vrati vsechny stoly ktere nemaji v danem intervalu rezervaci
     *
     * @param DateTime $from Cas zacatku intervalu
     * @param int $length delka intervalu v hodinach
     */
    public function getAvaliableTables(DateTime $from): Collection
    {
        $to = $this->endOfDay($from);

        return Table::whereDoesntHave('reservations', function (Builder $query) use($from, $to) {
                $query->where('to','>=', $from)
                    ->where('from','<=', $to);
            })
            ->orderBy('number_of_seats', 'asc')
            ->get();
    }

}
