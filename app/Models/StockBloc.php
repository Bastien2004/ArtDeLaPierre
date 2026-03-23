<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBloc extends Model
{
    protected $table = 'stockblocs';

    protected $fillable = [
        'reference',
        'matiere',
        'hauteur',
        'largeur',
        'longueur',
        'poids',
    ];
}
