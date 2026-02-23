<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $fillable = [
        'client', 'adresse', 'typePierre', 'longueurCM', 'largeurCM',
        'epaisseurCM', 'matiere', 'prixM2', 'rejingotML', 'oreilles'
    ];

    public function supplements()
    {
        return $this->belongsToMany(Supplement::class)->withPivot('quantite');
    }
}
