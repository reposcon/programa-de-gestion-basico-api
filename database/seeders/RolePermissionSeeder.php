<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')
            ->where('name_role', 'admin')
            ->value('id_role');

        $clientRoleId = DB::table('roles')
            ->where('name_role', 'client')
            ->value('id_role');

        $permissions = DB::table('permissions')->pluck('id_permission');

        foreach ($permissions as $permissionId) {
            DB::table('permission_role')->insert([
                'id_permission' => $permissionId,
                'id_role' => $adminRoleId,
            ]);
        }

        $clientPermissions = DB::table('permissions')
            ->whereIn('name_permission', [
                'view_products',
                'view_categories',
                'view_subcategories',
            ])
            ->pluck('id_permission');

        foreach ($clientPermissions as $permissionId) {
            DB::table('permission_role')->insert([
                'id_permission' => $permissionId,
                'id_role' => $clientRoleId,
            ]);
        }
    }
}
