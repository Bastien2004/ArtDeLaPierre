<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        /* Styles de base pour forcer le rendu PDF */
        @page { margin: 0.5cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a2e;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }

        /* Header moderne */
        .header {
            border-bottom: 2px solid #c5b9a5;
            padding-bottom: 10px;
            margin-bottom: 15px;
            width: 100%;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a252f;
        }
        .subtitle { color: #8fa0ad; font-size: 11px; }

        /* Grille de calendrier version TABLE (Indispensable pour le PDF) */
        .cal-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Force des colonnes égales */
        }
        .cal-table th {
            background-color: #1a252f;
            color: #c5b9a5;
            padding: 8px 2px;
            text-transform: uppercase;
            font-size: 9px;
            border: 1px solid #1a252f;
        }
        .cal-table td {
            border: 1px solid #eeeeee;
            vertical-align: top;
            padding: 4px;
            height: 85px; /* Hauteur calculée pour tenir sur une page A4 */
            background-color: #ffffff;
        }

        /* Classes spéciales */
        .weekend { background-color: #fdf9f5 !important; }
        .other-month { background-color: #fafafa !important; color: #cccccc !important; }

        .day-number {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
            display: block;
        }

        /* Badge de livraison moderne */
        .event-pill {
            background-color: #1a252f;
            color: #ffffff;
            padding: 3px 5px;
            margin-bottom: 3px;
            border-radius: 4px;
            font-size: 8px;
            border-left: 3px solid #c5b9a5;
            white-space: nowrap;
            overflow: hidden;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #8fa0ad;
            border-top: 1px solid #eeeeee;
            padding-top: 5px;
        }
    </style>
</head>
<body>

<table style="width: 100%;">
    <tr>
        <td style="border:none;">
            <div class="title">Livraisons — {{ $nomMois }} {{ $annee }}</div>
            <div class="subtitle">SASU L'Art de la Pierre</div>
        </td>
        <td style="border:none; text-align: right; color: #888; font-size: 9px;">
            Édité le {{ now()->format('d/m/Y') }}<br>
            frederic.oden.tailleur.pierre@gmail.com
        </td>
    </tr>
</table>

@php
    $premierJour = \Carbon\Carbon::create($annee, $mois, 1);
    $offset = $premierJour->dayOfWeek === 0 ? 6 : $premierJour->dayOfWeek - 1;
    $start = $premierJour->copy()->subDays($offset);

    // On limite à 5 semaines si possible pour gagner de la place, sinon 6.
    $jours = [];
    for ($i = 0; $i < 42; $i++) { $jours[] = $start->copy()->addDays($i); }
@endphp

<table class="cal-table">
    <thead>
    <tr>
        <th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th><th>Dim</th>
    </tr>
    </thead>
    <tbody>
    @foreach(array_chunk($jours, 7) as $semaine)
        {{-- On cache la 6ème semaine si elle ne contient que des jours du mois suivant --}}
        @if($loop->index < 5 || $semaine[0]->month == $mois)
            <tr>
                @foreach($semaine as $jour)
                    @php
                        $isOther = $jour->month !== $mois;
                        $isWeekend = $jour->isWeekend();
                        $events = $livraisons[$jour->format('Y-m-d')] ?? collect();
                    @endphp
                    <td class="{{ $isOther ? 'other-month' : '' }} {{ $isWeekend ? 'weekend' : '' }}">
                        <span class="day-number">{{ $jour->day }}</span>

                        @foreach($events->take(3) as $ev)
                            <div class="event-pill"> {{ Str::limit($ev->client, 15) }}</div>
                        @endforeach

                        @if($events->count() > 3)
                            <div style="font-size:7px; color:#aaa;">+ {{ $events->count() - 3 }} autres</div>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endif
    @endforeach
    </tbody>
</table>

<div class="footer">
    SASU L'Art de la Pierre — 13 bis Hameau de Breaugies, 59570 Bellignies — Tél : 06 15 85 06 25
</div>

</body>
</html>
