<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
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
