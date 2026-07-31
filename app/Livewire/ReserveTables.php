<?php

namespace App\Livewire;

use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

    public int $durationInMinutes = 90;

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

    #[On('reservation-duration-updated')]
    public function updateDurationInMinutes(int $durationInMinutes): void
    {
        $this->durationInMinutes = $durationInMinutes;
    }

    /**
     * Process reservation creation request
     *
     * @param ReservationService $service Service to handle reservation logic
     * @return void
     */
    public function save(ReservationService $service): void
    {
        $this->validate();

        $service->createReservation(
            Auth::user(),
            $this->tableIds,
            Carbon::createFromFormat('d.m.Y H:i', $this->startDate.' '.$this->startTime),
            $this->durationInMinutes
        );

        session()->flash('success', __('Reservation created successfully'));
        $this->redirect('/reservations');
    }

    protected function rules(): array
    {
        return [
            'startDate' => [
                'required',
                Rule::date()->format('d.m.Y')->afterOrEqual(today()),
            ],
            'startTime' => ['required', 'date_format:H:i'],
            'durationInMinutes' => ['required', 'integer', 'in:90,120,180'],
            'tableIds' => ['required', 'array', 'min:1'],
            'tableIds.*' => ['bail', 'integer', 'distinct', 'exists:tables,id'],
        ];
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
