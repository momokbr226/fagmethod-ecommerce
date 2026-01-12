<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'nom_complet',
        'email',
        'telephone',
        'sujet_contact',
        'sujet_personnalise',
        'message',
        'statut_contact',
        'priorite',
        'utilisateur_id',
        'assigne_a',
        'reponse',
        'date_reponse',
        'delai_reponse_heures',
        'fichiers_joints',
        'reference_ticket'
    ];

    protected $casts = [
        'fichiers_joints' => 'array',
        'date_reponse' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function assigneA()
    {
        return $this->belongsTo(Utilisateur::class, 'assigne_a');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('statut_contact', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priorite', $priority);
    }

    public function getSujetContactLabelAttribute()
    {
        $labels = [
            'information' => 'Information',
            'support_technique' => 'Support technique',
            'reclamation' => 'Réclamation',
            'suggestion' => 'Suggestion',
            'partenariat' => 'Partenariat'
        ];

        return $labels[$this->sujet_contact] ?? $this->sujet_personnalise ?? 'Autre';
    }

    public function getStatutContactLabelAttribute()
    {
        $labels = [
            'nouveau' => 'Nouveau',
            'en_cours' => 'En cours',
            'traite' => 'Traité',
            'ferme' => 'Fermé'
        ];

        return $labels[$this->statut_contact] ?? $this->statut_contact;
    }

    public function getPrioriteLabelAttribute()
    {
        $labels = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];

        return $labels[$this->priorite] ?? $this->priorite;
    }
}
