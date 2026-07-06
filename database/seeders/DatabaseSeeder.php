<?php

namespace Database\Seeders;

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
        // Ensure the admin account exists
        User::firstOrCreate(
            ['email' => 'admin@eios.vn'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Call the permissions seeder to populate the matrix and roles
        $this->call([
            CmsPermissionsSeeder::class,
        ]);
    }
}
