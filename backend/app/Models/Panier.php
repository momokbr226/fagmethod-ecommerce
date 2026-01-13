<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;

    protected $table = 'paniers';

    protected $fillable = [
        'utilisateur_id',
        'session_id',
        'sous_total',
        'montant_tva',
        'total',
        'frais_livraison',
        'remise',
        'devise',
        'code_promo',
        'expire_le'
    ];

    protected $casts = [
        'sous_total' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'total' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'remise' => 'decimal:2',
        'expire_le' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function articles()
    {
        return $this->hasMany(ArticlePanier::class, 'panier_id');
    }

    public function getTotalFormateAttribute()
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }

    public function calculerTotaux()
    {
        // Charger les articles si pas déjà chargés
        if (!$this->relationLoaded('articles')) {
            $this->load('articles');
        }

        // Calculer le sous-total
        $this->sous_total = $this->articles->sum(function ($article) {
            return $article->prix_unitaire * $article->quantite;
        });

        // Calculer la TVA (20% par défaut)
        $tauxTVA = 0.20;
        $this->montant_tva = $this->sous_total * $tauxTVA;

        // Calculer le total
        $this->total = $this->sous_total + $this->montant_tva + ($this->frais_livraison ?? 0) - ($this->remise ?? 0);
        
        $this->save();
    }

    public function getNombreArticlesAttribute()
    {
        return $this->articles->sum('quantite');
    }

    public function estVide()
    {
        return $this->articles->count() === 0;
    }

    public function vider()
    {
        $this->articles()->delete();
        $this->sous_total = 0;
        $this->montant_tva = 0;
        $this->total = 0;
        $this->remise = 0;
        $this->code_promo = null;
        $this->save();
    }

    public function ajouterProduit($produit, $quantite = 1)
    {
        // Vérifier si le produit est déjà dans le panier
        $articleExistant = $this->articles()->where('produit_id', $produit->id)->first();

        if ($articleExistant) {
            // Mettre à jour la quantité
            $articleExistant->quantite += $quantite;
            $articleExistant->save();
        } else {
            // Créer un nouvel article
            $this->articles()->create([
                'produit_id' => $produit->id,
                'quantite' => $quantite,
                'prix_unitaire' => $produit->prix_actuel,
            ]);
        }

        $this->calculerTotaux();
    }

    public function updateTotals()
    {
        $this->calculerTotaux();
    }
}
