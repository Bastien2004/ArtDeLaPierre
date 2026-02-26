<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre des Devis - Art de la Pierre</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                $totalGroupe = $lignes->sum('prixHT');
            @endphp

            <tr class="group-header">
                <td colspan="10">
                    <div class="group-content">
                        <div class="group-left">
                            <a href="#" class="btn-icon" title="Télécharger">
                                <i class="fa-solid fa-download"></i>
                            </a>
                            <span class="client-name">{{ $p->client }}</span>
                            <span class="group-date">— {{ $p->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="group-right">
                            <a href="{{ route('devis.create', ['client_prefill' => $p->client, 'time_prefill' => $p->created_at->format('Y-m-d H:i:s')]) }}" class="btn-add-line">
                                <i class="fa-solid fa-plus"></i> Ajouter une ligne
                            </a>
                            <span class="col-total-groupe">
                    {{ number_format($totalGroupe, 2, ',', ' ') }} €
                </span>
                        </div>
                    </div>
                </td>
                <td style="display:none;"></td><td style="display:none;"></td>
                <td style="display:none;"></td><td style="display:none;"></td>
                <td style="display:none;"></td><td style="display:none;"></td>
                <td style="display:none;"></td><td style="display:none;"></td>
                <td style="display:none;"></td>
            </tr>

            @foreach($lignes as $d)
                <tr class="row-detail">
                    <td class="col-ref">#{{ $d->id }}</td>
                    <td><span style="display:none;">{{ $d->client }}</span></td>
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
                    <td class="col-total-ligne">{{ number_format($d->prixHT, 2, ',', ' ') }}€</td>
                    <td class="col-actions">
                        <button class="btn-edit-modal" data-bs-toggle="modal" data-bs-target="#modificationModal"
                                data-id="{{ $d->id }}" data-pierre="{{ $d->typePierre }}" data-nb="{{ $d->nombrePierre }}"
                                data-long="{{ $d->longueurM }}" data-larg="{{ $d->largeurM }}" data-prix="{{ $d->prixM2 }}"
                                data-specs="{{ $d->specificites->toJson() }}">✏️</button>
                        <button class="btn-delete-trigger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                data-id="{{ $d->id }}">❌</button>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableDevis').DataTable({
            responsive: true,
            ordering: false,
            pageLength: 50,
            dom: '<"top"f>rt<"bottom"lp><"clear">',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            "columnDefs": [
                { "targets": "_all", "defaultContent": "" }
            ]
        });

        // Gestion Modal Modification
        $(document).on('click', '.btn-edit-modal', function() {
            const btn = $(this);
            const id = btn.data('id');

            $('#display_id').text(id);
            $('#edit_pierre').val(btn.data('pierre'));
            $('#edit_nb').val(btn.data('nb'));
            $('#edit_long').val(btn.data('long'));
            $('#edit_larg').val(btn.data('larg'));
            $('#edit_prix').val(btn.data('prix'));
            $('#editForm').attr('action', '/devis/' + id);

            const wrapper = $('#wrapper-specs-edit').empty();
            const specs = btn.data('specs');
            if (specs && specs.length > 0) {
                specs.forEach(s => addSpecRow(s.nom, s.prix, s.id));
            }
        });

        // Fonction ajout ligne spécificité
        function addSpecRow(nom = '', prix = 0, specId = '') {
            const uniqueId = Date.now() + Math.floor(Math.random() * 1000);
            const html = `
            <div class="row mb-2 spec-row-edit align-items-center">
                <input type="hidden" name="specs[${uniqueId}][id]" value="${specId}">
                <div class="col-7"><input type="text" name="specs[${uniqueId}][nom]" value="${nom}" class="form-control form-control-sm" required></div>
                <div class="col-3"><input type="number" step="0.01" name="specs[${uniqueId}][prix]" value="${prix}" class="form-control form-control-sm" required></div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-spec"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>`;
            $('#wrapper-specs-edit').append(html);
        }

        $('#add-spec-edit').on('click', () => addSpecRow());
        $(document).on('click', '.btn-remove-spec', function() { $(this).closest('.spec-row-edit').remove(); });

        // 3. Gestion Modal Suppression
        $(document).on('click', '.btn-delete-trigger', function() {
            const id = $(this).data('id');
            $('#delete_display_id').text(id);
            $('#deleteForm').attr('action', '/devis/' + id);
        });
    });
</script>

@include('partials.modals-devis') </body>
</html>
