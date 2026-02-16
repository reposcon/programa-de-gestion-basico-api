<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'id_category';
    protected $fillable = [
        'name_category',
        'state_category',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'id_category', 'id_category');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id_category');
    }
}
