<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCasson extends Model
{
    protected $fillable = [
        'matiere',
        'longueur',
        'largeur',
        'epaisseur',
    ];
}
