<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        if (!isset($row['nombre']) || empty(trim($row['nombre']))) {
            return null; 
        }

        return new Product([
            'name_product'    => $row['nombre'],
            'price_sell'      => $row['precio'],
            'price_cost'      => $row['precio_costo'] ?? $row['precio'],
            'price_net'       => $row['precio_neto'] ?? $row['precio'],  
            'stock'           => $row['stock'],
            'tax_id'          => $row['id_iva'],
            'subcategory_id'  => $row['id_subcategoria'],
            'category_id'     => $row['id_categoria'],
            'state_product'   => 1, 
            'created_by'      => Auth::id(), // Registra al usuario que sube el Excel
            'is_tax_included' => 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'          => 'required|string',
            'precio'          => 'required|numeric',
            'stock'           => 'required|integer',
            'id_iva'          => 'required|exists:tax_settings,id_tax',
            'id_subcategoria' => 'required|exists:subcategories,id_subcategory',
            'id_categoria'    => 'required|exists:categories,id_category',
            'precio_costo'    => 'nullable|numeric',
            'precio_neto'     => 'nullable|numeric',
        ];
    }
}