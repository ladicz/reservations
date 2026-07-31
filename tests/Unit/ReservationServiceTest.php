<?php

namespace Tests\Unit;

use App\Exceptions\ReservationExistsException;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_reservation(): void
    {
        $service = new ReservationService();
        $user = User::factory()->create();
        $tables = Table::factory(3)->create();
        $tableIds = $tables->pluck('id')->toArray();

        $fromDate = now();

        $this->assertDatabaseCount('reservations', 0);

        $reservation = $service->createReservation($user, $tableIds, $fromDate, 90);

        // reserved tables count corresponds to count of input table ids
        $this->assertEquals($reservation->tables->count(), count($tableIds));

        $this->assertEquals($reservation->from->format('Ymd H:i'), $fromDate->format('Ymd H:i'));
        $this->assertEquals($reservation->to->format('Ymd H:i'), $fromDate->copy()->addMinutes(90)->format('Ymd H:i'));

        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseCount('reserved_table',3);

        // reservation contains only input tables
        $i=0;
        foreach($reservation->tables as $table)
        {
            $this->assertEquals($table->id, $tableIds[$i]);
            $i++;
        }
    }

    public function test_user_cannot_create_reservation_for_same_table_on_same_time(): void
    {
        $service = new ReservationService();
        $user = User::factory()->create();
        $tables = Table::factory(3)->create();
        $tableIds = $tables->pluck('id')->toArray();

        $this->assertDatabaseCount('reservations', 0);
        $service->createReservation($user, $tableIds, now(), 90);
        $this->assertDatabaseCount('reservations', 1);

        $this->assertThrows(function() use($service, $user, $tableIds){
            $service->createReservation($user, $tableIds, now(), 90);
        },ReservationExistsException::class);
    }

    public function test_user_can_create_back_to_back_reservations_for_the_same_table(): void
    {
        $service = new ReservationService();
        $user = User::factory()->create();
        $table = Table::factory()->create();
        $from = now()->startOfDay()->addHours(11);

        $service->createReservation($user, [$table->id], $from, 90);
        $service->createReservation($user, [$table->id], $from->copy()->addMinutes(90), 120);

        $this->assertDatabaseCount('reservations', 2);
    }

    public function test_delete_reservation_and_all_reserved_tables_are_free()
    {
        $service = new ReservationService();
        $user = User::factory()->create();
        $reservation = Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
            ]);

        $this->assertDatabaseCount('reserved_table', 3);

        $service->delete($reservation);

        $this->assertDatabaseCount('reservations', 0);
        $this->assertDatabaseCount('reserved_table', 0);
    }

    public function test_user_can_get_all_own_reservations(): void
    {
        $this->assertDatabaseEmpty('reservations');
        $service = new ReservationService();
        $fromNow = now();
        $fromPast = now()->subDays(2);

        $user = User::factory()->create();
        // current reservation
        $reservationNow = Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => $fromNow
            ]);

        // past reservation
        $reservationPast = Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => $fromPast
            ]);

        $resBuilder = $service->getUserReservations($user, false);

        $this->assertEquals($resBuilder->count(), 2);
        $this->assertDatabaseCount('reservations', 2);
        $resIds = $resBuilder->get()->pluck('id')->toArray();
        $this->assertEquals($resIds[0], $reservationPast->id);
        $this->assertEquals($resIds[1], $reservationNow->id);
    }

    public function test_user_can_get_only_future_reservations(): void
    {
        $this->assertDatabaseEmpty('reservations');
        $service = new ReservationService();
        $fromNow = now();
        $fromPast = now()->subDays(2);

        $user = User::factory()->create();
        // current reservation
        $reservationNow = Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => $fromNow
            ]);

        // past reservation
        Reservation::factory()
            ->has(Table::factory(3))
            ->create([
                'user_id' => $user->id,
                'from' => $fromPast
            ]);

        $resBuilder = $service->getUserReservations($user, true);

        $this->assertEquals($resBuilder->count(), 1);
        $this->assertDatabaseCount('reservations', 2);
        $this->assertEquals($resBuilder->first()->id, $reservationNow->id);
    }
}
