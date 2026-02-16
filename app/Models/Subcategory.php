<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';
    protected $primaryKey = 'id_subcategory';

    protected $fillable = [
        'name_subcategory',
        'state_subcategory',
        'category_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id_category');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id', 'id_subcategory');
    }
}
