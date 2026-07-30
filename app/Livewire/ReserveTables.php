<?php

namespace App\Livewire;

use App\Services\ReservationService;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ReserveTables extends Component
{
    #[Reactive]
    public $startDate;

    #[Reactive]
    public $startTime;

    #[Reactive]
    public Collection $tables;

    // whether to show tables list
    public bool $show;

    // whether no tables were found for given date time
    public bool $noTablesFound;

    // IDs of tables to reserve
    public array $tableIds;

    /**
     * Clears reservation form
     */
    #[On('clear-tables-form')]
    public function clearTables()
    {
        $this->tableIds = [];
    }

    /**
     * Process reservation creation request
     *
     * @param ReservationService $service Service to handle reservation logic
     * @return void
     */
    public function save(ReservationService $service): void
    {
        $service->createReservation(Auth::user(), $this->tableIds, new DateTime($this->startDate.' '.$this->startTime));

        session()->flash('success', __('Reservation created successfully'));
        $this->redirect('/reservations');
    }

    /**
     * Mount the component
     *
     * @param array|null $tableIds IDs of tables to reserve
     * @return void
     */
    public function mount(?array $tableIds = []): void
    {
        $this->tableIds = $tableIds;
    }

    /**
     * Render the component view
     *
     * @return View
     */
    public function render(): View
    {
        $this->show = ($this->tables->count() > 0);
        $this->noTablesFound = ($this->tables->count() === 0) && !(empty($this->startTime));
        return view('livewire.reserve-tables');
    }
}
