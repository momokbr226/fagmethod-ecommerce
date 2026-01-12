<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'description_courte',
        'reference',
        'prix',
        'prix_compare',
        'quantite_stock',
        'suivi_quantite',
        'est_actif',
        'est_en_rupture',
        'est_mise_en_avant',
        'image_principale',
        'images_supplementaires',
        'poids',
        'dimensions_longueur',
        'dimensions_largeur',
        'dimensions_hauteur',
        'attributs',
        'specifications_techniques',
        'categorie_id',
        'meta_description',
        'meta_titre',
        'marque',
        'modele',
        'garantie'
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'prix_compare' => 'decimal:2',
        'quantite_stock' => 'integer',
        'suivi_quantite' => 'boolean',
        'est_actif' => 'boolean',
        'est_en_rupture' => 'boolean',
        'est_mise_en_avant' => 'boolean',
        'images_supplementaires' => 'array',
        'attributs' => 'array',
        'poids' => 'decimal:2',
        'dimensions_longueur' => 'decimal:2',
        'dimensions_largeur' => 'decimal:2',
        'dimensions_hauteur' => 'decimal:2',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function articlesPanier()
    {
        return $this->hasMany(ArticlePanier::class, 'produit_id');
    }

    public function articlesCommande()
    {
        return $this->hasMany(ArticleCommande::class, 'produit_id');
    }

    public function avis()
    {
        return $this->hasMany(AvisClient::class, 'produit_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'produit_id');
    }

    public function scopeActive($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantite_stock', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('est_mise_en_avant', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('categorie_id', $categoryId);
    }

    public function getPrixFormateAttribute()
    {
        return number_format($this->prix, 2, ',', ' ') . ' €';
    }

    public function getEstEnStockAttribute()
    {
        return $this->quantite_stock > 0;
    }
}
