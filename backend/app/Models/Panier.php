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

    public function updateTotals()
    {
        $this->sous_total = $this->articles->sum(function ($article) {
            return $article->prix_unitaire * $article->quantite;
        });

        $this->total = $this->sous_total + $this->frais_livraison - $this->remise;
        
        $this->save();
    }
}
