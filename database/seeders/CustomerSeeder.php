<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Cliente para ventas de mostrador (Persona Natural)
        Customer::updateOrCreate(
            ['document_number_customer' => '22222222'], 
            [
                'name_customer' => 'Consumidor Final',
                'email_customer' => 'anonimo@ejemplo.com',
                'phone_customer' => '0000000',
                'state_customer' => 1
            ]
        );

        // Cliente Empresa (Para probar montos altos > 5 UVT)
        Customer::updateOrCreate(
            ['document_number_customer' => '900123456-1'],
            [
                'name_customer' => 'Inversiones Tecnológicas S.A.S.',
                'email_customer' => 'compras@inversiones.com',
                'phone_customer' => '3157008090',
                'state_customer' => 1
            ]
        );

        // Cliente para probar el Toggle (Inactivo)
        Customer::updateOrCreate(
            ['document_number_customer' => '11111111'],
            [
                'name_customer' => 'Cliente Inactivo de Prueba',
                'email_customer' => 'inactivo@ejemplo.com',
                'phone_customer' => '5555555',
                'state_customer' => 0
            ]
        );

        $this->command->info('Clientes de prueba creados con éxito.');
    }
}