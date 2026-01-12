<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    use HasFactory;

    protected $table = 'adresses';

    protected $fillable = [
        'utilisateur_id',
        'type',
        'prenom',
        'nom',
        'entreprise',
        'adresse_ligne_1',
        'adresse_ligne_2',
        'ville',
        'code_postal',
        'pays',
        'telephone',
        'instructions_livraison',
        'est_par_defaut'
    ];

    protected $casts = [
        'est_par_defaut' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('utilisateur_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('est_par_defaut', true);
    }

    public function getAdresseCompleteAttribute()
    {
        $adresse = $this->adresse_ligne_1;
        if ($this->adresse_ligne_2) {
            $adresse .= ', ' . $this->adresse_ligne_2;
        }
        $adresse .= ', ' . $this->code_postal . ' ' . $this->ville;
        $adresse .= ', ' . $this->pays;

        return $adresse;
    }
}
