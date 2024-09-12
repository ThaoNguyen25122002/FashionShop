<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionValue extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = ['product_id', 'size_id', 'color_id'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [

        'product_id',
        'color_id',
        'size_id',
        'quantity',
    ];

    public function color()
    {
        return $this->belongsTo(OptionValue::class, 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(OptionValue::class, 'size_id');
    }
}
