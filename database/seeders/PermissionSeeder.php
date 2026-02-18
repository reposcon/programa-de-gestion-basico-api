<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permission_role')->truncate();
        $permissions = [
            'view_users',
            'create_users',
            'update_users',
            'delete_users',

            'view_roles',
            'create_roles',
            'update_roles',
            'delete_roles',

            'view_categories',
            'create_categories',
            'update_categories',
            'delete_categories',

            'view_subcategories',
            'create_subcategories',
            'update_subcategories',
            'delete_subcategories',

            'view_products',
            'create_products',
            'update_products',
            'delete_products',

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

        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name_permission' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
