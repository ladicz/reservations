<?php

namespace Tests\Unit;

use App\Models\Reservation;
use App\Models\ReservedTable;
use App\Models\Table;
use App\Models\User;
use App\Services\TableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_service_returns_correctly_available_tables(): void
    {
        $service = new TableService();
        Table::factory(2)->create();
        $tables = $service->getAvaliableTables(now(), 2);
        $this->assertEquals($tables->count(),2);
    }

    public function test_service_does_not_return_reserved_tables(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'from' => now(),
            'to' =>now()->addHours(2),
        ]);
        $table1 = Table::factory()->create();
        $table2 = Table::factory()->create();
        ReservedTable::factory()->create([
            'reservation_id' => $reservation->id,
            'table_id' => $table1->id
        ]);

        $service = new TableService();

        $tables = $service->getAvaliableTables(now(), 1);

        $this->assertEquals($tables->count(), 1);
        $this->assertEquals($tables->first()->id, $table2->id);

    }
}
