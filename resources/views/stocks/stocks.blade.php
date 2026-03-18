<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stocks - Pierres</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/stocks.css') }}" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="btn-back-stone">
            <i class="fa fa-arrow-left"></i> Retour à l’accueil
        </a>

        <div class="d-flex gap-3">
            <a href="{{ route('stocks.pdf') }}" class="btn btn-outline-danger">
                <i class="fa-solid fa-file-pdf me-2"></i>Exporter PDF
            </a>
            <button class="btn btn-stone" data-bs-toggle="modal" data-bs-target="#modalStock" id="btnAjouter">
                <i class="fa fa-plus-circle me-2"></i>Nouvelle Entrée
            </button>
        </div>
    </div>

    @php
        // Grille tarifaire (Choix Déclassé & Standard)
        $tarifs = [
        // Choix declassé
        2  => 33.25,
        3  => 44.00,
        4  => 50.50,
        5  => 51.50,
        6  => 60.75,
        8  => 70.75,
        10 => 83.75,
        12 => 100.75,
        15 => 135.75,
        //Choix superieur
        16 => 362.75,
        18 => 414.25,
        20 => 466.75,
        22 => 503.25,
        24 => 569.75,
        25 => 599.75,
        28 => 685.00,
        30 => 739.00
    ];
        ksort($tarifs); // Tri par épaisseur
    @endphp

    <div class="card shadow-sm border-0 p-4">
        <h2 class="mb-4"><i class="fa-solid fa-layer-group me-2" style="color: var(--stone-gold);"></i>Inventaire des Stocks</h2>

        <table id="tableStock" class="display table table-hover" style="width:100%">
            <thead>
            <tr>
                <th>Quantité</th>
                <th>Matière</th>
                <th>Dimensions</th>
                <th>Épaisseur</th>
                <th>Surface</th>
                <th>Valeur Est.</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @isset($stocks)
                @foreach($stocks as $item)
                    @php
                        $surface = $item->longueur * $item->largeur * $item->quantite;
                        $prixM2 = 0;

                        // Logique de recherche du palier
                        foreach($tarifs as $seuil => $prix) {
                            if ($seuil >= $item->epaisseur) {
                                $prixM2 = $prix;
                                break;
                            }
                        }

                        if ($prixM2 > 0) {
                            $valeurTotale = $surface * $prixM2;
                        } else {
                            // Formule pour > 30cm
                            $valeurTotale = ($item->longueur * $item->largeur * ($item->epaisseur / 100) * 2500) * $item->quantite;
                        }
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $item->quantite }} pcs</td>
                        <td><span class="text-uppercase fw-bold">{{ $item->matiere }}</span></td>
                        <td><small>{{ number_format($item->longueur, 2) }} x {{ number_format($item->largeur, 2) }}m</small></td>
                        <td><span class="badge bg-dark px-3 py-2">{{ $item->epaisseur }} cm</span></td>
                        <td class="fw-bold">{{ number_format($surface, 2, ',', ' ') }} m²</td>
                        <td class="text-success fw-bold">
                            {{ number_format($valeurTotale, 2, ',', ' ') }} €
                        </td>
                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <button class="btn btn-sm btn-outline-primary border-0 btn-edit"
                                        data-id="{{ $item->id }}"
                                        data-qte="{{ $item->quantite }}"
                                        data-long="{{ $item->longueur }}"
                                        data-larg="{{ $item->largeur }}"
                                        data-matiere="{{ $item->matiere }}"
                                        data-epais="{{ $item->epaisseur }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0 btn-delete"
                                        data-id="{{ $item->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endisset
            </tbody>
        </table>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        const table = $('#tableStock').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 25,
            order: [[3, 'asc']], // Tri par épaisseur par défaut
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            }
        });

        // Gestion Ajout
        $('#btnAjouter').on('click', function() {
            $('#formStock')[0].reset();
            $('#modalTitle').text('Nouvelle Pierre');
            $('#formMethod').val('POST');
            $('#formStock').attr('action', "{{ route('stocks.store') }}");
        });

        // Gestion Edition
        $('#tableStock').on('click', '.btn-edit', function() {
            const btn = $(this);
            $('#modalTitle').text('Modifier la pierre');
            $('#stock_id').val(btn.data('id'));
            $('#matiere').val(btn.data('matiere'));
            $('#quantite').val(btn.data('qte'));
            $('#longueur').val(btn.data('long'));
            $('#largeur').val(btn.data('larg'));
            $('#epaisseur').val(btn.data('epais'));

            $('#formStock').attr('action', "/stocks/" + btn.data('id'));
            $('#formMethod').val('PUT');
            $('#modalStock').modal('show');
        });

        // Gestion Suppression
        $('#tableStock').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            $('#formDelete').attr('action', "/stocks/" + id);
            $('#modalDelete').modal('show');
        });
    });
</script>

@include('partials.modals-stock')

</body>
</html>
