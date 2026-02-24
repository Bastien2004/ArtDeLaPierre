<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $fillable = [
        'client',
        'adresse',
        'typePierre',
        'nombrePierre',
        'longueurM',
        'largeurM',
        'matiere',
        'prixM2',
        'rejingotML',
        'oreilles',
        'prixHT',
        'prixUnitairePierre'
    ];

    public function specificites()
    {
        return $this->hasMany(Specificite::class);
    }
}
