<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
        'notes',
        // Champs de profil
        'type_profil',
        'raison_sociale',
        'siret',
        'numero_tva',
        'forme_juridique',
        'nom_contact',
        'prenom_contact',
        'fonction_contact'
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

    /**
     * Vérifie si l'utilisateur est une personne morale
     */
    public function estPersonneMorale(): bool
    {
        return $this->type_profil === 'morale';
    }

    /**
     * Vérifie si l'utilisateur est une personne physique
     */
    public function estPersonnePhysique(): bool
    {
        return $this->type_profil === 'physique';
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public function estAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Vérifie si l'utilisateur est un client
     */
    public function estClient(): bool
    {
        return $this->hasRole('client');
    }

    /**
     * Vérifie si l'utilisateur est un fournisseur
     */
    public function estFournisseur(): bool
    {
        return $this->hasRole('fournisseur');
    }

    /**
     * Retourne le nom d'affichage selon le type de profil
     */
    public function getNomAffichageAttribute(): string
    {
        if ($this->estPersonneMorale()) {
            return $this->raison_sociale ?? $this->nom_complet;
        }
        return $this->nom_complet;
    }
}
