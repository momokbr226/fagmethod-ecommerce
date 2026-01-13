<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilleProduit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'familles_produits';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'code',
        'est_active',
        'ordre',
        'attributs_specifiques'
    ];

    protected $casts = [
        'est_active' => 'boolean',
        'ordre' => 'integer',
        'attributs_specifiques' => 'array',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'famille_id');
    }

    public function scopeActive($query)
    {
        return $query->where('est_active', true);
    }

    public function scopeOrderByOrder($query)
    {
        return $query->orderBy('ordre', 'asc')->orderBy('nom', 'asc');
    }
}
