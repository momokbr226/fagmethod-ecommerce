<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'image',
        'couleur',
        'est_active',
        'ordre_affichage',
        'categorie_parente_id',
        'meta_description',
        'meta_titre'
    ];

    protected $casts = [
        'est_active' => 'boolean',
        'ordre_affichage' => 'integer',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'categorie_id');
    }

    public function categorieParente()
    {
        return $this->belongsTo(Categorie::class, 'categorie_parente_id');
    }

    public function sousCategories()
    {
        return $this->hasMany(Categorie::class, 'categorie_parente_id');
    }

    public function scopeActive($query)
    {
        return $query->where('est_active', true);
    }

    public function scopeOrderByOrder($query)
    {
        return $query->orderBy('ordre_affichage', 'asc')->orderBy('nom', 'asc');
    }
}
