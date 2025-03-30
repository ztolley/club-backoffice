<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create a default admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // Use a hashed password
        ]);

        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'coach']);

        $user = User::find(1);
        $user->assignRole('super_admin');
    }
}
