<?php

namespace App\Livewire;

use App\Services\TableService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TablesIndex extends Component
{
    // kolekce dostupnych stolu
    public Collection $tables;

    // pole casu rezervaci pro selectbox
    public array $times;

    //datum a cas rezervace
    public ?string $startDate;
    public ?string $startTime;

    /**
     * Funkce vytvori pole rezervacnich casu podle vstupnich stringu $beginTime a $endTime ve formatu hh:mm
     * vstup pro selectbox
     */
    protected function reservationTimes(string $beginTime, string $endTime): array
    {
        $times = [''];
        $startTime = Carbon::createFromTimeString($beginTime);
        $times[] = $startTime->toTimeString('minutes');

        $newTime = $startTime;

        $i = 0;
        while(($newTime->toTimeString('minutes') !== $endTime) && $i < 60)
        {
            $newTime = $newTime->addMinutes(30);
            $times[] = $newTime->toTimeString('minutes');
            $i++;
        }

        return $times;
    }

    public function updatedStartTime(TableService $service,$value)
    {
        if(empty($this->startDate))
            $this->startDate = now()->format('d.m.Y');
        $this->tables = $service->getAvaliableTables((new DateTime($this->startDate.' '.$value)));

        // pro pro prihlaseneho uzivatele odeslu event pro smazani rezervacniho formu
        if(Auth::check())
            $this->dispatch('clear-tables-form');
    }

    public function updatedStartDate(TableService $service,$value)
    {
        if(!empty($this->startTime))
            $this->tables = $service->getAvaliableTables((new DateTime($value.' '.$this->startTime)));

        // pro pro prihlaseneho uzivatele odeslu event pro smazani rezervacniho formu
        if(Auth::check())
            $this->dispatch('clear-tables-form');
    }

    public function mount(?string $startTime = null)
    {
        $this->startTime = $startTime;
        $this->tables = new Collection();
        $this->times = $this->reservationTimes('11:00', '22:00');
    }

    public function render()
    {
        return view('livewire.tables-index',);
    }
}
