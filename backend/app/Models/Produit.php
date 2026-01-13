<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produits';

    protected $fillable = [
        'nom',
        'slug',
        'reference',
        'description',
        'description_courte',
        'categorie_id',
        'marque_id',
        'famille_id',
        'fournisseur_id',
        'prix',
        'prix_promo',
        'date_debut_promo',
        'date_fin_promo',
        'prix_achat',
        'quantite_stock',
        'seuil_alerte_stock',
        'statut_stock',
        'gestion_stock',
        'poids',
        'longueur',
        'largeur',
        'hauteur',
        'image_principale',
        'images',
        'est_visible',
        'est_nouveau',
        'est_vedette',
        'est_promo',
        'ordre',
        'meta_titre',
        'meta_description',
        'meta_donnees',
        'caracteristiques',
        'vues',
        'ventes',
        'note_moyenne',
        'nombre_avis'
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'prix_promo' => 'decimal:2',
        'prix_achat' => 'decimal:2',
        'date_debut_promo' => 'date',
        'date_fin_promo' => 'date',
        'quantite_stock' => 'integer',
        'seuil_alerte_stock' => 'integer',
        'gestion_stock' => 'boolean',
        'poids' => 'decimal:2',
        'longueur' => 'decimal:2',
        'largeur' => 'decimal:2',
        'hauteur' => 'decimal:2',
        'images' => 'array',
        'est_visible' => 'boolean',
        'est_nouveau' => 'boolean',
        'est_vedette' => 'boolean',
        'est_promo' => 'boolean',
        'ordre' => 'integer',
        'meta_donnees' => 'array',
        'caracteristiques' => 'array',
        'vues' => 'integer',
        'ventes' => 'integer',
        'note_moyenne' => 'decimal:2',
        'nombre_avis' => 'integer',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function marque()
    {
        return $this->belongsTo(Marque::class, 'marque_id');
    }

    public function famille()
    {
        return $this->belongsTo(FamilleProduit::class, 'famille_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Utilisateur::class, 'fournisseur_id');
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

    public function scopeVisible($query)
    {
        return $query->where('est_visible', true);
    }

    public function scopeEnStock($query)
    {
        return $query->where('statut_stock', '!=', 'rupture');
    }

    public function scopeVedette($query)
    {
        return $query->where('est_vedette', true);
    }

    public function scopeNouveau($query)
    {
        return $query->where('est_nouveau', true);
    }

    public function scopeEnPromo($query)
    {
        return $query->where('est_promo', true)
            ->whereNotNull('prix_promo')
            ->where(function($q) {
                $q->whereNull('date_fin_promo')
                  ->orWhere('date_fin_promo', '>=', now());
            });
    }

    public function scopeParCategorie($query, $categorieId)
    {
        return $query->where('categorie_id', $categorieId);
    }

    public function scopeParMarque($query, $marqueId)
    {
        return $query->where('marque_id', $marqueId);
    }

    public function scopeParFamille($query, $familleId)
    {
        return $query->where('famille_id', $familleId);
    }

    public function scopeRecherche($query, $terme)
    {
        return $query->where(function($q) use ($terme) {
            $q->where('nom', 'like', "%{$terme}%")
              ->orWhere('description', 'like', "%{$terme}%")
              ->orWhere('reference', 'like', "%{$terme}%");
        });
    }

    public function getPrixFormateAttribute()
    {
        return number_format($this->prix, 2, ',', ' ') . ' €';
    }

    public function getPrixActuelAttribute()
    {
        if ($this->est_promo && $this->prix_promo) {
            $maintenant = now();
            if ((!$this->date_debut_promo || $this->date_debut_promo <= $maintenant) &&
                (!$this->date_fin_promo || $this->date_fin_promo >= $maintenant)) {
                return $this->prix_promo;
            }
        }
        return $this->prix;
    }

    public function getReductionPourcentageAttribute()
    {
        if ($this->est_promo && $this->prix_promo && $this->prix > 0) {
            return round((($this->prix - $this->prix_promo) / $this->prix) * 100);
        }
        return 0;
    }

    public function getEstEnStockAttribute()
    {
        return $this->statut_stock !== 'rupture' && $this->quantite_stock > 0;
    }

    public function getEstStockFaibleAttribute()
    {
        return $this->gestion_stock && $this->quantite_stock > 0 && $this->quantite_stock <= $this->seuil_alerte_stock;
    }

    public function mettreAJourStatutStock()
    {
        if (!$this->gestion_stock) {
            $this->statut_stock = 'en_stock';
            return;
        }

        if ($this->quantite_stock <= 0) {
            $this->statut_stock = 'rupture';
        } elseif ($this->quantite_stock <= $this->seuil_alerte_stock) {
            $this->statut_stock = 'stock_faible';
        } else {
            $this->statut_stock = 'en_stock';
        }
    }

    public function incrementerVues()
    {
        $this->increment('vues');
    }

    public function incrementerVentes($quantite = 1)
    {
        $this->increment('ventes', $quantite);
        if ($this->gestion_stock) {
            $this->decrement('quantite_stock', $quantite);
            $this->mettreAJourStatutStock();
            $this->save();
        }
    }
}
