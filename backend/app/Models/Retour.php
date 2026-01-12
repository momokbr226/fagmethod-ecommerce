<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retour extends Model
{
    use HasFactory;

    protected $table = 'retours';

    protected $fillable = [
        'commande_id',
        'utilisateur_id',
        'article_commande_id',
        'type_retour',
        'motif_retour',
        'description_motif',
        'statut_retour',
        'montant_remboursement',
        'reference_remboursement',
        'images_retour',
        'notes_admin',
        'date_demande',
        'date_traitement',
        'date_remboursement'
    ];

    protected $casts = [
        'montant_remboursement' => 'decimal:2',
        'images_retour' => 'array',
        'date_demande' => 'datetime',
        'date_traitement' => 'datetime',
        'date_remboursement' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function articleCommande()
    {
        return $this->belongsTo(ArticleCommande::class, 'article_commande_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('statut_retour', $status);
    }

    public function getTypeRetourLabelAttribute()
    {
        $labels = [
            'remboursement' => 'Remboursement',
            'echange' => 'Échange'
        ];

        return $labels[$this->type_retour] ?? $this->type_retour;
    }

    public function getMotifRetourLabelAttribute()
    {
        $labels = [
            'produit_defectueux' => 'Produit défectueux',
            'mauvaise_taille' => 'Mauvaise taille',
            'ne_convient_pas' => 'Ne convient pas',
            'erreur_commande' => 'Erreur de commande',
            'autre' => 'Autre'
        ];

        return $labels[$this->motif_retour] ?? $this->motif_retour;
    }

    public function getStatutRetourLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
            'en_cours' => 'En cours',
            'traite' => 'Traité'
        ];

        return $labels[$this->statut_retour] ?? $this->statut_retour;
    }

    public function getMontantRemboursementFormateAttribute()
    {
        return number_format($this->montant_remboursement, 2, ',', ' ') . ' €';
    }
}
