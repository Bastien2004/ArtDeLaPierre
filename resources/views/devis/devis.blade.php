<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre des Devis - Art de la Pierre</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/devisTableau.css') }}?v={{ filemtime(public_path('css/devisTableau.css')) }}">    <link rel="icon" href="{{ asset('LogoHead.png') }}" type="image/png">
</head>
<body>


<div class="container-fluid">
    <a href="{{ url('/dashboard') }}" class="btn-back-stone">
        <i class="fa fa-arrow-left"></i> Retour à l’accueil
    </a>
    <div class="table-header-flex">
        <h1>Registre des Devis</h1>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('devis.calendrier') }}" class="btn-calendrier">
                <i class="fa-solid fa-calendar-days"></i> Calendrier
            </a>
            <a href="{{ route('devis.create') }}" class="btn-new">+ Nouveau Devis</a>
        </div>
    </div>

    <table id="tableDevis" class="display" style="width:100%">
        <thead>
        <tr>
            <th style="width: 50px;">Réf.</th>
            <th style="display:none;">Client Hidden</th>
            <th>Désignation / Spécificité</th>
            <th style="width: 40px;">Nb</th>
            <th style="width: 100px;">Dimensions / m²</th>
            <th style="width: 100px;">Poids (kg)</th> <th style="width: 80px;">Prix M²</th>
            <th class="txt-right" style="width: 100px;">Total HT</th>
            <th class="txt-center" style="width: 80px;">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($devisGroupes as $cle => $lignes)
            @php
                $p = $lignes->first();
                $totalGroupe = $lignes->sum('prixHT');
                $fraisPort = $lignes->avg('livraison');
                $poidsTotalGroupe = $lignes->sum('poids');
            @endphp

            <tr class="group-header">
                <td colspan="9">
                    <div class="group-content">
                        <div class="group-left">
                            <div class="dl-dropdown-wrapper">
                                <button type="button" class="btn-dl-main" data-client="{{ $p->client }}">
                                    <i class="fa-solid fa-download"></i>
                                    <span>Télécharger</span>
                                    <i class="fa-solid fa-chevron-down dl-chevron"></i>
                                </button>
                                <div class="dl-menu">
                                    <a class="dl-option btn-trigger-pdf"
                                       data-url="{{ route('devis.downloadPDF', ['client' => $p->client, 'date' => $p->created_at->format('Y-m-d-H-i-s')]) }}">
                                        <span class="dl-icon-wrap"><i class="fa-solid fa-file-invoice"></i></span>
                                        <div>
                                            <span class="dl-label">Devis client</span>
                                            <span class="dl-sub">Document commercial</span>
                                        </div>
                                    </a>
                                    <a class="dl-option btn-trigger-pdf"
                                       data-url="{{ route('devis.downloadAtelierPDF', ['client' => $p->client, 'date' => $p->created_at->format('Y-m-d-H-i-s')]) }}">
                                        <span class="dl-icon-wrap"><i class="fa-solid fa-hammer"></i></span>
                                        <div>
                                            <span class="dl-label">Bon atelier</span>
                                            <span class="dl-sub">Fiche de fabrication</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <span class="client-name" style="margin-left: 10px">{{ $p->client }}</span>
                            <span class="group-date">— {{ $p->created_at->format('d/m/Y H:i') }}</span>
                            @if($fraisPort >= 0)
                                <span style="margin-left: 15px; font-size: 0.85em; color: #d4af37; font-weight: bold; cursor: pointer;"
                                      class="btn-edit-transport"
                                      data-client="{{ $p->client }}"
                                      data-date="{{ $p->created_at->format('Y-m-d H:i:s') }}"
                                      data-current="{{ $fraisPort }}">
                                <i class="fa-solid fa-truck"></i> Livraison : {{ number_format($fraisPort, 2, ',', ' ') }}€
                                <i class="fa-solid fa-pen-to-square ms-1" style="font-size: 0.8em; color: #666;"></i>
                            </span>
                            @endif
                        </div>

                        <div class="group-right">
                            <a href="{{ route('devis.create', ['client_prefill' => $p->client, 'adresse_prefill'  => $p->adresse, 'time_prefill' => $p->created_at->format('Y-m-d H:i:s'), 'livraison_prefill'=> $lignes->avg('livraison')]) }}" class="btn-add-line">
                                <i class="fa-solid fa-plus"></i> Ligne
                            </a>

                            <button type="button" class="btn-email-devis"
                                    data-client="{{ $p->client }}"
                                    data-date="{{ $p->created_at->format('Y-m-d H:i:s') }}"
                                    data-total="{{ number_format($totalGroupe, 2, '.', '') }}">
                                <i class="fa-solid fa-envelope"></i>
                            </button>

                            <span class="weight-badge-gold" style="color: #d4af37; font-weight: bold; font-size: 0.95em;">
                                <i class="fa-solid fa-weight-hanging"></i>
                                {{ number_format($poidsTotalGroupe, 2, ',', ' ') }} kg
                            </span>
                            <span class="col-total-groupe">
                            {{ number_format($totalGroupe, 2, ',', ' ') }} €
                        </span>
                        </div>
                    </div>
                </td>
                @for($i=0; $i<8; $i++) <td style="display:none;"></td> @endfor
            </tr>

            @foreach($lignes as $d)
                <tr class="row-detail">
                    <td class="col-ref">#{{ $d->id }}</td>
                    <td style="display:none;">{{ $d->client }}</td>
                    <td class="col-pierre">
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
                        <small class="text-muted">{{ $d->longueurM }}m x{{ $d->largeurM }}m x{{ $d->epaisseur }}cm</small><br>
                        <strong>{{ number_format($d->matiere, 2, ',', ' ') }} m²</strong>
                    </td>

                    <td class="txt-center">
                    <span class="badge bg-light text-dark border">
                        {{ number_format($d->poids, 2, ',', ' ') }} kg
                    </span>
                    </td>

                    <td class="col-prix">{{ number_format($d->prixM2, 2, ',', ' ') }}€</td>
                    <td class="col-total-ligne">{{ number_format($d->prixHT, 2, ',', ' ') }}€</td>
                    <td class="col-actions">
                        <button class="btn-edit-modal" data-bs-toggle="modal" data-bs-target="#modificationModal"
                                data-id="{{ $d->id }}"
                                data-pierre="{{ $d->typePierre }}"
                                data-nb="{{ $d->nombrePierre }}"
                                data-long="{{ $d->longueurM }}"
                                data-larg="{{ $d->largeurM }}"
                                data-prix="{{ $d->prixM2 }}"
                                data-poids="{{ $d->poids }}"
                                data-epaisseur="{{ $d->epaisseur }}"
                                data-specs="{{ $d->specificites->toJson() }}">✏️
                        </button>
                        <button class="btn-delete-trigger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="{{ $d->id }}">❌</button>
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
    // ============================================================
    // FONCTIONS GLOBALES (hors document.ready)
    // ============================================================

    function updateModalTotal() {
        const totalPierre = (parseFloat($('#edit_long').val())||0)
            * (parseFloat($('#edit_larg').val())||0)
            * (parseFloat($('#edit_prix').val())||0)
            * (parseFloat($('#edit_nb').val())||0);
        let totalSpecs = 0;
        $('.spec-prix-edit').each(function() {
            totalSpecs += parseFloat($(this).val()) || 0;
        });
        // $('#total_ligne_edit').text((totalPierre + totalSpecs).toFixed(2));
    }

    function addSpecRow(nom, prix, unite, basePrice) {
        nom       = nom       || '';
        prix      = prix      || 0;
        unite     = unite     || 'u';
        basePrice = basePrice || 0;

        const uniqueId  = 'spec_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const isManual  = (parseFloat(basePrice) === 0);
        const prixInput = parseFloat(prix).toFixed(2);

        const readonlyAttr = isManual ? '' : 'readonly';
        const bgStyle      = isManual ? '' : 'style="background:#f8f9fa"';

        const html =
            '<div class="row mb-2 align-items-center ligne-spec-edit" data-unite="' + unite + '" data-base-price="' + basePrice + '">' +
            '<div class="col-5">' +
            '<input type="text"' +
            ' name="specs[' + uniqueId + '][nom]"' +
            ' value="' + nom + '"' +
            ' class="form-control form-control-sm spec-nom-edit"' +
            ' placeholder="Nom de l\'option" required>' +
            '</div>' +
            '<div class="col-4">' +
            '<div class="input-group input-group-sm">' +
            '<input type="number" step="0.01"' +
            ' name="specs[' + uniqueId + '][prix]"' +
            ' value="' + prixInput + '"' +
            ' class="form-control spec-prix-edit"' +
            ' ' + readonlyAttr + ' ' + bgStyle + ' required>' +
            '<span class="input-group-text">€</span>' +
            '</div>' +
            '</div>' +
            '<div class="col-2">' +
            '<span class="badge bg-secondary" style="font-size:0.7em">' + unite + '</span>' +
            '</div>' +
            '<div class="col-1 text-end">' +
            '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-spec">' +
            '<i class="fa-solid fa-trash"></i>' +
            '</button>' +
            '</div>' +
            '<input type="hidden" name="specs[' + uniqueId + '][base_price]" value="' + basePrice + '">' +
            '<input type="hidden" name="specs[' + uniqueId + '][unite]" value="' + unite + '">' +
            '</div>';

        $('#wrapper-specs-edit').append(html);
        updateModalTotal();
    }

    function addSpecToEdit(nom, prixUnitaire, unite) {
        const long = parseFloat($('#edit_long').val()) || 0;
        const qte  = parseFloat($('#edit_nb').val())   || 1;
        let prixFinal;
        if (unite === 'ml') {
            const longEffective = (nom.toLowerCase().includes('rejingot') && long < 1 && long > 0) ? 1 : long;
            prixFinal = prixUnitaire * longEffective * qte;
        } else {
            prixFinal = prixUnitaire * qte;
        }
        addSpecRow(nom, prixFinal, unite, prixUnitaire);
    }

    // ============================================================
    // EVENTS (peuvent rester hors document.ready car délégués)
    // ============================================================

    $(document).on('click', '.btn-edit-modal', function() {
        const btn      = $(this);
        const id       = btn.data('id');
        const specs    = btn.data('specs');

        $('#display_id').text(id);
        $('#editForm').attr('action', '/devis/' + id);
        $('#edit_pierre').val(btn.data('pierre'));
        $('#edit_nb').val(btn.data('nb'));
        $('#edit_long').val(btn.data('long'));
        $('#edit_larg').val(btn.data('larg'));
        $('#edit_prix').val(btn.data('prix'));
        $('#edit_poids').val(btn.data('poids'));
        $('#edit_epaisseur').val(btn.data('epaisseur'));

        $('#wrapper-specs-edit').empty();
        if (specs && specs.length > 0) {
            specs.forEach(function(spec) {
                addSpecRow(
                    spec.nom,
                    spec.prix,
                    spec.unite      || 'u',
                    spec.base_price || 0
                );
            });
        }
        updateModalTotal();
    });

    $(document).on('click', '.btn-remove-spec', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('.ligne-spec-edit').remove();
        updateModalTotal();
    });

    $(document).on('click', '#add-spec-manual-edit', function() {
        addSpecRow('', 0, 'u', 0);
    });

    $(document).on('input', '#edit_long, #edit_nb, #edit_larg, #edit_prix, #edit_epaisseur', function() {
        const long   = parseFloat($('#edit_long').val())      || 0;
        const larg   = parseFloat($('#edit_larg').val())      || 0;
        const epais  = parseFloat($('#edit_epaisseur').val()) || 0;
        const qte    = parseFloat($('#edit_nb').val())        || 0;
        const prixM2 = parseFloat($('#edit_prix').val())      || 0;

        const densite      = 2700;
        const poidsCalcule = long * larg * (epais / 100) * densite * qte;
        $('#edit_poids').val(poidsCalcule.toFixed(2));

        $('.spec-prix-edit').each(function() {
            const row     = $(this).closest('.ligne-spec-edit');
            const base    = parseFloat(row.data('base-price')) || 0;
            const unite   = row.data('unite');
            const nomSpec = row.find('.spec-nom-edit').val().toLowerCase();

            if (base === 0) return; // spec manuelle → ne pas toucher

            if (unite === 'ml') {
                const longEff = (nomSpec.includes('rejingot') && long < 1 && long > 0) ? 1 : long;
                $(this).val((base * longEff * qte).toFixed(2));
            } else {
                $(this).val((base * qte).toFixed(2));
            }
        });

        updateModalTotal();
    });

    $(document).on('click', '.btn-dl-main', function(e) {
        e.stopPropagation();
        const wrapper = $(this).closest('.dl-dropdown-wrapper');
        $('.dl-dropdown-wrapper').not(wrapper).removeClass('open');
        wrapper.toggleClass('open');
    });

    $(document).on('click', function() {
        $('.dl-dropdown-wrapper').removeClass('open');
    });

    $(document).on('click', '.btn-edit-transport', function() {
        const btn = $(this);
        $('#livraison_client').val(btn.data('client'));
        $('#livraison_date').val(btn.data('date'));
        $('#livraison_input').val(btn.data('current'));
        $('#modalLivraison').modal('show');
    });

    $(document).on('click', '.btn-delete-trigger', function() {
        const id = $(this).data('id');
        $('#delete_display_id').text(id);
        $('#deleteForm').attr('action', '/devis/' + id);
    });

    // ============================================================
    // DOCUMENT READY
    // ============================================================

    $(document).ready(function() {

        $('#tableDevis').DataTable({
            responsive: true,
            ordering:   false,
            pageLength: 50,
            dom:        '<"top"f>rt<"bottom"lp><"clear">',
            language:   { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' }
        });

        $(document).on('click', '.btn-email-devis', function() {
            const client = $(this).data('client');
            const date   = $(this).data('date');
            const total  = $(this).data('total');

            $('#email_destinataire').val('');
            $('#email_objet').val('Votre devis – Art de la Pierre');
            $('#email_message').val(
                'Bonjour ' + client + ',\n\n' +
                'Veuillez trouver ci-joint votre devis d\'un montant de ' + total + ' € HT.\n\n' +
                'N\'hésitez pas à nous contacter pour toute question.\n\n' +
                'Cordialement,\nL\'art de la Pierre'
            );
            $('#email_client').val(client);
            $('#email_date').val(date);
            $('#modalEmail').modal('show');
        });

        $(document).on('click', '#btn-send-email', function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Envoi...');

            $.ajax({
                url:    '{{ route("devis.sendEmail") }}',
                method: 'POST',
                data: {
                    _token:       '{{ csrf_token() }}',
                    destinataire: $('#email_destinataire').val(),
                    objet:        $('#email_objet').val(),
                    message:      $('#email_message').val(),
                    client:       $('#email_client').val(),
                    date:         $('#email_date').val(),
                },
                success: function() {
                    $('#modalEmail').modal('hide');
                    btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane me-1"></i> Envoyer');
                    $('body').append('<div class="toast-success">Mail envoyé ✓</div>');
                    setTimeout(() => $('.toast-success').remove(), 3000);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane me-1"></i> Envoyer');
                    if      (xhr.status === 422) alert('Données invalides.');
                    else if (xhr.status === 500) alert('Erreur serveur SMTP.');
                    else if (xhr.status === 0)   alert('Pas de connexion réseau.');
                    else                         alert('Erreur inattendue (' + xhr.status + ').');
                }
            });
        });

        let currentPdfUrl = '';
        $(document).on('click', '.btn-trigger-pdf', function(e) {
            e.preventDefault();
            currentPdfUrl = $(this).attr('data-url');
            $('#pdfRefModal').modal('show');
        });

        $(document).on('click', '#confirmDownload', function() {
            const ref = $('#custom_ref').val().trim();
            window.location.href = currentPdfUrl + (ref
                ? (currentPdfUrl.includes('?') ? '&' : '?') + 'ref=' + encodeURIComponent(ref)
                : '');
            $('#pdfRefModal').modal('hide');
        });

    });
</script>
@include('partials.modals-devis')
</body>
</html>
