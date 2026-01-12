<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subtotal' => $this->subtotal,
            'formatted_subtotal' => $this->formatted_subtotal,
            'tax_amount' => $this->tax_amount,
            'formatted_tax_amount' => $this->formatted_tax_amount,
            'total' => $this->total,
            'formatted_total' => $this->formatted_total,
            'currency' => $this->currency,
            'items_count' => $this->items_count ?? $this->items->sum('quantity'),
            'items' => $this->when($this->relationLoaded('items') || $this->items, function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'formatted_price' => $item->formatted_price,
                        'total' => $item->total,
                        'formatted_total' => $item->formatted_total,
                        'product_attributes' => $item->product_attributes,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'slug' => $item->product->slug,
                            'sku' => $item->product->sku,
                            'image' => $item->product->image,
                            'stock_quantity' => $item->product->stock_quantity,
                            'is_out_of_stock' => $item->product->is_out_of_stock,
                        ],
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
