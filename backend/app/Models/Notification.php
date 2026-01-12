<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'utilisateur_id',
        'titre_notification',
        'contenu_notification',
        'type_notification',
        'canal_notification',
        'est_lue',
        'date_lecture',
        'donnees_supplementaires',
        'lien_action',
        'date_envoi',
        'date_expiration'
    ];

    protected $casts = [
        'est_lue' => 'boolean',
        'donnees_supplementaires' => 'array',
        'date_lecture' => 'datetime',
        'date_envoi' => 'datetime',
        'date_expiration' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('est_lue', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type_notification', $type);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('canal_notification', $channel);
    }

    public function getTypeNotificationLabelAttribute()
    {
        $labels = [
            'commande' => 'Commande',
            'paiement' => 'Paiement',
            'livraison' => 'Livraison',
            'promotion' => 'Promotion',
            'avis' => 'Avis',
            'systeme' => 'Système'
        ];

        return $labels[$this->type_notification] ?? $this->type_notification;
    }

    public function getCanalNotificationLabelAttribute()
    {
        $labels = [
            'email' => 'Email',
            'sms' => 'SMS',
            'push' => 'Push',
            'interne' => 'Interne'
        ];

        return $labels[$this->canal_notification] ?? $this->canal_notification;
    }
}
