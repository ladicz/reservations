<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reservations;
use App\Models\Reservation;
use App\Models\ReservedTable;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ReservationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Reservations::class)
            ->assertStatus(200);
    }

    public function test_component_exists_on_the_page(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/reservations')
            ->assertSeeLivewire(Reservations::class);
    }

    public function test_auth_user_can_see_own_reservations(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $reservation1 = Reservation::factory()->create([
            'user_id' => $user->id,
            'from' => now()
        ]);
        $tables = Table::factory(2)->create();
        foreach($tables as $table)
        {
            ReservedTable::factory()->create([
                'reservation_id' => $reservation1->id,
                'table_id' => $table->id,
            ]);
        }

        $reservation2 = Reservation::factory()->create([
            'user_id' => $user->id,
            'from' => now()
        ]);
        $tables = Table::factory(2)->create();
        foreach($tables as $table)
        {
            ReservedTable::factory()->create([
                'reservation_id' => $reservation2->id,
                'table_id' => $table->id,
            ]);
        }

        Livewire::test(Reservations::class)
            ->assertViewHas('reservations', function ($reservations) {
                return count($reservations) == 2;
            });
    }

    public function test_user_can_toggle_actual_and_past_reservations(): void
    {
        $fromNow = now();
        $fromPast = now()->subDays(2);
        $user = User::factory()->create();

        $this->actingAs($user);

        // aktualni rezervace
        $reservationNow = Reservation::factory()->create([
            'user_id' => $user->id,
            'from' => $fromNow
        ]);
        $tables = Table::factory(3)->create();
        foreach($tables as $table)
        {
            ReservedTable::factory()->create([
                'reservation_id' => $reservationNow->id,
                'table_id' => $table->id,
            ]);
        }

        // minula rezervace
        $reservationPast = Reservation::factory()->create([
            'user_id' => $user->id,
            'from' => $fromPast
        ]);
        $tables = Table::factory(3)->create();
        foreach($tables as $table)
        {
            ReservedTable::factory()->create([
                'reservation_id' => $reservationPast->id,
                'table_id' => $table->id,
            ]);
        }

        Livewire::test(Reservations::class)
            ->call('toggleFuture')
            ->assertSet('onlyFuture', false)
            ->assertViewHas('reservations', function ($reservations) {
                return count($reservations) == 2;
            });

        Livewire::test(Reservations::class)
            ->assertSet('onlyFuture', true)
            ->assertViewHas('reservations', function ($reservations) {
                return count($reservations) == 1;
            });
    }

    public function test_auth_user_can_delete_own_reservation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $now = now()->format('d.m.Y');

        // aktualni rezervace
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'from' => now()
        ]);
        $tables = Table::factory(3)->create();
        foreach($tables as $table)
        {
            ReservedTable::factory()->create([
                'reservation_id' => $reservation->id,
                'table_id' => $table->id,
            ]);
        }

        $this->assertDatabaseHas('reservations', [
            'user_id'=>$user->id,
        ]);
        Livewire::test(Reservations::class)
            ->assertViewHas('reservations', function ($reservations) {
                return count($reservations) == 1;
            })
            ->assertSee($now);
        Livewire::test(Reservations::class)
            ->call('delete', $reservation->id);

        $this->assertDatabaseEmpty('reservations');

        Livewire::test(Reservations::class)
            ->assertViewHas('reservations', function ($reservations) {
                return count($reservations) == 0;
            });
    }
}
