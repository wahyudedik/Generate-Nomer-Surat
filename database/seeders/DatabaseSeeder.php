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
        // User::factory(10)->create();
        $this->call(RolePermissionSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'unit_code' => 'ADM',
        ]);

        $admin->assignRole('admin');

        User::factory(5)->create()->each(function (User $user, int $index) {
            $user->update([
                'unit_code' => 'UNIT' . ($index + 1),
            ]);
            $user->assignRole('staff');
        });

        $this->call(LetterSeeder::class);
    }
}
