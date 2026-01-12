<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiements';

    protected $fillable = [
        'commande_id',
        'utilisateur_id',
        'montant',
        'devise',
        'methode_paiement',
        'statut',
        'transaction_id',
        'reference_paiement',
        'details_paiement',
        'date_paiement',
        'date_confirmation',
        'motif_echec',
        'frais_transaction',
        'porteur_carte',
        'type_carte',
        'derniers_chiffres_carte'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'frais_transaction' => 'decimal:2',
        'details_paiement' => 'array',
        'date_paiement' => 'datetime',
        'date_confirmation' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopeByCommande($query, $commandeId)
    {
        return $query->where('commande_id', $commandeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    public function getStatutLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'autorise' => 'Autorisé',
            'echoue' => 'Échoué',
            'annule' => 'Annulé',
            'rembourse' => 'Remboursé'
        ];

        return $labels[$this->statut] ?? $this->statut;
    }

    public function getMethodePaiementLabelAttribute()
    {
        $labels = [
            'carte_credit' => 'Carte de crédit',
            'paypal' => 'PayPal',
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'espece' => 'Espèces'
        ];

        return $labels[$this->methode_paiement] ?? $this->methode_paiement;
    }

    public function getMontantFormateAttribute()
    {
        return number_format($this->montant, 2, ',', ' ') . ' €';
    }
}
