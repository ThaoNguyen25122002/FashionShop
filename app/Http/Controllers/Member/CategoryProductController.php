<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class CategoryProductController extends Controller
{
    //
    public function newProducts()
    {
        // dd(1);
        $products = Product::latest()->take(8)->get();

        return ProductResource::collection($products);
    }
}
