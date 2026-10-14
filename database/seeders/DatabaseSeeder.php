<?php

namespace Database\Seeders;

use App\Support\Features;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Features::permissionsEnabled()) {
            $this->call([
                AdminRolesAndPermissionsSeeder::class,
            ]);
        }

        // User::factory(10)->create();

        /*
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        */
    }
}
