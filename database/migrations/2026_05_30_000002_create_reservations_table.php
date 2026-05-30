<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_table_id')->constrained('restaurant_tables')->restrictOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->timestamp('reserved_at');
            $table->unsignedSmallInteger('duration_minutes')->default(120);
            $table->unsignedSmallInteger('party_size')->nullable();
            $table->enum('status', ['pendente', 'confirmada', 'cancelada', 'concluida'])->default('pendente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_table_id', 'status', 'reserved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
