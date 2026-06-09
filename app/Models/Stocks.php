<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    protected $fillable = [
        'matiere',
        'quantite',
        'longueur',
        'largeur',
        'epaisseur'
    ];

    public function getSurfaceAttribute()
    {
        return $this->longueur * $this->largeur * $this->quantite;
    }
}
