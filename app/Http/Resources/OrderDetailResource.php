<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'size' => $this->size,
            'color' => $this->color,
            'quantity' => $this->quantity,
            'name' => $this->product->name,
            'price' => $this->product->price,
            'image' => $this->product->images->isNotEmpty() ? asset($this->product->images->first()->image_url) : null
        ];
    }
}
