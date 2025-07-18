<?php

namespace App\Services;

use App\Exceptions\ReservationDoesNotExistException;
use App\Exceptions\ReservationExists;
use App\Exceptions\ReservationExistsException;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\ReservedTable;
use App\Models\Table;
use App\Models\User;
use App\Traits\Traits\DateTimeFunctions;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReservationService
{
    use DateTimeFunctions;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    /**
     * Funkce vytvori novou rezervaci pro uziovatele $user v case $from
     *
     * @param User $user Uzivatel, ktery rezervuje
     * @param array $tableIds Pole id stolu k rezervovani
     * @param DateTime $from Cas zacatku rezervace
     *
     * @throws ReservationExistsException v pripade, ze nektery ze stolu z pole $tableIds je v uz v case $from zarezervovan
     */
    public function createReservation(User $user, array $tableIds, DateTime $from): Reservation
    {
        //dd($tableIds);
        $to = $this->endOfDay($from);

        // test, jestli existuji rezervace, ktere konci po zacatku
        // a zacinaji pred koncem nove rezervace
        $reservationCnt = Reservation::where('to','>=', $from)
            ->where('from', '<=', $to)
            ->whereHas('tables', function (Builder $query) use($tableIds) {
                $query->whereIn('tables.id',  $tableIds);
            })->count();

        if($reservationCnt===0)
        {
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'from' => $from,
                'to' => $to,
            ]);

            foreach($tableIds as $id)
            {
                ReservedTable::create([
                    'table_id' => $id,
                    'reservation_id' => $reservation->id,
                ]);
            }
        } else {
            throw new ReservationExistsException();
        }

        return $reservation;
    }

    /**
     * Funkce smaze rezevaci s ID $id uzivatele $user
     *
     * @throws ReservationDoesNotExistException v pripade, ze rezervace uzivatele $user s ID $id neexistuje
     */
    public function delete(User $user, int $id): void
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if(empty($reservation))
            throw new ReservationDoesNotExistException();

        ReservedTable::where('reservation_id', $id)->delete();
        $reservation->delete();
    }

    /**
     * Funkce vrati builder rezervaci uzivatele, bud pouze aktualnich (budoucich) nebo vsech, podle parametru $onlyFuture
     *
     * @param bool $onlyFuture Kdyz je true, funkce vraci jen budouci rezervace, jinak vsechny
     */
    public function getUserReservations(User $user, bool $onlyFuture): Builder
    {
        return Reservation::with('tables')
            ->withSum('tables','number_of_seats')
            ->where('user_id', $user->id)
            ->when($onlyFuture, function(Builder $query){
                $query->where('from', '>=', now()->startOfDay());
            })
            ->orderBy('reservations.from', 'asc');
    }
}
