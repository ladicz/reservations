<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Datepicker extends Component
{
    public string $modelName;
    public $datePickerMinDate;
    public $datePickerFormat;
    /**
     * Create a new component instance.
     */
    public function __construct(string $modelName)
    {
        $this->modelName = $modelName;
        $this->datePickerMinDate = now()->format('d.m.Y');
        $this->datePickerFormat = 'dd.mm.yyyy';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.datepicker');
    }
}
