<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Devis - Taille de Pierre</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('css/devis.css') }}">
</head>
<body>

<div class="container-fluid">
    <div class="header-section">
        <h1>Registre Technique des Devis</h1>
        <a href="{{ route('devis.create') }}" class="btn-add">+ Nouveau Devis</a>
    </div>

    <div class="table-container">
        <table id="tableDevis" class="display">
            <thead>
            <tr>
                <th>Réf.</th>
                <th>Client</th>
                <th>Pierre</th>
                <th>Long. <small>(cm)</small></th>
                <th>Larg. <small>(cm)</small></th>
                <th>Ép. <small>(cm)</small></th>
                <th>Matière</th>
                <th>Prix M²</th>
                <th>Rejingot</th>
                <th>Oreilles</th>
                <th>Total HT</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($devis as $d)
                <tr>
                    <td style="color: #bdc3c7;">#{{ $d->id }}</td>
                    <td class="col-client">{{ $d->client }}</td>
                    <td>{{ $d->typePierre }}</td>
                    <td>{{ $d->longueurCM }}</td>
                    <td>{{ $d->largeurCM }}</td>
                    <td>{{ $d->epaisseurCM }}</td>
                    <td>{{ number_format($d->matiere, 2, ',', ' ') }} €</td>
                    <td>{{ number_format($d->prixM2, 2, ',', ' ') }} €</td>
                    <td>{{ $d->rejingotML }}</td>
                    <td>{{ $d->oreilles }}</td>
                    <td class="col-total">
                        @php
                            $surface = ($d->longueurCM * $d->largeurCM) / 10000;
                            $total = $surface * $d->prixM2;
                            // Tu peux ajouter ici tes calculs de suppléments si besoin
                        @endphp
                        {{ number_format($total, 2, ',', ' ') }} €
                    </td>
                    <td>
                        <a href="{{ route('devis.edit', $d->id) }}" class="btn-action">✏️</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableDevis').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            order: [[0, 'desc']],
            pageLength: 15
        });
    });
</script>

</body>
</html>
