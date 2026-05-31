<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');
            }

            if (! Schema::hasColumn('tickets', 'service_amount')) {
                $table->decimal('service_amount', 10, 2)->default(0)->after('discount_amount');
            }

            if (! Schema::hasColumn('tickets', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('service_amount');
            }

            if (! Schema::hasColumn('tickets', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_amount');
            }

            if (! Schema::hasColumn('tickets', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('closed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            foreach (['paid_at', 'payment_method', 'paid_amount', 'service_amount', 'discount_amount'] as $column) {
                if (Schema::hasColumn('tickets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
