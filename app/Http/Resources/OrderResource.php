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
        return [
            'id' => $this->id,

            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'email' => $this->customer->email,
            ],

            'order_date' => $this->order_date?->format('Y-m-d'),

            'total_amount' => $this->total_amount,

            'status' => $this->status,

            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->pivot->unit_price, // Use the unit_price from the pivot table
                    'quantity' => $item->pivot->quantity, // Use the quantity from the pivot table
                    'subtotal' => number_format($item->pivot->unit_price * $item->pivot->quantity, 2, '.', ''), // Calculate subtotal for each item
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
