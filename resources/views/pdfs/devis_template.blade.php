<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        /* Injection directe du CSS pour éviter les erreurs de protocole Snappy */
        {!! file_get_contents(public_path('css/pdf_devis.css')) !!}
    </style>
</head>
<body>
<div class="page">
    <div class="sender-info">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" class="logo">
        <br><strong>SASU L'ART DE LA PIERRE</strong><br>
        13 bis HAMEAU DE BREAUGIES<br>
        59570 BELLIGNIES<br>
        Tél : 06 15 85 06 25<br>
        @ : frederic.oden.tailleur.pierre@gmail.com
    </div>

    <div class="client-info" style="margin-top: 20px;">
        <strong>{{ $client }}</strong><br>
        {!! nl2br(e($adresse)) !!}<br>
        <span style="text-transform: uppercase; font-weight: bold; margin-top: 5px; display: block;">
            {{ $pays }}
        </span>
    </div>

    <div class="devis-meta">
        <strong>
            Devis N° : {{ $date->format('ymd') }}{{ str_pad($id, 3, '0', STR_PAD_LEFT) }}<br>
            {{$reference}}
        </strong><br>
        Date d'émission : {{ $date->format('d/m/Y') }} <br>
        <span style="float: right;">Période de validité : 60 jours</span>
    </div>

    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th style="width: 50%;">Désignation</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>TVA</th>
                <th style="text-align: right;">Montant HT</th>
            </tr>
            </thead>
            <tbody>
            @php $cumulHT = 0; @endphp {{-- Initialisation du compteur --}}

            @foreach($lignes as $l)
                @php
                    $cumulHT += $l->prixHT;
                @endphp
                <tr>
                    <td>
                        <strong>{{ $l->typePierre }}</strong><br>
                        <small>{{ $l->longueurM }}m x {{ $l->largeurM }}m</small>
                    </td>
                    <td>{{ number_format($l->nombrePierre, 2, ',', ' ') }}</td>
                    <td>{{ number_format($l->prixHT, 2, ',', ' ') }} €</td>
                    <td>20%</td>
                    {{-- On affiche le cumul au lieu du prix unitaire de la ligne --}}
                    <td style="text-align: right;">{{ number_format($cumulHT, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            </tbody>        </table>

        <div class="totals">
            <div class="total-line">Total HT : {{ number_format($totalHT, 2, ',', ' ') }} €</div>
            <div class="total-line">TVA 20% : {{ number_format($totalHT * 0.2, 2, ',', ' ') }} €</div>
            <div class="total-ttc">
                Total TTC : {{ number_format($totalHT * 1.2, 2, ',', ' ') }} €
            </div>
        </div>

        <div class="legal-notices">
            Acompte de 40% à la signature du devis.<br>
            DEVIS HORS POSE, HORS LIVRAISON<br><br>
            <i style="color: #666;">Les Pierres Bleues de Soignies peuvent comporter toutes les particularités d'aspect de la matière : noirures, limés, tâches blanches, coquillages et fossiles. Aucune réclamation concernant ces particularités ne sera prise en considération. Pour la tolérance d'épaisseur 1 à 2 mm (dalles, seuils, appuis...)</i>
        </div>
    </div>

    <div class="signature-box">Signature</div>

    <div class="footer">
        SASU au capital de 1 000 euros<br>
        Siret : 833 976 210 00017 - RCS : VALENCIENNES<br>
        TVA Intracommunautaire : FR 76 833 976 210<br>
        <strong>COORDONNÉES BANCAIRES :</strong><br>
        IBAN : FR76 1627 5500 0008 0021 3604 660 &nbsp;&nbsp; BIC : CEPAFRPP627
    </div>
</div>
</body>
</html>
