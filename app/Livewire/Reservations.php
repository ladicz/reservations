<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Reservations extends Component
{
    use WithPagination;

    // toggles view of all and only future serervations
    public bool $onlyFuture = true;
    // date time for for listing of resrvations
    public string $dateFormat = 'd.m.Y H:i';

    /**
     * Action toggling view of actual (future) and all reservations in the list
     *
     * @return void
     */
    public function toggleFuture(): void
    {
        $this->onlyFuture = ($this->onlyFuture ? false : true);
        $this->resetPage();
    }

    /**
     * Action deletes particular reservation with $reservationId
     *
     * @param ReservationService $service Service to handle reservation logic
     * @param Reservation $reservation Reservation instance to delete
     * @return void
     */
    public function delete(ReservationService $service, Reservation $reservation): void
    {
        // check authorization
        if (!Gate::allows('delete', $reservation))
        {
            session()->flash('fail', __('You are not authorized to delete this reservation.'));
            return;
        }

        $service->delete($reservation);
        // paginator reset
        $this->resetPage();
    }

    /**
     * Render the component view
     *
     * @param ReservationService $service Service to handle reservation logic
     * @return View
     */
    public function render(ReservationService $service): View
    {
        return view('livewire.reservations', [
            'reservations' => $service->getUserReservations(Auth::user(), $this->onlyFuture)->paginate(5)
        ]);
    }
}
