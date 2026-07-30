<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reservations;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        Reservation::factory()
            ->has(Table::factory(2))
            ->create([
                'user_id' => $user->id,
                'from' => now()
            ]);

        Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => now()
            ]);

        Livewire::test(Reservations::class)
            ->assertViewHas('reservations', function ($reservations) {
                return count($reservations) == 2;
            });
    }

    public function test_user_can_toggle_and_view_actual_and_past_reservations(): void
    {
        $fromNow = now();
        $fromPast = now()->subDays(2);
        $user = User::factory()->create();

        $this->actingAs($user);

        // current rezervation
        Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => $fromNow
            ]);

        // past rezervation
        Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => $fromPast
            ]);

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
        $reservation = Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => now()
            ]);

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
