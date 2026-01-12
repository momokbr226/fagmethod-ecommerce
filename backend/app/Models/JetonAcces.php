<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JetonAcces extends Model
{
    use HasFactory;

    protected $table = 'jetons_acces';

    protected $fillable = [
        'utilisateur_id',
        'nom_jeton',
        'valeur_jeton',
        'permissions',
        'expire_le',
        'derniere_utilisation',
        'adresse_ip',
        'user_agent',
        'est_actif'
    ];

    protected $casts = [
        'permissions' => 'array',
        'expire_le' => 'datetime',
        'derniere_utilisation' => 'datetime',
        'est_actif' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopeActive($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expire_le', '<', now());
    }

    public function scopeValid($query)
    {
        return $query->where('expire_le', '>', now())->where('est_actif', true);
    }

    public function isExpired()
    {
        return $this->expire_le && $this->expire_le->isPast();
    }

    public function isValid()
    {
        return !$this->isExpired() && $this->est_actif;
    }
}
