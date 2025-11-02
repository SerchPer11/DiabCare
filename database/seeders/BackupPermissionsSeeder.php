<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BackupPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create backup permissions
        $permissions = [
            'administration.view' => 'Ver sección de administración',
            'backups.index' => 'Ver lista de respaldos',
            'backups.create' => 'Crear respaldos',
            'backups.restore' => 'Restaurar respaldos',
            'backups.download' => 'Descargar respaldos',
            'backups.delete' => 'Eliminar respaldos',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Assign all backup permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
        }

        $this->command->info('Backup permissions created and assigned to admin role.');
    }
}
