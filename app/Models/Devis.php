<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $fillable = [
        'client',
        'reference',
        'typeClient',
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
        'prixUnitairePierre',
        'epaisseur',
        'poids',
        'livraison',
        'datefindevis',
        'prixPose',
        'is_linteau',
        'type_linteau',
        'finition',
    ];

    public function specificites()
    {
        return $this->hasMany(Specificite::class);
    }
}
