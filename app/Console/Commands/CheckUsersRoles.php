<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckUsersRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar usuarios y sus roles/permisos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VERIFICACIÓN DE USUARIOS Y ROLES ===');
        
        $users = \App\Models\User::with(['roles', 'permissions'])->get();
        
        foreach ($users as $user) {
            $this->info("Usuario: {$user->name} ({$user->email})");
            $this->line("  Roles: " . $user->roles->pluck('name')->implode(', '));
            $this->line("  Permisos: " . $user->permissions->pluck('name')->implode(', '));
            $this->line("---");
        }
        
        $this->info('=== VERIFICACIÓN DE ROLES EXISTENTES ===');
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        
        foreach ($roles as $role) {
            $this->info("Rol: {$role->name}");
            $this->line("  Permisos: " . $role->permissions->pluck('name')->implode(', '));
            $this->line("---");
        }
        
        return Command::SUCCESS;
    }
}
