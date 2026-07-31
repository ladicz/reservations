<?php

namespace Tests\Unit;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use App\Services\TableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_returns_correctly_available_tables(): void
    {
        $service = new TableService();
        Table::factory(2)->create();
        $tables = $service->getAvaliableTables(now(), 90);
        $this->assertEquals($tables->count(),2);
    }

    public function test_service_does_not_return_reserved_tables(): void
    {
        $user = User::factory()->create();
        Reservation::factory()
            ->has(Table::factory())
            ->create([
                'user_id' => $user->id,
                'from' => now(),
                'to' =>now()->addHours(2),
            ]);

        $table2 = Table::factory()->create();

        $service = new TableService();

        $tables = $service->getAvaliableTables(now(), 90);

        $this->assertEquals($tables->count(), 1);
        $this->assertEquals($tables->first()->id, $table2->id);

    }

    public function test_service_returns_table_when_previous_reservation_ends_at_requested_start(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();
        $from = now()->startOfDay()->addHours(11);

        Reservation::factory()
            ->hasAttached($table)
            ->create([
                'user_id' => $user->id,
                'from' => $from,
                'to' => $from->copy()->addMinutes(90),
            ]);

        $tables = (new TableService())->getAvaliableTables($from->copy()->addMinutes(90), 120);

        $this->assertTrue($tables->contains($table));
    }
}
