<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_reservation_interface_strings_are_available_in_english(): void
    {
        app()->setLocale('en');

        $this->assertSame('Date', __('Date'));
        $this->assertSame('Start time', __('Start time'));
        $this->assertSame('Duration', __('Duration'));
        $this->assertSame('No available tables for the requested date, time, and duration.', __('No available tables for requested date and time.'));
    }

    public function test_reservation_interface_strings_are_available_in_czech(): void
    {
        app()->setLocale('cs');

        $this->assertSame('Datum', __('Date'));
        $this->assertSame('Čas začátku', __('Start time'));
        $this->assertSame('Délka', __('Duration'));
        $this->assertSame('Pro zadané datum, čas a délku nejsou dostupné žádné stoly.', __('No available tables for requested date and time.'));
    }
}
