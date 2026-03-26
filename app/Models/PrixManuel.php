<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrixManuel extends Model
{
    protected $table = 'prix_manuels';

    protected $fillable = [
        'nom',
        'prix',
    ];
}
