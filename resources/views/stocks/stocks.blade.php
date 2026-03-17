<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stocks - Pierres</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-stone { background-color: #2c3e50; color: white; border-radius: 8px; }
        .btn-stone:hover { background-color: #34495e; color: white; }
        .table-dark-custom { background-color: #2c3e50; color: white; }
        .modal-header { background-color: #2c3e50; color: white; }
        .badge-dim { background-color: #e9ecef; color: #495057; font-weight: 500; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-layer-group me-2"></i>Gestion des Stocks de Pierres</h2>
        <button class="btn btn-stone" data-bs-toggle="modal" data-bs-target="#modalStock" id="btnAjouter">
            <i class="fa fa-plus-circle me-1"></i> Entrée de stock
        </button>
    </div>

    <div class="card p-4">
        <table id="tableStock" class="display table table-striped" style="width:100%">
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
                        <td>{{ $item->quantite }}</td>

                        <td><strong>{{ $item->matiere }}</strong></td>

                        <td><span class="badge badge-dim">{{ $item->longueur }}m x {{ $item->largeur }}m</span></td>

                        <td>{{ $item->epaisseur }} cm</td>

                        <td>{{ number_format($item->longueur * $item->largeur * $item->quantite, 2, ',', ' ') }} m²</td>

                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary btn-edit"
                                    data-id="{{ $item->id }}"
                                    data-qte="{{ $item->quantite }}"
                                    data-long="{{ $item->longueur }}"
                                    data-larg="{{ $item->largeur }}"
                                    data-matiere="{{ $item->matiere }}"
                                    data-epais="{{ $item->epaisseur }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $item->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
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
                    <h5 class="modal-title"><i class="fa-solid fa-pen-to-square me-2"></i><span id="modalTitle">Nouvelle Pierre</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom de la Matière</label>
                        <input type="text" name="matiere" id="matiere" class="form-control" placeholder="ex: Granit Noir, Marbre Blanc..." required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Quantité (Nb)</label>
                            <input type="number" name="quantite" id="quantite" class="form-control" min="1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Épaisseur (cm)</label>
                            <input type="number" name="epaisseur" id="epaisseur" class="form-control" min="1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Longueur (m)</label>
                            <input type="number" step="0.01" name="longueur" id="longueur" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Largeur (m)</label>
                            <input type="number" step="0.01" name="largeur" id="largeur" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-stone">Enregistrer en stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3">
            <div class="modal-body">
                <i class="fa-solid fa-triangle-exclamation text-danger fa-3x mb-3"></i>
                <h5>Supprimer cette ligne ?</h5>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <form id="formDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Non</button>
                    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
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
            order: [[1, 'asc']] // Tri par défaut sur la "Matière" (désormais 2ème colonne, index 1)
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

            let id = btn.data('id');
            $('#formStock').attr('action', "/stocks/" + id);
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
