<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'image',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function panner()
    {
        return $this->hasMany(Banner::class, 'product_id', 'id');
    }
  public function cartItems() {
    return $this->hasMany(CartItem::class);
}

}
