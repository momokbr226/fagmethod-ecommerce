<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'compare_price',
        'stock_quantity',
        'track_quantity',
        'is_active',
        'image',
        'images',
        'weight',
        'attributes',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_quantity' => 'boolean',
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
        'images' => 'array',
        'attributes' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '€' . number_format($this->price, 2);
    }

    public function getFormattedComparePriceAttribute(): ?string
    {
        return $this->compare_price ? '€' . number_format($this->compare_price, 2) : null;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->has_discount) {
            return null;
        }
        
        return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->track_quantity && $this->stock_quantity <= 0;
    }
}
