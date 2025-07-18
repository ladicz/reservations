<?php

namespace Tests\Unit;

use App\Exceptions\ReservationDoesNotExistException;
use App\Exceptions\ReservationExistsException;
use App\Models\Reservation;
use App\Models\ReservedTable;
use App\Models\Table;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_user_can_create_reservation(): void
    {
        $service = new ReservationService();
        $user = User::factory()->create();
        $tables = Table::factory(3)->create();
        $tableIds = $tables->pluck('id')->toArray();

        $fromDate = now();

        $this->assertDatabaseCount('reservations', 0);

        $reservation = $service->createReservation($user,$tableIds,$fromDate);

        // pocet stolu v rezervaci odpovida poctu vstupnich stolu
        $this->assertEquals($reservation->tables->count(), count($tableIds));

        $this->assertEquals($reservation->from->format('Ymd H:i'), $fromDate->format('Ymd H:i'));

        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseCount('reserved_tables',3);

        // v rezervaci jsou pouze stoly, ktere tam maji byt
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
        $reservation = $service->createReservation($user,$tableIds,now());
        $this->assertDatabaseCount('reservations', 1);

        $this->assertThrows(function() use($service, $user, $tableIds){
            $service->createReservation($user,$tableIds,now());
        },ReservationExistsException::class);
    }

    public function test_user_can_delete_own_reservation_and_all_tables_are_free()
    {
        $service = new ReservationService();
        $user = User::factory()->create();
        $tables = Table::factory(3)->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseCount('reserved_tables', 0);
        foreach($tables as $table)
        {
            ReservedTable::factory()->create([
                'reservation_id' => $reservation->id,
                'table_id' => $table->id
            ]);
        }

        $this->assertDatabaseCount('reserved_tables', 3);

        $service->delete($user, $reservation->id);

        $this->assertDatabaseCount('reservations', 0);
        $this->assertDatabaseCount('reserved_tables', 0);
    }

    public function test_user_cannot_delete_other_users_reservation(): void
    {
        $service = new ReservationService();
        $user = User::factory()->create();
        $userOwner = User::factory()->create();
        $tables = Table::factory(3)->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $userOwner->id,
        ]);
        foreach($tables as $table)
        {
            ReservedTable::factory()->create([
                'reservation_id' => $reservation->id,
                'table_id' => $table->id
            ]);
        }
        $this->assertThrows(function() use($service, $user, $reservation){
             $service->delete($user, $reservation->id);
        },ReservationDoesNotExistException::class);

    }

    public function test_user_can_get_all_own_reservations(): void
    {
        $this->assertDatabaseEmpty('reservations');
        $service = new ReservationService();
        $fromNow = now();
        $fromPast = now()->subDays(2);

        $user = User::factory()->create();
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

        $resBuilder = $service->getUserReservations($user, true);

        $this->assertEquals($resBuilder->count(), 1);
        $this->assertDatabaseCount('reservations', 2);
        $this->assertEquals($resBuilder->first()->id, $reservationNow->id);
    }
}
