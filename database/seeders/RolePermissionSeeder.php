<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $productOwner = Role::create(['name' => 'product_owner']);
        $projectManager = Role::create(['name' => 'project_manager']);
        $member = Role::create(['name' => 'member']);

        // Crear permisos
        $permissions = [
            
            'crear_proyecto',
            'editar_proyecto',
            'eliminar_proyecto',
            'ver_proyecto',
            'cerrar_sprint',
        
            'crear_tarea',
            'editar_tarea',
            'eliminar_tarea',
            'ver_tarea',
            'asignar_tarea',
            'cambiar_estado_tarea',
            
            'ver_auditoria',
            'ver_historial',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar permisos a Product Owner 
        $productOwner->givePermissionTo($permissions);

        // Asignar permisos a Project Manager
        $pmPermissions = [
            'crear_proyecto',
            'editar_proyecto',
            'ver_proyecto',
            'crear_tarea',
            'editar_tarea',
            'ver_tarea',
            'asignar_tarea',
            'cambiar_estado_tarea',
            'ver_historial',
        ];
        $projectManager->givePermissionTo($pmPermissions);

        // Asignar permisos a Member
        $memberPermissions = [
            'ver_tarea',
            'cambiar_estado_tarea',
        ];
        $member->givePermissionTo($memberPermissions);
    }
}