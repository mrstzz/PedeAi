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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Identificação
            $table->string('customer_name')->nullable();
            $table->string('table_number')->nullable();
            
            // Controle de Estado
            $table->enum('status', ['aberta', 'em_andamento', 'fechada', 'paga', 'cancelada'])->default('aberta');
            $table->enum('priority', ['normal', 'alta'])->default('normal');
            
            // Valores
            $table->decimal('total_amount', 10, 2)->default(0.00);
            
            // Metadados
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            
            // Auditoria
            $table->timestamps();
            $table->softDeletes(); // Importante para comandas!
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
