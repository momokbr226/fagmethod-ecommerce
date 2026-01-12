<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'sku' => $this->sku,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'compare_price' => $this->compare_price,
            'formatted_compare_price' => $this->formatted_compare_price,
            'has_discount' => $this->has_discount,
            'discount_percentage' => $this->discount_percentage,
            'stock_quantity' => $this->stock_quantity,
            'is_out_of_stock' => $this->is_out_of_stock,
            'image' => $this->image,
            'images' => $this->images,
            'weight' => $this->weight,
            'attributes' => $this->attributes,
            'category' => $this->when($this->category, [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
