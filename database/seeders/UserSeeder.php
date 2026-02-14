<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name_user' => 'administrador',
            'password_user' => Hash::make('administrador'),
            'state_user' => true,
        ]);
        $adminRole = Role::where('name_role', 'admin')->first();
        $admin->roles()->attach($adminRole->id_role);

        $client = User::create([
            'name_user' => 'cliente',
            'password_user' => Hash::make('cliente'),
            'state_user' => true,
        ]);

        $clientRole = Role::where('name_role', 'client')->first();

        if ($clientRole) {
            $client->roles()->attach($clientRole->id_role);
        }
    }
}
