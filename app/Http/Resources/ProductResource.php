<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => url($image->image_url),
                ];
            }),
            // 'categories' => $this->categories->map(function($category) {
            //     return [
            //         'id' => $category->id,
            //         'name' => $category->name,
            //     ];
            // }),
            'category_ids' => $this->categories->pluck('id'),
            'variations' => $this->variations->map(function ($variation) {
                return [
                    'color' => $variation->color->name,
                    'size' => $variation->size->name,
                    'quantity' => $variation->quantity,
                ];
            }),
            'sum_qty' => $this->variations_sum_quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
