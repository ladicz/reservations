<?php

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
        Schema::table('reserved_table', function (Blueprint $table) {
            $table->unique(
                ['reservation_id', 'table_id'],
                'reserved_table_reservation_id_table_id_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserved_table', function (Blueprint $table) {
            $table->dropUnique('reserved_table_reservation_id_table_id_unique');
        });
    }
};
