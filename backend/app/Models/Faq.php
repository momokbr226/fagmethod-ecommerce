<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faq';

    protected $fillable = [
        'question',
        'reponse',
        'categorie_faq',
        'ordre_affichage',
        'est_publie',
        'nombre_vues',
        'est_utile',
        'nombre_votes_utiles',
        'tags',
        'utilisateur_id'
    ];

    protected $casts = [
        'est_publie' => 'boolean',
        'est_utile' => 'boolean',
        'tags' => 'array',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function scopePublished($query)
    {
        return $query->where('est_publie', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('categorie_faq', $category);
    }

    public function scopeOrderByOrder($query)
    {
        return $query->orderBy('ordre_affichage', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('nombre_vues', 'desc');
    }

    public function scopeUseful($query)
    {
        return $query->where('est_utile', true);
    }

    public function incrementerVues()
    {
        $this->increment('nombre_vues');
    }

    public function incrementerVotesUtiles()
    {
        $this->increment('nombre_votes_utiles');
    }

    public function marquerCommeUtile()
    {
        $this->update(['est_utile' => true]);
    }
}
