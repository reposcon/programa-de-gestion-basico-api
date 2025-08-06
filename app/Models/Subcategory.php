<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $primaryKey = 'id_subcategory';
    protected $fillable = ['name_subcategory', 'state_subcategory', 'amount_products', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class,'id_category' ,  'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id', 'id_subcategory');
    }
}
