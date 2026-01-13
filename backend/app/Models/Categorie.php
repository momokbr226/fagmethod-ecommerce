<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'image',
        'couleur',
        'categorie_parente_id',
        'ordre_affichage',
        'est_active',
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

    public function parent()
    {
        return $this->belongsTo(Categorie::class, 'categorie_parente_id');
    }

    public function enfants()
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

    public function scopeRacine($query)
    {
        return $query->whereNull('categorie_parente_id');
    }
}
