<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    use HasFactory;

    protected $table = 'parametres';

    protected $fillable = [
        'cle',
        'groupe',
        'libelle',
        'valeur',
        'type',
        'description',
        'ordre',
        'est_modifiable',
        'est_actif'
    ];

    protected $casts = [
        'est_modifiable' => 'boolean',
        'est_actif' => 'boolean',
        'ordre' => 'integer',
    ];

    public function scopeByGroupe($query, $groupe)
    {
        return $query->where('groupe', $groupe);
    }

    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeModifiable($query)
    {
        return $query->where('est_modifiable', true);
    }

    /**
     * Retourne la valeur formatée selon le type
     */
    public function getValeurFormateeAttribute()
    {
        switch ($this->type) {
            case 'integer':
                return (int) $this->valeur;
            case 'boolean':
                return filter_var($this->valeur, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($this->valeur, true);
            default:
                return $this->valeur;
        }
    }

    /**
     * Méthode statique pour récupérer une valeur de paramètre
     */
    public static function get(string $cle, $default = null)
    {
        $parametre = static::where('cle', $cle)->where('est_actif', true)->first();
        return $parametre ? $parametre->valeur_formatee : $default;
    }

    /**
     * Méthode statique pour définir une valeur de paramètre
     */
    public static function set(string $cle, $valeur): bool
    {
        $parametre = static::where('cle', $cle)->first();
        if ($parametre && $parametre->est_modifiable) {
            $parametre->valeur = is_array($valeur) ? json_encode($valeur) : $valeur;
            return $parametre->save();
        }
        return false;
    }
}
