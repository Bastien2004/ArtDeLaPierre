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

    <table id="tableDevis" class="display" style="width:100%">
        <thead>
        <tr>
            <th style="width: 50px;">Réf.</th>
            <th style="display:none;">Client Hidden</th> <th>Désignation / Spécificité</th>
            <th style="width: 40px;">Nb</th>
            <th style="width: 100px;">Dimensions / m²</th>
            <th style="width: 80px;">Prix M²</th>
            <th class="txt-right" style="width: 100px;">Total HT</th>
            <th class="txt-center" style="width: 80px;">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($devisGroupes as $cle => $lignes)
            @php
                $p = $lignes->first();
                $totalGroupe = $lignes->sum('prixHT');
            @endphp

            <tr class="group-header">
                <td colspan="8">
                    <div class="group-content">
                        <div class="group-left">
                            <button type="button"
                                    class="btn-icon btn-trigger-pdf"
                                    data-url="{{ route('devis.downloadPDF', ['client' => $p->client, 'date' => $p->created_at->format('Y-m-d-H-i-s')]) }}"
                                    title="Télécharger">
                                <i class="fa-solid fa-download"></i>
                            </button>
                            <span class="client-name">{{ $p->client }}</span>
                            <span class="group-date">— {{ $p->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="group-right">
                            <a href="{{ route('devis.create', ['client_prefill' => $p->client, 'time_prefill' => $p->created_at->format('Y-m-d H:i:s')]) }}" class="btn-add-line">
                                <i class="fa-solid fa-plus"></i> Ligne
                            </a>
                            <span class="col-total-groupe">
                                {{ number_format($totalGroupe, 2, ',', ' ') }} €
                            </span>
                        </div>
                    </div>
                </td>
                @for($i=0; $i<7; $i++) <td style="display:none;"></td> @endfor
            </tr>

            @foreach($lignes as $d)
                <tr class="row-detail">
                    <td class="col-ref">#{{ $d->id }}</td>
                    <td style="display:none;">{{ $d->client }}</td> <td class="col-pierre">
                        <div class="stone-name">{{ $d->typePierre ?? '' }}</div>
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
                    <td class="txt-center">{{ $d->nombrePierre }}</td>
                    <td class="col-mesure">
                        <small class="text-muted">{{ $d->longueurM }}x{{ $d->largeurM }}m</small><br>
                        <strong>{{ number_format($d->matiere, 2, ',', ' ') }} m²</strong>
                    </td>
                    <td class="col-prix">{{ number_format($d->prixM2, 2, ',', ' ') }}€</td>
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
        // 1. DataTable
        const table = $('#tableDevis').DataTable({
            responsive: true,
            ordering: false,
            pageLength: 50,
            dom: '<"top"f>rt<"bottom"lp><"clear">',
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' }
        });

        // 2. MODAL MODIFICATION (Ouverture)
        $(document).on('click', '.btn-edit-modal', function() {
            const btn = $(this);
            const id = btn.data('id');
            const qte = parseFloat(btn.data('nb')) || 1;

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
                specs.forEach(s => {
                    let unite = (s.nom.toLowerCase().includes('rejingot') || s.nom.toLowerCase().includes('ciselage')) ? 'ml' : 'u';
                    let basePrice = s.prix / qte;
                    addSpecRow(s.nom, s.prix, unite, basePrice);
                });
            }
            updateModalTotal();
        });

        // 3. FONCTION AJOUT SPEC
        function addSpecRow(nom = '', prix = 0, unite = 'u', basePrice = 0) {
            const uniqueId = Date.now() + Math.floor(Math.random() * 1000);
            const html = `
            <div class="row mb-2 spec-row-edit align-items-center ligne-spec-edit">
                <div class="col-6"><input type="text" name="specs[${uniqueId}][nom]" value="${nom}" class="form-control form-control-sm" required ${nom !== '' ? 'readonly' : ''}></div>
                <div class="col-4"><input type="number" step="0.01" name="specs[${uniqueId}][prix]" value="${parseFloat(prix).toFixed(2)}" class="form-control form-control-sm spec-prix-edit" data-unite="${unite}" data-base-price="${basePrice}" required></div>
                <div class="col-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-spec"><i class="fa-solid fa-trash"></i></button></div>
            </div>`;
            $('#wrapper-specs-edit').append(html);
            updateModalTotal();
        }

        // 4. BOUTONS SPEC (Action)
        $(document).on('click', '#add-spec-manual-edit', () => addSpecRow());
        $(document).on('click', '.btn-remove-spec', function() {
            $(this).closest('.ligne-spec-edit').remove();
            updateModalTotal();
        });

        window.addSpecToEdit = function(nom, prixUnitaire, unite) {
            const long = parseFloat($('#edit_long').val()) || 0;
            const qte = parseFloat($('#edit_nb').val()) || 1;
            let prixFinal = (unite === 'ml') ? (prixUnitaire * long * qte) : (prixUnitaire * qte);
            addSpecRow(nom, prixFinal, unite, prixUnitaire);
        };

        // 5. CALCULS AUTO
        $(document).on('input', '#edit_long, #edit_nb, #edit_larg, #edit_prix', function() {
            const long = parseFloat($('#edit_long').val()) || 0;
            const qte = parseFloat($('#edit_nb').val()) || 0;
            $('.spec-prix-edit').each(function() {
                const base = parseFloat($(this).data('base-price'));
                $(this).val(($(this).data('unite') === 'ml' ? (base * long * qte) : (base * qte)).toFixed(2));
            });
            updateModalTotal();
        });

        function updateModalTotal() {
            const totalPierre = (parseFloat($('#edit_long').val())||0) * (parseFloat($('#edit_larg').val())||0) * (parseFloat($('#edit_prix').val())||0) * (parseFloat($('#edit_nb').val())||0);
            let totalSpecs = 0;
            $('.spec-prix-edit').each(function() { totalSpecs += parseFloat($(this).val()) || 0; });
            $('#total_ligne_edit').text((totalPierre + totalSpecs).toFixed(2));
        }

        // 6. DELETE & PDF (Les fix)
        $(document).on('click', '.btn-delete-trigger', function() {
            const id = $(this).data('id');
            $('#delete_display_id').text(id);
            $('#deleteForm').attr('action', '/devis/' + id);
        });

        let currentPdfUrl = '';
        $(document).on('click', '.btn-trigger-pdf', function(e) {
            e.preventDefault();
            currentPdfUrl = $(this).attr('data-url');
            $('#pdfRefModal').modal('show');
        });

        $(document).on('click', '#confirmDownload', function() {
            const ref = $('#custom_ref').val().trim();
            window.location.href = currentPdfUrl + (ref ? (currentPdfUrl.includes('?') ? '&' : '?') + 'ref=' + encodeURIComponent(ref) : '');
            $('#pdfRefModal').modal('hide');
        });
    });
</script>@include('partials.modals-devis')
</body>
</html>
