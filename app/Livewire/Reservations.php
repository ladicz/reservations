<?php

namespace App\Livewire;

use App\Services\ReservationService;
use Database\Factories\ReservationFactory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Reservations extends Component
{
    use WithPagination;
    // ridi prepinani zobrazeni aktualnich a vsech rezervaci
    public bool $onlyFuture = true;
    public string $dateFormat = 'd.m.Y H:i';

    /**
     * Zpracovani pozdavku prepnuti zobraazeni rezervaci
     */
    public function toggleFuture()
    {
        $this->onlyFuture = ($this->onlyFuture ? false : true);
        $this->resetPage();
    }

    /**
     * Zpracovani pozdavku smazani rezervace
     */
    public function delete(ReservationService $service, int $reservationId)
    {
        $service->delete(Auth::user(), $reservationId);
        $this->resetPage();
    }

    public function render(ReservationService $service)
    {
        return view('livewire.reservations', [
            'reservations' => $service->getUserReservations(Auth::user(), $this->onlyFuture)->paginate(5)
        ]);
    }
}
