<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;



class AssignRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::find(1);
        $role = Role::find(1);
        $admin->assignRole($role);

        

        $patient = User::find(3);
        $role = Role::find(3);
        $patient->assignRole($role);
    }
}
