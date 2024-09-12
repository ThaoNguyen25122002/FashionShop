<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'invoice_code' => $this->invoice_code,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'created_date' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i:s'),
        ];
        if ($this->relationLoaded('details')) {
            $data['details'] = OrderDetailResource::collection($this->details);
        }
        if ($this->relationLoaded('user')) {
            $data['user_name'] = $this->user->name;
            $data['phone'] = $this->user->phone;
            $data['email'] = $this->user->email;
            $data['city'] = $this->user->city;
            $data['ward'] = $this->user->ward;
            $data['district'] = $this->user->district;
            $data['street_address'] = $this->user->street_address;
            $data['product_count'] = $this->details_count ? $this->details_count : null;
        }
        return $data;
    }
}
