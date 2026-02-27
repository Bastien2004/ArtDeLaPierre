<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = ['type_client', 'finition', 'epaisseur', 'prix_m2'];
}
