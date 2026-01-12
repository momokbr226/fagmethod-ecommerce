<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCommande extends Model
{
    use HasFactory;

    protected $table = 'articles_commande';

    protected $fillable = [
        'commande_id',
        'produit_id',
        'nom_produit',
        'reference_produit',
        'prix_unitaire',
        'quantite',
        'total_ligne',
        'remise_ligne',
        'attributs_produit',
        'notes_article',
        'etat_retour',
        'date_retour',
        'motif_retour'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'total_ligne' => 'decimal:2',
        'remise_ligne' => 'decimal:2',
        'attributs_produit' => 'array',
        'date_retour' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function retours()
    {
        return $this->hasMany(Retour::class, 'article_commande_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            $article->total_ligne = ($article->prix_unitaire * $article->quantite) - $article->remise_ligne;
        });
    }

    public function getTotalLigneFormateAttribute()
    {
        return number_format($this->total_ligne, 2, ',', ' ') . ' €';
    }
}
