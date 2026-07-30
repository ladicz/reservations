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
        $tables = $service->getAvaliableTables(now(), 2);
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

        $tables = $service->getAvaliableTables(now(), 1);

        $this->assertEquals($tables->count(), 1);
        $this->assertEquals($tables->first()->id, $table2->id);

    }
}
