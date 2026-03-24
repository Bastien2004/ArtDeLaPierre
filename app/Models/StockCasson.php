<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCasson extends Model
{
    protected $fillable = [
        'quantite',
        'matiere',
        'longueur',
        'largeur',
        'epaisseur',
    ];
}
