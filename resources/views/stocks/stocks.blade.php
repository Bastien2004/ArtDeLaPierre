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

    <div class="card">
        <h2 class="mb-4"><i class="fa-solid fa-layer-group me-2" style="color: var(--stone-gold);"></i>Inventaire des Stocks</h2>

        <table id="tableStock" class="display table" style="width:100%">
            <thead>
            <tr>
                <th>Quantité</th>
                <th>Matière</th>
                <th>Dimensions (L x l)</th>
                <th>Épaisseur</th>
                <th>Surface Totale</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @isset($stocks)
                @foreach($stocks as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->quantite }} pcs</td>
                        <td><span class="text-uppercase fw-bold" style="letter-spacing: 0.5px;">{{ $item->matiere }}</span></td>
                        <td><span class="badge-dim">{{ number_format($item->longueur, 2) }}m x {{ number_format($item->largeur, 2) }}m</span></td>
                        <td><span class="badge bg-dark px-3 py-2">{{ $item->epaisseur }} cm</span></td>
                        <td class="fw-bold text-dark">
                            {{ number_format($item->longueur * $item->largeur * $item->quantite, 2, ',', ' ') }} m²
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
                                <button class="btn btn-sm btn-outline-danger border-0 btn-delete" data-id="{{ $item->id }}">
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

<div class="modal fade" id="modalStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formStock" action="{{ route('stocks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="stock_id">

                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-gem me-2"></i><span id="modalTitle">Nouvelle Pierre</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Désignation de la Matière</label>
                        <input type="text" name="matiere" id="matiere" class="form-control" placeholder="ex: Granit Noir, Marbre Blanc..." required>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Quantité (Unités)</label>
                            <input type="number" name="quantite" id="quantite" class="form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Épaisseur (cm)</label>
                            <input type="number" name="epaisseur" id="epaisseur" class="form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Longueur (m)</label>
                            <input type="number" step="0.01" name="longueur" id="longueur" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Largeur (m)</label>
                            <input type="number" step="0.01" name="largeur" id="largeur" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-stone px-4">Confirmer l'entrée</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3 border-top border-danger border-4">
            <div class="modal-body">
                <div class="mb-3 text-danger">
                    <i class="fa-solid fa-circle-exclamation fa-4x"></i>
                </div>
                <h5 class="fw-bold">Confirmer la suppression ?</h5>
                <p class="text-muted small">Cette donnée sera définitivement retirée de l'inventaire.</p>
            </div>
            <form id="formDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center gap-3 mb-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger px-4">Supprimer</button>
                </div>
            </form>
        </div>
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

        $('#btnAjouter').on('click', function() {
            $('#formStock')[0].reset();
            $('#modalTitle').text('Nouvelle Pierre');
            $('#formMethod').val('POST');
            $('#formStock').attr('action', "{{ route('stocks.store') }}");
        });

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

        $('#tableStock').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            $('#formDelete').attr('action', "/stocks/" + id);
            $('#modalDelete').modal('show');
        });
    });
</script>

</body>
</html>
