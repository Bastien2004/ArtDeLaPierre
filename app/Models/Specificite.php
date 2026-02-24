<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specificite extends Model
{
    use HasFactory;

    // 1. Autoriser l'assignation de masse (indispensable pour le ->create() du contrôleur)
    protected $fillable = [
        'devis_id',
        'nom',
        'prix'
    ];

    // 2. Définir la relation inverse
    // Chaque spécificité appartient à une seule ligne de devis
    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }
}
