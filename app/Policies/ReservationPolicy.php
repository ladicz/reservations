<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    /**
     * Ověří, zda má uživatel právo smazat rezervaci.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        // Uživatel může smazat pouze svou vlastní rezervaci
        return $reservation->user_id === $user->id;
    }
}
