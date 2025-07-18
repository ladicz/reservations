<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReserveTables;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReserveTablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully(): void
    {
        Livewire::test(ReserveTables::class,[
            'tables' => new Collection(),
        ])
        ->assertStatus(200);
    }

    public function test_reservation_form_reset_after_date_changes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $tables = Table::factory(3)->create();

        Livewire::test(ReserveTables::class, [
            'startTime' => now()->format('H:i'),
            'startDate' => now()->format('d.m.Y'),
            'tables' => $tables,
            'tableIds' => $tables->pluck('id')->toArray(),
        ])->dispatch('clear-tables-form')
        ->assertSet('tableIds', []);
    }

    public function test_auth_user_can_list_and_reserve_tables(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $tables = Table::factory(3)->create();

        Livewire::test(ReserveTables::class, [
            'startTime' => now()->format('H:i'),
            'startDate' => now()->format('d.m.Y'),
            'tables' => $tables,
            'tableIds' => $tables->pluck('id')->toArray(),
        ])->call('save')
        ->assertRedirect('/reservations')
        ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations',[
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('reserved_tables', 3);
    }

    public function test_guest_see_warning_to_log_in_to_reserve_tables(): void
    {
        $tables = Table::factory(2)->create();
        Livewire::test(ReserveTables::class, [
            'startTime' => now()->format('H:i'),
            'startDate' => now()->format('d.m.Y'),
            'tables' => $tables,
        ])
        ->assertViewHas('tables', function ($tables) {
            return count($tables) == 2;
        })
        ->assertSee('Pro rezervaci stolu');
    }
}
