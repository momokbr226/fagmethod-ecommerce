<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
        'tax_amount',
        'total',
        'currency',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '€' . number_format($this->total, 2);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    protected static function booted()
    {
        static::updated(function ($cart) {
            $cart->updateTotals();
        });
    }

    public function updateTotals(): void
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        
        $this->tax_amount = $this->subtotal * 0.20;
        $this->total = $this->subtotal + $this->tax_amount;
        
        $this->saveQuietly();
    }
}
