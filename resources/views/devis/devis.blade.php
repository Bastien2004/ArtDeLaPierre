<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre des Devis - Art de la Pierre</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('css/devisTableau.css') }}">
</head>
<body>
<div class="container-fluid">
    <div class="table-header-flex">
        <h1>Registre des Devis</h1>
        <a href="{{ route('devis.create') }}" class="btn-new">+ Nouveau Devis</a>
    </div>

    <table id="tableDevis" class="display nowrap" style="width:100%">
        <thead>
        <tr>
            <th>Réf.</th>
            <th>Client</th>
            <th>Pierre / Options</th>
            <th>Nb</th>
            <th>Long</th>
            <th>Larg</th>
            <th>Prix M²</th>
            <th>Matière</th>
            <th class="txt-right">Total HT</th>
            <th class="txt-center">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($devisGroupes as $cle => $lignes)
            @php
                $p = $lignes->first();
                // Le prixHT en BDD inclut déjà les specs, donc sum() suffit
                $totalGroupe = $lignes->sum('prixHT');
            @endphp

            <tr class="group-header">
                <td colspan="8" class="group-title">
                    <span class="icon">🏢</span> {{ $p->client }}
                    <span class="group-date">— {{ $p->created_at->format('d/m/Y H:i') }}</span>
                </td>
                {{-- Masquage pour l'alignement DataTables --}}
                @for($i=0; $i<7; $i++) <td style="display:none"></td> @endfor

                <td class="col-total-groupe">{{ number_format($totalGroupe, 2, ',', ' ') }} €</td>
                <td class="col-actions-groupe"></td>
            </tr>

            @foreach($lignes as $d)
                <tr class="row-detail">
                    <td class="col-ref">#{{ $d->id }}</td>
                    <td>{{ $d->client }}</td>
                    <td class="col-pierre">
                        <div class="stone-name">{{ $d->typePierre }}</div>
                        @if($d->specificites->count() > 0)
                            <div class="specs-mini-list">
                                @foreach($d->specificites as $spec)
                                    <div class="spec-item">
                                        <span>• {{ $spec->nom }}</span>
                                        <span class="spec-price">+{{ number_format($spec->prix, 2, ',', ' ') }}€</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td>{{ $d->nombrePierre }}</td>
                    <td class="col-mesure">{{ $d->longueurM }}m</td>
                    <td class="col-mesure">{{ $d->largeurM }}m</td>
                    <td class="col-prix">{{ number_format($d->prixM2, 2, ',', ' ') }}€</td>
                    <td class="col-mesure">{{ number_format($d->matiere, 2, ',', ' ') }}m²</td>

                    {{-- Affichage du prix HT calculé par le contrôleur --}}
                    <td class="col-total-ligne">{{ number_format($d->prixHT, 2, ',', ' ') }}€</td>

                    <td class="col-actions">
                        <a href="{{ route('devis.edit', $d->id) }}" class="edit-link" title="Modifier">✏️</a>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableDevis').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            ordering: false,
            pageLength: 50,
            dom: 'frtip',
            scrollX: true,
            autoWidth: false
        });
    });
</script>
</body>
</html>
