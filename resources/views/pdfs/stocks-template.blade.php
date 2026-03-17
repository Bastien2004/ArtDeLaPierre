<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #2c3e50; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }

        .matiere-group { page-break-inside: avoid; margin-bottom: 25px; }
        .matiere-title {
            background-color: #2c3e50;
            color: white;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f8f9fa; padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; color: #7f8c8d; }
        td { padding: 8px; border-bottom: 1px solid #eee; }

        .recap-section { margin-top: 30px; border-top: 2px double #2c3e50; padding-top: 10px; }
        .total-final { background-color: #d4af37; color: white; font-weight: bold; }
        .txt-right { text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #95a5a6; }
    </style>
</head>
<body>

<div class="header">
    <h1>Inventaire des Stocks - Art de la Pierre</h1>
    <p>Document généré le {{ date('d/m/Y') }}</p>
</div>

@php $surfaceGlobale = 0; @endphp

@foreach($stocksGroupes as $matiere => $items)
    <div class="matiere-group">
        <div class="matiere-title">{{ $matiere }} cm</div>
        <table>
            <thead>
            <tr>
                <th style="width: 15%;">Quantité</th>
                <th style="width: 35%;">Dimensions (L x l)</th>
                <th style="width: 20%;">Épaisseur</th>
                <th style="width: 30%; text-align: right;">Surface Totale</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                @php $surface = $item->longueur * $item->largeur * $item->quantite; @endphp
                <tr>
                    <td>{{ $item->quantite }} pcs</td>
                    <td>{{ number_format($item->longueur, 2) }}m x {{ number_format($item->largeur, 2) }}m</td>
                    <td><strong>{{ $item->epaisseur }} cm</strong></td>
                    <td class="txt-right">{{ number_format($surface, 2, ',', ' ') }} m²</td>
                </tr>
                @php $surfaceGlobale += $surface; @endphp
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach

<div class="recap-section">
    <h3>Résumé de l'Inventaire</h3>
    <table>
        <thead>
        <tr>
            <th>Désignation</th>
            <th class="txt-right">Surface Totale</th>
        </tr>
        </thead>
        <tbody>
        @foreach($stocksGroupes as $matiere => $items)
            <tr>
                <td>{{ $matiere }} cm</td>
                <td class="txt-right">{{ number_format($items->sum(fn($i) => $i->longueur * $i->largeur * $i->quantite), 2, ',', ' ') }} m²</td>
            </tr>
        @endforeach
        <tr class="total-final">
            <td>TOTAL GÉNÉRAL DU STOCK</td>
            <td class="txt-right">{{ number_format($surfaceGlobale, 2, ',', ' ') }} m²</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="footer">
    Art de la Pierre — Page <span class="pagenum"></span>
</div>

</body>
</html>
