<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name_user' => 'Administrador',
            'password_user' => Hash::make('admin123'), 
            'role_id' => 1, 
            'state_user' => true,
        ]);
    }
}