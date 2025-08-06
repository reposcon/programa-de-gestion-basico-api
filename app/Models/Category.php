<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'id_category';
    protected $fillable = ['name_category', 'state_category'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id', 'id_category');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id_category');
    }
}
