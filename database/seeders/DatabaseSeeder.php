<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor\Catalogs\Medication;
use Database\Seeders\Catalogs\Medications;
use Database\Seeders\Doctor\SpecialtySeeder;
use Database\Seeders\ExerciseTypeSeeder;
use Database\Seeders\ExerciseSeeder;
use Database\Seeders\Catalogs\FoodSeeder;
use Database\Seeders\Doctor\RecomendationSeeder;

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
            SpecialtySeeder::class,
            AppointmentStatusSeeder::class,
            AppointmentSeeder::class,
            FoodSeeder::class,
            RecomendationSeeder::class,
        ]);
    }
}
