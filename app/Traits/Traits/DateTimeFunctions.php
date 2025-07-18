<?php

namespace App\Traits\Traits;

use Carbon\Carbon;
use DateTime;

trait DateTimeFunctions
{
    public function endOfDay(DateTime $dateVal): DateTime
    {
        return Carbon::parse($dateVal)->endOfDay();
    }
}
