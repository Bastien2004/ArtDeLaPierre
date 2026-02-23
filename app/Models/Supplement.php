<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplement extends Model
{
    public function devis()
    {
        return $this->belongsToMany(Devis::class);
    }
}
