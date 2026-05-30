<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'restaurant_table_id')) {
                $table->foreignId('restaurant_table_id')
                    ->nullable()
                    ->after('table_number')
                    ->constrained('restaurant_tables')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'reservation_id')) {
                $table->foreignId('reservation_id')
                    ->nullable()
                    ->unique()
                    ->after('restaurant_table_id')
                    ->constrained('reservations')
                    ->nullOnDelete();
            }

            $table->index(['restaurant_table_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            try {
                $table->dropIndex(['restaurant_table_id', 'status']);
            } catch (Throwable) {
                //
            }

            if (Schema::hasColumn('tickets', 'reservation_id')) {
                $table->dropForeign(['reservation_id']);
                $table->dropUnique(['reservation_id']);
                $table->dropColumn('reservation_id');
            }

            if (Schema::hasColumn('tickets', 'restaurant_table_id')) {
                $table->dropForeign(['restaurant_table_id']);
                $table->dropColumn('restaurant_table_id');
            }
        });
    }
};
