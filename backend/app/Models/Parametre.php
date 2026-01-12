<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    use HasFactory;

    protected $table = 'parametres';

    protected $fillable = [
        'cle_parametre',
        'valeur_parametre',
        'description',
        'type_parametre',
        'groupe_parametre',
        'est_public',
        'est_modifiable',
        'utilisateur_id'
    ];

    protected $casts = [
        'est_public' => 'boolean',
        'est_modifiable' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('groupe_parametre', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('est_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('est_public', false);
    }

    public function scopeModifiable($query)
    {
        return $query->where('est_modifiable', true);
    }

    public function getValeurFormateeAttribute()
    {
        switch ($this->type_parametre) {
            case 'nombre':
                return is_numeric($this->valeur_parametre) ? number_format($this->valeur_parametre, 2, ',', ' ') : $this->valeur_parametre;
            case 'booleen':
                return $this->valeur_parametre ? 'Oui' : 'Non';
            case 'json':
                return json_decode($this->valeur_parametre, true);
            default:
                return $this->valeur_parametre;
        }
    }
}
