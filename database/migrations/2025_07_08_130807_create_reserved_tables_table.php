<?php

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reserved_tables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Reservation::class)
                ->constrained()
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreignIdFor(Table::class)
                ->constrained()
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_tables');
    }
};
