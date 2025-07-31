<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
     protected $fillable = [
        'category_name',
        'image'
    ];

    // public function parent()
    //    {
    //        return $this->belongsTo(Category::class, 'parent_id')->withDefault([
    //            'category_name' => 'No Parent',
    //        ]);
    //    }
    //    public function children()
    // {
    //     return $this->hasMany(Category::class, 'parent_id');
    // }
     public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
