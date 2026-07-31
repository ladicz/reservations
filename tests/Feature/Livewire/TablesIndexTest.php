<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TablesIndex;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $user = User::factory()->create();
        Reservation::factory()
            ->has(Table::factory())
            ->create([
                'from' => now(),
                'user_id' => $user->id,
            ]);

        Livewire::test(TablesIndex::class)
            ->set('startTime', now()->format('H:i'))
            ->set('startDate', now()->format('d.m.Y'))
            ->assertViewHas('tables', function ($tables) {
                return count($tables) == 2;
            });

    }

    public function test_table_reservation_form_clear_event_dispatched_on_time_date_change(): void
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

    public function test_duration_change_reloads_available_tables(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();
        $from = now()->startOfDay()->addHours(11);

        Reservation::factory()
            ->hasAttached($table)
            ->create([
                'user_id' => $user->id,
                'from' => $from->copy()->addMinutes(120),
                'to' => $from->copy()->addMinutes(210),
            ]);

        Livewire::test(TablesIndex::class)
            ->set('startDate', $from->format('d.m.Y'))
            ->set('startTime', $from->format('H:i'))
            ->set('durationInMinutes', 90)
            ->assertSet('durationInMinutes', 90)
            ->assertViewHas('tables', function ($tables) use ($table) {
                return $tables->contains('id', $table->id);
            })
            ->set('durationInMinutes', 180)
            ->assertViewHas('tables', function ($tables) use ($table) {
                return ! $tables->contains('id', $table->id);
            });
    }

    public function test_reservation_message_visible_on_empty_time_input(): void
    {
       Livewire::test(TablesIndex::class)
        ->assertSee(__(''));
    }

    public function test_reservation_message_not_visible_on_picked_time(): void
    {
        Livewire::test(TablesIndex::class, ['startTime' => now()->format('H:i')])
            ->assertDontSee(__('Please pick date and time'));
    }
}
