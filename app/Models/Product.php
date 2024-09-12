<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'description',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Mối quan hệ với bảng ProductOptionValue (nếu có)
     * Một sản phẩm có nhiều biến thể
     */
    public function variations()
    {
        return $this->hasMany(ProductOptionValue::class);
    }
}
