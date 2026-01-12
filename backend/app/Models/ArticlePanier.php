<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlePanier extends Model
{
    use HasFactory;

    protected $table = 'articles_panier';

    protected $fillable = [
        'panier_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'total_ligne',
        'attributs_produit',
        'notes_article'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'total_ligne' => 'decimal:2',
        'attributs_produit' => 'array',
    ];

    public function panier()
    {
        return $this->belongsTo(Panier::class, 'panier_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            $article->total_ligne = $article->prix_unitaire * $article->quantite;
        });
    }
}
