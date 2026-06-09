<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specificite extends Model
{
    use HasFactory;

    protected $fillable = [
        'devis_id',
        'nom',
        'prix',
        'tailleRejingot',
        'base_price',
        'unite',
    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }
}
