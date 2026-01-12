<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'nom_complet',
        'email',
        'mot_de_passe',
        'telephone',
        'date_naissance',
        'sexe',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'est_actif',
        'photo_profil',
        'preferences',
        'notes'
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'est_actif' => 'boolean',
        'photo_profil' => 'string',
        'preferences' => 'array',
        'email_verified_at' => 'datetime',
    ];

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'utilisateur_id');
    }

    public function adresses()
    {
        return $this->hasMany(Adresse::class, 'utilisateur_id');
    }

    public function panier()
    {
        return $this->hasOne(Panier::class, 'utilisateur_id');
    }

    public function articlesPanier()
    {
        return $this->hasManyThrough(ArticlePanier::class, Panier::class, 'utilisateur_id');
    }

    public function avis()
    {
        return $this->hasMany(AvisClient::class, 'utilisateur_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'utilisateur_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'utilisateur_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'utilisateur_id');
    }

    public function newsletters()
    {
        return $this->hasMany(Newsletter::class, 'utilisateur_id');
    }

    public function jetonsAcces()
    {
        return $this->hasMany(JetonAcces::class, 'utilisateur_id');
    }

    public function historiques()
    {
        return $this->hasMany(Historique::class, 'utilisateur_id');
    }

    public function getAuthPasswordName()
    {
        return 'mot_de_passe';
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }
}
