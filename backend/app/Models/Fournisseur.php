<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fournisseurs';

    protected $fillable = [
        'nom',
        'raison_sociale',
        'siret',
        'numero_tva',
        'code_fournisseur',
        'email',
        'telephone',
        'fax',
        'site_web',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'nom_contact',
        'prenom_contact',
        'fonction_contact',
        'email_contact',
        'telephone_contact',
        'delai_livraison_moyen',
        'montant_minimum_commande',
        'conditions_paiement',
        'notes',
        'est_actif'
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'delai_livraison_moyen' => 'decimal:2',
        'montant_minimum_commande' => 'decimal:2',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'fournisseur_id');
    }

    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Retourne le nom complet du contact
     */
    public function getNomCompletContactAttribute(): string
    {
        return trim("{$this->prenom_contact} {$this->nom_contact}");
    }
}
