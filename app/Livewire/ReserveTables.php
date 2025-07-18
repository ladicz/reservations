<?php

namespace App\Livewire;

use App\Services\ReservationService;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ReserveTables extends Component
{
    #[Reactive]
    public $startDate;

    #[Reactive]
    public $startTime;

    #[Reactive]
    public Collection $tables;

    public bool $show;

    public bool $noTablesFound;

    public array $tableIds;

    /**
     * Funkce vycisti rezervacni formular
     */
    #[On('clear-tables-form')]
    public function clearTables()
    {
        $this->tableIds = [];
    }

    /**
     * Zpracovani pozadavku vytvoreni rezervace
     */
    public function save(ReservationService $service)
    {
        $service->createReservation(Auth::user(), $this->tableIds, new DateTime($this->startDate.' '.$this->startTime));

        session()->flash('success', __('Rezervace vytvořena'));
        $this->redirect('/reservations');
    }

    public function mount(?array $tableIds = [])
    {
        $this->tableIds = $tableIds;
    }

    public function render()
    {
        $this->show = ($this->tables->count() > 0);
        $this->noTablesFound = ($this->tables->count() === 0) && !(empty($this->startTime));
        return view('livewire.reserve-tables');
    }
}
