<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $table = 'commandes';

    protected $fillable = [
        'numero_commande',
        'utilisateur_id',
        'montant_total',
        'sous_total',
        'montant_tva',
        'frais_livraison',
        'montant_remise',
        'statut',
        'statut_paiement',
        'mode_paiement',
        'reference_paiement',
        'adresse_livraison',
        'adresse_facturation',
        'notes_commande',
        'notes_livraison',
        'date_expedition',
        'date_livraison',
        'numero_suivi',
        'transporteur',
        'instructions_livraison'
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'sous_total' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'montant_remise' => 'decimal:2',
        'adresse_livraison' => 'array',
        'adresse_facturation' => 'array',
        'date_expedition' => 'datetime',
        'date_livraison' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function articles()
    {
        return $this->hasMany(ArticleCommande::class, 'commande_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'commande_id');
    }

    public function retours()
    {
        return $this->hasMany(Retour::class, 'commande_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('utilisateur_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    public function getStatutLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'en_preparation' => 'En préparation',
            'expedie' => 'Expédiée',
            'livre' => 'Livrée',
            'annule' => 'Annulée'
        ];

        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutPaiementLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'paye' => 'Payée',
            'echoue' => 'Échouée',
            'rembourse' => 'Remboursée'
        ];

        return $labels[$this->statut_paiement] ?? $this->statut_paiement;
    }

    public function getMontantTotalFormateAttribute()
    {
        return number_format($this->montant_total, 2, ',', ' ') . ' €';
    }
}
