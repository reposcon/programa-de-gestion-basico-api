<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {

        $adminRoleId = DB::table('roles')->updateOrInsert(
            ['name_role' => 'admin'],
            ['created_at' => now()]
        );
        $sellerRoleId = DB::table('roles')->updateOrInsert(
            ['name_role' => 'seller'],
            ['created_at' => now()]
        );
        $clientRoleId = DB::table('roles')->updateOrInsert(
            ['name_role' => 'client'],
            ['created_at' => now()]
        );

        // Ahora los buscamos para tener el ID real
        $adminId = DB::table('roles')->where('name_role', 'admin')->value('id_role');
        $sellerId = DB::table('roles')->where('name_role', 'seller')->value('id_role');
        $clientId = DB::table('roles')->where('name_role', 'client')->value('id_role');

        // 2. Asignar TODO al Admin
        $allPermissions = DB::table('permissions')->pluck('id_permission');
        foreach ($allPermissions as $pId) {
            DB::table('permission_role')->updateOrInsert([
                'id_permission' => $pId,
                'id_role' => $adminId,
            ]);
        }

        // 3. Asignar permisos al SELLER
        $sellerPermissions = DB::table('permissions')
            ->whereIn('name_permission', [
                'view_sales',
                'create_sales',

                'view_customers',
                'create_customers',
                'update_customers',

                'view_taxsetting',

                'view_dailyClosing',

                'exportExcel',
                'importExcel',
                'downloadInvoice'
            ])->pluck('id_permission');

        foreach ($sellerPermissions as $pId) {
            DB::table('permission_role')->updateOrInsert([
                'id_permission' => $pId,
                'id_role' => $sellerId,
            ]);
        }

        $clientPermissions = DB::table('permissions')
            ->whereIn('name_permission', [
                'view_products',
                'view_categories',
                'view_subcategories',
            ])->pluck('id_permission');

        foreach ($clientPermissions as $pId) {
            DB::table('permission_role')->updateOrInsert([
                'id_permission' => $pId,
                'id_role' => $clientId,
            ]);
        }
    }
}
