<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'admin',
            'last_name' => 'adminovich',
            'email' => 'admin@crocos.kz',
            'password' => '12345678',
            'username' => 'ADMIN777',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'first_name' => 'user',
            'last_name' => 'userovich',
            'email' => 'user@crocos.kz',
            'password' => '12345678',
            'username' => 'USER777',
            'role' => 'user',
        ]);
    }
}
