<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar !== null ? url($this->avatar) : null,
            'address' => $this->treet_address,
            'ward' => $this->ward,
            'city' => $this->city,
            'district' => $this->district,
            'role' => $this->role,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}