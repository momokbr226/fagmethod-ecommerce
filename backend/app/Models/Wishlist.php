<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlists';

    protected $fillable = [
        'utilisateur_id',
        'produit_id',
        'nom_liste',
        'notes',
        'est_public',
        'date_ajout'
    ];

    protected $casts = [
        'est_public' => 'boolean',
        'date_ajout' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('utilisateur_id', $userId);
    }

    public function scopePublic($query)
    {
        return $query->where('est_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('est_public', false);
    }
}
