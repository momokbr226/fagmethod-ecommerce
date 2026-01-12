<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historique extends Model
{
    use HasFactory;

    protected $table = 'historiques';

    protected $fillable = [
        'utilisateur_id',
        'type_action',
        'table_concernee',
        'id_enregistrement',
        'description_action',
        'anciennes_valeurs',
        'nouvelles_valeurs',
        'adresse_ip',
        'user_agent',
        'date_action'
    ];

    protected $casts = [
        'anciennes_valeurs' => 'array',
        'nouvelles_valeurs' => 'array',
        'date_action' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('utilisateur_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('type_action', $action);
    }

    public function scopeByTable($query, $table)
    {
        return $query->where('table_concernee', $table);
    }

    public function getTypeActionLabelAttribute()
    {
        $labels = [
            'creation' => 'Création',
            'modification' => 'Modification',
            'suppression' => 'Suppression',
            'connexion' => 'Connexion',
            'deconnexion' => 'Déconnexion',
            'commande' => 'Commande',
            'paiement' => 'Paiement',
            'inscription' => 'Inscription',
            'desinscription' => 'Désinscription'
        ];

        return $labels[$this->type_action] ?? $this->type_action;
    }
}
