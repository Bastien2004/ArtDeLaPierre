<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAutre extends Model
{
    protected $table = 'stock_autres';

    protected $fillable = [
        'nom',
        'matiere',
        'longueur',
        'largeur',
        'epaisseur',
        'quantite',
        'prix_m2',
        'notes'
    ];

    /**
     * Surface unitaire d'une pierre (m²)
     */
    public function getSurfaceUnitaireAttribute(): float
    {
        return $this->longueur * $this->largeur;
    }

    /**
     * Surface totale du lot (m²)
     */
    public function getSurfaceTotaleAttribute(): float
    {
        return $this->longueur * $this->largeur * $this->quantite;
    }

    /**
     * Valeur totale estimée du lot (€)
     */
    public function getValeurTotaleAttribute(): float
    {
        return $this->surface_totale * $this->prix_m2;
    }
}
