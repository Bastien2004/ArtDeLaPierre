<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Pest\Arch\Blueprint;

class Poids extends Model
{
    protected $fillable = ['nom', 'epaisseurCM', 'poids_m2'];
}
