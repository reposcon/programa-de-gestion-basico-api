<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            TaxAndUvtSeeder::class,
            ProductTestSeeder::class,
            CustomerSeeder::class,
            PaymentMethodSeeder::class
        ]);
    }
}
