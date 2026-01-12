<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $table = 'newsletters';

    protected $fillable = [
        'email',
        'nom_complet',
        'statut_abonnement',
        'token_desinscription',
        'date_inscription',
        'date_desinscription',
        'date_confirmation',
        'ip_inscription',
        'pays',
        'langue',
        'preferences',
        'nombre_emails_envoyes',
        'nombre_ouvertures',
        'nombre_clics',
        'derniere_ouverture',
        'dernier_clic'
    ];

    protected $casts = [
        'preferences' => 'array',
        'date_inscription' => 'datetime',
        'date_desinscription' => 'datetime',
        'date_confirmation' => 'datetime',
        'derniere_ouverture' => 'datetime',
        'dernier_clic' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopeActive($query)
    {
        return $query->where('statut_abonnement', 'actif');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('statut_abonnement', 'desinscrit');
    }

    public function scopePending($query)
    {
        return $query->where('statut_abonnement', 'en_attente');
    }

    public function getStatutAbonnementLabelAttribute()
    {
        $labels = [
            'actif' => 'Actif',
            'desinscrit' => 'Désinscrit',
            'en_attente' => 'En attente'
        ];

        return $labels[$this->statut_abonnement] ?? $this->statut_abonnement;
    }

    public function getTauxOuvertureAttribute()
    {
        if ($this->nombre_emails_envoyes === 0) {
            return 0;
        }

        return round(($this->nombre_ouvertures / $this->nombre_emails_envoyes) * 100, 2);
    }

    public function getTauxClicAttribute()
    {
        if ($this->nombre_emails_envoyes === 0) {
            return 0;
        }

        return round(($this->nombre_clics / $this->nombre_emails_envoyes) * 100, 2);
    }
}
