<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id_product';

    protected $fillable = [
        'name_product',
        'price_cost',    // Nuevo
        'price_net',     // Nuevo
        'tax_id',        // Nuevo
        'price_sell',    // Nuevo
        'is_tax_included', // Nuevo
        'stock',
        'state_product',
        'category_id',
        'subcategory_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function tax()
    {
        return $this->belongsTo(TaxSetting::class, 'tax_id', 'id_tax');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id_category');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id_subcategory');
    }
}
