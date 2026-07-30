<?php

namespace App\Services;

use App\Exceptions\InvalidTablesException;
use App\Exceptions\ReservationExistsException;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * Creates new reservation for $user in time $from containing tables $tableIds
     *
     * @param User $user User instance
     * @param array $tableIds IDs of tables to be reserved
     * @param DateTime $from Reservation start time
     * @return Reservation Created reservation instance
     * @throws ReservationExistsException in case any of tabnles in $tableIds is already reserved
     */
    public function createReservation(User $user, array $tableIds, DateTime $from): Reservation
    {
        $to = Carbon::parse($from)->endOfDay();

        // validate table IDs
        $cntTable = Table::whereIn('id', $tableIds)->lockForUpdate()->count();
        if($cntTable !== count($tableIds))
        {
            throw new InvalidTablesException('One or more table IDs are invalid.');
        }

        // test, whether there are any reservations ending after start
        // and starting before end of newly creating reservation
        $reservationCnt = Reservation::where('to','>=', $from)
            ->where('from', '<=', $to)
            ->whereHas('tables', function (Builder $query) use($tableIds) {
                $query->whereIn('tables.id',  $tableIds);
            })->count();

        if($reservationCnt === 0)
        {
            // atomically creates reservation and attach reserved tables
            $reservation = DB::transaction(function () use ($user, $from, $to, $tableIds) {
                $reservation = Reservation::create([
                    'user_id' => $user->id,
                    'from' => $from,
                    'to' => $to,
                ]);

                $reservation->tables()->attach($tableIds);
                return $reservation;
            });

        } else {
            throw new ReservationExistsException();
        }

        return $reservation;
    }

    /**
     * Function deletes reservation $reservation and frees all reserved tables.
     *
     * @param Reservation $reservation Reservation instance.
     */
    public function delete(Reservation $reservation): void
    {
        // atomically detaches all tables and deletes reservation
        DB::transaction(function () use ($reservation) {
            $reservation->tables()->detach();
            $reservation->delete();
        });
    }

    /**
     * Creates builder for user reservations. All or only future reservations, depending on $onlyFuture.
     *
     * @param User $user User instance.
     * @param bool $onlyFuture If true, builder contains only furture reservations.
     * @return Builder Builder for user's reservations.
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
