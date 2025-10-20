<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor\Catalogs\Medication;
use Database\Seeders\Catalogs\Medications;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            ModuleSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AssignRoleUserSeeder::class,
            ExerciseTypeSeeder::class,
            ExerciseSeeder::class,
            Medications::class,
        ]);
    }
}
