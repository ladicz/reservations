<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReserveTables;
use App\Livewire\TablesIndex;
use App\Models\Reservation;
use App\Models\ReservedTable;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class TablesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully(): void
    {
        Livewire::test(TablesIndex::class)
            ->assertStatus(200);
    }

    public function test_component_exists_on_the_page(): void
    {
        $this->get('/')
            ->assertSeeLivewire(TablesIndex::class);
    }

    public function test_start_time_correctly_set(): void
    {
        Livewire::test(TablesIndex::class)
            ->set('startTime', '13:00')
            ->assertSet('startTime', '13:00');
    }

    public function test_start_date_correctly_set(): void
    {
        Livewire::test(TablesIndex::class)
            ->set('startDate', '23.04.2025')
            ->assertSet('startDate', '23.04.2025');
    }

    public function test_guest_can_list_only_available_tables(): void
    {
        Table::factory(2)->create();
        $table = Table::factory()->create();
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'from' => now(),
            'user_id' => $user->id,

        ]);

        ReservedTable::factory()->create([
            'reservation_id'=> $reservation->id,
            'table_id' => $table->id,
        ]);

        Livewire::test(TablesIndex::class)
            ->set('startTime', now()->format('H:i'))
            ->set('startDate', now()->format('d.m.Y'))
            ->assertViewHas('tables', function ($tables) {
                return count($tables) == 2;
            });

    }

    public function test_reservation_form_clear_event_dispatched_on_tim_date_change(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(TablesIndex::class)
            ->set('startTime', now()->format('H:i'))
            ->assertDispatched('clear-tables-form');

        Livewire::test(TablesIndex::class)
            ->set('startDate', now()->format('H:i'))
            ->assertDispatched('clear-tables-form');
    }

    public function test_reservation_message_visible_on_empty_time_input(): void
    {
       Livewire::test(TablesIndex::class)
        ->assertSee('Vyberte datum a čas');
    }

    public function test_reservation_message_not_visible_on_picked_time(): void
    {
        Livewire::test(TablesIndex::class, ['startTime' => now()->format('H:i')])
            ->assertDontSee('Vyberte datum a čas');
    }
}
