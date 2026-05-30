<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->unsignedSmallInteger('capacity');
            $table->enum('status', ['disponivel', 'ocupada', 'reservada', 'manutencao'])->default('disponivel');
            $table->timestamps();

            $table->index(['status', 'capacity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
