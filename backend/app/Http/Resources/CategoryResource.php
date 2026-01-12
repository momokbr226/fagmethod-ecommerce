<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'full_path' => $this->full_path,
            'parent_id' => $this->parent_id,
            'children' => $this->when($this->relationLoaded('children'), function () {
                return CategoryResource::collection($this->children);
            }),
            'products' => $this->when($this->relationLoaded('products'), function () {
                return ProductResource::collection($this->products);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
