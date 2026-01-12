<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvisClient extends Model
{
    use HasFactory;

    protected $table = 'avis_clients';

    protected $fillable = [
        'utilisateur_id',
        'produit_id',
        'commande_id',
        'note',
        'titre_avis',
        'contenu_avis',
        'statut_avis',
        'images_avis',
        'est_achat_verifie',
        'est_recommande',
        'nombre_votes_utiles',
        'reponse_vendeur',
        'date_reponse'
    ];

    protected $casts = [
        'note' => 'integer',
        'est_achat_verifie' => 'boolean',
        'est_recommande' => 'boolean',
        'images_avis' => 'array',
        'date_reponse' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('statut_avis', 'approuve');
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('produit_id', $productId);
    }

    public function getStatutAvisLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'approuve' => 'Approuvé',
            'rejete' => 'Rejeté'
        ];

        return $labels[$this->statut_avis] ?? $this->statut_avis;
    }

    public function getNoteEtoilesAttribute()
    {
        $etoiles = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->note) {
                $etoiles .= '⭐';
            } else {
                $etoiles .= '☆';
            }
        }
        return $etoiles;
    }
}
