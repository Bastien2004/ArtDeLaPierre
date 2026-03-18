<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StockCalculationTest extends TestCase
{
    /**
     * Grille de tarifs identique à celle utilisée dans tes fichiers.
     */
    private array $tarifs = [
        2  => 33.25, 3  => 44.00, 4  => 50.50, 5  => 51.50,
        6  => 60.75, 8  => 70.75, 10 => 83.75, 12 => 100.75,
        15 => 135.75, 16 => 362.75, 18 => 414.25, 20 => 466.75,
        22 => 503.25, 24 => 569.75, 25 => 599.75, 28 => 685.00, 30 => 739.00
    ];

    /**
     * Fonction de calcul simulant la logique du code HTML/Blade
     */
    private function calculerValeur($longueur, $largeur, $quantite, $epaisseur)
    {
        $surface = $longueur * $largeur * $quantite;
        $prixM2 = 0;

        // Tri pour assurer la logique de palier
        ksort($this->tarifs);

        foreach ($this->tarifs as $seuil => $prix) {
            if ($seuil >= $epaisseur) {
                $prixM2 = $prix;
                break;
            }
        }

        if ($prixM2 > 0) {
            return $surface * $prixM2;
        } else {
            // Formule > 30cm : L * l * (E/100) * 2500 * Qte
            return ($longueur * $largeur * ($epaisseur / 100) * 2500) * $quantite;
        }
    }

    /** @test */
    public function test_calcul_palier_exact()
    {
        // Test 2cm : 1m x 1m x 1pcs @ 33.25€
        $valeur = $this->calculerValeur(1, 1, 1, 2);
        $this->assertEquals(33.25, $valeur);
    }

    /** @test */
    public function test_calcul_palier_arrondi_superieur()
    {
        // Test 7cm : Doit prendre le prix de 8cm (70.75€)
        // 1m x 1m x 1pcs @ 70.75€
        $valeur = $this->calculerValeur(1, 1, 1, 7);
        $this->assertEquals(70.75, $valeur);

        // Test 14cm : Doit prendre le prix de 15cm (135.75€)
        $valeur = $this->calculerValeur(1, 1, 1, 14);
        $this->assertEquals(135.75, $valeur);
    }

    /** @test */
    public function test_calcul_volume_hors_grille()
    {
        // Test 40cm : Doit utiliser la formule L * l * E_m * 2500
        // 2m x 1m x 1pcs x 0.40m x 2500€ = 2000€
        $valeur = $this->calculerValeur(2, 1, 1, 40);
        $this->assertEquals(2000.00, $valeur);
    }

    /** @test */
    public function test_calcul_avec_quantite()
    {
        // Test 3cm : 2m x 2m x 3pcs @ 44.00€
        // Surface = 12m2 * 44 = 528€
        $valeur = $this->calculerValeur(2, 2, 3, 3);
        $this->assertEquals(528.00, $valeur);
    }
}
