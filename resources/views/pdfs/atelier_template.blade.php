<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        {!! file_get_contents(public_path('css/pdf_atelier.css')) !!}
    </style>
</head>
<body>

@php
    $nbLignes = $lignes->count();
    $zoomClass = 'zoom-large'; // Par défaut (1 à 7 lignes)

    if ($nbLignes > 12) {
        $zoomClass = 'zoom-compact';
    } elseif ($nbLignes > 7) {
        $zoomClass = 'zoom-medium';
    }
@endphp

<div class="container {{ $zoomClass }}">
    {{-- EN-TÊTE --}}
    <div class="header-atelier">
        <div class="header-left">
            <span class="label">QUANTITÉ</span> <span class="label">NOM :</span> <span class="val">{{ $client }}</span><br>
            <span class="label">REF :</span> <span class="val">{{ $reference }}</span>
        </div>
        <div class="header-right">
            <span class="label">DATE :</span> <span class="val">{{ $date->format('d / m / Y') }}</span><br>
            <span class="label">FINITION :</span> <span class="val">{{ $lignes->first()->finition ?? 'ADOUCI P40' }}</span>
        </div>
    </div>

    {{-- LISTE DE DÉBIT --}}
    <div class="content-body">
        @foreach($lignes as $l)
            @php $dynamicEpClass = 'ep-' . intval($l->epaisseur); @endphp
            <div class="debit-row">
                <div class="col-qty">{{ $l->nombrePierre }}p</div>
                <div class="col-main">
                    <span class="type-pierre">{{ strtoupper($l->typePierre) }}</span>
                    <span class="dims">
                        {{ number_format($l->longueurM, 2, ',', ' ') }} x {{ number_format($l->largeurM, 2, ',', ' ') }}
                    </span>

                    {{-- Badge d'épaisseur dynamique selon tes tables CSS --}}
                    <span class="epaisseur {{ $dynamicEpClass }}">
                        {{ $l->epaisseur }} cm
                    </span>

                    @if($l->specificites->count() > 0)
                        <div class="specs">
                            @foreach($l->specificites as $spec)
                                <span>+ {{ $spec->nom }}
                                    @if($spec->tailleRejingot && str_contains(strtolower($spec->nom), 'rejingot'))
                                        ({{ $spec->tailleRejingot }})
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- SIGNATURES EN BAS DE PAGE --}}
    <div class="footer-sign">
        <div class="sign-box">Nombre de Pièces : {{ $lignes->sum('nombrePierre') }}</div>
        <div class="sign-box">Signature :</div>
    </div>
</div>

</body>
</html>
