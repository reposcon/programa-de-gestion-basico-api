<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id_product';

    protected $fillable = [
        'name_product',
        'category_id',
        'subcategory_id',
        'state_product',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id_category');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id_subcategory');
    }
}
