<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $roles = collect([
            ['name' => 'Administrador', 'slug' => 'administrador'],
            ['name' => 'Cozinha', 'slug' => 'cozinha'],
            ['name' => 'Atendente', 'slug' => 'atendente'],
        ])->mapWithKeys(fn (array $role) => [
            $role['slug'] => Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']],
            ),
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $roles->get('administrador')->id,
        ]);

        $menuItems = [
            ['name' => 'X-Salada', 'price' => 24.90, 'description' => 'Hamburguer, queijo, alface, tomate e molho da casa.'],
            ['name' => 'X-Bacon', 'price' => 29.90, 'description' => 'Hamburguer, queijo, bacon crocante e molho da casa.'],
            ['name' => 'Batata frita', 'price' => 18.00, 'description' => 'Porcao individual de batata frita.'],
            ['name' => 'Porcao de frango', 'price' => 38.90, 'description' => 'Tiras de frango empanado com molho especial.'],
            ['name' => 'Refrigerante lata', 'price' => 7.00, 'description' => 'Lata 350ml.'],
            ['name' => 'Suco natural', 'price' => 10.00, 'description' => 'Suco natural preparado na hora.'],
            ['name' => 'Agua mineral', 'price' => 5.00, 'description' => 'Garrafa 500ml.'],
            ['name' => 'Cafe expresso', 'price' => 6.00, 'description' => 'Cafe expresso curto.'],
        ];

        foreach ($menuItems as $item) {
            MenuItem::query()->updateOrCreate(
                ['name' => $item['name']],
                $item + ['active' => true],
            );
        }
    }
}
