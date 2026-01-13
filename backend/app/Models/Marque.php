<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marque extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marques';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'logo',
        'site_web',
        'pays_origine',
        'est_active',
        'ordre',
        'meta_donnees'
    ];

    protected $casts = [
        'est_active' => 'boolean',
        'ordre' => 'integer',
        'meta_donnees' => 'array',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'marque_id');
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
