<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravailTarif extends Model
{
    protected $table = 'travail_tarifs';
    protected $fillable = ['nom', 'unite', 'prix'];
}
