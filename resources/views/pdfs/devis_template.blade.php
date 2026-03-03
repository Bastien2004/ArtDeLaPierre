<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        /* Injection directe du CSS */
        {!! file_get_contents(public_path('css/pdf_devis.css')) !!}

        /* Styles spécifiques pour les options */
        .spec-row td {
            font-size: 11px;
            background-color: #fafafa;
            border-bottom: 1px solid #eeeeee;
        }
        .pierre-row td {
            background-color: #ffffff;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="sender-info">
        <img src="data:image/png;base64,{{ base64_encode(@file_get_contents(public_path('images/logo.jpg'))) }}" class="logo">
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
                <th style="width: 45%;">Désignation</th>
                <th style="width: 10%;">Qté</th>
                <th style="width: 15%;">P.U. HT</th>
                <th style="width: 10%;">TVA</th>
                <th style="width: 20%; text-align: right;">Total HT</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lignes as $l)
                <tr class="pierre-row">
                    <td style="padding-bottom: 10px;">
                        <div style="font-size: 1.1em; margin-bottom: 2px;">{{ $l->typePierre }}</div>

                        <small style="font-weight: normal; color: #666; display: block; margin-bottom: 5px;">
                            Finition : {{ $l->finition }} |
                            {{ number_format($l->longueurM, 2, ',', ' ') }}m x
                            {{ number_format($l->largeurM, 2, ',', ' ') }}m x
                            {{ $l->epaisseur }} cm
                        </small>

                        @if(isset($l->specificites) && count($l->specificites) > 0)
                            <div style="margin-left: 10px; border-left: 2px solid #eee; padding-left: 10px; margin-top: 5px;">
                                @foreach($l->specificites as $spec)
                                    <div style="font-size: 10px; font-weight: normal; color: #555; font-style: italic;">
                                        <span style="color: #999;">+</span> {{ $spec->nom }}
                                        <span style="float: right; margin-right: 15px;">{{ number_format($spec->prix, 2, ',', ' ') }} €</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>

                    <td style="text-align: center; vertical-align: top; padding-top: 10px;">
                        {{ number_format($l->nombrePierre, 0, ',', ' ') }}
                    </td>
                    <td style="text-align: center; vertical-align: top; padding-top: 10px;">
                        {{ number_format($l->prixHT / ($l->nombrePierre ?: 1), 2, ',', ' ') }} €
                    </td>
                    <td style="text-align: center; vertical-align: top; padding-top: 10px;">
                        20%
                    </td>
                    <td style="text-align: right; vertical-align: top; padding-top: 10px;">
                        {{ number_format($l->prixHT, 2, ',', ' ') }} €
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-line">Sous-total HT : {{ number_format($totalHT, 2, ',', ' ') }} €</div>

            @if($montantLivraison > 0)
                <div class="total-line">Frais de livraison : {{ number_format($montantLivraison, 2, ',', ' ') }} €</div>
            @endif

            <div class="total-line"><strong>Total HT : {{ number_format($totalHTAvecLivraison, 2, ',', ' ') }} €</strong></div>
            <div class="total-line">TVA 20% : {{ number_format($totalHTAvecLivraison * 0.2, 2, ',', ' ') }} €</div>
            <div class="total-ttc">
                Total TTC : {{ number_format($totalHTAvecLivraison * 1.2, 2, ',', ' ') }} €
            </div>
        </div>

        @php
            $montantLivraison = $lignes->sum('livraison');
            $totalHTAvecLivraison = $totalHT + $montantLivraison;
        @endphp

        <div class="legal-notices">
            Acompte de 40% à la signature du devis.<br>

            @if($montantLivraison > 0)
                DEVIS HORS POSE, LIVRAISON INCLUSE<br>
            @else
                DEVIS HORS POSE, HORS LIVRAISON<br>
            @endif

            <br>
            <i style="color: #666; font-size: 10px;">Les Pierres Bleues de Soignies... (le reste de ton texte)</i>
        </div>
    </div>

    <div class="signature-box">Signature (précédée de la mention "bon pour accord")</div>

    <div class="footer">
        SASU au capital de 1 000 euros - Siret : 833 976 210 00017 - RCS : VALENCIENNES<br>
        TVA Intracommunautaire : FR 76 833 976 210<br>
        <strong>COORDONNÉES BANCAIRES :</strong> IBAN : FR76 1627 5500 0008 0021 3604 660 &nbsp; BIC : CEPAFRPP627
    </div>
</div>
</body>
</html>
