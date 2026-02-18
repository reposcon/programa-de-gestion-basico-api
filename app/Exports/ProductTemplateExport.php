<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\TaxSetting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductTemplateExport implements WithHeadings, WithEvents, ShouldAutoSize
{
    /**
     * Definimos los encabezados exactos que ya usas 
     */
    public function headings(): array
    {
        return [
            'nombre',
            'precio',
            'stock',
            'id_iva',
            'id_subcategoria',
            'id_categoria',
            'precio_costo',
            'precio_neto'
        ];
    }

    /**
     * Aquí cargamos los datos reales para que el usuario los vea 
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Cargamos los datos actuales de la DB
                $categories = Category::all(['id_category', 'name_category']);
                $subcategories = Subcategory::all(['id_subcategory', 'name_subcategory']);
                $taxes = TaxSetting::all(['id_tax', 'tax_name']);

                // 2. Escribimos una "Guía de IDs" a partir de la columna K (lejos de los datos)
                $sheet->setCellValue('K1', 'GUÍA DE CATEGORÍAS (ID)');
                $row = 2;
                foreach ($categories as $cat) {
                    $sheet->setCellValue("K{$row}", "{$cat->name_category} = ID: {$cat->id_category}");
                    $row++;
                }

                $sheet->setCellValue('M1', 'GUÍA DE SUBCATEGORÍAS (ID)');
                $row = 2;
                foreach ($subcategories as $sub) {
                    $sheet->setCellValue("M{$row}", "{$sub->name_subcategory} = ID: {$sub->id_subcategory}");
                    $row++;
                }

                $sheet->setCellValue('O1', 'GUÍA DE IVA (ID)');
                $row = 2;
                foreach ($taxes as $tax) {
                    $sheet->setCellValue("O{$row}", "{$tax->tax_name} = ID: {$tax->id_tax}");
                    $row++;
                }

                // 3. Estética: Ponemos los encabezados de la guía en negrita
                $sheet->getStyle('K1:O1')->getFont()->setBold(true);
            },
        ];
    }
}
