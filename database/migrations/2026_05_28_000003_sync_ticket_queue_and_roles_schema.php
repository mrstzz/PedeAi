<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        $roles = collect([
            'admin' => ['name' => 'Administrador', 'slug' => 'administrador'],
            'administrador' => ['name' => 'Administrador', 'slug' => 'administrador'],
            'kitchen' => ['name' => 'Cozinha', 'slug' => 'cozinha'],
            'cozinha' => ['name' => 'Cozinha', 'slug' => 'cozinha'],
            'attendant' => ['name' => 'Atendente', 'slug' => 'atendente'],
            'atendente' => ['name' => 'Atendente', 'slug' => 'atendente'],
        ]);

        foreach ($roles->unique('slug') as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                ['name' => $role['name'], 'updated_at' => now(), 'created_at' => now()],
            );
        }

        if (! Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')
                    ->nullable()
                    ->after(Schema::hasColumn('users', 'role') ? 'role' : 'password')
                    ->constrained('roles')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            foreach ($roles as $legacyRole => $role) {
                DB::table('users')
                    ->where('role', $legacyRole)
                    ->update(['role_id' => DB::table('roles')->where('slug', $role['slug'])->value('id')]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'priority')) {
                $table->enum('priority', ['normal', 'alta'])->default('normal')->after('status');
            }
        });

        Schema::table('ticket_items', function (Blueprint $table) {
            if (! Schema::hasColumn('ticket_items', 'status')) {
                $table->enum('status', ['pendente', 'em_preparo', 'entregue'])->default('pendente')->after('subtotal');
            }

            if (! Schema::hasColumn('ticket_items', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_items', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_items', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }

            if (Schema::hasColumn('ticket_items', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'priority')) {
                $table->dropColumn('priority');
            }
        });

        if (Schema::hasColumn('users', 'role_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['role_id']);
                });
            } catch (\Throwable) {
                //
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_id');
            });
        }
    }
};
