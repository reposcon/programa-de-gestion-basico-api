<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
public function run(): void
{
    Role::create(['name_role' => 'admin']);
    Role::create(['name_role' => 'client']);
}
}
