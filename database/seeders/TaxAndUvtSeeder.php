<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TaxAndUvtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/TaxAndUvtSeeder.php
    public function run()
    {
        // Configuración UVT 2026
        DB::table('global_configs')->updateOrInsert(
            ['config_key' => 'VALOR_UVT_2026'],
            [
                'config_value' => 52374.00,
                'description' => 'Valor de la Unidad de Valor Tributario para el año 2026 en Colombia',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );


        // Impuestos base DIAN
        $taxes = [
            ['tax_name' => 'IVA General 19%', 'tax_rate' => 19.00, 'tax_type' => 'IVA'],
            ['tax_name' => 'IVA Reducido 5%', 'tax_rate' => 5.00, 'tax_type' => 'IVA'],
            ['tax_name' => 'Impoconsumo 8%', 'tax_rate' => 8.00, 'tax_type' => 'INC'],
            ['tax_name' => 'Exento / Excluido', 'tax_rate' => 0.00, 'tax_type' => 'EXENTO'],
        ];

        foreach ($taxes as $tax) {
            DB::table('tax_settings')->insert($tax + ['created_at' => now()]);
        }
    }
}
