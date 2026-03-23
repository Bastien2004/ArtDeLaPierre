<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; color: #2c3e50; font-size: 10pt; line-height: 1.4; }
        .header { border-bottom: 3px solid #d4af37; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-text { font-size: 22pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .epaisseur-section { page-break-inside: avoid; margin-bottom: 40px; }
        .epaisseur-badge { background: #2c3e50; color: white; display: inline-block; padding: 8px 20px; font-size: 14pt; font-weight: bold; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f4f7f6; color: #7f8c8d; text-transform: uppercase; font-size: 8pt; padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .txt-right { text-align: right; }
        .price-tag { color: #7f8c8d; font-style: italic; }
        .subtotal-row { background: #fcfcfc; font-weight: bold; color: #2c3e50; }
        .grand-total-box { margin-top: 50px; background: #2c3e50; color: white; padding: 20px; border-left: 10px solid #d4af37; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo-text">L'Art de la Pierre</div>
    <div style="color: #7f8c8d;">Inventaire Général des Stocks | {{ date('d/m/Y') }}</div>
</div>

@php
    $totalSurfaceGlobale = 0;
    $totalValeurGlobale = 0;

    $tarifs = [
        2 => 33.25, 3 => 44.00, 4 => 50.50, 5 => 51.50, 6 => 60.75, 8 => 70.75, 10 => 83.75, 12 => 100.75, 15 => 135.75,
        16 => 362.75, 18 => 414.25, 20 => 466.75, 22 => 503.25, 24 => 569.75, 25 => 599.75, 28 => 685.00, 30 => 739.00
    ];
    ksort($tarifs);
@endphp

@foreach($stocksGroupes as $epaisseur => $items)
    @php
        $surfaceEpaisseur = $items->sum(fn($i) => $i->longueur * $i->largeur * $i->quantite);
        $valeurEpaisseur = 0;

        // Calcul de la valeur pour chaque item (car la formule >30cm dépend des dimensions de l'item)
        foreach($items as $item) {
            $surfaceItem = $item->longueur * $item->largeur * $item->quantite;
            $prixM2 = 0;

            foreach($tarifs as $seuil => $prix) {
                if ($seuil >= $item->epaisseur) {
                    $prixM2 = $prix;
                    break;
                }
            }

            if ($prixM2 > 0) {
                $valeurEpaisseur += $surfaceItem * $prixM2;
            } else {
                // TA FORMULE : Longueur * Largeur * (Epaisseur/100) * 2500 * Quantité
                $valeurEpaisseur += ($item->longueur * $item->largeur * ($item->epaisseur / 100) * 2500) * $item->quantite;
            }
        }

        $totalSurfaceGlobale += $surfaceEpaisseur;
        $totalValeurGlobale += $valeurEpaisseur;
    @endphp

    <div class="epaisseur-section">
        <div class="epaisseur-badge">{{ $epaisseur }} CM</div>
        <table>
            <thead>
            <tr><th>Matière</th><th>Quantité</th><th>Dimensions</th><th class="txt-right">Surface</th></tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td><strong>{{ $item->matiere }}</strong></td>
                    <td>{{ $item->quantite }} pcs</td>
                    <td>{{ number_format($item->longueur,2) }} x {{ number_format($item->largeur,2) }} m</td>
                    <td class="txt-right">{{ number_format($item->longueur * $item->largeur * $item->quantite, 2, ',', ' ') }} m²</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="3" class="txt-right">TOTAL {{ $epaisseur }} CM</td>
                <td class="txt-right">{{ number_format($surfaceEpaisseur, 2, ',', ' ') }} m²</td>
            </tr>
            <tr>
                <td colspan="4" class="txt-right price-tag">
                    Valeur estimée pour {{ $epaisseur }} CM : {{ number_format($valeurEpaisseur, 2, ',', ' ') }} €
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endforeach

{{-- ── Section Blocs ─────────────────────────────────────────────────────── --}}
@if(isset($blocs) && $blocs->count())
    @php
        $totalVolumeBlocs = 0;
        $totalValeurBlocs = 0;
    @endphp

    <div class="epaisseur-section" style="margin-top: 40px;">
        <div class="epaisseur-badge" style="background: #7f8c8d;">BLOCS</div>
        <table>
            <thead>
            <tr>
                <th>Référence</th>
                <th>Matière</th>
                <th>Dimensions (m)</th>
                <th class="txt-right">Volume (m³)</th>
                <th class="txt-right">Poids (t)</th>
                <th class="txt-right">Prix Est.</th>
            </tr>
            </thead>
            <tbody>
            @foreach($blocs as $bloc)
                @php
                    $volume = $bloc->hauteur * $bloc->largeur * $bloc->longueur;
                    $prix   = $volume * 155.75;
                    $totalVolumeBlocs += $volume;
                    $totalValeurBlocs += $prix;
                @endphp
                <tr>
                    <td><strong>{{ $bloc->reference ?? '—' }}</strong></td>
                    <td>{{ $bloc->matiere }}</td>
                    <td>{{ number_format($bloc->longueur,2) }} × {{ number_format($bloc->largeur,2) }} × {{ number_format($bloc->hauteur,2) }} m</td>
                    <td class="txt-right">{{ number_format($volume, 3, ',', ' ') }} m³</td>
                    <td class="txt-right">{{ number_format($bloc->poids, 3, ',', ' ') }} t</td>
                    <td class="txt-right">{{ number_format($prix, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="3" class="txt-right">TOTAL BLOCS</td>
                <td class="txt-right">{{ number_format($totalVolumeBlocs, 3, ',', ' ') }} m³</td>
                <td></td>
                <td class="txt-right">{{ number_format($totalValeurBlocs, 2, ',', ' ') }} €</td>
            </tr>
            </tbody>
        </table>
    </div>
    @php $totalValeurGlobale += $totalValeurBlocs; @endphp
@endif

<div class="grand-total-box">
    <table style="color: white; margin: 0; width: 100%;">
        <tr>
            <td style="font-size: 14pt; border: none;">SURFACE TOTALE</td>
            <td class="txt-right" style="font-size: 14pt; border: none;">{{ number_format($totalSurfaceGlobale, 2, ',', ' ') }} m²</td>
        </tr>
        <tr>
            <td style="font-size: 18pt; border: none; padding-top: 10px;">VALEUR TOTALE ESTIMÉE</td>
            <td class="txt-right" style="font-size: 18pt; border: none; font-weight: bold; color: #d4af37;">{{ number_format($totalValeurGlobale, 2, ',', ' ') }} €</td>
        </tr>
    </table>
</div>
</body>
</html>
