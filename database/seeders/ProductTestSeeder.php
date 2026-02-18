<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\TaxSetting;
use App\Models\User;

class ProductTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener el primer usuario (creador)
        $user = User::first();

        if (!$user) {
            $this->command->error('No hay usuarios en la base de datos. Crea un usuario antes de correr este seeder.');
            return;
        }

        // 2. Obtener los impuestos (Aseguramos que existan o damos un valor por defecto)
        // Usamos value('id_tax') para obtener solo el ID directamente
        $iva19Id = TaxSetting::where('tax_rate', 19)->value('id_tax');
        $iva5Id  = TaxSetting::where('tax_rate', 5)->value('id_tax');
        $iva0Id  = TaxSetting::where('tax_rate', 0)->value('id_tax');

        if (!$iva19Id) {
            $this->command->warn('No se encontró el impuesto del 19%. Asegúrate de correr TaxAndUvtSeeder primero.');
            return;
        }

        $cat = Category::firstOrCreate(
            ['name_category' => 'General'],
            ['state_category' => 1]
        );

        $sub = Subcategory::firstOrCreate(
            ['name_subcategory' => 'Varios'],
            [
                'category_id' => $cat->id_category,
                'state_subcategory' => 1
            ]
        );

        $products = [
            [
                'name_product' => 'Producto Premium 19%',
                'price_cost' => 70000,
                'price_net' => 100000,
                'tax_id' => $iva19Id,
                'price_sell' => 119000,
                'is_tax_included' => true,
                'stock' => 50,
                'state_product' => 1,
                'category_id' => $cat->id_category,
                'subcategory_id' => $sub->id_subcategory,
                'created_by' => $user->id
            ],
            [
                'name_product' => 'Producto Básico 5%',
                'price_cost' => 50000,
                'price_net' => 100000,
                'tax_id' => $iva5Id,
                'price_sell' => 105000,
                'is_tax_included' => true,
                'stock' => 30,
                'state_product' => 1,
                'category_id' => $cat->id_category,
                'subcategory_id' => $sub->id_subcategory,
                'created_by' => $user->id
            ],
            [
                'name_product' => 'Artículo de Lujo (Supera UVT)',
                'price_cost' => 200000,
                'price_net' => 300000,
                'tax_id' => $iva0Id,
                'price_sell' => 300000,
                'is_tax_included' => true,
                'stock' => 10,
                'state_product' => 1,
                'category_id' => $cat->id_category,
                'subcategory_id' => $sub->id_subcategory,
                'created_by' => $user->id
            ]
        ];

        foreach ($products as $prodData) {
            Product::updateOrCreate(
                ['name_product' => $prodData['name_product']],
                $prodData
            );
        }

        $this->command->info('Productos de prueba creados/actualizados con éxito.');
    }
}