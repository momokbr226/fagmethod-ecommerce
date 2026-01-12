<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodesPromo extends Model
{
    use HasFactory;

    protected $table = 'codes_promo';

    protected $fillable = [
        'code',
        'nom_promo',
        'description',
        'type_remise',
        'valeur_remise',
        'montant_minimum_achat',
        'remise_maximale',
        'utilisation_max',
        'utilisation_par_client_max',
        'utilisations_count',
        'date_debut',
        'date_fin',
        'est_actif',
        'produits_applicables',
        'categories_applicables',
        'clients_applicables',
        'conditions_utilisation'
    ];

    protected $casts = [
        'valeur_remise' => 'decimal:2',
        'montant_minimum_achat' => 'decimal:2',
        'remise_maximale' => 'decimal:2',
        'utilisation_max' => 'integer',
        'utilisation_par_client_max' => 'integer',
        'utilisations_count' => 'integer',
        'produits_applicables' => 'array',
        'categories_applicables' => 'array',
        'clients_applicables' => 'array',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'est_actif' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeValid($query)
    {
        return $query->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now());
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function isExpired()
    {
        return $this->date_fin && $this->date_fin->isPast();
    }

    public function isValid()
    {
        return !$this->isExpired() && $this->est_actif && $this->utilisation_max > $this->utilisations_count;
    }

    public function hasReachedMaxUsage($clientId)
    {
        if (!$this->clients_applicables || !in_array($clientId, $this->clients_applicables)) {
            return false;
        }

        return $this->utilisation_par_client_max <= $this->utilisations_count;
    }

    public function calculateRemise($montant)
    {
        if ($this->type_remise === 'pourcentage') {
            return $montant * ($this->valeur_remise / 100);
        } else {
            return min($this->valeur_remise, $montant);
        }
    }

    public function getRemiseMaximaleFormateeAttribute()
    {
        return number_format($this->remise_maximale, 2, ',', ' ') . ' €';
    }

    public function getValeurRemiseFormateeAttribute()
    {
        return $this->type_remise === 'pourcentage' 
            ? $this->valeur_remise . '%' 
            : number_format($this->valeur_remise, 2, ',', ' ') . ' €';
    }
}
