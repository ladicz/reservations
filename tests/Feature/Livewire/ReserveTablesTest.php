<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReserveTables;
use App\Models\Reservation;
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

    public function test_table_reservation_form_resets_after_date_changes(): void
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
        $from = now()->startOfMinute();

        Livewire::test(ReserveTables::class, [
            'startTime' => $from->format('H:i'),
            'startDate' => $from->format('d.m.Y'),
            'durationInMinutes' => 120,
            'tables' => $tables,
            'tableIds' => $tables->pluck('id')->toArray(),
        ])
        ->assertSet('durationInMinutes', 120)
        ->call('save')
        ->assertRedirect('/reservations')
        ->assertSessionHas('success');

        $reservation = Reservation::query()->where('user_id', $user->id)->sole();

        $this->assertEquals($from->format('d.m.Y H:i'), $reservation->from->format('d.m.Y H:i'));
        $this->assertEquals(120, $reservation->from->diffInMinutes($reservation->to));
        $this->assertDatabaseCount('reserved_table', 3);
    }

    public function test_reservation_requires_at_least_one_table(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ReserveTables::class, [
            'startTime' => now()->format('H:i'),
            'startDate' => now()->format('d.m.Y'),
            'tables' => new Collection(),
        ])
            ->call('save')
            ->assertHasErrors(['tableIds' => 'required']);

        $this->assertDatabaseEmpty('reservations');
    }

    public function test_reservation_requires_a_current_or_future_date_and_valid_time(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $table = Table::factory()->create();

        Livewire::test(ReserveTables::class, [
            'startDate' => now()->subDay()->format('d.m.Y'),
            'startTime' => 'invalid',
            'tables' => new Collection([$table]),
            'tableIds' => [$table->id],
        ])
            ->call('save')
            ->assertHasErrors([
                'startDate' => 'after_or_equal',
                'startTime' => 'date_format',
            ]);

        $this->assertDatabaseEmpty('reservations');
    }

    public function test_reservation_requires_existing_distinct_table_ids(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $table = Table::factory()->create();

        Livewire::test(ReserveTables::class, [
            'startDate' => now()->format('d.m.Y'),
            'startTime' => now()->format('H:i'),
            'tables' => new Collection([$table]),
            'tableIds' => [$table->id, $table->id, 999999],
        ])
            ->call('save')
            ->assertHasErrors([
                'tableIds.1' => 'distinct',
                'tableIds.2' => 'exists',
            ]);

        $this->assertDatabaseEmpty('reservations');
    }

    public function test_reservation_requires_an_allowed_duration(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $table = Table::factory()->create();

        Livewire::test(ReserveTables::class, [
            'startDate' => now()->format('d.m.Y'),
            'startTime' => now()->format('H:i'),
            'durationInMinutes' => 60,
            'tables' => new Collection([$table]),
            'tableIds' => [$table->id],
        ])
            ->call('save')
            ->assertHasErrors(['durationInMinutes' => 'in']);

        $this->assertDatabaseEmpty('reservations');
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
        ->assertSee(__('Please log in to reserve tables.'));
    }
}
